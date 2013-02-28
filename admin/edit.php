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

if (!isset($_GET["id"])) {
    header("Location: ../admin");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication &middot; Edit</title>
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
<li><a href="add.php">Add</a></li>
<li class="active"><a href="#">Edit</a></li>
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
<h1>Edit</h1>
</div>
<?php

//Connect to database
require_once("../config.php");

@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_GET["id"]);

//Check if ID exists
$doesidexist = mysql_query("SELECT id FROM Data WHERE id = \"$idtoedit\"");
$doesidexistresult = mysql_fetch_assoc($doesidexist); 
if ($doesidexistresult == 0) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>ID does not exist.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtoedit\"");
$resultnameofdownload = mysql_fetch_assoc($getnameofdownload);

?>
<form action="actions/edit.php" method="post">
<fieldset>
<?php

$getidinfo = mysql_query("SELECT * FROM Data WHERE id = \"$idtoedit\"");
while($row = mysql_fetch_assoc($getidinfo)) {
    echo "<div class=\"control-group\"><label class=\"control-label\" for=\"downloadname\">Name</label><div class=\"controls\"><input type=\"text\" id=\"downloadname\" name=\"downloadname\" value=\"" . $row["name"] . "\" placeholder=\"Type a name...\" required></div></div>";
    echo "<div class=\"control-group\"><label class=\"control-label\" for=\"id\">ID</label><div class=\"controls\"><input type=\"text\" id=\"id\" name=\"id\" value=\"" . $row["id"] . "\" placeholder=\"Type an ID...\" required></div></div>";
    echo "<div class=\"control-group\"><label class=\"control-label\" for=\"url\">URL</label><div class=\"controls\"><input type=\"text\" id=\"url\" name=\"url\" value=\"" . $row["url"] . "\" placeholder=\"Type a URL...\" pattern=\"(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-?]*)*\/?\" data-validation-pattern-message=\"Please enter a valid URL\" required></div></div>";
    echo "<div class=\"control-group\"><label class=\"control-label\" for=\"count\">Count</label><div class=\"controls\"><input type=\"number\" id=\"count\" name=\"count\" value=\"" . $row["count"] . "\" placeholder=\"Type a count...\" required></div></div>";
}

echo "<div class=\"control-group\"><div class=\"controls\"><label class=\"checkbox\">";
    
//Check if we should show ads
$checkifadsshow = mysql_query("SELECT showads FROM Data WHERE id = \"$idtoedit\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow); 
if ($checkifadsshowresult["showads"] == "1") { 
    echo "<input type=\"checkbox\" id=\"showadsstate\" name=\"showadsstate\" checked=\"checked\"> Show ads?";
} else {
    echo "<input type=\"checkbox\" id=\"showadsstate\"  name=\"showadsstate\"> Show ads?";
}

echo "</label></div></div><div class=\"control-group\"><div class=\"controls\"><label class=\"checkbox\">";
    
//Check if download is protected
$checkifprotected = mysql_query("SELECT protect FROM Data WHERE id = \"$idtoedit\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected); 
if ($checkifprotectedresult["protect"] == "1") { 
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\" checked=\"checked\"> Enable password protection?";
} else {
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\"> Enable password protection?";
}

mysql_close($con);

?>
</label>
</div>
</div>
<div class="form-actions">
<input type="hidden" name="idtoedit" value="<? echo $idtoedit; ?>" />
<input type="hidden" id="password" name="password">
<button type="submit" class="btn btn-primary">Update</button>
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
