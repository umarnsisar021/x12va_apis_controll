-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2021 at 01:47 PM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vuexy`
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
  `d_o_b` varchar(50) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `info` text NOT NULL,
  `score` varchar(50) DEFAULT NULL,
  `avatar` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `d_o_b` varchar(50) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `info` text NOT NULL,
  `score` varchar(50) DEFAULT NULL,
  `avatar` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_experts`
--

INSERT INTO `tbl_experts` (`id`, `member_id`, `first_name`, `middle_name`, `last_name`, `d_o_b`, `gender`, `email`, `mobile_number`, `country`, `region`, `info`, `score`, `avatar`, `created_at`, `updated_at`, `status`) VALUES
(11, '21', 'umar', 'Nisar', 'nisar', '21-06-1997', 'male', 'smartumar9112@gmail.com', NULL, 'pakistan', NULL, '', NULL, '', '2021-05-16 14:05:14', '2021-05-16 14:05:14', 0),
(12, '22', 'Umar', 'Nisar', 'Nisar', '2021-05-16T19:17:36.100Z', 'Male', 'umarnisar021@gmail.com', NULL, 'Afghanistan', NULL, '', NULL, '', '2021-05-16 14:17:57', '2021-05-16 14:17:57', 0),
(13, '23', 'Umar', 'Nisar', 'Nisar', '1994-05-03T19:00:00.000Z', 'Male', 'smartumar91@yahoo.com', '933132500948', 'Afghanistan', NULL, '', NULL, '', '2021-05-16 14:37:20', '2021-05-16 14:37:20', 0),
(14, '24', 'Umar', 'Nisar', 'Nisar', '2021-05-17T19:00:00.000Z', 'Male', 'umarnisar027@gmail.com', '923132500948', 'Pakistan', NULL, '', NULL, '', '2021-05-16 15:03:22', '2021-05-16 15:03:22', 0),
(15, '25', 'Umar', 'Nisar', 'Nisar', '2021-05-17T19:00:00.000Z', 'Male', 'umarnisar028@gmail.com', '933132500948', 'Afghanistan', NULL, '', NULL, '', '2021-05-18 13:57:27', '2021-05-18 13:57:27', 0),
(16, '26', 'Isaiah', 'Craig Alexander', 'Garcia', '24-5-2021', 'Male', 'jecynafuje@mailinator.com', '93345', 'Afghanistan', NULL, '', NULL, '', '2021-05-24 12:55:57', '2021-05-24 12:55:57', 0),
(17, '27', 'umar', 'Nisar', 'nisar', '21-06-1997', 'male', 'smartumar9114@gmail.com', '923132500948', 'pakistan', NULL, '', NULL, '', '2021-05-24 12:57:28', '2021-05-24 12:57:28', 0),
(18, '29', 'umar', NULL, 'nisar', '21-06-1997', 'male', 'smartumar9115@gmail.com', '923132500948', 'pakistan', NULL, '', NULL, '', '2021-05-29 14:22:42', '2021-05-29 14:22:42', 0),
(21, '37', 'lunda', NULL, 'Ballard', '30-5-2021', 'Male', 'kinyvyki@mailinator.com', '93468', 'Afghanistan', NULL, 'Vestibulum vel consectetur erat. Nam pulvinar commodo aliquam. Integer ac sem vulputate\n hendrerit elit sit amet, imperdiet nibh. Nam vitae volutpat sem. Donec porttitor dui, tempor\ntortor ultricies vitae. Vestibulum ultricies molestie dui, id laoreet mi. Nulla ultrices mattis arcu,\nnon ullamcorper odio tempus ut. Mauris fermentum.', NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/teqiqusag-37.png', '2021-05-29 14:34:09', '2021-06-10 14:03:48', 0);

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
  `date_from` varchar(50) NOT NULL,
  `date_to` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_experts_education`
--

INSERT INTO `tbl_experts_education` (`id`, `expert_id`, `institute_name`, `degree`, `date_from`, `date_to`, `created_at`, `updated_at`) VALUES
(15, '21', 'Institute of Business Administration 2', 'BSCS', '8-6-2021', '8-6-2021', '2021-06-08 14:00:04', '2021-06-08 14:04:56');

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
(7, '21', '1', '0', '2021-06-07 14:37:26', '2021-06-07 14:37:26', 0);

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
(4, NULL, NULL, '2021-06-08 14:39:24', '2021-06-08 14:39:24'),
(8, '21', 'Adobe Photoshop', '2021-06-08 14:56:58', '2021-06-08 14:56:58'),
(9, '21', 'Microsoft Excel', '2021-06-08 15:01:16', '2021-06-08 15:01:16'),
(10, '21', 'Power Point', '2021-06-08 15:09:18', '2021-06-08 15:09:18'),
(11, '21', 'Adobe XD', '2021-06-08 15:09:36', '2021-06-08 15:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_members`
--

CREATE TABLE `tbl_members` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(50) NOT NULL,
  `mobile_no` varchar(50) DEFAULT NULL,
  `is_seller` tinyint(4) DEFAULT 0,
  `is_buyer` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_members`
--

INSERT INTO `tbl_members` (`id`, `username`, `password`, `email`, `mobile_no`, `is_seller`, `is_buyer`, `created_at`, `updated_at`, `status`) VALUES
(21, 'umarnisar022', '$2y$10$oyg5BjgfWSmnM8q8vy.oEOP54a2sZiZgE44SnqBZYcpwMJoznIzmO', 'smartumar9112@gmail.com', '923132500948', 0, 0, '2021-05-16 14:05:14', '2021-05-16 14:05:14', 0),
(22, 'umarnisar021', '$2y$10$WH.TYF/GvoMdquNSVAxJTObtbvx4PXR4iJRkKM6kPrqP.eodR2hk2', 'umarnisar021@gmail.com', '933132500945', 0, 0, '2021-05-16 14:17:57', '2021-05-16 14:17:57', 0),
(23, 'umarnisar025', '$2y$10$hrs2q4iT2rhfGN2BzUlNpu7bhR8Px3jYNq28LAkGxFGnNKM4N4SRy', 'smartumar91@yahoo.com', NULL, 0, 0, '2021-05-16 14:37:20', '2021-05-16 14:37:20', 0),
(24, 'umarnisar027', '$2y$10$Sc.6NkxhyIEB6PJ/O0Q2ouZCt6DB.aKG8BCqAZn/35ChDnJekWuOa', 'umarnisar027@gmail.com', NULL, 0, 0, '2021-05-16 15:03:22', '2021-05-16 15:03:22', 0),
(25, 'umarnisar028', '$2y$10$bhvsiHhNiG69wrB/0xKIvuGRXsAOMZljG.l8CHO4nycKW6c.IBgs.', 'umarnisar028@gmail.com', NULL, 0, 0, '2021-05-18 13:57:27', '2021-05-18 13:57:27', 0),
(26, 'movis', '$2y$10$j1LIF7GW28Q95q/SDRyk8evL0CJzioZ4Suv5H43s5NJyk5khrGKBe', 'jecynafuje@mailinator.com', NULL, 0, 0, '2021-05-24 12:55:57', '2021-05-24 12:55:57', 0),
(27, 'umarnisar024', '$2y$10$SNqiEzVJ.hqp5WTmU8YG/e.yEtYssYrTFtlfhkt3tkFNEa9Ds8t7u', 'smartumar9114@gmail.com', NULL, 0, 0, '2021-05-24 12:57:28', '2021-05-24 12:57:28', 0),
(28, 'hoqacetob', '$2y$10$WW7if8chWp96j6VYJXpOOeHQdXXhCrynB2XJlmQrAuktnNO8Jozzq', 'wewevew@mailinator.com', NULL, 0, 0, '2021-05-29 14:21:57', '2021-05-29 14:21:57', 0),
(29, 'umarnisar026', '$2y$10$he74sJyi2rrn2lWjBCY6puL5BYSG0R81MiRZ8GSNVRzrxULDgLtNq', 'smartumar9115@gmail.com', NULL, 0, 0, '2021-05-29 14:22:42', '2021-05-29 14:22:42', 0),
(30, 'didobycip', '$2y$10$7Mk8fA/AUDa6PmWLmpX.XOTveOdIYlPOHrtRN7JySVK9oYq54zS2i', 'bexivabu@mailinator.com', NULL, 0, 0, '2021-05-29 14:23:19', '2021-05-29 14:23:19', 0),
(31, 'kuqamu', '$2y$10$BYrHtDWI5huTezqgHpvxHO8zuvWWO5/4zCoECR0wzYRtD6rmWAm7S', 'soma@mailinator.com', NULL, 0, 0, '2021-05-29 14:24:45', '2021-05-29 14:24:45', 0),
(32, 'moqara', '$2y$10$RN129pAVcfPNeQkJ6oiXVuKHhlLOybDOeK/yCMQY3JrcwN0bVO40u', 'foxuji@mailinator.com', NULL, 0, 0, '2021-05-29 14:27:14', '2021-05-29 14:27:14', 0),
(33, 'mewuj', '$2y$10$V5ooPyoVyna7oInxZBj4gu1Bq0HCJuDNgSOlU/GXm3XQw3uAwuYsq', 'tubo@mailinator.com', NULL, 0, 0, '2021-05-29 14:29:46', '2021-05-29 14:29:46', 0),
(34, 'necywujes', '$2y$10$U/6NQK.jPq483igWT99d/.ww/GyDdKSgmRieaguM1xofF1L4DL9ke', 'dasot@mailinator.com', NULL, 0, 0, '2021-05-29 14:30:11', '2021-05-29 14:30:11', 0),
(35, 'umarnisar0222', '$2y$10$bEPHpwajdcSnIFjtRr59cOVfG8Ru8U9n/cle/QSfjglGJaMwUDj86', 'smartumar9116@gmail.com', NULL, 0, 0, '2021-05-29 14:31:43', '2021-05-29 14:31:43', 0),
(36, 'umarnisar0223', '$2y$10$L.tUwC8k5fsux.Fq9KI3Wu1JnMEcaLl93go2TpOVPCLxoXnpDwe2G', 'smartumar9117@gmail.com', NULL, 0, 0, '2021-05-29 14:32:21', '2021-05-29 14:32:21', 0),
(37, 'teqiqusag', '$2y$10$k4MOBjyDZyGT8MwQLhSVFu3IVOAF6gHqxAtVsLJrI.4s92nRmvdoy', 'kinyvyki@mailinator.com', NULL, 0, 0, '2021-05-29 14:34:09', '2021-06-10 14:44:00', 0);

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
(1, 'Language Expert', 'LE', '2021-05-01 07:40:52', '2021-05-01 07:40:52', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tasks`
--

CREATE TABLE `tbl_tasks` (
  `id` int(11) NOT NULL,
  `task_id` varchar(50) NOT NULL,
  `client_id` varchar(256) NOT NULL,
  `assigned_to` tinyint(255) DEFAULT 0,
  `description` varchar(50) NOT NULL,
  `document` varchar(50) DEFAULT NULL,
  `days` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = new task\r\n1 = Assigned\r\n3 = completed\r\n4 = canceled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(52, 'umar', 'admin@x12va.com', NULL, '$2y$10$IsKArb9jHj2xF/TJqTY8juBb8jli/J98Zll.cPRN5iLP3.rC75aIq', 'admin', NULL, 'https://x12va.s3.ap-south-1.amazonaws.com/avatars/user-52.jpeg', '2021-04-11 06:20:30', '2021-05-30 06:13:35', 'active');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_countries`
--
ALTER TABLE `tbl_countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_experts`
--
ALTER TABLE `tbl_experts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbl_experts_availability`
--
ALTER TABLE `tbl_experts_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_experts_education`
--
ALTER TABLE `tbl_experts_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_experts_proposals`
--
ALTER TABLE `tbl_experts_proposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_experts_skills`
--
ALTER TABLE `tbl_experts_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_experts_tools`
--
ALTER TABLE `tbl_experts_tools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_members`
--
ALTER TABLE `tbl_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_tasks`
--
ALTER TABLE `tbl_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
