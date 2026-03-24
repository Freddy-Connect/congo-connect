-- tables
DROP TABLE IF EXISTS `[db_prefix]archive`;
  
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=manager/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_manager';
DELETE FROM `sys_menu_top` WHERE `name` = 'My Manager';
 
  
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Manager' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_manager_permalinks' );
 