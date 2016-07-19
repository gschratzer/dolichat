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
-- Tabellenstruktur für Tabelle `llx_chattext`
--

CREATE TABLE IF NOT EXISTS `llx_chattext` (
  `rowid` int(11) NOT NULL,
  `chattext` text NOT NULL,
  `chattextblob` blob NOT NULL,
  `user` varchar(60) NOT NULL,
  `user_id` int(11) NOT NULL,
  `privat` int(11) NOT NULL,
  `privat_name` varchar(60) NOT NULL,
  `gesehen` int(11) NOT NULL DEFAULT '0',
  `gesehen_Broadcast` varchar(120) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `llx_chattext`
--
ALTER TABLE `llx_chattext`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `UserID` (`user_id`,`privat`),
  ADD KEY `GesID` (`user_id`,`privat`,`gesehen`),
  ADD KEY `RowidID` (`rowid`) USING BTREE,
  ADD KEY `UserIN` (`user_id`),
  ADD KEY `PrivatIN` (`privat`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `llx_chattext`
--
ALTER TABLE `llx_chattext`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `llx_chattext` ADD `chattextblob` blob NOT NULL;