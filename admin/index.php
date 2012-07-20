<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

$version = "3.1";
$codename = "KindheartedKoala";

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
<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.1/css/jquery.dataTables_themeroller.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.1/jquery.dataTables.js"></script>
</head>
<body>
<!--[if IE]>
<p>Please use a browser than conforms to web standards and can actually renders webpages properly. I suggest Firefox or Chrome.</p>
<![endif]-->
<noscript><p>Your browser does not support JavaScript or it is disabled, nearly all functions will be broken! Please upgrade your browser or enable JavaScript.</p></noscript>
<script type="text/javascript">
$(document).ready(function() {
    /* jQuery UI buttons */
    $("#dogotoaddpage").button();
    $("#dogotoeditpage").button();
    $("#showdelete").button();
    $("#showtrackinglink").button();
    $("#dorefresh").button();
    $("#dogotosettingspage").button();
    $("#showlogout").button();
    /* End */
    /* DataTables */
    $("#downloads").dataTable({
        "aoColumns": [{ 
            "bSortable": false 
        },
            null,
            null,
            null,
            null
        ], 
        "bJQueryUI": true,
        "sPaginationType": "full_numbers"
    });
    /* End */
    /* Edit */
    $("#dogotoeditpage").click(function() {
        if (!$("input:radio[name=id]:checked").val()) {
            alert("No ID Selected!"); 
        } else {
            var id = $("input:radio[name=id]:checked").val();
            window.location = "edit.php?id="+ id +"";
        }
    });
    /* End */
    /* Delete */
    $("#showdelete").click(function() {
        if (!$("input:radio[name=id]:checked").val()) {
            alert("No ID Selected!"); 
        } else {
            deleteconfirm=confirm("Delete download?")
            if (deleteconfirm==true) {
                var id = $("input:radio[name=id]:checked").val();
                $.ajax({  
                    type: "POST",  
                    url: "actions/delete.php",  
                    data: "id="+ id +"",
                    success: function() {  
                        alert("Download has been deleted!");
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
        if (!$("input:radio[name=id]:checked").val()) {
            alert("No ID selected!");
        } else {
            var id = $("input:radio[name=id]:checked").val();
            prompt("Tracking link for selected download. Press Ctrl/Cmd C to copy to the clipboard:", "<? echo PATH_TO_SCRIPT; ?>/get.php?id="+ id +"");
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
echo "</tbody></table></p>";

?>
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

//Update checking
$remoteversion = file_get_contents("https://raw.github.com/joshf/SHTracker/master/version.txt");
if ($version < $remoteversion) {
    echo "<p class=\"noticebad\">An update to SHTracker is available! Version $remoteversion has been released (you have $version). Click <a href=\"https://github.com/joshf/shtracker/zipball/$remoteversion\" target=\"_blank\">here</a> to update.</p>";
}

?>
<hr />
<p><button id="dorefresh">Refresh</button>
<button id="dogotosettingspage">Settings</button>
<button id="showlogout">Logout</button></p>
<small>SHTracker <? echo $version; ?> "<? echo $codename; ?>" Copyright <a href="http://sidhosting.co.uk" target="_blank">Josh Fradley</a> <? echo date("Y"); ?></small>
</body>
</html>