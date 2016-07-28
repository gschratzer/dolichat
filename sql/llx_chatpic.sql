CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatpic` (
  `rowid` int(11) NOT NULL,
  `PicName` varchar(60) NOT NULL,
  `MsgID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `" . MAIN_DB_PREFIX . "chatpic`
  ADD PRIMARY KEY (`rowid`);

ALTER TABLE `" . MAIN_DB_PREFIX . "chatpic`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;