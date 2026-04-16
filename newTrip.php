<?php
session_start();


include("connectionInclude.php");
include("weatherChain.php");


//check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: logout.php");
    exit();
}

$user_id = filter_var($_SESSION['logged_in_user_id'] ?? null, FILTER_VALIDATE_INT);
if ($user_id === false || $user_id === null) {
    header("Location: logout.php");
    exit();
}

$notif_stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE userid = ? AND isread = 0");
$notif_stmt->bind_param("i", $user_id);
$notif_stmt->execute();
$notif_count = $notif_stmt->get_result()->fetch_assoc()['cnt'];
$notif_icon = $notif_count > 0 ? 'img/notif2.png' : 'img/notif.png';


//get users info
$select_query = "SELECT userid, username, password, email, pfpurl FROM users";
$select_result = $mysqli->query($select_query);
if ($mysqli->error) {
    print "Select query error!  Message: " . $mysqli->error;
}


//check if anything is null
function checkNull($dataPoint) {
    return ($dataPoint === null || $dataPoint === "") ? "N/A" : $dataPoint;
}


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_trip'])) {
    $tripname   = trim($_POST['tripname'] ?? '');
    $city       = trim($_POST['tripCity'] ?? '');
    $country    = trim($_POST['tripCountry'] ?? '');
    $lat        = (float)$_POST['tripLat'];
    $lon        = (float)$_POST['tripLon'];
    $startdate  = trim($_POST['startdate'] ?? '');
    $enddate    = trim($_POST['enddate'] ?? '');
    $notes      = trim($_POST['notes'] ?? '');
    $activity_raw = trim($_POST['activitytags'] ?? '');
    $iconurl = trim($_POST['iconurl'] ?? '');

    $activities_arr = array_values(array_filter(array_map(static function ($tag) {
        $tag = strtolower(trim($tag));
        return preg_match('/^[a-z0-9_-]{1,32}$/', $tag) ? $tag : '';
    }, explode(',', $activity_raw))));
    $activities = implode(',', $activities_arr);

    $start_ok = DateTime::createFromFormat('Y-m-d', $startdate);
    $end_ok = DateTime::createFromFormat('Y-m-d', $enddate);
    if (empty($tripname) || empty($city) || empty($country) || !$start_ok || !$end_ok) {
        $error = "Please fill in all required fields.";
    } else if ($lat === 0.0 && $lon === 0.0) {
        $error = "Please select a destination from the search dropdown.";
    } else {
        // Run weather chain to get tags
        $weather = fetch_weather($lat, $lon, $startdate, $enddate);
    $tags = [];
    if ($weather) {
        if ($weather['temp_max'] > 30) $tags[] = 'warm';
        if ($weather['temp_min'] < 18 || $weather['total_snow_cm'] > 0) $tags[] = 'cold';
        if ($weather['total_rain_mm'] > 0.5 || ($weather['avg_precip_prob'] !== null && $weather['avg_precip_prob'] > 50)) $tags[] = 'rain';
        if ($weather['total_snow_cm'] > 0.25) $tags[] = 'snow';
        if ($weather['wind_max'] >= 38) $tags[] = 'wind';
    }
    $weathertags = implode(',', $tags);
    $creationdate = date('Y-m-d H:i:s');



    $insert_stmt = $mysqli->prepare("INSERT INTO trips (tripname, city, country, latitude, longitude, startdate, enddate, creationdate, notes, weathertags, activitytags, userid, iconurl)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("sssddssssssis", $tripname, $city, $country, $lat, $lon, $startdate, $enddate, $creationdate, $notes, $weathertags, $activities, $user_id, $iconurl);
    if (!$insert_stmt->execute()) {
        $error = "Error saving trip: " . $mysqli->error;
    } else {
        // Get the new trip's id
        $new_tripid = $mysqli->insert_id;

        // Build the same tag conditions to find matching suggested items
        $suggested_sql = "SELECT itemid FROM suggesteditems WHERE ";
        $types = '';
        $params = [];

        if (count($tags) > 0) {
            $weather_placeholders = implode(',', array_fill(0, count($tags), '?'));
            $suggested_sql .= "(weathertag IS NULL OR weathertag IN ($weather_placeholders))";
            $types .= str_repeat('s', count($tags));
            foreach ($tags as $tag) {
                $params[] = $tag;
            }
        } else {
            $suggested_sql .= "weathertag IS NULL";
        }

        if (count($activities_arr) > 0) {
            $activity_placeholders = implode(',', array_fill(0, count($activities_arr), '?'));
            $suggested_sql .= " AND (activitytag IS NULL OR activitytag IN ($activity_placeholders))";
            $types .= str_repeat('s', count($activities_arr));
            foreach ($activities_arr as $tag) {
                $params[] = $tag;
            }
        } else {
            $suggested_sql .= " AND activitytag IS NULL";
        }

        // Pull matching suggested items
        $suggested_stmt = $mysqli->prepare($suggested_sql);
        if ($types !== '') {
            $suggested_stmt->bind_param($types, ...$params);
        }
        $suggested_stmt->execute();
        $suggested = $suggested_stmt->get_result();

        if ($suggested->num_rows === 0) die("No suggested items matched the selected tags.");
        $insert_item_stmt = $mysqli->prepare("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed, quantity) VALUES (?, ?, 0, 0, 1)");
        while ($item = $suggested->fetch_assoc()) {
            $item_id = (int)$item['itemid'];
            $insert_item_stmt->bind_param("ii", $new_tripid, $item_id);
            if (!$insert_item_stmt->execute()) die("tripitems insert error: " . $mysqli->error);
        }

        include("checkNotifications.php");
        checkTripNotifications($mysqli);

        header("Location: tripPreview.php?tripid=" . $new_tripid);
        exit();
    } // end else
} // end if POST
}





