-- tables
DROP TABLE IF EXISTS `[db_prefix]admin`;
DROP TABLE IF EXISTS `[db_prefix]preference`;
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_autofriend';
  
-- settings
SET @iCategoryID := (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'AutoFriend' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategoryID;
DELETE FROM `sys_options` WHERE `kateg` = @iCategoryID;
   
-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_AutoFriend');

-- cron_jobs
DELETE FROM `sys_cron_jobs` WHERE `name` = 'BxAutoFriendInit';



-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('autofriend add friend');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('autofriend add friend');


-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_autofriend_membership_change' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_autofriend_profile_join' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler; 
   
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_autofriend_profile_change' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler; 
  


