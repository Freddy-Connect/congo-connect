-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]member_settings`; 
DROP TABLE IF EXISTS `[db_prefix]field_mapping`; 
DROP TABLE IF EXISTS `[db_prefix]notifications`; 
DROP TABLE IF EXISTS `[db_prefix]notification_settings`; 
DROP TABLE IF EXISTS `[db_prefix]track_views`; 
   
-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_notify_main', 'modzzz_notify_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_notify_main', 'modzzz_notify_my');

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=notify/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_notify';
DELETE FROM `sys_menu_top` WHERE `name` = 'Notify Activity';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Notify' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
   
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Notify' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_notify_permalinks');
 
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxNotify');
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxNotifyInit');
 
DELETE FROM `sys_email_templates` WHERE `Name` IN (
'modzzz_notify_periodical', 
'modzzz_notify_forum_post',
'modzzz_notify_forum_post_participant',
'modzzz_notify_flagged_forum_reply',
'modzzz_notify_blocklisted',
'modzzz_notify_hotlisted',
'modzzz_notify_friend_delete',
'modzzz_notify_blocklist_delete',
'modzzz_notify_hotlist_delete',
'modzzz_notify_forum_post_reply',
'modzzz_notify_comment',
'modzzz_notify_profile_comment',
'modzzz_notify_rate', 
'modzzz_notify_profile_rate',
'modzzz_notify_add',
'modzzz_notify_profile_view',
'modzzz_notify_profile_status_message',
'modzzz_notify_poll_answered',


'modzzz_notify_event_join',
'modzzz_notify_job_join',
'modzzz_notify_investment_join',
'modzzz_notify_formation_join',
'modzzz_notify_business_listings_join',
'modzzz_notify_modzzz_articles_join',
'modzzz_notify_modzzz_classified_join',


'modzzz_notify_group_join',
'modzzz_notify_create',
'modzzz_notify_wall_update'
);
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_notify_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_notify_profile_join' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_notify' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;


-- cron_jobs
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxNotify','BxNotifyInit');