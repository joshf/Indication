<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../../config.php")) {
	die("Error: Config file not found! Please reinstall Indication.");
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
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_POST["idtoedit"]);

//Set variables
$newname = mysql_real_escape_string($_POST["name"]);
$newid = mysql_real_escape_string($_POST["id"]);
$newurl = mysql_real_escape_string($_POST["url"]);
$newcount = mysql_real_escape_string($_POST["count"]);

//Failsafes
if (empty($newname) || empty($newid) || empty($newurl)) {
    header("Location: ../edit.php?id=$idtoedit&error=emptyfields");
    exit;
}

//Make sure a password is set if the checkbox was enabled
if (isset($_POST["passwordprotectstate"])) {
    if (!isset($_POST["password"])) {
        header("Location: ../edit.php?id=$idtoedit&error=emptypassword");
        exit;
    } 
    $getprotectinfo = mysql_query("SELECT `password` FROM `Data` WHERE `id` = \"$idtoedit\"");
    $getprotectinforesult = mysql_fetch_assoc($getprotectinfo); 
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        $password = $getprotectinforesult["password"];
    } else {
        $hashedpassword = hash("sha256", $inputtedpassword);
        $password = hash("sha256", SALT . $hashedpassword);
    }
    $protect = "1";
} else {
    $protect = "0";
    $password = "";
}

if (isset($_POST["showadsstate"])) {
    $showads = "1";
} else {
    $showads = "0";
}

mysql_query("UPDATE `Data` SET `name` = \"$newname\", `id` = \"$newid\", `url` = \"$newurl\", `count` = \"$newcount\", `protect` = \"$protect\", `password` = \"$password\", `showads` = \"$showads\" WHERE `id` = \"$idtoedit\"");

mysql_close($con);

header("Location: ../index.php");

exit;

?>