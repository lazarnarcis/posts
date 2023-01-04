<?php
    require("../database.php");
    $database = new Database(); 
    
    $query = "SELECT id FROM tickets ORDER BY id DESC LIMIT 1";
    $ticket_id = $database->query($query);

    if (!count($ticket_id)) {
        echo 0;
    } else {
        $ticket_id = $ticket_id[0]['id'];
        echo json_encode($ticket_id);
    }
?>