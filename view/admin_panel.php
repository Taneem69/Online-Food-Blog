<?php
    
    session_start();
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header("location: ../view/food_experience.php");
        exit();
    }
    
    require_once('../model/adminModel.php');
    require_once('../model/foodExperienceModel.php');
    
    $members    = getAllMembers();
    $reviews    = getAllReviews();
    $fe_posts   = getAllFoodPosts();
    $fe_comments = getAllFoodExpComments();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../asset/food_experience.css">
    <style>
        .admin-section { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .admin-section h2 { border-bottom: 2px solid #e8b86d; padding-bottom: 8px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #222; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        tr:hover td { background: #fafafa; }
        .btn-del { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .no-data { color: #999; font-style: italic; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="../index.php">Home</a>
    <a href="food_experience.php">Food Experience</a>
    <a href="admin_panel.php">Admin Panel</a>
</div>

<div class="container">
    <h1>Admin Moderation Panel</h1>

    <!-- ── MEMBERS ──────────────────────────────────────────────────────── -->
    <div class="admin-section">
        <h2>Members (<?php echo count($members); ?>)</h2>
        <?php if (empty($members)): ?>
            <p class="no-data">No members found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Joined</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $m): ?>
                        <tr id="member-row-<?php echo $m['id']; ?>">
                            <td><?php echo $m['id']; ?></td>
                            <td><?php echo htmlspecialchars($m['name'],  ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $m['created_at']; ?></td>
                            <td>
                                <button class="btn-del" onclick="deleteMember(<?php echo $m['id']; ?>)">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ── FOOD ITEM REVIEWS (Task 3 table) ─────────────────────────────── -->
    <div class="admin-section">
        <h2>Food Item Reviews (<?php echo count($reviews); ?>)</h2>
        <?php if (empty($reviews)): ?>
            <p class="no-data">No reviews found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Member</th><th>Comment</th><th>Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $rev): ?>
                        <tr id="review-row-<?php echo $rev['id']; ?>">
                            <td><?php echo htmlspecialchars($rev['name'],    ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(substr($rev['comment'], 0, 80), ENT_QUOTES, 'UTF-8'); ?>...</td>
                            <td><?php echo $rev['created_at']; ?></td>
                            <td>
                                <button class="btn-del" onclick="deleteReview(<?php echo $rev['id']; ?>)">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ── FOOD EXPERIENCE POSTS ─────────────────────────────────────────── -->
    <div class="admin-section">
        <h2>Food Experience Posts (<?php echo count($fe_posts); ?>)</h2>
        <?php if (empty($fe_posts)): ?>
            <p class="no-data">No posts found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Author</th><th>Title</th><th>Type</th><th>Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($fe_posts as $p): ?>
                        <tr id="fepost-row-<?php echo $p['id']; ?>">
                            <td><?php echo htmlspecialchars($p['name'],  ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(substr($p['title'], 0, 60), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($p['post_type'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $p['created_at']; ?></td>
                            <td>
                                <button class="btn-del" onclick="adminDeletePost(<?php echo $p['id']; ?>)">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- ── FOOD EXPERIENCE COMMENTS ──────────────────────────────────────── -->
    <div class="admin-section">
        <h2>Food Experience Comments (<?php echo count($fe_comments); ?>)</h2>
        <?php if (empty($fe_comments)): ?>
            <p class="no-data">No comments found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Author</th><th>Comment</th><th>Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($fe_comments as $c): ?>
                        <tr id="fecomment-row-<?php echo $c['id']; ?>">
                            <td><?php echo htmlspecialchars($c['name'],    ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(substr($c['comment'], 0, 80), ENT_QUOTES, 'UTF-8'); ?>...</td>
                            <td><?php echo $c['created_at']; ?></td>
                            <td>
                                <button class="btn-del" onclick="adminDeleteComment(<?php echo $c['id']; ?>)">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

<script src="../asset/admin_panel.js"></script>
</body>
</html>