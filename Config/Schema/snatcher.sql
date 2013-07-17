SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `snatcher`
--

-- --------------------------------------------------------

--
-- Table structure for table `acos`
--

DROP TABLE IF EXISTS `acos`;
CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `model` varchar(255) DEFAULT '',
  `foreign_key` int(10) unsigned DEFAULT NULL,
  `alias` varchar(255) DEFAULT '',
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_acos_lft_rght` (`lft`,`rght`),
  KEY `idx_acos_alias` (`alias`),
  KEY `idx_acos_model_foreign_key` (`model`,`foreign_key`),
  KEY `acosParentId_fk` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=356 ;

-- --------------------------------------------------------

--
-- Table structure for table `aros`
--

DROP TABLE IF EXISTS `aros`;
CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `model` varchar(255) DEFAULT '',
  `foreign_key` int(10) unsigned DEFAULT NULL,
  `alias` varchar(255) DEFAULT '',
  `lft` int(11) DEFAULT NULL,
  `rght` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_aros_lft_rght` (`lft`,`rght`),
  KEY `idx_aros_alias` (`alias`),
  KEY `idx_aros_model_foreign_key` (`model`,`foreign_key`),
  KEY `arosParentId` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `aros_acos`
--

DROP TABLE IF EXISTS `aros_acos`;
CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) unsigned NOT NULL,
  `aco_id` int(10) unsigned NOT NULL,
  `_create` char(2) NOT NULL DEFAULT '0',
  `_read` char(2) NOT NULL DEFAULT '0',
  `_update` char(2) NOT NULL DEFAULT '0',
  `_delete` char(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_aros_acos_aro_id_aco_id` (`aro_id`,`aco_id`),
  KEY `aco_id` (`aco_id`),
  KEY `aro_id` (`aro_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=136 ;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_attachments_users` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Attachment files from project' AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `binary_files`
--

DROP TABLE IF EXISTS `binary_files`;
CREATE TABLE IF NOT EXISTS `binary_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `binary_texts_validated` smallint(5) unsigned NOT NULL DEFAULT '0',
  `binary_texts_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `reviews_validated` smallint(5) unsigned NOT NULL DEFAULT '0',
  `reviews_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(99) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_binary_files_users` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Binary files with texts from games' AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

--
-- Table structure for table `binary_files_testers`
--

DROP TABLE IF EXISTS `binary_files_testers`;
CREATE TABLE IF NOT EXISTS `binary_files_testers` (
  `binary_file_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `binary_files_testers_unique` (`binary_file_id`,`user_id`),
  KEY `fk_binary_files_testers_binary_files` (`binary_file_id`),
  KEY `fk_binary_files_testers_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='HABTM relation between binary_files and users';

-- --------------------------------------------------------

--
-- Table structure for table `binary_texts`
--

DROP TABLE IF EXISTS `binary_texts`;
CREATE TABLE IF NOT EXISTS `binary_texts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `binary_file_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `character_id_old` int(10) unsigned NOT NULL,
  `character_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order` smallint(5) unsigned NOT NULL,
  `text_offset` smallint(5) unsigned NOT NULL COMMENT 'Offset from first text in binary file (0x3800)',
  `nchars` smallint(5) unsigned NOT NULL,
  `review_count` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `validated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `new_text` text,
  `fixed_text_id` int(10) unsigned DEFAULT NULL,
  `binary` text,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_binary_texts_users` (`user_id`),
  KEY `fk_binary_texts_fixed_texts` (`fixed_text_id`),
  KEY `fk_binary_texts_binary_files` (`binary_file_id`),
  KEY `fk_binary_texts_characters` (`character_id`),
  KEY `fk_binary_texts_characters_old` (`character_id_old`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Translation of texts from binary files' AUTO_INCREMENT=24048 ;

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `readonly` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'if 1 can change original value',
  `translatable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `code` tinyint(2) unsigned NOT NULL COMMENT 'Hex value in integer value',
  `hex` char(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `new_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Characters from the game associated with the texts' AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order` tinyint(3) unsigned NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Frequently Asked Questions' AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `fixed_texts`
--

DROP TABLE IF EXISTS `fixed_texts`;
CREATE TABLE IF NOT EXISTS `fixed_texts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `binary_text_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `new_text` text NOT NULL,
  `validated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_fixed_texts_users` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Translation of texts that are repeated two or more times' AUTO_INCREMENT=601 ;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
CREATE TABLE IF NOT EXISTS `keywords` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `new_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keywords_name_new_name_unique` (`name`,`new_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Special keywords to replace in binary_texts' AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `binary_file_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `binary_text_id` int(10) unsigned NOT NULL,
  `new_text` text NOT NULL,
  `validated` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hasValidatedReview` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_reviews_testers` (`user_id`,`binary_file_id`),
  KEY `fk_reviews_binary_texts` (`binary_text_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Reviews from users associated with binary_files_testers' AUTO_INCREMENT=94 ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `NameUniqueKey` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Users role' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `saves`
--

DROP TABLE IF EXISTS `saves`;
CREATE TABLE IF NOT EXISTS `saves` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `binary_file_id` int(10) unsigned NOT NULL,
  `act` tinyint(2) unsigned NOT NULL,
  `slot` tinyint(2) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_saves_users` (`user_id`),
  KEY `fk_saves_binary_files` (`binary_file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Quicksaves from Kegafusion' AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '3',
  `username` varchar(45) NOT NULL,
  `password` char(64) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `timezone` varchar(45) NOT NULL DEFAULT 'Europe/Madrid',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If 1 user is active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  KEY `fk_users_roles` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aros_acos`
--
ALTER TABLE `aros_acos`
  ADD CONSTRAINT `aros_acos_ibfk_1` FOREIGN KEY (`aro_id`) REFERENCES `aros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aros_acos_ibfk_2` FOREIGN KEY (`aco_id`) REFERENCES `acos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `binary_files`
--
ALTER TABLE `binary_files`
  ADD CONSTRAINT `binary_files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `binary_files_testers`
--
ALTER TABLE `binary_files_testers`
  ADD CONSTRAINT `binary_files_testers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `binary_files_testers_ibfk_2` FOREIGN KEY (`binary_file_id`) REFERENCES `binary_files` (`id`);

--
-- Constraints for table `binary_texts`
--
ALTER TABLE `binary_texts`
  ADD CONSTRAINT `binary_texts_ibfk_1` FOREIGN KEY (`binary_file_id`) REFERENCES `binary_files` (`id`),
  ADD CONSTRAINT `binary_texts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `binary_texts_ibfk_3` FOREIGN KEY (`character_id`) REFERENCES `characters` (`id`),
  ADD CONSTRAINT `binary_texts_ibfk_4` FOREIGN KEY (`character_id_old`) REFERENCES `characters` (`id`),
  ADD CONSTRAINT `binary_texts_ibfk_5` FOREIGN KEY (`fixed_text_id`) REFERENCES `fixed_texts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `fixed_texts`
--
ALTER TABLE `fixed_texts`
  ADD CONSTRAINT `fixed_texts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_binary_texts` FOREIGN KEY (`binary_text_id`) REFERENCES `binary_texts` (`id`),
  ADD CONSTRAINT `fk_reviews_testers` FOREIGN KEY (`user_id`, `binary_file_id`) REFERENCES `binary_files_testers` (`user_id`, `binary_file_id`);

--
-- Constraints for table `saves`
--
ALTER TABLE `saves`
  ADD CONSTRAINT `saves_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `saves_ibfk_2` FOREIGN KEY (`binary_file_id`) REFERENCES `binary_files` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Dumping data for table `acos`
--

INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(215, NULL, NULL, NULL, 'controllers', 1, 164),
(222, 215, NULL, NULL, 'Pages', 2, 5),
(223, 222, NULL, NULL, 'display', 3, 4),
(231, 215, NULL, NULL, 'Users', 6, 25),
(232, 231, NULL, NULL, 'login', 7, 8),
(233, 231, NULL, NULL, 'logout', 9, 10),
(235, 231, NULL, NULL, 'admin_index', 11, 12),
(237, 231, NULL, NULL, 'admin_add', 13, 14),
(238, 231, NULL, NULL, 'admin_edit', 15, 16),
(239, 231, NULL, NULL, 'admin_delete', 17, 18),
(268, 215, NULL, NULL, 'Attachments', 26, 39),
(269, 268, NULL, NULL, 'index', 27, 28),
(270, 268, NULL, NULL, 'download', 29, 30),
(274, 215, NULL, NULL, 'Faqs', 40, 51),
(275, 274, NULL, NULL, 'index', 41, 42),
(276, 274, NULL, NULL, 'admin_index', 43, 44),
(277, 274, NULL, NULL, 'admin_edit', 45, 46),
(278, 274, NULL, NULL, 'admin_add', 47, 48),
(279, 274, NULL, NULL, 'admin_delete', 49, 50),
(286, 215, NULL, NULL, 'FixedTexts', 52, 61),
(293, 215, NULL, NULL, 'Saves', 62, 81),
(294, 293, NULL, NULL, 'index', 63, 64),
(295, 293, NULL, NULL, 'download', 65, 66),
(296, 293, NULL, NULL, 'add', 67, 68),
(297, 293, NULL, NULL, 'edit', 69, 70),
(298, 293, NULL, NULL, 'delete', 71, 72),
(299, 268, NULL, NULL, 'admin_index', 31, 32),
(300, 268, NULL, NULL, 'admin_add', 33, 34),
(301, 268, NULL, NULL, 'admin_edit', 35, 36),
(302, 268, NULL, NULL, 'admin_delete', 37, 38),
(303, 215, NULL, NULL, 'BinaryFiles', 82, 95),
(304, 303, NULL, NULL, 'index', 83, 84),
(305, 303, NULL, NULL, 'test', 85, 86),
(306, 303, NULL, NULL, 'download', 87, 88),
(307, 303, NULL, NULL, 'admin_index', 89, 90),
(308, 303, NULL, NULL, 'admin_downloadall', 91, 92),
(309, 303, NULL, NULL, 'admin_edit', 93, 94),
(310, 215, NULL, NULL, 'BinaryTexts', 96, 109),
(311, 310, NULL, NULL, 'search', 97, 98),
(312, 310, NULL, NULL, 'index', 99, 100),
(313, 310, NULL, NULL, 'edit', 101, 102),
(314, 310, NULL, NULL, 'admin_index', 103, 104),
(315, 310, NULL, NULL, 'admin_changeCharacter', 105, 106),
(316, 310, NULL, NULL, 'admin_edit', 107, 108),
(317, 215, NULL, NULL, 'Characters', 110, 121),
(318, 317, NULL, NULL, 'admin_index', 111, 112),
(319, 317, NULL, NULL, 'admin_view', 113, 114),
(320, 317, NULL, NULL, 'admin_edit', 115, 116),
(321, 317, NULL, NULL, 'admin_translate', 117, 118),
(322, 317, NULL, NULL, 'admin_restore', 119, 120),
(323, 286, NULL, NULL, 'admin_search', 53, 54),
(324, 286, NULL, NULL, 'admin_index', 55, 56),
(325, 286, NULL, NULL, 'admin_view', 57, 58),
(326, 286, NULL, NULL, 'admin_edit', 59, 60),
(327, 215, NULL, NULL, 'Keywords', 122, 137),
(328, 327, NULL, NULL, 'admin_index', 123, 124),
(329, 327, NULL, NULL, 'admin_view', 125, 126),
(330, 327, NULL, NULL, 'admin_add', 127, 128),
(331, 327, NULL, NULL, 'admin_edit', 129, 130),
(332, 327, NULL, NULL, 'admin_delete', 131, 132),
(333, 327, NULL, NULL, 'admin_translate', 133, 134),
(334, 327, NULL, NULL, 'admin_restore', 135, 136),
(335, 215, NULL, NULL, 'Reviews', 138, 161),
(336, 335, NULL, NULL, 'search', 139, 140),
(337, 335, NULL, NULL, 'index', 141, 142),
(338, 335, NULL, NULL, 'test', 143, 144),
(339, 335, NULL, NULL, 'add', 145, 146),
(340, 335, NULL, NULL, 'edit', 147, 148),
(341, 335, NULL, NULL, 'delete', 149, 150),
(342, 335, NULL, NULL, 'validation', 151, 152),
(343, 335, NULL, NULL, 'admin_index', 153, 154),
(344, 335, NULL, NULL, 'admin_edit', 155, 156),
(345, 335, NULL, NULL, 'admin_delete', 157, 158),
(346, 293, NULL, NULL, 'admin_index', 73, 74),
(347, 293, NULL, NULL, 'admin_edit', 75, 76),
(348, 293, NULL, NULL, 'admin_delete', 77, 78),
(350, 231, NULL, NULL, 'changePassword', 19, 20),
(351, 231, NULL, NULL, 'admin_changePassword', 21, 22),
(352, 215, NULL, NULL, 'AclExtras', 162, 163),
(353, 293, NULL, NULL, 'search', 79, 80),
(354, 231, NULL, NULL, 'edit', 23, 24),
(355, 335, NULL, NULL, 'other_reviews', 159, 160);

--
-- Dumping data for table `aros`
--

INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, NULL, 'Role', 1, NULL, 1, 2),
(2, NULL, 'Role', 2, NULL, 3, 4),
(3, NULL, 'Role', 3, NULL, 5, 6);

--
-- Dumping data for table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(25, 1, 215, '1', '1', '1', '1'),
(32, 2, 223, '1', '1', '1', '1'),
(33, 3, 223, '1', '1', '1', '1'),
(43, 3, 232, '1', '1', '1', '1'),
(44, 3, 233, '1', '1', '1', '1'),
(45, 2, 233, '1', '1', '1', '1'),
(46, 2, 232, '1', '1', '1', '1'),
(49, 2, 270, '1', '1', '1', '1'),
(51, 2, 269, '1', '1', '1', '1'),
(52, 3, 269, '1', '1', '1', '1'),
(53, 3, 270, '1', '1', '1', '1'),
(54, 2, 275, '1', '1', '1', '1'),
(55, 3, 275, '1', '1', '1', '1'),
(73, 3, 296, '1', '1', '1', '1'),
(74, 2, 296, '1', '1', '1', '1'),
(75, 2, 298, '1', '1', '1', '1'),
(76, 3, 298, '1', '1', '1', '1'),
(77, 2, 294, '1', '1', '1', '1'),
(78, 3, 294, '1', '1', '1', '1'),
(79, 3, 297, '1', '1', '1', '1'),
(80, 2, 297, '1', '1', '1', '1'),
(81, 2, 295, '1', '1', '1', '1'),
(82, 3, 295, '1', '1', '1', '1'),
(83, 2, 215, '-1', '-1', '-1', '-1'),
(84, 2, 350, '1', '1', '1', '1'),
(85, 2, 304, '1', '1', '1', '1'),
(86, 2, 305, '1', '1', '1', '1'),
(87, 2, 306, '1', '1', '1', '1'),
(88, 2, 307, '1', '1', '1', '1'),
(89, 2, 309, '1', '1', '1', '1'),
(90, 2, 308, '1', '1', '1', '1'),
(91, 2, 312, '1', '1', '1', '1'),
(92, 2, 311, '1', '1', '1', '1'),
(93, 2, 313, '1', '1', '1', '1'),
(94, 2, 314, '1', '1', '1', '1'),
(95, 2, 316, '1', '1', '1', '1'),
(96, 2, 315, '1', '1', '1', '1'),
(97, 2, 324, '1', '1', '1', '1'),
(98, 2, 323, '1', '1', '1', '1'),
(99, 2, 326, '1', '1', '1', '1'),
(100, 2, 325, '1', '1', '1', '1'),
(101, 2, 299, '1', '1', '1', '1'),
(102, 2, 300, '1', '1', '1', '1'),
(103, 2, 301, '1', '1', '1', '1'),
(104, 2, 302, '1', '1', '1', '1'),
(105, 2, 343, '1', '1', '1', '1'),
(106, 2, 344, '1', '1', '1', '1'),
(107, 2, 345, '1', '1', '1', '1'),
(108, 2, 337, '1', '1', '1', '1'),
(109, 2, 336, '1', '1', '1', '1'),
(110, 2, 338, '1', '1', '1', '1'),
(111, 2, 339, '1', '1', '1', '1'),
(112, 2, 340, '1', '1', '1', '1'),
(113, 2, 341, '1', '1', '1', '1'),
(114, 2, 342, '1', '1', '1', '1'),
(115, 3, 215, '-1', '-1', '-1', '-1'),
(116, 3, 350, '1', '1', '1', '1'),
(117, 3, 304, '1', '1', '1', '1'),
(118, 3, 305, '1', '1', '1', '1'),
(119, 3, 306, '1', '1', '1', '1'),
(120, 3, 312, '1', '1', '1', '1'),
(121, 3, 311, '1', '1', '1', '1'),
(122, 3, 313, '1', '1', '1', '1'),
(123, 3, 337, '1', '1', '1', '1'),
(124, 3, 336, '1', '1', '1', '1'),
(125, 3, 338, '1', '1', '1', '1'),
(126, 3, 339, '1', '1', '1', '1'),
(127, 3, 340, '1', '1', '1', '1'),
(128, 3, 341, '1', '1', '1', '1'),
(129, 3, 342, '1', '1', '1', '1'),
(130, 2, 353, '1', '1', '1', '1'),
(131, 3, 353, '1', '1', '1', '1'),
(132, 2, 354, '1', '1', '1', '1'),
(133, 3, 354, '1', '1', '1', '1'),
(134, 2, 355, '1', '1', '1', '1'),
(135, 3, 355, '1', '1', '1', '1');

--
-- Dumping data for table `characters`
--

INSERT INTO `characters` (`id`, `readonly`, `translatable`, `code`, `hex`, `name`, `new_name`) VALUES
(1, 1, 0, 56, '38', 'Menu', 'Menú'),
(2, 1, 0, 1, '01', 'Jordan''s Screen', 'Pantalla de Jordan'),
(3, 0, 1, 2, '02', 'Metal Gear', 'Metal Gear'),
(4, 0, 1, 3, '03', 'Gillian', 'Gillian'),
(5, 0, 1, 4, '04', 'Mika', 'Mika'),
(6, 0, 1, 5, '05', 'Togo', 'Togo'),
(7, 0, 0, 6, '06', 'Driver', 'Conductor'),
(8, 0, 0, 7, '07', 'Chief', 'Jefe'),
(9, 0, 1, 8, '08', 'Harry', 'Harry'),
(10, 0, 1, 9, '09', 'Napoleon', 'Napoleón'),
(11, 0, 1, 10, '0a', 'Jamie', 'Jamie'),
(12, 0, 1, 11, '0b', 'Employee', 'Empleado'),
(13, 0, 0, 12, '0c', 'Man', 'Hombre'),
(14, 0, 1, 13, '0d', 'Stella', 'Estela'),
(15, 0, 1, 14, '0e', 'Christy', 'Cristina'),
(16, 0, 0, 15, '0f', 'Woman', 'Mujer'),
(17, 0, 1, 16, '10', 'Fukui II', 'Fukui II'),
(18, 0, 1, 17, '11', 'Miss Hayasaka', 'Señorita Hayasaka'),
(19, 0, 1, 18, '12', 'Mr. Togo', 'Señor Togo'),
(20, 0, 1, 19, '13', 'Mr. Nakamura', 'Señor Nakamura'),
(21, 0, 1, 20, '14', 'Nakamura', 'Nakamura'),
(22, 0, 0, 21, '15', 'Mr. Sasaki', 'Señor Sasaki'),
(23, 0, 1, 22, '16', 'Jeremy', 'Jeremy'),
(24, 0, 1, 23, '17', 'Chie', 'Chiek'),
(25, 0, 1, 24, '18', 'Kiwi', 'Kiwi'),
(26, 0, 1, 25, '19', 'Mr. Kushibuchi', 'Señor Kushibuchi'),
(27, 0, 1, 26, '1a', 'Mr. Inamura', 'Señor Inamura'),
(28, 0, 1, 27, '1b', 'Jordan', 'Jordan'),
(29, 0, 1, 28, '1c', 'Konami', 'Konami'),
(30, 0, 1, 29, '1d', 'Vangie', 'Vangie'),
(31, 0, 1, 30, '1e', 'Katrina', 'Katrina'),
(32, 0, 1, 31, '1f', 'Passerby', 'Transeúnte'),
(33, 0, 1, 32, '20', 'P.A.', 'P.A.'),
(34, 0, 1, 33, '21', 'Doorman', 'Portero'),
(35, 0, 1, 34, '22', 'Manager', 'Gerente'),
(36, 0, 1, 35, '23', 'Isabella', 'Isabel'),
(37, 0, 1, 36, '24', 'Sparkster', 'Sparkster'),
(38, 0, 1, 37, '25', 'Goemon', 'Goemon'),
(39, 0, 1, 38, '26', 'Mr. Ueda', 'Mr. Ueda'),
(40, 0, 1, 39, '27', 'Contra', 'Contra'),
(41, 0, 1, 40, '28', 'Castlevania pair', 'Pareja del Castlevania'),
(42, 0, 0, 41, '29', 'Customer', 'Cliente'),
(43, 0, 1, 42, '2a', 'Dracula', 'Drácula'),
(44, 0, 1, 43, '2b', 'Simon Belmont', 'Simon Belmont'),
(45, 0, 1, 44, '2c', 'Freeman', 'Hombre libre'),
(46, 0, 1, 45, '2d', 'Ivan', 'Iván'),
(47, 0, 1, 46, '2e', 'Lisa', 'Lisa'),
(48, 0, 1, 47, '2f', 'Parrot', 'Loro'),
(49, 0, 1, 48, '30', 'Random', 'Random'),
(69, 1, 0, 68, '44', 'Nobody', 'Nadie'),
(70, 1, 0, 56, '38', 'Answer number', 'Respuesta númerica'),
(71, 1, 0, 56, '38', 'Answer', 'Respuesta');

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Manager'),
(3, 'User');
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
