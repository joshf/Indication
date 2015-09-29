<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

require_once("assets/version.php");

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

//Stats
$gettotal = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts`");
$resultgettotal = mysqli_fetch_assoc($gettotal);

$getday = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE `date` = CURDATE()");
$resultgetday = mysqli_fetch_assoc($getday);

$getweek = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE WEEKOFYEAR(`date`) = WEEKOFYEAR(NOW())");
$resultgetweek = mysqli_fetch_assoc($getweek);

$getmonth = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE YEAR(`date`) = YEAR(NOW()) AND MONTH(`date`) = MONTH(NOW())");
$resultgetmonth = mysqli_fetch_assoc($getmonth);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="assets/favicon.ico">
<title>Indication</title>
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
<li class="active"><a href="index.php">Overview <span class="sr-only">(current)</span></a></li>
<li><a href="breakdowns.php">Breakdowns</a></li>
<li><a href="export.php">Export</a></li>
</ul>
<ul class="nav nav-sidebar">
<li><a href="add.php">Add New</a></li>
<li><a href="edit.php">Edit</a></li>
</ul>
</div>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h1 class="page-header">Dashboard</h1>
<div class="row placeholders">
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgettotal["count"]; ?></span>
<h4>All Time</h4>
<span class="text-muted">Hits from install</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgetday["count"]; ?></span>
<h4>Day</h4>
<span class="text-muted">Hits today</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgetweek["count"]; ?></span>
<h4>Week</h4>
<span class="text-muted">Hits this week</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
<span class="badge"><?php echo $resultgetmonth["count"]; ?></span>
<h4>Month</h4>
<span class="text-muted">Hits this week</span>
</div>
</div>
<h2 class="sub-header">Links</h2>
<div class="form-group has-feedback">
<input type="text" id="search" name="search" class="form-control" placeholder="Search your links..."> <span id="counter" class="text-muted form-control-feedback"></span>
</div>
<div class="table-responsive">
<table class="table table-striped results">
<thead>
<tr>
<th>Name</th>
<th>Abbreviation</th>
<th>Count</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php

$getlinks = mysqli_query($con, "SELECT * FROM `links`");

while($links = mysqli_fetch_assoc($getlinks)) {
    $id = $links["id"];
    $getcounts = mysqli_query($con, "SELECT COUNT(id) AS `count` FROM `counts` WHERE `link_id` = \"$id\"");
    $resultgetcounts = mysqli_fetch_assoc($getcounts);
    echo "<tr>";
    echo "<td><a href=\"breakdowns.php?id=" . $links["id"] . "\">" . $links["name"] . "</a></td>";
    echo "<td>" . $links["abbreviation"] . "</td>";
    echo "<td><span class=\"badge\">" . $resultgetcounts["count"] . "</span></td>";
    echo "<td><a href=\"edit.php?id=" . $links["id"] . "\">Edit</a> | <a class=\"delete\" data-id=\"" . $links["id"] . "\">Delete</a> | <a class=\"link\" data-abbreviation=\"" . $links["abbreviation"] . "\">Link</a>";
    echo "</tr>";
}

?>
</tbody>
</table>
</div>
<span class="pull-right text-muted"><small>Version <?php echo $version; ?></small></span>
</div>
</div>
</div>
<script src="assets/bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/bootbox.js/bootbox.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/js-cookie/src/js.cookie.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/modernizr-load/modernizr.js" type="text/javascript" charset="utf-8"></script>
<script src="assets/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">  
$(document).ready(function () {    
    var indication_version = "<?php echo $version ?>";
    if (!Cookies.get("indication_didcheckforupdates")) {
        $.getJSON("https://api.github.com/repos/joshf/Indication/releases").done(function(resp) {
            var data = resp[0];
            var indication_remote_version = data.tag_name;
            var url = data.zipball_url;
            if (indication_version < indication_remote_version) {
                bootbox.dialog({
                    message: "Indication " + indication_remote_version + " is available. Do you wish to download the update? For more information about this update click <a href=\""+ data.html_url + "\" target=\"_blank\">here</a>. If you click \"Not Now\" you will be not reminded for another 7 days.",
                    title: "Update Available",
                    buttons: {
                        cancel: {
                            label: "Not Now",
                            callback: function() {
                                Cookies.set("indication_didcheckforupdates", "1", { expires: 7 });
                            }
                        },
                        main: {
                            label: "Download Update",
                            className: "btn-primary",
                            callback: function() {
                                window.location.href = data.zipball_url;
                            }
                        }
                    }
                });
            }
        });
    }
    $("td").on("click", ".delete", function() {
        var id = $(this).data("id");
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: "action=delete&id="+ id +"",
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
                    message: "Link deleted!",
                    icon: "glyphicon glyphicon-ok",
                },{
                    type: "success",
                    allow_dismiss: true
                });
                setTimeout(function() {
                	window.location.reload();
                }, 2000);
            }
        });
    });
    $("td").on("click", ".link", function() {
        var abbreviation = $(this).data("abbreviation");
        bootbox.prompt({
            title: "Link",
            <?php
            if (CUSTOM_URL_STATE == "Enabled") {
                if (CUSTOM_URL != "") {
                    echo "value: \"" . CUSTOM_URL . "/\" + abbreviation + \"\",\n";
                }
            } else {
                echo "value: \"" . PATH_TO_SCRIPT . "/get.php?id=\" + abbreviation + \"\",\n";
            }
            ?>
            callback: function(result) {
                //Do nothing
            }
        });
    });
    $("#search").keyup(function () {
        $("#counter").removeClass("hidden");
        var term = $("#search").val();
        if (term == "") {
            $("#counter").addClass("hidden");
        }
        var list_tem = $(".results tbody").children("tr");
        var search_split = term.replace(/ /g, "\"):containsi(\"")
        $.extend($.expr[":"], {"containsi": function(elem, i, match, array) {
                return (elem.textContent || elem.innerText || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
            }
        });
        $(".results tbody tr").not(":containsi(\"" + search_split + "\")").each(function(e){
            $(this).attr("visible","false");
        });
        $(".results tbody tr:containsi(\"" + search_split + "\")").each(function(e){
            $(this).attr("visible","true");
        });
        var count = $(".results tbody tr[visible=true]").length;
        $("#counter").text(count);
        if (count == "0") {
            $("#counter").text("0");
        }
    });
});
</script>
</body>
</html>