<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    die("Error: Config file not found!");
}

require_once("config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit;
} 

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

$getusersettings = mysqli_query($con, "SELECT `user`, `password`, `email`, `salt`, `api_key` FROM `users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysqli_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysqli_fetch_assoc($getusersettings);

//Get current settings
$currentwebsite = WEBSITE;
$currentpathtoscript = PATH_TO_SCRIPT;
$currentcountuniqueonlystate = COUNT_UNIQUE_ONLY_STATE;
$currentcustomurlstate = CUSTOM_URL_STATE;
$currentcustomurl = CUSTOM_URL; 

if (!empty($_POST)) {
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
    $pathtoscript = rtrim($_POST["pathtoscript"], "/");	
    $countuniqueonlystate = $_POST["countuniqueonlystate"];
    $customurlstate = $_POST["customurlstate"];
    $customurl = $_POST["customurl"];

    //Remember previous settings
    if (empty($customurl)) {
        $customurl = $currentcustomurl;
    }

    $settingsstring = "<?php\n\n//Database Settings\ndefine('DB_HOST', '" . DB_HOST . "');\ndefine('DB_USER', '" . DB_USER . "');\ndefine('DB_PASSWORD', '" . DB_PASSWORD . "');\ndefine('DB_NAME', '" . DB_NAME . "');\n\n//Other Settings\ndefine('SALT', '" . SALT . "');\ndefine('WEBSITE', " . var_export($website, true) . ");\ndefine('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");\ndefine('COUNT_UNIQUE_ONLY_STATE', " . var_export($countuniqueonlystate, true) . ");\ndefine('CUSTOM_URL_STATE', " . var_export($customurlstate, true) . ");\ndefine('CUSTOM_URL', " . var_export($customurl, true) . ");\n\n?>";

    //Update Settings
    mysqli_query($con, "UPDATE `users` SET `user` = \"$user\", `password` = \"$password\", `email` = \"$email\", `salt` = \"$salt\" WHERE `user` = \"" . $resultgetusersettings["user"] . "\"");
    
    //Write config
    $configfile = fopen("config.php", "w");
    fwrite($configfile, $settingsstring);
    fclose($configfile);

    //Show updated values
    header("Location: settings.php");
    exit;
}

mysqli_close($con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="assets/favicon.ico">
<title>Indication &raquo; Settings</title>
<link rel="apple-touch-icon" href="assets/icon.png">
<link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="screen">
<link rel="stylesheet" href="assets/css/indication.css" type="text/css" media="screen">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="index.php">Indication</a>
</div>
<div id="navbar" class="navbar-collapse collapse">
<ul class="nav navbar-nav navbar-right">
<li><a href="index.php">Dashboard</a></li>
<li class="active"><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>
<div class="container-fluid">
<div class="row">
<div class="col-sm-3 col-md-2 sidebar">
<ul class="nav nav-sidebar">
<li><a href="index.php">Overview</a></li>
<li><a href="breakdowns.php">Breakdowns</a></li>
<li><a href="export.php">Export</a></li>
</ul>
<ul class="nav nav-sidebar">
<li><a href="add.php">Add New</a></li>
<li><a href="edit.php">Edit</a></li>
</ul>
</div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h1 class="page-header">Settings</h1>
<form method="post" id="settingsform" autocomplete="off">
<div class="form-group">
<label class="control-label" for="user">User</label>
<input type="text" class="form-control" id="user" name="user" value="<?php echo $resultgetusersettings["user"]; ?>" placeholder="Enter a username..." required>
</div>
<div class="form-group">
<label class="control-label" for="email">Email</label>
<input type="email" class="form-control" id="email" name="email" value="<?php echo $resultgetusersettings["email"]; ?>" placeholder="Type an email..." required>
</div>
<div class="form-group">
<label class="control-label" for="password">Password</label>
<input type="password" class="form-control" id="password" name="password" value="<?php echo $resultgetusersettings["password"]; ?>" placeholder="Enter a password..." required>
</div>
<div class="form-group">
<label for="website">Website</label>
<input type="text" class="form-control" id="website" name="website" value="<?php echo $currentwebsite; ?>" placeholder="Enter your websites name..." required>
</div>
<div class="form-group">
<label for="pathtoscript">Path to Script</label>
<input type="url" class="form-control" id="pathtoscript" name="pathtoscript" value="<?php echo $currentpathtoscript; ?>" placeholder="Type the path to Indication..." required>
</div>
<h4>Count Unique Visitors Only</h4>
<p>This settings allows you to make sure an individual user's clicks are only counted once.</p>
<div class="radio">
<?php
if ($currentcountuniqueonlystate == "Enabled" ) {
    echo "<label><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\" checked=\"checked\"> Enabled</label></div>
        <div class=\"radio\"><label><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label><input type=\"radio\" id=\"countuniqueonlystateenable\" name=\"countuniqueonlystate\" value=\"Enabled\"> Enabled</label></div>
     <div class=\"radio\"><label><input type=\"radio\" id=\"countuniqueonlystatedisable\" name=\"countuniqueonlystate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>
<h4>Custom URL</h4>
<p>Allows you to use a custom get url (for clean URL's).</p>
<div class="radio">
<?php
if ($currentcustomurlstate == "Enabled" ) {
    echo "<label><input type=\"radio\" id=\"customurlstateenable\" name=\"customurlstate\" value=\"Enabled\" checked=\"checked\"> Enabled</label></div>
        <div class=\"radio\"><label><input type=\"radio\" id=\"customurlstatedisable\" name=\"customurlstate\" value=\"Disabled\"> Disabled</label>";    
} else {
    echo "<label><input type=\"radio\" id=\"customurlstateenable\" name=\"customurlstate\" value=\"Enabled\"> Enabled</label></div>
     <div class=\"radio\"><label><input type=\"radio\" id=\"customurlstatedisable\" name=\"customurlstate\" value=\"Disabled\" checked=\"checked\"> Disabled</label>";   
}   
?> 
</div>
<?php
if ($currentcustomurlstate == "Enabled" ) {
?>
<div class="form-group">
<label for="customurl">Custom URL</label>
<input type="url" class="form-control" id="customurl" name="customurl" value="<?php echo $currentcustomurl; ?>" placeholder="Enter a custom URL..." required>
</div>
<?php
}
?>
<button type="submit" class="btn btn-default">Update</button>
</form>
<br>
<hr>
<h5>API key</h5>
<p>Your API key is: <b><span id="api_key"><?php echo $resultgetusersettings["api_key"]; ?></span></b></p>
<button id="generateapikey" class="btn btn-default">Generate New Key</button>
</div>
</div>
</div>
<script src="assets/bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap-validator/dist/validator.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/js-cookie/src/js.cookie.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function() {
    if (Cookies.get("indication_settings_updated")) {
        $.notify({
            message: "Settings updated!",
            icon: "glyphicon glyphicon-ok",
        },{
            type: "success",
            allow_dismiss: true
        });
        Cookies.remove("indication_settings_updated");
    }
    $("#settingsform").validator({
        disable: true
    });
    $("form").submit(function() {
        Cookies.set("indication_settings_updated", "1", { expires: 7 });
    });
    $("#generateapikey").click(function() {
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: "action=generateapikey",
            error: function() {
                $("#api_key").html("Could not generate key. Failed to connect to worker.</b>");
            },
            success: function(api_key) {
                $("#api_key").html(api_key);
            }
        });
    });
});
</script>
</body>
</html>