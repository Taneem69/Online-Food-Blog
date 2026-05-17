<?php

    session_start();

    header("Content-Type: application/json");

    require_once('../model/foodExperienceModel.php');

    if(!isset($_SESSION['user_id'])){

        echo json_encode(["status"=>"error", "message"=>"Login Required"]);
        exit();
    }

    $post_id = (int)$_POST['post_id'];
    $comment = trim($_POST['comment']);

    if(empty($comment)){

        echo json_encode(["status"=>"error", "message"=>"Comment Required"]);
        exit();
    }

    $comment = htmlspecialchars($comment);

    $status = addComment($post_id,$_SESSION['user_id'],$comment);

    if($status){

        echo json_encode(["status"=>"success"]);
    }
    else{

        echo json_encode(["status"=>"error"]);
    }

?>