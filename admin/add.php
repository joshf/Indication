<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
	die("Error: Config file not found! Please reinstall Indication.");
}

require_once("../config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit; 
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Add</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if (THEME == "default") {
    echo "<link href=\"../resources/bootstrap/css/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.2/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}
?>
<link href="../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 60px;
}
@media (max-width: 980px) {
    body {
        padding-top: 0;
    }
}
</style>
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
<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="index.php">Indication</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li class="divider-vertical"></li>
<li class="active"><a href="add.php"><i class="icon-plus-sign"></i> Add</a></li>
<li><a href="edit.php"><i class="icon-edit"></i> Edit</a></li>
</ul>
<ul class="nav pull-right">
<li class="divider-vertical"></li>
<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i> <?php echo ADMIN_USER; ?> <b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="settings.php"><i class="icon-cog"></i> Settings</a></li>
<li><a href="logout.php"><i class="icon-off"></i> Logout</a></li>
</ul>
</li>
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
<?php

//Error display
if (isset($_GET["error"])) {
    $error = $_GET["error"];
    if ($error == "emptyfields") {
        echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Error</h4><p>One or more fields were left empty.</p></div>";
    } elseif ($error == "idexists") {
        echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Error</h4><p>ID already exists.</p></div>";
    } elseif ($error == "emptypassword") {
        echo "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Error</h4><p>Empty password.</p></div>";
    }
}

?>
<form action="actions/add.php" method="post" autocomplete="off">
<fieldset>
<div class="control-group">
<label class="control-label" for="name">Name</label>
<div class="controls">
<input type="text" id="name" name="name" placeholder="Type a name..." pattern="([0-9A-Za-z-\\.@:%_\+~#=\s]+)" required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="id">ID</label>
<div class="controls">
<input type="text" id="id" name="id" placeholder="Type an ID..." pattern="([0-9A-Za-z-\\.@:%_\+~#=]+)" required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="url">URL</label>
<div class="controls">
<input type="text" id="url" name="url" placeholder="Type a URL..." pattern="(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.?~-]*)*\/?" required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="count">Count</label>
<div class="controls">
<input type="number" id="count" name="count" placeholder="Type an initial count..." min="0">
</div>
</div>
<div class="control-group">
<div class="controls">
<label class="checkbox">
<input type="checkbox" id="showadsstate" name="showadsstate"> Show ads
</label>
</div>
</div>
<div class="control-group">
<div class="controls">
<label class="checkbox">
<input type="checkbox" id="passwordprotectstate" name="passwordprotectstate"> Enable password protection
</label>
</div>
</div>
<div id="passwordentry" style="display: none;">
<div class="control-group">
<label class="control-label" for="password">Password</label>
<div class="controls">
<input type="password" id="password" name="password" placeholder="Type a password...">
<span class="help-block">It is recommended that your password be at least 6 characters long</span>
</div>
</div>
</div>
<div class="form-actions">
<button type="submit" class="btn btn-primary">Add</button>
</div>
</fieldset>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="../resources/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.min.js"></script>
<script src="../resources/validation/jqBootstrapValidation.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#passwordprotectstate").click(function() {
        if ($("#passwordprotectstate").prop("checked") == true) {
            $("#password").prop("required", true);
            $("#passwordentry").show("fast");
        } else {
            $("#passwordentry").hide("fast");
            $("#password").prop("required", false);
        }
    });
    $("input").not("[type=submit]").jqBootstrapValidation();
});
</script>
<!-- Javascript end -->
</body>
</html>