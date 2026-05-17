<?php

header('Content-Type: application/json');
session_start();
require_once '../../config/database.php';
require_once '../../models/ReviewModel.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => 'You must be logged in to post a review.'
    ]);
    exit;
}

if ($_SESSION['role'] !== 'member') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error'   => 'Only members can post reviews.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request method.'
    ]);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$menu_item_id = isset($data['menu_item_id'])
                    ? (int) $data['menu_item_id']
                    : null;
$comment      = trim($data['comment'] ?? '');
$user_id      = (int) $_SESSION['user_id'];

if (empty($comment)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Comment cannot be empty.'
    ]);
    exit;
}

if (strlen($comment) > 1000) {
    echo json_encode([
        'success' => false,
        'error'   => 'Comment is too long. Maximum 1000 characters.'
    ]);
    exit;
}

try {
    $reviewModel = new ReviewModel($pdo);
    $newId       = $reviewModel->add($menu_item_id, $user_id, $comment);
    $newReview   = $reviewModel->getById($newId);

    echo json_encode([
        'success' => true,
        'message' => 'Review posted successfully.',
        'review'  => $newReview
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to save review. Please try again.'
    ]);
}