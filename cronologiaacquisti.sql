-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 02, 2025 alle 13:10
-- Versione del server: 10.4.21-MariaDB
-- Versione PHP: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bitems`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `cronologiaacquisti`
--

CREATE TABLE `cronologiaacquisti` (
  `cro_id` int(11) NOT NULL,
  `cro_art_id` int(11) NOT NULL,
  `cro_ute_id` int(11) NOT NULL,
  `cro_qta` int(11) NOT NULL,
  `cro_prezzoFinale` float NOT NULL,
  `cro_rec_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  ADD PRIMARY KEY (`cro_id`),
  ADD KEY `cro_art_id` (`cro_art_id`),
  ADD KEY `cro_rec_id` (`cro_rec_id`),
  ADD KEY `cro_ute_id` (`cro_ute_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  MODIFY `cro_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  ADD CONSTRAINT `cro_art_id` FOREIGN KEY (`cro_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `cro_rec_id` FOREIGN KEY (`cro_rec_id`) REFERENCES `recensione` (`rec_id`),
  ADD CONSTRAINT `cro_ute_id` FOREIGN KEY (`cro_ute_id`) REFERENCES `utenti` (`ute_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
