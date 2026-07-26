<?php
// Set strict session cookie parameters before starting the session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// ini_set('session.cookie_secure', 1); // Enable if using HTTPS

session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Session Hijacking Protection: Bind session to IP and User-Agent
$current_ip = $_SERVER['REMOTE_ADDR'];
$current_user_agent = $_SERVER['HTTP_USER_AGENT'];

if (!isset($_SESSION['ip_address'])) {
    $_SESSION['ip_address'] = $current_ip;
} elseif ($_SESSION['ip_address'] !== $current_ip) {
    // IP changed, potentially hijacked
    session_destroy();
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $current_user_agent;
} elseif ($_SESSION['user_agent'] !== $current_user_agent) {
    // User agent changed, potentially hijacked
    session_destroy();
    header("Location: login.php");
    exit();
}

// Optional: Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
?>
