-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2026 at 06:33 PM
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
(1, 'Passport', 'Essentials', 'null', 'null'),
(2, 'ID', 'Essentials', 'null', 'null'),
(3, 'Cash', 'Essentials', 'null', 'null'),
(4, 'Credit Card', 'Essentials', 'null', 'null'),
(5, 'Phone', 'Essentials', 'null', 'null'),
(6, 'Charger', 'Essentials', 'null', 'null'),
(7, 'Headphones', 'Essentials', 'null', 'null'),
(8, 'Power Bank', 'Essentials', 'null', 'null'),
(9, 'Power Adapter', 'Essentials', 'null', 'null'),
(10, 'Keys', 'Essentials', 'null', 'null'),
(11, 'Toothbrush', 'Toiletries', 'null', 'null'),
(12, 'Toothpaste', 'Toiletries', 'null', 'null'),
(13, 'Shampoo', 'Toiletries', 'null', 'null'),
(14, 'Conditioner', 'Toiletries', 'null', 'null'),
(15, 'Body Wash', 'Toiletries', 'null', 'null'),
(16, 'Deodorant', 'Toiletries', 'null', 'null'),
(17, 'Razor', 'Toiletries', 'null', 'null'),
(18, 'Moisturizer', 'Toiletries', 'null', 'null'),
(19, 'Sunscreen', 'Toiletries', 'null', 'null'),
(20, 'Lip Balm', 'Toiletries', 'null', 'null'),
(21, 'Hairbrush', 'Toiletries', 'null', 'null'),
(22, 'Comb', 'Toiletries', 'null', 'null'),
(23, 'T-Shirts', 'Clothing', 'null', 'null'),
(24, 'Tank Tops', 'Clothing', 'warm', 'null'),
(25, 'Long Sleeve Shirts', 'Clothing', 'cold', 'null'),
(26, 'Button-Down Shirts', 'Clothing', 'null', 'formal'),
(27, 'Polo Shirts', 'Clothing', 'warm', 'null'),
(28, 'Jacket', 'Clothing', 'cold', 'null'),
(29, 'Sweater', 'Clothing', 'cold', 'null'),
(30, 'Jeans', 'Clothing', 'cold', 'null'),
(31, 'Pants', 'Clothing', 'null', 'null'),
(32, 'Sweatpants', 'Clothing', 'cold', 'null'),
(33, 'Leggings', 'Clothing', 'cold', 'null'),
(34, 'Shorts', 'Clothing', 'warm', 'null'),
(35, 'Skirt', 'Clothing', 'warm', 'null'),
(36, 'Dress', 'Clothing', 'null', 'formal'),
(37, 'Raincoat', 'Clothing', 'rain', 'null'),
(38, 'Undergarments', 'Clothing', 'null', 'null'),
(39, 'Socks', 'Clothing', 'null', 'null'),
(40, 'Compression Socks', 'Clothing', 'null', 'sport'),
(41, 'Pajamas', 'Clothing', 'null', 'null'),
(42, 'Scarf', 'Clothing', 'cold', 'null'),
(43, 'Gloves', 'Clothing', 'cold', 'null'),
(44, 'Beanie', 'Clothing', 'cold', 'null'),
(45, 'Sneakers', 'Shoes', 'null', 'null'),
(46, 'Running Shoes', 'Shoes', 'null', 'sport'),
(47, 'Sandals', 'Shoes', 'warm', 'null'),
(48, 'Flip Flops', 'Shoes', 'warm', 'null'),
(49, 'Loafers', 'Shoes', 'null', 'business'),
(50, 'Boots', 'Shoes', 'cold', 'null'),
(51, 'Heels', 'Shoes', 'null', 'formal'),
(52, 'Dress Shoes', 'Shoes', 'null', 'formal'),
(53, 'Laptop', 'Essentials', 'null', 'null'),
(54, 'Laptop Charger', 'Essentials', 'null', 'null'),
(55, 'Camera', 'Misc', 'null', 'null'),
(56, 'Dress Shirts', 'Clothing', 'null', 'business'),
(57, 'Dress Pants', 'Clothing', 'null', 'business'),
(58, 'Blazer', 'Clothing', 'null', 'business'),
(59, 'Tie', 'Clothing', 'null', 'business'),
(60, 'Business Cards', 'Clothing', 'null', 'business'),
(61, 'Notebook', 'Misc', 'null', 'business'),
(62, 'Workout Clothes', 'Gym', 'null', 'gym'),
(63, 'Gym Towel', 'Gym', 'null', 'gym'),
(64, 'Water Bottle', 'Gym', 'null', 'gym'),
(65, 'Gym Towel', 'Gym', 'null', 'gym'),
(66, 'Towel', 'Essentials', 'null', 'null'),
(67, 'Swimsuit', 'Swimming', 'null', 'swimming'),
(68, 'Goggles', 'Swimming', 'null', 'swimming'),
(69, 'Swim Cap', 'Swimming', 'null', 'swimming'),
(70, 'Beach Towel', 'Swimming', 'null', 'beach'),
(71, 'Sunglasses', 'Swimming', 'null', 'beach'),
(72, 'Sun Hat', 'Swimming', 'null', 'beach'),
(73, 'Sunscreen', 'Swimming', 'null', 'beach'),
(74, 'Beach Bag', 'Swimming', 'null', 'beach'),
(75, 'Thermal Base Layer', 'Cold Weather Sports', 'cold', 'sport'),
(76, 'Ski Jacket', 'Cold Weather Sports', 'null', 'skiing'),
(77, 'Ski Pants', 'Cold Weather Sports', 'null', 'skiing'),
(78, 'Snow Goggles', 'Cold Weather Sports', 'cold', 'skiing'),
(79, 'Wool Socks', 'Clothing', 'cold', 'null'),
(80, 'Ski Boots', 'Cold Weather Sports', 'null', 'skiing'),
(81, 'Hiking Boots', 'Hiking', 'null', 'hiking'),
(82, 'Hiking Socks', 'Hiking', 'null', 'hiking'),
(83, 'Backpack', 'Hiking', 'null', 'hiking'),
(84, 'Insect Repellent', 'Misc', 'warm', 'sport'),
(85, 'Tent', 'Camping', 'null', 'camping'),
(86, 'Sleeping Bag', 'Camping', 'null', 'camping'),
(87, 'Sleeping Pad', 'Camping', 'null', 'camping'),
(88, 'Camp Stove (No Fuel)', 'Camping', 'null', 'camping'),
(89, 'Sunscreen', 'Misc', 'warm', 'null'),
(90, 'Reusable Water Bottle', 'Misc', 'warm', 'null'),
(91, 'Hand Warmers', 'misc', 'cold', 'null'),
(92, 'Lip Balm', 'Misc', 'wind', 'null'),
(93, 'Windbreaker Jacket', 'Clothing', 'wind', 'null'),
(94, 'Hair Ties', 'Clothing', 'wind', 'null'),
(95, 'Earmuffs', 'Clothing', 'wind', 'null'),
(96, 'Face Mask (Poor Air Quality)', 'Misc', 'airquality', 'null');

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
  MODIFY `itemid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
