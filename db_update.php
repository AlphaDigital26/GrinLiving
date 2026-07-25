<?php
$conn = new mysqli("localhost", "root", "", "grin_living_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$check1 = $conn->query("SHOW COLUMNS FROM `products` LIKE 'image_data'");
if ($check1->num_rows == 0) {
    $conn->query("ALTER TABLE `products` ADD `image_data` LONGBLOB NULL");
}

$check2 = $conn->query("SHOW COLUMNS FROM `products` LIKE 'image_type'");
if ($check2->num_rows == 0) {
    $conn->query("ALTER TABLE `products` ADD `image_type` VARCHAR(50) NULL");
}

echo "Database updated successfully.";
$conn->close();
?>
