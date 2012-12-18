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
$currentjquerytheme = JQUERY_THEME;

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
$jquerytheme = $_POST["jquerytheme"];

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
define(\"JQUERY_THEME\", \"$jquerytheme\");

?>";

//Write config
$configfile = fopen("../config.php", "w");
fwrite($configfile, $settingsstring);
fclose($configfile);

//Show updated values
header("Location: " . $_SERVER["REQUEST_URI"] . "");

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
<li class="divider-vertical"></li>
<li class="active"><a href="settings.php">Settings</a></li>
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
<p>FIXME: Give a notice when user updates settings</p>
<form method="post">
<p><b>Admin Details:</b></p>
<p>User: <input type="text" name="adminuser" value="<? echo $currentadminuser; ?>" /><br />
Email: <input type="text" name="adminemail" value="<? echo $currentadminemail; ?>" /><br />
Password: <input type="password" name="adminpassword" value="<? echo $currentadminpassword; ?>" /></p>
<p><b>Other settings:</b></p>
<p>Website Name: <input type="text" name="website" value="<? echo $currentwebsite; ?>" /><br />
Path to Script: <input type="text" name="pathtoscript" value="<? echo $currentpathtoscript; ?>" /></p>
<p><b>Ad Code:</b></p>
<p>Show an advert before user can continue to their download. This can be changed on a per download basis.</p>
<small><b>N.B:</b> On some server configurations using html code here may produce errors.</small>
<p><textarea cols="80" rows="8" name="adcode"><? echo $currentadcode; ?></textarea></p>
<p><b>Count Unique Visitors Only:</b></p>
<p>This settings allows you to make sure an individual users' clicks are only counted once.</p>
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
    echo "<p>Hours to consider a user unique: <input type=\"text\" name=\"countuniqueonlytime\" value=\"$currentcountuniqueonlytime\" /></p>
    <p><input type=\"radio\" name=\"countuniqueonlystate\" value=\"Enabled\" checked/> Enabled<br />
    <input type=\"radio\" name=\"countuniqueonlystate\" value=\"Disabled\" /> Disabled</p>";
} else {
    echo "<p><input type=\"radio\" name=\"countuniqueonlystate\" value=\"Enabled\" /> Enabled<br />
    <input type=\"radio\" name=\"countuniqueonlystate\" value=\"Disabled\" checked/> Disabled</p>";
}
?>
<button type="submit" class="btn btn-primary" name="save">Save Changes</button>
</form>
<p><h4>Database Backup</h4><p>
<?

if (isset($_GET["backup"])) {

    //Connect to database    
    $con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    if (!$con) {
        die("Could not connect: " . mysql_error());
    }
    
    mysql_select_db(DB_NAME, $con);
    
    $getdata = mysql_query("SELECT * FROM Data");
    $string = "CREATE TABLE Data (
    name VARCHAR(100) NOT NULL,
    id VARCHAR(25) NOT NULL,
    url VARCHAR(200) NOT NULL,
    count INT(10) NOT NULL default \"0\",
    protect TINYINT(1) NOT NULL default \"0\",
    password VARCHAR(200),
    showads TINYINT(1) NOT NULL default \"0\",
    PRIMARY KEY (id)
    ) ENGINE = MYISAM; \n\nINSERT INTO Data (name, id, url, count, protect, password, showads) VALUES ";
    
    while($row = mysql_fetch_assoc($getdata)) {
        $string .= "('" . $row["name"] . "', '" . $row["id"] . "', '" . $row["url"] . "', '" . $row["count"] . "', '" . $row["protect"] . "', '" . $row["password"] . "', '" . $row["showads"] . "'), ";
    }
    
    //Remove last comma
    $datastring = substr_replace($string, "", -2);
    
    echo "<p>Copy this somewhere safe and use your database admin tool to run as a SQL query.</p><p><textarea cols=\"80\" rows=\"16\">$datastring</textarea></p>"; 
} else {
    echo "<p><button class=\"btn\" onClick=\"window.location = 'settings.php?backup'\">Backup Database</button></p>";
}

?>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<script src="../resources/bootstrap/js/bootstrap-collapse.js"></script>
<!-- Javascript end -->
</body>
</html>
