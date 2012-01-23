<!-- SHTracker, Copyright Josh Fradley 2012 -->
<html>
<head>
<?php

require_once("config.php");
echo "<title>" . WEBSITE . " download statistics</title>";

?>
<!-- Change the style to match the rest of your site here -->
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php

//Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}
    
mysql_select_db(DB_NAME, $con);

$id = mysql_real_escape_string($_GET["id"]);

//Check ID is not blank
if (empty($id)) {
    die("<p>ID cannot be blank...</p>");
}

$getdownloadinfo = mysql_query("SELECT name, count FROM Data WHERE id = \"$id\"");
$info = mysql_fetch_row($getdownloadinfo);

echo "<p>" . $info["0"] . " has been downloaded " . $info["1"] . " times.</p>";

mysql_close($con);

?>
</body>
</html>