<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
	die("Error: Config file not found! Please reinstall Indication.");
}

require_once("../config.php");

session_start();

unset($_SESSION["indication_user"]);

if (isset($_COOKIE["indication_user_rememberme"])) {
	setcookie("indication_user_rememberme", "", time()-86400);
}

header("Location: login.php?logged_out=true");

exit;

?>