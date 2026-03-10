<?php
session_start();

//check if logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: logout.php");
    exit();
}

//server connect script
require("connectionInclude.php");


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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --bg: #ECEAE5;
      --surface: #ffffff;
      --text-primary: #1A1A1A;
      --text-secondary: #666;
      --text-muted: #999;
      --green: #2D8C4E;
      --green-light: #f0faf0;
      --green-border: #2D8C4E;
      --unread-bg: #f0faf3;
      --unread-border: #2D8C4E;
      --dot-unread: #2D8C4E;
      --dot-read: #d0d8e8;
      --badge-bg: #F5F3EF;
      --badge-text: #666;
      --shadow: 0 2px 16px rgba(0, 0, 0, 0.07);
      --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
      --radius: 18px;
      --radius-sm: 12px;
      --border: #E0DDD8;
      --input-bg: #F5F3EF;
      --accent-green: #2D8C4E;
      --accent-red: #A51C1C;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--bg);
      min-height: 100vh;
      color: var(--text-primary);
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

    .container {
      width: 100%;
      max-width: 900px;
      margin: 0 auto;
    }

    .container {
      width: 100%;
      max-width: 680px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    /* Header Card */
    .header-card {
      background: var(--surface);
      border-radius: var(--radius);
      padding: 20px 24px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: var(--shadow-sm);
    }

    .header-icon {
      width: 52px;
      height: 52px;
      background: #f0faf0;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .header-icon svg {
      color: var(--green);
    }

    .header-text {
      flex: 1;
    }

    .header-text h1 {
      font-size: 22px;
      font-weight: 700;
      letter-spacing: -0.3px;
      font-family: 'Syne', sans-serif;
    }

    .header-text p {
      font-size: 13px;
      color: var(--text-secondary);
      margin-top: 2px;
      font-weight: 400;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn {
      font-family: 'Syne', sans-serif;
      font-size: 13px;
      font-weight: 600;
      padding: 9px 18px;
      border-radius: 50px;
      border: none;
      cursor: pointer;
      transition: all 0.18s ease;
      white-space: nowrap;
    }

    .btn-outline {
      background: var(--input-bg);
      color: var(--text-primary);
      border: 1.5px solid var(--border);
    }

    .btn-outline:hover {
      background: var(--border);
    }

    .btn-danger {
      background: #fdecea;
      color: var(--accent-red);
      border: 1.5px solid #f5c0c0;
    }

    .btn-danger:hover {
      background: #fad4d4;
    }

    .icon-btn {
      width: 38px;
      height: 38px;
      background: var(--input-bg);
      border: 1.5px solid var(--border);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.15s;
      color: var(--text-secondary);
    }

    .icon-btn:hover {
      background: var(--border);
    }

    /* Section */
    .section {
      background: var(--surface);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 24px 14px;
      cursor: pointer;
      user-select: none;
    }

    .section-header:hover {
      background: #f5f3ef;
    }

    .section-header-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-title {
      font-size: 14px;
      font-weight: 700;
      letter-spacing: 0.01em;
      color: var(--text-primary);
      font-family: 'Syne', sans-serif;
    }

    .section-header-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-badge {
      font-family: 'DM Sans', monospace;
      font-size: 10px;
      font-weight: 500;
      letter-spacing: 0.08em;
      color: var(--badge-text);
      background: var(--badge-bg);
      padding: 4px 10px;
      border-radius: 20px;
      border: 1px solid var(--border);
      text-transform: uppercase;
    }

    .collapse-btn {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: var(--input-bg);
      border: 1.5px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--text-secondary);
      transition: background 0.15s, transform 0.25s ease;
      flex-shrink: 0;
    }

    .section.collapsed .collapse-btn {
      transform: rotate(-90deg);
    }

    /* Collapsible list */
    .notif-list-wrapper {
      overflow: hidden;
      max-height: 1000px;
      transition: max-height 0.35s ease, opacity 0.25s ease;
      opacity: 1;
    }

    .section.collapsed .notif-list-wrapper {
      max-height: 0;
      opacity: 0;
    }

    /* Notification Items */
    .notif-list {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .notif-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 24px 14px 16px;
      cursor: pointer;
      transition: background 0.15s ease;
      border-left: 3.5px solid transparent;
      position: relative;
    }

    .notif-item.unread {
      background: var(--unread-bg);
      border-left-color: var(--unread-border);
    }

    .notif-item:hover {
      filter: brightness(0.97);
    }

    .notif-item+.notif-item {
      border-top: 1px solid var(--border);
    }

    .notif-icon {
      width: 42px;
      height: 42px;
      background: var(--input-bg);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: background 0.15s;
    }

    .notif-item.unread .notif-icon {
      background: #e8f5e9;
    }

    .notif-body {
      flex: 1;
      min-width: 0;
    }

    .notif-text {
      font-size: 13.5px;
      font-weight: 500;
      color: var(--text-primary);
      line-height: 1.4;
    }

    .notif-item:not(.unread) .notif-text {
      font-weight: 400;
      color: var(--text-secondary);
    }

    .notif-text strong {
      font-weight: 700;
      color: var(--text-primary);
    }

    .notif-meta {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-shrink: 0;
    }

    .notif-time {
      font-size: 11.5px;
      color: var(--text-muted);
      font-weight: 400;
      white-space: nowrap;
    }

    .notif-dot {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: var(--dot-read);
      flex-shrink: 0;
      transition: background 0.3s ease, transform 0.3s ease;
    }

    .notif-item.unread .notif-dot {
      background: var(--dot-unread);
      box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.18);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.18);
      }

      50% {
        box-shadow: 0 0 0 6px rgba(76, 175, 80, 0.06);
      }
    }

    /* Empty state */
    .empty-state {
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 56px 24px;
      gap: 12px;
      color: var(--text-muted);
    }

    .empty-state svg {
      opacity: 0.35;
    }

    .empty-state p {
      font-size: 14px;
      font-weight: 500;
    }

    .container.empty .section {
      display: none;
    }

    .container.empty .empty-state {
      display: flex;
    }

    .container.empty .empty-card {
      display: block;
      background: var(--surface);
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
    }

    /* Animate removal */
    .notif-item.removing {
      animation: slideOut 0.28s ease forwards;
    }

    @keyframes slideOut {
      to {
        opacity: 0;
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
        overflow: hidden;
      }
    }

    .section.hiding {
      animation: fadeOut 0.3s ease forwards;
    }

    @keyframes fadeOut {
      to {
        opacity: 0;
        transform: scale(0.98);
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
    <div class="container" id="app">

      <!-- Header -->
      <div class="header-card">
        <div class="header-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
          </svg>
        </div>
        <div class="header-text">
          <h1>Notifications</h1>
          <p id="unread-count-text">You have 4 unread notifications</p>
        </div>
        <div class="header-actions">
          <button class="btn btn-outline" onclick="markAllRead()">Mark all read</button>
          <button class="btn btn-danger" onclick="clearAll()">Clear all</button>
          <button class="icon-btn" title="Filter">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
              stroke-linecap="round" stroke-linejoin="round">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Today Section -->
      <div class="section" id="section-today">
        <div class="section-header" onclick="toggleSection('today')">
          <div class="section-header-left">
            <span class="section-title">Today</span>
          </div>
          <div class="section-header-right">
            <span class="section-badge" id="badge-today">3 Unread</span>
            <div class="collapse-btn">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9" />
              </svg>
            </div>
          </div>
        </div>
        <div class="notif-list-wrapper" id="wrapper-today">
          <div class="notif-list" id="list-today">

            <div class="notif-item unread" data-id="1" onclick="markRead(this)">
              <div class="notif-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5b8ef5" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
              </div>
              <div class="notif-body">
                <div class="notif-text">Remember to finish packing for <strong>Miami!</strong></div>
              </div>
              <div class="notif-meta">
                <span class="notif-time">2 hours ago</span>
                <span class="notif-dot"></span>
              </div>
            </div>

            <div class="notif-item unread" data-id="2" onclick="markRead(this)">
              <div class="notif-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e05c7a" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path
                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
              </div>
              <div class="notif-body">
                <div class="notif-text">Your Packmate, <strong>Ellie</strong>, liked your list — <strong>Denver
                    Trip</strong></div>
              </div>
              <div class="notif-meta">
                <span class="notif-time">4 hours ago</span>
                <span class="notif-dot"></span>
              </div>
            </div>

            <div class="notif-item unread" data-id="3" onclick="markRead(this)">
              <div class="notif-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9b6ef5" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <line x1="19" y1="8" x2="19" y2="14" />
                  <line x1="22" y1="11" x2="16" y2="11" />
                </svg>
              </div>
              <div class="notif-body">
                <div class="notif-text">You've been added as a Packmate — <strong>Denver Trip</strong></div>
              </div>
              <div class="notif-meta">
                <span class="notif-time">6 hours ago</span>
                <span class="notif-dot"></span>
              </div>
            </div>

          </div>
        </div><!-- end wrapper-today -->
      </div>

      <!-- This Week Section -->
      <div class="section" id="section-week">
        <div class="section-header" onclick="toggleSection('week')">
          <div class="section-header-left">
            <span class="section-title">This Week</span>
          </div>
          <div class="section-header-right">
            <span class="section-badge" id="badge-week">1 Unread</span>
            <div class="collapse-btn">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9" />
              </svg>
            </div>
          </div>
        </div>
        <div class="notif-list-wrapper" id="wrapper-week">
          <div class="notif-list" id="list-week">

            <div class="notif-item unread" data-id="4" onclick="markRead(this)">
              <div class="notif-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4caf50" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <line x1="8" y1="6" x2="21" y2="6" />
                  <line x1="8" y1="12" x2="21" y2="12" />
                  <line x1="8" y1="18" x2="21" y2="18" />
                  <line x1="3" y1="6" x2="3.01" y2="6" />
                  <line x1="3" y1="12" x2="3.01" y2="12" />
                  <line x1="3" y1="18" x2="3.01" y2="18" />
                </svg>
              </div>
              <div class="notif-body">
                <div class="notif-text">Congratulations on packing <strong>5 items!</strong> — Miami Trip!</div>
              </div>
              <div class="notif-meta">
                <span class="notif-time">2 days ago</span>
                <span class="notif-dot"></span>
              </div>
            </div>

            <div class="notif-item" data-id="5" onclick="markRead(this)">
              <div class="notif-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e05c7a" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path
                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
              </div>
              <div class="notif-body">
                <div class="notif-text">Your Packmate, <strong>Mark</strong>, liked your recent trip to: <strong>New
                    York</strong></div>
              </div>
              <div class="notif-meta">
                <span class="notif-time">3 days ago</span>
                <span class="notif-dot"></span>
              </div>
            </div>

            <div class="notif-item" data-id="6" onclick="markRead(this)">
              <div class="notif-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5b8ef5" stroke-width="2.2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
              </div>
              <div class="notif-body">
                <div class="notif-text">Your trip is today — <strong>New York</strong>. Double check your list!</div>
              </div>
              <div class="notif-meta">
                <span class="notif-time">5 days ago</span>
                <span class="notif-dot"></span>
              </div>
            </div>

          </div>
        </div><!-- end wrapper-week -->
      </div>

      <!-- Empty state card (hidden until all cleared) -->
      <div class="empty-card" style="display:none;" id="empty-card">
        <div class="empty-state" style="display:flex;">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
          </svg>
          <p>You're all caught up!</p>
        </div>
      </div>

    </div>

    <script>
      function updateUnreadCount() {
        const unread = document.querySelectorAll('.notif-item.unread').length;
        const total = document.querySelectorAll('.notif-item').length;
        const countText = document.getElementById('unread-count-text');

        if (total === 0) {
          countText.textContent = 'No notifications';
        } else if (unread === 0) {
          countText.textContent = 'All caught up!';
        } else {
          countText.textContent = `You have ${unread} unread notification${unread !== 1 ? 's' : ''}`;
        }
      }

      function updateSectionBadges() {
        ['today', 'week'].forEach(id => {
          const list = document.getElementById(`list-${id}`);
          const section = document.getElementById(`section-${id}`);
          const badge = document.getElementById(`badge-${id}`);
          if (!list) return;

          const totalCount = list.querySelectorAll('.notif-item').length;
          const unreadCount = list.querySelectorAll('.notif-item.unread').length;

          if (unreadCount > 0) {
            badge.textContent = `${unreadCount} Unread`;
            badge.style.background = 'var(--unread-bg)';
            badge.style.color = '#3a9a3e';
            badge.style.borderColor = '#b6e5b6';
            badge.style.display = '';
          } else {
            badge.style.display = 'none';
          }

          if (totalCount === 0) section.style.display = 'none';
          else section.style.display = '';
        });

        const todayVisible = document.getElementById('section-today').style.display !== 'none';
        const weekVisible = document.getElementById('section-week').style.display !== 'none';
        document.getElementById('empty-card').style.display = (!todayVisible && !weekVisible) ? 'block' : 'none';
      }

      function markRead(el) {
        if (el.classList.contains('unread')) {
          el.classList.remove('unread');
          updateUnreadCount();
          updateSectionBadges();
        }
      }

      function markAllRead() {
        document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
        updateUnreadCount();
        updateSectionBadges();
      }

      function clearAll() {
        const items = document.querySelectorAll('.notif-item');
        let delay = 0;
        items.forEach(item => {
          setTimeout(() => {
            item.style.transition = 'all 0.25s ease';
            item.style.opacity = '0';
            item.style.maxHeight = item.offsetHeight + 'px';
            setTimeout(() => {
              item.style.maxHeight = '0';
              item.style.padding = '0 24px 0 16px';
              item.style.overflow = 'hidden';
            }, 50);
            setTimeout(() => item.remove(), 280);
          }, delay);
          delay += 60;
        });

        setTimeout(() => {
          updateSectionBadges();
          updateUnreadCount();
        }, delay + 300);
      }

      function toggleSection(id) {
        const section = document.getElementById(`section-${id}`);
        section.classList.toggle('collapsed');
      }

      // Init
      updateSectionBadges();
      updateUnreadCount();
    </script>
  </main>
</body>

</html>

<?php $mysqli->close(); ?>