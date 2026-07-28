<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Handle Bulk Actions
$toast_msg = '';
$toast_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action']) && !empty($_POST['product_ids'])) {
    $action = $_POST['bulk_action'];
    $ids = array_map('intval', $_POST['product_ids']);
    $ids_string = implode(',', $ids);
    
    if ($action === 'delete') {
        $sql = "DELETE FROM products WHERE id IN ($ids_string)";
        if ($conn->query($sql) === TRUE) {
            $toast_msg = "Selected products deleted successfully!";
            $toast_type = "success";
        } else {
            $toast_msg = "Error deleting products: " . $conn->error;
            $toast_type = "error";
        }
    } elseif ($action === 'feature') {
        $sql = "UPDATE products SET is_featured = 1 WHERE id IN ($ids_string)";
        if ($conn->query($sql) === TRUE) {
            $toast_msg = "Selected products featured!";
            $toast_type = "success";
        }
    } elseif ($action === 'unfeature') {
        $sql = "UPDATE products SET is_featured = 0 WHERE id IN ($ids_string)";
        if ($conn->query($sql) === TRUE) {
            $toast_msg = "Selected products unfeatured!";
            $toast_type = "success";
        }
    }
}

// Handle Delete Request
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $toast_msg = "Product deleted successfully!";
        $toast_type = "success";
    } else {
        $toast_msg = "Error deleting product: " . $conn->error;
        $toast_type = "error";
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
$sql = "SELECT id, title, category, image, is_featured FROM products" . $searchQuery . " ORDER BY is_featured DESC, id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Grin Living</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
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

            <div class="admin-container" style="flex: 1;">
        
        <div class="card">
            <div class="card-header" style="flex-wrap: wrap; gap: 15px;">
                <h2>Manage Products</h2>
                
                <form action="index" method="GET" style="display: flex; gap: 10px; flex: 1; max-width: 400px; margin-left: auto;">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if(!empty($search)): ?>
                        <a href="index" class="btn" style="background: #e2e8f0; color: #1e293b;">Clear</a>
                    <?php endif; ?>
                </form>

                <a href="add_product.php" class="btn btn-primary" style="white-space: nowrap;">+ Add New</a>
            </div>

            <form method="POST" action="index" id="bulk-form">
                <div id="bulk-actions-container" style="display: none; padding: 12px 20px; background: var(--bg-color); border: 1px solid var(--border-light); margin-bottom: 20px; border-radius: 8px;">
                    <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <button type="button" class="btn" style="background: white; border: 1px solid var(--border-light); color: var(--text-dark); display: flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onclick="document.getElementById('bulk-actions-menu').style.display = document.getElementById('bulk-actions-menu').style.display === 'none' ? 'flex' : 'none';">
                            <span style="font-size: 16px; font-weight: bold; transform: rotate(90deg); display: inline-block;">&#8230;</span> Bulk actions
                        </button>
                        <div id="selected-count" style="font-size: 14px; color: var(--text-dark); font-weight: 500;">0 records selected</div>
                        
                        <div id="bulk-actions-menu" style="display: none; align-items: center; gap: 10px; margin-left: auto;">
                            <select id="bulk_action_select" name="bulk_action" class="form-control" style="width: auto; margin-bottom: 0; padding: 6px 12px;">
                                <option value="">Select Action</option>
                                <option value="delete">Delete</option>
                                <option value="feature">Feature</option>
                                <option value="unfeature">Unfeature</option>
                            </select>
                            <button type="button" class="btn btn-primary" style="padding: 6px 16px;" onclick="confirmBulkAction()">Apply</button>
                        </div>
                    </div>
                </div>

            <table style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                        <th style="width: 60px;">#</th>
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
                        $counter = $offset + 1;
                        while($row = $result->fetch_assoc()) {
                            // Adjust image path for display in admin
                            $imgPath = '../' . $row['image'];
                            echo "<tr>";
                            echo "<td><input type='checkbox' name='product_ids[]' value='" . $row['id'] . "' class='bulk-checkbox'></td>";
                            echo "<td>" . $counter . "</td>";
                            echo "<td><img src='" . htmlspecialchars($imgPath) . "' class='product-thumb' onerror=\"this.onerror=null; this.src='https://placehold.co/60x60/e2e8f0/64748b?text=No+Image'\"></td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td><span style='background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;'>" . htmlspecialchars($row['category']) . "</span></td>";
                            $checked = !empty($row['is_featured']) ? 'checked' : '';
                            echo "<td>
                                    <label class='switch' style='position: relative; display: inline-block; width: 44px; height: 24px;'>
                                      <input type='checkbox' class='feature-toggle' data-id='" . $row['id'] . "' $checked style='opacity: 0; width: 0; height: 0;'>
                                      <span class='slider round' style='position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; transition: .4s; border-radius: 24px;'></span>
                                    </label>
                                  </td>";
                            echo "<td>
                                    <a href='edit_product?id=" . $row['id'] . "' class='btn btn-edit'>Edit</a>
                                    <a href='javascript:void(0)' class='btn btn-danger' onclick='confirmDelete(\"index?delete_id=" . $row['id'] . "\")'>Delete</a>
                                  </td>";
                            echo "</tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center;'>No products found. Add some!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            </form>

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
                
                fetch(`toggle_featured.php?_t=${Date.now()}`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    cache: 'no-store',
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

        function updateBulkActions() {
            let checkboxes = document.querySelectorAll('.bulk-checkbox');
            let selectedCount = 0;
            checkboxes.forEach(cb => {
                let tr = cb.closest('tr');
                if (cb.checked) {
                    selectedCount++;
                    tr.classList.add('selected-row');
                } else {
                    tr.classList.remove('selected-row');
                }
            });
            
            const container = document.getElementById('bulk-actions-container');
            const countText = document.getElementById('selected-count');
            const selectAll = document.getElementById('select-all');
            
            if (selectedCount > 0) {
                container.style.display = 'block';
                countText.innerText = selectedCount + ' record' + (selectedCount > 1 ? 's' : '') + ' selected';
                if (selectAll) selectAll.checked = selectedCount === checkboxes.length;
            } else {
                container.style.display = 'none';
                if (selectAll) selectAll.checked = false;
                document.getElementById('bulk-actions-menu').style.display = 'none';
            }
        }

        document.getElementById('select-all')?.addEventListener('change', function() {
            let checkboxes = document.querySelectorAll('.bulk-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        document.querySelectorAll('.bulk-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });
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

<div class="modal-overlay" id="confirm-modal">
    <div class="modal-content" style="max-width: 400px; text-align: center;">
        <h3 style="margin-bottom: 15px; font-weight: 600;">Confirm Action</h3>
        <p id="confirm-modal-text" style="color: var(--text-light); margin-bottom: 24px; font-size: 14px;">Are you sure you want to proceed?</p>
        <div style="display: flex; justify-content: center; gap: 12px;">
            <button type="button" class="btn" style="background: var(--border-light); color: var(--text-dark);" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirm-modal-btn">Confirm</button>
        </div>
    </div>
</div>

<script>
let confirmActionCallback = null;

function showConfirmModal(message, callback) {
    document.getElementById('confirm-modal-text').innerText = message;
    document.getElementById('confirm-modal').classList.add('active');
    confirmActionCallback = callback;
}

function closeConfirmModal() {
    document.getElementById('confirm-modal').classList.remove('active');
    confirmActionCallback = null;
}

document.getElementById('confirm-modal-btn').addEventListener('click', function() {
    if (confirmActionCallback) {
        confirmActionCallback();
    }
    closeConfirmModal();
});

function confirmDelete(url) {
    showConfirmModal('Are you sure you want to delete this product?', function() {
        window.location.href = url;
    });
}

function confirmBulkAction() {
    if(document.getElementById('bulk_action_select').value === '') {
        showToast('Please select an action', 'error');
        return;
    }
    showConfirmModal('Are you sure you want to apply this action to the selected items?', function() {
        document.getElementById('bulk-form').submit();
    });
}
</script>

<?php if (!empty($toast_msg)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        showToast(<?php echo json_encode($toast_msg); ?>, '<?php echo $toast_type; ?>');
    }, 100);
});
</script>
<?php endif; ?>

</body>
</html>

