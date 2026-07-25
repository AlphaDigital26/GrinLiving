<?php
$conn = new mysqli("localhost", "root", "", "grin_living_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Revert Products Table
$conn->query("ALTER TABLE products DROP COLUMN image_data, DROP COLUMN image_type");
echo "Dropped image_data and image_type from products.\n";

// Revert Blogs Table
$conn->query("ALTER TABLE blogs DROP COLUMN image_data, DROP COLUMN image_type");
echo "Dropped image_data and image_type from blogs.\n";

$conn->close();
?>
