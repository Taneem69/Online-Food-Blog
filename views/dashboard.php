<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
    <h1>Admin Dashboard</h1>
    <a href="index.php?page=restaurant_create" class="btn-primary">+ Add Restaurant</a>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-icon">🏪</span>
        <div class="stat-body">
            <h2><?= $stats['restaurants'] ?></h2>
            <p>Restaurants</p>
        </div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">🍜</span>
        <div class="stat-body">
            <h2><?= $stats['menu_items'] ?></h2>
            <p>Menu Items</p>
        </div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">⭐</span>
        <div class="stat-body">
            <h2><?= $stats['reviews'] ?></h2>
            <p>Reviews</p>
        </div>
    </div>
    <div class="stat-card">
        <span class="stat-icon">📝</span>
        <div class="stat-body">
            <h2><?= $stats['food_exp_posts'] ?></h2>
            <p>Food Experience Posts</p>
        </div>
    </div>
</div>

<!-- Restaurants Table -->
<section class="section">
    <div class="section-header">
        <h2>All Restaurants</h2>
    </div>

    <?php if (empty($restaurants)): ?>
        <div class="empty-state">
            <p>No restaurants yet. <a href="index.php?page=restaurant_create">Add one →</a></p>
        </div>
    <?php else: ?>
    <div class="table-wrapper">
        <table class="data-table" id="restaurantTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Area</th>
                    <th>Items</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($restaurants as $i => $r): ?>
                <tr id="row-restaurant-<?= $r['id'] ?>">
                    <td><?= $i + 1 ?></td>
                    <td><a href="index.php?page=restaurant_detail&id=<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></a></td>
                    <td><?= htmlspecialchars($r['location']) ?></td>
                    <td><?= htmlspecialchars($r['area']) ?></td>
                    <td><span class="badge"><?= $r['item_count'] ?></span></td>
                    <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                    <td class="actions">
                        <a href="index.php?page=restaurant_edit&id=<?= $r['id'] ?>" class="btn-sm btn-edit">Edit</a>
                        <button class="btn-sm btn-danger"
                                onclick="deleteRestaurant(<?= $r['id'] ?>, '<?= htmlspecialchars(addslashes($r['name'])) ?>')">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</section>

<!-- Hidden CSRF token for AJAX -->
<input type="hidden" id="csrfToken" value="<?= csrfToken() ?>">

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal hidden" role="dialog" aria-modal="true">
    <div class="modal-box">
        <h3>Confirm Delete</h3>
        <p id="deleteModalMsg">Are you sure you want to delete this restaurant and <strong>all its menu items</strong>? This cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn-outline" onclick="closeModal()">Cancel</button>
            <button class="btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
