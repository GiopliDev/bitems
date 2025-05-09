-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 09, 2025 alle 12:51
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
-- Struttura della tabella `articoli`
--

CREATE TABLE `articoli` (
  `art_id` int(11) NOT NULL,
  `art_titolo` varchar(50) NOT NULL,
  `art_qtaDisp` int(11) NOT NULL DEFAULT 1,
  `art_prezzoUnitario` float NOT NULL,
  `art_descrizione` varchar(500) NOT NULL,
  `art_status` enum('D','E','N') NOT NULL COMMENT 'disponibile,esaurito,non visibile',
  `art_tip_id` int(11) NOT NULL,
  `art_ute_id` int(11) NOT NULL,
  `art_gio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `articoli`
--

INSERT INTO `articoli` (`art_id`, `art_titolo`, `art_qtaDisp`, `art_prezzoUnitario`, `art_descrizione`, `art_status`, `art_tip_id`, `art_ute_id`, `art_gio_id`) VALUES
(1, 'Easter Egg Launcher LVL 144', 10, 0.5, 'The special weapon added in the latest STW update.', 'D', 1, 1, 1);

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

-- --------------------------------------------------------

--
-- Struttura della tabella `giochiaffiliati`
--

CREATE TABLE `giochiaffiliati` (
  `gio_id` int(11) NOT NULL,
  `gio_nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `giochiaffiliati`
--

INSERT INTO `giochiaffiliati` (`gio_id`, `gio_nome`) VALUES
(1, 'Fortnite'),
(2, 'Minecraft'),
(3, 'CS:GO'),
(4, 'Path Of Exile 2'),
(5, 'Other');

-- --------------------------------------------------------

--
-- Struttura della tabella `recensione`
--

CREATE TABLE `recensione` (
  `rec_id` int(11) NOT NULL,
  `rec_art_id` int(11) NOT NULL,
  `rec_ute_id` int(11) NOT NULL,
  `rec_voto` enum('0','1') NOT NULL,
  `rec_dex` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `segnalibri`
--

CREATE TABLE `segnalibri` (
  `seg_id` int(11) NOT NULL,
  `seg_ute_id` int(11) NOT NULL,
  `seg_art_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_nome`) VALUES
(1, 'Limited Edition'),
(2, 'Easter');

-- --------------------------------------------------------

--
-- Struttura della tabella `tags_articoli`
--

CREATE TABLE `tags_articoli` (
  `art_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tags_articoli`
--

INSERT INTO `tags_articoli` (`art_id`, `tag_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `tipologie`
--

CREATE TABLE `tipologie` (
  `tip_id` int(11) NOT NULL,
  `tip_nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tipologie`
--

INSERT INTO `tipologie` (`tip_id`, `tip_nome`) VALUES
(1, 'Item'),
(2, 'Code'),
(5, 'Account'),
(6, 'Cosmetic'),
(9, 'Boosting Service');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `ute_id` int(11) NOT NULL,
  `ute_nome` varchar(50) NOT NULL,
  `ute_cognome` varchar(50) NOT NULL,
  `ute_username` varchar(25) NOT NULL,
  `ute_email` varchar(60) NOT NULL,
  `ute_rep` int(11) NOT NULL DEFAULT 0,
  `ute_saldo` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`ute_id`, `ute_nome`, `ute_cognome`, `ute_username`, `ute_email`, `ute_rep`, `ute_saldo`) VALUES
(1, 'Gabriele', 'Bondoni', 'Giopli', '', 78, 0);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `articoli`
--
ALTER TABLE `articoli`
  ADD PRIMARY KEY (`art_id`),
  ADD KEY `art_gio_id` (`art_gio_id`),
  ADD KEY `art_tip_id` (`art_tip_id`),
  ADD KEY `art_ute_id` (`art_ute_id`);

--
-- Indici per le tabelle `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  ADD PRIMARY KEY (`cro_id`),
  ADD KEY `cro_art_id` (`cro_art_id`),
  ADD KEY `cro_rec_id` (`cro_rec_id`),
  ADD KEY `cro_ute_id` (`cro_ute_id`);

--
-- Indici per le tabelle `giochiaffiliati`
--
ALTER TABLE `giochiaffiliati`
  ADD PRIMARY KEY (`gio_id`);

--
-- Indici per le tabelle `recensione`
--
ALTER TABLE `recensione`
  ADD PRIMARY KEY (`rec_id`),
  ADD KEY `rec_art_id` (`rec_art_id`),
  ADD KEY `rec_ute_id` (`rec_ute_id`);

--
-- Indici per le tabelle `segnalibri`
--
ALTER TABLE `segnalibri`
  ADD PRIMARY KEY (`seg_id`),
  ADD KEY `seg_art_id` (`seg_art_id`),
  ADD KEY `seg_ute_id` (`seg_ute_id`);

--
-- Indici per le tabelle `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indici per le tabelle `tags_articoli`
--
ALTER TABLE `tags_articoli`
  ADD KEY `tagarticoli_art_id` (`art_id`),
  ADD KEY `tagarticoli_tag_id` (`tag_id`);

--
-- Indici per le tabelle `tipologie`
--
ALTER TABLE `tipologie`
  ADD PRIMARY KEY (`tip_id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`ute_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `articoli`
--
ALTER TABLE `articoli`
  MODIFY `art_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  MODIFY `cro_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `giochiaffiliati`
--
ALTER TABLE `giochiaffiliati`
  MODIFY `gio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `recensione`
--
ALTER TABLE `recensione`
  MODIFY `rec_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `segnalibri`
--
ALTER TABLE `segnalibri`
  MODIFY `seg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `tipologie`
--
ALTER TABLE `tipologie`
  MODIFY `tip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `ute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `articoli`
--
ALTER TABLE `articoli`
  ADD CONSTRAINT `art_gio_id` FOREIGN KEY (`art_gio_id`) REFERENCES `giochiaffiliati` (`gio_id`),
  ADD CONSTRAINT `art_tip_id` FOREIGN KEY (`art_tip_id`) REFERENCES `tipologie` (`tip_id`),
  ADD CONSTRAINT `art_ute_id` FOREIGN KEY (`art_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Limiti per la tabella `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  ADD CONSTRAINT `cro_art_id` FOREIGN KEY (`cro_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `cro_rec_id` FOREIGN KEY (`cro_rec_id`) REFERENCES `recensione` (`rec_id`),
  ADD CONSTRAINT `cro_ute_id` FOREIGN KEY (`cro_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Limiti per la tabella `recensione`
--
ALTER TABLE `recensione`
  ADD CONSTRAINT `rec_art_id` FOREIGN KEY (`rec_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `rec_ute_id` FOREIGN KEY (`rec_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Limiti per la tabella `segnalibri`
--
ALTER TABLE `segnalibri`
  ADD CONSTRAINT `seg_art_id` FOREIGN KEY (`seg_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `seg_ute_id` FOREIGN KEY (`seg_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Limiti per la tabella `tags_articoli`
--
ALTER TABLE `tags_articoli`
  ADD CONSTRAINT `tagarticoli_art_id` FOREIGN KEY (`art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `tagarticoli_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
