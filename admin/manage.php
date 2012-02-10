<?php

require("login.php");

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

//If no download is selected or manage.php is called directly go home
if (!isset($_POST["command"]) || !isset($_POST["id"])) {
    header("Location: ../admin");
}

$command = $_POST["command"];

//FIXME: Show link on index.php we  have to use a session
if ($command == "Reveal Link") {
    $_SESSION["idtoreveal"] = $_POST["id"];
    header("Location: ../admin");
}

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

//Connect to database
require_once("../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_POST["id"]);

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtoedit\"");
$resultnameofdownload = mysql_fetch_assoc($getnameofdownload);

?>
<h1>SHTracker: Editing Download <? echo $resultnameofdownload["name"]; ?></h1>
<p>Please edit any values you wish.</p>
<form action="actions/edit.php" method="post">
<?php

$getidinfo = mysql_query("SELECT * FROM Data WHERE id = \"$idtoedit\"");
while($row = mysql_fetch_assoc($getidinfo)) {
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
<hr />
<p><a href="../admin">Go Back</a></p>
</body>
</html>
<?php
} elseif ($command == "Delete") {
?>
<!-- Delete -->
<html> 
<head>
<title>SHTracker: Deleting Download</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<?php

//Connect to database
require_once("../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtodelete = mysql_real_escape_string($_POST["id"]);

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtodelete\"");
$resultnameofdownload = mysql_fetch_assoc($getnameofdownload);

mysql_close($con);

?>
<h1>SHTracker: Deleting Download</h1>
<p>Are you sure you wish to delete the download <strong><? echo $resultnameofdownload["name"]; ?></strong>?</p>
<form action="actions/delete.php" method="post">
<input type="hidden" name="idtodelete" value="<? echo $idtodelete; ?>" />
<input type="submit" name="command" value="Delete" />
<input type="submit" name="command" value="Keep" />
</form>
<hr />
<p><a href="../admin">Go Back</a></p>
</body>
</html>
<?php
} elseif ($command == "New") {
//New
header("Location: add.php");
}
?>