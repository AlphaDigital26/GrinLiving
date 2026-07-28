<?php
require_once 'auth.php';
require_once 'db_connect.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = trim($_POST['author']);
    $imagePath = "Images/hero_blog.webp"; // Default fallback image

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetDir = __DIR__ . '/../Images/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $uploadPath = $targetDir . $fileName;
        
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $imagePath = 'Images/' . $fileName;
        }
    }

    if (empty($title) || empty($content) || empty($author)) {
        $error = "Title, content, and author fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO blogs (title, content, author, image) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $title, $content, $author, $imagePath);
            if ($stmt->execute()) {
                header("Location: manage_blogs.php?msg=success");
                exit();
            } else {
                $error = "Database Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database Prepare Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Blog - Grin Living Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
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
            <div class="admin-container" style="max-width: 800px;">
                <div class="card">
                    <div class="card-header" style="justify-content: space-between;">
                        <h2>Add New Blog</h2>
                        <a href="manage_blogs" class="btn btn-danger">Cancel</a>
                    </div>
                    
                    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <form action="add_blog" method="POST" enctype="multipart/form-data" style="padding: 20px;">
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Blog Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Author Name</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Cover Image (Optional - default image used if omitted)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Content</label>
                            <textarea name="content" id="blogContent" class="form-control" rows="10" required></textarea>
                            <script>
                                CKEDITOR.replace('blogContent', { versionCheck: false });
                            </script>
                        </div>

                        <button type="submit" class="btn btn-primary">Publish Blog</button>
                    </form>
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

