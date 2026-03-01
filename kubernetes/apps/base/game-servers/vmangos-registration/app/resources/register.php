<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $email = "";
$username_err = $password_err = $email_err = "";
$regname = 'soapadmin';
$regpass = 'soapadmin';
$host = "vmangos-soap";
$soapport = 7878;
$command = "account create {USERNAME} {PASSWORD}";
$result = "";

// Define SOAP client
$client = new SoapClient(NULL,
array(
    "location" => "http://$host:$soapport",
    "uri" => "urn:MaNGOS",
    "style" => SOAP_RPC,
    'login' => $regname,
    'password' => $regpass
));

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Required";
	} elseif(!ctype_alnum($_POST["username"])) {
		$username_err = "Must be letters and numbers only";
    } elseif(strlen(trim($_POST["username"])) > 16){
        $username_err = "Too long";
    } elseif(strlen(trim($_POST["username"])) < 4){
        $username_err = "Too short";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM account WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = htmlspecialchars(trim($_POST["username"]));

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Taken";
                } else{
                    $username = htmlspecialchars(trim($_POST["username"]));
                }
            } else{
                echo "Error";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if(empty(trim($_POST["password"])))
	{
        $password_err = "Required";
    }
	if(strlen(trim($_POST["password"])) > 16)
	{
        $password_err = "Too long";
    }
	elseif(strlen(trim($_POST["password"])) < 6)
	{
        $password_err = "Too short";
    }
	else
	{
        $password = htmlspecialchars(trim($_POST["password"]));
    }

	// Validate passver
    if(empty(trim($_POST["passver"])))
	{
        $passver_err = "Required";
    }
	if(strlen(trim($_POST["passver"])) != strlen(trim($_POST["password"])))
	{
        $password_err = "Mismatch";
    }

    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Invalid";
    } else{
        $email = htmlspecialchars(trim($_POST["email"]));
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($passver_err) && empty($email_err)){
		$command = str_replace('{USERNAME}', strtoupper($_POST["username"]), $command);
		$command = str_replace('{PASSWORD}', strtoupper($_POST["password"]), $command);
        try {
        $result = $client->executeCommand(new SoapParam($command, "command"));
		}
		catch (Exception $e) {
		echo $e;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2><center>Sign Up</center></h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<table width="100%">
				<col style="width:25%">
				<col style="width:50%">
				<col style="width:25%">
			<tr><td>
				<label>Username:</label>
			</td><td>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            </td><td>
				<span class="invalid-feedback"><?php echo $username_err; ?></span>
            </td></tr>
			<tr><td>
                <label>Password:</label>
			</td><td>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
            </td><td>
				<span class="invalid-feedback"><?php echo $password_err; ?></span>
            </td></tr>
			<tr><td>
                <label>Verify:</label>
			</td><td>
                <input type="password" name="passver" class="form-control <?php echo (!empty($passver_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $pass_ver; ?>">
            </td><td>
				<span class="invalid-feedback"><?php echo $password_err; ?></span>
            </td></tr>
			<tr><td>
                <label>Email:</label>
			</td><td>
                <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
            </td><td>
				<span class="invalid-feedback"><?php echo $email_err; ?></span>
			</td></tr>
			</table>
            <div class="form-group">
				<input type="submit" class="clicker" value="Submit">
			<table>
            <h3>Already have an account?</h3>
	<tr><p><h4><input type="button" class="clicker" onclick="location.href='login.php';" value="Login here" /></p></tr></h4></p></tr>
			<tr><span class="invalid-feedback"><?php echo $result; ?></span></tr>
			</table>
			</div>
        </form>
    </div>
</body>
</html>
