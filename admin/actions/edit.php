<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html> 
<head>
<title>SHTracker: Download Edited</title>
<link rel="stylesheet" type="text/css" href="../../style.css" />
</head>
<body>
<?php

//Connect to database
require_once("../../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_POST["idtoedit"]);

//Set variables
$newname = mysql_real_escape_string($_POST["name"]);
$newid = mysql_real_escape_string($_POST["id"]);
$newurl = mysql_real_escape_string($_POST["url"]);
$newcount = mysql_real_escape_string($_POST["count"]);

//Check variables are not empty
if (empty($newname)) {
    die("<h1>SHTracker: Error</h1><p>Name is missing...</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>");
}
if (empty($newid)) {
    die("<h1>SHTracker: Error</h1><p>ID is missing...</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>");
}
if (empty($newurl)) {
    die("<h1>SHTracker: Error</h1><p>URL is missing...</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>");
}

//Prevent some injection attacks
if (!preg_match("/^[a-zA-Z0-9(). ]{1,}$/", $newname)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>"); 
}
if (!preg_match("/^[a-zA-Z0-9.]{1,}$/", $newid)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>"); 
}
if (!preg_match("/^[a-zA-Z0-9.:?=#\/_-]{1,}$/", $newurl)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>"); 
}
if (!preg_match("/^[0-9]{1,}$/", $newcount)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers.</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>"); 
}

mysql_query("UPDATE Data SET name = \"$newname\", id = \"$newid\", url = \"$newurl\", count = \"$newcount\" WHERE id = \"$idtoedit\"");

if (LOG_UPDATES_STATE == "Enabled") {
    $currentdate = date("d-m-y");
    $hasupdatebeenlogged = mysql_query("SELECT * FROM History WHERE dateupdated = \"$currentdate\" AND id = \"$newid\"");
    $getresult = mysql_fetch_assoc($hasupdatebeenlogged); 
    if ($getresult != 0) {
        mysql_query("UPDATE History SET count = \"$newcount\" WHERE dateupdated = \"$currentdate\" AND id = \"$newid\"");
    } else { 
        mysql_query("INSERT INTO History (dateupdated, id, count)
        VALUES (\"$dateupdated\",\"$newid\",\"$newcount\")");
    }
}

mysql_close($con);

?> 
<h1>SHTracker: Download Edited</h1>
<p>The download <strong><? echo $newname; ?></strong> has been edited successfully.</p>
<p><strong>Updated Details:</strong></p>
<ul>
<li>Name : <? echo $newname; ?></li>
<li>ID : <? echo $newid; ?></li>
<li>URL : <? echo $newurl; ?></li>
</ul>
<p><strong>Download link:</strong></p>
<p><? echo PATH_TO_SCRIPT; ?>/get.php?id=<? echo $newid; ?></p>
<hr />
<p><a href="../../admin">Back To Home</a></p>
</body>
</html>