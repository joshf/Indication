<?php

require("login.php");

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../config.php");

//Get current settings
$currentdbhost = DB_HOST;
$currentdbuser = DB_USER;
$currentdbpassword = DB_PASSWORD;
$currentdbname = DB_NAME;
$currentadminuser = ADMIN_USER;
$currentadminpassword = ADMIN_PASSWORD;
$currentwebsite = WEBSITE;
$currentpathtoscript = PATH_TO_SCRIPT;
$currentwaitstate = WAIT_STATE;
$currentwaitmessage = WAIT_MESSAGE;
$currentwaitadcode = WAIT_AD_CODE; 

if (isset($_POST["Save"])) {

//Get new settings from POST
$dbhost = $_POST["dbhost"];
$dbuser = $_POST["dbuser"];
$dbpassword = $_POST["dbpassword"];
$dbname = $_POST["dbname"];
$adminuser = $_POST["adminuser"];
$adminpassword = $_POST["adminpassword"];
if ($adminpassword != $currentadminpassword) {
    $adminpassword = sha1("$adminpassword");
}
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];
$waitstate = $_POST["waitstate"];
if (isset($_POST["waitmessage"])) {
    $waitmessage = $_POST["waitmessage"];
    $waitadcode = $_POST["waitadcode"];
}
$randomkey = RANDOM_KEY; 

//Remember previous settings for wait
if (empty($waitmessage)) {
    $waitmessage = $currentwaitmessage;
}
if (empty($waitadcode)) {
    $waitadcode = $currentwaitadcode;
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

//Wait Settings
define(\"WAIT_STATE\", \"$waitstate\");
define(\"WAIT_MESSAGE\", \"$waitmessage\");
define(\"WAIT_AD_CODE\", \"$waitadcode\");

//Random Key
define(\"RANDOM_KEY\", \"$randomkey\");

?>";
 
$configfile = fopen("../config.php", "w");
fwrite($configfile, $string);
fclose($configfile);

//Show updated values
header("Location: " . $_SERVER["REQUEST_URI"]);

}
 
?>
<html>
<head>
<title>SHTracker: Settings</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<h1>SHTracker: Settings</h1>
<p>Here you can change the settings for SHTracker.</p>
<p><strong>Database Settings:</strong></p>
<form method="post">
Host: <input type="text" name="dbhost" value="<? echo $currentdbhost; ?>" /><br />
User: <input type="text" name="dbuser" value="<? echo $currentdbuser; ?>" /><br />
Password: <input type="password" name="dbpassword" value="<? echo $currentdbpassword; ?>" /><br />
Name: <input type="text" name="dbname" value="<? echo $currentdbname; ?>" /><br />
<p><strong>Admin Details:</strong></p>
User: <input type="text" name="adminuser" value="<? echo $currentadminuser; ?>" /><br />
Password: <input type="password" name="adminpassword" value="<? echo $currentadminpassword; ?>" /><br />
<p><strong>Other settings:</strong></p>
Website Name: <input type="text" name="website" value="<? echo $currentwebsite; ?>" /><br />
Path to script: <input type="text" name="pathtoscript" value="<? echo $currentpathtoscript; ?>" /><br />
<p><strong>Wait settings:</strong></p>
<p>Make users wait before they are served their download. This is useful if you use adverts on your site.</p>
<?php

if ($currentwaitstate == "Enabled" ) {
    echo "<input type=\"radio\" name=\"waitstate\" value=\"Enabled\" checked/> Enabled<br />
    <input type=\"radio\" name=\"waitstate\" value=\"Disabled\" /> Disabled
    <p>Custom Message:</p><p><textarea cols=\"80\" rows=\"8\" name=\"waitmessage\">$currentwaitmessage</textarea></p>
    <p>Adsense/Ad Code: (HTML only, PHP will not work!)</p><p><textarea cols=\"80\" rows=\"8\" name=\"waitadcode\">$currentwaitadcode</textarea></p>";
} else {
    echo "<input type=\"radio\" name=\"waitstate\" value=\"Enabled\" /> Enabled<br />
    <input type=\"radio\" name=\"waitstate\" value=\"Disabled\" checked/> Disabled<br />";
}

?>
<p><input type="submit" name="Save" value="Save" /></p>
</form>
<hr />
<p><strong>Advanced settings:</strong></p>
<p><em>Do not use these settings/options unless you know what you are doing!</em></p>
<form action="actions/reset.php" method="post">
<p>To perform any of these actions, please enter your admin password.</p>
<p>Password: <input type="password" name="password" /></p>
<input type="submit" name="command" value="Reset All Counts to Zero" /><br />
<input type="submit" name="command" value="Delete All Downloads" />
</form>
<hr />
<p><a href="../admin">Go Back</a></p>
</body>
</html>