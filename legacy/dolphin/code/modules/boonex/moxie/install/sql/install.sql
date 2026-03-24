SET @sModuleName = 'bx_moxie';

-- Options
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('permalinks_module_moxie', 'on', 26, 'Enable friendly moxie permalink', 'checkbox', '', '', 0);


-- Links
INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
('modules/?r=moxie/', 'm/moxie/', 'permalinks_module_moxie');


-- Alerts
INSERT INTO `sys_alerts_handlers`(`name`, `class`, `file`, `eval`) VALUES 
(@sModuleName, '', '', 'BxDolService::call(\'moxie\', \'response\', array($this));');
SET @iHandlerId = LAST_INSERT_ID();

INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
('system', 'attach_editor', @iHandlerId);
