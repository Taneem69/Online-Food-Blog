<?php
$pageTitle = $item['name'];
require_once __DIR__ . '/../partials/header.php';
?>

<nav class="breadcrumb">
    <a href="index.php?page=home">Home</a> /
    <a href="index.php?page=restaurant_detail&id=<?= $item['restaurant_id'] ?>"><?= htmlspecialchars($item['restaurant_name']) ?></a> /
    <span><?= htmlspecialchars($item['name']) ?></span>
</nav>

<div class="item-detail-layout">
    <!-- Image -->
    <div class="item-image-wrap">
        <?php if ($item['image_path']): ?>
            <img src="public/<?= htmlspecialchars($item['image_path']) ?>"
                 alt="<?= htmlspecialchars($item['name']) ?>" class="item-hero-img">
        <?php else: ?>
            <div class="item-img-placeholder">🍽</div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="item-info">
        <h1><?= htmlspecialchars($item['name']) ?></h1>
        <p class="item-price">৳ <?= number_format($item['price'], 2) ?></p>
        <p class="item-desc"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
        <a href="index.php?page=restaurant_detail&id=<?= $item['restaurant_id'] ?>" class="restaurant-link">
            🏪 <?= htmlspecialchars($item['restaurant_name']) ?>
        </a>

        <?php if (isAdmin()): ?>
        <div class="admin-actions-inline">
            <a href="index.php?page=menu_item_edit&id=<?= $item['id'] ?>" class="btn-edit">✏️ Edit Item</a>
            <button class="btn-danger"
                    onclick="deleteMenuItem(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>')">
                🗑 Delete Item
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Reviews Section -->
<section class="reviews-section">
    <h2>Reviews (<?= count($reviews) ?>)</h2>

    <?php if (isMember() || isAdmin()): ?>
    <p class="review-note">
        <?php if (isMember()): ?>
            Want to leave a review?
            <a href="index.php?page=review_add&menu_item_id=<?= $item['id'] ?>">Post a review</a>
            (handled in Task 3).
        <?php endif; ?>
    </p>
    <?php else: ?>
        <p class="review-note"><a href="index.php?page=login">Login</a> to post a review.</p>
    <?php endif; ?>

    <?php if (empty($reviews)): ?>
        <p class="empty-state">No reviews yet. Be the first!</p>
    <?php else: ?>
    <div id="reviewList">
        <?php foreach ($reviews as $rev): ?>
        <div class="review-card" id="review-<?= $rev['id'] ?>">
            <div class="review-header">
                <span class="reviewer-name"><?= htmlspecialchars($rev['member_name']) ?></span>
                <span class="review-date"><?= date('d M Y', strtotime($rev['created_at'])) ?></span>
                <?php if (isAdmin()): ?>
                <button class="btn-sm btn-danger" style="margin-left:auto"
                        onclick="adminDeleteReview(<?= $rev['id'] ?>)">Remove</button>
                <?php endif; ?>
            </div>
            <p class="review-body"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<input type="hidden" id="csrfToken" value="<?= csrfToken() ?>">
<input type="hidden" id="currentItemId" value="<?= $item['id'] ?>">
<input type="hidden" id="restaurantId" value="<?= $item['restaurant_id'] ?>">

<!-- Delete Modal -->
<div id="deleteModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-box">
        <h3>Confirm Delete</h3>
        <p id="deleteModalMsg"></p>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal()">Cancel</button>
            <button class="btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
