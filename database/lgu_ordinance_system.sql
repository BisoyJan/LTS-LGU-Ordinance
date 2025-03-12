-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2025 at 08:19 PM
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
  `status` enum('Draft','Under Review','Pending Approval','Initial Planning','Public Comment Period','Approved','Rejected','Implemented') NOT NULL,
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

INSERT INTO `ordinance_proposals` (`id`, `proposal`, `proposal_date`, `details`, `status`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 'Street Renaming Protocol', '2025-03-15', 'Guidelines for renaming streets after historical figures.', 'Draft', 'street_renaming_draft.pdf', 'uploads/ordinances/street_renaming_draft.pdf', 'pdf', 1245678, '2025-03-12 13:31:43', '2025-03-12 13:31:43'),
(2, 'Public Building Designation Standards', '2025-03-20', 'Framework for naming public buildings and facilities.', 'Under Review', 'building_standards_v2.docx', 'uploads/ordinances/building_standards_v2.docx', 'docx', 856321, '2025-03-12 13:31:43', '2025-03-12 13:31:43'),
(3, 'Memorial Landmark Criteria', '2025-03-25', 'Criteria for dedicating landmarks as memorials.', 'Pending Approval', 'memorial_criteria.pdf', 'uploads/ordinances/memorial_criteria.pdf', 'pdf', 2345987, '2025-03-12 13:31:43', '2025-03-12 13:31:43'),
(4, 'Cultural District Naming Convention', '2025-04-01', 'Standards for designating and naming cultural districts.', 'Initial Planning', 'cultural_districts.pdf', 'uploads/ordinances/cultural_districts.pdf', 'pdf', 1567432, '2025-03-12 13:31:43', '2025-03-12 13:31:43'),
(5, 'Parks and Recreation Naming Policy', '2025-04-10', 'Comprehensive policy for naming parks and recreational areas.', 'Public Comment Period', 'parks_naming_policy.docx', 'uploads/ordinances/parks_naming_policy.docx', 'docx', 967845, '2025-03-12 13:31:43', '2025-03-12 13:31:43'),
(11, 'awdwadwad', '2025-03-12', 'wadwadwadwa', 'Under Review', '67d19c85dd7725.25413300.docx', '../../assets/file/ordinance_proposals/67d19c85dd7725.25413300.docx', 'docx', 156118, '2025-03-12 14:39:01', '2025-03-12 14:39:01'),
(12, 'awdwadwad', '2025-03-13', 'wadwadadwad', 'Draft', '67d19db15a8cd7.54208694.docx', '../../assets/file/ordinance_proposals/67d19db15a8cd7.54208694.docx', 'docx', 156118, '2025-03-12 14:44:01', '2025-03-12 14:44:01'),
(13, 'awdwadwad', '2025-03-13', 'wadwadwa', 'Draft', '67d19ea9c96c79.28355412.docx', '../../assets/file/ordinance_proposals/67d19ea9c96c79.28355412.docx', 'docx', 156118, '2025-03-12 14:48:09', '2025-03-12 14:48:09'),
(14, '1111', '2025-03-13', 'wadwadwad', 'Draft', '67d19ebe4e28c0.85491065.docx', '../../assets/file/ordinance_proposals/67d19ebe4e28c0.85491065.docx', 'docx', 21571, '2025-03-12 14:48:30', '2025-03-12 14:48:30'),
(15, '222', '2025-03-12', 'wwwwww', 'Draft', '1741791140_PC build Canvas.docx', '../../assets/file/ordinance_proposals/1741791140_PC build Canvas.docx', 'docx', 21571, '2025-03-12 14:52:20', '2025-03-12 14:52:20'),
(16, '3333', '2025-03-12', '333333', 'Draft', '1741791185_PC build Canvas.docx', '../../assets/file/ordinance_proposals/1741791185_PC build Canvas.docx', 'docx', 21571, '2025-03-12 14:53:05', '2025-03-12 14:53:05'),
(19, '4444', '2025-03-12', '4444', 'Draft', 'PC build Canvas.docx', '../../assets/file/ordinance_proposals/PC build Canvas.docx', 'docx', 21571, '2025-03-12 14:56:45', '2025-03-12 14:56:45'),
(20, '5555', '2025-03-12', '5555', 'Draft', 'use-case-diagram.docx', '../../assets/file/ordinance_proposals/use-case-diagram.docx', 'docx', 156118, '2025-03-12 15:13:35', '2025-03-12 15:13:35'),
(21, '666', '2025-03-12', '6666', 'Draft', 'logical-design.docx', '../../assets/file/ordinance_proposals/logical-design.docx', 'docx', 129105, '2025-03-12 15:20:13', '2025-03-12 15:20:13'),
(33, '7777', '2025-03-13', '7777777', 'Approved', 'Jan Ramil P Intong - CV.pdf', '../../assets/file/ordinance_proposals/Jan Ramil P Intong - CV.pdf', 'pdf', 205783, '2025-03-12 18:49:45', '2025-03-12 18:49:45');

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
(1, 'johndoe', 'john.doe@example.com', 'hashedpassword123', 'user', '2025-03-05 14:49:48'),
(2, 'janesmith', 'jane.smith@example.com', 'securepass456', 'admin', '2025-03-05 14:49:48'),
(3, 'mikebrown', 'mike.brown@example.com', 'safepassword789', 'editor', '2025-03-05 14:49:48'),
(4, 'sarahjones', 'sarah.jones@example.com', 'protectedpass321', 'user', '2025-03-05 14:49:48'),
(5, 'alexwilson', 'alex.wilson@example.com', 'guardedpass654', 'moderator', '2025-03-05 14:49:48'),
(37, 'admin', 'BISOYWORK@GMAIL.COM', 'admin', 'admin', '2025-03-06 15:26:15'),
(59, 'wadwadwa', '1234@gmail.com', '123132', 'admin', '2025-03-06 17:44:19'),
(61, 'wadwdwadaw', '222@gmail.com', 'admin', 'admin', '2025-03-06 17:45:53'),
(62, 'wadwad111', '21323@gmail.com', 'wadwadwa', 'admin', '2025-03-06 17:46:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ordinance_proposals`
--
ALTER TABLE `ordinance_proposals`
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
-- AUTO_INCREMENT for table `ordinance_proposals`
--
ALTER TABLE `ordinance_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
