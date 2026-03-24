
-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL, 
  `link` varchar(255) NOT NULL,  
  `category` varchar(255) NOT NULL,  
  `parent_category_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `language` int(10) NOT NULL,
  `tag` varchar(255) NOT NULL,   
  `status` enum('approved','pending') NOT NULL default 'approved',  
  `fetch_count` int(11) NOT NULL, 
  `source_link` int(11) NOT NULL,   
  `owner_id` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL, 
  `publish` int(11) NOT NULL default '1',
  `when` int(11) NOT NULL default '0', 
   PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`), 
  KEY `created` (`created`)  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=rssinfo/', 'm/rssinfo/', 'modzzz_rssinfo_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('RssInfo', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_rssinfo_permalinks', 'on', 26, 'Enable friendly permalinks in rssinfo', 'checkbox', '', '', '0', ''),
('modzzz_rssinfo_use_timestamp', 'on', @iCategId, 'Use timestamp of the post being imported', 'checkbox', '', '', '0', ''), 
('modzzz_rssinfo_feed_ping', 'daily', @iCategId, 'How often should feed be updated', 'select', '', '', '0', 'hour,daily');
  
 
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_rssinfo', '_modzzz_rssinfo', '{siteUrl}modules/?r=rssinfo/administration/', 'News Rss Auto Feed module by Modzzz', 'filter', @iMax+1);
 
-- INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
-- ('modzzz_rssinfo', '*/5 * * * *', 'BxRssInfoCron', 'modules/modzzz/rssinfo/classes/BxRssInfoCron.php', '');

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('modzzz_rssinfo', '0 * * * *', 'BxRssInfoCron', 'modules/modzzz/rssinfo/classes/BxRssInfoCron.php', '');