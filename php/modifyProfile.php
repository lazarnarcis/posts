<?php
    require("../database.php");
    $database = new Database();
    $err_message = 1;

    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    $username = NULL;
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    }
    $email = NULL;
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    if (!isset($username) || !isset($user_id) || !isset($email)) {
        return;
    }
    
    $database->where('user_id', $user_id);
    $data = array("username" => $username, "email" => $email);
    $updateUser = $database->update("users", $data);
    if (!$updateUser) {
        $err_message = "Cannot update user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>