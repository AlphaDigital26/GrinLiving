<?php
header('Content-Type: application/json');

require_once '../admin/db_connect.php';

$sql = "SELECT id, name, description FROM categories ORDER BY id ASC";
$result = $conn->query($sql);

$categories = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

echo json_encode($categories);
$conn->close();
?>
