<?php
function getTripMembers($mysqli, $tripid) {
    $result = $mysqli->query("
        SELECT u.userid, u.username, u.pfpurl
        FROM users u
        JOIN trips t ON t.userid = u.userid
        WHERE t.tripid = $tripid
        UNION
        SELECT u.userid, u.username, u.pfpurl
        FROM users u
        JOIN tripmembers tm ON tm.userid = u.userid
        WHERE tm.tripid = $tripid
    ");
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