DROP TABLE IF EXISTS `ACL`;
CREATE TABLE `ACL` (`id` int(11) NOT NULL auto_increment, PRIMARY KEY (`id`), `model` varchar(255) NOT NULL, `model_id` int(11) NULL, `uid` int(11) NOT NULL, `gid` int(11) NOT NULL, `permission` varchar(255) NOT NULL);
DROP TABLE IF EXISTS `Ban`;
CREATE TABLE `Ban` (`id` int(11) NOT NULL auto_increment, PRIMARY KEY (`id`), `type` int(1) NOT NULL, `value` varchar(40) NULL, `reason` varchar(255) NULL);
DROP TABLE IF EXISTS `Cron`;
CREATE TABLE `Cron` (`id` int(11) NOT NULL auto_increment, PRIMARY KEY (`id`), `name` varchar(255) NOT NULL, `module` varchar(100) NOT NULL, `command` varchar(100) NOT NULL, `minute` char(2) NOT NULL, `hour` char(2) NOT NULL, `day` char(2) NOT NULL, `week` char(2) NOT NULL, `month` char(2) NOT NULL, `last_ran` int(10) NULL, `next_run` int(10) NULL);
DROP TABLE IF EXISTS `Group_Members`;
CREATE TABLE `Group_Members` (`gid` int(11) NOT NULL, PRIMARY KEY (`gid`), `uid` int(11) NOT NULL, PRIMARY KEY (`uid`));
DROP TABLE IF EXISTS `Groups`;
CREATE TABLE `Groups` (`id` int(11) NOT NULL auto_increment, PRIMARY KEY (`id`), `group_name` varchar(20) NULL);
INSERT INTO Groups VALUES('1', 'Administrators');
DROP TABLE IF EXISTS `Persistent_Sessions`;
CREATE TABLE `Persistent_Sessions` (`user_email` varchar(255) NOT NULL, `created` int(11) NOT NULL, `series` char(128) NOT NULL, `token` char(128) NOT NULL, PRIMARY KEY (`token`));
DROP TABLE IF EXISTS `Sessions`;
CREATE TABLE `Sessions` (`id` varchar(32) NOT NULL, PRIMARY KEY (`id`), `session_data` longtext NULL, `access` int(10) NULL, `logged_in` int(11) NULL);
DROP TABLE IF EXISTS `Site_Stats_History`;
CREATE TABLE `Site_Stats_History` (`id` int(11) NOT NULL auto_increment, PRIMARY KEY (`id`), `timestamp` int(11) NOT NULL, `stats` text NOT NULL);
DROP TABLE IF EXISTS `User_Links`;
CREATE TABLE `User_Links` (`uid` int(11) NOT NULL, PRIMARY KEY (`uid`), `link` varchar(32) NOT NULL, PRIMARY KEY (`link`), `link_type` tinyint(2) NOT NULL, PRIMARY KEY (`link_type`), `link_timestamp` int(11) NOT NULL);
DROP TABLE IF EXISTS `User_Login_History`;
CREATE TABLE `User_Login_History` (`id` int(11) NOT NULL auto_increment, PRIMARY KEY (`id`), `uid` int(11) NOT NULL, `timestamp` int(11) NOT NULL, `type` tinyint(1) NOT NULL, `ip` varchar(15) NOT NULL);
DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (`uid` int(11) NOT NULL auto_increment, PRIMARY KEY (`uid`), `user_password` char(128) NOT NULL, `user_email` varchar(255) NOT NULL, `first_name` varchar(15) NULL, `last_name` varchar(15) NULL, `ip` varchar(16) NOT NULL, `registered_timestamp` int(11) NOT NULL DEFAULT '0', `enabled` int(1) NULL DEFAULT '0', `time_zone` varchar(20) NULL DEFAULT 'America/Chicago', `profile_picture` int(1) NOT NULL);