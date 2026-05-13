<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

function isMember(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'member';
}

function setFlash(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}

function flashMessage(string $key): ?string
{
    if (!empty($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }

    return null;
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        setFlash('flash_error', 'Please login first.');
        redirect('index.php?page=login');
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        setFlash('flash_error', 'Admin access required.');
        redirect('index.php?page=home');
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    if (
        empty($_POST['csrf_token']) ||
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        setFlash('flash_error', 'Invalid form request. Please try again.');
        redirect('index.php?page=home');
    }
}

function checkRememberLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    if (empty($_COOKIE['remember_me'])) {
        return;
    }

    $parts = explode(':', $_COOKIE['remember_me']);

    if (count($parts) !== 2) {
        return;
    }

    $userId = (int)$parts[0];
    $token = $parts[1];
    $tokenHash = hash('sha256', $token);

    $stmt = getDB()->prepare(
        "SELECT id, name, role, remember_token 
         FROM users 
         WHERE id = ?"
    );

    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (
        $user &&
        !empty($user['remember_token']) &&
        hash_equals($user['remember_token'], $tokenHash)
    ) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
    }
}

checkRememberLogin();