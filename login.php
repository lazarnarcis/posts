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
    <title>Login</title>
    <?php 
        echo $jquery->jquery(); 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
    ?>
    <style>
        #login_form {
            margin: 0 25%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Login Form</h1>
        <form id="login_form">
            <div class="form-group">
                <label for="email">Email address or Username</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email or username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="submit">Login</button>
                <a href="register.php" class="btn btn-outline-success">Register</a>
            </div>
            <p class="text-center">Forgot your password? <a href="forgot_password.php" class="btn btn-danger">Recover it!</a></p>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            function sweetAlert(title, message, type = "success") {
                Swal.fire(title, message, type);
            }
            $("#submit").click(function() {
                $.ajax({
                    url: "./php/login.php",
                    type: "POST",
                    data: $("#login_form").serialize(),
                    success: function (data) {
                        if (data != 1) {
                            sweetAlert("Warning...", data, "error");
                        } else {
                            sweetAlert("Success!", "You are logged in now!");
                            setTimeout(() => {
                                window.location = "index.php";
                            }, 1000);
                        }
                    }
                });
            });
            $("#email, #password").keypress(function(e) {
                if (e.which == 13) {
                    $("#submit").click();
                }
            });
        });
    </script>
</body>
</html>