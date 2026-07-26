<?php
// Load .env file
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

$db_host = $env['DB_HOST'] ?? "localhost";
$db_user = $env['DB_USER'] ?? "root";
$db_pass = $env['DB_PASS'] ?? "";
$db_name = $env['DB_NAME'] ?? "grin_living_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
