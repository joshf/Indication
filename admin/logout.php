<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("../config.php")) {
    header('Location: ../installer');
    exit;
}

require_once("../config.php");

session_start();

unset($_SESSION["indication_user"]);

header("Location: login.php?logged_out=true");

exit;

?>