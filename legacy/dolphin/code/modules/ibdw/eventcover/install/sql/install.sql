SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');

INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'Event Cover', 'Event Cover', '{siteUrl}modules/?r=eventcover/administration/', 'Event Cover - Settings', 'diamond', '', '', @iExtOrd+1);

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Event Cover', @iMaxOrder);

SET @iKategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` SET   `Name` = 'KeyCodeEV',   `kateg` = @iKategId,   `desc`  = 'License Key (Activation code)<br><a target="_blank" href="ibdw/eventcover/activation.php">Click here to get the code</a>',   `Type`  = 'digit',   `VALUE` = '',   `order_in_kateg` = 1;
INSERT INTO `sys_options` SET   `Name` = 'AlbumCoverNameEV',   `kateg` = @iKategId,   `desc`  = 'Name for the Event Cover Album',   `Type`  = 'digit',   `VALUE` = 'Event Cover',   `order_in_kateg` = 2;
INSERT INTO `sys_options` SET   `Name` = 'DisplaytitleEV',   `kateg` = @iKategId,   `desc`  = 'Display the Event Title',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 3;
INSERT INTO `sys_options` SET   `Name` = 'DisplayauthorEV',   `kateg` = @iKategId,   `desc`  = 'Display the Author Name',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 4;
INSERT INTO `sys_options` SET   `Name` = 'maxfilesizeEV',   `kateg` = @iKategId,   `desc`  = 'Max image size (MB)',   `Type`  = 'digit',   `VALUE` = '1',   `order_in_kateg` = 5;
INSERT INTO `sys_options` SET   `Name` = 'eventmoduleis',   `kateg` = @iKategId,   `desc`  = 'Events Module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 6,   `AvailableValues` ='Boonex,UE30,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'xyfactorEV',   `kateg` = @iKategId,   `desc`  = 'Aspect Ration (X/Y)',   `Type`  = 'digit',   `VALUE` = '0.35',   `order_in_kateg` = 7;

CREATE TABLE IF NOT EXISTS `eventcover_code_reminder` (`id` INT NOT NULL PRIMARY KEY ,`addressr` VARCHAR( 100 ) NOT NULL,`website` VARCHAR( 200 ) NOT NULL) ENGINE = MYISAM;

INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`) VALUES (NULL , 'bx_events_view', '', 'The Events page Cover', '_ibdw_eventcover_modulename', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/eventcover/cover.php'');', '0', '0', 'memb', '0');
INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`) VALUES (NULL , 'ue30_event_view', '', 'The Events page Cover', '_ibdw_eventcover_modulename', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/eventcover/cover.php'');', '0', '0', 'memb', '0');

CREATE TABLE IF NOT EXISTS `ibdw_event_cover` (`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`Owner` INT( 11 ) NOT NULL ,`Hash` VARCHAR( 32 ) NOT NULL ,`PositionY` smallint(6) NOT NULL DEFAULT '0' , `PositionX` smallint(6) NOT NULL DEFAULT '0', `Uri` varchar(255) NOT NULL default '', `width` smallint(6)) ENGINE=MyISAM;

INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES ( 'eventcover', '0 0 * * *', 'eventcoverCron', 'modules/ibdw/eventcover/classes/eventcoverCron.php', '');


