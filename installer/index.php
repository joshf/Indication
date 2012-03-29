<!-- SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker) -->
<html>
<head>
<title>SHTracker: Installer</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
</head>
<body>
<?php

//Security check
if (file_exists("../config.php")) {
    die("<h1>SHTracker: Error</h1><p>SHTracker has already been installed! If you wish to reinstall SHTracker, please delete config.php from your server.</p><hr /><p><a href=\"../admin\">&larr; Go Back</a></p></body></html>"); 
}

?>
<script type="text/javascript">
$(document).ready(function(){
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
            adminpassword: {
                required: true,
                minlength: 6
            },
            website: {
                required: true,
            },
            pathtoscript: {
                required: true,
                url: true
            },
        }
    });
});
</script>
<h1>SHTracker: Installer</h1>
<p><i>All fields are required</i></p>
<p><b>Database Settings:</b></p>
<form action="install.php" method="post" id="installform" >
Host: <input type="text" name="dbhost" value="localhost" /><br />
User: <input type="text" name="dbuser" /><br />
Password: <input type="password" name="dbpassword" /><br />
Name: <input type="text" name="dbname" /><br />
<p><b>Admin Details:</b></p>
User: <input type="text" name="adminuser" /><br />
Password: <input type="password" name="adminpassword" /><br />
<p><b>Other Settings:</b></p>
Website Name: <input type="text" name="website" /><br />
Path to Script: <input type="text" name="pathtoscript" /><br />
<p><input type="submit" value="Install" /></p>
</form>
</body>
</html>