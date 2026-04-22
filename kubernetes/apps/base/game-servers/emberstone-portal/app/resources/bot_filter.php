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
        $ids = database::$auth->fetchFirstColumn(
            "SELECT id FROM account WHERE UPPER(username) LIKE 'RNDBOT%'"
        );
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
    $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
        ->select('COUNT(*)')
        ->from('characters')
        ->where('online = 1');
    portal_apply_bot_filter($qb);
    return (int) $qb->executeQuery()->fetchOne();
}

function portal_online_players($realm)
{
    $qb = database::$chars[$realm['realmid']]->createQueryBuilder()
        ->select('name, race, class, gender, level')
        ->from('characters')
        ->where('online = 1')
        ->orderBy('level', 'DESC')
        ->setMaxResults(49);
    portal_apply_bot_filter($qb);
    $rows = $qb->executeQuery()->fetchAllAssociative();
    return empty($rows[0]['name']) ? false : $rows;
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
    try {
        $qb = $conn->createQueryBuilder()
            ->select('name, race, class, gender, level, totalKills')
            ->from('characters')
            ->orderBy('totalKills', 'DESC')
            ->setMaxResults(10);
        portal_apply_bot_filter($qb);
        $rows = $qb->executeQuery()->fetchAllAssociative();
        if (!empty($rows[0]['totalKills'])) {
            return $rows;
        }
    } catch (\Exception $e) {
        // CMaNGOS Classic: totalKills doesn't exist, fall through.
    }
    $qb = $conn->createQueryBuilder()
        ->select('name, race, class, gender, level, stored_honorable_kills as totalKills')
        ->from('characters')
        ->orderBy('stored_honorable_kills', 'DESC')
        ->setMaxResults(10);
    portal_apply_bot_filter($qb);
    $rows = $qb->executeQuery()->fetchAllAssociative();
    return empty($rows[0]['totalKills']) ? false : $rows;
}

function portal_top_honorpoints($realm)
{
    $conn = database::$chars[$realm['realmid']];
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
        if (!empty($rows[0]['level'])) {
            return $rows;
        }
    } catch (\Exception $e) {
        // CMaNGOS Classic: totalHonorPoints doesn't exist, fall through.
    }
    $qb = $conn->createQueryBuilder()
        ->select('name, race, class, gender, level, stored_honor_rating as totalHonorPoints')
        ->from('characters')
        ->orderBy('stored_honor_rating', 'DESC')
        ->setMaxResults(10);
    portal_apply_bot_filter($qb);
    $rows = $qb->executeQuery()->fetchAllAssociative();
    return empty($rows[0]['level']) ? false : $rows;
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
