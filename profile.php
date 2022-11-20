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
        }
        .container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }   
        .container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
        }
        .container:hover input ~ .checkmark {
            background-color: #ccc;
        }
        .container input:checked ~ .checkmark {
            background-color: #2196F3;
        }
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        .container input:checked ~ .checkmark:after {
            display: block;
        }
        .container .checkmark:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
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
    <h1 class="text-center"><?= $user['username']; ?>'s profile</h1>
    <form id="change_profile">
        <img alt="Profile photo" id="profile_photo" class="img-thumbnail">
        <input type="hidden" name="user_id" id="user_id" value="<?= $user['user_id']; ?>">
        <div class="form-group">
            <label for="email">Username</label>
            <input type="text" class="form-control" id="username" name="username" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> placeholder="Enter Username" value="<?= $user['username']; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> placeholder="Enter email" value="<?= $user['email']; ?>">
        </div>
        <?php if ($my_account['admin'] != 0 || $user['user_id'] == $my_account['user_id']) { ?>
            <div class="form-group">
                <label class="container">
                    <input type="checkbox" id="checkbox_photo">
                    <span class="checkmark"></span>
                </label>
                <label for="change_profile_photo">Change Photo</label> 
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="change_profile_photo" name="change_profile_photo">
                    <label class="custom-file-label" for="change_profile_photo">Change Profile Photo</label>
                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                </div>
            </div>
        <?php } ?>
        <div class="form-group">
            <button type="button" class="btn btn-primary" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> id="submit">Submit</button>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            let profile_base64 = "<?=$user['profile_photo'];?>";
            let condition_photo, profile_photo = "";
            $("#profile_photo").attr("src", profile_base64);

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
                    if (filename.length >= 25) {
                        filename = filename.substr(0,25) + "..." + ext;
                    }
                    $(".custom-file-label").text(filename);
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