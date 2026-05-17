<?php
    session_start();
    require_once('../model/foodExperienceModel.php');

    //Auth check
    if (!isset($_SESSION['user_id'])) {
        header("location: ../view/login.php");
        exit();
    }

    //CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        header("location: ../view/add_post.php");
        exit();
    }

    $title         = trim($_POST['title']);
    $content       = trim($_POST['content']);
    $post_type     = trim($_POST['post_type']);
    $restaurant_id = !empty($_POST['restaurant_id']) ? (int)$_POST['restaurant_id'] : NULL;
    $menu_item_id  = !empty($_POST['menu_item_id'])  ? (int)$_POST['menu_item_id']  : NULL;
    $allowed       = ['food', 'restaurant', 'both'];

    //Validation — session error + redirect, not die()
    if (empty($title) || empty($content) || !in_array($post_type, $allowed)) {
        $_SESSION['error'] = "All required fields must be filled in correctly.";
        header("location: ../view/add_post.php");
        exit();
    }

    if (strlen($title) > 300) {
        $_SESSION['error'] = "Title cannot exceed 300 characters.";
        header("location: ../view/add_post.php");
        exit();
    }

    $title   = htmlspecialchars($title,   ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

    $status = addFoodPost($_SESSION['user_id'], $title, $content, $post_type, $restaurant_id, $menu_item_id);

    if ($status) {
        $_SESSION['success'] = "Post published successfully!";
        header("location: ../view/food_experience.php");
    } else {
        $_SESSION['error'] = "Database error. Please try again.";
        header("location: ../view/add_post.php");
    }
    exit();
?>