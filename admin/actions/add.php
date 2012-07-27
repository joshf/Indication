<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: ../login.php");
    exit; 
}

?>
<html> 
<head>
<title>SHTracker: Download/Link Added</title>
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

//Set variables
$name = mysql_real_escape_string($_POST["downloadname"]);
$id = mysql_real_escape_string($_POST["id"]);
$url = mysql_real_escape_string($_POST["url"]);
$count = mysql_real_escape_string($_POST["count"]);

//Convert to lowercase
$id = strtolower($id);
$url = strtolower($url);

//Failsafes
if (empty($name) || empty($id) || empty($url)) {
    die("<h1>SHTracker: Error</h1><p>One or more fields are empty.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

//Check if ID exists
$checkid = mysql_query("SELECT id FROM Data WHERE id = \"$id\"");
$resultcheckid = mysql_fetch_assoc($checkid); 
if ($resultcheckid != 0) { 
    die("<h1>SHTracker: Error</h1><p>ID <b>$id</b> already exists.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

if (isset($_POST["passwordprotectstate"])) {
    $protect = "1";
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        die("<h1>SHTracker: Error</h1><p>Password is missing...</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
    }
    $password = sha1($inputtedpassword);
} else {
    $protect = "0";
    $password = "";
}

if (isset($_POST["showadsstate"])) {
    $showads = "1";
} else {
    $showads = "0";
}
    
mysql_query("INSERT INTO Data (name, id, url, count, protect, password, showads)
VALUES (\"$name\",\"$id\",\"$url\",\"$count\",\"$protect\",\"$password\",\"$showads\")");

mysql_close($con);

?>
<h1>SHTracker: Download/Link Added</h1>
<p>The download/link <b><? echo $name; ?></b> has been added successfully.</p>
<p><b>Details:</b></p>
<ul>
<li>Name : <? echo $name; ?></li>
<li>ID : <? echo $id; ?></li>
<li>URL : <? echo $url; ?></li>
</ul>
<p><b>Tracking Link:</b></p>
<p><? echo PATH_TO_SCRIPT; ?>/get.php?id=<? echo $id; ?></p>
<hr />
<p><a href="../../admin">Back To Home</a></p>
</body>
</html>