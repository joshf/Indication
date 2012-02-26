<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

if (!file_exists("../config.php")) {
    die("<html><head><title>SHTracker: Error</title><link rel=\"stylesheet\" type=\"text/css\" href=\"../style.css\" /></head><body><h1>SHTracker: Error</h1><p>SHTracker has not been installed. Please run install.php first!</p><hr /><p><a href=\"../install.php\">Go To Install</a></p></body></html>"); 
}

require_once("../config.php");

$password = ADMIN_PASSWORD;
$user = ADMIN_USER;

session_start();

//If cookie is set, skip login
if (isset($_COOKIE["shtrackerrememberme"])) {
    $_SESSION["is_logged_in"] = true;
}

if (isset($_POST["password"]) && isset($_POST["user"])) {
    if (sha1($_POST["password"]) == $password && $_POST["user"] == $user) {
        $_SESSION["is_logged_in"] = true;
            if (isset($_POST["rememberme"])) {
                setcookie("shtrackerrememberme", ADMIN_USER, time()+1209600);
            }
    } else {
        die("<html><head><title>SHTracker: Login</title><link rel=\"stylesheet\" type=\"text/css\" href=\"../style.css\" /></head><body><h1>SHTracker: Error</h1><p>Incorrect password or username...</p><hr /><p><a href=\"../admin\">&larr; Go Back</a></p></body></html>");
    }
} 

if(!isset($_SESSION["is_logged_in"])) {
?>
<html>
<head>
<title>SHTracker: Login</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<div id="loginform">
<h1>SHTracker: Login</h1>
<p>You need to login to continue.</p>
<form method="post">
User: <input type="text" name="user" /><br />
Password: <input type="password" name="password" />
<p><input type="checkbox" name="rememberme">Keep me logged in</p>
<p><input type="submit" name="submit" value="Login" /></p>
</form>
<p><a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>">&larr; Back to <?php echo WEBSITE; ?></a></p>
<small>SHTracker Copyright <a href="http://sidhosting.co.uk">Josh Fradley</a> <? echo date("Y"); ?></small>
</div>
</body>
</html>
<?php
} else {
    header("Location: index.php");
}
?>