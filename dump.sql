-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Sep 21, 2021 at 04:50 PM
-- Server version: 5.7.18
-- PHP Version: 7.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `localize`
--

-- --------------------------------------------------------

--
-- Table structure for table `key_lang`
--

CREATE TABLE `key_lang` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `key_lang`
--

INSERT INTO `key_lang` (`id`, `name`) VALUES
(6, 'main.country'),
(5, 'main.hello'),
(4, 'main.welcome');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isocode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ltr` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`id`, `name`, `isocode`, `ltr`, `created_at`, `updated_at`) VALUES
(6, 'English', 'en', 1, '2007-04-05 02:02:43', '2018-03-27 12:06:42'),
(7, 'French', 'fr_FR', 1, '2006-05-11 04:41:59', '2021-09-03 11:28:27'),
(8, 'Polish', 'pl_PL', 1, '2017-02-12 10:06:41', '2001-05-01 09:16:27'),
(9, 'Spanish', 'es', 1, '2021-06-07 16:33:57', '2014-12-04 20:11:58'),
(10, 'Italian', 'it', 1, '2014-04-02 22:40:10', '2009-05-19 02:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `translation`
--

CREATE TABLE `translation` (
  `id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `key_id` int(11) NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `translation`
--

INSERT INTO `translation` (`id`, `language_id`, `key_id`, `text`) VALUES
(9, 6, 5, 'Hello'),
(10, 9, 5, 'Hola'),
(11, 7, 5, 'Bonjour'),
(12, 10, 5, 'Buon giorno'),
(13, 8, 5, 'Здраво'),
(14, 6, 4, 'Welcome!'),
(15, 9, 4, 'Bienvenido!'),
(16, 7, 4, 'Bienvenu!');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `roles`) VALUES
(3, 'admin@example.com', '$2y$13$Uo5f9dU7IMgTFXDoaOvBX.VCych0XYtjqR2vVMXiEbont8cMSQ/S.', 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}'),
(4, 'reader@example.com', '$2y$13$uvYJkbgXh3jq10PuZ8qih.m.Vqpessgc0tb2ypr4kxdwXrUM/ckyW', 'a:1:{i:0;s:11:\"ROLE_READER\";}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `key_lang`
--
ALTER TABLE `key_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_idx` (`name`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translation`
--
ALTER TABLE `translation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B469456F82F1BAF4` (`language_id`),
  ADD KEY `IDX_B469456FD145533` (`key_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `key_lang`
--
ALTER TABLE `key_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `translation`
--
ALTER TABLE `translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `translation`
--
ALTER TABLE `translation`
  ADD CONSTRAINT `FK_B469456F82F1BAF4` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`),
  ADD CONSTRAINT `FK_B469456FD145533` FOREIGN KEY (`key_id`) REFERENCES `key_lang` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
