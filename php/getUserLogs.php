<?php
    require("../database.php");
    $database = new Database();
    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    $logs_length = NULL;
    if (isset($_POST['logs_length'])) {
        $logs_length = $_POST['logs_length'];
    }
    if (!isset($user_id) || !isset($logs_length)) {
        return;
    }
    $database->where("user_id", $user_id);
    $database->orderBy("created_at", "DESC");
    $logs = $database->select("logs", $logs_length);
    echo json_encode($logs);
?>