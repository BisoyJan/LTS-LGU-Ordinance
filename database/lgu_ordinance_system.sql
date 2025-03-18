-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2025 at 03:20 PM
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
-- Table structure for table `ordinance_proposals`
--

CREATE TABLE `ordinance_proposals` (
  `id` int(11) NOT NULL,
  `proposal` varchar(255) NOT NULL,
  `proposal_date` date NOT NULL,
  `details` text DEFAULT NULL,
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

INSERT INTO `ordinance_proposals` (`id`, `proposal`, `proposal_date`, `details`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(37, 'Testing', '2025-03-18', 'Testing', '37_use-case-diagram.docx', '15IFNhYWrDAQ6Wf5Wyn0mh7-3RlsMF729', 'docx', 156118, '2025-03-18 12:39:47', '2025-03-18 13:29:59'),
(38, '8888', '2025-03-18', 'Testing', '38_System Feature flow and functionalities.docx', '178Y6tN-tjO2RXD3eh4jfejfCn-b6CqS8', 'docx', 15863, '2025-03-18 13:28:55', '2025-03-18 13:32:48'),
(44, 'Sample', '2025-03-18', 'Hello World', '44_PC build Canvas.docx', '1NIdo4ndS-kd1-R4_x1AETuHScdrxLiWv', 'docx', 21571, '2025-03-18 13:49:52', '2025-03-18 13:50:18');

-- --------------------------------------------------------

--
-- Table structure for table `ordinance_status`
--

CREATE TABLE `ordinance_status` (
  `id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `action_type` enum('Draft','Under Review','Pending Approval','Initial Planning','Public Comment Period','Approved','Rejected','Implemented') NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordinance_status`
--

INSERT INTO `ordinance_status` (`id`, `proposal_id`, `user_id`, `remarks`, `action_type`, `action_date`, `created_at`, `updated_at`) VALUES
(6, 37, 62, 'Need more further investigation', '', '2025-03-18 13:16:27', '2025-03-18 13:16:27', '2025-03-18 13:16:27'),
(10, 38, 62, 'Testing', '', '2025-03-18 13:35:19', '2025-03-18 13:35:19', '2025-03-18 13:35:19'),
(11, 44, 62, 'Testing', '', '2025-03-18 13:50:18', '2025-03-18 13:50:18', '2025-03-18 13:50:18');

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
(1, 'johndoe', 'john.doe@example.com', '$2y$10$F9LAt3FbtGHodbSSKUzWAuBP7/r0hQv2Tag8A9C7TfxLnYp.65klG', 'user', '2025-03-05 14:49:48'),
(2, 'janesmith', 'jane.smith@example.com', '$2y$10$K8NTJy7j8/3xy7Vks/8W0.fMwZq5FvHyRk3ezRa.Y2jaEg5i5bT5S', 'admin', '2025-03-05 14:49:48'),
(3, 'mikebrown', 'mike.brown@example.com', '$2y$10$s4KjCG20Ly.XA53FIxBdgeJ7hZm2h52PbBjzBEkO4x17uNR8uTb2u', 'user', '2025-03-05 14:49:48'),
(4, 'sarahjones', 'sarah.jones@example.com', 'protectedpass321', 'user', '2025-03-05 14:49:48'),
(5, 'alexwilson', 'alex.wilson@example.com', 'guardedpass654', 'committee', '2025-03-05 14:49:48'),
(37, 'admin', 'BISOYWORK@GMAIL.COM', 'admin', 'admin', '2025-03-06 15:26:15'),
(59, 'wadwadwa', '1234@gmail.com', '123132', 'committee', '2025-03-06 17:44:19'),
(61, 'wadwdwadaw', '222@gmail.com', 'admin', 'admin', '2025-03-06 17:45:53'),
(62, '12', '21323e@gmail.com', '$2y$10$MCjIyL6FfX8f2r.hldwlSOO2EafHZlUX4lD60PpoD.EVdr.NFMPBK', 'admin', '2025-03-06 17:46:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ordinance_proposals`
--
ALTER TABLE `ordinance_proposals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ordinance_status`
--
ALTER TABLE `ordinance_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ordinance_status_proposal` (`proposal_id`),
  ADD KEY `fk_ordinance_status_user` (`user_id`);

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
-- AUTO_INCREMENT for table `ordinance_proposals`
--
ALTER TABLE `ordinance_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `ordinance_status`
--
ALTER TABLE `ordinance_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ordinance_status`
--
ALTER TABLE `ordinance_status`
  ADD CONSTRAINT `fk_ordinance_status_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `ordinance_proposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ordinance_status_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
