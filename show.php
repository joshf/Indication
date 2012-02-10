<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html>
<head>
<?php

require_once("config.php");
echo "<title>SHTracker: " . WEBSITE . " Download Statistics</title>";

?>
<!-- Change the style to match the rest of your site here -->
<link rel="stylesheet" type="text/css" href="style.css" />
<meta name="robots" content="noindex, nofollow">
</head>
<body>
<?php

//Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}
    
mysql_select_db(DB_NAME, $con);

//Accept POST or GET
if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} else {
    $id = mysql_real_escape_string($_POST["id"]);
}

//Check ID is not blank
if (empty($id)) {
    die("<h1>SHTracker: Error</h1><p>ID cannot be blank.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}

//Prevent some injection attacks
if (!preg_match("/^[a-zA-Z0-9.]{1,}$/", $id)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>"); 
}

//If ID exists show count or else die
$showinfo = mysql_query("SELECT name, count FROM Data WHERE id = \"$id\"");
$showresult = mysql_fetch_assoc($showinfo); 
if ($showresult != 0) { 
    if (isset($_GET["plain"])) {
        echo $showresult["count"];
    } else {
        echo "<p>" . $showresult["name"] . " has been downloaded " . $showresult["count"] . " times.</p>";
    }
} else { 
    die("<h1>SHTracker: Error</h1><p>ID <strong>$id</strong> does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}

mysql_close($con);

?>
</body>
</html>