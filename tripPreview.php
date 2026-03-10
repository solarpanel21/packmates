<?php
session_start();

// Check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: logout.php");
    exit();
}

require("connectionInclude.php");

// Check if tripid is provided
if (!isset($_GET['tripid'])) {
    header("Location: home.php");
    exit();
}

$tripid  = (int)$_GET['tripid'];
$user_id = $_SESSION['logged_in_user_id'];

// Make sure this trip belongs to the logged in user
$trip_query = $mysqli->query("SELECT * FROM trips WHERE tripid = $tripid AND userid = $user_id AND (isdeleted = 0 OR isdeleted IS NULL)");
if ($trip_query->num_rows === 0) {
    header("Location: home.php");
    exit();
}
$trip = $trip_query->fetch_assoc();



// Handle notes save (POST from inline edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'])) {
    $notes = $mysqli->real_escape_string($_POST['notes']);
    $mysqli->query("UPDATE trips SET notes = '$notes' WHERE tripid = $tripid AND userid = $user_id");
    echo json_encode(['success' => true]);
    exit();
}

// Handle item dismiss (POST from X button)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss_item'])) {
    $itemid = (int)$_POST['itemid'];
    // Check if row already exists
    $existing = $mysqli->query("SELECT id FROM tripitems WHERE tripid = $tripid AND itemid = $itemid");
    if ($existing->num_rows > 0) {
        $mysqli->query("UPDATE tripitems SET isdismissed = 1 WHERE tripid = $tripid AND itemid = $itemid");
    } else {
        $mysqli->query("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed) VALUES ($tripid, $itemid, 0, 1)");
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle item check toggle (POST from checkbox)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_item'])) {
    $itemid  = (int)$_POST['itemid'];
    $checked = (int)$_POST['checked'];
    $existing = $mysqli->query("SELECT id FROM tripitems WHERE tripid = $tripid AND itemid = $itemid");
    if ($existing->num_rows > 0) {
        $mysqli->query("UPDATE tripitems SET ischecked = $checked WHERE tripid = $tripid AND itemid = $itemid");
    } else {
        $mysqli->query("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed) VALUES ($tripid, $itemid, $checked, 0)");
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle trip delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_trip'])) {
    $mysqli->query("UPDATE trips SET isdeleted = 1 WHERE tripid = $tripid AND userid = $user_id");
    echo json_encode(['success' => true]);
    exit();
}

// Get weather and activity tags from trip
$weathertags  = array_filter(array_map('trim', explode(',', $trip['weathertags'] ?? '')));
$activitytags = array_filter(array_map('trim', explode(',', $trip['activitytags'] ?? '')));

// Build suggested items query
// Pull items where:
// - weathertag is null OR matches a trip weather tag
// - activitytag is null OR matches a trip activity tag
// Both conditions must be true$weather_condition  = count($weathertags)  ? "si.weathertag IS NULL OR si.weathertag IN ($weather_list)"  : "si.weathertag IS NULL";


$weather_list  = count($weathertags)  ? "'" . implode("','", array_map([$mysqli, 'real_escape_string'], $weathertags))  . "'" : null;
$activity_list = count($activitytags) ? "'" . implode("','", array_map([$mysqli, 'real_escape_string'], $activitytags)) . "'" : null;

$weather_condition  = $weather_list  ? "si.weathertag IS NULL OR si.weathertag IN ($weather_list)"  : "si.weathertag IS NULL";
$activity_condition = $activity_list ? "si.activitytag IS NULL OR si.activitytag IN ($activity_list)" : "si.activitytag IS NULL";
$items_query = $mysqli->query("
    SELECT si.itemid, si.itemname, si.category, si.weathertag, si.activitytag,
           ti.ischecked, ti.isdismissed
    FROM suggesteditems si
    LEFT JOIN tripitems ti ON ti.itemid = si.itemid AND ti.tripid = $tripid
    WHERE ($weather_condition)
      AND ($activity_condition)
      AND (ti.isdismissed IS NULL OR ti.isdismissed = 0)
    ORDER BY si.category, si.itemname
");

// Organise items by category
$items_by_category = [];
while ($row = $items_query->fetch_assoc()) {
    $items_by_category[$row['category']][] = $row;
}

// Check if anything is null helper
function checkNull($dataPoint) {
    return ($dataPoint === null || $dataPoint === "") ? "N/A" : $dataPoint;
}

// Format date helper
function formatDate($d) {
    if (!$d) return '';
    return date('n/j/Y', strtotime($d));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packmates – <?php echo htmlspecialchars($trip['tripname']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="packing-list.css">
        <style>
        /* Override body to allow split-view to take over the content cell */
        body {
            overflow: hidden;
        }

        /* The split sits inside body's 1fr grid column */
        .tripPreviewSplit {
            display: grid;
            grid-template-columns: 1fr 420px;
            /* Fill the grid cell, not the whole viewport */
            height: 100%;
            overflow: hidden;
        }

        /* ── LEFT PANEL ── */
        .tripPreviewLeft {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%;
        }

        .tripPreviewBanner {
            height: 200px;
            flex-shrink: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .tripPreviewMain {
            flex: 1;
            min-height: 0;          /* key: allows flex child to shrink & scroll */
            overflow-y: auto;
            padding: 24px 32px 32px;
            background: #f1f5f8;
        }

        .tripPreviewHeader {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            gap: 12px;
        }

        .tripPreviewHeader h1 {
            font-size: 1.4rem;
            margin-bottom: 4px;
        }

        .tripPreviewHeader .avatars {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tripPreviewHeader .avatars p {
            font-size: 0.8rem;
            color: #6b7f8e;
            margin: 0;
        }

        .tripDivider {
            height: 4px;
            border: none;
            background: #dde6ee;
            border-radius: 10px;
            margin: 14px 0;
        }

        /* Itinerary */
        .itinerarySection h2 {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .itineraryList {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .itineraryItem {
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
            border-radius: 14px;
            padding: 10px 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .itineraryItem img {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .itineraryItemInfo h3 {
            font-size: 0.9rem;
            margin-bottom: 3px;
        }

        .itineraryItemInfo p {
            font-size: 0.75rem;
            color: #6b7f8e;
        }

        /* Add Activity button */
        .addActivityBtn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 2px dashed #cdd6de;
            border-radius: 14px;
            padding: 10px 14px;
            cursor: pointer;
            width: 100%;
            text-align: left;
            font-family: inherit;
            margin-top: 10px;
            transition: border-color 0.2s, background 0.2s;
        }

        .addActivityBtn:hover {
            border-color: #5f9d30;
            background: #f6fdf0;
        }

        .addActivityIcon {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            background: #e8f5e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #5f9d30;
            flex-shrink: 0;
        }

        .addActivityLabel {
            font-size: 0.9rem;
            font-weight: 600;
            color: #5f9d30;
        }

        /* ── RIGHT PANEL ── */
        .tripPreviewRight {
            background: #fff;
            border-left: 1px solid #dde6ee;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            height: 100%;
        }

        .packingPanelHeader {
            padding: 18px 20px 12px;
            border-bottom: 1px solid #dde6ee;
            flex-shrink: 0;
        }

        .packingPanelHeader h2 {
            font-size: 0.95rem;
            margin-bottom: 3px;
        }

        .packingPanelMeta {
            font-size: 0.72rem;
            color: #6b7f8e;
        }

        .packingPanelProgress {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .packingProgressBar {
            flex: 1;
            height: 6px;
            background: #dde6ee;
            border-radius: 999px;
            overflow: hidden;
        }

        .packingProgressFill {
            height: 100%;
            background: #5f9d30;
            border-radius: 999px;
            transition: width 0.3s;
        }

        .packingProgressText {
            font-size: 0.72rem;
            color: #5f9d30;
            font-weight: 600;
            white-space: nowrap;
        }

        .packingPanelFilters {
            padding: 10px 16px;
            border-bottom: 1px solid #dde6ee;
            flex-shrink: 0;
        }

        /* Body must scroll — min-height: 0 is the critical fix */
        .packingPanelBody {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 12px 16px 16px;
        }

        /* Suggested banner inside the panel */
        .packingSuggestedBanner {
            font-size: 0.7rem;
            font-weight: 700;
            color: #5f9d30;
            background: #f0fbe8;
            border-radius: 8px;
            padding: 5px 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .packingCategoryGroup {
            margin-bottom: 14px;
        }

        .packingCategoryLabel {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #113a58;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .packingCategoryLabel .suggested-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #5f9d30;
            flex-shrink: 0;
        }

        .packingPanelItem {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 6px;
            border-radius: 7px;
            transition: background 0.15s;
        }

        .packingPanelItem:hover {
            background: #f1f5f8;
        }

        .packingPanelCheck {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            border: 1.5px solid #cdd6de;
            background: #f7fafc;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .packingPanelCheck--checked {
            background: #5f9d30;
            border-color: #5f9d30;
        }

        .packingPanelCheck--checked::after {
            content: "✓";
            color: #fff;
            font-size: 0.58rem;
        }

        .packingPanelItemName {
            flex: 1;
            font-size: 0.78rem;
            color: #333;
        }

        .packingPanelItemName--packed {
            text-decoration: line-through;
            color: #aab4bc;
        }

        .packingPanelFooter {
            padding: 12px 16px;
            border-top: 1px solid #dde6ee;
            flex-shrink: 0;
        }

        /* ── Add Activity Modal ── */
        .activityModal {
            position: fixed;
            inset: 0;
            background: rgba(17, 58, 88, 0.35);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .activityModal.is-visible {
            display: flex;
        }

        .activityModalBox {
            background: #fff;
            border-radius: 14px;
            width: 360px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        }

        .activityModalHeader {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .activityModalHeader h2 {
            font-size: 1rem;
            margin: 0;
        }

        .activityModalClose {
            border: none;
            background: transparent;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .activityForm {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .activityField {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 0.8rem;
            color: #4b5a66;
        }

        .activityField input,
        .activityField select {
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid #cdd6de;
            font-size: 0.85rem;
            font-family: inherit;
            outline: none;
            margin: 0;
            width: 100%;
        }

        .activityField input:focus,
        .activityField select:focus {
            border-color: #5f9d30;
        }

        .activityModalActions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Side Navbar (keep on all pages) -->
    <nav class="sidebar">
        <div class="brand">
            <img src="img/appIcon.png" alt="Packmates" class="icon">
            <span>Packmates</span>
        </div>
        <div class="nav-buttons">
            <button type="reset" onclick="location.href='home.php'"><img src="img/home.png" alt="" class="icon"><span>Home</span></button>
            <button type="reset" onclick="location.href='notifications.php'"><img src="img/notif.png" alt="" class="icon"><span>Notifications</span></button>
            <button type="reset" onclick="location.href='profile.php'"><img src="img/profile.png" alt="" class="icon"><span>Profile</span></button>
        </div>
        <div class="nav-bottom">
            <hr>
            <button class="logout" type="reset" onclick="location.href='logout.php'"><img src="img/home.png" alt="" class="icon"><span>Logout</span></button>
        </div>
    </nav>

    <div class="tripPreviewSplit">

        <!-- LEFT: Trip info -->
        <div class="tripPreviewLeft">
            <div style="background-image: url('<?php echo htmlspecialchars($trip['iconurl'] ?: 'img/tripPreviewBanner.jpg'); ?>')"
                 class="tripPreviewBanner"></div>

            <div class="tripPreviewMain">
                <div class="tripPreviewHeader">
                    <div>
                        <h1><?php echo htmlspecialchars($trip['tripname']); ?></h1>
                        <p><?php echo htmlspecialchars($trip['city']); ?>, <?php echo htmlspecialchars($trip['country']); ?></p>
                        <p><?php echo formatDate($trip['startdate']); ?> – <?php echo formatDate($trip['enddate']); ?></p>


                    </div>

                    <div>
                    <button id="deleteTripBtn" type="button" style="background:#fdecea;color:#c0392b;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-family:inherit;">
                    Delete Trip
                    </button>&nbsp;&nbsp;&nbsp;
                    <button class="primary" type="button"
                            onclick="location.href='packing-list.php?tripid=<?php echo $tripid; ?>'">
                        Full Packing List
                    </button>
                    </div>

                </div>

                <hr class="tripDivider">

                <!-- Editable notes section -->
                <section style="margin-bottom:1.5rem;">
                    <h2>Notes</h2>
                    <textarea id="tripNotes" style="width:100%;min-height:80px;padding:8px;border-radius:6px;border:1px solid #ccc;font-family:inherit;font-size:0.95rem;resize:vertical;"
                              placeholder="Add any additional info..."><?php echo htmlspecialchars($trip['notes'] ?? ''); ?></textarea>
                    <button id="saveNotesBtn" class="primary" style="margin-top:6px;" type="button">Save Notes</button>
                    <span id="notesSaved" style="margin-left:8px;color:green;display:none;">Saved!</span>
                </section>


                <hr class="tripDivider">

                <!-- Itinerary section -->
                <section class="itinerarySection">
                    <h2>Trip Itinerary</h2>
    <div class="itineraryList" id="itineraryList"></div>


                </section>
            </div>
        </div>

        <!-- RIGHT: Packing List Panel -->
        <div class="tripPreviewRight">
            <div class="packingPanelHeader">
                <h2><?php echo htmlspecialchars($trip['tripname']); ?></h2>
                <div class="packingPanelMeta">
                    <?php echo formatDate($trip['startdate']); ?> – <?php echo formatDate($trip['enddate']); ?>
                </div>

                <!-- Weather tag badges -->
                <?php if (!empty($weathertags)): ?>
                <div style="margin:6px 0;font-size:0.85rem;">
                    <?php
                    $badge_map = ['warm'=>'☀️ Warm','cold'=>'🧊 Cold','rain'=>'🌧️ Rainy','snow'=>'❄️ Snowy','wind'=>'💨 Windy'];
                    foreach ($weathertags as $tag) {
                        $label = $badge_map[$tag] ?? ucfirst($tag);
                        echo '<span style="margin-right:6px;">' . $label . '</span>';
                    }
                    ?>
                </div>
                <?php endif; ?>

                <!-- Progress bar -->
                <div class="packingPanelProgress">
                    <div class="packingProgressBar">
                        <div class="packingProgressFill" id="packingProgressFill" style="width:0%"></div>
                    </div>
                    <span class="packingProgressText" id="packingProgressText">0 packed</span>
                </div>
            </div>

            <!-- Filter buttons -->
            <div class="packingPanelFilters">
                <div class="packing-filters">
                    <button class="packing-filter-btn packing-filter-btn--active" data-filter="all">All</button>
                    <button class="packing-filter-btn" data-filter="not-packed">Unpacked</button>
                    <button class="packing-filter-btn" data-filter="packed">Packed</button>
                </div>
            </div>

            <!-- Packing items -->
            <div class="packingPanelBody" id="packingPanelBody">
                <?php
                $total  = 0;
                $packed = 0;
                foreach ($items_by_category as $category => $items):
                    $total += count($items);
                    foreach ($items as $item) {
                        if ($item['ischecked']) $packed++;
                    }
                ?>
                <div class="packingCategoryGroup" data-category="<?php echo htmlspecialchars($category); ?>">
                    <div class="packingCategoryLabel">
                        <span class="suggested-dot"></span>
                        <?php echo htmlspecialchars($category); ?>
                    </div>
                    <?php foreach ($items as $item): ?>
                    <div class="packingPanelItem"
                         data-itemid="<?php echo $item['itemid']; ?>"
                         data-checked="<?php echo $item['ischecked'] ? '1' : '0'; ?>">
                        <button class="packingPanelCheck <?php echo $item['ischecked'] ? 'packingPanelCheck--checked' : ''; ?>"></button>
                        <span class="packingPanelItemName <?php echo $item['ischecked'] ? 'packingPanelItemName--packed' : ''; ?>">
                            <?php echo htmlspecialchars($item['itemname']); ?>
                        </span>
                        <button class="panel-dismiss" data-itemid="<?php echo $item['itemid']; ?>"
                                title="Remove"
                                style="width:16px;height:16px;border-radius:50%;border:none;background:#fdecea;cursor:pointer;color:#c0392b;font-size:0.65rem;display:inline-flex;align-items:center;justify-content:center;padding:0;flex-shrink:0;">✕</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="packingPanelFooter">
                <button class="primary" style="width:100%;font-family:inherit"
                        onclick="location.href='packing-list.php?tripid=<?php echo $tripid; ?>'">
                    View Full Packing List →
                </button>
            </div>
        </div>
    </div>




    <script src="icons.js"></script>
    <script>
        const TRIPID = <?php echo $tripid; ?>;

        // ── Progress bar ──
        function updateProgress() {
            const all    = document.querySelectorAll('.packingPanelItem');
            const packed = document.querySelectorAll('.packingPanelItem[data-checked="1"]');
            const pct    = all.length ? Math.round(packed.length / all.length * 100) : 0;
            document.getElementById('packingProgressFill').style.width = pct + '%';
            document.getElementById('packingProgressText').textContent = `${packed.length}/${all.length} packed`;
        }
        updateProgress();

        // ── Filter buttons ──
        let currentFilter = 'all';
        document.querySelectorAll('.packing-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.packing-filter-btn').forEach(b => b.classList.remove('packing-filter-btn--active'));
                btn.classList.add('packing-filter-btn--active');
                currentFilter = btn.dataset.filter;
                document.querySelectorAll('.packingPanelItem').forEach(item => {
                    const checked = item.dataset.checked === '1';
                    if (currentFilter === 'packed')     item.style.display = checked ? '' : 'none';
                    else if (currentFilter === 'not-packed') item.style.display = !checked ? '' : 'none';
                    else item.style.display = '';
                });
            });
        });

        // ── Check toggle ──
        document.querySelectorAll('.packingPanelCheck').forEach(btn => {
            btn.addEventListener('click', function() {
                const item    = this.closest('.packingPanelItem');
                const itemid  = item.dataset.itemid;
                const checked = item.dataset.checked === '1' ? 0 : 1;

                item.dataset.checked = checked;
                this.classList.toggle('packingPanelCheck--checked', checked === 1);
                item.querySelector('.packingPanelItemName').classList.toggle('packingPanelItemName--packed', checked === 1);

                fetch('tripPreview.php?tripid=' + TRIPID, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `toggle_item=1&itemid=${itemid}&checked=${checked}`
                });

                updateProgress();
            });
        });

        // ── Dismiss (X) buttons ──
        document.querySelectorAll('.panel-dismiss').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemid = this.dataset.itemid;
                const item   = this.closest('.packingPanelItem');
                const group  = item.closest('.packingCategoryGroup');

                fetch('tripPreview.php?tripid=' + TRIPID, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `dismiss_item=1&itemid=${itemid}`
                });

                item.remove();
                // Remove category header if no items left
                if (!group.querySelectorAll('.packingPanelItem').length) group.remove();
                updateProgress();
            });
        });

        // ── Save notes ──
        document.getElementById('saveNotesBtn').addEventListener('click', function() {
            const notes = document.getElementById('tripNotes').value;
            const saved = document.getElementById('notesSaved');
            fetch('tripPreview.php?tripid=' + TRIPID, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `save_notes=1&notes=${encodeURIComponent(notes)}`
            }).then(() => {
                saved.style.display = 'inline';
                setTimeout(() => saved.style.display = 'none', 2000);
            });
        });


        // Activities still use localStorage for now since there's no activities table
        const destination = <?php echo json_encode($trip['city']); ?>;
        let activities = JSON.parse(localStorage.getItem('activities_<?php echo $tripid; ?>') || '[]');

            function capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function renderItinerary() {
                const list = document.getElementById('itineraryList');
                list.innerHTML = '';
                const tags = <?php echo json_encode(array_values($activitytags)); ?>;

                if (!tags.length) {
                    list.innerHTML = '<p style="color:#888;">No activities selected for this trip.</p>';
                    return;
                }

                tags.forEach(tag => {
                    const iconUrl = CATEGORY_ICONS[tag] || FALLBACK_SVG;
                    const item = document.createElement('div');
                    item.className = 'itineraryItem';
                    item.innerHTML = `
                        <img src="${iconUrl}" alt="${tag}" style="width:48px;height:48px;object-fit:contain;">
                        <div class="itineraryItemInfo">
                            <h3>${capitalize(tag)}</h3>
                        </div>`;
                    list.appendChild(item);
                });
            }
            document.addEventListener('DOMContentLoaded', function() {

                renderItinerary();
            });


        document.getElementById('deleteTripBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this trip? This cannot be undone.')) {
                fetch('tripPreview.php?tripid=' + TRIPID, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'delete_trip=1'
                }).then(res => res.json()).then(data => {
                    console.log(data);
                    if (data.success) location.href = 'home.php';
                });
            }
        });
    </script>
</body>
</html>
<?php $mysqli->close(); ?>