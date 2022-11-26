<?php
    require("../database.php");
    $database = new Database();
    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    if (!isset($user_id)) {
        return;
    }
    $database->where("user_id", $user_id);
    $database->orderBy("created_at", "DESC");
    $logs = $database->select("logs", 20);
    echo json_encode($logs);
?>