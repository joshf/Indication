<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

ob_start();

if (!file_exists("config.php")) {
    header("Location: installer");
    exit;
}

require_once("config.php");

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

//Get the ID from $_GET OR $_POST
if (isset($_GET["id"])) {
    $linkid = mysqli_real_escape_string($con, $_GET["id"]);
} elseif (isset($_POST["id"])) {
    $linkid = mysqli_real_escape_string($con, $_POST["id"]);
} else {
    die("Error: ID cannot be blank.");
}

//Check if ID exists
$getinfo = mysqli_query($con, "SELECT `name`, `url` FROM `Data` WHERE `linkid` = \"$linkid\"");
$getinforesult = mysqli_fetch_assoc($getinfo);
if (mysqli_num_rows($getinfo) == 0) {
    die("Error: ID does not exist.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<base href="<?php echo PATH_TO_SCRIPT; ?>/">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Indication</title>
<meta name="robots" content="noindex, nofollow">
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 15px;
    padding-bottom: 15px;
}
</style>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="container">		
<?php

//Cookies don't like dots
$linkidclean = str_replace(".", "_", $linkid);

//Ignore admin counts if setting has been enabled
session_start();

if (IGNORE_ADMIN_STATE == "Enabled" && isset($_SESSION["indication_user"])) {
    echo "<div class=\"alert alert-info\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Info:</b> You are currently logged in, links will not be counted.</div>";    
} else {
    if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
        if (!isset($_COOKIE["indicationhaslinked_$linkidclean"])) {
            mysqli_query($con, "UPDATE `Data` SET `count` = `count`+1 WHERE `linkid` = \"$linkid\"");
            setcookie("indicationhaslinked_$linkidclean", time(), time()+3600*COUNT_UNIQUE_ONLY_TIME);
        }
    } else {
        mysqli_query($con, "UPDATE `Data` SET `count` = `count`+1 WHERE `linkid` = \"$linkid\"");
    }
}

//Check if link is password protected
$checkifprotected = mysqli_query($con, "SELECT `protect`, `password` FROM `Data` WHERE `linkid` = \"$linkid\"");
$checkifprotectedresult = mysqli_fetch_assoc($checkifprotected);
if ($checkifprotectedresult["protect"] == "1") {
    $case = "passwordprotected";
}

//Check if we should show ads
$checkifadsshow = mysqli_query($con, "SELECT `showads` FROM `Data` WHERE `linkid` = \"$linkid\"");
$checkifadsshowresult = mysqli_fetch_assoc($checkifadsshow);
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
        echo "<p>$adcode</p><div class=\"btn-group pull-right\"><a class=\"btn btn-default\" href=\"javascript:history.go(-1)\">Go Back</a><a class=\"btn btn-primary\" href=\"" . $getinforesult["url"] . "\">Get Link</a></div>";
        break;
    case "passwordprotected":
        if (isset($_GET["error"])) {
            echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Error:</b> Incorrect password.</div>";
        }
        echo "<form role=\"form\" method=\"post\"><div class=\"form-group\"><label for=\"password\">Password</label><input type=\"password\" class=\"form-control\" id=\"password\" name=\"password\" placeholder=\"Password...\"><div class=\"help-block\">This link is password protected, please enter the password you were given.</div></div><div class=\"btn-group pull-right\"><a class=\"btn btn-default\" href=\"javascript:history.go(-1)\">Go Back</a><button type=\"submit\" class=\"btn btn-primary\">Get Link</button></div></form>";
        break;
    case "normal":
        header("Location: " . $getinforesult["url"] . "");
        exit;
        break;
    case "passwordprotectedandshowads":
        if (isset($_GET["error"])) {
            echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><b>Error:</b> Incorrect password.</div>";
        }
        $adcode = htmlspecialchars_decode(AD_CODE);
        echo "<p>$adcode</p><form role=\"form\" method=\"post\"><div class=\"form-group\"><label for=\"password\">Password</label><input type=\"password\" class=\"form-control\" id=\"password\" name=\"password\" placeholder=\"Password...\"><div class=\"help-block\">This link is password protected, please enter the password you were given.</div></div><div class=\"btn-group pull-right\"><a class=\"btn btn-default\" href=\"javascript:history.go(-1)\">Go Back</a><button type=\"submit\" class=\"btn btn-primary\">Get Link</button></div></form>";
        break;
    case "passwordcorrect":
        header("Location: " . $getinforesult["url"] . "");
        exit;
        break;
    case "passwordincorrect":
        header("Location: get.php?id=$linkid&error=true");
        exit;
        break;
} 
    
ob_end_flush();

mysqli_close($con);

?>
<br>
<h6>Powered by <a href="https://github.com/joshf/Indication" target="_blank">Indication</a>
<small><?php echo VERSION ?></small></h6>
</div>
<script src="assets/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>