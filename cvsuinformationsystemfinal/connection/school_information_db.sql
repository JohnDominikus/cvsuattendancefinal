-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2024 at 02:36 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_information_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_archive`
--

CREATE TABLE `admin_archive` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archived_subjects`
--

CREATE TABLE `archived_subjects` (
  `archived_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `subject_code` varchar(255) DEFAULT NULL,
  `subject_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `time` time DEFAULT NULL,
  `credits` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archived_subjects`
--

INSERT INTO `archived_subjects` (`archived_id`, `subject_id`, `subject_code`, `subject_name`, `description`, `time`, `credits`) VALUES
(3, 3, 'ENG101', 'English Composition', 'Development of critical thinking and writing skills.', '20:00:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(22) NOT NULL,
  `image` varchar(255) NOT NULL,
  `student_no` int(222) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `gender` varchar(222) NOT NULL,
  `student_status` varchar(22) NOT NULL,
  `email` varchar(50) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `image`, `student_no`, `first_name`, `last_name`, `gender`, `student_status`, `email`, `added_at`) VALUES
(58, 'Designer (3).png', 23456789, 'andrew', 'parker2', 'Male', '', 'andrew@gmail.com', '2024-06-16 20:49:19'),
(64, 'document (4).jpg', 2332232, 'andrew', 'parker', 'Male', 'excused', 'andrew@gmail.com', '2024-06-19 19:01:41');

-- --------------------------------------------------------

--
-- Table structure for table `student_archive`
--

CREATE TABLE `student_archive` (
  `id` int(11) NOT NULL,
  `student_no` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `student_status` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_archive`
--

INSERT INTO `student_archive` (`id`, `student_no`, `first_name`, `last_name`, `email`, `gender`, `student_status`, `image`, `archived_at`, `archived_by`) VALUES
(5, '2332232', 'andrew', 'parker', 'andrew@gmail.com', 'Male', 'excused', 'Designer (3).png', '2024-06-19 19:07:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `time` varchar(6) NOT NULL,
  `credits` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `description`, `time`, `credits`) VALUES
(1, 'COMP1012', 'Introduction to Computer Sciences', 'Fundamental concepts in computer science.', '18:00', 4),
(2, 'MATH201', 'Calculus I', 'Introduction to differential and integral calculus.', '13:00', 4),
(11, 'COMP1012', 'Introduction to Computer Sciences', NULL, '20:00', NULL),
(12, 'PHYS101', 'Physics I', 'Fundamental principles of classical physics.', '19:00:', 4);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_archive`
--

CREATE TABLE `teacher_archive` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_archive`
--

INSERT INTO `teacher_archive` (`id`, `first_name`, `last_name`, `email`, `gender`, `image`, `type`, `password`, `archived_at`) VALUES
(14, 'juan', 'lee', 'admin@gmail.com', 'Male', '', 'teacher', '$2y$10$Hn0gclqfTpg8fvsdQ6mh5uKpn7Xze5fV540a6O0fHC4', '2024-06-16 12:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_archive`
--

CREATE TABLE `user_archive` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('admin','teacher','student') NOT NULL,
  `password` varchar(255) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_form`
--

CREATE TABLE `user_form` (
  `id` int(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_form`
--

INSERT INTO `user_form` (`id`, `first_name`, `last_name`, `email`, `gender`, `image`, `type`, `password`) VALUES
(1, 'john dominic', 'calvadores', 'admin@gmail.com', 'male', 'download (9).jpg', 'admin', 'admin'),
(2, 'mark', 'alvin', 'markalvin@gmail.com', 'Male', 'download (10).jpg', 'teacher', '123456789'),
(3, 'martin', 'go', 'martingo@gmail.copm', 'male', '1_j3h9JWKzVkwwGsGqlOZCfg.jpg', 'student', '123456789'),
(16, 'juan', 'lee', 'juanlee@gmail.com', 'Female', 'document (3).jpg', 'student', '123456789'),
(21, 'john doe', 'lee', 'johndoelee@gmail.com', 'Male', 'download (12).png', 'admin', '123456789'),
(23, 'nestor', 'pimentel', 'testteacher@gmail.com', 'Male', 'Designer.jpeg', 'admin', 'test1234');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_archive`
--
ALTER TABLE `admin_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `archived_subjects`
--
ALTER TABLE `archived_subjects`
  ADD PRIMARY KEY (`archived_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_archive`
--
ALTER TABLE `student_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archived_by` (`archived_by`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `teacher_archive`
--
ALTER TABLE `teacher_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_archive`
--
ALTER TABLE `user_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_form`
--
ALTER TABLE `user_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_archive`
--
ALTER TABLE `admin_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `archived_subjects`
--
ALTER TABLE `archived_subjects`
  MODIFY `archived_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `student_archive`
--
ALTER TABLE `student_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `teacher_archive`
--
ALTER TABLE `teacher_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_archive`
--
ALTER TABLE `user_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_form`
--
ALTER TABLE `user_form`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_archive`
--
ALTER TABLE `student_archive`
  ADD CONSTRAINT `student_archive_ibfk_1` FOREIGN KEY (`archived_by`) REFERENCES `user_form` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
