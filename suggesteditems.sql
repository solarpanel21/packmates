-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 09:54 PM
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
-- Database: `packmatestest`
--

-- --------------------------------------------------------

--
-- Table structure for table `suggesteditems`
--

CREATE TABLE `suggesteditems` (
  `itemid` int(11) NOT NULL,
  `itemname` varchar(255) NOT NULL,
  `category` varchar(32) NOT NULL,
  `weathertag` varchar(16) DEFAULT NULL,
  `activitytag` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suggesteditems`
--

INSERT INTO `suggesteditems` (`itemid`, `itemname`, `category`, `weathertag`, `activitytag`) VALUES
(1, 'Passport', 'Essentials', NULL, NULL),
(2, 'ID', 'Essentials', NULL, NULL),
(3, 'Cash', 'Essentials', NULL, NULL),
(4, 'Credit Card', 'Essentials', NULL, NULL),
(5, 'Phone', 'Essentials', NULL, NULL),
(6, 'Charger', 'Essentials', NULL, NULL),
(7, 'Headphones', 'Essentials', NULL, NULL),
(8, 'Power Bank', 'Essentials', NULL, NULL),
(9, 'Power Adapter', 'Essentials', NULL, NULL),
(10, 'Keys', 'Essentials', NULL, NULL),
(11, 'Toothbrush', 'Toiletries', NULL, NULL),
(12, 'Toothpaste', 'Toiletries', NULL, NULL),
(13, 'Shampoo', 'Toiletries', NULL, NULL),
(14, 'Conditioner', 'Toiletries', NULL, NULL),
(15, 'Body Wash', 'Toiletries', NULL, NULL),
(16, 'Deodorant', 'Toiletries', NULL, NULL),
(17, 'Razor', 'Toiletries', NULL, NULL),
(18, 'Moisturizer', 'Toiletries', NULL, NULL),
(19, 'Sunscreen', 'Toiletries', NULL, NULL),
(20, 'Lip Balm', 'Toiletries', NULL, NULL),
(21, 'Hairbrush', 'Toiletries', NULL, NULL),
(22, 'Comb', 'Toiletries', NULL, NULL),
(23, 'T-Shirts', 'Clothing', NULL, NULL),
(24, 'Tank Tops', 'Clothing', 'warm', NULL),
(25, 'Long Sleeve Shirts', 'Clothing', 'cold', NULL),
(26, 'Button-Down Shirts', 'Clothing', NULL, 'formal'),
(27, 'Polo Shirts', 'Clothing', 'warm', NULL),
(28, 'Jacket', 'Clothing', 'cold', NULL),
(29, 'Sweater', 'Clothing', 'cold', NULL),
(30, 'Jeans', 'Clothing', 'cold', NULL),
(31, 'Pants', 'Clothing', NULL, NULL),
(32, 'Sweatpants', 'Clothing', 'cold', NULL),
(33, 'Leggings', 'Clothing', 'cold', NULL),
(34, 'Shorts', 'Clothing', 'warm', NULL),
(35, 'Skirt', 'Clothing', 'warm', NULL),
(36, 'Dress', 'Clothing', NULL, 'formal'),
(37, 'Raincoat', 'Clothing', 'rain', NULL),
(38, 'Undergarments', 'Clothing', NULL, NULL),
(39, 'Socks', 'Clothing', NULL, NULL),
(40, 'Compression Socks', 'Clothing', NULL, 'sport'),
(41, 'Pajamas', 'Clothing', NULL, NULL),
(42, 'Scarf', 'Clothing', 'cold', NULL),
(43, 'Gloves', 'Clothing', 'cold', NULL),
(44, 'Beanie', 'Clothing', 'cold', NULL),
(45, 'Sneakers', 'Shoes', NULL, NULL),
(46, 'Running Shoes', 'Shoes', NULL, 'sport'),
(47, 'Sandals', 'Shoes', 'warm', NULL),
(48, 'Flip Flops', 'Shoes', 'warm', NULL),
(49, 'Loafers', 'Shoes', NULL, 'business'),
(50, 'Boots', 'Shoes', 'cold', NULL),
(51, 'Heels', 'Shoes', NULL, 'formal'),
(52, 'Dress Shoes', 'Shoes', NULL, 'formal'),
(53, 'Laptop', 'Essentials', NULL, NULL),
(54, 'Laptop Charger', 'Essentials', NULL, NULL),
(55, 'Camera', 'Misc', NULL, NULL),
(56, 'Dress Shirts', 'Clothing', NULL, 'business'),
(57, 'Dress Pants', 'Clothing', NULL, 'business'),
(58, 'Blazer', 'Clothing', NULL, 'business'),
(59, 'Tie', 'Clothing', NULL, 'business'),
(60, 'Business Cards', 'Clothing', NULL, 'business'),
(61, 'Notebook', 'Misc', NULL, 'business'),
(62, 'Workout Clothes', 'Gym', NULL, 'gym'),
(63, 'Gym Towel', 'Gym', NULL, 'gym'),
(64, 'Water Bottle', 'Gym', NULL, 'gym'),
(65, 'Gym Towel', 'Gym', NULL, 'gym'),
(66, 'Towel', 'Essentials', NULL, NULL),
(67, 'Swimsuit', 'Swimming', NULL, 'swimming'),
(68, 'Goggles', 'Swimming', NULL, 'swimming'),
(69, 'Swim Cap', 'Swimming', NULL, 'swimming'),
(70, 'Beach Towel', 'Swimming', NULL, 'beach'),
(71, 'Sunglasses', 'Swimming', NULL, 'beach'),
(72, 'Sun Hat', 'Swimming', NULL, 'beach'),
(73, 'Sunscreen', 'Swimming', NULL, 'beach'),
(74, 'Beach Bag', 'Swimming', NULL, 'beach'),
(75, 'Thermal Base Layer', 'Winter Sports', 'cold', 'sport'),
(76, 'Ski Jacket', 'Winter Sports', NULL, 'wintersports'),
(77, 'Ski Pants', 'Winter Sports', NULL, 'wintersports'),
(78, 'Snow Goggles', 'Winter Sports', 'cold', 'wintersports'),
(79, 'Wool Socks', 'Clothing', 'cold', NULL),
(80, 'Ski Boots', 'Winter Sports', NULL, 'wintersports'),
(81, 'Hiking Boots', 'Hiking', NULL, 'hiking'),
(82, 'Hiking Socks', 'Hiking', NULL, 'hiking'),
(83, 'Backpack', 'Hiking', NULL, 'hiking'),
(84, 'Insect Repellent', 'Misc', 'warm', 'sport'),
(85, 'Tent', 'Camping', NULL, 'camping'),
(86, 'Sleeping Bag', 'Camping', NULL, 'camping'),
(87, 'Sleeping Pad', 'Camping', NULL, 'camping'),
(88, 'Camp Stove (No Fuel)', 'Camping', NULL, 'camping'),
(89, 'Sunscreen', 'Misc', 'warm', NULL),
(90, 'Reusable Water Bottle', 'Misc', 'warm', NULL),
(91, 'Hand Warmers', 'Misc', 'cold', NULL),
(92, 'Lip Balm', 'Misc', 'wind', NULL),
(93, 'Windbreaker Jacket', 'Clothing', 'wind', NULL),
(94, 'Hair Ties', 'Clothing', 'wind', NULL),
(95, 'Earmuffs', 'Clothing', 'wind', NULL),
(97, 'Makeup Kit', 'Misc', NULL, 'nightout'),
(98, 'Perfume/Cologne', 'Misc', NULL, 'nightout'),
(99, 'Car Charger', 'Essentials', NULL, 'roadtrip'),
(100, 'Travel Pillow', 'Misc', NULL, 'roadtrip'),
(101, 'Car Sunshade', 'Misc', NULL, 'roadtrip'),
(102, 'Makeup Wipes', 'Toiletries', NULL, 'nightout');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `suggesteditems`
--
ALTER TABLE `suggesteditems`
  ADD PRIMARY KEY (`itemid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `suggesteditems`
--
ALTER TABLE `suggesteditems`
  MODIFY `itemid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
