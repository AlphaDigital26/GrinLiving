<?php
require_once 'auth.php';
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    
    // Default image path fallback
    $imagePath = "Images/placeholder.jpg";
    $imageData = null;
    $imageType = null;

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetDir = __DIR__ . '/../Images/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $uploadPath = $targetDir . $fileName;
        
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $imagePath = 'Images/' . $fileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (title, category, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $category, $imagePath);
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index.php?msg=success");
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        $stmt->close();
    }
}

// Fetch categories from database
$categories = [];
$catResult = $conn->query("SELECT name FROM categories ORDER BY name ASC");
if ($catResult && $catResult->num_rows > 0) {
    while($catRow = $catResult->fetch_assoc()) {
        $categories[] = $catRow['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Grin Living</title>
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
                <a href="index" class="active">Products</a>
                <a href="manage_categories">Categories</a>
                <a href="manage_blogs">Blogs</a>
                <a href="settings">Settings</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../products.html" target="_blank" style="color: var(--secondary-color); text-decoration: none; font-size: 14px;">View Live Website &rarr;</a>
                <a href="logout" class="btn btn-danger" style="text-align: center;">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header"> <button class="hamburger-btn" onclick="document.getElementById('admin-sidebar').classList.toggle('collapsed')">&#9776;</button> <div style="margin-left: auto; display: flex; align-items: center;">
                    <!-- Optional top right items -->
                    <span style="font-size: 14px; color: var(--text-light);">Welcome, <?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?></span>
                </div>
            </header>

            <div class="admin-container" style="flex: 1; max-width: 800px;">
                <div style="margin-bottom: 20px;">
                    <a href="index" style="text-decoration: none; color: var(--text-light); font-weight: 500;">&larr; Back to Products</a>
                </div>
        <?php if(isset($msg)) echo $msg; ?>
        
        <div class="card">
            <h2 style="margin-bottom: 24px; border-bottom: 1px solid var(--border-light); padding-bottom: 16px;">Add New Product</h2>
            
            <form action="add_product" method="POST" enctype="multipart/form-data">
                <div style="display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 24px;">
                    
                    <!-- Left Column: Text Details -->
                    <div style="flex: 1; min-width: 260px; display: flex; flex-direction: column; justify-content: center;">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="title">Product Title</label>
                            <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. 100% Cotton Velvet" style="padding: 14px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control" required style="padding: 14px; cursor: pointer;">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column: Image Details -->
                    <div style="flex: 1; min-width: 260px; background: var(--bg-color); padding: 20px; border-radius: 8px; border: 1px solid var(--border-light); display: flex; flex-direction: column; justify-content: center;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="image" style="font-weight: 500; font-size: 14px; margin-bottom: 12px; display: block;">Product Image</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*" required style="font-size: 13px; padding: 12px; cursor: pointer; background: var(--surface-white);">
                        </div>
                    </div>
                    
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px; letter-spacing: 0.02em;">Save Product</button>
            </form>
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

