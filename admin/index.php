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
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th style="width: 150px;">Category</th>
                        <th>Featured</th>
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
                            echo "<td><img src='" . htmlspecialchars($imgPath) . "' class='product-thumb' onerror=\"this.onerror=null; this.src='https://placehold.co/60x60/e2e8f0/64748b?text=No+Image'\"></td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td><span style='background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;'>" . htmlspecialchars($row['category']) . "</span></td>";
                            $checked = $row['is_featured'] ? 'checked' : '';
                            echo "<td>
                                    <label class='switch' style='position: relative; display: inline-block; width: 44px; height: 24px;'>
                                      <input type='checkbox' class='feature-toggle' data-id='" . $row['id'] . "' $checked style='opacity: 0; width: 0; height: 0;'>
                                      <span class='slider round' style='position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; transition: .4s; border-radius: 24px;'></span>
                                    </label>
                                  </td>";
                            echo "<td>
                                    <a href='edit_product.php?id=" . $row['id'] . "' class='btn btn-edit'>Edit</a>
                                    <a href='index.php?delete_id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>No products found. Add some!</td></tr>";
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
    
    <div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>
    
    <style>
        .slider {
            background-color: #ccc;
        }
        .switch input:checked + .slider {
            background-color: var(--success-color, #10B981);
        }
        .switch input:checked + .slider:before {
            transform: translateX(20px);
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
    </style>
    <script>
        function showToast(message, type = 'error') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.style.padding = '12px 20px';
            toast.style.borderRadius = '6px';
            toast.style.color = '#fff';
            toast.style.fontWeight = '500';
            toast.style.fontSize = '14px';
            toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            toast.style.transform = 'translateY(20px)';
            toast.style.backgroundColor = type === 'error' ? 'var(--danger-color, #EF4444)' : 'var(--success-color, #10B981)';
            toast.innerText = message;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            }, 10);
            
            // Remove after 3s
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        document.querySelectorAll('.feature-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const productId = this.getAttribute('data-id');
                const isFeatured = this.checked ? 1 : 0;
                
                fetch('toggle_featured.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${productId}&is_featured=${isFeatured}`
                })
                .then(response => response.json())
                .then(data => {
                    if(!data.success) {
                        showToast(data.error || 'Error updating featured status');
                        this.checked = !isFeatured; // Revert
                    } else {
                        if (isFeatured) {
                           showToast('Product featured successfully', 'success');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Network error while updating.');
                    this.checked = !isFeatured; // Revert
                });
            });
        });
    </script>
</body>
</html>
