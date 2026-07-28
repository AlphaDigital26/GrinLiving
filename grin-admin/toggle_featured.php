<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Basic auth check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized session. Please login again.']);
    exit;
}

// Ensure is_featured column exists in products table (auto-migration if missing on hosting DB)
$checkCol = $conn->query("SHOW COLUMNS FROM products LIKE 'is_featured'");
if ($checkCol && $checkCol->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN is_featured TINYINT(1) DEFAULT 0");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['is_featured'])) {
        $id = intval($_POST['id']);
        $is_featured = intval($_POST['is_featured']) ? 1 : 0;
        
        $sql = "UPDATE products SET is_featured = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $is_featured, $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Statement preparation failed: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
$conn->close();
?>
