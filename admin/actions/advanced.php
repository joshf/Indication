<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../../config.php");

$uniquekey = UNIQUE_KEY;

session_start();
if (!isset($_SESSION["is_logged_in_" . $uniquekey . ""])) {
    header("Location: ../login.php");
    exit; 
}

if (!isset($_POST["command"])) {
    header("Location: ../settings.php");
    exit;
}

?>
<html>
<head>
<title>SHTracker</title>
<link rel="stylesheet" type="text/css" href="../../style.css" />
</head>
<body>
<?php

//Make user confirm action with a password
$password = ADMIN_PASSWORD;

if (sha1($_POST["password"]) != $password) {
    die("<h1>SHTracker: Action Failed</h1><p>Incorrect password entered. Please go back and try again.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

//Connect to database
$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

$command = $_POST["command"];

if ($command == "Reset All Counts to Zero") {

    //Reset All Counts to Zero
    
    mysql_query("UPDATE Data SET count = \"0\"");
    
    mysql_close($con);
    	
    die("<h1>SHTracker: Action Successful</h1><p>All counts have been reset to zero.</p><hr /><p><a href=\"../settings.php\">Back To Settings</a></p></body></html>");
	
} elseif ($command == "Delete All Downloads") {
	
    //Delete All Downloads
        
    mysql_query("DELETE FROM Data");
    
    mysql_close($con);
    	
    die("<h1>SHTracker: Action Successful</h1><p>All downloads have been deleted.</p><hr /><p><a href=\"../settings.php\">Back To Settings</a></p></body></html>");
	
} else {
    mysql_close($con);
    header("Location: ../settings.php");
    exit;
}
?>
</body>
</html>