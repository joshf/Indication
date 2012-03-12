<?php

session_start();
if (!isset($_SESSION["is_logged_in"])) {
	header("Location: login.php");
	exit; 
}

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

if (!isset($_POST["command"]) || !isset($_POST["id"])) {
	header("Location: index.php");
}

?>
<html> 
<head>
<title>SHTracker: Protect A Download</title>
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

$id = mysql_real_escape_string($_POST["id"]);

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$id\"");
$resultnameofdownload = mysql_fetch_assoc($getnameofdownload);

$command = $_POST["command"];

if ($command == "Protect") {
	// Protect
	$passwordpost = mysql_real_escape_string($_POST["password"]);
	$password = sha1($passwordpost);
	mysql_query("UPDATE Data SET protect = \"true\", password = \"$password\" WHERE id = \"$id\"");
	echo "<h1>SHTracker: Protect A Download</h1><p>The download <b>" . $resultnameofdownload["name"] . "</b> has been protected. The pasword is <b>$passwordpost</b>.</p><hr /><p><a href=\"index.php\">&larr; Go Back</a></p>";
} elseif ($command == "Unprotect") {
	// Unprotect
	mysql_query("UPDATE Data SET protect = \"false\", password = \"\" WHERE id = \"$id\"");
	echo "<h1>SHTracker: Protect A Download</h1><p>The download <b>" . $resultnameofdownload["name"] . "</b> has been unprotected.</p><hr /><p><a href=\"index.php\">&larr; Go Back</a></p>";
} elseif ($command == "Unprotect All") {
	// Unprotect All
	mysql_query("UPDATE Data SET protect = \"false\", password = \"\"");
	header("Location: index.php");
}

mysql_close($con);

?>
</body>
</html>