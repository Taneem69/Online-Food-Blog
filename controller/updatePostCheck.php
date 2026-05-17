<?php
    session_start();
    require_once('../model/foodExperienceModel.php');

    if (!isset($_SESSION['user_id'])) {
        header("location: ../view/login.php");
        exit();
    }

    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        header("location: ../view/food_experience.php");
        exit();
    }

    $id        = (int)trim($_POST['id']);
    $title     = trim($_POST['title']);
    $content   = trim($_POST['content']);
    $post_type = trim($_POST['post_type']);
    $allowed   = ['food', 'restaurant', 'both'];

    // Validation — session error + redirect, not die()
    if (empty($id) || empty($title) || empty($content) || !in_array($post_type, $allowed)) {
        $_SESSION['error'] = "All required fields must be filled in.";
        header("location: ../view/edit_post.php?id=" . $id);
        exit();
    }

    $post = getSinglePost($id);

    if (!$post) {
        header("location: ../view/food_experience.php");
        exit();
    }

    if ($_SESSION['user_id'] != $post['user_id'] && $_SESSION['role'] != 'admin') {
        $_SESSION['error'] = "You are not authorized to edit this post.";
        header("location: ../view/food_experience.php");
        exit();
    }

    $title   = htmlspecialchars($title,   ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

    $status = updateFoodPost($id, $title, $content, $post_type);

    if ($status) {
        $_SESSION['success'] = "Post updated successfully!";
        header("location: ../view/food_experience.php");
    } else {
        $_SESSION['error'] = "Database error. Please try again.";
        header("location: ../view/edit_post.php?id=" . $id);
    }
    exit();
?>