SET @sPluginName = 'aqb_automailer';

DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;

DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);

DELETE FROM `sys_options` WHERE `Name` = CONCAT('permalinks_module_', @sPluginName);

DELETE FROM `sys_cron_jobs` WHERE `name` = @sPluginName;

SELECT @iHandlerId:=`id` FROM `sys_alerts_handlers` WHERE `name`='aqb_automailer' LIMIT 1;
DELETE FROM `sys_alerts_handlers` WHERE `name`='aqb_automailer' LIMIT 1;
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;

DROP TABLE `[db_prefix]mails`;