<?php
    require("../database.php");
    $database = new Database();
    $err_message = 1;
    $post_id = NULL;
    if (isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];
    }
    $description = NULL;
    if (isset($_POST['description_post'])) {
        $description = $_POST['description_post'];
    }
    if (!isset($description) || !isset($post_id)) {
        return;
    }
    
    $database->where('post_id', $post_id);
    $data = array("description" => $description);
    $updatePost = $database->update("posts", $data);
    if (!$updatePost) {
        $err_message = "Cannot update post id: $post_id! (contact admin)";
    }
    echo $err_message;
?>