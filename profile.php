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
    $jquery = new jQuery();
    $sweetAlert = new sweetAlert();
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
                echo '<button type="button" class="btn btn-info" style="color: black;" disabled>Admin</button>';
            }
            if ($user['full_access'] != 0) {
                echo '<button type="button" class="btn btn-warning" style="color: black;" disabled>Full Access</button>';
            }
            if ($my_account['admin'] != 0 && $user['user_id'] != $my_account['user_id'] || $my_account['full_access'] != 0) {
                if ($user['banned'] == 0) {
                    echo '<button type="button" class="btn btn-warning" id="show_ban_modal">Ban</button>';
                } else {
                    echo '<button type="button" class="btn btn-warning" id="unban_user" data-user-id="'.$user_id.'">Unban</button>';
                }
            }
            $getBan = $api->getBan($user_id);
            if ($getBan) {
                $getUser = $api->userInfo($getBan['user_id']);
                echo '<p class="text-center ban-text">'.$user['username'].' has been banned by '.$getUser['username'].' at '.$getBan['created_at'].'. Reason: '.$getBan['reason'].'</p>';
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
                <label for="checkbox_photo">Change Photo</label> 
                <input type="checkbox" id="checkbox_photo">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="change_profile_photo" name="change_profile_photo">
                    <label class="custom-file-label" for="change_profile_photo">Choose Profile Photo</label>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" id="submit">Update</button>
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
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submit_modify_form">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let profile_base64 = "<?= $user['profile_photo']; ?>", condition_photo, profile_photo = "";
            $("#profile_photo").attr("src", profile_base64);
            let accepted_images = ["jpeg", "jpg", "png", "gif"];

            showChangePhoto();
            function showChangePhoto(condition = false) {
                if (condition == false) {
                    $(".custom-file").hide();
                    condition_photo = "no";
                } else {
                    $(".custom-file").show();
                    condition_photo = "yes";
                }
            }
            $("#checkbox_photo").on("change", function() {
                if ($(this).is(":checked")) {
                    showChangePhoto(true);
                } else {
                    showChangePhoto();
                }
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
                if ($("#ban_reason").val() == "") {
                    sweetAlert("You definitely have to give a reason!", "error");
                    return;
                }
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
            });

            $("#unban_user").click(function() {
                let user_id = $(this).data("user-id");
                $.ajax({
                    url: "./php/unbanUser.php",
                    type: "POST",
                    data: {
                        user_id: user_id
                    },
                    success: function (data) {
                        if (data == 1) {
                            window.location.reload();
                        } else {
                            sweetAlert(data, "error");
                        }
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
                    let form_data = {
                        user_id: $("#user_id").val(),
                        username: $("#username").val(),
                        email: $("#email").val(),
                        profile_photo: profile_photo,
                        change_photo: condition_photo
                    };
                    $.ajax({
                        url: "./php/modifyProfile.php",
                        type: "POST",
                        data: form_data,
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
            $("#open_my_account").click(function() {
                let user_id = $(this).data('user-id');
                window.location = "profile.php?user_id=" + user_id;
            });
            $("#logout").click(function() {
                $.ajax({
                    url: "./php/logout.php",
                    type: "POST",
                    success: function (data) {
                        if (data == 1) {
                            window.location = "login.php";
                        } else {
                            sweetAlert(data, "error");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>