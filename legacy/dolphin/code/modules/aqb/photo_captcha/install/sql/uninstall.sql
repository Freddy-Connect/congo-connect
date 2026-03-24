SET @sPluginName = 'aqb_photo_captcha';

DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;

DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);

DELETE FROM `sys_options` WHERE `Name` = CONCAT('permalinks_module_', @sPluginName);

DELETE FROM `sys_objects_captcha` WHERE `object` = @sPluginName;

SET @sOldCaptcha = (SELECT `Value` FROM `sys_options` WHERE `Name` = 'aqb_old_sys_captcha_default' LIMIT 1);
DELETE FROM `sys_options` WHERE `Name` = 'aqb_old_sys_captcha_default' LIMIT 1;
SET @sOldCaptcha = (IFNULL(@sOldCaptcha, (SELECT `Value` FROM `sys_options` WHERE `Name` = 'sys_captcha_default' LIMIT 1)));
UPDATE `sys_options` SET `Value` = @sOldCaptcha WHERE `Name` = 'sys_captcha_default' LIMIT 1;

DROP TABLE IF EXISTS `[db_prefix]questions`;