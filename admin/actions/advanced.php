<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../../config.php");

if (!isset($_POST["command"])) {
	header("Location: ../settings.php");
}

//Make user confirm action with a password
$password = ADMIN_PASSWORD;

if (sha1($_POST["password"]) != $password) {
	die("<html><head><title>SHTracker: Error</title><link rel=\"stylesheet\" type=\"text/css\" href=\"../../style.css\" /></head><body><h1>SHTracker: Error</h1><p>Password incorrect...</p><hr /><p><a href=\"../settings.php\">&larr; Go Back</a></p></body></html>");
}

$command = $_POST["command"];

if ($command == "Reset All Counts to Zero") {

?>
<!-- Reset -->
<html> 
<head>
<title>SHTracker: All Counts Reset to Zero</title>
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

mysql_query("UPDATE Data SET count = \"0\"");

mysql_close($con);

?>
<h1>SHTracker: All Counts Reset to Zero</h1>
<p>All counts have been reset to zero.</p>
<hr />
<p><a href="../../admin">&larr; Go Back</a></p>
</body>
</html>
<?php
} elseif ($command == "Delete All Downloads") {
?>
<!-- Delete -->
<html> 
<head>
<title>SHTracker: All Downloads Deleted</title>
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

mysql_query("DELETE FROM Data");

mysql_close($con);

?>
<h1>SHTracker: All Downloads Deleted</h1>
<p>All downloads have been deleted.</p>
<hr />
<p><a href="../../admin">&larr; Go Back</a></p>
</body>
</html>
<?php
} else {
	header("Location: ../../admin");
}
?>