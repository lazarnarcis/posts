<?php
    require("../database.php");
    $database = new Database();

    $max_users = NULL;
    if (isset($_POST['max_users'])) {
        $max_users = $_POST['max_users'];
    }

    if (!isset($max_users)) {
        return;
    }
    $query = "select bans.ban_id, bans.created_at, bans.reason, user1.user_id, user1.username as banned_user, user2.username from bans left join users as user1 on user1.user_id=bans.banned_user_id left join users as user2 on user2.user_id=bans.user_id order by created_at desc limit $max_users;";
    $users = $database->query($query);
    echo json_encode($users);
?>