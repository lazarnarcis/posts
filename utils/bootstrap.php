<?php
    class Bootstrap {
        function bootstrap4Css() {
            $bootstrap_links = "";
            $bootstrap_links .= '<link rel="stylesheet" href="./css/bootstrap.css" crossorigin="anonymous">';
            return $bootstrap_links;
        }
    
        function bootstrap4JS() {
            $bootstrap_links = "";
            $bootstrap_links .= '<script src="./js/bootstrap_popper.js" crossorigin="anonymous"></script>';
            $bootstrap_links .= '<script src="./js/bootstrap_min.js" crossorigin="anonymous"></script>';
            return $bootstrap_links;
        }
    }
?>