<?php
$nav_user = $mysqli->query("SELECT pfpurl FROM users WHERE userid = {$_SESSION['logged_in_user_id']}")->fetch_assoc();
$nav_pfp  = !empty($nav_user['pfpurl']) ? htmlspecialchars($nav_user['pfpurl']) : 'img/profile.png';
?>