<?php

    $host="127.0.0.1";
    $user="root";
    $password="";
    $name="online food blog";


    function connection(){
        global $host;
        global $user;
        global $password;
        global $name;


        $con=mysqli_connect($host, $user, $password, $name);


        if($con){
            return true;
        }

        else{
            return false;
        }

    }
?>