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
DROP TABLE IF EXISTS `[db_prefix]profiles`;
DROP TABLE IF EXISTS `[db_prefix]cities`;
DROP TABLE IF EXISTS `[db_prefix]countries`;
DROP TABLE IF EXISTS `[db_prefix]packages`;
DROP TABLE IF EXISTS `[db_prefix]invoices`;
DROP TABLE IF EXISTS `[db_prefix]orders`;
DROP TABLE IF EXISTS `[db_prefix]featured_orders`;
DROP TABLE IF EXISTS `[db_prefix]promo`;
DROP TABLE IF EXISTS `[db_prefix]inquiry`;
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
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_investment_view', 'modzzz_investment_celendar', 'modzzz_investment_main', 'modzzz_investment_my','modzzz_investment_edit','modzzz_investment_category','modzzz_investment_subcategory','modzzz_investment_local','modzzz_investment_local_state', 'modzzz_investment_packages');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_investment_view', 'modzzz_investment_celendar', 'modzzz_investment_main', 'modzzz_investment_my','modzzz_investment_edit','modzzz_investment_category','modzzz_investment_subcategory','modzzz_investment_local','modzzz_investment_local_state', 'modzzz_investment_packages');
DELETE FROM `sys_page_compose` WHERE `Page` = 'index' AND `Desc` = 'Investments';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` = 'User Investments';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'My Investments';
DELETE FROM `sys_page_compose` WHERE `Page` = 'member' AND `Desc` = 'Local Investments';

 

-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=investment/';
DELETE FROM `sys_objects_vote` WHERE `ObjectName` = 'modzzz_investment';
DELETE FROM `sys_objects_cmts` WHERE `ObjectName` = 'modzzz_investment';
DELETE FROM `sys_objects_views` WHERE `name` = 'modzzz_investment';
DELETE FROM `sys_objects_categories` WHERE `ObjectName` = 'modzzz_investment';
DELETE FROM `sys_categories` WHERE `Type` = 'modzzz_investment';
DELETE FROM `sys_categories` WHERE `Type` = 'bx_photos' AND `Category` = 'Investment';
DELETE FROM `sys_objects_tag` WHERE `ObjectName` = 'modzzz_investment';
DELETE FROM `sys_tags` WHERE `Type` = 'modzzz_investment';
DELETE FROM `sys_objects_search` WHERE `ObjectName` = 'modzzz_investment';
DELETE FROM `sys_objects_actions` WHERE `Type` = 'modzzz_investment' OR `Type` = 'modzzz_investment_title';
DELETE FROM `sys_stat_site` WHERE `Name` = 'modzzz_investment';
DELETE FROM `sys_stat_member` WHERE TYPE IN('modzzz_investment', 'modzzz_investmentp');
DELETE FROM `sys_account_custom_stat_elements` WHERE `Label` = '_modzzz_investment';
 
-- top menu
SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Investment' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

SET @iCatRoot := (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Investment' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iCatRoot;
DELETE FROM `sys_menu_top` WHERE `ID` = @iCatRoot;

DELETE FROM `sys_menu_top` WHERE `Parent` = 9 AND `Name` = 'Investment';
DELETE FROM `sys_menu_top` WHERE `Parent` = 4 AND `Name` = 'Investment';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_investment';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Investment' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_investment_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('investment mark as favorite', 'investment purchase', 'investment extend', 'investment relist', 'investment view investment', 'investment browse', 'investment search', 'investment add investment', 'investment comments delete and edit', 'investment edit any investment', 'investment delete any investment', 'investment mark as featured', 'investment approve investment', 'investment make inquiry', 'investment buy item', 'investment photos add','investment sounds add','investment videos add','investment files add','investment purchase featured');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('investment mark as favorite','investment purchase', 'investment extend', 'investment relist', 'investment view investment', 'investment browse', 'investment search', 'investment add investment', 'investment comments delete and edit', 'investment edit any investment', 'investment delete any investment', 'investment mark as featured', 'investment approve investment', 'investment make inquiry', 'investment buy item', 'investment photos add','investment sounds add','investment videos add','investment files add','investment purchase featured');
   

-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_investment_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_investment_media_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- member menu
DELETE FROM `sys_menu_member` WHERE `Name` = 'modzzz_investment';

-- privacy
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'investment';

-- subscriptions
DELETE FROM `sys_sbs_entries` USING `sys_sbs_types`, `sys_sbs_entries` WHERE `sys_sbs_types`.`id`=`sys_sbs_entries`.`subscription_id` AND `sys_sbs_types`.`unit`='modzzz_investment';
DELETE FROM `sys_sbs_types` WHERE `unit`='modzzz_investment';
 
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_investment_featured_expire_notify','modzzz_investment_featured_admin_notify','modzzz_investment_featured_buyer_notify',
'modzzz_investment_inquiry', 'modzzz_investment_invitation', 'modzzz_investment_expired', 'modzzz_investment_post_expired', 'modzzz_investment_expiring' );

-- cron jobs
DELETE FROM `sys_cron_jobs` WHERE `Name` IN ('BxInvestment');
 
DELETE FROM `sys_pre_values` WHERE `Key` = 'InvestmentCurrency';

DELETE FROM `sys_pre_values` WHERE `Key` = 'InvestmentStade';

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_investment_map_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

-- sitemap
DELETE FROM `sys_objects_site_maps` WHERE `object` = 'modzzz_investment';

-- chart
DELETE FROM `sys_objects_charts` WHERE `object` = 'modzzz_investment';

