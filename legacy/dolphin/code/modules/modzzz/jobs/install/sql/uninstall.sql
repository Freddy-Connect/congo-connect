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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN ('modzzz_jobs_edit','modzzz_jobs_packages','modzzz_jobs_category','modzzz_jobs_subcategory','modzzz_jobs_local','modzzz_jobs_local_state','modzzz_jobs_view', 'modzzz_jobs_company_view', 'modzzz_jobs_celendar', 'modzzz_jobs_main', 'modzzz_jobs_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN ('modzzz_jobs_edit','modzzz_jobs_packages','modzzz_jobs_category','modzzz_jobs_subcategory','modzzz_jobs_local','modzzz_jobs_local_state', 'modzzz_jobs_view', 'modzzz_jobs_company_view', 'modzzz_jobs_celendar', 'modzzz_jobs_main', 'modzzz_jobs_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Caption` LIKE '_modzzz_jobs_%';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Caption` LIKE '_modzzz_jobs_%';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Caption` LIKE '_modzzz_jobs_%';
  
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=jobs/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` IN ('modzzz_jobs','modzzz_jobs_company');
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` IN ('modzzz_jobs','modzzz_jobs_company');
DELETE FROM `sys_objects_views` WHERE `name` IN ('modzzz_jobs','modzzz_jobs_company');
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_jobs';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_jobs';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Jobs';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_jobs';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_jobs';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_jobs';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_jobs' OR`Type` = 'modzzz_jobs_company' OR `Type` = 'modzzz_jobs_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_jobs';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_jobs', 'modzzz_jobsp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_jobs';

  

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Jobs' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Jobs' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Jobs';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Jobs';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_jobs';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Jobs' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_jobs_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('jobs files add','jobs mark as today','jobs photos add','jobs purchase featured','jobs sounds add','jobs videos add','jobs purchase', 'jobs extend', 'jobs relist', 'jobs view job', 'jobs browse', 'jobs search', 'jobs apply', 'jobs add job','jobs mark as favorite', 'jobs comments delete and edit', 'jobs edit any job', 'jobs delete any job', 'jobs mark as featured', 'jobs approve jobs', 'jobs broadcast message', 'jobs allow embed');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('jobs mark as favorite','jobs files add','jobs mark as today','jobs photos add','jobs purchase featured','jobs sounds add','jobs videos add','jobs purchase', 'jobs extend', 'jobs relist', 'jobs view job', 'jobs browse', 'jobs search', 'jobs apply', 'jobs add job', 'jobs comments delete and edit', 'jobs edit any job', 'jobs delete any job', 'jobs mark as featured', 'jobs approve jobs', 'jobs broadcast message', 'jobs allow embed');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_jobs_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_jobs_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_jobs';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'jobs';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_jobs';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_jobs';


DELETE FROM `sys_pre_values` WHERE `Key` IN ('JobCurrency','JobType','JobStatus','JobExperience','JobEducation','JobCareerLevel','JobSalaryType');
 
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_jobs_apply_inbox', 'modzzz_jobs_expired', 'modzzz_jobs_expiring', 'modzzz_jobs_company_invitation', 'modzzz_jobs_apply','modzzz_jobs_sbs','modzzz_jobs_invitation','modzzz_jobs_fan_remove', 'modzzz_jobs_fan_become_admin', 'modzzz_jobs_admin_become_fan', 'modzzz_jobs_join_request', 'modzzz_jobs_join_reject', 'modzzz_jobs_join_confirm', 'modzzz_jobs_broadcast', 'modzzz_jobs_broadcast_applicants');

-- cron jobs
DELETE FROM `sys_cron_jobs` WHERE `Name` IN ('BxJobs');



SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_jobs_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_jobs';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_jobs';
 