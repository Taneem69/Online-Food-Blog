<?php
$isEdit    = isset($restaurant);
$pageTitle = $isEdit ? 'Edit Restaurant' : 'Add Restaurant';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
    <h1><?= $isEdit ? 'Edit Restaurant' : 'Add New Restaurant' ?></h1>
    <a href="index.php?page=admin_dashboard" class="btn-outline">← Back to Dashboard</a>
</div>

<div class="form-card">
    <form method="POST" action="" id="restaurantForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group">
            <label for="name">Restaurant Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" maxlength="200"
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                   class="<?= isset($errors['name']) ? 'input-error' : '' ?>">
            <?php if (isset($errors['name'])): ?>
                <span class="error-msg"><?= $errors['name'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="location">Location (City) <span class="required">*</span></label>
                <input type="text" id="location" name="location" maxlength="200"
                       value="<?= htmlspecialchars($old['location'] ?? '') ?>"
                       class="<?= isset($errors['location']) ? 'input-error' : '' ?>">
                <?php if (isset($errors['location'])): ?>
                    <span class="error-msg"><?= $errors['location'] ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="area">Area (Neighborhood) <span class="required">*</span></label>
                <input type="text" id="area" name="area" maxlength="200"
                       value="<?= htmlspecialchars($old['area'] ?? '') ?>"
                       class="<?= isset($errors['area']) ? 'input-error' : '' ?>">
                <?php if (isset($errors['area'])): ?>
                    <span class="error-msg"><?= $errors['area'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label for="short_background">Short Background <span class="required">*</span></label>
            <textarea id="short_background" name="short_background" rows="4"
                      class="<?= isset($errors['short_background']) ? 'input-error' : '' ?>"><?= htmlspecialchars($old['short_background'] ?? '') ?></textarea>
            <?php if (isset($errors['short_background'])): ?>
                <span class="error-msg"><?= $errors['short_background'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="goals">Goals <span class="required">*</span></label>
            <textarea id="goals" name="goals" rows="3"
                      class="<?= isset($errors['goals']) ? 'input-error' : '' ?>"><?= htmlspecialchars($old['goals'] ?? '') ?></textarea>
            <?php if (isset($errors['goals'])): ?>
                <span class="error-msg"><?= $errors['goals'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <a href="index.php?page=admin_dashboard" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Create Restaurant' ?>
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
