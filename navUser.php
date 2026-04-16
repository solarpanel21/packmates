<?php
$user_id = filter_var($_SESSION['logged_in_user_id'] ?? null, FILTER_VALIDATE_INT);
$nav_user = null;
if ($user_id !== false && $user_id !== null) {
    $stmt = $mysqli->prepare("SELECT pfpurl FROM users WHERE userid = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $nav_user = $stmt->get_result()->fetch_assoc();
}
$nav_pfp  = !empty($nav_user['pfpurl']) ? htmlspecialchars($nav_user['pfpurl']) : 'img/profile.png';
?>
