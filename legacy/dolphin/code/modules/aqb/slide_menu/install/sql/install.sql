SET @sPluginName = 'aqb_slide_menu';

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, @sPluginName, '_aqb_slidem_menu', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/administration/'), 'Mobile Friendly Slide Menu module from AQB Soft', 'share-square-o', @iMax+1);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Slide Menu', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
(CONCAT(@sPluginName, '_enable'), 'on', @iCategId, 'Enable menu', 'checkbox', '', '', '1', ''),
(CONCAT(@sPluginName, '_type'), 'multy-level', @iCategId, 'Select Menu Type', 'select', '', '', '1', 'simple,multy-level'),
(CONCAT(@sPluginName, '_side'), 'left', @iCategId, 'Choose menu side', 'select', '', '', '2', 'left,right'),
(CONCAT(@sPluginName, '_width'), '768px', @iCategId, 'Select minimum page''s width<b>(px)</b> to show menu', 'select', '', '', '3', '320px,360px,768px,800px,980px,1280px,1920px');


-- injection
INSERT INTO `sys_injections` SET 
	`name`       = 'slide_menu_header',
	`page_index` = '0',
	`key`        = 'injection_header',
	`type`       = 'php',
	`data`       = 'return BxDolService::call(''aqb_slide_menu'', ''get_page_code'', array(''open''));',
	`replace`    = '0',
	`active`     = '1';

INSERT INTO `sys_injections` SET 
	`name`       = 'slide_menu_footer',
	`page_index` = '0',
	`key`        = 'injection_footer',
	`type`       = 'php',
	`data`       = 'return BxDolService::call(''aqb_slide_menu'', ''get_page_code'', array(''close''));',
	`replace`    = '0',
	`active`     = '1';
	