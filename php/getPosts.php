<?php
    require("../database.php");
    $database = new Database();
    $start = $_POST['start'];
    $limit = $_POST['limit'];
    $query = "SELECT posts.description, users.username, posts.created_at, posts.post_id, posts.user_id FROM posts LEFT JOIN users ON users.user_id=posts.user_id ORDER BY posts.post_id DESC LIMIT $start, $limit";
    $posts = $database->query($query);
    echo json_encode($posts);
?>