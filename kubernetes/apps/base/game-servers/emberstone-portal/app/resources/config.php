<?php
/**
 * Emberstone Portal - Multi-Expansion Configuration
 *
 * Each expansion defines its own database, server core, theme, and content.
 * The selected expansion overrides the global $config before loader.php
 * processes core_handler.php and database.php, so all downstream code
 * (SRP6, registration, status) works unchanged.
 */

// ── Expansion Definitions ───────────────────────────────────────────────────
$expansions = [
    'vanilla' => [
        'label'          => 'Classic (1.12.1)',
        'page_title'     => 'Emberstone',
        'game_version'   => '1.12.1',
        'expansion'      => '0',
        'server_core'    => 5, // CMaNGOS: s/v fields, reversed salt in SRP6
        'realmlist'      => 'wow.owncloud.ai:3724',
        'soap_host'      => 'cmangos-soap',
        'soap_port'      => '7878',
        'soap_uri'       => 'urn:MaNGOS',
        'db_auth_host'   => 'cmangos-database',
        'db_auth_port'   => '3306',
        'db_auth_user'   => 'mangos',
        'db_auth_pass'   => 'mangos',
        'db_auth_dbname' => 'classicrealmd',
        'realmlists'     => [
            '1' => [
                'realmid'   => 1,
                'realmname' => 'Emberstone',
                'db_host'   => 'cmangos-database',
                'db_port'   => '3306',
                'db_user'   => 'mangos',
                'db_pass'   => 'mangos',
                'db_name'   => 'classiccharacters',
            ],
        ],
        'theme' => [
            'gold'          => '#C9A84C',
            'gold_bright'   => '#E8D48B',
            'gold_dark'     => '#8B6B1A',
            'gold_muted'    => '#A08A4E',
            'gold_glow'     => 'rgba(201, 168, 76, 0.5)',
            'gold_dim'      => 'rgba(201, 168, 76, 0.07)',
            'border'        => 'rgba(201, 168, 76, 0.08)',
            'border_hover'  => 'rgba(201, 168, 76, 0.20)',
            'border_active' => 'rgba(201, 168, 76, 0.40)',
            'particle_rgb'  => '201, 168, 76',
        ],
        'hero_logo'   => 'wow-classic-logo.png',
        'hero_badge'  => 'Classic',
        'hero_sub'    => 'Return to the world as it was meant to be. A Classic World of Warcraft realm with faithful 1.12.1 mechanics, authentic content progression, and a dedicated community.',
        'bg_glow_css' => '',
        'hero_filter' => '',
    ],
    'wotlk' => [
        'label'          => 'Wrath of the Lich King (3.3.5a)',
        'page_title'     => 'Emberstone',
        'game_version'   => '3.3.5a',
        'expansion'      => '2',
        'server_core'    => 1, // AzerothCore: salt/verifier fields
        'realmlist'      => 'wow.owncloud.ai:3725',
        'soap_host'      => 'azerothcore-soap',
        'soap_port'      => '7878',
        'soap_uri'       => 'urn:AC',
        'db_auth_host'   => 'azerothcore-database',
        'db_auth_port'   => '3306',
        'db_auth_user'   => 'root',
        'db_auth_pass'   => 'password',
        'db_auth_dbname' => 'acore_auth',
        'realmlists'     => [
            '1' => [
                'realmid'   => 1,
                'realmname' => 'Emberstone',
                'db_host'   => 'azerothcore-database',
                'db_port'   => '3306',
                'db_user'   => 'root',
                'db_pass'   => 'password',
                'db_name'   => 'acore_characters',
            ],
        ],
        'theme' => [
            'gold'          => '#7CB9E8',
            'gold_bright'   => '#B3D9F7',
            'gold_dark'     => '#1B5E8A',
            'gold_muted'    => '#5A9DC4',
            'gold_glow'     => 'rgba(124, 185, 232, 0.5)',
            'gold_dim'      => 'rgba(124, 185, 232, 0.07)',
            'border'        => 'rgba(124, 185, 232, 0.08)',
            'border_hover'  => 'rgba(124, 185, 232, 0.20)',
            'border_active' => 'rgba(124, 185, 232, 0.40)',
            'particle_rgb'  => '124, 185, 232',
        ],
        'hero_logo'   => 'wow-logo.png',
        'hero_badge'  => 'Wrath of the Lich King',
        'hero_sub'    => 'The Lich King awaits. A Wrath of the Lich King realm with 5x rates, AI companions, solo dungeons, and every quality-of-life feature to make your adventure legendary.',
        'bg_glow_css' => 'radial-gradient(ellipse 800px 600px at 25% 20%, rgba(56, 140, 204, 0.06) 0%, transparent 70%), radial-gradient(ellipse 600px 800px at 75% 80%, rgba(100, 60, 180, 0.05) 0%, transparent 70%), radial-gradient(ellipse 1200px 400px at 50% 50%, rgba(124, 185, 232, 0.03) 0%, transparent 60%)',
        'hero_filter' => 'hue-rotate(190deg) saturate(1.4) brightness(0.85)',
    ],
];

// ── Expansion Selection (GET > POST > cookie > default) ─────────────────────
$default_expansion = 'vanilla';
$selected_expansion = $default_expansion;

