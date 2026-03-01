<?php
/**
 * @author Amin Mahmoudi (MasterkinG)
 * @copyright    Copyright (c) 2019 - 2024, MasterkinG32. (https://masterking32.com)
 * @link    https://masterking32.com
 * @TODO: Add vote verify system.
 **/

class vote
{
    public static function post_handler()
    {
        if (get_config('vote_system') && !empty($_POST['account']) && !empty($_POST['siteid'])) {
            self::do_vote($_POST['account'], $_POST['siteid']);
        }
    }

    /**
     * Validate account and do vote.
     * @return bool
     */
    public static function do_vote($account, $siteID)
    {
        global $antiXss;
        $vote_sites = get_config('vote_sites');
        if (!is_numeric($siteID) || empty($vote_sites[$siteID - 1])) {
            error_msg(lang('vote_site_not_valid'));
            return false;
        }

        if (get_config('battlenet_support')) {
            if (!filter_var($account, FILTER_VALIDATE_EMAIL)) {
                error_msg(lang('use_valid_email'));
                return false;
            }

            $acc_data = user::get_user_by_email($account);
        } else {
            if (!preg_match('/^[0-9A-Z-_]+$/', strtoupper($account))) {
                error_msg(lang('use_valid_username'));
                return false;
            }

            $acc_data = user::get_user_by_username($account);
        }

        if (empty($acc_data['id'])) {
            error_msg(lang('account_is_not_valid'));
            return false;
        }

        if (!isset($acc_data['votePoints'])) {
            self::setup_vote_table();
        }
        $siteID--;
        database::$auth->executeStatement("DELETE FROM `votes` WHERE `votedate` < ? AND `done` = 0", [date("Y-m-d H:i:s", time() - 43200)]);

        if (!empty(self::get_vote_by_IP($siteID)) || !empty(self::get_vote_by_account($siteID, $acc_data['id']))) {
            error_msg(lang('you_already_voted'));
            return false;
        }

        database::$auth->insert('votes', [
            'ip' => $antiXss->xss_clean(strtoupper(getIP())),
            'vote_site' => $antiXss->xss_clean($siteID),
            'accountid' => $antiXss->xss_clean($acc_data['id']),
        ]);

        $queryBuilder = database::$auth->createQueryBuilder();
        $queryBuilder->update('account')
            ->set('votePoints', 'votePoints + 1')
            ->where('id = :id')
            ->setParameter('id', $acc_data['id']);

        $queryBuilder->executeQuery();

        header('location: ' . $vote_sites[$siteID]['site_url']);
        exit();
    }

    public static function get_vote_by_IP($siteID)
    {
        $queryBuilder = database::$auth->createQueryBuilder();
        $queryBuilder->select('*')
            ->from('votes')
            ->where('ip = :ip')
            ->andWhere('vote_site = :siteid')
            ->setParameter('ip', strtoupper(getIP()))
            ->setParameter('siteid', $siteID);

        $statement = $queryBuilder->executeQuery();
        $datas = $statement->fetchAllAssociative();

        if (!empty($datas[0]['id'])) {
            return $datas;
        }

        return false;
    }

    public static function get_vote_by_account($siteID, $accountID)
    {
        $queryBuilder = database::$auth->createQueryBuilder();
        $queryBuilder->select('*')
            ->from('votes')
            ->where('accountid = :accountid')
            ->andWhere('vote_site = :siteid')
            ->setParameter('accountid', $accountID)
            ->setParameter('siteid', $siteID);

        $statement = $queryBuilder->executeQuery();
        $datas = $statement->fetchAllAssociative();

        if (!empty($datas[0]['id'])) {
            return $datas;
        }

        return false;
    }

    public static function setup_vote_table()
    {
        database::$auth->executeQuery("ALTER TABLE `account` ADD COLUMN `votePoints` varchar(255) NULL DEFAULT '0';");
        database::$auth->executeQuery("
            CREATE TABLE `votes` (
                `id` bigint(255) NOT NULL AUTO_INCREMENT,
                `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                `vote_site` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                `accountid` bigint(255) NULL DEFAULT 0,
                `votedate` timestamp(0) NULL DEFAULT current_timestamp(0),
                `done` int(10) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`) USING BTREE
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;
        ");

        return true;
    }
}
