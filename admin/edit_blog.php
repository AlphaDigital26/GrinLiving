<?php
require_once 'auth.php';
require_once 'db_connect.php';

$error = '';
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$blog_id && !isset($_POST['id'])) {
    header("Location: manage_blogs.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $author = $conn->real_escape_string($_POST['author']);
    $hasNewImage = false;
    $imagePath = '';

    // Handle File Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $fileTmpName = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadPath = '../Images/' . $fileName;
        
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $imagePath = 'Images/' . $fileName;
            $hasNewImage = true;
        }
    }

    if (empty($title) || empty($content) || empty($author)) {
        $error = "Title, Author, and Content are required.";
    } else {
        if ($hasNewImage) {
            $stmt = $conn->prepare("UPDATE blogs SET title=?, content=?, author=?, image=? WHERE id=?");
            $stmt->bind_param("ssssi", $title, $content, $author, $imagePath, $id);
        } else {
            $stmt = $conn->prepare("UPDATE blogs SET title=?, content=?, author=? WHERE id=?");
            $stmt->bind_param("sssi", $title, $content, $author, $id);
        }
        
        if ($stmt->execute()) {
            header("Location: manage_blogs.php?msg=updated");
            exit();
        } else {
            $error = "Database Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $blog_id = $id; // For re-populating form
}

// Fetch existing data
$sql = "SELECT * FROM blogs WHERE id = $blog_id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    header("Location: manage_blogs.php");
    exit();
}
$blog = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blog - Grin Living Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
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
                <a href="manage_categories.php">Categories</a>
                <a href="manage_blogs.php" class="active">Blogs</a>
            </nav>
            <div class="sidebar-footer">
                <a href="../blog.html" target="_blank" style="color: var(--secondary-color); text-decoration: none; font-size: 14px;">View Live Blog &rarr;</a>
                <a href="logout.php" class="btn btn-danger" style="text-align: center;">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="admin-container" style="max-width: 800px;">
                <div class="card">
                    <div class="card-header" style="justify-content: space-between;">
                        <h2>Edit Blog</h2>
                        <a href="manage_blogs.php" class="btn btn-danger">Cancel</a>
                    </div>
                    
                    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

                    <form action="edit_blog.php" method="POST" enctype="multipart/form-data" style="padding: 20px;">
                        <input type="hidden" name="id" value="<?php echo $blog['id']; ?>">
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Blog Title</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Author Name</label>
                            <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($blog['author']); ?>" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Cover Image (Leave empty to keep current image)</label>
                            <?php if(!empty($blog['image'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <img src="../<?php echo htmlspecialchars($blog['image']); ?>" style="max-width: 200px; border-radius: 4px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label>Content</label>
                            <textarea name="content" id="blogContent" class="form-control" rows="10" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                            <script>
                                CKEDITOR.replace('blogContent');
                            </script>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Blog</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
