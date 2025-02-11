-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 27, 2016 at 08:55 AM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laramin`
--

-- --------------------------------------------------------

--
-- Table structure for table `activations`
--

CREATE TABLE `activations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `activations`
--

INSERT INTO `activations` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'MLKvcmkwGZ3e6bUbjJGd7BhH5cL0S6yK', 1, '2016-04-25 07:50:44', '2016-04-25 07:50:44', '2016-04-25 07:50:44'),
(2, 2, 'HXwtifEL692ocOaSEr9IpZ1VxxJPCkNv', 1, '2016-08-17 00:43:25', '2016-08-17 00:43:25', '2016-08-17 00:43:25'),
(3, 3, 'pVeuGt1A8pYXGI9nPoO36f13z2BOJAlu', 1, '2016-08-31 06:36:09', '2016-08-31 06:36:09', '2016-08-31 06:36:09');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `public_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `parent` int(11) NOT NULL COMMENT 'Ref to Parent Categiry',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `public_key`, `image`, `parent`, `created_at`, `updated_at`) VALUES
(15, '', 'bfdab035000fa5c7eea45c95203c34ee264e0a30.jpg', 0, '2016-03-02 03:58:41', '2016-03-02 03:58:41'),
(16, '', 'd43daf3d3a83376279736558602b999e16355d41.jpg', 0, '2016-03-02 03:59:13', '2016-03-02 03:59:13'),
(17, '', '60de6e632071e54b598785aec43c81437cc4ad05.jpg', 0, '2016-03-02 04:00:06', '2016-03-02 04:00:06'),
(18, '', '84a18e96741fe0cf1e3c03f293b58a84d825a9cb.jpg', 0, '2016-03-02 04:00:29', '2016-03-02 04:00:29'),
(19, '', '9ccaa11309b90b0bd136af7c8db9e1766aa1622f.png', 16, '2016-03-02 04:02:11', '2016-04-23 04:41:17'),
(20, '', 'ae38ae003cadea0dceab502d13e2cd53b3af85eb.jpg', 15, '2016-03-02 04:09:49', '2016-04-13 00:04:53'),
(21, '', '4b45c35a9cc507cc4416b5100db7dbe4dee2aca0.jpg', 15, '2016-03-02 04:10:18', '2016-03-02 04:10:18'),
(22, '', '5758a88ee16c370e9ac34a1001f96f457e9c8e9d.jpg', 15, '2016-03-02 04:10:40', '2016-03-02 04:10:40'),
(24, '', 'b060fc65ae5505af344b78cbde3215e8d02cc6b3.jpg', 17, '2016-03-02 04:14:04', '2016-03-02 04:14:04'),
(25, '', 'bbe3498abdf5bd47ab612e84853fd4ed0daa8c59.jpg', 18, '2016-03-02 04:14:34', '2016-03-02 04:14:34'),
(28, '', '2b4a78ab2452d188bf44d42265fc46a02de1978a.jpg', 19, '2016-03-02 04:48:49', '2016-03-02 04:48:49'),
(29, '', 'a4d76e36119b580b3bcc9dd64e9bca44ebed0107.jpg', 19, '2016-03-02 04:49:06', '2016-03-02 04:49:06'),
(30, '', '15f9ad6d5be43b7c4581ee735cb4c48fdb7a7b07.jpg', 15, '2016-04-23 01:36:51', '2016-04-23 01:36:51'),
(32, 'yBhF08o', '4dd743e053e50426b4113c13424c15b9e7090f46.png', 17, '2016-04-23 05:13:59', '2016-04-23 05:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `categories_translation`
--

CREATE TABLE `categories_translation` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `category_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `categories_translation`
--

