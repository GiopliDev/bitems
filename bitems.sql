-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 21, 2025 alle 20:03
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

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
  `art_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `art_isPrivato` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'non visibile',
  `art_tip_id` int(11) NOT NULL,
  `art_ute_id` int(11) NOT NULL,
  `art_gio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `articoli`
--

INSERT INTO `articoli` (`art_id`, `art_titolo`, `art_qtaDisp`, `art_prezzoUnitario`, `art_descrizione`, `art_timestamp`, `art_isPrivato`, `art_tip_id`, `art_ute_id`, `art_gio_id`) VALUES
(1, 'Easter Egg Launcher LVL 144', 10, 0.5, 'The special weapon added in the latest STW update.', '2025-05-16 09:46:30', 0, 1, 1, 1),
(2, 'Scavafosse 144 Glitched', 34, 12, 'Scavafosse con 2 attributi uguali a livello massimo e durabilità full.', '2025-05-19 18:17:58', 0, 1, 2, 1),
(3, 'asdasd', 1, 0.01, 'asdasd', '2025-05-21 16:51:45', 0, 5, 1, 3),
(4, 'asdasd', 1, 0.01, 'asdasd', '2025-05-21 16:51:57', 0, 5, 1, 3),
(5, 'asdasd', 13, 0.01, 'asdasd', '2025-05-21 16:53:13', 0, 5, 1, 3),
(6, 'asdasd', 1, 0.01, 'asdasd', '2025-05-21 16:56:01', 0, 5, 1, 3),
(7, 'asdasd', 1, 0.01, 'asdasd', '2025-05-21 16:56:52', 0, 6, 1, 3),
(8, 'asdasd', 1, 0.01, 'asdasd', '2025-05-21 16:57:23', 0, 6, 1, 3),
(9, 'Test', 112, 0.42, 'dexscrizione csgo', '2025-05-21 16:59:27', 0, 5, 1, 3),
(10, '10px Danger Zone Cases W/O Keys', 99, 5.45, '10 cases without keys', '2025-05-21 17:00:32', 0, 6, 1, 3),
(11, 'vsdvsdv', 0, 0.2, 'sdvsdvsdv', '2025-05-21 17:01:10', 0, 5, 1, 3),
(12, '50x Exalted Orbs', 10, 0.34, 'instant delivery', '2025-05-21 17:47:32', 0, 1, 1, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `chats`
--

CREATE TABLE `chats` (
  `cht_id` int(11) NOT NULL,
  `cht_creationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `cht_ute_id1` int(11) NOT NULL,
  `cht_ute_id2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `cro_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `cro_rec_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cronologiaacquisti`
--

INSERT INTO `cronologiaacquisti` (`cro_id`, `cro_art_id`, `cro_ute_id`, `cro_qta`, `cro_prezzoFinale`, `cro_timestamp`, `cro_rec_id`) VALUES
(1, 2, 1, 1, 12, '2025-05-21 17:34:14', 2),
(2, 10, 1, 1, 5.45, '2025-05-21 17:43:03', 3),
(3, 11, 1, 1, 0.2, '2025-05-21 17:50:17', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `giochiaffiliati`
--

CREATE TABLE `giochiaffiliati` (
  `gio_id` int(11) NOT NULL,
  `gio_nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Struttura della tabella `images`
--

CREATE TABLE `images` (
  `img_id` int(11) NOT NULL,
  `img_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `images`
--

INSERT INTO `images` (`img_id`, `img_url`) VALUES
(1, 'default.jpg'),
(2, 'img_682dfa69b3175.png'),
(3, 'img_682dfad636146.png'),
(4, 'img_682dfb49bcf9f.png'),
(5, 'img_682e0053e46f2.jpg'),
(6, 'img_682e02d921105.png'),
(7, 'img_682e036cc0e4f.png'),
(8, 'img_682e0449f389e.png'),
(9, 'img_682e04ad49ab7.png'),
(10, 'img_682e04f90d84f.jpg'),
(11, 'img_682e05a17cdc5.png'),
(12, 'img_682e05a17cdc5.png'),
(13, 'img_682e05d4344dd.png'),
(14, 'img_682e05d4344dd.png'),
(15, 'img_682e05f3c0c86.png'),
(16, 'img_682e05f3c0c86.png'),
(17, 'img_682e066fb311b.png'),
(18, 'img_682e066fb311b.png'),
(19, 'img_682e06b08457d.png'),
(20, 'img_682e06b08457d.png'),
(21, 'img_682e06d6b53df.jpg'),
(22, 'img_682e06d6b53df.jpg'),
(23, 'img_682e11b4c6dec.png'),
(24, 'img_682e11b4c6dec.png');

-- --------------------------------------------------------

--
-- Struttura della tabella `images_articoli`
--

CREATE TABLE `images_articoli` (
  `img_art_id` int(11) NOT NULL,
  `img_id` int(11) NOT NULL,
  `art_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `images_articoli`
--

INSERT INTO `images_articoli` (`img_art_id`, `img_id`, `art_id`) VALUES
(1, 18, 9),
(2, 20, 10),
(3, 22, 11),
(4, 24, 12);

-- --------------------------------------------------------

--
-- Struttura della tabella `messaggi`
--

CREATE TABLE `messaggi` (
  `mes_id` int(11) NOT NULL,
  `mes_content` varchar(255) NOT NULL,
  `mes_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `mes_ute_id` int(11) NOT NULL,
  `mes_chat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `recensioni`
--

CREATE TABLE `recensioni` (
  `rec_id` int(11) NOT NULL,
  `rec_art_id` int(11) NOT NULL,
  `rec_ute_id` int(11) NOT NULL,
  `rec_voto` enum('0','1') NOT NULL,
  `rec_dex` varchar(255) NOT NULL,
  `rec_timestamp` int(11) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `recensioni`
--

INSERT INTO `recensioni` (`rec_id`, `rec_art_id`, `rec_ute_id`, `rec_voto`, `rec_dex`, `rec_timestamp`) VALUES
(1, 1, 1, '1', 'Velocissimo!', 2147483647),
(2, 2, 1, '0', 'mi ha scammato', 2147483647),
(3, 10, 1, '0', 'ho ordinato 10 casse e me ne sono arrivate 8', 2147483647),
(4, 11, 1, '0', 'c\'era tutto,addirittura una spada 2YA', 2147483647);

-- --------------------------------------------------------

--
-- Struttura della tabella `segnalibri`
--

CREATE TABLE `segnalibri` (
  `seg_id` int(11) NOT NULL,
  `seg_ute_id` int(11) NOT NULL,
  `seg_art_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_nome`) VALUES
(1, 'Limited Edition'),
(2, 'Easter'),
(3, 'Christmas'),
(4, 'cheap'),
(5, 'golden'),
(6, 'Limited Time'),
(7, '2b2t'),
(8, 'stash'),
(9, '2012'),
(10, 'danger zone'),
(11, 'case'),
(12, 'test'),
(13, 'currency'),
(14, 'path of exile 2');

-- --------------------------------------------------------

--
-- Struttura della tabella `tags_articoli`
--

CREATE TABLE `tags_articoli` (
  `art_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tags_articoli`
--

INSERT INTO `tags_articoli` (`art_id`, `tag_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(3, 10),
(4, 10),
(5, 10),
(6, 10),
(7, 10),
(8, 10),
(9, 10),
(10, 10),
(10, 11),
(10, 4),
(11, 12),
(12, 13),
(12, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `tipologie`
--

CREATE TABLE `tipologie` (
  `tip_id` int(11) NOT NULL,
  `tip_nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `ute_password` varchar(100) NOT NULL,
  `ute_dex` varchar(500) NOT NULL DEFAULT 'Questa è la descrizione standard!',
  `ute_img_id` int(11) NOT NULL DEFAULT 1,
  `ute_rep` int(11) NOT NULL DEFAULT 0,
  `ute_saldo` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`ute_id`, `ute_nome`, `ute_cognome`, `ute_username`, `ute_email`, `ute_password`, `ute_dex`, `ute_img_id`, `ute_rep`, `ute_saldo`) VALUES
(1, 'Gabriele', 'Bondoni', 'Giopli', '', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'heyaaaaa!', 5, 78, 22.35),
(2, 'Tommaso', 'Colonni', 'Araton38', 'gabriele.bondoni@gmail.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Questa è la descrizione standard!', 1, -1, 40);

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
-- Indici per le tabelle `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`cht_id`),
  ADD KEY `cht_ute_id2` (`cht_ute_id2`),
  ADD KEY `cht_ute_id1` (`cht_ute_id1`);

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
-- Indici per le tabelle `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`img_id`);

--
-- Indici per le tabelle `images_articoli`
--
ALTER TABLE `images_articoli`
  ADD PRIMARY KEY (`img_art_id`),
  ADD KEY `img_id` (`img_id`),
  ADD KEY `art_id` (`art_id`);

--
-- Indici per le tabelle `messaggi`
--
ALTER TABLE `messaggi`
  ADD PRIMARY KEY (`mes_id`),
  ADD KEY `mes_chat_id` (`mes_chat_id`),
  ADD KEY `mes_ute_id` (`mes_ute_id`);

--
-- Indici per le tabelle `recensioni`
--
ALTER TABLE `recensioni`
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
  MODIFY `art_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `chats`
--
ALTER TABLE `chats`
  MODIFY `cht_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  MODIFY `cro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `giochiaffiliati`
--
ALTER TABLE `giochiaffiliati`
  MODIFY `gio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `images`
--
ALTER TABLE `images`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT per la tabella `images_articoli`
--
ALTER TABLE `images_articoli`
  MODIFY `img_art_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `messaggi`
--
ALTER TABLE `messaggi`
  MODIFY `mes_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `recensioni`
--
ALTER TABLE `recensioni`
  MODIFY `rec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `segnalibri`
--
ALTER TABLE `segnalibri`
  MODIFY `seg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Limiti per la tabella `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chat_ute_id1` FOREIGN KEY (`cht_ute_id1`) REFERENCES `utenti` (`ute_id`),
  ADD CONSTRAINT `chat_ute_id2` FOREIGN KEY (`cht_ute_id2`) REFERENCES `utenti` (`ute_id`);

--
-- Limiti per la tabella `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  ADD CONSTRAINT `cro_art_id` FOREIGN KEY (`cro_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `cro_rec_id` FOREIGN KEY (`cro_rec_id`) REFERENCES `recensioni` (`rec_id`),
  ADD CONSTRAINT `cro_ute_id` FOREIGN KEY (`cro_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Limiti per la tabella `images_articoli`
--
ALTER TABLE `images_articoli`
  ADD CONSTRAINT `art_id` FOREIGN KEY (`art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `img_id` FOREIGN KEY (`img_id`) REFERENCES `images` (`img_id`);

--
-- Limiti per la tabella `recensioni`
--
ALTER TABLE `recensioni`
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
