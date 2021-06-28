-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 28, 2021 at 11:59 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vuexy_ws`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_clients`
--

CREATE TABLE `tbl_clients` (
  `id` int(11) NOT NULL,
  `member_id` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `d_o_b` date DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `info` text DEFAULT NULL,
  `score` varchar(50) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_clients`
--

INSERT INTO `tbl_clients` (`id`, `member_id`, `first_name`, `middle_name`, `last_name`, `d_o_b`, `gender`, `email`, `mobile_number`, `country`, `region`, `info`, `score`, `avatar`, `created_at`, `updated_at`, `status`) VALUES
(8, '15', 'salal', NULL, 'khan', '1996-06-01', 'male', 'salal.khan91@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '2021-06-28 13:30:24', '2021-06-28 13:30:24', 0),
(9, '16', 'UMAR', NULL, 'NISAR', NULL, NULL, 'umarnisar021@gmail.com', NULL, NULL, NULL, NULL, NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/user-16.jpg', '2021-06-28 13:43:03', '2021-06-28 13:43:03', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_countries`
--

CREATE TABLE `tbl_countries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_code` varchar(50) NOT NULL,
  `currency` text NOT NULL,
  `flag` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_countries`
--

INSERT INTO `tbl_countries` (`id`, `name`, `short_code`, `currency`, `flag`, `created_at`, `updated_at`, `status`) VALUES
(11, 'Pakistan', 'PK', 'PKR', NULL, '2021-04-27 13:42:53', '2021-04-29 14:13:07', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_experts`
--

CREATE TABLE `tbl_experts` (
  `id` int(11) NOT NULL,
  `member_id` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `d_o_b` date DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `info` text DEFAULT NULL,
  `score` varchar(50) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_experts`
--

INSERT INTO `tbl_experts` (`id`, `member_id`, `first_name`, `middle_name`, `last_name`, `d_o_b`, `gender`, `email`, `mobile_number`, `country`, `region`, `info`, `score`, `avatar`, `created_at`, `updated_at`, `status`) VALUES
(1, '3', 'james', NULL, 'lart', '0000-00-00', 'Male', 'james@rxvj.com', '170972', 'Canada', NULL, NULL, NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/jameslart-3.jpeg', '2021-06-28 10:57:33', '2021-06-28 10:57:33', 0),
(2, '4', 'waqas', NULL, 'rajput', '1990-06-01', 'Male', 'waqas@gmail.com', '923322503077', 'Pakistan', NULL, NULL, NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/waqas-4.png', '2021-06-28 11:02:37', '2021-06-28 11:02:37', 0),
(3, '6', 'samad', NULL, 'khan', '1997-06-06', 'Male', 'samad@gmail.com', '92333333333', 'Pakistan', NULL, NULL, NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/samad-6.png', '2021-06-28 12:02:43', '2021-06-28 12:02:43', 0),
(4, '7', 'shaheer', NULL, 'khan', '1993-06-04', 'Male', 'shaheer@gmail.com', '92222222222', 'Pakistan', NULL, 'test', NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/shaheer-7.jpeg', '2021-06-28 12:04:55', '2021-06-28 15:16:11', 0),
(5, '17', 'kamran', NULL, 'khan', '1992-06-03', 'Male', 'kamran@gmail.com', '9200000000', 'Pakistan', NULL, 'test', NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/kamran-17.jpeg', '2021-06-28 15:18:04', '2021-06-28 15:28:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_experts_availability`
--

CREATE TABLE `tbl_experts_availability` (
  `id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `start_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_experts_education`
--

CREATE TABLE `tbl_experts_education` (
  `id` int(11) NOT NULL,
  `expert_id` varchar(255) NOT NULL,
  `institute_name` varchar(250) NOT NULL,
  `degree` varchar(250) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_experts_proposals`
--

CREATE TABLE `tbl_experts_proposals` (
  `id` int(11) NOT NULL,
  `task_id` varchar(50) NOT NULL,
  `expert_id` varchar(256) NOT NULL,
  `budget` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = new task\r\n1 = accepted\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_experts_skills`
--

CREATE TABLE `tbl_experts_skills` (
  `id` int(11) NOT NULL,
  `expert_id` varchar(50) NOT NULL,
  `skill_id` varchar(250) NOT NULL,
  `star` varchar(250) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_experts_skills`
--

INSERT INTO `tbl_experts_skills` (`id`, `expert_id`, `skill_id`, `star`, `created_at`, `updated_at`, `status`) VALUES
(1, '4', '12', '0', '2021-06-28 12:05:30', '2021-06-28 12:05:30', 0),
(2, '3', '6', '0', '2021-06-28 12:06:35', '2021-06-28 12:06:35', 0),
(3, '2', '11', '0', '2021-06-28 12:07:11', '2021-06-28 12:07:11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_experts_tools`
--

CREATE TABLE `tbl_experts_tools` (
  `id` int(11) NOT NULL,
  `expert_id` varchar(50) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_experts_tools`
--

INSERT INTO `tbl_experts_tools` (`id`, `expert_id`, `name`, `created_at`, `updated_at`) VALUES
(1, '4', 'Photoshop', '2021-06-28 12:05:45', '2021-06-28 12:05:45'),
(2, '4', 'HTML', '2021-06-28 12:05:53', '2021-06-28 12:05:53'),
(3, '4', 'CSS', '2021-06-28 12:05:56', '2021-06-28 12:05:56'),
(4, '4', 'Javascript', '2021-06-28 12:06:02', '2021-06-28 12:06:02'),
(5, '3', 'PHP Core', '2021-06-28 12:06:46', '2021-06-28 12:06:46'),
(6, '3', 'Codeigniter', '2021-06-28 12:06:52', '2021-06-28 12:06:52'),
(7, '3', 'Laravel', '2021-06-28 12:06:57', '2021-06-28 12:06:57'),
(8, '2', 'Excel', '2021-06-28 12:07:17', '2021-06-28 12:07:17');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_members`
--

CREATE TABLE `tbl_members` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `mobile_no` varchar(50) DEFAULT NULL,
  `is_seller` tinyint(4) DEFAULT 0,
  `is_buyer` tinyint(4) DEFAULT 0,
  `signup_with` tinyint(4) DEFAULT 0 COMMENT '0 normal,1 google, 2 facebook, 3 twitter',
  `google_id` varchar(150) DEFAULT NULL,
  `facebook_id` varchar(150) DEFAULT NULL,
  `twitter_id` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_members`
--

INSERT INTO `tbl_members` (`id`, `username`, `password`, `email`, `mobile_no`, `is_seller`, `is_buyer`, `signup_with`, `google_id`, `facebook_id`, `twitter_id`, `created_at`, `updated_at`, `status`, `token`) VALUES
(3, 'jameslart', '$2y$10$TbrCsSsN0OvFEFy1lpmmVeA2xZPzHJtSRcxl1R9Xb81Ca9BPm24qy', 'james@rxvj.com', NULL, 0, 0, 0, NULL, NULL, NULL, '2021-06-28 10:57:32', '2021-06-28 10:57:32', 0, NULL),
(4, 'waqas', '$2y$10$z0y0WNRCxj27Pwmh36ZHi.7op7.8e/SJllqoYfSbrljGED8muVULW', 'waqas@gmail.com', NULL, 0, 0, 0, NULL, NULL, NULL, '2021-06-28 11:02:36', '2021-06-28 11:02:36', 0, NULL),
(6, 'samad', '$2y$10$vzLgdmiWFr7PNlzd2hjjKeC68VorWz6XIL5NJjWfKrMzxhHP9GXnG', 'samad@gmail.com', NULL, 0, 0, 0, NULL, NULL, NULL, '2021-06-28 12:02:42', '2021-06-28 12:02:42', 0, NULL),
(7, 'shaheer', '$2y$10$o6yQSww32yT2foI/dC3Wfebbuw.z/2KfnjU/pSLM7OLvDWBpMdRxu', 'shaheer@gmail.com', NULL, 0, 0, 0, NULL, NULL, NULL, '2021-06-28 12:04:54', '2021-06-28 12:04:54', 0, NULL),
(15, 'salal', '$2y$10$EkORC8URnPo8SlEkN95Q.OgtV1OzdCxU/PL/WLXklVpZVsrjlXgm2', 'salal.khan91@gmail.com', NULL, 0, 0, 0, NULL, NULL, NULL, '2021-06-28 13:30:23', '2021-06-28 13:30:23', 0, NULL),
(16, NULL, NULL, 'umarnisar021@gmail.com', NULL, 0, 0, 0, NULL, NULL, NULL, '2021-06-28 13:43:01', '2021-06-28 13:43:03', 0, '35a44fc46f03f79279606bd67fa3a7b10ffed518592f461a67f6ef20696ce513fbcacbdfbf0c70fd4e36debce8a3643a2e7efc0cd35a35d52b23aafb08604845'),
(17, 'kamran', '$2y$10$Amw6TeQiB2AoOxyJlLGmG.jKPSrPasRGnUSsDtfSW8BqeEn.mu.KK', 'kamran@gmail.com', NULL, 0, 0, 0, NULL, NULL, NULL, '2021-06-28 15:18:03', '2021-06-28 15:18:03', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pages`
--

CREATE TABLE `tbl_pages` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_pages`
--

INSERT INTO `tbl_pages` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Home', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_pages_field`
--

CREATE TABLE `tbl_pages_field` (
  `id` int(11) NOT NULL,
  `page_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value_key` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `options_list` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `label` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'text',
  `created_at` datetime DEFAULT NULL,
  `modified_at` timestamp NULL DEFAULT current_timestamp(),
  `modify_by` int(11) DEFAULT NULL,
  `create_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `section` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `col_width` varchar(10) COLLATE utf8_unicode_ci DEFAULT '6'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_pages_field`
--

INSERT INTO `tbl_pages_field` (`id`, `page_id`, `value_key`, `value`, `options_list`, `label`, `type`, `created_at`, `modified_at`, `modify_by`, `create_by`, `updated_at`, `section`, `col_width`) VALUES
(1, '1', 'banners', '[{\"heading\":\"What Services Are<br>You Looking For?\",\"text\":\"We help businesses build their tempor sodales at sit amet quam etiam vel lacus consectetur.\",\"image\":\"https:\\/\\/x12va.s3.ap-south-1.amazonaws.com\\/banners\\/home_banner_0.png\"},{\"heading\":\"What Services Are<br>You Looking For?\",\"text\":\"We help businesses build their tempor sodales at sit amet quam etiam vel lacus consectetur.\",\"image\":\"https:\\/\\/x12va.s3.ap-south-1.amazonaws.com\\/banners\\/home_banner_1.png\"},{\"heading\":\"What Services Are<br>You Looking For?\",\"text\":\"We help businesses build their tempor sodales at sit amet quam etiam vel lacus consectetur.\",\"image\":\"https:\\/\\/x12va.s3.ap-south-1.amazonaws.com\\/banners\\/home_banner_3.png?60c7492db7b1d\"}]', NULL, 'Banner heading', 'text', '2013-10-22 00:00:00', '2021-02-11 06:49:28', 23, NULL, '2021-06-14 07:56:48', 'company_details', '6'),
(2, '1', 'marketplace', '{\"heading\":\"What Services Are<br>You Looking For?\",\"desc\":\"We help businesses build their tempor sodales at sit amet quam etiam vel lacus consectetur.\",\"items\":[{\"title\":\"Explore Platform\",\"icons\":\"\"},{\"title\":\"Bayesian Statistics\",\"icons\":\"\"},{\"title\":\"Aerospace Engineering\",\"icons\":\"\"}]}', NULL, 'Banner heading', 'text', '2013-10-22 00:00:00', '2021-02-11 06:49:28', 23, NULL, '2021-06-14 12:58:50', 'company_details', '6');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_skills`
--

CREATE TABLE `tbl_skills` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `short_code` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_skills`
--

INSERT INTO `tbl_skills` (`id`, `name`, `short_code`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Language Expert', 'LE', '2021-05-01 07:40:52', '2021-05-01 07:40:52', 0),
(2, 'Math Expert', 'MA', '2021-06-28 10:42:04', '2021-06-28 10:42:04', 0),
(3, 'English Expert', 'Eng', '2021-06-28 10:42:21', '2021-06-28 10:42:21', 0),
(4, 'C++', 'C++', '2021-06-28 10:42:40', '2021-06-28 10:42:40', 0),
(5, 'Java', 'JV', '2021-06-28 10:42:53', '2021-06-28 10:42:53', 0),
(6, 'PHP', 'PHP', '2021-06-28 10:43:01', '2021-06-28 10:43:01', 0),
(7, 'C#', 'C#', '2021-06-28 10:43:13', '2021-06-28 10:43:13', 0),
(8, 'Reactjs', 'React', '2021-06-28 10:43:35', '2021-06-28 10:43:35', 0),
(9, 'Android', 'And', '2021-06-28 10:43:47', '2021-06-28 10:43:47', 0),
(10, 'IOS', 'IOS', '2021-06-28 10:43:54', '2021-06-28 10:43:54', 0),
(11, 'Accounting', 'Acc', '2021-06-28 10:44:08', '2021-06-28 10:44:08', 0),
(12, 'Designing', 'Designing', '2021-06-28 10:44:30', '2021-06-28 10:44:30', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tasks`
--

CREATE TABLE `tbl_tasks` (
  `id` int(11) NOT NULL,
  `task_id` varchar(50) DEFAULT NULL,
  `client_id` varchar(256) DEFAULT NULL,
  `assigned_to` tinyint(255) DEFAULT 0,
  `description` varchar(50) DEFAULT NULL,
  `document` text DEFAULT NULL,
  `days` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0 COMMENT '0 = new task\r\n1 = Assigned\r\n3 = completed\r\n4 = canceled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_tasks`
--

INSERT INTO `tbl_tasks` (`id`, `task_id`, `client_id`, `assigned_to`, `description`, `document`, `days`, `created_at`, `updated_at`, `status`) VALUES
(38, NULL, '16', 0, 'test', NULL, '2', '2021-06-28 16:08:24', '2021-06-28 16:08:24', 0),
(39, NULL, '16', 0, 'test', NULL, '2', '2021-06-28 16:08:50', '2021-06-28 16:08:50', 0),
(40, NULL, '16', 0, 'test', 'https://x12va.s3.ap-south-1.amazonaws.com/documents/1660da3ca36206b7.89565175.jpg', '2', '2021-06-28 16:18:28', '2021-06-28 16:18:28', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tasks_sended`
--

CREATE TABLE `tbl_tasks_sended` (
  `id` int(11) NOT NULL,
  `task_id` varchar(50) NOT NULL,
  `expert_id` varchar(256) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = new task\r\n1 = closed\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `avatar`, `created_at`, `updated_at`, `status`) VALUES
(2, 'Mr. Hollis Miller III', 'user@demo.com', '2021-04-09 13:25:22', '$2y$10$UdTOKm/sd8CUCyJPmi7UaOuvFNRPKWA5Uc9fSD9JmjNBwxnoLk4vO', 'client', 'Qr6VPOkzCF', '', '2021-04-09 13:25:22', '2021-04-09 13:25:22', 'active'),
(3, 'Prof. Erwin Strosin', 'dietrich.sharon@example.com', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ersPWBAXMy', '', '2021-04-09 13:25:22', '2021-04-09 13:25:22', 'active'),
(4, 'Frida Thiel', 'lesch.cole@example.org', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'GQpHBDmMrC', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(5, 'Keanu Runolfsson DDS', 'maddison99@example.com', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Vs1IvHVdJX', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(6, 'Mr. Robbie Reynolds', 'jude.mayert@example.net', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'IAob3Sh2pz', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(7, 'Miss Earnestine Nader', 'audreanne.murphy@example.com', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'gs6MxrMyYO', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(8, 'Montana Rippin PhD', 'fisher.elian@example.org', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'YlmeIrRp6i', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(9, 'Dallas Hermiston', 'senger.sarina@example.org', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'wG1VjtYE3b', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(10, 'Donald Moore', 'eichmann.vivianne@example.org', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0rpEpt9vBO', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(11, 'Ms. Jade Bednar', 'jaquan.hayes@example.org', '2021-04-09 13:25:22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'ALmNqvWSS7', '', '2021-04-09 13:25:23', '2021-04-09 13:25:23', 'active'),
(12, 'Ebony Ziemann', 'geoffrey.powlowski@example.net', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'FyzQumFMpV', '', '2021-04-09 13:25:36', '2021-04-09 13:25:36', 'active'),
(13, 'Dr. Braden Corwin I', 'baumbach.keeley@example.com', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'JHfc7xp0G0', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(14, 'Mr. Kendall Collins', 'sjaskolski@example.com', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'csDcMat0jG', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(15, 'Dr. Keagan Watsica I', 'turner.ruben@example.net', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'EyksnWlXsL', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(16, 'Prof. Francisca Harber', 'drew.jacobs@example.org', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'rwtn1dFf8c', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(17, 'Laurianne Hintz', 'kendall18@example.org', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'PGXFJr2tbG', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(18, 'Kaitlin Runte', 'ylynch@example.net', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'U1DFKaL05f', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(19, 'Amos Goodwin', 'letha.nikolaus@example.net', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'nQsf6yWPq9', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(20, 'Dylan Kshlerin', 'little.loraine@example.net', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'MfDQ8uIBHC', '', '2021-04-09 13:25:37', '2021-04-09 13:25:37', 'active'),
(21, 'Chauncey Kub', 'feeney.maverick@example.com', '2021-04-09 13:25:36', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'JdqodaPK0K', '', '2021-04-09 13:25:38', '2021-04-09 13:25:38', 'active'),
(22, 'Mrs. Samanta Oberbrunner DDS', 'weber.dolly@example.net', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'NZoHFFWTVS', '', '2021-04-09 13:25:40', '2021-04-09 13:25:40', 'active'),
(23, 'Dr. Helga Beahan', 'tatyana.littel@example.com', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'i3Kui3tObK', '', '2021-04-09 13:25:41', '2021-04-09 13:25:41', 'active'),
(24, 'Mittie Weimann', 'schuppe.jarred@example.com', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'qnrn0CPTph', '', '2021-04-09 13:25:41', '2021-04-09 13:25:41', 'active'),
(25, 'Mayra Doyle', 'laury.leffler@example.org', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'rP1zbnotsv', '', '2021-04-09 13:25:42', '2021-04-09 13:25:42', 'active'),
(26, 'Mr. Ignacio DuBuque V', 'bmedhurst@example.com', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'AYMsWIWLw7', '', '2021-04-09 13:25:43', '2021-04-09 13:25:43', 'active'),
(27, 'Fabian Littel', 'prosacco.ned@example.com', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'NbnhxHmoDH', '', '2021-04-09 13:25:44', '2021-04-09 13:25:44', 'active'),
(28, 'Dominique Bosco', 'ronny.weimann@example.com', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'zmISMSygN5', '', '2021-04-09 13:25:44', '2021-04-09 13:25:44', 'active'),
(29, 'Mr. Marcos Spencer PhD', 'hattie.jacobi@example.net', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '4ucjkGV4en', '', '2021-04-09 13:25:44', '2021-04-09 13:25:44', 'active'),
(30, 'Dr. Roselyn Sipes', 'abshire.amie@example.org', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Q4oPwrfYVA', '', '2021-04-09 13:25:45', '2021-04-09 13:25:45', 'active'),
(31, 'Carrie Hyatt', 'dedrick.langworth@example.com', '2021-04-09 13:25:40', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'YvCXGE2Afr', '', '2021-04-09 13:25:45', '2021-04-09 13:25:45', 'active'),
(32, 'Karolann Haag IV', 'predovic.rudolph@example.com', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Ya1mHYRNQw', '', '2021-04-09 13:25:46', '2021-04-09 13:25:46', 'active'),
(33, 'Jacinthe Mueller', 'rice.bertrand@example.com', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Aw3w6rCOkv', '', '2021-04-09 13:25:47', '2021-04-09 13:25:47', 'active'),
(34, 'Troy Fahey', 'nella.rau@example.org', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'UOv6B91fys', '', '2021-04-09 13:25:47', '2021-04-09 13:25:47', 'active'),
(35, 'Malcolm Kuphax', 'maryse.skiles@example.org', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'subscriber', 'iJ81kJTUZX', '', '2021-04-09 13:25:48', '2021-04-11 14:18:12', 'active'),
(36, 'Tommie Zulauf', 'alejandra.shanahan@example.org', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'UwtPJVlVih', '', '2021-04-09 13:25:48', '2021-04-09 13:25:48', 'active'),
(37, 'Ms. Jacynthe Wiegand', 'kemmer.mauricio@example.com', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'pkYH1qHaub', '', '2021-04-09 13:25:49', '2021-04-09 13:25:49', 'active'),
(38, 'Janelle Stanton', 'domenico.greenfelder@example.com', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '7PfXZImvzh', '', '2021-04-09 13:25:49', '2021-04-09 13:25:49', 'active'),
(40, 'Miss Theresa Shields PhD', 'german51@example.com', '2021-04-09 13:25:46', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'vJj6cfsHXZ', '', '2021-04-09 13:25:51', '2021-04-09 13:25:51', 'active'),
(42, 'admin', 'adminx@demo.com', NULL, '$2y$10$gKQqZo8HO2rq4N0U/NX.aunkM.NYXWRSzG8LZQClT6RENMpkuCVBy', 'admin', NULL, '', '2021-04-09 14:49:10', '2021-04-09 14:49:10', 'active'),
(43, 'salal', 'salal.khan91@gmail.com', NULL, '$2y$10$BdAFZG6TM5KdQx/JLNc1jeOddtvX72x.7XdX4xQv4PYm.75XXn3o6', 'admin', NULL, '', '2021-04-09 14:51:16', '2021-04-09 14:51:16', 'active'),
(44, 'test', 'test3@gmail.com', NULL, '$2y$10$Z4x5iu0cqFmZkV6JkEetiuny8YHOI8yoO05lPmfmileXVDZNh5may', 'editor', NULL, '', '2021-04-10 13:14:20', '2021-04-10 13:14:20', 'active'),
(45, 'John Doe13', 'abdullah1@admin.com', NULL, '$2y$10$OD3BiteWXiIonhdQZzBRbu/.ttL0trUbzWJ38GAW5CVUlVr8TXK3S', 'subscriber', NULL, '', '2021-04-10 15:28:16', '2021-04-10 15:28:16', 'active'),
(46, 'salal2', 'salal.khan92@gmail.com', NULL, '$2y$10$Rj41EYjS8zlnqISJLH9aduCLLfszPa9.xYqb3XpAZTO1IJLC3xHb6', 'subscriber', NULL, '', '2021-04-10 15:32:43', '2021-04-10 15:32:43', 'active'),
(51, 'test', 'test45@gmail.com', NULL, '$2y$10$IsKArb9jHj2xF/TJqTY8juBb8jli/J98Zll.cPRN5iLP3.rC75aIq', 'subscriber', NULL, '', '2021-04-11 06:20:30', '2021-04-17 02:46:31', 'active'),
(52, 'umar', 'admin@x12va.com', NULL, '$2y$10$IsKArb9jHj2xF/TJqTY8juBb8jli/J98Zll.cPRN5iLP3.rC75aIq', 'admin', NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/user-52.png', '2021-04-11 06:20:30', '2021-06-28 14:04:10', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `tbl_clients`
--
ALTER TABLE `tbl_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_countries`
--
ALTER TABLE `tbl_countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_experts`
--
ALTER TABLE `tbl_experts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_experts_availability`
--
ALTER TABLE `tbl_experts_availability`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_experts_education`
--
ALTER TABLE `tbl_experts_education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_experts_proposals`
--
ALTER TABLE `tbl_experts_proposals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_experts_skills`
--
ALTER TABLE `tbl_experts_skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expert_id` (`expert_id`,`skill_id`);

--
-- Indexes for table `tbl_experts_tools`
--
ALTER TABLE `tbl_experts_tools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_members`
--
ALTER TABLE `tbl_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_pages`
--
ALTER TABLE `tbl_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_pages_field`
--
ALTER TABLE `tbl_pages_field`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_skills`
--
ALTER TABLE `tbl_skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tasks`
--
ALTER TABLE `tbl_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tasks_sended`
--
ALTER TABLE `tbl_tasks_sended`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_clients`
--
ALTER TABLE `tbl_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_countries`
--
ALTER TABLE `tbl_countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_experts`
--
ALTER TABLE `tbl_experts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_experts_availability`
--
ALTER TABLE `tbl_experts_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_experts_education`
--
ALTER TABLE `tbl_experts_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_experts_proposals`
--
ALTER TABLE `tbl_experts_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_experts_skills`
--
ALTER TABLE `tbl_experts_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_experts_tools`
--
ALTER TABLE `tbl_experts_tools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_members`
--
ALTER TABLE `tbl_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_pages`
--
ALTER TABLE `tbl_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_pages_field`
--
ALTER TABLE `tbl_pages_field`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `tbl_skills`
--
ALTER TABLE `tbl_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_tasks`
--
ALTER TABLE `tbl_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tbl_tasks_sended`
--
ALTER TABLE `tbl_tasks_sended`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
