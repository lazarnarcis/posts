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
    $query = "SELECT user_id, username, created_at FROM users ORDER BY created_at DESC LIMIT $max_users";
    $users = $database->query($query);
    echo json_encode($users);
?>