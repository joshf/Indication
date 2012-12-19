<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker: Add</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<style>
    body {
        padding-top: 60px;
    }
    label.error {
        color: #ff0000;
    }
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" type="text/css" rel="stylesheet">
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
<a class="btw btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">SHTracker</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li><a href="index.php">Home</a></li>
<li class="divider-vertical"></li>
<li class="active"><a href="add.php">Add</a></li>
<li><a href="#">Edit</a></li>
<li class="divider-vertical"></li>
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Add</h1>
</div>
<form action="actions/add.php" method="post">
<fieldset>
<legend>Add a download or link</legend>
<label for="downloadname">Name</label>
<input type="text" id="downloadname" name="downloadname" placeholder="Type a name...">
<label for="id">ID</label>
<input type="text" id="id" name="id" placeholder="Type an ID...">
<label for="url">URL</label>
<input type="text" id="url" name="url" placeholder="Type a URL...">
<label for="count">Count</label>
<input type="text" id="count" name="count" placeholder="Type an initial count...">
<label class="checkbox">
<input type="checkbox" id="showadsstate" name="showadsstate"> Show ads?
</label>
<label class="checkbox">
<input type="checkbox" id="passwordprotectstate" name="passwordprotectstate"> Enable password protection?
</label>
<input type="hidden" id="password" name="password">
<button type="submit" class="btn btn-primary">Submit</button>
</fieldset>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<script src="../resources/bootstrap/js/bootstrap-collapse.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#passwordprotectstate").click(function() {
        password = prompt("Enter a password","");
        if (password != "" && password != null) {
            $("#password").val(password);
            $("#passwordprotectstate").prop("checked", true);
        } else {
            $("#passwordprotectstate").prop("checked", false);
        }
    });
});
</script>
<!-- Javascript end -->
</body>
</html>