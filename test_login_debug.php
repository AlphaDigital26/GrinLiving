<?php
require_once 'admin/db_connect.php';

echo "Testing DB Connection...\n";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully.\n";

$result = $conn->query("SHOW TABLES LIKE 'admins'");
if ($result->num_rows > 0) {
    echo "Table 'admins' exists.\n";
    $users = $conn->query("SELECT * FROM admins");
    echo "Number of admins: " . $users->num_rows . "\n";
    if ($users->num_rows > 0) {
        $row = $users->fetch_assoc();
        echo "Found user: " . $row['username'] . "\n";
        echo "Hash: " . $row['password'] . "\n";
        $test_pw = 'admin123';
        if (password_verify($test_pw, $row['password'])) {
            echo "Password verify SUCCESS for 'admin123'\n";
        } else {
            echo "Password verify FAILED for 'admin123'\n";
            echo "Let's generate a correct hash for admin123: " . password_hash($test_pw, PASSWORD_DEFAULT) . "\n";
        }
    } else {
        echo "No users in admins table.\n";
    }
} else {
    echo "Table 'admins' DOES NOT EXIST.\n";
}
?>
