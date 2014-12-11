<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

require_once("assets/version.php");

if (!file_exists("config.php")) {
    header("Location: install");
    exit;
}

require_once("config.php");

session_start();
if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit;
}

//Set cookie so we dont constantly check for updates
setcookie("indicationupdatecheck", time(), time()+3600*24*7);

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
<link rel="apple-touch-icon" href="assets/icon.png">
<link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/bootstrap-notify/css/bootstrap-notify.min.css" rel="stylesheet">
<style type="text/css">
body {
    padding-top: 30px;
    padding-bottom: 30px;
}
a.close.pull-right {
    padding-left: 10px;
}
.edit, .delete {
    cursor: pointer;
}
</style>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="index.php">Indication</a>
</div>
<div class="navbar-collapse collapse" id="navbar-collapse">
<div class="navbar-form navbar-left" role="search">
<div class="form-group">
<input type="text" class="form-control" id="search" placeholder="Search downloads">
</div>
</div>
<ul class="nav navbar-nav navbar-right">
<li class="dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $resultgetusersettings["user"]; ?> <span class="caret"></span></a>
<ul class="dropdown-menu" role="menu">
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</li>
</ul>
</div>
</div>
</nav>
<div class="container">
<div class="page-header">
<h1>Downloads
<small><?php echo WEBSITE; ?></small></h1>
</h1></div><div class="notifications top-right"></div>
<noscript><div class="alert alert-info"><h4 class="alert-heading">Information</h4><p>Please enable JavaScript to use Indication. For instructions on how to do this, see <a href="http://www.activatejavascript.org" class="alert-link" target="_blank">here</a>.</p></div></noscript>
<?php

//Update checking
if (!isset($_COOKIE["indicationupdatecheck"])) {
    $remoteversion = file_get_contents("https://raw.github.com/joshf/Indication/master/version.txt");
    if (version_compare($version, $remoteversion) < 0) {
        echo "<div class=\"alert alert-warning\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button><h4 class=\"alert-heading\">Update</h4><p>Indication <a href=\"https://github.com/joshf/Indication/releases/$remoteversion\" class=\"alert-link\" target=\"_blank\">$remoteversion</a> is available. <a href=\"https://github.com/joshf/Indication#updating\" class=\"alert-link\" target=\"_blank\">Click here for instructions on how to update</a>.</p></div>";
    }
}

$getdownloads = mysqli_query($con, "SELECT * FROM `Data`");

$numberofitems = 0;

echo "<ul class=\"list-group\">";
if (mysqli_num_rows($getdownloads) != 0) {
    while($row = mysqli_fetch_assoc($getdownloads)) {
        $numberofitems++;
        echo "<li class=\"list-group-item\" id=\"" . $row["id"] . "\" >" . $row["name"] . "<div class=\"pull-right\">";
        echo "<span class=\"edit glyphicon glyphicon-edit\" data-id=\"" . $row["id"] . "\"></span> ";
        echo "<span class=\"delete glyphicon glyphicon-trash\" data-id=\"" . $row["id"] . "\"></span>";
        echo "</div></li>";
    }
} else {
    echo "<li class=\"list-group-item\">No downloads to show</li>";
}
echo "</ul>";

?>
<button type="button" id="launchaddmodal" class="btn btn-default">Add</button><br><br>
<div class="alert alert-info">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
<b>Info:</b> To edit, delete or show the tracking link for a download please select the radio button next to it.  
</div>
<div class="well">
<?php

