<?php
$isEdit    = isset($item);
$pageTitle = $isEdit ? 'Edit Menu Item' : 'Add Menu Item';
require_once __DIR__ . '/../partials/header.php';
?>

<div class="page-header">
    <h1><?= $isEdit ? 'Edit Menu Item' : 'Add Menu Item' ?>
        <span class="subtitle">for <?= htmlspecialchars($restaurant['name']) ?></span>
    </h1>
    <a href="index.php?page=restaurant_detail&id=<?= $restaurant['id'] ?>" class="btn-outline">← Back to Restaurant</a>
</div>

<div class="form-card">
    <form method="POST" action="" enctype="multipart/form-data" id="menuItemForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

        <div class="form-group">
            <label for="name">Item Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" maxlength="200"
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                   class="<?= isset($errors['name']) ? 'input-error' : '' ?>">
            <?php if (isset($errors['name'])): ?>
                <span class="error-msg"><?= $errors['name'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="description">Description <span class="required">*</span></label>
            <textarea id="description" name="description" rows="4"
                      class="<?= isset($errors['description']) ? 'input-error' : '' ?>"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
            <?php if (isset($errors['description'])): ?>
                <span class="error-msg"><?= $errors['description'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="price">Price (৳) <span class="required">*</span></label>
            <input type="number" id="price" name="price" step="0.01" min="0.01"
                   value="<?= htmlspecialchars($old['price'] ?? '') ?>"
                   class="<?= isset($errors['price']) ? 'input-error' : '' ?>">
            <?php if (isset($errors['price'])): ?>
                <span class="error-msg"><?= $errors['price'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="image">Image (JPEG/PNG, max 2 MB)</label>

            <?php if ($isEdit && !empty($old['image_path'])): ?>
                <div class="img-preview-wrap">
                    <img src="public/<?= htmlspecialchars($old['image_path']) ?>" alt="Current image" class="img-preview">
                    <small>Upload a new image to replace the current one.</small>
                </div>
            <?php endif; ?>

            <input type="file" id="image" name="image" accept="image/jpeg,image/png"
                   class="<?= isset($errors['image']) ? 'input-error' : '' ?>">
            <span class="field-hint">Accepted: JPEG, PNG — Max size: 2 MB</span>
            <?php if (isset($errors['image'])): ?>
                <span class="error-msg"><?= $errors['image'] ?></span>
            <?php endif; ?>
            <!-- Live preview -->
            <img id="imgLivePreview" src="#" alt="Preview" class="img-preview hidden" style="margin-top:8px;">
        </div>

        <div class="form-actions">
            <a href="index.php?page=restaurant_detail&id=<?= $restaurant['id'] ?>" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Add Item' ?>
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
