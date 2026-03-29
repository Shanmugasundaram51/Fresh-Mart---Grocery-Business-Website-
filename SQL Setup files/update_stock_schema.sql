-- Migration script to add stock management features
-- Run this if you already have the database set up

USE onlinesale;

-- Add stock_quantity column to products table (if not exists)
ALTER TABLE `products` 
ADD COLUMN `stock_quantity` int(11) DEFAULT 100 AFTER `discount`;

-- Remove old stock_available column if it exists
ALTER TABLE `products` 
DROP COLUMN IF EXISTS `stock_available`;

-- Add quantity column to users_products table (if not exists)
ALTER TABLE `users_products` 
ADD COLUMN `quantity` int(11) DEFAULT 1 AFTER `status`;

-- Update existing products with stock quantities
UPDATE `products` SET `stock_quantity` = 50 WHERE `id` = 1;
UPDATE `products` SET `stock_quantity` = 100 WHERE `id` = 2;
UPDATE `products` SET `stock_quantity` = 30 WHERE `id` = 3;
UPDATE `products` SET `stock_quantity` = 3 WHERE `id` = 4;
UPDATE `products` SET `stock_quantity` = 80 WHERE `id` = 5;
UPDATE `products` SET `stock_quantity` = 0 WHERE `id` = 6;
UPDATE `products` SET `stock_quantity` = 120 WHERE `id` = 7;
UPDATE `products` SET `stock_quantity` = 2 WHERE `id` = 8;
UPDATE `products` SET `stock_quantity` = 40 WHERE `id` = 9;
UPDATE `products` SET `stock_quantity` = 60 WHERE `id` = 10;
UPDATE `products` SET `stock_quantity` = 25 WHERE `id` = 11;
UPDATE `products` SET `stock_quantity` = 4 WHERE `id` = 12;
UPDATE `products` SET `stock_quantity` = 35 WHERE `id` = 13;
UPDATE `products` SET `stock_quantity` = 0 WHERE `id` = 14;
UPDATE `products` SET `stock_quantity` = 150 WHERE `id` = 15;
UPDATE `products` SET `stock_quantity` = 1 WHERE `id` = 16;

-- Update existing cart items to have default quantity of 1
UPDATE `users_products` SET `quantity` = 1 WHERE `quantity` IS NULL OR `quantity` = 0;

COMMIT;
