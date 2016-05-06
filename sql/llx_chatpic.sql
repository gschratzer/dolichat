-- phpMyAdmin SQL Dump
-- version 4.4.15.1
-- http://www.phpmyadmin.net
--
-- Host: Portal.backbone.co.at:3306
-- Erstellungszeit: 25. Mrz 2016 um 15:02
-- Server-Version: 5.5.47-log
-- PHP-Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Datenbank: `sh006vfr_dolibarr_bb32`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `llx_chatpic`
--

CREATE TABLE IF NOT EXISTS `llx_chatpic` (
  `rowid` int(11) NOT NULL,
  `PicName` varchar(60) NOT NULL,
  `MsgID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `llx_chatpic`
--
ALTER TABLE `llx_chatpic`
  ADD PRIMARY KEY (`rowid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `llx_chatpic`
--
ALTER TABLE `llx_chatpic`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;