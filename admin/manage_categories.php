<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Handle Add/Edit Category
if (isset($_POST['save_category'])) {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = $conn->real_escape_string(trim($_POST['name']));
    $desc = $conn->real_escape_string(trim($_POST['description']));
    
    if (!empty($name)) {
        if ($id > 0) {
            $sql = "UPDATE categories SET name='$name', description='$desc' WHERE id=$id";
            $msgSuccess = "Category updated successfully!";
            $msgError = "Error updating category: ";
        } else {
            $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$desc')";
            $msgSuccess = "Category added successfully!";
            $msgError = "Error adding category: ";
        }
        
        if ($conn->query($sql) === TRUE) {
            $msg = "<div class='alert alert-success'>$msgSuccess</div>";
        } else {
            $msg = "<div class='alert alert-danger'>$msgError" . $conn->error . "</div>";
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql = "DELETE FROM categories WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        $msg = "<div class='alert alert-success'>Category deleted successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Error deleting category: " . $conn->error . "</div>";
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
    <title>Manage Categories - Grin Living</title>
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
                <a href="index.php">Products</a>
                <a href="manage_categories.php" class="active">Categories</a>
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
                                    <a href="manage_categories.php?delete_id=<?php echo $cat['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
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
            <form action="manage_categories.php" method="POST">
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

    <script>
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
</body>
</html>
