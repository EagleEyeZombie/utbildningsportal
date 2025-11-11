-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 11, 2025 at 04:33 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2025_login`
--

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `r_id` int NOT NULL,
  `r_name` varchar(255) NOT NULL,
  `r_level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int NOT NULL,
  `u_name` varchar(255) NOT NULL,
  `u_fname` varchar(255) NOT NULL,
  `u_lname` varchar(255) NOT NULL,
  `u_email` varchar(255) NOT NULL,
  `u_password` varchar(255) NOT NULL,
  `u_lastlogin` datetime NOT NULL,
  `u_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `u_isactive` tinyint(1) NOT NULL,
  `u_role_fk` int NOT NULL,
  `u_class_fk` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`),
  ADD KEY `u_role_fk` (`u_role_fk`),
  ADD KEY `u_class_fk` (`u_class_fk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `r_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `classes`
--
CREATE TABLE `classes` (
  `c_id` int NOT NULL,
  `c_name` varchar(255) NOT NULL,
  `c_progress_speed_fk` int DEFAULT NULL,
  `c_teacher_fk` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `c_progress_speed_fk` (`c_progress_speed_fk`),
  ADD KEY `c_teacher_fk` (`c_teacher_fk`);

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `c_id` int NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `progress_speeds`
--
CREATE TABLE `progress_speeds` (
  `ps_id` int NOT NULL,
  `ps_name` varchar(255) NOT NULL,
  `ps_level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `progress_speeds`
--
ALTER TABLE `progress_speeds`
  ADD PRIMARY KEY (`ps_id`);

--
-- AUTO_INCREMENT for table `progress_speeds`
--
ALTER TABLE `progress_speeds`
  MODIFY `ps_id` int NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `tasks`
--
CREATE TABLE `tasks` (
  `t_id` int NOT NULL,
  `t_name` varchar(255) NOT NULL,
  `t_type_fk` int NOT NULL,
  `t_teacher_fk` int DEFAULT NULL,
  `t_text` text,
  `t_questions` text COMMENT 'Kan vara JSON-data med frågor och svar',
  `t_level_fk` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`t_id`),
  ADD KEY `t_type_fk` (`t_type_fk`),
  ADD KEY `t_teacher_fk` (`t_teacher_fk`),
  ADD KEY `t_level_fk` (`t_level_fk`);

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `t_id` int NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `task_levels`
--
CREATE TABLE `task_levels` (
  `tl_id` int NOT NULL,
  `tl_name` varchar(255) NOT NULL,
  `tl_level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `task_levels`
--
ALTER TABLE `task_levels`
  ADD PRIMARY KEY (`tl_id`);

--
-- AUTO_INCREMENT for table `task_levels`
--
ALTER TABLE `task_levels`
  MODIFY `tl_id` int NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `task_types`
--
CREATE TABLE `task_types` (
  `tt_id` int NOT NULL,
  `tt_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `task_types`
--
ALTER TABLE `task_types`
  ADD PRIMARY KEY (`tt_id`);

--
-- AUTO_INCREMENT for table `task_types`
--
ALTER TABLE `task_types`
  MODIFY `tt_id` int NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `student_tasks`
--
CREATE TABLE `student_tasks` (
  `st_id` int NOT NULL,
  `st_s_id_fk` int NOT NULL COMMENT 'Student ID (user)',
  `st_t_id_fk` int NOT NULL COMMENT 'Task ID',
  `st_completed` tinyint(1) NOT NULL DEFAULT '0',
  `st_score` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `student_tasks`
--
ALTER TABLE `student_tasks`
  ADD PRIMARY KEY (`st_id`),
  ADD KEY `st_s_id_fk` (`st_s_id_fk`),
  ADD KEY `st_t_id_fk` (`st_t_id_fk`);

--
-- AUTO_INCREMENT for table `student_tasks`
--
ALTER TABLE `student_tasks`
  MODIFY `st_id` int NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `student_achivements`
--
CREATE TABLE `student_achivements` (
  `sa_id` int NOT NULL,
  `sa_s_id_fk` int NOT NULL COMMENT 'Student ID (user)',
  `sa_name` varchar(255) NOT NULL,
  `sa_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for table `student_achivements`
--
ALTER TABLE `student_achivements`
  ADD PRIMARY KEY (`sa_id`),
  ADD KEY `sa_s_id_fk` (`sa_s_id_fk`);

--
-- AUTO_INCREMENT for table `student_achivements`
--
ALTER TABLE `student_achivements`
  MODIFY `sa_id` int NOT NULL AUTO_INCREMENT;



--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`u_role_fk`) REFERENCES `roles` (`r_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`u_class_fk`) REFERENCES `classes` (`c_id`) ON DELETE SET NULL;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`c_progress_speed_fk`) REFERENCES `progress_speeds` (`ps_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`c_teacher_fk`) REFERENCES `users` (`u_id`) ON DELETE SET NULL;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`t_type_fk`) REFERENCES `task_types` (`tt_id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`t_teacher_fk`) REFERENCES `users` (`u_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`t_level_fk`) REFERENCES `task_levels` (`tl_id`);

--
-- Constraints for table `student_tasks`
--
ALTER TABLE `student_tasks`
  ADD CONSTRAINT `student_tasks_ibfk_1` FOREIGN KEY (`st_s_id_fk`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_tasks_ibfk_2` FOREIGN KEY (`st_t_id_fk`) REFERENCES `tasks` (`t_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_achivements`
--
ALTER TABLE `student_achivements`
  ADD CONSTRAINT `student_achivements_ibfk_1` FOREIGN KEY (`sa_s_id_fk`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
