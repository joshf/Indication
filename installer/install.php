<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html>
<head>
<title>SHTracker: Installer</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<?php

//Get new settings from POST
$dbhost = $_POST["dbhost"];
$dbuser = $_POST["dbuser"];
$dbpassword = $_POST["dbpassword"];
$dbname = $_POST["dbname"];
$adminuser = $_POST["adminuser"];
$adminpassword = sha1($_POST["adminpassword"]);
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];

//Write Settings
$installstring = "<?php

//Database Settings
define(\"DB_HOST\", \"$dbhost\");
define(\"DB_USER\", \"$dbuser\");
define(\"DB_PASSWORD\", \"$dbpassword\");
define(\"DB_NAME\", \"$dbname\");

//Admin Details
define(\"ADMIN_USER\", \"$adminuser\");
define(\"ADMIN_PASSWORD\", \"$adminpassword\");

//Other Settings
define(\"WEBSITE\", \"$website\");
define(\"PATH_TO_SCRIPT\", \"$pathtoscript\");
define(\"COUNT_UNIQUE_ONLY_STATE\", \"Disabled\");
define(\"COUNT_UNIQUE_ONLY_TIME\", \"24\");

//Wait Settings
define(\"WAIT_STATE\", \"Disabled\");
define(\"WAIT_MESSAGE\", \"\");
define(\"WAIT_AD_CODE\", \"\");

?>";

//Create Data table
$con = mysql_connect($dbhost, $dbuser, $dbpassword);
if (!$con) {
    die("<h1>SHTracker: Install Failed</h1><p>Could not connect to database: " . mysql_error() . "</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

mysql_select_db($dbname, $con);

$createtable = "CREATE TABLE Data (
name VARCHAR(100) NOT NULL,
id VARCHAR(25) NOT NULL,
url VARCHAR(200) NOT NULL,
count INT(10) NOT NULL default \"0\",
protect VARCHAR(10) NOT NULL default \"false\",
password VARCHAR(200) NOT NULL,
PRIMARY KEY (id)
) ENGINE = MYISAM;";

mysql_query($createtable);

mysql_close($con);

//Write Config
$configfile = fopen("../config.php", "w");
fwrite($configfile, $installstring);
fclose($configfile);
 
?>
<h1>SHTracker: Install Complete</h1>
<p>SHTracker has been successfully installed. Please delete the "installer" folder from your server, as it poses a potential security risk!</p>
<p>It may also be helpful to make config.php unwritable.</p>
<p><a href="../admin">Go To Login</a></p>
</body>
</html>