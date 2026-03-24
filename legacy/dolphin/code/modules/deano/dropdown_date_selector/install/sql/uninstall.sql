
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Deano - Dropdown Date Selector' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'deano_dropdown_date_selector_permalinks';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=dropdown_date_selector/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'Dropdown Date Selector';

-- Alerts
SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'dropdown_date_selector');
DELETE FROM `sys_alerts_handlers` WHERE `id`  = @iHandlerId;
DELETE FROM `sys_alerts` WHERE `handler_id` =  @iHandlerId;
