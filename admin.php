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
    <title>Admin Panel</title>
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
        .list-group-item-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        <?php echo $ui->styleNav(); ?>
    </style>
</head>
<body>
    <?php echo $ui->nav($user['user_id']); ?>
    <h1 class="text-center">Admin Panel</h1>
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <button type="button" class="btn btn-success" id="user_accounts">New Accounts</button>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="new_accounts_modal" tabindex="-1" role="dialog" aria-labelledby="new_accounts_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="new_accounts_modal_label">New accounts (last 100)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="users"></div>
                </div>
                <div class="modal-footer">
                    <!-- <?php
                        if ($my_account['full_access'] != 0) {
                            echo '<button type="button" class="btn btn-danger" id="delete_logs">Delete Logs</button>';
                        }
                    ?> -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#user_accounts").click(function() {
                $("#new_accounts_modal").modal("show");
                $.ajax({
                    url: "./php/getLastAccounts.php",
                    type: "POST",
                    data: {
                        max_users: 100
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        let html_text = `<div class="list-group">`;
                        $("#users").html("");
                        if (data.length == 0) {
                            html_text += `<a href="#" class="list-group-item list-group-item-action">No users</a>`;
                        }
                        for (let i = 0; i < data.length; i++) {
                            html_text += `<a href="#" class="list-group-item list-group-item-action open_my_account" data-user-id="${data[i].user_id}"><span>${data[i].username} (user id: ${data[i].user_id})</span><span>created at: ${data[i].created_at}</span></a>`;
                        }
                        html_text += "</div>";
                        $("#users").html(html_text);
                    }
                })
            });
        });
    </script>
</body>
</html>