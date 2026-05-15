<?php
$pageTitle = 'Home';
require_once __DIR__ . '/header.php';
?>
<div class="hero">
    <h1>Welcome to FoodBlog 🍽</h1>
    <p>Discover amazing restaurants and dishes in your city.</p>
    <div class="hero-actions">
        <a href="index.php?page=login" class="btn-primary">Login</a>
        <a href="index.php?page=register" class="btn-outline">Register</a>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
