
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=listingcover/', 'm/listingcover/', 'modzzz_listingcover_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('ListingCover', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_listingcover_permalinks', 'on', 26, 'Enable friendly permalinks in listingcovers', 'checkbox', '', '', '0', ''),
('modzzz_listingcover_show_administrator', 'on', @iCategId, 'Show Listing Administrator thumbnail on Cover<br>(if disabled, Listing thumbnail is shown)', 'checkbox', '', '', '0', ''), 
('modzzz_listingcover_perpage_browse', '30', @iCategId, 'Number of Cover entries to show on browse page', 'digit', '', '', '0', ''), 
('modzzz_listingcover_active', 'on', @iCategId, 'Business Listing Cover integration is activated', 'checkbox', '', '', '0', '');
 
-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{evalResult}', 'plus', 'modules/?r=listingcover/cover/add/{ID}', '', 'return ($GLOBALS[''logged''][''member''] && BxDolModule::getInstance(''BxListingCoverModule'')->isAllowedAdd({ID})) || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_listingcover_action_title_add_cover'') : '''';', 30, 'modzzz_listing');
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Name`='Listing' AND `Caption`='_modzzz_listing_menu_root' AND `Type`='system' AND `Active`=1);
 
-- INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
-- (NULL, @iCatRoot, 'Listing View Cover', '_modzzz_listingcover_menu_view_cover', 'modules/?r=listingcover/cover/browse/{modzzz_listing_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
  
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=listingcover/cover/add/|modules/?r=listingcover/cover/edit/|modules/?r=listingcover/cover/browse/') WHERE `Parent`=0 AND `Name`='Listing' AND `Type`='system' AND `Caption`='_modzzz_listing_menu_root'; 

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_listingcover', '_modzzz_listingcover', '{siteUrl}modules/?r=listingcover/administration/', 'Business Listing Cover module by Modzzz','picture-o', @iMax+1);


-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listingcover add cover', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
  

CREATE TABLE IF NOT EXISTS `modzzz_listingcover_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `listing_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `allow_view_to` varchar(16) NOT NULL default '3', 
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a',  
  PRIMARY KEY (`id`),
  UNIQUE KEY `cover_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_listingcover_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listingcover_browse', 'Business Listing Cover Browse', @iMaxOrder+1);
  
UPDATE `sys_page_compose` SET `Column`=`Column`+1 WHERE `Page`='modzzz_listing_view' AND `Column`!=0; 


INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES  
('modzzz_listing_view', '1140px', 'Listing''s Cover block', '_modzzz_listingcover_block_cover', 1, 0, 'PHP', 'return BxDolService::call(''listingcover'', ''cover_block'', array($this->aDataEntry));', 0, 100, 'non,memb', 0), 
('modzzz_listingcover_browse', '1140px', 'Business Listing Cover''s browse block', '_modzzz_listingcover_block_browse_covers', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0');
 


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_listing_parent_delete', '', '', 'BxDolService::call(''listingcover'', ''response_parent_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'modzzz_listing', 'delete', @iHandler);