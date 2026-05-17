<?php
require_once __DIR__ . '/config/init.php';
require_once __DIR__ . '/models/RestaurantModel.php';
require_once __DIR__ . '/models/MenuItemModel.php';

$page = $_GET['page'] ?? 'home';

$restaurantModel = new RestaurantModel();
$menuItemModel = new MenuItemModel();

switch ($page) {
    case 'home':
        $restaurants = $restaurantModel->getAll();
        $pageTitle = 'Home';
        require __DIR__ . '/view/partials/header.php';
        ?>
        <div class="page-header">
            <h1>Welcome to Online Food Blog</h1>
            <p>Discover restaurants, browse food items, and explore delicious meals.</p>

            <?php if (!isLoggedIn()): ?>
                <p>
                    <a href="index.php?page=register" class="btn-primary">Register</a>
                    <a href="index.php?page=login" class="btn-outline">Login</a>
                </p>
            <?php endif; ?>
        </div>

        <section class="section">
            <div class="section-header">
                <h2>Restaurants</h2>
                <a href="index.php?page=restaurants" class="btn-outline">View All</a>
            </div>

            <?php if (empty($restaurants)): ?>
                <div class="empty-state">
                    <p>No restaurants added yet.</p>
                </div>
            <?php else: ?>
                <div class="menu-grid">
                    <?php foreach ($restaurants as $restaurant): ?>
                        <div class="menu-card">
                            <div class="menu-card-body">
                                <h3><?= e($restaurant['name']) ?></h3>
                                <p><strong>Location:</strong> <?= e($restaurant['location']) ?></p>
                                <p><strong>Area:</strong> <?= e($restaurant['area']) ?></p>
                                <p class="menu-desc"><?= e(mb_substr($restaurant['short_background'], 0, 120)) ?>...</p>
                                <a class="btn-primary" href="index.php?page=restaurant_detail&id=<?= (int)$restaurant['id'] ?>">
                                    View Restaurant
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
        require __DIR__ . '/view/partials/footer.php';
        break;

    case 'restaurants':
        $restaurants = $restaurantModel->getAll();
        $pageTitle = 'Restaurants';
        require __DIR__ . '/view/partials/header.php';
        ?>
        <div class="page-header">
            <h1>All Restaurants</h1>
            <p>Visitors, members, and admins can browse restaurants.</p>
        </div>

        <?php if (empty($restaurants)): ?>
            <div class="empty-state">
                <p>No restaurants available.</p>
            </div>
        <?php else: ?>
            <div class="menu-grid">
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="menu-card">
                        <div class="menu-card-body">
                            <h3><?= e($restaurant['name']) ?></h3>
                            <p><strong>Location:</strong> <?= e($restaurant['location']) ?></p>
                            <p><strong>Area:</strong> <?= e($restaurant['area']) ?></p>
                            <p class="menu-desc"><?= e(mb_substr($restaurant['short_background'], 0, 140)) ?>...</p>
                            <a class="btn-primary" href="index.php?page=restaurant_detail&id=<?= (int)$restaurant['id'] ?>">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php
        require __DIR__ . '/view/partials/footer.php';
        break;

    case 'restaurant_detail':
        $id = (int)($_GET['id'] ?? 0);
        $restaurant = $restaurantModel->getById($id);

        if (!$restaurant) {
            die('Restaurant not found.');
        }

        $menuItems = $menuItemModel->getByRestaurant($id);
        require __DIR__ . '/view/restaurants/detail.php';
        break;

    case 'menu_item_detail':
        $id = (int)($_GET['id'] ?? 0);
        $item = $menuItemModel->getById($id);

        if (!$item) {
            die('Menu item not found.');
        }

        $stmt = getDB()->prepare(
            "SELECT reviews.*, users.name AS member_name
             FROM reviews
             JOIN users ON users.id = reviews.user_id
             WHERE reviews.menu_item_id = ?
             ORDER BY reviews.created_at DESC"
        );
        $stmt->execute([$id]);
        $reviews = $stmt->fetchAll();

        require __DIR__ . '/view/menu_items/detail.php';
        break;

    case 'login':
        require __DIR__ . '/view/partials/login_stub.php';
        break;

    case 'register':
        require __DIR__ . '/view/partials/register_stub.php';
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        redirect('index.php?page=home');
        break;

    default:
        require __DIR__ . '/view/partials/404.php';
        break;
}
