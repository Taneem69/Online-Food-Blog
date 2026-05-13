<?php

    session_start();

    header("Content-Type: application/json");

    require_once('../model/foodExperienceModel.php');

    $id = $_POST['id'];

    $comment = getSingleComment($id);

    if($_SESSION['user_id'] != $comment['user_id'] && $_SESSION['role'] != 'admin' ){

        echo json_encode(["status"=>"error"]);

        exit();
    }

    deleteComment($id);

    echo json_encode(["status"=>"success"]);

?>