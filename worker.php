<?php

//Indication, Copyright Josh Fradley (http://github.com/joshf/Indication)

if (!file_exists("config.php")) {
    die("Error: Config file not found!");
}

require_once("config.php");

//Connect to database
@$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (mysqli_connect_errno()) {
    die("Error: Could not connect to database (" . mysqli_connect_error() . "). Check your database settings are correct.");
}

session_start();
if (isset($_POST["api_key"]) || isset($_GET["api_key"])) {
    if (isset($_POST["api_key"])) {
        $api_key = mysqli_real_escape_string($con, $_POST["api_key"]);
    } elseif (isset($_GET["api_key"])) {
        $api_key = mysqli_real_escape_string($con, $_GET["api_key"]);
    }
    if (empty($api_key)) {
        die("Error: No API key passed!");
    }
    $checkkey = mysqli_query($con, "SELECT `id`, `user` FROM `users` WHERE `api_key` = \"$api_key\"");
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

$getusersettings = mysqli_query($con, "SELECT `user` FROM `users` WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
if (mysqli_num_rows($getusersettings) == 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}
$resultgetusersettings = mysqli_fetch_assoc($getusersettings);

if (isset($_POST["action"])) {
    $action = $_POST["action"];
} elseif (isset($_GET["action"])) {
    $action = $_GET["action"];
} else {
	die("Error: No action passed!");
}

//Check if ID exists
$actions = array("edit", "delete", "info");
if (in_array($action, $actions)) {
    if (isset($_POST["id"]) || isset($_GET["id"])) {
        if (isset($_POST["action"])) {
            $id = mysqli_real_escape_string($con, $_POST["id"]);
        } elseif (isset($_GET["action"])) {
            $id = mysqli_real_escape_string($con, $_GET["id"]);
        }
        $checkid = mysqli_query($con, "SELECT `id` FROM `links` WHERE `id` = \"$id\"");
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
if (isset($_POST["abbreviation"])) {
    $abbreviation = mysqli_real_escape_string($con, $_POST["abbreviation"]);
}
if (isset($_POST["url"])) {
    $url = mysqli_real_escape_string($con, $_POST["url"]);
}

if ($action == "add") {
    if (empty($abbreviation) || empty($url)) {
        die("Error: Data was empty!");
    }

    //Check if abbreviation exists
    $checkabbreviation = mysqli_query($con, "SELECT `abbreviation` FROM `links` WHERE `abbreviation` = \"$abbreviation\"");
    $resultcheckid = mysqli_fetch_assoc($checkabbreviation); 
    if (mysqli_num_rows($checkabbreviation) != 0) {
        die("Error: Abbreviation Exists!");
    }

    //Make sure a password is set if the checkbox was enabled
    if (isset($_POST["passwordprotectstate"])) {
        $protect = "1";
        $inputtedpassword = mysqli_real_escape_string($con, $_POST["password"]);
        if (empty($inputtedpassword)) {
            die("Error: Empty password!");
        }
        $hashedpassword = hash("sha256", $inputtedpassword);
        $password = hash("sha256", SALT . $hashedpassword);
    } else {
        $protect = "0";
        $password = "";
    }

    mysqli_query($con, "INSERT INTO `links` (`name`, `abbreviation`, `url`, `count`, `protect`, `password`)
    VALUES (\"$name\",\"$abbreviation\",\"$url\",\"0\",\"$protect\",\"$password\")");
    
    echo "Info: Link added!";
} elseif ($action == "edit") {
    if (empty($abbreviation) || empty($url)) {
        die("Error: Data was empty!");
    }
    
    //Make sure a password is set if the checkbox was enabled
    if (isset($_POST["passwordprotectstate"])) {
        if (empty($_POST["password"])) {
            die("Error: Empty password!");
        } 
        $getprotectinfo = mysqli_query($con, "SELECT `password` FROM `links` WHERE `id` = \"$id\"");
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

    mysqli_query($con, "UPDATE `links` SET `name` = \"$name\", `abbreviation` = \"$abbreviation\", `url` = \"$url\", `protect` = \"$protect\", `password` = \"$password\" WHERE `id` = \"$id\"");
    
    echo "Info: Link edited!";
} elseif ($action == "delete") {
    mysqli_query($con, "DELETE FROM `links` WHERE `id` = \"$id\"");
    mysqli_query($con, "DELETE FROM `counts` WHERE `link_id` = \"$id\"");
    
    echo "Info: Link deleted!";
} elseif ($action == "generateapikey") {
    $api_key = substr(str_shuffle(MD5(microtime())), 0, 50);
    mysqli_query($con, "UPDATE `Users` SET `api_key` = \"$api_key\" WHERE `id` = \"" . $_SESSION["indication_user"] . "\"");
    echo $api_key;
} elseif ($action == "export") {
    
    $today = date("d-m-y");
    
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=data/export-$today.csv");
    
    if (!file_exists("data")) {
        mkdir("data");
        $protect = fopen("data/index.php", "w");
        fclose($protect);
    }
    
    $output = fopen("data/export-$today.csv", "w");

    fputcsv($output, array("#", "Name", "Abbreviation", "URL", "Count", "Protect", "Password"));

    $getdata = mysqli_query($con, "SELECT * FROM `links`");

    while ($row = mysqli_fetch_assoc($getdata)) {
        fputcsv($output, $row);   
    }

    fclose($output);
    
    echo "Info: CSV created!";
} elseif ($action == "info") {
    
    $getdata = mysqli_query($con, "SELECT `id`, `name`, `abbreviation`, `url`, `count`, `protect` FROM `links` WHERE `id` = \"$id\"");
    $resultgetdata = mysqli_fetch_assoc($getdata); 
    
    $data = array(
        "id" => $resultgetdata["id"],
        "name" => $resultgetdata["name"],
        "abbreviation" => $resultgetdata["abbreviation"],
        "url" => $resultgetdata["url"],
        "count" => $resultgetdata["count"],
        "protect" => $resultgetdata["protect"]
    );
    
    echo json_encode(array("data" => $data));
    
} else {
    die("Error: Action not recognised!");
}

mysqli_close($con);

?>