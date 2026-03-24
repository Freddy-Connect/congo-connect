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
DROP TABLE IF EXISTS `[db_prefix]favorites`;
DROP TABLE IF EXISTS `[db_prefix]youtube`;
DROP TABLE IF EXISTS `[db_prefix]schedule`;
DROP TABLE IF EXISTS `[db_prefix]request_main`;
DROP TABLE IF EXISTS `[db_prefix]booking`;
DROP TABLE IF EXISTS `[db_prefix]availability`;
  
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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_professional_view', 'modzzz_professional_celendar', 'modzzz_professional_main', 'modzzz_professional_my','modzzz_professional_edit','modzzz_professional_category','modzzz_professional_subcategory','modzzz_professional_local','modzzz_professional_local_state', 'modzzz_professional_packages');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_professional_view', 'modzzz_professional_celendar', 'modzzz_professional_main', 'modzzz_professional_my','modzzz_professional_edit','modzzz_professional_category','modzzz_professional_subcategory','modzzz_professional_local','modzzz_professional_local_state', 'modzzz_professional_packages');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Professionals';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Professionals';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'My Professionals';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Local Professionals';

 
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=professional/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_professional';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_professional';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_professional';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_professional';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_professional';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Professional';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_professional';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_professional';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_professional';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_professional' OR `Type` = 'modzzz_professional_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_professional';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_professional', 'modzzz_professionalp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_professional';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Professional' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Professional' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Professional';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Professional';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_professional';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Professional' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_professional_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('professional make booking', 'professional mark as favorite','professional broadcast message','professional post reviews', 'professional purchase', 'professional extend', 'professional relist', 'professional view professional', 'professional browse', 'professional search', 'professional add professional', 'professional comments delete and edit', 'professional edit any professional', 'professional delete any professional', 'professional mark as featured', 'professional approve professional', 'professional make claim', 'professional make inquiry' );

DELETE FROM `sys_acl_actions` WHERE `Name` IN('professional make booking', 'professional mark as favorite','professional broadcast message','professional post reviews', 'professional purchase', 'professional extend', 'professional relist', 'professional view professional', 'professional browse', 'professional search', 'professional add professional', 'professional comments delete and edit', 'professional edit any professional', 'professional delete any professional', 'professional mark as featured', 'professional approve professional', 'professional make claim', 'professional make inquiry', 'professional photos add','professional sounds add','professional videos add','professional files add','professional purchase featured');
   

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_professional_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_professional_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_professional';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'professional';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_professional';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_professional';

DELETE FROM `sys_cron_jobs` WHERE `name` = 'BxProfessional';
 
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_professional_purchase', 'modzzz_professional_featured_expire_notify','modzzz_professional_featured_admin_notify','modzzz_professional_featured_buyer_notify','modzzz_professional_inquiry','modzzz_professional_request','modzzz_professional_respond','modzzz_professional_claim','modzzz_professional_claim_assign');
 
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_professional_booking_cancel', 'modzzz_professional_booking_confirm', 'modzzz_professional_booking_notify', 'modzzz_professional_invitation', 'modzzz_professional_expired', 'modzzz_professional_expiring','modzzz_professional_fan_remove','modzzz_professional_fan_become_admin','modzzz_professional_admin_become_fan','modzzz_professional_join_request','modzzz_professional_join_confirm','modzzz_professional_join_reject');
 
 

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
DROP TABLE IF EXISTS `[db_prefix]review_views_tk`;


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_professional_reviews_view','modzzz_professional_reviews_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_professional_reviews_view','modzzz_professional_reviews_browse'); 
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_professional_review';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_professional_review';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_professional_review';  
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_professional_review';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_professional_review'; 
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_professional_review';


-- remove client components
DROP TABLE IF EXISTS `[db_prefix]client_main`; 
DROP TABLE IF EXISTS `[db_prefix]client_images`;
DROP TABLE IF EXISTS `[db_prefix]client_videos`;
DROP TABLE IF EXISTS `[db_prefix]client_sounds`;
DROP TABLE IF EXISTS `[db_prefix]client_files`;
DROP TABLE IF EXISTS `[db_prefix]client_rating`;
DROP TABLE IF EXISTS `[db_prefix]client_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]client_cmts`;
DROP TABLE IF EXISTS `[db_prefix]client_cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]client_views_tk`;


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_professional_clients_view','modzzz_professional_clients_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_professional_clients_view','modzzz_professional_clients_browse'); 
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_professional_client';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_professional_client';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_professional_client';  
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_professional_client';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_professional_client'; 
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_professional_client';



-- remove booking components
DROP TABLE IF EXISTS `[db_prefix]booking_main`;  
DROP TABLE IF EXISTS `[db_prefix]booking_cmts`;
DROP TABLE IF EXISTS `[db_prefix]booking_cmts_track`;
 

DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_professional_bookings_view');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_professional_bookings_view'); 
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_professional_booking';
 
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_professional_booking';

-- remove service components
DROP TABLE IF EXISTS `[db_prefix]service_main`; 
DROP TABLE IF EXISTS `[db_prefix]service_images`;
DROP TABLE IF EXISTS `[db_prefix]service_videos`;
DROP TABLE IF EXISTS `[db_prefix]service_sounds`;
DROP TABLE IF EXISTS `[db_prefix]service_files`;
DROP TABLE IF EXISTS `[db_prefix]service_rating`;
DROP TABLE IF EXISTS `[db_prefix]service_rating_track`;
DROP TABLE IF EXISTS `[db_prefix]service_cmts`;
DROP TABLE IF EXISTS `[db_prefix]service_cmts_track`;
DROP TABLE IF EXISTS `[db_prefix]service_views_tk`;


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_professional_services_view','modzzz_professional_services_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_professional_services_view','modzzz_professional_services_browse'); 
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_professional_service';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_professional_service';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_professional_service';  
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_professional_service';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_professional_service'; 
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_professional_service';



SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_professional_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_professional';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_professional';


DELETE FROM `sys_pre_values` WHERE `Key` IN ('ServiceCurrency','ServiceLength'); 

