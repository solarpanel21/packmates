<?php
session_start();

//check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: logout.php");
    exit();
}

//server connect script
require("connectionInclude.php");

/*
//adding new row script
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    //grab form data
    $game_name = $mysqli->real_escape_string($_POST['game_name']);
    $game_review = $mysqli->real_escape_string($_POST['game_review']);
    $game_rating = intval($_POST['game_rating']);
    $game_image_url = $mysqli->real_escape_string($_POST['game_image_url']);
    $user_id = $_SESSION['logged_in_user_id']; //user id of current user

    //insert script
    $insert_query = "INSERT INTO a5_reviews (game_name, game_review, game_rating, game_image_url, review_creation_date, user_id)
                    VALUES ('$game_name', '$game_review', $game_rating, '$game_image_url', NOW(), $user_id)";

    $mysqli->query($insert_query);
    if ($mysqli->error) {
        print "Insert failed: " . $mysqli->error;
    } else {
        //couldnt figure out the xml on my own then tried to get ai to help me fix it and still couldnt figure it out and im super tired and burnt out so im just gonna take the point off for not getting the xml part working
        function updateXMLFeed($mysqli) {
            $rss = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="0.91"><channel><title>Game Reviews</title><link>http://students.gaim.ucf.edu/~your_username/dig3134/assignment05/reviews.php</link><description>Game Reviews Feed</description></channel></rss>');

            //get review info
            $query = "SELECT r.review_id, r.game_name, r.game_review, r.review_creation_date
                    FROM a5_reviews r
                    ORDER BY r.review_creation_date DESC";
            $result = $mysqli->query($query);

            //add reviews to feed
            while ($row = $result->fetch_assoc()) {
                $item = $rss->channel->addChild('item');
                $item->addChild('title', $row['game_name'] . ' Review');
                $item->addChild('link', 'http://students.gaim.ucf.edu/~th603449' . $_SERVER['REMOTE_USER'] . '/dig3134/assignment05/review.php?review_id=' . $row['review_id']);
                $item->addChild('description', $row['game_review']);
                $item->addChild('date_published', $row['review_creation_date']);
            }

            //save to xml file
            $rss->asXML($_SERVER['DOCUMENT_ROOT'] . '/~th603449' . $_SERVER['REMOTE_USER'] . '/dig3134/assignment05/reviews.xml');
        }

        updateXMLFeed($mysqli);

        header("Location: admin.php");
        exit();
    }
}
*/


//get users info
$select_query = "SELECT userid, username, password, email, pfpurl FROM users";
$select_result = $mysqli->query($select_query);
if ($mysqli->error) {
    print "Select query error!  Message: " . $mysqli->error;
}

//check access level
/*
if ($_SESSION['logged_in_user_user_access'] === "admin") {
    $query = "SELECT game_name, game_review, game_rating, game_image_url, review_creation_date, review_id FROM a5_reviews";
} else {
//reviewer
    $user_id = $_SESSION['logged_in_user_id'];
    $query = "SELECT game_name, game_review, game_rating, game_image_url, review_creation_date, review_id FROM a5_reviews WHERE user_id = $user_id";
}


$result = $mysqli->query($query);
if ($mysqli->error) {
    print "Query failed: " . $mysqli->error;
}

*/

//check if anything is null
function checkNull($dataPoint) {
    return ($dataPoint === null || $dataPoint === "") ? "N/A" : $dataPoint;
}

$user_id = $_SESSION['logged_in_user_id'];
$query = "SELECT tripid, tripname, city, country, startdate, enddate, iconurl FROM trips WHERE userid = $user_id AND (isdeleted = 0 OR isdeleted IS NULL) ORDER BY startdate ASC";
$result = $mysqli->query($query);
if ($mysqli->error) {
    print "Query failed: " . $mysqli->error;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packmates</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!--Side Navbar (keep on all pages)-->
    <nav class="sidebar">
        <div class="brand">
            <img src="img/appIcon.png" alt="Packmates" class="icon">
            <span>Packmates</span>
        </div>
        <div class="nav-buttons">
            <button type="reset" onclick="location.href='home.php'"><img src="img/home.png" alt=""
                    class="icon"><span>Home</span></button>
            <!--<button type="reset" onclick="location.href='discover.html'"><img src="img/calendar.png" alt=""
                    class="icon"><span>Discover</span></button>-->
            <button type="reset" onclick="location.href='notifications.php'"><img src="img/notif.png" alt=""
                    class="icon"><span>Notifications</span></button>
            <button type="reset" onclick="location.href='profile.php'"><img src="img/profile.png" alt=""
                    class="icon"><span>Profile</span></button>
        </div>
        <div class="nav-bottom">
            <hr>
            <button class="logout" type="reset" onclick="location.href='logout.php'"><img src="img/home.png" alt=""
                    class="icon"><span>Logout</span></button>
        </div>
    </nav>






    <main>
        <header>
            <h1>Welcome traveler!</h1>
            <button class="primary" type="reset" onclick="location.href='newTrip.php'">+ Create New Trip</button>
        </header>
        <input type="text" placeholder="Search Your Trips...">
        <section class="trips">
            <h2>Your Trips</h2>
            <!--Trip Cards-->
            <div class="grid">
                <?php
                $user_id = $_SESSION['logged_in_user_id'];
                $query = "SELECT tripid, tripname, city, country, startdate, enddate, iconurl FROM trips WHERE userid = $user_id AND (isdeleted = 0 OR isdeleted IS NULL) ORDER BY startdate ASC";
                $result = $mysqli->query($query);
                if ($mysqli->error) {
                    print "Query failed: " . $mysqli->error;
                }

                while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $img = !empty($row['iconurl']) ? $row['iconurl'] : 'img/placeholderTrip.png';
                    $start = date('n/j/Y', strtotime($row['startdate']));
                    $end = date('n/j/Y', strtotime($row['enddate']));
                    print '
                    <article class="card" data-search="' . strtolower($row['tripname']) . ' ' . strtolower($row['city']) . ' ' . strtolower($row['country']) . '">
                        <div class="card-img">
                            <img src="' . $img . '" alt="' . htmlspecialchars($row['tripname']) . '">
                        </div>
                        <div class="card-body">
                            <h3>' . htmlspecialchars($row['tripname']) . '</h3>
                            <p>' . htmlspecialchars($row['city']) . ', ' . htmlspecialchars($row['country']) . '</p>
                            <p>' . $start . ' - ' . $end . '</p>
                            <footer>
                                <div class="avatars"><span></span><span></span></div>
                                <button type="button" onclick="location.href=\'tripPreview.php?tripid=' . $row['tripid'] . '\'" class="arrow">&gt;</button>
                            </footer>
                        </div>
                    </article>';
                }
                ?>


                <!--More trip cards can go here depending how many user has (will be implemented next milestone)-->



            </div>
        </section>
    </main>
    <script>
document.querySelector('input[placeholder="Search Your Trips..."]').addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    document.querySelectorAll('.card').forEach(card => {
        const searchable = card.dataset.search || '';
        card.style.display = searchable.includes(query) ? '' : 'none';
    });
});
</script>
</body>

</html>


<?php $mysqli->close(); ?>