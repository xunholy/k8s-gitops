<?php
/**
 * VMaNGOS Registration Portal - User Management
 **/

use Gregwar\Captcha\CaptchaBuilder;

class user
{
    public static $captcha;

    public static function post_handler()
    {
        if (!empty($_GET['restore']) && !empty($_GET['key'])) {
            self::restorepassword_setnewpw($_GET['restore'], $_GET['key']);
        }

        if (!empty($_GET['enabletfa']) && !empty($_GET['account'])) {
            self::account_set_2fa($_GET['enabletfa'], $_GET['account']);
        }

        if (!empty($_POST['langchangever'])) {
            self::lang_cookie_changer($_POST['langchange']);
        }

        if (!empty($_POST['submit'])) {
            self::tfa_enable();
            if (get_config('battlenet_support')) {
                self::bnet_register();
                self::bnet_changepass();
            } else {
                self::normal_register();
                self::normal_changepass();
            }
            self::restorepassword();
            if (empty(get_config('captcha_type'))) {
                unset($_SESSION['captcha']);
                self::$captcha = new CaptchaBuilder;
                self::$captcha->build();
                $_SESSION['captcha'] = self::$captcha->getPhrase();
            }
        } else {
            if (empty(get_config('captcha_type'))) {
                unset($_SESSION['captcha']);
                self::$captcha = new CaptchaBuilder;
                self::$captcha->build();
                $_SESSION['captcha'] = self::$captcha->getPhrase();
            }
        }
    }

    /**
     * Language Changer
     */
    public static function lang_cookie_changer($getlang)
    {
        $supported_langs = get_config('supported_langs');
        if (!empty($supported_langs) && !empty($supported_langs[$getlang])) {
            setcookie('website_lang', $getlang); //sets the language cookie to selected language
            header("location: " . get_config("baseurl"));
            exit();
        }
    }

