<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — FoodBlog' : 'FoodBlog Admin' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<nav class="navbar">
    <a class="navbar-brand" href="index.php?page=home">🍽 FoodBlog</a>
    <div class="navbar-links">
        <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
                <a href="index.php?page=admin_dashboard">Dashboard</a>
            <?php endif; ?>
            <a href="index.php?page=restaurants">Restaurants</a>
            <a href="index.php?page=logout" class="btn-outline">Logout</a>
        <?php else: ?>
            <a href="index.php?page=login">Login</a>
            <a href="index.php?page=register" class="btn-primary">Register</a>
        <?php endif; ?>
    </div>
</nav>

<main class="container">
<?php
$success = flashMessage('flash_success');
$error   = flashMessage('flash_error');
if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif;
if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>
