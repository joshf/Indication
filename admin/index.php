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

@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
} else {
    $does_db_exist = mysql_select_db(DB_NAME, $con);
    if (!$does_db_exist) {
        die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
    }
}

$getusersettings = mysql_query("SELECT `user`, `theme` FROM `Users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
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
<title>Indication</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../assets/bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">  
<link href="../assets/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
<link href="../assets/datatables/jquery.dataTables-bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="../assets/bootstrap-notify/css/bootstrap-notify.min.css" type="text/css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 60px;
}
@media (max-width: 980px) {
    body {
        padding-top: 0;
    }
}
/* Slim down the actions column */
tr td:last-child {
    width: 84px;
    white-space: nowrap;
}
.btn-toolbar {
    margin-top: 0px;
    margin-bottom: 0px;
}
</style>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<div class="navbar navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">Indication</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li class="divider-vertical"></li>
<li class="active"><a href="index.php">Home</a></li>
<li><a href="add.php">Add</a></li>
<li><a href="edit.php">Edit</a></li>
</ul>
<ul class="nav pull-right">
<li class="divider-vertical"></li>
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
</div>
<div class="container">
<div class="page-header">
<h1>Downloads for <?php echo WEBSITE; ?></h1>
</div>
<div class="notifications top-right"></div>		
<noscript><div class="alert alert-info"><h4 class="alert-heading">Information</h4><p>Please enable JavaScript to use Indication. For instructions on how to do this, see <a href="http://www.activatejavascript.org" target="_blank">here</a>.</p></div></noscript>
<?php

//Update checking
if (!isset($_COOKIE["indicationupdatecheck"])) {
    $remoteversion = file_get_contents("https://raw.github.com/joshf/Indication/master/version.txt");
    if (preg_match("/^[0-9.-]{1,}$/", $remoteversion)) {
        if ($version < $remoteversion) {
            echo "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Update</h4><p>Indication <a href=\"https://github.com/joshf/Indication/releases/$remoteversion\" target=\"_blank\">$remoteversion</a> is available. <a href=\"https://github.com/joshf/Indication#updating\" target=\"_blank\">Click here to update</a>.</p></div>";
        }
    }
}

$getdownloads = mysql_query("SELECT * FROM `Data`");

echo "<table id=\"downloads\" class=\"table table-bordered table-hover table-condensed\">
<thead>
<tr>
<th>Name</th>
<th class=\"hidden-phone\">URL</th>
<th>Count</th>
<th>Actions</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td class=\"hidden-phone\">" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "<td><div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\"><a href=\"edit.php?id=" . $row["id"] . "\" class=\"btn btn-default btn-mini\" role=\"button\"><span class=\"icon-edit\"></span></a><button type=\"button\" class=\"trackinglink btn btn-default btn-mini\" data-id=\"" . $row["id"] . "\"><span class=\"icon-share-alt\"></span></button><button type=\"button\" class=\"delete btn btn-default btn-mini\" data-id=\"" . $row["id"] . "\"><span class=\"icon-trash\"></span></button></div></div></td>";
    echo "</tr>";
}
echo "</tbody></table>";

?>
<div class="alert alert-info">
<button type="button" class="close" data-dismiss="alert">&times;</button>   
<b>Info:</b> To edit, delete or show the tracking link for a download please select the radio button next to it.  
</div>
<div class="well">
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM `Data`");
$resultgetnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<i class=\"icon-list-alt\"></i> <b>" . $resultgetnumberofdownloads["COUNT(id)"] . "</b> items<br>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM `Data`");
$resultgettotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
if (is_null($resultgettotalnumberofdownloads["SUM(count)"])) {
    echo "<i class=\"icon-download\"></i> <b>0</b> total downloads";
} else {
    echo "<i class=\"icon-download\"></i> <b>" . $resultgettotalnumberofdownloads["SUM(count)"] . "</b> total downloads";
}

mysql_close($con);

?>
</div>
<hr>
<p class="muted pull-right">Indication <?php echo $version; ?> &copy; <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <?php echo date("Y"); ?>. Themed by <a href="http://twitter.github.com/bootstrap/" target="_blank">Bootstrap</a>.</p>
</div>
<script src="../assets/jquery.min.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/datatables/jquery.dataTables-bootstrap.min.js"></script>
<script src="../assets/bootstrap-notify/js/bootstrap-notify.min.js"></script>
<script src="../assets/bootbox/bootbox.min.js"></script>
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
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "aoColumns": [
            null,
            null,
            null,
            {"bSortable": false}
        ]
    });
    $.extend($.fn.dataTableExt.oStdClasses, {
        "sSortable": "header",
        "sWrapper": "dataTables_wrapper form-inline"
    });
    /* End */  
    /* Delete */
    $("table").on("click", ".delete", function() {
        var id = $(this).data("id");
        bootbox.confirm("Are you sure you want to delete the selected download?", "No", "Yes", function(result) {
            if (result == true) {
                $.ajax({
                    type: "POST",
                    url: "actions/worker.php",
                    data: "action=delete&id="+ id +"",
                    error: function() {
                        show_notification("error", "warning-sign", "Ajax query failed!");
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
        bootbox.prompt("Tracking Link", "Cancel", "Ok", null, "<?php echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
        /* Select form automatically (For Firefox) */
        $(".input-block-level").select();
    });
    /* End */
});
</script>
</body>
</html>