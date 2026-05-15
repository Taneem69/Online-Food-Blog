/* ================================================================
   main.js — Task 2 JS (Client-side validation + AJAX endpoints)
   Student: 23-50695-1
   ================================================================ */

'use strict';

// ─── CSRF helper ───────────────────────────────────────────────────────────
function getCsrf() {
  const el = document.getElementById('csrfToken');
  return el ? el.value : '';
}

// ─── Toast helper ──────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  t.className = 'alert alert-' + type;
  t.style.cssText = 'position:fixed;top:76px;right:20px;z-index:9999;min-width:260px;animation:fadeIn .3s';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3500);
}

// ─── Modal ─────────────────────────────────────────────────────────────────
let pendingDeleteCallback = null;

function openModal(msg, callback) {
  document.getElementById('deleteModalMsg').innerHTML = msg;
  pendingDeleteCallback = callback;
  document.getElementById('deleteModal').classList.remove('hidden');
}

function closeModal() {
  document.getElementById('deleteModal').classList.add('hidden');
  pendingDeleteCallback = null;
}

document.addEventListener('DOMContentLoaded', () => {
  const confirmBtn = document.getElementById('confirmDeleteBtn');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      const callback = pendingDeleteCallback;
      closeModal();
      if (typeof callback === 'function') callback();
    });
  }

  // Close modal on backdrop click
  const modal = document.getElementById('deleteModal');
  if (modal) {
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  }
});

// ─── AJAX: Delete Restaurant ────────────────────────────────────────────────
function deleteRestaurant(id, name) {
  openModal(
    `Are you sure you want to delete <strong>${escHtml(name)}</strong> and <strong>all its menu items</strong>? This cannot be undone.`,
    async () => {
      try {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', getCsrf());
        fd.append('ajax', '1');

        const res = await fetch('index.php?page=restaurant_delete', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
          // Remove row from dashboard table OR redirect if on detail page
          const row = document.getElementById('row-restaurant-' + id);
          if (row) {
            row.style.transition = 'opacity .3s';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 320);
            showToast('Restaurant deleted.');
          } else {
            window.location.href = 'index.php?page=admin_dashboard';
          }
        } else {
          showToast(data.message || 'Delete failed.', 'error');
        }
      } catch (err) {
        showToast('Network error. Please try again.', 'error');
      }
    }
  );
}

// ─── AJAX: Delete Menu Item ─────────────────────────────────────────────────
function deleteMenuItem(id, name) {
  openModal(
    `Delete menu item <strong>${escHtml(name)}</strong>? This cannot be undone.`,
    async () => {
      try {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', getCsrf());
        fd.append('ajax', '1');

        const res = await fetch('index.php?page=menu_item_delete', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
          const card = document.getElementById('menu-item-' + id);
          if (card) {
            card.style.transition = 'opacity .3s';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 320);
            showToast('Menu item deleted.');
          } else {
            // On detail page — go back to restaurant
            const restId = document.getElementById('restaurantId')?.value;
            window.location.href = 'index.php?page=restaurant_detail&id=' + (restId || '');
          }
        } else {
          showToast(data.message || 'Delete failed.', 'error');
        }
      } catch (err) {
        showToast('Network error. Please try again.', 'error');
      }
    }
  );
}

// ─── AJAX: Admin delete review (on menu item detail page) ──────────────────
function adminDeleteReview(id) {
  openModal(
    'Remove this review permanently?',
    async () => {
      try {
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', getCsrf());
        fd.append('ajax', '1');

        const res = await fetch('index.php?page=delete_review', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
          const el = document.getElementById('review-' + id);
          if (el) { el.style.opacity = '0'; setTimeout(() => el.remove(), 300); }
          showToast('Review removed.');
        } else {
          showToast(data.message || 'Failed.', 'error');
        }
      } catch (err) {
        showToast('Network error.', 'error');
      }
    }
  );
}

// ─── JS Validation: Restaurant Form ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const restaurantForm = document.getElementById('restaurantForm');
  if (restaurantForm) {
    restaurantForm.addEventListener('submit', (e) => {
      clearErrors(restaurantForm);
      let valid = true;

      valid = requireField(restaurantForm, 'name', 'Name is required.') && valid;
      valid = requireField(restaurantForm, 'location', 'Location is required.') && valid;
      valid = requireField(restaurantForm, 'area', 'Area is required.') && valid;
      valid = requireField(restaurantForm, 'short_background', 'Background is required.') && valid;
      valid = requireField(restaurantForm, 'goals', 'Goals are required.') && valid;

      if (!valid) e.preventDefault();
    });
  }

  // ─── JS Validation: Menu Item Form ───────────────────────────────────────
  const menuItemForm = document.getElementById('menuItemForm');
  if (menuItemForm) {
    menuItemForm.addEventListener('submit', (e) => {
      clearErrors(menuItemForm);
      let valid = true;

      valid = requireField(menuItemForm, 'name', 'Item name is required.') && valid;
      valid = requireField(menuItemForm, 'description', 'Description is required.') && valid;

      const priceEl = menuItemForm.querySelector('[name=price]');
      const price = parseFloat(priceEl?.value);
      if (!priceEl || isNaN(price) || price <= 0) {
        showFieldError(priceEl, 'Price must be a positive number.');
        valid = false;
      }

      // Image validation (only if a file is selected)
      const imageEl = menuItemForm.querySelector('[name=image]');
      if (imageEl && imageEl.files.length > 0) {
        const file = imageEl.files[0];
        if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
          showFieldError(imageEl, 'Only JPEG and PNG images are allowed.');
          valid = false;
        } else if (file.size > 2 * 1024 * 1024) {
          showFieldError(imageEl, 'Image must be under 2 MB.');
          valid = false;
        }
      }

      if (!valid) e.preventDefault();
    });

    // Live image preview
    const imgInput = menuItemForm.querySelector('[name=image]');
    const livePreview = document.getElementById('imgLivePreview');
    if (imgInput && livePreview) {
      imgInput.addEventListener('change', () => {
        const file = imgInput.files[0];
        if (file && ['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
          const reader = new FileReader();
          reader.onload = (ev) => {
            livePreview.src = ev.target.result;
            livePreview.classList.remove('hidden');
          };
          reader.readAsDataURL(file);
        } else {
          livePreview.classList.add('hidden');
        }
      });
    }
  }
});

// ─── Validation Helpers ─────────────────────────────────────────────────────
function requireField(form, name, msg) {
  const el = form.querySelector(`[name=${name}]`);
  if (!el || el.value.trim() === '') {
    showFieldError(el, msg);
    return false;
  }
  return true;
}

function showFieldError(el, msg) {
  if (!el) return;
  el.classList.add('input-error');
  const span = document.createElement('span');
  span.className = 'error-msg';
  span.textContent = msg;
  el.insertAdjacentElement('afterend', span);
}

function clearErrors(form) {
  form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
  form.querySelectorAll('.error-msg').forEach(el => el.remove());
}

function escHtml(str) {
  const d = document.createElement('div');
  d.appendChild(document.createTextNode(str));
  return d.innerHTML;
}

// ─── CSS animation for toast ─────────────────────────────────────────────
const style = document.createElement('style');
style.textContent = `@keyframes fadeIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }`;
document.head.appendChild(style);
