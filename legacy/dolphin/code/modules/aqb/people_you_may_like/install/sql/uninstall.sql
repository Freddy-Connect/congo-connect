SET @sPluginName = 'aqb_pyml';

DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;

DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);

DELETE FROM `sys_options` WHERE `Name` = CONCAT('permalinks_module_', @sPluginName);

SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = @sPluginName);
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryId;

DELETE FROM `sys_page_compose` WHERE `Desc` = 'People You May Like';

DROP TABLE IF EXISTS `[db_prefix]fields`;