-- create tables 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]archive` ( 
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Caption` varchar(100) NOT NULL,
  `Icon` varchar(100) NOT NULL,
  `Url` varchar(250) NOT NULL,
  `Script` varchar(250) NOT NULL,
  `Eval` text NOT NULL,
  `Order` int(5) NOT NULL,
  `Type` varchar(20) NOT NULL,
  `bDisplayInSubMenuHeader` tinyint(1) NOT NULL default '0',
  `Date` int(5) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
 -- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=manager/', 'm/manager/', 'modzzz_manager_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Manager', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_manager_permalinks', 'on', 26, 'Enable friendly permalinks in Manager', 'checkbox', '', '', '0', '');
  
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_manager', '_modzzz_manager', '{siteUrl}modules/?r=manager/administration/', 'Actions Manager module by Modzzz','wrench', @iMax+1);
 