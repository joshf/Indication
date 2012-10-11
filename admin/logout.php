<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();

unset($_SESSION["is_logged_in_" . $uniquekey . ""]);

if (isset($_COOKIE["shtrackerrememberme_" . $uniquekey . ""])) {
	setcookie("shtrackerrememberme_" . $uniquekey . "", "", time()-86400);
}

header("Location: login.php?logged_out=true");
exit;

?>