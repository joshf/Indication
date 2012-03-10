<?php

session_start();
if (!isset($_SESSION["is_logged_in"])) {
    header("Location: login.php");
    exit; 
}

?>
<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html> 
<head>
<title>SHTracker: Admin Home</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
<noscript><p>Your browser does not support JavaScript or it is disabled, certain functions such as the displaying of tracking links will be broken!</p></noscript>
<?php

if (!file_exists("../config.php")) {
    die("<h1>SHTracker: Error</h1><p>SHTracker has not been installed.</p><hr /><p><a href=\"../install.php\">Go To Install</a></p></body></html>"); 
}

//Connect to database
require_once("../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

if (isset($_GET["page"])) {
    $page = mysql_real_escape_string($_GET["page"]);
    if (empty($page)) {
        die("<h1>SHDetect: Error</h1><p>Page value is empty...</p><hr /><p><a href=\"../admin\">&larr; Go Back</a></p></body></html>"); 
    }
    if (!preg_match("/^[0-9]{1,}$/", $page)) {
        die("<h1>SHDetect: Error</h1><p>Illegal character used...</p><hr /><p><a href=\"../admin\">&larr; Go Back</a></p></body></html>"); 
    }
} else {
    $page = 1;
}
$startfrom = ($page-1) * 20;

$getdownloads = mysql_query("SELECT * FROM Data ORDER BY name ASC LIMIT $startfrom, 20");

echo "<h1>SHTracker: " . WEBSITE . " Download Statistics</h1>
<form action=\"manage.php\" method=\"post\"><table>
<thead>
<tr>
<th></th>
<th>Name</th>
<th>ID</th>
<th>URL</th>
<th>Count</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td><input type=\"radio\" name=\"id\" value=\"" . $row["id"] . "\" /></td>";
    echo "<td class=\"name\">" . $row["name"] . "</td>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

$getdatacount = mysql_query("SELECT COUNT(*) FROM Data");
$resultgetdatacount = mysql_fetch_assoc($getdatacount); 
$totalpages = ceil($resultgetdatacount["COUNT(*)"] / 20);
echo "<p><em>Go To Page: </em>";
for ($i=1; $i <= $totalpages; $i++) { 
    echo " <a href=\"index.php?page=".$i."\">".$i."</a> "; 
} 
echo "</p>";

?>
<script type="text/javascript">
function showtrackinglink() 
{
    if ($("input[name=id]:checked").val()) {
        prompt("Tracking link for the download " + $("input[name=id]:checked").parent().siblings("td.name").text() + ". Press Ctrl/Cmd C to copy to the clipboard:","<?php echo PATH_TO_SCRIPT ?>/get.php?id=" + $("input[name=id]:checked").val());
    } else {
        alert("No ID selected!");
    }
}
function isempty() 
{
    if (!$("input[name=id]:checked").val()) {
        alert("No ID selected!");
        return false;
    }
}
function deleteconfirm() 
{
    if (!$("input[name=id]:checked").val()) {
        alert("No ID selected!");
        return false;
    } else {
        var delconfirm=confirm("Are you wish to delete the download " + $("input[name=id]:checked").parent().siblings("td.name").text() + "?");
        if (delconfirm==true) {
            return true;
        } else {
            return false; 
        }
    }
}
function logoutconfirm() 
{
    var logoutconfirm=confirm("<?php echo ADMIN_USER; ?>, are you sure you wish to logout?");
    if (logoutconfirm==true) {
        return true;
    } else {
        return false; 
    }
}
</script>
<input type="submit" name="command" value="Add" />
<input type="submit" name="command" onClick="return isempty()" value="Edit" />
<input type="submit" name="command" onClick="return deleteconfirm()" value="Delete" />
<input type="button" onClick="showtrackinglink()" name="command" value="Show Tracking Link" />
</form>
<p><i>To edit, delete or show the tracking link for a ID please select the radio button next to it.</i></p>
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
$resultnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<p><b>Number of downloads: </b>" . $resultnumberofdownloads["COUNT(id)"] . "</p>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM Data");
$resulttotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
echo "<p><b>Total downloads: </b>" . $resulttotalnumberofdownloads["SUM(count)"] . "</p>";

mysql_close($con);

?>
<hr />
<p><a href="index.php">Refresh</a> | <a href="settings.php">Settings</a> | <a href="logout.php" onClick="return logoutconfirm()">Logout</a></p>
<small>SHTracker 2.0 "DangerousDachshund" Copyright <a href="http://sidhosting.co.uk">Josh Fradley</a> <? echo date("Y"); ?></small>
<p><small><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9QFKYNSKM8CBJ">Donate</a></p>
</body>
</html>