-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versione server:              10.4.6-MariaDB - mariadb.org binary distribution
-- S.O. server:                  Win64
-- HeidiSQL Versione:            10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


CREATE DATABASE IF NOT EXISTS `banfi3_in_diretta_db` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `banfi3_in_diretta_db`;

CREATE TABLE IF NOT EXISTS `prenotazioni` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Mail` varchar(255) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Cognome` varchar(255) NOT NULL,
  `Comune` varchar(255) NOT NULL,
  `Scuola` varchar(255) NOT NULL,
  `Telefono` varchar(255) NOT NULL,
  `DataID` int(11) NOT NULL,
  `Ip` varchar(255),
  `Timestamp` int(11),
  `Useragent` varchar(255),
  PRIMARY KEY (`ID`)
) DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `date_prenotabili` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Corso` varchar(255) NOT NULL,
  `Mese` int(11) NOT NULL,
  `Data` int(11) NOT NULL,
  `GiorniDisponibili` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `configurazione` (
  `Descrizione` varchar(800),
  `MessaggioRegistrazioneCompletata` varchar(800),
  `Anno` int(11)
) DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `accounts` (
  `Mail` varchar(255) NOT NULL,
  `Secret` varchar(255) NOT NULL,
  PRIMARY KEY (`Mail`)
) DEFAULT CHARSET=latin1;



-- ----------------
-- dati di demo --
-- ----------------

INSERT INTO `configurazione` (MessaggioRegistrazioneCompletata, Descrizione, Anno) VALUES
(
  "Ti aspettiamo alle 8:20 presso la nostra sede in via ---. L'attivita avrà un durata di 3 ore",
  "Descrizione breve, con informazioni essenziali quali ad esempio l'orario dei corsi. Questo testo sarà configurabile nel pannello di controllo",
  2019
);

INSERT INTO `date_prenotabili` (Corso, Mese, `Data`, GiorniDisponibili) VALUES
("scientifico", 11, 12, 20),
("scientifico", 11, 13, 20),
("scientifico", 11, 14, 20),
("scientifico", 11, 15, 20),
("scientifico", 11, 16, 20),
("scientifico", 11, 17, 20),
("scientifico", 11, 18, 20),
("scientifico", 11, 19, 20),
("scientifico", 11, 25, 20),
("scientifico", 12, 2, 20),
("scientifico", 12, 3, 20),
("scientifico", 12, 4, 20),
("scientifico", 12, 5, 20),

("classico", 11, 12, 20),
("classico", 11, 13, 20),
("classico", 11, 14, 20),
("classico", 11, 15, 20),
("classico", 11, 16, 20),
("classico", 11, 17, 20),
("classico", 11, 18, 20),
("classico", 11, 19, 20),
("classico", 11, 25, 20),
("classico", 12, 2, 20),
("classico", 12, 3, 20),
("classico", 12, 4, 20),
("classico", 12, 5, 20),

("scienze applicate", 11, 12, 20),
("scienze applicate", 11, 13, 20),
("scienze applicate", 11, 14, 20),
("scienze applicate", 11, 15, 20),
("scienze applicate", 11, 16, 20),
("scienze applicate", 11, 17, 20),
("scienze applicate", 11, 18, 20),
("scienze applicate", 11, 19, 20),
("scienze applicate", 11, 25, 20),
("scienze applicate", 12, 2, 20),
("scienze applicate", 12, 3, 20),
("scienze applicate", 12, 4, 20),
("scienze applicate", 12, 5, 20);
