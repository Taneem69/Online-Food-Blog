<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/user.php';

$email = 'mim@gmail.com';
$password = '12345678';

$user = User::findByEmail(getDB(), $email);

echo "<pre>";

if (!$user) {
    echo "User not found";
    exit;
}

echo "User found:\n";
print_r([
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role'],
    'hash' => $user['password_hash']
]);

echo "\nPassword verify result:\n";
var_dump(password_verify($password, $user['password_hash']));

echo "</pre>";