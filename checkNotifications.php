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
        $tripid   = $trip['tripid'];
        $tripname = $mysqli->real_escape_string($trip['tripname']);
        $message = $mysqli->real_escape_string(
            $type === '24h'
               ? "Your trip '$tripname' starts in less than 24 hours!"
               : "Your trip '$tripname' is coming up in less than a week!"
         );
        $now = date('Y-m-d H:i:s');

        // Get all users to notify: owner + tripmembers
        $users = $mysqli->query("
            SELECT userid FROM tripmembers WHERE tripid = $tripid
            UNION
            SELECT {$trip['owner_id']} AS userid
        ");

        while ($user = $users->fetch_assoc()) {
            $uid = $user['userid'];
            $exists = $mysqli->query("SELECT notifid FROM notifications WHERE userid = $uid AND tripid = $tripid AND message = '$message'");
            if ($exists->num_rows === 0) {
                $mysqli->query("INSERT INTO notifications (userid, tripid, message, isread, createdat)
                                VALUES ($uid, $tripid, '$message', 0, '$now')");
            }
        }

        // Mark trip as notified for this threshold
        $col = $type === '24h' ? 'notified_24h' : 'notified_7d';
        $mysqli->query("UPDATE trips SET $col = 1 WHERE tripid = $tripid");
    }
}
?>