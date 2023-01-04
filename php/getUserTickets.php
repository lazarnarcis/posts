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
    
    $query = "SELECT tickets.id, tickets.user_id, tickets.text, tickets.ip, tickets.created_at, tickets.flags, users.username FROM tickets LEFT JOIN users ON users.user_id=tickets.user_id WHERE tickets.user_id='".$user_id."' ORDER BY created_at DESC";
    $tickets = $database->query($query);
    echo json_encode($tickets);
?>