-- Orders and Order Items Tables Schema
-- Run this script to add order management functionality

USE onlinesale;

-- Create orders table
CREATE TABLE IF NOT EXISTS `orders` (
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
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `order_date` (`order_date`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Create order_items table
CREATE TABLE IF NOT EXISTS `order_items` (
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

-- Sample data: Create orders from existing confirmed users_products
-- This migrates historical data into the new structure

-- First, get distinct user_ids with confirmed orders
INSERT INTO `orders` (`user_id`, `order_number`, `total_amount`, `discount_amount`, `final_amount`, `order_status`, `payment_status`, `order_date`)
SELECT 
    up.user_id,
    CONCAT('ORD-', LPAD(up.user_id, 4, '0'), '-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(FLOOR(RAND() * 10000), 4, '0')) as order_number,
    SUM(p.price * up.quantity) as total_amount,
    SUM((p.price * p.discount / 100) * up.quantity) as discount_amount,
    SUM((p.price - (p.price * p.discount / 100)) * up.quantity) as final_amount,
    'Delivered' as order_status,
    'Paid' as payment_status,
    NOW() as order_date
FROM users_products up
INNER JOIN products p ON up.item_id = p.id
WHERE up.status = 'Confirmed'
GROUP BY up.user_id
LIMIT 10;

-- Populate order_items from users_products for the created orders
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_price`, `product_unit`, `discount_percent`, `quantity`, `subtotal`, `discount_amount`, `final_price`)
SELECT 
    o.id as order_id,
    p.id as product_id,
    p.name as product_name,
    p.price as product_price,
    p.unit as product_unit,
    p.discount as discount_percent,
    up.quantity,
    (p.price * up.quantity) as subtotal,
    ((p.price * p.discount / 100) * up.quantity) as discount_amount,
    ((p.price - (p.price * p.discount / 100)) * up.quantity) as final_price
FROM users_products up
INNER JOIN products p ON up.item_id = p.id
INNER JOIN orders o ON o.user_id = up.user_id
WHERE up.status = 'Confirmed'
ORDER BY o.id, up.id;

COMMIT;

-- Verify the data
SELECT 'Orders Created:' as Info, COUNT(*) as Count FROM orders;
SELECT 'Order Items Created:' as Info, COUNT(*) as Count FROM order_items;
