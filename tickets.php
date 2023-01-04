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
    <h1 class="text-center">Tickets</h1>
    <div class="card">
        <div class="card-header">
            Online Admins
        </div>
        <div class="card-body">
            <ul class="list-group list-admins"></ul>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            Your tickets
        </div>
        <div class="card-body">
            <ul class="list-group list-tickets"></ul>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            getOnlineAdmins();
            function getOnlineAdmins() {
                $(".list-admins").html("");
                $.ajax({
                    url: "./php/getOnlineAdmins.php",
                    success: function (data) {
                        data = JSON.parse(data);
                        for (let i = 0; i < data.length; i++) {
                            let user = data[i], text = "";
                            let user_id = user.user_id;
                            if (user.admin != 0) {
                                text += ` [ADMIN]`;
                            }
                            if (user.full_access != 0) {
                                text += ` [FULL ACCESS]`;
                            }                 

                            $(".list-admins").append(`<li class="list-group-item">${user.username + text} <a href="profile.php?user_id=${user_id}">View profile &#8629;</a></li>`);
                        }
                    }
                });
            }
            getUserTickets();
            function getUserTickets() {
                $(".list-tickets").html("");
                $.ajax({
                    type: "POST",
                    data: {
                        user_id: <?=$user_id?>
                    },
                    url: "./php/getUserTickets.php",
                    success: function (data) {
                        data = JSON.parse(data);
                        for (let i = 0; i < data.length; i++) {
                            let ticket = data[i];       
                            let ticket_id = ticket.id; 
                            let text = ticket.text; 
                            let flags = ticket.flags;
                            let created_at = ticket.created_at;
                            let username = ticket.username;
                            let user_id = ticket.user_id;
                            flags = flags.split(",");
                            let flags_text = "";
                            for (let i = 0; i < flags.length; i++) {
                                let button_class = "";
                                if (flags[i] == "Bug") {
                                    button_class = "btn-danger";
                                } else if (flags[i] == "Report Problem") {
                                    button_class = "btn-warning";
                                } else if (flags[i] == "Question") {
                                    button_class = "btn-info";
                                } else if (flags[i] == "Enhacement") {
                                    button_class = "btn-light";
                                } else {
                                    button_class = "btn-dark";
                                }

                                flags_text += `<button type="button" style="margin-left: 5px;" class="btn ${button_class} disabled">${flags[i]}</button>`;
                            }
                            let user_link = `<a href="profile.php?user_id=${user_id}">${username}</a>`;

                            $(".list-tickets").append(`
                                <li class="list-group-item">
                                    <div class="d-flex align-center justify-content-between">
                                        <div>
                                            <span>
                                                <p class="font-weight-bold">Ticket ID: #${ticket_id} (created by ${user_link})</p>
                                                ${text}
                                            </span> 
                                            <a href="view_ticket.php?ticket_id=${ticket_id}">View ticket &#8629;</a>
                                        </div>
                                        <div>
                                            ${flags_text}
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <span>Created at: ${created_at}</span>
                                    </div>
                                </li>
                            `);
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>