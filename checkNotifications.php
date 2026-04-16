<?php
function checkTripNotifications($mysqli) {
    // Find trips starting within 24 hours that haven't been notified for 24hr yet
    $trips_24h = $mysqli->query("
        SELECT t.tripid, t.tripname, t.userid AS owner_id
        FROM trips t
        WHERE t.startdate BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
        AND (t.isdeleted = 0 OR t.isdeleted IS NULL)
        AND (t.notified_24h = 0 OR t.notified_24h IS NULL)
    ");
    sendNotificationsForTrips($mysqli, $trips_24h, '24h');

    // Find trips starting within 7 days (but not within 24 hours) that haven't been notified for 1 week yet
    $trips_7d = $mysqli->query("
        SELECT t.tripid, t.tripname, t.userid AS owner_id
        FROM trips t
        WHERE t.startdate BETWEEN DATE_ADD(NOW(), INTERVAL 24 HOUR) AND DATE_ADD(NOW(), INTERVAL 7 DAY)
        AND (t.isdeleted = 0 OR t.isdeleted IS NULL)
        AND (t.notified_7d = 0 OR t.notified_7d IS NULL)
    ");
    sendNotificationsForTrips($mysqli, $trips_7d, '7d');
}

function sendNotificationsForTrips($mysqli, $trips, $type) {
    while ($trip = $trips->fetch_assoc()) {
        $tripid = (int)$trip['tripid'];
        $owner_id = (int)$trip['owner_id'];
        $tripname = (string)$trip['tripname'];
        $message = (
            $type === '24h'
               ? "Your trip '$tripname' starts in less than 24 hours!"
               : "Your trip '$tripname' is coming up in less than a week!"
        );
        $now = date('Y-m-d H:i:s');

        // Get all users to notify: owner + tripmembers
        $users_stmt = $mysqli->prepare("
            SELECT userid FROM tripmembers WHERE tripid = ?
            UNION
            SELECT ? AS userid
        ");
        $users_stmt->bind_param("ii", $tripid, $owner_id);
        $users_stmt->execute();
        $users = $users_stmt->get_result();

        while ($user = $users->fetch_assoc()) {
            $uid = (int)$user['userid'];
            $exists_stmt = $mysqli->prepare("SELECT notifid FROM notifications WHERE userid = ? AND tripid = ? AND message = ?");
            $exists_stmt->bind_param("iis", $uid, $tripid, $message);
            $exists_stmt->execute();
            $exists = $exists_stmt->get_result();
            if ($exists->num_rows === 0) {
                $insert_stmt = $mysqli->prepare("INSERT INTO notifications (userid, tripid, message, isread, createdat)
                                VALUES (?, ?, ?, 0, ?)");
                $insert_stmt->bind_param("iiss", $uid, $tripid, $message, $now);
                $insert_stmt->execute();
            }
        }

        // Mark trip as notified for this threshold
        $col = $type === '24h' ? 'notified_24h' : 'notified_7d';
        if (!in_array($col, ['notified_24h', 'notified_7d'], true)) {
            continue;
        }
        $update_stmt = $mysqli->prepare("UPDATE trips SET $col = 1 WHERE tripid = ?");
        $update_stmt->bind_param("i", $tripid);
        $update_stmt->execute();
    }
}
?>
