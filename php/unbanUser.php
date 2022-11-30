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
    $data = array("banned" => 0, "ban_ip" => 0);
    $unbanUser = $database->update("users", $data);

    $database->where("banned_user_id", $user_id);
    $deleteBan = $database->deleteRow("bans");

    $getUserWhoBan = $api->userInfo($user_id);
    $getMyUser = $api->userInfo($my_user_id);

    $data = array(
        "text" => $getUserWhoBan['username']." has been unbanned by ".$getMyUser['username']."!",
        "user_id" => $user_id,
        "ip" => $_SERVER['REMOTE_ADDR']
    );
    $database->insert("logs", $data);

    if ($user_id != $my_user_id) {
        $data = array(
            "text" => $getUserWhoBan['username']." has been unbanned by ".$getMyUser['username']."!",
            "user_id" => $my_user_id,
            "ip" => $_SERVER['REMOTE_ADDR']
        );
        $database->insert("logs", $data);
    }
    
    if (!$unbanUser || !$deleteBan) {
        $err_message = "Cannot ban user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>