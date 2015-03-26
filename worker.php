<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    header("Location: install");
    exit;
}

require_once("config.php");

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

session_start();
if (isset($_POST["api_key"])) {
    $api = mysqli_real_escape_string($con, $_POST["api_key"]);
    if (empty($api)) {
        die("Error: No API key passed!");
    }
    $checkkey = mysqli_query($con, "SELECT `id`, `user` FROM `Users` WHERE `api_key` = \"$api\"");
    $checkkeyresult = mysqli_fetch_assoc($checkkey);
    if (mysqli_num_rows($checkkey) == 0) {
        die("Error: API key is not valid!");
    } else {
        $_SESSION["indication_user"] = $checkkeyresult["id"];
    }
}

if (!isset($_SESSION["indication_user"])) {
    header("Location: login.php");
    exit;
}

$getusersettings = mysqli_query($con, "SELECT `user` FROM `Users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysqli_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysqli_fetch_assoc($getusersettings);

if (isset($_POST["action"])) {
    $action = $_POST["action"];
} else {
	die("Error: No action passed!");
}

//Check if ID exists
$actions = array("edit", "delete", "details");
if (in_array($action, $actions)) {
    if (isset($_POST["id"])) {
        $id = mysqli_real_escape_string($con, $_POST["id"]);
        $checkid = mysqli_query($con, "SELECT `id` FROM `Data` WHERE `id` = \"$id\"");
        if (mysqli_num_rows($checkid) == 0) {
        	die("Error: ID does not exist!");
        }
    } else {
    	die("Error: ID not set!");
    }
}

//Define variables
if (isset($_POST["name"])) {
    $name = mysqli_real_escape_string($con, $_POST["name"]);
}
if (isset($_POST["linkid"])) {
    $linkid = mysqli_real_escape_string($con, $_POST["linkid"]);
}
if (isset($_POST["url"])) {
    $url = mysqli_real_escape_string($con, $_POST["url"]);
}
if (isset($_POST["count"])) {
    $count = mysqli_real_escape_string($con, $_POST["count"]);
}

if ($action == "add") {
    if (empty($linkid) || empty($url)) {
        die("Error: Data was empty!");
    }

    //Check if ID exists
    $checkid = mysqli_query($con, "SELECT `linkid` FROM `Data` WHERE `linkid` = \"$linkid\"");
    $resultcheckid = mysqli_fetch_assoc($checkid); 
    if (mysqli_num_rows($checkid) != 0) {
        die("Error: ID Exists!");
        exit;
    }

    //Make sure a password is set if the checkbox was enabled
    if (isset($_POST["passwordprotectstate"])) {
        $protect = "1";
        $inputtedpassword = mysqli_real_escape_string($con, $_POST["password"]);
        if (empty($inputtedpassword)) {
            die("Error: Empty password!");
            exit;
        }
        $hashedpassword = hash("sha256", $inputtedpassword);
        $password = hash("sha256", SALT . $hashedpassword);
    } else {
        $protect = "0";
        $password = "";
    }

    if (isset($_POST["showadsstate"])) {
        $showads = "1";
    } else {
        $showads = "0";
    }

    mysqli_query($con, "INSERT INTO `Data` (`name`, `linkid`, `url`, `count`, `protect`, `password`, `showads`)
    VALUES (\"$name\",\"$linkid\",\"$url\",\"$count\",\"$protect\",\"$password\",\"$showads\")");
} elseif ($action == "edit") {
    if (empty($linkid) || empty($url)) {
        die("Error: Data was empty!");
    }
    
    //Make sure a password is set if the checkbox was enabled
    if (isset($_POST["passwordprotectstate"])) {
        if (empty($_POST["password"])) {
            die("Error: Empty password!");
            exit;
        } 
        $getprotectinfo = mysqli_query($con, "SELECT `password` FROM `Data` WHERE `id` = \"$id\"");
        $getprotectinforesult = mysqli_fetch_assoc($getprotectinfo); 
        $inputtedpassword = mysqli_real_escape_string($con, $_POST["password"]);
        if (empty($inputtedpassword)) {
            $password = $getprotectinforesult["password"];
        } else {
            $hashedpassword = hash("sha256", $inputtedpassword);
            $password = hash("sha256", SALT . $hashedpassword);
        }
        $protect = "1";
    } else {
        $protect = "0";
        $password = "";
    }

    if (isset($_POST["showadsstate"])) {
        $showads = "1";
    } else {
        $showads = "0";
    }

    mysqli_query($con, "UPDATE `Data` SET `name` = \"$name\", `linkid` = \"$linkid\", `url` = \"$url\", `count` = \"$count\", `protect` = \"$protect\", `password` = \"$password\", `showads` = \"$showads\" WHERE `id` = \"$id\"");
    
} elseif ($action == "delete") {
    mysqli_query($con, "DELETE FROM `Data` WHERE `id` = \"$id\"");
} elseif ($action == "details") {
    $getdetails = mysqli_query($con, "SELECT `name`, `linkid`, `url`, `count`, `showads`, `protect`, `password` FROM `Data` WHERE `id` = \"$id\"");
    $resultgetdetails = mysqli_fetch_assoc($getdetails);
    
    $arr = array();
    $arr[0] = $resultgetdetails["name"];
    $arr[1] = $resultgetdetails["linkid"];
    $arr[2] = $resultgetdetails["url"];
    $arr[3] = $resultgetdetails["count"];
    $arr[4] = $resultgetdetails["showads"];
    $arr[5] = $resultgetdetails["protect"];
    $arr[6] = $resultgetdetails["password"];
    
    echo json_encode($arr);
} elseif ($action == "generateapikey") {
    $api = substr(str_shuffle(MD5(microtime())), 0, 50);
    mysqli_query($con, "UPDATE `Users` SET `api_key` = \"$api\" WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
    echo $api;
} else {
    die("Error: Action not recognised!");
}

mysqli_close($con);

?>