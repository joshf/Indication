<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$user = ADMIN_USER;
$password = ADMIN_PASSWORD;
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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
<label for="user">User</label><br />
<input type="text" name="user" id="user" style="width: 190px; text-align: center;" /><br />
<label for="password">Password</label><br />
<input type="password" name="password" id="password" style="width: 190px; text-align: center;" /><br />
<p><input type="checkbox" name="rememberme"> Remember Me?</p>
<p><input type="submit" value="Login" /></p>
</form>
<p class="loginfooter"><a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>">&larr; Back to <?php echo WEBSITE; ?></a></p>
<p class="loginfooter">Copyright Josh Fradley <? echo date("Y"); ?></p>
</div>
</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit;
}
?>