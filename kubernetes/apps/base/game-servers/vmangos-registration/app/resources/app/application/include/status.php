<?php
/**
 * VMaNGOS Registration Portal - Server Status
 **/

class status
{
    public static function get_character_by_guid($realmID, $guid)
    {
        if (!empty($guid)) {
            $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
            $queryBuilder->select("name, race, class, gender, level")
                ->from("characters")
                ->where("guid = :guid")
                ->setParameter("guid", $guid);

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["level"])) {
                return $datas[0];
            }
        }
        return false;
    }

    public static function get_top_achievements($realmID)
    {
        try {
            $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
            $queryBuilder->select("guid, COUNT(*) as total")
                ->from("character_achievement")
                ->groupBy("guid")
                ->orderBy("total", "DESC")
                ->setMaxResults(10);

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["guid"])) {
                return $datas;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    public static function get_top_arenateams($realmID)
    {
        try {
            $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
            $queryBuilder->select("arenaTeamId, name, captainGuid, rating")
                ->from("arena_team")
                ->orderBy("rating", "DESC")
                ->setMaxResults(10);

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["arenaTeamId"])) {
                return $datas;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    public static function get_top_killers($realmID)
    {
        // CMaNGOS Classic uses stored_honorable_kills instead of totalKills
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
        try {
            $queryBuilder->select("name, race, class, gender, level, totalKills")
                ->from("characters")
                ->orderBy("totalKills", "DESC")
                ->setMaxResults(10);
            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["totalKills"])) {
                return $datas;
            }
        } catch (\Exception $e) {
            // Fallback for CMaNGOS Classic
            $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
            $queryBuilder->select("name, race, class, gender, level, stored_honorable_kills as totalKills")
                ->from("characters")
                ->orderBy("stored_honorable_kills", "DESC")
                ->setMaxResults(10);
            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["totalKills"])) {
                return $datas;
            }
        }
        return false;
    }

    public static function get_top_arenapoints($realmID)
    {
        try {
            $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
            $queryBuilder->select("name, race, class, gender, level, arenaPoints")
                ->from("characters")
                ->orderBy("arenaPoints", "DESC")
                ->setMaxResults(10);
            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["arenaPoints"])) {
                return $datas;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    public static function get_top_honorpoints($realmID)
    {
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();

        try {
            if (get_config('expansion') >= 6) {
                $queryBuilder->select("name, race, class, gender, level, honorLevel, honor")
                    ->from("characters")
                    ->orderBy("honorLevel", "DESC")
                    ->addOrderBy("honor", "DESC")
                    ->setMaxResults(10);
            } else {
                // Try totalHonorPoints first (VMaNGOS/TBC+)
                $queryBuilder->select("name, race, class, gender, level, totalHonorPoints")
                    ->from("characters")
                    ->orderBy("totalHonorPoints", "DESC")
                    ->setMaxResults(10);
            }

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["level"])) {
                return $datas;
            }
        } catch (\Exception $e) {
            // Fallback for CMaNGOS Classic: use stored_honor_rating
            $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
            $queryBuilder->select("name, race, class, gender, level, stored_honor_rating as totalHonorPoints")
                ->from("characters")
                ->orderBy("stored_honor_rating", "DESC")
                ->setMaxResults(10);
            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]["level"])) {
                return $datas;
            }
        }
        return false;
    }

    public static function get_top_playtime($realmID)
    {
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
        $queryBuilder->select("name, race, class, gender, level, totaltime")
            ->from("characters")
            ->orderBy("totaltime", "DESC")
            ->setMaxResults(10);

        $statement = $queryBuilder->executeQuery();
        $datas = $statement->fetchAllAssociative();

        if (!empty($datas[0]["totaltime"])) {
            return $datas;
        }
        return false;
    }

    public static function get_top_gold($realmID)
    {
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
        $queryBuilder->select("name, race, level, totaltime, money")
            ->from("characters")
            ->orderBy("money", "DESC")
            ->setMaxResults(10);

        $statement = $queryBuilder->executeQuery();
        $datas = $statement->fetchAllAssociative();

        if (!empty($datas[0]["money"])) {
            return $datas;
        }
        return false;
    }
}
