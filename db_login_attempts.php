<?php
require_once __DIR__ . '/grin-admin/db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table login_attempts created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
$conn->close();
?>
