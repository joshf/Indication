<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    header("Location: installer");
    exit;
}

require_once("config.php");

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

if (isset($_GET["id"])) {
    $downloadid = mysqli_real_escape_string($con, $_GET["id"]);
} else {
    die("Error: ID cannot be blank.");
}

//If ID exists, show count or else die
$showinfo = mysqli_query($con, "SELECT `count` FROM `Data` WHERE `downloadid` = \"$downloadid\"");
$showinforesult = mysqli_fetch_assoc($showinfo);
if (mysqli_num_rows($showinfo) != 0) {
    echo $showinforesult["count"];
} else {
    die("Error: ID does not exist.");
}

mysqli_close($con);

?>