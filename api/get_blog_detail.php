<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
require_once __DIR__ . '/../grin-admin/db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No blog ID provided']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT id, title, content, image, author, created_at FROM blogs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
    
    if (empty($blog['image'])) {
        $blog['image'] = 'https://placehold.co/600x400/e2e8f0/64748b?text=No+Image';
    }
    
    echo json_encode(['status' => 'success', 'blog' => $blog]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Blog not found']);
}

$stmt->close();
$conn->close();
?>
