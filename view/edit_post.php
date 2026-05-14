<?php

    session_start();

    require_once('../model/foodExperienceModel.php');

    if(!isset($_SESSION['user_id'])){
        header("location: login.php");
        exit();
    }

    $id = $_GET['id'];

    $post = getSinglePost($id);

    if($_SESSION['user_id'] != $post['user_id'] && $_SESSION['role'] != 'admin'){
        die("Unauthorized");
    }

?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Post</title>
</head>

<body>

<h1>Edit Post</h1>

    <form method="POST" action="../controller/updatePostCheck.php"onsubmit="return validate()">

        <input type="hidden" name="id" value="<?= $post['id'] ?>">

        <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>">

        <br><br>

        <textarea name="content" id="content"><?= htmlspecialchars($post['content']) ?></textarea>

        <br><br>

        <select name="post_type">

            <option value="food" <?= ($post['post_type']=='food') ? 'selected' : '' ?>>Food</option>

            <option value="restaurant"<?= ($post['post_type']=='restaurant') ? 'selected' : '' ?>>Restaurant</option>

            <option value="both" <?= ($post['post_type']=='both') ? 'selected' : '' ?>>Both</option>

        </select>

        <br><br>

        <input type="submit" value="Update">

    </form>

    <p id="msg"></p>

    <script>

        function validate(){
            let title=document.getElementById('title').value;

            let content=document.getElementById('content').value;

            if(title.trim()=="" || content.trim()==""){

                document.getElementById('msg').innerHTML="All fields required";

                return false;
            }

            return true;
        }

    </script>

</body>
</html>