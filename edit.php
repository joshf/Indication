<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    die("Error: Config file not found!");
}

require_once("config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit;
}

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

$getusersettings = mysqli_query($con, "SELECT `user` FROM `users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysqli_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysqli_fetch_assoc($getusersettings);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="assets/favicon.ico">
<title>Indication &raquo; Edit</title>
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
<nav class="navbar navbar-inverse navbar-fixed-top">
<div class="container-fluid">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="index.php">Indication</a>
</div>
<div id="navbar" class="navbar-collapse collapse">
<ul class="nav navbar-nav navbar-right">
<li><a href="index.php">Dashboard</a></li>
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>
<div class="container-fluid">
<div class="row">
<div class="col-sm-3 col-md-2 sidebar">
<ul class="nav nav-sidebar">
<li><a href="index.php">Overview</a></li>
<li><a href="breakdowns.php">Breakdowns</a></li>
<li><a href="export.php">Export</a></li>
</ul>
<ul class="nav nav-sidebar">
<li><a href="add.php">Add New</a></li>
<li class="active"><a href="edit.php">Edit <span class="sr-only">(current)</span></a></li>
</ul>
</div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h1 class="page-header">Edit Link</h1>
<?php

if (!isset($_GET["id"])) {
    $getids = mysqli_query($con, "SELECT `id`, `name` FROM `links`");
    if (mysqli_num_rows($getids) != 0) {
        echo "<form action=\"edit.php\" method=\"get\"><div class=\"form-group\"><label for=\"id\">Select a Link</label><select class=\"form-control\" id=\"id\" name=\"id\">";
        while($row = mysqli_fetch_assoc($getids)) {
            echo "<option value=\"" . $row["id"] . "\">" . ucfirst($row["name"]) . "</option>";
        }
        echo "</select></div><button type=\"submit\" class=\"btn btn-default\">Edit</button></form>";
    } else {
        echo "<div class=\"alert alert-info\"><h4 class=\"alert-heading\">Information</h4><p>No downloads available to edit.</p><p><a class=\"btn btn-info\" href=\"index.php\">Go Back</a></p></div>";
    }
} else {

$idtoedit = mysqli_real_escape_string($con, $_GET["id"]);

//Check if ID exists
$getdata = mysqli_query($con, "SELECT `id`, `name`, `abbreviation`, `url`, `protect`, `password` FROM `links` WHERE `id` = $idtoedit");
if (mysqli_num_rows($getdata) == 0) {
    echo "<div class=\"alert alert-danger\"><h4 class=\"alert-heading\">Error</h4><p>ID does not exist.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div>";
} else {
    
$resultgetdata = mysqli_fetch_assoc($getdata);

?>
<form id="editform" autocomplete="off">
<div class="form-group">
<input type="text" class="form-control" id="name" name="name" placeholder="Type a name..." value="<?php echo $resultgetdata["name"]; ?>" required>
</div>
<div class="form-group">
<input type="text" class="form-control" id="abbreviation" name="abbreviation" value="<?php echo $resultgetdata["abbreviation"]; ?>" placeholder="Type an abbreviation..." required>
</div>
<div class="form-group">
<input type="url" class="form-control" id="url" name="url" value="<?php echo $resultgetdata["url"]; ?>" placeholder="Type a URL..." required>
</div>
<div class="checkbox">
<label>
<?php

if ($resultgetdata["protect"] == "1") { 
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\" checked> Password protection";
} else {
    echo "<input type=\"checkbox\" id=\"passwordprotectstate\" name=\"passwordprotectstate\"> Password protection";
}
    
?>
</label>
</div>
<input type="hidden" id="id" name="id" value="<?php echo $resultgetdata["id"]; ?>">
<?php

if ($resultgetdata["protect"] == "1") { 
    echo "<input type=\"hidden\" id=\"password\" name=\"password\" value=\"" . $resultgetdata["password"] . "\">";
} else {
    echo "<input type=\"hidden\" id=\"password\" name=\"password\">";
}
    
?>
<input type="hidden" id="action" name="action" value="edit">
<button type="submit" class="btn btn-default">Edit</button>
</form>
<?php
}
    }
?>
</div>
</div>
</div>
<script src="assets/bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap-validator/dist/validator.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootbox.js/bootbox.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function () {
    $("#editform").validator({
        disable: true
    });
    $("#passwordprotectstate").click(function() {
        if ($("#passwordprotectstate").prop("checked") == true) {
            bootbox.prompt({
                title: "Enter a password",
                inputType: "password",
                callback: function(result) {
                    if (result === null) {
                        $("#passwordprotectstate").prop("checked", false)
                    } else {
                        $("#password").val(result);
                    }
                }
            });
        } else {
            $("#password").val("");
        }
    });
    $("#editform").validator().on("submit", function (e) {
        if (e.isDefaultPrevented()) {
            return false;
        } 
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: $("#editform").serialize(),
            error: function() {
                $.notify({
                    message: "Ajax query failed!",
                    icon: "glyphicon glyphicon-warning-sign",
                },{
                    type: "danger",
                    allow_dismiss: true
                });
            },
            success: function() {
                $.notify({
                    message: "Link edited!",
                    icon: "glyphicon glyphicon-ok",
                },{
                    type: "success",
                    allow_dismiss: true
                });
            }
        });
        return false;
    });
});
</script>
</body>
</html>