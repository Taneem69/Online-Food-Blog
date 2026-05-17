<?php
    session_start();
    require_once('../model/foodExperienceModel.php');

    if (!isset($_SESSION['user_id'])) {
        header("location: login.php");
        exit();
    }

    // Generate CSRF token
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
    }

    $id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $post = getSinglePost($id);

    if (!$post) {
        header("location: food_experience.php");
        exit();
    }

    if ($_SESSION['user_id'] != $post['user_id'] && $_SESSION['role'] != 'admin') {
        die("Unauthorized");
    }

    $error   = isset($_SESSION['error'])   ? $_SESSION['error']   : '';
    $success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
    unset($_SESSION['error'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="../asset/food_experience.css">
</head>
<body>

    <div class="navbar">
        <a href="../index.php">Home</a>
        <a href="food_experience.php">Food Experience</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Edit Post</h1>

        <?php if ($error): ?>
            <p class="msg-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <p id="msg" style="color:red;"></p>

        <form method="POST" action="../controller/updatePostCheck.php" onsubmit="return validate()">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">

            <label>Title *</label><br>
            <input type="text" name="title" id="title"
                   style="width:100%;padding:8px;margin-bottom:10px;"
                   value="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>"><br>

            <label>Content *</label><br>
            <textarea name="content" id="content"><?php echo htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>

            <label>Post Type *</label><br>
            <select name="post_type" style="padding:8px;margin-bottom:10px;">
                <option value="food"       <?php echo $post['post_type'] == 'food'       ? 'selected' : ''; ?>>Food</option>
                <option value="restaurant" <?php echo $post['post_type'] == 'restaurant' ? 'selected' : ''; ?>>Restaurant</option>
                <option value="both"       <?php echo $post['post_type'] == 'both'       ? 'selected' : ''; ?>>Both</option>
            </select><br><br>

            <input type="submit" value="Update Post">

        </form>
    </div>

    <script>
    function validate() {
        let title   = document.getElementById('title').value.trim();
        let content = document.getElementById('content').value.trim();
        let msg     = document.getElementById('msg');

        if (title === '') {
            msg.innerHTML = 'Title is required.';
            return false;
        }
        if (content === '') {
            msg.innerHTML = 'Content is required.';
            return false;
        }
        return true;
    }
    </script>

</body>
</html>