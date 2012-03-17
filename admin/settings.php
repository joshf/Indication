<?php

session_start();
if (!isset($_SESSION["is_logged_in"])) {
    header("Location: login.php");
    exit; 
}

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
$currentcountuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$currentcountuniqueonlytime = COUNT_UNIQUE_ONLY_TIME;
$currentprotectdownloadsstate = PROTECT_DOWNLOADS_STATE;
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
    $adminpassword = sha1($adminpassword);
}
$website = $_POST["website"];
$pathtoscript = $_POST["pathtoscript"];
$countuniqueonlystate = $_POST["countuniqueonlystate"];
if (isset($_POST["countuniqueonlytime"])) {
    $countuniqueonlytime = $_POST["countuniqueonlytime"];
}
$protectdownloadsstate = $_POST["protectdownloadsstate"];
$waitstate = $_POST["waitstate"];
if (isset($_POST["waitmessage"])) {
    $waitmessage = $_POST["waitmessage"];
    $waitadcode = $_POST["waitadcode"];
}

//Remember previous settings
if (empty($waitmessage)) {
    $waitmessage = $currentwaitmessage;
}
if (empty($waitadcode)) {
    $waitadcode = $currentwaitadcode;
}
if (empty($countuniqueonlytime)) {
    $countuniqueonlytime = $currentcountuniqueonlytime;
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
define(\"COUNT_UNIQUE_ONLY_STATE\", \"$countuniqueonlystate\");
define(\"COUNT_UNIQUE_ONLY_TIME\", \"$countuniqueonlytime\");
define(\"PROTECT_DOWNLOADS_STATE\", \"$protectdownloadsstate\");

//Wait Settings
define(\"WAIT_STATE\", \"$waitstate\");
define(\"WAIT_MESSAGE\", \"$waitmessage\");
define(\"WAIT_AD_CODE\", \"$waitadcode\");

?>";
 
$configfile = fopen("../config.php", "w");
fwrite($configfile, $string);
fclose($configfile);

//Show updated values
header("Location: " . $_SERVER["REQUEST_URI"] . "");

}
 
?>
<html>
<head>
<title>SHTracker: Settings</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
<h1>SHTracker: Settings</h1>
<p>Here you can change the settings for SHTracker.</p>
<p><b>Database Settings:</b></p>
<form method="post">
Host: <input type="text" name="dbhost" value="<? echo $currentdbhost; ?>" /><br />
User: <input type="text" name="dbuser" value="<? echo $currentdbuser; ?>" /><br />
Password: <input type="password" name="dbpassword" value="<? echo $currentdbpassword; ?>" /><br />
Name: <input type="text" name="dbname" value="<? echo $currentdbname; ?>" /><br />
<p><b>Admin Details:</b></p>
User: <input type="text" name="adminuser" value="<? echo $currentadminuser; ?>" /><br />
Password: <input type="password" name="adminpassword" value="<? echo $currentadminpassword; ?>" /><br />
<p><b>Other settings:</b></p>
Website Name: <input type="text" name="website" value="<? echo $currentwebsite; ?>" /><br />
Path to Script: <input type="text" name="pathtoscript" value="<? echo $currentpathtoscript; ?>" /><br />
<p><b>Wait settings:</b></p>
<p>Make users wait before they are served their download. This is useful if you use adverts on your site.</p>
<?php
if ($currentwaitstate == "Enabled" ) {
    echo "<input type=\"radio\" name=\"waitstate\" value=\"Enabled\" checked/> Enabled<br />
    <input type=\"radio\" name=\"waitstate\" value=\"Disabled\" /> Disabled
    <p>Custom Message:</p><p><textarea cols=\"80\" rows=\"8\" name=\"waitmessage\">$currentwaitmessage</textarea></p>
    <p>Adsense/Ad Code: (HTML only, PHP will not work!)</p><p><textarea cols=\"80\" rows=\"8\" name=\"waitadcode\">$currentwaitadcode</textarea></p>";
} else {
    echo "<input type=\"radio\" name=\"waitstate\" value=\"Enabled\" /> Enabled<br />
    <input type=\"radio\" name=\"waitstate\" value=\"Disabled\" checked/> Disabled";
}
?>
<p><b>Count Unique Visitors Only:</b></p>
<p>This settings allows you to make sure an individual users clicks are only counted once.</p>
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
    echo "<p>Hours to consider a user unique: <input type=\"text\" name=\"countuniqueonlytime\" value=\"$currentcountuniqueonlytime\" /></p>
    <input type=\"radio\" name=\"countuniqueonlystate\" value=\"Enabled\" checked/> Enabled<br />
    <input type=\"radio\" name=\"countuniqueonlystate\" value=\"Disabled\" /> Disabled";
} else {
    echo "<input type=\"radio\" name=\"countuniqueonlystate\" value=\"Enabled\" /> Enabled<br />
    <input type=\"radio\" name=\"countuniqueonlystate\" value=\"Disabled\" checked/> Disabled";
}
?>
<p><b>Download Protection:</b></p>
<p>Choose whether you want certain downloads to be password protected. If this is enabled, wait settings will be ignored.</p>
<?php
if ($currentprotectdownloadsstate == "Enabled" ) {
    echo "<input type=\"radio\" name=\"protectdownloadsstate\" value=\"Enabled\" checked/> Enabled<br />
    <input type=\"radio\" name=\"protectdownloadsstate\" value=\"Disabled\" /> Disabled
    <p>To set password protect for a download, go <a href=\"protect\">here</a>.</p>";
} else {
    echo "<input type=\"radio\" name=\"protectdownloadsstate\" value=\"Enabled\" /> Enabled<br />
    <input type=\"radio\" name=\"protectdownloadsstate\" value=\"Disabled\" checked/> Disabled";
}
?>
<p><input type="submit" name="Save" value="Save" /></p>
</form>
<hr />
<script type="text/javascript">
function ispasswordempty() 
{
    if (!$("input[name=password]").val()) {
        alert("Please enter your admin password!");
        return false;
    }
}
</script>
<p><b>Advanced Options:</b></p>
<p><i>Do not use these options unless you know what you are doing!</i></p>
<form action="actions/advanced.php" method="post">
<p>To perform any of these actions, please enter your admin password.</p>
<p>Password: <input type="password" name="password" /></p>
<input type="submit" name="command" onClick="return ispasswordempty()" value="Reset All Counts to Zero" /><br />
<input type="submit" name="command" onClick="return ispasswordempty()" value="Delete All Downloads" />
</form>
<hr />
<p><a href="../admin">&larr; Go Back</a></p>
</body>
</html>