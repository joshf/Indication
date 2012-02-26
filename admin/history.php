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
<title>SHTracker: Count History</title>
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

//Set id using SESSION
$id = mysql_real_escape_string($_SESSION["idtoviewhistory"]);

//Check ID is not blank
if (empty($id)) {
    die("<h1>SHTracker: Error</h1><p>ID cannot be blank.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

//Prevent some injection attacks
if (!preg_match("/^[a-zA-Z0-9.]{1,}$/", $id)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>"); 
}

//Check if ID exists
$checkifidexists = mysql_query("SELECT id FROM Data WHERE id = \"$id\"");
$getresult = mysql_fetch_assoc($checkifidexists); 
if ($getresult == 0) { 
    die("<h1>SHTracker: Error</h1><p>ID <strong>$id</strong> does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

$gethistory = mysql_query("SELECT * FROM History WHERE id = \"$id\" ORDER BY dateupdated DESC");

echo "<h1>SHTracker: Count History</h1>
<p>Count history for <strong>$id</strong>. Use these stats to compare different versions of the same download. Only the most recent update per day is stored.</p>
<table class=\"sortable\">
<tr>
<th>Date Updated</th>
<th>Prior Count</th>
</tr>";

while($row = mysql_fetch_assoc($gethistory)) {
    echo "<tr>";
    echo "<td>" . $row["dateupdated"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</table>";

mysql_close($con);
unset($_SESSION["idtoviewhistory"]);

?>
<br />
<hr />
<p><a href="../admin">&larr; Go Back</a></p>
</body>
</html>