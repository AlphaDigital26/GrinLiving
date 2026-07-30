<?php
require_once 'auth.php';
require_once 'db_connect.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = null;

if ($id > 0) {
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
}

if (!$product) {
    die("Product not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $imagePath = $product['image']; // Keep old image by default
    $imageData = null;
    $imageType = null;
    $hasNewImage = false;

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
            $hasNewImage = true;
        }
    } elseif (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        $imagePath = 'Images/placeholder.jpg';
        $hasNewImage = true;
    }

    if ($hasNewImage) {
        $stmt = $conn->prepare("UPDATE products SET title = ?, category = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $category, $imagePath, $id);
    } else {
        $stmt = $conn->prepare("UPDATE products SET title = ?, category = ? WHERE id = ?");
        $stmt->bind_param("ssi", $title, $category, $id);
    }
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index?msg=updated");
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        $stmt->close();
    }
}

// Fetch categories from database
$dbCategories = [];
$catResult = $conn->query("SELECT name FROM categories ORDER BY name ASC");
if ($catResult && $catResult->num_rows > 0) {
    while($catRow = $catResult->fetch_assoc()) {
        $dbCategories[] = $catRow['name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Grin Living</title>
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
            <h2 style="margin-bottom: 24px; border-bottom: 1px solid var(--border-light); padding-bottom: 16px;">Edit Product</h2>
            
            <form action="edit_product?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div style="display: flex; flex-wrap: wrap; gap: 24px; margin-bottom: 24px;">
                    
                    <!-- Left Column: Text Details -->
                    <div style="flex: 1; min-width: 260px; display: flex; flex-direction: column; justify-content: center;">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="title">Product Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($product['title']); ?>" required style="padding: 14px;">
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control" required style="padding: 14px; cursor: pointer;">
                                <?php
                                foreach ($dbCategories as $cat) {
                                    $selected = ($product['category'] == $cat) ? "selected" : "";
                                    echo "<option value='" . htmlspecialchars($cat) . "' $selected>" . htmlspecialchars($cat) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Right Column: Image Details -->
                    <div style="flex: 1; min-width: 260px; background: var(--bg-color); padding: 20px; border-radius: 8px; border: 1px solid var(--border-light);">
                        <label style="display: block; font-weight: 500; margin-bottom: 12px; font-size: 14px;">Current Image Preview</label>
                        
                        <div style="position: relative; height: 140px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; background: var(--surface-white); border-radius: 6px; overflow: hidden; border: 1px dashed #cbd5e1;" id="image-preview-container">
                            <img src="../<?php echo htmlspecialchars($product['image']); ?>" id="current-image" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            
                            <?php if ($product['image'] !== 'Images/placeholder.jpg' && !empty($product['image'])): ?>
                            <button type="button" onclick="removeImage(event)" style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.6); color: white; border: none; border-radius: 50%; width: 26px; height: 26px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; transition: background 0.2s;" onmouseover="this.style.background='rgba(220,38,38,0.9)'" onmouseout="this.style.background='rgba(0,0,0,0.6)'" title="Remove Image">&times;</button>
                            <?php endif; ?>
                        </div>
                        
                        <input type="hidden" name="remove_image" id="remove_image_input" value="0">
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="image" style="font-size: 13px; color: var(--text-light);">Upload New Image (Optional)</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*" style="font-size: 13px; padding: 8px; cursor: pointer; background: var(--surface-white);">
                        </div>
                    </div>
                    
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px; letter-spacing: 0.02em;">Update Product</button>
            </form>
        </div>
    </div>
        </main>
    </div>

    <script>
        function removeImage(event) {
            // Hide the image
            document.getElementById('current-image').style.display = 'none';
            // Set the hidden input so PHP knows to remove it
            document.getElementById('remove_image_input').value = '1';
            // Hide the button itself
            event.target.style.display = 'none';
            
            // Add a placeholder text to indicate it's removed
            const container = document.getElementById('image-preview-container');
            const text = document.createElement('span');
            text.style.color = 'var(--text-light)';
            text.style.fontSize = '14px';
            text.innerText = 'Image Removed';
            container.appendChild(text);
        }
    </script>
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

