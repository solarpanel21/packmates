<?php
session_start();

// Check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: logout.php");
    exit();
}

require("connectionInclude.php");

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

// Check if tripid is provided
$tripid = filter_input(INPUT_GET, 'tripid', FILTER_VALIDATE_INT);
if ($tripid === false || $tripid === null) {
    header("Location: home.php");
    exit();
}

// Make sure this trip belongs to the logged in user
$trip_stmt = $mysqli->prepare("SELECT * FROM trips WHERE tripid = ? AND userid = ? AND (isdeleted = 0 OR isdeleted IS NULL)");
$trip_stmt->bind_param("ii", $tripid, $user_id);
$trip_stmt->execute();
$trip_query = $trip_stmt->get_result();

$member_stmt = $mysqli->prepare("SELECT * FROM tripmembers WHERE tripid = ? AND userid = ?");
$member_stmt->bind_param("ii", $tripid, $user_id);
$member_stmt->execute();
$member_query = $member_stmt->get_result();
if (($trip_query->num_rows === 0 ) && ($member_query->num_rows === 0 )) {
    header("Location: home.php");
    exit();
}

$trip_data_stmt = $mysqli->prepare("SELECT * FROM trips WHERE tripid = ? AND (isdeleted = 0 OR isdeleted IS NULL)");
$trip_data_stmt->bind_param("i", $tripid);
$trip_data_stmt->execute();
$trip = $trip_data_stmt->get_result()->fetch_assoc();


