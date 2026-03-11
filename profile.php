<?php
session_start();

// Check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: logout.php");
    exit();
}

require("connectionInclude.php");

$notif_count = $mysqli->query("SELECT COUNT(*) as cnt FROM notifications WHERE userid = {$_SESSION['logged_in_user_id']} AND isread = 0")->fetch_assoc()['cnt'];
$notif_icon = $notif_count > 0 ? 'img/notif2.png' : 'img/notif.png';

$user_id = $_SESSION['logged_in_user_id'];

// Handle profile save (username + email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $username = $mysqli->real_escape_string(trim($_POST['username']));
    $email    = $mysqli->real_escape_string(trim($_POST['email']));
    if (empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'error' => 'Username and email are required.']);
    } else {
        $mysqli->query("UPDATE users SET username = '$username', email = '$email' WHERE userid = $user_id");
        if ($mysqli->error) {
            echo json_encode(['success' => false, 'error' => $mysqli->error]);
        } else {
            $_SESSION['logged_in_user'] = $username;
            echo json_encode(['success' => true]);
        }
    }
    exit();
}

// Handle pfp URL save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_pfp'])) {
    $pfpurl = $mysqli->real_escape_string(trim($_POST['pfpurl']));
    $mysqli->query("UPDATE users SET pfpurl = '$pfpurl' WHERE userid = $user_id");
    echo json_encode(['success' => $mysqli->error ? false : true, 'error' => $mysqli->error]);
    exit();
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_password'])) {
    $current = md5($_POST['current_password']);
    $new     = md5($_POST['new_password']);
    $check   = $mysqli->query("SELECT userid FROM users WHERE userid = $user_id AND password = '$current'");
    if ($check->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'Current password is incorrect.']);
    } else {
        $mysqli->query("UPDATE users SET password = '$new' WHERE userid = $user_id");
        echo json_encode(['success' => true]);
    }
    exit();
}

// Get current user info
$user_query = $mysqli->query("SELECT userid, username, email, pfpurl FROM users WHERE userid = $user_id");
$user = $user_query->fetch_assoc();

