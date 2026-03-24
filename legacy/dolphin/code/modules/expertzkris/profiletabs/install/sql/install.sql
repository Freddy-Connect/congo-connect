CREATE TABLE IF NOT EXISTS `rw_ptabs_profile_tabs` (
  `profile_tabs_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_tabs_block_id` int(11) NOT NULL,
  `profile_tabs_order` int(11) NOT NULL,
  `profile_tabs_status` int(11) NOT NULL,
  `profile_tabs_type` varchar(20) NOT NULL,
  `profile_tabs_default` int(11) NOT NULL,
  PRIMARY KEY (`profile_tabs_id`)
) ;

CREATE TABLE IF NOT EXISTS `rw_ptabs_tabs` (
  `tabs_id` int(11) NOT NULL AUTO_INCREMENT,
  `tabs_caption` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `tabs_order` int(11) NOT NULL,
  PRIMARY KEY (`tabs_id`)
) ;


SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');

INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'Profile Tabs', 'rw_ptabs_profiletabs_admin', '{siteUrl}modules/?r=ptabs/administration/', 'Profile Tabs module from Raywintdesigns.com (bullblast)', 'list-ol', '', '', @iMax);

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Profile Tabs', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('profile_tabs_number', '5', @iCategId, 'How many profile tabs to show', 'digit', '', '', 1, '');

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) VALUES
(NULL, 'profile', '1140px', 'Custom Profile Tabs', 'rw_ptabs_profiletabs', 2, 0, 'PHP', 'return BxDolService::call(''ptabs'', ''profile_tab_custom'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0, 0);