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
    $my_username = NULL;
    if (isset($_POST['my_username'])) {
        $my_username = $_POST['my_username'];
    }
    $my_email = NULL;
    if (isset($_POST['my_email'])) {
        $my_email = $_POST['my_email'];
    }
    $profile_photo = NULL;
    if (isset($_POST['profile_photo'])) {
        $profile_photo = $_POST['profile_photo'];
    }
    $password = NULL;
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    }

    if (!isset($username) || !isset($user_id) || !isset($email)) {
        return;
    }

    $database->where("email", $email);
    $check_email = $database->select("users", 1);

    $database->where("username", $username);
    $check_username = $database->select("users", 1);

    if (count($check_username) && $username != $my_username) {
        $err_message = "Already exist an account with this username!";
    } elseif (preg_match('/[A-Z]/', $username)) {
        $err_message = "Your username cannot contain uppercase letters!";
    } elseif (strlen($username) > 20) {
        $err_message = "Your username can contain a maximum of 20 characters!";
    } elseif (strlen($username) < 6) {
        $err_message = "Your username must contain 6 characters!";
    } elseif (preg_match('/\s/', $username)) {
        $err_message = "Your username cannot contain spaces!";
    } elseif (count($check_email) && $email != $my_email) {
        $err_message = "Already exist an account with this email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err_message = "Invalid email format!";
    } elseif (strlen($password) < 8 && !empty($password)) {
        $err_message = "The password cannot have less than 8 characters.";
    }else {
        $database->where('user_id', $user_id);
        $data = array("username" => $username, "email" => $email);
        if (!empty($profile_photo)) {
            $data["profile_photo"] = $profile_photo;
        }
        if (!empty($password)) {
            $data["password"] = md5($password);
        }
        $updateUser = $database->update("users", $data);
        if (!$updateUser) {
            $err_message = "Cannot update user id: $user_id! (contact admin)";
        }
    }

    echo $err_message;
?>