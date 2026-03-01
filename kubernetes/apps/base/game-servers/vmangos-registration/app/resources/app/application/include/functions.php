<?php
/**
 * @author Amin Mahmoudi (MasterkinG)
 * @copyright    Copyright (c) 2019 - 2024, MasterkinG32. (https://masterking32.com)
 * @link    https://masterking32.com
 **/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_msg = "";
$success_msg = "";

function getIP()
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        // IP passed from Cloudflare
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function get_config($name)
{
    global $config;
    if (!empty($name)) {
        if (isset($config[$name])) {
            return $config[$name];
        }
    }
    return false;
}

function get_core_config($name)
{
    global $core_config;
    if (!empty($name)) {
        if (isset($core_config[$name])) {
            return $core_config[$name];
        }
    }
    return false;
}

function error_msg($input = false)
{
    global $error_error;
    if (!empty($error_error)) {
        echo "<p class=\"alert alert-danger\">$error_error</p>";
    } elseif (!empty($input)) {
        $error_error = $input;
    }
}

function success_msg($input = false)
{
    global $success_msg;
    if (!empty($success_msg)) {
        echo "<p class=\"alert alert-success\">$success_msg</p>";
    } elseif (!empty($input)) {
        $success_msg = $input;
    }
}

function GetRaceID($race)
{
    switch ($race) {
        case "HUMAN":
            return 1;
        case "ORC":
            return 2;
        case "DWARF":
            return 3;
        case "NIGHTELF":
            return 4;
        case "SCOURGE":
            return 5;
        case "TAUREN":
            return 6;
        case "GNOME":
            return 7;
        case "TROLL":
            return 8;
        case "BLOODELF":
            return 10;
        case "DRAENEI":
            return 11;
        default:
            exit("error");
    }
}

function GetClassID($class)
{
    switch ($class) {
        case "WARRIOR":
            return 1;
        case "PALADIN":
            return 2;
        case "HUNTER":
            return 3;
        case "ROGUE":
            return 4;
        case "PRIEST":
            return 5;
        case "DEATHKNIGHT":
            return 6;
        case "SHAMAN":
            return 7;
        case "MAGE":
            return 8;
        case "WARLOCK":
            return 9;
        case "DRUID":
            return 11;
        default:
            exit("<br>YOUR CHARACTER CLASS IS NOT BLIZZLIKE FOR 3.3.5a<br>");
    }
}

function get_human_time_from_sec($seconds)
{
    $interval = new DateInterval("PT{$seconds}S");
    $now = new DateTimeImmutable('now', new DateTimeZone('utc'));
    return $now->diff($now->add($interval))->format('%a:%h:%i');
}

