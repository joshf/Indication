<!-- SHTracker, Copyright Josh Fradley 2012 -->
<html> 
<head>
<title>SHTracker: All Counts Reset To Zero</title>
<link rel="stylesheet" type="text/css" href="../../style.css" />
</head>
<body>
<?php

require_once("../../config.php");

//Make user confirm action with a password
$storedpassword = ADMIN_PASSWORD;
$password = sha1($storedpassword);

if (sha1($_POST["password"]) != $password) {
    die("<h1>SHTracker: Error</h1><p>Password incorrect...</p><hr /><p><a href=\"../../admin\">Go Back</a></p>");
}

//Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

mysql_query("UPDATE Data SET count = \"0\"");

mysql_close($con);

?>
<h1>SHTracker: Counts Reset to Zero</h1>
<p>All counts have been reset to zero.</p>
<hr />
<p><a href="../../admin">Go Back</a></p>
</body>
</html>