<?php
require_once 'auth.php';
require_once 'db_connect.php';

$error = '';
$msg = '';

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Invalid CSRF token.";
    } else {
        $admin_id = $_SESSION['admin_id'];
        
        if (isset($_POST['update_username'])) {
            $new_username = trim($_POST['username']);
            if (empty($new_username)) {
                $error = "Username cannot be empty.";
            } else {
                $update = $conn->prepare("UPDATE admins SET username = ? WHERE id = ?");
                $update->bind_param("si", $new_username, $admin_id);
                if ($update->execute()) {
                    $_SESSION['admin_username'] = $new_username;
                    $msg = "<div class='alert alert-success'>Username updated successfully!</div>";
                } else {
                    $error = "Failed to update username. It might already be taken.";
                }
                $update->close();
            }
        } elseif (isset($_POST['update_password'])) {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error = "All fields are required.";
            } elseif ($new_password !== $confirm_password) {
                $error = "New passwords do not match.";
            } elseif (strlen($new_password) < 8) {
                $error = "New password must be at least 8 characters long.";
            } else {
                // Verify current password
                $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $stmt->bind_result($hashed_password);
                if ($stmt->fetch()) {
                    if (password_verify($current_password, $hashed_password)) {
                        $stmt->close();
                        
                        // Hash new password and update
                        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                        $update = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                        $update->bind_param("si", $new_hashed, $admin_id);
                        if ($update->execute()) {
                            $msg = "<div class='alert alert-success'>Password updated successfully!</div>";
                        } else {
                            $error = "Failed to update password. Please try again.";
                        }
                        $update->close();
                    } else {
                        $error = "Current password is incorrect.";
                        $stmt->close();
                    }
                } else {
                    $error = "Admin record not found.";
                    $stmt->close();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Grin Living Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="admin-sidebar">
            <div class="sidebar-header">
                <h1>Grin Living Admin</h1> <button class="sidebar-toggle-btn" onclick="document.getElementById('admin-sidebar').classList.toggle('collapsed')">&#10094;</button>
            </div>
            <nav class="sidebar-nav">
                <a href="index">Products</a>
                <a href="manage_categories">Categories</a>
                <a href="manage_blogs">Blogs</a>
                <a href="settings" class="active">Settings</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../products.html" target="_blank" style="color: var(--secondary-color); text-decoration: none; font-size: 14px;">View Live Website &rarr;</a>
                <a href="logout" class="btn btn-danger" style="text-align: center;">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header"> <button class="hamburger-btn" onclick="document.getElementById('admin-sidebar').classList.toggle('collapsed')">&#9776;</button> <div style="margin-left: auto; display: flex; align-items: center;">
                    <span style="font-size: 14px; color: var(--text-light);">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                </div>
            </header>

            <div class="admin-container" style="flex: 1; max-width: 600px;">
                <div class="card">
                    <div class="card-header">
                        <h2>Settings</h2>
                    </div>

                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if(!empty($msg)): ?>
                        <?php echo $msg; ?>
                    <?php endif; ?>

                    <div class="tabs-container" style="display: flex; gap: 30px; border-bottom: 1px solid var(--border-light); margin-bottom: 30px;">
                        <div id="tab-username" class="settings-tab active" onclick="switchTab('username')" style="padding-bottom: 10px; cursor: pointer; font-weight: 500; color: var(--primary-color); border-bottom: 2px solid var(--primary-color); transition: all 0.2s;">
                            Update Username
                        </div>
                        <div id="tab-password" class="settings-tab" onclick="switchTab('password')" style="padding-bottom: 10px; cursor: pointer; font-weight: 500; color: var(--text-light); border-bottom: 2px solid transparent; transition: all 0.2s;">
                            Update Password
                        </div>
                    </div>

                    <div id="form-username">
                        <form action="settings" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="update_username" value="1">
                            
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($_SESSION['admin_username'] ?? ''); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Username</button>
                        </form>
                    </div>

                    <div id="form-password" style="display: none;">
                        <form action="settings" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="update_password" value="1">
                            
                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Current Password</label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="current_password" class="form-control" required>
                                    <button type="button" class="toggle-password-btn" aria-label="Show password" title="Show password" onclick="togglePasswordVisibility(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>New Password (min 8 characters)</label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="new_password" class="form-control" required minlength="8">
                                    <button type="button" class="toggle-password-btn" aria-label="Show password" title="Show password" onclick="togglePasswordVisibility(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label>Confirm New Password</label>
                                <div class="password-input-wrapper">
                                    <input type="password" name="confirm_password" class="form-control" required minlength="8">
                                    <button type="button" class="toggle-password-btn" aria-label="Show password" title="Show password" onclick="togglePasswordVisibility(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Password</button>
                        </form>
                    </div>

                    <script>
                        function togglePasswordVisibility(btn) {
                            const wrapper = btn.closest('.password-input-wrapper');
                            const input = wrapper.querySelector('input');
                            const isPassword = input.type === 'password';
                            input.type = isPassword ? 'text' : 'password';
                            
                            if (isPassword) {
                                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
                                btn.setAttribute('aria-label', 'Hide password');
                                btn.setAttribute('title', 'Hide password');
                            } else {
                                btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
                                btn.setAttribute('aria-label', 'Show password');
                                btn.setAttribute('title', 'Show password');
                            }
                        }

                        // Maintain tab state after form submission
                        const currentTab = '<?php echo isset($_POST['update_password']) ? 'password' : 'username'; ?>';
                        
                        function switchTab(tab) {
                            const tabUsername = document.getElementById('tab-username');
                            const tabPassword = document.getElementById('tab-password');
                            const formUsername = document.getElementById('form-username');
                            const formPassword = document.getElementById('form-password');

                            if (tab === 'username') {
                                tabUsername.style.color = 'var(--primary-color)';
                                tabUsername.style.borderBottom = '2px solid var(--primary-color)';
                                tabPassword.style.color = 'var(--text-light)';
                                tabPassword.style.borderBottom = '2px solid transparent';
                                
                                formUsername.style.display = 'block';
                                formPassword.style.display = 'none';
                            } else {
                                tabPassword.style.color = 'var(--primary-color)';
                                tabPassword.style.borderBottom = '2px solid var(--primary-color)';
                                tabUsername.style.color = 'var(--text-light)';
                                tabUsername.style.borderBottom = '2px solid transparent';
                                
                                formPassword.style.display = 'block';
                                formUsername.style.display = 'none';
                            }
                        }

                        // Set initial tab on load
                        switchTab(currentTab);
                    </script>
                </div>
            </div>
        </main>
    </div>
<script>
let lastWidth = window.innerWidth;
function checkSidebar() {
    const sb = document.getElementById('admin-sidebar');
    if(sb) {
        const currentWidth = window.innerWidth;
        if(lastWidth > 768 && currentWidth <= 768) {
            sb.classList.add('collapsed');
        } else if(lastWidth <= 768 && currentWidth > 768) {
            sb.classList.remove('collapsed');
        }
        lastWidth = currentWidth;
    }
}
window.addEventListener('resize', checkSidebar);
if(window.innerWidth <= 768 && document.getElementById('admin-sidebar')) {
    document.getElementById('admin-sidebar').classList.add('collapsed');
}
</script>
</body>
</html>
