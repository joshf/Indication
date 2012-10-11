<?php

// SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

//Security check, check if config exists
if (file_exists("../config.php")) {
    header("Location: ../admin");
    exit;
}

$version = "3.4.2";

?>
<html>
<head>
<title>SHTracker: Installer</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex, nofollow">
<link rel="stylesheet" type="text/css" href="../style.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
</head>
<body>
<?php

//Get path to script
$currenturl = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$pathtoscriptwithslash = "http://" . substr($currenturl, 0, strpos($currenturl, "installer"));
$pathtoscript = rtrim($pathtoscriptwithslash, "/");

?>
<script type="text/javascript">
$(document).ready(function() {
    $.ajax({  
        type: "POST",  
        url: "http://sidhosting.co.uk/misc/installs/detect.php",  
        data: "product=shtracker&url=<? echo $pathtoscript; ?>&version=<? echo $version; ?>",
    });
    $.validator.addMethod(
        "legalurl",
        function(value, element) { 
            return this.optional(element) || /^[a-zA-Z0-9.?=:#_\-/\-]+$/.test(value);
        },
        "Please enter a valid URL."
    ); 
    $("#installform").validate({
        rules: {
            adminpassword: {
                required: true,
            },
            dbhost: {
                required: true,
            },
            dbuser: {
                required: true,
            },
            dbpassword: {
                required: true,
            },
            dbname: {
                required: true,
            },
            adminuser: {
                required: true,
            },
            adminemail: {
                required: true,
                email: true
            },
            adminpassword: {
                required: true,
                minlength: 6
            },
            adminpasswordconfirm: {
                equalTo: "#adminpassword",
            },
            website: {
                required: true,
            },
            pathtoscript: {
                required: true,
                legalurl: true
            }
        }
    });
});
</script>
<h1>SHTracker: Installer</h1>
<p><i>All fields are required!</i></p>
<form action="install.php" method="post" id="installform" >
<p><b>Database Settings:</b></p>
Host: <input type="text" name="dbhost" value="localhost" /><br />
User: <input type="text" name="dbuser" /><br />
Password: <input type="password" name="dbpassword" /><br />
Name: <input type="text" name="dbname" /><br />
<p><b>Admin Details:</b></p>
User: <input type="text" name="adminuser" /><br />
Email: <input type="text" name="adminemail" /><br />
Password: <input type="password" name="adminpassword" id="adminpassword" /><br />
Confirm Password: <input type="password" name="adminpasswordconfirm" /><br />
<p><b>Other Settings:</b></p>
Website Name: <input type="text" name="website" /><br />
Path to Script: <input type="text" name="pathtoscript" value="<? echo $pathtoscript; ?>" /><br />
<input type="hidden" name="doinstall" />
<p><input type="submit" value="Install" /></p>
</form>
<small>SHTracker <? echo $version; ?> Copyright <a href="http://sidhosting.co.uk">Josh Fradley</a> <? echo date("Y"); ?></small>
</body>
</html>