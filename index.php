<?php
    session_start();
    if ($_SESSION['logged'] != true || !isset($_SESSION['logged'])) {
        header("location: login.php");
        exit();
    }
    include("./utils/bootstrap.php");
    include("./utils/jquery.php");
    include("./utils/sweetAlert.php");
    include("./utils/fontAwesome.php");
    include("./uihandler.php");
    require("./php/api.php");
    $bootstrap = new Bootstrap();
    $jquery = new jquery_class();
    $sweetAlert = new sweet_alert();
    $api = new api();
    $ui = new UIHandler();
    $font_awesome = new fontAwesome();

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
        echo $font_awesome->fontAwesome();
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
    <?php echo $ui->nav($user['user_id']); ?>
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
        <?php if ($user['admin'] != 0 || $user['full_access'] != 0) { ?>
            &nbsp;
            <button class="btn btn-warning" type="button" id="delete_posts"><span style="color: white;">Delete Posts</span></button>
        <?php } ?>
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
            function createPost(description, username, post_id, post_user_id) {
                let post_delete_button = ``;
                let user_id = '<?php echo $user['user_id']; ?>';
                let admin = '<?php echo $user['admin']; ?>';
                let full_access = '<?php echo $user['full_access']; ?>';
                
                if (post_user_id == user_id || admin != 0 || full_access != 0) {
                    post_delete_button += `
                        <i class="fa fa-edit edit-post" data-post-id='${post_id}'"></i>
                        <i class="fa fa-trash delete-post" data-post-id='${post_id}'"></i>
                    `;
                }
                let post = `<div class="card post card-post-${post_id}" style="width: 18rem;">
                    <div class="card-body">
                        <span class="open_my_account" data-user-id="${post_user_id}" style="text-decoration: underline; cursor: pointer;">${username}</span> says: ${description}${post_delete_button}
                    </div>
                </div>`;
                return post;
            }
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
                                let html_posts = createPost(posts[i]['description'], posts[i]['username'], posts[i]['post_id'], posts[i]['user_id']);
                                $("#posts").append(html_posts);
                            }
                            initial_limit+=posts_length;
                        }
                    }
                });
            }
            let initial_limit = 0;
            $("#delete_posts").click(function() {
                $.ajax({
                    url: "./php/getAllPosts.php",
                    success: function (data) {
                        if (data == 0) {
                            sweetAlert("No posts to delete!", "error");
                        } else {
                            Swal.fire({
                                title: 'Are you sure you want to delete all posts?',
                                text: "You won't be able to revert this!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, delete them!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: "./php/deletePosts.php",
                                        success: function (data) {
                                            sweetAlert("The posts have been deleted!");
                                            $("#posts").html("");
                                        }
                                    });
                                }
                            });
                        }
                    }
                });
            });
            $("#refresh_posts").click(function() {
                initial_limit = 0;
                $("#posts").html("");
                refreshPosts();
            });
            refreshPosts();
            $("#description").keypress(function(e) {
                if (e.which == 13) {
                    $("#post").click();
                }
            });
            window.onscroll = function() {
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
                        setTimeout(function() {
                            $("#refresh_posts").click();
                        }, 1000);
                    }
                });
            });
            $(document).on("click", ".delete-post", function() {
                let post_id = $(this).data("post-id");
                Swal.fire({
                    title: 'Are you sure you want to delete this post?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete this post!'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                    }
                });
            });
            $("#post").click(function() {
                let banned = '<?=$user['banned'];?>';
                if (banned != 0) {
                    sweetAlert("You can not post something because you are banned!", "error");
                    return;
                }
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
                            $("#refresh_posts").click();
                            $("#description").val("");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>