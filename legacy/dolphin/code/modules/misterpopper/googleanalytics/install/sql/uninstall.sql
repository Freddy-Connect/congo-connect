-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = '[db_prefix]' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = '[db_prefix]_permalinks';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=googleanalytics/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = '[db_prefix]';

-- injections
DELETE FROM `sys_injections` WHERE `name` = '[db_prefix]';
