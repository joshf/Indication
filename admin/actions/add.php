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

//Set variables
$name = mysqli_real_escape_string($con, $_POST["name"]);
$id = mysqli_real_escape_string($con, $_POST["id"]);
$url = mysqli_real_escape_string($con, $_POST["url"]);
$count = mysqli_real_escape_string($con, $_POST["count"]);

//Failsafes
if (empty($name) || empty($id) || empty($url)) {
    header("Location: ../add.php?error=emptyfields");
    exit;
}

//Check if ID exists
$checkid = mysqli_query($con, "SELECT `id` FROM `Data` WHERE `id` = \"$id\"");
$resultcheckid = mysqli_fetch_assoc($checkid); 
if (mysqli_num_rows($checkid) != 0) {
    header("Location: ../add.php?error=idexists");
    exit;
}

//Make sure a password is set if the checkbox was enabled
if (isset($_POST["passwordprotectstate"])) {
    $protect = "1";
    $inputtedpassword = mysqli_real_escape_string($con, $_POST["password"]);
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

mysqli_query($con, "INSERT INTO `Data` (`name`, `id`, `url`, `count`, `protect`, `password`, `showads`)
VALUES (\"$name\",\"$id\",\"$url\",\"$count\",\"$protect\",\"$password\",\"$showads\")");

mysqli_close($con);

header("Location: ../index.php");

exit;

?>