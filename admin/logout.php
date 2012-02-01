<?php

//SHTracker, Copyright Josh Fradley (http://sidhosting.co.uk/projects/shtracker)

require_once("../config.php");
$randomkey = RANDOM_KEY;

session_start();
unset($_SESSION["loggedin" . $randomkey . ""]);
session_destroy();

?>
<html>
<head>
<title>SHTracker: Logged Out</title>
<link rel="stylesheet" type="text/css" href="../style.css" />
</head>
<body>
<h1>SHTracker: Logged Out</h1>
<p>You have been successfully logged out.</p>
<hr />
<p><a href="../admin">Go Back</a></p>
</body>
</html>
