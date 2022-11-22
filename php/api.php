<?php
    $dirname = dirname(__FILE__);
    require($dirname."/../database.php");
    $database = new Database();

    class api {
        function userInfo($user_id = NULL) {
            $GLOBALS['database']->where("user_id", $user_id);
            $user_info = $GLOBALS['database']->select("users", 1);
            return $user_info[0];
        }
        function getBan($user_id = NULL) {
            $GLOBALS['database']->where("banned_user_id", $user_id);
            $ban_info = $GLOBALS['database']->select("bans", 1);
            if ($ban_info) {
                return $ban_info[0];
            } else {
                return 0;
            }
        }
    }
?>