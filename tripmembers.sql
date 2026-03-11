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
-- Table structure for table `tripmembers`
--

CREATE TABLE `tripmembers` (
  `tripmembersid` int(11) NOT NULL,
  `role` varchar(16) NOT NULL DEFAULT 'viewer',
  `joindate` date NOT NULL,
  `tripid` int(11) NOT NULL COMMENT 'foreign key from trips',
  `userid` int(11) NOT NULL COMMENT 'foreign key from users'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tripmembers`
--

INSERT INTO `tripmembers` (`tripmembersid`, `role`, `joindate`, `tripid`, `userid`) VALUES
(1, 'viewer', '2026-03-11', 14, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tripmembers`
--
ALTER TABLE `tripmembers`
  ADD PRIMARY KEY (`tripmembersid`),
  ADD KEY `tripid` (`tripid`),
  ADD KEY `userid` (`userid`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tripmembers`
--
ALTER TABLE `tripmembers`
  MODIFY `tripmembersid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tripmembers`
--
ALTER TABLE `tripmembers`
  ADD CONSTRAINT `tripidFromTripstoTripmembers` FOREIGN KEY (`tripid`) REFERENCES `trips` (`tripid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `useridFromUserstoTripMembers` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
