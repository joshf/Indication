<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

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

?>
<html> 
<head>
<title>SHTracker: Admin Home</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/themes/flick/jquery-ui.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
</head>
<body>
<!--[if IE]>
<p>Please use a browser than conforms to web standards and can actually renders webpages properly. I suggest Firefox or Chrome.</p>
<![endif]-->
<noscript><p>Your browser does not support JavaScript or it is disabled, nearly all functions will be broken! Please upgrade your browser or enable JavaScript.</p></noscript>
<script type="text/javascript">
$(document).ready(function() {
    /* Edit */
    $("#dogotoeditpage").click(function() {
        if (!$("input[name=id]:checked").val()) {
            $("#noidselectedmessage").show("fast");
            setTimeout(function(){
                $("#noidselectedmessage").hide("fast");
            },3000)
            return false;
        } else {
            $("#noidselectedmessage").hide("fast");
            var id = $("input:radio[name=id]:checked").val();
            $("#edit").append("<form name=\"edit\" action=\"edit.php\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\""+ id +"\" /><form>");
            document.forms["edit"].submit();        
        }
    });
    /* End */
    /* Delete Dialog */
    $("#delete").dialog({
        autoOpen: false,
        resizable: false,
        modal: false,
        height: 200,
        width: 400,
        buttons: {
            "Delete": function() {
                var id = $("input:radio[name=id]:checked").val();
                $.ajax({  
                    type: "POST",  
                    url: "actions/delete.php",  
                    data: "id="+ id +"",
                    success: function() {  
                        $("#deletedone").show("fast");
                        setTimeout(function(){
                            $("#deletedone").hide("fast");
                            window.location.reload();
                        },3000)
                        return false;
                    }	
                });
                $(this).dialog("close");
            },
            "Cancel": function() {
                $(this).dialog("close");
            }
        }
    });
    $("#showdelete").click(function() {
        if (!$("input[name=id]:checked").val()) {
            $("#noidselectedmessage").show("fast");
            setTimeout(function(){
                $("#noidselectedmessage").hide("fast");
            },3000)
            return false;
        } else {
            $("#noidselectedmessage").hide("fast");
            $("#delete").dialog("open");
            return false;
        } 
    });
    /* End */
    /* Tracking Link */
    $("#showtrackinglink").click(function() {
        if (!$("input[name=id]:checked").val()) {
            $("#noidselectedmessage").show("fast");
            setTimeout(function(){
                $("#noidselectedmessage").hide("fast");
            },3000)
            return false;
        } else {
            $("#noidselectedmessage").hide("fast");
            var id = $("input:radio[name=id]:checked").val();
            $("<div title=\"SHTracker: Show Tracking Link\"><p>Tracking link for download. Press Ctrl/Cmd C to copy to the clipboard:<input type=\"text\" size=\"70\" value=\"<? echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"\"</p></div>").dialog({
                autoOpen: true,
                resizable: false,
                modal: false,
                height: 200,
                width: 680,
                buttons: {
                    "Close": function() {
                        $(this).dialog("close");
                    }
                }
            });
        } 
    });
    /* End */
    /* Logout dialog */
    $("#logout").dialog({
        autoOpen: false,
        resizable: false,
        modal: true,
        height: 170,
        width: 400,
        buttons: {
            "Logout": function() {
                window.location.replace("logout.php");
            },
            "Cancel": function() {
                $(this).dialog("close");
            }
        }
    });
    $("#showlogout").click(function() {
        $("#logout").dialog("open");
        return false;
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
    /* Add */
    $("#dogotoaddpage").click(function() {
        window.location = "add.php";
    });
    /* End */
    /* Hide DIVS */
    $("#noidselectedmessage").click(function() {
        $("#noidselectedmessage").hide("fast");
    });
    $("#deletedone").click(function() {
        $("#deletedone").hide("fast");
        window.location.reload();
    });
    /* End */
});
</script>
<?php

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

//Pagination fail safes
if (isset($_GET["page"])) {
    $page = mysql_real_escape_string($_GET["page"]);
    if (empty($page)) {
        $page = 1;
    }
    if (!preg_match("/^[0-9]{1,}$/", $page)) {
        die("<h1>SHTracker: Error</h1><p>Page does not exist...</p><hr /><p><a href=\"../admin\">&larr; Go Back</a></p></body></html>"); 
    }
} else {
    $page = 1;
}
$startfrom = ($page-1) * 20;

$getdownloads = mysql_query("SELECT * FROM Data ORDER BY name ASC LIMIT $startfrom, 20");

echo "<h1>SHTracker: Downloads for " . WEBSITE . "</h1>
<table>
<thead>
<tr>
<th></th>
<th>Name</th>
<th>ID</th>
<th>URL</th>
<th>Count</th>
</tr></thead><tbody>";
    
$isalt = false;

while($row = mysql_fetch_assoc($getdownloads)) {
    if($isalt == false){
        echo "<tr>";
        echo "<td><input type=\"radio\" name=\"id\" value=\"" . $row["id"] . "\" /></td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["url"] . "</td>";
        echo "<td>" . $row["count"] . "</td>";
        echo "</tr>";
        $isalt = true;
    } else {
        echo "<tr class=\"alt\">";
        echo "<td><input type=\"radio\" name=\"id\" value=\"" . $row["id"] . "\" /></td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["url"] . "</td>";
        echo "<td>" . $row["count"] . "</td>";
        echo "</tr>";
        $isalt = false;
    }
}
echo "</tbody></table>";

//Pagination
$getdatacount = mysql_query("SELECT COUNT(*) FROM Data");
$resultgetdatacount = mysql_fetch_assoc($getdatacount); 
$totalpages = ceil($resultgetdatacount["COUNT(*)"] / 20);
if ($resultgetdatacount["COUNT(*)"] > "20") {
    echo "<p><i>Go To Page: </i>";
    for ($i=1; $i <= $totalpages; $i++) { 
        echo " <a href=\"index.php?page=".$i."\">".$i."</a> "; 
    } 
    echo "</p>";
} else {
    echo "<br />";
}

?>
<div id="edit" style="display: none">
    <p>Loading...</p>
</div>
<div id="delete" style="display: none" title="SHTracker: Delete Download">
    <p>Delete download?</p>
</div>
<div id="deletedone" style="display: none">
    <p class="noticegood">Download deleted!</p>
</div>
<div id="noidselectedmessage" style="display: none">
    <p class="noticebad">No ID selected!</p>
</div>
<div id="logout" style="display: none" title="SHTracker: Logout">
    <p><? echo ADMIN_USER; ?>, are you sure you wish to logout?</p>
</div>
<button id="dogotoaddpage">Add</button>
<button id="dogotoeditpage">Edit</button>
<button id="showdelete">Delete</button>
<button id="showtrackinglink">Show Tracking Link</button>
<p><i>To edit, delete or show the tracking link for a ID please select the radio button next to it.</i></p>
<?php

$getnumberofdownloads = mysql_query("SELECT COUNT(id) FROM Data");
$resultnumberofdownloads = mysql_fetch_assoc($getnumberofdownloads);
echo "<p><b>Number of downloads: </b>" . $resultnumberofdownloads["COUNT(id)"] . "</p>";

$gettotalnumberofdownloads = mysql_query("SELECT SUM(count) FROM Data");
$resulttotalnumberofdownloads = mysql_fetch_assoc($gettotalnumberofdownloads);
echo "<p><b>Total downloads: </b>" . $resulttotalnumberofdownloads["SUM(count)"] . "</p>";

mysql_close($con);

?>
<hr />
<p><button id="dorefresh">Refresh</button>
<button id="dogotosettingspage">Settings</button>
<button id="showlogout">Logout</button></p>
<small>SHTracker 3.0 "JoyfulJaguar" Copyright <a href="http://sidhosting.co.uk">Josh Fradley</a> <? echo date("Y"); ?></small>
<p><small><a href="http://sidhosting.co.uk/misc/donate.html">Donate</a></p>
</body>
</html>