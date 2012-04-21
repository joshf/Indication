<?php

session_start();
if (!isset($_SESSION["is_logged_in"])) {
	header("Location: ../login.php");
	exit; 
}

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../../config.php");

if (!isset($_POST["command"])) {
	header("Location: ../settings.php");
}

//Make user confirm action with a password
$password = ADMIN_PASSWORD;

if (sha1($_POST["password"]) != $password) {
	header("Location: ../settings.php");
	exit;
}

$command = $_POST["command"];

if ($command == "Reset All Counts to Zero") {

	//Reset All Counts to Zero
	
	//Connect to database
	$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if (!$con) {
		die("Could not connect: " . mysql_error());
	}
	
	mysql_select_db(DB_NAME, $con);
	
	mysql_query("UPDATE Data SET count = \"0\"");
	
	mysql_close($con);	
	
	header("Location: ../settings.php");
	
} elseif ($command == "Delete All Downloads") {
	
	//Delete All Downloads
	
	//Connect to database
	$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if (!$con) {
		die("Could not connect: " . mysql_error());
	}
	
	mysql_select_db(DB_NAME, $con);
	
	mysql_query("DELETE FROM Data");
	
	mysql_close($con);
	
	header("Location: ../settings.php");
	
} else {
	header("Location: ../settings.php");
}
?>