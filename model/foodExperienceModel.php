<?php

    require_once('../config/database.php');

    function getAllFoodPosts(){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "SELECT food_experience_posts.*, users.name
            FROM food_experience_posts
            INNER JOIN users
            ON food_experience_posts.user_id = users.id
            ORDER BY food_experience_posts.created_at   DESC"
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $posts = [];

        while($row = mysqli_fetch_assoc($result)){
            $posts[] = $row;
        }

        return $posts;
    }

    function getSinglePost($id){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "SELECT * FROM food_experience_posts WHERE  id=?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    function addFoodPost($user_id,$title,$content,$post_type,$restaurant_id,$menu_item_id){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "INSERT INTO food_experience_posts
            (
                user_id,
                title,
                content,
                post_type,
                restaurant_id,
                menu_item_id
            )
            VALUES(?,?,?,?,?,?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isssii",
            $user_id,
            $title,
            $content,
            $post_type,
            $restaurant_id,
            $menu_item_id
        );

        return mysqli_stmt_execute($stmt);
    }

    function updateFoodPost($id, $title, $content,  $post_type){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "UPDATE food_experience_posts
            SET title=?, content=?, post_type=?
            WHERE id=?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssi",
            $title,
            $content,
            $post_type,
            $id
        );

        return mysqli_stmt_execute($stmt);
    }

    function deleteFoodPost($id){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "DELETE FROM food_experience_posts WHERE    id=?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    function getCommentsByPost($post_id){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "SELECT food_experience_comments.*, users.  name
            FROM food_experience_comments
            INNER JOIN users
            ON food_experience_comments.user_id =   users.id
            WHERE post_id=?
            ORDER BY created_at DESC"
        );

        mysqli_stmt_bind_param($stmt, "i", $post_id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $comments = [];

        while($row = mysqli_fetch_assoc($result)){
            $comments[] = $row;
        }

        return $comments;
    }

    function addComment($post_id, $user_id, $comment){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "INSERT INTO food_experience_comments
            (
                post_id,
                user_id,
                comment
            )
            VALUES(?,?,?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "iis",
            $post_id,
            $user_id,
            $comment
        );

        return mysqli_stmt_execute($stmt);
    }

    function deleteComment($id){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "DELETE FROM food_experience_comments   WHERE id=?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    function getSingleComment($id){
        global $conn;

        $stmt = mysqli_prepare($conn,
            "SELECT * FROM food_experience_comments     WHERE id=?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }


    function getAllRestaurantsForForm() {
        global $conn;
        $result = mysqli_query($conn, "SELECT id, name  FROM restaurants ORDER BY name ASC");
        $list   = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
        return $list;
    }

    function getAllMenuItemsForForm() {
        global $conn;
        $result = mysqli_query($conn, "SELECT id, name  FROM menu_items ORDER BY name ASC");
        $list   = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
        return $list;
    }

?>
