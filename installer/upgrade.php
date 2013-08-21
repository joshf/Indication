<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
    header("Location: index.php");
}

require_once("../config.php");

//Check if we can connect
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

//Check if database exists
$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
}

//Define Version
$version = "4.4";

if ($version == VERSION) {
    die("Information: The latest version of Burden is already installed and an upgrade is not required.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Upgrade</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 60px;
}
@media (max-width: 980px) {
    body {
        padding-top: 0;
    }
}
</style>
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
<a class="brand" href="#">Indication</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Upgrade</h1>
</div>
<?php

$dbhost = DB_HOST;
$dbuser = DB_USER;
$dbpassword = DB_PASSWORD;
$dbname = DB_NAME;
$adminuser = ADMIN_USER;

//$adminpassword = ADMIN_PASSWORD;
//$salt = SALT;

//Salt and hash passwords
//From 4.3.1 --> 4.4
$temppassword = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz123456789"), 0, 6);
$randsalt = md5(uniqid(rand(), true));
$salt = substr($randsalt, 0, 3);
$hashedpassword = hash("sha256", $temppassword);
$adminpassword = hash("sha256", $salt . $hashedpassword);

$website = WEBSITE;
$pathtoscript = PATH_TO_SCRIPT;
$adcode = AD_CODE;
$countuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$countuniqueonlytime = COUNT_UNIQUE_ONLY_TIME;
$ignoreadminstate = IGNORE_ADMIN_STATE;
$theme = THEME;

$updatestring = "<?php

//Database Settings
define('DB_HOST', " . var_export($dbhost, true) . ");
define('DB_USER', " . var_export($dbuser, true) . ");
define('DB_PASSWORD', " . var_export($dbpassword, true) . ");
define('DB_NAME', " . var_export($dbname, true) . ");

//Admin Details
define('ADMIN_USER', " . var_export($adminuser, true) . ");
define('ADMIN_PASSWORD', " . var_export($adminpassword, true) . ");
define('SALT', " . var_export($salt, true) . ");

//Other Settings
define('WEBSITE', " . var_export($website, true) . ");
define('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");
define('AD_CODE', " . var_export($adcode, true) . ");
define('COUNT_UNIQUE_ONLY_STATE', " . var_export($countuniqueonlystate, true) . ");
define('COUNT_UNIQUE_ONLY_TIME', " . var_export($countuniqueonlytime, true) . ");
define('IGNORE_ADMIN_STATE', " . var_export($ignoreadminstate, true) . ");
define('THEME', " . var_export($theme, true) . ");
define('VERSION', " . var_export($version, true) . ");

?>";

//Write Config
$configfile = fopen("../config.php", "w");
fwrite($configfile, $updatestring);
fclose($configfile);

mysql_close($con);

?>
<div class="alert alert-success">
<h4 class="alert-heading">Upgrade Complete</h4>
<p>Indication has been successfully upgraded. Because Indication <?php echo $version; ?> uses salt password hashing, your password is now <b><?php echo $temppassword; ?></b>. Please change it to something more memorable as soon as possible using the settings page.<p><a href="../admin/login.php" class="btn btn-success">Go To Login</a></p>
</div>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="../resources/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.min.js"></script>
<!-- Javascript end -->
</body>
</html>