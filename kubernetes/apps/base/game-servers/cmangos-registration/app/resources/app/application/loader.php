<?php
/**
 * VMaNGOS Registration Portal - Application Loader
 **/

use voku\helper\AntiXSS;

ob_start();
session_start();

define('base_path', str_replace('application/loader.php', '', str_replace("\\", '/', __FILE__)));
define('app_path', str_replace('application/loader.php', '', str_replace("\\", '/', __FILE__)) . 'application/');

require app_path . 'vendor/autoload.php';
require_once app_path . 'config/config.php';
require_once app_path . 'include/core_handler.php';
require_once app_path . 'include/functions.php';

/* Configuration check */
if (!get_config('disable_changepassword') && get_config('soap_for_register')) {
    $config['disable_changepassword'] = true;
}

if (get_config('debug_mode')) {
    error_reporting(-1);
    ini_set('display_errors', 1);
} else {
    ini_set('display_errors', 0);
    if (version_compare(PHP_VERSION, '5.3', '>=')) {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
    } else {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
    }
}

require_once app_path . 'include/database.php';
require_once app_path . 'include/user.php';
require_once app_path . 'include/vote.php';
require_once app_path . 'include/status.php';

$languageName = strtolower(get_config('language'));
$languageFile = app_path . 'language/' . $languageName . '.php';
$language = [];

if (!preg_match('/^([a-z-]+)$/i', $languageName) || !file_exists($languageFile)) {
    exit('Language is not valid!');
}

if (!empty($_COOKIE['website_lang']) && !empty($config['supported_langs'][$_COOKIE['website_lang']]) && file_exists(app_path . 'language/' . strtolower($_COOKIE['website_lang']) . '.php')) {
    require_once app_path . 'language/' . strtolower($_COOKIE['website_lang']) . '.php';
} else {
    require_once $languageFile;
}

$antiXss = new AntiXSS();

if (!empty(get_config('script_version'))) {
    /* @TODO Add online version check! */
    if (version_compare(get_config('script_version'), '2.0.2', '<')) {
        exit('Use last version of config.php file.');
    }
} else {
    exit('Use last version of config.php file.');
}

if ($config['srp6_support'] && !extension_loaded('gmp')) {
    exit('Please enable GMP in your php.ini');
}

if ($config['captcha_type'] == 0 && !extension_loaded('gd')) {
    exit('Please enable gd or gd2 in your php.ini');
}

if ($config['soap_for_register'] && !extension_loaded('soap')) {
    exit('Please enable SOAP in your php.ini');
}

database::db_connect();
