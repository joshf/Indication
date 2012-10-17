<?php

//SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

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
<title>SHTracker: Add A Download/Link</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<script type="text/javascript">
$(document).ready(function() {
    $("#passwordprotectstate").click(function() {
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
    $("#addform").validate({
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
<h1>SHTracker: Add A Download/Link</h1>
<form action="actions/add.php" method="post" id="addform">
<p>Name: <input type="text" size="50" name="downloadname" /></p>
<p>ID: <input type="text" size="50" name="id" /></p>
<p>URL: <input type="text" size="50" name="url" /></p>
<p>Count: <input type="text" size="50" name="count" value="0" /></p>
<p>Enable password protection? <input type="checkbox" id="passwordprotectstate" name="passwordprotectstate" /></p>
<div id="passwordentry" style="display: none">
<p><i>Please enter a password:</i> <input type="password" name="password" /></p>
</div>
<p>Show Ads? <input type="checkbox" name="showadsstate" /></p>
<input type="submit" value="Add" />
</form>
<hr />
<p><a href="../admin">&larr; Go Back</a></p>
</body>
</html>