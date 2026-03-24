-- tables 
DROP TABLE IF EXISTS `[db_prefix]main`;
   
-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=rssinfo/';
  
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_rssinfo';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'RssInfo' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_rssinfo_permalinks';
 
DELETE FROM `sys_cron_jobs` WHERE `name`='modzzz_rssinfo';