function checkNull($dataPoint) {
    return ($dataPoint === null || $dataPoint === "") ? "N/A" : $dataPoint;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PackMate — Profile</title>
  <link rel="stylesheet" href="style.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap"
    rel="stylesheet">

  <style>
    :root {
      --bg: #ECEAE5;
      --surface: #FFFFFF;
      --card: #FFFFFF;
      --text: #1A1A1A;
      --text-primary: #1A1A1A;
      --text-secondary: #666;
      --muted: #666;
      --text-muted: #999;
      --accent-green: #2D8C4E;
      --green: #2D8C4E;
      --accent-red: #A51C1C;
      --icon-bg: #2A2A2A;
      --shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
      --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
      --radius: 18px;
      --radius-sm: 12px;
      --input-bg: #F5F3EF;
      --border: #E0DDD8;
      --badge-bg: #F5F3EF;
      --badge-text: #666;
      --unread-bg: #f0faf3;
      --unread-border: #2D8C4E;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: var(--bg);
      font-family: 'DM Sans', sans-serif;
      color: var(--text);
      min-height: 100vh;
      margin: 0;
    }

    .sidebar {
      position: fixed !important;
      top: 0;
      left: 0;
      height: 100vh;
      width: 200px;
      z-index: 100;
      overflow-y: auto;
    }

    main {
      margin-left: 200px;
      padding: 40px 48px;
      min-height: 100vh;
      background: var(--bg);
      box-sizing: border-box;
    }

    .page {
      width: 100%;
      max-width: 100%;
      display: flex;
      flex-direction: column;
      gap: 16px;
      animation: fadeUp 0.5s ease both;
    }

    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* PROFILE CARD */
    .profile-card {
      background: var(--card);
      border-radius: var(--radius);
      padding: 28px 32px;
      display: flex;
      align-items: center;
      gap: 24px;
      box-shadow: var(--shadow);
      flex-wrap: wrap;
    }

    .avatar-wrap {
      position: relative;
      width: 76px;
      height: 76px;
      flex-shrink: 0;
      cursor: pointer;
    }

    .avatar {
      width: 76px;
      height: 76px;
      border-radius: 16px;
      background: #D5D0C8;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: none;
    }

    .avatar.has-photo svg {
      display: none;
    }

    .avatar.has-photo img {
      display: block;
    }

    .avatar-overlay {
      position: absolute;
      inset: 0;
      border-radius: 16px;
      background: rgba(0, 0, 0, 0.45);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.2s;
      color: #fff;
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-align: center;
    }

    .avatar-wrap:hover .avatar-overlay {
      opacity: 1;
    }

    #avatar-input {
      display: none;
    }

    .profile-info {
      flex: 1;
      min-width: 160px;
    }

    .profile-info h1 {
      font-family: 'Syne', sans-serif;
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: -0.5px;
      line-height: 1.1;
    }

    .profile-info .handle {
      font-size: 0.82rem;
      color: var(--text-secondary);
      letter-spacing: 0.04em;
      margin-top: 4px;
    }

    .profile-edit-inputs {
      display: none;
      flex-direction: column;
      gap: 8px;
      flex: 1;
      min-width: 160px;
    }

    .profile-edit-inputs input {
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      background: var(--input-bg);
      border: 1.5px solid var(--border);
      border-radius: 10px;
      padding: 8px 14px;
      color: var(--text);
      outline: none;
      transition: border-color 0.2s;
      width: 100%;
    }

    .profile-edit-inputs input:first-child {
      font-family: 'Syne', sans-serif;
      font-size: 1.1rem;
      font-weight: 700;
    }

    .profile-edit-inputs input:focus {
      border-color: var(--accent-green);
    }

    .btn-edit {
      background: var(--accent-green);
      color: #fff;
      font-family: 'Syne', sans-serif;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      border: none;
      border-radius: 100px;
      padding: 10px 22px;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      flex-shrink: 0;
    }

    .btn-edit:hover {
      background: #25753f;
      transform: translateY(-1px);
    }

    /* GRID */
    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .menu-item.open {
      grid-column: span 2;
    }

    .menu-item.no-span.open {
      grid-column: span 1;
    }

    .menu-item {
      background: var(--card);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow);
      cursor: pointer;
      transition: transform 0.18s, box-shadow 0.18s;
    }

    .menu-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.11);
    }

    .menu-item.open {
      transform: none;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    }

    .menu-header {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 22px 24px;
    }

    .menu-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: var(--icon-bg);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      color: #fff;
    }

    .menu-text strong {
      font-family: 'Syne', sans-serif;
      font-size: 0.95rem;
      font-weight: 700;
      display: block;
      line-height: 1.2;
    }

    .menu-text span {
      font-size: 0.78rem;
      color: var(--text-secondary);
      margin-top: 2px;
      display: block;
    }

    .menu-chevron {
      margin-left: auto;
      color: var(--text-secondary);
      transition: transform 0.25s;
      flex-shrink: 0;
    }

    .menu-item.open .menu-chevron {
      transform: rotate(180deg);
    }

    .panel {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.38s ease;
    }

    .panel-inner {
      padding: 20px 24px 24px;
      border-top: 1.5px solid var(--border);
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    /* Form */
    .field {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .field label {
      font-size: 0.72rem;
      font-weight: 500;
      color: var(--text-secondary);
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .field input[type="text"],
    .field input[type="email"],
    .field input[type="tel"],
    .field input[type="password"] {
      font-family: 'DM Sans', sans-serif;
      font-size: 0.9rem;
      background: var(--input-bg);
      border: 1.5px solid var(--border);
      border-radius: 10px;
      padding: 10px 14px;
      color: var(--text);
      outline: none;
      transition: border-color 0.2s;
      width: 100%;
    }

    .field input:focus {
      border-color: var(--accent-green);
    }

    .field-msg {
      font-size: 0.75rem;
      padding: 7px 12px;
      border-radius: 8px;
      display: none;
    }

    .field-msg.error {
      background: #fdecea;
      color: var(--accent-red);
      display: block;
    }

    .field-msg.success {
      background: #e6f5ec;
      color: var(--accent-green);
      display: block;
    }

    .btn-save {
      background: var(--accent-green);
      color: #fff;
      font-family: 'Syne', sans-serif;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      border: none;
      border-radius: 100px;
      padding: 10px 22px;
      cursor: pointer;
      align-self: flex-start;
      transition: background 0.2s, transform 0.15s;
      margin-top: 4px;
    }

    .btn-save:hover {
      background: #25753f;
      transform: translateY(-1px);
    }

    /* Toggles */
    .toggle-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid var(--border);
    }

    .toggle-row:last-of-type {
      border-bottom: none;
    }

    .toggle-label strong {
      font-size: 0.875rem;
      font-weight: 500;
      display: block;
    }

    .toggle-label small {
      font-size: 0.75rem;
      color: var(--text-secondary);
    }

    .toggle {
      position: relative;
      width: 42px;
      height: 24px;
      flex-shrink: 0;
    }

    .toggle input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .toggle-slider {
      position: absolute;
      inset: 0;
      background: #D0CCC6;
      border-radius: 100px;
      cursor: pointer;
      transition: background 0.25s;
    }

    .toggle-slider::before {
      content: '';
      position: absolute;
      width: 18px;
      height: 18px;
      left: 3px;
      top: 3px;
      background: #fff;
      border-radius: 50%;
      transition: transform 0.25s;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle input:checked+.toggle-slider {
      background: var(--accent-green);
    }

    .toggle input:checked+.toggle-slider::before {
      transform: translateX(18px);
    }

    /* Subscription */
    .plan-badge {
      background: var(--input-bg);
      border-radius: 12px;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .plan-badge strong {
      font-family: 'Syne', sans-serif;
      font-size: 0.95rem;
    }

    .plan-badge span {
      font-size: 0.78rem;
      color: var(--text-secondary);
      margin-top: 2px;
      display: block;
    }

    .plan-tag {
      background: var(--icon-bg);
      color: #fff;
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 100px;
      white-space: nowrap;
    }

    .btn-upgrade {
      background: transparent;
      color: var(--accent-green);
      font-family: 'Syne', sans-serif;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      border: 2px solid var(--accent-green);
      border-radius: 100px;
      padding: 8px 18px;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
      align-self: flex-start;
      margin-top: 4px;
    }

    .btn-upgrade:hover {
      background: var(--accent-green);
      color: #fff;
    }

    /* FAQ */
    .faq-item {
      border-bottom: 1px solid var(--border);
    }

    .faq-item:last-child {
      border-bottom: none;
    }

    .faq-q {
      width: 100%;
      background: none;
      border: none;
      text-align: left;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--text);
      padding: 12px 0;
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 8px;
    }

    .faq-q svg {
      flex-shrink: 0;
      transition: transform 0.2s;
      color: var(--text-secondary);
    }

    .faq-item.open .faq-q svg {
      transform: rotate(180deg);
    }

    .faq-a {
      font-size: 0.82rem;
      color: var(--text-secondary);
      line-height: 1.6;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease, padding 0.3s;
    }

    .faq-item.open .faq-a {
      max-height: 200px;
      padding-bottom: 12px;
    }

    /* Logout */
    .btn-logout {
      background: var(--accent-red);
      color: #fff;
      font-family: 'Syne', sans-serif;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      border: none;
      border-radius: 100px;
      padding: 18px;
      cursor: pointer;
      width: 100%;
      transition: background 0.2s, transform 0.15s;
      box-shadow: 0 4px 16px rgba(165, 28, 28, 0.25);
    }

    .btn-logout:hover {
      background: #8c1818;
      transform: translateY(-1px);
    }

    /* MODAL */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.4);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.2s;
      z-index: 100;
      padding: 20px;
    }

    .modal-overlay.active {
      opacity: 1;
      pointer-events: all;
    }

    .modal {
      background: var(--card);
      border-radius: var(--radius);
      padding: 32px 36px;
      max-width: 380px;
      width: 100%;
      transform: scale(0.95) translateY(10px);
      transition: transform 0.25s ease;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
      text-align: center;
    }

    .modal-overlay.active .modal {
      transform: scale(1) translateY(0);
    }

    .modal-icon {
      width: 56px;
      height: 56px;
      background: #fdecea;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      color: var(--accent-red);
    }

    .modal h2 {
      font-family: 'Syne', sans-serif;
      font-size: 1.2rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .modal p {
      font-size: 0.875rem;
      color: var(--text-secondary);
      line-height: 1.6;
    }

    .modal-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .btn-cancel {
      flex: 1;
      background: var(--input-bg);
      color: var(--text);
      font-family: 'Syne', sans-serif;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.07em;
      text-transform: uppercase;
      border: none;
      border-radius: 100px;
      padding: 12px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .btn-cancel:hover {
      background: var(--border);
    }

    .btn-confirm-logout {
      flex: 1;
      background: var(--accent-red);
      color: #fff;
      font-family: 'Syne', sans-serif;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.07em;
      text-transform: uppercase;
      border: none;
      border-radius: 100px;
      padding: 12px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .btn-confirm-logout:hover {
      background: #8c1818;
    }

    /* Toast */
    .toast {
      position: fixed;
      bottom: 32px;
      left: 50%;
      transform: translateX(-50%) translateY(20px);
      background: var(--icon-bg);
      color: #fff;
      font-size: 0.82rem;
      font-weight: 500;
      padding: 10px 22px;
      border-radius: 100px;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.25s, transform 0.25s;
      z-index: 200;
      white-space: nowrap;
    }

    .toast.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }

    @media (max-width: 540px) {
      .grid {
        grid-template-columns: 1fr;
      }

      .profile-card {
        flex-direction: column;
        align-items: flex-start;
      }

      .btn-edit {
        width: 100%;
        text-align: center;
      }
    }
  </style>
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
            <button type="reset" onclick="location.href='notifications.php'">
                <img src="<?php echo $notif_icon; ?>" alt="" class="icon">
                <span>Notifications</span>
            </button>
            <button type="reset" onclick="location.href='profile.php'">
              <img id="avatar-img" class="icon" style="border-radius:7px;" src="<?php echo htmlspecialchars($user['pfpurl'] ?? ''); ?>" alt="Profile photo">
              <span>Profile</span></button>
        </div>
        <div class="nav-bottom">
            <hr>
            <button class="logout" type="reset" onclick="location.href='logout.php'"><img src="img/home.png" alt=""
                    class="icon"><span>Logout</span></button>
        </div>
    </nav>


  <main>
    <div class="page">

      <!-- PROFILE HEADER -->

      <div class="profile-card">
        <div class="avatar-wrap">
          <div class="avatar <?php echo !empty($user['pfpurl']) ? 'has-photo' : ''; ?>" id="avatar">
            <svg width="42" height="42" viewBox="0 0 42 42" fill="none">
              <circle cx="21" cy="16" r="8" fill="#888" />
              <ellipse cx="21" cy="36" rx="14" ry="9" fill="#888" />
            </svg>
            <img id="avatar-img" src="<?php echo htmlspecialchars($user['pfpurl'] ?? ''); ?>" alt="Profile photo">
          </div>
        </div>

        <div class="profile-info" id="profile-display">
          <h1 id="display-name"><?php echo htmlspecialchars($user['username']); ?></h1>
          <div class="handle" id="display-handle"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>

        <div class="profile-edit-inputs" id="profile-edit">
          <input type="text" id="edit-name" placeholder="Username" maxlength="24">
          <input type="email" id="edit-handle" placeholder="Email">
        </div>

        <div class="profile-edit-inputs" id="profile-edit">
            <input type="text" id="edit-name" placeholder="Username" maxlength="24">
            <input type="email" id="edit-handle" placeholder="Email">
            <input type="text" id="edit-pfp" placeholder="Profile photo URL">
        </div>


      </div>

      <!-- GRID -->
      <div class="grid">

        <!-- Personal Data -->
        <div class="menu-item" id="card-personal">
          <div class="menu-header" onclick="toggleCard('personal')">
            <div class="menu-icon">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
            </div>
            <div class="menu-text"><strong>Personal Data</strong><span>Username, Email, Profile Photo</span></div>
            <svg class="menu-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </div>
          <div class="panel" id="panel-personal">
            <div class="panel-inner">
              <div class="field"><label>Username</label><input type="text" id="pd-username" maxlength="24" placeholder="Your username" value="<?php echo htmlspecialchars($user['username']); ?>"></div>
              <div class="field"><label>Email</label><input type="email" id="pd-email" placeholder="you@example.com" value="<?php echo htmlspecialchars($user['email']); ?>"></div>
              <div class="field"><label>Profile Photo URL</label><input type="text" id="pd-pfpurl" placeholder="https://..." value="<?php echo htmlspecialchars($user['pfpurl'] ?? ''); ?>"></div>
              <div class="field-msg" id="pd-msg"></div>
              <button class="btn-save" onclick="savePersonalData()">Save Changes</button>
            </div>
          </div>
        </div>

        <!-- Help Center -->
        <div class="menu-item no-span" id="card-help">
          <div class="menu-header" onclick="toggleCard('help')">
            <div class="menu-icon">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
              </svg>
            </div>
            <div class="menu-text"><strong>Help Center</strong><span>Support and FAQs</span></div>
            <svg class="menu-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
              stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <polyline points="6 9 12 15 18 9" />
            </svg>
          </div>
          <div class="panel" id="panel-help">
            <div class="panel-inner">
              <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">How do I create a packing list?<svg width="14"
                    height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9" />
                  </svg></button>
                <div class="faq-a">Head to the Dashboard and tap "New List". Give it a name, set your destination and
                  travel dates, then start adding items from our smart suggestions or manually.</div>
              </div>
              <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">Can I share lists with others?<svg width="14"
                    height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9" />
                  </svg></button>
                <div class="faq-a">Yes! Open any list, tap the share icon, and send an invite link. Collaborators can
                  view and check off items in real time. This feature requires a Pro plan.</div>
              </div>
              <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">How do I cancel my subscription?<svg width="14"
                    height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9" />
                  </svg></button>
                <div class="faq-a">Go to Subscription in your profile and tap "Manage Plan". You can cancel anytime and
                  your Pro access continues until the end of the billing period.</div>
              </div>
              <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">I forgot my password. What do I do?<svg width="14"
                    height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9" />
                  </svg></button>
                <div class="faq-a">On the login screen tap "Forgot password?" and enter your email. We'll send a reset
                  link within a few minutes. Check your spam folder if it doesn't arrive.</div>
              </div>
              <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">How do I contact support?<svg width="14" height="14"
                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="6 9 12 15 18 9" />
                  </svg></button>
                <div class="faq-a">Email us at support@packmates.app or use the chat bubble in the bottom-right of the
                  app. We typically respond within 24 hours on business days.</div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <button class="btn-logout" onclick="openLogoutModal()">Logout</button>

    </div>

    <!-- LOGOUT MODAL -->
    <div class="modal-overlay" id="logout-modal">
      <div class="modal">
        <div class="modal-icon">
          <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" viewBox="0 0 24 24">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
        </div>
        <h2>Log out of PackMate?</h2>
        <p>You'll need to sign back in to access your packing lists and account settings.</p>
        <div class="modal-actions">
          <button class="btn-cancel" onclick="closeLogoutModal()">Stay</button>
          <button class="btn-confirm-logout" onclick="confirmLogout()">Log Out</button>
        </div>
      </div>
    </div>

    <div class="toast" id="toast"></div>

    <script>
      // ── UTILS ──
      function showToast(msg, duration = 2500) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), duration);
      }

      function showMsg(id, msg, type) {
        const el = document.getElementById(id);
        el.textContent = msg;
        el.className = 'field-msg ' + type;
        setTimeout(() => { el.className = 'field-msg'; el.textContent = ''; }, 3500);
      }

      // ── INIT ──
      window.addEventListener('DOMContentLoaded', () => {
        // Apply avatar if pfpurl is set
        const avatarImg = document.getElementById('avatar-img');
        if (avatarImg.src && avatarImg.src !== window.location.href) {
          document.getElementById('avatar').classList.add('has-photo');
        }
      });

      // ── PROFILE EDIT (username + email) ──
      let editingProfile = false;

      function toggleProfileEdit() {
        editingProfile = !editingProfile;
        const display = document.getElementById('profile-display');
        const editEl  = document.getElementById('profile-edit');
        const btn     = document.getElementById('btn-edit-profile');

        if (editingProfile) {
          document.getElementById('edit-name').value   = document.getElementById('display-name').textContent;
          document.getElementById('edit-handle').value = document.getElementById('display-handle').textContent;
          document.getElementById('edit-pfp').value = document.getElementById('avatar-img').src !== window.location.href ? document.getElementById('avatar-img').src : '';
          display.style.display = 'none';
          editEl.style.display  = 'flex';
          btn.textContent = 'Save';
        } else {
          const username = document.getElementById('edit-name').value.trim();
          const email    = document.getElementById('edit-handle').value.trim();
          const pfpurl = document.getElementById('edit-pfp').value.trim();
fetch('profile.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `save_pfp=1&pfpurl=${encodeURIComponent(pfpurl)}`
}).then(r => r.json()).then(data => {
    if (data.success) {
        const img = document.getElementById('avatar-img');
        if (pfpurl) {
            img.src = pfpurl;
            document.getElementById('avatar').classList.add('has-photo');
        } else {
            img.src = '';
            document.getElementById('avatar').classList.remove('has-photo');
        }
    }
});
          if (!username || !email) { editingProfile = true; showToast('Username and email cannot be empty.'); return; }

          fetch('profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `save_profile=1&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}`
          }).then(r => r.json()).then(data => {
            if (data.success) {
              document.getElementById('display-name').textContent   = username;
              document.getElementById('display-handle').textContent = email;
              showToast('Profile updated!');
            } else {
              showToast('Error: ' + (data.error || 'Could not save.'));
              editingProfile = true;
              return;
            }
          });

          display.style.display = '';
          editEl.style.display  = 'none';
          btn.textContent = 'Edit Profile';
        }
      }

      // ── CARD TOGGLE ──
      function toggleCard(id) {
        const card  = document.getElementById('card-' + id);
        const panel = document.getElementById('panel-' + id);
        const isOpen = card.classList.contains('open');

        document.querySelectorAll('.menu-item.open').forEach(c => {
          c.classList.remove('open');
          c.querySelector('.panel').style.maxHeight = '';
        });

        if (!isOpen) {
          card.classList.add('open');
          panel.style.maxHeight = panel.scrollHeight + 80 + 'px';
        }
      }

      // ── PERSONAL DATA (username, email, pfp url) ──
      function savePersonalData() {
        const username = document.getElementById('pd-username').value.trim();
        const email    = document.getElementById('pd-email').value.trim();
        const pfpurl   = document.getElementById('pd-pfpurl').value.trim();

        if (!username || !email) { showMsg('pd-msg', 'Username and email are required.', 'error'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showMsg('pd-msg', 'Please enter a valid email address.', 'error'); return; }

        // Save username + email
        fetch('profile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `save_profile=1&username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}`
        }).then(r => r.json()).then(data => {
          if (!data.success) { showMsg('pd-msg', data.error || 'Could not save.', 'error'); return; }

          // Save pfp url separately
          fetch('profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `save_pfp=1&pfpurl=${encodeURIComponent(pfpurl)}`
          }).then(r => r.json()).then(pfpData => {
            if (pfpData.success) {
              // Update avatar display
              const img = document.getElementById('avatar-img');
              if (pfpurl) {
                img.src = pfpurl;
                document.getElementById('avatar').classList.add('has-photo');
              } else {
                img.src = '';
                document.getElementById('avatar').classList.remove('has-photo');
              }
              // Update profile header display
              document.getElementById('display-name').textContent   = username;
              document.getElementById('display-handle').textContent = email;
              showMsg('pd-msg', 'Saved successfully!', 'success');
              showToast('Personal data saved!');
            } else {
              showMsg('pd-msg', pfpData.error || 'Could not save photo URL.', 'error');
            }
          });
        });
      }

      // ── SECURITY (password change) ──
      function savePassword() {
        const current = document.getElementById('sec-current').value;
        const newPw   = document.getElementById('sec-new').value;
        const confirm = document.getElementById('sec-confirm').value;
        if (!current) { showMsg('sec-msg', 'Please enter your current password.', 'error'); return; }
        if (newPw.length < 8) { showMsg('sec-msg', 'New password must be at least 8 characters.', 'error'); return; }
        if (newPw !== confirm) { showMsg('sec-msg', 'Passwords do not match.', 'error'); return; }

        fetch('profile.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `save_password=1&current_password=${encodeURIComponent(current)}&new_password=${encodeURIComponent(newPw)}`
        }).then(r => r.json()).then(data => {
          if (data.success) {
            document.getElementById('sec-current').value = '';
            document.getElementById('sec-new').value     = '';
            document.getElementById('sec-confirm').value = '';
            showMsg('sec-msg', 'Password updated successfully!', 'success');
            showToast('Password updated!');
          } else {
            showMsg('sec-msg', data.error || 'Could not update password.', 'error');
          }
        });
      }

      // ── NOTIFICATIONS (localStorage only — no DB table) ──
      function loadNotifications() {
        try {
          const saved = JSON.parse(localStorage.getItem('pm_notif') || '{}');
          document.getElementById('notif-email').checked     = saved.email     ?? true;
          document.getElementById('notif-push').checked      = saved.push      ?? false;
          document.getElementById('notif-reminders').checked = saved.reminders ?? true;
          document.getElementById('notif-updates').checked   = saved.updates   ?? false;
        } catch (e) {
          document.getElementById('notif-email').checked     = true;
          document.getElementById('notif-reminders').checked = true;
        }
      }

      function saveNotifications() {
        try {
          localStorage.setItem('pm_notif', JSON.stringify({
            email:     document.getElementById('notif-email').checked,
            push:      document.getElementById('notif-push').checked,
            reminders: document.getElementById('notif-reminders').checked,
            updates:   document.getElementById('notif-updates').checked,
          }));
        } catch (e) {}
        showToast('Notification preferences saved!');
      }

      // ── PRIVACY (localStorage only) ──
      function loadPrivacy() {
        try {
          const saved = JSON.parse(localStorage.getItem('pm_privacy') || '{}');
          document.getElementById('priv-analytics').checked   = saved.analytics   ?? true;
          document.getElementById('priv-personalise').checked = saved.personalise ?? true;
          document.getElementById('priv-share').checked       = saved.share       ?? false;
        } catch (e) {
          document.getElementById('priv-analytics').checked   = true;
          document.getElementById('priv-personalise').checked = true;
        }
      }

      function savePrivacy() {
        try {
          localStorage.setItem('pm_privacy', JSON.stringify({
            analytics:   document.getElementById('priv-analytics').checked,
            personalise: document.getElementById('priv-personalise').checked,
            share:       document.getElementById('priv-share').checked,
          }));
        } catch (e) {}
        showToast('Privacy settings saved!');
      }

      loadNotifications();
      loadPrivacy();

      // ── FAQ ──
      function toggleFaq(btn) {
        const item   = btn.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
        if (!isOpen) item.classList.add('open');
      }

      // ── LOGOUT MODAL ──
      function openLogoutModal()  { document.getElementById('logout-modal').classList.add('active'); }
      function closeLogoutModal() { document.getElementById('logout-modal').classList.remove('active'); }
      function confirmLogout() {
        closeLogoutModal();
        showToast('Logging out…');
        setTimeout(() => { location.href = 'logout.php'; }, 1500);
      }
    </script>
</body>

</html>
<?php $mysqli->close(); ?>