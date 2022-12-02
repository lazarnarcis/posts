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
    $database->where("username", $username);
    $getUser = $database->select("users", 1);

    $err_message = [];
    $err_message['type'] = "warning";
    if (count($getUser)) {
        $err_message['text'] = "This username is already used!";
    } elseif (preg_match('/[A-Z]/', $username)) {
        $err_message['text'] = "Your username cannot contain uppercase letters!";
    } elseif (strlen($username) > 20) {
        $err_message['text'] = "Your username can contain a maximum of 20 characters!";
    } elseif (strlen($username) < 6) {
        $err_message['text'] = "Your username must contain 6 characters!";
    } elseif (preg_match('/\s/', $username)) {
        $err_message['text'] = "Your username cannot contain spaces!";
    } else {
        $err_message['text'] = "This username is valid!";
        $err_message['type'] = "success";
    }

    echo json_encode($err_message);
?>