<?php
require_once 'auth.php';
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    
    // Default image path fallback
    $imagePath = "Images/placeholder.jpg";

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../Images/';
        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            // Store relative path in DB (e.g. Images/filename.jpg)
            $imagePath = 'Images/' . $fileName;
        }
    }

    $sql = "INSERT INTO products (title, category, image) VALUES ('$title', '$category', '$imagePath')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=success");
        exit();
    } else {
        $msg = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
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
    <title>Add Product - Grin Living</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1>Grin Living Admin</h1>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php" class="active">Products</a>
                <a href="manage_categories.php">Categories</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../products.html" target="_blank" style="color: var(--secondary-color); text-decoration: none; font-size: 14px;">View Live Website &rarr;</a>
                <a href="logout.php" class="btn btn-danger" style="text-align: center;">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div>
                    <!-- Optional top right items -->
                    <span style="font-size: 14px; color: var(--text-light);">Welcome, Admin</span>
                </div>
            </header>

            <div class="admin-container" style="flex: 1; max-width: 600px;">
                <div style="margin-bottom: 20px;">
                    <a href="index.php" style="text-decoration: none; color: var(--text-light); font-weight: 500;">&larr; Back to Products</a>
                </div>
        <?php if(isset($msg)) echo $msg; ?>
        
        <div class="card">
            <h2 style="margin-bottom: 24px; border-bottom: 1px solid var(--border-light); padding-bottom: 16px;">Add New Product</h2>
            
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Product Title</label>
                    <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. 100% Cotton Velvet">
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control" required>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Product</button>
            </form>
        </div>
    </div>
        </main>
    </div>
</body>
</html>
