<?php
function requireAdmin(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['flash_error'] = 'Access denied. Admins only.';
        header('Location: /index.php?page=login');
        exit;
    }
}

function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isMember(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'member';
}

function flashMessage(string $key): ?string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
    }
    return null;
}

function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('CSRF token mismatch.');
    }
}
