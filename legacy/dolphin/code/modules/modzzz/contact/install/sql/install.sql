-- create tables  
CREATE TABLE IF NOT EXISTS `[db_prefix]main` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL, 
  `desc` text NOT NULL, 
  `status` enum('approved','pending') NOT NULL default 'approved',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]feedback_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `subject` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL default '', 
  `email` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL, 
  `desc` text NOT NULL, 
  `created` int(11) NOT NULL,
  `department_id` int(10) unsigned NOT NULL default '0',  
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_feedback_uri` (`uri`),
  KEY `contact_feedback_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;  

CREATE TABLE IF NOT EXISTS `[db_prefix]action_types` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(50) NOT NULL,
  `field_name` varchar(255) NOT NULL, 
  `select_type` enum('text','multi-text','date','single-select','multi-select') NOT NULL default 'text',
  `compulsory` INT NOT NULL, 
  PRIMARY KEY (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8; 

CREATE TABLE IF NOT EXISTS `[db_prefix]actions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` varchar(50) NOT NULL, 
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8; 

 -- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=contact/', 'm/contact/', 'modzzz_contact_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Contact', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_contact_permalinks', 'on', 26, 'Enable friendly permalinks in Advanced Contact', 'checkbox', '', '', '0', '');
  
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_contact', '_modzzz_contact', '{siteUrl}modules/?r=contact/administration/', 'Advanced Contact module by Modzzz','envelope', @iMax+1);


-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_contact_main', 'Contact Us Page', @iMaxOrder+1);
  

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
('modzzz_contact_main', '1140px', 'Contact block', '_modzzz_contact_block_contact_us', '1', '0', 'ContactUs', '', '1', '50', 'non,memb', '0'), 
('modzzz_contact_main', '1140px', 'Departments block', '_modzzz_contact_block_departments', '2', '0', 'Departments', '', '1', '50', 'non,memb', '0'),    
('modzzz_contact_main', '1140px', 'Help block', '_modzzz_contact_block_help', '2', '1', 'Help', '', '1', '50', 'non,memb', '0');    

 

-- email templates 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_contact_msg', 'Someone sent a support message', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello</b>,</p><p>The following support request has been sent to you on <a href="<Domain>"><SiteName></a><br><br><Message><br><br>From <Name> with email <Email>.</p><bx_include_auto:_email_footer.html />', 'Contact Us notification', '0');


UPDATE `sys_menu_bottom` SET `Link` = 'm/contact/home' WHERE `Caption` = '_contact_us' AND `Link`='contact.php';


UPDATE `sys_menu_top` SET `Link` = 'm/contact/home' WHERE `Caption` = '_Contact' AND `Link`='contact.php';
