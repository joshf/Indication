<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

session_start();
unset($_SESSION["is_logged_in"]);
if (isset($_COOKIE["shtrackerrememberme"])) {
	setcookie("shtrackerrememberme", "", time()-86400);
}
header("Location: login.php");
exit;

?>