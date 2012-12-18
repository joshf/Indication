<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

$version = "4.0beta";
$codename = "PoignantPony";
$rev = "400";

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

if (isset($_GET["nojs"])) {
    die("<html><head><title>SHTracker: Error</title><link rel=\"stylesheet\" type=\"text/css\" href=\"../resources/bootstrap/css/bootstrap.css\" /></head><body><p style=\"padding-left: 5px;\">Please enable JavaScript to use SHTracker. For instructions on how to do this, see <a href=\"http://www.activatejavascript.org\">here</a>.</p></body></html>");
}

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

?>
<html> 
<head>
<title>SHTracker: Admin Home</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<link href="../resources/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../resources/datatables/DT_bootstrap.css" rel="stylesheet">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.js"></script>
<script type="text/javascript" src="../resources/datatables/DT_bootstrap.js"></script>
<style>
    html, body {
        padding-top: 30px;
        height: 100%;
    }
    #wrap {
        min-height: 100%;
        height: auto !important;
        height: 100%;
        margin: 0 auto -60px;
    }
    #push,
    #footer {
        height: 60px;
    }
    #footer {
        background-color: #f5f5f5;
    }
    @media (max-width: 767px) {
    #footer {
        margin-left: -20px;
        margin-right: -20px;
        padding-left: 20px;
        padding-right: 20px;
        }
    }
    .container .credit {
        margin: 20px 0;
    } 
</style>
<link href="../resources/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
</head>
<!--[if IE]>
<p>Please use a browser than conforms to web standards and can actually renders webpages properly. I suggest Firefox or Chrome.</p>
<![endif]-->
<noscript><meta http-equiv="refresh" content="0; url=index.php?nojs=true"></noscript>
<script type="text/javascript">
$(document).ready(function() {
    /* Table selection */
    is_selected = false;
    $("#downloads input[name=id]").click(function() {
        id = $("#downloads input[name=id]:checked").val();
        is_selected = true;
    });
    /* End */
    /* Datatables */
    $("#downloads").dataTable({
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap"
    });
    $.extend($.fn.dataTableExt.oStdClasses, {
        "sSortable": "header"
    });
    /* End */
    /* Edit */
    $("#dogotoeditpage").click(function() {
        if (!is_selected) {
            alert("No download selected!");
        } else {
            window.location = "edit.php?id="+ id +"";
        }
    });
    /* End */
    /* Delete */
    $("#showdelete").click(function() {
        if (!is_selected) {
            alert("No download selected!");
        } else {
            deleteconfirm=confirm("Delete "+ name +"?")
            if (deleteconfirm==true) {
                $.ajax({  
                    type: "POST",  
                    url: "actions/delete.php",  
                    data: "id="+ id +"",
                    error: function() {  
                        alert("Ajax query failed!");
                    },
                    success: function() {  
                        alert("Download "+ name +" deleted!");
                        window.location.reload();          
                    }	
                });
            } else {
                return false;
            }
        } 
    });
    /* End */
    /* Tracking Link */
    $("#showtrackinglink").click(function() {
        if (!is_selected) {
            alert("No download selected!");
        } else {
            prompt("Tracking link for "+ name +". Press Ctrl/Cmd C to copy to the clipboard:", "<? echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
        } 
    });
    /* End */
    /* Logout */
    $("#showlogout").click(function() {
        logoutconfirm=confirm("<? echo ADMIN_USER; ?>, are you sure you wish to logout?")
        if (logoutconfirm==true) {
            window.location.replace("logout.php");
        } else {
            return false;
        }
    });
    /* End */
});
</script>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
<div class="navbar-inner">
<div class="container">
<a class="btw btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</a>
<a class="brand" href="#">SHTracker</a>
<div class="nav-collapse collapse">
<ul class="nav">
<li class="active"><a href="index.php">Downloads</a></li>
<li><a href="add.php">Add</a></li>
<li><a href="settings.php">Settings</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</div>
</div>
<div id="wrap">
<div class="container">
<div class="page-header">
<h1>Downloads for <? echo WEBSITE; ?></h1>
</div>
<?php

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("<h1>SHTracker: Error</h1><p>Could not connect to database: " . mysql_error() . ". Check your database settings are correct.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("<h1>SHTracker: Error</h1><p>Could not connect to database: " . mysql_error() . ". Check your database settings are correct.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

$getdownloads = mysql_query("SELECT * FROM Data");

echo "<p><table id=\"downloads\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"table table-striped table-bordered\">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>URL</th>
<th>Count</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td><input name=\"id\" type=\"radio\" value=\"" . $row["id"] . "\" /></td>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table></p>";

//Update checking
$remoteversion = file_get_contents("https://raw.github.com/joshf/SHTracker/master/version.txt");
if (preg_match("/^[0-9.-]{1,}$/", $remoteversion)) {
    if ($version < $remoteversion) {
        echo "<p><div class=\"alert alert-block\"><h4>Update:</h4> An update to SHTracker is available! Version $remoteversion has been released (you have $version). To see what changes are included see the <a href=\"https://github.com/joshf/SHTracker/compare/$version...$remoteversion\" target=\"_blank\">changelog</a>. Click <a href=\"https://github.com/joshf/SHTracker/wiki/Updating-SHTracker\" target=\"_blank\">here</a> for information on how to update.</div></p>";
    }
}

?>
<p><button class="btn" id="dogotoeditpage">Edit</button>
<button class="btn" id="showdelete">Delete</button>
<button class="btn" id="showtrackinglink">Show Tracking Link</button></p>
<p><div class="alert alert-info">   
<strong>Info:</strong> To edit, delete or show the tracking link for a ID please select the radio button next to it.  
</div></p>
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
$resultnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<p><h4>Number of Downloads: </h4>" . $resultnumberofdownloads["COUNT(id)"] . "</p>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM Data");
$resulttotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
echo "<p><h4>Total Downloads: </h4>" . $resulttotalnumberofdownloads["SUM(count)"] . "</p>";

mysql_close($con);

?>
<div id="push"></div>
</div>
<div id="footer">
<div class="container">
<p class="muted credit">SHTracker <? echo $version; ?> (<? echo $rev; ?>) "<? echo $codename; ?>" Copyright <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <? echo date("Y"); ?>. Uses Twitter Bootstrap.</p>
</div>
</div>
</body>
</html>