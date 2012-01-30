<!-- SHTracker, Copyright Josh Fradley 2012 -->
<html> 
<head>
<title>SHTracker: Download Deleted</title>
<link rel="stylesheet" type="text/css" href="../../style.css" />
</head>
<body>
<?php

if ($_POST["command"] == "Keep") {
    die("<h1>SHTracker: Error</h1><p>Delete cancelled...</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>");
}

//Set variables
$idtodelete = mysql_real_escape_string($_POST["idtodelete"]);

//Check variables are not empty
if (empty($idtodelete)) {
    die("<h1>SHTracker: Error</h1><p>ID is missing...</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>");
}

//Connect to database
require_once("../../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtodelete\"");
$result = mysql_fetch_row($getnameofdownload);

mysql_query("DELETE FROM Data WHERE id = \"$idtodelete\"");

mysql_close($con);

?>
<h1>SHTracker: Download deleted</h1>
<p>The download <strong><? echo $result["0"]; ?></strong> has been deleted.</p>
<hr />
<p><a href="../../admin">Back To Home</a></p>
</body>
</html>