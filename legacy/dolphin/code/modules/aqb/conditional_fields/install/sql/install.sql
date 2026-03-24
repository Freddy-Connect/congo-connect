SET @sPluginName = 'aqb_conditional_fields';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_conditional_fields', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing Conditional Fields module', 'list-alt', '', '', @iOrder + 1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for Conditional Fields module', 'checkbox', '', '', 0);

CREATE TABLE IF NOT EXISTS `[db_prefix]data` (
  `field` varchar(64) NOT NULL,
  `depends_on` varchar(64) NOT NULL,
  `show_if_value` varchar(64) NOT NULL,
  PRIMARY KEY (`field`, `depends_on`, `show_if_value`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;