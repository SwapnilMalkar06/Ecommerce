-- ChicThreads E-commerce Database Setup
-- This script creates the database, tables, and populates them with sample data.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. Create and Use Database
CREATE DATABASE IF NOT EXISTS `ecommerce`;
USE `ecommerce`;

-- --------------------------------------------------------

-- 2. Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping dummy user (Password is 'password123' hashed)
INSERT INTO `users` (`fullname`, `email`, `password`) VALUES
('Swapnil Malkar', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

-- 3. Table structure for table `products`
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` text NOT NULL,
  `audience` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- 4. Population of Products (15+ Items for major filters)
TRUNCATE TABLE `products`;

-- Women's Collection (15 Items)
INSERT INTO `products` (`name`, `category`, `price`, `image_url`, `audience`) VALUES
('Classic White Tee', 'Tops', 29.99, 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?q=80&w=600&auto=format&fit=crop', 'Women'),
('Floral Midi Dress', 'Dresses', 89.99, 'https://images.unsplash.com/photo-1657214059212-5062a424a25c?q=80&w=600&auto=format&fit=crop', 'Women'),
('High-Waist Skinny Jeans', 'Jeans', 79.50, 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?q=80&w=600&auto=format&fit=crop', 'Women'),
('Silk Blend Blouse', 'Tops', 65.00, 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?q=80&w=600&auto=format&fit=crop', 'Women'),
('Pleated A-Line Skirt', 'Skirts', 55.25, 'https://images.unsplash.com/photo-1596501318536-a81e9f2a9b39?q=80&w=600&auto=format&fit=crop', 'Women'),
('Bohemian Maxi Dress', 'Dresses', 95.00, 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?q=80&w=600&auto=format&fit=crop', 'Women'),
('Cropped Linen Top', 'Tops', 48.00, 'https://images.unsplash.com/photo-1554512239-201e74148a28?q=80&w=600&auto=format&fit=crop', 'Women'),
('Distressed Boyfriend Jeans', 'Jeans', 88.00, 'https://images.unsplash.com/photo-1565490348226-e97576f36592?q=80&w=600&auto=format&fit=crop', 'Women'),
('Evening Cocktail Dress', 'Dresses', 145.00, 'https://images.unsplash.com/photo-1539008835657-9e8e9680c956?q=80&w=600&auto=format&fit=crop', 'Women'),
('Summer Sun Hat', 'Accessories', 35.00, 'https://images.unsplash.com/photo-1521335629791-ce4aec67dd15?q=80&w=600&auto=format&fit=crop', 'Women'),
('Quilted Leather Purse', 'Bags', 110.00, 'https://images.unsplash.com/photo-1591561954557-26941169b79e?q=80&w=600&auto=format&fit=crop', 'Women'),
('Pointed Toe Heels', 'Shoes', 125.00, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=600&auto=format&fit=crop', 'Women'),
('Wool Winter Coat', 'Outerwear', 210.00, 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?q=80&w=600&auto=format&fit=crop', 'Women'),
('Velvet Party Top', 'Tops', 52.00, 'https://images.unsplash.com/photo-1574015974293-817f0efebb1b?q=80&w=600&auto=format&fit=crop', 'Women'),
('Casual Denim Skirt', 'Skirts', 45.00, 'https://images.unsplash.com/photo-1582142306909-195724d33ffc?q=80&w=600&auto=format&fit=crop', 'Women');

-- Men's Collection (15 Items)
INSERT INTO `products` (`name`, `category`, `price`, `image_url`, `audience`) VALUES
('Men\'s Graphic T-Shirt', 'Tops', 34.99, 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?q=80&w=600&auto=format&fit=crop', 'Men'),
('V-Neck Pullover Sweater', 'Tops', 65.99, 'https://images.unsplash.com/photo-1616199496424-722631584226?q=80&w=600&auto=format&fit=crop', 'Men'),
('Slim-Fit Chino Pants', 'Jeans', 89.99, 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?q=80&w=600&auto=format&fit=crop', 'Men'),
('Straight Leg Dark Wash Jeans', 'Jeans', 95.00, 'https://images.unsplash.com/photo-1604176354204-9268737828e4?q=80&w=600&auto=format&fit=crop', 'Men'),
('Men\'s Bomber Jacket', 'Outerwear', 118.99, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?q=80&w=600&auto=format&fit=crop', 'Men'),
('Water-Resistant Windbreaker', 'Outerwear', 72.08, 'https://images.unsplash.com/photo-1543087902-386127cb4202?q=80&w=600&auto=format&fit=crop', 'Men'),
('Leather Dress Shoes', 'Shoes', 130.00, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?q=80&w=600&auto=format&fit=crop', 'Men'),
('Casual Canvas Sneakers', 'Shoes', 60.00, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?q=80&w=600&auto=format&fit=crop', 'Men'),
('Classic Leather Watch', 'Watches', 250.00, 'https://images.unsplash.com/photo-1533139502658-0198f920d8e8?q=80&w=600&auto=format&fit=crop', 'Men'),
('Oxford Button-Down Shirt', 'Tops', 55.00, 'https://images.unsplash.com/photo-1596755094514-5b92400121a8?q=80&w=600&auto=format&fit=crop', 'Men'),
('Denim Trucker Jacket', 'Outerwear', 135.00, 'https://images.unsplash.com/photo-1543076499-8f0b7c421b31?q=80&w=600&auto=format&fit=crop', 'Men'),
('Leather Belt', 'Accessories', 49.50, 'https://images.unsplash.com/photo-1556015584-c50a41c187e5?q=80&w=600&auto=format&fit=crop', 'Men'),
('Crewneck Sweatshirt', 'Tops', 58.00, 'https://images.unsplash.com/photo-1620799140188-3b2a02fd9a77?q=80&w=600&auto=format&fit=crop', 'Men'),
('Cargo Shorts', 'Jeans', 45.00, 'https://images.unsplash.com/photo-1617114919939-22a860183b16?q=80&w=600&auto=format&fit=crop', 'Men'),
('Canvas Backpack', 'Bags', 85.00, 'https://images.unsplash.com/photo-1553062407-98eeb68c6a62?q=80&w=600&auto=format&fit=crop', 'Men');

-- Kids' Collection (10 Items)
INSERT INTO `products` (`name`, `category`, `price`, `image_url`, `audience`) VALUES
('Kids Dino Hoodie', 'Outerwear', 45.00, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Unicorn Graphic Tee', 'Tops', 19.99, 'https://images.unsplash.com/photo-1527719327859-c6ce80353573?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Children\'s Play Shoes', 'Shoes', 40.00, 'https://images.unsplash.com/photo-1511556532299-8f662fc26c06?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Toddler Rain Boots', 'Shoes', 35.00, 'https://images.unsplash.com/photo-1595995493232-04a4413c9e6e?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Kids Cargo Shorts', 'Jeans', 25.00, 'https://images.unsplash.com/photo-1595462039913-939a456c6496?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Girls Twirl Skirt', 'Skirts', 22.50, 'https://images.unsplash.com/photo-1519340241574-2cec6a12a193?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Boys Striped Polo', 'Tops', 28.00, 'https://images.unsplash.com/photo-1508427953056-b00b8d78ebf5?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Girls Jean Jacket', 'Outerwear', 55.00, 'https://images.unsplash.com/photo-1520939550324-b0411a0ca79f?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Kids Character Backpack', 'Bags', 38.00, 'https://images.unsplash.com/photo-1577043212693-376be802593b?q=80&w=600&auto=format&fit=crop', 'Kids'),
('Sparkly Hair Clips', 'Accessories', 9.99, 'https://images.unsplash.com/photo-1508055659269-537a79a52cca?q=80&w=600&auto=format&fit=crop', 'Kids');

-- Electronics (10 Items)
INSERT INTO `products` (`name`, `category`, `price`, `image_url`, `audience`) VALUES
('Wireless Bluetooth Headphones', 'Electronics', 189.99, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Smart Fitness Tracker', 'Electronics', 59.99, 'https://images.unsplash.com/photo-1526506118085-60ce8714f8c5?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Portable Power Bank', 'Electronics', 29.99, 'https://images.unsplash.com/photo-1588661298533-5b3c34891a9f?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Bluetooth Speaker', 'Electronics', 45.50, 'https://images.unsplash.com/photo-1589256469207-82b3a4a3a693?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Digital Sports Watch', 'Watches', 75.00, 'https://images.unsplash.com/photo-1547996160-24dfa0f62183?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Noise Cancelling Earbuds', 'Electronics', 149.00, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Wireless Charging Pad', 'Electronics', 39.00, 'https://images.unsplash.com/photo-1586816829380-482a47738260?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Smart Home Camera', 'Electronics', 85.00, 'https://images.unsplash.com/photo-1557324232-b8917d3c3dcb?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Minimalist Keyboard', 'Electronics', 65.00, 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Action Sports Camera', 'Electronics', 120.00, 'https://images.unsplash.com/photo-1526444854795-a4b738ede31a?q=80&w=600&auto=format&fit=crop', 'Unisex');

-- Unisex & Additional Items (5 Items)
INSERT INTO `products` (`name`, `category`, `price`, `image_url`, `audience`) VALUES
('Unisex Beanie Hat', 'Accessories', 25.00, 'https://images.unsplash.com/photo-1576871335624-783ef288258a?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Aviator Sunglasses', 'Accessories', 110.00, 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Leather Passport Wallet', 'Accessories', 45.00, 'https://images.unsplash.com/photo-1590156221122-c748b7a7f7ec?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Oversized Scarf', 'Accessories', 32.00, 'https://images.unsplash.com/photo-1520903920243-00d872a2d1c9?q=80&w=600&auto=format&fit=crop', 'Unisex'),
('Canvas Messenger Bag', 'Bags', 68.00, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?q=80&w=600&auto=format&fit=crop', 'Unisex');

-- --------------------------------------------------------

-- 5. Table structure for table `contact_messages`
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `received_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;