SET @sPluginName = 'aqb_credits';
SET @sPluginTitle = 'Credits System';

DELETE FROM `sys_menu_admin` WHERE `name` = @sPluginName;

DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'aqb_credits_sys';
DELETE FROM `sys_page_compose` WHERE `Page` = 'aqb_credits_sys';

DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_credits_my_credits_title';

SET @iId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = @sPluginTitle);
DELETE FROM `sys_options` WHERE `kateg`= @iId OR `Name` = 'aqb_credits_actions_settings';
DELETE FROM `sys_options_cats` WHERE `name`=@sPluginTitle;

DROP TABLE IF EXISTS `[db_prefix]history`;
DROP TABLE IF EXISTS `[db_prefix]transactions`;

DELETE FROM `sys_injections` WHERE `name` = 'aqb_credits_popup';

DELETE FROM `sys_objects_actions` WHERE `Icon` = 'credit-card-alt' AND `Type` = 'Profile';

SELECT @iHandlerId:=`id` FROM `sys_alerts_handlers` WHERE `name`='aqb_credits' LIMIT 1;
DELETE FROM `sys_alerts_handlers` WHERE `name`='aqb_credits' LIMIT 1;
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;