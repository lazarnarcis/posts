<?php
    session_start();
    if ($_SESSION['logged'] != true || !isset($_SESSION['logged'])) {
        header("location: login.php");
        exit();
    }
    include("./utils/bootstrap.php");
    include("./utils/jquery.php");
    include("./utils/sweetAlert.php");
    require("./php/api.php");
    $bootstrap = new Bootstrap();
    $jquery = new jQuery();
    $sweetAlert = new sweetAlert();
    $api = new api();

    $user = $api->userInfo($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <?php 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
        echo $jquery->jquery(); 
    ?>
    <style>
        #posts_form {
            margin: 0 17.5%;
        }
        .post {
            margin: 10px 0;
        }
        #posts {
            flex-direction: column;
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
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" disabled href="#">Welcome, <?php echo $user['username']; ?>!</a>
                </li>
            </ul>
            <button type="button" class="btn btn-danger" id="logout">Logout</button>
        </div>
    </nav>
    <h1 class="text-center">Feed</h1>
    <div class="container">
        <div class="input-group mb-3">
            <input type="text" id="description" class="form-control" placeholder="Post description">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="post">Post</button>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        <button class="btn btn-primary" type="button" id="refresh_posts">Refresh Posts</button>
        &nbsp;
        <button class="btn btn-warning" type="button" id="delete_posts"><span style="color: white;">Delete Posts</span></button>
    </div>
    <div id="posts" class="d-flex justify-content-center align-items-center"></div>
    <script>
        $(document).ready(function() {
            function sweetAlert(title, message, type = "success") {
                Swal.fire(title, message, type);
            }
            let initial_limit = 0;
            $("#delete_posts").click(function() {
                let admin = '<?php echo $user['admin']; ?>';
                if (admin != 1) {
                    sweetAlert("Oops..", "You don't have admin role!", "error");
                } else {
                    if (!$("#posts").html()) {
                        sweetAlert("Oops..", "No posts to delete!", "error");
                    } else {
                        $.ajax({
                            url: "./php/deletePosts.php",
                            success: function (data) {
                                sweetAlert("Success!", "The posts have been deleted!");
                                $("#posts").html("");
                            }
                        });
                    }
                }
            });
            $("#refresh_posts").click(function() {
                initial_limit = 0;
                $("#posts").html("");
                refreshPosts();
            });
            function refreshPosts() {
                $.ajax({
                    url: "./php/getPosts.php",
                    type: "POST",
                    data: {
                        start: initial_limit,
                        limit: 5
                    },
                    success: function (posts) {
                        posts = JSON.parse(posts);
                        console.log(posts);
                        let posts_length = posts.length;
                        if (posts_length != 0) {
                            for (let i = 0; i < posts.length; i++) {
                                let html_posts = `<div class="card post" style="width: 18rem;">
                                    <div class="card-body">
                                    <p class="card-text">`+posts[i]['description']+`</p>
                                    <p class="card-text">`+posts[i]['created_at']+`</p>
                                    <a href="#" class="btn btn-primary">`+posts[i]['username']+`</a>
                                    </div>
                                </div>`;
                                $("#posts").append(html_posts);
                            }
                            initial_limit+=posts_length;
                        }
                    }
                });
            }
            refreshPosts();
            $("#description").keypress(function(e) {
                if (e.which == 13) {
                    $("#post").click();
                }
            });
            window.onscroll = function(ev) {
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                    refreshPosts();
                }
            };
            $("#post").click(function() {
                let description = $("#description").val();
                if (description != "") {
                    $.ajax({
                        url: "./php/addPost.php",
                        type: "POST",
                        data: {
                            description: description,
                            user_id: <?php echo $user['user_id']; ?>
                        },
                        success: function (data) {
                            data = JSON.parse(data);
                            let post_id = data[0].post_id;
                            if (data.length) {
                                $.ajax({
                                    url: "./php/getPost.php",
                                    type: "POST",
                                    data: {
                                        post_id: post_id
                                    },
                                    success: function (posts) {
                                        posts = JSON.parse(posts);
                                        posts = posts[0];
                                        let html_posts = `<div class="card post" style="width: 18rem;">
                                            <div class="card-body">
                                            <p class="card-text">`+posts['description']+`</p>
                                            <p class="card-text">`+posts['created_at']+`</p>
                                            <a href="#" class="btn btn-primary">`+posts['username']+`</a>
                                            </div>
                                        </div>`;
                                        $("#posts").prepend(html_posts);
                                        initial_limit+=1;
                                    }
                                });
                                $("#description").val("");
                            }
                        }
                    });
                } else {
                    sweetAlert("Oops!..", "You need a description to create a post!", "error");
                }
            });
            $("#logout").click(function() {
                $.ajax({
                    url: "./php/logout.php",
                    type: "POST",
                    success: function (data) {
                        if (data == 1) {
                            window.location = "login.php";
                        } else {
                            sweetAlert("Warning...", data, "error");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>