    /**
     * Battle.net registration
     * @return bool
     */
    public static function bnet_register()
    {
        global $antiXss;
        if ($_POST['submit'] != 'register' || empty($_POST['password']) || empty($_POST['repassword']) || empty($_POST['email'])) {
            return false;
        }

        if (!captcha_validation()) {
            return false;
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            error_msg(lang('use_valid_email'));
            return false;
        }

        if ($_POST['password'] != $_POST['repassword']) {
            error_msg(lang('passwords_not_equal'));
            return false;
        }

        if(get_config('srp6_support') && get_config('srp6_version') == 2) {
            if (!(strlen($_POST['password']) >= 4 && strlen($_POST['password']) <= 128)) {
                error_msg(lang('passwords_length'));
                return false;
            }
        }
        else {
            if (!(strlen($_POST['password']) >= 4 && strlen($_POST['password']) <= 16)) {
                error_msg(lang('passwords_length'));
                return false;
            }
        }

        if (!self::check_email_exists(strtoupper($_POST["email"]))) {
            error_msg(lang('username_or_email_exists'));
            return false;
        }

        if (empty(get_config('soap_for_register'))) {
            if (empty(get_config('srp6_support'))) {
                $bnet_hashed_pass = strtoupper(bin2hex(strrev(hex2bin(strtoupper(hash('sha256', strtoupper(hash('sha256', strtoupper($_POST['email'])) . ':' . strtoupper($_POST['password']))))))));
                database::$auth->insert('battlenet_accounts', [
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'sha_pass_hash' => $antiXss->xss_clean($bnet_hashed_pass),
                ]);

                $bnet_account_id = database::$auth->lastInsertId();
                $username = $bnet_account_id . '#1';
                $hashed_pass = strtoupper(sha1(strtoupper($username . ':' . $_POST['password'])));
                database::$auth->insert('account', [
                    'username' => $antiXss->xss_clean(strtoupper($username)),
                    'sha_pass_hash' => $antiXss->xss_clean($hashed_pass),
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'expansion' => $antiXss->xss_clean(get_config('expansion')),
                    'battlenet_account' => $bnet_account_id,
                    'battlenet_index' => 1,
                ]);
                success_msg(lang('account_created'));
                return true;
            }

            if(get_config('srp6_version') == 0) {
                list($salt, $verifier) = getRegistrationData(strtoupper($_POST['username']), $_POST['password']);
                $bnet_hashed_pass = strtoupper(bin2hex(strrev(hex2bin(strtoupper(hash('sha256', strtoupper(hash('sha256', strtoupper($_POST['email'])) . ':' . strtoupper($_POST['password']))))))));
                database::$auth->insert('battlenet_accounts', [
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'sha_pass_hash' => $antiXss->xss_clean($bnet_hashed_pass),
                ]);

                $bnet_account_id = database::$auth->lastInsertId();
                $username = $bnet_account_id . '#1';
                database::$auth->insert('account', [
                    'username' => $antiXss->xss_clean(strtoupper($username)),
                    get_core_config("salt_field") => $salt,
                    get_core_config("verifier_field") => $verifier,
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'expansion' => $antiXss->xss_clean(get_config('expansion')),
                    'battlenet_account' => $bnet_account_id,
                    'battlenet_index' => 1,
                ]);
                success_msg(lang('account_created'));
                return true;
            }

            if(get_config('srp6_version') == 1) {
                list($salt, $verifier) = getRegistrationDataBnetV1(strtoupper($_POST['email']), $_POST['password']);
                database::$auth->insert('battlenet_accounts', [
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'srp_version' => 1,
                    get_core_config("salt_field") => $salt,
                    get_core_config("verifier_field") => $verifier,
                ]);

                $bnet_account_id = database::$auth->lastInsertId();
                $game_account_name = $bnet_account_id . '#1';
                list($game_account_salt, $game_account_verifier) = getRegistrationData($game_account_name, $_POST['password']);
                database::$auth->insert('account', [
                    'username' => $antiXss->xss_clean($game_account_name),
                    get_core_config("salt_field") => $game_account_salt,
                    get_core_config("verifier_field") => $game_account_verifier,
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'expansion' => $antiXss->xss_clean(get_config('expansion')),
                    'battlenet_account' => $bnet_account_id,
                    'battlenet_index' => 1,
                ]);
                success_msg(lang('account_created'));
                return true;
            }

            if(get_config('srp6_version') == 2) {
                list($salt, $verifier) = getRegistrationDataBnetV2(strtoupper($_POST['email']), $_POST['password']);
                database::$auth->insert('battlenet_accounts', [
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'srp_version' => 2,
                    get_core_config("salt_field") => $salt,
                    get_core_config("verifier_field") => $verifier,
                ]);

                $bnet_account_id = database::$auth->lastInsertId();
                $game_account_name = $bnet_account_id . '#1';
                list($game_account_salt, $game_account_verifier) = getRegistrationData($game_account_name, substr($_POST['password'], 0, 16));
                database::$auth->insert('account', [
                    'username' => $antiXss->xss_clean($game_account_name),
                    get_core_config("salt_field") => $game_account_salt,
                    get_core_config("verifier_field") => $game_account_verifier,
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'expansion' => $antiXss->xss_clean(get_config('expansion')),
                    'battlenet_account' => $bnet_account_id,
                    'battlenet_index' => 1,
                ]);
                success_msg(lang('account_created'));
                return true;
            }
        }

        $command = str_replace('{USERNAME}', $antiXss->xss_clean($_POST['email']), get_config('soap_ca_command'));
        $command = str_replace('{PASSWORD}', $antiXss->xss_clean($_POST['password']), $command);
        if (RemoteCommandWithSOAP($command)) {
            success_msg(lang('account_created'));
        } else {
            error_msg(lang('error_try_again'));
        }
        return true;
    }

