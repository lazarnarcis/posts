<?php
    session_start();
    if ($_SESSION['logged'] != true || !isset($_SESSION['logged'])) {
        header("location: login.php");
        exit();
    }
    if (!isset($_GET['user_id'])) {
        header("location: index.php");
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

    $my_user_id = $_SESSION['user_id'];
    $profile_user_id = $_GET['user_id'];
    
    $my_account = $api->userInfo($my_user_id);
    $user = $api->userInfo($profile_user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $user['username']; ?>'s Profile</title>
    <?php 
        echo $jquery->jquery(); 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
    ?>
    <style>
        #profile_photo {
            cursor: pointer;
            height: 200px;
            width: 200px;
            border-radius: 50%;
            transition: .3s all;
            padding: 0;
        }
        #change_profile_photo {
            cursor: pointer;
        }
        #change_profile {
            margin: 0 25%;
        }
        #profile_photo_modal {
            width: 100%;
        }
        #profile_photo:hover {
            filter: brightness(0.6);
        }
        .ban-text {
            color: red;
            text-shadow: 0.5px 0.5px 0.5px black;
        }
        .btn:not(#submit) {
            margin-left: 5px;
            color: white;
        }
        @media only screen and (max-width: 600px) {
            #change_profile {
                margin: 0 25px;
            }
        }
        <?php echo $ui->styleNav(); ?>
    </style>
