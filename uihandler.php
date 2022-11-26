<?php
    class UIHandler {
        function nav($my_user_id = NULL, $user_id = NULL) {
            $current_page = basename($_SERVER['PHP_SELF']); // get current file name
            $home_active = ($current_page == "index.php") ? "active" : "";
            $profile_active = ($current_page == "profile.php" && $my_user_id == $user_id) ? "active" : "";

            return '<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <a class="navbar-brand" href="#">Posts</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item '.$home_active.'">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item '.$profile_active.'">
                            <a class="nav-link open_my_account" href="#" data-user-id="'.$my_user_id.'">My account</a>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-danger" id="logout">Logout</button>
                </div>
            </nav>';
        }
    }
?>