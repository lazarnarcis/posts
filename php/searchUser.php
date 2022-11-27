<?php
    require("../database.php");
    $database = new Database();
    $username = NULL;
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    }
    if (!isset($username)) {
        return;
    }
    $query = "SELECT user_id, username, profile_photo FROM users WHERE username LIKE '%$username%' LIMIT 100";
    $users = $database->query($query);
    echo json_encode($users);
?>