CREATE DATABASE `Klinton_03_presched` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `Klinton_03_presched`;

CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(255) NOT NULL,
  `team_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `employees` (`id`, `employee_name`, `team_id`) VALUES
(9,	'Rebecca',	2),
(10,	'Jeeva',	4),
(11,	'Klinton',	6),
(12,	'Christy',	5),
(13,	'Deepika',	7),
(14,	'Klinton',	8);

CREATE TABLE `list_wee` (
  `id` int NOT NULL AUTO_INCREMENT,
  `week_name` varchar(255) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `list_wee` (`id`, `week_name`, `from_date`, `to_date`) VALUES
(9,	'week_1',	'2024-09-01',	'2024-09-07'),
(10,	'week_12',	'2024-09-08',	'2024-09-14'),
(21,	'week_50',	'2024-09-15',	'2024-09-21'),
(22,	'week_100',	'2024-09-01',	'2024-09-07'),
(25,	'week_10',	'2024-09-22',	'2024-09-28');

CREATE TABLE `teams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `team_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `teams` (`id`, `team_name`) VALUES
(1,	'Teach'),
(2,	'Tech'),
(4,	'Design '),
(5,	'Content'),
(6,	'Digital Mark'),
(7,	'HR'),
(8,	'Management');

CREATE TABLE `week_table_week_1` (
  `emp_id` int NOT NULL,
  `team_id` int NOT NULL,
  `team_name` varchar(32) NOT NULL,
  `sun` varchar(10) DEFAULT NULL,
  `mon` varchar(10) DEFAULT NULL,
  `tue` varchar(10) DEFAULT NULL,
  `wed` varchar(10) DEFAULT NULL,
  `thu` varchar(10) DEFAULT NULL,
  `fri` varchar(10) DEFAULT NULL,
  `sat` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `week_table_week_10` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emp_id` int NOT NULL,
  `team_id` int NOT NULL,
  `team_name` varchar(32) DEFAULT NULL,
  `employee_name` varchar(128) DEFAULT NULL,
  `sun` varchar(10) DEFAULT NULL,
  `mon` varchar(10) DEFAULT NULL,
  `tue` varchar(10) DEFAULT NULL,
  `wed` varchar(10) DEFAULT NULL,
  `thu` varchar(10) DEFAULT NULL,
  `fri` varchar(10) DEFAULT NULL,
  `sat` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `week_table_week_10` (`id`, `emp_id`, `team_id`, `team_name`, `employee_name`, `sun`, `mon`, `tue`, `wed`, `thu`, `fri`, `sat`) VALUES
(1,	14,	8,	'Management',	'Klinton',	'04:00',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	11,	8,	'Management',	'Klinton',	NULL,	'02:30',	NULL,	NULL,	NULL,	NULL,	NULL),
(3,	9,	2,	'Tech',	'Rebecca',	NULL,	NULL,	NULL,	NULL,	NULL,	'02:00',	NULL),
(4,	10,	5,	'Content',	'Jeeva',	NULL,	NULL,	'03:00',	NULL,	NULL,	NULL,	NULL);

CREATE TABLE `week_table_week_12` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emp_id` int DEFAULT NULL,
  `team_id` int DEFAULT NULL,
  `team_name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `employee_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `sun` varchar(10) DEFAULT NULL,
  `mon` varchar(10) DEFAULT NULL,
  `tue` varchar(10) DEFAULT NULL,
  `wed` varchar(10) DEFAULT NULL,
  `thu` varchar(10) DEFAULT NULL,
  `fri` varchar(10) DEFAULT NULL,
  `sat` varchar(10) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `week_table_week_12` (`id`, `emp_id`, `team_id`, `team_name`, `employee_name`, `sun`, `mon`, `tue`, `wed`, `thu`, `fri`, `sat`) VALUES
(9,	10,	4,	'Design ',	'Jeeva',	NULL,	'02:00',	NULL,	'02:00',	NULL,	NULL,	NULL),
(10,	10,	1,	'Teach',	'Jeeva',	NULL,	'02:00',	NULL,	NULL,	NULL,	NULL,	NULL),
(11,	9,	2,	'Tech',	'Rebecca',	NULL,	'04:00',	'02:30',	NULL,	NULL,	NULL,	NULL),
(12,	11,	2,	'Tech',	'Klinton',	NULL,	NULL,	'04:00',	NULL,	NULL,	NULL,	NULL);

CREATE TABLE `week_table_week_50` (
  `id` int NOT NULL AUTO_INCREMENT,
  `emp_id` int NOT NULL,
  `team_id` int NOT NULL,
  `team_name` varchar(32) DEFAULT NULL,
  `employee_name` varchar(128) DEFAULT NULL,
  `sun` varchar(10) DEFAULT NULL,
  `mon` varchar(10) DEFAULT NULL,
  `tue` varchar(10) DEFAULT NULL,
  `wed` varchar(10) DEFAULT NULL,
  `thu` varchar(10) DEFAULT NULL,
  `fri` varchar(10) DEFAULT NULL,
  `sat` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `week_table_week_50` (`id`, `emp_id`, `team_id`, `team_name`, `employee_name`, `sun`, `mon`, `tue`, `wed`, `thu`, `fri`, `sat`) VALUES
(1,	12,	2,	'Tech',	'Christy',	NULL,	'02:30',	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	10,	2,	'Tech',	'Jeeva',	NULL,	'04:00',	NULL,	NULL,	NULL,	NULL,	NULL),
(3,	11,	1,	'Teach',	'Klinton',	NULL,	NULL,	'02:30',	NULL,	NULL,	NULL,	NULL),
(4,	9,	4,	'Design ',	'Rebecca',	NULL,	'01:30',	NULL,	NULL,	NULL,	NULL,	NULL);

CREATE TABLE `work_hours` (
  `id` int NOT NULL AUTO_INCREMENT,
  `week_id` int NOT NULL,
  `team_id` int NOT NULL,
  `emp_id` int NOT NULL,
  `day` varchar(32) NOT NULL,
  `sun` int NOT NULL,
  `mon` int NOT NULL,
  `tue` int NOT NULL,
  `wed` int NOT NULL,
  `thu` int NOT NULL,
  `fri` int NOT NULL,
  `sat` int NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `week_id` (`week_id`),
  KEY `team_id` (`team_id`),
  KEY `emp_id` (`emp_id`),
  CONSTRAINT `work_hours_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `list_wee` (`id`),
  CONSTRAINT `work_hours_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `work_hours_ibfk_3` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;