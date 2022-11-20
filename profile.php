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
    require("./php/api.php");
    $bootstrap = new Bootstrap();
    $jquery = new jQuery();
    $sweetAlert = new sweetAlert();
    $api = new api();

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
            height: 200px;
            width: 200px;
            border-radius: 50%;
        }
        #change_profile_photo {
            cursor: pointer;
        }
        #change_profile {
            margin: 0 25%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Facebook</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#" data-user-id="<?php echo $my_account['user_id']; ?>" id="open_my_account">My account</a>
                </li>
            </ul>
            <button type="button" class="btn btn-danger" id="logout">Logout</button>
        </div>
    </nav>
    <div class="text-center" style="margin-top: 15px;">
        <img alt="Profile photo" id="profile_photo" class="img-thumbnail">
        <h1 class="text-center"><?= $user['username']; ?></h1>
    </div>
    <form id="change_profile">
        <input type="hidden" name="user_id" id="user_id" value="<?= $user['user_id']; ?>">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> placeholder="Enter Username" value="<?= $user['username']; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> placeholder="Enter email" value="<?= $user['email']; ?>">
        </div>
        <?php if ($my_account['admin'] != 0 || $user['user_id'] == $my_account['user_id']) { ?>
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