<?php
session_start();
require_once '../../config/database.php';
/** @var \PDO $pdo */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die('Invalid menu item.');
}

$itemStmt = $pdo->prepare("
    SELECT
        mi.id,
        mi.name,
        mi.description,
        mi.price,
        mi.image_path,
        r.id   AS restaurant_id,
        r.name AS restaurant_name
    FROM menu_items mi
    JOIN restaurants r ON mi.restaurant_id = r.id
    WHERE mi.id = ?
");
$itemStmt->execute([$id]);
$item = $itemStmt->fetch();

if (!$item) {
    die('Menu item not found.');
}

$reviewStmt = $pdo->prepare("
    SELECT
        r.id,
        r.comment,
        r.created_at,
        r.user_id,
        u.name AS reviewer_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.menu_item_id = ?
    ORDER BY r.created_at DESC
");
$reviewStmt->execute([$id]);
$reviews = $reviewStmt->fetchAll();
$isMember = isset($_SESSION['user_id']) && $_SESSION['role'] === 'member';
$isAdmin  = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['name']) ?> — Online Food Blog</title>
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
        .breadcrumb a { color: #c0392b; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .item-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .item-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }
        .item-body { padding: 25px; }
        .item-body h1 {
            font-size: 26px;
            margin-bottom: 8px;
        }
        .item-restaurant {
            font-size: 14px;
            color: #888;
            margin-bottom: 15px;
        }
        .item-restaurant a {
            color: #c0392b;
            text-decoration: none;
        }
        .item-price {
            font-size: 22px;
            font-weight: bold;
            color: #c0392b;
            margin-bottom: 15px;
        }
        .item-description {
            font-size: 15px;
            line-height: 1.7;
            color: #555;
        }

        .reviews-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .reviews-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #c0392b;
            color: #c0392b;
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
            color: #333;
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
            margin-top: 5px;
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
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/ONLINE-FOOD-BLOG/view/profile.php">Profile</a>
            <a href="/ONLINE-FOOD-BLOG/logout.php">Logout</a>
        <?php else: ?>
            <a href="/ONLINE-FOOD-BLOG/view/auth/login.php">Login</a>
            <a href="/ONLINE-FOOD-BLOG/view/auth/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="breadcrumb">
    <a href="/ONLINE-FOOD-BLOG/view/browse/index.php">Browse</a> →
    <a href="/ONLINE-FOOD-BLOG/view/browse/restaurant.php?id=<?= $item['restaurant_id'] ?>">
        <?= htmlspecialchars($item['restaurant_name']) ?>
    </a> →
    <?= htmlspecialchars($item['name']) ?>
</div>

<div class="container">
    <div class="item-card">
        <div class="item-image">
            <?php if (!empty($item['image_path'])): ?>
                <img
                    src="/ONLINE-FOOD-BLOG/public/<?= htmlspecialchars($item['image_path']) ?>"
                    alt="<?= htmlspecialchars($item['name']) ?>"
                    style="width:100%; height:280px; object-fit:cover;"
                    onerror="this.parentElement.innerHTML='🍽'"
                >
            <?php else: ?>
                🍽
            <?php endif; ?>
        </div>

        <div class="item-body">
            <h1><?= htmlspecialchars($item['name']) ?></h1>
            <div class="item-restaurant">
                From:
                <a href="/ONLINE-FOOD-BLOG/view/browse/restaurant.php?id=<?= $item['restaurant_id'] ?>">
                    <?= htmlspecialchars($item['restaurant_name']) ?>
                </a>
            </div>
            <div class="item-price">
                ৳ <?= number_format($item['price'], 2) ?>
            </div>
            <p class="item-description">
                <?= htmlspecialchars($item['description']) ?>
            </p>
        </div>
    </div>

    <div class="reviews-section">
        <h2>Reviews (<?= count($reviews) ?>)</h2>
        <?php if ($isMember): ?>
            <div class="review-form">
                <h3>Write a Review</h3>
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
                        placeholder="Share your experience with this dish..."
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

        <?php elseif (!isset($_SESSION['user_id'])): ?>
            <div class="login-prompt">
                <a href="/ONLINE-FOOD-BLOG/view/auth/login.php">Login as a member</a>
                to post a review.
            </div>
        <?php endif; ?>
        <div id="reviewList">
            <?php if (empty($reviews)): ?>
                <div class="no-reviews" id="noReviews">
                    No reviews yet. Be the first to review this item!
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item" id="review-<?= $review['id'] ?>">
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

                        <!-- Delete button — only show for own reviews -->
                        <?php if (
                            isset($_SESSION['user_id']) &&
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
const MENU_ITEM_ID = <?= $item['id'] ?>;
function updateCharCount() {
    const box      = document.getElementById('commentBox');
    const counter  = document.getElementById('charCount');
    const length   = box.value.length;

    counter.textContent = length + ' / 1000';
    counter.className   = 'char-count';

    if (length > 900) counter.classList.add('danger');
    else if (length > 700) counter.classList.add('warning');
}

function submitReview() {
    const commentBox = document.getElementById('commentBox');
    const errorBox   = document.getElementById('formError');
    const submitBtn  = document.getElementById('submitBtn');
    const comment    = commentBox.value.trim();

    errorBox.style.display = 'none';
    errorBox.textContent   = '';

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
            menu_item_id: MENU_ITEM_ID,
            comment:      comment
        })
    })
    .then(function(response) { return response.json(); })
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
    .catch(function(error) {
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Post Review';
        showError('Something went wrong. Please try again.');
        console.error('Error:', error);
    });
}

function addReviewToPage(review) {
    // Hide the "no reviews" message if it was showing
    const noReviews = document.getElementById('noReviews');
    if (noReviews) noReviews.style.display = 'none';

    const list = document.getElementById('reviewList');

    // Build the new review HTML
    const div = document.createElement('div');
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
    if (!confirm('Are you sure you want to delete this review?')) {
        return;
    }

    fetch(`/ONLINE-FOOD-BLOG/api/reviews/delete.php?id=${reviewId}`, {
        method: 'DELETE'
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {

            const reviewEl = document.getElementById('review-' + reviewId);
            if (reviewEl) reviewEl.remove();

            updateReviewCount(-1);

            showToast('Review deleted.');

            const list = document.getElementById('reviewList');
            if (list.children.length === 0) {
                list.innerHTML =
                    '<div class="no-reviews" id="noReviews">' +
                    'No reviews yet. Be the first to review this item!' +
                    '</div>';
            }
        } else {
            showToast(data.error || 'Failed to delete review.', true);
        }
    })
    .catch(function(error) {
        showToast('Something went wrong. Please try again.', true);
        console.error('Error:', error);
    });
}

function updateReviewCount(change) {
    const heading = document.querySelector('.reviews-section h2');
    if (!heading) return;

    const match = heading.textContent.match(/\d+/);
    if (match) {
        const newCount    = Math.max(0, parseInt(match[0]) + change);
        heading.textContent = `Reviews (${newCount})`;
    }
}

function showError(message) {
    const errorBox         = document.getElementById('formError');
    errorBox.textContent   = message;
    errorBox.style.display = 'block';
}

function showToast(message, isError = false) {
    const toast       = document.getElementById('toast');
    toast.textContent = message;
    toast.className   = isError ? 'toast error' : 'toast';
    toast.style.display = 'block';

    // Hide after 3 seconds
    setTimeout(function() {
        toast.style.display = 'none';
    }, 3000);
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