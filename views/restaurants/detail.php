<?php
$pageTitle = $restaurant['name'];
require_once __DIR__ . '/../partials/header.php';
?>

<div class="restaurant-hero">
    <h1><?= htmlspecialchars($restaurant['name']) ?></h1>
    <div class="meta-tags">
        <span class="tag tag-location">📍 <?= htmlspecialchars($restaurant['location']) ?></span>
        <span class="tag tag-area">🏘 <?= htmlspecialchars($restaurant['area']) ?></span>
    </div>
</div>

<div class="two-col">
    <section class="info-card">
        <h2>About</h2>
        <p><?= nl2br(htmlspecialchars($restaurant['short_background'])) ?></p>

        <h3>Our Goals</h3>
        <p><?= nl2br(htmlspecialchars($restaurant['goals'])) ?></p>
    </section>

    <?php if (isAdmin()): ?>
    <aside class="admin-panel">
        <h3>Admin Actions</h3>
        <a href="index.php?page=restaurant_edit&id=<?= $restaurant['id'] ?>" class="btn-edit btn-block">✏️ Edit Restaurant</a>
        <a href="index.php?page=menu_item_create&restaurant_id=<?= $restaurant['id'] ?>" class="btn-primary btn-block">+ Add Menu Item</a>
        <button class="btn-danger btn-block" style="margin-top:8px"
                onclick="deleteRestaurant(<?= $restaurant['id'] ?>, '<?= htmlspecialchars(addslashes($restaurant['name'])) ?>')">
            🗑 Delete Restaurant
        </button>
    </aside>
    <?php endif; ?>
</div>

<!-- Menu Items -->
<section class="section">
    <div class="section-header">
        <h2>Menu (<?= count($menuItems) ?> items)</h2>
        <?php if (isAdmin()): ?>
            <a href="index.php?page=menu_item_create&restaurant_id=<?= $restaurant['id'] ?>" class="btn-primary">+ Add Item</a>
        <?php endif; ?>
    </div>

    <?php if (empty($menuItems)): ?>
        <div class="empty-state">
            <p>No menu items yet.
                <?php if (isAdmin()): ?>
                    <a href="index.php?page=menu_item_create&restaurant_id=<?= $restaurant['id'] ?>">Add one →</a>
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
    <div class="menu-grid" id="menuGrid">
        <?php foreach ($menuItems as $item): ?>
        <div class="menu-card" id="menu-item-<?= $item['id'] ?>">
            <?php if ($item['image_path']): ?>
                <a href="index.php?page=menu_item_detail&id=<?= $item['id'] ?>">
                    <img src="public/<?= htmlspecialchars($item['image_path']) ?>"
                         alt="<?= htmlspecialchars($item['name']) ?>" class="menu-img" loading="lazy">
                </a>
            <?php else: ?>
                <div class="menu-img-placeholder">🍽</div>
            <?php endif; ?>

            <div class="menu-card-body">
                <h3><a href="index.php?page=menu_item_detail&id=<?= $item['id'] ?>">
                    <?= htmlspecialchars($item['name']) ?>
                </a></h3>
                <p class="menu-desc"><?= htmlspecialchars(mb_substr($item['description'], 0, 100)) ?>…</p>
                <div class="menu-footer">
                    <span class="price">৳ <?= number_format($item['price'], 2) ?></span>
                    <span class="review-count"><?= $item['review_count'] ?> review<?= $item['review_count'] !== 1 ? 's' : '' ?></span>
                </div>

                <?php if (isAdmin()): ?>
                <div class="card-admin-actions">
                    <a href="index.php?page=menu_item_edit&id=<?= $item['id'] ?>" class="btn-sm btn-edit">Edit</a>
                    <button class="btn-sm btn-danger"
                            onclick="deleteMenuItem(<?= $item['id'] ?>, '<?= htmlspecialchars(addslashes($item['name'])) ?>')">
                        Delete
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<input type="hidden" id="csrfToken" value="<?= csrfToken() ?>">

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
