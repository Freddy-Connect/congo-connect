-- tables
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
DROP TABLE IF EXISTS `[db_prefix]claim`;
DROP TABLE IF EXISTS `[db_prefix]profiles`;
DROP TABLE IF EXISTS `[db_prefix]cities`;
DROP TABLE IF EXISTS `[db_prefix]countries`;
DROP TABLE IF EXISTS `[db_prefix]categ`;
DROP TABLE IF EXISTS `[db_prefix]packages`;
DROP TABLE IF EXISTS `[db_prefix]invoices`;
DROP TABLE IF EXISTS `[db_prefix]orders`;
DROP TABLE IF EXISTS `[db_prefix]featured_orders`;
DROP TABLE IF EXISTS `[db_prefix]fans`;
DROP TABLE IF EXISTS `[db_prefix]paypal_trans`;
 
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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_church_view', 'modzzz_church_celendar', 'modzzz_church_main', 'modzzz_church_my','modzzz_church_edit','modzzz_church_category','modzzz_church_subcategory','modzzz_church_local','modzzz_church_local_state', 'modzzz_church_packages');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_church_view', 'modzzz_church_celendar', 'modzzz_church_main', 'modzzz_church_my','modzzz_church_edit','modzzz_church_category','modzzz_church_subcategory','modzzz_church_local','modzzz_church_local_state', 'modzzz_church_packages');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Churches';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Churches';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'My Churches';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Local Churches';
 

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=church/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_church';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_church';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_church';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_church';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_church';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Church';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_church';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_church';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_church';
DELETE FROM `sys_objects_actions` WHERE `Type` LIKE 'modzzz_church%';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_church';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_church', 'modzzz_churchp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_church';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Church' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Church' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Church';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Church';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_church';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Church' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_church_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('church purchase', 'church add review', 'church extend', 'church relist', 'church view church', 'church browse', 'church search', 'church add church', 'church comments delete and edit', 'church edit any church', 'church delete any church', 'church mark as featured', 'church approve church', 'church make claim', 'church make inquiry', 'church photos add','church sounds add','church videos add','church files add','church purchase featured', 'church broadcast message', 'church make donation', 'church view donors', 'church post reviews');

DELETE FROM `sys_acl_actions` WHERE `Name` IN('church purchase', 'church add review', 'church extend', 'church relist', 'church view church', 'church browse', 'church search', 'church add church', 'church comments delete and edit', 'church edit any church', 'church delete any church', 'church mark as featured', 'church approve church', 'church make claim', 'church make inquiry', 'church photos add','church sounds add','church videos add','church files add','church purchase featured', 'church broadcast message', 'church make donation', 'church view donors', 'church post reviews');
   

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_church_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_church_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_church';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'church';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_church';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_church';
 
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_church_donation_notify', 'modzzz_church_donation_thanks','modzzz_church_broadcast','modzzz_church_sbs','modzzz_church_featured_expire_notify','modzzz_church_featured_admin_notify','modzzz_church_featured_buyer_notify',
'modzzz_church_inquiry', 'modzzz_church_claim','modzzz_church_claim_assign', 'modzzz_church_invitation', 'modzzz_church_expired', 'modzzz_church_expiring','modzzz_church_fan_remove','modzzz_church_fan_become_admin','modzzz_church_admin_become_fan',
'modzzz_church_join_request','modzzz_church_join_reject','modzzz_church_join_confirm');

 
-- cron jobs
DELETE FROM `sys_cron_jobs` WHERE `Name` IN ('BxChurch');
 

-- NEWS
DROP TABLE IF EXISTS `[db_prefix]news_main`;
DROP TABLE IF EXISTS `[db_prefix]news_images`;
DROP TABLE IF EXISTS `[db_prefix]news_videos`;
DROP TABLE IF EXISTS `[db_prefix]news_sounds`;
DROP TABLE IF EXISTS `[db_prefix]news_files`; 
DROP TABLE IF EXISTS `[db_prefix]news_rating`;
DROP TABLE IF EXISTS `[db_prefix]news_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts`;
DROP TABLE IF EXISTS `[db_prefix]news_cmts_track`;
 
-- Branches
DROP TABLE IF EXISTS `[db_prefix]branches_main`;
DROP TABLE IF EXISTS `[db_prefix]branches_images`;
DROP TABLE IF EXISTS `[db_prefix]branches_videos`;
DROP TABLE IF EXISTS `[db_prefix]branches_sounds`;
DROP TABLE IF EXISTS `[db_prefix]branches_files`; 
DROP TABLE IF EXISTS `[db_prefix]branches_rating`;
DROP TABLE IF EXISTS `[db_prefix]branches_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]branches_cmts`;
DROP TABLE IF EXISTS `[db_prefix]branches_cmts_track`;

-- ministries
DROP TABLE IF EXISTS `[db_prefix]ministries_main`;
DROP TABLE IF EXISTS `[db_prefix]ministries_images`;
DROP TABLE IF EXISTS `[db_prefix]ministries_videos`;
DROP TABLE IF EXISTS `[db_prefix]ministries_sounds`;
DROP TABLE IF EXISTS `[db_prefix]ministries_files`; 
DROP TABLE IF EXISTS `[db_prefix]ministries_rating`;
DROP TABLE IF EXISTS `[db_prefix]ministries_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]ministries_cmts`;
DROP TABLE IF EXISTS `[db_prefix]ministries_cmts_track`;
 