?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packmates</title>
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
    <link rel="stylesheet" href="style.css">
    <style>
        .activity-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 10px 0 20px;
        }

        .activity-chip {
            padding: 6px 14px;
            border-radius: 999px;
            border: 1.5px solid #cdd6de;
            background: #f7fafc;
            cursor: pointer;
            font-size: 0.82rem;
            font-family: inherit;
            color: #4b5a66;
            transition: all 0.15s;
            margin: 0;
            width: auto;
        }

        .activity-chip.selected {
            background: #e8f5e0;
            border-color: #5f9d30;
            color: #3a6b1a;
            font-weight: 600;
        }

        .activity-chip:hover:not(.selected) {
            border-color: #99a8b4;
            background: #edf2f7;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,0.7);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:16px;">
    <img src="img/loading.gif" alt="Loading..." style="width:80px;height:80px;">
    <p style="font-family:'Syne',sans-serif;font-weight:600;color:#2D8C4E;">Creating your trip...</p>
</div>
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
            <button type="reset" onclick="location.href='notifications.php'">
                <img src="<?php echo $notif_icon; ?>" alt="" class="icon">
                <span>Notifications</span>
            </button>
            <?php include("navUser.php"); ?>
            <button type="button" onclick="location.href='profile.php'">
                <img class="icon" style="border-radius:7px;" src="<?php echo $nav_pfp; ?>" alt="Profile">
                <span>Profile</span>
            </button>
        </div>
        <div class="nav-bottom">
            <hr>
            <button class="logout" type="reset" onclick="location.href='logout.php'"><img src="img/home.png" alt=""
                    class="icon"><span>Logout</span></button>
        </div>
    </nav>


    <div class="gridNewTrip">
        <div class="newTripMenu">
            <header class="bottomMargin">
                <h1>Create a New Trip</h1>
                <a href="home.php">
                    <img src="img/back.png" height="30px" width="30px">
                </a>
            </header>

            <form id="newTripForm" method="post" action="">

            <h3>Trip Name</h3>
            <input maxlength="32" id="tripName" name="tripname" class="bottomMargin" type="text" placeholder="Enter a trip name..." required>




            <!-- Destination Search -->
                <h3>Destination</h3>
                <div class="destination-wrap" style="position:relative;">
                <input id="tripDestination" class="bottomMargin" type="text"
                        placeholder="Search Destinations..." autocomplete="off"
                        oninput="handleDestinationInput(this.value)">
                <div id="destinationDropdown" style="
                    display:none;
                    position:absolute;
                    top:100%;
                    left:0;
                    right:0;
                    background:#fff;
                    border:1px solid #ccc;
                    border-radius:6px;
                    z-index:1000;
                    max-height:200px;
                    overflow-y:auto;
                    box-shadow:0 4px 12px rgba(0,0,0,0.15);
                "></div>
                </div>

                <!-- Hidden fields populated when user picks a result -->
                <input type="hidden" id="tripCity" name="tripCity">
                <input type="hidden" id="tripCountry" name="tripCountry">
                <input type="hidden" id="tripAdmin1" name="tripAdmin1">
                <input type="hidden" id="tripLat" name="tripLat">
                <input type="hidden" id="tripLon" name="tripLon">

                <script>
                let debounceTimer = null;
                let destinationConfirmed = false;

                function handleDestinationInput(val) {
                    // Clear confirmed location if user edits the field
                    destinationConfirmed = false;
                    clearHiddenFields();

                    clearTimeout(debounceTimer);
                    const dropdown = document.getElementById('destinationDropdown');

                    if (val.trim().length < 2) {
                    dropdown.style.display = 'none';
                    return;
                    }

                    debounceTimer = setTimeout(() => fetchDestinations(val.trim()), 350);
                }

                async function fetchDestinations(query) {
                    const dropdown = document.getElementById('destinationDropdown');
                    dropdown.innerHTML = '<div style="padding:10px;color:#888;">Searching...</div>';
                    dropdown.style.display = 'block';

                    try {
                    const res = await fetch(`geocode.php?name=${encodeURIComponent(query)}&count=10`);
                    const data = await res.json();

                    if (data.error || !data.results || data.results.length === 0) {
                        dropdown.innerHTML = '<div style="padding:10px;color:#888;">No results found</div>';
                        return;
                    }

                    dropdown.innerHTML = '';
                    data.results.forEach(place => {
                        const label = [place.name, place.admin1, place.country].filter(Boolean).join(', ');
                        const item = document.createElement('div');
                        item.style.cssText = 'padding:10px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;';
                        item.textContent = label;
                        item.addEventListener('mouseenter', () => item.style.background = '#f5f5f5');
                        item.addEventListener('mouseleave', () => item.style.background = '');
                        item.addEventListener('click', () => selectDestination(place, label));
                        dropdown.appendChild(item);
                    });

                    } catch (e) {
                    dropdown.innerHTML = '<div style="padding:10px;color:#888;">Search failed</div>';
                    }
                }

                function selectDestination(place, label) {
                    document.getElementById('tripDestination').value = label;
                    document.getElementById('tripCity').value = place.name;
                    document.getElementById('tripCountry').value = place.country;
                    document.getElementById('tripAdmin1').value = place.admin1 || '';
                    document.getElementById('cityMap').src = `https://maps.google.com/maps?q=${place.latitude},${place.longitude}&z=12&output=embed`;
                    document.getElementById('tripLat').value = place.latitude;
                    document.getElementById('tripLon').value = place.longitude;
                    document.getElementById('destinationDropdown').style.display = 'none';
                    destinationConfirmed = true;
                    fetchCityImage(place.name).then(url => {
                        if (url) document.getElementById('tripIconUrl').value = url;
                    });
                }

                function clearHiddenFields() {
                    ['tripCity','tripCountry','tripAdmin1','tripLat','tripLon'].forEach(id => {
                    document.getElementById(id).value = '';
                    });
                }

                // Close dropdown if user clicks outside
                document.addEventListener('click', function(e) {
                    const wrap = document.querySelector('.destination-wrap');
                    if (!wrap.contains(e.target)) {
                    document.getElementById('destinationDropdown').style.display = 'none';
                    }
                });
                </script>


                <div class="sideBySide">
                    <div>
                        <h3>From</h3>
                        <input name="startdate" id="tripFromDate" class="dateInput" type="date">
                    </div>
                    <div>
                        <h3>To</h3>
                        <input name="enddate" id="tripToDate" class="dateInput" type="date">
                    </div>
                </div>

                <h3>Trip Type & Activities</h3>
                <div class="activity-chips" id="activityChips">
                    <button type="button" class="activity-chip" data-category="beach">🏖️ Beach</button>
                    <button type="button" class="activity-chip" data-category="hiking">🥾 Hiking</button>
                    <button type="button" class="activity-chip" data-category="camping">⛺ Camping</button>
                    <button type="button" class="activity-chip" data-category="gym">💪 Gym</button>
                    <button type="button" class="activity-chip" data-category="sport">🏀 Sports</button>
                    <button type="button" class="activity-chip" data-category="swimming">🏊 Swimming</button>
                    <button type="button" class="activity-chip" data-category="wintersports">⛷️ Winter Sports</button>
                    <button type="button" class="activity-chip" data-category="business">💼 Business Trip</button>
                    <button type="button" class="activity-chip" data-category="nightout">🌃 Night Out</button>
                    <button type="button" class="activity-chip" data-category="roadtrip">🚗 Road Trip</button>
                    <button type="button" class="activity-chip" data-category="formal">👔 Formal Event</button>
                </div>


                <!--HIDDEN INPUT FOR ACTIVITY TAGS-->
                <input type="hidden" id="activityTagsInput" name="activitytags">
                <!--HIDDEN INPUT FOR ICON URL-->
                <input type="hidden" id="tripIconUrl" name="iconurl">


                <h3>Notes</h3>
                <textarea name="notes" maxlength="500" id="tripNotes" placeholder="Add Any Additional Info..."></textarea>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($error)): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

                <div class="cancelSaveButtons">
                    <button class="cancel" type="button" onclick="location.href='home.php'">Cancel Trip</button>
                    <button class="save" type="submit" id="saveTripBtn" name="save_trip" value="true">Save Trip</button>
                </div>
            </form>
        </div>
        <div>
        <iframe id="cityMap"
            src="https://maps.google.com/maps?q=New+York&output=embed"
            style="pointer-events:none;width:100%;height:100%;border:none;border-radius:12px;">
        </iframe>
