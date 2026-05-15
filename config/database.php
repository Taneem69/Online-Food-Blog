<?php

    $host = "127.0.0.1";
    $user = "root";
    $pass = "";
    $db = "online food blog";

    $conn = mysqli_connect($host, $user, $pass, $db);

    if(!$conn){
        die("Database Connection Failed");
    }

    mysqli_set_charset($conn, 'utf8mb4');
    
?>