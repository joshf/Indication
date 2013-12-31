<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

session_start();

unset($_SESSION["indication_user"]);

header("Location: login.php?logged_out=true");

exit;

?>