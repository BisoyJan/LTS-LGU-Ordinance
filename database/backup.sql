-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 04:44 AM
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
-- Database: `lgu_ordinance_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `committees`
--

CREATE TABLE `committees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `committees`
--

INSERT INTO `committees` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Appropriations', 'Handles the municipal budget and financial matters. ', '2025-03-19 14:57:39', '2025-03-19 20:30:14'),
(2, 'Women and Family', 'Focuses on issues related to women\'s rights, gender equality, and family welfare. ', '2025-03-19 14:57:39', '2025-03-19 20:30:30'),
(3, 'Human Rights', 'Addresses human rights concerns within the municipality. ', '2025-03-19 14:57:39', '2025-03-19 20:30:41'),
(4, 'Youth and Sports Development', 'Deals with programs and initiatives for youth and sports development. ', '2025-03-19 14:57:39', '2025-03-19 20:30:46'),
(5, 'Environmental Protection', 'Focuses on environmental issues and sustainability. ', '2025-03-19 14:57:39', '2025-03-19 20:30:53'),
(6, 'Cooperatives', 'Deals with matters related to cooperatives and their operations. ', '2025-03-19 14:57:39', '2025-03-19 20:30:58'),
(7, 'Rules and Privileges', 'Ensures the proper functioning of the Sangguniang Bayan and its committees. ', '2025-03-19 14:57:39', '2025-03-19 20:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `ordinance_proposals`
--

CREATE TABLE `ordinance_proposals` (
  `id` int(11) NOT NULL,
  `proposal` varchar(255) NOT NULL,
  `current_status` varchar(100) DEFAULT NULL,
  `proposal_date` date NOT NULL,
  `details` text DEFAULT NULL,
  `committee_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_type` enum('pdf','doc','docx','txt') DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordinance_proposals`
--

INSERT INTO `ordinance_proposals` (`id`, `proposal`, `current_status`, `proposal_date`, `details`, `committee_id`, `user_id`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(23, 'Hello', NULL, '2025-03-20', 'hello', 1, 62, 'Managing Webpage (For Renewal of Domain and Server).docx', '1H3fOaJ_CGV44A4cmOzKvhzffzJJWBSWj', 'docx', 296509, '2025-03-19 20:11:02', '2025-04-22 16:18:53'),
(24, 'Testing', '', '2025-04-19', 'Wdawdw adadwadwa dawdawdawdawdad ', 1, 62, 'PC build Canvas.docx', '1P7jo7fV8GCP6JMNe8eHPs1xVrUwEkWlB', 'docx', 21571, '2025-04-18 19:17:51', '2025-04-25 19:42:56'),
(25, 'wadwadwdaw', NULL, '2025-04-26', 'wadwdawd awdwadwdawdwa dwadaw', 1, 62, 'System Feature flow and functionalities.docx', '1xf9vWb_aGkl6HlRatDt-I9OBn4JjhztU', 'docx', 15863, '2025-04-25 17:33:37', '2025-04-25 17:33:37'),
(26, 'No Parking Near SM Mall', NULL, '2025-04-27', 'Because it Cause a traffic in rush hours', 6, 62, 'Screenshots and Code.docx', '1q-lIJsdGdqEHj4B0kRaF7UEvfWjrjXTX', 'docx', 6260984, '2025-04-27 12:19:07', '2025-04-27 12:19:07'),
(33, '12131321312', NULL, '2025-05-03', 'waaaaadddddddddaaaaaaaaaaaaddddddd', 3, 72, 'legislator.docx', '1Vk66IGzumoqBLA0qew_eESkgRIamG580', NULL, NULL, '2025-05-02 18:30:09', '2025-05-02 19:19:45');

-- --------------------------------------------------------

--
-- Table structure for table `ordinance_status`
--

CREATE TABLE `ordinance_status` (
  `id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `action_type` enum('Draft','Under Review','Pending Approval','Initial Planning','Public Comment Period','Approved','Rejected','Implemented') NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordinance_status`
--

INSERT INTO `ordinance_status` (`id`, `proposal_id`, `remarks`, `action_type`, `action_date`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 23, 'adding more indeep review', 'Under Review', '2025-03-19 20:20:58', 62, '2025-03-19 20:20:58', '2025-03-19 20:20:58'),
(4, 23, 'adding more indeep review', 'Approved', '2025-04-17 23:35:56', 62, '2025-04-17 23:35:56', '2025-04-17 23:35:56'),
(5, 24, 'Under Review', 'Under Review', '2025-04-18 19:23:27', 62, '2025-04-18 19:23:27', '2025-04-18 19:23:27'),
(6, 24, 'Nothing to change anymore', 'Approved', '2025-04-18 19:51:04', 62, '2025-04-18 19:51:04', '2025-04-18 19:51:04'),
(8, 24, 'Nothing to change anymore', 'Rejected', '2025-04-30 20:28:34', 37, '2025-04-30 20:28:34', '2025-04-30 20:28:34'),
(9, 25, 'dwadwadaw', 'Approved', '2025-04-30 20:38:19', 37, '2025-04-30 20:38:19', '2025-04-30 20:38:19');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `hearing_date` date NOT NULL,
  `hearing_time` time NOT NULL,
  `session_type` enum('Regular','Special') DEFAULT 'Regular',
  `reading_status` enum('Approved','Deferred','For Amendment') DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `hearing_status` enum('1st Hearing','2nd Hearing','3rd Hearing') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `proposal_id`, `hearing_date`, `hearing_time`, `session_type`, `reading_status`, `remarks`, `hearing_status`, `created_at`) VALUES
(6, 24, '2025-04-24', '02:00:00', 'Regular', 'For Amendment', 'dddddds', '2nd Hearing', '2025-04-25 17:46:07'),
(7, 23, '2025-05-03', '04:57:00', 'Regular', 'For Amendment', 'wadwadw', '1st Hearing', '2025-05-02 20:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(5, 'committee', 'alex.wilson@example.com', '$2y$10$S97DbCIHy7aUsSkfVTnnJuQzvIOK1bYrIiV5490e/8V9ktyYnOjfW', 'committee', '2025-03-05 14:49:48'),
(37, 'admin', 'BISOYWORK@GMAIL.COM', '$2y$10$suKURXBXzBU6uBcZq3WKXeS1thleY5Fv62VvWo704o.nXAcyJRmOq', 'admin', '2025-03-06 15:26:15'),
(62, 'legislator', '21323e@gmail.com', '$2y$10$Ob8jx5fXMtcemFm8wb195eqjtTAJ6/UeE913rrjru.z0XRMpO6kca', 'legislator', '2025-03-06 17:46:28'),
(70, 'secretary', 'secretary@gmail.com', '$2y$10$dv9fifMmwgSeg2APr1YW7uURzHbZw.E1fWg3G9TYsNyquSAZfeT6G', 'secretary', '2025-05-02 16:41:32'),
(71, 'user', 'user@gmail.com', '$2y$10$9YTCHzrfj4xQh7QecrN7ZOKH3cE4ThOEpWNYMAJlzMjNNSv4ORJvS', 'user', '2025-05-02 16:41:53'),
(72, 'legislator1', 'legislator1@gmail.com', '$2y$10$Iz3sceu/ujMC6nEFlpqX1u/AJUJGjiOOONqXdEVCXRYDGm3WBOCHW', 'legislator', '2025-05-02 18:29:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `committees`
--
ALTER TABLE `committees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_committee_name` (`name`);

--
-- Indexes for table `ordinance_proposals`
--
ALTER TABLE `ordinance_proposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ordinance_committee` (`committee_id`),
  ADD KEY `fk_ordinance_user` (`user_id`);

--
-- Indexes for table `ordinance_status`
--
ALTER TABLE `ordinance_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ordinance_status_proposal` (`proposal_id`),
  ADD KEY `added_by` (`user_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_id` (`proposal_id`);

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
-- AUTO_INCREMENT for table `committees`
--
ALTER TABLE `committees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ordinance_proposals`
--
ALTER TABLE `ordinance_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `ordinance_status`
--
ALTER TABLE `ordinance_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ordinance_proposals`
--
ALTER TABLE `ordinance_proposals`
  ADD CONSTRAINT `fk_ordinance_committee` FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ordinance_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ordinance_status`
--
ALTER TABLE `ordinance_status`
  ADD CONSTRAINT `fk_ordinance_status_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `ordinance_proposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ordinance_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `ordinance_proposals` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
