SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `lgu_ordinance_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lgu_ordinance_system`;

CREATE TABLE `committees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `committee_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proposal_id` int(11) NOT NULL,
  `hearing_date` date NOT NULL,
  `hearing_time` time NOT NULL,
  `session_type` enum('Regular','Special') DEFAULT 'Regular',
  `reading_status` enum('Approved','Deferred','For Amendment') DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `hearing_status` ENUM('1st Hearing', '2nd Hearing', '3rd Hearing') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_schedule_proposal` (`proposal_id`),
  CONSTRAINT `fk_schedule_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `ordinance_proposals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `ordinance_status` (
  `id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `action_type` enum('Draft','Under Review','Pending Approval','Initial Planning','Public Comment Period','Approved','Rejected','Implemented') NOT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,  /* Changed from added_by back to user_id */
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `committees` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Appropriations', NULL, '2025-03-19 14:57:39', '2025-03-19 14:57:39'),
(2, 'Women and Family', NULL, '2025-03-19 14:57:39', '2025-03-19 14:57:39'),
(3, 'Human Rights', NULL, '2025-03-19 14:57:39', '2025-03-19 14:57:39'),
(4, 'Youth and Sports Development', NULL, '2025-03-19 14:57:39', '2025-03-19 14:57:39'),
(5, 'Environmental Protection', NULL, '2025-03-19 14:57:39', '2025-03-19 14:57:39'),
(6, 'Cooperatives', NULL, '2025-03-19 14:57:39', '2025-03-19 14:57:39'),
(7, 'Rules and Privileges', NULL, '2025-03-19 14:57:39', '2025-03-19 14:57:39');

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

ALTER TABLE `committees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_committee_name` (`name`);

ALTER TABLE `ordinance_proposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ordinance_committee` (`committee_id`),
  ADD KEY `fk_ordinance_user` (`user_id`);

ALTER TABLE `ordinance_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ordinance_status_proposal` (`proposal_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_committee` (`committee_id`);

ALTER TABLE `committees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `ordinance_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ordinance_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

ALTER TABLE `ordinance_proposals`
  ADD CONSTRAINT `fk_ordinance_committee` FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ordinance_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ordinance_status`
  ADD CONSTRAINT `fk_ordinance_status_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `ordinance_proposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ordinance_proposals`
  ADD COLUMN `current_status` varchar(100) DEFAULT NULL AFTER `proposal`;

ALTER TABLE `ordinance_proposals`
  DROP COLUMN `proposal_time`;

ALTER TABLE `schedule`
  CHANGE COLUMN `reading_result` `reading_status` ENUM('Approved', 'Deferred', 'For Amendment') DEFAULT NULL;

ALTER TABLE `schedule`
  ADD COLUMN `hearing_status` ENUM('1st Hearing', '2nd Hearing', '3rd Hearing') DEFAULT NULL AFTER `remarks`;

ALTER TABLE `users`
  ADD COLUMN `committee_id` int(11) DEFAULT NULL AFTER `role`,
  ADD KEY `fk_users_committee` (`committee_id`);

ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_committee` FOREIGN KEY (`committee_id`) REFERENCES `committees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `users`
  ADD COLUMN `name` varchar(100) NOT NULL AFTER `username`;

COMMIT;
