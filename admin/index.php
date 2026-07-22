<?php
require_once 'auth.php';
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

// Search and Pagination setup
$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchQuery = '';
if (!empty($search)) {
    $safeSearch = $conn->real_escape_string($search);
    $searchQuery = " WHERE title LIKE '%$safeSearch%' OR category LIKE '%$safeSearch%'";
}

// Get total products for pagination calculation
$total_result = $conn->query("SELECT COUNT(id) AS total FROM products" . $searchQuery);
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $limit);

// Fetch products for current page
$sql = "SELECT * FROM products" . $searchQuery . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Grin Living</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
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

            <div class="admin-container" style="flex: 1;">
        <?php if(isset($msg)) echo $msg; ?>
        
        <div class="card">
            <div class="card-header" style="flex-wrap: wrap; gap: 15px;">
                <h2>Manage Products</h2>
                
                <form action="index.php" method="GET" style="display: flex; gap: 10px; flex: 1; max-width: 400px; margin-left: auto;">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if(!empty($search)): ?>
                        <a href="index.php" class="btn" style="background: #e2e8f0; color: #1e293b;">Clear</a>
                    <?php endif; ?>
                </form>

                <a href="add_product.php" class="btn btn-primary" style="white-space: nowrap;">+ Add New</a>
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

            <?php if ($total_products > 0): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; margin-top: 16px; flex-wrap: wrap; gap: 16px;">
                <div style="font-size: 14px; color: var(--text-light);">
                    <?php 
                    $startItem = ($page - 1) * $limit + 1;
                    $endItem = min($page * $limit, $total_products);
                    echo "Showing $startItem to $endItem of $total_products results"; 
                    ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 0; padding-bottom: 0;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $searchParam; ?>">&laquo; Prev</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?><?php echo $searchParam; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $searchParam; ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        </div>
        </main>
    </div>
</body>
</html>
