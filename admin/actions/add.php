<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../../config.php")) {
    header("Location: ../../installer");
    exit;
}

require_once("../../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

if (!isset($_POST["id"])) {
    header("Location: ../../admin");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Add</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if (THEME == "default") {
    echo "<link href=\"../../resources/bootstrap/css/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.2/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}
?>
<style type="text/css">
body {
    padding-top: 60px;
}
</style>
<link href="../../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
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
<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">Indication</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li><a href="../index.php">Home</a></li>
<li class="divider-vertical"></li>
<li class="active"><a href="../add.php">Add</a></li>
<li><a href="../edit.php">Edit</a></li>
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
<h1>Add</h1>
</div>
<?php

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Could not connect to database (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

mysql_select_db(DB_NAME, $con);

//Set variables
$name = mysql_real_escape_string($_POST["downloadname"]);
$id = mysql_real_escape_string($_POST["id"]);
$url = mysql_real_escape_string($_POST["url"]);
$count = mysql_real_escape_string($_POST["count"]);

//Failsafes
if (empty($name) || empty($id) || empty($url)) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>One or more fields are empty.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Check if ID exists
$checkid = mysql_query("SELECT id FROM Data WHERE id = \"$id\"");
$resultcheckid = mysql_fetch_assoc($checkid); 
if ($resultcheckid != 0) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>ID $id already exists.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Make sure a password is set if the checkbox was enabled
if (isset($_POST["passwordprotectstate"])) {
    $protect = "1";
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Password is missing.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
    }
    $password = sha1($inputtedpassword);
} else {
    $protect = "0";
    $password = "";
}

if (isset($_POST["showadsstate"])) {
    $showads = "1";
} else {
    $showads = "0";
}

mysql_query("INSERT INTO Data (name, id, url, count, protect, password, showads)
VALUES (\"$name\",\"$id\",\"$url\",\"$count\",\"$protect\",\"$password\",\"$showads\")");

mysql_close($con);

?>
<div class="alert alert-info">
<h4 class="alert-heading">Download Added</h4>
<ul>
<li>Name: <?php echo $name; ?></li>
<li>ID: <?php echo $id; ?></li>
<li>URL: <?php echo $url; ?></li>
</ul>
<p><b>Tracking Link:</b></p>
<p><?php echo PATH_TO_SCRIPT; ?>/get.php?id=<?php echo $id; ?></p>
<p><a class="btn btn-info" href="../../admin/index.php">Back To Home</a></p>
</div>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="../../resources/jquery.min.js"></script>
<script src="../../resources/bootstrap/js/bootstrap.min.js"></script>
<!-- Javascript end -->
</body>
</html>