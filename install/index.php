<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication

require_once("../assets/version.php");

//Check if Indication has been installed
if (file_exists("../config.php")) {
    die("Information: Indication has already been installed! You can login <a href=\"../login.php\">here</a> or to reinstall the app please delete your config.php file and run this installer again.");
}

if (isset($_POST["install"])) {
    
    $dbhost = $_POST["dbhost"];
    $dbuser = $_POST["dbuser"];
    $dbpassword = $_POST["dbpassword"];
    $dbname = $_POST["dbname"];
	$website = $_POST["website"];
	$pathtoscript = $_POST["pathtoscript"];
    
    //Check if we can connect
    @$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
    if (mysqli_connect_errno()) {
        die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
    }
    
    //Second salt for password protection
    $randsalt = md5(uniqid(rand(), true));
    $salt = substr($randsalt, 0, 3);
    
    $installstring = "<?php\n\n//Database Settings\ndefine('DB_HOST', " . var_export($dbhost, true) . ");\ndefine('DB_USER', " . var_export($dbuser, true) . ");\ndefine('DB_PASSWORD', " . var_export($dbpassword, true) . ");\ndefine('DB_NAME', " . var_export($dbname, true) . ");\n\n//Other Settings\ndefine('SALT', " . var_export($salt, true) . ");\ndefine('WEBSITE', " . var_export($website, true) . ");\ndefine('PATH_TO_SCRIPT', " . var_export($pathtoscript, true) . ");\ndefine('COUNT_UNIQUE_ONLY_STATE', 'Enabled');\ndefine('CUSTOM_URL_STATE', 'Disabled');\ndefine('CUSTOM_URL', '');\n\n?>";

    //Write Config
    $configfile = fopen("../config.php", "w");
    fwrite($configfile, $installstring);
    fclose($configfile);
    
    $user = $_POST["user"];
    $email = $_POST["email"];
    if (empty($_POST["password"])) {
        die("Error: No password set.");
    } else {
        //Salt and hash passwords
        $randsalt = md5(uniqid(rand(), true));
        $salt = substr($randsalt, 0, 3);
        $hashedpassword = hash("sha256", $_POST["password"]);
        $password = hash("sha256", $salt . $hashedpassword);
    }
    $api_key = substr(str_shuffle(MD5(microtime())), 0, 50);
    
    //Check if we can connect
    @$con = mysqli_connect($dbhost, $dbuser, $dbuser, $dbname);
    if (mysqli_connect_errno()) {
        die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
    }

	//Create count table
	$createcounttable = "CREATE TABLE `counts` (
    `id` int(8) NOT NULL,
    `link_id` varchar(100) NOT NULL,
    `date` date NOT NULL,
    `ip` varchar(50) NOT NULL,
    `referrer` varchar(300) NOT NULL
    ) ENGINE = InnoDB;";
    
    mysqli_query($con, $createcounttable) or die(mysqli_error($con));
    
	//Create links table
	$createlinkstable = "CREATE TABLE `links` (
    `id` int(8) NOT NULL,
    `name` varchar(100) NOT NULL,
    `abbreviation` varchar(25) NOT NULL,
    `url` varchar(2000) NOT NULL,
    `count` int(10) NOT NULL DEFAULT \"0\",
    `protect` tinyint(1) NOT NULL DEFAULT \"0\",
    `password` varchar(200) DEFAULT NULL
    ) ENGINE = InnoDB;";
    
    mysqli_query($con, $createlinkstable) or die(mysqli_error($con));
    
    //Create users table
    $createuserstable = "CREATE TABLE `users` (
    `id` int(8) NOT NULL,
    `user` varchar(20) NOT NULL,
    `password` varchar(200) NOT NULL,
    `salt` varchar(3) NOT NULL,
    `email` varchar(100) NOT NULL,
    `hash` varchar(200) NOT NULl,
    `api_key` varchar(200) NOT NULl
    ) ENGINE = InnoDB;";
    
    mysqli_query($con, $createuserstable) or die(mysqli_error($con));
    
    //Add keys
    mysqli_query($con, "ALTER TABLE `counts` ADD PRIMARY KEY (`id`)");
    mysqli_query($con, "ALTER TABLE `links` ADD PRIMARY KEY (`id`)");
    mysqli_query($con, "ALTER TABLE `users` ADD PRIMARY KEY (`id`)");
    mysqli_query($con, "ALTER TABLE `counts` CHANGE `id` `id` INT(8) NOT NULL AUTO_INCREMENT");
    mysqli_query($con, "ALTER TABLE `links` CHANGE `id` `id` INT(8) NOT NULL AUTO_INCREMENT");
    mysqli_query($con, "ALTER TABLE `users` CHANGE `id` `id` INT(8) NOT NULL AUTO_INCREMENT");
    
    mysqli_query($con, "INSERT INTO users (user, password, salt, email, hash, api_key)
    VALUES (\"$user\",\"$password\",\"$salt\",\"$email\",\"\",\"$api_key\")");
        
    mysqli_close($con);
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="../assets/favicon.ico">
<title>Indication &raquo; Install</title>
<link rel="apple-touch-icon" href="../assets/icon.png">
<link rel="stylesheet" href="../assets/bower_components/bootstrap/dist/css/bootstrap.min.css" type="text/css" media="screen">
<link rel="stylesheet" href="../assets/css/indication.css" type="text/css" media="screen">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
<div class="container-fluid">
<div class="navbar-header">
<a class="navbar-brand" href="index.php">Indication</a>
</div>
</div>
</nav>
<div class="container-fluid top-pad">
<?php

if (isset($_POST["install"])) {    
 
?>
<p>Indication has been successfully installed. Please delete the "install" folder from your server, as it poses a potential security risk!</p>
<a href="../login.php" class="btn btn-default" role="button">Login</a>
<?php
} else {
?>
<div class="alert alert-info">Welcome to Indication <?php echo $version ?>. Before getting started, we need some information on your database and for you to create an admin user.</div>
<form id="installform" method="post" autocomplete="off">
<?php
      
//Get path to script
$currenturl = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$pathtoscriptwithslash = "http://" . substr($currenturl, 0, strpos($currenturl, "install"));
$pathtoscript = rtrim($pathtoscriptwithslash, "/");	

?>
<div class="form-group">
<label for="dbhost">Database Host</label>
<input type="text" class="form-control" id="dbhost" name="dbhost" value="localhost" placeholder="Type your database host..." required>
</div>
<div class="form-group">
<label for="dbuser">Database User</label>
<input type="text" class="form-control" id="dbuser" name="dbuser" placeholder="Type your database user..." required>
</div>
<div class="form-group">
<label for="dbpassword">Database Password</label>
<input type="password" class="form-control" id="dbpassword" name="dbpassword" placeholder="Type your database password..." required>
</div>
<div class="form-group">
<label for="dbname">Database Name</label>
<input type="text" class="form-control" id="dbname" name="dbname" placeholder="Type your database name..." required>
</div>
<div class="form-group">
<label for="website">Website Name</label>
<input type="text" class="form-control" id="website" name="website" value="Indication" placeholder="Type your websites name..." required>
</div>
<div class="form-group">
<label for="pathtoscript">Path to Script</label>
<input type="url" class="form-control" id="pathtoscript" name="pathtoscript" value="<?php echo $pathtoscript; ?>" placeholder="Type the path to Indication..." required>
</div>
<div class="form-group">
<label for="user">User</label>
<input type="text" class="form-control" id="user" name="user" placeholder="Type a username..." required>
</div>
<div class="form-group">
<label for="email">Email</label>
<input type="email" class="form-control" id="email" name="email" placeholder="Type an email..." required>
</div>
<div class="form-group">
<label for="password">Password</label>
<input type="password" class="form-control" id="password" name="password" placeholder="Type a password..." required>
</div>
<input type="hidden" name="install">
<input type="submit" class="btn btn-default" value="Install">
</form>
<br>
<?php
}
?>
</div>
<script src="../assets/bower_components/jquery/dist/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../assets/bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
<script src="../assets/bower_components/bootstrap-validator/dist/validator.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function () {
    $("#installform").validator({
        disable: true
    });
});
</script>
</body>
</html>