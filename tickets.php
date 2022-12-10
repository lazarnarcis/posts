<?php
    session_start();
    if ($_SESSION['logged'] != true || !isset($_SESSION['logged'])) {
        header("location: login.php");
        exit();
    }

    include("./utils/bootstrap.php");
    include("./utils/jquery.php");
    include("./utils/sweetAlert.php");
    include("./uihandler.php");
    require("./php/api.php");
    $bootstrap = new Bootstrap();
    $jquery = new jquery_class();
    $sweetAlert = new sweet_alert();
    $api = new api();
    $ui = new UIHandler();

    $user_id = $_SESSION['user_id'];
    $my_account = $api->userInfo($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets</title>
    <?php 
        echo $jquery->jquery(); 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
    ?>
    <style>
        .card {
            margin: 0 25%;
        }
        @media only screen and (max-width: 600px) {
            .card {
                margin: 0 25px;
            }
        }
    </style>
</head>
<body>
    <?php echo $ui->nav($my_account['user_id']); ?>
    <div class="card">
        <div class="card-header">
            Online Admins
        </div>
        <div class="card-body">
            <ul class="list-group"></ul>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            getOnlineAdmins();
            function getOnlineAdmins() {
                $(".list-group").html("");
                $.ajax({
                    url: "./php/getOnlineAdmins.php",
                    success: function (data) {
                        data = JSON.parse(data);
                        for (let i = 0; i < data.length; i++) {
                            let user = data[i], text = "";
                            if (user.admin != 0) {
                                text += ` [ADMIN]`;
                            }
                            if (user.full_access != 0) {
                                text += ` [FULL ACCESS]`;
                            }                 

                            $(".list-group").append(`<li class="list-group-item">${user.username + text}</li>`);
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>