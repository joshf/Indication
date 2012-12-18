<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$user = ADMIN_USER;
$password = ADMIN_PASSWORD;
$uniquekey = UNIQUE_KEY;

session_start();

//If cookie is set, skip login
if (isset($_COOKIE["shtrackerrememberme_" . $uniquekey . ""])) {
    $_SESSION["is_logged_in_" . $uniquekey . ""] = true;
}

if (isset($_POST["password"]) && isset($_POST["user"])) {
    if (sha1($_POST["password"]) == $password && $_POST["user"] == $user) {
        $_SESSION["is_logged_in_" . $uniquekey . ""] = true;
            if (isset($_POST["rememberme"])) {
                setcookie("shtrackerrememberme_" . $uniquekey . "", ADMIN_USER, time()+1209600);
            }
    } else {
        header("Location: login.php?login_error=true");
    }
} 

if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
?>
<html>
<head>
<title>SHTracker: Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
<style type="text/css">
  body {
    padding-top: 40px;
    padding-bottom: 40px;
    background-color: #f5f5f5;
  }

  .form-signin {
    max-width: 300px;
    padding: 19px 29px 29px;
    margin: 0 auto 20px;
    background-color: #fff;
    border: 1px solid #e5e5e5;
    -webkit-border-radius: 5px;
       -moz-border-radius: 5px;
            border-radius: 5px;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
       -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
            box-shadow: 0 1px 2px rgba(0,0,0,.05);
  }
  .form-signin .form-signin-heading,
  .form-signin .checkbox {
    margin-bottom: 10px;
  }
  .form-signin input[type="text"],
  .form-signin input[type="password"] {
    font-size: 16px;
    height: auto;
    margin-bottom: 15px;
    padding: 7px 9px;
  }
</style>
<link href="../bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
</head>
<body>
<div class="container">
<form class="form-signin" method="post">
<h2 class="form-signin-heading">SHTracker</h2>
<?php 

if (isset($_GET["login_error"])) {
    echo "<div class=\"alert alert-error\"><a class=\"close\" data-dismiss=\"alert\">×</a>Incorrect username or password.</div>";
} elseif (isset($_GET["logged_out"])) {
    echo "<div class=\"alert alert-success\"><a class=\"close\" data-dismiss=\"alert\">×</a>Successfully logged out.</div>";
}

?>
<input type="text" class="input-block-level" name="user" placeholder="Username">
<input type="password" class="input-block-level" name="password" placeholder="Password">
<label class="checkbox">
<input type="checkbox" value="remember-me"> Remember me
</label>
<button class="btn btn-large btn-primary" type="submit">Sign in</button>
</form>
</div>
</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit;
}
?>