$getnumberofdownloads = mysqli_query($con, "SELECT COUNT(id) FROM `Data`");
$resultgetnumberofdownloads = mysqli_fetch_assoc($getnumberofdownloads);
echo "<i class=\"glyphicon glyphicon-list-alt\"></i> <b>" . $numberofitems . "</b> items<br>";

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
<!-- Add form -->
<div class="modal fade" id="addformmodal" tabindex="-1" role="dialog" aria-labelledby="addformmodal" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<h4 class="modal-title" id="addformmodaltitle">Add Download</h4>
</div>
<div class="modal-body">
<form id="addform" role="form" autocomplete="off">
<div class="form-group">
<input type="text" class="form-control" id="name" name="name" placeholder="Type a name..." required>
</div>
<div class="form-group">
<input type="text" class="form-control" id="downloadid" name="downloadid" placeholder="Type a ID..." required>
</div>
<div class="form-group">
<input type="text" class="form-control" id="url" name="url" placeholder="Type a URL..." required>
</div>
<div class="form-group">
<input type="number" class="form-control" id="count" name="count" placeholder="Type an initial count...">
</div>
<div class="checkbox">
<label>
<input type="checkbox" id="showadsstate" name="showadsstate"> Show ads
</label>
</div>
<div class="checkbox">
<label>
<input type="checkbox" id="passwordprotectstate" name="passwordprotectstate"> Enable password protection
</label>
</div>
<div id="passwordentry" style="display: none;">
<div class="form-group">
<input type="password" class="form-control" id="password" name="password" placeholder="Type a password..." required>
</div>
</div>
<input type="hidden" id="action" name="action" value="add">
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<button type="button" id="add" class="btn btn-primary">Add</button>
</div>
</div>
</div>
</div>
<!-- Add form end -->
<!-- Edit form -->
<div class="modal fade" id="editformmodal" tabindex="-1" role="dialog" aria-labelledby="editformmodal" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
<h4 class="modal-title" id="editformmodaltitle">Edit Download</h4>
</div>
<div class="modal-body">
<form id="editform" role="form" autocomplete="off">
<div class="form-group">
<input type="text" class="form-control" id="editname" name="name" placeholder="Type a name..." required>
</div>
<div class="form-group">
<input type="text" class="form-control" id="editdownloadid" name="downloadid" placeholder="Type a ID..." required>
</div>
<div class="form-group">
<input type="text" class="form-control" id="editurl" name="url" placeholder="Type a URL..." required>
</div>
<div class="form-group">
<input type="number" class="form-control" id="editcount" name="count" placeholder="Type an initial count...">
</div>
<div class="checkbox">
<label>
<input type="checkbox" id="editshowadsstate" name="showadsstate"> Show ads
</label>
</div>
<div class="checkbox">
<label>
<input type="checkbox" id="editpasswordprotectstate" name="passwordprotectstate"> Enable password protection
</label>
</div>
<div id="editpasswordentry" style="display: none;">
<div class="form-group">
<input type="password" class="form-control" id="editpassword" name="password" placeholder="Type a password..." required>
</div>
</div>
<input type="hidden" id="action" name="action" value="edit">
<input type="hidden" id="editid" name="id">
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<button type="button" id="edit" class="btn btn-primary">Edit</button>
</div>
</div>
</div>
</div>
<!-- Edit form end -->
<hr>
<div class="footer">
Indication <?php echo $version; ?> &copy; <a href="http://joshf.co.uk" target="_blank">Josh Fradley</a> <?php echo date("Y"); ?>. Themed by <a href="http://getbootstrap.com" target="_blank">Bootstrap</a>.
</div>
</div>
<script src="assets/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/bootstrap-notify/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    /* Search */
    $("#search").keyup(function() {
        $("#search-error").remove();
        var filter = $(this).val();
        var count = 0;
        $(".list-group .list-group-item").each(function() {
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
            } else {
                $(this).show();
                count++;
            }            
        });
        if (count === 0) {
            $(".list-group").prepend("<li class=\"list-group-item\" id=\"search-error\">No downloads found</li>");
        }
        document.title = "Indication (" + count + ")";
    });
    /* End */
    $("#editpasswordprotectstate").click(function() {
        if ($("#editpasswordprotectstate").prop("checked") == true) {
            $("#editpassword").prop("required", true);
            $("#editpasswordentry").show("fast");
        } else {
            $("#editpasswordentry").hide("fast");
            $("#editpassword").prop("required", false);
        }
    });
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
    /* Form Overrides */
    $("#addformmodal").on("keypress", function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            $("#add").trigger("click");
        }
    });
    $("#editformmodal").on("keypress", function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            $("#edit").trigger("click");
        }
    });
    /* End */
    /* Add */
    $("#launchaddmodal").click(function() {
        $("#addformmodal").modal();
    });
    $("#add").click(function() {
        var haserrors = false;
        if ($("#downloadid").val() == "") {
            if (!$(".form-group:eq(2)").hasClass("has-error")) {
                $(".form-group:eq(2)").addClass("has-error");
                $(".form-group:eq(2)").append("<span class=\"help-block\">ID cannot be empty</span>");
            }
            haserrors = true;
        }
        if ($("#url").val() == "") {
            if (!$(".form-group:eq(3)").hasClass("has-error")) {
                $(".form-group:eq(3)").addClass("has-error");
                $(".form-group:eq(3)").append("<span class=\"help-block\">A URL is required</span>");
            }
            haserrors = true;
        }
        if (haserrors == true) {
            return false;
        }
        $(".form-group:eq(2)").removeClass("has-error");
        $(".form-group:eq(3)").removeClass("has-error");
        $(".help-block").remove();
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: $("#addform").serialize(),
            error: function() {
                show_notification("danger", "warning-sign", "Ajax query failed!");
            },
            success: function() {
                show_notification("success", "ok", "Task added!", true);
                $("#addformmodal").modal("hide");
            }
        });
    });
    /* End */
    /* Edit */
    $("li").on("click", ".edit", function() {
        var id = $(this).data("id");
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "worker.php",
            data: "action=details&id="+ id +"",
            error: function() {
                show_notification("danger", "warning-sign", "Ajax query failed!");
            },
            success: function(data) {
                /* Stop auto checked */
                $("#editshowadsstate").prop("checked", false);
                $("#editname").val(data[0]);
                $("#editdownloadid").val(data[1]);
                $("#editurl").val(data[2]);
                $("#editcount").val(data[3]);
                if (data[4] == "1") {
                    $("#editshowadsstate").prop("checked", true);
                }
                if (data[5] == "1") {
                    $("#editpasswordprotectstate").prop("checked", true);
                    $("#editpassword").val(data[6]);
                    $("#editpasswordentry").show("fast");
                }
                $("#editid").val(id);
            }
        });
        $("#editformmodal").modal();
    });
    $("#edit").click(function() {
        var haserrors = false;
        if ($("#editdownloadid").val() == "") {
            if (!$(".form-group:eq(7)").hasClass("has-error")) {
                $(".form-group:eq(7)").addClass("has-error");
                $(".form-group:eq(7)").append("<span class=\"help-block\">ID cannot be empty</span>");
            }
            haserrors = true;
        }
        if ($("#editurl").val() == "") {
            if (!$(".form-group:eq(8)").hasClass("has-error")) {
                $(".form-group:eq(8)").addClass("has-error");
                $(".form-group:eq(8)").append("<span class=\"help-block\">A URL is required</span>");
            }
            haserrors = true;
        }
        if (haserrors == true) {
            return false;
        }
        $(".form-group:eq(7)").removeClass("has-error");
        $(".form-group:eq(8)").removeClass("has-error");
        $(".help-block").remove();
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: $("#editform").serialize(),
            error: function() {
                show_notification("danger", "warning-sign", "Ajax query failed!");
            },
            success: function() {
                show_notification("success", "ok", "Task edited!", true);
                $("#editformmodal").modal("hide");
            }
        });
    });
    /* End */
    /* Details */
    $("li").on("click", ".details", function() {
        var id = $(this).data("id");
        if ($("#detailsitem"+id).length) {
            $("#detailsitem"+id).hide("fast");
            setTimeout(function() {
                $("#detailsitem"+id).remove();
            }, 400);            
            return false;
        }
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "worker.php",
            data: "action=details&id="+ id +"",
            error: function() {
                show_notification("danger", "warning-sign", "Ajax query failed!");
            },
            success: function(data) {
                if (data[1] == "") {
                    data[1] = "<i>No details available</i>";
                }
                $("#"+id).append("<div id=\"detailsitem"+ id +"\" style=\"display: none;\"><p><dl><dt>Details</dt><dd>" + data[1] +  "</dd><dt>Due</dt><dd>" + data[5] +  "</dd><dt>Created</dt><dd>" + data[6] +  "</dd></dl></p></div>");
                $("#detailsitem"+id).show("fast");
            }
        });
    });
    /* End */
    /* Delete */
    $("li").on("click", ".delete", function() {
        var id = $(this).data("id");
        $.ajax({
            type: "POST",
            url: "worker.php",
            data: "action=delete&id="+ id +"",
            error: function() {
                show_notification("danger", "warning-sign", "Ajax query failed!");
            },
            success: function() {
                show_notification("success", "ok", "Task deleted!", true);
            }
        });
    });
    /* End */
    /* Update Title */
    document.title = "Indication (<?php echo $numberofitems; ?>)";
    /* End */
});
</script>
</body>
</html>
