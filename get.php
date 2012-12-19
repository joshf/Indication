<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

ob_start();

require_once("config.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<style>
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
<a class="brand" href="#">SHTracker</a>
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
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Could not connect to database (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Database does not exist (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

mysql_select_db(DB_NAME, $con);

//Get the ID from $_GET OR $_POST
if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} elseif (isset($_POST["id"])) {
    $id = mysql_real_escape_string($_POST["id"]);
} else {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>ID cannot be blank.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Check if ID exists
$getinfo = mysql_query("SELECT name, url FROM Data WHERE id = \"$id\"");
$getinforesult = mysql_fetch_assoc($getinfo);
if ($getinforesult == 0) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>ID does not exist.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

//Cookies don't like dots
$idclean = str_replace(".", "_", $id);

if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
    if (!isset($_COOKIE["shtrackerhasdownloaded_$idclean"])) {
        mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
        setcookie("shtrackerhasdownloaded_$idclean", "True", time()+3600*COUNT_UNIQUE_ONLY_TIME);
    }
} else {
    mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
}

//Check if download is password protected
$checkifprotected = mysql_query("SELECT protect, password FROM Data WHERE id = \"$id\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected);
if ($checkifprotectedresult["protect"] == "1") {
    if (isset($_POST["password"])) {
        if (sha1($_POST["password"]) != $checkifprotectedresult["password"]) {
            die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Incorrect password.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
        } else {
            setcookie("shtrackerhasauthed_$idclean", time()+900, time()+900);
        }
    } elseif (isset($_COOKIE["shtrackerhasauthed_$idclean"])) {
        $time = ($_COOKIE["shtrackerhasauthed_$idclean"]-time()) / 60;
        $timeleft = ceil($time);
        echo "<div class=\"alert alert-info\"><b>Notice:</b> your download session wll expire in $timeleft minutes...</div>";
    } else {
        die("<h3>Downloading " . $getinforesult["name"] . "</h3>
        <form method=\"post\">
        <p>To access this download please enter the password you were given.</p>
        <p>Password: <input type=\"password\" name=\"password\"></p>
        <input type=\"submit\" class=\"btn btn-success\" value=\"Get Download\"></form>
        <p><a href=\"javascript:history.go(-1)\" class=\"btn\">Go Back</a></p>
        </div>
        </body>
        </html>");
    }
}

//Check if we should show ads
$checkifadsshow = mysql_query("SELECT showads FROM Data WHERE id = \"$id\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow);
if ($checkifadsshowresult["showads"] == "1") {
    $adcode = htmlspecialchars_decode(AD_CODE);
    die("<h4>Downloading " . $getinforesult["name"] . "</h4><p>" . $adcode . "</p><p><button class=\"btn btn-success\" onClick=\"window.location = '" . $getinforesult["url"] . "'\">Start Download</button></p><p><a href=\"javascript:history.go(-1)\" class=\"btn\">Go Back</a></p></div></body></html>");
}

mysql_close($con);

//Redirect user to the download
header("Location: " . $getinforesult["url"] . "");
ob_end_flush();
exit;

?>
</div>
<!-- Content end -->
</body>
</html>