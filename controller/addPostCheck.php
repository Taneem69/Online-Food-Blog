<?php

    session_start();

    require_once('../model/foodExperienceModel.php');

    if(!isset($_SESSION['user_id'])){
        exit();
    }

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $post_type = trim($_POST['post_type']);

    $restaurant_id = !empty($_POST['restaurant_id']) ? $_POST['restaurant_id'] : NULL;

    $menu_item_id = !empty($_POST['menu_item_id']) ? $_POST['menu_item_id'] : NULL;

    $allowed = ['food','restaurant','both'];

    if(empty($title) || empty($content) || !in_array($post_type, $allowed) ){
        die("Invalid Input");
    }

    $title = htmlspecialchars($title);
    $content = htmlspecialchars($content);

    $status = addFoodPost($_SESSION['user_id'], $title, $content, $post_type, $restaurant_id, $menu_item_id );

    if($status){

        header("location: ../view/food_experience.php");
    }
    else{

        echo "Database Error";
    }

?>