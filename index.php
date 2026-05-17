<?php
require_once __DIR__ . '/models/user.php';
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
    $pageTitle = 'Login';

    $errors = [];
    $old = [
        'email' => ''
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verifyCsrf();

        $old['email'] = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required.';
        }

        if ($password === '') {
            $errors['password'] = 'Password is required.';
        }

        if (empty($errors)) {
            $user = User::findByEmail(getDB(), $old['email']);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $tokenHash = hash('sha256', $token);

                    User::setRememberToken(getDB(), (int)$user['id'], $tokenHash);

                    setcookie('remember_me', $user['id'] . ':' . $token, [
                        'expires' => time() + (30 * 24 * 60 * 60),
                        'path' => '/',
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                }

                setFlash('flash_success', 'Login successful.');
                redirect('index.php?page=home');
            }

            $errors['login'] = 'Invalid email or password.';
        }
    }

    require __DIR__ . '/view/auth/login.php';
    break;
    case 'register':
    $pageTitle = 'Register';

    $errors = [];
    $old = [
        'name' => '',
        'email' => '',
        'role' => 'member'
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verifyCsrf();

        $old['name'] = trim($_POST['name'] ?? '');
        $old['email'] = trim($_POST['email'] ?? '');
        $old['role'] = $_POST['role'] ?? 'member';

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($old['name'] === '') {
            $errors['name'] = 'Name is required.';
        }

        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required.';
        } elseif (User::emailExists(getDB(), $old['email'])) {
            $errors['email'] = 'This email is already registered.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if (!in_array($old['role'], ['admin', 'member'], true)) {
            $errors['role'] = 'Invalid role selected.';
        }

        if (empty($errors)) {
            User::create(getDB(), $old['name'], $old['email'], $password, $old['role']);

            setFlash('flash_success', 'Registration successful. Please login.');
            redirect('index.php?page=login');
        }
    }

    require __DIR__ . '/view/auth/register.php';
    break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        redirect('index.php?page=home');
        break;

case 'profile':
    requireLogin();

    $pageTitle = 'Profile';
    $user = User::findById(getDB(), (int)$_SESSION['user_id']);

    if (!$user) {
        setFlash('flash_error', 'User not found.');
        redirect('index.php?page=logout');
    }

    $errors = [];
    $old = [
        'name' => $user['name'],
        'email' => $user['email']
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verifyCsrf();

        $old['name'] = trim($_POST['name'] ?? '');
        $old['email'] = trim($_POST['email'] ?? '');

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $profilePicture = null;

        if ($old['name'] === '') {
            $errors['name'] = 'Name is required.';
        }

        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required.';
        } elseif (User::emailExists(getDB(), $old['email'], (int)$user['id'])) {
            $errors['email'] = 'This email is already used by another account.';
        }

        if (!empty($_FILES['profile_picture']['name'])) {
            $file = $_FILES['profile_picture'];

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png'
            ];

            $maxSize = 2 * 1024 * 1024;

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['profile_picture'] = 'Upload failed.';
            } elseif ($file['size'] > $maxSize) {
                $errors['profile_picture'] = 'Image must be 2 MB or less.';
            } else {
                $mimeType = mime_content_type($file['tmp_name']);

                if (!isset($allowedTypes[$mimeType])) {
                    $errors['profile_picture'] = 'Only JPG and PNG images are allowed.';
                } else {
                    $fileName = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $allowedTypes[$mimeType];
                    $uploadDir = __DIR__ . '/public/uploads/profiles/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                        $profilePicture = 'uploads/profiles/' . $fileName;
                    } else {
                        $errors['profile_picture'] = 'Could not save uploaded image.';
                    }
                }
            }
        }

        $wantsPasswordChange = $currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '';

        if ($wantsPasswordChange) {
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $errors['current_password'] = 'Current password is incorrect.';
            }

            if (strlen($newPassword) < 8) {
                $errors['new_password'] = 'New password must be at least 8 characters.';
            }

            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = 'Passwords do not match.';
            }
        }

        if (empty($errors)) {
            User::updateProfile(getDB(), (int)$user['id'], $old['name'], $old['email'], $profilePicture);

            if ($wantsPasswordChange) {
                User::updatePassword(getDB(), (int)$user['id'], $newPassword);
            }

            $_SESSION['name'] = $old['name'];

            setFlash('flash_success', 'Profile updated successfully.');
            redirect('index.php?page=profile');
        }
    }

    require __DIR__ . '/view/profile/edit.php';
    break;

    default:
        require __DIR__ . '/view/partials/404.php';
        break;
}
