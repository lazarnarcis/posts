<?php
    require("../database.php");
    $database = new Database();

    $start = NULL;
    if (isset($_POST['start'])) {
        $start = $_POST['start'];
    }
    $limit = NULL;
    if (isset($_POST['limit'])) {
        $limit = $_POST['limit'];
    }

    if (!isset($limit) || !isset($start)) {
        return;
    }
    $query = "SELECT posts.description, users.username, posts.created_at, posts.post_id, posts.user_id, users.profile_photo FROM posts LEFT JOIN users ON users.user_id=posts.user_id ORDER BY posts.post_id DESC LIMIT $start, $limit";
    $posts = $database->query($query);
    echo json_encode($posts);
?>