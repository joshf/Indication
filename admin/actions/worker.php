<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../../config.php")) {
    header("Location: ../../installer");
    exit;
}

require_once("../../config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: ../login.php");
    exit; 
}

if (!isset($_POST["id"])) {
    header("Location: ../../admin");
    exit;
}

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

$id = mysqli_real_escape_string($con, $_POST["id"]);

if (isset($_POST["action"])) {
	$action = $_POST["action"];
} else {
	die("Error: No action passed");
}

if ($action == "delete") {
	mysqli_query($con, "DELETE FROM `Data` WHERE `id` = \"$id\"");
} else {
    die("Error: Action not recognised!");
}

mysqli_close($con);

?>