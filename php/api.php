<?php
    $dirname = dirname(__FILE__);
    require($dirname."/../database.php");
    $database = new Database();

    class api {
        function userInfo($user_id) {
            $GLOBALS['database']->where("user_id", $user_id);
            $user_info = $GLOBALS['database']->select("users", 1);
            return $user_info[0];
        }
    }
?>