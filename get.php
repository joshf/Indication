<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

ob_start();

if (!file_exists("config.php")) {
    header("Location: installer");
    exit;
}

require_once("config.php");

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

mysql_select_db(DB_NAME, $con);

//Get the ID from $_GET OR $_POST
if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} elseif (isset($_POST["id"])) {
    $id = mysql_real_escape_string($_POST["id"]);
} else {
    die("Error: ID cannot be blank.");
}

//Check if ID exists
$getinfo = mysql_query("SELECT `name`, `url`, `count` FROM `Data` WHERE `id` = \"$id\"");
$getinforesult = mysql_fetch_assoc($getinfo);
if (mysql_num_rows($getinfo) == 0) {
    die("Error: ID does not exist.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Indication</title>
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 30px;
    padding-bottom: 30px;
}
</style>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container">
<div class="navbar-header">
<a class="navbar-brand" href="#">Indication</a>
</div>
</div>
</div>
<div class="container">
<div class="page-header">
<h1><?php echo $getinforesult["name"]; ?></h1>
</div>		
<?php

//Cookies don't like dots
$idclean = str_replace(".", "_", $id);

//Ignore admin counts if setting has been enabled
session_start();

if (IGNORE_ADMIN_STATE == "Enabled" && isset($_SESSION["indication_user"])) {
    echo "<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Info:</b> Currently logged in, downloads will not be counted.</div>";    
} else {
    if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
        if (!isset($_COOKIE["indicationhasdownloaded_$idclean"])) {
            mysql_query("UPDATE `Data` SET `count` = `count`+1 WHERE `id` = \"$id\"");
            setcookie("indicationhasdownloaded_$idclean", time(), time()+3600*COUNT_UNIQUE_ONLY_TIME);
        }
    } else {
        mysql_query("UPDATE `Data` SET `count` = `count`+1 WHERE `id` = \"$id\"");
    }
}

//Check if download is password protected
$checkifprotected = mysql_query("SELECT `protect`, `password` FROM `Data` WHERE `id` = \"$id\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected);
if ($checkifprotectedresult["protect"] == "1") {
    $case = "passwordprotected";
}

//Check if we should show ads
$checkifadsshow = mysql_query("SELECT `showads` FROM `Data` WHERE `id` = \"$id\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow);
if ($checkifadsshowresult["showads"] == "1") {
    $case = "showads";
}

if ($checkifprotectedresult["protect"] == "1" && $checkifadsshowresult["showads"] == "1") {
    $case = "passwordprotectedandshowads";
}

if ($checkifprotectedresult["protect"] != "1" && $checkifadsshowresult["showads"] != "1") {
    $case = "normal";
}

if (isset($_POST["password"])) {
    if (hash("sha256", SALT . hash("sha256", $_POST["password"])) == $checkifprotectedresult["password"]) {
        $case = "passwordcorrect";
    } else {
        $case = "passwordincorrect";
    }
}

switch ($case) {
    case "showads":
        $adcode = htmlspecialchars_decode(AD_CODE); 
        echo "<p>$adcode</p><a class=\"btn btn-default\" href=\"javascript:history.go(-1)\">Go Back</a><a class=\"btn btn-default pull-right\" href=\"" . $getinforesult["url"] . "\">Get Download</a>";
        break;
    case "passwordprotected":
        echo "<form role=\"form\" method=\"post\"><div class=\"form-group\"><label for=\"password\">Password</label><input type=\"password\" class=\"form-control\" id=\"password\" name=\"password\" placeholder=\"Password...\"><div class=\"help-block\">This download is password protected, please enter the password you were given.</div><a class=\"btn btn-default\" href=\"javascript:history.go(-1)\">Go Back</a><button type=\"submit\" class=\"btn btn-default pull-right\">Get Download</button></form>";
        break;
    case "normal":
        header("Location: " . $getinforesult["url"] . "");
        exit;
        break;
    case "passwordprotectedandshowads":
        $adcode = htmlspecialchars_decode(AD_CODE);
        echo "<p>$adcode</p><form role=\"form\" method=\"post\"><div class=\"form-group\"><label for=\"password\">Password</label><input type=\"password\" class=\"form-control\" id=\"password\" name=\"password\" placeholder=\"Password...\"><div class=\"help-block\">This download is password protected, please enter the password you were given.</div><a class=\"btn btn-default\" href=\"javascript:history.go(-1)\">Go Back</a><button type=\"submit\" class=\"btn btn-default pull-right\">Get Download</button></form>";
        break;
    case "passwordcorrect":
        header("Location: " . $getinforesult["url"] . "");
        exit;
        break;
    case "passwordincorrect":
        echo "<div class=\"alert alert-danger\"><h4 class=\"alert-heading\">Error</h4><p>Incorrect password.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
        break;
} 
    
ob_end_flush();

mysql_close($con);

?>
</div>
<script src="assets/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>