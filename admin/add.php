<!DOCTYPE html>
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
<html> 
<head>
<title>SHTracker: Add A Download/Link</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
<link href="../resources/bootstrap/css/bootstrap.css" rel="stylesheet">
<style>
    html, body {
        padding-top: 30px;
        height: 100%;
    }
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
$(document).ready(function() {
    $("#passwordprotectstate").click(function() {
        $("#passwordentry").toggle(this.checked);
    });
    $.validator.addMethod(
        "legalname",
        function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9()._\-\s]+$/.test(value);
        },
        "Illegal character. Only points, spaces, underscores or dashes are allowed."
    );
    $.validator.addMethod(
        "legalid",
        function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9._-]+$/.test(value);
        },
        "Illegal character. Only points, underscores or dashes are allowed."
    ); 
    $.validator.addMethod(
        "legalurl",
        function(value, element) { 
            return this.optional(element) || /^[a-zA-Z0-9.?=:#_\-/\-]+$/.test(value);
        },
        "Illegal character. Please use a valid URL or directory path."
    ); 
    $("#addform").validate({
        rules: {
            downloadname: {
                required: true,
                legalname: true
            },
            id: {
                required: true,
                legalid: true
            },
            url: {
                required: true,
                legalurl: true
            },
            count: {
                digits: true
            },
            password: {
                required: true,
                minlength: 6
            },
        }
    });
});
</script>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
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
<li><a href="index.php">Downloads</a></li>
<li class="active"><a href="add.php">Add</a></li>
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</div>
</div>
<div class="container">
<div class="page-header">
<h1>Add</h1>
</div>
<form action="actions/add.php" method="post" id="addform">
<p><b>Name:</b> <input type="text" size="50" name="downloadname" /></p>
<p><b>ID:</b> <input type="text" size="50" name="id" /></p>
<p><b>URL:</b> <input type="text" size="50" name="url" /></p>
<p><b>Count:</b> <input type="text" size="50" name="count" value="0" /></p>
<p>Enable password protection? <input type="checkbox" id="passwordprotectstate" name="passwordprotectstate" /></p>
<div id="passwordentry" style="display: none">
<p><i>Please enter a password:</i> <input type="password" name="password" /></p>
</div>
<p>Show Ads? <input type="checkbox" name="showadsstate" /></p>
<input class="btn" type="submit" value="Add" />
</form>
</div>
</body>
</html>