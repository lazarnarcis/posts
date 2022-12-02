<?php
    require("../database.php");
    $database = new Database();

    $password = NULL;
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    }
    if (!isset($password)) {
        return;
    }

    $err_message = [];
    $err_message['type'] = "warning";
    if (strlen($password) < 8) {
        $err_message['text'] = "The password cannot have less than 8 characters.";
    } else {
        $err_message['text'] = "This password is valid!";
        $err_message['type'] = "success";
    }

    echo json_encode($err_message);
?>