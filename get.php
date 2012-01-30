<?php
//SHTracker, Copyright Josh Fradley 2012
ob_start();
?>
<html>
<head>
<title>SHTracker</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta name="robots" content="noindex, nofollow">
</head>
<body>
<?php

//Accept POST or GET
if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} else {
    $id = mysql_real_escape_string($_POST["id"]);
}

//Check ID is not blank
if (empty($id)) {
    die("<h1>SHTracker: Error</h1><p>ID cannot be blank.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}

//Prevent some injection attacks, is this neccesary?
if (!preg_match("/^[a-zA-Z0-9.]{1,}$/", $id)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>"); 
}

//Connect to database
require_once("config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

//If ID exists update count or die
$getinfo = "SELECT * FROM Data WHERE id = \"$id\"";
$result = mysql_query($getinfo) or die ("Error");
$num = mysql_num_rows($result); 
if ($num != 0) { 
    mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
} else { 
    die("<h1>SHTracker: Error</h1><p>ID <strong>$id</strong> does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}

//Get download URL
$geturl = mysql_query("SELECT * FROM Data WHERE id = \"$id\"");
$url = mysql_fetch_row($geturl);

mysql_close($con);

//Check whether wait is enabled
if (WAIT_STATE == "Enabled" ) {
    echo "<p>" . WAIT_MESSAGE . "</p><p>" . WAIT_AD_CODE . "</p><hr /><p><a href=\"" . $url["2"] . "\">Continue to download</a></p></body></html>";
    exit;
}

//Redirect user to the download
header("Location: " . $url["2"] . "");
ob_end_flush();
exit;
?>