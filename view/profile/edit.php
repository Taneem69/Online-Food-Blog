<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="form-card wide">
    <h1>My Profile</h1>
    <p>Update your account information.</p>

    <?php if (!empty($user['profile_picture'])): ?>
        <img 
            src="public/<?= e($user['profile_picture']) ?>" 
            alt="Profile Picture" 
            class="profile-img"
        >
    <?php endif; ?>

    <form method="POST" action="index.php?page=profile" enctype="multipart/form-data" id="profileForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">
            <label for="profileName">Name</label>
            <input 
                type="text" 
                name="name" 
                id="profileName" 
                value="<?= e($old['name'] ?? '') ?>"
            >

            <?php if (!empty($errors['name'])): ?>
                <span class="error-msg"><?= e($errors['name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="profileEmail">Email</label>
            <input 
                type="email" 
                name="email" 
                id="profileEmail" 
                value="<?= e($old['email'] ?? '') ?>"
            >

            <?php if (!empty($errors['email'])): ?>
                <span class="error-msg"><?= e($errors['email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="profilePicture">Profile Picture</label>
            <input 
                type="file" 
                name="profile_picture" 
                id="profilePicture" 
                accept="image/jpeg,image/png"
            >
            <small>Only JPG/PNG. Maximum 2 MB.</small>

            <?php if (!empty($errors['profile_picture'])): ?>
                <span class="error-msg"><?= e($errors['profile_picture']) ?></span>
            <?php endif; ?>
        </div>

        <hr>

        <h3>Change Password</h3>
        <p>Leave password fields empty if you do not want to change password.</p>

        <div class="form-group">
            <label for="currentPassword">Current Password</label>
            <input 
                type="password" 
                name="current_password" 
                id="currentPassword"
            >

            <?php if (!empty($errors['current_password'])): ?>
                <span class="error-msg"><?= e($errors['current_password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="newPassword">New Password</label>
            <input 
                type="password" 
                name="new_password" 
                id="newPassword"
            >

            <?php if (!empty($errors['new_password'])): ?>
                <span class="error-msg"><?= e($errors['new_password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="confirmNewPassword">Confirm New Password</label>
            <input 
                type="password" 
                name="confirm_password" 
                id="confirmNewPassword"
            >

            <?php if (!empty($errors['confirm_password'])): ?>
                <span class="error-msg"><?= e($errors['confirm_password']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn-primary">Update Profile</button>
    </form>
</div>

<script src="public/js/auth.js"></script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>