<?php
/**
 * @author Amin Mahmoudi (MasterkinG)
 * @copyright    Copyright (c) 2019 - 2024, MasterkinG32. (https://masterking32.com)
 * @link    https://masterking32.com
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
        return false;
    }

    public static function get_top_arenateams($realmID)
    {
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
        return false;
    }

    public static function get_top_killers($realmID)
    {
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
        $queryBuilder->select("name, race, class, gender, level, totalKills")
            ->from("characters")
            ->orderBy("totalKills", "DESC")
            ->setMaxResults(10);
        $statement = $queryBuilder->executeQuery();
        $datas = $statement->fetchAllAssociative();

        if (!empty($datas[0]["totalKills"])) {
            return $datas;
        }
        return false;
    }

    public static function get_top_arenapoints($realmID)
    {
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
        return false;
    }

    public static function get_top_honorpoints($realmID)
    {
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();

        if (get_config('expansion') >= 6) {
            $queryBuilder->select("name, race, class, gender, level, honorLevel, honor")
                ->from("characters")
                ->orderBy("honorLevel", "DESC")
                ->addOrderBy("honor", "DESC")
                ->setMaxResults(10);
        } else {
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
