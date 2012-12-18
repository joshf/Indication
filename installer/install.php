<!-- SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker) -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker: Installer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<link href="../resources/datatables/DT_bootstrap.css" type="text/css" rel="stylesheet">
<style>
    body {
        padding-top: 60px;
    }
    #footer {
        background-color: #f5f5f5;
    }
    @media (max-width: 767px) {
        #footer {
            margin-left: -20px;
            margin-right: -20px;
            padding-left: 20px;
			padding-right: 20px;
        }
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
<div class="navbar navbar-inverse navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<a class="brand" href="#">SHTracker</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Installer</h1>
</div>		
<?php

if (!isset($_POST["doinstall"])) {
    die("<p>This installer can not be called directly!</p><p><a href=\"../installer\" class=\"btn\">Go To Installer</a></p></body></html>");
}

//Get new settings from POST
$dbhost = $_POST["dbhost"];
$dbuser = $_POST["dbuser"];
$dbpassword = $_POST["dbpassword"];
$dbname = $_POST["dbname"];
$adminuser = $_POST["adminuser"];
$adminemail= $_POST["adminemail"];
$adminpassword = sha1($_POST["adminpassword"]);
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];
$uniquekey = str_shuffle("abcdefghijklmnopqrstuvwxyz123456789");

$installstring = "<?php

//Database Settings
define(\"DB_HOST\", \"$dbhost\");
define(\"DB_USER\", \"$dbuser\");
define(\"DB_PASSWORD\", \"$dbpassword\");
define(\"DB_NAME\", \"$dbname\");

//Admin Details
define(\"ADMIN_USER\", \"$adminuser\");
define(\"ADMIN_EMAIL\", \"$adminemail\");
define(\"ADMIN_PASSWORD\", \"$adminpassword\");

//Other Settings
define(\"WEBSITE\", \"$website\");
define(\"PATH_TO_SCRIPT\", \"$pathtoscript\");
define(\"COUNT_UNIQUE_ONLY_STATE\", \"Disabled\");
define(\"COUNT_UNIQUE_ONLY_TIME\", \"24\");
define(\"UNIQUE_KEY\", \"$uniquekey\");
define(\"AD_CODE\", \"\");
define(\"JQUERY_THEME\", \"flick\");

?>";

//Check if we can connect
$con = mysql_connect($dbhost, $dbuser, $dbpassword);
if (!$con) {
    die("<p>Could not connect to database: " . mysql_error() . ". Please go back and try again.</p><p><a href=\"javascript:history.go(-1)\" class=\"btn\">Go Back</a></p></body></html>");
}

//Check if database exists
$does_db_exist = mysql_select_db($dbname, $con);
if (!$does_db_exist) {
    die("<p>Database does not exist: " . mysql_error() . ". Please go back and try again.</p><p><a href=\"javascript:history.go(-1)\ class=\"btn\">Go Back</a></p></body></html>");
}

//Create Data table
$createtable = "CREATE TABLE Data (
name VARCHAR(100) NOT NULL,
id VARCHAR(25) NOT NULL,
url VARCHAR(200) NOT NULL,
count INT(10) NOT NULL default \"0\",
protect TINYINT(1) NOT NULL default \"0\",
password VARCHAR(200),
showads TINYINT(1) NOT NULL default \"0\",
PRIMARY KEY (id)
) ENGINE = MYISAM;";

//Run query
mysql_query($createtable);

//Write Config
$configfile = fopen("../config.php", "w");
fwrite($configfile, $installstring);
fclose($configfile);

mysql_close($con);

?>
<h1>SHTracker: Install Complete</h1>
<p>SHTracker has been successfully installed. Please delete the "installer" folder from your server, as it poses a potential security risk!</p>
<p><a href="../login.php" class="btn">Go To Login</a></p>
</div>
<!-- Content end -->
<!-- Footer start -->	
<div id="footer">
<div class="container">
<p class="muted credit">SHTracker <? echo $version; ?> (<? echo $rev; ?>) "<? echo $codename; ?>" Copyright <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <? echo date("Y"); ?>. Uses Twitter Bootstrap.</p>
</div>
</div>
<!-- Footer end -->
</body>
</html>