<?php
    require("../database.php");
    $database = new Database();

    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }

    $ticket_title = NULL;
    if (isset($_POST['ticket_title'])) {
        $ticket_title = $_POST['ticket_title'];
    }

    $flags = NULL;
    if (isset($_POST['flags'])) {
        $flags = $_POST['flags'];
    }

    if (!isset($user_id) || !isset($ticket_title) || !isset($flags)) {
        return;
    }

    $database->where("user_id", $user_id);
    $user = $database->select("users");
    $username = $user[0]['username'];

    $data = array(
        "text" => $username." created a ticket with the name: ".$ticket_title,
        "user_id" => $user_id,
        "ip" => $_SERVER['REMOTE_ADDR']
    );
    $database->insert("logs", $data);

    $data = array(
        "user_id" => $user_id,
        "flags" => $flags,
        "text" => $ticket_title,
        "ip" => $_SERVER['REMOTE_ADDR']
    );
    $users = $database->insert("tickets", $data);
    echo 1;
?>