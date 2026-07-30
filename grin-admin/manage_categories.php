<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Handle Add/Edit Category
if (isset($_POST['save_category'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    
    if (!empty($name)) {
        if ($id > 0) {
            $sql = "UPDATE categories SET name='$name', description='$desc' WHERE id=$id";
            $redirectMsg = "updated";
            $msgError = "Error updating category: ";
        } else {
            $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$desc')";
            $redirectMsg = "added";
            $msgError = "Error adding category: ";
        }
        
        if ($conn->query($sql) === TRUE) {
            header("Location: manage_categories?msg=" . $redirectMsg);
            exit();
        } else {
            $toast_msg = $msgError . $conn->error;
            $toast_type = "error";
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql = "DELETE FROM categories WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_categories?msg=deleted");
        exit();
    } else {
        $toast_msg = "Error deleting category: " . $conn->error;
        $toast_type = "error";
    }
}

// Handle redirect messages for Add, Edit, Delete
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'success' || $_GET['msg'] === 'added') {
        $toast_msg = "Category added successfully!";
        $toast_type = "success";
    } elseif ($_GET['msg'] === 'updated') {
        $toast_msg = "Category updated successfully!";
        $toast_type = "success";
    } elseif ($_GET['msg'] === 'deleted') {
        $toast_msg = "Category deleted successfully!";
        $toast_type = "error"; // red toast message for delete
    } elseif ($_GET['msg'] === 'error') {
        $toast_msg = "Error processing category request!";
        $toast_type = "error";
    }
}


// Fetch all categories
$categories = [];
$result = $conn->query("SELECT * FROM categories ORDER BY id ASC");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Grin Living</title>
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
                <a href="index">Products</a>
                <a href="manage_categories" class="active">Categories</a>
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
        <div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>
        
        <div class="card">
            <div class="card-header">
                <h2>Manage Categories</h2>
                <button type="button" class="btn btn-primary" onclick="openModal()">+ Add New</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th style="width: 250px;">Name</th>
                        <th>Description</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>#<?php echo $cat['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                <td style="color: #64748b; font-size: 14px;"><?php echo htmlspecialchars($cat['description']); ?></td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="btn btn-edit" onclick="openModal(<?php echo $cat['id']; ?>, '<?php echo htmlspecialchars(addslashes($cat['name'])); ?>', '<?php echo htmlspecialchars(addslashes($cat['description'])); ?>')">Edit</button>
                                    <a href="javascript:void(0)" class="btn btn-danger" onclick="confirmDelete('manage_categories?delete_id=<?php echo $cat['id']; ?>')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan='4' style='text-align:center;'>No categories found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal-overlay" id="categoryModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal()">&times;</button>
            <h2 id="modalTitle" style="margin-bottom: 24px;">Add New Category</h2>
            <form action="manage_categories" method="POST">
                <input type="hidden" id="categoryId" name="id" value="0">
                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Silk Fabrics">
                </div>
                <div class="form-group">
                    <label for="description">Description (optional)</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Description shown on live website..."></textarea>
                </div>
                <button type="submit" name="save_category" class="btn btn-primary" style="width: 100%;">Save Category</button>
            </form>
        </div>
    </div>

    <!-- Confirm Action Modal -->
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
            showConfirmModal('Are you sure you want to delete this category?', function() {
                window.location.href = url;
            });
        }

        function openModal(id = 0, name = '', desc = '') {
            document.getElementById('categoryId').value = id;
            document.getElementById('name').value = name;
            document.getElementById('description').value = desc;
            
            document.getElementById('modalTitle').textContent = id > 0 ? 'Edit Category' : 'Add New Category';
            document.getElementById('categoryModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.remove('active');
        }
    </script>
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

function showToast(message, type = 'error') {
    const container = document.getElementById('toast-container');
    if (!container) return;
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
    toast.style.backgroundColor = (type === 'error' || type === 'delete' || type === 'danger' || type === 'red') ? 'var(--danger-color, #EF4444)' : 'var(--success-color, #10B981)';
    toast.innerText = message;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    }, 10);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
<?php if (!empty($toast_msg)): ?>
<script>
setTimeout(function() {
    if (typeof showToast === 'function') {
        showToast(<?php echo json_encode($toast_msg); ?>, '<?php echo $toast_type; ?>');
    }
}, 100);
</script>
<?php endif; ?>
</body>
</html>

