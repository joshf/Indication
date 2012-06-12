<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

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
$currentadcode = htmlspecialchars_decode(AD_CODE); 

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
if (isset($_POST["adcode"])) {
    if (get_magic_quotes_gpc()) {
        $adcode = stripslashes(htmlspecialchars($_POST["adcode"]));
    } else {
        $adcode = htmlspecialchars($_POST["adcode"]);
    }
}

//Remember previous settings
if (empty($adcode)) {
    $adcode = $currentadcode;
}
if (empty($countuniqueonlytime)) {
    $countuniqueonlytime = $currentcountuniqueonlytime;
}

$settingsstring = "<?php

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
define(\"UNIQUE_KEY\", \"$uniquekey\");
define(\"AD_CODE\", \"$adcode\");

?>";

//Write config
$configfile = fopen("../config.php", "w");
fwrite($configfile, $settingsstring);
fclose($configfile);

//Show updated values
header("Location: " . $_SERVER["REQUEST_URI"] . "");

}
 
?>
<html>
<head>
<title>SHTracker: Settings</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="http://hashmask.googlecode.com/svn/trunk/jquery.sha1.js"></script>
</head>
<body>
<script type="text/javascript">
$(document).ready(function() {
    /* Do RAC */
    $("#dorac").click(function() {
        if (!$("input[name=password]").val()) {
            $("#passwordempty").show("fast");
            setTimeout(function(){
                $("#passwordempty").hide("fast");
            },3000)
            return false;
        } else {
            var inputtedpassword = $("#password").val();
            var pass = $.sha1(inputtedpassword);
            var storedpass = "<? echo ADMIN_PASSWORD; ?>";
            if (pass != storedpass) {
                $("#incorrectpass").show("fast");
                setTimeout(function(){
                    $("#incorrectpass").hide("fast");
                },3000)
                return false;
            }
            var pass = $("#password").val();
            $.ajax({  
                type: "POST",  
                url: "actions/advanced.php",  
                data: "command=Reset All Counts to Zero&password="+ pass +"",
                success: function() { 
                    $("#racsuccess").show("fast");
                    setTimeout(function(){
                        $("#racsuccess").hide("fast");
                    },3000)
                }      
            });
        }
    });
    /* End */
    /* Do DAD */
    $("#dodad").click(function() {
        if (!$("input[name=password]").val()) {
            $("#passwordempty").show("fast");
            setTimeout(function(){
                $("#passwordempty").hide("fast");
            },3000)
            return false;
        } else {
            var inputtedpassword = $("#password").val();
            var pass = $.sha1(inputtedpassword);
            var storedpass = "<? echo ADMIN_PASSWORD; ?>";
            if (pass != storedpass) {
                $("#incorrectpass").show("fast");
                setTimeout(function(){
                    $("#incorrectpass").hide("fast");
                },3000)
                return false;
            }
            var pass = $("#password").val();
            $.ajax({  
                type: "POST",  
                url: "actions/advanced.php",  
                data: "command=Delete All Downloads&password="+ pass +"",
                success: function() { 
                    $("#dadsuccess").show("fast");
                    setTimeout(function(){
                        $("#dadsuccess").hide("fast");
                    },3000)
                }      
            });
        }
    });
    /* End */
    /* Hide DIVS */
    $("#racsuccess").click(function() {
        $("#racsuccess").hide("fast");
    });
    $("#dadsuccess").click(function() {
        $("#dadsuccess").hide("fast");
    });
    $("#passwordempty").click(function() {
        $("#passwordempty").hide("fast");
    });
    $("#incorrectpass").click(function() {
        $("#incorrectpass").hide("fast");
    });

    /* End */
});
</script>
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
<p><b>Ad Code:</b></p>
<p>Show an advert before user can continue to their download. This can be changed on a per download basis.</p>
<p><textarea cols="80" rows="8" name="adcode"><? echo $currentadcode; ?></textarea></p>
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
<p><input type="submit" name="Save" value="Save" /></p>
</form>
<hr />
<p><b>Advanced Options:</b></p>
<p><i>Do not use these options unless you know what you are doing!</i></p>
<p>To perform any of these actions, please enter your admin password.</p>
<p>Password: <input type="password" id="password" name="password" /></p>
<div id="passwordempty" style="display: none">
    <div id="noticebad"><p>Please enter a password!</p></div>
</div>
<div id="racsuccess" style="display: none">
    <div id="noticegood"><p>All downloads reset to zero!</p></div>
</div>
<div id="dadsuccess" style="display: none">
    <div id="noticegood"><p>All downloads deleted!</p></div>
</div>
<div id="incorrectpass" style="display: none">
    <div id="noticebad"><p>Incorrect password!</p></div>
</div>
<button id="dorac">Reset All Counts to Zero</button><br />
<button id="dodad">Delete All Downloads</button>
<hr />
<p><a href="../admin">&larr; Go Back</a></p>
</body>
</html>