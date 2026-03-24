-- tables
DROP TABLE IF EXISTS `[db_prefix]companies`; 
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
DROP TABLE IF EXISTS `[db_prefix]company_rating`;
DROP TABLE IF EXISTS `[db_prefix]company_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]company_cmts`;
DROP TABLE IF EXISTS `[db_prefix]company_cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]company_views_track`; 
DROP TABLE IF EXISTS `[db_prefix]categ`;
DROP TABLE IF EXISTS `[db_prefix]apply`;
DROP TABLE IF EXISTS `[db_prefix]fans`;
DROP TABLE IF EXISTS `[db_prefix]packages`;
DROP TABLE IF EXISTS `[db_prefix]invoices`; 
DROP TABLE IF EXISTS `[db_prefix]orders`;
DROP TABLE IF EXISTS `[db_prefix]featured_orders`; 
DROP TABLE IF EXISTS `[db_prefix]payment_track`;
DROP TABLE IF EXISTS `[db_prefix]youtube`;
DROP TABLE IF EXISTS `[db_prefix]favorites`;

-- forum tables
DROP TABLE IF EXISTS `[db_prefix]forum`;
DROP TABLE IF EXISTS `[db_prefix]forum_cat`;
DROP TABLE IF EXISTS `[db_prefix]forum_flag`;
DROP TABLE IF EXISTS `[db_prefix]forum_post`;
DROP TABLE IF EXISTS `[db_prefix]forum_topic`;
DROP TABLE IF EXISTS `[db_prefix]forum_user`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_activity`;
DROP TABLE IF EXISTS `[db_prefix]forum_user_stat`;
DROP TABLE IF EXISTS `[db_prefix]forum_vote`;
DROP TABLE IF EXISTS `[db_prefix]forum_actions_log`;
DROP TABLE IF EXISTS `[db_prefix]forum_attachments`;
DROP TABLE IF EXISTS `[db_prefix]forum_signatures`;


-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ('modzzz_formations_edit','modzzz_formations_packages','modzzz_formations_category','modzzz_formations_subcategory','modzzz_formations_local','modzzz_formations_local_state','modzzz_formations_view', 'modzzz_formations_company_view', 'modzzz_formations_celendar', 'modzzz_formations_main', 'modzzz_formations_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN ('modzzz_formations_edit','modzzz_formations_packages','modzzz_formations_category','modzzz_formations_subcategory','modzzz_formations_local','modzzz_formations_local_state', 'modzzz_formations_view', 'modzzz_formations_company_view', 'modzzz_formations_celendar', 'modzzz_formations_main', 'modzzz_formations_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Caption` LIKE '_modzzz_formations_%';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Caption` LIKE '_modzzz_formations_%';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Caption` LIKE '_modzzz_formations_%';
  
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=formations/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` IN ('modzzz_formations','modzzz_formations_company');
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` IN ('modzzz_formations','modzzz_formations_company');
DELETE FROM `sys_objects_views` WHERE `name` IN ('modzzz_formations','modzzz_formations_company');
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_formations';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_formations';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Formations';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_formations';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_formations';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_formations';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_formations' OR`Type` = 'modzzz_formations_company' OR `Type` = 'modzzz_formations_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_formations';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_formations', 'modzzz_formationsp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_formations';

  

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Formations' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Formations' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Formations';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Formations';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_formations';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Formations' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_formations_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('formations files add','formations mark as today','formations photos add','formations purchase featured','formations sounds add','formations videos add','formations purchase', 'formations extend', 'formations relist', 'formations view formation', 'formations browse', 'formations search', 'formations apply', 'formations add formation','formations mark as favorite', 'formations comments delete and edit', 'formations edit any formation', 'formations delete any formation', 'formations mark as featured', 'formations approve formations', 'formations broadcast message', 'formations allow embed');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('formations mark as favorite','formations files add','formations mark as today','formations photos add','formations purchase featured','formations sounds add','formations videos add','formations purchase', 'formations extend', 'formations relist', 'formations view formation', 'formations browse', 'formations search', 'formations apply', 'formations add formation', 'formations comments delete and edit', 'formations edit any formation', 'formations delete any formation', 'formations mark as featured', 'formations approve formations', 'formations broadcast message', 'formations allow embed');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_formations_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_formations_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_formations';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'formations';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_formations';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_formations';


DELETE FROM `sys_pre_values` WHERE `Key` IN ('FormationCurrency','FormationType','FormationStatus','FormationExperience','FormationEducation','FormationCareerLevel','FormationSalaryType');
 
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_formations_apply_inbox', 'modzzz_formations_expired', 'modzzz_formations_expiring', 'modzzz_formations_company_invitation', 'modzzz_formations_apply','modzzz_formations_sbs','modzzz_formations_invitation','modzzz_formations_fan_remove', 'modzzz_formations_fan_become_admin', 'modzzz_formations_admin_become_fan', 'modzzz_formations_join_request', 'modzzz_formations_join_reject', 'modzzz_formations_join_confirm', 'modzzz_formations_broadcast', 'modzzz_formations_broadcast_applicants');

-- cron formations
DELETE FROM `sys_cron_jobs` WHERE `Name` IN ('BxFormations');



SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_formations_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_formations';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_formations';
 