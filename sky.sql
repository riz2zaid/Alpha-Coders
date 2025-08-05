-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.35 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for skylink_main
CREATE DATABASE IF NOT EXISTS `skylink_main` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `skylink_main`;

-- Dumping structure for table skylink_main.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.admin: ~1 rows (approximately)
DELETE FROM `admin`;
INSERT INTO `admin` (`id`, `username`, `password`) VALUES
	(1, 'skylink@admin', '12345678');

-- Dumping structure for table skylink_main.audit_log
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action` varchar(250) NOT NULL,
  `time_stamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `request_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_audit_log_request1_idx` (`request_id`),
  CONSTRAINT `fk_audit_log_request1` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.audit_log: ~0 rows (approximately)
DELETE FROM `audit_log`;

-- Dumping structure for table skylink_main.billing
CREATE TABLE IF NOT EXISTS `billing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `amount` varchar(45) NOT NULL,
  `bill_type` enum('Monthly','Subsciption','Additional Candidates') NOT NULL,
  `bill_date` date NOT NULL,
  `due_date` date NOT NULL,
  `payment_status` enum('Paid','Panding','OverDue') NOT NULL,
  `bnk_slip` varchar(250) NOT NULL,
  `slip_status` enum('Panding','Approved','Rejecterd','Not Uploarderd') NOT NULL,
  `candidate_purchase_count` varchar(45) NOT NULL,
  `payment_link` varchar(45) NOT NULL,
  `request_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_billing_request1_idx` (`request_id`),
  CONSTRAINT `fk_billing_request1` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.billing: ~3 rows (approximately)
DELETE FROM `billing`;
INSERT INTO `billing` (`id`, `amount`, `bill_type`, `bill_date`, `due_date`, `payment_status`, `bnk_slip`, `slip_status`, `candidate_purchase_count`, `payment_link`, `request_id`) VALUES
	(26, '20000', 'Subsciption', '2025-07-28', '2025-08-27', 'Paid', 'slips/6887cac0cb7c3.png', 'Approved', '0', 'NULL', 5),
	(27, '20000', 'Subsciption', '2025-07-28', '2025-08-27', 'Paid', 'slips/6887cac0cb7c3.png', 'Approved', '0', 'NULL', 5),
	(28, '20000', 'Subsciption', '2025-07-28', '2025-08-27', 'Paid', 'slips/6887cac0cb7c3.png', 'Approved', '0', 'NULL', 5);

-- Dumping structure for table skylink_main.candidate
CREATE TABLE IF NOT EXISTS `candidate` (
  `id` int NOT NULL AUTO_INCREMENT,
  `added_date` varchar(45) NOT NULL,
  `request_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_candidate_request1_idx` (`request_id`),
  CONSTRAINT `fk_candidate_request1` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.candidate: ~0 rows (approximately)
DELETE FROM `candidate`;

-- Dumping structure for table skylink_main.candidates
CREATE TABLE IF NOT EXISTS `candidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(150) DEFAULT NULL,
  `dob` varchar(45) DEFAULT NULL,
  `profile_photo` varchar(250) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `district_province` varchar(100) DEFAULT NULL,
  `contact_number` varchar(10) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `nic_no` varchar(100) DEFAULT NULL,
  `issue_date` varchar(100) DEFAULT NULL,
  `nic_front` varchar(250) DEFAULT NULL,
  `nic_back` varchar(250) DEFAULT NULL,
  `passport_no` varchar(150) DEFAULT NULL,
  `country_issue` varchar(45) DEFAULT NULL,
  `passport_issue` varchar(100) DEFAULT NULL,
  `passport_expair` varchar(100) DEFAULT NULL,
  `passport_scan` varchar(250) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `relationship` varchar(45) DEFAULT NULL,
  `mobile_no` varchar(10) DEFAULT NULL,
  `alternative_no` varchar(10) DEFAULT NULL,
  `o_address` varchar(250) DEFAULT NULL,
  `highest_qualification` varchar(105) DEFAULT NULL,
  `fieldofstudy` varchar(45) DEFAULT NULL,
  `institute_name` varchar(100) DEFAULT NULL,
  `complete_year` varchar(100) DEFAULT NULL,
  `certificate_image` varchar(250) DEFAULT NULL,
  `language` varchar(45) DEFAULT NULL,
  `work_skill` varchar(45) DEFAULT NULL,
  `technical_image` varchar(250) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `position` varchar(45) DEFAULT NULL,
  `duration` varchar(45) DEFAULT NULL,
  `country_work` varchar(45) DEFAULT NULL,
  `reasonof_leave` varchar(300) DEFAULT NULL,
  `experiance_letter` varchar(250) DEFAULT NULL,
  `blood` varchar(45) DEFAULT NULL,
  `allergies` varchar(45) DEFAULT NULL,
  `chronic` varchar(45) DEFAULT NULL,
  `medical_report` varchar(250) DEFAULT NULL,
  `covid_card` varchar(250) DEFAULT NULL,
  `vision_test` varchar(250) DEFAULT NULL,
  `fitness_status` varchar(150) DEFAULT NULL,
  `police_certificate` varchar(250) DEFAULT NULL,
  `police_issuedate` varchar(100) DEFAULT NULL,
  `police_expiredate` varchar(100) DEFAULT NULL,
  `other_document` varchar(250) DEFAULT NULL,
  `cv` varchar(250) DEFAULT NULL,
  `gender_id` int DEFAULT NULL,
  `clivil_status_id` int DEFAULT NULL,
  `passport_status_id` int DEFAULT NULL,
  `computer_skill_id` int DEFAULT NULL,
  `candidate_option_id` int DEFAULT NULL,
  `register_date` date DEFAULT NULL,
  `agreement` varchar(250) DEFAULT NULL,
  `contract` varchar(250) DEFAULT NULL,
  `undertaking_letter` varchar(250) DEFAULT NULL,
  `request_id` int NOT NULL,
  `gs_letter` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_candidate_gender_idx` (`gender_id`),
  KEY `fk_candidate_clivil_status1_idx` (`clivil_status_id`),
  KEY `fk_candidate_passport_status1_idx` (`passport_status_id`),
  KEY `fk_candidate_computer_skill1_idx` (`computer_skill_id`),
  KEY `fk_candidate_candidate_option1_idx` (`candidate_option_id`),
  KEY `fk_candidates_request1_idx` (`request_id`),
  CONSTRAINT `fk_candidate_candidate_option1` FOREIGN KEY (`candidate_option_id`) REFERENCES `candidate_option` (`id`),
  CONSTRAINT `fk_candidate_clivil_status1` FOREIGN KEY (`clivil_status_id`) REFERENCES `clivil_status` (`id`),
  CONSTRAINT `fk_candidate_computer_skill1` FOREIGN KEY (`computer_skill_id`) REFERENCES `computer_skill` (`id`),
  CONSTRAINT `fk_candidate_gender` FOREIGN KEY (`gender_id`) REFERENCES `gender` (`id`),
  CONSTRAINT `fk_candidate_passport_status1` FOREIGN KEY (`passport_status_id`) REFERENCES `passport_status` (`id`),
  CONSTRAINT `fk_candidates_request1` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.candidates: ~1 rows (approximately)
DELETE FROM `candidates`;
INSERT INTO `candidates` (`id`, `fullname`, `dob`, `profile_photo`, `address`, `district_province`, `contact_number`, `email`, `nic_no`, `issue_date`, `nic_front`, `nic_back`, `passport_no`, `country_issue`, `passport_issue`, `passport_expair`, `passport_scan`, `name`, `relationship`, `mobile_no`, `alternative_no`, `o_address`, `highest_qualification`, `fieldofstudy`, `institute_name`, `complete_year`, `certificate_image`, `language`, `work_skill`, `technical_image`, `company_name`, `position`, `duration`, `country_work`, `reasonof_leave`, `experiance_letter`, `blood`, `allergies`, `chronic`, `medical_report`, `covid_card`, `vision_test`, `fitness_status`, `police_certificate`, `police_issuedate`, `police_expiredate`, `other_document`, `cv`, `gender_id`, `clivil_status_id`, `passport_status_id`, `computer_skill_id`, `candidate_option_id`, `register_date`, `agreement`, `contract`, `undertaking_letter`, `request_id`, `gs_letter`) VALUES
	(1, 'Nimas Ahamed', '2003-11-12', '6887e9fbcfb35.jpg', 'no-03\r\nmain rd', 'uva', '0760895111', 'nimasahamed15@gmail.com', '', '', NULL, NULL, '', 'Sri Lanka', '', '', NULL, '', '', '', '', NULL, '', '', '', '', NULL, '', '', NULL, '', '', '', '', '', NULL, '', '', '', NULL, NULL, NULL, '', NULL, '', '', NULL, NULL, 1, 1, 1, 1, 1, '2025-07-29', NULL, NULL, NULL, 5, NULL);

-- Dumping structure for table skylink_main.candidate_option
CREATE TABLE IF NOT EXISTS `candidate_option` (
  `id` int NOT NULL AUTO_INCREMENT,
  `option` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.candidate_option: ~2 rows (approximately)
DELETE FROM `candidate_option`;
INSERT INTO `candidate_option` (`id`, `option`) VALUES
	(1, 'Going Abroad through the Agency'),
	(2, 'Local work (Not Going Abroad)');

-- Dumping structure for table skylink_main.clivil_status
CREATE TABLE IF NOT EXISTS `clivil_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.clivil_status: ~2 rows (approximately)
DELETE FROM `clivil_status`;
INSERT INTO `clivil_status` (`id`, `status`) VALUES
	(1, 'Married'),
	(2, 'Single');

