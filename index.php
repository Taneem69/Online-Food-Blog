<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/controllers/AdminController.php';

$page = $_GET['page'] ?? 'home';
$controller = new AdminController();


$routes = [
    
    'admin_dashboard'   => [$controller, 'dashboard'],
    'restaurant_create' => [$controller, 'restaurantCreate'],
    'restaurant_edit'   => [$controller, 'restaurantEdit'],
    'menu_item_create'  => [$controller, 'menuItemCreate'],
    'menu_item_edit'    => [$controller, 'menuItemEdit'],

    'restaurant_detail' => [$controller, 'restaurantDetail'],
    'restaurants'       => [$controller, 'restaurantList'],
    'menu_item_detail'  => [$controller, 'menuItemDetail'],

    'restaurant_delete' => [$controller, 'restaurantDelete'],
    'menu_item_delete'  => [$controller, 'menuItemDelete'],
    'delete_review'     => [$controller, 'deleteReview'],

    
    'home' => function() {
        if (isAdmin()) {
            header('Location: index.php?page=admin_dashboard'); exit;
        }
        include __DIR__ . '/views/partials/home_visitor.php';
    },

    // Login page
    'login' => function() { include __DIR__ . '/views/partials/login_stub.php'; },

    'login_post' => function() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=login'); exit;
        }
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Email and password are required.';
            header('Location: index.php?page=login'); exit;
        }

        $stmt = getDB()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: index.php?page=admin_dashboard'); exit;
            }
            header('Location: index.php?page=home'); exit;
        }

        $_SESSION['flash_error'] = 'Invalid email or password.';
        header('Location: index.php?page=login'); exit;
    },

    'register' => function() { include __DIR__ . '/views/partials/register_stub.php'; },

    'logout' => function() {
        session_destroy();
        header('Location: index.php?page=login'); exit;
    },
];

if (isset($routes[$page]) && is_callable($routes[$page])) {
    call_user_func($routes[$page]);
} else {
    http_response_code(404);
    include __DIR__ . '/views/partials/404.php';
}
