<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("location: login.php");
        exit();
    }

    require_once('../model/foodExperienceModel.php');

    $restaurants = getAllRestaurantsForForm();
    $menu_items  = getAllMenuItemsForForm();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Post</title>
    <link rel="stylesheet" href="../asset/food_experience.css">
</head>
<body>

    <div class="navbar">
        <a href="../index.php">Home</a>
        <a href="food_experience.php">Food Experience</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Add Food Experience Post</h1>
        <p id="msg" style="color:red;"></p>

        <form method="POST" action="../controller/addPostCheck.php" onsubmit="return validatePost()">

            <label>Title *</label><br>
            <input type="text" name="title" id="title" placeholder="Title"
                   style="width:100%;padding:8px;margin-bottom:10px;"><br>

            <label>Content *</label><br>
            <textarea name="content" id="content"
                      placeholder="Describe your experience..."></textarea><br><br>

            <label>Post Type *</label><br>
            <select name="post_type" style="padding:8px;margin-bottom:10px;">
                <option value="food">Food</option>
                <option value="restaurant">Restaurant</option>
                <option value="both">Both</option>
            </select><br><br>

            <label>Link to Restaurant (optional)</label><br>
            <select name="restaurant_id" style="padding:8px;margin-bottom:10px;">
                <option value="">-- None --</option>
                <?php foreach ($restaurants as $r): ?>
                    <option value="<?php echo $r['id']; ?>">
                        <?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label>Link to Menu Item (optional)</label><br>
            <select name="menu_item_id" style="padding:8px;margin-bottom:10px;">
                <option value="">-- None --</option>
                <?php foreach ($menu_items as $mi): ?>
                    <option value="<?php echo $mi['id']; ?>">
                        <?php echo htmlspecialchars($mi['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <input type="submit" value="Publish Post">

        </form>
    </div>

    <script>
        function validatePost() {
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
            if (content.length < 20) {
                msg.innerHTML = 'Content must be at least 20 characters.';
                return false;
            }
            return true;
        }
    </script>

</body>
</html>