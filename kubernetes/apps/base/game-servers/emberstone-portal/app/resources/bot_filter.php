<?php
/**
 * Emberstone Portal - Real-player filter
 *
 * The upstream user::/status:: classes in cmangos-registration include AI
 * Playerbot accounts in their online and top-player listings, which makes
 * the server-status and top-players tabs useless once random-bot autologin
 * is on (300-500 bots drown out real players).
 *
 * Each helper below mirrors the upstream query but adds a
 * `account NOT IN (<rndbot account ids>)` clause. Bot account ids are
 * looked up once per request from the auth DB (RandomBotAccountPrefix
 * defaults to "rndbot") and cached in a static variable.
 *
 * These names deliberately differ from the upstream (`portal_*` vs
 * `user::`/`status::`) so that a future upstream bump cannot silently
 * revert the filter — main.php has to be updated to opt in.
 */

/**
 * Account ids of AI Playerbot accounts. Cached per request.
 * @return int[]
 */
function portal_bot_account_ids()
{
    static $ids = null;
    if ($ids === null) {
        // LIKE 'RNDBOT%' with UPPER() to survive collation quirks. Matches the
        // default AiPlayerbot.RandomBotAccountPrefix; if the admin changes
        // that prefix, this filter stops working and bots reappear in the
        // lists — make that the failure mode, not silent matching of the
        // wrong accounts.
        try {
            $ids = database::$auth->fetchFirstColumn(
                "SELECT id FROM account WHERE UPPER(username) LIKE 'RNDBOT%'"
            );
        } catch (\Throwable $e) {
            // Auth DB unreachable/corrupt (e.g. a crashed MyISAM account
            // table). Degrade to an empty filter — bots may briefly reappear
            // in the lists, but the whole page must not 500 over it. Do NOT
            // cache the failure: leave $ids null so a later request retries.
            return [];
        }
    }
    return $ids;
}

/**
 * Attach the bot-exclusion clause to a QueryBuilder that selects from
 * characters. No-op when there are no bot accounts.
 */
function portal_apply_bot_filter($qb)
{
    $bots = portal_bot_account_ids();
    if (!empty($bots)) {
        $qb->andWhere('account NOT IN (:portalBotIds)')
           ->setParameter('portalBotIds', $bots, \Doctrine\DBAL\ArrayParameterType::INTEGER);
    }
    return $qb;
}

function portal_online_count($realm)
{
    try {
        $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('characters')
            ->where('online = 1');
        portal_apply_bot_filter($qb);
        return (int) $qb->executeQuery()->fetchOne();
    } catch (\Throwable $e) {
        return 0;
    }
}

function portal_online_players($realm)
{
    try {
        $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
            ->select('name, race, class, gender, level')
            ->from('characters')
            ->where('online = 1')
            ->orderBy('level', 'DESC')
            ->setMaxResults(49);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        return empty($rows[0]['name']) ? false : $rows;
    } catch (\Throwable $e) {
        return false;
    }
}

/**
 * Real characters active in the past 30 days (online now or logged out
 * within the window), online-first and then most-recently-seen. Returns
 * raw `online` flag and `logout_time` so the view can render a
 * "Last seen" label.
 */
function portal_recent_activity($realm, $windowSeconds = 2592000)
{
    try {
        $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
            ->select('name, race, class, gender, level, online, logout_time')
            ->from('characters')
            ->where('online = 1 OR logout_time >= :cutoff')
            ->setParameter('cutoff', time() - $windowSeconds)
            ->orderBy('online', 'DESC')
            ->addOrderBy('logout_time', 'DESC')
            ->setMaxResults(49);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        return empty($rows[0]['name']) ? false : $rows;
    } catch (\Throwable $e) {
        return false;
    }
}

/**
 * Highest-level real character on the realm (all-time, not restricted to
 * the recent-activity window — the point is bragging rights). Returns a
 * single row or false.
 */
function portal_highest_level_char($realm)
{
    try {
        $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
            ->select('name, race, class, gender, level')
            ->from('characters')
            ->orderBy('level', 'DESC')
            ->addOrderBy('totaltime', 'DESC')
            ->setMaxResults(1);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        return empty($rows[0]['name']) ? false : $rows[0];
    } catch (\Throwable $e) {
        return false;
    }
}

/**
 * Render a short "Last seen" label for a character row with `online`
 * and `logout_time` fields. Pure formatter — no DB access.
 */
function portal_format_last_seen($row)
{
    if (!empty($row['online'])) {
        return 'Online now';
    }
    $ago = max(0, time() - (int) ($row['logout_time'] ?? 0));
    if ($ago < 60) {
        return 'just now';
    }
    if ($ago < 3600) {
        return floor($ago / 60) . 'm ago';
    }
    if ($ago < 86400) {
        return floor($ago / 3600) . 'h ago';
    }
    return floor($ago / 86400) . 'd ago';
}

