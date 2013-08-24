<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

$version = "4.4";

if (!file_exists("../config.php")) {
	die("Error: Config file not found! Please reinstall Indication.");
}

require_once("../config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit; 
}

//Set cookie so we dont constantly check for updates
setcookie("indicationhascheckedforupdates", "checkedsuccessfully", time()+604800);

@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
} else {
    $does_db_exist = mysql_select_db(DB_NAME, $con);
    if (!$does_db_exist) {
        die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if (THEME == "default") {
    echo "<link href=\"../resources/bootstrap/css/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.2/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}
?>
<link href="../resources/bootstrap/css/bootstrap-responsive.min.css" type="text/css" rel="stylesheet">
<link href="../resources/datatables/jquery.dataTables-bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="../resources/bootstrap-notify/css/bootstrap-notify.min.css" type="text/css" rel="stylesheet">
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
<li><a href="add.php"><i class="icon-plus-sign"></i> Add</a></li>
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
<h1>All Downloads</h1>
</div>
<div class="notifications top-right"></div>		
<noscript><div class="alert alert-info"><h4 class="alert-heading">Information</h4><p>Please enable JavaScript to use Indication. For instructions on how to do this, see <a href="http://www.activatejavascript.org" target="_blank">here</a>.</p></div></noscript>
<?php

//Update checking
if (!isset($_COOKIE["indicationhascheckedforupdates"])) {
    $remoteversion = file_get_contents("https://raw.github.com/joshf/Indication/master/version.txt");
    if (preg_match("/^[0-9.-]{1,}$/", $remoteversion)) {
        if ($version < $remoteversion) {
            echo "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Update</h4><p>Indication <a href=\"https://github.com/joshf/Indication/releases/$remoteversion\" target=\"_blank\">$remoteversion</a> is available. <a href=\"https://github.com/joshf/Indication#updating\" target=\"_blank\">Click here to update</a>.</p></div>";
        }
    }
}

$getdownloads = mysql_query("SELECT * FROM `Data`");

echo "<table id=\"downloads\" class=\"table table-striped table-bordered table-condensed\">
<thead>
<tr>
<th></th>
<th>Name</th>
<th class=\"hidden-phone\">URL</th>
<th>Count</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td><input name=\"id\" type=\"radio\" value=\"" . $row["id"] . "\"></td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td class=\"hidden-phone\">" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table>";

?>
<div class="btn-group">
<button id="edit" class="btn">Edit</button>
<button id="delete" class="btn">Delete</button>
<button id="trackinglink" class="btn">Copy Tracking Link</button>
</div>
<br>
<br>
<div class="alert alert-info">   
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
<!-- Content end -->
<!-- Javascript start -->
<script src="../resources/jquery.min.js"></script>
<script src="../resources/bootstrap/js/bootstrap.min.js"></script>
<script src="../resources/datatables/jquery.dataTables.min.js"></script>
<script src="../resources/datatables/jquery.dataTables-bootstrap.min.js"></script>
<script src="../resources/bootstrap-notify/js/bootstrap-notify.min.js"></script>
<script src="../resources/bootbox/bootbox.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    /* Table selection */
    id_selected = false;
    $("#downloads input[name=id]").click(function() {
        id = $("#downloads input[name=id]:checked").val();
        id_selected = true;
    });
    /* End */
    /* Datatables */
    $("#downloads").dataTable({
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "aoColumnDefs": [{
            "bSortable": false,
            "aTargets": [0]
        }]
    });
    $.extend($.fn.dataTableExt.oStdClasses, {
        "sSortable": "header",
        "sWrapper": "dataTables_wrapper form-inline"
    });
    /* End */  
    /* Edit */
    $("#edit").click(function() {
        if (id_selected == true) {
            window.location = "edit.php?id="+ id +"";
        } else {
            $(".top-right").notify({
                type: "info",
                transition: "fade",
                icon: "info-sign",
                message: {
                    text: "No ID selected!"
                }
            }).show();
        }
    });
    /* End */
    /* Delete */
    $("#delete").click(function() {
        if (id_selected == true) {
            bootbox.confirm("Are you sure you want to delete the selected download?", "No", "Yes", function(result) {
                if (result == true) {
                    $.ajax({
                        type: "POST",
                        url: "actions/worker.php",
                        data: "action=delete&id="+ id +"",
                        error: function() {
                            $(".top-right").notify({
                                type: "error",
                                transition: "fade",
                                icon: "warning-sign",
                                message: {
                                    text: "Ajax query failed!"
                                }
                            }).show();
                        },
                        success: function() {
                            $(".top-right").notify({
                                type: "success",
                                transition: "fade",
                                icon: "ok",
                                message: {
                                    text: "Download deleted!"
                                },
                                onClosed: function() {
                                    window.location.reload();
                                }
                            }).show();
                        }
                    });
                }
            });
        } else {
            $(".top-right").notify({
                type: "info",
                transition: "fade",
                icon: "info-sign",
                message: {
                    text: "No ID selected!"
                }
            }).show();
        }
    });
    /* End */
    /* Show tracking Link */
    $("#trackinglink").click(function() {
        if (id_selected == true) {
            bootbox.prompt("Tracking Link", "Cancel", "Ok", null, "<?php echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
            /* Select form automatically (For Firefox) */
            $(".input-block-level").select();
        } else {
            $(".top-right").notify({
                type: "info",
                transition: "fade",
                icon: "info-sign",
                message: {
                    text: "No ID selected!"
                }
            }).show();
        }
    });
    /* End */
});
</script>
<!-- Javascript end -->
</body>
</html>