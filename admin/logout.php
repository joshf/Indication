<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

session_start();
unset($_SESSION["is_logged_in"]);
session_destroy();
header("Location: login.php");

?>