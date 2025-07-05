-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 07:03 PM
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `user_id`, `name`, `type`, `description`) VALUES
(2, 1, 'abcd', 'Cardio', 'ff');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `review` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`)
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `weight` float DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `age`, `weight`, `blood_group`, `phone`, `address`, `email`, `password`, `gender`, `dob`, `created_at`) VALUES
(1, 'Mahendra Kashiap T', 'mahendra', 19, 54, 'A+', '9778525583', 'Thaipurayil(H), Moothakunnam PO', 'mahendrakashiap130@gmail.com', '$2y$10$tbsYkJn.7WbkdjlbK8iBk.dTDxITfCLhLXW0OAxT1Cp.xHg2n9YAG', 'Male', '2005-10-14', '2025-06-21 07:49:27'),
(2, 'Christiano ', 'chris', 40, 75, 'A+', '1234567890', 'portugal', 'christiano@gmail.com', '$2y$10$uVNfCSwd3cgnbAZYUOKMfeVK4eAUInNRM5mgRXcgo9tdtpC4aHLOS', 'Male', '1985-05-15', '2025-06-21 20:35:49'),
(3, 'Reena C K', 'reena', 48, 67, 'B+', '8547184322', 'Thaipurayil(H), Moothakunnam P O', 'krishnendrakashiapt@gmail.com', '$2y$10$p56MuH1cn0n4EsT5ggZViOIF989f7thaBGhzcfeT9t3FyAkWaaZLe', 'Female', '1976-12-11', '2025-06-22 08:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `workout_routines`
--

CREATE TABLE `workout_routines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `workout_routines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_routines`
--

INSERT INTO `workout_routines` (`id`, `user_id`, `name`, `created_at`) VALUES
(2, 2, 'eg 2', '2025-06-27 15:31:40');

-- --------------------------------------------------------

--
-- Table structure for table `workout_routine_exercises`
--

CREATE TABLE `workout_routine_exercises` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `routine_id` int(11) NOT NULL,
  `exercise_id` int(11) NOT NULL,
  `sets` int(11) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_routine_id` (`routine_id`),
  KEY `idx_exercise_id` (`exercise_id`),
  CONSTRAINT `workout_routine_exercises_ibfk_1` FOREIGN KEY (`routine_id`) REFERENCES `workout_routines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workout_routine_exercises_ibfk_2` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_routine_exercises`
--

INSERT INTO `workout_routine_exercises` (`id`, `routine_id`, `exercise_id`, `sets`, `reps`, `position`) VALUES
(5, 2, 1026, 4, 3, 0),
(6, 2, 1001, 2, 7, 1),
(7, 2, 1002, 2, 8, 2),
(8, 2, 1003, 2, 9, 3),
(9, 2, 1028, 5, 7, 4),
(10, 2, 1028, 5, 5, 5),
(11, 2, 1029, 2, 2, 6);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */; 