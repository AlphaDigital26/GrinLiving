<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
require_once __DIR__ . '/../grin-admin/db_connect.php';

// Ensure is_featured column exists in products table (auto-migration if missing on hosting DB)
$checkCol = $conn->query("SHOW COLUMNS FROM products LIKE 'is_featured'");
if ($checkCol && $checkCol->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN is_featured TINYINT(1) DEFAULT 0");
}

// Fetch up to 4 featured products, newest first
$sql = "SELECT id, title, category, image FROM products WHERE is_featured = 1 ORDER BY id DESC LIMIT 4";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Fallback: If no products are explicitly marked featured, display 4 newest products so homepage is never empty
if (empty($products)) {
    $sqlFallback = "SELECT id, title, category, image FROM products ORDER BY id DESC LIMIT 4";
    $resultFallback = $conn->query($sqlFallback);
    if ($resultFallback && $resultFallback->num_rows > 0) {
        while($row = $resultFallback->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

echo json_encode($products);
$conn->close();
?>
