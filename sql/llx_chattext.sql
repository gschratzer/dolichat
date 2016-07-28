CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chattext` (
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

ALTER TABLE `" . MAIN_DB_PREFIX . "chattext`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `UserID` (`user_id`,`privat`),
  ADD KEY `GesID` (`user_id`,`privat`,`gesehen`),
  ADD KEY `RowidID` (`rowid`) USING BTREE,
  ADD KEY `UserIN` (`user_id`),
  ADD KEY `PrivatIN` (`privat`);

ALTER TABLE `" . MAIN_DB_PREFIX . "chattext`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `" . MAIN_DB_PREFIX . "chattext` ADD `chattextblob` blob NOT NULL;