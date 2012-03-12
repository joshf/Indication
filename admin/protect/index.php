<?php

session_start();
if (!isset($_SESSION["is_logged_in"])) {
    header("Location: ../login.php");
    exit; 
}

?>
<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html> 
<head>
<title>SHTracker: Protect Downloads</title>
<link rel="stylesheet" type="text/css" href="../../style.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
<script type="text/javascript">
function isempty() 
{
    if (!$("input[name=id]:checked").val()) {
        alert("No ID selected!");
        return false;
    }
}
function ispasswordempty() 
{
    if (!$("input[name=password]").val()) {
        alert("Please enter a password!");
        return false;
    }
}
</script>
<h1>SHTracker: Protect A Download</h1>
<?php

//Connect to database
require_once("../../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$getdatacountpro = mysql_query("SELECT COUNT(*) FROM Data WHERE protect = \"true\"");
$resultgetdatacountpro = mysql_fetch_assoc($getdatacountpro); 
echo "<h2>Protected (" . $resultgetdatacountpro["COUNT(*)"] . ")</h2>";

?>
<form id=\"protected\" action="process.php" method="post">
<table>
<thead>
<tr>
<th></th>
<th>Name</th>
<th>ID</th>
</tr>
</thead>
<tbody>
<?php

$getprotecteddownloads = mysql_query("SELECT * FROM Data WHERE protect = \"true\"");

while($row = mysql_fetch_assoc($getprotecteddownloads)) {
    echo "<tr>";
    echo "<td><input type=\"radio\" name=\"id\" value=\"" . $row["id"] . "\" /></td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["id"] . "</td>";
    echo "</tr>";
}

?>
</tbody>
</table>
<p><input type="submit" name="command" onClick="return isempty()" value="Unprotect" />
<input type="submit" name="command" value="Unprotect All" /></p>
</form>
<hr />
<?

$getdatacountunpro = mysql_query("SELECT COUNT(*) FROM Data WHERE protect = \"false\" || protect = \"\"");
$resultgetdatacountunpro = mysql_fetch_assoc($getdatacountunpro); 
echo "<h2>Unprotected (" . $resultgetdatacountunpro["COUNT(*)"] . ")</h2>";

?>
<form id=\"unprotected\" method="post">
<table>
<thead>
<tr>
<th></th>
<th>Name</th>
<th>ID</th>
</tr>
</thead>
<tbody>
<?php

$getunprotecteddownloads = mysql_query("SELECT * FROM Data WHERE protect = \"false\" || protect = \"\"");

while($row = mysql_fetch_assoc($getunprotecteddownloads)) {
    echo "<tr>";
    echo "<td><input type=\"radio\" name=\"id\" value=\"" . $row["id"] . "\" /></td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["id"] . "</td>";
    echo "</tr>";
}

?>
</tbody>
</table>
<p><input type="submit" name="command" onClick="return isempty()" value="Protect" /></p>
</form>
<?

if (isset($_POST["command"])) {
    if ($_POST["command"] == "Protect") {
        $getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"" . $_POST["id"] . "\"");
        $resultnameofdownload = mysql_fetch_assoc($getnameofdownload);
        echo "<form action=\"process.php\" method=\"post\">
        Enter a password for <b>" . $resultnameofdownload["name"] . "</b>:<br />
        <input type=\"password\" name=\"password\" />
        <input type=\"hidden\" name=\"id\" value=\"" . $_POST["id"] . "\" />
        <input type=\"submit\" name=\"command\" onClick=\"return ispasswordempty()\" value=\"Protect\" />
        </form>";
    }
}

?>
<hr />
<small>BETA: Please be careful</small>
<p><a href="../../admin">&larr; Go Back</a></p>
</body>
</html>