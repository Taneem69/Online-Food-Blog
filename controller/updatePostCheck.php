<?php

    session_start();

    require_once('../model/foodExperienceModel.php');

    if(!isset($_SESSION['user_id'])){
        header("location: ../view/login.php");
        exit();
    }

    $id = trim($_POST['id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $post_type = trim($_POST['post_type']);
    $allowed = ['food','restaurant','both'];

    if(empty($id) || empty($title) || empty($content) || !in_array($post_type, $allowed) ){
        die("Invalid Input");
    }

    $post = getSinglePost($id);

    if($_SESSION['user_id'] != $post['user_id'] && $_SESSION['role'] != 'admin' ){
        die("Unauthorized");
    }

    $title = htmlspecialchars($title);
    $content = htmlspecialchars($content);

    $status = updateFoodPost($id,$title,$content,$post_type);

    if($status){
        header("location: ../view/food_experience.php");
    }
    else{
        echo "Database Error";
    }

?>