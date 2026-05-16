<?php
session_start();

$page = isset($_GET['page']) ? $_GET['page'] : 'food-experience';

if ($page === 'food-experience') {
    include 'view/food_experience.php';

} elseif ($page === 'add-post') {
    include 'view/add_post.php';

} elseif ($page === 'edit-post') {
    include 'view/edit_post.php';

} elseif ($page === 'admin') {
    include 'view/admin_panel.php';

} else {
    include 'view/food_experience.php';
}
?>