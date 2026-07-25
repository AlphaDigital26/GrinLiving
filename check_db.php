<?php
$conn = new mysqli("localhost", "root", "", "grin_living_db");

$result = $conn->query("SELECT id FROM products WHERE image_data IS NOT NULL");
echo "Products with image_data: " . $result->num_rows . "\n";

$result2 = $conn->query("SELECT id FROM blogs WHERE image_data IS NOT NULL");
echo "Blogs with image_data: " . ($result2 ? $result2->num_rows : 0) . "\n";

$conn->close();
?>
