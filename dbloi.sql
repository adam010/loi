/*
MySQL Data Transfer
Source Host: localhost
Source Database: dbloi
Target Host: localhost
Target Database: dbloi

*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for leden
-- ----------------------------
DROP TABLE IF EXISTS `leden`;
CREATE TABLE `leden` (
  `id` smallint(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(30) DEFAULT NULL,
  `tussenvoegsel` varchar(10) DEFAULT NULL,
  `achternaam` varchar(30) DEFAULT NULL,
  `geboortedatum` date DEFAULT NULL,
  `geslacht` char(1) DEFAULT 'M',
  `email` varchar(50) DEFAULT NULL,
  `straat` varchar(30) DEFAULT NULL,
  `huisnummer` int(10) DEFAULT NULL,
  `postcode` varchar(12) DEFAULT NULL,
  `woonplaats` varchar(30) DEFAULT NULL,
  `gewijzigd` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for lidmaatschap
-- ----------------------------
DROP TABLE IF EXISTS `lidmaatschap`;
CREATE TABLE `lidmaatschap` (
  `id` smallint(11) NOT NULL AUTO_INCREMENT,
  `lidnummer` smallint(11) DEFAULT NULL,
  `datumingang` date DEFAULT NULL,
  `datumeinde` date DEFAULT NULL,
  `sportonderdeel` varchar(30) DEFAULT NULL,
  `lesdag` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
