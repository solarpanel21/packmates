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
-- Table structure for table `customitems`
--

CREATE TABLE `customitems` (
  `customid` int(11) NOT NULL,
  `customname` varchar(32) NOT NULL,
  `timecreated` datetime NOT NULL,
  `userid` int(11) NOT NULL COMMENT 'foreign key from users (id of user who made the item)',
  `isdismissed` tinyint(4) DEFAULT 0,
  `quantity` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `tripid` int(11) NOT NULL DEFAULT 0,
  `ischecked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customitems`
--

INSERT INTO `customitems` (`customid`, `customname`, `timecreated`, `userid`, `isdismissed`, `quantity`, `tripid`, `ischecked`) VALUES
(1, 'Nintendo Switch', '2026-03-11 16:24:51', 1, 0, 1, 14, 0),
(2, 'bluetooth keyboard', '2026-03-11 18:09:17', 1, 0, 1, 14, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customitems`
--
ALTER TABLE `customitems`
  ADD PRIMARY KEY (`customid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `TripidFromTripsToCustomitems` (`tripid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customitems`
--
ALTER TABLE `customitems`
  MODIFY `customid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customitems`
--
ALTER TABLE `customitems`
  ADD CONSTRAINT `TripidFromTripsToCustomitems` FOREIGN KEY (`tripid`) REFERENCES `trips` (`tripid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `UseridFromUsersToCustomitems` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
