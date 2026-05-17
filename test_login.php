<?php
// ============================================================
//  test_login.php
//  TEMPORARY FILE FOR TESTING ONLY
//  Delete this file before final submission!
// ============================================================

session_start();

// Fake a member session
$_SESSION['user_id'] = 2;
$_SESSION['name']    = 'Nusrat Jahan';
$_SESSION['role']    = 'member';

echo "✅ You are now logged in as a member!";
echo "<br><br>";
echo "<a href='/Online-Food-Blog/view/browse/index.php'>Go to Browse Page</a>";
?>