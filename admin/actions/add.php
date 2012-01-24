<!-- SHTracker, Copyright Josh Fradley 2012 -->
<html> 
<head>
<title>SHTracker: Download Added</title>
<link rel="stylesheet" type="text/css" href="../../style.css" />
</head>
<body>
<?php

//Set variables
$name = $_POST["name"];
$id = $_POST["id"];
$url = $_POST["url"];
$count = $_POST["count"];

//Check variables are not empty
if (empty($name)) {
    die("<h1>SHTracker: Error</h1><p>Name is missing...</p><hr /><p><a href=\"../add.php\">Go Back</a></p></body></html>");
}
if (empty($id)) {
    die("<h1>SHTracker: Error</h1><p>ID is missing...</p><hr /><p><a href=\"../add.php\">Go Back</a></p></body></html>");
}
if (empty($url)) {
    die("<h1>SHTracker: Error</h1><p>URL is missing...</p><hr /><p><a href=\"../add.php\">Go Back</a></p></body></html>");
}

//Prevent some injection attacks
if (!preg_match("/^[a-zA-Z0-9(). ]{1,}$/", $name)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../add.php\">Go Back</a></p></body></html>"); 
}

if (!preg_match("/^[a-zA-Z0-9.]{1,}$/", $id)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../add.php\">Go Back</a></p></body></html>"); 
}

if (!preg_match("/^[a-zA-Z0-9.:?=#\/_-]{1,}$/", $url)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"../../admin\">Go Back</a></p></body></html>"); 
}

//Connect to database
require_once("../../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

mysql_query("INSERT INTO Data (name, id, url, count)
VALUES (\"$name\",\"$id\",\"$url\",\"$count\")");

mysql_close($con);

?>
<h1>SHTracker: Download added</h1>
<p>The download <strong><? echo $name; ?></strong> has been added successfully.</p>
<p><strong>Details:</strong></p>
<ul>
<li>Name : <? echo $name; ?></li>
<li>ID : <? echo $id; ?></li>
<li>URL : <? echo $url; ?></li>
</ul>
<p><strong>Download link:</strong></p>
<p><? echo PATH_TO_SCRIPT; ?>/get.php?id=<? echo $id; ?></p>
<hr />
<p><a href="../../admin">Back To Home</a></p>
</body>
</html>