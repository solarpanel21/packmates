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

// Make sure this trip belongs to the logged in user and is not deleted
$trip_query = $mysqli->query("SELECT * FROM trips WHERE tripid = $tripid AND userid = $user_id AND (isdeleted = 0 OR isdeleted IS NULL)");
if ($trip_query->num_rows === 0) {
    header("Location: home.php");
    exit();
}
$trip = $trip_query->fetch_assoc();

// Handle item dismiss (POST from X button)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss_item'])) {
    $itemid = (int)$_POST['itemid'];
    $existing = $mysqli->query("SELECT id FROM tripitems WHERE tripid = $tripid AND itemid = $itemid");
    if ($existing->num_rows > 0) {
        $mysqli->query("UPDATE tripitems SET isdismissed = 1 WHERE tripid = $tripid AND itemid = $itemid");
    } else {
        $mysqli->query("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed, quantity) VALUES ($tripid, $itemid, 0, 1, 1)");
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle item check toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_item'])) {
    $itemid  = (int)$_POST['itemid'];
    $checked = (int)$_POST['checked'];
    $existing = $mysqli->query("SELECT id FROM tripitems WHERE tripid = $tripid AND itemid = $itemid");
    if ($existing->num_rows > 0) {
        $mysqli->query("UPDATE tripitems SET ischecked = $checked WHERE tripid = $tripid AND itemid = $itemid");
    } else {
        $mysqli->query("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed, quantity) VALUES ($tripid, $itemid, $checked, 0, 1)");
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $itemid   = (int)$_POST['itemid'];
    $quantity = max(1, (int)$_POST['quantity']);

    $existing = $mysqli->query("SELECT id FROM tripitems WHERE tripid = $tripid AND itemid = $itemid");
    if ($existing->num_rows > 0) {
        $mysqli->query("UPDATE tripitems SET quantity = $quantity WHERE tripid = $tripid AND itemid = $itemid");
    } else {
        $mysqli->query("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed, quantity) VALUES ($tripid, $itemid, 0, 0, $quantity)");
    }
    echo json_encode(['success' => true]);
    exit();
}

// Get weather and activity tags from trip
$weathertags  = array_filter(array_map('trim', explode(',', $trip['weathertags']  ?? '')));
$activitytags = array_filter(array_map('trim', explode(',', $trip['activitytags'] ?? '')));

// Build tag conditions
$weather_list  = count($weathertags)  ? "'" . implode("','", array_map([$mysqli, 'real_escape_string'], $weathertags))  . "'" : null;
$activity_list = count($activitytags) ? "'" . implode("','", array_map([$mysqli, 'real_escape_string'], $activitytags)) . "'" : null;

$weather_condition  = $weather_list  ? "si.weathertag IS NULL OR si.weathertag IN ($weather_list)"  : "si.weathertag IS NULL";
$activity_condition = $activity_list ? "si.activitytag IS NULL OR si.activitytag IN ($activity_list)" : "si.activitytag IS NULL";

// Pull items for this trip that are not dismissed
$items_query = $mysqli->query("
    SELECT si.itemid, si.itemname, si.category,
           COALESCE(ti.ischecked, 0) AS ischecked,
           COALESCE(ti.quantity, 1)  AS quantity
    FROM suggesteditems si
    LEFT JOIN tripitems ti ON ti.itemid = si.itemid AND ti.tripid = $tripid
    WHERE ($weather_condition)
      AND ($activity_condition)
      AND (ti.isdismissed IS NULL OR ti.isdismissed = 0)
    ORDER BY si.category, si.itemname
");

// Organise by category
$items_by_category = [];
$total  = 0;
$packed = 0;
while ($row = $items_query->fetch_assoc()) {
    $items_by_category[$row['category']][] = $row;
    $total++;
    if ($row['ischecked']) $packed++;
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
    <title><?php echo htmlspecialchars($trip['tripname']); ?> – Packing List | Packmates</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="packing-list.css">
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

    <main>
        <!-- Banner image -->
        <?php if (!empty($trip['iconurl'])): ?>
        <div class="city-banner" style="background-image:url('<?php echo htmlspecialchars($trip['iconurl']); ?>');"></div>
        <?php endif; ?>

        <header>
            <h1><?php echo htmlspecialchars($trip['tripname']); ?> – Packing List</h1>
            <button class="primary" type="button"
                    onclick="location.href='tripPreview.php?tripid=<?php echo $tripid; ?>'">
                ← Back to Trip
            </button>
        </header>

        <section class="trips packing-screen">

            <!-- Trip summary -->
            <div class="packing-summary">
                <div>
                    <h2><?php echo htmlspecialchars($trip['city']); ?>, <?php echo htmlspecialchars($trip['country']); ?></h2>
                    <p class="packing-sub">
                        <?php echo formatDate($trip['startdate']); ?> – <?php echo formatDate($trip['enddate']); ?>
                    </p>
                </div>
                <div class="packing-summary-count">
                    <span class="packing-count-main" id="packedCount"><?php echo $packed; ?>/<?php echo $total; ?> packed</span>
                    <span class="packing-count-sub" id="unpackedCount"><?php echo $total - $packed; ?> items left</span>
                </div>
            </div>

            <!-- Filters -->
            <div class="packing-controls">
                <div class="packing-filters">
                    <button class="packing-filter-btn packing-filter-btn--active" data-filter="all">All</button>
                    <button class="packing-filter-btn" data-filter="not-packed">Not packed</button>
                    <button class="packing-filter-btn" data-filter="packed">Packed</button>
                </div>
                <button class="packing-add-btn" id="openAddItemModal">+ Add item</button>
            </div>

            <!-- Packing list items grouped by category -->
            <div class="packing-list" id="packingListBody">
                <?php foreach ($items_by_category as $category => $items): ?>
                <div class="packing-category-group" data-category="<?php echo htmlspecialchars($category); ?>">
                    <div class="packing-category-header">
                        <?php echo htmlspecialchars($category); ?>
                    </div>
                    <?php foreach ($items as $item): ?>
                    <div class="packing-item"
                         data-itemid="<?php echo $item['itemid']; ?>"
                         data-checked="<?php echo $item['ischecked'] ? '1' : '0'; ?>">

                        <!-- Checkbox -->
                        <button class="packing-check <?php echo $item['ischecked'] ? 'packing-check--checked' : ''; ?>"
                                aria-label="Toggle packed"></button>

                        <!-- Item name -->
                        <span class="packing-item-name <?php echo $item['ischecked'] ? 'packing-item-name--packed' : ''; ?>">
                            <?php echo htmlspecialchars($item['itemname']); ?>
                        </span>

                        <!-- Quantity -->
                        <div class="packing-qty-wrap" style="display:inline-flex;align-items:center;gap:4px;">
                            <button type="button" class="packing-qty-btn packing-qty-minus"
                                    data-itemid="<?php echo $item['itemid']; ?>"
                                    style="width:20px;height:20px;border-radius:50%;border:1px solid #ccc;background:#f5f5f5;cursor:pointer;font-size:0.9rem;display:inline-flex;align-items:center;justify-content:center;padding:0;">−</button>
                            <input class="packing-qty"
                                   type="number"
                                   min="1"
                                   value="<?php echo max(1, (int)$item['quantity']); ?>"
                                   data-itemid="<?php echo $item['itemid']; ?>"
                                   style="width:36px;text-align:center;border:1px solid #ccc;border-radius:4px;padding:2px 4px;-moz-appearance:textfield;">
                            <button type="button" class="packing-qty-btn packing-qty-plus"
                                    data-itemid="<?php echo $item['itemid']; ?>"
                                    style="width:20px;height:20px;border-radius:50%;border:1px solid #ccc;background:#f5f5f5;cursor:pointer;font-size:0.9rem;display:inline-flex;align-items:center;justify-content:center;padding:0;">+</button>
                        </div>

                        <!-- Dismiss -->
                        <button class="packing-dismiss"
                                data-itemid="<?php echo $item['itemid']; ?>"
                                title="Remove item"
                                style="width:18px;height:18px;border-radius:50%;border:none;background:#fdecea;cursor:pointer;color:#c0392b;font-size:0.65rem;display:inline-flex;align-items:center;justify-content:center;padding:0;flex-shrink:0;">✕</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

        </section>
    </main>

    <!-- Add item modal (functionality to be added later) -->
    <div class="packing-modal-backdrop" id="addItemModal" aria-hidden="true">
        <div class="packing-modal" role="dialog" aria-modal="true">
            <div class="packing-modal-header">
                <h2>Add item</h2>
                <button class="packing-modal-close" id="closeAddItemModal" aria-label="Close">✕</button>
            </div>
            <p style="padding:1rem;color:#888;">Coming soon.</p>
        </div>
    </div>

    <script src="icons.js"></script>
    <script>
        const TRIPID = <?php echo $tripid; ?>;

        // ── Update summary counts ──
        function updateCounts() {
            const all    = document.querySelectorAll('.packing-item');
            const packed = document.querySelectorAll('.packing-item[data-checked="1"]');
            const left   = all.length - packed.length;
            document.getElementById('packedCount').textContent   = `${packed.length}/${all.length} packed`;
            document.getElementById('unpackedCount').textContent = `${left} item${left !== 1 ? 's' : ''} left`;
        }

        // ── Filter buttons ──
        document.querySelectorAll('.packing-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.packing-filter-btn').forEach(b => b.classList.remove('packing-filter-btn--active'));
                btn.classList.add('packing-filter-btn--active');
                const filter = btn.dataset.filter;
                document.querySelectorAll('.packing-item').forEach(item => {
                    const checked = item.dataset.checked === '1';
                    if (filter === 'packed')     item.style.display = checked ? '' : 'none';
                    else if (filter === 'not-packed') item.style.display = !checked ? '' : 'none';
                    else item.style.display = '';
                });
                // Hide empty category groups
                document.querySelectorAll('.packing-category-group').forEach(group => {
                    const visible = [...group.querySelectorAll('.packing-item')].some(i => i.style.display !== 'none');
                    group.style.display = visible ? '' : 'none';
                });
            });
        });

        // ── Check toggle ──
        document.querySelectorAll('.packing-check').forEach(btn => {
            btn.addEventListener('click', function() {
                const item    = this.closest('.packing-item');
                const itemid  = item.dataset.itemid;
                const checked = item.dataset.checked === '1' ? 0 : 1;

                item.dataset.checked = checked;
                this.classList.toggle('packing-check--checked', checked === 1);
                item.querySelector('.packing-item-name').classList.toggle('packing-item-name--packed', checked === 1);

                fetch('packing-list.php?tripid=' + TRIPID, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `toggle_item=1&itemid=${itemid}&checked=${checked}`
                });

                updateCounts();
            });
        });

            // ── Quantity buttons ──
        document.querySelectorAll('.packing-qty-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemid = this.dataset.itemid;
                const input  = document.querySelector(`.packing-qty[data-itemid="${itemid}"]`);
                let val = parseInt(input.value) || 1;
                if (this.classList.contains('packing-qty-minus')) val = Math.max(1, val - 1);
                if (this.classList.contains('packing-qty-plus'))  val = val + 1;
                input.value = val;

                fetch('packing-list.php?tripid=' + TRIPID, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `update_quantity=1&itemid=${itemid}&quantity=${val}`
                });
            });
        });

        // ── Quantity manual input ──
        document.querySelectorAll('.packing-qty').forEach(input => {
            input.addEventListener('change', function() {
                const itemid   = this.dataset.itemid;
                const quantity = Math.max(1, parseInt(this.value) || 1);
                this.value = quantity;

                fetch('packing-list.php?tripid=' + TRIPID, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `update_quantity=1&itemid=${itemid}&quantity=${quantity}`
                });
            });
        });

        // ── Dismiss ──
        document.querySelectorAll('.packing-dismiss').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemid = this.dataset.itemid;
                const item   = this.closest('.packing-item');
                const group  = item.closest('.packing-category-group');

                fetch('packing-list.php?tripid=' + TRIPID, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `dismiss_item=1&itemid=${itemid}`
                });

                item.remove();
                if (!group.querySelectorAll('.packing-item').length) group.remove();
                updateCounts();
            });
        });

        // ── Add item modal (placeholder) ──
        document.getElementById('openAddItemModal').addEventListener('click', () => {
            document.getElementById('addItemModal').removeAttribute('aria-hidden');
            document.getElementById('addItemModal').style.display = 'flex';
        });
        document.getElementById('closeAddItemModal').addEventListener('click', () => {
            document.getElementById('addItemModal').setAttribute('aria-hidden', 'true');
            document.getElementById('addItemModal').style.display = 'none';
        });
    </script>
</body>
</html>
<?php $mysqli->close(); ?>