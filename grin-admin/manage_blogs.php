<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM blogs WHERE id = $del_id");
    header("Location: manage_blogs.php?msg=deleted");
    exit();
}

$msg = "";
if(isset($_GET['msg'])) {
    if($_GET['msg'] == 'success') $msg = "<div class='alert alert-success'>Blog added successfully!</div>";
    if($_GET['msg'] == 'updated') $msg = "<div class='alert alert-success'>Blog updated successfully!</div>";
    if($_GET['msg'] == 'deleted') $msg = "<div class='alert alert-success'>Blog deleted successfully!</div>";
}

// Pagination & Search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$searchQuery = "";
if (!empty($search)) {
    $searchQuery = " WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
}

$total_result = $conn->query("SELECT COUNT(id) AS total FROM blogs" . $searchQuery);
$total_row = $total_result->fetch_assoc();
$total_blogs = $total_row['total'];
$total_pages = ceil($total_blogs / $limit);

// Fetch blogs
$sql = "SELECT id, title, author, created_at, image FROM blogs" . $searchQuery . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs - Grin Living</title>
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
                <a href="manage_categories">Categories</a>
                <a href="manage_blogs" class="active">Blogs</a>
                <a href="settings">Settings</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../blog.html" target="_blank" style="color: var(--secondary-color); text-decoration: none; font-size: 14px;">View Live Blog &rarr;</a>
                <a href="logout" class="btn btn-danger" style="text-align: center;">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header"> <button class="hamburger-btn" onclick="document.getElementById('admin-sidebar').classList.toggle('collapsed')">&#9776;</button> <div style="margin-left: auto; display: flex; align-items: center;">
                    <span style="font-size: 14px; color: var(--text-light);">Welcome, <?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?></span>
                </div>
            </header>

            <div class="admin-container" style="flex: 1;">
        <?php if($msg) echo $msg; ?>
        
        <div class="card">
            <div class="card-header" style="flex-wrap: wrap; gap: 15px;">
                <h2>Manage Blogs</h2>
                
                <form action="manage_blogs" method="GET" style="display: flex; gap: 10px; flex: 1; max-width: 400px; margin-left: auto;">
                    <input type="text" name="search" class="form-control" placeholder="Search blogs..." value="<?php echo htmlspecialchars($search); ?>" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if(!empty($search)): ?>
                        <a href="manage_blogs" class="btn" style="background: #e2e8f0; color: #1e293b;">Clear</a>
                    <?php endif; ?>
                </form>

                <a href="add_blog.php" class="btn btn-primary" style="white-space: nowrap;">+ Add New</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th style="width: 150px;">Author</th>
                        <th style="width: 150px;">Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $imgPath = !empty($row['image']) ? '../' . $row['image'] : 'https://placehold.co/60x60/e2e8f0/64748b?text=No+Image';
                            echo "<tr>";
                            echo "<td>#" . $row['id'] . "</td>";
                            echo "<td><img src='" . $imgPath . "' class='product-thumb' style='object-fit:cover;' onerror=\"this.onerror=null; this.src='https://placehold.co/60x60/e2e8f0/64748b?text=No+Image'\"></td>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['author']) . "</td>";
                            echo "<td>" . date('M j, Y', strtotime($row['created_at'])) . "</td>";
                            echo "<td>
                                    <a href='edit_blog.php?id=" . $row['id'] . "' class='btn btn-edit'>Edit</a>
                                    <a href='manage_blogs.php?delete_id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this blog?\");'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>No blogs found. Add some!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
            <div class="pagination" style="margin-top: 20px;">
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
</script>
</body>
</html>

