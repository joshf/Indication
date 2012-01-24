<?php
require("login.php");
?>
<!-- SHTracker, Copyright Josh Fradley 2012 -->
<html> 
<head>
<title>SHTracker: Admin Home</title>
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

$getdownloads = mysql_query("SELECT * FROM Data ORDER BY name ASC");

echo "<h1>SHTracker: " . WEBSITE . " download statistics</h1>
<form action=\"manage.php\" method=\"post\"><table>
<tr>
<th></th>
<th>Name</th>
<th>ID</th>
<th>URL</th>
<th>Count</th>
</tr>";

while($row = mysql_fetch_array($getdownloads)) {
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
</form>
<p><em>To edit or delete a ID please select the radio button next to it.</em></p>
<?

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
while($row = mysql_fetch_array($getnumberofdownloads)) {
    echo "<p><strong>Number of downloads: </strong>" . $row["COUNT(id)"] . "</p>";
}

$gettotaldownloads = mysql_query("SELECT SUM(count) FROM Data");
while($row = mysql_fetch_array($gettotaldownloads)) {
    echo "<p><strong>Total downloads: </strong>" . $row["SUM(count)"] . "</p>";
}

mysql_close($con);

?>
<hr />
<p>You can access your downloads with this link: <? echo PATH_TO_SCRIPT; ?>/get.php?id=<em>id</em></p>
<hr />
<p><a href="settings.php">Settings</a> | <a href="logout.php">Logout</a></p>
<small>SHTracker 1.6.7 "BigBewilderedBuffalo" Copyright <a href="http://sidhosting.co.uk">Josh Fradley</a> 2012</small>
</body>
</html>