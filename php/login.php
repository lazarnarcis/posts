<?php
    session_start();
    require("../database.php");
    $database = new Database();
    $email = NULL;
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    $password = NULL;
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    }
    if (!isset($password) || !isset($email)) {
        return;
    }
    $err_message = 1;

    if (strlen($email) == 0 || strlen($password) == 0) {
        $err_message = "Please complete with your email/password.";
    } else {
        $query = "SELECT * FROM users WHERE email='".$email."' OR username='".$email."' LIMIT 1";
        $check_login = $database->query($query);
        
        if ($check_login) {
            $user = $check_login[0];
            if (md5($password) != $user['password']) {
                $err_message = "Please enter correct password!";
            } else {
                $_SESSION['logged'] = true;
                $_SESSION['user_id'] = $user['user_id'];
            }
        } else {
            $err_message = "Doesn't exist user with this email!";
        }
    }

    echo $err_message;
?>