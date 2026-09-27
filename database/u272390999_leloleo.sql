-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 05, 2026 at 11:41 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u272390999_leloleo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_logged_in` tinyint(1) DEFAULT 0,
  `active_session_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Active',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `profile_image`, `created_at`, `remember_token`, `token_expiry`, `last_login`, `is_logged_in`, `active_session_id`, `status`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'Super Admin', 'admin.farmlelo@gmail.com', '$2y$10$x9lZGNGmw5Dl6oMJCzVbUu5iYEEtrnc5HZc/5Ty.1bt1rKtAfKx16', '', '2026-05-05 07:01:18', '6ea24ca6b86ddc533ed5050bc671835d447d4067710060438946154e1d3678bb', '2026-09-19 07:50:50', '2026-08-26 10:15:27', 1, NULL, 'active', NULL, NULL),
(3, 'admin', 'admin@gmail.com', '$2y$10$sRccv3aiEdQueWT7EHWeD.KgW68Qom2QRfdZDsc2RApSm0ZTcuzJW', '', '2026-08-06 06:14:19', '74fec83379f5660ce2a2913940740022', '2026-09-05 17:15:45', '2026-09-05 11:15:45', 1, NULL, 'active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT 'general' COMMENT 'general | kitchen | bedroom | bathroom | other',
  `icon_class` varchar(100) DEFAULT NULL COMMENT 'Icon class or key (e.g. fa-swimming-pool)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `name`, `category`, `icon_class`) VALUES
(1, 'Swimming Pool', 'general', 'pool'),
(2, 'Garden', 'general', 'yard'),
(3, 'Parking', 'general', 'local_parking'),
(4, 'WiFi', 'general', 'wifi'),
(5, 'Kitchen', 'kitchen', 'kitchen'),
(6, 'Music System', 'general', 'speaker'),
(7, 'AC Rooms', 'bedroom', 'ac_unit'),
(8, 'Bonfire', 'general', 'local_fire_department'),
(12, 'CCTV Camera', 'general', 'videocam'),
(13, 'Private Swimming Pool', 'general', 'pool'),
(14, 'Parking Facility', 'general', 'directions_car'),
(15, 'Washroom', 'bedroom', 'wc'),
(16, 'Wardrobe', 'bedroom', 'door_front'),
(17, 'Double Bed', 'bedroom', 'bed'),
(18, 'Fan', 'bedroom', 'mode_fan'),
(20, 'Stove', 'kitchen', 'oven'),
(21, 'Utensils', 'kitchen', 'restaurant'),
(22, 'RO', 'kitchen', 'coffee_maker');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_dates`
--

CREATE TABLE `blocked_dates` (
  `id` int(11) NOT NULL,
  `farmhouse_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `locked_by` enum('admin','owner') NOT NULL DEFAULT 'admin',
  `owner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `farmhouse_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_requests`
--

CREATE TABLE `booking_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `farmhouse_id` int(11) NOT NULL,
  `booking_type` enum('complete','per_room') NOT NULL DEFAULT 'complete',
  `room_type_id` int(11) DEFAULT NULL,
  `room_type_name` varchar(100) DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `guests` int(11) DEFAULT 1,
  `rooms` int(11) DEFAULT 1,
  `price_per_room` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','approved','rejected','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `message` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_requests`
--

INSERT INTO `booking_requests` (`id`, `user_id`, `farmhouse_id`, `booking_type`, `room_type_id`, `room_type_name`, `check_in`, `check_in_time`, `check_out`, `check_out_time`, `guests`, `rooms`, `price_per_room`, `price`, `status`, `created_at`, `message`, `start_date`, `end_date`) VALUES
(32, 35, 34, 'complete', NULL, NULL, '2026-08-29', '08:00:00', '2026-08-31', '08:00:00', 2, 1, NULL, 2520.00, 'rejected', '2026-08-20 11:33:30', NULL, '0000-00-00', '0000-00-00'),
(34, 3, 36, 'complete', NULL, NULL, '2026-08-25', '08:00:00', '2026-08-29', '08:00:00', 2, 1, NULL, 21000.00, 'rejected', '2026-08-22 10:37:16', '', '2026-08-25', '2026-08-29'),
(35, 37, 34, 'complete', NULL, NULL, '2026-08-23', '08:00:00', '2026-09-01', '08:00:00', 4, 2, NULL, 22680.00, 'rejected', '2026-08-22 10:49:23', '', '2026-08-23', '2026-09-01'),
(36, 3, 37, 'complete', NULL, NULL, '2026-08-29', '08:00:00', '2026-08-31', '08:00:00', 2, 1, NULL, 4200.00, 'rejected', '2026-08-24 06:09:46', '', '2026-08-29', '2026-08-31'),
(37, 38, 36, 'complete', NULL, NULL, '2026-09-05', '08:00:00', '2026-09-06', '08:00:00', 2, 1, NULL, 5250.00, 'completed', '2026-08-30 09:15:38', '', '2026-09-05', '2026-09-06');

-- --------------------------------------------------------

--
-- Table structure for table `contact_inquiries`
--

CREATE TABLE `contact_inquiries` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `Message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_inquiries`
--

INSERT INTO `contact_inquiries` (`id`, `full_name`, `phone`, `email`, `Message`, `created_at`) VALUES
(2, 'Sarah Williams', '+91 9123456780', 'sarah.williams@example.com', 'Looking for a family vacation package to Manali in December with sightseeing and snow activities.', '2026-05-07 05:22:22'),
(3, 'Michael Brown', '+91 9988776655', 'michael.brown@example.com', 'Business trip to Dubai from 5th August to 9th August. Require airport pickup and hotel booking.', '2026-05-07 05:22:22'),
(4, 'Priya Sharma', '+91 9090909090', 'priya.sharma@example.com', 'Need a honeymoon package for Bali including private villa and local tours for 7 days.', '2026-05-07 05:22:22'),
(5, 'bytelabs', '9111959109', 'vishalmarmat1222@gmail.com', 'test', '2026-05-07 06:40:33'),
(6, 'Grover Schumacher', '', 'domains@search-farmlelo.com', 'Hi\r\n\r\nInclude farmlelo.com in GoogleSearchIndex so it can appear in google search results!\r\n\r\nSubmit farmlelo.com now: https://searchregister.info', '2026-05-11 17:42:01'),
(7, 'Fawn Meacham', '7764843473', 'domains@search-farmlelo.com', 'Hello\r\n\r\nInclude farmlelo.com in GoogleSearchIndex to show up in web search results!\r\n\r\nPlace farmlelo.com now: https://searchregister.info', '2026-05-11 19:08:16'),
(8, 'VISHAL MARMAT', '0000000000', 'vishal@gmail.com', 'vvvvvvvvvv', '2026-05-19 06:04:07'),
(9, 'Sofia', '672052270', 'sofia.sale01@gmail.com', 'Hi http://farmlelo.com,\r\n\r\nI hope you\'re doing well.\r\n\r\nI wanted to reach out and offer my Virtual Assistant services. I can help with administrative tasks, email management, scheduling, data entry, internet research, customer support, and other routine work so you can focus on growing your business.\r\n\r\nI provide reliable support, quick communication, and attention to detail at affordable rates.\r\n\r\nIf you\'re interested, I\'d be happy to share my pricing and service details.\r\n\r\nThanks & Regards\r\nSofia', '2026-06-17 09:43:57'),
(10, 'Radhika', '6646965683', 'radhikasunar788@gmail.com', 'Hi http://farmlelo.com,\r\n\r\nI noticed that many business owners spend valuable time on administrative work instead of growing their business.\r\n\r\nI can help with email management, scheduling, data entry, customer support, research, and other daily tasks so you can focus on more important work.\r\n\r\nWould you like me to send a list of services and pricing?\r\n\r\nThanks\r\nRadhika', '2026-06-17 09:46:29'),
(11, 'Judi Wilsmore', '6999752883', 'domains@search-farmlelo.com', 'Hey\r\n\r\nFeature farmlelo.com in GoogleSearchIndex and have it show up in web search results!\r\n\r\nInsert farmlelo.com now: https://searchregister.org', '2026-06-22 20:12:45'),
(12, 'Mattie Bethel', '477313040', 'mattie.bethel5@yahoo.com', 'Grow farmlelo.com website\'s backlinks with best seo backlinks and google standings!\r\nDailyseolinks.com - we build daily backlinks and improve google appearance every day:\r\n\r\n1,000+ backlinks daily\r\nReal google visitors\r\nPlans as low as $1\r\nUp to 85% savings:\r\n\r\nhttps://tiny.cc/daily-85save\r\n\r\nDailyseolinks.com - daily seo backlinks to skyrocket your seo backlinks every day', '2026-06-23 21:31:11'),
(13, 'Emma Johnson', '6609388493', 'emma.99seosolutionworld@gmail.com', 'Hello http://farmlelo.com,\r\n\r\nI’m an SEO Expert and I helped over 1700+businesses rank on the (First Page on Google).\r\n \r\nWe analyze your website to identify SEO errors, keyword opportunities, and competition.\r\n\r\nWe can place your website on Google\'s 1st page, Facebook, Twitter, YouTube... \r\n\r\nLet me know if you are interested. So can I send you a price list/Quote with package? \r\n\r\nThank you,  \r\nEmma', '2026-07-01 18:18:00'),
(14, 'Margo Starling', '616377299', 'domains@search-farmlelo.com', 'Greetings\r\n\r\nFeature farmlelo.com in GoogleSearchIndex to be visible in online search results!\r\n\r\nInclude farmlelo.com now: https://searchregister.live', '2026-07-01 22:05:11'),
(15, 'Milla Maresca', '46975823', 'milla.maresca@googlemail.com', 'Hello from DailySeoLinks.com - we build daily backlinks and expand google appearance every day:\r\n\r\n+ 1,000+ seo backlinks every day\r\n+ Organic google clicks\r\n+ Free backlinks to test\r\n+ Up to 85% deals:\r\n\r\nhttps://dailyseolinks.com/deal\r\n\r\nDailyseolinks.com - daily seo plans to increase your website\'s seo everyday', '2026-07-07 06:23:39'),
(16, 'Phoebe Strout', '3956619511', 'noreply@noreply-farmlelo.com', 'Dear Sir/Madam\r\n\r\nHi,\r\n\r\nCongrats on your new domain, farmlelo.com.\r\n\r\nIf you’re looking for someone to build your website, we’d be happy to help.\r\n\r\nWe create professional WordPress websites that are fast, responsive and SEO-friendly.\r\n\r\nWhether you need a brand-new website or a modern redesign, we’re here to help.\r\n\r\nTake a look at WebLaunched.net for examples of our work.', '2026-07-08 07:43:04'),
(17, 'Vincent Dorsch', '353613005', 'domains@search-farmlelo.com', 'Hey\r\n\r\nInsert farmlelo.com in GoogleSearchIndex and have it be visible in online search results!\r\n\r\nFeature farmlelo.com now: https://searchregister.live', '2026-07-14 21:23:25'),
(18, 'Francesco Chisolm', '8193752270', 'francesco.chisolm@msn.com', 'Howdy from Dreamproxies\r\n\r\nSuperb proxy offer: premium, fast speed and trusted private proxies now even cheaper!\r\n50% Cheaper - for all private proxies:\r\n\r\nhttps://tiny.cc/dreamproxies-offers\r\n\r\nFully Anonymous, Premium Quality, Blazing Speed, Unlimited Data, Reliable Servers, Affordable Prices, Bonus Discounts and many more\r\n\r\n50% Cheaper for all proxy packages - by Dreamproxies.com', '2026-07-17 13:02:35'),
(19, 'Janell Scerri', '421639407', 'domains@search-farmlelo.com', 'Dear Sir/Madam\r\n\r\nFeature farmlelo.com in Google\'s Search Index and have it be visible in web search results!\r\n\r\nSubmit farmlelo.com today:\r\n\r\nhelpindex.org', '2026-07-24 19:14:25'),
(20, 'Shane Waldman', '6994826558', 'domains@search-farmlelo.com', 'Hey\r\n\r\nFeature farmlelo.com in Google\'s Search Index and have it appear in online search results!\r\n\r\nSubmit farmlelo.com here:\r\n\r\nhelpindex.pro', '2026-08-05 23:58:25'),
(21, 'Mishra', '722599885', 'anaya.dgtlsolution@gmail.com', 'Greetings, \r\n\r\nI recently came across http://farmlelo.com and wanted to get in touch. Your website has good potential, and with the right SEO strategy, it could reach more customers through Google.\r\n\r\nOur SEO services are customized to improve rankings, increase organic traffic, and help businesses generate consistent leads.\r\n\r\nIf this is something you\'d be interested in, let me know, and I\'ll send our SEO strategies along with our package details.\r\n\r\nBest regards,\r\nAnaya', '2026-08-11 09:49:26'),
(22, 'Teri MacDevitt', '4153744698', 'domains@search-farmlelo.com', 'Hello\r\n\r\nRegister farmlelo.com in Google\'s Search Index to be displayed in web search results!\r\n\r\nAdd farmlelo.com today:\r\n\r\nsearchindex.net', '2026-08-17 20:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `farmhouses`
--

CREATE TABLE `farmhouses` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `google_map_link` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Farmhouse' COMMENT 'Guest House | Resort | Farmhouse | Villa',
  `price` decimal(10,2) DEFAULT NULL,
  `room_price` decimal(10,2) DEFAULT NULL,
  `allow_room_booking` tinyint(1) NOT NULL DEFAULT 0,
  `bedrooms` int(11) DEFAULT 1 COMMENT 'Number of bedrooms',
  `bedroom_capacity` int(11) NOT NULL DEFAULT 2,
  `day_capacity` int(11) DEFAULT NULL COMMENT 'Max guests during daytime',
  `night_capacity` int(11) DEFAULT NULL COMMENT 'Max guests for overnight stay',
  `is_negotiable` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','active','rejected') DEFAULT 'pending',
  `admin_approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `contact_phone` varchar(20) DEFAULT NULL,
  `whatsapp_number` varchar(20) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `owner_notes` text NOT NULL,
  `created_by` enum('admin','owner') DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farmhouses`
--

INSERT INTO `farmhouses` (`id`, `title`, `description`, `location`, `address`, `google_map_link`, `category`, `price`, `room_price`, `allow_room_booking`, `bedrooms`, `bedroom_capacity`, `day_capacity`, `night_capacity`, `is_negotiable`, `created_at`, `status`, `admin_approval_status`, `contact_phone`, `whatsapp_number`, `owner_id`, `owner_notes`, `created_by`) VALUES
(4, 'Eco Farm', 'Eco friendly', 'Bhopal', 'Village Area', NULL, 'Farmhouse', 3500.00, NULL, 0, 1, 2, NULL, 20, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(6, 'Palm Retreat', 'Palm trees farm', 'Goa', 'Beach Side', NULL, 'Farmhouse', 8000.00, NULL, 0, 1, 2, NULL, 17, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(13, 'Budget Farm', 'Affordable', 'Indore', 'Main Road', NULL, 'Farmhouse', 2000.00, NULL, 0, 1, 2, NULL, 31, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(14, 'Organic Farm', 'Organic farming', 'Bhopal', 'Village', NULL, 'Farmhouse', 3800.00, NULL, 0, 1, 2, NULL, 35, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(15, 'River Farm', 'River side', 'Udaipur', 'River Road', NULL, 'Farmhouse', 5500.00, NULL, 0, 1, 2, NULL, 38, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(16, 'Desert Farm', 'Desert stay', 'Jaisalmer', 'Desert', NULL, 'Farmhouse', 7000.00, NULL, 0, 1, 2, NULL, 34, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(17, 'Green Hills', 'Hill farm', 'Shimla', 'Hill Road', NULL, 'Farmhouse', 7200.00, NULL, 0, 1, 2, NULL, 35, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(18, 'Farm Bliss', 'Relax stay', 'Manali', 'Town Area', NULL, 'Farmhouse', 6800.00, NULL, 0, 1, 2, NULL, NULL, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(20, 'Peace Farm', 'Calm place', 'Bhopal', 'Lake Area', NULL, 'Farmhouse', 4800.00, NULL, 0, 1, 2, NULL, 12, 0, '2026-03-21 13:21:00', 'active', 'approved', NULL, NULL, NULL, '', 'admin'),
(34, 'green vally', 'this is for testing purpose', 'indore', '57 shree krishna square sastri nagar', NULL, 'Farmhouse', 1200.00, NULL, 0, 10, 2, 10, 8, 1, '2026-05-19 06:12:06', 'rejected', 'rejected', '9876543210', '9876543211', NULL, 'nothing', 'owner'),
(36, 'VANWAS', 'NEAR INDORE<ol><li>&nbsp;INDORE SE 20 KM&nbsp;</li><li>NEAR 4PAI RESTURANT</li><li>PARTY FRIENDLY</li><li>SWIMMING POOL&nbsp;</li></ol>', 'INDORE', 'INDORE , NEAR 4 PAI RESTURANT', NULL, 'Farmhouse', 15000.00, NULL, 1, 3, 2, 4, 4, 0, '2026-08-20 10:23:09', 'active', 'approved', '9754015070', '9754015070', 11, '', 'admin'),
(37, 'HOLIDAY FARM', '<ol><li>facilities&nbsp;</li><li>&nbsp;1 baby pool 15*15</li><li>1 big pool 16*40</li><li>&nbsp;Rain Shower</li><li>1 big lawn</li></ol>', 'INDORE', 'PARADISE 21 FARMS AND RESORT THIKANA UK NEAR CRESENT WATER PARK INDORE [ MP ]', NULL, 'Farmhouse', 15000.00, 2000.00, 1, 3, 2, 2, 15, 0, '2026-08-20 12:04:43', 'active', 'approved', '9669660621', '9669660621', 11, '', 'admin'),
(38, 'CASA BONITA', '<b><span style=\"font-size: 0.875rem;\">1.</span><span style=\"font-size: 0.875rem;\">GARDEN</span></b><div><span style=\"font-size: 0.875rem;\"><b>2. SWIMMING POOL</b></span></div><div><span style=\"font-size: 0.875rem;\"><b>3.PARKING FACILITY</b></span></div><div><span style=\"font-size: 0.875rem;\"><b>4.AC ROOMS</b></span></div><div><br></div>', 'SOHNA', 'SOHNA', NULL, 'Farmhouse', 30000.00, NULL, 0, 6, 2, 50, 50, 1, '2026-09-02 10:46:10', 'active', 'approved', '9754015070', '9754015070', 11, '', 'admin'),
(39, 'THE FARM SEZ', '1.SWIMMING POOL<div>2.PARKING FACILITY</div><div>3.AC ROOMS</div>', 'JAIPUR , 302026, RAJASTHAN , INDIA', 'PLOT NO.122 MAHINDRA SEZ ROAD, JAIPUR 302026', NULL, 'Farmhouse', 12000.00, NULL, 0, 3, 2, 25, 10, 0, '2026-09-02 11:01:36', 'active', 'approved', '8209923791', '8209923791', 11, '', 'admin'),
(40, 'PARIKALP FARMSTAY', '', 'PACHMARI ROAD , 461775', 'MATKULI , IN FRONT SARASWATI SISHU MANDIR SCHOOL , PACHMARHI ROAD 461775', NULL, 'Farmhouse', 15000.00, NULL, 0, 8, 2, 25, 32, 0, '2026-09-02 11:35:46', 'active', 'approved', '9425367490', '9425367490', 11, '', 'admin'),
(41, 'Surve Natures Resort', '<div>.LOCATION NEAR BY&nbsp;&nbsp;</div><div>1. PEACE FULL MOUNTAIN VIEW/HILL STATION VIEW/RIVER/LAKE VIEW&nbsp; &nbsp;=Yes&nbsp;</div><div>2. RESTURANT/CAFE FOR GOOD FOOD&nbsp; =Yes</div><div>&nbsp; &nbsp;</div><div>3. SWING &amp; SLIDS&nbsp; = YES</div><div>4.TOTAL ROOMS= ( 7)</div><div>5.PER ROOM CHARGE =&nbsp; (&nbsp;15000 / per room deluxe:- 3000</div><div>Per room classic : 2500)</div><div>6.MAXIMUM GUEST- (15)</div><div>7.EXTRA PERSON ADDITIONAL CHARGE = (500)</div><div>8. BEAUTYFULL/LUXURY POOLSIDE PROPERTY&nbsp; &nbsp;= Yes</div><div>9.BEAUTIFULL GARDEN =Yes</div><div>10. LUXURY LIVING AREA&nbsp; = Yes&nbsp;</div><div>11.MODERN KICHEN THERE YOU CAN MAKE YOUR FOOD&nbsp; &nbsp;( Available But we don’t allowed to use )</div><div>12. PARTY/ LAWN AREA = Yes&nbsp;</div><div>13. SUSPICIOUS PARKING&nbsp; = Yes&nbsp;</div><div>14. OUTSAID FOOD ALLOWED = Yes</div>', 'Location :- OPP hanuman mandir, Marg thamane village,chiplun taluka, dist Ratnagiri :- 415702', 'Address And Location :- OPP hanuman mandir, Marg thamane village,chiplun taluka, dist Ratnagiri :- 415702', NULL, 'Farmhouse', 3000.00, NULL, 0, 7, 2, 14, 15, 1, '2026-09-03 12:03:36', 'active', 'approved', '8928730032', '8928730032', 11, '', 'owner'),
(42, 'KANHA GREEN', '<div><b>1.NEAR LNCT COLLECTION RAISEN ROAD BHOPAL</b></div><div><b>2. RESTURANT/CAFE FOR GOOD FOOD</b></div><div><b>3. BIG/KIDS PLAY AREA</b></div><div><b>4. SWING &amp; SLIDS /PLAY ZONE FOR KIDS</b></div><div><b>5.TOTAL ROOMS- 12</b></div><div><b>6.PER ROOM CHARGE- VARY THE FACILITY OF THE ROOM&nbsp;</b></div><div><b>7.MAXIMUM GUEST-</b></div><div><b>8.EXTRA PERSON ADDITIONAL CHARGE - 300&nbsp;</b></div><div><b>9. BEAUTYFULL/LUXURY POOLSIDE PROPERTY</b></div><div><b>10.BEAUTIFULL GARDEN</b></div><div><b>11. LUXURY LIVING AREA</b></div><div><b>12. 24/7 SECURITY</b></div><div><b>13. PARTY/ LAWN AREA</b></div><div><b>14. PARTY FRIENDLY</b></div><div><b>15. SUSPICIOUS PARKING</b></div>', 'BHOPAL', 'NEAR LNCT COLLECTION RAISEN ROAD BHOPAL', NULL, 'Resort', 45000.00, 2000.00, 1, 12, 2, 25, 10, 0, '2026-09-03 12:15:25', 'active', 'approved', '9111000945', '9111000945', 11, '', 'admin'),
(43, 'Bagayatdar Farm House', '<div>1.LOCATION NEAR BY =&nbsp;DODAMARG SINDHUDURG</div><div>2. PEACE FULL MOUNTAIN VIEW/HILL STATION VIEW/RIVER/LAKE VIEW =Yes</div><div>3. BIG/KIDS PLAY AREA =&nbsp; Yes</div><div>4.TOTAL ROOMS= 3&nbsp;</div><div>5.PER ROOM CHARGE= 5000</div><div>6.MAXIMUM GUEST= 10</div><div>7.EXTRA PERSON ADDITIONAL CHARGE = No</div><div>8.BEAUTIFULL GARDEN = Yes</div><div>9. PARTY FRIENDLY =Yes&nbsp;</div><div>10. OUTSAID FOOD ALLOWED&nbsp; =No</div>', 'North Goa', 'zolambe Fanaswadi, near Shree Dattmandir, Zolambe, Dodamarg, Maharashtra 416511', NULL, 'Farmhouse', 5000.00, NULL, 0, 3, 2, 2, 10, 0, '2026-09-04 06:17:16', 'active', 'approved', '', '', 11, '', 'owner'),
(44, 'SUMAN\'S COURTYARD HOMESTAY', '', 'CHHINDWARA', '', NULL, 'Guest House', 20000.00, 2000.00, 1, 2, 2, 25, 10, 0, '2026-09-04 06:24:03', 'active', 'approved', '79990 40187', '8889000399', 11, '', 'admin'),
(45, 'Anant Home Stay', '<div>1.LOCATION NEAR BY = B<span style=\"color: rgb(31, 31, 31); font-family: Arial, sans-serif; font-weight: 400; font-size: 0.875rem;\">ehind Bank Of India</span></div><div>2.TOTAL ROOMS= 4&nbsp;&nbsp;</div><div>3.EXTRA PERSON ADDITIONAL CHARGE = 3&nbsp;&nbsp;</div><div>4.BEAUTIFULL GARDEN = Yes</div><div>5. SUSPICIOUS PARKING =Yes&nbsp;</div><div>6. OUTSAID FOOD ALLOWED&nbsp; =Yes</div>', 'Malipura, Ujjain, Madhya Pradesh 456001', 'kshir sagar, 3/5, behind Bank of India, Malipura, Ujjain, Madhya Pradesh 456001', NULL, 'Guest House', 8000.00, NULL, 0, 4, 2, 2, 10, 0, '2026-09-04 06:42:16', 'active', 'approved', '7000277052', '7000277052', 11, '', 'owner'),
(46, 'Paradise 21 Farms And Resort Thikana', '<div>1. NEAR&nbsp;cresent water park indore mp</div><div><span style=\"font-size: 0.875rem;\">2. 6 AC rooms&nbsp;</span></div><div>3. 1 baby pool 15*15</div><div>4. 1 big pool 16*40</div><div>5. Rain shower&nbsp;</div><div>6. Sound troly speekar&nbsp;</div><div>7. Freez&nbsp;</div><div>8. Cooler hall me&nbsp;</div><div>9. Dining hall 25*60&nbsp;</div><div>10 Outdoor sitting 20 members gajibo&nbsp;</div><div>11 . 1 small lawn&nbsp;</div><div>12 .1 big lawn&nbsp;</div><div>13 .20 cars parking&nbsp;</div><div>14 .Full Security&nbsp;</div><div>15.&nbsp; 24*7 Security facility</div>', 'INDORE', 'Paradise 21 farms and resort thikana uk near cresent water park indore mp', NULL, 'Resort', 15000.00, 3000.00, 1, 2, 2, 25, 25, 0, '2026-09-04 06:50:53', 'active', 'approved', '96696 60621', '96696 60621', 11, '', 'admin'),
(47, 'Bhavya Farms and Resort', '<div><span style=\"font-size: 0.875rem;\">🏠 *Property Amenities:</span></div><div><span style=\"font-size: 0.875rem;\">&nbsp;&nbsp;</span></div><div>🔸 *4 Bedrooms:* Attached washroom + 100L cooler in each (Non-AC)</div><div>🔸 *2 Big Halls:* Spacious gathering area</div><div>🔸 *Kitchen:* Available (Gas &amp; utensils not provided)</div><div>🔸 *Outdoors:* Private Pool, Garden &amp; Music System</div><div>🔸 *Parking:* Ample space inside the property</div><div><span style=\"font-size: 0.875rem;\">🔸 *</span><span style=\"font-size: 0.875rem;\">&nbsp;PRIVATE SWIMMING POOL</span></div><div>🛏️ *Extra Mattress:* Available on request (charges apply).</div><div>⚠️ *House Rules:*</div><div>We kindly ask that you treat the farm like your own, keep the pool clean, use the dustbins, and check out on time so we can prepare the property for the next party, helping you avoid any standard extra fees for damages or delays.</div><div>Looking forward to hosting you!</div><div>&nbsp; &nbsp; &nbsp; &nbsp; PRICE :-</div><div><div>16 and less - 12k + 2k refundable security deposit&nbsp;</div><div><br></div><div>16-20 members - 14k + 2k refundable security deposit&nbsp;</div><div><br></div><div>20- 30 - 16k + 2k refundable security deposit&nbsp;</div><div><br></div><div>Mattress and blankets will be charged separately</div></div>', 'Harsola , Madhya Pradesh', 'Ambachandan,Harsola , Madhya Pradesh', NULL, 'Farmhouse', 30000.00, NULL, 0, 4, 2, 25, 10, 0, '2026-09-04 07:11:25', 'active', 'approved', '797456 9831', '797456 9831', 11, '', 'admin'),
(48, 'Roshan Resort', '<div>*1.LOCATION* NEAR BY&nbsp; Adarsh colony Gandhi nagar</div><div>*2. BIG/KIDS PLAY AREA* =YES</div><div>*3. SWING &amp; SLIDS /PLAY ZONE FOR KIDS* = Yes</div><div>*4.TOTAL ROOMS-* =&nbsp; 23 ROOM ( 3 Hall )</div><div>*5.MAXIMUM GUEST-* =70&nbsp;</div><div>*6.EXTRA PERSON ADDITIONAL CHARGE* =500&nbsp;</div><div>*7.BEAUTIFULL GARDEN* =Garden&nbsp;</div><div>*8. PARTY/ LAWN AREA* = Yes</div><div>*9. PARTY FRIENDLY*= Yes</div><div>*10. SUSPICIOUS PARKING* = Yes</div><div><br></div>', 'BHOPAL', 'M-2, Adarsh Colony, Gandhi Nagar, Bhopal, Madhya Pradesh 462030', NULL, 'Resort', 50000.00, 2000.00, 1, 23, 2, 20, 70, 0, '2026-09-04 07:15:34', 'active', 'approved', '82694 13815', '82694 13815', 11, '', 'owner'),
(49, 'Gangotri Green', '<div>1. Near Trans Ganga City, Kanpur&nbsp;</div><div>2.&nbsp;<span style=\"font-size: 0.875rem;\">TOTAL ROOMS-&nbsp;</span>4 room 1 drawing room&nbsp;</div><div>3. MAXIMUM GUEST-35 MEMBERS</div><div>4. EXTRA PERSON ADDITIONAL CHARGE</div><div>5. BEAUTYFULL &amp;LUXURY POOLSIDE PROPERTY</div><div>6.BEAUTIFULL GARDEN</div><div>7. LUXURY LIVING AREA</div><div>8.FOOD FACILITY ( LIMITED TO MENU )</div><div>9. 24/7 SECURITY</div><div>10. PARTY/ LAWN AREA</div><div>11. PARTY FRIENDLY</div><div>12. SUSPICIOUS PARKING</div><div>13. OUTSAID FOOD ALLOWED</div><div>14. BIG SEIMMING POOL</div>', 'KANPUR', 'Near Trans Ganga City, Kanpur', NULL, 'Farmhouse', 25000.00, NULL, 0, 4, 2, 35, 35, 0, '2026-09-04 07:33:43', 'active', 'approved', '9305966146', '9305966146', 11, '', 'admin'),
(50, 'Gruham Farms & Resorts', '<div>*1.LOCATION* NEAR BY =&nbsp;<span style=\"color: rgb(31, 31, 31); font-family: Arial, sans-serif; font-weight: 400; font-size: 0.875rem;\">Patwari Halka, GARM</span></div><div>*2. PEACE FULL MOUNTAIN VIEW/HILL STATION VIEW/RIVER/LAKE VIEW* = Yes</div><div>*3. SWING &amp; SLIDS /PLAY ZONE FOR KIDS* =Yes</div><div>*4.TOTAL ROOMS-* = 7</div><div>*5.PER ROOM CHARGE-* = ( 3 Room up to 12 Members 27k )</div><div>*6. BEAUTYFULL/LUXURY POOLSIDE PROPERTY* =Yes</div><div>*7.BEAUTIFULL GARDEN* =Yes</div><div>*8. LUXURY LIVING AREA* =Yes</div><div>*9.MODERN KICHEN THERE YOU CAN MAKE YOUR FOOD* = Yes</div><div>*10. PARTY/ LAWN AREA* = Yes</div><div>*11. PARTY FRIENDLY* =Yes</div><div>*12. SUSPICIOUS PARKING* = Yes</div><div>*13. OUTSAID FOOD ALLOWED* = Yes</div>', 'INDORE', 'No 10, Patwari Halka, Gram, Panod, Chimli, Madhya Pradesh 453551', NULL, 'Resort', 50000.00, NULL, 0, 7, 2, 2, 5, 0, '2026-09-04 08:33:29', 'active', 'approved', '8878790040', '8878790040', 11, '', 'owner'),
(51, 'MG villas', '<div>1.LOCATION NEAR BY&nbsp; = jk hospital k pass Kolar&nbsp;</div><div>2.TOTAL ROOMS= 10</div><div>3.MAXIMUM GUEST= 20</div><div>4.EXTRA PERSON ADDITIONAL CHARGE =500</div><div>5.MODERN KICHEN THERE YOU CAN MAKE YOUR FOOD = Yes</div><div>6. OUTSAID FOOD ALLOWED = Yes</div>', 'Bhopal', 'MG vilaasc  k hospital k pass Kolar ,bhopal', NULL, 'Villa', 100000.00, 2000.00, 1, 10, 2, 2, 10, 0, '2026-09-04 09:00:53', 'active', 'approved', '9244349398', '9244349398', 11, '', 'owner'),
(52, 'Jaipur Angel Hills Resort', '<div>*1.LOCATION* NEAR BY =&nbsp;Seengwana Amer Jaipur&nbsp;</div><div>*3. SWING &amp; SLIDS /PLAY ZONE FOR KIDS* = Yes</div><div>*4.TOTAL ROOMS-* = 4</div><div>*11.MODERN KICHEN THERE YOU CAN MAKE YOUR FOOD* =Yes&nbsp;</div><div>*15. SUSPICIOUS PARKING* =Yes</div><div>*16. OUTSAID FOOD ALLOWED* = Yes</div>', 'Rajasthan', 'Angel hill resorts, Seengwana, Bheempura, Rajasthan 303805', NULL, 'Resort', 20000.00, 1799.00, 1, 4, 2, 25, 15, 0, '2026-09-04 09:23:27', 'active', 'approved', '9024109982', '9024109982', 11, '', 'owner'),
(53, 'DAKKHAN VILLA', '<div><br></div><div>1.NEAR BY - Wadi ratnagiri, Jotiba Dongar, Kolhapur</div><div>2 .MOBLE NUMBER- 9175735353 , 9028412626</div><div>3.TOTAL ROOMS- 6&nbsp;</div><div>4.EXTRA PERSON&nbsp; - 200</div><div>5.SWIMMING POOL&nbsp;</div><div>6.GARDEN&nbsp;</div><div>7.FOOD FACILITY&nbsp; ( YES,NO) YES</div><div>8.KITCHEN (YES,NO)- YES</div><div>9.MUSIC/DJ (YES,NO)- YES</div><div>10.PARKING (YES,NO)- YES</div><div>11.AC ROOM (YES,NO)- NO</div><div>12.ALCOHOL ALLOWED (YES,NO)- YES</div><div>13WI-FI (YES,NO)- NO</div><div>14.SMONKING ( YES,NO)- YES</div><div>15.NON VEG ( YES ,NO)- YES</div><div>16.OUTSAID FOOD ( YES,NO)- YES</div>', 'KOLHAPUR', 'Wadi ratnagiri, Jotiba Dongar, Kolhapur', NULL, 'Villa', 7000.00, 1500.00, 1, 6, 2, 150, 30, 0, '2026-09-05 06:20:37', 'active', 'approved', '9175735353', '9028412626', 11, '', 'admin'),
(54, 'Villa Riverine Cafe & Riverview Residences', '<div>*1.LOCATION* NEAR BY&nbsp; =Ower Vashisth Rohtang Rd Near SASE Helipad</div><div>*2. PEACE FULL MOUNTAIN VIEW/HILL STATION VIEW/RIVER/LAKE VIEW* =&nbsp; Yes&nbsp;</div><div>*3. RESTURANT/CAFE FOR GOOD FOOD* = Yes</div><div>*4.TOTAL ROOMS-* = 12</div><div>*9.BEAUTIFULL GARDEN* = Yes&nbsp;</div><div>*11.MODERN KICHEN THERE YOU CAN MAKE YOUR FOOD* = Yes&nbsp;</div><div>*13. PARTY/ LAWN AREA* =&nbsp; Yes&nbsp;</div><div>*14. PARTY FRIENDLY* =Yes</div><div>*15. SUSPICIOUS PARKING*&nbsp; = Yes&nbsp;</div><div>*16. OUTSAID FOOD ALLOWED* = Yes</div>', 'Manali, Himachal Pradesh', 'Vashisth, Rohtang Road, Near Sase Helipad, Manali, Himachal Pradesh 175103', NULL, 'Villa', 15000.00, NULL, 0, 12, 2, 2, 5, 0, '2026-09-05 06:29:58', 'active', 'approved', '9871001007', '98711 49966', 11, '', 'owner'),
(55, 'SEA BIRD RESORT', '<div>*1.LOCATION* NEAR BY = Tajpur Sea Beach Road East Medinipur (WB)&nbsp;<span style=\"font-size: 0.875rem;\">&nbsp;</span></div><div>*2.TOTAL ROOMS-* =&nbsp;</div><div>*3.MAXIMUM GUEST-* = 30&nbsp;</div><div>*4.EXTRA PERSON ADDITIONAL CHARGE*= 300&nbsp;</div><div>*5. BEAUTYFULL/LUXURY POOLSIDE PROPERTY*= Yes&nbsp;</div><div>*6.BEAUTIFULL GARDEN* =Yes&nbsp;</div><div>*7.MODERN KICHEN THERE YOU CAN MAKE YOUR FOOD* = Yes&nbsp;</div><div>*8. PARTY/ LAWN AREA* = Yes&nbsp;</div><div>*9. PARTY FRIENDLY* = Yes&nbsp;</div><div>*10. SUSPICIOUS PARKING* = Yes&nbsp;</div><div>*11. OUTSAID FOOD ALLOWED* = No&nbsp;</div>', 'West Bengal', 'Balisai- Tajpur Sea Beach Rd, Tajpur, West Bengal 721423', NULL, 'Resort', 15500.00, NULL, 0, 0, 2, 2, 5, 0, '2026-09-05 06:49:11', 'active', 'approved', '9875304349', '9875304349', 11, '', 'owner'),
(56, 'HIRAN GIR RETRATE', '<ol><li>1.NEAR BY - CHITRAVAD , TALALA GIR SOMNATH , GUJARAT&nbsp;</li><li>2.</li></ol>', 'GUJARAT , 362150', 'CHITRAVAD , TALALA GIR SOMNATH , GUJARAT 362150', NULL, 'Villa', 35000.00, 6000.00, 1, 5, 2, 25, 10, 0, '2026-09-05 07:00:08', 'active', 'approved', '7096555006', '7096555006', 11, '', 'admin'),
(57, 'CRAZY NATURAL RESORT', '<div>1. 1 KM NEAR FROM AIRPORT</div><div>2.&nbsp;<span style=\"font-size: 0.875rem;\">RESTURANT/CAFE FOR GOOD FOOD</span></div><div>3. BIG/KIDS PLAY AREA</div><div>4.TOTAL ROOMS- 10</div><div>5.PER ROOM CHARGE- 3000</div><div>6.MAXIMUM GUEST-100</div><div>7.EXTRA PERSON ADDITIONAL CHARGE - 500</div><div>8. BEAUTYFULL/LUXURY POOLSIDE PROPERTY</div><div>9.BEAUTIFULL GARDEN</div><div>10. LUXURY LIVING AREA</div><div>11. PARTY/ LAWN AREA</div><div>12. PARTY FRIENDLY</div><div>13. SUSPICIOUS PARKING</div><div>14. OUTSAID FOOD ALLOWED</div>', 'INDORE , MADHYA PRADESH', 'Airport bijsan mandir se 1 km  piche dilip nagar wine shop Ke paas', NULL, 'Resort', 111000.00, 3000.00, 1, 9, 2, 100, 25, 0, '2026-09-05 08:00:04', 'active', 'approved', '8224944444', '8224944444', 11, '', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `farmhouse_amenities`
--

CREATE TABLE `farmhouse_amenities` (
  `id` int(11) NOT NULL,
  `farmhouse_id` int(11) DEFAULT NULL,
  `amenity_id` int(11) DEFAULT NULL,
  `bedroom_number` int(11) DEFAULT NULL COMMENT 'Bedroom number (1,2,3...) or NULL for general/kitchen amenity'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farmhouse_amenities`
--

INSERT INTO `farmhouse_amenities` (`id`, `farmhouse_id`, `amenity_id`, `bedroom_number`) VALUES
(7, 4, 5, NULL),
(9, 6, 2, NULL),
(16, 13, 4, NULL),
(17, 14, 5, NULL),
(18, 15, 1, NULL),
(19, 16, 2, NULL),
(20, 17, 3, NULL),
(450, 37, 7, NULL),
(451, 37, 17, NULL),
(452, 37, 18, NULL),
(453, 37, 16, NULL),
(454, 37, 15, NULL),
(455, 37, 14, NULL),
(456, 37, 13, NULL),
(457, 37, 5, NULL),
(483, 36, 7, NULL),
(484, 36, 17, NULL),
(485, 36, 18, NULL),
(486, 36, 16, NULL),
(487, 36, 15, NULL),
(488, 36, 12, NULL),
(489, 36, 2, NULL),
(490, 36, 6, NULL),
(491, 36, 3, NULL),
(492, 36, 13, NULL),
(493, 36, 1, NULL),
(494, 36, 4, NULL),
(495, 36, 5, NULL),
(496, 36, 22, NULL),
(497, 36, 20, NULL),
(498, 36, 21, NULL),
(510, 38, 7, NULL),
(511, 38, 17, NULL),
(512, 38, 18, NULL),
(513, 38, 16, NULL),
(514, 38, 15, NULL),
(515, 38, 2, NULL),
(516, 38, 14, NULL),
(517, 38, 13, NULL),
(518, 38, 4, NULL),
(519, 38, 5, NULL),
(520, 38, 20, NULL),
(521, 38, 21, NULL),
(522, 39, 7, NULL),
(523, 39, 17, NULL),
(524, 39, 18, NULL),
(525, 39, 16, NULL),
(526, 39, 15, NULL),
(527, 39, 2, NULL),
(528, 39, 6, NULL),
(529, 39, 14, NULL),
(530, 39, 1, NULL),
(531, 39, 4, NULL),
(532, 39, 5, NULL),
(533, 39, 22, NULL),
(534, 39, 20, NULL),
(535, 39, 21, NULL),
(583, 34, 7, NULL),
(584, 34, 8, NULL),
(585, 34, 14, NULL),
(586, 34, 22, NULL),
(631, 41, 7, NULL),
(632, 41, 17, NULL),
(633, 41, 18, NULL),
(634, 41, 16, NULL),
(635, 41, 15, NULL),
(636, 41, 8, NULL),
(637, 41, 2, NULL),
(638, 41, 6, NULL),
(639, 41, 14, NULL),
(640, 41, 1, NULL),
(641, 41, 4, NULL),
(642, 41, 5, NULL),
(651, 45, 17, NULL),
(652, 45, 18, NULL),
(653, 45, 16, NULL),
(654, 45, 15, NULL),
(655, 45, 2, NULL),
(656, 45, 6, NULL),
(657, 45, 4, NULL),
(658, 45, 22, NULL),
(659, 43, 17, NULL),
(660, 43, 18, NULL),
(661, 43, 16, NULL),
(662, 43, 15, NULL),
(663, 43, 2, NULL),
(664, 43, 3, NULL),
(665, 43, 4, NULL),
(666, 43, 5, NULL),
(667, 43, 22, NULL),
(746, 48, 7, NULL),
(747, 48, 17, NULL),
(748, 48, 18, NULL),
(749, 48, 16, NULL),
(750, 48, 15, NULL),
(751, 48, 2, NULL),
(752, 48, 6, NULL),
(753, 48, 3, NULL),
(754, 48, 1, NULL),
(755, 48, 4, NULL),
(756, 48, 5, NULL),
(757, 48, 22, NULL),
(758, 49, 7, NULL),
(759, 49, 17, NULL),
(760, 49, 18, NULL),
(761, 49, 15, NULL),
(762, 49, 2, NULL),
(763, 49, 14, NULL),
(764, 49, 1, NULL),
(765, 49, 4, NULL),
(766, 49, 5, NULL),
(774, 42, 7, NULL),
(775, 42, 17, NULL),
(776, 42, 18, NULL),
(777, 42, 16, NULL),
(778, 42, 15, NULL),
(779, 42, 2, NULL),
(780, 42, 14, NULL),
(793, 46, 7, NULL),
(794, 46, 17, NULL),
(795, 46, 18, NULL),
(796, 46, 16, NULL),
(797, 46, 15, NULL),
(798, 46, 2, NULL),
(799, 46, 6, NULL),
(800, 46, 14, NULL),
(801, 46, 1, NULL),
(802, 46, 5, NULL),
(803, 46, 22, NULL),
(804, 46, 20, NULL),
(805, 46, 21, NULL),
(860, 47, 7, NULL),
(861, 47, 17, NULL),
(862, 47, 18, NULL),
(863, 47, 15, NULL),
(864, 47, 2, NULL),
(865, 47, 6, NULL),
(866, 47, 14, NULL),
(867, 47, 13, NULL),
(868, 47, 4, NULL),
(869, 47, 5, NULL),
(881, 51, 7, NULL),
(882, 51, 17, NULL),
(883, 51, 18, NULL),
(884, 51, 16, NULL),
(885, 51, 15, NULL),
(886, 51, 2, NULL),
(887, 51, 6, NULL),
(888, 51, 3, NULL),
(889, 51, 4, NULL),
(890, 51, 5, NULL),
(891, 50, 7, NULL),
(892, 50, 17, NULL),
(893, 50, 18, NULL),
(894, 50, 16, NULL),
(895, 50, 15, NULL),
(896, 50, 2, NULL),
(897, 50, 6, NULL),
(898, 50, 3, NULL),
(899, 50, 1, NULL),
(900, 50, 4, NULL),
(901, 50, 5, NULL),
(902, 50, 22, NULL),
(903, 52, 7, NULL),
(904, 52, 17, NULL),
(905, 52, 18, NULL),
(906, 52, 16, NULL),
(907, 52, 15, NULL),
(908, 52, 2, NULL),
(909, 52, 6, NULL),
(910, 52, 3, NULL),
(911, 52, 1, NULL),
(912, 52, 4, NULL),
(913, 52, 5, NULL),
(914, 53, 7, NULL),
(915, 53, 17, NULL),
(916, 53, 18, NULL),
(917, 53, 2, NULL),
(918, 53, 14, NULL),
(919, 53, 1, NULL),
(920, 53, 4, NULL),
(921, 53, 5, NULL),
(922, 53, 22, NULL),
(923, 53, 20, NULL),
(924, 53, 21, NULL),
(954, 56, 7, NULL),
(955, 56, 17, NULL),
(956, 56, 18, NULL),
(957, 56, 15, NULL),
(958, 56, 14, NULL),
(959, 55, 7, NULL),
(960, 55, 17, NULL),
(961, 55, 18, NULL),
(962, 55, 16, NULL),
(963, 55, 15, NULL),
(964, 55, 2, NULL),
(965, 55, 6, NULL),
(966, 55, 3, NULL),
(967, 55, 1, NULL),
(968, 55, 4, NULL),
(969, 55, 5, NULL),
(970, 54, 17, NULL),
(971, 54, 18, NULL),
(972, 54, 16, NULL),
(973, 54, 15, NULL),
(974, 54, 2, NULL),
(975, 54, 6, NULL),
(976, 54, 3, NULL),
(977, 54, 4, NULL),
(978, 54, 5, NULL),
(991, 57, 7, NULL),
(992, 57, 17, NULL),
(993, 57, 18, NULL),
(994, 57, 16, NULL),
(995, 57, 15, NULL),
(996, 57, 12, NULL),
(997, 57, 2, NULL),
(998, 57, 6, NULL),
(999, 57, 14, NULL),
(1000, 57, 1, NULL),
(1001, 57, 4, NULL),
(1002, 57, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `farmhouse_rules`
--

CREATE TABLE `farmhouse_rules` (
  `id` int(11) NOT NULL,
  `farmhouse_id` int(11) NOT NULL,
  `rule_name` varchar(100) NOT NULL COMMENT 'e.g. Pets, Non Veg, Alcohol, Smoking',
  `is_allowed` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = allowed (green tick), 0 = not allowed (red cross)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farmhouse_rules`
--

INSERT INTO `farmhouse_rules` (`id`, `farmhouse_id`, `rule_name`, `is_allowed`) VALUES
(370, 37, 'Alcohol', 1),
(371, 37, 'Bachelor Group', 1),
(372, 37, 'DJ / Loud Music', 1),
(373, 37, 'Networking', 1),
(374, 37, 'Non Veg', 1),
(375, 37, 'Outside Food', 1),
(376, 37, 'Pets', 1),
(377, 37, 'Smoking', 1),
(378, 37, 'VIDEOS', 1),
(469, 36, 'Alcohol', 1),
(470, 36, 'Bachelor Group', 1),
(471, 36, 'DJ / Loud Music', 1),
(472, 36, 'Networking', 1),
(473, 36, 'Non Veg', 1),
(474, 36, 'Outside Food', 1),
(475, 36, 'Pets', 1),
(476, 36, 'Smoking', 1),
(477, 36, 'VIDEOS', 1),
(487, 38, 'Alcohol', 1),
(488, 38, 'Bachelor Group', 1),
(489, 38, 'DJ / Loud Music', 0),
(490, 38, 'Networking', 1),
(491, 38, 'Non Veg', 1),
(492, 38, 'Outside Food', 1),
(493, 38, 'Pets', 0),
(494, 38, 'Smoking', 1),
(495, 38, 'VIDEOS', 1),
(505, 39, 'Alcohol', 1),
(506, 39, 'Bachelor Group', 1),
(507, 39, 'DJ / Loud Music', 1),
(508, 39, 'Networking', 1),
(509, 39, 'Non Veg', 1),
(510, 39, 'Outside Food', 1),
(511, 39, 'Pets', 0),
(512, 39, 'Smoking', 1),
(513, 39, 'VIDEOS', 1),
(523, 40, 'Alcohol', 1),
(524, 40, 'Bachelor Group', 1),
(525, 40, 'DJ / Loud Music', 1),
(526, 40, 'Networking', 1),
(527, 40, 'Non Veg', 1),
(528, 40, 'Outside Food', 1),
(529, 40, 'Pets', 1),
(530, 40, 'Smoking', 1),
(531, 40, 'VIDEOS', 1),
(586, 34, 'Alcohol', 0),
(587, 34, 'Bachelor Group', 1),
(588, 34, 'DJ / Loud Music', 1),
(589, 34, 'Networking', 1),
(590, 34, 'Non Veg', 1),
(591, 34, 'Outside Food', 1),
(592, 34, 'Pets', 1),
(593, 34, 'Smoking', 1),
(594, 34, 'VIDEOS', 1),
(631, 44, 'Alcohol', 1),
(632, 44, 'Bachelor Group', 1),
(633, 44, 'DJ / Loud Music', 1),
(634, 44, 'Networking', 1),
(635, 44, 'Non Veg', 1),
(636, 44, 'Outside Food', 1),
(637, 44, 'Pets', 1),
(638, 44, 'Smoking', 1),
(639, 44, 'VIDEOS', 1),
(658, 41, 'Alcohol', 1),
(659, 41, 'Bachelor Group', 1),
(660, 41, 'DJ / Loud Music', 1),
(661, 41, 'Networking', 1),
(662, 41, 'Non Veg', 1),
(663, 41, 'Outside Food', 1),
(664, 41, 'Pets', 1),
(665, 41, 'Smoking', 1),
(666, 41, 'VIDEOS', 1),
(676, 45, 'Alcohol', 1),
(677, 45, 'Bachelor Group', 1),
(678, 45, 'DJ / Loud Music', 1),
(679, 45, 'Networking', 1),
(680, 45, 'Non Veg', 1),
(681, 45, 'Outside Food', 1),
(682, 45, 'Pets', 0),
(683, 45, 'Smoking', 1),
(684, 45, 'VIDEOS', 1),
(685, 43, 'Alcohol', 0),
(686, 43, 'Bachelor Group', 1),
(687, 43, 'DJ / Loud Music', 1),
(688, 43, 'Networking', 1),
(689, 43, 'Non Veg', 0),
(690, 43, 'Outside Food', 0),
(691, 43, 'Pets', 0),
(692, 43, 'Smoking', 0),
(693, 43, 'VIDEOS', 1),
(757, 48, 'Alcohol', 1),
(758, 48, 'Bachelor Group', 1),
(759, 48, 'DJ / Loud Music', 1),
(760, 48, 'Networking', 1),
(761, 48, 'Non Veg', 1),
(762, 48, 'Outside Food', 1),
(763, 48, 'Pets', 0),
(764, 48, 'Smoking', 1),
(765, 48, 'VIDEOS', 1),
(766, 49, 'Alcohol', 0),
(767, 49, 'Bachelor Group', 1),
(768, 49, 'DJ / Loud Music', 1),
(769, 49, 'Networking', 1),
(770, 49, 'Non Veg', 0),
(771, 49, 'Outside Food', 1),
(772, 49, 'Pets', 0),
(773, 49, 'Smoking', 0),
(774, 49, 'VIDEOS', 1),
(784, 42, 'Alcohol', 1),
(785, 42, 'Bachelor Group', 1),
(786, 42, 'DJ / Loud Music', 1),
(787, 42, 'Networking', 1),
(788, 42, 'Non Veg', 0),
(789, 42, 'Outside Food', 0),
(790, 42, 'Pets', 0),
(791, 42, 'Smoking', 1),
(792, 42, 'VIDEOS', 1),
(802, 46, 'Alcohol', 1),
(803, 46, 'Bachelor Group', 1),
(804, 46, 'DJ / Loud Music', 1),
(805, 46, 'Networking', 1),
(806, 46, 'Non Veg', 1),
(807, 46, 'Outside Food', 1),
(808, 46, 'Pets', 1),
(809, 46, 'Smoking', 1),
(810, 46, 'VIDEOS', 1),
(856, 47, 'Alcohol', 1),
(857, 47, 'Bachelor Group', 1),
(858, 47, 'DJ / Loud Music', 1),
(859, 47, 'Networking', 1),
(860, 47, 'Non Veg', 1),
(861, 47, 'Outside Food', 1),
(862, 47, 'Pets', 1),
(863, 47, 'Smoking', 1),
(864, 47, 'VIDEOS', 1),
(874, 51, 'Alcohol', 1),
(875, 51, 'Bachelor Group', 1),
(876, 51, 'DJ / Loud Music', 1),
(877, 51, 'Networking', 1),
(878, 51, 'Non Veg', 1),
(879, 51, 'Outside Food', 1),
(880, 51, 'Pets', 1),
(881, 51, 'Smoking', 1),
(882, 51, 'VIDEOS', 1),
(883, 50, 'Alcohol', 1),
(884, 50, 'Bachelor Group', 1),
(885, 50, 'DJ / Loud Music', 1),
(886, 50, 'Networking', 1),
(887, 50, 'Non Veg', 1),
(888, 50, 'Outside Food', 1),
(889, 50, 'Pets', 1),
(890, 50, 'Smoking', 1),
(891, 50, 'VIDEOS', 1),
(892, 52, 'Alcohol', 1),
(893, 52, 'Bachelor Group', 1),
(894, 52, 'DJ / Loud Music', 1),
(895, 52, 'Networking', 1),
(896, 52, 'Non Veg', 1),
(897, 52, 'Outside Food', 1),
(898, 52, 'Pets', 1),
(899, 52, 'Smoking', 1),
(900, 52, 'VIDEOS', 1),
(901, 53, 'Alcohol', 1),
(902, 53, 'Bachelor Group', 1),
(903, 53, 'DJ / Loud Music', 1),
(904, 53, 'Networking', 1),
(905, 53, 'Non Veg', 1),
(906, 53, 'Outside Food', 1),
(907, 53, 'Pets', 0),
(908, 53, 'Smoking', 1),
(909, 53, 'VIDEOS', 1),
(937, 56, 'Alcohol', 1),
(938, 56, 'Bachelor Group', 1),
(939, 56, 'DJ / Loud Music', 1),
(940, 56, 'Networking', 1),
(941, 56, 'Non Veg', 1),
(942, 56, 'Outside Food', 1),
(943, 56, 'Pets', 1),
(944, 56, 'Smoking', 1),
(945, 56, 'VIDEOS', 1),
(946, 55, 'Alcohol', 1),
(947, 55, 'Bachelor Group', 1),
(948, 55, 'DJ / Loud Music', 1),
(949, 55, 'Networking', 1),
(950, 55, 'Non Veg', 1),
(951, 55, 'Outside Food', 1),
(952, 55, 'Pets', 1),
(953, 55, 'Smoking', 1),
(954, 55, 'VIDEOS', 1),
(955, 54, 'Alcohol', 1),
(956, 54, 'Bachelor Group', 1),
(957, 54, 'DJ / Loud Music', 1),
(958, 54, 'Networking', 1),
(959, 54, 'Non Veg', 1),
(960, 54, 'Outside Food', 1),
(961, 54, 'Pets', 1),
(962, 54, 'Smoking', 1),
(963, 54, 'VIDEOS', 1),
(973, 57, 'Alcohol', 1),
(974, 57, 'Bachelor Group', 1),
(975, 57, 'DJ / Loud Music', 1),
(976, 57, 'Networking', 1),
(977, 57, 'Non Veg', 1),
(978, 57, 'Outside Food', 1),
(979, 57, 'Pets', 1),
(980, 57, 'Smoking', 1),
(981, 57, 'VIDEOS', 1);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `farmhouse_id` int(11) DEFAULT NULL,
  `image_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `farmhouse_id`, `image_url`) VALUES
(4, 4, 'img4.jpg'),
(6, 6, 'img6.jpg'),
(13, 13, 'img13.jpg'),
(14, 14, 'img14.jpg'),
(15, 15, 'img15.jpg'),
(16, 16, 'img16.jpg'),
(17, 17, 'img17.jpg'),
(18, 18, 'img18.jpg'),
(20, 20, 'img20.jpg'),
(37, 36, 'farm_6a86d58d0ec9a1787221389.jpeg'),
(38, 36, 'farm_6a86d58d0eecc1787221389.jpeg'),
(39, 36, 'farm_6a86d58d0f0b31787221389.jpeg'),
(40, 36, 'farm_6a86d58d0f24d1787221389.jpeg'),
(43, 37, 'farmhouse_6a9550be6a2751788170430.jpeg'),
(47, 38, 'farmhouse_6a97fe728cc341788345970.jpeg'),
(48, 39, 'farmhouse_6a980544e6b051788347716.jpeg'),
(49, 40, 'farmhouse_6a980d475fdaf1788349767.jpeg'),
(50, 40, 'farmhouse_6a980d475ffca1788349767.jpeg'),
(51, 40, 'farmhouse_6a980d47601111788349767.jpeg'),
(52, 40, 'farmhouse_6a980d476025b1788349767.jpeg'),
(53, 40, 'farmhouse_6a980d47603801788349767.jpeg'),
(54, 40, 'farmhouse_6a980d47604c91788349767.jpeg'),
(55, 40, 'farmhouse_6a980d476060b1788349767.jpeg'),
(56, 40, 'farmhouse_6a980d476076b1788349767.jpeg'),
(57, 40, 'farmhouse_6a980d47609251788349767.jpeg'),
(58, 40, 'farmhouse_6a980d4760aa81788349767.jpeg'),
(59, 40, 'farmhouse_6a980d4760c171788349767.jpeg'),
(60, 40, 'farmhouse_6a980d4760d641788349767.jpeg'),
(61, 40, 'farmhouse_6a980d4760eb31788349767.jpeg'),
(62, 40, 'farmhouse_6a980d476101d1788349767.jpeg'),
(63, 40, 'farmhouse_6a980d47611921788349767.jpeg'),
(64, 40, 'farmhouse_6a980d47612fb1788349767.jpeg'),
(65, 40, 'farmhouse_6a980d476145f1788349767.jpeg'),
(66, 40, 'farmhouse_6a980d47616031788349767.jpeg'),
(67, 40, 'farmhouse_6a980d47617a31788349767.jpeg'),
(68, 40, 'farmhouse_6a980d47618ea1788349767.jpeg'),
(69, 41, 'farmhouse_6a9962185da9b1788437016.jpeg'),
(70, 41, 'farmhouse_6a9962185dd4a1788437016.jpeg'),
(71, 41, 'farmhouse_6a9962185df451788437016.jpeg'),
(72, 41, 'farmhouse_6a9962185e1831788437016.jpeg'),
(73, 41, 'farmhouse_6a9962185e33b1788437016.jpeg'),
(74, 41, 'farmhouse_6a9962185e4c21788437016.jpeg'),
(75, 41, 'farmhouse_6a9962185e6cf1788437016.jpeg'),
(81, 42, 'farmhouse_6a9a56ab2b62a1788499627.jpeg'),
(82, 42, 'farmhouse_6a9a56ab2ce051788499627.jpeg'),
(83, 42, 'farmhouse_6a9a56ab2deb71788499627.jpeg'),
(84, 42, 'farmhouse_6a9a56ab2f1f31788499627.jpeg'),
(85, 42, 'farmhouse_6a9a56ab304eb1788499627.jpeg'),
(86, 42, 'farmhouse_6a9a56ab312e91788499627.jpeg'),
(87, 43, 'farmhouse_6a9a626c66b821788502636.jpeg'),
(88, 43, 'farmhouse_6a9a626c66ef71788502636.jpeg'),
(89, 43, 'farmhouse_6a9a626c671cf1788502636.jpeg'),
(90, 43, 'farmhouse_6a9a626c6732f1788502636.jpeg'),
(91, 43, 'farmhouse_6a9a626c674bd1788502636.jpeg'),
(92, 43, 'farmhouse_6a9a626c676201788502636.jpeg'),
(93, 43, 'farmhouse_6a9a626c678a31788502636.jpeg'),
(94, 43, 'farmhouse_6a9a626c67b901788502636.jpeg'),
(95, 44, 'farmhouse_6a9a640383aa31788503043.jpeg'),
(96, 44, 'farmhouse_6a9a640383d751788503043.jpeg'),
(97, 44, 'farmhouse_6a9a6403840781788503043.jpeg'),
(98, 44, 'farmhouse_6a9a64038426c1788503043.jpeg'),
(99, 44, 'farmhouse_6a9a6403844a91788503043.jpeg'),
(100, 44, 'farmhouse_6a9a6403847091788503043.jpeg'),
(101, 44, 'farmhouse_6a9a6403849631788503043.jpeg'),
(102, 44, 'farmhouse_6a9a640384bdf1788503043.jpeg'),
(103, 44, 'farmhouse_6a9a640384d831788503043.jpeg'),
(104, 44, 'farmhouse_6a9a640384f041788503043.jpeg'),
(105, 45, 'farmhouse_6a9a6847f42331788504135.jpeg'),
(106, 45, 'farmhouse_6a9a6848001f01788504136.jpeg'),
(107, 45, 'farmhouse_6a9a6848003711788504136.jpeg'),
(108, 45, 'farmhouse_6a9a6848004d31788504136.jpeg'),
(109, 45, 'farmhouse_6a9a68480062c1788504136.jpeg'),
(110, 45, 'farmhouse_6a9a6848008171788504136.jpeg'),
(111, 45, 'farmhouse_6a9a684800a321788504136.jpeg'),
(114, 46, 'farmhouse_6a9a6a4d419291788504653.jpeg'),
(115, 46, 'farmhouse_6a9a6a4d41b001788504653.jpeg'),
(116, 46, 'farmhouse_6a9a6a4d41ce71788504653.jpeg'),
(117, 46, 'farmhouse_6a9a6a4d41f4a1788504653.jpeg'),
(118, 46, 'farmhouse_6a9a6a4d421f11788504653.jpeg'),
(119, 46, 'farmhouse_6a9a6a4d4243d1788504653.jpeg'),
(120, 46, 'farmhouse_6a9a6a4d4260d1788504653.jpeg'),
(121, 46, 'farmhouse_6a9a6a4d4278e1788504653.jpeg'),
(122, 46, 'farmhouse_6a9a6a4d429f61788504653.jpeg'),
(123, 46, 'farmhouse_6a9a6a4d42c641788504653.jpeg'),
(124, 46, 'farmhouse_6a9a6a4d42e101788504653.jpeg'),
(125, 46, 'farmhouse_6a9a6a4d42fc51788504653.jpeg'),
(126, 46, 'farmhouse_6a9a6a4d4316c1788504653.jpeg'),
(127, 46, 'farmhouse_6a9a6a4d433311788504653.jpeg'),
(128, 46, 'farmhouse_6a9a6a4d4351c1788504653.jpeg'),
(129, 46, 'farmhouse_6a9a6a4d437bd1788504653.jpeg'),
(130, 46, 'farmhouse_6a9a6a4d439ad1788504653.jpeg'),
(131, 46, 'farmhouse_6a9a6a4d43bb21788504653.jpeg'),
(133, 47, 'farmhouse_6a9a6f1de42f31788505885.jpeg'),
(134, 47, 'farmhouse_6a9a6f1de44e31788505885.jpeg'),
(135, 47, 'farmhouse_6a9a6f1de47401788505885.jpeg'),
(136, 47, 'farmhouse_6a9a6f1de49b61788505885.jpeg'),
(137, 47, 'farmhouse_6a9a6f1de4beb1788505885.jpeg'),
(138, 47, 'farmhouse_6a9a6f1de4eed1788505885.jpeg'),
(139, 48, 'farmhouse_6a9a7016a49701788506134.jpeg'),
(140, 49, 'farmhouse_6a9a74573d7471788507223.jpeg'),
(141, 49, 'farmhouse_6a9a74573dab01788507223.jpeg'),
(142, 49, 'farmhouse_6a9a74573dce71788507223.jpeg'),
(143, 49, 'farmhouse_6a9a74573decb1788507223.jpeg'),
(144, 49, 'farmhouse_6a9a74573e0c01788507223.jpeg'),
(145, 49, 'farmhouse_6a9a74573e3261788507223.jpeg'),
(146, 50, 'farmhouse_6a9a8259aa9441788510809.jpeg'),
(147, 50, 'farmhouse_6a9a8259aab931788510809.jpeg'),
(151, 51, 'farmhouse_6a9a88c5ba28e1788512453.jpeg'),
(152, 51, 'farmhouse_6a9a88c5ba4661788512453.jpeg'),
(154, 52, 'farmhouse_6a9a8e0f9a5461788513807.jpeg'),
(155, 52, 'farmhouse_6a9a8e0f9a97c1788513807.jpeg'),
(156, 52, 'farmhouse_6a9a8e0f9ab571788513807.jpeg'),
(157, 52, 'farmhouse_6a9a8e0f9ad781788513807.jpeg'),
(158, 52, 'farmhouse_6a9a8e0f9b1111788513807.jpeg'),
(159, 52, 'farmhouse_6a9a8e0f9b8521788513807.jpeg'),
(160, 52, 'farmhouse_6a9a8e0f9b9df1788513807.jpeg'),
(161, 51, 'farmhouse_6a9badd0dd4411788587472.jpeg'),
(162, 53, 'farmhouse_6a9bb4b5dcb7a1788589237.jpeg'),
(163, 53, 'farmhouse_6a9bb4b5e126b1788589237.jpeg'),
(164, 53, 'farmhouse_6a9bb4b5e4ce41788589237.jpeg'),
(165, 53, 'farmhouse_6a9bb4b5e890a1788589237.jpeg'),
(166, 53, 'farmhouse_6a9bb4b5ea8711788589237.jpeg'),
(167, 53, 'farmhouse_6a9bb4b5ec23f1788589237.jpeg'),
(168, 53, 'farmhouse_6a9bb4b5ee02a1788589237.jpeg'),
(169, 53, 'farmhouse_6a9bb4b5ee79a1788589237.jpeg'),
(170, 54, 'farmhouse_6a9bb6e63dc4a1788589798.jpeg'),
(171, 54, 'farmhouse_6a9bb6e63de771788589798.jpeg'),
(172, 54, 'farmhouse_6a9bb6e63e0031788589798.jpeg'),
(173, 54, 'farmhouse_6a9bb6e63e2511788589798.jpeg'),
(174, 54, 'farmhouse_6a9bb6e63e50a1788589798.jpeg'),
(175, 54, 'farmhouse_6a9bb6e63e66f1788589798.jpeg'),
(176, 55, 'farmhouse_6a9bbb675a30d1788590951.jpeg'),
(177, 55, 'farmhouse_6a9bbb675a66a1788590951.jpeg'),
(178, 55, 'farmhouse_6a9bbb675a84e1788590951.jpeg'),
(179, 55, 'farmhouse_6a9bbb675aa0b1788590951.jpeg'),
(180, 55, 'farmhouse_6a9bbb675ab471788590951.jpeg'),
(181, 55, 'farmhouse_6a9bbb675ac9b1788590951.jpeg'),
(182, 55, 'farmhouse_6a9bbb675adc71788590951.jpeg'),
(183, 55, 'farmhouse_6a9bbb675af8a1788590951.jpeg'),
(184, 55, 'farmhouse_6a9bbb675b0df1788590951.jpeg'),
(185, 55, 'farmhouse_6a9bbb675b2751788590951.jpeg'),
(186, 55, 'farmhouse_6a9bbb675b3b01788590951.jpeg'),
(187, 55, 'farmhouse_6a9bbb675b5061788590951.jpeg'),
(188, 55, 'farmhouse_6a9bbb675b6281788590951.jpeg'),
(189, 55, 'farmhouse_6a9bbb675b80d1788590951.jpeg'),
(190, 56, 'farmhouse_6a9bbdf8a56b21788591608.jpg'),
(191, 57, 'farmhouse_6a9bcc04ba79b1788595204.jpg'),
(192, 57, 'farmhouse_6a9bcf02c73681788595970.jpg'),
(193, 57, 'farmhouse_6a9bcf02c76a81788595970.jpg'),
(194, 57, 'farmhouse_6a9bcf02c78a61788595970.jpg'),
(195, 57, 'farmhouse_6a9bcf02c7adc1788595970.jpg'),
(196, 57, 'farmhouse_6a9bcf02c7ce51788595970.jpg'),
(197, 57, 'farmhouse_6a9bcf02c7ec61788595970.jpg'),
(198, 57, 'farmhouse_6a9bcf02c80ae1788595970.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `farmhouse_id` int(11) DEFAULT NULL,
  `type` enum('call','whatsapp') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','contacted','closed') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `user_id`, `farmhouse_id`, `type`, `created_at`, `name`, `phone`, `message`, `status`) VALUES
(4, 8, 4, 'whatsapp', '2026-03-21 13:21:49', NULL, NULL, NULL, 'new'),
(6, 12, 6, 'whatsapp', '2026-03-21 13:21:49', NULL, NULL, NULL, 'new'),
(13, 8, 13, 'call', '2026-03-21 13:21:49', NULL, NULL, NULL, 'contacted'),
(14, 10, 14, 'whatsapp', '2026-03-21 13:21:49', NULL, NULL, NULL, 'contacted'),
(15, 12, 15, 'call', '2026-03-21 13:21:49', NULL, NULL, NULL, 'new'),
(16, 14, 16, 'whatsapp', '2026-03-21 13:21:49', NULL, NULL, NULL, 'new'),
(17, 16, 17, 'call', '2026-03-21 13:21:49', NULL, NULL, NULL, 'new'),
(18, 18, 18, 'whatsapp', '2026-03-21 13:21:49', NULL, NULL, NULL, 'new'),
(20, 4, 20, 'whatsapp', '2026-03-21 13:21:49', NULL, NULL, NULL, 'new');

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` text NOT NULL,
  `is_logged_in` tinyint(1) NOT NULL DEFAULT 0,
  `profile_image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_phone_verified` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = phone verified, 0 = unverified',
  `is_email_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = email verified, 0 = unverified',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `name`, `email`, `phone`, `password`, `status`, `is_logged_in`, `profile_image`, `created_at`, `remember_token`, `token_expiry`, `last_login`, `is_phone_verified`, `is_email_verified`, `reset_token`, `reset_token_expiry`) VALUES
(7, 'bytelabs', 'admin@farmlelo.com', '9111959109', '$2y$10$f5AWm6fxjCO3nrWakxHCBeGNQxegYnso7smvHQ/Oaw.4zD74f7oAm', 'active', 0, '', '2026-05-18 07:51:27', NULL, NULL, NULL, 1, 0, NULL, NULL),
(9, 'owner', 'owner@farmlelo.com', '9340121212', '$2y$10$hhdX8ytscL1.pmBRpcXdTO6UjO9Fnu1v4rybMl4AoOXKGvTtcF4py', 'active', 0, '', '2026-06-09 08:09:52', NULL, NULL, '2026-06-09 08:11:26', 1, 0, NULL, NULL),
(11, 'Farmlelo', 'admin.farmlelo@gmail.com', '8889000399', '$2y$10$FSV38gGr7I7LYqjo4qxOpOVNDx.vm/mh.LZQjz0kV3d0dlXHd4Kra', 'active', 1, '', '2026-08-20 07:54:04', 'ceaa3265c57e2d9b265d408e0e4361320ef93315fd9b843ec2932e9bea1bc14c', '2026-10-03 16:42:43', '2026-09-03 11:12:43', 1, 0, NULL, NULL),
(14, 'TARANGINI HOLIDAY HOME', 'subhashmacha@gmail.com', '9819043703', '$2y$10$JsUcbnSkx.q3uoU0Ik7phuiI85wmbZwrFQUhZPfmCvQeO.92p4C9K', 'active', 0, '', '2026-08-20 10:44:04', NULL, NULL, NULL, 1, 0, NULL, NULL),
(17, 'VANWAS FARM', 'abcd@gmail.com', '9754015070', '$2y$10$b.XUgOJbNS0YmuolCkXI1OcvHxzt2NOdCNWj2fuKi41Dfn8KuTOi.', 'active', 0, '', '2026-08-20 10:48:40', NULL, NULL, NULL, 1, 0, NULL, NULL),
(18, 'Hirendra Chouhan', 'hirendrachouhan.hc@gmail.com', '9111959107', '$2y$10$sh1YpvWiu7h.W49S1dd8dewYCM10UXi.O0Vt4thDHKr8JVU/Gg0me', 'active', 1, '', '2026-08-21 09:26:11', 'db5d1e11aaa9e7677d7df236c8cedaa07104c430e1484867b76e96b426f9dc38', '2026-09-30 11:37:56', '2026-08-31 11:37:56', 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rule_presets`
--

CREATE TABLE `rule_presets` (
  `id` int(11) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `icon_class` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rule_presets`
--

INSERT INTO `rule_presets` (`id`, `rule_name`, `icon_class`) VALUES
(1, 'Pets', 'pets'),
(2, 'Outside Food', 'lunch_dining'),
(3, 'Non Veg', 'restaurant'),
(4, 'Smoking', 'smoking_rooms'),
(6, 'Alcohol', 'no_drinks'),
(7, 'DJ / Loud Music', 'music_note'),
(8, 'Bachelor Group', 'groups'),
(10, 'Networking', 'wifi'),
(11, 'VIDEOS', 'videocam');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `admin_phone` varchar(15) DEFAULT NULL,
  `admin_whatsapp` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `whatsapp_number` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `google_map_link` text DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `instagram_link` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `site_name` varchar(100) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `payment_qr_code` varchar(255) DEFAULT NULL,
  `upi_id` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_name` varchar(150) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `ifsc_code` varchar(30) DEFAULT NULL,
  `payment_instructions` text DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `mobile_number`, `whatsapp_number`, `email`, `address`, `google_map_link`, `facebook_link`, `instagram_link`, `youtube_link`, `twitter_link`, `site_name`, `tagline`, `logo`, `favicon`, `payment_qr_code`, `upi_id`, `bank_name`, `account_name`, `account_number`, `ifsc_code`, `payment_instructions`, `footer_text`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, '9111666415', '9111666415', 'admin.farmlelo@gmail.com', 'Indore, Madhya Pradesh', 'https://maps.google.com', 'https://facebook.com/', 'https://instagram.com/farmleloofficial', 'https://instagram.com/farmleloofficial', '', 'FarmLelo', 'Luxury Farmhouses & Private Villa Escapes', NULL, 'favicon_6a8816379efa3_1787303479.png', NULL, '', '', '', '', '', '', '© 2026 FarmLelo. All rights reserved.', '', '2026-05-04 13:32:38', '2026-09-04 12:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default_profile.png',
  `status` enum('active','blocked') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `is_logged_in` tinyint(1) DEFAULT 0,
  `is_phone_verified` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = phone verified, 0 = unverified',
  `is_email_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = email verified, 0 = unverified',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `gender`, `date_of_birth`, `password`, `profile_image`, `status`, `created_at`, `last_login`, `remember_token`, `token_expiry`, `is_logged_in`, `is_phone_verified`, `is_email_verified`, `reset_token`, `reset_token_expiry`) VALUES
(4, 'Ravi', 'ravi4@gmail.com', '9000000004', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(5, 'Suresh', 'suresh5@gmail.com', '9000000005', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(6, 'Ankit', 'ankit6@gmail.com', '9000000006', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(7, 'Deepak', 'deepak7@gmail.com', '9000000007', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(8, 'Karan', 'karan8@gmail.com', '9000000008', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(9, 'Manish', 'manish9@gmail.com', '9000000009', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(10, 'Rohit', 'rohit10@gmail.com', '9000000010', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(11, 'Ajay', 'ajay11@gmail.com', '9000000011', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(12, 'Pankaj', 'pankaj12@gmail.com', '9000000012', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(13, 'Nitin', 'nitin13@gmail.com', '9000000013', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(14, 'Yash', 'yash14@gmail.com', '9000000014', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(15, 'Arjun', 'arjun15@gmail.com', '9000000015', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(16, 'Mohit', 'mohit16@gmail.com', '9000000016', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(17, 'Sumit', 'sumit17@gmail.com', '9000000017', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(18, 'Vikas', 'vikas18@gmail.com', '9000000018', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(20, 'Admin', 'admin@farmlelo.com', '9000000020', NULL, NULL, 'pass', 'default_profile.png', 'active', '2026-03-21 13:20:54', NULL, NULL, NULL, 0, 1, 0, '8a72e68f47f22518e5ac12b4f29a511c6baa0093f0c15058a62c74c7165696a0', '2026-06-09 14:30:36'),
(21, 'VISHAL MARMAT', 'vishalmarmat1222@gmail.com', '9340121212', NULL, NULL, '$2y$10$pjffMKhlma0a7mim0Nj5IeUmiLGGh8NaxTGwsWPpFZOk83AeAQqqa', 'default_profile.png', 'active', '2026-04-30 13:59:40', '2026-08-10 05:46:22', NULL, NULL, 0, 1, 0, NULL, NULL),
(22, 'test', 'vishalmarmat@gmail.com', '1234567890', NULL, NULL, '$2y$10$fAOyWTTXQJOoNvxMHe0IB.Iu72wpy6vwHEP7S1X63UvsI0fcrzPvm', 'default_profile.png', 'active', '2026-05-06 05:34:49', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(23, 'vishalmarmat1212@gmail.com', 'hirendrachouhan.bytelabs@gmail.com', '0987654321', NULL, NULL, '$2y$10$bjfxtv43OPB6q4vQpUxeNe/MBNuLDtVQGu9iiv4ZqZhR3JmpLJeKW', 'default_profile.png', 'active', '2026-05-06 05:44:01', '2026-06-09 13:25:24', NULL, NULL, 0, 1, 0, NULL, NULL),
(24, 'vishal marmat', 'vishalmarmat2@gmail.com', '9340121212', NULL, NULL, '$2y$10$oSErPgIud4.pl0DJuI1y9.LhYiNiiwamZBZB29BfQZ1AQia8RQl.q', 'default_profile.png', 'active', '2026-05-06 11:59:22', '2026-05-06 11:59:43', NULL, NULL, 0, 1, 0, NULL, NULL),
(25, 'Vishal', 'vishal1212@gmail.com', '9340121212', NULL, NULL, '$2y$10$Z7qlpWdJaZgVW8/1cYn2B.8.UAgkHRxyv5DScTe9WrWIWAT5LHOkC', 'default_profile.png', 'active', '2026-05-06 12:36:26', '2026-05-14 06:52:17', NULL, NULL, 0, 1, 0, NULL, NULL),
(26, 'TEST', 'test2@gmiali.com', '9876543210', NULL, NULL, '$2y$10$QI.QPTEFTVcQSdcUmwZi6u4aRkAHpe06yxB.C7e0SuitjdHG.X1oC', 'default_profile.png', 'active', '2026-05-16 10:48:02', '2026-08-21 12:17:58', 'c82bae56c683f0c4bf0e5511b863bc1b', '2026-08-21 14:59:11', 1, 1, 0, NULL, NULL),
(27, 'test', 'test@gmail.com', '9999999888', NULL, NULL, '$2y$10$EM77Whit2GoRpru0RAs2qOzvQ5l4qXBRURfg9/rM3SJfOfUy6vybG', 'default_profile.png', 'active', '2026-05-19 05:22:08', '2026-05-19 05:22:42', NULL, NULL, 0, 1, 0, NULL, NULL),
(28, 'user@farmlelo.com', 'user@farmlelo.com', '9999988888', NULL, NULL, '$2y$10$mXHSPX9tNFzAVtf6N6r6/u04BDCovJo0lHiW8MWSDQnOWlBAtYYxG', 'user_28_1779178972.png', 'active', '2026-05-19 06:25:43', '2026-06-09 12:57:56', NULL, NULL, 0, 1, 0, NULL, NULL),
(29, 'Ajay', 'ajay@gmail.com', '8889000034', NULL, NULL, '$2y$10$rqYrpDVNw04qiuLRcNGuYe48vXQawuktmCxQvhCD5aAxRkGKKsS5W', 'default_profile.png', 'active', '2026-06-02 12:58:11', '2026-06-03 14:34:59', '5bacd32312e8cc6a2254eb904ffb10d1a6a7769fef6c5334dfc9e390f583c78e', '2026-07-03 14:34:59', 1, 1, 0, NULL, NULL),
(30, 'vishal', 'vishalmarmat1212@gmail.com', '9340749491', NULL, NULL, '$2y$10$OK9uTIPleQkVtlRXHeWzjeF/4DtZkuh2qSEsrCPZKyMZrvhdTO.5a', 'default_profile.png', 'active', '2026-06-09 13:14:01', '2026-06-09 13:25:22', '5390e7560f1530999fb87e253730b3beb57349d697ef9f0ec8bc2d5528f952cd', '2026-07-09 13:25:22', 1, 1, 0, 'f29c860db0456987502c2a19384ad5fed6b37a501185ecd173ccc9971d540e5b', '2026-06-09 14:30:42'),
(32, 'Modi', 'narendra@gmail.com', '0467826107', NULL, NULL, '$2y$10$UJuJLLX9MU86s4Wwa7SlMuD8CbJVv6UXk5NP3i4pYsx8KilYQ29Bm', 'default_profile.png', 'active', '2026-06-20 12:03:41', '2026-06-20 12:51:11', 'a06882f0ccbfcc090eec07f908770631', '2026-06-20 14:27:26', 1, 1, 0, NULL, NULL),
(33, 'Modiji', 'modi123@gmail.com', '8451076497', NULL, NULL, '$2y$10$s/1ECVOYuKIJ5nrTm0ZNKua.8bJm6rdLTyeOKttIRV25hRzNWPPnG', 'default_profile.png', 'active', '2026-07-14 18:22:12', '2026-07-14 18:22:31', NULL, NULL, 0, 1, 0, NULL, NULL),
(34, 'Ashwary Agrawal', 'lucky.agrawal555@gmail.com', '9424567807', NULL, NULL, '$2y$10$8Df/r6S2Ja4RM9ewQikCGO.7.eLkrwtg6Qd.zwC9v/WTauazcDK6K', 'default_profile.png', 'active', '2026-08-16 19:02:19', '2026-08-16 19:02:34', '42e39e109b4fe7b576c78cd36ba0be91', '2026-08-16 19:32:41', 1, 1, 0, NULL, NULL),
(35, 'ajay', 'asdf@gmail.com', '8889000034', NULL, '2026-08-11', '$2y$10$gWZwLb6EMtZUPBEcAy0OvOrttBF.Fe8IcX05/bCx2rA/VVVLt3TSG', 'default_profile.png', 'active', '2026-08-20 11:32:08', '2026-08-20 11:32:47', '6efc88958865dee23b5e3dc10dc6f724', '2026-08-20 12:02:47', 1, 1, 0, NULL, NULL),
(36, 'Divyansh Jain', 'jdivyansh261@gmail.com', '7738136187', NULL, NULL, '$2y$10$WdolgE.Oh6PrAVG8sealT.pxwi6ROS7X7XOTsZsjKQKsWwC9DSweO', 'default_profile.png', 'active', '2026-08-22 10:37:50', NULL, NULL, NULL, 0, 1, 0, NULL, NULL),
(37, 'deepti', 'deepti.dvd143@gmail.com', '9981820143', NULL, NULL, '$2y$10$n9j3.r6TwFAml9hTOUWPbeZ4fZEhWHVmqe7foxwfGDTZczMoqTV.2', 'default_profile.png', 'active', '2026-08-22 10:46:41', '2026-08-22 11:13:19', 'b461f77fb8a2dc5ba474fa39bf25d958', '2026-08-22 11:47:47', 1, 1, 0, NULL, NULL),
(38, 'Ajay', '164akki@gmail.com', '8889000034', NULL, NULL, '$2y$10$.9yQr9zXMP5/JU6bFYu..ehIi6BFUjLx/.HHwePBEBEnXdoaf5ixu', 'default_profile.png', 'active', '2026-08-30 09:08:52', '2026-08-31 10:16:24', 'b2cf7be2d4c447356aa6f3a1eb49faf5', '2026-08-31 10:46:24', 1, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `farmhouse_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `farmhouse_id`) VALUES
(4, 8, 4),
(6, 12, 6),
(13, 8, 13),
(14, 10, 14),
(15, 12, 15),
(16, 14, 16),
(17, 16, 17),
(18, 18, 18),
(20, 4, 20);


-- --------------------------------------------------------

--
-- Table structure for table `farmhouse_room_types`
--

CREATE TABLE `farmhouse_room_types` (
  `id` int(11) NOT NULL,
  `farmhouse_id` int(11) NOT NULL,
  `room_type_name` varchar(100) NOT NULL COMMENT 'e.g. Standard Room, Deluxe Room, Suite',
  `total_rooms` int(11) NOT NULL DEFAULT 1 COMMENT 'Number of available rooms of this type',
  `price_per_room` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Nightly rate per room',
  `capacity_per_room` int(11) NOT NULL DEFAULT 2 COMMENT 'Max guests per room',
  `description` varchar(255) DEFAULT NULL COMMENT 'Optional notes/features',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `farmhouse_room_types`
--

INSERT INTO `farmhouse_room_types` (`id`, `farmhouse_id`, `room_type_name`, `total_rooms`, `price_per_room`, `capacity_per_room`, `description`, `status`) VALUES
(1, 36, 'Standard Room', 3, 3000.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(2, 37, 'Standard Room', 3, 2000.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(3, 42, 'Standard Room', 12, 2000.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(4, 44, 'Standard Room', 2, 2000.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(5, 46, 'Standard Room', 2, 3000.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(6, 52, 'Standard Room', 4, 1799.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(7, 53, 'Standard Room', 6, 1500.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(8, 56, 'Standard Room', 5, 6000.00, 2, 'Standard comfortable bedroom with essential amenities', 'active'),
(9, 57, 'Standard Room', 9, 3000.00, 2, 'Standard comfortable bedroom with essential amenities', 'active');


-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL,
  `identifier` varchar(100) NOT NULL COMMENT 'Mobile or email address',
  `otp_code` varchar(255) NOT NULL COMMENT '6-digit secure OTP code',
  `purpose` enum('registration','phone_change','forgot_password_sms','forgot_password_email') NOT NULL COMMENT 'Context of OTP',
  `user_id` int(11) DEFAULT NULL COMMENT 'User ID if known',
  `role` enum('users','owners','admins') NOT NULL DEFAULT 'users' COMMENT 'User role',
  `meta_data` longtext DEFAULT NULL COMMENT 'JSON-encoded context',
  `attempts` int(11) NOT NULL DEFAULT 0 COMMENT 'Verification attempts',
  `max_attempts` int(11) NOT NULL DEFAULT 5 COMMENT 'Max allowed attempts',
  `expires_at` datetime NOT NULL COMMENT 'Expiration timestamp (IST)',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = pending, 1 = verified',
  `verified_at` datetime DEFAULT NULL COMMENT 'Verified timestamp (IST)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Client IP address',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Creation timestamp (IST)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Secure OTP verification records';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmhouse_id` (`farmhouse_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmhouse_id` (`farmhouse_id`);

--
-- Indexes for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_inquiries`
--
ALTER TABLE `contact_inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `farmhouses`
--
ALTER TABLE `farmhouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_price` (`price`);

--
-- Indexes for table `farmhouse_amenities`
--
ALTER TABLE `farmhouse_amenities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmhouse_id` (`farmhouse_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `farmhouse_rules`
--
ALTER TABLE `farmhouse_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmhouse_id` (`farmhouse_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmhouse_id` (`farmhouse_id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `inquiries_ibfk_1` (`user_id`),
  ADD KEY `inquiries_ibfk_2` (`farmhouse_id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `rule_presets`
--
ALTER TABLE `rule_presets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `farmhouse_id` (`farmhouse_id`);


--
-- Indexes for table `farmhouse_room_types`
--
ALTER TABLE `farmhouse_room_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_farmhouse_id` (`farmhouse_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_identifier_purpose` (`identifier`,`purpose`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_requests`
--
ALTER TABLE `booking_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `contact_inquiries`
--
ALTER TABLE `contact_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `farmhouses`
--
ALTER TABLE `farmhouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `farmhouse_amenities`
--
ALTER TABLE `farmhouse_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1003;

--
-- AUTO_INCREMENT for table `farmhouse_rules`
--
ALTER TABLE `farmhouse_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=982;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rule_presets`
--
ALTER TABLE `rule_presets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;


--
-- AUTO_INCREMENT for table `farmhouse_room_types`
--
ALTER TABLE `farmhouse_room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blocked_dates`
--
ALTER TABLE `blocked_dates`
  ADD CONSTRAINT `blocked_dates_ibfk_1` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farmhouse_amenities`
--
ALTER TABLE `farmhouse_amenities`
  ADD CONSTRAINT `farmhouse_amenities_ibfk_1` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `farmhouse_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farmhouse_rules`
--
ALTER TABLE `farmhouse_rules`
  ADD CONSTRAINT `farmhouse_rules_ibfk_1` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `farmhouse_room_types`
--
ALTER TABLE `farmhouse_room_types`
  ADD CONSTRAINT `fk_room_types_farmhouse` FOREIGN KEY (`farmhouse_id`) REFERENCES `farmhouses` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
