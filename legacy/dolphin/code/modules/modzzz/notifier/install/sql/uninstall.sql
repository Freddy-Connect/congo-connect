-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]member_settings`; 
DROP TABLE IF EXISTS `[db_prefix]field_mapping`; 
DROP TABLE IF EXISTS `[db_prefix]notifications`; 
DROP TABLE IF EXISTS `[db_prefix]notification_settings`; 
 
-- compose pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_notifier_main', 'modzzz_notifier_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_notifier_main', 'modzzz_notifier_my');

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=notifier/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_notifier';
DELETE FROM `sys_menu_top` WHERE `name` = 'Notifier Activity';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Notifier' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;
  
  
 
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Notifier' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_notifier_permalinks');
 
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxNotifier');
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxNotifierInit');
 
DELETE FROM `sys_email_templates` WHERE `Name` IN (
'modzzz_notifier_periodical', 
'modzzz_notifier_forum_post',
'modzzz_notifier_forum_post_participant',
'modzzz_notifier_flagged_forum_reply',
'modzzz_notifier_blocklisted',
'modzzz_notifier_hotlisted',
'modzzz_notifier_friend_delete',
'modzzz_notifier_blocklist_delete',
'modzzz_notifier_hotlist_delete',
'modzzz_notifier_blog_post_added',
'modzzz_notifier_forum_post_reply',
'modzzz_notifier_comment',
'modzzz_notifier_profile_comment',
'modzzz_notifier_rate', 
'modzzz_notifier_profile_rate',
'modzzz_notifier_add',
'modzzz_notifier_profile_view',
'modzzz_notifier_profile_status_message',
'modzzz_notifier_poll_answered',
'modzzz_notifier_event_join',
'modzzz_notifier_group_join',
'modzzz_notifier_create',
'modzzz_notifier_wall_update'
);
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_notifier_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_notifier_profile_join' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_notifier' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;


-- cron_jobs
DELETE FROM `sys_cron_jobs` WHERE `name` IN ('BxNotifier','BxNotifierInit');