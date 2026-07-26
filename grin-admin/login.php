<?php
// Strict session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

session_start();
require_once 'db_connect.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';

function check_brute_force($ip, $conn) {
    $time_ago = date('Y-m-d H:i:s', strtotime('-15 minutes'));
    $stmt = $conn->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > ?");
    $stmt->bind_param("ss", $ip, $time_ago);
    $stmt->execute();
    $stmt->bind_result($attempts);
    $stmt->fetch();
    $stmt->close();
    return $attempts >= 5;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token.";
    } elseif (check_brute_force($ip_address, $conn)) {
        $error = "Too many failed login attempts. Please try again after 15 minutes.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $id;
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                    $_SESSION['last_regeneration'] = time();

                    $clear = $conn->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                    $clear->bind_param("s", $ip_address);
                    $clear->execute();

                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid username or password.";
                    $log = $conn->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)");
                    $log->bind_param("s", $ip_address);
                    $log->execute();
                }
            } else {
                $error = "Invalid username or password.";
                $log = $conn->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)");
                $log->bind_param("s", $ip_address);
                $log->execute();
            }
            $stmt->close();
        } else {
            $error = "Database error. Could not prepare statement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Grin Living</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8fafc;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .login-card h1 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.5rem;
            color: #1e293b;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Grin Living Admin</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
    </div>
</body>
</html>
