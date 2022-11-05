<?php
    require("../database.php");
    $database = new Database();
    $description = $_POST['description'];
    $user_id = $_POST['user_id'];
    $err_message = 1;

    $data = array(
        "description" => $description,
        "user_id" => $user_id
    );
    $addPost = $database->insert("posts", $data);

    if (!$addPost) {
        $err_message = "Cannot add post! (contact admin)";
    }

    echo $err_message;
?>