<?php
    session_start();
    $err_message = 1;
    session_reset();
    session_destroy();
    echo $err_message;
    exit();
?>