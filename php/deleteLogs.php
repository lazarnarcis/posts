<?php
    require("./api.php");
    $api = new api();
    $err_message = 1;

    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    $my_user_id = NULL;
    if (isset($_POST['my_user_id'])) {
        $my_user_id = $_POST['my_user_id'];
    }
    if (!isset($user_id) || !isset($my_user_id)) {
        return;
    }

    $database->where("user_id", $user_id);
    $deleteLogs = $database->deleteRow("logs");

    $getUserDeletedLogs = $api->userInfo($user_id);
    $getMyUser = $api->userInfo($my_user_id);

    $data = array(
        "text" => $getMyUser['username']." deleted ".$getUserDeletedLogs['username']." logs!",
        "user_id" => $user_id,
        "ip" => $_SERVER['REMOTE_ADDR']
    );
    $database->insert("logs", $data);

    if ($user_id != $my_user_id) {
        $data['user_id'] = $my_user_id;
        $database->insert("logs", $data);
    }
    
    if (!$deleteLogs) {
        $err_message = "Cannot delete logs for user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>