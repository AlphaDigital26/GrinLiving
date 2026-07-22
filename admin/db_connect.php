<?php
// Load .env file
$envFile = __DIR__ . '/../.env';
$env = [];
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
}

$host = $env['DB_HOST'] ?? "localhost";
$username = $env['DB_USER'] ?? "root";
$password = $env['DB_PASS'] ?? "";
$database = $env['DB_NAME'] ?? "grin_living_db";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
