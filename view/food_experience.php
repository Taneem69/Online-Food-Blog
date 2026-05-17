<?php

    session_start();

    require_once('../model/foodExperienceModel.php');

    $posts = getAllFoodPosts();

    $success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
    $error   = isset($_SESSION['error'])   ? $_SESSION['error']   : '';
    unset($_SESSION['success'], $_SESSION['error']);

?>

<!DOCTYPE html>
<html>

<head>

    <link rel="stylesheet" href="../asset/food_experience.css">
    <title>Food Experience</title>

</head>

<body>

    <div class="navbar">
        

        <a href="../index.php">Home</a>
        <a href="food_experience.php">Food Experience</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="add_post.php">Add Post</a>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="admin_panel.php">Admin Panel</a>
            <?php endif; ?>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>

    </div>

    <div class="container">
            
        <?php if ($success): ?>
        <p class="msg-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="msg-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        
        <h1>Food Experience Blog</h1>
        
        <?php if (empty($posts)): ?>
            <p>No posts yet. Be the first to share!</p>
        <?php endif; ?>
        
        <?php foreach ($posts as $post): ?>
        
            <div class="post" id="post-card-<?php echo $post['id']; ?>">
        
                <h2><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
        
                <p><?php echo nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?></p>
        
                <!-- ── Fixed: no more nested <p> tags ── -->
                <p>
                    <b>Author:</b> <?php echo htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8'); ?> |
                    <b>Type:</b> <?php echo htmlspecialchars($post['post_type'], ENT_QUOTES, 'UTF-8'); ?> |
                    <b>Date:</b> <?php echo $post['created_at']; ?>
                </p>
        
                <?php if (isset($_SESSION['user_id']) && ($post['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] == 'admin')): ?>
                    <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a> |
                    <a href="#" onclick="deletePost(<?php echo $post['id']; ?>)">Delete</a>
                <?php endif; ?>
                
                <hr>
                
                <h3>Comments</h3>
                
                <?php
                    $comments = getCommentsByPost($post['id']);
                    foreach ($comments as $comment):
                ?>
    
                    <p>
                        <b><?php echo htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8'); ?></b>:
                        <?php echo htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8'); ?>
                    
                        <?php if (isset($_SESSION['user_id']) && ($comment['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] == 'admin')): ?>
                            <a href="#" onclick="deleteComment(<?php echo $comment['id']; ?>)">Delete</a>
                        <?php endif; ?>
                    </p>
                        
                <?php endforeach; ?>
                        
                <?php if (isset($_SESSION['user_id'])): ?>
                    <textarea id="comment<?php echo $post['id']; ?>" placeholder="Write a comment..."></textarea>
                    <br><br>
                    <button onclick="addComment(<?php echo $post['id']; ?>)">Comment</button>
                <?php endif; ?>
                
            </div>
                
        <?php endforeach; ?>
                
    </div>

    <script src="../asset/food_experience.js"></script>

</body>
</html>