SET @sPluginName = 'aqb_guests';


-- options
SET @iCategoryOrder = (SELECT MAX(`menu_order`) FROM `sys_options_cats`) + 1;
INSERT INTO `sys_options_cats`(`name` , `menu_order` ) VALUES (@sPluginName, @iCategoryOrder);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for My Guests module', 'checkbox', '', '', 0, ''),
('aqb_guests_per_page', '10', @iCategoryId, 'Number of guests displayed on a page', 'digit', '', '', 1, ''),
('aqb_guests_def_duration', 'all', @iCategoryId, 'The default tab in My Guests block', 'select', '', '', 2, 'today,week,all');


-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', '1140px', 'My Guests', '_aqb_gst_block_my_guests_profile', 2, 1, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''aqb_guests'', ''get_block_profile'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'memb', 0),
('member', '1140px', 'My Guests', '_aqb_gst_block_my_guests_account', 3, 1, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''aqb_guests'', ''get_block_account'');', 1, 71.9, 'memb', 0);


-- admin menu
SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_gst_am_item', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing My Guests', 'modules/aqb/guests/|adm_menu_icon.png', '', '', @iOrder+1);


INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));