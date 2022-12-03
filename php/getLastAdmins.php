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
    $query = "SELECT user_id, username, created_at, admin, full_access FROM users WHERE admin!=0 OR full_access!=0 ORDER BY created_at DESC LIMIT $max_users";
    $users = $database->query($query);
    echo json_encode($users);
?>