</head>
<body>
    <?php echo $ui->nav($my_account['user_id'], $user['user_id']); ?>
    <div class="text-center" style="margin-top: 15px;">
        <img alt="Profile photo" id="profile_photo" class="img-thumbnail">
        <h1 class="text-center"><?= $user['username']; ?></h1>
        <?php
            $user_id = $user['user_id'];
            if ($user['admin'] != 0) {
                echo '<button type="button" class="btn btn-info" disabled>Admin</button>';
            }
            if ($user['full_access'] != 0) {
                echo '<button type="button" class="btn btn-warning" disabled>Full Access</button>';
            }
            if ($my_account['admin'] != 0) {
                echo '<button type="button" class="btn btn-warning" id="show_logs">Logs</button>';
            }
            if ($my_account['admin'] != 0 && $user['full_access'] == 0 && $user['user_id'] != $my_account['user_id'] || $my_account['full_access'] != 0) {
                if ($user['banned'] == 0) {
                    echo '<button type="button" class="btn btn-danger" id="show_ban_modal">Ban</button>';
                } else {
                    echo '<button type="button" class="btn btn-danger" id="unban_user" data-user-id="'.$user_id.'">Unban</button>';
                }
            }
            if ($my_account['full_access'] != 0) {
                if ($user['admin'] == 0) {
                    echo '<button type="button" class="btn btn-warning" id="add_admin">Add Admin</button>';
                } elseif ($user['admin'] == 1) {
                    echo '<button type="button" class="btn btn-warning" id="remove_admin">Remove Admin</button>';
                }
            }
            $getBan = $api->getBan($user_id);
            if ($getBan) {
                $getUser = $api->userInfo($getBan['user_id']);
                $bannedIP = $api->userInfo($user_id);
                $banned_ip_text = NULL;
                if ($bannedIP["ban_ip"] == 1) {
                    $banned_ip_text .= " <span style='text-decoration: underline'>on IP</span>";
                }
                echo '<p class="text-center ban-text">'.$user['username'].' has been banned by '.$getUser['username'].$banned_ip_text.' at '.$getBan['created_at'].'. Reason: '.$getBan['reason'].'</p>';
            }
        ?>
    </div>
    <form id="change_profile">
        <input type="hidden" name="user_id" id="user_id" value="<?= $user['user_id']; ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id'] && $my_account['full_access'] == 0) echo "disabled"; ?> placeholder="Enter Username" value="<?= $user['username']; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id'] && $my_account['full_access'] == 0) echo "disabled"; ?> placeholder="Enter email" value="<?= $user['email']; ?>">
        </div>
        <?php if ($my_account['admin'] != 0 || $user['user_id'] == $my_account['user_id'] || $my_account['full_access'] != 0) { ?>
            <div class="form-group">
                <label for="change_profile_photo">Change Photo</label> 
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="change_profile_photo" name="change_profile_photo">
                    <label class="custom-file-label" for="change_profile_photo">Choose Profile Photo</label>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id'] && $my_account['full_access'] == 0) echo "disabled"; ?> placeholder="Enter Password">
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" id="submit">Update Data</button>
            </div>
        <?php } ?>
    </form>
    <div class="modal fade" id="img_thumbnail_modal" tabindex="-1" role="dialog" aria-labelledby="img_thumbnail_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="img_thumbnail_modal_label"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="user_logs_modal" tabindex="-1" role="dialog" aria-labelledby="user_logs_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="user_logs_modal_label"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ban_user_modal" tabindex="-1" role="dialog" aria-labelledby="ban_user_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ban_user_modal_label">Ban <?=$user['username'];?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form_user_modal">
                        <input type="hidden" name="user_id" value="<?=$my_account['user_id'];?>">
                        <input type="hidden" name="banned_user_id" value="<?=$user['user_id'];?>">
                        <div class="form-group">
                            <label for="ban_reason">Ban Reason</label>
                            <input type="text" class="form-control" name="ban_reason" id="ban_reason" placeholder="Ban Reason">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="ban_ip" id="ban_ip">
                            <label class="form-check-label" for="ban_ip">Ban IP (current user IP: <?=$user['ip']?>)</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submit_modify_form">Ban User</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let profile_base64 = "<?= $user['profile_photo']; ?>", profile_photo = "";
            $("#profile_photo").attr("src", profile_base64);
            let accepted_images = ["jpeg", "jpg", "png", "gif"];

            $("#checkbox_data_manage").on("change", function() {
                if ($(this).is(":checked")) {
                    showChangePhoto(true);
                } else {
                    showChangePhoto();
                }
            });

            $("#add_admin").click(function() {
                let username = '<?=$user['username']?>';
                let my_user_id = '<?=$my_account['user_id']?>';
                let user_id = '<?=$user['user_id']?>';

                Swal.fire({
                    title: 'Are you sure you want to add '+username+' as admin?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Add him as an admin!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "./php/addAdmin.php",
                            type: "POST",
                            data: {
                                user_id: user_id,
                                my_user_id: my_user_id
                            },
                            success: function (data) {
                                if (data == 1) {
                                    sweetAlert(username + ' is the new administrator!');
                                    setTimeout(() => window.location.reload(), 2500);
                                } else {
                                    sweetAlert(data, "error");
                                }
                            }
                        });
                    }
                });
            });

            $("#remove_admin").click(function() {
                let username = '<?=$user['username']?>';
                let my_user_id = '<?=$my_account['user_id']?>';
                let user_id = '<?=$user['user_id']?>';

                Swal.fire({
                    title: 'Are you sure you want to remove admin '+username+'?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove his admin role!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "./php/removeAdmin.php",
                            type: "POST",
                            data: {
                                user_id: user_id,
                                my_user_id: my_user_id
                            },
                            success: function (data) {
                                if (data == 1) {
                                    sweetAlert(username + ' no longer has the administrator role!');
                                    setTimeout(() => window.location.reload(), 2500);
                                } else {
                                    sweetAlert(data, "error");
                                }
                            }
                        });
                    }
                });
            });

            $("#show_logs").click(function() {
                let user_id = "<?php echo $user['user_id']; ?>";
                let username = "<?php echo $user['username']; ?>";
                let logs_length = 100;

                $.ajax({
                    url: "./php/getUserLogs.php",
                    type: "POST",
                    data: {
                        user_id: user_id,
                        logs_length: logs_length
                    },
                    success: function (data) {
                        data = JSON.parse(data);
                        let text = "";
                        for (let i = 0; i < data.length; i++) {
                            text+="[log id: " + data[i].id + "] " + data[i].text + " || " + data[i].created_at + " (IP: " + data[i].ip + ")<br>";
                        }
                        $("#user_logs_modal .modal-title").html(`${username}'s Logs (last ${logs_length} logs, current logs: ${data.length})`);
                        if (text == "") {
                            text = "No logs yet for " + username + "!";
                        }
                        let modal_body = text;
                        $("#user_logs_modal .modal-body").html(modal_body);
                        $("#user_logs_modal").modal("show");
                    }
                });
            });

            $("#show_ban_modal").click(function() {
                $("#ban_user_modal").modal("show");
            });

            $("#ban_reason").keypress(function(e) {
                if (e.which == 13) {
                    $("#submit_modify_form").click();
                }
            });
            
            $("#submit_modify_form").click(function() {
                let username = '<?=$user['username']?>';
                if ($("#ban_reason").val() == "") {
                    sweetAlert("You definitely have to give a reason!", "error");
                    return;
                }
                Swal.fire({
                    title: 'Are you sure you want to ban '+username+'?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, ban '+username+'!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "./php/banUser.php",
                            type: "POST",
                            data: $("#form_user_modal").serialize(),
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

            $("#unban_user").click(function() {
                let user_id = $(this).data("user-id");
                let my_user_id = '<?=$my_account['user_id'];?>';
                let username = '<?=$user['username']?>';
                Swal.fire({
                    title: 'Are you sure you want to unban '+username+'?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, unban '+username+'!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "./php/unbanUser.php",
                            type: "POST",
                            data: {
                                user_id: user_id,
                                my_user_id: my_user_id
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

            $(".img-thumbnail").click(function() {
                let img = $("<img id='profile_photo_modal'>");
                img.attr("src", $("#profile_photo").attr("src"));
                $("#img_thumbnail_modal .modal-body").html(img);
                let username = '<?php echo $user['username']; ?>';
                $("#img_thumbnail_modal .modal-title").html(username+"'s profile photo");
                $("#img_thumbnail_modal").modal("show");
            });

            $("#change_profile_photo").on("change", function() {
                var file = this.files[0];  
                var reader = new FileReader();  
                reader.onloadend = function() {  
                    profile_photo = reader.result;
                    let filename = $('#change_profile_photo').val().replace(/.*(\/|\\)/, '');
                    let ext = filename.split('.').pop();
                    ext = ext.toLowerCase();
                    if (accepted_images.includes(ext)) {
                        if (filename.length >= 25) {
                            filename = filename.substr(0,25) + "..." + ext;
                        }
                        $(".custom-file-label").text(filename);
                    } else {
                        $(".custom-file-label").text("Choose Profile Photo");
                        sweetAlert("The images we accept are: PNG, JPEG, JPG or GIF.", "error");
                    }
                }  
                reader.readAsDataURL(file);
            });

            $("#submit").click(function() {
                if ($("#username").val() == "" || $("#email").val() == "") {
                    sweetAlert("You must fill in all the data to proceed!", "error");
                    return;
                } else {
                    Swal.fire({
                        title: 'Are you sure you want to make changes to the account?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let form_data = {
                                user_id: $("#user_id").val(),
                                username: $("#username").val(),
                                email: $("#email").val(),
                                profile_photo: profile_photo,
                                password: $("#password").val(),
                                my_username: '<?=$user['username']?>',
                                my_email: '<?=$user['email']?>'
                            };
                            $.ajax({
                                url: "./php/modifyProfile.php",
                                type: "POST",
                                data: form_data,
                                success: function (data) {
                                    if (data == 1) {
                                        sweetAlert("Success!");
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1000);
                                    } else {
                                        sweetAlert(data, "error");
                                    }
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>