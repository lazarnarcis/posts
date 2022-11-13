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
        echo $jquery->jquery(); 
        echo $bootstrap->bootstrap4Css();
        echo $bootstrap->bootstrap4JS();
        echo $sweetAlert->sweetAlert();
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
    <div class="modal fade" id="edit_post_modal" tabindex="-1" role="dialog" aria-labelledby="edit_post_modal_label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_post_modal_label"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit_modify_form">Save changes</button>
            </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let initial_limit = 0;
            $("#delete_posts").click(function() {
                let admin = '<?php echo $user['admin']; ?>';
                if (admin != 1) {
                    sweetAlert("You don't have admin role!", "error");
                } else {
                    $.ajax({
                        url: "./php/getAllPosts.php",
                        success: function (data) {
                            if (data == 0) {
                                sweetAlert("No posts to delete!", "error");
                            } else {
                                $.ajax({
                                    url: "./php/deletePosts.php",
                                    success: function (data) {
                                        sweetAlert("The posts have been deleted!");
                                        $("#posts").html("");
                                    }
                                });
                            }
                        }
                    });
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
                                let html_posts = createPost(posts[i]['description'], posts[i]['created_at'], posts[i]['username'], posts[i]['post_id'], posts[i]['user_id']);
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
            $(document).on("click", ".edit-post", function(){
                $("#edit_post_modal").modal("show");
                let post_id = $(this).data("post-id");
                $.ajax({
                    url: "./php/getPost.php",
                    type: "POST",
                    data: {
                        post_id: post_id
                    },
                    success: function (posts) {
                        posts = JSON.parse(posts);
                        posts = posts[0];
                        let username = posts.username;
                        let post_id = posts.post_id;
                        let description = posts.description;
                        let created_at = posts.created_at;
                        let updated_at = posts.updated_at;

                        $("#edit_post_modal .modal-title").html(`Modify post id ${post_id} (created by: ${username})`);
                        let modal_body = `
                            <form id="modify_post">
                                <input type="hidden" name="post_id" value="${post_id}">
                                <div class="form-group">
                                    <label for="description_post">Post Description</label>
                                    <textarea class="form-control" name="description_post" id="description_post" rows="3">${description}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="created_at_post">Created At</label>
                                    <input class="form-control" disabled name="created_at_post" id="created_at_post" value="${created_at}">
                                </div>
                                <div class="form-group">
                                    <label for="last_modified_post">Last Modified</label>
                                    <input class="form-control" disabled name="last_modified_post" id="last_modified_post" value="${updated_at}">
                                </div>
                            </form>
                        `;
                        $("#edit_post_modal .modal-body").html(modal_body);
                    }
                });
            });
            $("#submit_modify_form").click(function() {
                if ($("#description_post").val() == "") {
                    sweetAlert("You must have a description!", "error");
                    return;
                }
                $.ajax({
                    url: "./php/modifyPost.php",
                    type: "POST",
                    data: $("#modify_post").serialize(),
                    success: function (data) {
                        $("#edit_post_modal").modal('hide');
                        sweetAlert("The post has been modified!");
                        setTimeout(() => window.location.reload(), 1000);
                    }
                });
            });
            $(document).on("click", ".delete-post", function() {
                let post_id = $(this).data("post-id");
                $.ajax({
                    url: "./php/deletePost.php",
                    type: "POST",
                    data: {
                        post_id: post_id
                    },
                    success: function (data) {
                        if (data == 1) {
                            $(`.card-post-${post_id}`).css("display", "none");
                            sweetAlert("The post has been deleted!");
                        } else {
                            sweetAlert(data, "error");
                        }
                    }
                });
            });
            function createPost(description, created_at, username, post_id, post_user_id) {
                let post_delete_button = ``;
                let user_id = '<?php echo $user['user_id']; ?>';
                let admin = '<?php echo $user['admin']; ?>';
                if (post_user_id == user_id || admin == 1) {
                    post_delete_button += `
                        <button type="button" class="btn btn-warning delete-post" data-post-id='${post_id}'>Delete post</button>
                        <button type="button" class="btn btn-danger edit-post" data-post-id='${post_id}'>Edit post</button>
                    `;
                }
                let post = `<div class="card post card-post-${post_id}" style="width: 18rem;">
                    <div class="card-body">
                    <p class="card-text">${description}</p>
                    <p class="card-text">${created_at}</p>
                    ${post_delete_button}
                    </div>
                </div>`;
                return post;
            }
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
                                        let html_post = createPost(posts['description'], posts['created_at'], posts['username'], posts['post_id'], posts['user_id']);
                                        $("#posts").prepend(html_post);
                                        initial_limit+=1;
                                    }
                                });
                                $("#description").val("");
                            }
                        }
                    });
                } else {
                    sweetAlert("You need a description to create a post!", "error");
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
                            sweetAlert(data, "error");
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>