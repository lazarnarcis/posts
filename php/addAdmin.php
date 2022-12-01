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
    $data = array("admin" => 1);
    $addAdmin = $database->update("users", $data);

    $getFullAccess = $api->userInfo($my_user_id);
    $getNewAdmin = $api->userInfo($user_id);

    $data = array(
        "text" => $getFullAccess['username'] . " added " . $getNewAdmin['username'] . ' as admin!',
        "user_id" => $user_id,
        "ip" => $_SERVER['REMOTE_ADDR']
    );
    $database->insert("logs", $data);

    if ($user_id != $my_user_id) {
        $data['user_id'] = $my_user_id;
        $database->insert("logs", $data);
    }

    if (!$addAdmin) {
        $err_message = "Cannot add admin user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>