    /**
     * Registration without battle net servers.
     * @return bool
     */
    public static function normal_register()
    {
        global $antiXss;
        if ($_POST['submit'] != 'register' || empty($_POST['password']) || empty($_POST['username']) || empty($_POST['repassword']) || empty($_POST['email'])) {
            return false;
        }

        if (!captcha_validation()) {
            return false;
        }

        if (!preg_match('/^[0-9A-Z-_]+$/', strtoupper($_POST['username']))) {
            error_msg(lang('use_valid_username'));
            return false;
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            error_msg(lang('use_valid_email'));
            return false;
        }

        if ($_POST['password'] != $_POST['repassword']) {
            error_msg(lang('passwords_not_equal'));
            return false;
        }

        if (!(strlen($_POST['password']) >= 4 && strlen($_POST['password']) <= 16)) {
            error_msg(lang('passwords_length'));
            return false;
        }

        if (!(strlen($_POST['username']) >= 2 && strlen($_POST['username']) <= 16)) {
            error_msg(lang('username_length'));
            return false;
        }

        if (!get_config('multiple_email_use') && !self::check_email_exists(strtoupper($_POST['email']))) {
            error_msg(lang('email_exists'));
            return false;
        }

        if (!self::check_username_exists(strtoupper($_POST['username']))) {
            error_msg(lang('username_exists'));
            return false;
        }

        if (empty(get_config('soap_for_register'))) {
            if (empty(get_config('srp6_support'))) {
                $hashed_pass = strtoupper(sha1(strtoupper($_POST['username'] . ':' . $_POST['password'])));
                database::$auth->insert('account', [
                    'username' => $antiXss->xss_clean(strtoupper($_POST['username'])),
                    'sha_pass_hash' => $antiXss->xss_clean($hashed_pass),
                    'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    //'reg_mail' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                    'expansion' => $antiXss->xss_clean(get_config('expansion')),
                ]);
                success_msg(lang('account_created'));
                return true;
            }

            list($salt, $verifier) = getRegistrationData(strtoupper($_POST['username']), $_POST['password']);
            database::$auth->insert('account', [
                'username' => $antiXss->xss_clean(strtoupper($_POST['username'])),
                get_core_config("salt_field") => $salt,
                get_core_config("verifier_field") => $verifier,
                'email' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                //'reg_mail' => $antiXss->xss_clean(strtoupper($_POST['email'])),
                'expansion' => $antiXss->xss_clean(get_config('expansion')),
            ]);
            success_msg(lang('account_created'));
            return true;
        }

        $command = str_replace('{USERNAME}', $antiXss->xss_clean(strtoupper($_POST['username'])), get_config('soap_ca_command'));
        $command = str_replace('{PASSWORD}', $antiXss->xss_clean($_POST['password']), $command);
        $command = str_replace('{EMAIL}', $antiXss->xss_clean(strtoupper($_POST['email'])), $command);
        if (RemoteCommandWithSOAP($command)) {
            if (!empty(get_config('soap_asa_command'))) {
                $command_addon = str_replace('{USERNAME}', $antiXss->xss_clean(strtoupper($_POST['username'])), get_config('soap_asa_command'));
                $command_addon = str_replace('{EXPANSION}', get_config('expansion'), $command_addon);
                RemoteCommandWithSOAP($command_addon);
            }

            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->update('account')
                ->set('email', ':email')
                ->where('username = :username')
                ->setParameter('email', $antiXss->xss_clean(strtoupper($_POST['email'])))
                ->setParameter('username', $antiXss->xss_clean(strtoupper($_POST['username'])));
            $queryBuilder->executeQuery();

            success_msg(lang('account_created'));
        } else {
            error_msg(lang('error_try_again'));
        }

        return true;
    }

