<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

ob_start();

if (!file_exists("config.php")) {
    die("Error: Config file not found! Please contact the site administrator.");
}

require_once("config.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if (THEME == "default") {
    echo "<link href=\"resources/bootstrap/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.1/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}
?>
<style type="text/css">
body {
    padding-top: 60px;
}
</style>
<link href="resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
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
<a class="brand" href="#">Indication</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1><? echo WEBSITE; ?></h1>
</div>		
<?php

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Could not connect to database (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Check database exists
$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Database does not exist (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Get the ID from $_GET OR $_POST
if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} elseif (isset($_POST["id"])) {
    $id = mysql_real_escape_string($_POST["id"]);
} else {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>ID cannot be blank.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Check if ID exists
$getinfo = mysql_query("SELECT name, url, count FROM Data WHERE id = \"$id\"");
$getinforesult = mysql_fetch_assoc($getinfo);
if ($getinforesult == 0) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>ID does not exist.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Cookies don't like dots
$idclean = str_replace(".", "_", $id);

if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
    if (!isset($_COOKIE["indicationhasdownloaded_$idclean"])) {
        mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
        setcookie("indicationhasdownloaded_$idclean", "True", time()+3600*COUNT_UNIQUE_ONLY_TIME);
    }
} else {
    mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
}

//Check if download is password protected
$checkifprotected = mysql_query("SELECT protect, password FROM Data WHERE id = \"$id\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected);

//Check if we should show ads
$checkifadsshow = mysql_query("SELECT showads FROM Data WHERE id = \"$id\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow);

if ($checkifprotectedresult["protect"] == "1") {
    $case = "passwordprotected";
}

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
    if (sha1($_POST["password"]) == $checkifprotectedresult["password"]) {
        $case = "passwordcorrect";
    } else {
        $case = "passwordincorrect";
    }
}

switch ($case) {
    case "showads":
        $adcode = htmlspecialchars_decode(AD_CODE); 
        echo "<h3>" . $getinforesult["name"] . "</h3><div class=\"well\">$adcode</div><p><i class=\"icon-download\"></i> " . $getinforesult["count"] . " downloads</p><fieldset><div class=\"form-actions\"><a class=\"btn btn-primary\" href=\"" . $getinforesult["url"] . "\">Get Download</a><a class=\"btn pull-right\" href=\"javascript:history.go(-1)\">Go Back</a></div></fieldset>";
        break;
    case "passwordprotected":
        echo "<h3>" . $getinforesult["name"] . "</h3><p>This download is password protected, please enter the password you were given</p><form method=\"post\"><fieldset><div class=\"control-group\"><label class=\"control-label\" for=\"password\">Password</label><div class=\"controls\"><input type=\"password\" id=\"password\" name=\"password\" placeholder=\"Password...\"></div></div><div class=\"form-actions\"><button type=\"submit\" class=\"btn btn-primary\">Get Download</button><a class=\"btn pull-right\" href=\"javascript:history.go(-1)\">Go Back</a></div></fieldset></form>";
        break;
    case "normal":
        header("Location: " . $getinforesult["url"] . "");
        break;
    case "passwordprotectedandshowads":
        $adcode = htmlspecialchars_decode(AD_CODE); 
        echo "<h3>" . $getinforesult["name"] . "</h3><div class=\"well\">$adcode</div><p>This download is password protected, please enter the password you were given.</p><form method=\"post\"><fieldset><div class=\"control-group\"><label class=\"control-label\" for=\"password\">Password</label><div class=\"controls\"><input type=\"password\" id=\"password\" name=\"password\" placeholder=\"Password...\"></div></div><div class=\"form-actions\"><button type=\"submit\" class=\"btn btn-primary\">Get Download</button><a class=\"btn pull-right\" href=\"javascript:history.go(-1)\">Go Back</a></div></fieldset></form>";
        break;
    case "passwordcorrect":
        header("Location: " . $getinforesult["url"] . "");
        break;
    case "passwordincorrect":
        echo "<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Incorrect password.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
        break;
} 
    
ob_end_flush();

mysql_close($con);

?>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="resources/jquery.js"></script>
<script src="resources/bootstrap/js/bootstrap.js"></script>
<!-- Javascript end -->
</body>
</html>