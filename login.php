<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    die("Error: Config file not found!");
}

require_once("config.php");

session_start();

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

if (isset($_COOKIE["indication_user_rememberme"])) {
    $hash = $_COOKIE["indication_user_rememberme"];
    $getuser = mysqli_query($con, "SELECT `id`, `hash` FROM `users` WHERE `hash` = \"$hash\"");
    if (mysqli_num_rows($getuser) == 0) {
        header("Location: logout.php");
        exit;
    }
    $userinforesult = mysqli_fetch_assoc($getuser);
    $_SESSION["indication_user"] = $userinforesult["id"];
}

if (isset($_POST["password"]) && isset($_POST["username"])) {
    $username = mysqli_real_escape_string($con, $_POST["username"]);
    $password = mysqli_real_escape_string($con, $_POST["password"]);
    $userinfo = mysqli_query($con, "SELECT `id`, `user`, `password`, `salt` FROM `Users` WHERE `user` = \"$username\"");
    $userinforesult = mysqli_fetch_assoc($userinfo);
    if (mysqli_num_rows($userinfo) == 0) {
        header("Location: login.php?login_error=true");
        exit;
    }
    $salt = $userinforesult["salt"];
    $hashedpassword = hash("sha256", $salt . hash("sha256", $password));
    if ($hashedpassword == $userinforesult["password"]) {
        $_SESSION["indication_user"] = $userinforesult["id"];
        if (isset($_POST["rememberme"])) {
            $hash = substr(str_shuffle(MD5(microtime())), 0, 50);
            mysqli_query($con, "UPDATE `Users` SET `hash` = \"$hash\" WHERE `id` = \"" . $userinforesult["id"] . "\"");
            setcookie("indication_user_rememberme", $hash, time()+3600*24*7);
        }
    } else {
        header("Location: login.php?login_error=true");
        exit;
    }
}

if (!isset($_SESSION["indication_user"])) {
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="assets/favicon.ico">
<title>Indication &raquo; Login</title>
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
<div class="container form-fix">
<div class="row">
<div class="col-sm-6 col-md-4 col-md-offset-4">
<div class="panel panel-default">
<div class="panel-heading">
<strong>Indication &raquo; Login</strong>
</div>
<div class="panel-body">
<form method="post">
<fieldset>
<div class="row">
<div class="center-block">
<img class="profile-img" src="assets/icon.png" alt="Indication">
</div>
</div>
<div class="row">
<div class="col-sm-12 col-md-10 col-md-offset-1">
<?php 
if (isset($_GET["login_error"])) {
    echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Incorrect login.</div>";
} elseif (isset($_GET["logged_out"])) {
    echo "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Successfully logged out.</div>";
}
?>
<div class="form-group">
<div class="input-group">
<span class="input-group-addon">
<i class="glyphicon glyphicon-user"></i>
</span> 
<input type="text" class="form-control" name="username" id="username" placeholder="Username" autofocus>
</div>
</div>
<div class="form-group">
<div class="input-group">
<span class="input-group-addon">
<i class="glyphicon glyphicon-lock"></i>
</span>
<input type="password" class="form-control" name="password" id="password" placeholder="Password">
</div>
</div>
<div class="form-group">
<input type="submit" class="btn btn-primary btn-block" value="Sign in">
</div>
</div>
</div>
</fieldset>
</form>
</div>
<div class="panel-footer">
Forgot your password? <a href="login.php?resetpassword">Click Here</a>
</div>
</div>
</div>
</div>
</div>
<script src="assets/bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit;
}
?>