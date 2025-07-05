-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 01:26 PM
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
  `age` int(11) DEFAULT NULL,
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

INSERT INTO `users` (`id`, `full_name`, `username`, `age`, `weight`, `blood_group`, `phone`, `address`, `email`, `password`, `gender`, `dob`, `created_at`) VALUES
(1, 'Mahendra Kashiap T', 'mahendra', 19, 54, 'A+', '9778525583', 'Thaipurayil(H), Moothakunnam PO', 'mahendrakashiap130@gmail.com', '$2y$10$tbsYkJn.7WbkdjlbK8iBk.dTDxITfCLhLXW0OAxT1Cp.xHg2n9YAG', 'Male', '2005-10-14', '2025-06-21 07:49:27'),
(2, 'Christiano ', 'chris', 40, 75, 'A+', '1234567890', 'portugal', 'christiano@gmail.com', '$2y$10$uVNfCSwd3cgnbAZYUOKMfeVK4eAUInNRM5mgRXcgo9tdtpC4aHLOS', 'Male', '1985-05-15', '2025-06-21 20:35:49'),
(3, 'Reena C K', 'reena', 48, 67, 'B+', '8547184322', 'Thaipurayil(H), Moothakunnam P O', 'krishnendrakashiapt@gmail.com', '$2y$10$p56MuH1cn0n4EsT5ggZViOIF989f7thaBGhzcfeT9t3FyAkWaaZLe', 'Female', '1976-12-11', '2025-06-22 08:53:12');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Table for all exercises (predefined and user-created)
CREATE TABLE IF NOT EXISTS exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL, -- NULL for predefined, user id for custom
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    description TEXT
);

-- Table for user workout routines
CREATE TABLE IF NOT EXISTS workout_routines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table linking routines to exercises, with sets/reps/duration
CREATE TABLE IF NOT EXISTS workout_routine_exercises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    routine_id INT NOT NULL,
    exercise_id INT NOT NULL,
    sets INT DEFAULT NULL,
    reps INT DEFAULT NULL,
    duration INT DEFAULT NULL, -- in seconds or minutes
    position INT DEFAULT 0 -- order in routine
);

-- (Foreign keys can be added if needed)
