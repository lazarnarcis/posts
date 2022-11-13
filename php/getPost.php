<?php
    require("../database.php");
    $database = new Database();
    $post_id = $_POST['post_id'];
    $query = "SELECT posts.description, posts.updated_at, users.username, posts.created_at, posts.post_id, posts.user_id FROM posts LEFT JOIN users ON users.user_id=posts.user_id WHERE posts.post_id='".$post_id."' ORDER BY posts.post_id DESC LIMIT 1";
    $posts = $database->query($query);
    echo json_encode($posts);
?>