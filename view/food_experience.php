<?php

    session_start();

    require_once('../model/foodExperienceModel.php');

    $posts = getAllFoodPosts();

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

        <?php if(isset($_SESSION['user_id'])){ ?>

        <a href="add_post.php">Add Post</a>

        <?php } ?>

        <?php if(isset($_SESSION['role']) && $_SESSION['role']=='admin'){ ?>

            <a href="admin_panel.php">Admin Panel</a>

        <?php } ?>

    </div>

    <div class="container">

        <h1>Food Experience Blog</h1>

        <?php foreach($posts as $post){ ?>

    <div class="post">

    <h2>
        <?= htmlspecialchars($post['title']) ?>
    </h2>

    <p>
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </p>

    <p>

        <b>Author:</b>
        <?= htmlspecialchars($post['name']) ?> |

        <b>Type:</b>
        <p>
            <b>Date:</b>
            <?= $post['created_at'] ?>
        </p>
        <?= htmlspecialchars($post['post_type']) ?>

    </p>

    <?php

        if(isset($_SESSION['user_id']) && ($_SESSION['user_id']==$post['user_id'] || $_SESSION['role']=='admin' ) ){

    ?>

    <a href="edit_post.php?id=<?= $post['id'] ?>">Edit</a>|

    <a href="#" onclick="deletePost(<?= $post['id'] ?>)"> Delete </a>

    <?php } ?>

    <hr>

    <h3>Comments</h3>

    <?php

        $comments = getCommentsByPost($post['id']);

        foreach($comments as $comment){

    ?>

    <p>

        <b><?= htmlspecialchars($comment['name']) ?></b>

        :<?= htmlspecialchars($comment['comment']) ?>

        <?php

            if(isset($_SESSION['user_id']) && ($_SESSION['user_id']==$comment['user_id'] || $_SESSION['role']=='admin' ) ){

        ?>

        <a href="#" onclick="deleteComment(<?= $comment['id'] ?>)"> Delete </a>

        <?php } ?>

    </p>

    <?php } ?>

    <?php if(isset($_SESSION['user_id'])){ ?>

    <textarea id="comment<?= $post['id'] ?>"></textarea>

    <br><br>

    <button onclick="addComment(<?= $post['id'] ?>)">Comment</button>

    <?php } ?>

    </div>

        <?php } ?>

    </div>

    <script src="../asset/food_experience.js"></script>

</body>
</html>