<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

//Get current settings
$currentadminuser = ADMIN_USER;
$currentadminemail = ADMIN_EMAIL;
$currentadminpassword = ADMIN_PASSWORD;
$currentwebsite = WEBSITE;
$currentpathtoscript = PATH_TO_SCRIPT;
$currentcountuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$currentcountuniqueonlytime = COUNT_UNIQUE_ONLY_TIME;
$currentadcode = htmlspecialchars_decode(AD_CODE); 

if (isset($_POST["save"])) {

//Get new settings from POST
$adminuser = $_POST["adminuser"];
if (empty($adminuser)) {
    $adminuser = $currentadminuser;
}
$adminemail = $_POST["adminemail"];
$adminpassword = $_POST["adminpassword"];
if (empty($adminpassword)) {
    $adminpassword = $currentadminpassword;
}
if ($adminpassword != $currentadminpassword) {
    $adminpassword = sha1($adminpassword);
}
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];
$countuniqueonlystate = $_POST["countuniqueonlystate"];
if (isset($_POST["countuniqueonlytime"])) {
    $countuniqueonlytime = $_POST["countuniqueonlytime"];
}
if (isset($_POST["adcode"])) {
    if (get_magic_quotes_gpc()) {
        $adcode = stripslashes(htmlspecialchars($_POST["adcode"]));
    } else {
        $adcode = htmlspecialchars($_POST["adcode"]);
    }
}

//Remember previous settings
if (empty($adcode)) {
    $adcode = $currentadcode;
}
if (empty($countuniqueonlytime)) {
    $countuniqueonlytime = $currentcountuniqueonlytime;
}

$settingsstring = "<?php

//Database Settings
define(\"DB_HOST\", \"" . DB_HOST . "\");
define(\"DB_USER\", \"" . DB_USER . "\");
define(\"DB_PASSWORD\", \"" . DB_PASSWORD . "\");
define(\"DB_NAME\", \"" . DB_NAME . "\");

//Admin Details
define(\"ADMIN_USER\", \"$adminuser\");
define(\"ADMIN_EMAIL\", \"$adminemail\");
define(\"ADMIN_PASSWORD\", \"$adminpassword\");

//Other Settings
define(\"WEBSITE\", \"$website\");
define(\"PATH_TO_SCRIPT\", \"$pathtoscript\");
define(\"COUNT_UNIQUE_ONLY_STATE\", \"$countuniqueonlystate\");
define(\"COUNT_UNIQUE_ONLY_TIME\", \"$countuniqueonlytime\");
define(\"UNIQUE_KEY\", \"$uniquekey\");
define(\"AD_CODE\", \"$adcode\");

?>";

//Write config
$configfile = fopen("../config.php", "w");
fwrite($configfile, $settingsstring);
fclose($configfile);

//Show updated values
header("Location: settings.php?updated=true");

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker: Settings</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<style>
    body {
        padding-top: 60px;
    }
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
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
<a class="brand" href="#">SHTracker</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li><a href="index.php">Home</a></li>
<li class="divider-vertical"></li>
<li><a href="add.php">Add</a></li>
<li><a href="#">Edit</a></li>
</ul>
<ul class="nav pull-right">
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Settings</h1>
</div>
<?php

if (isset($_GET["updated"])) {
    echo "<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><b>Info:</b> Settings updated.</div>";
}

?>
<form method="post">
<fieldset>
<h4>Admin Details</h4>

<div class="control-group">
<label class="control-label" for="adminuser">Admin User</label>
<div class="controls">
<input type="text" id="adminuser" name="adminuser" value="<? echo $currentadminuser; ?>" placeholder="Enter a admin user...">
</div>
</div>

<div class="control-group">
<label class="control-label" for="adminpassword">Admin Password</label>
<div class="controls">
<input type="password" id="adminpassword" name="adminpassword" value="<? echo $currentadminpassword; ?>" placeholder="Enter a admin password...">
</div>
</div>

<h4>Other Settings</h4>

<div class="control-group">
<label class="control-label" for="website">Website</label>
<div class="controls">
<input type="text" id="website" name="website" value="<? echo $currentwebsite; ?>" placeholder="Enter your websites name...">
</div>
</div>

<div class="control-group">
<label class="control-label" for="pathtoscript">Path to Script</label>
<div class="controls">
<input type="text" id="pathtoscript" name="pathtoscript" value="<? echo $currentpathtoscript; ?>" placeholder="Type where SHTracker is installed...">
</div>
</div>

<h4>Ad Code</h4>

<p>Show an advert before user can continue to their download. This can be changed on a per download basis.</p>
<div class="alert alert-warning"><b>Warning:</b> On some server configurations using HTML code here may produce errors.</div>

<div class="control-group">
<div class="controls">
<textarea name="adcode" placeholder="Enter a ad code..."><? echo $currentadcode; ?></textarea>
</div>
</div>

<h4>Count Unique Visitors Only</h4>
<p>This settings allows you to make sure an individual users' clicks are only counted once.</p>
<div class="control-group">
<label class="control-label">Enable/Disable</label>
<div class="controls">
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
    echo "<label class=\"radio\"><input type=\"radio\" name=\"countuniqueonlystate\" value=\"Enabled\" checked=\"checked\"> Enabled</label>
          <label class=\"radio\"><input type=\"radio\" name=\"countuniqueonlystate\" value=\"Disabled\"> Disabled</label>";    
} else {
  echo "<label class=\"radio\"><input type=\"radio\" name=\"countuniqueonlystate\" value=\"Enabled\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" name=\"countuniqueonlystate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>  
</div>
<div class="control-group">
<label class="textarea" for="countuniqueonlytime">Time to consider user unique</label>
<div class="controls">
<input type="text" id="countuniqueonlytime" name="countuniqueonlytime" value="<? echo $currentcountuniqueonlytime; ?>" placeholder="Enter a time...">
</div>
</div>
<div class="form-actions">
<button type="submit" name="save" class="btn btn-primary">Save Changes</button>
</div>
</fieldset>
</form>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<script src="../resources/bootstrap/js/bootstrap-collapse.js"></script>
<!-- Javascript end -->
</body>
</html>
