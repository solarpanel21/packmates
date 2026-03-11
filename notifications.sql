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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notifid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `tripid` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `isread` tinyint(1) NOT NULL DEFAULT 0,
  `createdat` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notifid`, `userid`, `tripid`, `message`, `isread`, `createdat`) VALUES
(1, 2, 15, 'Your trip \'SUPER CLOSE TRIP TOMORROW\' starts in less than 24 hours!', 1, '2026-03-11 19:48:23'),
(2, 2, 16, 'Your trip \'SUPER CLOSE TRIP TOMORROW\' starts in less than 24 hours!', 1, '2026-03-11 19:48:23'),
(3, 2, 17, 'Your trip \'SUPER CLOSE TRIP TOMORROW\' starts in less than 24 hours!', 1, '2026-03-11 19:48:23'),
(4, 2, 18, 'Your trip \'SUPER CLOSE TRIP TOMORROW\' starts in less than 24 hours!', 1, '2026-03-11 19:48:23'),
(5, 1, 19, 'Your trip \'trip tomorrow\' starts in less than 24 hours!', 1, '2026-03-11 20:03:03'),
(6, 3, 24, 'Your trip \'Burlington is such a cool place\' starts in less than 24 hours!', 0, '2026-03-11 21:22:12'),
(7, 3, 25, 'Your trip \'Burlington is such a cool place\' is coming up in less than a week!', 0, '2026-03-11 21:22:39'),
(8, 1, 26, 'Your trip \'Going to Palembang\' starts in less than 24 hours!', 1, '2026-03-11 21:47:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notifid`),
  ADD KEY `userid` (`userid`),
  ADD KEY `tripid` (`tripid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notifid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notif_tripid` FOREIGN KEY (`tripid`) REFERENCES `trips` (`tripid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notif_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
