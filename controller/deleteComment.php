<?php
    session_start();
    header("Content-Type: application/json");
    require_once('../model/foodExperienceModel.php');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Login required"]);
        exit();
    }

    $id      = (int)$_POST['id'];
    $comment = getSingleComment($id);

    if (!$comment) {
        echo json_encode(["status" => "error", "message" => "Comment not    found"]);
        exit();
    }

    if ($_SESSION['user_id'] != $comment['user_id'] && $_SESSION['role'] !='admin') {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }

    deleteComment($id);
    echo json_encode(["status" => "success"]);
?>