</div>
    </div>
<script>
    const PEXELS_KEY = 'vnk5OqRIBUtBdRV9LUp1oaAn2QiAB5lO0HL8GwM5WrRRQZt5GOaFlVIq';

    async function fetchCityImage(city) {
        try {
            const res = await fetch(
                `https://api.pexels.com/v1/search?query=${encodeURIComponent(city)}+downtown+skyline&orientation=landscape&per_page=15&size=large`,
                { headers: { Authorization: PEXELS_KEY } }
            );
            const data = await res.json();
            const photos = data.photos || [];
            if (!photos.length) return null;
            const best = photos.reduce((a, b) => (a.width * a.height >= b.width * b.height ? a : b));
            return best.src.original || null;
        } catch (e) {
            console.warn('City image fetch failed:', e);
            return null;
        }
    }

    // Activity chip toggle
    document.querySelectorAll('.activity-chip').forEach(chip => {
        chip.addEventListener('click', (e) => {
            e.stopPropagation();
            chip.classList.toggle('selected');
        });
    });

    // Date validation
    const startInput = document.getElementById('tripFromDate');
    const endInput   = document.getElementById('tripToDate');
    const today = new Date().toISOString().split('T')[0];
    startInput.min = today;
    endInput.min = today;

    startInput.addEventListener('change', function() {
        endInput.min = this.value;
        if (endInput.value && endInput.value < this.value) {
            endInput.value = this.value;
        }
    });

    // Single form submit listener — collects chips + blocks duplicates
    let tripSubmitting = false;

    document.getElementById('newTripForm').addEventListener('submit', function(e) {
        // Collect activity chips into hidden input
        const selected = Array.from(document.querySelectorAll('.activity-chip.selected'))
            .map(el => el.dataset.category)
            .join(',');
        document.getElementById('activityTagsInput').value = selected;

        // Block duplicate submissions
        const tripname  = document.getElementById('tripName').value.trim();
        const city      = document.getElementById('tripCity').value.trim();
        const startdate = document.getElementById('tripFromDate').value;
        const enddate   = document.getElementById('tripToDate').value;

        if (tripname && city && startdate && enddate) {
            if (tripSubmitting) { e.preventDefault(); return; }
            tripSubmitting = true;
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
    });
</script>
</body>

</html>


<?php $mysqli->close(); ?>
