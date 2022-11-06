<?php
    require("../database.php");
    $database = new Database();
    $err_message = 1;
    $deletePosts = $database->deleteRow("posts");
    if (!$deletePosts) {
        $err_message = "Cannot delete posts! (contact admin)";
    }
    echo $err_message;
?>