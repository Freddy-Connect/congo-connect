-- tables
DROP TABLE IF EXISTS `[db_prefix]main`;
 
  
-- compose pages 
DELETE FROM `sys_page_compose` WHERE `Caption` LIKE '_modzzz_memlog_%';
 
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=memlog/';
DELETE FROM `sys_objects_actions` WHERE `Eval` LIKE '%_modzzz_memlog_%';
  
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_memlog';
DELETE FROM `sys_menu_top` WHERE `name` = 'MemLog';
  
 
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'MemLog' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_memlog_permalinks');
  
-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_memlog_profile_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_memlog_profile_logout' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
 

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('memlog add');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('memlog add');


DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_memlog_moderator');

DELETE FROM `sys_menu_member` WHERE `Link` LIKE 'm/memlog/return%';