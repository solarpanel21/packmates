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
-- Table structure for table `tripitems`
--

CREATE TABLE `tripitems` (
  `id` int(11) NOT NULL,
  `ischecked` tinyint(1) NOT NULL,
  `quantity` tinyint(4) UNSIGNED NOT NULL DEFAULT 1,
  `tripid` int(11) NOT NULL COMMENT 'foreign key from trips',
  `itemid` int(11) NOT NULL COMMENT 'foreign key from suggested items'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tripitems`
--
ALTER TABLE `tripitems`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tripid` (`tripid`),
  ADD KEY `itemid` (`itemid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tripitems`
--
ALTER TABLE `tripitems`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tripitems`
--
ALTER TABLE `tripitems`
  ADD CONSTRAINT `itemidFromSuggesteditemstoTripitems` FOREIGN KEY (`itemid`) REFERENCES `suggesteditems` (`itemid`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tripidFromTripstoTripitems` FOREIGN KEY (`tripid`) REFERENCES `trips` (`tripid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