    /**
     * Change password for Battle.net Cores.
     * @return bool
     */
    public static function bnet_changepass()
    {
        global $antiXss;

        if (!empty(get_config('disable_changepassword'))) {
            return false;
        }

        if ($_POST['submit'] != 'changepass' || empty($_POST['password']) || empty($_POST['old_password']) || empty($_POST['repassword']) || empty($_POST['email'])) {
            return false;
        }

        if (!captcha_validation()) {
            return false;
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            error_msg(lang('use_valid_email'));
            return false;
        }

        if ($_POST['password'] != $_POST['repassword']) {

            error_msg(lang('passwords_not_equal'));
            return false;
        }

        if(get_config('srp6_support') && get_config('srp6_version') == 2) {
            if (!(strlen($_POST['password']) >= 4 && strlen($_POST['password']) <= 128)) {
                error_msg(lang('passwords_length'));
                return false;
            }
        }
        else {
            if (!(strlen($_POST['password']) >= 4 && strlen($_POST['password']) <= 16)) {
                error_msg(lang('passwords_length'));
                return false;
            }
        }

        $userinfo = self::get_user_by_email(strtoupper($_POST['email']));
        if ((empty(get_config('srp6_support')) && empty($userinfo['username'])) || (!empty(get_config('srp6_support')) && (get_config('srp6_version') == 0) && empty($userinfo['username']))) {
            error_msg(lang('email_not_correct'));
            return false;
        }

        $bnetAccountInfo = self::get_bnetaccount_by_email(strtoupper($_POST['email']));
        if (empty($bnetAccountInfo['email']) && !empty(get_config('srp6_support')) && (get_config('srp6_version') > 0)) {
            error_msg(lang('email_not_correct'));
            return false;
        }

        if (empty(get_config('srp6_support'))) {
            $Old_hashed_pass = strtoupper(sha1(strtoupper($userinfo['username'] . ':' . $_POST['old_password'])));
            $hashed_pass = strtoupper(sha1(strtoupper($userinfo['username'] . ':' . $_POST['password'])));

            if (strtoupper($userinfo['sha_pass_hash']) != $Old_hashed_pass) {
                error_msg(lang('old_password_not_valid'));
                return false;
            }

            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->update('account')
                ->set('sha_pass_hash', ':sha_pass_hash')
                ->set('sessionkey', '')
                ->set('v', '')
                ->set('s', '')
                ->where('id = :id')
                ->setParameter('sha_pass_hash', $antiXss->xss_clean($hashed_pass))
                ->setParameter('id', $userinfo['id']);
            $queryBuilder->executeQuery();

            $bnet_hashed_pass = strtoupper(bin2hex(strrev(hex2bin(strtoupper(hash('sha256', strtoupper(hash('sha256', strtoupper($userinfo['email'])) . ':' . strtoupper($_POST['password']))))))));

            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->update('battlenet_accounts')
                ->set('sha_pass_hash', ':sha_pass_hash')
                ->set('sessionkey', '')
                ->set('v', '')
                ->set('s', '')
                ->where('id = :id')
                ->setParameter('sha_pass_hash', $antiXss->xss_clean($bnet_hashed_pass))
                ->setParameter('id', $userinfo['battlenet_account']);
            $queryBuilder->executeQuery();
        } else {
            if (get_config('srp6_version') == 0) {
                if (!verifySRP6($userinfo['username'], $_POST['old_password'], $userinfo[get_core_config("salt_field")], $userinfo[get_core_config("verifier_field")])) {
                error_msg(lang('old_password_not_valid'));
                return false;
                }

                list($salt, $verifier) = getRegistrationData(strtoupper($userinfo['username']), $_POST['password']);

                $queryBuilder = database::$auth->createQueryBuilder();
                $queryBuilder->update('account')
                    ->set(get_core_config("salt_field"), ':salt')
                    ->set(get_core_config("verifier_field"), ':verifier')
                    ->where('id = :id')
                    ->setParameter('salt', $salt)
                    ->setParameter('verifier', $verifier)
                    ->setParameter('id', $userinfo['id']);
                $queryBuilder->executeQuery();
            }
            if (get_config('srp6_version') == 1) {
                if (!verifySRP6BnetV1($bnetAccountInfo['email'], $_POST['old_password'], $bnetAccountInfo[get_core_config("salt_field")], $bnetAccountInfo[get_core_config("verifier_field")])) {
                    error_msg(lang('old_password_not_valid'));
                    return false;
                }

                $game_account_name = $bnetAccountInfo['id'] . '#1';
                list($salt, $verifier) = getRegistrationData($game_account_name, substr($_POST['password'], 0, 16));

                $queryBuilder = database::$auth->createQueryBuilder();
                $queryBuilder->update('account')
                    ->set(get_core_config("salt_field"), ':salt')
                    ->set(get_core_config("verifier_field"), ':verifier')
                    ->where('email = :email')
                    ->setParameter('salt', $salt)
                    ->setParameter('verifier', $verifier)
                    ->setParameter('email', $bnetAccountInfo['email']);
                $queryBuilder->executeQuery();

                list($salt, $verifier) = getRegistrationDataBnetV1($bnetAccountInfo['email'], $_POST['password']);

                $queryBuilder = database::$auth->createQueryBuilder();
                $queryBuilder->update('battlenet_accounts')
                    ->set('srp_version', 1)
                    ->set(get_core_config("salt_field"), ':salt')
                    ->set(get_core_config("verifier_field"), ':verifier')
                    ->where('id = :id')
                    ->setParameter('salt', $salt)
                    ->setParameter('verifier', $verifier)
                    ->setParameter('id', $bnetAccountInfo['id']);
                $queryBuilder->executeQuery();
            }
            if (get_config('srp6_version') == 2) {
                if (!verifySRP6BnetV2($bnetAccountInfo['email'], $_POST['old_password'], $bnetAccountInfo[get_core_config("salt_field")], $bnetAccountInfo[get_core_config("verifier_field")])) {
                    error_msg($bnetAccountInfo[get_core_config("salt_field")]);
                    return false;
                }

                $game_account_name = $bnetAccountInfo['id'] . '#1';
                list($salt, $verifier) = getRegistrationData($game_account_name, substr($_POST['password'], 0, 16));

                $queryBuilder = database::$auth->createQueryBuilder();
                $queryBuilder->update('account')
                    ->set(get_core_config("salt_field"), ':salt')
                    ->set(get_core_config("verifier_field"), ':verifier')
                    ->where('email = :email')
                    ->setParameter('salt', $salt)
                    ->setParameter('verifier', $verifier)
                    ->setParameter('email', $bnetAccountInfo['email']);
                $queryBuilder->executeQuery();

                list($salt, $verifier) = getRegistrationDataBnetV2($bnetAccountInfo['email'], $_POST['password']);

                $queryBuilder = database::$auth->createQueryBuilder();
                $queryBuilder->update('battlenet_accounts')
                    ->set('srp_version', 2)
                    ->set(get_core_config("salt_field"), ':salt')
                    ->set(get_core_config("verifier_field"), ':verifier')
                    ->where('id = :id')
                    ->setParameter('salt', $salt)
                    ->setParameter('verifier', $verifier)
                    ->setParameter('id', $bnetAccountInfo['id']);
                $queryBuilder->executeQuery();
            }
        }

        success_msg(lang('password_changed'));
        return true;
    }

