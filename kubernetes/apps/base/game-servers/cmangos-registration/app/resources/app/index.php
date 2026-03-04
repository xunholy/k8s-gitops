<?php
/**
 * VMaNGOS Registration Portal
 **/

$osType = PHP_OS;
if (version_compare(PHP_VERSION, '8.0', '<')) {
    echo "<p>Your server needs to run PHP version 8.0.0 or higher.</p>";

    if (stripos($osType, 'win') !== false) {
        // Windows OS
        echo "<p>For Windows, you can download PHP from:</p>";
        echo "<ul>";
        echo "<li><a href='https://www.apachefriends.org/index.html' target='_blank'>XAMPP for Windows/Mac/Linux</a></li>";
        echo "<li><a href='http://www.wampserver.com/en/' target='_blank'>WAMP Server for Windows</a></li>";
        echo "</ul>";
    } elseif (stripos($osType, 'linux') !== false) {
        // Linux OS
        echo "<p>For Linux, you can follow these guides to install PHP:</p>";
        echo "<ul>";
        echo "<li><a href='https://computingforgeeks.com/how-to-install-php-8-2-on-debian/' target='_blank'>Installing PHP 8.2 on Debian 12/11/10</a></li>";
        echo "<li><a href='https://techvblogs.com/blog/install-php-8-2-ubuntu-22-04' target='_blank'>Installing PHP 8.2 on Ubuntu 22.04</a></li>";
        echo "<li><a href='https://linuxgenie.net/how-to-install-php-8-2-on-almalinux-8-9/' target='_blank'>Installing PHP 8.2 on AlmaLinux 8/9</a></li>";
        echo "</ul>";
    }

    exit();
}

require_once './application/loader.php';
user::post_handler();
vote::post_handler();
require_once base_path . 'template/' . get_config('template') . '/tpl/main.php';
