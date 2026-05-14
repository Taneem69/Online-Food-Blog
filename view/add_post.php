<?php

    session_start();

    if(!isset($_SESSION['user_id'])){
        header("location: login.php");
        exit();
    }

    require_once('../config/database.php');
        global $conn;


    $restaurants = mysqli_query($conn,
    "SELECT * FROM restaurants");

    $menuItems = mysqli_query($conn,
    "SELECT * FROM menu_items");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Post</title>
</head>
<body>

    <h1>Add Food Experience Post</h1>

    <form method="POST" action="../controller/addPostCheck.php" onsubmit="return validate()">

        <input type="text" name="title" id="title" placeholder="Title">

        <br><br>

        <textarea name="content" id="content"></textarea>

        <br><br>

        <select name="post_type">
            <option value="food">Food</option>
            <option value="restaurant">Restaurant</option>
            <option value="both">Both</option>
        </select>

        <br><br>

        <select name="restaurant_id">
            <option value="">Select Restaurant</option>

            <?php while($row = mysqli_fetch_assoc($restaurants)){ ?>

            <option value="<?= $row['id'] ?>">
                <?= htmlspecialchars($row['name']) ?>
            </option>

            <?php } ?>

        </select>

        <br><br>

        <select name="menu_item_id">
            <option value="">Select Menu Item</option>

            <?php while($row=mysqli_fetch_assoc($menuItems)){ ?>

            <option value="<?= $row['id'] ?>">
                <?= htmlspecialchars($row['name']) ?>
            </option>

            <?php } ?>

        </select>

        <br><br>

        <input type="submit" value="Post">

    </form>

    <p id="msg"></p>

    <script>

        function validate()
        {
            let title = document.getElementById('title').value;
            let content = document.getElementById('content').value;

            if(title.trim()=="" || content.trim()==""){
                document.getElementById('msg').innerHTML =
                "All fields required";

                return false;
            }

            return true;
        }

    </script>

</body>
</html>
