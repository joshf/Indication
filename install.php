<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html>
<head>
<title>SHTracker: Install</title>
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php

//Security check
if (file_exists("config.php")) {
    die("<h1>SHTracker: Error</h1><p>SHTracker has already been installed! If you wish to reinstall SHTracker, please delete config.php from your server.</p><hr /><p><a href=\"admin\">Go Back</a></p></body></html>"); 
}

if (isset($_POST["Install"])) {
 
//Get new settings from POST
$dbhost = $_POST["dbhost"];
$dbuser = $_POST["dbuser"];
$dbpassword = $_POST["dbpassword"];
$dbname = $_POST["dbname"];
$adminuser = $_POST["adminuser"];
$adminpassword = $_POST["adminpassword"];
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];

//Make sure we actually install something
if (empty($dbhost)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a database host.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}
if (empty($dbuser)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a database user.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}
if (empty($dbpassword)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a database password.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}
if (empty($dbname)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a database name.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}
if (empty($adminuser)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a admin user.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}
if (empty($adminpassword)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a admin password.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
} else {
    if (strlen($adminpassword) < "6") {
        die("<h1>SHTracker: Error</h1><p>You password must be longer than six characters.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
    } else {
        $adminpassword = sha1($adminpassword);
    }
}
if (empty($website)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a website name.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}
if (empty($pathtoscript)) {
    die("<h1>SHTracker: Error</h1><p>Please enter a script path.</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}

$string = "<?php

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
define(\"LOG_UPDATES_STATE\", \"Disabled\");

//Wait Settings
define(\"WAIT_STATE\", \"Disabled\");
define(\"WAIT_MESSAGE\", \"\");
define(\"WAIT_AD_CODE\", \"\");

?>";
 
$configfile = fopen("config.php", "w");
fwrite($configfile, $string);
fclose($configfile);

//Create Data table
$con = mysql_connect($dbhost, $dbuser, $dbpassword);
if (!$con) {
    die("<h1 style=\"color: red\">SHTracker: Install failed!</h1><p>Could not connect: " . mysql_error() . "</p><hr /><p><a href=\"javascript:history.go(-1)\">Go Back</a></p></body></html>");
}

mysql_select_db($dbname, $con);

$createtable = "CREATE TABLE Data (
name VARCHAR(100) NOT NULL,
id VARCHAR(25) NOT NULL,
url VARCHAR(200) NOT NULL,
count INT(10) NOT NULL default \"0\",
PRIMARY KEY (id)
) ENGINE = MYISAM;";

mysql_query($createtable);

mysql_close($con);

die("<h1 style=\"color: green\">SHTracker: Install complete</h1><p>Please delete this file (install.php) from your server, as it poses a security risk!</p><p>It may also be helpful to make config.php unwritable.</p><p><a href=\"admin\">Go To Login</a></p></body></html>");

}
 
?>
<h1>SHTracker: Install</h1>
<p><em>All fields are required</em></p>
<p><strong>Database Settings:</strong></p>
<form method="post">
Host: <input type="text" name="dbhost" value="localhost" /><br />
User: <input type="text" name="dbuser" /><br />
Password: <input type="password" name="dbpassword" /><br />
Name: <input type="text" name="dbname" /><br />
<p><strong>Admin Details:</strong></p>
User: <input type="text" name="adminuser" /><br />
Password: <input type="password" name="adminpassword" /><br />
<p><strong>Other Settings:</strong></p>
Website Name: <input type="text" name="website" /><br />
Path to Script: <input type="text" name="pathtoscript" /><br />
<p><input type="submit" name="Install" value="Install"></p>
</form>
</body>
</html>