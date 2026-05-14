<?php

    session_start();

    if($_SESSION['role'] != 'admin'){
        die("Unauthorized");
    }

    require_once('../model/adminModel.php');

    $members = getAllMembers();
    $reviews = getAllReviews();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Panel</title>

</head>

<body>

    <h1>Admin Moderation Panel</h1>

    <h2>Members</h2>

    <?php foreach($members as $member){ ?>

        <p>
    
            <?= htmlspecialchars($member['name']) ?>
        
            <a href="#"onclick="deleteMember(<?= $member['id'] ?>)">Delete</a>
    
        </p>

    <?php } ?>

    <hr>

    <h2>Food Item Reviews</h2>

    <?php foreach($reviews as $review){ ?>

    <p>

        <?= htmlspecialchars($review['comment']) ?>

        <a href="#"onclick="deleteReview(<?= $review['id'] ?>)">Delete</a>

    </p>

    <?php } ?>

    <script src="../asset/admin_panel.js"></script>

</body>
</html>