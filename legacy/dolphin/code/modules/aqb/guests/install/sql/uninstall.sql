SET @sPluginName = 'aqb_guests';


DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Caption` = '_aqb_gst_block_my_guests_profile';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Caption` = '_aqb_gst_block_my_guests_account';


SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`=@sPluginName LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`=@sPluginName LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name` IN (CONCAT('permalinks_module_', @sPluginName));


DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;


DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);