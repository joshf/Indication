<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    die("Error: Config file not found!");
}

require_once("config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit;
}

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

$getusersettings = mysqli_query($con, "SELECT `user` FROM `users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysqli_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysqli_fetch_assoc($getusersettings);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="assets/favicon.ico">
<title>Indication &raquo; Breakdowns</title>
<link rel="apple-touch-icon" href="assets/icon.png">
<link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="screen">
<link rel="stylesheet" href="assets/css/indication.css" type="text/css" media="screen">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="index.php">Indication</a>
</div>
<div id="navbar" class="navbar-collapse collapse">
<ul class="nav navbar-nav navbar-right">
<li><a href="index.php">Dashboard</a></li>
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>
<div class="container-fluid">
<div class="row">
<div class="col-sm-3 col-md-2 sidebar">
<ul class="nav nav-sidebar">
<li><a href="index.php">Overview</a></li>
<li class="active"><a href="breakdowns.php">Breakdowns <span class="sr-only">(current)</span></a></li>
<li><a href="export.php">Export</a></li>
</ul>
<ul class="nav nav-sidebar">
<li><a href="edit.php">Add New</a></li>
<li><a href="edit.php">Edit</a></li>
</ul>
</div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h1 class="page-header">Breakdowns</h1>
<?php

if (!isset($_GET["id"])) {
    $getids = mysqli_query($con, "SELECT `id`, `name` FROM `links`");
    if (mysqli_num_rows($getids) != 0) {
        echo "<form action=\"breakdowns.php\" method=\"get\"><div class=\"form-group\"><label for=\"id\">Select a Link</label><select class=\"form-control\" id=\"id\" name=\"id\">";
        while($ids = mysqli_fetch_assoc($getids)) {
            echo "<option value=\"" . $ids["id"] . "\">" . ucfirst($ids["name"]) . "</option>";
        }
        echo "</select></div><button type=\"submit\" class=\"btn btn-default\">Select</button></form>";
    } else {
        echo "<div class=\"alert alert-info\"><h4 class=\"alert-heading\">Information</h4><p>No downloads available to edit.</p><p><a class=\"btn btn-info\" href=\"index.php\">Go Back</a></p></div>";
    }
} else {

$id = mysqli_real_escape_string($con, $_GET["id"]);

//Check if ID exists
$getdata = mysqli_query($con, "SELECT `id`, `name`, `abbreviation`, `url`, `count` FROM `links` WHERE `id` = $id");
if (mysqli_num_rows($getdata) == 0) {
    echo "<div class=\"alert alert-danger\"><h4 class=\"alert-heading\">Error</h4><p>ID does not exist.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
} else {
    
$resultgetdata = mysqli_fetch_assoc($getdata);

//Stats
$gettotal = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE `link_id` = \"$id\"");
$resultgettotal = mysqli_fetch_assoc($gettotal);

$getday = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE `date` = CURDATE() AND `link_id` = \"$id\"");
$resultgetday = mysqli_fetch_assoc($getday);

$getweek = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE WEEKOFYEAR(`date`) = WEEKOFYEAR(NOW()) AND `link_id` = \"$id\"");
$resultgetweek = mysqli_fetch_assoc($getweek);

$getmonth = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE YEAR(`date`) = YEAR(NOW()) AND MONTH(`date`) = MONTH(NOW()) AND `link_id` = \"$id\"");
$resultgetmonth = mysqli_fetch_assoc($getmonth);

?>
<h3>Info</h3>
<ul>
<li>ID: <?php echo $resultgetdata["id"]; ?></li>
<li>Name: <?php echo $resultgetdata["name"]; ?></li>
<li>Abbreviation: <?php echo $resultgetdata["abbreviation"]; ?></li>
<li>URL: <?php echo $resultgetdata["url"]; ?></li>
</ul>
<h3>Statistics</h3>
<div class="row placeholders">
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgettotal["count"]; ?></span>
<h4>All Time</h4>
<span class="text-muted">Hits from install</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgetday["count"]; ?></span>
<h4>Day</h4>
<span class="text-muted">Hits today</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgetweek["count"]; ?></span>
<h4>Week</h4>
<span class="text-muted">Hits this week</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgetmonth["count"]; ?></span>
<h4>Month</h4>
<span class="text-muted">Hits this week</span>
</div>
</div>
<h3>Top Referrers</h3>
<ul class="list-group">
<?php

$getreferrers = mysqli_query($con, "SELECT `referrer`, COUNT(*) AS `count` FROM `counts` WHERE `link_id` = \"$id\" GROUP BY `referrer` ORDER BY `count` DESC LIMIT 10");

while($referrers = mysqli_fetch_assoc($getreferrers)) {
    echo "<li class=\"list-group-item\">";
    if ($referrers["referrer"] == "") {
        $referrer = "Blank referrer";
    } else {
        $referrer = $referrers["referrer"];
    }
    echo "<span class=\"badge\">" . $referrers["count"] . "</span><a href=\"" . $referrers["referrer"] . "\">" . $referrer . "</a>";
    echo "</li>";
}
    
?>
</ul>
<h3>Top IP Addresses</h3>
<ul class="list-group">
<?php

$getips = mysqli_query($con, "SELECT `ip`, COUNT(*) AS `count` FROM `counts` WHERE `link_id` = \"$id\" GROUP BY `ip` ORDER BY `count` DESC LIMIT 10");

while($ips = mysqli_fetch_assoc($getips)) {
    echo "<li class=\"list-group-item\">";
    echo "<span class=\"badge\">" . $ips["count"] . "</span>" . $ips["ip"] . "";
    echo "</li>";
}
    
?>
</ul>
<?php
}
    }
?>
</div>
</div>
</div>
<script src="assets/bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>