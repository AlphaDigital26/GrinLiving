<?php
require 'grin-admin/db_connect.php';
$res = $conn->query('SELECT id, username, password FROM admins');
if ($res && $res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | Username: " . $row['username'] . " | Hash: " . $row['password'] . "\n";
    }
} else {
    echo "No admins found in database.\n";
}
?>
