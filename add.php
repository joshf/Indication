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
<title>Indication &raquo; Add</title>
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
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout (<?php echo $resultgetusersettings["user"]; ?>)</a></li>
</ul>
</div>
</div>
</nav>
<div class="container-fluid">
<div class="row">
<div class="col-sm-3 col-md-2 sidebar">
<ul class="nav nav-sidebar">
<li><a href="index.php">Dashboard</a></li>
<li><a href="details.php">Details</a></li>
<li><a href="export.php">Export</a></li>
</ul>
<ul class="nav nav-sidebar">
<li class="active"><a href="add.php">Add New <span class="sr-only">(current)</span></a></li>
<li><a href="edit.php">Edit</a></li>
</ul>
</div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h1 class="page-header">Add Link</h1>
<form id="addform" autocomplete="off">
<div class="form-group">
<input type="text" class="form-control" id="name" name="name" placeholder="Type a name..." required>
</div>
<div class="form-group">
<input type="text" class="form-control" id="abbreviation" name="abbreviation" placeholder="Type an abbreviation..." required>
</div>
<div class="form-group">
<input type="url" class="form-control" id="url" name="url" placeholder="Type a URL..." required>
</div>
<div class="checkbox">
<label>
<input type="checkbox" id="passwordprotectstate" name="passwordprotectstate"> Password Protection
</label>
</div>
<input type="hidden" id="password" name="password">
<input type="hidden" id="action" name="action" value="add">
<button type="submit" class="btn btn-default">Add</button>
</form>
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
    $("#addform").validator({
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
    $("#addform").validator().on("submit", function (e) {
        if (e.isDefaultPrevented()) {
            return false;
        } 
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: $("#addform").serialize(),
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
                    message: "Link added!",
                    icon: "glyphicon glyphicon-ok",
                },{
                    type: "success",
                    allow_dismiss: true
                });
                $("#addform").trigger("reset");
            }
        });
        return false;
    });
});
</script>
</body>
</html>