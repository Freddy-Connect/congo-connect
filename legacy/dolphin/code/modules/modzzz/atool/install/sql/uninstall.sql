-- tables
DROP TABLE IF EXISTS `[db_prefix]page_content`;
DROP TABLE IF EXISTS `[db_prefix]site_stat_archive`;
DROP TABLE IF EXISTS `[db_prefix]sitemap_archive`;
  
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=atool/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_atool';
DELETE FROM `sys_menu_top` WHERE `name` = 'My ATool';
 
  
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'ATool' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_atool_permalinks' );

DELETE FROM `sys_objects_actions` WHERE `Url` LIKE '%modules/?r=atool%' OR `Script` LIKE '%modules/?r=atool%';
 
DELETE FROM `sys_objects_actions` WHERE `Url` = 'modules/modzzz/atool/member.php?ID={ID}';
 
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_atool_about', 'modzzz_atool_advice', 'modzzz_atool_faq', 'modzzz_atool_terms', 'modzzz_atool_help', 'modzzz_atool_contact', 'modzzz_atool_privacy', 'modzzz_atool_chat_rules');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_atool_about', 'modzzz_atool_advice', 'modzzz_atool_faq', 'modzzz_atool_terms', 'modzzz_atool_help', 'modzzz_atool_contact', 'modzzz_atool_privacy', 'modzzz_atool_chat_rules');

 