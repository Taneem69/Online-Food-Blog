<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="form-card">
    <h1>Register</h1>
    <p>Please fill in the form below to create an account.</p>

    <form method="POST" action="index.php?page=register" id="registerForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">
            <label for="name">Name</label>
            <input 
                type="text" 
                name="name" 
                id="name" 
                value="<?= e($old['name'] ?? '') ?>" 
                placeholder="Enter your full name"
            >

            <?php if (!empty($errors['name'])): ?>
                <span class="error-msg"><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="<?= e($old['email'] ?? '') ?>" 
                placeholder="Enter your email"
            >

            <span id="emailStatus" class="field-hint"></span>

            <?php if (!empty($errors['email'])): ?>
                <span class="error-msg"><?= e($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                placeholder="Minimum 8 characters"
            >

            <?php if (!empty($errors['password'])): ?>
                <span class="error-msg"><?= e($errors['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input 
                type="password" 
                name="confirm_password" 
                id="confirmPassword" 
                placeholder="Confirm password"
            >

            <?php if (!empty($errors['confirm_password'])): ?>
                <span class="error-msg"><?= e($errors['confirm_password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role">
                <option value="member" <?= ($old['role'] ?? 'member') === 'member' ? 'selected' : '' ?>>
                    Member
                </option>
                <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                    Admin
                </option>
            </select>

            <?php if (!empty($errors['role'])): ?>
                <span class="error-msg"><?= e($errors['role']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-primary">Register</button>
    </form>

    <p class="form-bottom-text">
        Already have an account?
        <a href="index.php?page=login">Login here</a>
    </p>
</div>

<script src="public/js/auth.js"></script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>

.form-card {
    max-width: 480px;
    margin: 90px auto;
    background: #ffffff;
    padding: 35px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
}

.form-card h1 {
    margin-bottom: 10px;
}

.form-card p {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
}

.btn-primary {
    background: #c93b0c;
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    text-decoration: none;
    display: inline-block;
}

.btn-primary:hover {
    background: #a93008;
}

.error-msg {
    display: block;
    color: #dc2626;
    font-size: 14px;
    margin-top: 5px;
}

.field-hint {
    display: block;
    font-size: 13px;
    margin-top: 5px;
}

.form-bottom-text {
    margin-top: 20px;
}