<?php
    session_start();
    header("Content-Type: application/json");

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit();
    }

    require_once('../model/adminModel.php');

    $id = (int)$_POST['id'];

    deleteMember($id);

    echo json_encode(["status" => "success"]);
?>