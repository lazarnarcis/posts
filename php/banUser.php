<?php
    require("../database.php");
    $database = new Database();
    $err_message = 1;

    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    $banned_user_id = NULL;
    if (isset($_POST['banned_user_id'])) {
        $banned_user_id = $_POST['banned_user_id'];
    }
    $reason = NULL;
    if (isset($_POST['ban_reason'])) {
        $reason = $_POST['ban_reason'];
    }
    if (!isset($user_id) || !isset($reason) || !isset($banned_user_id)) {
        return;
    }

    $database->where("user_id", $banned_user_id);
    $data = array("banned" => 1);
    $banUser = $database->update("users", $data);

    $data = array(
        "reason" => $reason,
        "user_id" => $user_id,
        "banned_user_id" => $banned_user_id
    );
    $insertBan = $database->insert("bans", $data);
    if (!$banUser) {
        $err_message = "Cannot ban user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>