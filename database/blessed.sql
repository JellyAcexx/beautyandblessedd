-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2026 at 05:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u937180775_bblessed_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_forgot_tb`
--

CREATE TABLE `admin_forgot_tb` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_list_login_tb`
--

CREATE TABLE `admin_list_login_tb` (
  `ID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Login_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_tb`
--

CREATE TABLE `admin_login_tb` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_TnD` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login_tb`
--

INSERT INTO `admin_login_tb` (`admin_id`, `email`, `password`, `login_TnD`) VALUES
(1, 'beautyandblessed2@gmail.com', '$2y$10$1BfEA69CAvu50eH6bKls.OcwvpdBaluBhUlTmfU4KA8nT49sL.cwu', '2025-09-26 06:51:59');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `task_performed` text NOT NULL,
  `date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `login_id` int(11) NOT NULL,
  `total` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_items_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `add_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Perfumes'),
(2, 'Sunscreens'),
(3, 'Concealer'),
(4, 'Blush'),
(5, 'Nails'),
(6, 'Body Soaps'),
(7, 'Contact Lenses'),
(8, 'Hair Oil'),
(9, 'Palette'),
(10, 'Brush'),
(11, 'Ointments'),
(15, 'Sample'),
(17, 'Gell');

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password_tb`
--

CREATE TABLE `forgot_password_tb` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stocks` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_tb`
--

CREATE TABLE `login_tb` (
  `login_id` int(11) NOT NULL,
  `register_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_history_tb`
--

CREATE TABLE `log_history_tb` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Login','Logout') NOT NULL,
  `dateandtime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_history_tb`
--

INSERT INTO `log_history_tb` (`id`, `email`, `password`, `status`, `dateandtime`) VALUES
(1, 'beautyandblessed2@gmail.com', '$2y$10$1BfEA69CAvu50eH6bKls.OcwvpdBaluBhUlTmfU4KA8nT49sL.cwu', 'Login', '2026-05-03 00:26:46'),
(2, 'beautyandblessed2@gmail.com', '$2y$10$1BfEA69CAvu50eH6bKls.OcwvpdBaluBhUlTmfU4KA8nT49sL.cwu', 'Login', '2026-05-22 02:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `notifadmin`
--

CREATE TABLE `notifadmin` (
  `notif_id` int(11) NOT NULL,
  `register_id` int(11) NOT NULL,
  `notif_message` text NOT NULL,
  `notif_type` varchar(50) NOT NULL,
  `notif_link` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifcustomer`
--

CREATE TABLE `notifcustomer` (
  `notif_id` int(11) NOT NULL,
  `register_id` int(11) NOT NULL,
  `notif_message` text NOT NULL,
  `notif_type` varchar(50) NOT NULL,
  `notif_link` varchar(255) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(10) UNSIGNED NOT NULL,
  `register_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `cart_items_id` int(10) UNSIGNED DEFAULT NULL,
  `reservation_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `price`, `image_path`) VALUES
(1, 1, 'Victoria Secret – Romantic', 650.00, 'romantic.jpg'),
(2, 1, 'Victoria Secret – Love Spell', 800.00, 'pictures/perfume/lovespell.jpg'),
(3, 1, 'Victoria Secret – Pure Seduction', 800.00, 'seduction.jpg'),
(4, 1, 'Victoria Secret – Love Spell Shimmer', 900.00, 'pictures/perfume/shimmer.jpg'),
(5, 2, 'Karite Sunscreen SFP50 Waterproof', 250.00, 'pictures/suncreen/waterproof.jpg'),
(6, 2, 'Karite 2 in 1 Nourish Skin IV Protective SFP50', 280.00, 'pictures/suncreen/nourish.jpg'),
(7, 2, 'Whitening Sunscreen Lotion SFP50', 300.00, 'pictures/suncreen/whitening.jpg'),
(8, 2, 'Disaar Sunscreen Bronz foundation SFP50', 320.00, 'pictures/suncreen/disaar.jpg'),
(9, 2, 'Goree Day and Night Beauty Cream', 200.00, 'pictures/suncreen/goree.jpg'),
(10, 3, 'PRO Concealer Cream', 180.00, 'pictures/concealer/cream.jpg'),
(11, 3, 'PRO Concealer Peach', 180.00, 'pictures/concealer/peach.jpg'),
(12, 3, 'PRO Concealer Green', 180.00, 'pictures/concealer/green.jpg'),
(13, 3, 'PRO Concealer Yellow', 180.00, 'pictures/concealer/yellow.jpg'),
(14, 3, 'PRO Concealer Brown', 180.00, 'pictures/concealer/brown.jpg'),
(15, 4, 'Kiss Beauty – Lips and Cheeks Color Change', 170.00, 'pictures/blushon/colorchange.jpg'),
(16, 4, 'Kiss Beauty – Blush Sweet', 160.00, 'pictures/blushon/blushsweet.jpg'),
(17, 4, 'Kiss Beauty – Magic Blusher', 170.00, 'pictures/blushon/magic.jpg'),
(18, 5, 'NXS01', 120.00, 'pictures/nails/nx01.jpg'),
(19, 5, 'NXS02', 120.00, 'pictures/nails/nx02.jpg'),
(20, 5, 'NXS03', 120.00, 'pictures/nails/nx03.jpg'),
(21, 5, 'NXS04', 120.00, 'pictures/nails/nx04.jpg'),
(22, 5, 'NXS05', 120.00, 'pictures/nails/nx05.jpg'),
(23, 6, 'Safi – Mencerah and Menghidrat', 200.00, 'pictures/soap/mencerah.jpg'),
(24, 6, 'Safi – Melembut and Menyegar', 200.00, 'pictures/soap/melembut.jpg'),
(25, 6, 'Safi – Anti Jerawat', 220.00, 'pictures/soap/antijewart.jpg'),
(26, 6, 'Safi – Mencerah and Menyari', 220.00, 'pictures/soap/menyari.jpg'),
(27, 6, 'Safi – Mengawal Minyak and Mencera', 220.00, 'pictures/soap/mengawal.jpg'),
(28, 7, 'Girl Contact Lenses Sterile (Brown Color)', 300.00, 'pictures/lense/brown.jpg'),
(29, 7, 'Girl Contact Lenses Sterile (Barbie Eye)', 300.00, 'pictures/lense/barbieye.jpg'),
(30, 7, 'Girl Contact Lenses Sterile (Gray Color)', 300.00, 'pictures/lense/gray.jpg'),
(31, 7, 'Girl Contact Lenses Sterile (Cloud Gray)', 320.00, 'pictures/lense/cloud.jpg'),
(32, 7, 'Girl Contact Lenses Sterile (Star Brown)', 320.00, 'pictures/lense/star.jpg'),
(33, 8, 'Kenspeckle – HSE01', 200.00, 'pictures/haircare/hse01.jpg'),
(34, 8, 'Kenspeckle – HSE02', 200.00, 'pictures/haircare/hse02.jpg'),
(35, 8, 'Kenspeckle – HSE03', 200.00, 'pictures/haircare/hse03.jpg'),
(36, 8, 'Kenspeckle – HSE04', 200.00, 'pictures/haircare/hse04.jpg'),
(37, 8, 'Kenspeckle – HSE05', 200.00, 'pictures/haircare/hse05.jpg'),
(38, 9, 'Kiss Beauty – Peach Blossom', 350.00, 'pictures/palette/peach.png'),
(39, 9, 'S.f.r color Glitter', 380.00, 'pictures/palette/glitter.png'),
(40, 9, 'S.f.r Wild Jaguar', 380.00, 'pictures/palette/wild.jpg'),
(41, 9, 'Kiss Beauty 4in1 Baked Highlight', 400.00, 'pictures/palette/baked.jpg'),
(42, 9, 'Glow Palette Limited Edition', 450.00, 'pictures/palette/limited.jpg'),
(43, 10, 'Pink Key – Beautify your beauty', 280.00, 'pictures/brush/pinkkey.jpg'),
(44, 10, 'Sweet Beauty – Spongebob', 300.00, 'pictures/brush/spongebob.jpg'),
(45, 10, 'Sweet Beauty – Beauty Tools', 300.00, 'pictures/brush/beauty.jpg'),
(46, 10, 'GR New Make up brush Set', 350.00, 'pictures/brush/newmake.jpg'),
(47, 10, 'Mermaid Like Brushes', 400.00, 'pictures/brush/mermaid.jpg'),
(48, 11, 'Minyak Terapi', 150.00, 'pictures/panghilot/minyak.jpg'),
(49, 11, 'Arima Theraphy Lemon', 120.00, 'pictures/panghilot/aroma.jpg'),
(50, 11, 'Chiom Chengki Lemon', 160.00, 'pictures/panghilot/chiom.jpg'),
(51, 11, 'Kwan Loong Medicated Oil', 180.00, 'kwanloong.jpg'),
(55, 3, 'PRO Concealer Cream', 120.00, ''),
(57, 17, 'sample', 120.00, '1764569507_Screenshot 2025-12-01 004149.png');

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `purchase_id` int(11) NOT NULL,
  `walk_in_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `purchaseMethod` varchar(225) NOT NULL,
  `purchaseDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `purchase_item_id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registers_tb`
--

CREATE TABLE `registers_tb` (
  `register_id` int(11) NOT NULL,
  `register_fname` varchar(255) NOT NULL,
  `register_lname` varchar(255) NOT NULL,
  `register_email` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `register_password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `register_id` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `reservation_date` date NOT NULL,
  `pickup_date` date DEFAULT NULL,
  `date_picked_up` date DEFAULT NULL,
  `cancel_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_items`
--

CREATE TABLE `reservation_items` (
  `reservation_item_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `walk_in`
--

CREATE TABLE `walk_in` (
  `walk_in_id` int(11) NOT NULL,
  `walk_in_name` varchar(255) DEFAULT NULL,
  `total` int(11) NOT NULL,
  `walk_in_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `walk_in_items`
--

CREATE TABLE `walk_in_items` (
  `walk_in_item_id` int(11) NOT NULL,
  `walk_in_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_forgot_tb`
--
ALTER TABLE `admin_forgot_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_list_login_tb`
--
ALTER TABLE `admin_list_login_tb`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `admin_login_tb`
--
ALTER TABLE `admin_login_tb`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_items_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `forgot_password_tb`
--
ALTER TABLE `forgot_password_tb`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_register` (`user_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `login_tb`
--
ALTER TABLE `login_tb`
  ADD PRIMARY KEY (`login_id`);

--
-- Indexes for table `log_history_tb`
--
ALTER TABLE `log_history_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifadmin`
--
ALTER TABLE `notifadmin`
  ADD PRIMARY KEY (`notif_id`);

--
-- Indexes for table `notifcustomer`
--
ALTER TABLE `notifcustomer`
  ADD PRIMARY KEY (`notif_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`purchase_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`purchase_item_id`);

--
-- Indexes for table `registers_tb`
--
ALTER TABLE `registers_tb`
  ADD PRIMARY KEY (`register_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD PRIMARY KEY (`reservation_item_id`);

--
-- Indexes for table `walk_in`
--
ALTER TABLE `walk_in`
  ADD PRIMARY KEY (`walk_in_id`);

--
-- Indexes for table `walk_in_items`
--
ALTER TABLE `walk_in_items`
  ADD PRIMARY KEY (`walk_in_item_id`),
  ADD KEY `walk_in_id` (`walk_in_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_forgot_tb`
--
ALTER TABLE `admin_forgot_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_list_login_tb`
--
ALTER TABLE `admin_list_login_tb`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_login_tb`
--
ALTER TABLE `admin_login_tb`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_items_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `forgot_password_tb`
--
ALTER TABLE `forgot_password_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_tb`
--
ALTER TABLE `login_tb`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_history_tb`
--
ALTER TABLE `log_history_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifadmin`
--
ALTER TABLE `notifadmin`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifcustomer`
--
ALTER TABLE `notifcustomer`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `purchase_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registers_tb`
--
ALTER TABLE `registers_tb`
  MODIFY `register_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation_items`
--
ALTER TABLE `reservation_items`
  MODIFY `reservation_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `walk_in`
--
ALTER TABLE `walk_in`
  MODIFY `walk_in_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `walk_in_items`
--
ALTER TABLE `walk_in_items`
  MODIFY `walk_in_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
