
-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]fans`;
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
DROP TABLE IF EXISTS `[db_prefix]youtube`;
DROP TABLE IF EXISTS `[db_prefix]memberships`;
DROP TABLE IF EXISTS `[db_prefix]banned`;

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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('bx_groups_edit', 'bx_groups_view', 'bx_groups_celendar', 'bx_groups_main', 'bx_groups_my');
DELETE FROM `sys_page_compose` WHERE `Page` IN('bx_groups_edit', 'bx_groups_view', 'bx_groups_celendar', 'bx_groups_main', 'bx_groups_my');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Groups';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Groups';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'Joined Groups';

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=groups/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_groups';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_groups';
DELETE FROM `sys_objects_views` WHERE `name` = 'bx_groups';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'bx_groups';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_groups';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Groups';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'bx_groups';
DELETE FROM `sys_tags` WHERE `Type` = 'bx_groups';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'bx_groups';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'bx_groups' OR `Type` LIKE 'bx_groups_%';
DELETE FROM `sys_stat_site` WHERE `Name` = 'bx_groups';
DELETE FROM `sys_stat_member` WHERE TYPE IN('bx_groups', 'bx_groupsp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_bx_groups';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` = 'bx_groups_broadcast' OR `Name` = 'bx_groups_join_request' OR `Name` = 'bx_groups_join_reject' OR `Name` = 'bx_groups_join_confirm' OR `Name` = 'bx_groups_fan_remove' OR `Name` = 'bx_groups_fan_become_admin' OR `Name` = 'bx_groups_admin_become_fan' OR `Name` = 'bx_groups_sbs' OR `Name` = 'bx_groups_invitation';

-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Groups' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Groups' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Groups';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Groups';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'bx_groups';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Groups' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'bx_groups_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('groups view group', 'groups browse', 'groups search', 'groups add group', 'groups comments delete and edit', 'groups edit any group', 'groups delete any group', 'groups mark as featured', 'groups approve groups', 'groups broadcast message');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('groups view group', 'groups browse', 'groups search', 'groups add group', 'groups comments delete and edit', 'groups edit any group', 'groups delete any group', 'groups mark as featured', 'groups approve groups', 'groups broadcast message');

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_groups_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_groups_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'bx_groups';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'groups';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='bx_groups';
DELETE FROM `sys_sbs_types` WHERE `unit`='bx_groups';





-- [BEGIN] CUSTOMIZED ADDITIONS 
DROP TABLE IF EXISTS `[db_prefix]activity`;
DROP TABLE IF EXISTS `[db_prefix]invite`;
DROP TABLE IF EXISTS `[db_prefix]profiles`;
DROP TABLE IF EXISTS `[db_prefix]cities`;
DROP TABLE IF EXISTS `[db_prefix]countries`;
DROP TABLE IF EXISTS `[db_prefix]rss`;
DROP TABLE IF EXISTS `[db_prefix]featured_orders`;

DROP TABLE IF EXISTS `[db_prefix]event_main`;
DROP TABLE IF EXISTS `[db_prefix]event_images`;
DROP TABLE IF EXISTS `[db_prefix]event_videos`;
DROP TABLE IF EXISTS `[db_prefix]event_sounds`;
DROP TABLE IF EXISTS `[db_prefix]event_files`;

DROP TABLE IF EXISTS `[db_prefix]event_rating`;
DROP TABLE IF EXISTS `[db_prefix]event_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]event_cmts`;
DROP TABLE IF EXISTS `[db_prefix]event_cmts_track`;

-- system objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_groups_event';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_groups_event';

DROP TABLE IF EXISTS `[db_prefix]sponsor_main`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_images`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_videos`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_sounds`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_files`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_rating`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_cmts`;
DROP TABLE IF EXISTS `[db_prefix]sponsor_cmts_track`;

-- system objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_groups_sponsor';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_groups_sponsor';
 
 

DROP TABLE IF EXISTS `[db_prefix]blog_main`;
DROP TABLE IF EXISTS `[db_prefix]blog_images`;
DROP TABLE IF EXISTS `[db_prefix]blog_videos`;
DROP TABLE IF EXISTS `[db_prefix]blog_sounds`;
DROP TABLE IF EXISTS `[db_prefix]blog_files`;
DROP TABLE IF EXISTS `[db_prefix]blog_rating`;
DROP TABLE IF EXISTS `[db_prefix]blog_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]blog_cmts`;
DROP TABLE IF EXISTS `[db_prefix]blog_cmts_track`;

-- system objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_groups_blog';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_groups_blog';
 

DROP TABLE IF EXISTS `[db_prefix]news_main`;
DROP TABLE IF EXISTS `[db_prefix]news_images`;
DROP TABLE IF EXISTS `[db_prefix]news_videos`;
DROP TABLE IF EXISTS `[db_prefix]news_sounds`;
DROP TABLE IF EXISTS `[db_prefix]news_files`;
DROP TABLE IF EXISTS `[db_prefix]news_rating`;
DROP TABLE IF EXISTS `[db_prefix]news_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts_track`;

-- system objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_groups_news';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_groups_news';
 


DROP TABLE IF EXISTS `[db_prefix]venue_main`;
DROP TABLE IF EXISTS `[db_prefix]venue_images`;
DROP TABLE IF EXISTS `[db_prefix]venue_videos`;
DROP TABLE IF EXISTS `[db_prefix]venue_sounds`;
DROP TABLE IF EXISTS `[db_prefix]venue_files`;
DROP TABLE IF EXISTS `[db_prefix]venue_rating`;
DROP TABLE IF EXISTS `[db_prefix]venue_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]venue_cmts`;
DROP TABLE IF EXISTS `[db_prefix]venue_cmts_track`;

-- system objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'bx_groups_venue';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'bx_groups_venue';
  

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('bx_groups_featured_expire_notify', 'bx_groups_featured_admin_notify', 'bx_groups_featured_buyer_notify', 
'bx_groups_invitation');

DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('bx_groups_venues_view','bx_groups_venues_browse','bx_groups_events_view','bx_groups_events_browse','bx_groups_sponsors_view','bx_groups_sponsors_browse','bx_groups_blogs_view','bx_groups_blogs_browse', 'bx_groups_news_view','bx_groups_news_browse', 'bx_groups_edit', 'bx_groups_local', 'bx_groups_local_state');
DELETE FROM `sys_page_compose` WHERE `Page` IN('bx_groups_venues_view','bx_groups_venues_browse','bx_groups_events_view','bx_groups_events_browse','bx_groups_sponsors_view','bx_groups_sponsors_browse','bx_groups_blogs_view','bx_groups_blogs_browse', 'bx_groups_news_view','bx_groups_news_browse', 'bx_groups_edit', 'bx_groups_local', 'bx_groups_local_state' );
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Groups';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Joined Groups';
 
-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('groups extend', 'groups relist', 'groups purchase', 'groups purchase featured', 'groups view group', 'groups photos add', 'groups sounds add', 'groups videos add', 'groups files add', 'groups rss add');

DELETE FROM `sys_acl_actions` WHERE `Name` IN('groups purchase featured', 'groups view group', 'groups photos add', 'groups sounds add', 'groups videos add', 'groups files add', 'groups rss add');

DELETE FROM `sys_cron_jobs` WHERE `name` = 'BxGroups';

DELETE FROM `sys_pre_values` WHERE `Key` = 'GroupEventCategories';


-- ALTER TABLE `bx_events_main` DROP `group_id`; 


SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_groups_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_groups_map_event_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_groups_map_sponsor_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_groups_map_venue_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'bx_groups';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'bx_groups';
