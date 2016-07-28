SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `" . MAIN_DB_PREFIX . "chatstat` (
  `rowid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `checks` int(11) NOT NULL,
  `online` int(11) NOT NULL,
  `last_stat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

ALTER TABLE `" . MAIN_DB_PREFIX . "chatstat`
  ADD PRIMARY KEY (`rowid`),
  ADD KEY `rowid` (`rowid`);

ALTER TABLE `" . MAIN_DB_PREFIX . "chatstat`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
