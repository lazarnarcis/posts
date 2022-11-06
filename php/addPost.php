<?php
    require("../database.php");
    $database = new Database();
    $description = $_POST['description'];
    $user_id = $_POST['user_id'];
    $data = array(
        "description" => $description,
        "user_id" => $user_id
    );
    $database->insert("posts", $data);
    $query = "SELECT * FROM posts WHERE description='".$description."' AND user_id='".$user_id."' LIMIT 1";
    $getPost = $database->query($query);
    echo json_encode($getPost);
?>