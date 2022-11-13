<?php
    require("../database.php");
    $database = new Database();
    $err_message = 1;
    $post_id = NULL;
    if (isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];
    }
    if (!isset($post_id)) {
        return;
    }
    $database->where("post_id", $post_id);
    $deletePost = $database->deleteRow("posts");
    if (!$deletePost) {
        $err_message = "Cannot delete post id: $post_id! (contact admin)";
    }
    echo $err_message;
?>