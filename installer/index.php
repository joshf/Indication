<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

//Security check, check if config exists
if (file_exists("../config.php")) {
    header("Location: ../admin");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Installer</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
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
<a class="brand" href="#">Indication</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Installer</h1>
</div>
<?php

//Get path to script
$currenturl = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$pathtoscriptwithslash = "http://" . substr($currenturl, 0, strpos($currenturl, "installer"));
$pathtoscript = rtrim($pathtoscriptwithslash, "/");

?>	
<form action="install.php" method="post">
<fieldset>
<h4>Database Settings</h4>
<div class="control-group">
<label class="control-label" for="dbhost">Database Host</label>
<div class="controls">
<input type="text" id="dbhost" name="dbhost" value="localhost" placeholder="Type a database host..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="dbuser">Database User</label>
<div class="controls">
<input type="text" id="dbuser" name="dbuser" placeholder="Type a database user..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="dbpassword">Database Password</label>
<div class="controls">
<input type="password" id="dbpassword" name="dbpassword" placeholder="Type a database password..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="dbname">Database Name</label>
<div class="controls">
<input type="text" id="dbname" name="dbname" placeholder="Type a database name..." required>
</div>
</div>
<h4>Admin Details</h4>
<div class="control-group">
<label class="control-label" for="adminuser">Admin User</label>
<div class="controls">
<input type="text" id="adminuser" name="adminuser" placeholder="Type a username..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="adminpassword">Password</label>
<div class="controls">
<input type="password" id="adminpassword" name="adminpassword" placeholder="Type a password..." required>
</div>
</div>
<div class="control-group">
<label class="control-label" for="adminpasswordconfirm">Confirm Password</label>
<div class="controls">
<input type="password" id="adminpasswordconfirm" name="adminpasswordconfirm" placeholder="Type your password again..." data-validation-match-match="adminpassword" required>
<span class="help-block">It is recommended that your password be at least 6 characters long</span>
</div>
</div>
<h4>Other Settings</h4>
<div class="control-group">
<label class="control-label" for="website">Website Name</label>
<div class="controls">
<input type="text" id="website" name="website" required placeholder="Type your websites name...">
</div>
</div>
<div class="control-group">
<label class="control-label" for="pathtoscript">Path to Script</label>
<div class="controls">
<input type="text" id="pathtoscript" name="pathtoscript" value="<? echo $pathtoscript; ?>" placeholder="Type the path to Indication..." pattern="(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-?]*)*\/?" data-validation-pattern-message="Please enter a valid URL" required>
</div>
</div>
<div class="form-actions">
<input type="hidden" name="doinstall">
<input type="submit" class="btn btn-primary" value="Install">
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
    $("input").not("[type=submit]").jqBootstrapValidation();
});
</script>
<!-- Javascript end -->
</body>
</html>