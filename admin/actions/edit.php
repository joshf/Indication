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

$idtoedit = mysqli_real_escape_string($con, $_POST["idtoedit"]);

//Set variables
$newname = mysqli_real_escape_string($con, $_POST["name"]);
$newid = mysqli_real_escape_string($con, $_POST["id"]);
$newurl = mysqli_real_escape_string($con, $_POST["url"]);
$newcount = mysqli_real_escape_string($con, $_POST["count"]);

//Failsafes
if (empty($newname) || empty($newid) || empty($newurl)) {
    header("Location: ../edit.php?id=$idtoedit&error=emptyfields");
    exit;
}

//Make sure a password is set if the checkbox was enabled
if (isset($_POST["passwordprotectstate"])) {
    if (empty($_POST["password"])) {
        header("Location: ../edit.php?id=$idtoedit&error=emptypassword");
        exit;
    } 
    $getprotectinfo = mysqli_query($con, "SELECT `password` FROM `Data` WHERE `id` = \"$idtoedit\"");
    $getprotectinforesult = mysqli_fetch_assoc($getprotectinfo); 
    $inputtedpassword = mysqli_real_escape_string($con, $_POST["password"]);
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

mysqli_query($con, "UPDATE `Data` SET `name` = \"$newname\", `id` = \"$newid\", `url` = \"$newurl\", `count` = \"$newcount\", `protect` = \"$protect\", `password` = \"$password\", `showads` = \"$showads\" WHERE `id` = \"$idtoedit\"");

mysqli_close($con);

header("Location: ../index.php");

exit;

?>