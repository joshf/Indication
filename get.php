<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    die("Error: Config file not found!");
}

require_once("config.php");

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

//Get the ID from $_GET OR $_POST
if (isset($_GET["id"])) {
    $abbreviation = mysqli_real_escape_string($con, $_GET["id"]);
} elseif (isset($_POST["id"])) {
    $abbreviation = mysqli_real_escape_string($con, $_POST["id"]);
} else {
    die("Error: ID cannot be blank.");
}

//Check if ID exists
$getinfo = mysqli_query($con, "SELECT `id`, `name`, `url`, `protect`, `password` FROM `links` WHERE `abbreviation` = \"$abbreviation\"");
$getinforesult = mysqli_fetch_assoc($getinfo);
if (mysqli_num_rows($getinfo) == 0) {
    die("Error: ID does not exist.");
}

//Cookies don't like dots
$abbreviationclean = str_replace(".", "_", $abbreviation);

session_start();

if (!isset($_SESSION["indication_user"])) {
    $id = $getinforesult["id"];
    
    //Get IP
    $ip = $_SERVER["REMOTE_ADDR"];

    //Get referrer
    $referrer = $_SERVER["HTTP_REFERER"];

    if (empty($referrer)) {
        $referrer = "";
    }
    
    //Check against blacklist
    $checkblacklist = mysqli_query($con, "SELECT `id`, `ip` FROM `blacklist` WHERE `ip` = \"$ip\"");
    $checkblacklistresult = mysqli_fetch_assoc($checkblacklist);
    if (mysqli_num_rows($checkblacklist) == 1) {
        die("Error: The IP address " . $checkblacklistresult["ip"] . " has been blocked by the site administrator.");
    }
    
    if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
        if (!isset($_COOKIE["ihl_$abbreviationclean"])) {
            mysqli_query($con, "UPDATE `links` SET `count` = `count`+1 WHERE `abbreviation` = \"$abbreviation\"");
            setcookie("ihl_$abbreviationclean", "true", time()+3600);
            mysqli_query($con, "INSERT INTO `counts` (link_id, date, ip, referrer)
            VALUES (\"$id\",CURDATE(),\"$ip\",\"$referrer\")");
        }
    } else {
        mysqli_query($con, "UPDATE `links` SET `count` = `count`+1 WHERE `abbreviation` = \"$abbreviation\"");
        mysqli_query($con, "INSERT INTO `counts` (link_id, date, ip, referrer)
        VALUES (\"$id\",CURDATE(),\"$ip\",\"$referrer\")");
    }

}

if ($getinforesult["protect"] == "0") {
    header("Location: " . $getinforesult["url"] . "");
    mysqli_close($con);
    exit;
}

if (isset($_POST["password"])) {
    $password = $_POST["password"];
    $hashedpassword = hash("sha256", SALT . hash("sha256", $password));
    if ($hashedpassword == $getinforesult["password"]) {
        header("Location: " . $getinforesult["url"] . "");
        mysqli_close($con);
        exit;
    } else {
    	$wrong_pass = true;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<base href="<?php echo PATH_TO_SCRIPT; ?>/">
<link rel="icon" href="assets/favicon.ico">
<title>Indication &raquo; Password Required</title>
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
<div class="container">
<form method="post" class="form-signin">
<img class="logo-img" src="assets/icon.png" alt="Indication">
<?php 
if ($wrong_pass) {
    echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Incorrect password.</div>";
}
?>
<label for="password" class="sr-only">Password</label>
<input type="password" id="password" name="password" class="form-control" placeholder="Password..." required autofocus>
<button class="btn btn-primary btn-block" type="submit">Follow Link</button>
</form>
</div>
<script src="assets/bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>
<?php

mysqli_close($con);

?>