-- MEMBERS
DROP TABLE IF EXISTS `[db_prefix]members_main`;
DROP TABLE IF EXISTS `[db_prefix]members_images`;
DROP TABLE IF EXISTS `[db_prefix]members_videos`;
DROP TABLE IF EXISTS `[db_prefix]members_sounds`;
DROP TABLE IF EXISTS `[db_prefix]members_files`; 
DROP TABLE IF EXISTS `[db_prefix]members_rating`;
DROP TABLE IF EXISTS `[db_prefix]members_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]members_cmts`;
DROP TABLE IF EXISTS `[db_prefix]members_cmts_track`;

-- events
DROP TABLE IF EXISTS `[db_prefix]event_main`;
DROP TABLE IF EXISTS `[db_prefix]event_images`;
DROP TABLE IF EXISTS `[db_prefix]event_videos`;
DROP TABLE IF EXISTS `[db_prefix]event_sounds`;
DROP TABLE IF EXISTS `[db_prefix]event_files`; 
DROP TABLE IF EXISTS `[db_prefix]event_rating`;
DROP TABLE IF EXISTS `[db_prefix]event_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]event_cmts`;
DROP TABLE IF EXISTS `[db_prefix]event_cmts_track`; 
DELETE FROM `sys_pre_values` WHERE `Key` = 'ChurchEventCategories';

-- doctrines
DROP TABLE IF EXISTS `[db_prefix]doctrine_main`;
DROP TABLE IF EXISTS `[db_prefix]doctrine_images`;
DROP TABLE IF EXISTS `[db_prefix]doctrine_videos`;
DROP TABLE IF EXISTS `[db_prefix]doctrine_sounds`;
DROP TABLE IF EXISTS `[db_prefix]doctrine_files`; 
DROP TABLE IF EXISTS `[db_prefix]doctrine_rating`;
DROP TABLE IF EXISTS `[db_prefix]doctrine_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]doctrine_cmts`;
DROP TABLE IF EXISTS `[db_prefix]doctrine_cmts_track`; 
 
-- staffs
DROP TABLE IF EXISTS `[db_prefix]staff_main`;
DROP TABLE IF EXISTS `[db_prefix]staff_images`;
DROP TABLE IF EXISTS `[db_prefix]staff_videos`;
DROP TABLE IF EXISTS `[db_prefix]staff_sounds`;
DROP TABLE IF EXISTS `[db_prefix]staff_files`; 
DROP TABLE IF EXISTS `[db_prefix]staff_rating`;
DROP TABLE IF EXISTS `[db_prefix]staff_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]staff_cmts`;
DROP TABLE IF EXISTS `[db_prefix]staff_cmts_track`;

-- sermons
DROP TABLE IF EXISTS `[db_prefix]sermon_main`;
DROP TABLE IF EXISTS `[db_prefix]sermon_images`;
DROP TABLE IF EXISTS `[db_prefix]sermon_videos`;
DROP TABLE IF EXISTS `[db_prefix]sermon_sounds`;
DROP TABLE IF EXISTS `[db_prefix]sermon_files`; 
DROP TABLE IF EXISTS `[db_prefix]sermon_rating`;
DROP TABLE IF EXISTS `[db_prefix]sermon_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]sermon_cmts`;
DROP TABLE IF EXISTS `[db_prefix]sermon_cmts_track`;


-- remove review components
DROP TABLE IF EXISTS `[db_prefix]review_main`; 
DROP TABLE IF EXISTS `[db_prefix]review_images`;
DROP TABLE IF EXISTS `[db_prefix]review_videos`;
DROP TABLE IF EXISTS `[db_prefix]review_sounds`;
DROP TABLE IF EXISTS `[db_prefix]review_files`;
DROP TABLE IF EXISTS `[db_prefix]review_rating`;
DROP TABLE IF EXISTS `[db_prefix]review_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]review_cmts`;
DROP TABLE IF EXISTS `[db_prefix]review_cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]review_views_trck`;
 
-- remove faq components
DROP TABLE IF EXISTS `[db_prefix]faq_main`;   
DROP TABLE IF EXISTS `[db_prefix]faq_items`;   
  
-- system objects
DELETE FROM `sys_objects_vote` WHERE `ObjectName` LIKE 'modzzz_church%';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` LIKE 'modzzz_church%'; 
DELETE FROM `sys_objects_views` WHERE `name` LIKE 'modzzz_church%';  
DELETE FROM `sys_objects_tag` WHERE `ObjectName` LIKE 'modzzz_church%'; 
DELETE FROM `sys_page_compose_pages` WHERE `Name` LIKE 'modzzz_church%';
DELETE FROM `sys_page_compose` WHERE `Page` LIKE 'modzzz_church%';
DELETE FROM `sys_objects_actions` WHERE `Type` LIKE 'modzzz_church%';
DELETE FROM `sys_tags` WHERE `Type` LIKE 'modzzz_church%';



-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_church';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_church';


-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_church_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
  
-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_church_branches_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_church_event_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
