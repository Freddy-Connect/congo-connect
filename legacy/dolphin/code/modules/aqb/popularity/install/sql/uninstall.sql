SET @sPluginName = 'aqb_popularity';


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN (CONCAT(@sPluginName, '_view'));
DELETE FROM `sys_page_compose` WHERE `Page` IN (CONCAT(@sPluginName, '_view'));

DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Caption` IN('_aqb_plt_block_viewed_me_profile', '_aqb_plt_block_favorited_me_profile', '_aqb_plt_block_subscribed_me_profile');
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Caption` IN ('_aqb_plt_block_viewed_me_account', '_aqb_plt_block_favorited_me_account', '_aqb_plt_block_subscribed_me_account');


SET @iCategoryId = (SELECT `ID` FROM `sys_options_cats` WHERE `name`=@sPluginName LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `name`=@sPluginName LIMIT 1;
DELETE FROM `sys_options` WHERE `kateg`=@iCategoryId OR `Name` IN (CONCAT('permalinks_module_', @sPluginName));


DELETE FROM `sys_menu_admin` WHERE `name`=@sPluginName;
DELETE FROM `sys_menu_top` WHERE `Name` IN(CONCAT(@sPluginName, '_view'));
DELETE FROM `sys_menu_member` WHERE `Name`='My Popularity';


DELETE FROM `sys_permalinks` WHERE `check`=CONCAT('permalinks_module_', @sPluginName);


ALTER TABLE `sys_fave_list` DROP `aqb_viewed`;
ALTER TABLE `sys_sbs_entries` DROP `aqb_viewed`; 
ALTER TABLE `sys_profile_views_track` DROP `aqb_viewed`; 