<?php
    require("../database.php");
    $database = new Database();
    
    $query = "SELECT user_id, admin, full_access, username FROM users WHERE online=1 ORDER BY created_at DESC";
    $users = $database->query($query);
    echo json_encode($users);
?>