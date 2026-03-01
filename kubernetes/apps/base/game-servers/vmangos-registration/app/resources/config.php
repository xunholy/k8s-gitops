<?php
/* Database credentials */
define('DB_SERVER', 'vmangos-database:3306');
define('DB_USERNAME', 'mangos');
define('DB_PASSWORD', 'mangos');
define('DB_NAME', 'realmd');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
