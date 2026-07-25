<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Basic auth check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['is_featured'])) {
        $id = intval($_POST['id']);
        $is_featured = intval($_POST['is_featured']) ? 1 : 0;
        
        // If turning ON, check limit
        if ($is_featured === 1) {
            $countResult = $conn->query("SELECT COUNT(*) AS total FROM products WHERE is_featured = 1");
            if ($countResult) {
                $row = $countResult->fetch_assoc();
                if ($row['total'] >= 4) {
                    echo json_encode(['success' => false, 'error' => 'You can only feature up to 4 products at a time. Please un-feature one first.']);
                    exit;
                }
            }
        }
        
        $sql = "UPDATE products SET is_featured = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $is_featured, $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed']);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Statement preparation failed']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
$conn->close();
?>