INSERT INTO `categories_translation` (`id`, `category_id`, `category_title`, `category_slug`, `locale`, `updated_at`, `created_at`) VALUES
(37, 15, 'Cruises, Sailing & Water Tours ', 'cruises-sailing-water-tours', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 16, 'Cultural & Theme Tours', 'cultural-theme-tours', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 17, 'Day Trips & Excursions ', 'day-trips-excursions', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 18, 'Food, Wine & Nightlife', 'food-wine-nightlife', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 19, 'Outdoor Activities', 'outdoor-activities', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 20, 'DayCruises', 'daycruises', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 21, 'Dinner Cruises', 'dinner-cruises', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 22, 'Night Cruises', 'night-cruises', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 23, 'Cultural Tours', 'cultural-tours', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 24, 'Day Trips', 'day-trips', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 25, 'Beer & Brewery Tours', 'beer-brewery-tours', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, 28, '4WD, ATV & Off-Road Tours', '4wd-atv-off-road-tours', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, 29, 'Adrenaline & Extreme', 'adrenaline-extreme', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(55, 19, 'dsaff', 'dsaff', 'de', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, 15, 'Cruises, Sailing & Water Tours ', 'cruises-sailing-water-tours', 'de', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, 30, 'Sample', 'sample', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, 0, 'ewrwerrrer', 'ewrwerrrer', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, 19, '123', '123', 'it', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, 19, '456', '456', 'hr', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 0, 'Test ', 'test', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 0, 'Classic Thumblin', 'classic-thumblin', 'en', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `id` int(11) NOT NULL,
  `public_key` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'active :1 , disable/not-active:0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `public_key`, `country_id`, `state_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'j4dEqm2', 358, 0, 1, '2016-08-17 01:17:14', '2016-08-17 01:17:14');

-- --------------------------------------------------------

--
-- Table structure for table `city_translation`
--

CREATE TABLE `city_translation` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `city_title` varchar(500) CHARACTER SET utf8 NOT NULL,
  `city_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `city_translation`
--

INSERT INTO `city_translation` (`id`, `city_id`, `city_title`, `city_slug`, `locale`, `created_at`, `updated_at`) VALUES
(1, 1, 'badlapur', 'badlapur', 'en', '2016-08-17 01:17:14', '2016-08-17 01:17:14');

-- --------------------------------------------------------

--
-- Table structure for table `contact_enquiry`
--

CREATE TABLE `contact_enquiry` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8 NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8 NOT NULL,
  `comments` text CHARACTER SET utf8 NOT NULL,
  `is_view` enum('1','0') CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `contact_enquiry`
--

INSERT INTO `contact_enquiry` (`id`, `user_name`, `email`, `phone`, `subject`, `comments`, `is_view`, `created_at`, `updated_at`, `deleted_at`) VALUES
(13, 'vikas sawant ', 'vikass@webwingtechnologies.com', '', 'About hospital', 'About hospital', '1', '2016-09-12 11:56:44', '2016-10-18 08:31:35', NULL),
(14, 'vikas sawant', 'vikass@webwingtechnologies.com', '', 'About hospital', 'About hospital', '1', '2016-09-12 13:05:09', '2016-10-18 07:29:11', NULL),
(15, 'sagar', 'sagari@webwingtechnologies.com', '9874563210', 'test', 'test', '1', '2016-10-24 14:07:21', '2016-10-25 04:55:55', NULL),
(16, 'sagar', 'sagars@webwingtechnologies.com', '9874563210', 'testing ', 'testing', '1', '2016-10-25 05:55:57', '2016-10-27 00:33:15', '2016-10-27 00:33:15'),
(17, 'sagar', 'sagars@webwingtechnologies.com', '9874563210', 'testing ', 'testing', '0', '2016-10-25 05:56:40', '2016-10-27 00:33:15', '2016-10-27 00:33:15'),
(18, 'sagar', 'sagars@webwingtechnologies.com', '9874563210', 'testing ', 'testing', '0', '2016-10-25 05:57:17', '2016-10-27 00:33:15', '2016-10-27 00:33:15'),
(19, 'nayan', 'nayans@webwingtechnologies.com', '9874563210', 'test', 'test', '0', '2016-10-25 06:00:26', '2016-10-27 00:33:06', '2016-10-27 00:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_code` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'active :1 , disable/not-active:0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_code`, `country_name`, `is_active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(259, 'DZ', 'Algeria', 1, NULL, '2016-05-26 23:18:14', '2016-06-29 23:42:32'),
(260, 'AS', 'American Samoa', 1, NULL, '0000-00-00 00:00:00', '2016-06-29 18:33:50'),
(261, 'AD', 'Andorra', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(262, 'AO', 'Angola', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(263, 'AI', 'Anguilla', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(264, 'AQ', 'Antarctica', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(265, 'AG', 'Antigua and Barbuda', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(266, 'AR', 'Argentina', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(267, 'AW', 'Aruba', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(268, 'AU', 'Australia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(269, 'AT', 'Austria', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(270, 'AZ', 'Azerbaijan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(271, 'BS', 'The Bahamas', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(272, 'BH', 'Bahrain', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(273, 'BD', 'Bangladesh', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(274, 'BB', 'Barbados', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(275, 'BY', 'Belarus', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(276, 'BE', 'Belgium', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(277, 'BZ', 'Belize', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(278, 'BJ', 'Benin', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:21'),
(279, 'BM', 'Bermuda', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(280, 'BT', 'Bhutan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(281, 'BO', 'Bolivia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(282, 'BA', 'Bosnia and Herzegovina', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(283, 'BW', 'Botswana', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(284, 'BV', 'Bouvet Island', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(285, 'BR', 'Brazil', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(286, 'IO', 'British Indian Ocean Territory', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(287, 'VG', 'British Virgin Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(288, 'BN', 'Brunei Darussalam', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(289, 'BG', 'Bulgaria', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(290, 'BF', 'Burkina Faso', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(291, 'MM', 'Burma', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(292, 'BI', 'Burundi', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(293, 'KH', 'Cambodia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(294, 'CM', 'Cameroon', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(295, 'CA', 'Canada', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(296, 'CV', 'Cape Verde', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(297, 'KY', 'Cayman Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(298, 'CF', 'Central African Republic', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(299, 'TD', 'Chad', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(300, 'CL', 'Chile', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(301, 'CN', 'China', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(302, 'CX', 'Christmas Island', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(303, 'CC', 'Cocos (Keeling) Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:22'),
(304, 'CO', 'Colombia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(305, 'KM', 'Comoros', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(306, 'CD', 'Congo, Democratic Republic of the', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(307, 'CG', 'Congo, Republic of the', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(308, 'CK', 'Cook Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(309, 'CR', 'Costa Rica', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(310, 'CI', 'Cote d''Ivoire', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(311, 'CU', 'Cuba', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(312, 'CY', 'Cyprus', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(313, 'CZ', 'Czech Republic', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(314, 'DK', 'Denmark', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(315, 'DJ', 'Djibouti', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(316, 'DM', 'Dominica', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(317, 'DO', 'Dominican Republic', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(318, 'TP', 'East Timor', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(319, 'EC', 'Ecuador', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(320, 'EG', 'Egypt', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(321, 'SV', 'El Salvador', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(322, 'GQ', 'Equatorial Guinea', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(323, 'ER', 'Eritrea', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(324, 'EE', 'Estonia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:23'),
(325, 'ET', 'Ethiopia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(326, 'FK', 'Falkland Islands (Islas Malvinas)', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(327, 'FO', 'Faroe Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(328, 'FJ', 'Fiji', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(329, 'FI', 'Finland', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(330, 'FR', 'France', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(331, 'FX', 'France, Metropolitan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(332, 'GF', 'French Guiana', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(333, 'PF', 'French Polynesia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(334, 'TF', 'French Southern and Antarctic Lands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(335, 'GA', 'Gabon', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(336, 'GM', 'The Gambia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(337, 'GE', 'Georgia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(338, 'DE', 'Germany', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(339, 'GH', 'Ghana', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(340, 'GI', 'Gibraltar', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(341, 'GR', 'Greece', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(342, 'GL', 'Greenland', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(343, 'GD', 'Grenada', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(344, 'GP', 'Guadeloupe', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(345, 'GU', 'Guam', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(346, 'GT', 'Guatemala', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(347, 'GG', 'Guernsey', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(348, 'GN', 'Guinea', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:24'),
(349, 'GW', 'Guinea-Bissau', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(350, 'GY', 'Guyana', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(351, 'HT', 'Haiti', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(352, 'HM', 'Heard Island and McDonald Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(353, 'VA', 'Holy See (Vatican City)', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(354, 'HN', 'Honduras', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(355, 'HK', 'Hong Kong (SAR)', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(356, 'HU', 'Hungary', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(357, 'IS', 'Iceland', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(358, 'IN', 'India', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(359, 'ID', 'Indonesia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(360, 'IR', 'Iran', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(361, 'IQ', 'Iraq', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:25'),
(362, 'IE', 'Ireland', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(363, 'IL', 'Israel', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(364, 'IT', 'Italy', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(365, 'JM', 'Jamaica', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(366, 'JP', 'Japan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(367, 'JE', 'Jersey', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(368, 'JO', 'Jordan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(369, 'KZ', 'Kazakhstan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(370, 'KE', 'Kenya', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(371, 'KI', 'Kiribati', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(372, 'KP', 'Korea, North', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(373, 'KR', 'Korea, South', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(374, 'KW', 'Kuwait', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(375, 'KG', 'Kyrgyzstan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:26'),
(376, 'LA', 'Laos', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(377, 'LV', 'Latvia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(378, 'LB', 'Lebanon', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(379, 'LS', 'Lesotho', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(380, 'LR', 'Liberia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(381, 'LY', 'Libya', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(382, 'LI', 'Liechtenstein', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(383, 'LT', 'Lithuania', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(384, 'LU', 'Luxembourg', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(385, 'MO', 'Macao', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(386, 'MK', 'Macedonia, The Former Yugoslav Republic of', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(387, 'MG', 'Madagascar', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(388, 'MW', 'Malawi', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(389, 'MY', 'Malaysia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(390, 'MV', 'Maldives', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(391, 'ML', 'Mali', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:27'),
(392, 'MT', 'Malta', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(393, 'IM', 'Man, Isle of', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(394, 'MH', 'Marshall Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(395, 'MQ', 'Martinique', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(396, 'MR', 'Mauritania', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(397, 'MU', 'Mauritius', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(398, 'YT', 'Mayotte', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(399, 'MX', 'Mexico', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(400, 'FM', 'Micronesia, Federated States of', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(401, 'MD', 'Moldova', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(402, 'MC', 'Monaco', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(403, 'MN', 'Mongolia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(404, 'MS', 'Montserrat', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(405, 'MA', 'Morocco', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(406, 'MZ', 'Mozambique', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(407, 'NA', 'Namibia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:28'),
(408, 'NR', 'Nauru', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(409, 'NP', 'Nepal', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(410, 'NL', 'Netherlands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(411, 'AN', 'Netherlands Antilles', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(412, 'NC', 'New Caledonia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(413, 'NZ', 'New Zealand', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(414, 'NI', 'Nicaragua', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(415, 'NE', 'Niger', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(416, 'NG', 'Nigeria', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(417, 'NU', 'Niue', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(418, 'NF', 'Norfolk Island', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(419, 'MP', 'Northern Mariana Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(420, 'NO', 'Norway', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(421, 'OM', 'Oman', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(422, 'PK', 'Pakistan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(423, 'PW', 'Palau', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(424, 'PA', 'Panama', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:29'),
(425, 'PG', 'Papua New Guinea', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(426, 'PY', 'Paraguay', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(427, 'PE', 'Peru', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(428, 'PH', 'Philippines', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(429, 'PN', 'Pitcairn Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(430, 'PL', 'Poland', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(431, 'PT', 'Portugal', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(432, 'PR', 'Puerto Rico', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(433, 'QA', 'Qatar', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(434, 'RE', 'R?union', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(435, 'RO', 'Romania', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(436, 'RU', 'Russia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(437, 'RW', 'Rwanda', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(438, 'SH', 'Saint Helena', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:30'),
(439, 'KN', 'Saint Kitts and Nevis', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(440, 'LC', 'Saint Lucia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(441, 'PM', 'Saint Pierre and Miquelon', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(442, 'VC', 'Saint Vincent and the Grenadines', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(443, 'WS', 'Samoa', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(444, 'SM', 'San Marino', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(445, 'ST', 'S?o Tom? and Pr?ncipe', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(446, 'SA', 'Saudi Arabia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(447, 'SN', 'Senegal', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(448, 'SC', 'Seychelles', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(449, 'SL', 'Sierra Leone', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(450, 'SG', 'Singapore', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:31'),
(451, 'SK', 'Slovakia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(452, 'SI', 'Slovenia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(453, 'SB', 'Solomon Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(454, 'SO', 'Somalia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(455, 'ZA', 'South Africa', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(456, 'GS', 'South Georgia and the South Sandwich Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(457, 'ES', 'Spain', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(458, 'LK', 'Sri Lanka', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(459, 'SD', 'Sudan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(460, 'SR', 'Suriname', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(461, 'SJ', 'Svalbard', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(462, 'SZ', 'Swaziland', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(463, 'SE', 'Sweden', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(464, 'CH', 'Switzerland', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:32'),
(465, 'SY', 'Syria', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(466, 'TW', 'Taiwan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(467, 'TJ', 'Tajikistan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(468, 'TZ', 'Tanzania', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(469, 'TH', 'Thailand', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(470, 'TG', 'Togo', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(471, 'TK', 'Tokelau', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(472, 'TO', 'Tonga', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(473, 'TT', 'Trinidad and Tobago', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(474, 'TN', 'Tunisia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(475, 'TR', 'Turkey', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(476, 'TM', 'Turkmenistan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:33'),
(477, 'TC', 'Turks and Caicos Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(478, 'TV', 'Tuvalu', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(479, 'UG', 'Uganda', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(480, 'UA', 'Ukraine', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(481, 'AE', 'United Arab Emirates', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(482, 'UK', 'United Kingdom', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(483, 'US', 'United States', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(484, 'UM', 'United States Minor Outlying Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(485, 'UY', 'Uruguay', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(486, 'UZ', 'Uzbekistan', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(487, 'VU', 'Vanuatu', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(488, 'VE', 'Venezuela', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(489, 'VN', 'Vietnam', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(490, 'VI', 'Virgin Islands', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(491, 'WF', 'Wallis and Futuna', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(492, 'EH', 'Western Sahara', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(493, 'YE', 'Yemen', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(494, 'YU', 'Yugoslavia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(495, 'ZM', 'Zambia', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:34'),
(496, 'ZW', 'Zimbabwe', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:35'),
(497, 'PS', 'Palestinian Territory, Occupied', 1, NULL, '0000-00-00 00:00:00', '2016-05-17 23:15:35'),
(498, 'ABC', 'Abhijeet Bhosale', 0, '2016-05-26 17:33:15', '2016-05-26 23:03:15', '2016-05-26 17:33:15'),
(499, 'TS', 'Tejas Sonar', 1, '2016-05-26 18:02:09', '2016-05-26 23:32:09', '2016-05-26 18:02:09'),
(500, 'SK', 'Snehal Kamodkar', 1, '2016-05-26 18:02:28', '2016-05-26 23:32:29', '2016-05-26 18:02:28'),
(501, 'NK', 'Nitish Kasar', 1, '2016-05-26 23:03:10', '2016-05-27 04:33:10', '2016-05-26 23:03:10'),
(502, 'PC', 'Pravin Chaudhary', 1, '2016-05-26 23:02:55', '2016-05-27 04:32:55', '2016-05-26 23:02:55'),
(503, '22', 'sdf', 1, '2016-05-26 17:34:29', '2016-05-26 23:04:29', '2016-05-26 17:34:29'),
(504, '22', 'fsffsdf', 1, '2016-05-26 17:44:37', '2016-05-26 23:14:37', '2016-05-26 17:44:37'),
(505, 'sfa', 'fsadf', 1, '2016-05-26 17:53:38', '2016-05-26 23:23:38', '2016-05-26 17:53:38'),
(506, 'sfd', 'fsadfa', 1, '2016-05-26 17:53:10', '2016-05-26 23:23:10', '2016-05-26 17:53:10'),
(507, 'sfa', 'fsffsdf', 1, '2016-05-26 17:57:07', '2016-05-26 23:27:07', '2016-05-26 17:57:07'),
(508, 'sfd', 'fsadfa', 1, '2016-05-26 17:57:07', '2016-05-26 23:27:07', '2016-05-26 17:57:07'),
(509, '22', 'dfsfdf', 1, '2016-05-26 17:57:44', '2016-05-26 23:27:44', '2016-05-26 17:57:44'),
(510, 'qwe', 'qwerty', 1, '2016-07-08 17:47:27', '2016-07-08 17:46:53', '2016-07-08 17:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `countries_translation`
--

CREATE TABLE `countries_translation` (
  `id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `country_name` varchar(500) CHARACTER SET utf8 NOT NULL,
  `locale` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countries_translation`
--

INSERT INTO `countries_translation` (`id`, `country_id`, `country_name`, `locale`, `created_at`, `updated_at`) VALUES
(1, 1, 'India', 'en', '2016-05-05 23:16:04', '2016-05-05 23:16:04'),
(2, 2, 'Croatia', 'en', '2016-05-06 08:21:59', '2016-05-06 08:21:59'),
(3, 1, 'Ind1a', 'es', '2016-05-06 08:24:18', '2016-05-06 08:24:18');

-- --------------------------------------------------------

--
-- Table structure for table `email_template`
--

CREATE TABLE `email_template` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_subject` text COLLATE utf8_unicode_ci NOT NULL,
  `template_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_from_mail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_html` text COLLATE utf8_unicode_ci NOT NULL,
  `template_variables` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NA' COMMENT '~ Separated',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `email_template`
--

INSERT INTO `email_template` (`id`, `template_name`, `template_subject`, `template_from`, `template_from_mail`, `template_html`, `template_variables`, `deleted_at`, `created_at`, `updated_at`) VALUES
(3, 'Contact Enquiry', 'New Contact Enquiry at Quedemonos', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##SITE_URL## Admin<br />&nbsp; &nbsp;<br />You have new contact enquiry&nbsp;from <strong>##USER_NAME## </strong>following are details.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Enquiry Details:</strong></p>\r\n<table style="height: 82px; width: 724px;">\r\n<tbody>\r\n<tr>\r\n<td style="width: 153px;"><strong>Email Id</strong></td>\r\n<td style="width: 555px;">##USER_EMAIL##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 153px;"><strong>Phone No.</strong></td>\r\n<td style="width: 555px;">##PHONE##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 153px;"><strong>Enquiry Message</strong></td>\r\n<td style="width: 555px;">##ENQUIRY##</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><br /><br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>\r\n<p>&nbsp;&nbsp;&nbsp;&nbsp; <br /><br />Thanks and Regards,<br />##SITE_URL##</p>', '##USER_NAME##~##SUBJECT##~##USER_EMAIL##~##SITE_URL##~##PHONE##~##ENQUIRY##', NULL, '2016-05-05 22:11:18', '2016-07-12 19:58:25'),
(6, 'Account Activation', 'VERIFY YOUR ACCOUNT', 'Quedemonos - Admin', 'admin@vr.com', '<h2>Verify Your Email Address</h2>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;Hello<strong> ##USER_FNAME##, </strong></p>\r\n<div style="text-align: left;"><br />Thank you&nbsp;&nbsp;for creating an account with&nbsp;&nbsp;##APP_NAME##.</div>\r\n<div style="text-align: left;">&nbsp;</div>\r\n<div style="text-align: left;">You are successfully register with us and now you have to complete our authentication process. You have to verify your account by clicking following activation button.</div>\r\n<div style="text-align: left;">&nbsp;</div>\r\n<div style="text-align: left;">&nbsp;</div>\r\n<div style="text-align: center;">&nbsp;</div>\r\n<div style="text-align: center;">&nbsp;</div>\r\n<div style="text-align: center;"><strong>Please click on below button to verify your account.</strong></div>\r\n<div style="text-align: center;">&nbsp;</div>\r\n<div style="text-align: center;">&nbsp;</div>\r\n<div style="text-align: center;">##ACTIVATION_URL## .<br /><br /></div>\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>&nbsp;</div>\r\n<div>Thanks and Regards,<br />&nbsp;##APP_NAME##</div>', '##USER_FNAME##~##APP_NAME##~##ACTIVATION_URL##', NULL, '2016-05-15 23:12:14', '2016-07-12 20:05:00'),
(7, 'Forgot Password', 'FORGOT PASSWORD', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello <strong>##FIRST_NAME##</strong> ,</p>\r\n<p>You forget your password, don''t worry. Our system help you to reset your password easily.</p>\r\n<p>Following &nbsp;are the details of your account. You can now set your new password by just click on following button.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Account Details:</strong></p>\r\n<table style="height: 36px; width: 604px;">\r\n<tbody>\r\n<tr>\r\n<td style="width: 103px;"><strong>Username</strong></td>\r\n<td style="width: 485px;">##EMAIL##</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n<p style="text-align: center;"><strong>Reset Your Password Just Click On Following Button</strong></p>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;">##REMINDER_URL##</p>\r\n<p style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </p>\r\n<p style="text-align: left;">&nbsp;</p>\r\n<p style="text-align: left;">&nbsp;</p>\r\n<p style="text-align: left;"><br /><br />Thanks and Regards, &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; <br />##SITE_URL## &nbsp;&nbsp;</p>', '##FIRST_NAME##~##EMAIL##~##REMINDER_URL##~##SITE_URL##', NULL, '2016-05-16 01:43:47', '2016-07-10 20:05:18'),
(8, 'Social Auth Registration', 'Your Account Details for Quedemonos', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello&nbsp; ##USER_NAME##,<br /><br />Thanks you for registering at <strong>##SITE_LINK##</strong>. You may now log in to your account using following credentials</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Acount Details:</strong></p>\r\n<table style="height: 48px; width: 682px;">\r\n<tbody>\r\n<tr>\r\n<td style="width: 184px;">User Name/ Email Id</td>\r\n<td style="width: 482px;">##USER_EMAIL##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 184px;">Password</td>\r\n<td style="width: 482px;">##USER_PWD##</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><br /><br /><strong>Note</strong>: Please Change your password after first login</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p><br /><br />Thanks and Regards,<br />##SITE_LINK##</p>', '##USER_EMAIL##~##USER_NAME##~##SITE_LINK##~##USER_PWD##', NULL, '2016-05-18 02:41:57', '2016-07-10 20:16:42'),
(9, 'Property Booking Request', 'New Property Booking Request From Quedemonos', 'Quedemonos - Admin', 'admin@vr.com', 'Hello ##OWNER_NAME##,<br><br>&nbsp;&nbsp; &nbsp; You have a new property booking request from&nbsp; <b>##TRAVELLER_NAME##.<br></b>&nbsp;<br><b><u>Property Name:</u> </b>##PROPERTY_NAME## <br><br><b><u>Booking Request Details:</u></b><br><br><b>Arrival Date&nbsp;</b>&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; : ##ARRIVAL_DATE##<br><b>Departure Date</b>&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; : ##DEPARTURE_DATE##<br><b>Number of Adults</b>&nbsp;&nbsp; &nbsp; : ##NUM_OF_ADULTS##<br><b>Number of Children</b> : ##NUM_OF_CHILDREN##<br><br><b><u>Traveler Details:</u></b><br><br><b>Traveler Name&nbsp; </b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; : ##TRAVELLER_NAME##<br><b>Traveler Email Id</b>&nbsp; &nbsp; &nbsp;&nbsp; : ##TRAVELLER_EMAIL##<br><b>Phone&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; : ##TRAVELLER_PHONE##<br><br><b><u>Message From Traveler :</u></b><br><br>##TRAVELLER_MESSAGE##<br><br><br><br>Thanks and Regards,<br>##SITE_URL##<br><br>', '##OWNER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##NUM_OF_ADULTS##~##NUM_OF_CHILDREN##~##TRAVELLER_NAME##~##TRAVELLER_EMAIL##~##TRAVELLER_PHONE##~##TRAVELLER_MESSAGE##~##SITE_URL##', NULL, '2016-06-11 00:36:53', '2016-07-08 02:41:57'),
(11, 'Property Booking Cancel By System After Confirmation For Owner', 'Property Booking Cancelled By System at Quedemonos', 'Quedemonos - Admin', 'admin@vr.com', 'Dear ##OWNER_NAME##,&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br><br>Sorry. &nbsp;booking was cancel by system for following reasons.<br>&nbsp; &nbsp; 1. ##PAYMENT_STAGE## Payment was not done within two days by ##USER_NAME## after&nbsp;confirmation from you.<br><br>PROPERTY NAME &nbsp;:&nbsp;&nbsp;##PROPERTY_NAME##&nbsp;<br>ORDER _ID &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:&nbsp;&nbsp;##ORDER_ID##<br>ARRIVAL DATE &nbsp; &nbsp; &nbsp;:&nbsp;&nbsp;##ARRIVAL_DATE##&nbsp;<br>DEPARTURE DATE :&nbsp;##DEPARTURE_DATE##<br><br>Happy owners,<br><b>##SITE_NAME##</b><br>', '##OWNER_NAME##~##SITE_NAME##~##USER_NAME##~##PROPERTY_NAME##~##ORDER_ID##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##PAYMENT_STAGE##', NULL, '2016-06-13 02:13:42', '2016-07-08 03:32:54'),
(13, 'Property Booking Cancellation By Renter - Email To Owner', 'Property Booking Cancellation By Traveler', 'Quedemonos - Admin', 'admin@vr.com', 'Hello ##OWNER_NAME## ,<br><br>&nbsp; &nbsp; Booking request for your property&nbsp;<br><b>&nbsp; &nbsp;&nbsp;##PROPERTY_NAME##</b>&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<br>&nbsp;&nbsp; &nbsp;for the dates <b>##ARRIVAL_DATE##</b> &nbsp;to&nbsp;<b>##DEPARTURE_DATE##</b> is canceled&nbsp;by<br>&nbsp; &nbsp;&nbsp;##RENTER_NAME## .<br><br>Thanks and Regards &nbsp;<br>##SITE_NAME##', '##OWNER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##SITE_NAME##', NULL, '2016-06-13 18:54:24', '2016-07-08 02:49:32'),
(14, 'Property Booking Cancellation By Owner - Email To Renter', 'Property Booking Cancellation By Property Owner', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##RENTER_NAME## ,<br /><br />&nbsp; &nbsp; Unfortunately&nbsp;due to some reason,&nbsp;Booking request for property&nbsp;&nbsp;<strong><u>##PROPERTY_NAME##</u></strong>&nbsp; for&nbsp; the dates <strong><u>##ARRIVAL_DATE##</u></strong> &nbsp;to&nbsp;<strong>##DEPARTURE_DATE##</strong>&nbsp; is canceled&nbsp;by property owner<br />&nbsp; &nbsp;&nbsp;<u><strong>##OWNER_NAME##</strong></u> .<br /><br />You are applicable to get a Refund of amount&nbsp;<u> </u><strong><u>$</u></strong><u>&nbsp;</u><strong><u>##TOTAL_REFUND##</u></strong><u>&nbsp;</u>.<br />Refund will be transferred to your account shortly<strong><br /><br />Note:</strong> If you have any kind of issue please contact to property owner<strong><br /></strong>&nbsp; &nbsp;<br /><br />Thanks and Regards &nbsp;<br />##SITE_NAME##</p>', '##RENTER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##OWNER_NAME##~##SITE_NAME##~##TOTAL_REFUND##', NULL, '2016-06-13 19:18:43', '2016-07-11 00:27:09'),
(15, 'Property Booking Cancellation By Renter - Email To Renter', 'Property Booking Cancellation Successfully.1', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##RENTER_NAME## ,<br /><br />&nbsp; &nbsp; Booking Cancellation&nbsp;request for property <br />&nbsp; &nbsp;&nbsp;<strong>##PROPERTY_NAME##</strong>&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<br />&nbsp;&nbsp; &nbsp;for the dates <strong>##ARRIVAL_DATE##</strong> &nbsp;to&nbsp;<strong>##DEPARTURE_DATE##</strong> is Successfully<br />&nbsp; &nbsp;&nbsp;canceled&nbsp;by&nbsp;you. you are not eligble for any kind of refund<br /><br />Thanks and Regards &nbsp;<br />##SITE_NAME##</p>', '##RENTER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##SITE_NAME##', NULL, '2016-06-13 19:27:01', '2016-07-11 00:30:49'),
(16, 'Property Booking Cancellation By Owner - Email To Owner', 'Property Booking Cancellation Successfully.', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##OWNER_NAME## ,<br /><br />Your Booking Cancellation&nbsp;request of&nbsp; property &nbsp;<strong><u>##PROPERTY_NAME##</u></strong>, for the dates <strong><u>##ARRIVAL_DATE##</u></strong> &nbsp;to&nbsp;<strong><u>##DEPARTURE_DATE##</u></strong> is Successfully canceled.<br /><br /><br /><br />Thanks and Regards &nbsp;<br />##SITE_NAME##</p>', '##OWNER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##SITE_NAME##', NULL, '2016-06-13 19:28:39', '2016-07-11 00:24:46'),
(17, 'Property Booking Confirmation - Normal Booking', 'Your Property Booking Confirmed By Property Owner', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##TRAVELLER_NAME##,<br /><br />Your request has been confirmed by property owner<br /><br /></p>\r\n<p><strong>Property Name</strong>:&nbsp; ##PROPERTY_NAME##. <br /><br /><br /><strong><u>Booking Details</u></strong> :</p>\r\n<p>&nbsp;</p>\r\n<table style="height: 93px; width: 719px;" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="width: 149.217px;"><strong>Arrival Date</strong></td>\r\n<td style="width: 548.783px;">##ARRIVAL_DATE##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 149.217px;"><strong>Departure Date</strong>&nbsp;</td>\r\n<td style="width: 548.783px;">##DEPARTURE_DATE##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 149.217px;"><strong>Number of Adults</strong></td>\r\n<td style="width: 548.783px;">##NUM_OF_ADULTS##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 149.217px;"><strong>Number of Children</strong></td>\r\n<td style="width: 548.783px;">##NUM_OF_CHILDREN##</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n<p><strong><u>Payment Details</u></strong> :</p>\r\n<p>&nbsp;</p>\r\n<table style="height: 52px; width: 725px;">\r\n<tbody>\r\n<tr style="height: 14px;">\r\n<td style="width: 150px; height: 14px;"><strong>Booking Fee</strong></td>\r\n<td style="width: 559px; height: 14px;">##BOOKING_FEE## (Includes IVA)</td>\r\n</tr>\r\n<tr style="height: 16px;">\r\n<td style="width: 150px; height: 16px;"><strong>Deposit Amount</strong></td>\r\n<td style="width: 559px; height: 16px;">##DEPOSIT_AMOUNT##</td>\r\n</tr>\r\n<tr style="height: 16px;">\r\n<td style="width: 150px; height: 16px;"><strong>Total Initial Payment</strong></td>\r\n<td style="width: 559px; height: 16px;"><strong>##TOTAL_INITIAL_PAY_AMT##</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;"><strong>Please click on following button to make your payment</strong></p>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;">##PAYMENT_LINK##</p>\r\n<p>&nbsp;</p>\r\n<p><br /><br /><br /><strong><u>Note:</u></strong> <strong>Y</strong><strong>ou need to pay final payment&nbsp;within 2 days from now. If you will fail to pay final amount within 2 days your property booking will be canceled.<br /><u><br />Note:</u> If you have any kind of issue regarding this, please contact Property Owner. <br /></strong><br /><br /><br />Thanks and Regards,<br />##SITE_URL##<br /><br /><br /></p>', '##TRAVELLER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##NUM_OF_ADULTS##~##NUM_OF_CHILDREN##~##BOOKING_FEE##~##DEPOSIT_AMOUNT##~##TOTAL_INITIAL_PAY_AMT##~##PAYMENT_LINK##~##SITE_URL##~##ORDER_ID##', NULL, '2016-06-14 20:07:55', '2016-07-10 19:32:28'),
(18, 'Property Booking Confirmation - Last Minute Booking', 'Your Property Booking Confirmed By Property Owner', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##TRAVELLER_NAME##,<br /><br />Your request has been confirmed by property owner<br /><br /></p>\r\n<p><strong>Property Name</strong>:&nbsp; ##PROPERTY_NAME##. <br /><br /><br /><strong><u>Booking Details</u></strong> :</p>\r\n<p>&nbsp;</p>\r\n<table style="height: 93px; width: 719px;" border="0">\r\n<tbody>\r\n<tr>\r\n<td style="width: 148.217px;"><strong>Arrival Date</strong></td>\r\n<td style="width: 549.783px;">##ARRIVAL_DATE##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 148.217px;"><strong>Departure Date</strong>&nbsp;</td>\r\n<td style="width: 549.783px;">##DEPARTURE_DATE##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 148.217px;"><strong>Number of Adults</strong></td>\r\n<td style="width: 549.783px;">##NUM_OF_ADULTS##</td>\r\n</tr>\r\n<tr>\r\n<td style="width: 148.217px;"><strong>Number of Children</strong></td>\r\n<td style="width: 549.783px;">##NUM_OF_CHILDREN##</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>&nbsp;</p>\r\n<p><strong><u>Payment Details</u></strong> :</p>\r\n<p>&nbsp;</p>\r\n<table style="height: 52px; width: 725px;">\r\n<tbody>\r\n<tr style="height: 14px;">\r\n<td style="width: 147px; height: 14px;"><strong>Booking Fee</strong></td>\r\n<td style="width: 562px; height: 14px;">##BOOKING_FEE## (Includes IVA)</td>\r\n</tr>\r\n<tr style="height: 16px;">\r\n<td style="width: 147px; height: 16px;"><strong>Deposit Amount</strong></td>\r\n<td style="width: 562px; height: 16px;">##DEPOSIT_AMOUNT##</td>\r\n</tr>\r\n<tr style="height: 16px;">\r\n<td style="width: 147px; height: 16px;"><strong>Rental Fee</strong></td>\r\n<td style="width: 562px; height: 16px;">##RENTAL_FEE##</td>\r\n</tr>\r\n<tr style="height: 16px;">\r\n<td style="width: 147px; height: 16px;"><strong>Custom Fee</strong></td>\r\n<td style="width: 562px; height: 16px;">##CUSTOM_FEE_BLOCK##</td>\r\n</tr>\r\n<tr style="height: 16px;">\r\n<td style="width: 147px; height: 16px;"><strong>Total&nbsp; Payment</strong></td>\r\n<td style="width: 562px; height: 16px;"><strong>##TOTAL_PAY_AMT##</strong></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;"><strong>Please click on following button to make your payment</strong></p>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;">##PAYMENT_LINK##</p>\r\n<p>&nbsp;</p>\r\n<p><br /><br /><br /><strong><u>Note:</u></strong> <strong>Y</strong><strong>ou need to pay final payment&nbsp;within 2 days from now. If you will fail to pay final amount within 2 days your property booking will be canceled.<br /><u><br />Note:</u> If you have any kind of issue regarding this, please contact Property Owner. <br /></strong><br /><br /><br />Thanks and Regards,<br />##SITE_URL##<br /><br /><br /></p>', '##TRAVELLER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##NUM_OF_ADULTS##~##NUM_OF_CHILDREN##~##BOOKING_FEE##~##DEPOSIT_AMOUNT##~##RENTAL_FEE##~##CUSTOM_FEE_BLOCK##~##TOTAL_PAY_AMT##~##PAYMENT_LINK##~##SITE_URL##~##ORDER_ID##', NULL, '2016-06-14 20:11:44', '2016-07-10 19:32:17'),
(19, 'Property Booking Cancellation By Owner - Immediately After Request, Mail To Renter', 'Property Booking Cancelled - By Owner', 'Quedemonos - Admin', 'admin@vr.com', 'Hello ##TRAVELLER_NAME##,<br><br>Your request has been canceled by the owner due to some reason<br><br><b><u>Property Details:</u></b><br>&nbsp;<br><b>Property Name</b>: ##PROPERTY_NAME##. <br><br><br>Below mention are the booking request details:<br><br><b>Order Id : ##ORDER_ID##</b><br><br>Arrival Date&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;: ##ARRIVAL_DATE##<br>Departure Date&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; : ##DEPARTURE_DATE##<br>Number of Adults&nbsp;&nbsp; &nbsp;&nbsp; : ##NUM_OF_ADULTS##<br>Number of Children : ##NUM_OF_CHILDREN##<br><br><br>Please contact property owner, if you have any kind of issue<br><br><br>Thanks and Regards,<br>##SITE_URL##<br><br><br><br>', '##TRAVELLER_NAME##~##PROPERTY_NAME##~##ORDER_ID##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##NUM_OF_ADULTS##~##NUM_OF_CHILDREN##~##SITE_URL##~##ORDER_ID##', NULL, '2016-06-14 23:58:16', '2016-07-08 02:50:47'),
(20, 'Reminder 14 Days Remaining To Arrival Date', 'Reminder for Final Payment for Your Booking at Quedemonos', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##USER_NAME##,&nbsp;<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <br />This is a reminder email for final payment of your property booking.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Property Details:</strong></p>\r\n<table style="height: 80px; width: 678px;">\r\n<tbody>\r\n<tr style="height: 13px;">\r\n<td style="width: 170px; height: 13px;"><strong>PROPERTY NAME</strong></td>\r\n<td style="width: 492px; height: 13px;">##PROPERTY_NAME##</td>\r\n</tr>\r\n<tr style="height: 13px;">\r\n<td style="width: 170px; height: 13px;"><strong>ORDER _ID</strong></td>\r\n<td style="width: 492px; height: 13px;">##ORDER_ID##</td>\r\n</tr>\r\n<tr style="height: 13px;">\r\n<td style="width: 170px; height: 13px;"><strong>ARRIVAL DATE </strong></td>\r\n<td style="width: 492px; height: 13px;">&nbsp;##ARRIVAL_DATE##</td>\r\n</tr>\r\n<tr style="height: 13px;">\r\n<td style="width: 170px; height: 13px;"><strong>DEPARTURE DATE</strong></td>\r\n<td style="width: 492px; height: 13px;">##DEPARTURE_DATE##</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;"><strong><br /><br />Please click on the below button to make final payment</strong>.</p>\r\n<p style="text-align: center;">&nbsp;</p>\r\n<p style="text-align: center;">##PAYMENT_LINK##<br /><br />&nbsp;</p>\r\n<p><br /><br /></p>\r\n<div><u><strong>PLEASE NOTE :</strong></u><u><strong>&nbsp;Y</strong></u><u><strong>ou need to pay final payment&nbsp;within 4 days from now. If you will fail to pay final amount within 4 days your property booking will be cancelled.&nbsp;In case of cancellation&nbsp;NO REFUND will be&nbsp;provided.</strong></u></div>\r\n<p><br /><br /><br /><br /><br />Thanks &amp; Regards,</p>\r\n<p>##SITE_NAME##</p>\r\n<p>&nbsp;</p>', '##USER_NAME##~##SITE_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##ORDER_ID##~##PROPERTY_NAME##~##PAYMENT_LINK##', NULL, '2016-06-15 01:13:41', '2016-07-10 22:18:00'),
(21, 'Property Booking Cancellation By Owner - Immediately After Request - Mail To Owner', 'Property Booking Cancelled - By Owner.', 'Quedemonos - Admin', 'admin@vr.com', 'Hello ##OWNER_NAME##,<br><br>&nbsp;&nbsp; &nbsp; You have just cancelled a property booking request for the property<br>&nbsp;##PROPERTY_NAME##. <br><br><br>Below mention are the booking request details:<br><br>Order Id : ##ORDER_ID##<br><br>Arrival Date : ##ARRIVAL_DATE##<br>Departure Date : ##DEPARTURE_DATE##<br>Number of Adults : ##NUM_OF_ADULTS##<br>Number of Children : ##NUM_OF_CHILDREN##<br><br>Below mention are the traveller details:<br><br>Traveller Name: ##TRAVELLER_NAME##<br>Traveller Email Id: ##TRAVELLER_EMAIL##<br>Phone : ##TRAVELLER_PHONE##<br><br><br><br>Thanks and Regards,<br>##SITE_URL##<br><br><br><br><br><br>', '##OWNER_NAME##~##ORDER_ID##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##NUM_OF_ADULTS##~##NUM_OF_CHILDREN##~##TRAVELLER_NAME##~##TRAVELLER_EMAIL##~##TRAVELLER_PHONE##~##SITE_URL##', NULL, '2016-06-15 02:45:59', '2016-07-08 02:50:55'),
(22, 'Reminder to Traveler For Feedback And Rating', 'Reminder For Feedback And Rating', 'Quedemonos - Admin', 'admin@vr.com', 'Hello ##TRAVELLER_NAME##,<br><br>&nbsp;&nbsp; &nbsp; We hope you like our service. We are waiting for your feedback&nbsp; and rating for the property.<br><br><br>Please follow the link below to feedback and rate the property.<br>##PROPERTY_DETAILS_LINK##.<br><br><br><br>Thanks and Regards,<br>##SITE_URL##<br><br><br>', '##TRAVELLER_NAME##~##PROPERTY_NAME##~##PROPERTY_DETAILS_LINK##~##SITE_URL##', NULL, '2016-06-15 19:03:14', '2016-07-08 02:50:59'),
(23, 'Property Booking Cancelled By System For Traveler', 'Your Booking Has been Cancelled at Quedemonos Due to Non Payment Before Due ', 'Quedemonos - Admin', 'admin@vr.com', 'Dear ##USER_NAME##,&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>Sorry.&nbsp; your booking has been&nbsp; cancelled by system for following reasons.<br>&nbsp; &nbsp; 1. ##PAYMENT_STAGE##&nbsp; Payment was not done .<br><br>PROPERTY NAME &nbsp;:&nbsp;&nbsp;##PROPERTY_NAME##&nbsp;<br>ORDER _ID &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:&nbsp;&nbsp;##ORDER_ID##<br>ARRIVAL DATE &nbsp; &nbsp; &nbsp;:&nbsp;&nbsp;##ARRIVAL_DATE##&nbsp;<br>DEPARTURE DATE :&nbsp;##DEPARTURE_DATE##<br><br>Thanks and Regards,<br><b>##SITE_NAME##</b><br>', '##PAYMENT_STAGE##~##USER_NAME##~##PROPERTY_NAME##~##ORDER_ID##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##SITE_NAME##', NULL, '2016-06-15 19:05:21', '2016-07-08 03:21:14'),
(24, 'Property Booking Cancel By System 10 Days Before Arrival For Traveler  ', 'Your Booking Has been Cancelled at Quedemonos Due to Non Payment Before 10 days of arrival ', 'Quedemonos - Admin', 'admin@vr.com', '<p>Dear ##USER_NAME##,&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br /><br />Sorry. Your booking is cancelled by system for following reasons.<br />&nbsp; &nbsp; 1. Final Payment was not done before 10 days of arrival.<br /><br />PROPERTY NAME &nbsp;: &nbsp;##PROPERTY_NAME##&nbsp;<br />ORDER _ID &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;: &nbsp;##ORDER_ID##<br />ARRIVAL DATE &nbsp; &nbsp; &nbsp;:&nbsp;&nbsp;##ARRIVAL_DATE##&nbsp;<br />DEPARTURE DATE :&nbsp;##DEPARTURE_DATE##<br /><br />Please try again.<br /><br />Happy travels,<br /><strong>##SITE_NAME##</strong></p>', '##USER_NAME##~##PROPERTY_NAME##~##ORDER_ID##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##SITE_NAME##', NULL, '2016-06-15 19:07:25', '2016-07-10 22:50:10'),
(25, 'Property Booking Cancellation By Renter Eligble for Refund - Email To Traveler', 'Property Booking Cancellation Successful', 'Quedemonos - Admin', 'admin@vr.com', '<p>Hello ##RENTER_NAME## ,<br /><br />&nbsp; &nbsp; Booking Cancellation&nbsp;request for property&nbsp;<br />&nbsp; &nbsp;&nbsp;<strong>##PROPERTY_NAME##</strong>&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<br />&nbsp;&nbsp; &nbsp;for the dates&nbsp;<strong>##ARRIVAL_DATE##</strong>&nbsp;&nbsp;to&nbsp;<strong>##DEPARTURE_DATE##</strong>&nbsp;is Successfully<br />&nbsp; &nbsp;&nbsp;canceled&nbsp;by&nbsp;you.&nbsp;You are applicable to get a Refund of $&nbsp;<strong>##TOTAL_REFUND##</strong>&nbsp;.<br />&nbsp; &nbsp; Refund will be transferred to your account shortly.<br /><br />Thanks and Regards &nbsp;<br />##SITE_NAME##</p>', '##RENTER_NAME##~##PROPERTY_NAME##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##TOTAL_REFUND##~##SITE_NAME##', NULL, '2016-06-16 21:55:44', '2016-07-11 00:29:51'),
(26, 'Traveler Transaction Successful', 'Traveler Transaction Successfully Done', 'Quedemonos - Admin', 'admin@vr.com', 'Hello ##TRAVELER_NAME##,<br><br><b>Your ##</b>BOOKING_STAGE<b>## Transaction Done Successfully. </b><br><br><u><b>Transaction and Booking Details:</b></u> <br><br>Transaction Id&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; : ##TRANSACTION_ID##<br>Order Id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; : ##ORDER_ID##<br>Amount&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; : ##AMOUNT##<br>Arrival Date&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; : ##ARRIVAL_DATE##<br>Departure Date&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; : ##DEPARTURE_DATE##<br>Number of Adults&nbsp;&nbsp; &nbsp; : ##NUM_OF_ADULTS##<br>Number of Children : ##NUM_OF_CHILDREN##<br><br><u><b>Property Details:</b></u><br><br>Property Name&nbsp;  : ##PROPERTY_NAME## <br><br><br><br>Thanks and Regards,<br>##SITE_URL##<br><br>', '##TRAVELER_NAME##~##TRANSACTION_ID##~##ORDER_ID##~##AMOUNT##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##NUM_OF_ADULTS##~##NUM_OF_CHILDREN##~##PROPERTY_NAME##~##SITE_URL##', NULL, '2016-06-19 21:26:02', '2016-07-08 02:51:24'),
(27, 'Owner Transaction Successful ', 'Your Property Transaction Done Successfully', 'Quedemonos - Admin', 'admin@vr.com', 'Hello ##OWNER_NAME##,<br><br><b>Your Property Transaction Done Successfully. </b><br><br><b><u>Transaction Details:</u></b><br><br>Transaction Id&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; : ##TRANSACTION_ID##<br>Order Id&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; : ##ORDER_ID##<br>Amount&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; : ##AMOUNT##<br><br><b><u>Traveler Details:</u></b><br><br>Traveler Name&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  : ##TRAVELER_NAME##<br>Traveler Mobile No.&nbsp; : ##MOBILE_NO##<br>Traveler Email Id&nbsp; &nbsp; &nbsp; : ##EMAIL_ID##<br>Arrival Date&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp; : ##ARRIVAL_DATE##<br>Departure Date&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; : ##DEPARTURE_DATE##<br>Number of Adults&nbsp;&nbsp; &nbsp; : ##NUM_OF_ADULTS##<br>Number of Children : ##NUM_OF_CHILDREN##<br><br><b><u>Property Details:</u></b><br><br>Property Name&nbsp;  : ##PROPERTY_NAME##<br><br><br><br>Thanks and Regards,<br>##SITE_URL##<br><br><br>', '##OWNER_NAME##~##TRANSACTION_ID##~##ORDER_ID##~##AMOUNT##~##TRAVELER_NAME##~##MOBILE_NO##~##EMAIL_ID##~##ARRIVAL_DATE##~##DEPARTURE_DATE##~##NUM_OF_ADULTS##~##NUM_OF_CHILDREN##~##PROPERTY_NAME##~##SITE_URL##', NULL, '2016-06-19 21:45:48', '2016-07-08 02:51:29'),
(28, 'Property Booking Cancel By System Before 10 Days from Arrival For Owner', 'Property Booking Cancelled By System at Quedemonos Due to Non Payment 10 days Before of arrival ', 'Quedemonos - Admin', 'admin@vr.com', '<p>Dear ##OWNER_NAME##,&nbsp;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br /><br />Sorry. &nbsp;booking is cancel by system for following reasons.<br />&nbsp; &nbsp; 1. Payment was not done within two days by ##USER_NAME## before 10 days from arrival .<br /><br />PROPERTY NAME &nbsp;:&nbsp;&nbsp;##PROPERTY_NAME##&nbsp;<br />ORDER _ID &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;:&nbsp;&nbsp;##ORDER_ID##<br />ARRIVAL DATE &nbsp; &nbsp; &nbsp;:&nbsp;&nbsp;##ARRIVAL_DATE##&nbsp;<br />DEPARTURE DATE :&nbsp;##DEPARTURE_DATE##<br /><br />Happy owners,<br /><strong>##SITE_NAME##</strong><br /><br /></p>', '####USER_NAME####~####ORDER_ID####~####ARRIVAL_DATE## ##~####DEPARTURE_DATE####~####SITE_NAME####', NULL, '2016-07-08 03:35:26', '2016-07-10 23:23:30'),
(29, 'New Property Posted', 'New Property Posted', 'Quedemonos- Admin', 'admin@vr.com', '<p>Hello <strong>Admin</strong>,<br /><br />&nbsp;&nbsp; &nbsp; New property has been posted by following user</p>\r\n<p><strong>Owner Details :</strong></p>\r\n<p>Name :<strong> ##OWNER_NAME##</strong>.</p>\r\n<p>Email : ##EMAIL##.</p>\r\n<p><strong>Property Details :</strong></p>\r\n<p>Property ID : ##PROPERTY_ID##</p>\r\n<p>Headline : ##HEADLINE##.</p>\r\n<p>Bedrooms : ##BEDROOMS##.</p>\r\n<p>Bathrooms : ##BATHROOMS##.</p>\r\n<p>Sleeps : ##SLEEPS##</p>\r\n<p>&nbsp;&nbsp; To verify this property <strong>##CLICK_HERE##</strong><br /><br />Thanks &amp; Regards,<br />##SITE_LINK##</p>', '##OWNER_NAME##~##SITE_LINK##~##CLICK_HERE##~##EMAIL##~##PROPERTY_ID##~##HEADLINE##~##BEDROOMS##~##BATHROOMS##~##SLEEPS##', NULL, '2016-07-11 03:02:47', '2016-07-11 18:59:54');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `title`, `locale`, `status`, `created_at`, `updated_at`) VALUES
(1, 'English', 'en', 1, '2016-02-06 15:47:35', '2016-02-03 03:22:23'),
(2, 'Deutsch', 'de', 0, '2016-02-06 15:47:35', '2016-02-17 00:10:19'),
(3, 'Italiano', 'it', 1, '2016-02-06 15:47:35', '2016-02-19 03:14:03'),
(4, 'Fran&ccedil;ais', 'fr', 0, '2016-02-06 15:47:35', '2016-02-10 08:15:21'),
(5, 'Espa&ntilde;ol', 'eo', 0, '2016-02-06 15:47:35', '2016-02-10 08:15:22'),
(6, 'Portugu&ecirc;s (Brasil)', 'pt-BR', 0, '2016-02-06 15:47:35', '2016-02-04 08:10:24'),
(7, 'Croatian', 'hr', 1, '2016-02-06 15:47:35', '2016-02-04 08:10:25'),
(8, 'Nederlands', 'nl-NL', 0, '2016-02-06 15:47:35', '2016-02-03 07:22:19'),
(9, 'Norsk', 'nn-NO', 0, '2016-02-06 15:47:35', '2016-02-03 05:35:21'),
(10, 'Svenska', 'sv-SE', 0, '2016-02-06 15:47:35', '2016-02-03 05:35:22');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_07_02_230147_migration_cartalyst_sentinel', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2014_07_02_230147_migration_cartalyst_sentinel', 1),
('2014_10_12_100000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persistences`
--

CREATE TABLE `persistences` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `persistences`
--

INSERT INTO `persistences` (`id`, `user_id`, `code`, `created_at`, `updated_at`) VALUES
(3, 1, 'YWEhdzyHy17o28NxgIwPocgKAZdsWR6m', '2016-04-25 09:11:50', '2016-04-25 09:11:50'),
(4, 1, 'ohjZeLW0IJcBzKoSOsVYYPq4pNGLwNW9', '2016-04-25 23:31:27', '2016-04-25 23:31:27'),
(5, 1, 'Kv0vgkcewQYBKGNTrSdKKaMaP2srjqL2', '2016-04-26 00:48:34', '2016-04-26 00:48:34'),
(6, 1, 'BtZFQI6mqYLkBds3viVMWxYFa6ntpjiA', '2016-04-27 06:13:48', '2016-04-27 06:13:48'),
(7, 1, 'kO6IY5kueVHDeCwiaByXUEurIryxIIsZ', '2016-04-30 04:40:09', '2016-04-30 04:40:09'),
(8, 1, 'ODbVCEd5GlK0x1t5YfYGijzITInKeB1w', '2016-04-30 07:01:36', '2016-04-30 07:01:36'),
(9, 1, 'lTdCjvFHEA0fQW27RKpfnL4KzKxNqgDM', '2016-05-01 22:38:20', '2016-05-01 22:38:20'),
(10, 1, 'Toevdm6KNeIiX164iMcyQwYjLmUxf3WX', '2016-05-02 02:55:21', '2016-05-02 02:55:21'),
(11, 1, '2tKCFPQxNKIcnvLNk1FNI8GqjRQtyTY6', '2016-05-02 04:26:53', '2016-05-02 04:26:53'),
(12, 1, 'ph9qUZpA2yWLbfkWXn4VLQ3MBvggZB5g', '2016-05-03 22:51:32', '2016-05-03 22:51:32'),
(13, 1, 'dzq5xfSAGsqhfyJzDXiPzqxCnBFvtrpf', '2016-05-04 22:20:59', '2016-05-04 22:20:59'),
(14, 1, 'KSUtIgDd1r3ut2eg43i0eZYoaMhPJQti', '2016-05-04 23:43:56', '2016-05-04 23:43:56'),
(15, 1, 'tTZF8nLGA4le3uSOIlKHkGTtPLVsxtSz', '2016-05-05 22:26:49', '2016-05-05 22:26:49'),
(17, 1, 'qW7plMaYUYLt53fXOJ40ffdyZVkUjitu', '2016-05-06 00:06:40', '2016-05-06 00:06:40'),
(18, 1, 'xWyvBYTdhyMzsnu3doHgjCFdMEX2Aqvq', '2016-05-06 03:09:00', '2016-05-06 03:09:00'),
(19, 1, 'e2MkxKOD1S14l1BCXva9KgIEoAIewQcC', '2016-05-06 03:18:34', '2016-05-06 03:18:34'),
(20, 1, '5VwxmQJG8HjVbf9RRxG8RtnEH7xpLLsB', '2016-05-06 03:53:32', '2016-05-06 03:53:32'),
(23, 1, 'l4RTliZuRS2bdHEFmiXkpxH1QxTDH196', '2016-05-06 05:34:10', '2016-05-06 05:34:10'),
(25, 1, 'qW2GgENX14F4CXdjRF6SSj0eL9ANNzTo', '2016-05-06 08:17:20', '2016-05-06 08:17:20'),
(26, 1, 'XGRFKQI0wGijkuNFMnaPhI296WVDhnIc', '2016-05-06 08:51:48', '2016-05-06 08:51:48'),
(29, 1, 'QuqQ4OGPVbmCO3M64DLwr8bP8j1UmpKm', '2016-05-06 09:02:00', '2016-05-06 09:02:00'),
(30, 1, 'yBeF82eCEgP9TpBxaiU4xvJnmIQkK1bk', '2016-05-06 09:42:55', '2016-05-06 09:42:55'),
(31, 1, 'u6lsmMYSUmpHzDASlYHn0sWlRw2A9lou', '2016-05-19 08:47:49', '2016-05-19 08:47:49'),
(32, 1, 'Mx3EwspZkZ7gQpYCKIXqcYIXXgMkJsSo', '2016-05-21 04:08:02', '2016-05-21 04:08:02'),
(33, 1, 'sN6jdZX71x8okxAcUnBXq6wBBkGeqspg', '2016-05-22 22:52:22', '2016-05-22 22:52:22'),
(34, 1, 'Xuj6I2ah7iJQZM1RAWyGwo45QUaQrdBK', '2016-06-11 06:26:10', '2016-06-11 06:26:10'),
(35, 1, '5b4nIGn89LO6rVDs7zw45krOKhejc6qW', '2016-07-15 00:53:28', '2016-07-15 00:53:28'),
(37, 1, 'N4iQXjTlnci6vSLgWZxYVfdxxuK5haZn', '2016-07-15 01:59:59', '2016-07-15 01:59:59'),
(38, 1, 'Yh2H8G0XzJLsyqgCKUoecqoXpKM4OfEA', '2016-08-06 04:39:56', '2016-08-06 04:39:56'),
(39, 1, 'aTlVcTPqszrlnmDFVNroSJYlqIGg0iz9', '2016-08-16 07:35:36', '2016-08-16 07:35:36'),
(40, 1, 'bi5UHzlPjARxFGFGDh06OV6bAjxJwt27', '2016-08-16 22:27:44', '2016-08-16 22:27:44'),
(42, 2, 'gdUYcFmBPfmKdQtJ1bvjrPeVtzZD1W9C', '2016-08-17 00:44:16', '2016-08-17 00:44:16'),
(43, 1, 'Hcy4RAVDTI9wRYDqDzXPtCFIXLl6Sv9k', '2016-08-17 01:08:04', '2016-08-17 01:08:04'),
(44, 1, 'X7A1lyoyoh6bT2wyC0f9Mif4nasQPzRX', '2016-08-17 03:43:02', '2016-08-17 03:43:02'),
(45, 2, 'Yf5hU5wX1VjfkdKcp2C1SkZICMAECJVt', '2016-08-20 06:22:56', '2016-08-20 06:22:56'),
(46, 1, 'eselAvWp1pSzx42SZ8AK99shvRGx2U9x', '2016-08-29 03:24:18', '2016-08-29 03:24:18'),
(48, 1, 'mlHBvcAAHdTgRmxIrxCn6Cn7yMHKcoJa', '2016-09-01 03:27:31', '2016-09-01 03:27:31'),
(52, 1, 'iQ6axEtp98AejaWLfACVvzAaHcF6gSgC', '2016-09-01 05:09:17', '2016-09-01 05:09:17'),
(53, 1, 'lGeO68klod1Q7tIZOldKCgtbWrYF4YEX', '2016-09-02 22:26:56', '2016-09-02 22:26:56'),
(54, 1, '6MAmtOfdDbKGjoJdnQY7iETYTpb8gnGR', '2016-09-03 05:07:33', '2016-09-03 05:07:33'),
(55, 1, '9fTeIZjjG5nVMH9PnWBMHp3WzXjx2Ysk', '2016-09-04 23:15:12', '2016-09-04 23:15:12'),
(57, 1, 'Cjpacmqk6ep1G3rk4C0ZWfIJwTTCscIt', '2016-09-06 07:40:25', '2016-09-06 07:40:25'),
(58, 1, 'zcGBqgeiTORN4YvaJXVGvQLGDaPWoBMS', '2016-09-07 01:56:19', '2016-09-07 01:56:19'),
(59, 1, 'ETWChHJZnCXG7q8oG1jrEBt16OQMGAuN', '2016-09-20 22:26:12', '2016-09-20 22:26:12'),
(60, 1, 'MQGg9qur9mxfKDZS6Q901pxiKOGov9ta', '2016-10-03 04:21:48', '2016-10-03 04:21:48'),
(61, 1, 'V4SGvqIP4DUtNO7rNdJMnWdHbojuIWOw', '2016-10-13 05:51:16', '2016-10-13 05:51:16'),
(62, 1, 'fxbR67uUSywpttSrXLMfIb50wXrv74Cm', '2016-10-24 23:05:33', '2016-10-24 23:05:33'),
(63, 1, 'uVWt2FJjD4lZh5QMt8kLYmB4kXvBeO80', '2016-10-26 23:05:55', '2016-10-26 23:05:55'),
(64, 1, 'uju4KRqzNoDfXlOGsoBYyYahEXNm9GCI', '2016-10-27 00:59:44', '2016-10-27 00:59:44');

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `reminders`
--

INSERT INTO `reminders` (`id`, `user_id`, `code`, `completed`, `completed_at`, `created_at`, `updated_at`) VALUES
(19, 1, '6RhbybyEJwpfJaeIvY6tZ87QS6L3Ltg5', 1, '2016-09-01 04:43:00', '2016-09-01 04:33:06', '2016-09-01 04:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `slug`, `name`, `permissions`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'Admin', '{"admin":true}', '2016-04-25 07:50:54', '2016-05-06 06:05:21', NULL),
(11, 'user', 'User', '{"admin":false}', '2016-05-06 06:20:47', '2016-05-06 06:20:47', NULL),
(12, 'business_user', 'Business User', '{"admin":false}', '2016-05-06 06:21:03', '2016-05-06 06:21:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_users`
--

CREATE TABLE `role_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_users`
--

INSERT INTO `role_users` (`user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2016-04-25 07:51:20', '2016-04-25 07:51:20'),
(2, 1, '2016-08-17 00:43:25', '2016-08-17 00:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `site_setting_id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `site_address` varchar(255) NOT NULL,
  `site_contact_number` varchar(255) NOT NULL,
  `meta_desc` text NOT NULL,
  `meta_keyword` varchar(500) NOT NULL,
  `site_email_address` varchar(255) NOT NULL,
  `fb_url` varchar(255) NOT NULL,
  `twitter_url` varchar(255) NOT NULL,
  `google_plus_url` varchar(500) NOT NULL,
  `youtube_url` varchar(255) NOT NULL,
  `rss_feed_url` varchar(255) NOT NULL,
  `instagram_url` varchar(255) NOT NULL,
  `site_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0 - Offline / 1- Online',
  `deleted_at` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`site_setting_id`, `site_name`, `site_address`, `site_contact_number`, `meta_desc`, `meta_keyword`, `site_email_address`, `fb_url`, `twitter_url`, `google_plus_url`, `youtube_url`, `rss_feed_url`, `instagram_url`, `site_status`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, ' Quedemonos12', 'Rental House Mall 39, M.G. Road Boulevard Ground Floor London ', '9876543210', 'Quedemonos Pura Vida', 'Quedemonos Pura Vida', 'info@quedemonos.com', 'http://facebook.com', 'http://twitter.com', 'http://plus.google.com', 'http://youtube.com', 'http://rssfeed.com', 'http://www.instagram.com', '1', 0, '2016-05-30 22:59:12', '2016-08-17 00:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `id` int(11) NOT NULL,
  `public_key` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'active :1 , disable/not-active:0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`id`, `public_key`, `country_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'xyL2xcz', 1, 1, '2016-05-05 23:18:07', '2016-08-16 23:17:49');

-- --------------------------------------------------------

--
-- Table structure for table `state_translation`
--

CREATE TABLE `state_translation` (
  `id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `state_title` varchar(500) CHARACTER SET utf8 NOT NULL,
  `state_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `state_translation`
--

INSERT INTO `state_translation` (`id`, `state_id`, `state_title`, `state_slug`, `locale`, `created_at`, `updated_at`) VALUES
(1, 1, 'Maharashtra', 'maharashtra', 'en', '2016-05-05 23:18:07', '2016-05-05 23:18:07'),
(3, 1, 'M4harashtra', 'm4harashtra', 'es', '2016-05-06 09:02:05', '2016-05-06 09:02:05');

-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

CREATE TABLE `static_pages` (
  `id` int(11) NOT NULL,
  `page_slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `static_pages`
--

INSERT INTO `static_pages` (`id`, `page_slug`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10, 'about-us', '1', '2016-05-26 06:54:01', '2016-05-26 01:24:01', NULL),
(11, 'contact-us', '1', '2016-05-26 06:54:01', '2016-05-26 01:24:01', NULL),
(13, 'faq', '1', '2016-05-26 06:54:01', '2016-05-26 01:24:01', NULL),
(14, 'terms-and-conditions', '1', '2016-05-26 06:54:01', '2016-05-26 01:24:01', NULL),
(15, 'privacy-policy', '1', '2016-05-26 06:54:01', '2016-05-26 01:24:01', NULL),
(16, 'blogs', '1', '2016-05-26 06:54:01', '2016-05-26 01:24:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `static_pages_translation`
--

CREATE TABLE `static_pages_translation` (
  `id` int(11) NOT NULL,
  `static_page_id` int(11) NOT NULL,
  `page_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `page_desc` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `meta_keyword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `static_pages_translation`
--

INSERT INTO `static_pages_translation` (`id`, `static_page_id`, `page_title`, `page_desc`, `locale`, `meta_keyword`, `meta_desc`) VALUES
(4, 10, 'About Us ', '<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s</p>', 'en', 'test Meta Keyword', 'This is the test meta description'),
(13, 13, 'faq', '<p>faq</p>', 'en', 'faq', 'faq'),
(16, 11, 'Contact Us', '<div class="heading">Address</div>\r\n<div class="con-location">900 Biscayne Boulevard, Miami, FL 33132, USA</div>\r\n<p>&nbsp;</p>\r\n<div class="heading">Contact Details</div>\r\n<div class="contact-links">\r\n<ul>\r\n<li><i class="fa fa-phone fa-2x" aria-hidden="true"></i>1-222-333-4444</li>\r\n<li><i class="fa fa-mobile fa-2x" aria-hidden="true"></i>1-234-456-7894</li>\r\n</ul>\r\n</div>\r\n<div class="contact-links">\r\n<ul>\r\n<li><i class="fa fa-fax fa-2x" aria-hidden="true"></i>1-234-456-7894</li>\r\n<li><i class="fa fa-envelope-o fa-2x" aria-hidden="true"></i>info@rentalhouse.com</li>\r\n</ul>\r\n</div>', 'en', 'test', 'testss'),
(17, 14, 'Terms And Conditions', '<div class="pp-head">Lorem Ipsum is simply</div>\r\n<div class="abt-head">\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley &nbsp;desktop publ</p>\r\n</div>\r\n<div class="pp-head">Contrary to popular belief</div>\r\n<div class="abt-head">\r\n<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical.</p>\r\n</div>', 'en', 'Terms And Conditions', 'Terms And Conditions'),
(18, 15, 'Privacy Policy', '<div class="pp-head">Lorem Ipsum is simply</div>\r\n<div class="abt-head">\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\r\n</div>', 'en', 'Privacy Policy', 'Privacy Policy'),
(19, 16, 'Blogs', '<p>Blogs</p>', 'en', 'Blogs', 'Blogs'),
(24, 11, 'Contáctenos', '<div class="heading">\n<pre class="tw-data-text vk_txt tw-ta tw-text-large" data-placeholder="Translation" id="tw-target-text" data-fulltext="" dir="ltr"><span lang="es">Direcci&oacute;n</span></pre>\n</div>\n<div class="con-location"><span>900 Biscayne Boulevard, Miami , FL 33132 , EE.UU.</span></div>\n<p>&nbsp;</p>\n<div class="heading">\n<pre class="tw-data-text vk_txt tw-ta tw-text-medium" data-placeholder="Translation" id="tw-target-text" data-fulltext="" dir="ltr"><span lang="es">Detalles de contacto</span></pre>\n</div>\n<div class="contact-links">\n<ul>\n<li><i class="fa fa-phone fa-2x" aria-hidden="true"></i>1-222-333-4444</li>\n<li><i class="fa fa-mobile fa-2x" aria-hidden="true"></i>1-234-456-7894</li>\n</ul>\n</div>\n<div class="contact-links">\n<ul>\n<li><i class="fa fa-fax fa-2x" aria-hidden="true"></i>1-234-456-7894</li>\n<li><i class="fa fa-envelope-o fa-2x" aria-hidden="true"></i>info@rentalhouse.com</li>\n</ul>\n</div>', 'es', '', ''),
(25, 10, 'Sobre nosotros', '<p><span lang="es">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industr</span></p>', 'es', '', ''),
(26, 13, 'Preguntas más frecuentes', '<div class="tw-ta-container tw-nfl" id="tw-target-text-container">Preguntas m&aacute;s frecuentes</div>', 'es', 'Preguntas más frecuentes', 'Preguntas más frecuentes'),
(27, 14, 'Términos y Condiciones', '<p><span lang="es">Lorem Ipsum es simplemente Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto . Lorem Ipsum ha sido el texto de relleno est&aacute;ndar de la industria desde el a&ntilde;o 1500, cuando un desconocido tom&oacute; una impresora de escritorio publ galera Contrario a la creencia popular Contrariamente a la creencia popular , Lorem Ipsum no es simplemente texto aleatorio . Tiene sus ra&iacute;ces en una pieza de la literatura cl&aacute;sica latina de 45 aC , por lo que es m&aacute;s de 2000 a&ntilde;os de antig&uuml;edad. Richard McClintock , un profesor de lat&iacute;n en Hampden - Sydney College en Virginia, encontr&oacute; una de las palabras latinas m&aacute;s oscuros , Consectetur , a partir de un pasaje de Lorem Ipsum , y pasando por la cita de la palabra en el cl&aacute;sico.</span></p>', 'es', 'Términos y Condiciones', 'Términos y Condiciones'),
(28, 15, 'Política de privacidad', '<p><span lang="es">Lorem Ipsum es simplemente Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto . Lorem Ipsum ha sido el texto de relleno est&aacute;ndar de la industria desde el a&ntilde;o 1500, cuando un desconocido tom&oacute; una impresora de cocina tipo y codificados para hacer un libro de textos especimen. Ha sobrevivido no s&oacute;lo cinco siglos , sino tambi&eacute;n el salto a la composici&oacute;n tipogr&aacute;fica electr&oacute;nica , quedando esencialmente sin cambios . Se populariz&oacute; en la d&eacute;cada de 1960 con el lanzamiento de las hojas de Letraset que contienen pasajes de Lorem Ipsum, y m&aacute;s recientemente con software de autoedici&oacute;n , como Aldus PageMaker incluidas las versiones de Lorem Ipsum .</span></p>', 'es', 'Política de privacidad', 'Política de privacidad'),
(29, 16, 'Blogs', '<p>Blogs</p>', 'es', 'Blogs', 'Blogs'),
(30, 17, 'test', '<p>test</p>', 'en', 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `throttle`
--

CREATE TABLE `throttle` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `throttle`
--

INSERT INTO `throttle` (`id`, `user_id`, `type`, `ip`, `created_at`, `updated_at`) VALUES
(1, NULL, 'global', NULL, '2016-09-07 01:54:20', '2016-09-07 01:54:20'),
(2, NULL, 'ip', '192.168.1.61', '2016-09-07 01:54:20', '2016-09-07 01:54:20'),
(3, NULL, 'global', NULL, '2016-09-07 01:56:11', '2016-09-07 01:56:11'),
(4, NULL, 'ip', '192.168.1.61', '2016-09-07 01:56:11', '2016-09-07 01:56:11'),
(5, 1, 'user', NULL, '2016-09-07 01:56:11', '2016-09-07 01:56:11'),
(6, NULL, 'global', NULL, '2016-10-27 00:59:36', '2016-10-27 00:59:36'),
(7, NULL, 'ip', '192.168.1.62', '2016-10-27 00:59:36', '2016-10-27 00:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permissions` text COLLATE utf8_unicode_ci,
  `last_login` timestamp NULL DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `permissions`, `last_login`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
(1, 'admin@laramin.com', '$2y$10$D45W4.w0QmoZb5AUo/KIiuYh0RCnIC4JJZYgA3q60NmWbEG8E0HRa', NULL, '2016-10-27 00:59:44', 'Admin1', 'Admin', '2016-04-25 07:50:44', '2016-10-27 00:59:44'),
(2, 'nitesha@webwingtechnologies.com', '$2y$10$D45W4.w0QmoZb5AUo/KIiuYh0RCnIC4JJZYgA3q60NmWbEG8E0HRa', NULL, '2016-08-20 06:22:56', 'Nitesh', 'Acharya', '2016-08-17 00:43:25', '2016-08-20 06:22:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activations`
--
ALTER TABLE `activations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories_translation`
--
ALTER TABLE `categories_translation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `city_translation`
--
ALTER TABLE `city_translation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_enquiry`
--
ALTER TABLE `contact_enquiry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries_translation`
--
ALTER TABLE `countries_translation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`),
  ADD KEY `password_resets_token_index` (`token`);

--
-- Indexes for table `persistences`
--
ALTER TABLE `persistences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `persistences_code_unique` (`code`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_users`
--
ALTER TABLE `role_users`
  ADD PRIMARY KEY (`user_id`,`role_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`site_setting_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `state_translation`
--
ALTER TABLE `state_translation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `static_pages_translation`
--
ALTER TABLE `static_pages_translation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `throttle`
--
ALTER TABLE `throttle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `throttle_user_id_index` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activations`
--
ALTER TABLE `activations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT for table `categories_translation`
--
ALTER TABLE `categories_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `city_translation`
--
ALTER TABLE `city_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `contact_enquiry`
--
ALTER TABLE `contact_enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=511;
--
-- AUTO_INCREMENT for table `countries_translation`
--
ALTER TABLE `countries_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `email_template`
--
ALTER TABLE `email_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `persistences`
--
ALTER TABLE `persistences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `site_setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `state_translation`
--
ALTER TABLE `state_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `static_pages_translation`
--
ALTER TABLE `static_pages_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT for table `throttle`
--
ALTER TABLE `throttle`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
