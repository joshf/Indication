<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();

unset($_SESSION["is_logged_in_" . $uniquekey . ""]);

if (isset($_COOKIE["indicationrememberme_" . $uniquekey . ""])) {
	setcookie("indicationrememberme_" . $uniquekey . "", "", time()-86400);
}

header("Location: login.php?logged_out=true");

exit;

?>