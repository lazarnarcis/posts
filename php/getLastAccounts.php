<?php
    require("../database.php");
    $database = new Database();

    $max_users = NULL;
    if (isset($_POST['max_users'])) {
        $max_users = $_POST['max_users'];
    }

    if (!isset($max_users)) {
        return;
    }
    $database->orderBy("user_id", "DESC");
    $users = $database->select("users", $max_users);
    echo json_encode($users);
?>