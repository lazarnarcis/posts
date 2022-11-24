<?php
    class sweet_alert {
        function sweetAlert() {
            $sweet_links = '';
            $sweet_links .= '<script src="./js/sweetalert.js"></script>';
            $sweet_links .= '
            <script>
                function sweetAlert(message, type = "success") {
                    if (type == "success") {
                        Swal.fire("Success!", message, type);
                    } else if (type == "error") {
                        Swal.fire("Oops..", message, type);
                    }
                }
            </script>';
            return $sweet_links;
        }
    }
?>