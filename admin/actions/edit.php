<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

require_once("../../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: ../login.php");
    exit; 
}

if (!isset($_POST["id"])) {
    header("Location: ../../admin");
}

?>
<html> 
<head>
<title>SHTracker: Download/Link Edited</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" type="text/css" href="../../style.css" />
</head>
<body>
<?php

//Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_POST["idtoedit"]);

//Set variables
$newname = mysql_real_escape_string($_POST["downloadname"]);
$newid = mysql_real_escape_string($_POST["id"]);
$newurl = mysql_real_escape_string($_POST["url"]);
$newcount = mysql_real_escape_string($_POST["count"]);

//Convert to lowercase
$newid = strtolower($newid);
$newurl = strtolower($newurl);

//Failsafes
if (empty($newname) || empty($newid) || empty($newurl)) {
    die("<h1>SHTracker: Error</h1><p>One or more fields are empty.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

if (isset($_POST["passwordprotectstate"])) {
    if (!isset($_POST["password"])) {
        die("<h1>SHTracker: Error</h1><p>Password is missing...</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
    } 
    $getprotectinfo = mysql_query("SELECT password FROM Data WHERE id = \"$idtoedit\"");
    $getprotectinforesult = mysql_fetch_assoc($getprotectinfo); 
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        $password = $getprotectinforesult["password"];
    } else {
        $password = sha1($inputtedpassword);
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

mysql_query("UPDATE Data SET name = \"$newname\", id = \"$newid\", url = \"$newurl\", count = \"$newcount\", protect = \"$protect\", password = \"$password\", showads = \"$showads\" WHERE id = \"$idtoedit\"");

mysql_close($con);

?> 
<h1>SHTracker: Download/Link Edited</h1>
<p>The download/link <b><? echo $newname; ?></b> has been edited successfully.</p>
<p><b>Updated Details:</b></p>
<ul>
<li>Name : <? echo $newname; ?></li>
<li>ID : <? echo $newid; ?></li>
<li>URL : <? echo $newurl; ?></li>
</ul>
<p><b>Tracking Link:</b></p>
<p><? echo PATH_TO_SCRIPT; ?>/get.php?id=<? echo $newid; ?></p>
<hr />
<p><a href="../../admin">Back To Home</a></p>
</body>
</html>