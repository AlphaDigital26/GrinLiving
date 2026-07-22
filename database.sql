CREATE DATABASE IF NOT EXISTS `grin_living_db`;
USE `grin_living_db`;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `image` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user (admin / admin123)
INSERT INTO `admins` (`username`, `password`) VALUES 
('admin', '$2y$10$w0/5s1.9c6y6.oN1K3FwJ.xH.Xf1gG7lS.4K.92iL2X9oI1R2x5M6') 
ON DUPLICATE KEY UPDATE `username`='admin';

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`name`, `description`) VALUES
('Cotton Fabrics', 'Experience the breathability and comfort of our premium cotton fabrics. Ideal for high-quality bedsheets and everyday apparel.'),
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

-- Default product data
INSERT INTO `products` (`title`, `category`, `image`) VALUES
('Yummy fabric 90% polyester 10% spandex fluorescent digital printed', 'Poly Spandex Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.55 PM (1).jpeg'),
('poly spandex fancy knits', 'Poly Spandex Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.55 PM.jpeg'),
('Yummy fabric 90% polyester 10% spandex', 'Poly Spandex Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.56 PM.jpeg'),
('Rayon twill big floral printed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.57 PM (1).jpeg'),
('40s rayon poplin digital printed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.57 PM.jpeg'),
('40s rayon poplin flocking', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.58 PM (1).jpeg'),
('40s rayon slub poplin leopard printed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.58 PM.jpeg'),
('100% cotton corduroy solid dyed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.59 PM (1).jpeg'),
('40s rayon slub poplin solid dyed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.59 PM (2).jpeg'),
('40s rayon poplin flocking', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.20.59 PM.jpeg'),
('30s rayon poplin crinkle', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.00 PM (1).jpeg'),
('30s rayon poplin crinkle', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.00 PM.jpeg'),
('30s rayon poplin discharge printed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.01 PM (1).jpeg'),
('40s rayon poplin discharge printed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.01 PM (2).jpeg'),
('Rayon twill digital printed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.01 PM.jpeg'),
('30s rayon poplin discharge printed', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.02 PM (1).jpeg'),
('Cotton yarn dyed double layers muslin', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.02 PM (2).jpeg'),
('Rayon challis crinkle tie dye', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.02 PM (3).jpeg'),
('40s rayon poplin Tie dye', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.02 PM.jpeg'),
('Rayon metallic poplin tie dye', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.03 PM (1).jpeg'),
('100% Cotton eyelet solid dyed', 'Embroidered Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.03 PM (2).jpeg'),
('Rayon metallic poplin Tie dye', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.03 PM.jpeg'),
('Viscose slub poplin plain dye', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.04 PM (1).jpeg'),
('Viscose crepe solid dyed', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.04 PM (2).jpeg'),
('100% cotton voile digital printed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.04 PM.jpeg'),
('100% cotton poplin digital printed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.05 PM (1).jpeg'),
('Rayon nylon fancy crinkle', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.05 PM (2).jpeg'),
('Cotton double layer muslin plain dye', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.05 PM.jpeg'),
('Rayon nylon check flocking', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.06 PM (1).jpeg'),
('Cotton voile eyelet solid dyed', 'Embroidered Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.06 PM (2).jpeg'),
('100% cotton canvas pigment printed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.06 PM.jpeg'),
('100% cotton corduroy digital printed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.07 PM (1).jpeg'),
('Nylon cotton lace fabric', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.07 PM (2).jpeg'),
('Cotton flex solid dyed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.07 PM (3).jpeg'),
('100% Cotton embroidered fabric', 'Embroidered Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.07 PM.jpeg'),
('Nylon cotton lace fabric', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.08 PM (1).jpeg'),
('Cotton rayon metallic dobby plain dye', 'Rayon Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.08 PM (2).jpeg'),
('Viscose satin burnout printed', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.08 PM.jpeg'),
('Viscose velvet burnout printed', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.09 PM (1).jpeg'),
('100% viscose flocking', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.09 PM (2).jpeg'),
('Cotton double layer muslin pigment printed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.09 PM.jpeg'),
('Mesh embroidered fabric', 'Mesh Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.10 PM (1).jpeg'),
('Cotton flex stripe yarn dyed', 'Cotton Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.10 PM (2).jpeg'),
('100% Viscose georgette digital printed', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.10 PM (3).jpeg'),
('100% viscose georgette Tie dye', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.10 PM.jpeg'),
('100% Viscose georgette flocking', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.11 PM.jpeg'),
('Viscose satin burnout solid dyed', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.12 PM (1).jpeg'),
('Viscose georgette eyelet solid dyed', 'Viscose Fabrics', 'Images/WhatsApp Image 2026-07-14 at 2.21.12 PM.jpeg');
