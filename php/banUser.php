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
    if (isset($_POST['ban_ip'])) {
        $data = array("banned" => 1, "ban_ip" => 1);
    } else {
        $data = array("banned" => 1);
    }
    $banUser = $database->update("users", $data);

    $data = array(
        "reason" => $reason,
        "user_id" => $user_id,
        "banned_user_id" => $banned_user_id
    );
    $insertBan = $database->insert("bans", $data);

    $getBan = $database->query("SELECT * FROM bans WHERE user_id='".$user_id."' AND banned_user_id='".$banned_user_id."'");
    $database->where("user_id", $getBan[0]['user_id']);
    $getUserWhoBan = $database->select("users", 1);
    $database->where("user_id", $getBan[0]['banned_user_id']);
    $getUserBanned = $database->select("users", 1);
    $reason = $getBan[0]['reason'];

    $ip_ban_text = NULL;
    if (isset($_POST['ban_ip'])) {
        $ip_ban_text = " on IP ";
    }

    $data = array(
        "text" => $getUserBanned[0]['username']." has been banned by ".$getUserWhoBan[0]['username'].$ip_ban_text.". Reason: ". $reason,
        "user_id" => $getBan[0]['user_id'],
        "ip" => $_SERVER['REMOTE_ADDR']
    );
    $database->insert("logs", $data);

    $data = array(
        "text" => $getUserBanned[0]['username']." has been banned by ".$getUserWhoBan[0]['username'].$ip_ban_text.". Reason: ". $reason,
        "user_id" => $getBan[0]['banned_user_id'],
        "ip" => $_SERVER['REMOTE_ADDR']
    );
    $database->insert("logs", $data);

    if (!$banUser) {
        $err_message = "Cannot ban user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>