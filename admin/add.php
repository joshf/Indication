<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

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
<title>Indication &middot; Add</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php

if (THEME == "default") {
    echo "<link href=\"../resources/bootstrap/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.0/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}

?>
<style type="text/css">
body {
    padding-top: 60px;
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
<a class="brand" href="#">Indication</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li><a href="index.php">Home</a></li>
<li class="divider-vertical"></li>
<li class="active"><a href="add.php">Add</a></li>
<li><a href="#">Edit</a></li>
</ul>
<ul class="nav pull-right">
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
<div class="control-group">
<label class="control-label" for="downloadname">Name</label>
<div class="controls">
<input type="text" id="downloadname" name="downloadname" placeholder="Type a name..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="id">ID</label>
<div class="controls">
<input type="text" id="id" name="id" placeholder="Type an ID..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="url">URL</label>
<div class="controls">
<input type="text" id="url" name="url" placeholder="Type a URL..." pattern="(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-?]*)*\/?" data-validation-pattern-message="Please enter a valid URL" required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="count">Count</label>
<div class="controls">
<input type="number" id="count" name="count" placeholder="Type an initial count...">
</div>
</div>
<div class="control-group">
<div class="controls">
<label class="checkbox">
<input type="checkbox" id="showadsstate" name="showadsstate"> Show ads?
</label>
</div>
</div>
<div class="control-group">
<div class="controls">
<label class="checkbox">
<input type="checkbox" id="passwordprotectstate" name="passwordprotectstate"> Enable password protection?
</label>
</div>
</div>
<div class="form-actions">
<input type="hidden" id="password" name="password">
<button type="submit" class="btn btn-primary">Add</button>
</div>
</fieldset>
</form>
</div>
<!-- Content end -->
<!-- Javascript start -->
<script src="../resources/jquery.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<script src="../resources/validation/jqBootstrapValidation.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#passwordprotectstate").click(function() {
        if ($("#passwordprotectstate").prop("checked") == true) {
            password = prompt("Enter a password","");
            passwordconfirm = prompt("Confirm password","");
            if (password != passwordconfirm) {
                alert("Passwords do not match");
                return false   
            }
            if (password != "" && password != null) {
                $("#password").val(password);
            } else {
                $("#passwordprotectstate").prop("checked", false);
            }
        }
    });
    $("input").not("[type=submit]").jqBootstrapValidation();
});
</script>
<!-- Javascript end -->
</body>
</html>