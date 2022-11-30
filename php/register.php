<?php
    session_start();
    require("../database.php");
    $database = new Database();
    $err_message = 1;
    
    $email = NULL;
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    $password = NULL;
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    }
    $username = NULL;
    if (isset($_POST['username'])) {
        $username = $_POST['username'];
    }
    $profile_photo = NULL;
    if (isset($_POST['profile_photo'])) {
        $profile_photo = $_POST['profile_photo'];
    }
    
    if (!isset($username) || !isset($password) || !isset($profile_photo) || !isset($email) || !isset($profile_photo)) {
        return;
    }

    $my_ip = $_SERVER['REMOTE_ADDR'];
    $select_banned = $database->query("SELECT * FROM users WHERE ban_ip=1 AND last_ip='".$my_ip."'");

    if (strlen($email) == 0 || strlen($password) == 0 || strlen($profile_photo) == 0 || strlen($username) == 0 || strlen($profile_photo) == 0) {
        $err_message = "Complete all the fields to register!";
    } elseif (preg_match('/\s/',$username)) {
        $err_message = "Your username cannot contain spaces!";
    } elseif (preg_match('/[A-Z]/', $username)) {
        $err_message = "Your username cannot contain uppercase letters!";
    } elseif (strlen($username) > 20) {
        $err_message = "Your username can contain a maximum of 20 characters!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err_message = "Invalid email format";
    } elseif (count($select_banned)) {
        $err_message = "You can't register because you are banned on IP!";
    } else {
        $query = "SELECT * FROM users WHERE email='".$email."' OR username='".$username."' LIMIT 1";
        $check_user = $database->query($query);
        if (count($check_user) == 0) {
            $data = array(
                "email" => $email,
                "password" => md5($password),
                "username" => $username,
                "profile_photo" => $profile_photo
            );
            $database->insert("users", $data);

            $database->where("email", $email);
            $user = $database->select("users", 1);
            $user = $user[0];
            $user_id = $user['user_id'];
            $_SESSION['logged'] = true;
            $_SESSION['user_id'] = $user_id;
            $ip = $_SERVER['REMOTE_ADDR'];

            $data = array("last_ip" => $ip, "ip" => $ip);
            $database->where("user_id", $user_id);
            $database->update("users", $data);

            $data = array(
                "user_id" => $user_id,
                "ip" => $ip,
                "text" => $user['username'] . " just created an account!"
            );
            $database->insert("logs", $data);
        } else {
            $err_message = "There is already a user with this username or email!";
        }
    }

    echo $err_message;
?>