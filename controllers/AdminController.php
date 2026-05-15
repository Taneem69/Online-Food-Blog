<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/RestaurantModel.php';
require_once __DIR__ . '/../models/MenuItemModel.php';

class AdminController {

    private RestaurantModel $restaurantModel;
    private MenuItemModel   $menuItemModel;

    public function __construct() {
        $this->restaurantModel = new RestaurantModel();
        $this->menuItemModel   = new MenuItemModel();
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard(): void {
        requireAdmin();
        $stats = [
            'restaurants'         => $this->restaurantModel->countAll(),
            'menu_items'          => $this->menuItemModel->countAll(),
            'reviews'             => $this->menuItemModel->countReviews(),
            'food_exp_posts'      => $this->menuItemModel->countFoodExperiencePosts(),
        ];
        $restaurants = $this->restaurantModel->getAll();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    // ─── Restaurants ──────────────────────────────────────────────────────────

    public function restaurantCreate(): void {
        requireAdmin();
        $errors = [];
        $old    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrf();
            $old = [
                'name'             => trim($_POST['name'] ?? ''),
                'location'         => trim($_POST['location'] ?? ''),
                'area'             => trim($_POST['area'] ?? ''),
                'short_background' => trim($_POST['short_background'] ?? ''),
                'goals'            => trim($_POST['goals'] ?? ''),
            ];

            // PHP Validation
            if ($old['name'] === '')             $errors['name']             = 'Name is required.';
            if ($old['location'] === '')         $errors['location']         = 'Location is required.';
            if ($old['area'] === '')             $errors['area']             = 'Area is required.';
            if ($old['short_background'] === '') $errors['short_background'] = 'Background is required.';
            if ($old['goals'] === '')            $errors['goals']            = 'Goals are required.';

            if (empty($errors)) {
                $this->restaurantModel->create($old);
                $_SESSION['flash_success'] = 'Restaurant created successfully.';
                header('Location: index.php?page=admin_dashboard');
                exit;
            }
        }

        include __DIR__ . '/../views/admin/restaurant_form.php';
    }

    public function restaurantEdit(): void {
        requireAdmin();
        $id         = (int)($_GET['id'] ?? 0);
        $restaurant = $this->restaurantModel->getById($id);

        if (!$restaurant) {
            $_SESSION['flash_error'] = 'Restaurant not found.';
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        $errors = [];
        $old    = $restaurant;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrf();
            $old = [
                'name'             => trim($_POST['name'] ?? ''),
                'location'         => trim($_POST['location'] ?? ''),
                'area'             => trim($_POST['area'] ?? ''),
                'short_background' => trim($_POST['short_background'] ?? ''),
                'goals'            => trim($_POST['goals'] ?? ''),
            ];

            if ($old['name'] === '')             $errors['name']             = 'Name is required.';
            if ($old['location'] === '')         $errors['location']         = 'Location is required.';
            if ($old['area'] === '')             $errors['area']             = 'Area is required.';
            if ($old['short_background'] === '') $errors['short_background'] = 'Background is required.';
            if ($old['goals'] === '')            $errors['goals']            = 'Goals are required.';

            if (empty($errors)) {
                $this->restaurantModel->update($id, $old);
                $_SESSION['flash_success'] = 'Restaurant updated successfully.';
                header('Location: index.php?page=restaurant_detail&id=' . $id);
                exit;
            }
        }

        include __DIR__ . '/../views/admin/restaurant_form.php';
    }

    public function restaurantDelete(): void {
        requireAdmin();

        // Accept both AJAX and normal POST
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                   || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) { $this->jsonError('Method not allowed', 405); } else { http_response_code(405); exit; }
        }

        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);

        if (!$this->restaurantModel->getById($id)) {
            if ($isAjax) { $this->jsonError('Restaurant not found', 404); exit; }
            $_SESSION['flash_error'] = 'Restaurant not found.';
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        $this->restaurantModel->delete($id);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Restaurant deleted.']);
            exit;
        }

        $_SESSION['flash_success'] = 'Restaurant and all its menu items deleted.';
        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    // ─── Menu Items ───────────────────────────────────────────────────────────

    public function menuItemCreate(): void {
        requireAdmin();
        $restaurantId = (int)($_GET['restaurant_id'] ?? 0);
        $restaurant   = $this->restaurantModel->getById($restaurantId);

        if (!$restaurant) {
            $_SESSION['flash_error'] = 'Restaurant not found.';
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        $errors = [];
        $old    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrf();
            $old = [
                'name'          => trim($_POST['name'] ?? ''),
                'description'   => trim($_POST['description'] ?? ''),
                'price'         => trim($_POST['price'] ?? ''),
                'restaurant_id' => $restaurantId,
                'image_path'    => null,
            ];

            if ($old['name'] === '')        $errors['name']        = 'Name is required.';
            if ($old['description'] === '') $errors['description']  = 'Description is required.';
            if (!is_numeric($old['price']) || (float)$old['price'] <= 0)
                                            $errors['price']        = 'Price must be a positive number.';

            // Image upload
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if (isset($uploadResult['error'])) {
                    $errors['image'] = $uploadResult['error'];
                } else {
                    $old['image_path'] = $uploadResult['path'];
                }
            }

            if (empty($errors)) {
                $old['price'] = (float) $old['price'];
                $newId = $this->menuItemModel->create($old);
                $_SESSION['flash_success'] = 'Menu item added successfully.';
                header('Location: index.php?page=menu_item_detail&id=' . $newId);
                exit;
            }
        }

        include __DIR__ . '/../views/admin/menu_item_form.php';
    }

    public function menuItemEdit(): void {
        requireAdmin();
        $id   = (int)($_GET['id'] ?? 0);
        $item = $this->menuItemModel->getById($id);

        if (!$item) {
            $_SESSION['flash_error'] = 'Menu item not found.';
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        $restaurant = $this->restaurantModel->getById($item['restaurant_id']);
        $errors     = [];
        $old        = $item;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verifyCsrf();
            $old = [
                'name'          => trim($_POST['name'] ?? ''),
                'description'   => trim($_POST['description'] ?? ''),
                'price'         => trim($_POST['price'] ?? ''),
                'restaurant_id' => $item['restaurant_id'],
                'image_path'    => $item['image_path'],
            ];

            if ($old['name'] === '')        $errors['name']       = 'Name is required.';
            if ($old['description'] === '') $errors['description'] = 'Description is required.';
            if (!is_numeric($old['price']) || (float)$old['price'] <= 0)
                                            $errors['price']       = 'Price must be a positive number.';

            // Replace image?
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->handleImageUpload($_FILES['image']);
                if (isset($uploadResult['error'])) {
                    $errors['image'] = $uploadResult['error'];
                } else {
                    // Delete old image
                    if ($item['image_path'] && file_exists(__DIR__ . '/../public/' . $item['image_path'])) {
                        unlink(__DIR__ . '/../public/' . $item['image_path']);
                    }
                    $old['image_path'] = $uploadResult['path'];
                }
            }

            if (empty($errors)) {
                $old['price'] = (float) $old['price'];
                $this->menuItemModel->update($id, $old);
                $_SESSION['flash_success'] = 'Menu item updated.';
                header('Location: index.php?page=menu_item_detail&id=' . $id);
                exit;
            }
        }

        include __DIR__ . '/../views/admin/menu_item_form.php';
    }

    public function menuItemDelete(): void {
        requireAdmin();

        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                   || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) { $this->jsonError('Method not allowed', 405); } else { http_response_code(405); exit; }
        }

        verifyCsrf();
        $id   = (int)($_POST['id'] ?? 0);
        $item = $this->menuItemModel->getById($id);

        if (!$item) {
            if ($isAjax) { $this->jsonError('Item not found', 404); exit; }
            $_SESSION['flash_error'] = 'Menu item not found.';
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        $restaurantId = $item['restaurant_id'];

        // Delete image file
        if ($item['image_path'] && file_exists(__DIR__ . '/../public/' . $item['image_path'])) {
            unlink(__DIR__ . '/../public/' . $item['image_path']);
        }

        $this->menuItemModel->delete($id);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Menu item deleted.']);
            exit;
        }

        $_SESSION['flash_success'] = 'Menu item deleted.';
        header('Location: index.php?page=restaurant_detail&id=' . $restaurantId);
        exit;
    }


    public function restaurantList(): void {
        $restaurants = $this->restaurantModel->getAll();
        include __DIR__ . '/../views/restaurants/list.php';
    }

    public function restaurantDetail(): void {
        $id         = (int)($_GET['id'] ?? 0);
        $restaurant = $this->restaurantModel->getById($id);

        if (!$restaurant) {
            http_response_code(404);
            include __DIR__ . '/../views/partials/404.php';
            return;
        }

        $menuItems = $this->menuItemModel->getByRestaurant($id);
        include __DIR__ . '/../views/restaurants/detail.php';
    }

    public function menuItemDetail(): void {
        $id   = (int)($_GET['id'] ?? 0);
        $item = $this->menuItemModel->getById($id);

        if (!$item) {
            http_response_code(404);
            include __DIR__ . '/../views/partials/404.php';
            return;
        }

     
        $stmt = getDB()->prepare(
            "SELECT rv.*, u.name AS member_name
             FROM reviews rv
             JOIN users u ON u.id = rv.user_id
             WHERE rv.menu_item_id = ?
             ORDER BY rv.created_at DESC"
        );
        $stmt->execute([$id]);
        $reviews = $stmt->fetchAll();

        include __DIR__ . '/../views/menu_items/detail.php';
    }

  

    public function deleteReview(): void {
        requireAdmin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'POST required']); exit;
        }

        verifyCsrf();
        $id = (int)($_POST['id'] ?? 0);
        $stmt = getDB()->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Review removed.']);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function handleImageUpload(array $file): array {
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
        $maxSize      = 2 * 1024 * 1024; // 2 MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Upload failed. Please try again.'];
        }

        // Validate MIME type via finfo (server-side)
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedMimes, true)) {
            return ['error' => 'Only JPEG and PNG images are allowed.'];
        }

        if ($file['size'] > $maxSize) {
            return ['error' => 'Image must be under 2 MB.'];
        }

        $ext      = ($mimeType === 'image/png') ? '.png' : '.jpg';
        $filename = 'menu_' . bin2hex(random_bytes(8)) . $ext;
        $dest     = __DIR__ . '/../public/uploads/menu/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['error' => 'Could not save image.'];
        }

        return ['path' => 'uploads/menu/' . $filename];
    }

    private function jsonError(string $message, int $code = 400): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }
}
