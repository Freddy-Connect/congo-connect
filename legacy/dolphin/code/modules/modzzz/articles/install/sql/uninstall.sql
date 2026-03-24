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
DROP TABLE IF EXISTS `[db_prefix]favorites`;
DROP TABLE IF EXISTS `[db_prefix]categ`;
DROP TABLE IF EXISTS `[db_prefix]youtube`;

-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_articles_category', 'modzzz_articles_subcategory','modzzz_articles_view', 'modzzz_articles_celendar', 'modzzz_articles_main', 'modzzz_articles_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_articles_category', 'modzzz_articles_subcategory','modzzz_articles_view', 'modzzz_articles_celendar', 'modzzz_articles_main', 'modzzz_articles_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Articles';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Articles';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=articles/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_articles';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_articles';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_articles';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_articles';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_articles';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Articles';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_articles';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_articles';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_articles';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_articles' OR `Type` = 'modzzz_articles_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_articles';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_articles', 'modzzz_articlesp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_articles';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Articles' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Articles' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Articles';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Articles';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_articles';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Articles' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_articles_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('articles mark as favorite','articles autoapprove article','articles view article', 'articles browse', 'articles search', 'articles add article', 'articles comments delete and edit', 'articles edit any article', 'articles delete any article', 'articles mark as featured', 'articles approve articles', 'articles allow embed');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('articles mark as favorite','articles autoapprove article','articles view article', 'articles browse', 'articles search', 'articles add article', 'articles comments delete and edit', 'articles edit any article', 'articles delete any article', 'articles mark as featured', 'articles approve articles', 'articles allow embed');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_articles_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_articles_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_articles';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'articles';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_articles';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_articles';

-- email templates
DELETE FROM `sys_email_templates` WHERE  `Name` = 'modzzz_articles_sbs';

DELETE FROM `sys_cron_jobs` WHERE `name`='modzzz_articles';
 
DELETE FROM `sys_pre_values` WHERE `Key` = 'ArticlesLetter';
 
-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_articles';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_articles';

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_articles_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;


-- injections
DELETE FROM `sys_injections` WHERE `name` IN ('articles_bg_injection');