    /**
     * Change password for normal servers.
     * @return bool
     */
    public static function normal_changepass()
    {
        global $antiXss;

        if (!empty(get_config('disable_changepassword'))) {
            return false;
        }

        if ($_POST['submit'] != 'changepass' || empty($_POST['password']) || empty($_POST['old_password']) || empty($_POST['repassword']) || empty($_POST['username'])) {
            return false;
        }

        if (!captcha_validation()) {
            return false;
        }

        if ($_POST['password'] != $_POST['repassword']) {
            error_msg(lang('passwords_not_equal'));
            return false;
        }

        if (!(strlen($_POST['password']) >= 4 && strlen($_POST['password']) <= 16)) {
            error_msg(lang('passwords_length'));
            return false;
        }

        $userinfo = self::get_user_by_username(strtoupper($_POST['username']));
        if (empty($userinfo['username'])) {
            error_msg(lang('username_not_correct'));
            return false;
        }

        if (empty(get_config('srp6_support'))) {
            $Old_hashed_pass = strtoupper(sha1(strtoupper($userinfo['username'] . ':' . $_POST['old_password'])));
            $hashed_pass = strtoupper(sha1(strtoupper($userinfo['username'] . ':' . $_POST['password'])));
            if (strtoupper($userinfo['sha_pass_hash']) != $Old_hashed_pass) {
                error_msg(lang('old_password_not_valid'));
                return false;
            }

            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->update('account')
                ->set('sha_pass_hash', ':sha_pass_hash')
                ->set('sessionkey', '')
                ->set('v', '')
                ->set('s', '')
                ->where('id = :id')
                ->setParameter('sha_pass_hash', $antiXss->xss_clean($hashed_pass))
                ->setParameter('id', $userinfo['id']);
            $queryBuilder->executeQuery();
        } else {
            if (!verifySRP6($userinfo['username'], $_POST['old_password'], $userinfo[get_core_config("salt_field")], $userinfo[get_core_config("verifier_field")])) {
                error_msg(lang('old_password_not_valid'));
                return false;
            }

            list($salt, $verifier) = getRegistrationData(strtoupper($userinfo['username']), $_POST['password']);

            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->update('account')
                ->set(get_core_config("salt_field"), ':salt')
                ->set(get_core_config("verifier_field"), ':verifier')
                ->where('id = :id')
                ->setParameter('salt', $salt)
                ->setParameter('verifier', $verifier)
                ->setParameter('id', $userinfo['id']);
            $queryBuilder->executeQuery();
        }

        success_msg(lang('password_changed'));
        return true;
    }

    /**
     * Change password for normal servers.
     * @return bool
     */
    public static function restorepassword()
    {
        global $antiXss;
        if ($_POST['submit'] != 'restorepassword') {
            return false;
        }

        if (get_config('battlenet_support') && empty($_POST['email'])) {
            return false;
        } elseif (!get_config('battlenet_support') && empty($_POST['username'])) {
            return false;
        }

        if (!captcha_validation()) {
            return false;
        }

        if (get_config('battlenet_support')) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                error_msg(lang('use_valid_email'));
                return false;
            }

            $userinfo = self::get_user_by_email(strtoupper($_POST['email']));
            if (empty($userinfo['email'])) {
                error_msg(lang('email_not_correct'));
                return false;
            }

