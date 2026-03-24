SET @sPluginName = 'aqb_pyml';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_pyml', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing People You May Like module', 'users', '', '', @iOrder + 1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for People You May Like module', 'checkbox', '', '', 0);

INSERT INTO `sys_options_cats` SET `name` = @sPluginName;
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`)  VALUES
('aqb_pyml_perpage', '5', @iCategoryId, 'Show profiles per page', 'digit', '', '', 0, ''),
('aqb_pyml_date_diff', '5', @iCategoryId, 'For Date fields: Max difference in years to consider as a match', 'digit', '', '', 1, '');

SET @iColumn := 1;
SET @iOrder := (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column` = @iColumn);
SET @sPageWidth := (SELECT `PageWidth` FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column` = @iColumn AND `Order` = @iOrder);
SET @fColWidth := (SELECT `ColWidth` FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Column` = @iColumn AND `Order` = @iOrder);
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', @sPageWidth, 'People You May Like', '_aqb_pyml_pb_title', @iColumn, @iOrder+1, 'PHP', 'return BxDolService::call(''aqb_pyml'', ''get_profiles_block'', array(''profile''));', 1, @fColWidth, 'memb', 0);

SET @iColumn := 1;
SET @iOrder := (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page` = 'member' AND `Column` = @iColumn);
SET @sPageWidth := (SELECT `PageWidth` FROM `sys_page_compose` WHERE `Page` = 'member' AND `Column` = @iColumn AND `Order` = @iOrder);
SET @fColWidth := (SELECT `ColWidth` FROM `sys_page_compose` WHERE `Page` = 'member' AND `Column` = @iColumn AND `Order` = @iOrder);
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('member', @sPageWidth, 'People You May Like', '_aqb_pyml_pb_title', @iColumn, @iOrder+1, 'PHP', 'return BxDolService::call(''aqb_pyml'', ''get_profiles_block'', array(''member''));', 1, @fColWidth, 'memb', 0);

SET @iColumn := 1;
SET @iOrder := (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page` = 'index' AND `Column` = @iColumn);
SET @sPageWidth := (SELECT `PageWidth` FROM `sys_page_compose` WHERE `Page` = 'index' AND `Column` = @iColumn AND `Order` = @iOrder);
SET @fColWidth := (SELECT `ColWidth` FROM `sys_page_compose` WHERE `Page` = 'index' AND `Column` = @iColumn AND `Order` = @iOrder);
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('index', @sPageWidth, 'People You May Like', '_aqb_pyml_pb_title', @iColumn, @iOrder+1, 'PHP', 'return BxDolService::call(''aqb_pyml'', ''get_profiles_block'', array(''index''));', 1, @fColWidth, 'memb', 0);

CREATE TABLE IF NOT EXISTS `[db_prefix]fields` (
	`field_id` INT(11) NOT NULL,
  	`type` ENUM('MATCH', 'DONTMATCH') NOT NULL,
  	PRIMARY KEY (`field_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]fields` (`field_id`, `type`)
SELECT `ID`, 'DONTMATCH' FROM `sys_profile_fields` WHERE `Name` = 'Sex';

INSERT INTO `[db_prefix]fields` (`field_id`, `type`)
SELECT `ID`, 'MATCH' FROM `sys_profile_fields` WHERE `Name` = 'Country';

INSERT INTO `[db_prefix]fields` (`field_id`, `type`)
SELECT `ID`, 'MATCH' FROM `sys_profile_fields` WHERE `Name` = 'DateOfBirth';