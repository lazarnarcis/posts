<?php
    session_start();
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
        header("location: index.php");
        exit();
    }
    include("./utils/bootstrap.php");
    include("./utils/jquery.php");
    include("./utils/sweetAlert.php");
    $bootstrap = new Bootstrap();
    $jquery = new jQuery();
    $sweetAlert = new sweetAlert();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <?php 
        echo $jquery->jquery(); 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
    ?>
    <style>
        #register_form {
            margin: 0 25%;
        }
        #profile_photo {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Register Form</h1>
        <form id="register_form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="profile_photo">Profile Photo</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="profile_photo" name="profile_photo">
                    <label class="custom-file-label" for="profile_photo">Choose Profile Photo</label>
                    <div class="invalid-feedback">Example invalid custom file feedback</div>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="submit">Register</button>
            </div>
            <p class="text-center">Do you have already an account? <a href="login.php" class="btn btn-danger">Log in</a></p>
        </form>
        <img id="demo"></img>
    </div>
    <script>
        $(document).ready(function() {
            let profile_photo = "";
            $("#profile_photo").on("change", function() {
                var file = this.files[0];  
                var reader = new FileReader();  
                reader.onloadend = function() {  
                    profile_photo = reader.result;
                    let filename = $('#profile_photo').val().replace(/.*(\/|\\)/, '');
                    if (filename.length >= 25) {
                        filename = filename.substr(0,25) + "...";
                    }
                    $(".custom-file-label").text(filename);
                }  
                reader.readAsDataURL(file);
            });

            $("#submit").click(function() {
                $.ajax({
                    url: "./php/register.php",
                    type: "POST",
                    data: {
                        username: $("#username").val(),
                        email: $("#email").val(),
                        password: $("#password").val(),
                        profile_photo: profile_photo
                    },
                    success: function (data) {
                        if (data != 1) {
                            sweetAlert(data, "error");
                        } else {
                            window.location = "index.php";
                        }
                    }
                });
            });
            $("#email, #password, #username").keypress(function(e) {
                if (e.which == 13) {
                    $("#submit").click();
                }
            });
        });
    </script>
</body>
</html>