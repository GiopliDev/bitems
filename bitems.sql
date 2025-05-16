-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 01:12 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `articoli`
--

CREATE TABLE `articoli` (
  `art_id` int(11) NOT NULL,
  `art_titolo` varchar(50) NOT NULL,
  `art_qtaDisp` int(11) NOT NULL DEFAULT '1',
  `art_prezzoUnitario` float NOT NULL,
  `art_descrizione` varchar(500) NOT NULL,
  `art_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `art_status` enum('D','E','N') NOT NULL COMMENT 'disponibile,esaurito,non visibile',
  `art_tip_id` int(11) NOT NULL,
  `art_ute_id` int(11) NOT NULL,
  `art_gio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `articoli`
--

INSERT INTO `articoli` (`art_id`, `art_titolo`, `art_qtaDisp`, `art_prezzoUnitario`, `art_descrizione`, `art_timestamp`, `art_status`, `art_tip_id`, `art_ute_id`, `art_gio_id`) VALUES
(1, 'Easter Egg Launcher LVL 144', 10, 0.5, 'The special weapon added in the latest STW update.', '2025-05-16 09:46:30', 'D', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `cht_id` int(11) NOT NULL,
  `cht_creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cht_ute_id1` int(11) NOT NULL,
  `cht_ute_id2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cronologiaacquisti`
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
-- Table structure for table `giochiaffiliati`
--

CREATE TABLE `giochiaffiliati` (
  `gio_id` int(11) NOT NULL,
  `gio_nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `giochiaffiliati`
--

INSERT INTO `giochiaffiliati` (`gio_id`, `gio_nome`) VALUES
(1, 'Fortnite'),
(2, 'Minecraft'),
(3, 'CS:GO'),
(4, 'Path Of Exile 2'),
(5, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `messaggi`
--

CREATE TABLE `messaggi` (
  `mes_id` int(11) NOT NULL,
  `mes_content` varchar(255) NOT NULL,
  `mes_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mes_ute_id` int(11) NOT NULL,
  `mes_chat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `recensioni`
--

CREATE TABLE `recensioni` (
  `rec_id` int(11) NOT NULL,
  `rec_art_id` int(11) NOT NULL,
  `rec_ute_id` int(11) NOT NULL,
  `rec_voto` enum('0','1') NOT NULL,
  `rec_dex` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `segnalibri`
--

CREATE TABLE `segnalibri` (
  `seg_id` int(11) NOT NULL,
  `seg_ute_id` int(11) NOT NULL,
  `seg_art_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `tag_nome` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `tag_nome`) VALUES
(1, 'Limited Edition'),
(2, 'Easter');

-- --------------------------------------------------------

--
-- Table structure for table `tags_articoli`
--

CREATE TABLE `tags_articoli` (
  `art_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tags_articoli`
--

INSERT INTO `tags_articoli` (`art_id`, `tag_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tipologie`
--

CREATE TABLE `tipologie` (
  `tip_id` int(11) NOT NULL,
  `tip_nome` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tipologie`
--

INSERT INTO `tipologie` (`tip_id`, `tip_nome`) VALUES
(1, 'Item'),
(2, 'Code'),
(5, 'Account'),
(6, 'Cosmetic'),
(9, 'Boosting Service');

-- --------------------------------------------------------

--
-- Table structure for table `utenti`
--

CREATE TABLE `utenti` (
  `ute_id` int(11) NOT NULL,
  `ute_nome` varchar(50) NOT NULL,
  `ute_cognome` varchar(50) NOT NULL,
  `ute_username` varchar(25) NOT NULL,
  `ute_email` varchar(60) NOT NULL,
  `ute_rep` int(11) NOT NULL DEFAULT '0',
  `ute_saldo` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `utenti`
--

INSERT INTO `utenti` (`ute_id`, `ute_nome`, `ute_cognome`, `ute_username`, `ute_email`, `ute_rep`, `ute_saldo`) VALUES
(1, 'Gabriele', 'Bondoni', 'Giopli', '', 78, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articoli`
--
ALTER TABLE `articoli`
  ADD PRIMARY KEY (`art_id`),
  ADD KEY `art_gio_id` (`art_gio_id`),
  ADD KEY `art_tip_id` (`art_tip_id`),
  ADD KEY `art_ute_id` (`art_ute_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`cht_id`),
  ADD KEY `cht_ute_id2` (`cht_ute_id2`),
  ADD KEY `cht_ute_id1` (`cht_ute_id1`);

--
-- Indexes for table `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  ADD PRIMARY KEY (`cro_id`),
  ADD KEY `cro_art_id` (`cro_art_id`),
  ADD KEY `cro_rec_id` (`cro_rec_id`),
  ADD KEY `cro_ute_id` (`cro_ute_id`);

--
-- Indexes for table `giochiaffiliati`
--
ALTER TABLE `giochiaffiliati`
  ADD PRIMARY KEY (`gio_id`);

--
-- Indexes for table `messaggi`
--
ALTER TABLE `messaggi`
  ADD PRIMARY KEY (`mes_id`),
  ADD KEY `mes_chat_id` (`mes_chat_id`),
  ADD KEY `mes_ute_id` (`mes_ute_id`);

--
-- Indexes for table `recensioni`
--
ALTER TABLE `recensioni`
  ADD PRIMARY KEY (`rec_id`),
  ADD KEY `rec_art_id` (`rec_art_id`),
  ADD KEY `rec_ute_id` (`rec_ute_id`);

--
-- Indexes for table `segnalibri`
--
ALTER TABLE `segnalibri`
  ADD PRIMARY KEY (`seg_id`),
  ADD KEY `seg_art_id` (`seg_art_id`),
  ADD KEY `seg_ute_id` (`seg_ute_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `tags_articoli`
--
ALTER TABLE `tags_articoli`
  ADD KEY `tagarticoli_art_id` (`art_id`),
  ADD KEY `tagarticoli_tag_id` (`tag_id`);

--
-- Indexes for table `tipologie`
--
ALTER TABLE `tipologie`
  ADD PRIMARY KEY (`tip_id`);

--
-- Indexes for table `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`ute_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articoli`
--
ALTER TABLE `articoli`
  MODIFY `art_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `cht_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  MODIFY `cro_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `giochiaffiliati`
--
ALTER TABLE `giochiaffiliati`
  MODIFY `gio_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `messaggi`
--
ALTER TABLE `messaggi`
  MODIFY `mes_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `recensioni`
--
ALTER TABLE `recensioni`
  MODIFY `rec_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `segnalibri`
--
ALTER TABLE `segnalibri`
  MODIFY `seg_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tipologie`
--
ALTER TABLE `tipologie`
  MODIFY `tip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `utenti`
--
ALTER TABLE `utenti`
  MODIFY `ute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `articoli`
--
ALTER TABLE `articoli`
  ADD CONSTRAINT `art_gio_id` FOREIGN KEY (`art_gio_id`) REFERENCES `giochiaffiliati` (`gio_id`),
  ADD CONSTRAINT `art_tip_id` FOREIGN KEY (`art_tip_id`) REFERENCES `tipologie` (`tip_id`),
  ADD CONSTRAINT `art_ute_id` FOREIGN KEY (`art_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chat_ute_id1` FOREIGN KEY (`cht_ute_id1`) REFERENCES `utenti` (`ute_id`),
  ADD CONSTRAINT `chat_ute_id2` FOREIGN KEY (`cht_ute_id2`) REFERENCES `utenti` (`ute_id`);

--
-- Constraints for table `cronologiaacquisti`
--
ALTER TABLE `cronologiaacquisti`
  ADD CONSTRAINT `cro_art_id` FOREIGN KEY (`cro_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `cro_rec_id` FOREIGN KEY (`cro_rec_id`) REFERENCES `recensioni` (`rec_id`),
  ADD CONSTRAINT `cro_ute_id` FOREIGN KEY (`cro_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Constraints for table `recensioni`
--
ALTER TABLE `recensioni`
  ADD CONSTRAINT `rec_art_id` FOREIGN KEY (`rec_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `rec_ute_id` FOREIGN KEY (`rec_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Constraints for table `segnalibri`
--
ALTER TABLE `segnalibri`
  ADD CONSTRAINT `seg_art_id` FOREIGN KEY (`seg_art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `seg_ute_id` FOREIGN KEY (`seg_ute_id`) REFERENCES `utenti` (`ute_id`);

--
-- Constraints for table `tags_articoli`
--
ALTER TABLE `tags_articoli`
  ADD CONSTRAINT `tagarticoli_art_id` FOREIGN KEY (`art_id`) REFERENCES `articoli` (`art_id`),
  ADD CONSTRAINT `tagarticoli_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
