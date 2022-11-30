<?php
    require("../database.php");
    $database = new Database();
    session_start();
    $err_message = 1;
    $ip = $_SERVER['REMOTE_ADDR'];

    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    
    $database->where("user_id", $user_id);
    $user = $database->select("users", 1);
    $username = $user[0]['username'];

    $data = array(
        "user_id" => $user_id,
        "ip" => $ip,
        "text" => $username . " just disconnected!"
    );
    $database->insert("logs", $data);
    session_reset();
    session_destroy();
    echo $err_message;
    exit();
?>