<!DOCTYPE html>
<!-- SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker) -->
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    p {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 14px;
        color: #333333;
    }
</style>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<!-- Content start -->
<?php

//Connect to database
require_once("config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

//List all downloads
if (isset($_GET["list"])) {
    $listdownloads = mysql_query("SELECT name, count FROM Data");
    echo "<p>";
    while($info = mysql_fetch_assoc($listdownloads)) {
        echo "<b>" . $info["name"] . "</b>: " . $info["count"] . "<br>";
    }
    echo "</p></body></html>";
    mysql_close($con);
    exit;
}

if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} else {
    die("<p><b>Error:</b> ID cannot be blank.</p></body></html>");
}

//If ID exists, show count or else die
$showinfo = mysql_query("SELECT name, count FROM Data WHERE id = \"$id\"");
$showresult = mysql_fetch_assoc($showinfo);
if ($showresult != 0) {
    if (isset($_GET["plain"])) {
        echo $showresult["count"];
    } else {
        echo "<p>" . $showresult["name"] . " has been downloaded " . $showresult["count"] . " time(s).</p>";
    }
} else {
    die("<p><b>Error:</b> ID does not exist.</p></body></html>");
}

mysql_close($con);

?>
<!-- Content end -->
</body>
</html>