<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    header("Location: installer");
}

require_once("config.php");

//Connect to database
@$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if (!$con) {
    die("Error: Could not connect to database (" . mysql_error() . "). Check your database settings are correct.");
}

//Check database exists
$does_db_exist = mysql_select_db(DB_NAME, $con);
if (!$does_db_exist) {
    die("Error: Database does not exist (" . mysql_error() . "). Check your database settings are correct.");
}

if (isset($_GET["id"])) {
    $id = mysql_real_escape_string($_GET["id"]);
} else {
    die("Error: ID cannot be blank.");
}

//If ID exists, show count or else die
$showinfo = mysql_query("SELECT count FROM Data WHERE id = \"$id\"");
$showresult = mysql_fetch_assoc($showinfo);
if ($showresult != 0) {
    echo $showresult["count"];
} else {
    die("Error: ID does not exist.");
}

mysql_close($con);

?>