-- create tables  
CREATE TABLE IF NOT EXISTS `[db_prefix]page_content` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `language` int(10) NOT NULL, 
  `page` varchar(100) NOT NULL,
  `identifier` varchar(100) NOT NULL, 
  `title` varchar(100) NOT NULL,
  `uri` varchar(100) NOT NULL,
  `desc` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE `[db_prefix]site_stat_archive` (
  `ID` tinyint(4) unsigned NOT NULL auto_increment,
  `Name` varchar(20) NOT NULL default '',
  `Title` varchar(50) NOT NULL default '',
  `UserLink` varchar(255) NOT NULL default '',
  `UserQuery` varchar(255) NOT NULL default '',
  `AdminLink` varchar(255) NOT NULL default '',
  `AdminQuery` varchar(255) NOT NULL default '',
  `IconName` varchar(50) NOT NULL default '',
  `StatOrder` int(4) unsigned NOT NULL default '0',
  `Date` int(5) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]sitemap_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `priority` varchar(5) NOT NULL DEFAULT '0.6',
  `changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never','auto') NOT NULL DEFAULT 'auto',
  `class_name` varchar(255) NOT NULL,
  `class_file` varchar(255) NOT NULL,
  `order` tinyint(4) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `date` int(5) NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `object` (`object`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


 -- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=atool/', 'm/atool/', 'modzzz_atool_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('ATool', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_atool_permalinks', 'on', 26, 'Enable friendly permalinks in Admin Tools', 'checkbox', '', '', '0', ''),
('modzzz_atool_refresh_language', 'no', @iCategId, 'Refresh Pages if new language is added', 'select', '', '', '0', 'no,yes');
  
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_atool', '_modzzz_atool', '{siteUrl}modules/?r=atool/administration/', 'Admin Tools module by Modzzz','wrench', @iMax+1);
 


-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_advice', 'Advice Page', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_about', 'About Page', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_faq', 'FAQ Page', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_terms', 'Terms Page', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_help', 'Help Page', @iMaxOrder+5);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_contact', 'Contact Page', @iMaxOrder+6);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_privacy', 'Privacy Page', @iMaxOrder+7);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_atool_chat_rules', 'Chat rules Page', @iMaxOrder+8);
 
 
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
	('modzzz_atool_about', '1140px', 'About block', '_About Us', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0'),    
	('modzzz_atool_advice', '1140px', 'Advice block', '_ADVICE_H1', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0'),    
	('modzzz_atool_faq', '1140px', 'FAQ block', '_FAQ_H1', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0'),    
	('modzzz_atool_terms', '1140px', 'Terms block', '_TERMS_OF_USE_H1', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0'),    
	('modzzz_atool_help', '1140px', 'Help block', '_HELP_H1', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0'),    
	('modzzz_atool_contact', '1140px', 'Contact block', '_CONTACT_H1', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0'),    
	('modzzz_atool_privacy', '1140px', 'Privacy block', '_PRIVACY_H1', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0'),    
	('modzzz_atool_chat_rules', '1140px', 'Chat rules block', '_chat_page_rules_caption', '2', '0', 'Desc', '', '1', '100', 'non,memb', '0');    
  
  
SET @iMaxOrder = IFNULL((SELECT `Order` + 1 FROM `sys_objects_actions` WHERE `Type`='Profile' ORDER BY `Order` DESC LIMIT 1),1); 
INSERT INTO `sys_objects_actions` ( `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES 
( '{evalResult}', 'envelope', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', site_url+''modules/?r=atool/send_confirmation/{ID}'', false, ''post'');return false;',  '$oModule = BxDolModule::getInstance(''BxAToolModule''); return $oModule->isProfileUnconfirmed({ID});', @iMaxOrder, 'Profile', 0);  
     
 
-- SET @iMaxOrder = IFNULL((SELECT `Order` + 1 FROM `sys_objects_actions` WHERE `Type`='Profile' ORDER BY `Order` DESC LIMIT 1),1); 
-- INSERT INTO `sys_objects_actions` ( `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES 
-- ( '{evalResult}', 'key', 'modules/modzzz/atool/member.php?ID={ID}', '',  '$oModule = BxDolModule::getInstance(''BxAToolModule''); if (({ID} != {member_id}) && $oModule->isAdmin())\r\nreturn _t(''_modzzz_atool_action_login_as_member'');', @iMaxOrder, 'Profile', 0);   


SET @iMaxOrder = IFNULL((SELECT `Order` + 1 FROM `sys_objects_actions` WHERE `Type`='Profile' ORDER BY `Order` DESC LIMIT 1),1); 
INSERT INTO `sys_objects_actions` ( `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES 
( '{evalResult}', 'wrench', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', site_url+''modules/?r=atool/make_admin/{ID}'', false, ''post'');return false;',  '$oModule = BxDolModule::getInstance(''BxAToolModule''); return $oModule->isActionAdmin({ID});', @iMaxOrder, 'Profile', 0);  
