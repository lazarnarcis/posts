<?php
    require("../database.php");
    $database = new Database();
    $query = "SELECT posts.description, users.username, posts.created_at FROM posts LEFT JOIN users ON users.user_id=posts.user_id ORDER BY posts.post_id DESC";
    $posts = $database->query($query);
    
    echo json_encode($posts);
?>