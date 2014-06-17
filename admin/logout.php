<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

session_start();

unset($_SESSION["indication_user"]);

if (isset($_COOKIE["indication_user_rememberme"])) {
    setcookie("indication_user_rememberme", "", time()-86400);
}

header("Location: login.php?logged_out=true");

exit;

?>