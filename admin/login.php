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
if (isset($_COOKIE["shtrackerrememberme" . $uniquekey . ""])) {
    $_SESSION["is_logged_in" . $uniquekey . ""] = true;
}

if (isset($_POST["password"]) && isset($_POST["user"])) {
    if (sha1($_POST["password"]) == $password && $_POST["user"] == $user) {
        $_SESSION["is_logged_in" . $uniquekey . ""] = true;
            if (isset($_POST["rememberme"])) {
                setcookie("shtrackerrememberme" . $uniquekey . "", ADMIN_USER, time()+1209600);
            }
    } else {
        header("Location: login.php?login_error=true");
    }
} 

if (!isset($_SESSION["is_logged_in" . $uniquekey . ""])) {
?>
<html>
<head>
<title>SHTracker: Login</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<div id="loginform">
<h1>SHTracker: Login</h1>
<?php 

if (isset($_GET["login_error"])) {
    echo "<div id=\"noticebad\"><p>Incorrect password or username!</p></div>";
}
if (isset($_GET["logged_out"])) {
    echo "<div id=\"noticegood\"><p>Logged out!</p></div>";
}

?>
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