INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_fbook_install', '', '', 'BxDolService::call(''fbook'', ''mod_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);


INSERT INTO `sys_page_compose` (`Page`, `Desc`, `Caption`, `Func`, `Content`)  
SELECT  `Name` , 'Facebook Comments', '_modzzz_fbook_block_comments', 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''fbook'', ''comments_block'');' 
FROM `sys_page_compose_pages`;

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_fbook', '_modzzz_fbook', '{siteUrl}modules/?r=fbook/administration/', 'FBook module by Modzzz','home', @iMax+1);
  
-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=fbook/', 'm/fbook/', 'modzzz_fbook_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('FBook', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_fbook_permalinks', 'on', 26, 'Enable friendly permalinks in fbook', 'checkbox', '', '', '0', ''),
('modzzz_fbook_num_fcomments', '10', @iCategId, 'Number of facebook comments to display', 'digit', '', '', '0', ''),
('modzzz_fbook_width_fcomments', '470', @iCategId, 'Width of facebook comments box', 'digit', '', '', '0', ''),
('modzzz_fbook_colorscheme', 'light', @iCategId, 'Choose color scheme for comments box', 'select', '', '', '0', 'light,dark'), 
('modzzz_fbook_language', 'en_US', @iCategId, 'Display language', 'digit', '', '', '0', ''),  
('modzzz_fbook_fapp_id', '', @iCategId, 'Facebook Application ID', 'digit', '', '', '0', '')  
;


-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'fbook view', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
