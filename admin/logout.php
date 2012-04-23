<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
unset($_SESSION["is_logged_in" . $uniquekey . ""]);
if (isset($_COOKIE["shtrackerrememberme" . $uniquekey . ""])) {
	setcookie("shtrackerrememberme" . $uniquekey . "", "", time()-86400);
}
header("Location: login.php?logged_out=true");
exit;

?>