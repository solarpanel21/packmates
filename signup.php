<?php
session_start();
include("connectionInclude.php");
include("checkNotifications.php");

if (isset($_SESSION['logged_in'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_account'])) {
    $username = $mysqli->real_escape_string($_POST['username']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = md5($_POST['password']);
    $password2 = md5($_POST['password2']);

    if ($password !== $password2) {
        $error = "Passwords don't match";
    } else {
        $check = $mysqli->query("SELECT userid FROM users WHERE username = '$username' OR email = '$email'");
        if ($check->num_rows > 0) {
            $error = "Username or email already taken";
        } else {
            $insert_query = "INSERT INTO users (username, password, email) VALUES ('$username', '$password', '$email')";
            $mysqli->query($insert_query);
            if ($mysqli->error) {
                $error = "Error creating account: " . $mysqli->error;
            } else {
              //go home if already logged in
                $new_user = $mysqli->query("SELECT userid, username FROM users WHERE email = '$email'")->fetch_object();
                $_SESSION['logged_in'] = true;
                $_SESSION['logged_in_user'] = $new_user->username;
                $_SESSION['logged_in_user_id'] = $new_user->userid;
                $_SESSION['logged_in_user_fullname'] = $new_user->username;
                checkTripNotifications($mysqli);
                header("Location: home.php");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PackMates | Sign Up</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
    <style>
      @font-face {
        font-family: 'Blauer Nue';
        src: url('font/Blauer-Nue-Medium-iF6626350c78103.otf') format('opentype');
        font-weight: 500;
      }
      @font-face {
        font-family: 'Blauer Nue';
        src: url('font/Blauer-Nue-Heavy-iF6626350c62afa.otf') format('opentype');
        font-weight: 700;
      }
      @font-face {
        font-family: 'Blauer Nue';
        src: url('font/Blauer-Nue-Light-iF6626350c6db36.otf') format('opentype');
        font-weight: 300;
      }
      @font-face {
        font-family: 'Montserrat';
        src: url('font/Montserrat-Regular.ttf') format('truetype');
        font-weight: 400;
      }
      @font-face {
        font-family: 'Montserrat';
        src: url('font/Montserrat-Medium.ttf') format('truetype');
        font-weight: 500;
      }
      @font-face {
        font-family: 'Montserrat';
        src: url('font/Montserrat-ExtraBold.ttf') format('truetype');
        font-weight: 700;
      }

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      :root {
        --green: #5f9d30;
        --green-dark: #4d8225;
        --teal-dark: #0b2f3f;
        --teal-mid: #1b6f8a;
        --white-glass: rgba(255, 255, 255, 0.08);
        --white-glass-hover: rgba(255, 255, 255, 0.13);
        --input-bg: rgba(255, 255, 255, 0.92);
      }

      body {
        min-height: 100vh;
        font-family: 'Montserrat', Arial, sans-serif;
        background: linear-gradient(160deg, #1b6f8a 0%, #0e4a60 45%, #0b2f3f 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
      }

      body::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: rgba(95, 157, 48, 0.12);
        pointer-events: none;
      }

      body::after {
        content: '';
        position: absolute;
        bottom: -100px;
        left: -80px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: rgba(27, 111, 138, 0.2);
        pointer-events: none;
      }

      .login-card {
        width: 100%;
        max-width: 420px;
        padding: 44px 36px 36px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 24px;
        text-align: center;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.3);
        animation: fadeUp 0.5s ease both;
      }

      @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
      }

      .logo-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 24px;
      }

      .logo-badge {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        object-fit: contain;
        flex-shrink: 0;
      }

      .logo-name {
        font-size: 22px;
        font-family: 'Blauer Nue', sans-serif;
        font-weight: 500;
        color: white;
        letter-spacing: 0.5px;
      }

      .login-card h1 {
        color: white;
        font-family: 'Blauer Nue', sans-serif;
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 6px;
      }

      .subtitle {
        color: rgba(255, 255, 255, 0.55);
        font-size: 13.5px;
        margin-bottom: 30px;
      }

      .login-form {
        display: flex;
        flex-direction: column;
      }

      .field-label {
        display: block;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 6px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
      }

      .field-wrap {
        position: relative;
        margin-bottom: 16px;
      }

      .login-form input[type="email"],
      .login-form input[type="password"],
      .login-form input[type="text"] {
        width: 100%;
        padding: 13px 14px;
        border-radius: 12px;
        border: 1.5px solid rgba(255, 255, 255, 0.15);
        background: var(--input-bg);
        font-family: 'Montserrat', Arial, sans-serif;
        font-size: 14px;
        color: #1a1a1a;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
      }

      .login-form input:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(95, 157, 48, 0.18);
      }

      .login-form input::placeholder {
        color: #aab5bc;
      }

      /* input with show/hide button needs right padding */
      .login-form input.has-toggle {
        padding-right: 60px;
      }

      .toggle-pw {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #888;
        font-size: 13px;
        font-family: 'Montserrat', Arial, sans-serif;
        font-weight: 600;
        padding: 0;
      }

      /* Password strength bar */
      .strength-bar-wrap {
        display: flex;
        gap: 5px;
        margin-top: -8px;
        margin-bottom: 16px;
      }

      .strength-bar-wrap span {
        flex: 1;
        height: 4px;
        border-radius: 4px;
        background: rgba(255,255,255,0.15);
        transition: background 0.3s;
      }

      .strength-label {
        text-align: left;
        font-size: 11px;
        color: rgba(255,255,255,0.5);
        margin-top: -12px;
        margin-bottom: 14px;
      }

      /* Error / success message */
      .msg {
        font-size: 13px;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 14px;
        display: none;
        text-align: left;
      }

      .msg.error   { background: rgba(220,50,50,0.18); color: #ff8080; display: block; }
      .msg.success { background: rgba(95,157,48,0.2);  color: #a0e060; display: block; }

      .login-btn {
        width: 100%;
        padding: 14px;
        background: var(--green);
        border: none;
        border-radius: 12px;
        font-family: 'Montserrat', Arial, sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: white;
        cursor: pointer;
        letter-spacing: 0.3px;
        transition: background 0.2s, transform 0.15s;
        margin-bottom: 26px;
        margin-top: 6px;
      }

      .login-btn:hover  { background: var(--green-dark); transform: translateY(-1px); }
      .login-btn:active { transform: translateY(0); }

      .switch {
        font-size: 13.5px;
        color: rgba(255, 255, 255, 0.65);
      }

      .switch a {
        color: var(--green);
        text-decoration: none;
        font-weight: 700;
        transition: opacity 0.2s;
      }

      .switch a:hover { opacity: 0.8; }
  </style>
</head>
<body>
  <div class="login-card">

    <div class="logo-wrap">
      <img src="img/appIcon.png" class="logo-badge" alt="PackMates logo">
      <span class="logo-name">PackMates</span>
    </div>

    <h1>Create Account</h1>
    <p class="subtitle">Join PackMates and start exploring</p>

    <?php if (isset($error)): ?>
      <div class="msg error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form class="login-form" method="post" action="">

      <label class="field-label" for="username">Username</label>
      <div class="field-wrap">
        <input type="text" id="username" name="username" placeholder="Enter your username" required>
      </div>

      <label class="field-label" for="email">Email</label>
      <div class="field-wrap">
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <label class="field-label" for="password">Password</label>
      <div class="field-wrap">
        <input type="password" id="password" name="password" placeholder="Create a password" required
               class="has-toggle" oninput="checkStrength(this.value)">
        <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">Show</button>
      </div>

      <div class="strength-bar-wrap">
        <span id="bar1"></span><span id="bar2"></span>
        <span id="bar3"></span><span id="bar4"></span>
      </div>
      <div class="strength-label" id="strength-label"></div>

      <label class="field-label" for="confirm">Confirm Password</label>
      <div class="field-wrap">
        <input type="password" id="confirm" name="password2" placeholder="Repeat your password" required class="has-toggle">
        <button type="button" class="toggle-pw" onclick="togglePassword('confirm', this)">Show</button>
      </div>

      <button type="submit" class="login-btn" name="add_account" value="true">Create Account</button>
    </form>

    <div class="switch">
      Already have an account? <a href="welcome.php">Sign in</a>
    </div>
  </div>

  <script>
    function togglePassword(id, btn) {
      const input = document.getElementById(id);
      const showing = input.type === 'text';
      input.type = showing ? 'password' : 'text';
      btn.textContent = showing ? 'Show' : 'Hide';
    }

    function checkStrength(val) {
      const bars = [1,2,3,4].map(i => document.getElementById('bar' + i));
      const label = document.getElementById('strength-label');
      const colors = ['#e74c3c','#e67e22','#f1c40f','#5f9d30'];
      const labels = ['Weak','Fair','Good','Strong'];
      let score = 0;
      if (val.length >= 8)           score++;
      if (/[A-Z]/.test(val))         score++;
      if (/[0-9]/.test(val))         score++;
      if (/[^A-Za-z0-9]/.test(val))  score++;
      bars.forEach((b, i) => {
        b.style.background = i < score ? colors[score - 1] : 'rgba(255,255,255,0.15)';
      });
      label.textContent = val.length ? labels[score - 1] || '' : '';
    }
  </script>

</body>
</html>
<?php $mysqli->close(); ?>