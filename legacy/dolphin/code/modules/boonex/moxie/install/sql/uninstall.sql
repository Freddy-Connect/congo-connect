SET @sModuleName = 'bx_moxie';

-- Options
DELETE FROM `sys_options` WHERE `Name` IN ('permalinks_module_moxie');


-- Links
DELETE FROM `sys_permalinks` WHERE `check`='permalinks_module_moxie';


-- Alerts
SET @iHandlerId = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name`=@sModuleName LIMIT 1);
DELETE FROM `sys_alerts_handlers` WHERE `id`=@iHandlerId LIMIT 1;
DELETE FROM `sys_alerts` WHERE `handler_id`=@iHandlerId;
