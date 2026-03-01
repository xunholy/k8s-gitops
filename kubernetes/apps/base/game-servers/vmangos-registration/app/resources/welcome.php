<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
		.wrapper{ width:350px; height:620px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
<table>
    <tr><h2>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to the server.</h2></tr>
	<tr><p>You have successfully registered. To connect, open your realmlist file and change it to read the following:</p></tr>
	<tr><p><strong>set realmlist wow.owncloud.ai</strong></p></tr>
	<tr><p>Be aware that this project is a fan server, and is not affiliated with Blizzard.</p></tr>
		<form>
		<input type="button" class="clicker" onclick="location.href='logout.php';" value="Logout"/>
	</form>
</table>
</div>
</body>
</html>
