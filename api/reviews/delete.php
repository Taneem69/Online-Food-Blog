<?php
header('Content-Type: application/json');
session_start();

require_once '../../config/database.php';
/** @var \PDO $pdo */

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error'   => 'You must be logged in to delete a review.'
    ]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid request method.'
    ]);
    exit;
}

// ─────────────────────────────────────────────────────────────
$review_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id   = (int) $_SESSION['user_id'];

if ($review_id <= 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'Invalid review ID.'
    ]);
    exit;
}

$checkStmt = $pdo->prepare("
    SELECT id, user_id
    FROM reviews
    WHERE id = ?
");
$checkStmt->execute([$review_id]);
$review = $checkStmt->fetch();

if (!$review) {
    echo json_encode([
        'success' => false,
        'error'   => 'Review not found.'
    ]);
    exit;
}

if ((int) $review['user_id'] !== $user_id) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error'   => 'You can only delete your own reviews.'
    ]);
    exit;
}

try {
    $deleteStmt = $pdo->prepare("
        DELETE FROM reviews
        WHERE id = ? AND user_id = ?
    ");
    
    $deleteStmt->execute([$review_id, $user_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Review deleted successfully.'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to delete review. Please try again.'
    ]);
}