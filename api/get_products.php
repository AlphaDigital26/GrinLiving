<?php
header('Content-Type: application/json');

// Include db_connect.php relative to api directory
require_once '../admin/db_connect.php';

$sql = "SELECT id, title, category, image FROM products ORDER BY id DESC";
$result = $conn->query($sql);

$products = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

echo json_encode($products);
$conn->close();
?>
