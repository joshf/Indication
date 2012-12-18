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
<form action="actions/add.php" method="post" id="addform">
<p>FIXME: USE PROPER SIZING AKA BOOTSTRAP</p>
<p><input type="text" size="50" name="downloadname" placeholder="Name" /></p>
<p><input type="text" size="50" name="id" placeholder="ID" /></p>
<p><input type="text" size="50" name="url" placeholder="URL" /></p>
<p><input type="text" size="50" name="count" placeholder="Count" value="0" /></p>
<p>Enable password protection? <input type="checkbox" id="passwordprotectstate" name="passwordprotectstate" /></p>
<div id="passwordentry" style="display: none">
<p><i>Please enter a password:</i> <input type="password" name="password" /></p>
</div>
<p>Show Ads? <input type="checkbox" name="showadsstate" /></p>
<button type="submit" class="btn btn-primary">Add</button>			
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<script src="../resources/bootstrap/js/bootstrap-collapse.js"></script>
<script src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
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
<!-- Javascript end -->
</body>
</html>