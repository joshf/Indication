<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

$version = "4.3.1dev";

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

//Set cookie so we dont constantly check for updates
setcookie("indicationhascheckedforupdates", "checkedsuccessfully", time()+604800);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Indication</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if (THEME == "default") {
    echo "<link href=\"../resources/bootstrap/css/bootstrap.css\" type=\"text/css\" rel=\"stylesheet\">\n";  
} else {
    echo "<link href=\"//netdna.bootstrapcdn.com/bootswatch/2.3.1/" . THEME . "/bootstrap.min.css\" type=\"text/css\" rel=\"stylesheet\">\n";
}
?>
<link href="../resources/datatables/dataTables.bootstrap.css" type="text/css" rel="stylesheet">
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
<li class="active"><a href="index.php">Home</a></li>
<li class="divider-vertical"></li>
<li><a href="add.php">Add</a></li>
<li><a href="edit.php">Edit</a></li>
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
<h1>All Downloads</h1>
</div>		
<noscript><div class="alert alert-info"><h4 class="alert-heading">Information</h4><p>Please enable JavaScript to use Indication. For instructions on how to do this, see <a href="http://www.activatejavascript.org" target="_blank">here</a>.</p></div></noscript>
<?php

@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Could not connect to database (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("<div class=\"alert alert-error\"><h4 class=\"alert-heading\">Error</h4><p>Database does not exist (" . mysql_error() . "). Check your database settings are correct.</p><p><a class=\"btn btn-danger\" href=\"javascript:history.go(-1)\">Go Back</a></p></div></div></body></html>");
}

$getdownloads = mysql_query("SELECT * FROM Data");

//Update checking
if (!isset($_COOKIE["indicationhascheckedforupdates"])) {
    $remoteversion = file_get_contents("https://raw.github.com/joshf/Indication/master/version.txt");
    if (preg_match("/^[0-9.-]{1,}$/", $remoteversion)) {
        if ($version < $remoteversion) {
            echo "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><h4 class=\"alert-heading\">Update</h4><p><a href=\"https://github.com/joshf/Indication/compare/$version...$remoteversion\" target=\"_blank\">Indication $remoteversion</a> is available. <a href=\"https://github.com/joshf/Indication/wiki/Updating-Indication\" target=\"_blank\">Click here to update</a>.</p></div>";
        }
    }
}

echo "<table id=\"downloads\" class=\"table table-striped table-bordered table-condensed\">
<thead>
<tr>
<th>ID</th>
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
<div id="deleteconfirmdialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="dcdheader" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h3 id="dcdheader">Confirm Delete</h3>
</div>
<div class="modal-body">
<p>Are you sure you want to delete the selected download?</p>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
<button id="deleteconfirm" class="btn btn-primary">Delete</button>
</div>
</div>
<div id="trackinglinkdialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="tldheader" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h3 id="tldheader">Tracking Link</h3>
</div>
<div class="modal-body">
<p>The tracking link for the selected download has been copied to your clipboard.</p>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
</div>
</div>
<div id="noidselecteddialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="nisdheader" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
<h3 id="nisdheader">Error</h3>
</div>
<div class="modal-body">
<p>No ID selected.</p>
</div>
<div class="modal-footer">
<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
</div>
</div>
<div class="alert alert-info">   
<b>Info:</b> To edit, delete or show the tracking link for a download please select the radio button next to it.  
</div>
<div class="well">
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
$resultnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<i class=\"icon-list-alt\"></i> <b>" . $resultnumberofdownloads["COUNT(id)"] . "</b> items<br>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM Data");
$resulttotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
if ($resulttotalnumberofdownloads["SUM(count)"] > "1") {
    echo "<i class=\"icon-download\"></i> <b>" . $resulttotalnumberofdownloads["SUM(count)"] . "</b> total downloads";
} else {
    echo "<i class=\"icon-download\"></i> <b>0</b> total downloads";
}

mysql_close($con);

?>
</div>
<hr>
<p class="muted pull-right">Indication <?php echo $version; ?> &copy; <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <?php echo date("Y"); ?>. Themed by <a href="http://twitter.github.com/bootstrap/" target="_blank">Bootstrap</a>.</p>
</div>
<!-- Content end -->
<!-- Javascript start -->	
<script src="../resources/jquery.js"></script>
<script src="../resources/bootstrap/js/bootstrap.js"></script>
<script src="../resources/datatables/jquery.dataTables.js"></script>
<script src="../resources/datatables/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="../resources/zeroclipboard/ZeroClipboard.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    /* Table selection */
    id_selected = false;
    $("#downloads input[name=id]").click(function() {
        id = $("#downloads input[name=id]:checked").val();
        id_selected = true;
        /* Set clipboard text */
        clip.setText("<?php echo PATH_TO_SCRIPT; ?>/get.php?id=" + id + "");
        /* End */
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
            $("#noidselecteddialog").modal("show");    
        }
    });
    /* End */
    /* Show Delete Dialog */
    $("#delete").click(function() {
        if (id_selected == true) {
            $("#deleteconfirmdialog").modal("show");
        } else {
            $("#noidselecteddialog").modal("show");    
        }
    });
    /* End */
    /* Delete worker */
    $("#deleteconfirm").click(function() {
        $("#deleteconfirmdialog").modal("hide");
        $.ajax({  
            type: "POST",  
            url: "actions/worker.php",  
            data: "action=delete&id="+ id +"",
            error: function() {  
                alert("Ajax query failed!");
            },
            success: function() {  
                window.location.reload();    
            }	
        });
    });
    /* End */
    /* Copy/show tracking Link */
    no_flash = false;
    var clip = new ZeroClipboard($("#trackinglink"), {
        moviePath: "../resources/zeroclipboard/ZeroClipboard.swf"
    });
    clip.on("noflash", function(client, args) {
        no_flash = true;
    });
    if (no_flash == true) {
        $("#trackinglink").click(function() {
            if (id_selected == true) {
                prompt("Tracking link for selected download. Press Ctrl/Cmd C to copy to the clipboard:", "<?php echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
            } else {
                $("#noidselecteddialog").modal("show");
            }
        });
    }
    clip.on("complete", function(client, args) {
        if (id_selected == true) {
            $("#trackinglinkdialog").modal("show");
        } else {
            $("#noidselecteddialog").modal("show");    
        }
    });
    /* End */
});
</script>
<!-- Javascript end -->
</body>
</html>