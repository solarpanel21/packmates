<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    // Store intended destination and redirect to login
    $_SESSION['invite_code'] = $_GET['code'] ?? '';
    header("Location: login.php");
    exit();
}

require("connectionInclude.php");

$code    = $mysqli->real_escape_string($_GET['code'] ?? '');
$user_id = $_SESSION['logged_in_user_id'];

if (empty($code)) {
    header("Location: home.php");
    exit();
}

// Look up the invite
$invite = $mysqli->query("SELECT * FROM invites WHERE code = '$code'")->fetch_assoc();

if (!$invite) {
    die("Invalid invite link.");
}
if ($invite['uses'] <= 0) {
    die("This invite link has no uses remaining.");
}

$tripid = $invite['tripid'];

// Check if user is already a member or the owner
$already = $mysqli->query("SELECT tripmembersid FROM tripmembers WHERE tripid = $tripid AND userid = $user_id");
$is_owner = $mysqli->query("SELECT tripid FROM trips WHERE tripid = $tripid AND userid = $user_id");

if ($already->num_rows > 0 || $is_owner->num_rows > 0) {
    header("Location: tripPreview.php?tripid=$tripid");
    exit();
}

// Add user to tripmembers
$joindate = date('Y-m-d');
$mysqli->query("INSERT INTO tripmembers (role, joindate, tripid, userid) VALUES ('viewer', '$joindate', $tripid, $user_id)");

// Decrement uses
$mysqli->query("UPDATE invites SET uses = uses - 1 WHERE inviteid = {$invite['inviteid']}");

$mysqli->close();
header("Location: tripPreview.php?tripid=$tripid");
exit();
?>