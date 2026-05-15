<?php
    // controller/deletePost.php
    session_start();
    header("Content-Type: application/json");
    require_once('../model/foodExperienceModel.php');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Login  required"]);
        exit();
    }
    
    $id   = (int)$_POST['id'];
    $post = getSinglePost($id);
    
    if (!$post) {
        echo json_encode(["status" => "error", "message" => "Post not   found"]);
        exit();
    }
    
    if ($_SESSION['user_id'] != $post['user_id'] && $_SESSION['role'] != 'admin') {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }
    
    deleteFoodPost($id);
    echo json_encode(["status" => "success"]);
?>