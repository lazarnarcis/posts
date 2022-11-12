<?php
    require("../database.php");
    $database = new Database();
    $err_message = 1;
    $post_id = $_POST['post_id'];
    $description = $_POST['description_post'];
    
    $database->where('post_id', $post_id);
    $data = array("description" => $description);
    $updatePost = $database->update("posts", $data);
    if (!$updatePost) {
        $err_message = "Cannot update post id: $post_id! (contact admin)";
    }
    echo $err_message;
?>