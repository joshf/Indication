<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

$version = "3.4.3";
$codename = "ObsceneOstrich";
$rev = "350";

if (!file_exists("../config.php")) {
    header("Location: ../installer");
}

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

if (isset($_GET["nojs"])) {
    die("<html><head><title>SHTracker: Error</title><link rel=\"stylesheet\" type=\"text/css\" href=\"../style.css\" /></head><body><p>Please enable JavaScript to use SHTracker. For instructions on how to do this, see <a href=\"http://www.activatejavascript.org\">here</a>.</p></body></html>");
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
<link rel="stylesheet" type="text/css" href="../style.css" />
<link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/<? echo JQUERY_THEME; ?>/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.js"></script>
</head>
<body>
<!--[if IE]>
<p>Please use a browser than conforms to web standards and can actually renders webpages properly. I suggest Firefox or Chrome.</p>
<![endif]-->
<noscript><meta http-equiv="refresh" content="0; url=index.php?nojs=true"></noscript>
<script type="text/javascript">
$(document).ready(function() {
    /* jQuery UI buttons */
    $("#adminfunctions").buttonset();
    $("#userfunctions").buttonset();
    /* End */
    /* DataTables */
    $("#downloads").dataTable({
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
    });
    /* End */
    /* Table selection */
    var is_selected = 0;
    $("#downloads tbody").delegate("tr", "click", function() {
        if (is_selected) {
            $("td:first", is_selected).parent().children().each(function() {
                $(this).removeClass("highlight-row");
            });
        }
        is_selected = this;
        $("td:first", this).parent().children().each(function() {
            $(this).addClass("highlight-row");
        });
        name = $("td:eq(0)", this).text();
        id = $("td:eq(1)", this).text();
    });
    /* End */
    /* Add */
    $("#dogotoaddpage").click(function() {
        window.location = "add.php";
    });
    /* End */
    /* Edit */
    $("#dogotoeditpage").click(function() {
        if (!is_selected) {
            $("#noidselectedmessage").show("fast");
            $("#adminfunctions").hide("fast");
            setTimeout(function(){
                $("#noidselectedmessage").hide("fast");
                $("#adminfunctions").show("fast");
            }, 3000); 
        } else {
            window.location = "edit.php?id="+ id +"";
        }
    });
    /* End */
    /* Delete */
    $("#showdelete").click(function() {
        if (!is_selected) {
            $("#noidselectedmessage").show("fast");
            $("#adminfunctions").hide("fast");
            setTimeout(function(){
                $("#noidselectedmessage").hide("fast");
                $("#adminfunctions").show("fast");
            }, 3000); 
        } else {
            deleteconfirm=confirm("Delete "+ name +"?")
            if (deleteconfirm==true) {
                $("#loadingmessage").ajaxStart(function() {
                    $(this).show();
                });
                $("#loadingmessage").ajaxStop(function() {
                    $(this).hide();
                });
                $.ajax({  
                    type: "POST",  
                    url: "actions/delete.php",  
                    data: "id="+ id +"",
                    error: function() {  
                        $("#failuremessage").show("fast");
                        $("#adminfunctions").hide("fast");
                        setTimeout(function(){
                            $("#failuremessage").hide("fast");
                            $("#adminfunctions").show("fast");
                        }, 3000); 
                    },
                    success: function() {  
                        $("#downloaddeletedmessage").show("fast");
                        $("#adminfunctions").hide("fast");
                        setTimeout(function(){
                            $("#downloaddeletedmessage").hide("fast");
                            $("#adminfunctions").show("fast");
                            window.location.reload()
                        }, 3000);               
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
            $("#noidselectedmessage").show("fast");
            $("#adminfunctions").hide("fast");
            setTimeout(function(){
                $("#noidselectedmessage").hide("fast");
                $("#adminfunctions").show("fast");
            }, 3000);
        } else {
            prompt("Tracking link for "+ name +". Press Ctrl/Cmd C to copy to the clipboard:", "<? echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
        } 
    });
    /* End */
    /* Refresh */
    $("#dorefresh").click(function() {
        window.location.reload();
    });
    /* End */
    /* Settings */
    $("#dogotosettingspage").click(function() {
        window.location = "settings.php";
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

echo "<h1>SHTracker: Downloads for " . WEBSITE . "</h1>
<p><table id=\"downloads\">
<thead>
<tr>
<th>Name</th>
<th>ID</th>
<th>URL</th>
<th>Count</th>
</tr></thead><tbody>";

while($row = mysql_fetch_assoc($getdownloads)) {
    echo "<tr>";
    echo "<td>" . $row["name"] . "</td>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["url"] . "</td>";
    echo "<td>" . $row["count"] . "</td>";
    echo "</tr>";
}
echo "</tbody></table></p>";

//Update checking
$remoteversion = file_get_contents("https://raw.github.com/joshf/SHTracker/master/version.txt");
if (preg_match("/^[0-9.-]{1,}$/", $remoteversion)) {
    if ($version < $remoteversion) {
        echo "<p><div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em;\"><p><span class=\"ui-icon ui-icon-refresh\" style=\"float: left; margin-right: .3em;\"></span><b>Info:</b> An update to SHTracker is available! Version $remoteversion has been released (you have $version). To see what changes are included see the <a href=\"https://github.com/joshf/SHTracker/compare/$version...$remoteversion\" target=\"_blank\">changelog</a>. Click <a href=\"http://sidhosting.co.uk/misc/shtracker_update.php?v=$version\" target=\"_blank\">here</a> to update.</p></div></p>";
    }
}

?>
<div id="failuremessage" class="ui-state-error ui-corner-all" style="display: none; padding: 0 .7em;">
<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b>Error:</b> Ajax query failed!</p>
</div>
<div id="loadingmessage" class="ui-state-highlight ui-corner-all" style="display: none; padding: 0 .7em;">
<p><span class="ui-icon ui-icon-refresh" style="float: left; margin-right: .3em;"></span><b>Info:</b> Working...</p>
</div>
<div id="noidselectedmessage" class="ui-state-error ui-corner-all" style="display: none; padding: 0 .7em;">
<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><b>Error:</b> No ID selected!</p>
</div>
<div id="downloaddeletedmessage" class="ui-state-highlight ui-corner-all" style="display: none; padding: 0 .7em;">
<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span><b>Info:</b> Download deleted!</p>
</div>
<div id="adminfunctions">
<p><button id="dogotoaddpage">Add</button>
<button id="dogotoeditpage">Edit</button>
<button id="showdelete">Delete</button>
<button id="showtrackinglink">Show Tracking Link</button></p>
</div>
<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>To edit, delete or show the tracking link for a ID please select the radio button next to it.</p>
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
$resultnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<p><b>Number of Downloads: </b>" . $resultnumberofdownloads["COUNT(id)"] . "</p>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM Data");
$resulttotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
echo "<p><b>Total Downloads: </b>" . $resulttotalnumberofdownloads["SUM(count)"] . "</p>";

mysql_close($con);

?>
<div id="userfunctions"
<p><button id="dorefresh">Refresh</button>
<button id="dogotosettingspage">Settings</button>
<button id="showlogout">Logout</button></p>
</div>
<small>SHTracker <? echo $version; ?> (<? echo $rev; ?>) "<? echo $codename; ?>" Copyright <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <? echo date("Y"); ?></small>
</body>
</html>