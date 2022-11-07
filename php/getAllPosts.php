<?php
    require("../database.php");
    $database = new Database();
    $query = "SELECT COUNT(*) as count_posts FROM posts";
    $posts = $database->query($query);
    echo $posts[0]['count_posts'];
?>