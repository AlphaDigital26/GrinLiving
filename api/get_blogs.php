<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../admin/db_connect.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Fetch total count
$total_result = $conn->query("SELECT COUNT(id) AS total FROM blogs");
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $limit);

// Fetch blogs
$sql = "SELECT id, title, content, author, created_at, image FROM blogs ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$blogs = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if (empty($row['image'])) {
            $row['image'] = 'https://placehold.co/600x400/e2e8f0/64748b?text=No+Image';
        }
        $blogs[] = $row;
    }
}

echo json_encode([
    'blogs' => $blogs,
    'totalPages' => $total_pages,
    'currentPage' => $page,
    'totalItems' => $total_items
]);

$conn->close();
?>
