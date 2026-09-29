<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/../grin-admin/db_connect.php';

$sql = "SELECT id, name, description FROM categories ORDER BY id ASC";
$result = $conn->query($sql);

$categories = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if (empty($categories)) {
    $categories = [
        ['id' => 1, 'name' => 'Cotton Fabrics', 'description' => 'Experience the breathability and comfort of our premium cotton fabrics. Ideal for high-quality bedsheets and everyday apparel.'],
        ['id' => 2, 'name' => 'Polyester Fabrics', 'description' => 'Durable, wrinkle-resistant, and perfect for activewear and outerwear. Our polyester blends offer superior performance.'],
        ['id' => 3, 'name' => 'Poly Spandex Fabrics', 'description' => 'Enjoy the perfect stretch and recovery. Excellent for activewear, leggings, and form-fitting garments.'],
        ['id' => 4, 'name' => 'Rayon Fabrics', 'description' => 'Soft, smooth, and highly absorbent. Our rayon fabrics are ideal for comfortable summer dresses and blouses.'],
        ['id' => 5, 'name' => 'Viscose Fabrics', 'description' => 'Luxurious drape and silk-like feel. Viscose is perfect for elegant dresses and high-end fashion.'],
        ['id' => 6, 'name' => 'Mesh Fabrics', 'description' => 'Breathable and lightweight. Our mesh fabrics are perfect for sportswear panels and stylish overlays.'],
        ['id' => 7, 'name' => 'Knit Fabrics', 'description' => 'Comfortable and stretchy. From t-shirts to cozy sweaters, our knit fabrics are incredibly versatile.'],
        ['id' => 8, 'name' => 'Velvet Fabrics', 'description' => 'Rich, soft, and luxurious. Velvet adds a touch of elegance to evening wear and home decor.'],
        ['id' => 9, 'name' => 'Embroidered Fabrics', 'description' => 'Intricate designs and beautiful textures. Our embroidered fabrics are perfect for special occasion garments.'],
        ['id' => 10, 'name' => 'Fancy / Fashion Fabrics', 'description' => 'Make a statement with our unique and trendy fashion fabrics. Perfect for standout pieces and accessories.']
    ];
}

echo json_encode($categories);
$conn->close();
?>
