<?php
require("login.php");
?>
<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html> 
<head>
<title>SHTracker: Admin Home</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<script src="../sorttable.js"></script>
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

$getdownloads = mysql_query("SELECT * FROM Data ORDER BY name ASC");

echo "<h1>SHTracker: " . WEBSITE . " Download Statistics</h1>
<form action=\"manage.php\" method=\"post\"><table class=\"sortable\">
<tr>
<th></th>
<th>Name</th>
<th>ID</th>
<th>URL</th>
<th>Count</th>
</tr>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td><input type=\"radio\" name=\"id\" value=\"" . $row["id"] . "\" /></td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</table>";

?>
<br />
<input type="submit" name="command" value="New" />
<input type="submit" name="command" value="Edit" />
<input type="submit" name="command" value="Delete" />
<input type="submit" name="command" value="Reveal Link" />
</form>
<p><em>To edit or delete a ID please select the radio button next to it.</em></p>
<?php

//FIXME: This could be better. Show link with ID
if (isset($_SESSION["idtoreveal"])) {
    echo "<p><em>Link for ID: " . $_SESSION["idtoreveal"] . "</em></p>";
    echo "<textarea rows=\"1\" cols=\"100\">" . PATH_TO_SCRIPT . "/get.php?id=" . $_SESSION["idtoreveal"] . "</textarea>";
    unset($_SESSION["idtoreveal"]);
}

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
$resultnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<p><strong>Number of downloads: </strong>" . $resultnumberofdownloads["COUNT(id)"] . "</p>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM Data");
$resulttotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
echo "<p><strong>Total downloads: </strong>" . $resulttotalnumberofdownloads["SUM(count)"] . "</p>";

mysql_close($con);

?>
<hr />
<p>To track link clicks use the following URL rather than the original link: <? echo PATH_TO_SCRIPT; ?>/get.php?id=<em>id</em></p>
<hr />
<p><a href="index.php">Refresh</a> | <a href="settings.php">Settings</a> | <a href="logout.php">Logout</a></p>
<small>SHTracker 1.8 "InvisibleIguana" Copyright <a href="http://sidhosting.co.uk">Josh Fradley</a> <? echo date("Y"); ?></small>
<p><small><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9QFKYNSKM8CBJ">Donate</a></p>
</body>
</html>