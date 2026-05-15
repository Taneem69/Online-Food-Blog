<?php
$pageTitle = 'Restaurants';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
    <h1>All Restaurants</h1>
    <?php if (isAdmin()): ?>
        <a href="index.php?page=restaurant_create" class="btn-primary">+ Add Restaurant</a>
    <?php endif; ?>
</div>

<?php if (empty($restaurants)): ?>
    <div class="empty-state">
        <p>No restaurants available yet.</p>
        <?php if (isAdmin()): ?>
            <p><a href="index.php?page=restaurant_create">Add one →</a></p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <table class="data-table">
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
                <?php foreach ($restaurants as $i => $restaurant): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><a href="index.php?page=restaurant_detail&id=<?= $restaurant['id'] ?>"><?= htmlspecialchars($restaurant['name']) ?></a></td>
                        <td><?= htmlspecialchars($restaurant['location']) ?></td>
                        <td><?= htmlspecialchars($restaurant['area']) ?></td>
                        <td><span class="badge"><?= $restaurant['item_count'] ?></span></td>
                        <td><?= date('d M Y', strtotime($restaurant['created_at'])) ?></td>
                        <td class="actions">
                            <a href="index.php?page=restaurant_detail&id=<?= $restaurant['id'] ?>" class="btn-sm btn-view">View</a>
                            <?php if (isAdmin()): ?>
                                <a href="index.php?page=restaurant_edit&id=<?= $restaurant['id'] ?>" class="btn-sm btn-edit">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
