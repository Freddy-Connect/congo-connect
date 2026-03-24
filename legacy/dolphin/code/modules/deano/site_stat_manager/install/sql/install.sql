CREATE TABLE IF NOT EXISTS `dbcs_sys_stat_site` (
  `ID` tinyint(4) unsigned NOT NULL auto_increment,
  `Name` varchar(20) NOT NULL default '',
  `Title` varchar(50) NOT NULL default '',
  `UserLink` varchar(255) NOT NULL default '',
  `UserQuery` varchar(255) NOT NULL default '',
  `AdminLink` varchar(255) NOT NULL default '',
  `AdminQuery` varchar(255) NOT NULL default '',
  `IconName` varchar(50) NOT NULL default '',
  `StatOrder` int(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'SiteStatManager', '_dbcs_SM_bx_SiteStatManager', '{siteUrl}modules/?r=site_stat_manager/administration/', 'Site Stat Manager by Deano', 'modules/deano/site_stat_manager/|site_stat_manager.png', @iMax+1);

