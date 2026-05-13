<?php
$success = flashMessage('flash_success');
$error = flashMessage('flash_error');
$currentPage = $_GET['page'] ?? 'home';
$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= e($pageTitle ?? 'Online Food Blog') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="site-nav">
        <a class="brand" href="index.php?page=home">
            <span class="brand-icon">🍽</span>
            <span>FoodBlog</span>
        </a>

        <div class="nav-menu">
            <a 
                href="index.php?page=home" 
                class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>"
            >
                Home
            </a>

            <a 
                href="index.php?page=restaurants" 
                class="nav-link <?= $currentPage === 'restaurants' ? 'active' : '' ?>"
            >
                Restaurants
            </a>

            <?php if (isLoggedIn()): ?>
                <a 
                    href="index.php?page=profile" 
                    class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>"
                >
                    Profile
                </a>

                <?php if (isAdmin()): ?>
                    <a 
                        href="index.php?page=admin_dashboard" 
                        class="nav-link <?= $currentPage === 'admin_dashboard' ? 'active' : '' ?>"
                    >
                        Admin Dashboard
                    </a>
                <?php endif; ?>

                <a href="index.php?page=logout" class="nav-logout">
                    Logout
                </a>

                <div class="user-chip">
                    <span class="user-dot"></span>
                    <span><?= e($userName) ?></span>
                    <small><?= e($userRole) ?></small>
                </div>
            <?php else: ?>
                <a 
                    href="index.php?page=login" 
                    class="nav-link <?= $currentPage === 'login' ? 'active' : '' ?>"
                >
                    Login
                </a>

                <a href="index.php?page=register" class="nav-register">
                    Register
                </a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main class="container">
    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>