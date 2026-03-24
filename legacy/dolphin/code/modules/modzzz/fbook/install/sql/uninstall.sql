DELETE FROM `sys_page_compose` WHERE Caption='_modzzz_fbook_block_comments';
  
-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_fbook_install' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;

 -- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=fbook/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_fbook';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'FBook' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_fbook_permalinks';
DELETE FROM `sys_options` WHERE `Name` LIKE 'modzzz_fbook%';
 

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('fbook view');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('fbook view');
