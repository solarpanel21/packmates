<?php
session_start();
include("connectionInclude.php");
include("checkNotifications.php");

if (isset($_SESSION['logged_in'])) {
    header("Location: home.php");
    checkTripNotifications($mysqli);
    exit();
}

$error = null;

if (isset($_POST['submit']) && isset($_POST['email']) && isset($_POST['password'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = md5($_POST['password']);
    if ($email === false) {
        $error = "Incorrect email or password";
    } else {
        $stmt = $mysqli->prepare("SELECT userid, username, password, email, pfpurl FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_object();
        if ($row && hash_equals($row->password, $password)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['logged_in_user'] = $row->username;
            $_SESSION['logged_in_user_id'] = $row->userid;
            $_SESSION['logged_in_user_fullname'] = $row->username;
            checkTripNotifications($mysqli);

            header("Location: home.php");
            exit();
        }
        $error = "Incorrect email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PackMates | Login</title>
  <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="img/favicon-16x16.png" sizes="16x16" />
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

    /* Subtle background decoration */
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

    /* Card */
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
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Logo */
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

    /* Heading */
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

    /* Form */
    .login-form {
      display: flex;
      flex-direction: column;
      gap: 0;
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

    .field-wrap .field-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 15px;
      color: #888;
      pointer-events: none;
    }

    .login-form input[type="email"],
    .login-form input[type="password"],
    .login-form input[type="text"] {
      width: 100%;
      padding: 13px 14px 13px 14px;
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

    /* Password toggle */
    .toggle-pw {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #888;
      font-size: 14px;
      padding: 0;
    }

    /* Remember + Forgot row */
    .row-extras {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 22px;
    }

    .remember {
      display: flex;
      align-items: center;
      gap: 7px;
      color: rgba(255, 255, 255, 0.75);
      font-size: 13px;
      cursor: pointer;
    }

    .remember input[type="checkbox"] {
      width: 15px;
      height: 15px;
      accent-color: var(--green);
      cursor: pointer;
    }

    .forgot-link {
      font-size: 13px;
      color: var(--green);
      text-decoration: none;
      font-weight: 600;
      transition: opacity 0.2s;
    }

    .forgot-link:hover {
      opacity: 0.8;
    }

    /* Sign In button */
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
      margin-bottom: 22px;
    }

    .login-btn:hover {
      background: var(--green-dark);
      transform: translateY(-1px);
    }

    .login-btn:active {
      transform: translateY(0);
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      color: rgba(255, 255, 255, 0.4);
      font-size: 12.5px;
      margin-bottom: 18px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(255, 255, 255, 0.18);
    }

    /* Social buttons */
    .socials {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 26px;
    }

    .social-btn {
      flex: 1;
      max-width: 120px;
      padding: 10px 12px;
      background: var(--white-glass);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      color: white;
      font-family: 'Montserrat', Arial, sans-serif;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      transition: background 0.2s;
    }

    .social-btn:hover {
      background: var(--white-glass-hover);
    }

    .social-icon {
      font-size: 16px;
      font-weight: 900;
    }

    .google-color { color: #ea4335; }
    .fb-color { color: #4267B2; }

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

    /* Switch */
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

    .switch a:hover {
      opacity: 0.8;
    }
  </style>
</head>
<body>

  <div class="login-card">

    <div class="logo-wrap">
      <img src="img/appIcon.png" class="logo-badge" alt="PackMates logo">
      <span class="logo-name">PackMates</span>
    </div>

    <h1>Welcome Back!</h1>
    <p class="subtitle">Sign in to continue your adventure</p>

    <?php if ($error): ?>
      <div class="msg error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form class="login-form" method="post" action="">

      <label class="field-label" for="email">Email</label>
      <div class="field-wrap">
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>

      <label class="field-label" for="password">Password</label>
      <div class="field-wrap">
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        <button type="button" class="toggle-pw" onclick="togglePassword()">Show</button>
      </div>

      <div class="row-extras">
        <label class="remember">
          <input type="checkbox" id="remember">
          <span>Remember me</span>
        </label>
        <a href="#" class="forgot-link">Forgot password?</a>
      </div>

      <input name="submit" id="submit" type="submit" value="Sign In" class="login-btn">

    </form>


    <div class="switch">
      Don't have an account? <a href="signup.php">Sign up</a>
    </div>

  </div>

  <script>
    function togglePassword() {
      const pw = document.getElementById('password');
      const btn = document.querySelector('.toggle-pw');
      const showing = pw.type === 'text';
      pw.type = showing ? 'password' : 'text';
      btn.textContent = showing ? 'Show' : 'Hide';
    }
  </script>

</body>
</html>
<?php $mysqli->close(); ?>
