<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../admin/db_connect.php';

// Fetch up to 4 featured products, newest first
$sql = "SELECT id, title, category, image FROM products WHERE is_featured = 1 ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>
