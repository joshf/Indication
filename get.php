<?php
//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)
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

//Connect to database
require_once("config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

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

//Prevent some injection attacks
if (!preg_match("/^[a-zA-Z0-9.]{1,}$/", $id)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>"); 
}

//If ID exists update count or else die
$getinfo = mysql_query("SELECT id, url FROM Data WHERE id = \"$id\"");
$getresult = mysql_fetch_row($getinfo); 
if ($getresult != 0) { 
    mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
} else { 
    die("<h1>SHTracker: Error</h1><p>ID <strong>$id</strong> does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}

mysql_close($con);

//Check whether wait is enabled
if (WAIT_STATE == "Enabled" ) {
    echo "<p>" . WAIT_MESSAGE . "</p><p>" . WAIT_AD_CODE . "</p><hr /><p><a href=\"" . $getresult["1"] . "\">Continue To Download</a></p></body></html>";
    exit;
}

//Redirect user to the download
header("Location: " . $getresult["1"] . "");
ob_end_flush();
exit;
?>