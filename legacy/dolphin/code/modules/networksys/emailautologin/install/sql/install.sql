/*
	Enable / disable
*/
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('EmailAutoLogin', @iMaxOrder);

SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES('EmailAutoLogin_activated', 'on', @iCategId, 'Active', 'checkbox', '', '', 0, '');
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES('EmailAutoLogin_expiry', '+1 week', @iCategId, 'Expiry of links', 'select', 'return strlen($arg0) > 0;', 'Cannot be empty.', 1, '+1 day,+1 week,+2 weeks,+3 weeks,+4 weeks');
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES('EmailAutoLogin_hash', '', @iCategId, 'Secret key (generated on installation)', 'text', 'return strlen($arg0) > 0;', 'Cannot be empty.', 2, '');
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES('EmailAutoLogin_exclude', '', @iCategId, 'Exclude specific links if multiple seperate by comma (profile_activate.php is always excluded)', 'text', '', '', 3, '');



SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES(2, 'Emailautologin', '_ns_eal', CONCAT('{siteAdminUrl}settings.php?cat=',@iCategId), 'Emailautologin','modules/networksys/emailautologin/|icon.png', @iMax+1);
