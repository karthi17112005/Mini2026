-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2026 at 10:01 AM
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
-- Database: `campus_ministry_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `last_name`, `email`, `password`, `created_at`) VALUES
(1, 'Super', 'Admin', 'admin@ministry.com', 'admin123', '2026-08-26 06:30:42');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `first_name`, `last_name`, `email`, `password`, `department`, `role`) VALUES
(1, 'Staff', 'Member', 'staff@ministry.com', 'staff123', 'Computer Science', 'Tutor'),
(2, 'jegan', 'g', 'jegan@gmail.com', '171104', 'IT', 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `d_number` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `religion` varchar(50) NOT NULL,
  `accommodation_type` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `class` varchar(50) NOT NULL,
  `year` varchar(20) NOT NULL,
  `section` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`d_number`, `first_name`, `last_name`, `religion`, `accommodation_type`, `department`, `class`, `year`, `section`) VALUES
('22UCA301', 'Nithesh', 'D', 'Non-Catholic', 'Day Scholar', 'IT', 'BCA', 'III Year', 'A'),
('22UCA302', 'Patrick', 'G', 'Catholic', 'Hosteler', 'IT', 'BCA', 'III Year', 'A'),
('22UCA303', 'Pradeep', 'K', 'Non-Catholic', 'Day Scholar', 'IT', 'BCA', 'III Year', 'A'),
('22UCA304', 'Peter', 'L', 'Catholic', 'Day Scholar', 'IT', 'BCA', 'III Year', 'B'),
('22UCA305', 'Praveen', 'M', 'Non-Catholic', 'Hosteler', 'IT', 'BCA', 'III Year', 'B'),
('22UCS301', 'Deepak', 'N', 'Non-Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'III Year', 'A'),
('22UCS302', 'Dominic', 'S', 'Catholic', 'Hosteler', 'CS', 'B.Sc CS', 'III Year', 'A'),
('22UCS303', 'Dinesh', 'T', 'Non-Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'III Year', 'A'),
('22UCS304', 'Edward', 'L', 'Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'III Year', 'B'),
('22UCS305', 'Elango', 'V', 'Non-Catholic', 'Hosteler', 'CS', 'B.Sc CS', 'III Year', 'B'),
('23UCA201', 'Mathias', 'K', 'Catholic', 'Hosteler', 'IT', 'BCA', 'II Year', 'A'),
('23UCA202', 'Mukesh', 'V', 'Non-Catholic', 'Day Scholar', 'IT', 'BCA', 'II Year', 'A'),
('23UCA203', 'Michael', 'R', 'Catholic', 'Day Scholar', 'IT', 'BCA', 'II Year', 'A'),
('23UCA204', 'Naveen', 'B', 'Non-Catholic', 'Day Scholar', 'IT', 'BCA', 'II Year', 'B'),
('23UCA205', 'Nicholas', 'S', 'Catholic', 'Hosteler', 'IT', 'BCA', 'II Year', 'B'),
('23UCS201', 'Balan', 'P', 'Non-Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'II Year', 'A'),
('23UCS202', 'Benedict', 'A', 'Catholic', 'Hosteler', 'CS', 'B.Sc CS', 'II Year', 'A'),
('23UCS203', 'Chandran', 'G', 'Non-Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'II Year', 'A'),
('23UCS204', 'Charles', 'D', 'Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'II Year', 'B'),
('23UCS205', 'David', 'J', 'Catholic', 'Hosteler', 'CS', 'B.Sc CS', 'II Year', 'B'),
('24PCA901', 'Hari', 'P', 'Non-Catholic', 'Day Scholar', 'CS', 'MCA', 'II Year', 'A'),
('24PCA902', 'Iniyan', 'S', 'Non-Catholic', 'Hosteler', 'CS', 'MCA', 'II Year', 'A'),
('24PCA903', 'James', 'W', 'Catholic', 'Hosteler', 'CS', 'MCA', 'II Year', 'A'),
('24PCA904', 'Jeeva', 'M', 'Non-Catholic', 'Day Scholar', 'CS', 'MCA', 'II Year', 'B'),
('24PCA905', 'Joseph', 'B', 'Catholic', 'Hosteler', 'CS', 'MCA', 'II Year', 'B'),
('24PCS901', 'Sanjay', 'V', 'Non-Catholic', 'Day Scholar', 'IT', 'M.Sc CS', 'II Year', 'A'),
('24PCS902', 'Stephen', 'T', 'Catholic', 'Hosteler', 'IT', 'M.Sc CS', 'II Year', 'A'),
('24UCA101', 'Karthik', 'N', 'Non-Catholic', 'Day Scholar', 'IT', 'BCA', 'I Year', 'A'),
('24UCA102', 'Lawrence', 'T', 'Catholic', 'Hosteler', 'IT', 'BCA', 'I Year', 'A'),
('24UCA103', 'Kishore', 'E', 'Non-Catholic', 'Day Scholar', 'IT', 'BCA', 'I Year', 'A'),
('24UCA104', 'Leo', 'P', 'Catholic', 'Day Scholar', 'IT', 'BCA', 'I Year', 'B'),
('24UCA105', 'Manoj', 'C', 'Non-Catholic', 'Hosteler', 'IT', 'BCA', 'I Year', 'B'),
('24UCS101', 'Aarav', 'K', 'Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'I Year', 'A'),
('24UCS102', 'Abishek', 'M', 'Non-Catholic', 'Hosteler', 'CS', 'B.Sc CS', 'I Year', 'A'),
('24UCS103', 'Alphonse', 'R', 'Catholic', 'Hosteler', 'CS', 'B.Sc CS', 'I Year', 'A'),
('24UCS104', 'Anand', 'S', 'Non-Catholic', 'Day Scholar', 'CS', 'B.Sc CS', 'I Year', 'B'),
('24UCS105', 'Antony', 'V', 'Catholic', 'Hosteler', 'CS', 'B.Sc CS', 'I Year', 'B'),
('25PCA801', 'Francis', 'X', 'Catholic', 'Hosteler', 'CS', 'MCA', 'I Year', 'A'),
('25PCA802', 'Ganesh', 'R', 'Non-Catholic', 'Day Scholar', 'CS', 'MCA', 'I Year', 'A'),
('25PCA803', 'George', 'M', 'Catholic', 'Hosteler', 'CS', 'MCA', 'I Year', 'A'),
('25PCA804', 'Gowtham', 'K', 'Non-Catholic', 'Day Scholar', 'CS', 'MCA', 'I Year', 'B'),
('25PCA805', 'Ignatius', 'A', 'Catholic', 'Day Scholar', 'CS', 'MCA', 'I Year', 'B'),
('25PCS801', 'Philip', 'A', 'Catholic', 'Hosteler', 'IT', 'M.Sc CS', 'I Year', 'A'),
('25PCS802', 'Raghav', 'S', 'Non-Catholic', 'Day Scholar', 'IT', 'M.Sc CS', 'I Year', 'A'),
('25PCS803', 'Robert', 'J', 'Catholic', 'Day Scholar', 'IT', 'M.Sc CS', 'I Year', 'A');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`d_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
