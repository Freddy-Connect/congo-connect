SET @sPluginName = 'aqb_automailer';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_automailer', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing Automailer module', 'modules/aqb/automailer/|adm_menu_icon.png', '', '', @iOrder+1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for Automailer module', 'checkbox', '', '', 0);

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
(@sPluginName, '0 5 * * *', '', '', 'BxDolService::call(''aqb_automailer'', ''queue_emails'');');

INSERT INTO `sys_alerts_handlers`(`name`, `class`, `file`, `eval`) VALUES ('aqb_automailer', '', '', 'BxDolService::call(''aqb_automailer'', ''langid_bugfix'');');
SET @iHandlerId = LAST_INSERT_ID();
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES('system', 'design_included', @iHandlerId);


CREATE TABLE `[db_prefix]mails` (
`ID` INT NOT NULL AUTO_INCREMENT,
`Name` VARCHAR(255) NOT NULL,
`Subject` TEXT NOT NULL,
`Body` TEXT NOT NULL,
`Filter` TEXT NOT NULL,
`FilterQuery` TEXT NOT NULL,
`Schedule` TEXT NOT NULL,
`Options` TEXT NOT NULL,
`Active` BOOLEAN NOT NULL DEFAULT 1,
PRIMARY KEY (`ID`)
);