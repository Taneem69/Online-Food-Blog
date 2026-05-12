<?php $pageTitle = 'Login'; require_once __DIR__ . '/header.php'; ?>
<div class="form-card" style="max-width:420px;margin:60px auto">
    <h2>Login</h2>
    <p>For testing: use <strong>admin@foodblog.com</strong> / <strong>Admin@1234</strong></p>
    <form method="POST" action="index.php?page=login_post">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
