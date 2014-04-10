# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.34)
# Database: patrizi1_movienet_db
# Generation Time: 2014-04-10 22:24:56 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table Director
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Director`;

CREATE TABLE `Director` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table Document
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Document`;

CREATE TABLE `Document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table Genre
# ------------------------------------------------------------

DROP TABLE IF EXISTS `Genre`;

CREATE TABLE `Genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table movie
# ------------------------------------------------------------

DROP TABLE IF EXISTS `movie`;

CREATE TABLE `movie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titolo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sottotitolo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titolo_originale` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `supporto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tipo_supporto` int(11) DEFAULT NULL,
  `lingua` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_uscita` datetime DEFAULT NULL,
  `cast` longtext COLLATE utf8_unicode_ci,
  `trama` longtext COLLATE utf8_unicode_ci,
  `durata` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locandina` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visto` tinyint(1) DEFAULT NULL,
  `id_genere` int(11) DEFAULT NULL,
  `id_regista` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DC9FDD6B66E2D4C9` (`id_genere`),
  KEY `IDX_DC9FDD6B2A520104` (`id_regista`),
  CONSTRAINT `FK_DC9FDD6B2A520104` FOREIGN KEY (`id_regista`) REFERENCES `Director` (`id`),
  CONSTRAINT `FK_DC9FDD6B66E2D4C9` FOREIGN KEY (`id_genere`) REFERENCES `Genre` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
