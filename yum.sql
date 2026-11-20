-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2025 at 11:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yum`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`) VALUES
(1, 'Biriyani'),
(5, 'Burger'),
(4, 'Chicken'),
(6, 'Fish & Seafood'),
(7, 'Momo'),
(9, 'Nachos'),
(3, 'Pasta'),
(2, 'Pizza'),
(10, 'Ramen'),
(11, 'Sushi'),
(8, 'Tacos');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `full_name`, `email`, `phone`, `subject`, `message`) VALUES
(1, 'Rahim', 'rahim@gmail.com', '01234567891', 'Feedback on website', 'Awesome food website');

-- --------------------------------------------------------

--
-- Table structure for table `cuisines`
--

CREATE TABLE `cuisines` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuisines`
--

INSERT INTO `cuisines` (`id`, `name`) VALUES
(1, 'American'),
(2, 'Bangla'),
(3, 'Chinese'),
(4, 'Indian'),
(5, 'Italian'),
(6, 'Japanese'),
(7, 'Mexican');

-- --------------------------------------------------------

--
-- Table structure for table `food`
--

CREATE TABLE `food` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `cuisine_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food`
--

INSERT INTO `food` (`id`, `name`, `price`, `description`, `image`, `category_id`, `restaurant_id`, `cuisine_id`) VALUES
(6, 'Mutton Biryani', 280.00, 'Tender mutton pieces cooked with aromatic basmati rice and rich spices', 'Screenshot 2025-08-05 004123.jpg', 1, 9, 4),
(7, 'African Periperi', 450.00, 'Spicy Chicken, hearty Mushrooms, tangy Jalapenos, and a perfect Peri Peri Seasoning', 'Screenshot 2025-08-04 135120.jpg', 2, 1, 5),
(8, 'Chick Supremeo', 375.00, 'Texas BBQ Chicken Fillet', 'javier-molina-XXtrOkeAZN0-unsplash.jpg', 5, 2, 1),
(9, 'Crispy Chicken Wings', 699.00, 'Get 8 pc Crispy Chicken Wings', 'pexels-marianasilvestre-2833499.jpg', 4, 10, 1),
(10, 'BBQ Fried Chicken Momo', 150.00, '5 pcs - Tantalizing fusion of tender chicken marinated in smoky bbq sauce, wrapped in a delicate momo shell', 'dim-sum-6902894_1920.jpg', 7, 6, 3),
(11, 'Basmati Mutton Kacchi', 300.00, '1:1 - Prepared with chunks of mutton, basmati rice, potato, blended with spices giving out an amazing flavor', 'Screenshot 2025-08-05 004012.jpg', 1, 3, 2),
(12, 'Fried Camarons Tacos', 585.00, '1:3 - Chipotle fried shrimp, green chili, habanero salsa & grilled pineapple', 'Screenshot 2025-08-04 141449(1).jpg', 8, 12, 7),
(13, 'Nachos', 255.00, ' Loaded with melted cheese & chicken', 'erick-su-WjBp05j8LXI-unsplash.jpg', 9, 12, 7),
(14, 'Japanese Miso Ramen', 300.00, 'A bowl of pure comfort – rich broth, tender noodles, unforgettable taste.', 'stephen-bedase-WjdOYhgTGCM-unsplash.jpg', 10, 8, 6),
(15, 'Spicy Tuna Roll', 1060.00, 'Tender tuna mixed with a spicy kick, rolled with cucumber and rice for bold flavor.', 'derek-duran-Jz4QMhLvGgw-unsplash.jpg', 11, 8, 6),
(16, 'Chicken Biriyani', 190.00, 'Perfectly spiced chicken and fluffy rice cooked to perfection', 'Screenshot 2025-08-05 004317.jpg', 1, 9, 4),
(17, 'Fried Pomfret', 250.00, 'Juicy on the inside, crispy on the outside – a seafood lover’s delight', 'ezgif-4c7830de4d39a0.jpg', 6, 13, 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `amount` decimal(10,2) NOT NULL CHECK (`amount` >= 0),
  `status` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `food_id`, `quantity`, `amount`, `status`, `created_at`) VALUES
(1, 6, 6, 4, 1120.00, 'Pending', '2025-08-17 02:46:44'),
(2, 6, 7, 2, 900.00, 'Pending', '2025-08-17 02:46:44'),
(3, 9, 7, 1, 450.00, 'Pending', '2025-08-18 19:42:30'),
(4, 6, 6, 2, 560.00, 'Completed', '2025-08-18 23:29:56');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`) VALUES
(2, 'Chomok'),
(11, 'Club Grille'),
(6, 'Friolento Restaurant & Cafe'),
(3, 'Kacchi Express'),
(10, 'KFC'),
(13, 'La Gondola Restaurant BD'),
(9, 'Ladhidh'),
(12, 'MexiGo BD'),
(7, 'Omerta'),
(5, 'Pizza Hut'),
(1, 'Pizzaburg'),
(4, 'Snackza'),
(8, 'Wabi Sabi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(128) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `role` varchar(50) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `phone`, `email`, `password`, `address`, `role`) VALUES
(6, 'Kalpita Chakraborty', '01234567891', 'kalpita@gmail.com', '$2y$10$SuwN8e1LuRl3sRSJAZT/JuiQe/ISE8zvpXFmQfzEPg0sw8wzIBz82', '16/B East Nasirabad, Chattogram', 'user'),
(7, '', '', 'admin@gmail.com', 'admin1234', '', 'admin'),
(9, 'Pranta Das', '01789456214', 'pranta@gmail.com', '$2y$10$Cb/ly3hVQxx1s8RRySUfpuN/7FuD7oQ0uW1kDa.eIQLrdxlBspTSC', 'Rahamatganj, Chattogram', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cuisines`
--
ALTER TABLE `cuisines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `cuisine_id` (`cuisine_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cuisines`
--
ALTER TABLE `cuisines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `food`
--
ALTER TABLE `food`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `food`
--
ALTER TABLE `food`
  ADD CONSTRAINT `food_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `food_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `food_ibfk_3` FOREIGN KEY (`cuisine_id`) REFERENCES `cuisines` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


ALTER TABLE restaurants
ADD COLUMN address VARCHAR(255) NOT NULL AFTER name;
UPDATE restaurants SET address = 'Chawkbazar, Chattogram' WHERE id = 2;   -- Chomok
UPDATE restaurants SET address = 'Khulshi, Chattogram' WHERE id = 11;  -- Club Grille
UPDATE restaurants SET address = 'Naval, Chattogram' WHERE id = 6;   -- Friolento Restaurant & Cafe
UPDATE restaurants SET address = 'CDA, Chattogram' WHERE id = 3;   -- Kacchi Express
UPDATE restaurants SET address = 'GEC, Chattogram' WHERE id = 10;  -- KFC
UPDATE restaurants SET address = 'Baddarhat, Chattogram' WHERE id = 13;  -- La Gondola Restaurant BD
UPDATE restaurants SET address = 'Chatteshwari, Chattogram' WHERE id = 9;   -- Ladhidh
UPDATE restaurants SET address = 'LalChand, Chattogram' WHERE id = 12;  -- MexiGo BD
UPDATE restaurants SET address = 'CDA Ave, Chattogram' WHERE id = 7;   -- Omerta
UPDATE restaurants SET address = 'GEC Circle, Chattogram' WHERE id = 5;   -- Pizza Hut
UPDATE restaurants SET address = 'Halishahar, Chattogram' WHERE id = 1;   -- Pizzaburg
UPDATE restaurants SET address = 'Bayezid, Chattogram' WHERE id = 4;   -- Snackza
UPDATE restaurants SET address = 'Khulshi Rd, Chattogram' WHERE id = 8;   -- Wabi Sabi;

