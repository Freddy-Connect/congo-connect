SET @sPluginName = 'aqb_advanced_search_form';

DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;

DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);

DELETE FROM `sys_options` WHERE `Name` = CONCAT('permalinks_module_', @sPluginName);

DROP TABLE `[db_prefix]properties`;