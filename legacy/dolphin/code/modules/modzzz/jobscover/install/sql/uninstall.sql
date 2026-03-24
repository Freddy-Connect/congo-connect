-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=jobscover/';
 
DELETE FROM `sys_menu_top` WHERE `Caption` IN ('_modzzz_jobscover_menu_view_cover');
  
UPDATE `sys_menu_top` SET `Link` = REPLACE(`Link`, '|modules/?r=jobscover/cover/add/|modules/?r=jobscover/cover/edit/|modules/?r=jobscover/cover/browse/', '') WHERE `Parent`=0 AND `Name`='Jobs' AND `Type`='system' AND `Caption`='_modzzz_jobs_menu_root'; 
 

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_jobscover';

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'JobsCover' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'modzzz_jobscover_permalinks';

-- membership levels
DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN('jobscover add cover');
DELETE FROM `sys_acl_actions` WHERE `Name` IN('jobscover add cover');
 
DROP TABLE IF EXISTS `[db_prefix]main`;
DROP TABLE IF EXISTS `[db_prefix]images`;
 
-- compose pages 
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_jobscover_browse');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_jobscover_browse'); 
DELETE FROM `sys_page_compose` WHERE `Caption` LIKE '_modzzz_jobscover_block_%';
 
DELETE FROM  `sys_objects_actions` WHERE `Url` = 'modules/?r=jobscover/cover/add/{ID}';



-- alerts
SET @iHandler := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'modzzz_jobs_parent_delete' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;