function send_phpmailer($email, $subject, $message)
{
    try {
        $mail = new PHPMailer(true);
        if (get_config('debug_mode')) {
            $mail->SMTPDebug = 2;
        }
        $mail->isSMTP();
        $mail->Host = get_config('smtp_host');
        $mail->SMTPAuth = get_config('smtp_auth');
        $mail->Username = get_config('smtp_user');
        $mail->Password = get_config('smtp_pass');
        $mail->SMTPSecure = get_config('smtp_secure');
        $mail->Port = get_config('smtp_port');

        //Recipients
        $mail->setFrom(get_config('smtp_mail'));
        $mail->addAddress($email);     // Add a recipient
        $mail->addReplyTo(get_config('smtp_mail'));

        // Content
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    } catch (Exception $e) {
        if (get_config('debug_mode')) {
            echo 'Message: ' . $e->getMessage();
        }
    }
    return true;
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function RemoteCommandWithSOAP($COMMAND)
{
    if (empty($COMMAND)) {
        return false;
    }

    try {
        $conn = new SoapClient(NULL, array(
            'location' => 'http://' . get_config('soap_host') . ':' . get_config('soap_port') . '/',
            'uri' => get_config('soap_uri'),
            'style' => get_config('soap_style'),
            'login' => get_config('soap_username'),
            'password' => get_config('soap_password')
        ));
        $conn->executeCommand(new SoapParam($COMMAND, 'command'));
        unset($conn);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function validate_hcaptcha()
{
    if (empty($_POST['h-captcha-response'])) {
        return false;
    }

    try {
        $data = array(
            'secret' => get_config('captcha_secret'),
            'response' => $_POST['h-captcha-response']
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $responseData = json_decode($response);
        if ($responseData->success) {
            return true;
        }
    } catch (Exception $e) {
    }

    return false;
}

function validate_recaptcha()
{
    if (empty($_POST['g-recaptcha-response'])) {
        return false;
    }

    try {
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify?secret=" . get_config('captcha_secret') . "&response=" . $_POST['g-recaptcha-response']);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $responseData = json_decode($response, true);
        if ($responseData["success"] == true) {
            return true;
        }
    } catch (Exception $e) {
    }

    return false;
}

function validate_cloudflare_turnstile()
{
    if (empty($_POST['cf-turnstile-response'])) {
        return false;
    }

    try {
        $data = array(
            'secret' => get_config('captcha_secret'),
            'response' => $_POST['cf-turnstile-response'],
            'remoteip' => getIP()
        );
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://challenges.cloudflare.com/turnstile/v0/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($verify);
        $responseData = json_decode($response, true);
        if (isset($responseData["success"]) && $responseData["success"] == true) {
            return true;
        }
    } catch (Exception $e) {
    }

    return false;
}

function captcha_validation()
{
    if (empty(get_config('captcha_type')) && !empty($_POST['captcha']) && !empty($_SESSION['captcha'])) {
        if (strtolower($_SESSION['captcha']) != strtolower($_POST['captcha'])) {
            error_msg(lang('captcha_not_valid'));
            return false;
        }
        unset($_SESSION['captcha']);
    } else if (!empty(get_config('captcha_type')) && get_config('captcha_type') > 3) {
        return true;
    } elseif (!empty(get_config('captcha_type')) && get_config('captcha_type') == 1 && !empty($_POST['h-captcha-response'])) {
        if (!validate_hcaptcha()) {
            error_msg(lang('hcaptcha_not_valid'));
            return false;
        }
    } elseif (!empty(get_config('captcha_type')) && get_config('captcha_type') == 2 && !empty($_POST['g-recaptcha-response'])) {
        if (!validate_recaptcha()) {
            error_msg(lang('recaptcha_not_valid'));
            return false;
        }
    } elseif (!empty(get_config('captcha_type')) && get_config('captcha_type') == 3 && !empty($_POST['cf-turnstile-response'])) {
        if (!validate_cloudflare_turnstile()) {
            error_msg(lang('turnstile_not_valid'));
            return false;
        }
    } else {
        error_msg(lang('captcha_required'));
        return false;
    }

    return true;
}

function getCaptchaJS()
{
    if (!empty(get_config('captcha_type'))) {
        if (get_config('captcha_type') == 1) {
            return '<script src="https://hcaptcha.com/1/api.js?hl=' . get_config('captcha_language') . '" async defer></script><style>.h-captcha { display: inline-block;}</style>';
        } else if (get_config('captcha_type') == 2) {
            return '<script src="https://www.google.com/recaptcha/api.js?hl=' . get_config('captcha_language') . '" async defer></script><style>.g-recaptcha { display: inline-block;}</style>';
        } else if (get_config('captcha_type') == 3) {
            return '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script><style>.cf-turnstile { display: inline-block;}</style>';
        }
    }

    return '';
}

function GetCaptchaHTML($bootstrap = true)
{
    if (!empty(get_config('captcha_type'))) {
        if (get_config('captcha_type') == 1) {
            return '<div class="row text-center"><div class="col-md-12 text-center"><div class="h-captcha" data-sitekey="' . get_config('captcha_key') . '" style=\'margin:10px auto\'></div></div></div>';
        } else if (get_config('captcha_type') == 2) {
            return '<div class="row text-center"><div class="col-md-12 text-center"><div class="g-recaptcha" data-sitekey="' . get_config('captcha_key') . '" style=\'margin:10px auto\'></div></div></div>';
        } else if (get_config('captcha_type') == 3) {
            return '<div class="row text-center"><div class="col-md-12 text-center"><div class="cf-turnstile" data-sitekey="' . get_config('captcha_key') . '" style=\'margin:10px auto\'></div></div></div>';
        } else {
            return '';
        }
    }

    if(empty($bootstrap))
    {
        return '<div class="input-group"><input type="text" placeholder="' . lang('captcha') . '" name="captcha"></div><p style="text-align: center;margin-top: 10px;"><img src="' . user::$captcha->inline() . '" style="border - radius: 5px;"/></p>';
    }

    return '<div class="input-group"><span class="input-group">' . lang('captcha') . '</span><input type="text" class="form-control" placeholder="' . lang('captcha') . '" name="captcha"></div><p style="text-align: center;margin-top: 10px;"><img src="' . user::$captcha->inline() . '" style="border - radius: 5px;"/></p>';
}

// Its from TrinityCore/account-creator
function calculateSRP6Verifier($username, $password, $salt)
{
    // algorithm constants
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);

    // calculate first hash
    $h1 = sha1(strtoupper($username . ':' . $password), TRUE);

    // calculate second hash
    if(get_config('server_core') == 5)
    {
        $h2 = sha1(strrev($salt) . $h1, TRUE);  // From haukw
    } else {
        $h2 = sha1($salt . $h1, TRUE);
    }

    // convert to integer (little-endian)
    $h2 = gmp_import($h2, 1, GMP_LSW_FIRST);

    // g^h2 mod N
    $verifier = gmp_powm($g, $h2, $N);

    // convert back to a byte array (little-endian)
    $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);

    // pad to 32 bytes, remember that zeros go on the end in little-endian!
    $verifier = str_pad($verifier, 32, chr(0), STR_PAD_RIGHT);

    // done!
    if(get_config('server_core') == 5)
    {
        return strrev($verifier);  // From haukw
    } else {
        return $verifier;
    }
}

function calculateSRP6VerifierBnetV1($email, $password, $salt)
{
    // algorithm constants
    $g = gmp_init(2);
    $N = gmp_init('86A7F6DEEB306CE519770FE37D556F29944132554DED0BD68205E27F3231FEF5A10108238A3150C59CAF7B0B6478691C13A6ACF5E1B5ADAFD4A943D4A21A142B800E8A55F8BFBAC700EB77A7235EE5A609E350EA9FC19F10D921C2FA832E4461B7125D38D254A0BE873DFC27858ACB3F8B9F258461E4373BC3A6C2A9634324AB', 16);

    // calculate x
    $srpPassword = hash('sha256',strtoupper($username . ':' . $password), TRUE);
    $x = hash('sha256', $salt . $srpPassword, TRUE);

    // g^h2 mod N
    $verifier = gmp_powm($g, $x, $N);

    // convert back to a byte array (little-endian)
    $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);

    // pad to 256 bytes, remember that zeros go on the end in little-endian!
    $verifier = str_pad($verifier, 256, chr(0), STR_PAD_RIGHT);

    // done!
    return $verifier;
}

function calculateSRP6VerifierBnetV2($email, $password, $salt)
{
    // algorithm constants
    $g = gmp_init(2);
    $N = gmp_init('AC6BDB41324A9A9BF166DE5E1389582FAF72B6651987EE07FC3192943DB56050A37329CBB4A099ED8193E0757767A13DD52312AB4B03310DCD7F48A9DA04FD50E8083969EDB767B0CF6095179A163AB3661A05FBD5FAAAE82918A9962F0B93B855F97993EC975EEAA80D740ADBF4FF747359D041D5C33EA71D281E446B14773BCA97B43A23FB801676BD207A436C6481F1D2B9078717461A5B9D32E688F87748544523B524B0D57D5EA77A2775D2ECFA032CFBDBF52FB3786160279004E57AE6AF874E7303CE53299CCC041C7BC308D82A5698F3A8D0C38271AE35F8E9DBFBB694B5C803D89F7AE435DE236D525F54759B65E372FCD68EF20FA7111F9E4AFF73', 16);

    $srpPassword = strtoupper(hash('sha256', strtoupper($email), false)) . ":" . $password;

    // calculate x
    $xBytes = hash_pbkdf2("sha512", $srpPassword, $salt, 15000, 64, true);
    $x = gmp_import($xBytes, 1, GMP_MSW_FIRST);
    if (ord($xBytes[0]) & 0x80)
    {
        $fix = gmp_init('100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000', 16);
        $x = gmp_sub($x, $fix);
    }
    $x = gmp_mod($x, gmp_sub($N, 1));

    // g^h2 mod N
    $verifier = gmp_powm($g, $x, $N);

    // convert back to a byte array (little-endian)
    $verifier = gmp_export($verifier, 1, GMP_LSW_FIRST);

    // pad to 256 bytes, remember that zeros go on the end in little-endian!
    $verifier = str_pad($verifier, 256, chr(0), STR_PAD_RIGHT);

    // done!
    return $verifier;
}

// Returns SRP6 parameters to register this username/password combination with
function getRegistrationData($username, $password)
{
    // generate a random salt
    $salt = random_bytes(32);

    // calculate verifier using this salt
    $verifier = calculateSRP6Verifier($username, $password, $salt);

    // done - this is what you put in the account table!
    if(get_config('server_core') == 5)
    {
        $salt = strtoupper(bin2hex($salt));             // From haukw
        $verifier = strtoupper(bin2hex($verifier));     // From haukw
    }

    return array($salt, $verifier);
}

function getRegistrationDataBnetV1($email, $password)
{
    // generate a random salt
    $salt = random_bytes(32);

    // calculate verifier using this salt
    $verifier = calculateSRP6VerifierBnetV1($email, $password, $salt);

    // done - this is what you put in the battlenet_accounts table!
    return array($salt, $verifier);
}

function getRegistrationDataBnetV2($email, $password)
{
    // generate a random salt
    $salt = random_bytes(32);

    // calculate verifier using this salt
    $verifier = calculateSRP6VerifierBnetV2($email, $password, $salt);

    // done - this is what you put in the battlenet_accounts table!
    return array($salt, $verifier);
}

//From TrinityCore/AOWOW
function verifySRP6($user, $pass, $salt, $verifier)
{
    $g = gmp_init(7);
    $N = gmp_init('894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
    $x = gmp_import(
        sha1($salt . sha1(strtoupper($user . ':' . $pass), TRUE), TRUE),
        1,
        GMP_LSW_FIRST
    );
    $v = gmp_powm($g, $x, $N);
    return ($verifier === str_pad(gmp_export($v, 1, GMP_LSW_FIRST), 32, chr(0), STR_PAD_RIGHT));
}

function verifySRP6BnetV1($email, $pass, $salt, $verifier)
{
    return ($verifier === calculateSRP6VerifierBnetV1($email, $pass, $salt));
}

function verifySRP6BnetV2($email, $pass, $salt, $verifier)
{
    return ($verifier === calculateSRP6VerifierBnetV2($email, $pass, $salt));
}

// Get language text
function lang($val)
{
    global $language;
    if (!empty($language[$val])) {
        return $language[$val];
    }
    return "";
}

// Echo language text
function elang($val)
{
    echo lang($val);
}
