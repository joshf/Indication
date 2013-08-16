<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
	die("Error: Config file not found! Please reinstall Indication.");
}

require_once("config.php");

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

mysql_select_db(DB_NAME, $con);

if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} else {
    die("Error: ID cannot be blank.");
}

//If ID exists, show count or else die
$showinfo = mysql_query("SELECT `count` FROM `Data` WHERE `id` = \"$id\"");
$showinforesult = mysql_fetch_assoc($showinfo);
if (mysql_num_rows($showinfo) != 0) {
    echo $showinforesult["count"];
} else {
    die("Error: ID does not exist.");
}

mysql_close($con);

?>