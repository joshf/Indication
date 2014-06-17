<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

require_once("../assets/version.php");

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

//Set cookie so we dont constantly check for updates
setcookie("indicationupdatecheck", time(), time()+604800);

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

$getusersettings = mysqli_query($con, "SELECT `user` FROM `Users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
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
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimal-ui">
<title>Indication</title>
<link rel="apple-touch-icon" href="../assets/icon.png">
<link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="../assets/datatables/css/dataTables.bootstrap.min.css" rel="stylesheet">
<link href="../assets/bootstrap-notify/css/bootstrap-notify.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 30px;
    padding-bottom: 30px;
}
/* Fix weird notification appearance */
a.close.pull-right {
    padding-left: 10px;
}
/* Slim down the actions column */
tr td:last-child {
    width: 74px;
    white-space: nowrap;
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
<li class="active"><a href="index.php">Home</a></li>
<li><a href="add.php">Add</a></li>
<li><a href="edit.php">Edit</a></li>
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
<h1>Downloads for <?php echo WEBSITE; ?></h1>
</div>
<div class="notifications top-right"></div>	
<?php

echo "<noscript><div class=\"alert alert-info\"><h4 class=\"alert-heading\">Information</h4><p>Please enable JavaScript to use Indication. For instructions on how to do this, see <a href=\"http://www.activatejavascript.org\" class=\"alert-link\" target=\"_blank\">here</a>.</p></div></noscript>";

//Update checking
if (!isset($_COOKIE["indicationupdatecheck"])) {
    $remoteversion = file_get_contents("https://raw.github.com/joshf/Indication/master/version.txt");
    if (version_compare($version, $remoteversion) < 0) {            
        echo "<div class=\"alert alert-warning\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><h4 class=\"alert-heading\">Update</h4><p>Indication <a href=\"https://github.com/joshf/Indication/releases/$remoteversion\" class=\"alert-link\" target=\"_blank\">$remoteversion</a> is available. <a href=\"https://github.com/joshf/Indication#updating\" class=\"alert-link\" target=\"_blank\">Click here for instructions on how to update</a>.</p></div>";
    }
} 

$getdownloads = mysqli_query($con, "SELECT * FROM `Data`");

echo "<table id=\"downloads\" class=\"table table-bordered table-hover table-condensed\">
<thead>
<tr>
<th>Name</th>
<th class=\"hidden-xs\">URL</th>
<th>Count</th>
<th>Actions</th>
</tr></thead><tbody>";

while($row = mysqli_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td class=\"hidden-xs\">" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "<td><div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\"><a href=\"edit.php?id=" . $row["id"] . "\" class=\"btn btn-default btn-xs\" role=\"button\"><span class=\"glyphicon glyphicon-edit\"></span></a><button type=\"button\" class=\"trackinglink btn btn-default btn-xs\" data-id=\"" . $row["id"] . "\"><span class=\"glyphicon glyphicon-share-alt\"></span></button><button type=\"button\" class=\"delete btn btn-default btn-xs\" data-id=\"" . $row["id"] . "\"><span class=\"glyphicon glyphicon-trash\"></span></button></div></div></td>";
    echo "</tr>";
}
echo "</tbody></table>";

?>
<div class="alert alert-info">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>   
<b>Info:</b> To edit, delete or show the tracking link for a download please select the radio button next to it.  
</div>
<div class="well">
<?php

$getnumberofdownloads = mysqli_query($con, "SELECT COUNT(id) FROM `Data`");
$resultgetnumberofdownloads = mysqli_fetch_assoc($getnumberofdownloads);
echo "<i class=\"glyphicon glyphicon-list-alt\"></i> <b>" . $resultgetnumberofdownloads["COUNT(id)"] . "</b> items<br>";

$gettotalnumberofdownloads = mysqli_query($con, "SELECT SUM(count) FROM `Data`");
$resultgettotalnumberofdownloads = mysqli_fetch_assoc($gettotalnumberofdownloads);
if (is_null($resultgettotalnumberofdownloads["SUM(count)"])) {
    echo "<i class=\"glyphicon glyphicon-download\"></i> <b>0</b> total downloads";
} else {
    echo "<i class=\"glyphicon glyphicon-download\"></i> <b>" . $resultgettotalnumberofdownloads["SUM(count)"] . "</b> total downloads";
}

mysqli_close($con);

?>
</div>
<hr>
<div class="footer">
Indication <?php echo $version; ?> &copy; <a href="http://joshf.co.uk" target="_blank">Josh Fradley</a> <?php echo date("Y"); ?>. Themed by <a href="http://getbootstrap.com" target="_blank">Bootstrap</a>.
</div>
</div>
<script src="../assets/jquery.min.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/datatables/js/jquery.dataTables.min.js"></script>
<script src="../assets/datatables/js/dataTables.bootstrap.min.js"></script>
<script src="../assets/bootbox.min.js"></script>
<script src="../assets/bootstrap-notify/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    /* Set Up Notifications */
    var show_notification = function(type, icon, text, reload) {
        $(".top-right").notify({
            type: type,
            transition: "fade",
            icon: icon,
            message: {
                text: text
            },
            onClosed: function() {
                if (reload == true) {
                    window.location.reload();
                }
            }
        }).show();
    };
    /* End */
    /* Datatables */
    $("#downloads").dataTable({
        "aoColumns": [
            null,
            null,
            null,
            {"bSortable": false}
        ]
    });
    /* End */
    /* Delete */
    $("table").on("click", ".delete", function() {
        var id = $(this).data("id");
        bootbox.confirm("Are you sure you want to delete this download?", function(result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: "actions/worker.php",
                    data: "action=delete&id="+ id +"",
                    error: function() {
                        show_notification("danger", "warning-sign", "Ajax query failed!");
                    },
                    success: function() {
                        show_notification("success", "ok", "Download deleted!", true);
                    }
                });
            }
        });
    });
    /* End */
    /* Show tracking Link */
    $("table").on("click", ".trackinglink", function() {
        var id = $(this).data("id");
        bootbox.prompt({
            title: "Tracking Link",
            value: "<?php echo PATH_TO_SCRIPT; ?>/get.php?id=" + id + "",
            callback: function(result) {
                /* This has to be here for some reason */
            }
        });
    });
    /* End */
});
</script>
</body>
</html>