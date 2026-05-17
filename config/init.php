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
        die('Invalid form request.');
    }
}
