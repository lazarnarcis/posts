<?php
    require("../database.php");
    $database = new Database();
    $err_message = 1;

    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    if (!isset($user_id)) {
        return;
    }

    $database->where("user_id", $user_id);
    $data = array("banned" => 0);
    $unbanUser = $database->update("users", $data);

    $database->where("banned_user_id", $user_id);
    $deleteBan = $database->deleteRow("bans");
    
    if (!$unbanUser || !$deleteBan) {
        $err_message = "Cannot ban user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>