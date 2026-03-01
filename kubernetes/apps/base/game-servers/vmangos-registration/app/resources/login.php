<?php
// Initialize the session
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Invalid";
	} elseif(!ctype_alnum($_POST["username"])) {
		$username_err = "Invalid";
    } else{
        $username = htmlspecialchars(trim($_POST["username"]));
    }

    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Invalid";
    } else{
        $password = htmlspecialchars(trim($_POST["password"]));
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){

			// Password is correct, so start a new session
			if (session_status() === PHP_SESSION_NONE) { session_start(); }

			// Store data in session variables
			$_SESSION["loggedin"] = true;
			$_SESSION["username"] = $username;

			// Redirect user to welcome page
			header("location: welcome.php");
		}

		else{
			echo "Oops! Something went wrong. Please try again later.";
		}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php
        if(!empty($login_err)){
            echo $login_err;
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<table width="100%">
				<col style="width:25%">
				<col style="width:50%">
				<col style="width:25%">
			<tr><td>
                <label>Username:</label>
			</td>
			<td>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            </td><td>
				<span class="invalid-feedback"><?php echo $username_err; ?></span>
            </td></tr>
			<tr><td>
                <label>Password:</label>
			</td>
			<td>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
            </td><td>
			<span class="invalid-feedback"><?php echo $password_err; ?></span>
            </td></tr>
			</table>
            <div class="form-group">
                <input type="submit" class="clicker" value="Login">
			<table><br/>
			<tr><br/></tr>
            <h3>New to the project?</h3>
	<tr><p><h4><input type="button" class="clicker" onclick="location.href='register.php';" value="Register here" /></p></tr></h4></p></tr>
			</table>
			</div>
        </form>
    </div>
</body>
</html>
