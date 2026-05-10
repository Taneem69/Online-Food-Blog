<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "online food blog";

function connection() {
    global $host, $user, $password, $dbname;
    $con = mysqli_connect($host, $user, $password, $dbname);
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, "utf8mb4");
    return $con;
}

$conn = connection();  
?>