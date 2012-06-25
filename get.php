<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

ob_start();

?>
<html>
<head>
<title>SHTracker</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<meta name="robots" content="noindex, nofollow">
</head>
<body>
<script type="text/javascript">
$(document).ready(function() { 
    var count = 5;
    countdown = setInterval(function(){
        $("#counterplacholder").html("<p>Your download will be ready in " + count + " second(s)</p>");
        if (count <= 0) {
            clearInterval(countdown);
            $("#counterplacholder").fadeOut("fast");
            $("#downloadurl").delay(500).fadeIn("fast");
        }
        count--;
    }, 1000);
});
</script>
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

//Check if ID exists
$getinfo = mysql_query("SELECT name, url FROM Data WHERE id = \"$id\"");
$getinforesult = mysql_fetch_assoc($getinfo); 
if ($getinforesult == 0) { 
    die("<h1>SHTracker: Error</h1><p>ID does not exist.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
}

if (COUNT_UNIQUE_ONLY_STATE == "Enabled") {
    if (!isset($_COOKIE["shtrackerhasdownloaded$id"])) {
        mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
        setcookie("shtrackerhasdownloaded$id", "True", time()+3600*COUNT_UNIQUE_ONLY_TIME);
    }
} else {
    mysql_query("UPDATE Data SET count = count+1 WHERE id = \"$id\"");
}

//Check if download is password protected
$checkifprotected = mysql_query("SELECT protect, password FROM Data WHERE id = \"$id\"");
$checkifprotectedresult = mysql_fetch_assoc($checkifprotected); 
if ($checkifprotectedresult["protect"] == "1") { 
    if (isset($_POST["password"])) {
        if (sha1($_POST["password"]) != $checkifprotectedresult["password"]) {
            die("<h1>SHTracker: Error</h1><p>Incorrect password.</p><hr /><p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p></body></html>");
        }
    } else {
        die("<h1>Downloading " . $getinforesult["name"] . "</h1>
        <form method=\"post\">
        <p>To access this download please enter the password you were given.</p>
        <p>Password: <input type=\"password\" name=\"password\" /></p>
        <input type=\"submit\" value=\"Get Download\" /></p></form>
        <hr />
        <p><a href=\"javascript:history.go(-1)\">&larr; Go Back</a></p>
        </body>
        </html>");
    }
}

//Check if we should show ads
$checkifadsshow = mysql_query("SELECT showads FROM Data WHERE id = \"$id\"");
$checkifadsshowresult = mysql_fetch_assoc($checkifadsshow); 
if ($checkifadsshowresult["showads"] == "1") { 
    $adcode = htmlspecialchars_decode(AD_CODE); 
    die("<h1>Downloading " . $getinforesult["name"] . "</h1><p>" . $adcode . "</p><hr /><div id=\"counterplacholder\"></div><div id=\"downloadurl\" style=\"display: none\"><p><a href=\"" . $getinforesult["url"] . "\">Start Download</a></p></div></body></html>");
}

mysql_close($con);

//Redirect user to the download
header("Location: " . $getinforesult["url"] . "");
ob_end_flush();
exit;

?>
</body>
</html>