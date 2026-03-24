
-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]ratehelp`; 
DROP TABLE IF EXISTS `[db_prefix]today_item`; 
DROP TABLE IF EXISTS `[db_prefix]sel_daily_items`;  
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
DROP TABLE IF EXISTS `[db_prefix]rate_word`;
 
-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_faq_view', 'modzzz_faq_celendar', 'modzzz_faq_main', 'modzzz_faq_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_faq_view', 'modzzz_faq_celendar', 'modzzz_faq_main', 'modzzz_faq_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'FAQ';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'FAQ';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=faq/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_faq';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_faq';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_faq';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_faq';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_faq';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'FAQ';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_faq';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_faq';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_faq';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_faq' OR `Type` = 'modzzz_faq_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_faq';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_faq', 'modzzz_faqp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_faq';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'FAQ' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'FAQ' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'FAQ';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'FAQ';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_faq';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'FAQ' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_faq_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('faq view faq', 'faq browse', 'faq search', 'faq add faq', 'faq comments delete and edit', 'faq edit any faq', 'faq delete any faq', 'faq mark as featured', 'faq approve faq', 'faq rate help' );
DELETE FROM `sys_acl_actions` WHERE `Name` IN('faq view faq', 'faq browse', 'faq search', 'faq add faq', 'faq comments delete and edit', 'faq edit any faq', 'faq delete any faq', 'faq mark as featured', 'faq approve faq', 'faq rate help' );

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_faq_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_faq_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_faq';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'faq';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_faq';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_faq';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 'modzzz_faq_sbs';


-- cron_jobs
DELETE FROM `sys_cron_jobs` WHERE `name` = 'BxFAQCron';


-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_faq';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_faq';