-- Dumping structure for table skylink_main.computer_skill
CREATE TABLE IF NOT EXISTS `computer_skill` (
  `id` int NOT NULL AUTO_INCREMENT,
  `skill` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.computer_skill: ~3 rows (approximately)
DELETE FROM `computer_skill`;
INSERT INTO `computer_skill` (`id`, `skill`) VALUES
	(1, 'Basic'),
	(2, 'Intermiditate'),
	(3, 'Expert');

-- Dumping structure for table skylink_main.country
CREATE TABLE IF NOT EXISTS `country` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cname` varchar(100) NOT NULL,
  `request_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_country_request1_idx` (`request_id`),
  CONSTRAINT `fk_country_request1` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.country: ~1 rows (approximately)
DELETE FROM `country`;
INSERT INTO `country` (`id`, `cname`, `request_id`) VALUES
	(1, 'England', 5);

-- Dumping structure for table skylink_main.gender
CREATE TABLE IF NOT EXISTS `gender` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.gender: ~3 rows (approximately)
DELETE FROM `gender`;
INSERT INTO `gender` (`id`, `name`) VALUES
	(1, 'Male'),
	(2, 'Female'),
	(3, 'Other');

-- Dumping structure for table skylink_main.passport_status
CREATE TABLE IF NOT EXISTS `passport_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.passport_status: ~2 rows (approximately)
DELETE FROM `passport_status`;
INSERT INTO `passport_status` (`id`, `status`) VALUES
	(1, 'Valid'),
	(2, 'Expair');

-- Dumping structure for table skylink_main.payment_gatway
CREATE TABLE IF NOT EXISTS `payment_gatway` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gatway_name` varchar(45) NOT NULL,
  `gatway_icon` varchar(45) NOT NULL,
  `gatway_image` varchar(250) NOT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `link` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.payment_gatway: ~0 rows (approximately)
DELETE FROM `payment_gatway`;

-- Dumping structure for table skylink_main.request
CREATE TABLE IF NOT EXISTS `request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `bname` varchar(100) NOT NULL,
  `baddress` varchar(150) NOT NULL,
  `status_request` enum('Panding','Approved','Rejecterd') NOT NULL,
  `registration_date` date NOT NULL,
  `is_spacial_client` tinyint NOT NULL DEFAULT '0',
  `registration_fee_status` enum('Paid','Panding','Deferred') NOT NULL,
  `subscription_start_date` date NOT NULL,
  `subscription_end_date` date NOT NULL,
  `subscription_status` enum('Active','Grace','Blocked') NOT NULL,
  `candidate_limit` varchar(45) NOT NULL,
  `canditate_added` varchar(45) NOT NULL,
  `allow_additional_candidates` tinyint NOT NULL DEFAULT '1',
  `subscription_plans_id` int NOT NULL,
  `slip` varchar(250) NOT NULL,
  `password` varchar(100) NOT NULL,
  `year_start_date` date DEFAULT NULL,
  `year_end_date` date DEFAULT NULL,
  `mobile` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_request_subscription_plans_idx` (`subscription_plans_id`),
  CONSTRAINT `fk_request_subscription_plans` FOREIGN KEY (`subscription_plans_id`) REFERENCES `subscription_plans` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.request: ~1 rows (approximately)
DELETE FROM `request`;
INSERT INTO `request` (`id`, `fullname`, `email`, `bname`, `baddress`, `status_request`, `registration_date`, `is_spacial_client`, `registration_fee_status`, `subscription_start_date`, `subscription_end_date`, `subscription_status`, `candidate_limit`, `canditate_added`, `allow_additional_candidates`, `subscription_plans_id`, `slip`, `password`, `year_start_date`, `year_end_date`, `mobile`) VALUES
	(5, 'Nimas Ahamed', 'nimasahamed15@gmail.com', 'flexmobo', 'Welimada', 'Approved', '2025-07-28', 0, 'Paid', '2025-07-28', '2025-08-27', 'Active', '200', 'No', 0, 5, 'slips/6887cac0cb7c3.png', 'r1koYNlq', '2025-07-28', '2026-07-23', '0760895111');

-- Dumping structure for table skylink_main.subscription_plans
CREATE TABLE IF NOT EXISTS `subscription_plans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(45) NOT NULL,
  `monthly_rental` varchar(45) NOT NULL,
  `advantage` varchar(200) NOT NULL,
  `base_candidate_limit` varchar(45) NOT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.subscription_plans: ~1 rows (approximately)
DELETE FROM `subscription_plans`;
INSERT INTO `subscription_plans` (`id`, `plan_name`, `monthly_rental`, `advantage`, `base_candidate_limit`, `is_active`) VALUES
	(5, 'Basic', '3500', '200 Candidate Access and control', '200', 1);

-- Dumping structure for table skylink_main.vacancy
CREATE TABLE IF NOT EXISTS `vacancy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `job_position` varchar(45) NOT NULL,
  `created_at` date NOT NULL,
  `country_id` int NOT NULL,
  `request_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_vacancy_country1_idx` (`country_id`),
  KEY `fk_vacancy_request1_idx` (`request_id`),
  CONSTRAINT `fk_vacancy_country1` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`),
  CONSTRAINT `fk_vacancy_request1` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table skylink_main.vacancy: ~1 rows (approximately)
DELETE FROM `vacancy`;
INSERT INTO `vacancy` (`id`, `job_position`, `created_at`, `country_id`, `request_id`) VALUES
	(2, 'House Cleaner girl', '2025-07-29', 1, 5);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
