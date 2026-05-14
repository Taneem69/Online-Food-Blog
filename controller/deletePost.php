<?php

    session_start();

    header("Content-Type: application/json");

    require_once('../model/foodExperienceModel.php');

    $id = $_POST['id'];

    $post = getSinglePost($id);

    if($_SESSION['user_id'] != $post['user_id'] && $_SESSION['role'] != 'admin' ){

        echo json_encode(["status"=>"error"]);

        exit();
    }

    deleteFoodPost($id);

    echo json_encode(["status"=>"success"]);

?>