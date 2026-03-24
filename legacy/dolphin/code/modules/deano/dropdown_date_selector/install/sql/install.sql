
-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Deano - Dropdown Date Selector', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('deano_dropdown_date_selector_permalinks', 'on', 26, 'Enable friendly permalinks in Dropdown Date Selector', 'checkbox', '', '', '0', ''),
('deano_dropdown_date_selector_date_order', 'Month-Day-Year', @iCategId, 'Order of date selectors', 'select', '', '', '1', 'Month-Day-Year,Year-Month-Day,Day-Month-Year'),
('deano_dropdown_date_selector_enable_join', 'on', @iCategId, 'Enable selectors on join form', 'checkbox', '', '', '2', ''),
('deano_dropdown_date_selector_enable_edit', 'on', @iCategId, 'Enable selectors on profile edit', 'checkbox', '', '', '3', '');

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=dropdown_date_selector/', 'm/dropdown_date_selector/', 'deano_dropdown_date_selector_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'Dropdown Date Selector', '_deano_dropdown_date_selector', '{siteUrl}modules/?r=dropdown_date_selector/administration/', 'Dropdown Date Selector by Deano', 'modules/deano/dropdown_date_selector/|icon.png', @iMax+1);

-- Alerts
INSERT INTO `sys_alerts_handlers` SET `name`  = 'dropdown_date_selector', `class` = '', `file` = '', `eval`  = "BxDolService::call('dropdown_date_selector', 'get_script', array($this));";
SET @iHandlerId := (SELECT `id` FROM `sys_alerts_handlers`  WHERE `name`  =  'dropdown_date_selector');
INSERT INTO `sys_alerts` SET `unit` = 'profile', `action` = 'show_profile_form', `handler_id` = @iHandlerId;

