<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../../config.php")) {
    header("Location: ../../installer");
}

require_once("../../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: ../login.php");
    exit; 
}

if (!isset($_POST["id"])) {
    header("Location: ../../admin");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Edit</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php

if (THEME == "default") {
    echo "<link href=\"../../resources/bootstrap/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.0/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}

?>
<style type="text/css">
body {
    padding-top: 60px;
}
</style>
<link href="../../resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<!-- Nav start -->
<div class="navbar navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<a class="btw btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">Indication</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li><a href="../index.php">Home</a></li>
<li class="divider-vertical"></li>
<li><a href="../add.php">Add</a></li>
<li class="active"><a href="#">Edit</a></li>
</ul>
<ul class="nav pull-right">
<li><a href="../settings.php">Settings</a></li>
<li><a href="../logout.php">Logout</a></li>
</ul>
</div>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Edit</h1>
</div>		
<?php

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_POST["idtoedit"]);

//Set variables
$newname = mysql_real_escape_string($_POST["downloadname"]);
$newid = mysql_real_escape_string($_POST["id"]);
$newurl = mysql_real_escape_string($_POST["url"]);
$newcount = mysql_real_escape_string($_POST["count"]);

//Convert to lowercase
$newid = strtolower($newid);
$newurl = strtolower($newurl);

//Failsafes
if (empty($newname) || empty($newid) || empty($newurl)) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>One or more fields are empty.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Make sure a password is set if the checkbox was enabled
if (isset($_POST["passwordprotectstate"])) {
    if (!isset($_POST["password"])) {
        die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Password is missing.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
    } 
    $getprotectinfo = mysql_query("SELECT password FROM Data WHERE id = \"$idtoedit\"");
    $getprotectinforesult = mysql_fetch_assoc($getprotectinfo); 
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        $password = $getprotectinforesult["password"];
    } else {
        $password = sha1($inputtedpassword);
    }
    $protect = "1";
} else {
    $protect = "0";
    $password = "";
}

if (isset($_POST["showadsstate"])) {
    $showads = "1";
} else {
    $showads = "0";
}

mysql_query("UPDATE Data SET name = \"$newname\", id = \"$newid\", url = \"$newurl\", count = \"$newcount\", protect = \"$protect\", password = \"$password\", showads = \"$showads\" WHERE id = \"$idtoedit\"");

mysql_close($con);

?> 
<div class="alert alert-info">
<b>Info:</b> The download has been edited successfully.
</div>
<p><b>Updated Details:</b></p>
<ul>
<li>Name: <? echo $newname; ?></li>
<li>ID: <? echo $newid; ?></li>
<li>URL: <? echo $newurl; ?></li>
</ul>
<p><b>Tracking Link:</b></p>
<p><? echo PATH_TO_SCRIPT; ?>/get.php?id=<? echo $newid; ?></p>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="../../resources/jquery.js"></script>
<script src="../../resources/bootstrap/js/bootstrap.js"></script>
<!-- Javascript end -->
</body>
</html>
