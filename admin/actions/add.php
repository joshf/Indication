<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

require_once("../../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
	header("Location: login.php");
	exit; 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker: Add</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<style>
    body {
	   padding-top: 60px;
    }  
    label.error {
	   color: #ff0000;
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
<div class="navbar navbar-inverse navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<a class="btw btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">SHTracker</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li><a href="../index.php">Home</a></li>
<li class="divider-vertical"></li>
<li class="active"><a href="../add.php">Add</a></li>
<li class="divider-vertical"></li>
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
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

//Set variables
$name = mysql_real_escape_string($_POST["downloadname"]);
$id = mysql_real_escape_string($_POST["id"]);
$url = mysql_real_escape_string($_POST["url"]);
$count = mysql_real_escape_string($_POST["count"]);

//Convert to lowercase
$id = strtolower($id);
$url = strtolower($url);

//Failsafes
if (empty($name) || empty($id) || empty($url)) {
    die("<p>One or more fields are empty.</p><p><a href=\"javascript:history.go(-1)\" class=\"btn\">Go Back</a></p></body></html>");
}

//Check if ID exists
$checkid = mysql_query("SELECT id FROM Data WHERE id = \"$id\"");
$resultcheckid = mysql_fetch_assoc($checkid); 
if ($resultcheckid != 0) { 
    die("<p>ID <b>$id</b> already exists.</p><p><a href=\"javascript:history.go(-1)\" class=\"btn\">Go Back</a></p></body></html>");
}

if (isset($_POST["passwordprotectstate"])) {
    $protect = "1";
    $inputtedpassword = mysql_real_escape_string($_POST["password"]);
    if (empty($inputtedpassword)) {
        die("<p>Password is missing...</p><p><a href=\"javascript:history.go(-1)\" class=\"btn\">Go Back</a></p></body></html>");
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
<p>The download/link <b><? echo $name; ?></b> has been added successfully.</p>
<p><b>Details:</b></p>
<ul>
<li>Name : <? echo $name; ?></li>
<li>ID : <? echo $id; ?></li>
<li>URL : <? echo $url; ?></li>
</ul>
<p><b>Tracking Link:</b></p>
<p><? echo PATH_TO_SCRIPT; ?>/get.php?id=<? echo $id; ?></p>
<p><a href="../index.php" class="btn">Go Home</a></p>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../../resources/bootstrap/js/bootstrap.js"></script>
<script src="../../resources/bootstrap/js/bootstrap-collapse.js"></script>
<script src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#passwordprotectstate").click(function() {
		$("#passwordentry").toggle(this.checked);
	});
	$.validator.addMethod(
		"legalname",
		function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9()._\-\s]+$/.test(value);
		},
		"Illegal character. Only points, spaces, underscores or dashes are allowed."
	);
	$.validator.addMethod(
		"legalid",
		function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9._-]+$/.test(value);
		},
		"Illegal character. Only points, underscores or dashes are allowed."
	); 
	$.validator.addMethod(
		"legalurl",
		function(value, element) { 
			return this.optional(element) || /^[a-zA-Z0-9.?=:#_\-/\-]+$/.test(value);
		},
		"Illegal character. Please use a valid URL or directory path."
	); 
	$("#addform").validate({
		rules: {
			downloadname: {
				required: true,
				legalname: true
			},
			id: {
				required: true,
				legalid: true
			},
			url: {
				required: true,
				legalurl: true
			},
			count: {
				digits: true
			},
			password: {
				required: true,
				minlength: 6
			},
		}
	});
});
</script>
<!-- Javascript end -->
</body>
</html>