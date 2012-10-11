<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

if (!isset($_GET["id"])) {
    header("Location: ../admin");
}

?>
<html> 
<head>
<title>SHTracker: Editing Download/Link</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="../style.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
</head>
<body>
<script type="text/javascript">
$(document).ready(function() {
    $("input:checkbox[name=passwordprotectstate]").click(function() {
        $("#passwordentry").toggle(this.checked);
    });
    $.validator.addMethod(
        "legalname",
        function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9()._\-\s]+$/.test(value);
        },
        "Illegal character. Only points, spaces, underscores or dashes are allowed."
    );
    $.validator.addMethod(
        "legalid",
        function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9._-]+$/.test(value);
        },
        "Illegal character. Only points, underscores or dashes are allowed."
    );
    $.validator.addMethod(
        "legalurl",
        function(value, element) { 
            return this.optional(element) || /^[a-zA-Z0-9.?=:#_\-/\-]+$/.test(value);
        },
        "Illegal character. Please use a valid URL or directory path."
    ); 
    $("#editform").validate({
        rules: {
            downloadname: {
                required: true,
                legalname: true
            },
            id: {
                required: true,
                legalid: true
            },
            url: {
                required: true,
                legalurl: true
            },
            count: {
                digits: true
            },
            password: {
                required: true,
                minlength: 6
            },
        }
    });
});
</script>
<?php

//Connect to database
require_once("../config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$idtoedit = mysql_real_escape_string($_GET["id"]);

//Check if ID exists
$doesidexist = mysql_query("SELECT id FROM Data WHERE id = \"$idtoedit\"");
$doesidexistresult = mysql_fetch_assoc($doesidexist); 
if ($doesidexistresult == 0) { 
    die("<h1>SHTracker: Error</h1><p>ID does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtoedit\"");
$resultnameofdownload = mysql_fetch_assoc($getnameofdownload);

?>
<h1>SHTracker: Editing Download/Link <? echo $resultnameofdownload["name"]; ?></h1>
<p>Please edit any values you wish.</p>
<form action="actions/edit.php" method="post" id="editform">
<?php

$getidinfo = mysql_query("SELECT * FROM Data WHERE id = \"$idtoedit\"");
while($row = mysql_fetch_assoc($getidinfo)) {
    echo "<p>Name: <input type=\"text\" size=\"50\" name=\"downloadname\" value=\"" . $row["name"] . "\" /></p>";
    echo "<p>ID: <input type=\"text\" size=\"50\" name=\"id\" value=\"" . $row["id"] . "\" /></p>";
    echo "<p>URL: <input type=\"text\" size=\"50\" name=\"url\" value=\"" . $row["url"] . "\" /></p>";
    echo "<p>Count: <input type=\"text\" size=\"50\" name=\"count\" value=\"" . $row["count"] . "\" /></p>";
}

//Check if download is protected
$checkifprotected = mysql_query("SELECT protect FROM Data WHERE id = \"$idtoedit\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected); 
if ($checkifprotectedresult["protect"] == "1") { 
    echo "<p>Enable password protection? <input type=\"checkbox\" name=\"passwordprotectstate\" checked=\"yes\" /></p>";
} else {
    echo "<p>Enable password protection? <input type=\"checkbox\" name=\"passwordprotectstate\" /></p>";
}

?>
<div id="passwordentry" style="display: none">
<p><i>Please enter a password:</i> <input type="password" name="password" /></p>
</div>
<?

//Check if we should show ads
$checkifadsshow = mysql_query("SELECT showads FROM Data WHERE id = \"$idtoedit\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow); 
if ($checkifadsshowresult["showads"] == "1") { 
    echo "<p>Show Ads? <input type=\"checkbox\" name=\"showadsstate\" checked=\"yes\" /></p>";
} else {
    echo "<p>Show Ads? <input type=\"checkbox\" name=\"showadsstate\" /></p>";
}

mysql_close($con);

?>
<input type="hidden" name="idtoedit" value="<? echo $idtoedit; ?>" />
<p><input type="submit" value="Update" /></p>
</form>
<hr />
<p><a href="../admin">&larr; Go Back</a></p>
</body>
</html>