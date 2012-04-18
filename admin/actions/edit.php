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
    die("<h1>SHTracker: Error</h1><p>Name is missing...</p><hr /><p><a href=\"../../admin\">&larr; Go Back</a></p></body></html>");
}
if (empty($newid)) {
    die("<h1>SHTracker: Error</h1><p>ID is missing...</p><hr /><p><a href=\"../../admin\">&larr; Go Back</a></p></body></html>");
}
if (empty($newurl)) {
    die("<h1>SHTracker: Error</h1><p>URL is missing...</p><hr /><p><a href=\"../../admin\">&larr; Go Back</a></p></body></html>");
}

//Prevent some injection attacks
if (!preg_match("/^[a-zA-Z0-9()._-]{1,}$/", $newname)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../../admin\">&larr; Go Back</a></p></body></html>"); 
}
if (!preg_match("/^[a-zA-Z0-9._-]{1,}$/", $newid)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../../admin\">&larr; Go Back</a></p></body></html>"); 
}
if (!preg_match("/^[a-zA-Z0-9.:?=#\/_-]{1,}$/", $newurl)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../../admin\">&larr; Go Back</a></p></body></html>"); 
}
if (!preg_match("/^[0-9]{1,}$/", $newcount)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers.</p><hr /><p><a href=\"../../admin\">&larr; Go Back</a></p></body></html>"); 
}

//Convert to lowercase
$newid = strtolower($newid);
$newurl = strtolower($newurl);

if (isset($_POST["passwordprotectstate"])) {
    $protect = "true";
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        die("<h1>SHTracker: Error</h1><p>Password is missing...</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
    }
    $password = sha1($inputtedpassword);
} else {
    $protect = "false";
    $password = "";
}

mysql_query("UPDATE Data SET name = \"$newname\", id = \"$newid\", url = \"$newurl\", count = \"$newcount\", protect = \"$protect\", password = \"$password\" WHERE id = \"$idtoedit\"");

mysql_close($con);

?> 
<h1>SHTracker: Download Edited</h1>
<p>The download <b><? echo $newname; ?></b> has been edited successfully.</p>
<p><b>Updated Details:</b></p>
<ul>
<li>Name : <? echo $newname; ?></li>
<li>ID : <? echo $newid; ?></li>
<li>URL : <? echo $newurl; ?></li>
</ul>
<p><b>Download link:</b></p>
<p><? echo PATH_TO_SCRIPT; ?>/get.php?id=<? echo $newid; ?></p>
<hr />
<p><a href="../../admin">Back To Home</a></p>
</body>
</html>