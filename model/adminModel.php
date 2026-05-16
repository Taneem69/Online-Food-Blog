<?php

    require_once('../config/database.php');
    
    function getAllMembers(){
        global $conn;
    
        $stmt = mysqli_prepare($conn,"SELECT * FROM users WHERE role='member'");
    
        mysqli_stmt_execute($stmt);
    
        $result = mysqli_stmt_get_result($stmt);
    
        $users = [];
    
        while($row = mysqli_fetch_assoc($result)){
            $users[] = $row;
        }
    
        return $users;
    }
    
    function deleteMember($id) {
        global $conn;
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=? AND role='member'");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
    
    function getAllReviews(){
        global $conn;
    
        $stmt = mysqli_prepare($conn,
            "SELECT reviews.*, users.name
            FROM reviews
            INNER JOIN users
            ON reviews.user_id = users.id
            ORDER BY reviews.created_at DESC"
        );
    
        mysqli_stmt_execute($stmt);
    
        $result = mysqli_stmt_get_result($stmt);
    
        $reviews = [];
    
        while($row = mysqli_fetch_assoc($result)){
            $reviews[] = $row;
        }
    
        return $reviews;
    }
    
    function deleteReview($id){
        global $conn;
    
        $stmt = mysqli_prepare($conn,
            "DELETE FROM reviews WHERE id=?"
        );
    
        mysqli_stmt_bind_param($stmt, "i", $id);
    
        return mysqli_stmt_execute($stmt);
    }

    function getAllFoodExpComments() {
        global $conn;

        $stmt = mysqli_prepare($conn,
            "SELECT food_experience_comments.*, users.name
             FROM food_experience_comments
             INNER JOIN users ON food_experience_comments.user_id = users.id
             ORDER BY food_experience_comments.created_at DESC"
        );

        mysqli_stmt_execute($stmt);
        $result= mysqli_stmt_get_result($stmt);
        $comments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $comments[] = $row;
        }
        return $comments;
    }



?>
