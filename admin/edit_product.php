<?php
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
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $imagePath = $product['image']; // Keep old image by default

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../Images/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $imagePath = 'Images/' . $fileName;
        }
    }

    $sql = "UPDATE products SET title = '$title', category = '$category', image = '$imagePath' WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=updated");
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
    <title>Edit Product - Grin Living</title>
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
            <h2 style="margin-bottom: 24px; border-bottom: 1px solid var(--border-light); padding-bottom: 16px;">Edit Product</h2>
            
            <form action="edit_product.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Product Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control" required>
                        <?php
                        $categories = [
                            "Cotton Fabrics", "Polyester Fabrics", "Poly Spandex Fabrics", 
                            "Rayon Fabrics", "Viscose Fabrics", "Mesh Fabrics", 
                            "Knit Fabrics", "Velvet Fabrics", "Embroidered Fabrics", "Fancy / Fashion Fabrics"
                        ];
                        foreach ($categories as $cat) {
                            $selected = ($product['category'] == $cat) ? "selected" : "";
                            echo "<option value='$cat' $selected>$cat</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Current Image</label>
                    <br>
                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" style="width: 100px; border-radius: 8px; margin-bottom: 10px;">
                </div>

                <div class="form-group">
                    <label for="image">Upload New Image (Leave empty to keep current)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Product</button>
            </form>
        </div>
    </div>
</body>
</html>
