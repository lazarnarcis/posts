<?php
    session_start();
    require("./api.php");
    $api = new api();
    $err_message = 1;
    $user = $api->userInfo($_SESSION['user_id']);

    if ($user['admin'] == 0) {
        return;
    }
    $deletePosts = $database->deleteRow("posts");
    if (!$deletePosts) {
        $err_message = "Cannot delete posts! (contact admin)";
    }
    echo $err_message;
?>