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
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `tripid` int(16) NOT NULL,
  `tripname` varchar(32) NOT NULL DEFAULT 'New Trip',
  `city` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `country` varchar(64) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `creationdate` datetime NOT NULL,
  `iconurl` varchar(255) DEFAULT NULL,
  `userid` int(11) NOT NULL COMMENT 'foreign key, takes user id from users table',
  `weathertags` varchar(255) DEFAULT NULL,
  `activitytags` varchar(255) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `isdeleted` tinyint(4) DEFAULT 0,
  `notified_24h` tinyint(1) NOT NULL DEFAULT 0,
  `notified_7d` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`tripid`, `tripname`, `city`, `country`, `startdate`, `enddate`, `creationdate`, `iconurl`, `userid`, `weathertags`, `activitytags`, `notes`, `latitude`, `longitude`, `isdeleted`, `notified_24h`, `notified_7d`) VALUES
(1, 'July NYC Trip', 'New York', 'United States', '2026-07-10', '2026-07-23', '2026-03-10 16:47:00', NULL, 3, 'cold,rain,snow', 'business', 'notes', 40.714270, -74.005970, 1, 0, 0),
(2, 'NYC July trip 2', 'New York', 'United States', '2026-07-10', '2026-07-23', '2026-03-10 16:58:56', NULL, 3, 'warm,cold,rain', 'business', 'business trip to nyc', 40.714270, -74.005970, 1, 0, 0),
(3, 'Trip to Hell (michigan)', 'Hell', 'United States', '2026-11-26', '2026-12-11', '2026-03-10 17:32:07', NULL, 3, 'cold,rain,snow', 'wintersports', '', 42.434760, -83.984950, 1, 0, 0),
(4, 'Springfield trip', 'Springfield', 'United States', '2026-03-28', '2026-03-30', '2026-03-10 17:37:39', NULL, 1, 'cold,rain', 'hiking,camping,gym', 'homer simpson', 39.924230, -83.808820, 1, 0, 0),
(5, 'Trip with a lot of activities', 'Tokyo', 'Japan', '2026-09-23', '2026-10-15', '2026-03-10 18:59:05', NULL, 3, 'warm,cold,rain,wind', 'beach,hiking,camping,gym,sport,swimming,wintersports,business,nightout,roadtrip,formal', 'i have so many things to do on this trip its honestly a bit worrying', 35.689500, 139.691710, 1, 0, 0),
(6, 'Trip to hell (retribution)', 'Hell', 'United States', '2026-04-19', '2026-04-27', '2026-03-10 21:26:23', 'https://images.pexels.com/photos/31416335/pexels-photo-31416335.jpeg', 3, 'cold,rain,snow', 'hiking,camping,gym', 'I <3 Michigan', 42.434760, -83.984950, 0, 0, 0),
(7, 'Chicago trip', 'Chicago', 'United States', '2026-06-17', '2026-07-02', '2026-03-10 21:28:55', 'https://images.pexels.com/photos/17661149/pexels-photo-17661149.jpeg', 1, 'warm,cold,rain', 'nightout,roadtrip,formal', '', 41.850030, -87.650050, 0, 0, 0),
(8, 'Trip 8 I think', 'Beijing', 'China', '2027-02-10', '2027-03-05', '2026-03-11 15:21:39', 'https://images.pexels.com/photos/16428155/pexels-photo-16428155.jpeg', 1, 'cold,rain,snow', 'formal', '', 39.907500, 116.397230, 1, 0, 0),
(9, 'Trip 8 I think', 'Beijing', 'China', '2027-02-10', '2027-03-05', '2026-03-11 15:21:44', 'https://images.pexels.com/photos/16428155/pexels-photo-16428155.jpeg', 1, 'cold,rain,snow', 'formal', '', 39.907500, 116.397230, 1, 0, 0),
(10, 'Trip 8 I think', 'Beijing', 'China', '2027-02-10', '2027-03-05', '2026-03-11 15:21:50', 'https://images.pexels.com/photos/16428155/pexels-photo-16428155.jpeg', 1, 'cold,rain,snow', 'formal', '', 39.907500, 116.397230, 1, 0, 0),
(11, 'New beijing trip', 'Beijing', 'China', '2026-03-31', '2026-04-21', '2026-03-11 15:26:31', 'https://images.pexels.com/photos/16428155/pexels-photo-16428155.jpeg', 1, 'cold,rain', 'business', '', 39.907500, 116.397230, 1, 0, 0),
(12, 'Is the insert script working pls', 'Paris', 'France', '2026-03-27', '2026-03-31', '2026-03-11 15:38:49', 'https://images.pexels.com/photos/30689741/pexels-photo-30689741.jpeg', 1, 'cold,rain', 'business', '', 48.853410, 2.348800, 1, 0, 0),
(13, 'test insert', 'Paris', 'France', '2026-03-20', '2026-03-17', '2026-03-11 15:39:53', 'https://images.pexels.com/photos/30689741/pexels-photo-30689741.jpeg', 1, '', 'business', '', 48.853410, 2.348800, 1, 0, 0),
(14, 'dublin trip so cool', 'Dublin', 'Ireland', '2026-03-26', '2026-03-31', '2026-03-11 15:42:36', 'https://images.pexels.com/photos/33999754/pexels-photo-33999754.jpeg', 1, '', 'hiking', 'cool', 53.333060, -6.248890, 0, 0, 0),
(15, 'SUPER CLOSE TRIP TOMORROW', 'Belgrade', 'Serbia', '2026-03-12', '2026-03-17', '2026-03-11 19:42:41', 'https://images.pexels.com/photos/34432788/pexels-photo-34432788.jpeg', 2, 'cold,rain', 'gym', '', 44.804010, 20.465130, 0, 1, 0),
(16, 'SUPER CLOSE TRIP TOMORROW', 'Belgrade', 'Serbia', '2026-03-12', '2026-03-17', '2026-03-11 19:45:40', 'https://images.pexels.com/photos/34432788/pexels-photo-34432788.jpeg', 2, 'cold,rain', 'gym', '', 44.804010, 20.465130, 1, 1, 0),
(17, 'SUPER CLOSE TRIP TOMORROW', 'Belgrade', 'Serbia', '2026-03-12', '2026-03-17', '2026-03-11 19:46:47', 'https://images.pexels.com/photos/34432788/pexels-photo-34432788.jpeg', 2, 'cold,rain', 'gym', '', 44.804010, 20.465130, 1, 1, 0),
(18, 'SUPER CLOSE TRIP TOMORROW', 'Belgrade', 'Serbia', '2026-03-12', '2026-03-17', '2026-03-11 19:48:23', 'https://images.pexels.com/photos/34432788/pexels-photo-34432788.jpeg', 2, 'cold,rain', 'gym', '', 44.804010, 20.465130, 1, 1, 0),
(19, 'trip tomorrow', 'Sydney', 'Australia', '2026-03-12', '2026-03-18', '2026-03-11 20:03:03', 'https://images.pexels.com/photos/34689481/pexels-photo-34689481.jpeg', 1, 'cold,rain', 'hiking', '', -33.867850, 151.207320, 0, 1, 0),
(20, 'berlin trip', 'Berlin', 'Germany', '2026-04-17', '2026-05-29', '2026-03-11 20:17:42', 'https://images.pexels.com/photos/34969636/pexels-photo-34969636.jpeg', 3, 'cold,rain', 'camping,formal', '', 52.524370, 13.410530, 0, 0, 0),
(21, 'london', '', '', '0000-00-00', '0000-00-00', '2026-03-11 20:22:48', '', 3, '', '', '', 0.000000, 0.000000, 1, 0, 0),
(22, 'Kuala Lumpur Business Trip', 'Kuala Lumpur', 'Malaysia', '2026-05-26', '2026-07-22', '2026-03-11 20:27:14', 'https://images.pexels.com/photos/35059637/pexels-photo-35059637.jpeg', 3, 'warm,rain', 'business,formal', '', 3.141200, 101.686530, 0, 0, 0),
(23, 'trip for cool seminar', '', '', '0000-00-00', '0000-00-00', '2026-03-11 20:33:05', '', 3, '', '', '', 0.000000, 0.000000, 1, 0, 0),
(24, 'Burlington is such a cool place', 'Burlington', 'United States', '2026-03-12', '2026-03-13', '2026-03-11 21:22:12', 'https://images.pexels.com/photos/18734918/pexels-photo-18734918.jpeg', 3, 'cold,rain', 'camping', '', 44.475880, -73.212070, 1, 1, 0),
(25, 'Burlington is such a cool place', 'Burlington', 'United States', '2026-03-13', '2026-03-17', '2026-03-11 21:22:39', 'https://images.pexels.com/photos/18734918/pexels-photo-18734918.jpeg', 3, 'cold,rain,snow', 'hiking,camping', '', 44.475880, -73.212070, 0, 0, 1),
(26, 'Going to Palembang', 'Palembang', 'Indonesia', '2026-03-12', '2026-03-17', '2026-03-11 21:47:27', 'https://images.pexels.com/photos/18388935/pexels-photo-18388935.jpeg', 1, 'warm', 'formal', '', -2.916730, 104.745800, 0, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`tripid`),
  ADD KEY `userid` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `tripid` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `UseridFromUserstoTrips` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
