<?php
session_start();
if (isset($_SESSION['logged_in'])) {
	unset($_SESSION['logged_in']);
}
if (isset($_SESSION['logged_in_user'])) {
	unset($_SESSION['logged_in_user']);
}
if (isset($_SESSION['logged_in_user_user_access'])) {
	unset($_SESSION['logged_in_user_user_access']);
}
if (isset($_SESSION['logged_in_user_id'])) {
	unset($_SESSION['logged_in_user_id']);
}
header("Location: welcome.php");
?>
