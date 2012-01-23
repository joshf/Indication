<?php

require("login.php");

//SHTracker, Copyright Josh Fradley 2012

$command = $_POST["command"];

if ($command == "Edit") {

?>
<!-- Edit -->
<html> 
<head>
<title>SHTracker: Editing Download</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<?php

$idtoedit = $_POST["id"];

//Check variables are not empty
if (empty($idtoedit)) {
    die("<h1>SHTracker: Error</h1><p>No download selected...</p><hr /><p><a href=\"../admin\">Go Back</a></p></body></html>");
}

//Connect to database
require_once("../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtoedit\"");
$result = mysql_fetch_row($getnameofdownload);

?>
<h1>SHTracker: Editing download <? echo $result["0"]; ?></h1>
<p>Please edit any values you wish.</p>
<div id="form">
<form action="actions/edit.php" method="post">
<?php

$getidinfo = mysql_query("SELECT * FROM Data WHERE id = \"$idtoedit\"");
while($row = mysql_fetch_array($getidinfo)) {
    echo "<p>Name: <input type=\"text\" size=\"50\" name=\"name\" value=\"" . $row["name"] . "\" /></p>";
    echo "<p>ID: <input type=\"text\" size=\"50\" name=\"id\" value=\"" . $row["id"] . "\" /></p>";
    echo "<p>URL: <input type=\"text\" size=\"50\" name=\"url\" value=\"" . $row["url"] . "\" /></p>";
    echo "<p>Count: <input type=\"text\" size=\"50\" name=\"count\" value=\"" . $row["count"] . "\" /></p>";
}

mysql_close($con);

?>
<input type="hidden" name="idtoedit" value="<? echo $idtoedit; ?>" />
<p><input type="submit" name="command" value="Edit" /></p>
</form>
</div>
<hr />
<p><a href="../admin">Go Back</a></p>
</body>
</html>
<?php
} elseif ($command == "Delete") {

$idtodelete = $_POST["id"];

?>
<!-- Delete -->
<html> 
<head>
<title>SHTracker: Deleting Download</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<?

//Check variables are not empty
if (empty($idtodelete)) {
    die("<h1>SHTracker: Error</h1><p>No download selected...</p><hr /><p><a href=\"../admin\">Go Back</a></p></body></html>");
}

//Connect to database
require_once("../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtodelete\"");
$result = mysql_fetch_row($getnameofdownload);

mysql_close($con);

?>
<h1>SHTracker: Delete download</h1>
<p>Are you sure you wish to delete the download <strong><? echo $result["0"]; ?></strong>?</p>
<div id="form">
<form action="actions/delete.php" method="post">
<input type="hidden" name="idtodelete" value="<? echo $idtodelete; ?>" />
<input type="submit" name="command" value="Delete" />
<input type="submit" name="command" value="Keep" />
</form>
</div>
<hr />
<p><a href="../admin">Go Back</a></p>
</body>
</html>
<?php
} else {
//New
header("Location: add.php");
}
?>