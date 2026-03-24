SET @sPluginName = 'aqb_photo_captcha';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_photo_captcha', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing Photo Captcha module', 'lock', '', '', @iOrder + 1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for Photo Captcha module', 'checkbox', '', '', 0);

INSERT INTO `sys_objects_captcha` (`object`, `title`, `override_class_name`, `override_class_file`) VALUES
(@sPluginName, 'Photo Captcha', 'AqbPhotoCaptchaInterface', 'modules/aqb/photo_captcha/classes/AqbPhotoCaptchaInterface.php');

SET @sOldCaptcha = (SELECT `Value` FROM `sys_options` WHERE `Name` = 'sys_captcha_default' LIMIT 1);
INSERT INTO `sys_options` (`Name`, `Value`) VALUES ('aqb_old_sys_captcha_default', @sOldCaptcha);
UPDATE `sys_options` SET `Value` = @sPluginName WHERE `Name` = 'sys_captcha_default' LIMIT 1;

CREATE TABLE IF NOT EXISTS `[db_prefix]questions` (
`ID` INT NOT NULL AUTO_INCREMENT,
`Question` TEXT NOT NULL,
`ImageExt` CHAR(3) NOT NULL,
PRIMARY KEY (`ID`)
);

INSERT INTO `[db_prefix]questions` (`ID`, `Question`, `ImageExt`) VALUES
(1, 'a:1:{s:2:"en";a:2:{s:1:"q";s:44:"How many pillars do you see on this picture?";s:1:"a";s:7:"4, four";}}', 'jpg'),
(2, 'a:1:{s:2:"en";a:2:{s:1:"q";s:36:"What city this picture was taken at?";s:1:"a";s:5:"Paris";}}', 'jpg'),
(3, 'a:1:{s:2:"en";a:2:{s:1:"q";s:42:"What kind of an animal is on this picture?";s:1:"a";s:5:"Horse";}}', 'jpg');