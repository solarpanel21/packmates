<?php
session_start();

if (!isset($_SESSION['logged_in'])) {
    // Store intended destination and redirect to login
    $_SESSION['invite_code'] = trim($_GET['code'] ?? '');
    header("Location: welcome.php");
    exit();
}

require("connectionInclude.php");

$code = trim($_GET['code'] ?? '');
$user_id = filter_var($_SESSION['logged_in_user_id'] ?? null, FILTER_VALIDATE_INT);

if ($user_id === false || $user_id === null) {
    header("Location: logout.php");
    exit();
}

if ($code === '' || !preg_match('/^[a-f0-9]{16}$/i', $code)) {
    header("Location: home.php");
    exit();
}

// Look up the invite
$invite_stmt = $mysqli->prepare("SELECT inviteid, uses, tripid FROM invites WHERE code = ?");
$invite_stmt->bind_param("s", $code);
$invite_stmt->execute();
$invite = $invite_stmt->get_result()->fetch_assoc();

if (!$invite) {
    die("Invalid invite link.");
}
if ($invite['uses'] <= 0) {
    die("This invite link has no uses remaining.");
}

$tripid = $invite['tripid'];

// Check if user is already a member or the owner
$already_stmt = $mysqli->prepare("SELECT tripmembersid FROM tripmembers WHERE tripid = ? AND userid = ?");
$already_stmt->bind_param("ii", $tripid, $user_id);
$already_stmt->execute();
$already = $already_stmt->get_result();

$owner_stmt = $mysqli->prepare("SELECT tripid FROM trips WHERE tripid = ? AND userid = ?");
$owner_stmt->bind_param("ii", $tripid, $user_id);
$owner_stmt->execute();
$is_owner = $owner_stmt->get_result();

if ($already->num_rows > 0 || $is_owner->num_rows > 0) {
    header("Location: tripPreview.php?tripid=$tripid");
    exit();
}

// Add user to tripmembers
$joindate = date('Y-m-d');
$insert_stmt = $mysqli->prepare("INSERT INTO tripmembers (role, joindate, tripid, userid) VALUES ('viewer', ?, ?, ?)");
$insert_stmt->bind_param("sii", $joindate, $tripid, $user_id);
$insert_stmt->execute();

// Decrement uses
$invite_id = (int)$invite['inviteid'];
$decrement_stmt = $mysqli->prepare("UPDATE invites SET uses = uses - 1 WHERE inviteid = ?");
$decrement_stmt->bind_param("i", $invite_id);
$decrement_stmt->execute();

$mysqli->close();
header("Location: tripPreview.php?tripid=$tripid");
exit();
?>
