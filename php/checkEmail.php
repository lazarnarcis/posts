<?php
    require("../database.php");
    $database = new Database();

    $email = NULL;
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    if (!isset($email)) {
        return;
    }
    $database->where("email", $email);
    $getUser = $database->select("users", 1);

    $err_message = [];
    $err_message['type'] = "warning";
    if (count($getUser)) {
        $err_message['text'] = "This email is already used!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err_message['text'] = "Invalid email format!";
    } else {
        $err_message['text'] = "This email is valid!";
        $err_message['type'] = "success";
    }

    echo json_encode($err_message);
?>