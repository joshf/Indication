<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
    die("Error: Config file not found! Please reinstall Indication.");
}

require_once("../config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit; 
} 

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

mysql_select_db(DB_NAME, $con);

$getusersettings = mysql_query("SELECT `user`, `password`, `email`, `salt`, `theme` FROM `Users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysql_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysql_fetch_assoc($getusersettings);

//Get current settings
$currentwebsite = WEBSITE;
$currentpathtoscript = PATH_TO_SCRIPT;
$currentadcode = htmlspecialchars_decode(AD_CODE);
$currentcountuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$currentcountuniqueonlytime = COUNT_UNIQUE_ONLY_TIME;
$currentignoreadminstate = IGNORE_ADMIN_STATE; 

if (isset($_POST["save"])) {
    //Get new settings from POST
    //Get new settings from POST
    $user = $_POST["user"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $salt = $resultgetusersettings["salt"];
    if ($password != $resultgetusersettings["password"]) {
        //Salt and hash passwords
        $randsalt = md5(uniqid(rand(), true));
        $salt = substr($randsalt, 0, 3);
        $hashedpassword = hash("sha256", $password);
        $password = hash("sha256", $salt . $hashedpassword);
    }
    $website = $_POST["website"];
    $pathtoscript = $_POST["pathtoscript"];
    if (isset($_POST["advertcode"])) {
        if (get_magic_quotes_gpc()) {
            $adcode = stripslashes(htmlspecialchars($_POST["advertcode"]));
        } else {
            $adcode = htmlspecialchars($_POST["advertcode"]);
        }
    }
    $countuniqueonlystate = $_POST["countuniqueonlystate"];
    $countuniqueonlytime = $_POST["countuniqueonlytime"];
    $ignoreadminstate = $_POST["ignoreadminstate"];
    $theme = $_POST["theme"];

    //Remember previous settings
    if (empty($adcode)) {
        $adcode = $currentadcode;
    }

    $settingsstring = "<?php\n\n//Database Settings\ndefine('DB_HOST', '" . DB_HOST . "');\ndefine('DB_USER', '" . DB_USER . "');\ndefine('DB_PASSWORD', '" . DB_PASSWORD . "');\ndefine('DB_NAME', '" . DB_NAME . "');\n\n//Other Settings\ndefine('WEBSITE', " . var_export($website, true) . ");\ndefine('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");\ndefine('AD_CODE', " . var_export($adcode, true) . ");\ndefine('COUNT_UNIQUE_ONLY_STATE', " . var_export($countuniqueonlystate, true) . ");\ndefine('COUNT_UNIQUE_ONLY_TIME', " . var_export($countuniqueonlytime, true) . ");\ndefine('IGNORE_ADMIN_STATE', " . var_export($ignoreadminstate, true) . ");\ndefine('VERSION', '" . VERSION . "');\n\n?>";

    //Update Settings
    mysql_query("UPDATE Users SET `user` = \"$user\", `password` = \"$password\", `email` = \"$email\", `salt` = \"$salt\", `theme` = \"$theme\" WHERE `user` = \"" . $resultgetusersettings["user"] . "\"");
    
    //Write config
    $configfile = fopen("../config.php", "w");
    fwrite($configfile, $settingsstring);
    fclose($configfile);

    //Show updated values
    header("Location: settings.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Settings</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if ($resultgetusersettings["theme"] == "default") {
    echo "<link href=\"../resources/bootstrap/css/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.2/" . $resultgetusersettings["theme"] . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}
?>
<link href="../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
<link href="../resources/bootstrap-notify/css/bootstrap-notify.min.css" type="text/css" rel="stylesheet">
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
<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">Indication</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li class="divider-vertical"></li>
<li><a href="index.php">Home</a></li>
<li><a href="add.php">Add</a></li>
<li><a href="edit.php">Edit</a></li>
</ul>
<ul class="nav pull-right">
<li class="divider-vertical"></li>
<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $resultgetusersettings["user"]; ?> <b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</li>
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
<div class="notifications top-right"></div>
<form method="post" autocomplete="off">
<fieldset>
<h4>User Details</h4>
<div class="control-group">
<label class="control-label" for="user">User</label>
<div class="controls">
<input type="text" id="user" name="user" value="<?php echo $resultgetusersettings["user"]; ?>" placeholder="Enter a username..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="email">Email</label>
<div class="controls">
<input type="email" id="email" name="email" value="<?php echo $resultgetusersettings["email"]; ?>" placeholder="Type an email..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="password">Password</label>
<div class="controls">
<input type="password" id="password" name="password" value="<?php echo $resultgetusersettings["password"]; ?>" placeholder="Enter a password..." required>
</div>
</div>
<h4>Site Settings</h4>
<div class="control-group">
<label class="control-label" for="website">Website</label>
<div class="controls">
<input type="text" id="website" name="website" value="<?php echo $currentwebsite; ?>" placeholder="Enter your websites name..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="pathtoscript">Path to Script</label>
<div class="controls">
<input type="text" id="pathtoscript" name="pathtoscript" value="<?php echo $currentpathtoscript; ?>" placeholder="Type the path to Indication..." pattern="(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-?]*)*\/?" data-validation-pattern-message="Please enter a valid URL" required>
</div>
</div>
<h4>Ad Code</h4>
<p>Show an advert before user can continue to their download. This can be changed on a per download basis.</p>
<div class="alert alert-warning"><b>Warning:</b> On some server configurations using HTML code here may produce errors.</div>
<div class="control-group">
<div class="controls">
<textarea id="advertcode" name="advertcode" placeholder="Enter a ad code..."><?php echo $currentadcode; ?></textarea>
</div>
</div>
<h4>Count Unique Visitors Only</h4>
<p>This settings allows you to make sure an individual user's clicks are only counted once.</p>
<div class="control-group">
<div class="controls">
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
    echo "<label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\" checked=\"checked\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>  
</div>
<div class="control-group">
<label class="control-label" for="countuniqueonlytime">Time to consider a user unique</label>
<div class="controls">
<input type="number" id="countuniqueonlytime" name="countuniqueonlytime" value="<?php echo $currentcountuniqueonlytime; ?>" placeholder="Enter a time..." min="0" required>
</div>
</div>
<h4>Ignore Admin</h4>
<p>This settings prevents downloads being counted when you are logged in to Indication.</p>
<div class="control-group">
<div class="controls">
<?php
if ($currentignoreadminstate == "Enabled" ) {
    echo "<label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstateenable\" name=\"ignoreadminstate\" value=\"Enabled\" checked=\"checked\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstatedisable\" name=\"ignoreadminstate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstateenable\" name=\"ignoreadminstate\" value=\"Enabled\"> Enabled</label>
    <label class=\"radio\"><input type=\"radio\" id=\"ignoreadminstatedisable\" name=\"ignoreadminstate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>  
</div>
<h4>Theme</h4>
<div class="control-group">
<label class="control-label" for="theme">Select a theme</label>
<div class="controls">
<?php
$themes = array("default", "amelia", "cerulean", "cosmo", "cyborg", "flatly", "journal", "readable", "simplex", "slate", "spacelab", "spruce", "superhero", "united");

echo "<select id=\"theme\" name=\"theme\">";
foreach ($themes as $value) {
    if ($value == $resultgetusersettings["theme"]) {
        echo "<option value=\"$value\" selected=\"selected\">". ucfirst($value) . "</option>";
    } else {
        echo "<option value=\"$value\">". ucfirst($value) . "</option>";
    }
}
echo "</select>";
?>
</div>
</div>
<div class="form-actions">
<button type="submit" name="save" class="btn btn-primary">Save Changes</button>
</div>
</fieldset>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="../resources/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.min.js"></script>
<script src="../resources/validation/jqBootstrapValidation.min.js"></script>
<script src="../resources/bootstrap-notify/js/bootstrap-notify.min.js"></script>
<script src="../resources/cookie/jquery.cookie.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    if ($.cookie("settings_updated")) {
        $(".top-right").notify({
            type: "info",
            transition: "fade",
            icon: "info-sign",
            message: {
                text: "Settings saved!"
            }
        }).show();
        $.removeCookie("settings_updated");
    }
    $("form").submit(function() {
        $.cookie("settings_updated", "true");
    });
    $("input").not("[type=submit]").jqBootstrapValidation();
});
</script>
<!-- Javascript end -->
</body>
</html>