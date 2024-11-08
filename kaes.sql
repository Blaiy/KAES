-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2024 at 07:02 AM
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
-- Database: `kaes`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `year_of_birth` int(11) DEFAULT NULL,
  `school` varchar(50) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_of_graduation` int(11) DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni`
--

INSERT INTO `alumni` (`id`, `name`, `email`, `phone`, `year_of_birth`, `school`, `course`, `year_of_graduation`, `employment_status`, `location`, `password`) VALUES
(1, 'Margaret', 'maggie@gmail.com', '0712345678', 1982, 'health', 'Nursing', 2006, 'employed', 'Mombasa', '$2y$10$KL0I9zJugGu4A5KuhD.d/O3MVB0ip6BzuIDRzeFRNEqBo876nTZ/W'),
(2, 'Chrispus Alukwe', 'alukwe@gmail.com', '0787654321', 1980, 'science', 'Computer Forensics', 2004, 'employed', 'Nakuru', '$2y$10$6tg/GnhCP1YHynCMZjbpaOCMM.WeAyJIXRsKXkFiOq5OAR.EvUgLi');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `year_of_birth` int(11) DEFAULT NULL,
  `school` varchar(50) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `reg_number` varchar(50) DEFAULT NULL,
  `year_of_study` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`, `year_of_birth`, `school`, `course`, `reg_number`, `year_of_study`, `password`) VALUES
(1, 'Annabel Blessing', 'annabel@gmail.com', '0711234323', 2002, 'science', 'Computer Science', 'CS/MG/3072/09/21', 'Y4 S2', '$2y$10$TOXXuf1KdtM2MzhY7Cl3O.G/HMeWi3xGPh8hUlwMBFhCkJsfbQiVm'),
(5, 'Annabel Blessing', 'annabel02@gmail.com', '0711234323', 2002, 'science', 'Computer Science', 'CS/MG/3073/09/21', 'Y4 S2', 'annabel-3702');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reg_number` (`reg_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumni`
--
ALTER TABLE `alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