function portal_top_playtime($realm)
{
    $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
        ->select('name, race, class, gender, level, totaltime')
        ->from('characters')
        ->orderBy('totaltime', 'DESC')
        ->setMaxResults(10);
    portal_apply_bot_filter($qb);
    $rows = $qb->executeQuery()->fetchAllAssociative();
    return empty($rows[0]['totaltime']) ? false : $rows;
}

function portal_top_killers($realm)
{
    $conn = database::$chars[$realm['realmid']];
    // AzerothCore-style schema (totalKills column).
    try {
        $qb = $conn->createQueryBuilder()
            ->select('name, race, class, gender, level, totalKills')
            ->from('characters')
            ->orderBy('totalKills', 'DESC')
            ->setMaxResults(10);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        // Query succeeded — schema is AzerothCore. Don't fall through to
        // the CMaNGOS-only column even if no real player has any kills,
        // otherwise stored_honorable_kills will throw on AzerothCore.
        return empty($rows[0]['totalKills']) ? false : $rows;
    } catch (\Exception $e) {
        // CMaNGOS Classic: totalKills column doesn't exist; fall through.
    }
    // CMaNGOS Classic schema (stored_honorable_kills column).
    try {
        $qb = $conn->createQueryBuilder()
            ->select('name, race, class, gender, level, stored_honorable_kills as totalKills')
            ->from('characters')
            ->orderBy('stored_honorable_kills', 'DESC')
            ->setMaxResults(10);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        return empty($rows[0]['totalKills']) ? false : $rows;
    } catch (\Exception $e) {
        return false;
    }
}

function portal_top_honorpoints($realm)
{
    $conn = database::$chars[$realm['realmid']];
    // AzerothCore / Classic-Era schema (totalHonorPoints / honorLevel+honor).
    try {
        $qb = $conn->createQueryBuilder();
        if (get_config('expansion') >= 6) {
            $qb->select('name, race, class, gender, level, honorLevel, honor')
               ->from('characters')
               ->orderBy('honorLevel', 'DESC')
               ->addOrderBy('honor', 'DESC')
               ->setMaxResults(10);
        } else {
            $qb->select('name, race, class, gender, level, totalHonorPoints')
               ->from('characters')
               ->orderBy('totalHonorPoints', 'DESC')
               ->setMaxResults(10);
        }
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        // Query succeeded — schema fits. Don't fall through to the
        // CMaNGOS-only column even if no real player has honor; otherwise
        // stored_honor_rating will throw on AzerothCore.
        return empty($rows[0]['level']) ? false : $rows;
    } catch (\Exception $e) {
        // CMaNGOS Classic: totalHonorPoints column doesn't exist; fall through.
    }
    // CMaNGOS Classic schema (stored_honor_rating column).
    try {
        $qb = $conn->createQueryBuilder()
            ->select('name, race, class, gender, level, stored_honor_rating as totalHonorPoints')
            ->from('characters')
            ->orderBy('stored_honor_rating', 'DESC')
            ->setMaxResults(10);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        return empty($rows[0]['level']) ? false : $rows;
    } catch (\Exception $e) {
        return false;
    }
}

function portal_top_arenapoints($realm)
{
    try {
        $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
            ->select('name, race, class, gender, level, arenaPoints')
            ->from('characters')
            ->orderBy('arenaPoints', 'DESC')
            ->setMaxResults(10);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        return empty($rows[0]['arenaPoints']) ? false : $rows;
    } catch (\Exception $e) {
        return false;
    }
}

/**
 * Arena teams don't store account directly; filter by captain character.
 * Fetch a wider slice (20 teams) so the post-filter has a decent chance
 * of still returning 10 real entries when bot captains dominate the top.
 */
function portal_top_arenateams($realm)
{
    try {
        $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
            ->select('arenaTeamId, name, captainGuid, rating')
            ->from('arena_team')
            ->orderBy('rating', 'DESC')
            ->setMaxResults(20);
        $teams = $qb->executeQuery()->fetchAllAssociative();
    } catch (\Exception $e) {
        return false;
    }
    if (empty($teams)) {
        return false;
    }

    $bots = portal_bot_account_ids();
    if (empty($bots)) {
        return array_slice($teams, 0, 10);
    }

    $captainGuids = array_values(array_filter(array_column($teams, 'captainGuid')));
    if (empty($captainGuids)) {
        return array_slice($teams, 0, 10);
    }

    $guidQb = database::$chars[$realm['realmid']]->createQueryBuilder()
        ->select('guid')
        ->from('characters')
        ->where('guid IN (:guids)')
        ->andWhere('account NOT IN (:bots)')
        ->setParameter('guids', $captainGuids, \Doctrine\DBAL\ArrayParameterType::INTEGER)
        ->setParameter('bots', $bots, \Doctrine\DBAL\ArrayParameterType::INTEGER);
    $realCaptainGuids = array_flip($guidQb->executeQuery()->fetchFirstColumn());

    $filtered = array_values(array_filter(
        $teams,
        fn($t) => isset($realCaptainGuids[$t['captainGuid']])
    ));
    return empty($filtered) ? false : array_slice($filtered, 0, 10);
}
