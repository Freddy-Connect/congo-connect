
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
DROP TABLE IF EXISTS `[db_prefix]categ`;
DROP TABLE IF EXISTS `[db_prefix]packages`;
DROP TABLE IF EXISTS `[db_prefix]invoices`;
DROP TABLE IF EXISTS `[db_prefix]orders`;
DROP TABLE IF EXISTS `[db_prefix]offers`;
DROP TABLE IF EXISTS `[db_prefix]featured_orders`;
DROP TABLE IF EXISTS `[db_prefix]promo`;
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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_classified_view', 'modzzz_classified_celendar', 'modzzz_classified_main', 'modzzz_classified_my','modzzz_classified_edit','modzzz_classified_category','modzzz_classified_subcategory','modzzz_classified_local','modzzz_classified_local_state', 'modzzz_classified_packages');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_classified_view', 'modzzz_classified_celendar', 'modzzz_classified_main', 'modzzz_classified_my','modzzz_classified_edit','modzzz_classified_category','modzzz_classified_subcategory','modzzz_classified_local','modzzz_classified_local_state', 'modzzz_classified_packages');
DELETE FROM `sys_page_compose` WHERE `Caption` LIKE '_modzzz_classified%';
  
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=classified/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_classified';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_classified';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_classified';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_classified';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_classified';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Classified';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_classified';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_classified';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_classified';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_classified' OR `Type` = 'modzzz_classified_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_classified';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_classified', 'modzzz_classifiedp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_classified';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Classified' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Classified' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Classified';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Classified';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_classified';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Classified' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_classified_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('classified purchase','classified mark as favorite', 'classified extend', 'classified relist', 'classified view classified', 'classified browse', 'classified search', 'classified add classified', 'classified comments delete and edit', 'classified edit any classified', 'classified delete any classified', 'classified mark as featured', 'classified approve classified', 'classified make inquiry', 'classified buy item', 'classified photos add','classified sounds add','classified videos add','classified files add','classified purchase featured', 'classified view contacts', 'classified allow embed');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('classified purchase','classified mark as favorite', 'classified extend', 'classified relist', 'classified view classified', 'classified browse', 'classified search', 'classified add classified', 'classified comments delete and edit', 'classified edit any classified', 'classified delete any classified', 'classified mark as featured', 'classified approve classified', 'classified make inquiry', 'classified buy item', 'classified photos add','classified sounds add','classified videos add','classified files add','classified purchase featured', 'classified view contacts', 'classified allow embed');
   

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_classified_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_classified_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_classified';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'classified';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_classified';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_classified';
 
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_classified_featured_expire_notify','modzzz_classified_featured_admin_notify','modzzz_classified_featured_buyer_notify',
'modzzz_classified_inquiry', 'modzzz_classified_invitation', 'modzzz_classified_expired', 'modzzz_classified_post_expired', 'modzzz_classified_expiring', 'modzzz_classified_make_buy_offer');

-- cron jobs
DELETE FROM `sys_cron_jobs` WHERE `Name` IN ('BxClassified');
 
DELETE FROM `sys_pre_values` WHERE `Key` IN ('ClassifiedType','ClassifiedPaymentType','ClassifiedsCurrency');



SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_classified_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_classified';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_classified';


 