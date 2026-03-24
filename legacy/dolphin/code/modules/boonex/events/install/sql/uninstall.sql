-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]images`;
DROP TABLE IF EXISTS `[db_prefix]videos`;
DROP TABLE IF EXISTS `[db_prefix]sounds`;
DROP TABLE IF EXISTS `[db_prefix]files`;
DROP TABLE IF EXISTS `[db_prefix]participants`;
DROP TABLE IF EXISTS `[db_prefix]admins`;
DROP TABLE IF EXISTS `[db_prefix]rating`;
DROP TABLE IF EXISTS `[db_prefix]rating_track`;
DROP TABLE IF EXISTS `[db_prefix]cmts`;
DROP TABLE IF EXISTS `[db_prefix]cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]views_track`;
DROP TABLE IF EXISTS `[db_prefix]youtube`;
DROP TABLE IF EXISTS `[db_prefix]monitored_groups`;

-- forum tables
DROP TABLE IF EXISTS `[db_prefix]forum`;
DROP TABLE IF EXISTS `[db_prefix]forum_cat`;
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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('bx_events_edit', 'bx_events_view', 'bx_events_celendar', 'bx_events_main', 'bx_events_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('bx_events_edit', 'bx_events_view', 'bx_events_celendar', 'bx_events_main', 'bx_events_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Caption` LIKE '%bx_events%';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Caption` LIKE '%bx_events%';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Caption` LIKE '%bx_events%';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=events/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_events';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_events';
DELETE FROM `sys_objects_views` WHERE `name` = 'bx_events';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'bx_events';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_events';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Events';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'bx_events';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'bx_events';
DELETE FROM `sys_tags` WHERE `Type` = 'bx_events';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'bx_events' OR `Type` LIKE 'bx_events_%';
DELETE FROM `sys_stat_site` WHERE `Name` = 'evs';
DELETE FROM `sys_stat_member` WHERE TYPE IN('bx_events', 'bx_eventsp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_bx_events';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Events' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Events' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Events';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Events';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'bx_events';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 'bx_events_invitation' OR `Name` = 'bx_events_broadcast' OR `Name` = 'bx_events_sbs' OR `Name` = 'bx_events_join_request' OR `Name` = 'bx_events_join_reject' OR `Name` = 'bx_events_join_confirm' OR `Name` = 'bx_events_fan_remove' OR `Name` = 'bx_events_fan_become_admin' OR `Name` = 'bx_events_admin_become_fan';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Events' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'bx_events_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('events view', 'events browse', 'events search', 'events add', 'events comments delete and edit', 'events edit any event', 'events delete any event', 'events mark as featured', 'events approve', 'events broadcast message','events allow embed');
DELETE FROM `sys_acl_actions` WHERE `Name` IN ('events view', 'events browse', 'events search', 'events add', 'events comments delete and edit', 'events edit any event', 'events delete any event', 'events mark as featured', 'events approve', 'events broadcast message', 'events allow embed');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_events_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_events_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_events' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'bx_events';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'events';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='bx_events';
DELETE FROM `sys_sbs_types` WHERE `unit`='bx_events';


-- [BEGIN] CUSTOMIZED ADDITIONS 
DROP TABLE IF EXISTS `[db_prefix]activity`;
DROP TABLE IF EXISTS `[db_prefix]invite`;
DROP TABLE IF EXISTS `[db_prefix]packages`;
DROP TABLE IF EXISTS `[db_prefix]invoices`;
DROP TABLE IF EXISTS `[db_prefix]orders`;
DROP TABLE IF EXISTS `[db_prefix]featured_orders`;
DROP TABLE IF EXISTS `[db_prefix]profiles`;
DROP TABLE IF EXISTS `[db_prefix]cities`;
DROP TABLE IF EXISTS `[db_prefix]countries`;
DROP TABLE IF EXISTS `[db_prefix]rss`;


DROP TABLE IF EXISTS `[db_prefix]sponsor_main`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_images`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_rating`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_cmts`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_cmts_track`;

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=events/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_events_sponsor';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_events_sponsor';
 

DROP TABLE IF EXISTS `[db_prefix]news_main`;
DROP TABLE IF EXISTS `[db_prefix]news_images`;
DROP TABLE IF EXISTS `[db_prefix]news_rating`;
DROP TABLE IF EXISTS `[db_prefix]news_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts_track`;

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=events/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_events_news';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_events_news';
 


DROP TABLE IF EXISTS `[db_prefix]venue_main`;
DROP TABLE IF EXISTS `[db_prefix]venue_images`;
DROP TABLE IF EXISTS `[db_prefix]venue_rating`;
DROP TABLE IF EXISTS `[db_prefix]venue_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]venue_cmts`;
DROP TABLE IF EXISTS `[db_prefix]venue_cmts_track`;

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=events/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_events_venue';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_events_venue';
  
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('bx_events_expiring', 'bx_events_featured_expire_notify', 'bx_events_featured_admin_notify', 'bx_events_featured_buyer_notify', 'bx_events_post_expired', 'bx_events_expired', 'bx_events_remind_notify','bx_events_recurr_notify');

DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('bx_events_venues_view','bx_events_venues_browse','bx_events_sponsors_view','bx_events_sponsors_browse', 
'bx_events_news_view','bx_events_news_browse', 'bx_events_edit', 'bx_events_packages', 'bx_events_local', 'bx_events_local_state');
DELETE FROM `sys_page_compose` WHERE `Page` IN('bx_events_venues_view','bx_events_venues_browse','bx_events_sponsors_view','bx_events_sponsors_browse', 
'bx_events_news_view','bx_events_news_browse', 'bx_events_edit', 'bx_events_packages', 'bx_events_local', 'bx_events_local_state' );
  
-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('events extend', 'events relist', 'events purchase', 'events purchase featured', 'events view event', 'events photos add', 'events sounds add', 'events videos add', 'events files add', 'events rss add');

DELETE FROM `sys_acl_actions` WHERE `Name` IN('events extend', 'events relist', 'events purchase', 'events purchase featured', 'events view event', 'events photos add', 'events sounds add', 'events videos add', 'events files add', 'events rss add');

DELETE FROM `sys_cron_jobs` WHERE `name` = 'BxEvents';



SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_events_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_events_map_sponsor_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'bx_events';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'bx_events';


