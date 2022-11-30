<?php
    require("./api.php");
    $api = new api();
    session_start();
    $err_message = 1;
    $ip = $_SERVER['REMOTE_ADDR'];

    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    
    $user = $api->userInfo($user_id);
    $username = $user['username'];

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