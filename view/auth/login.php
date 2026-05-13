<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="form-card">
    <h1>Login</h1>
    <p>Login to your FoodBlog account.</p>

    <?php if (!empty($errors['login'])): ?>
        <div class="alert alert-error"><?= e($errors['login']) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?page=login" id="loginForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">
            <label for="loginEmail">Email</label>
            <input
                type="email"
                name="email"
                id="loginEmail"
                value="<?= e($old['email'] ?? '') ?>"
                placeholder="Enter your email"
            >

            <?php if (!empty($errors['email'])): ?>
                <span class="error-msg"><?= e($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="loginPassword">Password</label>
            <input
                type="password"
                name="password"
                id="loginPassword"
                placeholder="Enter your password"
            >

            <?php if (!empty($errors['password'])): ?>
                <span class="error-msg"><?= e($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group checkbox-row">
            <label>
                <input type="checkbox" name="remember">
                Remember Me
            </label>
        </div>

        <button type="submit" class="btn-primary">Login</button>
    </form>

    <p class="form-bottom-text">
        No account?
        <a href="index.php?page=register">Register here</a>
    </p>
</div>

<script src="public/js/auth.js"></script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>