<?php
$conn = new mysqli("localhost", "root", "", "grin_living_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Migrate Products
$result = $conn->query("SELECT id, image_data FROM products WHERE image_data IS NOT NULL");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $base64_string = $row['image_data'];
        
        // Extract base64 data
        list($type, $data) = explode(';', $base64_string);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        
        // Determine extension
        $ext = 'jpg';
        if (strpos($type, 'png') !== false) $ext = 'png';
        if (strpos($type, 'webp') !== false) $ext = 'webp';
        
        $filename = 'product_' . $id . '_' . time() . '.' . $ext;
        $filepath = 'Images/' . $filename;
        $db_path = 'Images/' . $filename;
        
        file_put_contents($filepath, $data);
        
        $conn->query("UPDATE products SET image = '$db_path' WHERE id = $id");
        echo "Migrated product $id to $filepath\n";
    }
} else {
    echo "No products to migrate.\n";
}

// Migrate Blogs
$result = $conn->query("SELECT id, image_data FROM blogs WHERE image_data IS NOT NULL");
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $base64_string = $row['image_data'];
        
        list($type, $data) = explode(';', $base64_string);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        
        $ext = 'jpg';
        if (strpos($type, 'png') !== false) $ext = 'png';
        if (strpos($type, 'webp') !== false) $ext = 'webp';
        
        $filename = 'blog_' . $id . '_' . time() . '.' . $ext;
        $filepath = 'Images/' . $filename;
        $db_path = 'Images/' . $filename;
        
        file_put_contents($filepath, $data);
        
        $conn->query("ALTER TABLE blogs ADD COLUMN IF NOT EXISTS image VARCHAR(255) AFTER author");
        $conn->query("UPDATE blogs SET image = '$db_path' WHERE id = $id");
        echo "Migrated blog $id to $filepath\n";
    }
} else {
    echo "No blogs to migrate.\n";
}

$conn->close();
?>
