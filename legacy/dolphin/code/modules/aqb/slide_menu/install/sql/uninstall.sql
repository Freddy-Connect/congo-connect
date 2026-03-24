SET @sPluginName = 'aqb_slide_menu';

DELETE FROM `sys_menu_admin` WHERE `name` = @sPluginName;

SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Slide Menu' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;
DELETE FROM `sys_injections` WHERE `name` IN ('slide_menu_header', 'slide_menu_footer');