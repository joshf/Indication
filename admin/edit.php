<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: login.php");
    exit; 
}

if (!isset($_POST["id"])) {
    header("Location: ../admin");
}

?>
<html> 
<head>
<title>SHTracker: Editing Download</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
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
            return this.optional(element) || /^[a-zA-Z0-9()._-\s]+$/.test(value);
        },
        "Illegal character. Only spaces, points, underscores or dashes are allowed."
    );
    $.validator.addMethod(
        "legalid",
        function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9._-]+$/.test(value);
        },
        "Illegal character. Only points, underscores or dashes are allowed."
    ); 
    $("#editform").validate({
        rules: {
            name: {
                required: true,
                legalname: true
            },
            id: {
                required: true,
                legalid: true
            },
            url: {
                required: true,
                url: true
            },
            count: {
                digits: true
            },
            password: {
                required: true
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

$idtoedit = mysql_real_escape_string($_POST["id"]);

$getnameofdownload = mysql_query("SELECT name FROM Data WHERE id = \"$idtoedit\"");
$resultnameofdownload = mysql_fetch_assoc($getnameofdownload);

?>
<h1>SHTracker: Editing Download <? echo $resultnameofdownload["name"]; ?></h1>
<p>Please edit any values you wish.</p>
<form action="actions/edit.php" method="post" id="editform">
<?php

$getidinfo = mysql_query("SELECT * FROM Data WHERE id = \"$idtoedit\"");
while($row = mysql_fetch_assoc($getidinfo)) {
    echo "<p>Name: <input type=\"text\" size=\"50\" name=\"name\" value=\"" . $row["name"] . "\" /></p>";
    echo "<p>ID: <input type=\"text\" size=\"50\" name=\"id\" value=\"" . $row["id"] . "\" /></p>";
    echo "<p>URL: <input type=\"text\" size=\"50\" name=\"url\" value=\"" . $row["url"] . "\" /></p>";
    echo "<p>Count: <input type=\"text\" size=\"50\" name=\"count\" value=\"" . $row["count"] . "\" /></p>";
}

//Check if download is protected
$checkprotected = mysql_query("SELECT protect, password FROM Data WHERE id = \"$idtoedit\"");
$checkprotectedresult = mysql_fetch_assoc($checkprotected); 
if ($checkprotectedresult["protect"] == "true") { 
    echo "<p>Enable password protection? <input type=\"checkbox\" name=\"passwordprotectstate\" checked=\"yes\" /></p>";
} else {
    echo "<p>Enable password protection? <input type=\"checkbox\" name=\"passwordprotectstate\" /></p>";
}

mysql_close($con);

?>
<div id="passwordentry" style="display: none">
    <p>Please enter a password: <input type="password" name="password" /></p>
</div>
<input type="hidden" name="idtoedit" value="<? echo $idtoedit; ?>" />
<p><input type="submit" name="command" value="Edit" /></p>
</form>
<hr />
<p><a href="../admin">&larr; Go Back</a></p>
</body>
</html>