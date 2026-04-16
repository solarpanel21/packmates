<?php
function getTripMembers($mysqli, $tripid) {
    $tripid = filter_var($tripid, FILTER_VALIDATE_INT);
    if ($tripid === false || $tripid === null) {
        return [];
    }

    $stmt = $mysqli->prepare("
        SELECT u.userid, u.username, u.pfpurl
        FROM users u
        JOIN trips t ON t.userid = u.userid
        WHERE t.tripid = ?
        UNION
        SELECT u.userid, u.username, u.pfpurl
        FROM users u
        JOIN tripmembers tm ON tm.userid = u.userid
        WHERE tm.tripid = ?
    ");
    $stmt->bind_param("ii", $tripid, $tripid);
    $stmt->execute();
    $result = $stmt->get_result();
    $members = [];
    while ($row = $result->fetch_assoc()) $members[] = $row;
    return $members;
}

function renderMemberAvatars($members, $overlap = true) {
    $html = '<div class="member-avatars' . ($overlap ? ' overlap' : '') . '">';
    foreach ($members as $m) {
        $pfp  = !empty($m['pfpurl']) ? htmlspecialchars($m['pfpurl']) : 'img/profile.png';
        $name = htmlspecialchars($m['username']);
        $html .= '<img src="' . $pfp . '" alt="' . $name . '" title="' . $name . '" class="member-avatar">';
    }
    $html .= '</div>';
    return $html;
}
?>
