-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 06, 2025 at 02:23 PM
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
-- Database: `fitness_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `user_id`, `name`, `type`, `description`) VALUES
(2, 1, 'abcd', 'Cardio', 'ff'),
(10, 1, 'triceps dips', 'Strength', 'kjdjklads'),
(11, 6, 'Explosive Pushups', 'Strength', 'for increasing strength');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `review` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `username`, `review`, `created_at`) VALUES
(2, 'mahendra', 'hello this is my review', '2025-06-21 07:50:45'),
(4, 'mahendra', 'hello this is my second review', '2025-06-21 20:26:47'),
(6, 'chris', 'great website', '2025-06-21 20:37:00'),
(7, 'reena', 'hello', '2025-06-22 11:09:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `weight`, `blood_group`, `phone`, `address`, `email`, `password`, `gender`, `dob`, `created_at`) VALUES
(1, 'Mahendra Kashiap T', 'mahendra', 54, 'A+', '9778525583', 'Thaipurayil(H), Moothakunnam PO', 'mahendrakashiap130@gmail.com', '$2y$10$tbsYkJn.7WbkdjlbK8iBk.dTDxITfCLhLXW0OAxT1Cp.xHg2n9YAG', 'Male', '2005-10-14', '2025-06-21 07:49:27'),
(3, 'Reena C K', 'reena', 67, 'B+', '8547184322', 'Thaipurayil(H), Moothakunnam P O', 'krishnendrakashiapt@gmail.com', '$2y$10$p56MuH1cn0n4EsT5ggZViOIF989f7thaBGhzcfeT9t3FyAkWaaZLe', 'Female', '1976-12-11', '2025-06-22 08:53:12'),
(6, 'Mahendra Kashiap T', 'noobmaster', 54, 'A+', '9778525583', 'ABCD(H), San Andreas', 'lhp221175@gmail.com', '$2y$10$4bx3ZBW64ldRUf1RFWba6.c3GHJOf796oM7TCjX3EHP7Ynh40molK', 'Male', '2005-10-14', '2025-06-30 08:13:56'),
(8, 'Kevin', 'kvin', 60, 'B-', '123456689', 'dadffffgg rrerg', 'kevin222@gmail.com', '$2y$10$KJbbV9/9OnBT7V6.rnpMw.RgrgspdsAQFW3jEnn3ChBV7nCNiYazu', 'Male', '2004-12-15', '2025-07-06 10:44:06');

-- --------------------------------------------------------

--
-- Table structure for table `water_goal`
--

CREATE TABLE `water_goal` (
  `user_id` int(11) NOT NULL,
  `daily_goal` int(11) NOT NULL DEFAULT 2000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `water_goal`
--

INSERT INTO `water_goal` (`user_id`, `daily_goal`) VALUES
(3, 2000),
(6, 5000),
(8, 2000);

-- --------------------------------------------------------

--
-- Table structure for table `water_intake`
--

CREATE TABLE `water_intake` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` int(11) NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `water_intake`
--

