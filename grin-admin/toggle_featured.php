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

$id = null;
$is_featured = null;

if (isset($_REQUEST['id']) && isset($_REQUEST['is_featured'])) {
    $id = intval($_REQUEST['id']);
    $is_featured = intval($_REQUEST['is_featured']) ? 1 : 0;
} else {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);
    if (is_array($input) && isset($input['id']) && isset($input['is_featured'])) {
        $id = intval($input['id']);
        $is_featured = intval($input['is_featured']) ? 1 : 0;
    }
}

if ($id !== null && $is_featured !== null) {
    if ($is_featured === 1) {
        $countResult = $conn->query("SELECT COUNT(*) as cnt FROM products WHERE is_featured = 1 AND id != " . intval($id));
        $row = $countResult ? $countResult->fetch_assoc() : ['cnt' => 0];
        if (intval($row['cnt']) >= 4) {
            echo json_encode(['success' => false, 'error' => 'You cannot feature more than 4 products on the homepage!']);
            exit;
        }
    }

    $sql = "UPDATE products SET is_featured = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $is_featured, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'is_featured' => $is_featured]);
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
$conn->close();
?>
