SET @sPluginName = 'aqb_advanced_search_form';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_advanced_search_form', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing Advanced Search Form module', 'list-alt col-red2', '', '', @iOrder+1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for Advanced Search Form module', 'checkbox', '', '', 0);

CREATE TABLE `[db_prefix]properties` (
`FieldID` INT,
`Control` ENUM('default', 'select_box', 'select_multiple', 'checkbox_set') DEFAULT 'default',
`ShowEmptyValue` TINYINT(1) DEFAULT 0,
PRIMARY KEY (`FieldID`)
);