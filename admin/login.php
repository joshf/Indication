<?php

//SHTracker, Copyright Josh Fradley 2012

//Dont bork if no config file is found
if (!file_exists("../config.php")) {
    die("<html><head><title>SHTracker: Error</title><link rel=\"stylesheet\" type=\"text/css\" href=\"../style.css\" /></head><body><h1>SHTracker: Error</h1><p>SHTracker has not been installed. Please run install.php first!</p><hr /><p><a href=\"../install.php\">Go To Install</a></p></body></html>"); 
}

require_once("../config.php");

$password = ADMIN_PASSWORD;
$user = ADMIN_USER;
$randomkey = RANDOM_KEY;

session_start();
if (!isset($_SESSION["loggedin" . $randomkey . ""])) {
    $_SESSION["loggedin" . $randomkey . ""] = false;
}

if (isset($_POST["password"]) && isset($_POST["user"])) {
    if (sha1($_POST["password"]) == $password && $_POST["user"] == $user) {
        $_SESSION["loggedin" . $randomkey . ""] = true;
    } else {
        die("<html><head><title>SHTracker: Login</title><link rel=\"stylesheet\" type=\"text/css\" href=\"../style.css\" /></head><body><h1>SHTracker: Error</h1><p>Incorrect password or username...</p><hr /><p><a href=\"../admin\">Go Back</a></p></body></html>");
    }
} 

if (!$_SESSION["loggedin" . $randomkey . ""]): ?>
<html>
<head>
<title>SHTracker: Login</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<h1>SHTracker: Login</h1>
<p>You need to login to continue.</p>
<form method="post">
User: <input type="text" name="user" /><br />
Password: <input type="password" name="password" />
<p><input type="submit" name="submit" value="Login" /></p>
</form>
</body>
</html>
<?php
exit();
endif;
?>