-- ========================================
-- FRESH MART - COMPLETE DATABASE SETUP
-- ========================================
-- This script sets up the entire database from scratch
-- Run this file to create a fresh installation
-- 
-- Database: onlinesale
-- Author: Fresh Mart Team
-- Date: March 2026
-- ========================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `onlinesale` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `onlinesale`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- ========================================
-- TABLE 1: USERS
-- ========================================
-- Stores customer information

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email_id` varchar(255) NOT NULL UNIQUE,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `registration_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_id` (`email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Sample test users (password: 123456 for all)
INSERT INTO `users` (`id`, `email_id`, `first_name`, `last_name`, `phone`, `password`) VALUES
(1, 'test@freshmart.com', 'Test', 'User', '9876543210', 'e10adc3949ba59abbe56e057f20f883e'),
(2, 'demo@freshmart.com', 'Demo', 'Customer', '9876543211', 'e10adc3949ba59abbe56e057f20f883e');

-- ========================================
-- TABLE 2: PRODUCTS
-- ========================================
-- Stores product catalog with stock management

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `category` varchar(50) DEFAULT 'Fruits',
  `price` int(20) NOT NULL,
  `unit` varchar(20) DEFAULT 'piece',
  `discount` int(3) DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 100,
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `stock_quantity` (`stock_quantity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert all products with categories and image paths
INSERT INTO `products` (`id`, `name`, `category`, `price`, `unit`, `discount`, `stock_quantity`, `image_path`) VALUES
-- FRUITS (IDs 1-6)
(1, 'Fresh Apples', 'Fruits', 120, 'kg', 10, 50, 'images/apple.webp'),
(2, 'Bananas', 'Fruits', 50, 'dozen', 0, 100, 'images/banana.webp'),
(3, 'Orange', 'Fruits', 80, 'kg', 5, 30, 'images/orange.webp'),
(4, 'Grapes', 'Fruits', 150, 'kg', 15, 25, 'images/grapes.webp'),
(5, 'Mango', 'Fruits', 140, 'kg', 15, 60, 'images/mango.webp'),
(6, 'Watermelon', 'Fruits', 40, 'kg', 0, 70, 'images/watermelon.webp'),

-- VEGETABLES (IDs 7-12)
(7, 'Tomatoes', 'Vegetables', 40, 'kg', 0, 80, 'images/tomato.webp'),
(8, 'Onions', 'Vegetables', 30, 'kg', 0, 50, 'images/onion.webp'),
(9, 'Potatoes', 'Vegetables', 25, 'kg', 0, 120, 'images/potato.webp'),
(10, 'Carrots', 'Vegetables', 45, 'kg', 5, 40, 'images/carrot.webp'),
(11, 'Broccoli', 'Vegetables', 60, 'kg', 0, 35, 'images/broccoli.webp'),
(12, 'Bell Pepper', 'Vegetables', 70, 'kg', 5, 50, 'images/bell-pepper.webp'),

-- DAIRY (IDs 13-18)
(13, 'Fresh Milk', 'Dairy', 60, 'liter', 0, 40, 'images/milk.webp'),
(14, 'Yogurt', 'Dairy', 50, 'pack', 10, 60, 'images/yogurt.webp'),
(15, 'Cheese', 'Dairy', 200, 'pack', 0, 25, 'images/cheese.webp'),
(16, 'Butter', 'Dairy', 180, 'pack', 5, 30, 'images/butter.webp'),
(17, 'Paneer', 'Dairy', 150, 'pack', 0, 30, 'images/paneer.webp'),
(18, 'Cream', 'Dairy', 90, 'pack', 5, 40, 'images/cream.webp'),

-- BEVERAGES (IDs 19-24)
(19, 'Orange Juice', 'Beverages', 120, 'liter', 10, 35, 'images/orange-juice.webp'),
(20, 'Apple Juice', 'Beverages', 100, 'liter', 0, 45, 'images/apple-juice1.jpg'),
(21, 'Mineral Water', 'Beverages', 40, 'liter', 0, 150, 'images/water.webp'),
(22, 'Soft Drink', 'Beverages', 80, 'liter', 15, 60, 'images/soft-drink.webp'),
(23, 'Green Tea', 'Beverages', 150, 'pack', 10, 55, 'images/green-tea.webp'),
(24, 'Coffee', 'Beverages', 200, 'pack', 0, 45, 'images/coffee.webp');

-- ========================================
-- TABLE 3: USERS_PRODUCTS (Shopping Cart)
-- ========================================
-- Stores items added to cart and confirmed orders

DROP TABLE IF EXISTS `users_products`;
CREATE TABLE `users_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `status` enum('Added To Cart','Confirmed') NOT NULL DEFAULT 'Added To Cart',
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`),
  KEY `status` (`status`),
  CONSTRAINT `users_products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_products_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ========================================
-- TABLE 4: ORDERS
-- ========================================
-- Stores order information

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order_status` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `payment_status` enum('Pending','Paid','Failed') NOT NULL DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `delivery_address` text DEFAULT NULL,
  `delivery_phone` varchar(15) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `order_date` (`order_date`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ========================================
-- TABLE 5: ORDER_ITEMS
-- ========================================
-- Stores individual items in each order

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_unit` varchar(20) NOT NULL,
  `discount_percent` int(3) NOT NULL DEFAULT 0,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ========================================
-- TABLE 6: ADMINS
-- ========================================
-- Stores admin user credentials

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Default admin credentials
-- Username: admin
-- Password: admin123
INSERT INTO `admins` (`username`, `password`, `full_name`) VALUES
('admin', '0192023a7bbd73250516f069df18b500', 'Administrator');

-- ========================================
-- TABLE 7: INVOICE_LOGS (Optional)
-- ========================================
-- Tracks invoice generation for analytics

-- ========================================
-- AUTO_INCREMENT SETTINGS
-- ========================================

ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
ALTER TABLE `products` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `users_products` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `orders` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `order_items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
ALTER TABLE `admins` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `invoice_logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

COMMIT;

-- ========================================
-- SETUP COMPLETE!
-- ========================================
-- 
-- Next Steps:
-- 1. Ensure all product images are in the images/ folder
-- 2. Update database connection in includes/common.php
-- 3. Update database connection in admin/includes/admin_common.php
-- 4. Set proper permissions: chmod 755 images/
-- 
-- Default Credentials:
-- - Admin: username=admin, password=admin123
-- - Test User: email=test@freshmart.com, password=123456
-- 
-- Access URLs (adjust port if needed):
-- - Customer: http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/
-- - Admin: http://localhost:8888/onlinesale/ECOMMERCE-WEBSITE/admin/
-- 
-- ========================================

-- Verification queries (optional - uncomment to run)
-- SELECT 'Users' as Table_Name, COUNT(*) as Count FROM users;
-- SELECT 'Products' as Table_Name, COUNT(*) as Count FROM products;
-- SELECT 'Admins' as Table_Name, COUNT(*) as Count FROM admins;
-- SELECT 'Orders' as Table_Name, COUNT(*) as Count FROM orders;
-- SELECT 'Order Items' as Table_Name, COUNT(*) as Count FROM order_items;
-- SELECT 'Cart Items' as Table_Name, COUNT(*) as Count FROM users_products;
-- SELECT 'Invoice Logs' as Table_Name, COUNT(*) as Count FROM invoice_logs;
