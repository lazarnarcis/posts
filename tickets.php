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
        p {
            margin: 0;
            padding: 0;
        }
        .list-group-item {
            color: black;
        }
        .list-group-item:hover {
            color: black;
            text-decoration: none;
            background-color: #ededed;
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
    <h1 class="text-center">Tickets <button type="button" class="btn btn-success" id="create_ticket">Create Ticket</button></h1>
    <div class="card">
        <div class="card-header">
            Online Admins
        </div>
        <div class="card-body">
            <div class="list-group list-admins"></div>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            Your tickets
        </div>
        <div class="card-body">
            <div class="list-group list-tickets"></div>
        </div>
    </div>
    <div class="modal fade" id="create_ticket_modal" tabindex="-1" role="dialog" aria-labelledby="create_ticket_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create_ticket_modal_label"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_create_ticket">
                        <input type="hidden" name="user_id" id="user_id" value="<?=$user_id;?>">
                        <div class="form-group">
                            <label for="ban_reason"><b>Ticket Title</b></label>
                            <input type="text" class="form-control" name="ticket_title" id="ticket_title" placeholder="Ticket Title">
                        </div>
                        <div class="form-group">
                            <label><b>Select the ticket category:</b></label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Bug" id="bug_flag" checked>
                                <label class="form-check-label" for="bug_flag">
                                    Bug
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Report Problem" id="report_flag">
                                <label class="form-check-label" for="report_flag">
                                    Report Problem
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Question" id="question_flag">
                                <label class="form-check-label" for="question_flag">
                                    Question
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Enhancement" id="enhancement_flag">
                                <label class="form-check-label" for="enhancement_flag">
                                    Enhancement
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submit_create_ticket">Create Ticket</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#submit_create_ticket").click(function() {
                let flags = [];
                let bug_flag = $("#bug_flag").val();
                if ($("#bug_flag").is(":checked")) {
                    flags.push(bug_flag);
                }
                let report_flag = $("#report_flag").val();
                if ($("#report_flag").is(":checked")) {
                    flags.push(report_flag);
                }
                let question_flag = $("#question_flag").val();
                if ($("#question_flag").is(":checked")) {
                    flags.push(question_flag);
                }
                let enhancement_flag = $("#enhancement_flag").val();
                if ($("#enhancement_flag").is(":checked")) {
                    flags.push(enhancement_flag);
                }
                if (flags.length > 2) {
                    sweetAlert("You cannot select more than 2 categories!", "error");
                    return;
                }
                flags = flags.toString();
                let ticket_title = $("#ticket_title").val();
                if (ticket_title == "") {
                    sweetAlert("You definitely have to give a ticket title!", "error");
                    return;
                }
                if (ticket_title.length < 10) {
                    sweetAlert("The title must have at least 10 characters!", "error");
                    return;
                }
                if (flags.length == 0) {
                    sweetAlert("You must select at least one category!", "error");
                    return;
                }
                Swal.fire({
                    title: 'Are you sure you want to create this ticket?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, create it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "./php/createTicket.php",
                            type: "POST",
                            data: {
                                flags: flags,
                                user_id: $("#user_id").val(),
                                ticket_title: $("#ticket_title").val()
                            },
                            success: function (data) {
                                if (data == 1) {
                                    window.location.reload();
                                } else {
                                    sweetAlert(data, "error");
                                }
                            }
                        });
                    }
                });
            });
            getOnlineAdmins();
            $("#create_ticket").click(function() {
                $.ajax({
                    url: "./php/getNewTicketID.php",
                    success: function (data) {
                        data = JSON.parse(data);
                        let new_ticket_id = Number(data)+1;
                        $("#create_ticket_modal_label").html("Create Ticket #" + new_ticket_id);
                        $("#create_ticket_modal").modal("show");
                    }
                });
            });
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

                            $(".list-admins").append(`
                                <a class="list-group-item list-group-item-action" href="profile.php?user_id=${user_id}">
                                    ${user.username + text}
                                </a>
                            `);
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
                                } else if (flags[i] == "Enhancement") {
                                    button_class = "btn-light";
                                } else {
                                    button_class = "btn-dark";
                                }

                                flags_text += `<button type="button" style="margin-left: 5px;" class="btn ${button_class} disabled">${flags[i]}</button>`;
                            }
                            let closed = ticket.closed;
                            flags_text += `<button type="button" style="margin-left: 5px;" class="btn ${closed == 0 ? "btn-success" : "btn-danger"} disabled">${closed == 0 ? "Opened" : "Closed"}</button>`;

                            $(".list-tickets").append(`
                                <a class="list-group-item" href="view_ticket.php?ticket_id=${ticket_id}">
                                    <span class="d-flex align-center justify-content-between">
                                        <span>
                                            <p class="font-weight-bold">Ticket ID: #${ticket_id} (created by ${username})</p>
                                            <p>${text}</p>
                                        </span>
                                        <span>
                                            ${flags_text}
                                        </span>
                                    </span>
                                    <p style="text-align: right;">
                                        Created at: ${created_at}
                                    </p>
                                </a>
                            `);
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>