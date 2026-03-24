-- tables
DROP TABLE IF EXISTS `[db_prefix]entries`; 
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]admins`;
DROP TABLE IF EXISTS `[db_prefix]images`;
DROP TABLE IF EXISTS `[db_prefix]videos`;
DROP TABLE IF EXISTS `[db_prefix]sounds`;
DROP TABLE IF EXISTS `[db_prefix]files`;
DROP TABLE IF EXISTS `[db_prefix]rating`;
DROP TABLE IF EXISTS `[db_prefix]rating_track`;
DROP TABLE IF EXISTS `[db_prefix]cmts`;
DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`; 
DROP TABLE IF EXISTS `[db_prefix]react_admin`;
DROP TABLE IF EXISTS `[db_prefix]react_tracking`;
DROP TABLE IF EXISTS `[db_prefix]vote_tracking`;
DROP TABLE IF EXISTS `[db_prefix]rss`;
DROP TABLE IF EXISTS `[db_prefix]youtube`;
DROP TABLE IF EXISTS `[db_prefix]categ`;
 
-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_news_category', 'modzzz_news_subcategory','modzzz_news_view', 'modzzz_news_celendar', 'modzzz_news_main', 'modzzz_news_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_news_category', 'modzzz_news_subcategory','modzzz_news_view', 'modzzz_news_celendar', 'modzzz_news_main', 'modzzz_news_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'News';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User News';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=news/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_news';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_news';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_news';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_news';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_news';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'News';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_news';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_news';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_news';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_news' OR `Type` = 'modzzz_news_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_news';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_news', 'modzzz_newsp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_news';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'News' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'News' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'News';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'News';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_news';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'News' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_news_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('news add news', 'news view news', 'news browse', 'news search', 'news comments delete and edit', 'news edit any news', 'news delete any news', 'news mark as featured', 'news approve news','news autoapprove news', 'news allow embed');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('news add news', 'news view news', 'news browse', 'news search', 'news comments delete and edit', 'news edit any news', 'news delete any news', 'news mark as featured', 'news approve news','news autoapprove news', 'news allow embed');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_news_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_news_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_news';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'news';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_news';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_news';

DELETE FROM `sys_cron_jobs` WHERE `name`='modzzz_news';

DELETE FROM `sys_pre_values` WHERE `Key` = 'NewsLetter';


-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_news';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_news';


SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_news_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
