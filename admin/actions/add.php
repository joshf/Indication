<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../../config.php")) {
    header("Location: ../../installer");
    exit;
}

require_once("../../config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
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

//Set variables
$name = mysql_real_escape_string($_POST["name"]);
$id = mysql_real_escape_string($_POST["id"]);
$url = mysql_real_escape_string($_POST["url"]);
$count = mysql_real_escape_string($_POST["count"]);

//Failsafes
if (empty($name) || empty($id) || empty($url)) {
    header("Location: ../add.php?error=emptyfields");
    exit;
}

//Check if ID exists
$checkid = mysql_query("SELECT `id` FROM `Data` WHERE `id` = \"$id\"");
$resultcheckid = mysql_fetch_assoc($checkid); 
if (mysql_num_rows($checkid) != 0) {
    header("Location: ../add.php?error=idexists");
    exit;
}

//Make sure a password is set if the checkbox was enabled
if (isset($_POST["passwordprotectstate"])) {
    $protect = "1";
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        header("Location: ../add.php?error=emptypassword");
        exit;
    }
    $hashedpassword = hash("sha256", $inputtedpassword);
    $password = hash("sha256", SALT . $hashedpassword);
} else {
    $protect = "0";
    $password = "";
}

if (isset($_POST["showadsstate"])) {
    $showads = "1";
} else {
    $showads = "0";
}

mysql_query("INSERT INTO `Data` (`name`, `id`, `url`, `count`, `protect`, `password`, `showads`)
VALUES (\"$name\",\"$id\",\"$url\",\"$count\",\"$protect\",\"$password\",\"$showads\")");

mysql_close($con);

header("Location: ../index.php");

exit;

?>