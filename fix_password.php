<?php
require_once 'admin/db_connect.php';

$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "UPDATE admins SET password = ? WHERE username = 'admin'";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $new_hash);
    if ($stmt->execute()) {
        echo "Successfully updated password for admin.\n";
    } else {
        echo "Error executing query: " . $stmt->error . "\n";
    }
    $stmt->close();
} else {
    echo "Error preparing query.\n";
}
?>
