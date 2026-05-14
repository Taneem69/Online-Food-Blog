<?php

    session_start();

    header("Content-Type: application/json");

    if($_SESSION['role'] != 'admin'){

        echo json_encode(["status"=>"error"]);

        exit();
    }

    require_once('../model/adminModel.php');

    $id = $_POST['id'];

    deleteReview($id);

    echo json_encode(["status"=>"success"]);

?>