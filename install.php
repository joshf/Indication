<!-- SHTracker, Copyright Josh Fradley 2012 -->
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
$adminpassword = sha1($_POST["adminpassword"]);
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];
$randomkey = str_shuffle("abcdefghijklmnopqrstuvwxyz123456789");

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

//Random Key
define(\"RANDOM_KEY\", \"$randomkey\");

?>";
 
$configfile = fopen("config.php", "w");
fwrite($configfile, $string);
fclose($configfile);
echo "<h1 style=\"color:green\">SHTracker: Install complete</h1><p>Please delete this file (install.php) from your server, as it poses a security risk!</p><p>It may also be helpful to make config.php unwritable.</p><p><a href=\"admin\">Admin Home</a></p></body></html>";
exit;
 
}
 
?>
<h1>SHTracker: Install</h1>
<p><strong>Database Settings:</strong></p>
<form method="post">
Host: <input type="text" name="dbhost" /><br />
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