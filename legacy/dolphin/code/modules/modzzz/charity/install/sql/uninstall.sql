
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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_charity_view', 'modzzz_charity_celendar', 'modzzz_charity_main', 'modzzz_charity_my','modzzz_charity_edit','modzzz_charity_category','modzzz_charity_subcategory','modzzz_charity_local','modzzz_charity_local_state', 'modzzz_charity_packages');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_charity_view', 'modzzz_charity_celendar', 'modzzz_charity_main', 'modzzz_charity_my','modzzz_charity_edit','modzzz_charity_category','modzzz_charity_subcategory','modzzz_charity_local','modzzz_charity_local_state', 'modzzz_charity_packages');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Charities';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Charities';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'My Charities';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Local Charities';
 

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=charity/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` LIKE 'modzzz_charity%';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` LIKE 'modzzz_charity%';
DELETE FROM `sys_objects_views` WHERE `name` LIKE 'modzzz_charity%';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` LIKE 'modzzz_charity%';
DELETE FROM `sys_categories` WHERE `Type` LIKE 'modzzz_charity%';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Charity';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` LIKE 'modzzz_charity%';
DELETE FROM `sys_tags` WHERE `Type` LIKE 'modzzz_charity%';
DELETE FROM `sys_objects_search` WHERE `ObjectName` LIKE 'modzzz_charity%';
DELETE FROM `sys_objects_actions` WHERE `Type` LIKE 'modzzz_charity%';
DELETE FROM `sys_stat_site` WHERE `Name` LIKE 'modzzz_charity%';
DELETE FROM `sys_stat_member` WHERE TYPE IN ('modzzz_charity', 'modzzz_charityp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_charity';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Charity' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Charity' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Charity';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Charity';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_charity';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Charity' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_charity_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN( 'charity add review',  'charity view charity', 'charity browse', 'charity search', 'charity add charity', 'charity comments delete and edit', 'charity edit any charity', 'charity delete any charity', 'charity mark as featured', 'charity approve charity', 'charity make claim', 'charity make inquiry', 'charity photos add','charity sounds add','charity videos add','charity files add','charity purchase featured', 'charity broadcast message', 'charity make donation', 'charity view donors', 'charity post reviews', 'charity create faqs' );

DELETE FROM `sys_acl_actions` WHERE `Name` IN ('charity add review','charity view charity', 'charity browse', 'charity search', 'charity add charity', 'charity comments delete and edit', 'charity edit any charity', 'charity delete any charity', 'charity mark as featured', 'charity approve charity', 'charity make claim', 'charity make inquiry', 'charity photos add','charity sounds add','charity videos add','charity files add','charity purchase featured', 'charity broadcast message', 'charity make donation', 'charity view donors', 'charity post reviews', 'charity create faqs');
   

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_charity_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_charity_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_charity';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'charity';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_charity';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_charity';
 
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_charity_donation_notify', 'modzzz_charity_donation_thanks','modzzz_charity_broadcast','modzzz_charity_sbs','modzzz_charity_featured_expire_notify','modzzz_charity_featured_admin_notify','modzzz_charity_featured_buyer_notify',
'modzzz_charity_inquiry', 'modzzz_charity_claim','modzzz_charity_claim_assign', 'modzzz_charity_invitation', 'modzzz_charity_expired', 'modzzz_charity_expiring','modzzz_charity_fan_remove','modzzz_charity_fan_become_admin','modzzz_charity_admin_become_fan',
'modzzz_charity_join_request','modzzz_charity_join_reject','modzzz_charity_join_confirm');

 
-- cron jobs
DELETE FROM `sys_cron_jobs` WHERE `Name` IN ('BxCharity');
 

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

-- programs
DROP TABLE IF EXISTS `[db_prefix]programs_main`;
DROP TABLE IF EXISTS `[db_prefix]programs_images`;
DROP TABLE IF EXISTS `[db_prefix]programs_videos`;
DROP TABLE IF EXISTS `[db_prefix]programs_sounds`;
DROP TABLE IF EXISTS `[db_prefix]programs_files`; 
DROP TABLE IF EXISTS `[db_prefix]programs_rating`;
DROP TABLE IF EXISTS `[db_prefix]programs_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]programs_cmts`;
DROP TABLE IF EXISTS `[db_prefix]programs_cmts_track`;
 
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
DELETE FROM `sys_pre_values` WHERE `Key` = 'CharityEventCategories';

-- supporters
DROP TABLE IF EXISTS `[db_prefix]supporter_main`;
DROP TABLE IF EXISTS `[db_prefix]supporter_images`;
DROP TABLE IF EXISTS `[db_prefix]supporter_videos`;
DROP TABLE IF EXISTS `[db_prefix]supporter_sounds`;
DROP TABLE IF EXISTS `[db_prefix]supporter_files`; 
DROP TABLE IF EXISTS `[db_prefix]supporter_rating`;
DROP TABLE IF EXISTS `[db_prefix]supporter_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]supporter_cmts`;
DROP TABLE IF EXISTS `[db_prefix]supporter_cmts_track`; 
 
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
DELETE FROM `sys_objects_vote` WHERE `ObjectName` LIKE 'modzzz_charity%';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` LIKE 'modzzz_charity%'; 
DELETE FROM `sys_objects_views` WHERE `name` LIKE 'modzzz_charity%';  
DELETE FROM `sys_objects_tag` WHERE `ObjectName` LIKE 'modzzz_charity%'; 
DELETE FROM `sys_page_compose_pages` WHERE `Name` LIKE 'modzzz_charity%';
DELETE FROM `sys_page_compose` WHERE `Page` LIKE 'modzzz_charity%';
DELETE FROM `sys_objects_actions` WHERE `Type` LIKE 'modzzz_charity%';
DELETE FROM `sys_tags` WHERE `Type` LIKE 'modzzz_charity%';



-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_charity';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_charity';


-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_charity_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
  
-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_charity_branches_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_charity_event_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
