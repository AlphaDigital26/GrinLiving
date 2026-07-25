<?php
header('Content-Type: application/json');

// Include db_connect.php relative to api directory
require_once __DIR__ . '/../admin/db_connect.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 12; // 12 items per page

$offset = ($page - 1) * $limit;

// Count total products for pagination
$count_sql = "SELECT COUNT(*) as total FROM products";
if ($category !== 'All') {
    $count_sql .= " WHERE category = '" . $conn->real_escape_string($category) . "'";
}
$count_result = $conn->query($count_sql);
$total_row = $count_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $limit);

// Fetch products
$sql = "SELECT id, title, category, image FROM products";
if ($category !== 'All') {
    $sql .= " WHERE category = '" . $conn->real_escape_string($category) . "'";
}
$sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

$products = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode(array(
    'products' => $products,
    'total' => $total_items,
    'page' => $page,
    'limit' => $limit,
    'total_pages' => $total_pages
));
$conn->close();
?>
