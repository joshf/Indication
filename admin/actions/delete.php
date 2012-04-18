<?php

session_start();
if (!isset($_SESSION["is_logged_in"])) {
    header("Location: ../login.php");
    exit; 
}

if (!isset($_POST["id"])) {
    header("Location: ../../admin");
}

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

//Connect to database
require_once("../../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtodelete = mysql_real_escape_string($_POST["id"]);

mysql_query("DELETE FROM Data WHERE id = \"$idtodelete\"");

mysql_close($con);

header("Location: ../admin");

?>