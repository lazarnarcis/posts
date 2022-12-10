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

    $user = $api->userInfo($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <?php 
        echo $jquery->jquery(); 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
    ?>
    <style>
        #main_div {
            margin: 0 200px;
        }
        .list-group > a {
            display: flex;
            align-items: left;
        }
        @media only screen and (max-width: 600px) {
            #main_div {
                margin: 0 25px;
            }
        }
    </style>
</head>
<body>
    <?php echo $ui->nav($user['user_id']); ?>
    <h1 class="text-center">Search</h1>
    <div id="main_div">
        <label for="username">Username or email (or IP if you admin)</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">@</span>
            </div>
            <input type="text" class="form-control" id="username" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
        </div>
        <div id="users"></div>
    </div>
    <script>
        $(document).ready(function() {
            $("#username").on("input", function(e) {
                let admin = '<?=$user['admin']?>';
                let full_access = '<?=$user['full_access']?>';
                let max_users = 100;

                $.ajax({
                    url: "./php/searchUser.php",
                    type: "POST",
                    data: {
                        username: $("#username").val(),
                        admin: admin,
                        full_access: full_access,
                        max_users: max_users
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        let html_text = `<div class="list-group">`;
                        $("#users").html("");
                        if (data.length == 0) {
                            html_text += `<a href="#" class="list-group-item list-group-item-action">No users</a>`;
                        }
                        for (let i = 0; i < data.length; i++) {
                            html_text += `<a href="#" class="list-group-item list-group-item-action open_my_account" data-user-id="${data[i].user_id}">${data[i].username}</a>`;
                        }
                        html_text += "</div>";
                        $("#users").html(html_text);
                    }
                });
            });
        });
    </script>
</body>
</html>