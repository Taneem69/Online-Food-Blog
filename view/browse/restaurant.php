<?php
session_start();
require_once '../../config/database.php';
/** @var \PDO $pdo */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('Invalid restaurant.');
}
$restaurantStmt = $pdo->prepare("
    SELECT id, name, location, area, short_background, goals
    FROM restaurants
    WHERE id = ?
");
$restaurantStmt->execute([$id]);
$restaurant = $restaurantStmt->fetch();

if (!$restaurant) {
    die('Restaurant not found.');
}
$menuStmt = $pdo->prepare("
    SELECT id, name, description, price, image_path
    FROM menu_items
    WHERE restaurant_id = ?
    ORDER BY name ASC
");
$menuStmt->execute([$id]);
$menuItems = $menuStmt->fetchAll();
$reviewStmt = $pdo->prepare("
    SELECT
        r.id,
        r.comment,
        r.created_at,
        r.user_id,
        u.name AS reviewer_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.menu_item_id IS NULL
    AND r.user_id IN (
        SELECT user_id FROM reviews
        WHERE menu_item_id IS NULL
    )
    ORDER BY r.created_at DESC
");
$reviewStmt = $pdo->prepare("
    SELECT
        r.id,
        r.comment,
        r.created_at,
        r.user_id,
        u.name AS reviewer_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.menu_item_id IS NULL
    ORDER BY r.created_at DESC
");
$reviewStmt->execute();
$restaurantReviews = $reviewStmt->fetchAll();

// Session helpers
$isMember  = isset($_SESSION['user_id']) && $_SESSION['role'] === 'member';
$isAdmin   = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($restaurant['name']) ?> — Online Food Blog</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .navbar {
            background: #c0392b;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
        }
        .navbar a:hover { text-decoration: underline; }

        .breadcrumb {
            padding: 12px 30px;
            background: white;
            font-size: 13px;
            color: #888;
            border-bottom: 1px solid #eee;
        }
        .breadcrumb a {
            color: #c0392b;
            text-decoration: none;
        }
        .breadcrumb a:hover { text-decoration: underline; }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .restaurant-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .restaurant-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .restaurant-header h1 {
            font-size: 28px;
            color: #222;
        }
        .restaurant-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .badge {
            background: #fdecea;
            color: #c0392b;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
        }
        .restaurant-section {
            margin-bottom: 20px;
        }
        .restaurant-section h3 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #aaa;
            margin-bottom: 8px;
        }
        .restaurant-section p {
            font-size: 15px;
            line-height: 1.7;
            color: #555;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #c0392b;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #c0392b;
        }

        .menu-section {
            margin-bottom: 30px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .menu-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }
        .menu-card-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #ddd;
        }
        .menu-card-image img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }
        .menu-card-body {
            padding: 15px;
        }
        .menu-card-body h3 {
            font-size: 16px;
            margin-bottom: 6px;
            color: #222;
        }
        .menu-card-desc {
            font-size: 13px;
            color: #888;
            line-height: 1.5;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-clamp: 2;
            overflow: hidden;
        }
        .menu-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .menu-price {
            font-size: 16px;
            font-weight: bold;
            color: #c0392b;
        }
        .btn-view {
            background: #c0392b;
            color: white;
            padding: 6px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-view:hover { background: #a93226; }

        .no-items {
            text-align: center;
            padding: 40px;
            color: #aaa;
            background: white;
            border-radius: 10px;
        }

        .reviews-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .review-form {
            background: #fff8f8;
            border: 1px solid #f5c6c6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .review-form h3 {
            font-size: 16px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 12px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
            outline: none;
        }
        .form-group textarea:focus {
            border-color: #c0392b;
        }
        .char-count {
            font-size: 12px;
            color: #aaa;
            text-align: right;
            margin-top: 4px;
        }
        .char-count.warning { color: #e67e22; }
        .char-count.danger  { color: #c0392b; }
        .form-error {
            color: #c0392b;
            font-size: 13px;
            margin-bottom: 10px;
            display: none;
        }
        .btn-submit {
            background: #c0392b;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-submit:hover { background: #a93226; }
        .btn-submit:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .login-prompt {
            background: #f9f9f9;
            border: 1px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            color: #888;
        }
        .login-prompt a {
            color: #c0392b;
            text-decoration: none;
            font-weight: bold;
        }

        .review-item {
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 0;
        }
        .review-item:last-child { border-bottom: none; }
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        .reviewer-name {
            font-weight: bold;
            font-size: 14px;
        }
        .review-date {
            font-size: 12px;
            color: #aaa;
        }
        .review-comment {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
        }
        .btn-delete {
            background: none;
            border: 1px solid #e74c3c;
            color: #e74c3c;
            padding: 4px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 8px;
        }
        .btn-delete:hover {
            background: #e74c3c;
            color: white;
        }

        .no-reviews {
            text-align: center;
            padding: 30px;
            color: #aaa;
            font-size: 14px;
        }

        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #27ae60;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            display: none;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .toast.error { background: #c0392b; }
    </style>
</head>
<body>

<nav class="navbar">
    <strong>🍽 Online Food Blog</strong>
    <div>
        <a href="/ONLINE-FOOD-BLOG/view/browse/index.php">Browse</a>
        <?php if ($isLoggedIn): ?>
            <a href="/ONLINE-FOOD-BLOG/view/profile.php">Profile</a>
            <a href="/ONLINE-FOOD-BLOG/logout.php">Logout</a>
        <?php else: ?>
            <a href="/ONLINE-FOOD-BLOG/view/auth/login.php">Login</a>
            <a href="/ONLINE-FOOD-BLOG/view/auth/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="breadcrumb">
    <a href="/ONLINE-FOOD-BLOG/view/browse/index.php">Browse</a>
    → <?= htmlspecialchars($restaurant['name']) ?>
</div>

<div class="container">

    <div class="restaurant-card">
        <div class="restaurant-header">
            <div>
                <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
                <div class="restaurant-badges">
                    <span class="badge">
                        📍 <?= htmlspecialchars($restaurant['location']) ?>
                    </span>
                    <span class="badge">
                        🏘 <?= htmlspecialchars($restaurant['area']) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="restaurant-section">
            <h3>About</h3>
            <p><?= htmlspecialchars($restaurant['short_background']) ?></p>
        </div>

        <div class="restaurant-section">
            <h3>Our Goals</h3>
            <p><?= htmlspecialchars($restaurant['goals']) ?></p>
        </div>
    </div>

    <div class="menu-section">
        <div class="section-title">
            Menu Items (<?= count($menuItems) ?>)
        </div>

        <?php if (empty($menuItems)): ?>
            <div class="no-items">
                <p>No menu items available for this restaurant yet.</p>
            </div>
        <?php else: ?>
            <div class="menu-grid">
                <?php foreach ($menuItems as $item): ?>
                    <div class="menu-card"
                         onclick="window.location='/ONLINE-FOOD-BLOG/view/browse/menu_item.php?id=<?= $item['id'] ?>'">
                        <!-- Item Image -->
                        <div class="menu-card-image">
                            <?php if (!empty($item['image_path'])): ?>
                                <img
                                    src="/ONLINE-FOOD-BLOG/public/<?= htmlspecialchars($item['image_path']) ?>"
                                    alt="<?= htmlspecialchars($item['name']) ?>"
                                    onerror="this.parentElement.innerHTML='🍽'"
                                >
                            <?php else: ?>
                                🍽
                            <?php endif; ?>
                        </div>

                        <div class="menu-card-body">
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="menu-card-desc">
                                <?= htmlspecialchars($item['description']) ?>
                            </p>
                            <div class="menu-card-footer">
                                <span class="menu-price">
                                    ৳ <?= number_format($item['price'], 2) ?>
                                </span>
                                <a class="btn-view"
                                   href="/ONLINE-FOOD-BLOG/view/browse/menu_item.php?id=<?= $item['id'] ?>"
                                   onclick="event.stopPropagation()">
                                    View Item
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="reviews-section">
        <div class="section-title">
            Restaurant Reviews (<?= count($restaurantReviews) ?>)
        </div>

        <?php if ($isMember): ?>
            <div class="review-form">
                <h3>Write a Review About This Restaurant</h3>
                <div class="form-error" id="formError"></div>
                <div class="form-group">
                    <label>Your Name</label>
                    <input
                        type="text"
                        value="<?= htmlspecialchars($_SESSION['name']) ?>"
                        disabled
                        style="width:100%; padding:8px; border:1px solid #ddd;
                               border-radius:6px; background:#f9f9f9; color:#888;"
                    >
                </div>
                <div class="form-group">
                    <label>Your Review</label>
                    <textarea
                        id="commentBox"
                        placeholder="Share your overall experience at this restaurant..."
                        maxlength="1000"
                        oninput="updateCharCount()"
                    ></textarea>
                    <div class="char-count" id="charCount">0 / 1000</div>
                </div>
                <button
                    class="btn-submit"
                    id="submitBtn"
                    onclick="submitReview()"
                >
                    Post Review
                </button>
            </div>

        <?php elseif (!$isLoggedIn): ?>
            <div class="login-prompt">
                <a href="/ONLINE-FOOD-BLOG/view/auth/login.php">Login as a member</a>
                to post a review about this restaurant.
            </div>
        <?php endif; ?>

        <div id="reviewList">
            <?php if (empty($restaurantReviews)): ?>
                <div class="no-reviews" id="noReviews">
                    No reviews yet. Be the first to review this restaurant!
                </div>
            <?php else: ?>
                <?php foreach ($restaurantReviews as $review): ?>
                    <div class="review-item"
                         id="review-<?= $review['id'] ?>">
                        <div class="review-header">
                            <div>
                                <div class="reviewer-name">
                                    <?= htmlspecialchars($review['reviewer_name']) ?>
                                </div>
                                <div class="review-date">
                                    <?= date('M d, Y', strtotime($review['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <p class="review-comment">
                            <?= htmlspecialchars($review['comment']) ?>
                        </p>

                        <?php if (
                            $isLoggedIn &&
                            (int)$_SESSION['user_id'] === (int)$review['user_id']
                        ): ?>
                            <button
                                class="btn-delete"
                                onclick="deleteReview(<?= $review['id'] ?>)"
                            >
                                Delete
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

</div>

<div class="toast" id="toast"></div>

<script>
function updateCharCount() {
    const box     = document.getElementById('commentBox');
    const counter = document.getElementById('charCount');
    const length  = box.value.length;

    counter.textContent = length + ' / 1000';
    counter.className   = 'char-count';

    if (length > 900)      counter.classList.add('danger');
    else if (length > 700) counter.classList.add('warning');
}

function submitReview() {
    const commentBox = document.getElementById('commentBox');
    const errorBox   = document.getElementById('formError');
    const submitBtn  = document.getElementById('submitBtn');
    const comment    = commentBox.value.trim();

    errorBox.style.display = 'none';

    if (comment === '') {
        showError('Please write a comment before submitting.');
        return;
    }
    if (comment.length > 1000) {
        showError('Comment is too long. Maximum 1000 characters.');
        return;
    }

    submitBtn.disabled    = true;
    submitBtn.textContent = 'Posting...';

    fetch('/ONLINE-FOOD-BLOG/api/reviews/add.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            menu_item_id: null,
            comment:      comment
        })
    })
    .then(function(res)  { return res.json(); })
    .then(function(data) {
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Post Review';

        if (data.success) {
            commentBox.value = '';
            updateCharCount();
            addReviewToPage(data.review);
            showToast('Review posted successfully!');
        } else {
            showError(data.error || 'Failed to post review.');
        }
    })
    .catch(function() {
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Post Review';
        showError('Something went wrong. Please try again.');
    });
}

function addReviewToPage(review) {
    const noReviews = document.getElementById('noReviews');
    if (noReviews) noReviews.style.display = 'none';

    const list = document.getElementById('reviewList');
    const div  = document.createElement('div');

    div.className = 'review-item';
    div.id        = 'review-' + review.id;
    div.innerHTML = `
        <div class="review-header">
            <div>
                <div class="reviewer-name">
                    ${escapeHtml(review.reviewer_name)}
                </div>
                <div class="review-date">Just now</div>
            </div>
        </div>
        <p class="review-comment">${escapeHtml(review.comment)}</p>
        <button class="btn-delete" onclick="deleteReview(${review.id})">
            Delete
        </button>
    `;

    list.insertBefore(div, list.firstChild);
    updateReviewCount(1);
}

function deleteReview(reviewId) {
    if (!confirm('Are you sure you want to delete this review?')) return;

    fetch(`/ONLINE-FOOD-BLOG/api/reviews/delete.php?id=${reviewId}`, {
        method: 'DELETE'
    })
    .then(function(res)  { return res.json(); })
    .then(function(data) {
        if (data.success) {
            const el = document.getElementById('review-' + reviewId);
            if (el) el.remove();

            updateReviewCount(-1);
            showToast('Review deleted.');

            const list = document.getElementById('reviewList');
            if (list.children.length === 0) {
                list.innerHTML =
                    '<div class="no-reviews" id="noReviews">' +
                    'No reviews yet. Be the first to review this restaurant!' +
                    '</div>';
            }
        } else {
            showToast(data.error || 'Failed to delete.', true);
        }
    })
    .catch(function() {
        showToast('Something went wrong. Please try again.', true);
    });
}
function updateReviewCount(change) {
    const titles = document.querySelectorAll('.section-title');
    titles.forEach(function(title) {
        if (title.textContent.includes('Restaurant Reviews')) {
            const match = title.textContent.match(/\d+/);
            if (match) {
                const newCount      = Math.max(0, parseInt(match[0]) + change);
                title.textContent   = `Restaurant Reviews (${newCount})`;
            }
        }
    });
}
function showError(message) {
    const errorBox         = document.getElementById('formError');
    errorBox.textContent   = message;
    errorBox.style.display = 'block';
}

function showToast(message, isError = false) {
    const toast         = document.getElementById('toast');
    toast.textContent   = message;
    toast.className     = isError ? 'toast error' : 'toast';
    toast.style.display = 'block';
    setTimeout(function() { toast.style.display = 'none'; }, 3000);
}
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#039;');
}
</script>

</body>
</html>