// Handle item dismiss (POST from X button)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss_item'])) {
    $itemid = filter_input(INPUT_POST, 'itemid', FILTER_VALIDATE_INT);
    if ($itemid === false || $itemid === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid item id']);
        exit();
    }
    $existing_stmt = $mysqli->prepare("SELECT id FROM tripitems WHERE tripid = ? AND itemid = ?");
    $existing_stmt->bind_param("ii", $tripid, $itemid);
    $existing_stmt->execute();
    $existing = $existing_stmt->get_result();
    if ($existing->num_rows > 0) {
        $update_stmt = $mysqli->prepare("UPDATE tripitems SET isdismissed = 1 WHERE tripid = ? AND itemid = ?");
        $update_stmt->bind_param("ii", $tripid, $itemid);
        $update_stmt->execute();
    } else {
        $insert_stmt = $mysqli->prepare("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed, quantity) VALUES (?, ?, 0, 1, 1)");
        $insert_stmt->bind_param("ii", $tripid, $itemid);
        $insert_stmt->execute();
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle item check toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_item'])) {
    $itemid = filter_input(INPUT_POST, 'itemid', FILTER_VALIDATE_INT);
    $checked = filter_input(INPUT_POST, 'checked', FILTER_VALIDATE_INT);
    if ($itemid === false || $itemid === null || $checked === false || $checked === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit();
    }
    $checked = $checked ? 1 : 0;
    $existing_stmt = $mysqli->prepare("SELECT id FROM tripitems WHERE tripid = ? AND itemid = ?");
    $existing_stmt->bind_param("ii", $tripid, $itemid);
    $existing_stmt->execute();
    $existing = $existing_stmt->get_result();
    if ($existing->num_rows > 0) {
        $update_stmt = $mysqli->prepare("UPDATE tripitems SET ischecked = ? WHERE tripid = ? AND itemid = ?");
        $update_stmt->bind_param("iii", $checked, $tripid, $itemid);
        $update_stmt->execute();
    } else {
        $insert_stmt = $mysqli->prepare("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed, quantity) VALUES (?, ?, ?, 0, 1)");
        $insert_stmt->bind_param("iii", $tripid, $itemid, $checked);
        $insert_stmt->execute();
    }
    echo json_encode(['success' => true]);
    exit();
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $itemid = filter_input(INPUT_POST, 'itemid', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    if ($itemid === false || $itemid === null || $quantity === false || $quantity === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit();
    }
    $quantity = max(1, $quantity);
    $existing_stmt = $mysqli->prepare("SELECT id FROM tripitems WHERE tripid = ? AND itemid = ?");
    $existing_stmt->bind_param("ii", $tripid, $itemid);
    $existing_stmt->execute();
    $existing = $existing_stmt->get_result();
    if ($existing->num_rows > 0) {
        $update_stmt = $mysqli->prepare("UPDATE tripitems SET quantity = ? WHERE tripid = ? AND itemid = ?");
        $update_stmt->bind_param("iii", $quantity, $tripid, $itemid);
        $update_stmt->execute();
    } else {
        $insert_stmt = $mysqli->prepare("INSERT INTO tripitems (tripid, itemid, ischecked, isdismissed, quantity) VALUES (?, ?, 0, 0, ?)");
        $insert_stmt->bind_param("iii", $tripid, $itemid, $quantity);
        $insert_stmt->execute();
    }
    echo json_encode(['success' => true, 'error' => $mysqli->error, 'rows' => $existing->num_rows]);
    exit();
}

// Handle custom item add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_custom_item'])) {
    $itemname = trim($_POST['itemname'] ?? '');
    if (!empty($itemname)) {
        $timecreated = date('Y-m-d H:i:s');
        $insert_custom_stmt = $mysqli->prepare("INSERT INTO customitems (customname, timecreated, userid, tripid, ischecked, isdismissed, quantity)
                        VALUES (?, ?, ?, ?, 0, 0, 1)");
        $insert_custom_stmt->bind_param("ssii", $itemname, $timecreated, $user_id, $tripid);
        $insert_custom_stmt->execute();
    }
    echo json_encode(['success' => true, 'id' => $mysqli->insert_id]);
    exit();
}

// Handle custom item check toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_custom'])) {
    $itemid = filter_input(INPUT_POST, 'itemid', FILTER_VALIDATE_INT);
    $checked = filter_input(INPUT_POST, 'checked', FILTER_VALIDATE_INT);
    if ($itemid === false || $itemid === null || $checked === false || $checked === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit();
    }
    $checked = $checked ? 1 : 0;
    $toggle_custom_stmt = $mysqli->prepare("UPDATE customitems SET ischecked = ? WHERE customid = ? AND tripid = ? AND userid = ?");
    $toggle_custom_stmt->bind_param("iiii", $checked, $itemid, $tripid, $user_id);
    $toggle_custom_stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// Handle custom item dismiss
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss_custom'])) {
    $itemid = filter_input(INPUT_POST, 'itemid', FILTER_VALIDATE_INT);
    if ($itemid === false || $itemid === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid item id']);
        exit();
    }
    $dismiss_custom_stmt = $mysqli->prepare("UPDATE customitems SET isdismissed = 1 WHERE customid = ? AND tripid = ? AND userid = ?");
    $dismiss_custom_stmt->bind_param("iii", $itemid, $tripid, $user_id);
    $dismiss_custom_stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// Handle custom item quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_custom_qty'])) {
    $itemid = filter_input(INPUT_POST, 'itemid', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    if ($itemid === false || $itemid === null || $quantity === false || $quantity === null) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit();
    }
    $quantity = max(1, $quantity);
    $update_custom_qty_stmt = $mysqli->prepare("UPDATE customitems SET quantity = ? WHERE customid = ? AND tripid = ? AND userid = ?");
    $update_custom_qty_stmt->bind_param("iiii", $quantity, $itemid, $tripid, $user_id);
    $update_custom_qty_stmt->execute();
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
$items_stmt = $mysqli->prepare("
    SELECT si.itemid, si.itemname, si.category,
           COALESCE(ti.ischecked, 0) AS ischecked,
           COALESCE(ti.quantity, 1)  AS quantity
    FROM suggesteditems si
    LEFT JOIN tripitems ti ON ti.itemid = si.itemid AND ti.tripid = ?
    WHERE ($weather_condition)
      AND ($activity_condition)
      AND (ti.isdismissed IS NULL OR ti.isdismissed = 0)
    ORDER BY si.category, si.itemname
");
$items_stmt->bind_param("i", $tripid);
$items_stmt->execute();
$items_query = $items_stmt->get_result();

// Organise by category
$items_by_category = [];
$total  = 0;
$packed = 0;
while ($row = $items_query->fetch_assoc()) {
    $items_by_category[$row['category']][] = $row;
    $total++;
    if ($row['ischecked']) $packed++;
}


// Pull existing custom items for this trip
$custom_stmt = $mysqli->prepare("
    SELECT customid, customname, ischecked, quantity
    FROM customitems
    WHERE tripid = ? AND userid = ? AND (isdismissed = 0 OR isdismissed IS NULL)
");
$custom_stmt->bind_param("ii", $tripid, $user_id);
$custom_stmt->execute();
$custom_query = $custom_stmt->get_result();
$custom_items = [];
while ($row = $custom_query->fetch_assoc()) {
    $custom_items[] = $row;
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
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
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

                        <!-- Custom items -->
            <?php if (!empty($custom_items)): ?>
            <div class="packing-category-group" id="customItemsGroup">
                <div class="packing-category-header">Custom Items</div>
                <?php foreach ($custom_items as $item): ?>
                <div class="packing-item"
                    data-itemid="custom_<?php echo $item['customid']; ?>"
                    data-checked="<?php echo $item['ischecked'] ? '1' : '0'; ?>"
                    data-custom="1">
                    <button class="packing-check <?php echo $item['ischecked'] ? 'packing-check--checked' : ''; ?>"
                            aria-label="Toggle packed"></button>
                    <span class="packing-item-name <?php echo $item['ischecked'] ? 'packing-item-name--packed' : ''; ?>">
                        <?php echo htmlspecialchars($item['customname']); ?>
                    </span>
                    <div class="packing-qty-wrap" style="display:inline-flex;align-items:center;gap:4px;">
                        <button type="button" class="packing-qty-btn packing-qty-minus"
                                data-itemid="custom_<?php echo $item['customid']; ?>"
                                style="width:20px;height:20px;border-radius:50%;border:1px solid #ccc;background:#f5f5f5;cursor:pointer;font-size:0.9rem;display:inline-flex;align-items:center;justify-content:center;padding:0;">−</button>
                        <input class="packing-qty" type="number" min="1"
                            value="<?php echo max(1, (int)$item['quantity']); ?>"
                            data-itemid="custom_<?php echo $item['customid']; ?>"
                            style="width:36px;text-align:center;border:1px solid #ccc;border-radius:4px;padding:2px 4px;-moz-appearance:textfield;">
                        <button type="button" class="packing-qty-btn packing-qty-plus"
                                data-itemid="custom_<?php echo $item['customid']; ?>"
                                style="width:20px;height:20px;border-radius:50%;border:1px solid #ccc;background:#f5f5f5;cursor:pointer;font-size:0.9rem;display:inline-flex;align-items:center;justify-content:center;padding:0;">+</button>
                    </div>
                    <button class="packing-dismiss"
                            data-itemid="custom_<?php echo $item['customid']; ?>"
                            data-custom="1"
                            title="Remove item"
                            style="width:18px;height:18px;border-radius:50%;border:none;background:#fdecea;cursor:pointer;color:#c0392b;font-size:0.65rem;display:inline-flex;align-items:center;justify-content:center;padding:0;flex-shrink:0;">✕</button>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
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
   <!-- Add item modal -->
    <div class="packing-modal-backdrop" id="addItemModal" aria-hidden="true">
        <div class="packing-modal" role="dialog" aria-modal="true">
            <div class="packing-modal-header">
                <h2>Add Custom Item</h2>
                <button class="packing-modal-close" id="closeAddItemModal" aria-label="Close">✕</button>
            </div>
            <div style="padding:1rem;">
                <input type="text" id="customItemName" maxlength="32" placeholder="Item name..."
                    style="width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;font-family:inherit;font-size:0.95rem;box-sizing:border-box;">
                <button id="saveCustomItemBtn" class="primary" style="margin-top:10px;width:100%;" type="button">Save Item</button>
                <p id="customItemError" style="color:#c0392b;margin-top:6px;display:none;">Please enter an item name.</p>
            </div>
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

    // ── Attach listeners to an item element ──
    // isCustom: whether to use customitems endpoints
    function attachItemListeners(itemEl, isCustom) {
        const rawId  = itemEl.dataset.itemid; // e.g. "custom_3" or "44"
        const dbId   = isCustom ? rawId.replace('custom_', '') : rawId;
        const toggle = isCustom ? 'toggle_custom' : 'toggle_item';
        const dismiss= isCustom ? 'dismiss_custom' : 'dismiss_item';
        const qtyKey = isCustom ? 'update_custom_qty' : 'update_quantity';

        // Check
        itemEl.querySelector('.packing-check').addEventListener('click', function() {
            const checked = itemEl.dataset.checked === '1' ? 0 : 1;
            itemEl.dataset.checked = checked;
            this.classList.toggle('packing-check--checked', checked === 1);
            itemEl.querySelector('.packing-item-name').classList.toggle('packing-item-name--packed', checked === 1);
            fetch('packing-list.php?tripid=' + TRIPID, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `${toggle}=1&itemid=${dbId}&checked=${checked}`
            });
            updateCounts();
        });

        // Qty minus
        itemEl.querySelector('.packing-qty-minus').addEventListener('click', function() {
            const input = itemEl.querySelector('.packing-qty');
            const val   = Math.max(1, parseInt(input.value) - 1);
            input.value = val;
            fetch('packing-list.php?tripid=' + TRIPID, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `${qtyKey}=1&itemid=${dbId}&quantity=${val}`
            });
        });

        // Qty plus
        itemEl.querySelector('.packing-qty-plus').addEventListener('click', function() {
            const input = itemEl.querySelector('.packing-qty');
            const val   = parseInt(input.value) + 1;
            input.value = val;
            fetch('packing-list.php?tripid=' + TRIPID, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `${qtyKey}=1&itemid=${dbId}&quantity=${val}`
            });
        });

        // Qty manual
        itemEl.querySelector('.packing-qty').addEventListener('change', function() {
            const val = Math.max(1, parseInt(this.value) || 1);
            this.value = val;
            fetch('packing-list.php?tripid=' + TRIPID, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `${qtyKey}=1&itemid=${dbId}&quantity=${val}`
            });
        });

        // Dismiss
        itemEl.querySelector('.packing-dismiss').addEventListener('click', function() {
            fetch('packing-list.php?tripid=' + TRIPID, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `${dismiss}=1&itemid=${dbId}`
            });
            const group = itemEl.closest('.packing-category-group');
            itemEl.remove();
            if (!group.querySelectorAll('.packing-item').length) group.remove();
            updateCounts();
        });
    }

    // ── Attach listeners to all existing items on page load ──
    document.querySelectorAll('.packing-item').forEach(item => {
        attachItemListeners(item, item.dataset.custom === '1');
    });

    // ── Filter buttons ──
    document.querySelectorAll('.packing-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.packing-filter-btn').forEach(b => b.classList.remove('packing-filter-btn--active'));
            btn.classList.add('packing-filter-btn--active');
            const filter = btn.dataset.filter;
            document.querySelectorAll('.packing-item').forEach(item => {
                const checked = item.dataset.checked === '1';
                if (filter === 'packed')          item.style.display = checked  ? '' : 'none';
                else if (filter === 'not-packed') item.style.display = !checked ? '' : 'none';
                else                              item.style.display = '';
            });
            document.querySelectorAll('.packing-category-group').forEach(group => {
                const visible = [...group.querySelectorAll('.packing-item')].some(i => i.style.display !== 'none');
                group.style.display = visible ? '' : 'none';
            });
        });
    });

    // ── Add item modal ──
    document.getElementById('openAddItemModal').addEventListener('click', () => {
        document.getElementById('addItemModal').removeAttribute('aria-hidden');
        document.getElementById('addItemModal').style.display = 'flex';
        document.getElementById('customItemName').focus();
    });
    document.getElementById('closeAddItemModal').addEventListener('click', () => {
        document.getElementById('addItemModal').setAttribute('aria-hidden', 'true');
        document.getElementById('addItemModal').style.display = 'none';
        document.getElementById('customItemName').value = '';
        document.getElementById('customItemError').style.display = 'none';
    });

    document.getElementById('saveCustomItemBtn').addEventListener('click', function() {
        const name = document.getElementById('customItemName').value.trim();
        if (!name) {
            document.getElementById('customItemError').style.display = 'block';
            return;
        }
        document.getElementById('customItemError').style.display = 'none';

        fetch('packing-list.php?tripid=' + TRIPID, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `add_custom_item=1&itemname=${encodeURIComponent(name)}`
        }).then(res => res.json()).then(data => {
            if (!data.success) return;

            // Get or create custom items group
            let customGroup = document.getElementById('customItemsGroup');
            if (!customGroup) {
                customGroup = document.createElement('div');
                customGroup.className = 'packing-category-group';
                customGroup.id = 'customItemsGroup';
                customGroup.innerHTML = '<div class="packing-category-header">Custom Items</div>';
                document.getElementById('packingListBody').insertBefore(customGroup, document.getElementById('packingListBody').firstChild);
            }

            // Build new item element
            const item = document.createElement('div');
            item.className = 'packing-item';
            item.dataset.itemid  = 'custom_' + data.id;
            item.dataset.checked = '0';
            item.dataset.custom  = '1';
            item.innerHTML = `
                <button class="packing-check" aria-label="Toggle packed"></button>
                <span class="packing-item-name">${name}</span>
                <div class="packing-qty-wrap" style="display:inline-flex;align-items:center;gap:4px;">
                    <button type="button" class="packing-qty-btn packing-qty-minus" data-itemid="custom_${data.id}"
                            style="width:20px;height:20px;border-radius:50%;border:1px solid #ccc;background:#f5f5f5;cursor:pointer;font-size:0.9rem;display:inline-flex;align-items:center;justify-content:center;padding:0;">−</button>
                    <input class="packing-qty" type="number" min="1" value="1"
                           data-itemid="custom_${data.id}"
                           style="width:36px;text-align:center;border:1px solid #ccc;border-radius:4px;padding:2px 4px;-moz-appearance:textfield;">
                    <button type="button" class="packing-qty-btn packing-qty-plus" data-itemid="custom_${data.id}"
                            style="width:20px;height:20px;border-radius:50%;border:1px solid #ccc;background:#f5f5f5;cursor:pointer;font-size:0.9rem;display:inline-flex;align-items:center;justify-content:center;padding:0;">+</button>
                </div>
                <button class="packing-dismiss" data-itemid="custom_${data.id}" data-custom="1"
                        title="Remove item"
                        style="width:18px;height:18px;border-radius:50%;border:none;background:#fdecea;cursor:pointer;color:#c0392b;font-size:0.65rem;display:inline-flex;align-items:center;justify-content:center;padding:0;flex-shrink:0;">✕</button>`;

            customGroup.appendChild(item);
            attachItemListeners(item, true);
            updateCounts();

            // Close modal
            document.getElementById('customItemName').value = '';
            document.getElementById('addItemModal').setAttribute('aria-hidden', 'true');
            document.getElementById('addItemModal').style.display = 'none';
        });
    });
</script>
</body>
</html>
<?php $mysqli->close(); ?>
