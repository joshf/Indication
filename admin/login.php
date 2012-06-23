<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$password = ADMIN_PASSWORD;
$user = ADMIN_USER;
$uniquekey = UNIQUE_KEY;

session_start();

//If cookie is set, skip login
if (isset($_COOKIE["shtrackerrememberme_" . $uniquekey . ""])) {
    $_SESSION["is_logged_in_" . $uniquekey . ""] = true;
}

if (isset($_POST["password"]) && isset($_POST["user"])) {
    if (sha1($_POST["password"]) == $password && $_POST["user"] == $user) {
        $_SESSION["is_logged_in_" . $uniquekey . ""] = true;
            if (isset($_POST["rememberme"])) {
                setcookie("shtrackerrememberme_" . $uniquekey . "", ADMIN_USER, time()+1209600);
            }
    } else {
        header("Location: login.php?login_error=true");
    }
} 

if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
?>
<html>
<head>
<title>SHTracker: Login</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<div id="loginform">
<h1>SHTracker</h1>
<?php 

if (isset($_GET["login_error"])) {
    echo "<p class=\"loginerror\">Incorrect password or username!</p>";
} elseif (isset($_GET["logged_out"])) {
    echo "<p class=\"loggedout\">You have been logged out!</p>";
} else {
    echo "<p style=\"font-size: 14px;\">Welcome back! Please login</p>";
}

?>
<form method="post">
Username:<br />
<input type="text" size="35" name="user" /><br />
Password:<br />
<input type="password" size="35" name="password" /><br />
<p><input type="checkbox" name="rememberme"> Keep me logged in</p>
<p><input class="loginbutton" type="submit" name="submit" value="Login" /></p>
</form>
<p class="loginfooter"><a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>">&larr; Back to <?php echo WEBSITE; ?></a></p>
<p class="loginfooter">Copyright Josh Fradley <? echo date("Y"); ?></p>
</div>
</body>
</html>
<?php
} else {
    header("Location: index.php");
}
?>