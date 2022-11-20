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
    $profile_photo = NULL;
    if (isset($_POST['profile_photo'])) {
        $profile_photo = $_POST['profile_photo'];
    }
    $change_photo = NULL;
    if (isset($_POST['change_photo'])) {
        $change_photo = $_POST['change_photo'];
    }

    if ($change_photo == "yes") {
        if (!isset($username) || !isset($user_id) || !isset($email) || !isset($profile_photo)) {
            return;
        }
    } else {
        if (!isset($username) || !isset($user_id) || !isset($email)) {
            return;
        }
    }
    
    $database->where('user_id', $user_id);
    if ($change_photo == "yes") {
        $data = array(
            "username" => $username, 
            "email" => $email,
            "profile_photo" => $profile_photo
        );
    } else {
        $data = array(
            "username" => $username, 
            "email" => $email
        );
    }
    $updateUser = $database->update("users", $data);
    if (!$updateUser) {
        $err_message = "Cannot update user id: $user_id! (contact admin)";
    }
    echo $err_message;
?>