if (!empty($_GET['expansion']) && isset($expansions[$_GET['expansion']])) {
    $selected_expansion = $_GET['expansion'];
} elseif (!empty($_POST['expansion']) && isset($expansions[$_POST['expansion']])) {
    $selected_expansion = $_POST['expansion'];
} elseif (!empty($_COOKIE['selected_expansion']) && isset($expansions[$_COOKIE['selected_expansion']])) {
    $selected_expansion = $_COOKIE['selected_expansion'];
}

setcookie('selected_expansion', $selected_expansion, time() + 86400 * 30, '/');

// ── Shared Config (same across all expansions) ──────────────────────��───────
$config['baseurl']                = 'https://emberstone.owncloud.ai';
$config['language']               = 'english';
$config['supported_langs']        = ['english' => 'English'];
$config['debug_mode']             = false;
$config['patch_location']         = '';
$config['battlenet_support']      = false;
$config['srp6_support']           = true;
$config['srp6_version']           = 0;
$config['template']               = 'kaelthas';
$config['captcha_type']           = 0;
$config['captcha_key']            = '';
$config['captcha_secret']         = '';
$config['captcha_language']       = 'en';
$config['soap_for_register']      = false;
$config['soap_style']             = 'SOAP_RPC';
$config['soap_username']          = 'soapadmin';
$config['soap_password']          = 'soapadmin';
$config['soap_ca_command']        = 'account create {USERNAME} {PASSWORD}';
$config['smtp_host']              = '';
$config['smtp_port']              = 587;
$config['smtp_auth']              = false;
$config['smtp_user']              = '';
$config['smtp_pass']              = '';
$config['smtp_secure']            = 'tls';
$config['smtp_mail']              = '';
$config['vote_system']            = false;
$config['vote_sites']             = [];
$config['2fa_support']            = false;
$config['soap_2d_command']        = 'account set 2fa {USERNAME} off';
$config['soap_2e_command']        = 'account set 2fa {USERNAME} {SECRET}';
$config['disable_changepassword'] = true;
$config['disable_top_players']    = false;
$config['disable_online_players'] = false;
$config['multiple_email_use']     = true;
$config['script_version']         = '2.0.4';

// ── Override config with selected expansion ─��───────────────────────────────
$exp = $expansions[$selected_expansion];
$override_keys = [
    'page_title', 'game_version', 'expansion', 'server_core', 'realmlist',
    'soap_host', 'soap_port', 'soap_uri',
    'db_auth_host', 'db_auth_port', 'db_auth_user', 'db_auth_pass', 'db_auth_dbname',
    'realmlists',
];
foreach ($override_keys as $key) {
    $config[$key] = $exp[$key];
}

// Expose expansion metadata for templates
$config['_expansions']       = $expansions;
$config['_selected']         = $selected_expansion;
$config['_expansion_theme']  = $exp['theme'];
$config['_expansion_meta']   = $exp;

// ── Unified registration helper ─────────────────────────────────────────────
// Computes SRP6 salt+verifier for a specific core type and returns values
// ready for DB insertion (hex strings for CMaNGOS, binary for AzerothCore).
function portal_compute_srp6($username, $password, $server_core) {
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    $salt = random_bytes(32);
    $h1 = sha1(strtoupper($username . ':' . $password), true);
    if ($server_core == 5) {
        $h2 = sha1(strrev($salt) . $h1, true);
    } else {
        $h2 = sha1($salt . $h1, true);
    }
    $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);
    $verifier = gmp_powm($g, $h2, $N);
    $verifier = str_pad(gmp_export($verifier, 1, GMP_LSW_FIRST), 32, chr(0), STR_PAD_RIGHT);
    if ($server_core == 5) {
        return ['salt' => strtoupper(bin2hex($salt)), 'verifier' => strtoupper(bin2hex($verifier)), 'salt_field' => 's', 'verifier_field' => 'v'];
    }
    return ['salt' => $salt, 'verifier' => $verifier, 'salt_field' => 'salt', 'verifier_field' => 'verifier'];
}

// Creates account in a specific expansion's auth DB. Returns true on success.
function portal_create_mirror_account($exp_config, $username, $password, $email) {
    $conn = \Doctrine\DBAL\DriverManager::getConnection([
        'dbname' => $exp_config['db_auth_dbname'],
        'user'   => $exp_config['db_auth_user'],
        'password' => $exp_config['db_auth_pass'],
        'host'   => $exp_config['db_auth_host'],
        'port'   => $exp_config['db_auth_port'],
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
    ], new \Doctrine\DBAL\Configuration());
    // Skip if account already exists
    $existing = $conn->createQueryBuilder()
        ->select('id')->from('account')
        ->where('username = :u')->setParameter('u', strtoupper($username))
        ->executeQuery()->fetchOne();
    if ($existing) {
        $conn->close();
        return false;
    }
    $srp = portal_compute_srp6($username, $password, $exp_config['server_core']);
    $conn->insert('account', [
        'username'                  => strtoupper($username),
        $srp['salt_field']          => $srp['salt'],
        $srp['verifier_field']      => $srp['verifier'],
        'email'                     => strtoupper($email),
        'expansion'                 => $exp_config['expansion'],
    ]);
    $conn->close();
    return true;
}
