<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
    header("Location: ../installer");
    exit;
}

require_once("../config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit; 
}

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

mysql_select_db(DB_NAME, $con);

$getusersettings = mysql_query("SELECT `user` FROM `Users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysql_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysql_fetch_assoc($getusersettings);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Indication &middot; Edit</title>
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 30px;
    padding-bottom: 30px;
}
</style>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="#">Indication</a>
</div>
<div class="navbar-collapse collapse">
<ul class="nav navbar-nav">
<li><a href="index.php">Home</a></li>
<li><a href="add.php">Add</a></li>
<li class="active"><a href="edit.php">Edit</a></li>
</ul>
<ul class="nav navbar-nav navbar-right">
<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $resultgetusersettings["user"]; ?> <b class="caret"></b></a>
<ul class="dropdown-menu">
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</li>
</ul>
</div>
</div>
</div>
<div class="container">
<div class="page-header">
<h1>Edit</h1>
</div>
<?php

//Quick edit selector
if (!isset($_GET["id"])) {
	$getids = mysql_query("SELECT `id`, `name` FROM `Data`");
    if (mysql_num_rows($getids) != 0) {
        echo "<form role=\"form\" method=\"get\"><div class=\"form-group\"><label for=\"id\">Select a download to edit</label><select class=\"form-control\" id=\"id\" name=\"id\">";
        while($row = mysql_fetch_assoc($getids)) {
            echo "<option value=\"" . $row["id"] . "\">" . ucfirst($row["name"]) . "</option>";
        }
        echo "</select></div><button type=\"submit\" class=\"btn btn-default\">Select</button></form>";
    } else {
        echo "<div class=\"alert alert-info\"><h4 class=\"alert-heading\">Information</h4><p>No downloads available to edit.</p><p><a class=\"btn btn-info\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
    }
} else {

?>
<?php

$idtoedit = mysql_real_escape_string($_GET["id"]);

//Check if ID exists
$doesidexist = mysql_query("SELECT `id` FROM `Data` WHERE `id` = \"$idtoedit\"");
if (mysql_num_rows($doesidexist) == 0) {
    echo "<div class=\"alert alert-danger\"><h4 class=\"alert-heading\">Error</h4><p>ID does not exist.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
} else {

//Error display
if (isset($_GET["error"])) {
    $error = $_GET["error"];
    if ($error == "emptyfields") {
        echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><h4 class=\"alert-heading\">Error</h4><p>One or more fields were left empty.</p></div>";
    } elseif ($error == "emptypassword") {
        echo "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><h4 class=\"alert-heading\">Error</h4><p>Empty password.</p></div>";
    }
}
?>
<form role="form" action="actions/edit.php" method="post" autocomplete="off">
<?php

$getidinfo = mysql_query("SELECT * FROM `Data` WHERE `id` = \"$idtoedit\"");
$getidinforesult = mysql_fetch_assoc($getidinfo);

echo "<div class=\"form-group\"><label for=\"name\">Name</label><input type=\"text\" class=\"form-control\" id=\"name\" name=\"name\" value=\"" . $getidinforesult["name"] . "\" placeholder=\"Type a name...\" required></div>";
echo "<div class=\"form-group\"><label for=\"id\">ID</label><input type=\"text\" class=\"form-control\" id=\"id\" name=\"id\" value=\"" . $getidinforesult["id"] . "\" placeholder=\"Type a ID...\" required></div>";
echo "<div class=\"form-group\"><label for=\"url\">URL</label><input type=\"text\" class=\"form-control\" id=\"url\" name=\"url\" value=\"" . $getidinforesult["url"] . "\" placeholder=\"Type a URL...\" required></div>";
echo "<div class=\"form-group\"><label for=\"count\">Count</label><input type=\"number\" class=\"form-control\" id=\"count\" name=\"count\" value=\"" . $getidinforesult["count"] . "\" placeholder=\"Type an initial count...\"></div>";


echo "<div class=\"checkbox\"><label>";
    
//Check if we should show ads
$checkifadsshow = mysql_query("SELECT `showads` FROM `Data` WHERE `id` = \"$idtoedit\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow); 
if ($checkifadsshowresult["showads"] == "1") { 
    echo "<input type=\"checkbox\" id=\"showadsstate\" name=\"showadsstate\" checked=\"checked\"> Show ads";
} else {
    echo "<input type=\"checkbox\" id=\"showadsstate\"  name=\"showadsstate\"> Show ads";
}

echo "</label></div><div class=\"checkbox\"><label>";
    
//Check if download is protected
$checkifprotected = mysql_query("SELECT `protect` FROM `Data` WHERE `id` = \"$idtoedit\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected); 
if ($checkifprotectedresult["protect"] == "1") { 
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\" checked=\"checked\"> Enable password protection";
} else {
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\"> Enable password protection";
}

mysql_close($con);

?>
</label>
</div>
<div id="passwordentry" style="display: none;">
<div class="form-group">
<label for="password">Password</label>
<input type="password" class="form-control" id="password" name="password" placeholder="Type a password..." required>
</div>
</div>
<input type="hidden" id="idtoedit" name="idtoedit" value="<?php echo $idtoedit; ?>" />
<button type="submit" class="btn btn-default">Edit</button>
</form>
<?php
}
    }
?>
</div>
<script src="../assets/jquery.min.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/bootstrap-select/js/bootstrap-select.min.js"></script>
<script src="../assets/nod.min.js"></script>
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
    $("select").selectpicker({
        liveSearch: "true"
    });
    var metrics = [
        ["#name", "presence", "Name cannot be empty"],
        ["#id", "presence", "ID cannot be empty!"],
        ["#url", /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/, "Enter a valid URL!"],
        ["#count", "min-num:1", "Count must be higer than 0"]
    ];
    $("form").nod(metrics);
});
</script>
</body>
</html>