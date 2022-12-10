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

        $my_ip = $_SERVER['REMOTE_ADDR'];
        $select_banned = $database->query("SELECT * FROM users WHERE ban_ip=1 AND last_ip='".$my_ip."'");
        
        if ($check_login) {
            $user = $check_login[0];
            if (md5($password) != $user['password']) {
                $err_message = "Please enter correct password!";
            } elseif (count($select_banned)) {
                $err_message = "You can't connect because you are banned on IP!";
            } else {
                $_SESSION['logged'] = true;
                $user_id = $user['user_id'];
                $_SESSION['user_id'] = $user_id;
                $ip = $_SERVER['REMOTE_ADDR'];

                $data = array("last_ip" => $ip, "online" => 1);
                $database->where("user_id", $user_id);
                $database->update("users", $data);

                $data = array(
                    "user_id" => $user_id,
                    "ip" => $ip,
                    "text" => $user['username'] . " just connected!"
                );
                $database->insert("logs", $data);
            }
        } else {
            $err_message = "Doesn't exist user with this email!";
        }
    }

    echo $err_message;
?>