<?php

// SHTracker, Copyright Josh Fradley (http://github.com/joshf/SHTracker)

//Security check, check if config exists
if (file_exists("../config.php")) {
    header("Location: ../admin");
    exit;
}

$version = "4.0beta";

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>SHTracker: Installer</title>
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../resources/bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet">
<style>
    body {
        padding-top: 60px;
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
    label.error {
        color: #ff0000;
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
<a class="brand" href="#">SHTracker</a>
</div>
</div>
</div>
<!-- Nav end -->
<!-- Content start -->
<div class="container">
<div class="page-header">
<h1>Installer</h1>
</div>
<?php

//Get path to script
$currenturl = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$pathtoscriptwithslash = "http://" . substr($currenturl, 0, strpos($currenturl, "installer"));
$pathtoscript = rtrim($pathtoscriptwithslash, "/");

?>		
<form action="install.php" method="post" id="installform" >
<h4>Database Settings:</h4>
Host: <input type="text" name="dbhost" value="localhost" /><br />
User: <input type="text" name="dbuser" /><br />
Password: <input type="password" name="dbpassword" /><br />
Name: <input type="text" name="dbname" /><br />
<h4>Admin Details:</h4>
User: <input type="text" name="adminuser" /><br />
Email: <input type="text" name="adminemail" /><br />
Password: <input type="password" name="adminpassword" id="adminpassword" /><br />
Confirm Password: <input type="password" name="adminpasswordconfirm" /><br />
<h4>Other Settings:</h4>
Website Name: <input type="text" name="website" /><br />
Path to Script: <input type="text" name="pathtoscript" value="<? echo $pathtoscript; ?>" /><br />
<input type="hidden" name="doinstall" />
<p><input type="submit" class="btn btn-primary" value="Install" /></p>
</form>
</div>
<!-- Content end -->
<!-- Footer start -->	
<div id="footer">
<div class="container">
<p class="muted credit">SHTracker <? echo $version; ?> Copyright <a href="http://github.com/joshf" target="_blank">Josh Fradley</a> <? echo date("Y"); ?>. Uses Twitter Bootstrap.</p>
</div>
</div>
<!-- Footer end -->
<!-- Javascript start -->	
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function() {
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
<!-- Javascript end -->
</body>
</html>