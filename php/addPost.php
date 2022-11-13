<?php
    require("../database.php");
    $database = new Database();
    $description = NULL;
    if (isset($_POST['description'])) {
        $description = $_POST['description'];
    }
    $user_id = NULL;
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    }
    if (!isset($user_id) || !isset($description)) {
        return;
    }
    $data = array(
        "description" => $description,
        "user_id" => $user_id
    );
    $database->insert("posts", $data);
    $query = "SELECT * FROM posts WHERE description='".$description."' AND user_id='".$user_id."' LIMIT 1";
    $getPost = $database->query($query);
    echo json_encode($getPost);
?>