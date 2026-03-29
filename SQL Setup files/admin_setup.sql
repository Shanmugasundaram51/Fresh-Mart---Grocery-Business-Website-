-- Admin Panel Setup Script
-- Run this script to add admin functionality to the database

USE onlinesale;

-- Create admin table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Add default admin (username: admin, password: admin123)
-- Password is hashed using MD5 (for consistency with existing user passwords)
INSERT INTO `admins` (`username`, `password`, `full_name`) VALUES
('admin', '0192023a7bbd73250516f069df18b500', 'Administrator');

-- Add category and image_path columns to products table
ALTER TABLE `products` 
ADD COLUMN `category` varchar(50) DEFAULT 'Fruits' AFTER `name`,
ADD COLUMN `image_path` varchar(255) DEFAULT NULL AFTER `stock_quantity`;

-- Update existing products with categories and image paths
UPDATE `products` SET `category` = 'Fruits', `image_path` = 'images/apple.webp' WHERE `id` = 1;
UPDATE `products` SET `category` = 'Fruits', `image_path` = 'images/banana.webp' WHERE `id` = 2;
UPDATE `products` SET `category` = 'Fruits', `image_path` = 'images/orange.webp' WHERE `id` = 3;
UPDATE `products` SET `category` = 'Fruits', `image_path` = 'images/grapes.webp' WHERE `id` = 4;
UPDATE `products` SET `category` = 'Vegetables', `image_path` = 'images/tomato.webp' WHERE `id` = 5;
UPDATE `products` SET `category` = 'Vegetables', `image_path` = 'images/onion.webp' WHERE `id` = 6;
UPDATE `products` SET `category` = 'Vegetables', `image_path` = 'images/potato.webp' WHERE `id` = 7;
UPDATE `products` SET `category` = 'Vegetables', `image_path` = 'images/carrot.webp' WHERE `id` = 8;
UPDATE `products` SET `category` = 'Dairy', `image_path` = 'images/milk.webp' WHERE `id` = 9;
UPDATE `products` SET `category` = 'Dairy', `image_path` = 'images/yogurt.webp' WHERE `id` = 10;
UPDATE `products` SET `category` = 'Dairy', `image_path` = 'images/cheese.webp' WHERE `id` = 11;
UPDATE `products` SET `category` = 'Dairy', `image_path` = 'images/butter.webp' WHERE `id` = 12;
UPDATE `products` SET `category` = 'Beverages', `image_path` = 'images/orange-juice.webp' WHERE `id` = 13;
UPDATE `products` SET `category` = 'Beverages', `image_path` = 'images/apple-juice1.jpg' WHERE `id` = 14;
UPDATE `products` SET `category` = 'Beverages', `image_path` = 'images/water.webp' WHERE `id` = 15;
UPDATE `products` SET `category` = 'Beverages', `image_path` = 'images/soft-drink.webp' WHERE `id` = 16;

COMMIT;
