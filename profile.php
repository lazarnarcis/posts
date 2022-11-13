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
        <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
        <div class="form-group">
            <label for="email">Username</label>
            <input type="text" class="form-control" id="username" name="username" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> placeholder="Enter Username" value="<?= $user['username']; ?>">
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> placeholder="Enter email" value="<?= $user['email']; ?>">
        </div>
        <button type="button" class="btn btn-primary" <?php if ($my_account['admin'] == 0 && $user['user_id'] != $my_account['user_id']) echo "disabled"; ?> id="submit">Submit</button>
    </form>
    <script>
        $(document).ready(function() {
            $("#submit").click(function() {
                if ($("#username").val() == "" || $("#email").val() == "") {
                    sweetAlert("You must fill in all the data to proceed!", "error");
                    return;
                } else {
                    $.ajax({
                        url: "./php/modifyProfile.php",
                        type: "POST",
                        data: $("#change_profile").serialize(),
                        success: function () {
                            window.location.reload();
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