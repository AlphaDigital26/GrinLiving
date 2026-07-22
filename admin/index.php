<?php
require_once 'db_connect.php';

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $msg = "<div class='alert alert-success'>Product deleted successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error deleting product: " . $conn->error . "</div>";
    }
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Grin Living</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="admin-header">
        <h1>Grin Living Admin</h1>
        <a href="../products.html" target="_blank">View Live Website &rarr;</a>
    </header>

    <div class="admin-container">
        <?php if(isset($msg)) echo $msg; ?>
        
        <div class="card">
            <div class="card-header">
                <h2>Manage Products</h2>
                <a href="add_product.php" class="btn btn-primary">+ Add New Product</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Adjust image path for display in admin (going up one dir)
                            $imgPath = '../' . $row['image'];
                            echo "<tr>";
                            echo "<td>#" . $row['id'] . "</td>";
                            echo "<td><img src='" . htmlspecialchars($imgPath) . "' class='product-thumb' onerror=\"this.src='https://via.placeholder.com/60'\"></td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td><span style='background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;'>" . htmlspecialchars($row['category']) . "</span></td>";
                            echo "<td>
                                    <a href='edit_product.php?id=" . $row['id'] . "' class='btn btn-edit'>Edit</a>
                                    <a href='index.php?delete_id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>No products found. Add some!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
