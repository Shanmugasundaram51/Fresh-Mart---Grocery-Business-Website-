-- Add 2 new products to each category

-- Fruits (IDs 22-23)
INSERT INTO `products` (`id`, `name`, `category`, `price`, `unit`, `discount`, `stock_quantity`, `image_path`) VALUES
(22, 'Mango', 'Fruits', 140, 'kg', 15, 60, 'images/mango.webp'),
(23, 'Watermelon', 'Fruits', 40, 'kg', 0, 70, 'images/watermelon.webp');

-- Vegetables (IDs 24-25)
INSERT INTO `products` (`id`, `name`, `category`, `price`, `unit`, `discount`, `stock_quantity`, `image_path`) VALUES
(24, 'Broccoli', 'Vegetables', 60, 'kg', 0, 35, 'images/broccoli.webp'),
(25, 'Bell Pepper', 'Vegetables', 70, 'kg', 5, 50, 'images/bell-pepper.webp');

-- Dairy (IDs 26-27)
INSERT INTO `products` (`id`, `name`, `category`, `price`, `unit`, `discount`, `stock_quantity`, `image_path`) VALUES
(26, 'Paneer', 'Dairy', 150, 'pack', 0, 30, 'images/paneer.webp'),
(27, 'Cream', 'Dairy', 90, 'pack', 5, 40, 'images/cream.webp');

-- Beverages (IDs 28-29)
INSERT INTO `products` (`id`, `name`, `category`, `price`, `unit`, `discount`, `stock_quantity`, `image_path`) VALUES
(28, 'Green Tea', 'Beverages', 150, 'pack', 10, 55, 'images/green-tea.webp'),
(29, 'Coffee', 'Beverages', 200, 'pack', 0, 45, 'images/coffee.webp');

-- Update AUTO_INCREMENT
ALTER TABLE `products` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

COMMIT;
