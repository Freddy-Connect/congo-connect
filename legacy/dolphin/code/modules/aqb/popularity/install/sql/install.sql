SET @sPluginName = 'aqb_popularity';


-- options
SET @iCategoryOrder = (SELECT MAX(`menu_order`) FROM `sys_options_cats`) + 1;
INSERT INTO `sys_options_cats`(`name` , `menu_order` ) VALUES (@sPluginName, @iCategoryOrder);
SET @iCategoryId = LAST_INSERT_ID();

INSERT INTO `sys_options`(`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
(CONCAT('permalinks_module_', @sPluginName), 'on', 26, 'Enable user friendly permalinks for My Popularity module', 'checkbox', '', '', 0, ''),
('aqb_popularity_per_page', '10', @iCategoryId, 'Number of guests displayed on a page', 'digit', '', '', 1, ''),
('aqb_popularity_def_duration', 'all', @iCategoryId, 'The default tab in My Guests block', 'select', '', '', 2, 'today,week,all');


-- page compose blocks
SET @iPCPOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`);
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES
(CONCAT(@sPluginName, '_view'), 'My Popularity', @iPCPOrder + 1);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(CONCAT(@sPluginName, '_view'), '1140px', 'Viewed Me', '_aqb_plt_block_viewed_me_account', 1, 1, 'ViewedMe', '', 1, 33, 'memb', 0),
(CONCAT(@sPluginName, '_view'), '1140px', 'Favorited Me', '_aqb_plt_block_favorited_me_account', 2, 1, 'FavoritedMe', '', 1, 34, 'memb', 0),
(CONCAT(@sPluginName, '_view'), '1140px', 'Subscribed Me', '_aqb_plt_block_subscribed_me_account', 3, 1, 'SubscribedMe', '', 1, 33, 'memb', 0);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', '1140px', 'Viewed me', '_aqb_plt_block_viewed_me_profile', 1, 1, 'PHP', 'return BxDolService::call(''aqb_popularity'', ''get_block_profile'', array(''viewed_me'', $this->oProfileGen->_iProfileID));', 1, 71.9, 'memb', 0),
('member', '1140px', 'Viewed me', '_aqb_plt_block_viewed_me_account', 2, 1, 'PHP', 'return BxDolService::call(''aqb_popularity'', ''get_block_account'', array(''viewed_me''));', 1, 71.9, 'memb', 0);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', '1140px', 'Favorited me', '_aqb_plt_block_favorited_me_profile', 1, 1, 'PHP', 'return BxDolService::call(''aqb_popularity'', ''get_block_profile'', array(''favorited_me'', $this->oProfileGen->_iProfileID));', 1, 71.9, 'memb', 0),
('member', '1140px', 'Favorited me', '_aqb_plt_block_favorited_me_account', 2, 1, 'PHP', 'return BxDolService::call(''aqb_popularity'', ''get_block_account'', array(''favorited_me''));', 1, 71.9, 'memb', 0);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('profile', '1140px', 'Subscribed me', '_aqb_plt_block_subscribed_me_profile', 1, 1, 'PHP', 'return BxDolService::call(''aqb_popularity'', ''get_block_profile'', array(''subscribed_me'', $this->oProfileGen->_iProfileID));', 1, 71.9, 'memb', 0),
('member', '1140px', 'Subscribed me', '_aqb_plt_block_subscribed_me_account', 2, 1, 'PHP', 'return BxDolService::call(''aqb_popularity'', ''get_block_account'', array(''subscribed_me''));', 1, 71.9, 'memb', 0);


-- admin menu
SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_plt_am_item', CONCAT('{siteUrl}modules/?r=', @sPluginName, '/admin/'), 'For managing My Popularity', 'modules/aqb/popularity/|adm_menu_icon.png', '', '', @iOrder+1);

-- top menu
SET @iTMParent = 118;
SET @iTMOrder = (SELECT MAX(`Order`) FROM `sys_menu_top` WHERE `Parent`=@iTMParent);
INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(@iTMParent, CONCAT(@sPluginName, '_view'), '_aqb_plt_tm_item', CONCAT('modules/?r=', @sPluginName, '/view/'), @iTMOrder+1, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, '');

-- member menu
SET @iMMOrder = (SELECT MAX(`Order`) FROM `sys_menu_member` WHERE `Position`='top_extra' AND `Type`='link');
INSERT INTO `sys_menu_member` (`Caption`, `Name`, `Icon`, `Link`, `Script`, `Eval`, `PopupMenu`, `Order`, `Active`, `Editable`, `Deletable`, `Target`, `Position`, `Type`, `Parent`, `Bubble`, `Description`) VALUES
('_aqb_plt_tbar_item_caption', 'My Popularity', 'eye', CONCAT('modules/?r=', @sPluginName, '/view/'), '', '', 'return BxDolService::call(''aqb_popularity'', ''get_toolbar_items'', array({ID}));', @iMMOrder+1, 1, 0, 0, '', 'top_extra', 'link', 0, '$aRetEval = BxDolService::call(''aqb_popularity'', ''get_toolbar_count'', array({ID}, {iOldCount}));', '_aqb_plt_tbar_item_description');


-- permalink
INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
(CONCAT('modules/?r=', @sPluginName, '/'), CONCAT('m/', @sPluginName, '/'), CONCAT('permalinks_module_', @sPluginName));

-- Alter Content Tables
ALTER TABLE `sys_fave_list` ADD `aqb_viewed` TINYINT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `sys_sbs_entries` ADD `aqb_viewed` TINYINT( 4 ) NOT NULL DEFAULT '0';
ALTER TABLE `sys_profile_views_track` ADD `aqb_viewed` TINYINT( 4 ) NOT NULL DEFAULT '0';