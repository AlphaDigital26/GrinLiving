CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `image_data` LONGBLOB NULL,
  `image_type` VARCHAR(50) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user (admin / admin123)
INSERT INTO `admins` (`username`, `password`) VALUES 
('admin', '$2y$10$5pWLt5mFZL7nxixqKKQzBeOdb66MBFaN8Y6hNDfb/.hKFjVvthNX2') 
ON DUPLICATE KEY UPDATE `username`='admin';

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`name`, `description`) VALUES
('Cotton Fabrics', 'Experience the breathability and comfort of our premium cotton fabrics. Ideal for high-quality textiles and everyday apparel.'),
('Polyester Fabrics', 'Durable, wrinkle-resistant, and perfect for activewear and outerwear. Our polyester blends offer superior performance.'),
('Poly Spandex Fabrics', 'Enjoy the perfect stretch and recovery. Excellent for activewear, leggings, and form-fitting garments.'),
('Rayon Fabrics', 'Soft, smooth, and highly absorbent. Our rayon fabrics are ideal for comfortable summer dresses and blouses.'),
('Viscose Fabrics', 'Luxurious drape and silk-like feel. Viscose is perfect for elegant dresses and high-end fashion.'),
('Mesh Fabrics', 'Breathable and lightweight. Our mesh fabrics are perfect for sportswear panels and stylish overlays.'),
('Knit Fabrics', 'Comfortable and stretchy. From t-shirts to cozy sweaters, our knit fabrics are incredibly versatile.'),
('Velvet Fabrics', 'Rich, soft, and luxurious. Velvet adds a touch of elegance to evening wear and home decor.'),
('Embroidered Fabrics', 'Intricate designs and beautiful textures. Our embroidered fabrics are perfect for special occasion garments.'),
('Fancy / Fashion Fabrics', 'Make a statement with our unique and trendy fashion fabrics. Perfect for standout pieces and accessories.')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

CREATE TABLE IF NOT EXISTS `blogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `author` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) NULL,
  `image_data` LONGTEXT NULL,
  `image_type` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempt_time` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;