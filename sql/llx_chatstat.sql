-- phpMyAdmin SQL Dump
-- version 4.4.15.1
-- http://www.phpmyadmin.net
--
-- Host: Portal.backbone.co.at:3306
-- Erstellungszeit: 25. Mrz 2016 um 15:01
-- Server-Version: 5.5.47-log
-- PHP-Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `sh006vfr_dolibarr_bb32`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `llx_chatstat`
--

CREATE TABLE IF NOT EXISTS `llx_chatstat` (
  `rowid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `checks` int(11) NOT NULL,
  `online` int(11) NOT NULL,
  `last_stat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `llx_chatstat`
--
ALTER TABLE `llx_chatstat`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `rowid` (`rowid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `llx_chatstat`
--
ALTER TABLE `llx_chatstat`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;