INSERT INTO `water_intake` (`id`, `user_id`, `date`, `amount`, `time`) VALUES
(36, 6, '2025-07-03', 250, '08:41:45'),
(37, 6, '2025-07-03', 500, '08:41:52'),
(38, 6, '2025-07-03', 750, '08:41:54'),
(39, 6, '2025-07-03', 750, '08:41:55'),
(40, 6, '2025-07-03', 750, '08:41:55'),
(41, 6, '2025-07-03', 750, '08:41:56'),
(42, 6, '2025-07-03', 750, '08:41:56'),
(43, 6, '2025-07-03', 750, '08:41:56'),
(44, 6, '2025-07-03', 750, '08:41:57'),
(45, 6, '2025-07-03', 750, '08:41:57'),
(46, 6, '2025-07-03', 750, '08:41:58'),
(47, 6, '2025-07-03', 750, '08:41:58'),
(48, 6, '2025-07-03', 750, '08:41:59'),
(49, 6, '2025-07-03', 750, '08:41:59'),
(50, 6, '2025-07-03', 750, '08:41:59'),
(51, 6, '2025-07-03', 750, '08:43:38'),
(52, 6, '2025-07-03', 250, '08:43:55'),
(53, 6, '2025-07-03', 500, '08:44:00'),
(54, 6, '2025-07-03', 500, '08:44:00'),
(55, 6, '2025-07-03', 500, '08:44:03'),
(56, 6, '2025-07-02', 250, '08:45:54'),
(57, 6, '2025-07-02', 500, '08:45:55'),
(58, 6, '2025-07-02', 750, '08:45:56'),
(59, 6, '2025-07-02', 750, '08:46:06'),
(60, 6, '2025-07-02', 750, '08:46:06'),
(61, 6, '2025-07-02', 750, '08:46:07'),
(62, 6, '2025-07-02', 750, '08:46:07'),
(63, 6, '2025-07-02', 750, '08:46:07'),
(64, 6, '2025-07-02', 750, '08:46:08'),
(65, 6, '2025-07-02', 750, '08:46:08'),
(66, 6, '2025-07-02', 750, '08:46:08'),
(67, 6, '2025-07-02', 750, '08:46:11'),
(68, 6, '2025-07-02', 750, '08:46:11'),
(69, 6, '2025-07-02', 750, '08:46:11'),
(70, 6, '2025-07-02', 750, '08:46:12'),
(71, 6, '2025-07-02', 750, '08:46:12'),
(72, 6, '2025-07-02', 750, '08:46:13'),
(73, 6, '2025-07-02', 1000, '08:46:14'),
(74, 6, '2025-07-02', 1000, '08:46:15'),
(75, 6, '2025-07-02', 1000, '08:46:16'),
(76, 6, '2025-07-02', 1000, '08:46:16'),
(77, 6, '2025-07-02', 1000, '08:46:17'),
(78, 6, '2025-07-02', 1000, '08:46:17'),
(79, 6, '2025-07-02', 1000, '08:46:18'),
(80, 6, '2025-07-02', 1000, '08:46:18'),
(81, 6, '2025-07-02', 1000, '08:46:18'),
(82, 6, '2025-07-02', 1000, '08:46:19'),
(83, 6, '2025-07-02', 1000, '08:46:19'),
(84, 6, '2025-07-02', 1000, '08:46:19'),
(85, 6, '2025-07-02', 1000, '08:46:19'),
(86, 6, '2025-07-02', 1000, '08:46:20'),
(87, 6, '2025-07-02', 1000, '08:46:20'),
(88, 6, '2025-07-02', 1000, '08:46:20'),
(89, 6, '2025-07-02', 1000, '08:46:20'),
(90, 6, '2025-07-02', 1000, '08:46:21'),
(91, 6, '2025-07-01', 1000, '08:46:34'),
(92, 6, '2025-07-01', 1000, '08:46:35'),
(93, 6, '2025-07-01', 1000, '08:46:36'),
(94, 6, '2025-07-01', 1000, '08:46:36'),
(95, 6, '2025-07-01', 1000, '08:46:37'),
(96, 6, '2025-07-01', 1000, '08:46:37'),
(97, 6, '2025-07-01', 1000, '08:46:37'),
(98, 6, '2025-07-01', 1000, '08:46:38'),
(99, 6, '2025-07-01', 1000, '08:46:38'),
(100, 6, '2025-07-01', 10000, '08:46:42'),
(101, 6, '2025-07-01', 1000, '08:46:45'),
(102, 6, '2025-07-01', 1000, '08:46:45'),
(103, 6, '2025-07-01', 1000, '08:46:45'),
(104, 6, '2025-07-01', 1000, '08:46:46'),
(105, 6, '2025-07-01', 1000, '08:46:46'),
(106, 6, '2025-07-01', 1000, '08:46:47'),
(175, 8, '2025-07-06', 1000, '12:59:04');

-- --------------------------------------------------------

--
-- Table structure for table `workout_routines`
--

CREATE TABLE `workout_routines` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_routines`
--

INSERT INTO `workout_routines` (`id`, `user_id`, `name`, `created_at`) VALUES
(2, 2, 'eg 2', '2025-06-27 15:31:40'),
(3, 1, '123', '2025-06-27 18:24:45'),
(4, 1, 'New routine', '2025-06-28 01:54:18'),
(5, 6, 'routine morning', '2025-07-03 05:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `workout_routine_exercises`
--

CREATE TABLE `workout_routine_exercises` (
  `id` int(11) NOT NULL,
  `routine_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_routine_exercises`
--

INSERT INTO `workout_routine_exercises` (`id`, `routine_id`, `exercise_id`, `sets`, `reps`, `duration`, `position`) VALUES
(5, 2, 1026, 4, 3, NULL, 0),
(6, 2, 1001, 2, 7, NULL, 1),
(7, 2, 1002, 2, 8, NULL, 2),
(8, 2, 1003, 2, 9, NULL, 3),
(9, 2, 1028, 5, 7, NULL, 4),
(10, 2, 1028, 5, 5, NULL, 5),
(11, 2, 1029, 2, 2, NULL, 6),
(12, 3, 2, 2, 2, NULL, 0),
(13, 3, 1001, 2, 2, NULL, 1),
(14, 3, 1002, 2, 2, NULL, 2),
(15, 4, 2, 3, 15, NULL, 0),
(16, 4, 1001, 2, 10, NULL, 1),
(17, 4, 1002, 3, 20, NULL, 2),
(18, 4, 1003, 1, 30, NULL, 3),
(19, 4, 1004, 3, 10, NULL, 4),
(20, 5, 1001, 3, 15, NULL, 0),
(21, 5, 1002, 5, 10, NULL, 1),
(22, 5, 1003, 3, 5, NULL, 2),
(23, 5, 11, 3, 10, NULL, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `water_goal`
--
ALTER TABLE `water_goal`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `water_intake`
--
ALTER TABLE `water_intake`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `workout_routines`
--
ALTER TABLE `workout_routines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workout_routine_exercises`
--
ALTER TABLE `workout_routine_exercises`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `water_intake`
--
ALTER TABLE `water_intake`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `workout_routines`
--
ALTER TABLE `workout_routines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `workout_routine_exercises`
--
ALTER TABLE `workout_routine_exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `water_goal`
--
ALTER TABLE `water_goal`
  ADD CONSTRAINT `water_goal_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `water_intake`
--
ALTER TABLE `water_intake`
  ADD CONSTRAINT `water_intake_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
