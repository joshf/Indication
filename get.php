<?php
//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)
ob_start();
?>
<html>
<head>
<title>SHTracker</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<meta name="robots" content="noindex, nofollow">
</head>
<body>
<?php

//Connect to database
require_once("config.php");

$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Could not connect: " . mysql_error());
}

mysql_select_db(DB_NAME, $con);

//Accept POST or GET
if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} else {
    $id = mysql_real_escape_string($_POST["id"]);
}

//Check ID is not blank
if (empty($id)) {
    die("<h1>SHTracker: Error</h1><p>ID cannot be blank.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

//Prevent some injection attacks
if (!preg_match("/^[a-zA-Z0-9.]{1,}$/", $id)) {
    die("<h1>SHTracker: Error</h1><p>Please enter only numbers, letters or points.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>"); 
}

//Check if ID exists
$getinfo = mysql_query("SELECT name, url FROM Data WHERE id = \"$id\"");
$getresult = mysql_fetch_assoc($getinfo); 
if ($getresult == 0) { 
    die("<h1>SHTracker: Error</h1><p>ID <strong>$id</strong> does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
    if (!isset($_COOKIE["shtrackerhasdownloaded$id"])) {
        mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
        setcookie("shtrackerhasdownloaded$id", "True", time()+3600*COUNT_UNIQUE_ONLY_TIME);
    }
} else {
    mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
}

mysql_close($con);

//Password protect some downloads

//These have to be set manually
// ID => password
$downloadspasses = array("download1" => "nygftrykhui");

if (PASSWORD_PROTECT == "Enabled") {
    if (array_key_exists($id, $downloadspasses)) {
        if (isset($_POST["password"])) {
            if (sha1($_POST["password"]) == sha1($downloadspasses["$id"])) {
                header("Location: " . $getresult["url"] . "");
                exit;
            } else {
                die("<h1>SHTracker: Error</h1><p>Incorrect password.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
            }
        } else {
            die("<h1>Downloading " . $getresult["name"] . "</h1>
            <form method=\"post\">
            <p>To access this download please enter the password you were given.</p>
            <p>Password: <input type=\"password\" name=\"password\" /></p>
            <input type=\"submit\" name=\"submit\" value=\"Get Download\" /></p></form>
            </body>
            </html>");
        }
    }
}

//Check whether wait is enabled
if (WAIT_STATE == "Enabled" ) {
    echo "<h1>Downloading " . $getresult["name"] . "</h1><p>" . WAIT_MESSAGE . "</p><p>" . WAIT_AD_CODE . "</p><hr /><p><a href=\"" . $getresult["url"] . "\">Start Download</a></p></body></html>";
    exit;
}

//Redirect user to the download
header("Location: " . $getresult["url"] . "");
ob_end_flush();
exit;
?>
</body>
</html>