            $field_acc = $userinfo['email'];
        } else {
            if (!preg_match('/^[0-9A-Z-_]+$/', strtoupper($_POST['username']))) {
                error_msg(lang('use_valid_username'));
                return false;
            }

            $userinfo = self::get_user_by_username(strtoupper($_POST['username']));
            if (empty($userinfo['email'])) {
                error_msg(lang('username_not_correct'));
                return false;
            }

            $field_acc = $userinfo['username'];
        }

        if (!isset($userinfo['restore_key'])) {
            self::add_password_key_to_acctbl();
        }

        $restore_key = strtolower(md5(time() . mt_rand(1000, 9999)) . mt_rand(10000, 99999));

        $queryBuilder = database::$auth->createQueryBuilder();
        $queryBuilder->update('account')
            ->set('restore_key', ':restore_key')
            ->where('id = :id')
            ->setParameter('restore_key', $antiXss->xss_clean($restore_key))
            ->setParameter('id', $userinfo['id']);
        $queryBuilder->executeQuery();

        $restorepass_URL = get_config('baseurl') . '/index.php?restore=' . strtolower($field_acc) . '&key=' . $restore_key;
        $message = "For restore you game account open <a href='$restorepass_URL' target='_blank'>this link</a>: <BR>$restorepass_URL";
        send_phpmailer(strtolower($userinfo['email']), lang('restore_account_password'), $message);
        success_msg(lang('check_your_email'));
        return true;
    }

    public static function restorepassword_setnewpw($user_data, $restore_key)
    {
        global $antiXss;
        if (empty($user_data) || empty($restore_key)) {
            return false;
        }

        if ($restore_key == 1 || strlen($restore_key) < 30) {
            return false;
        }

        if (get_config('battlenet_support')) {
            if (!filter_var($user_data, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $userinfo = self::get_user_by_email(strtoupper($user_data));
        } else {
            if (!preg_match('/^[0-9A-Z-_]+$/', strtoupper($user_data))) {
                error_msg(lang('use_valid_username'));
                return false;
            }

            $userinfo = self::get_user_by_username(strtoupper($user_data));
        }

        if (empty($userinfo['email'])) {
            return false;
        }

        if ($userinfo['restore_key'] != $restore_key) {
            return false;
        }

        $new_password = generateRandomString(12);

        if (get_config('battlenet_support')) {
            $message = 'Your new account information : <br>Email: ' . strtolower($userinfo['email']) . '<br>Password: ' . $new_password;
            if (empty(get_config('srp6_support'))) {
                $hashed_pass = strtoupper(sha1(strtoupper($userinfo['username'] . ':' . $new_password)));

                $queryBuilder = database::$auth->createQueryBuilder();
                $queryBuilder->update('account')
                    ->set('sha_pass_hash', ':sha_pass_hash')
                    ->set('sessionkey', '')
                    ->set('v', '')
                    ->set('s', '')
                    ->set('restore_key', '1')
                    ->where('id = :id')
                    ->setParameter('sha_pass_hash', $antiXss->xss_clean($hashed_pass))
                    ->setParameter('id', $userinfo['id']);
                $queryBuilder->executeQuery();
            } else {
                list($salt, $verifier) = getRegistrationData(strtoupper($userinfo['username']), $new_password);

                $queryBuilder = database::$auth->createQueryBuilder();
                $queryBuilder->update('account')
                    ->set(get_core_config("salt_field"), ':salt')
                    ->set(get_core_config("verifier_field"), ':verifier')
                    ->set('restore_key', '1')
                    ->where('id = :id')
                    ->setParameter('salt', $salt)
                    ->setParameter('verifier', $verifier)
                    ->setParameter('id', $userinfo['id']);
                $queryBuilder->executeQuery();
            }

            $bnet_hashed_pass = strtoupper(bin2hex(strrev(hex2bin(strtoupper(hash('sha256', strtoupper(hash('sha256', strtoupper($userinfo['email'])) . ':' . strtoupper($new_password))))))));

            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->update('battlenet_accounts')
                ->set('sha_pass_hash', ':sha_pass_hash')
                ->where('id = :id')
                ->setParameter('sha_pass_hash', $antiXss->xss_clean($bnet_hashed_pass))
                ->setParameter('id', $userinfo['battlenet_account']);
            $queryBuilder->executeQuery();
        } else {
            $message = 'Your new account information : <br>Username: ' . strtolower($userinfo['username']) . '<br>Password: ' . $new_password;
            if (empty(get_config('soap_for_register'))) {
                if (empty(get_config('srp6_support'))) {
                    $hashed_pass = strtoupper(sha1(strtoupper($userinfo['username'] . ':' . $new_password)));

                    $queryBuilder = database::$auth->createQueryBuilder();
                    $queryBuilder->update('account')
                        ->set('sha_pass_hash', ':sha_pass_hash')
                        ->set('sessionkey', '')
                        ->set('v', '')
                        ->set('s', '')
                        ->set('restore_key', '1')
                        ->where('id = :id')
                        ->setParameter('sha_pass_hash', $antiXss->xss_clean($hashed_pass))
                        ->setParameter('id', $userinfo['id']);
                    $queryBuilder->executeQuery();
                } else {
                    list($salt, $verifier) = getRegistrationData(strtoupper($userinfo['username']), $new_password);

                    $queryBuilder = database::$auth->createQueryBuilder();
                    $queryBuilder->update('account')
                        ->set(get_core_config("salt_field"), ':salt')
                        ->set(get_core_config("verifier_field"), ':verifier')
                        ->set('restore_key', '1')
                        ->where('id = :id')
                        ->setParameter('salt', $salt)
                        ->setParameter('verifier', $verifier)
                        ->setParameter('id', $userinfo['id']);
                    $queryBuilder->executeQuery();
                }
            } else {
                $command = str_replace('{USERNAME}', $antiXss->xss_clean(strtoupper($userinfo['username'])), get_config('soap_cp_command'));
                $command = str_replace('{PASSWORD}', $antiXss->xss_clean($new_password), $command);
                if (RemoteCommandWithSOAP($command)) {
                    success_msg(lang('password_changed'));

                    $queryBuilder = database::$auth->createQueryBuilder();
                    $queryBuilder->update('account')
                        ->set('restore_key', '1')
                        ->where('id = :id')
                        ->setParameter('id', $userinfo['id']);
                    $queryBuilder->executeQuery();
                } else {
                    error_msg(lang('error_try_again'));
                    return false;
                }
            }
        }

        send_phpmailer(strtolower($userinfo['email']), 'New Account Password', $message);
        success_msg(lang('check_your_email'));
        return false;
    }

    public static function check_email_exists($email)
    {
        if (!empty($email)) {
            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->select('id')
                ->from('account')
                ->where('email = :email')
                ->setParameter('email', strtoupper($email));

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (empty($datas[0])) {
                return true;
            }
        }
        return false;
    }

    public static function get_user_by_email($email)
    {
        if (!empty($email)) {
            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->select('*')
                ->from('account')
                ->where('email = :email')
                ->setParameter('email', strtoupper($email));

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]['username'])) {
                return $datas[0];
            }
        }
        return false;
    }

    public static function get_bnetaccount_by_email($email)
    {
        if (!empty($email)) {
            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->select('*')
                ->from('battlenet_accounts')
                ->where('email = :email')
                ->setParameter('email', strtoupper($email));

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (!empty($datas[0]['email'])) {
                return $datas[0];
            }
        }
        return false;
    }

    public static function get_user_by_username($username)
    {
        if (!empty($username)) {
            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->select('*')
                ->from('account')
                ->where('username = :username')
                ->setParameter('username', strtoupper($username));

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();
            if (!empty($datas[0]['username'])) {
                return $datas[0];
            }
        }
        return false;
    }

    /**
     * @param $username
     * @return bool
     */
    public static function check_username_exists($username)
    {
        if (!empty($username)) {
            $queryBuilder = database::$auth->createQueryBuilder();
            $queryBuilder->select('id')
                ->from('account')
                ->where('username = :username')
                ->setParameter('username', strtoupper($username));

            $statement = $queryBuilder->executeQuery();
            $datas = $statement->fetchAllAssociative();

            if (empty($datas[0])) {
                return true;
            }
        }
        return false;
    }

    public static function get_online_players($realmID)
    {
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
        $queryBuilder->select('name, race, class, gender, level')
            ->from('characters')
            ->where('online = :online')
            ->orderBy('level', 'DESC')
            ->setMaxResults(49)
            ->setParameter('online', 1);

        $statement = $queryBuilder->executeQuery();
        $datas = $statement->fetchAllAssociative();

        if (!empty($datas[0]['name'])) {
            return $datas;
        }
        return false;
    }

    public static function get_online_players_count($realmID)
    {
        $queryBuilder = database::$chars[$realmID]->createQueryBuilder();
        $queryBuilder->select('COUNT(*)')
            ->from('characters')
            ->where('online = :online')
            ->setParameter('online', 1);
        $statement = $queryBuilder->executeQuery();
        $datas = $statement->fetchOne();
        if (!empty($datas)) {
            return $datas;
        }
        return 0;
    }

    public static function add_password_key_to_acctbl()
    {
        database::$auth->executeQuery("ALTER TABLE `account` ADD COLUMN `restore_key` varchar(255) NULL DEFAULT '1';");
        return true;
    }

    /**
     * Enable 2fa
     * @return bool
     */
    public static function tfa_enable()
    {
        global $antiXss;

        if (empty(get_config('2fa_support'))) {
            return false;
        }

        if (empty($_POST['submit']) || $_POST['submit'] != 'etfa' || empty($_POST['email']) || (empty(get_config('battlenet_support')) && empty($_POST['username']))) {
            return false;
        }

        if (!captcha_validation()) {
            return false;
        }

        $userinfo = self::get_user_by_email(strtoupper($_POST['email']));
        if (empty($userinfo['id'])) {
            error_msg(lang('account_is_not_valid'));
            return false;
        }

        if (empty(get_config('battlenet_support')) && strtolower($userinfo['username']) != strtolower($_POST['username'])) {
            error_msg(lang('account_is_not_valid'));
            return false;
        }

        $verify_key = md5(strtolower($userinfo['email']) . "_" . time() . rand(1, 999999));

        if (!isset($userinfo['restore_key'])) {
            self::add_password_key_to_acctbl();
        }

        $queryBuilder = database::$auth->createQueryBuilder();
        $queryBuilder->update('account')
            ->set('restore_key', ':restore_key')
            ->where('id = :id')
            ->setParameter('restore_key', $antiXss->xss_clean($verify_key))
            ->setParameter('id', $userinfo['id']);
        $queryBuilder->executeQuery();

        $account = $userinfo['email'];
        if (empty(get_config('battlenet_support'))) {
            $account = $userinfo['username'];
        }

        $restorepass_URL = get_config('baseurl') . '/index.php?enabletfa=' . strtolower($verify_key) . '&account=' . strtolower($account);
        $message = "Hey, to enable Two-Factor Authentication (2FA), Please open  <a href='$restorepass_URL' target='_blank'>this link</a>: <BR>$restorepass_URL";
        send_phpmailer(strtolower($userinfo['email']), 'Enable Account 2FA', $message);
        success_msg(lang('check_your_email'));
        return true;
    }

    public static function account_set_2fa($verify_key, $account)
    {
        global $antiXss;

        if (empty(get_config('2fa_support'))) {
            return false;
        }

        if (empty($verify_key) || empty($account)) {
            return false;
        }

        if ($verify_key == 1 || strlen($verify_key) < 30) {
            return false;
        }

        $acc_name = "";
        if (get_config('battlenet_support')) {
            if (!filter_var($account, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            $userinfo = self::get_user_by_email(strtoupper($account));
            $acc_name = $userinfo['email'];
        } else {
            if (!preg_match('/^[0-9A-Z-_]+$/', strtoupper($account))) {
                return false;
            }

            $userinfo = self::get_user_by_username(strtoupper($account));
            $acc_name = $userinfo['username'];
        }

        if (empty($userinfo['email'])) {
            return false;
        }

        if ($userinfo['restore_key'] != $verify_key) {
            return false;
        }

        $ga = new PHPGangsta_GoogleAuthenticator();
        $tfa_key = $ga->createSecret();

        $queryBuilder = database::$auth->createQueryBuilder();
        $queryBuilder->update('account')
            ->set('restore_key', '1')
            ->where('id = :id')
            ->setParameter('id', $userinfo['id']);
        $queryBuilder->executeQuery();

        $command = str_replace('{USERNAME}', $antiXss->xss_clean(strtoupper($userinfo['username'])), get_config('soap_2d_command'));
        RemoteCommandWithSOAP($command);
        $command = str_replace('{USERNAME}', $antiXss->xss_clean(strtoupper($userinfo['username'])), get_config('soap_2e_command'));
        $command = str_replace('{SECRET}', $tfa_key, $command);
        RemoteCommandWithSOAP($command);

        $acc_name = str_replace('-', '', $acc_name);
        $acc_name = str_replace('.', '', $acc_name);
        $acc_name = str_replace('_', '', $acc_name);
        $acc_name = str_replace('@', '', $acc_name);

        $message = 'Two-Factor Authentication (2FA) enabled on your account.<br>Please scan the barcode with Google Authenticator.<BR>';
        $message .= '<img src="' . $ga->getQRCodeGoogleUrl($acc_name, $tfa_key) . '"><BR>';
        $message .= 'or you can add this code to Google Authenticator: <B>' . $tfa_key . '</B>.<BR>';

        send_phpmailer(strtolower($userinfo['email']), 'Account 2FA enabled', $message);
        success_msg(lang('check_your_email'));
    }
}
