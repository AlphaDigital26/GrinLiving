<?php
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
    <header class="admin-header">
        <h1>Grin Living Admin</h1>
        <a href="index.php">&larr; Back to Dashboard</a>
    </header>

    <div class="admin-container" style="max-width: 600px;">
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
                        <option value="Cotton Fabrics">Cotton Fabrics</option>
                        <option value="Polyester Fabrics">Polyester Fabrics</option>
                        <option value="Poly Spandex Fabrics">Poly Spandex Fabrics</option>
                        <option value="Rayon Fabrics">Rayon Fabrics</option>
                        <option value="Viscose Fabrics">Viscose Fabrics</option>
                        <option value="Mesh Fabrics">Mesh Fabrics</option>
                        <option value="Knit Fabrics">Knit Fabrics</option>
                        <option value="Velvet Fabrics">Velvet Fabrics</option>
                        <option value="Embroidered Fabrics">Embroidered Fabrics</option>
                        <option value="Fancy / Fashion Fabrics">Fancy / Fashion Fabrics</option>
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
</body>
</html>
