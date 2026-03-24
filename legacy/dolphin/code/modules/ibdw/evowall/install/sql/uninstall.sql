DELETE FROM `sys_page_compose` WHERE `Caption` = '_ibdw_evowall_member_news';
DELETE FROM `sys_privacy_actions` WHERE `module_uri` = 'evowall';
DELETE FROM `sys_menu_admin` WHERE `name` = 'EVO Wall';
SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'EVO Wall' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;
DELETE FROM `sys_options` WHERE `kateg` = @iKategId;
DROP TABLE IF EXISTS `photodeluxe_likeopt`;

DELETE `sys_acl_actions`, `sys_acl_matrix` FROM `sys_acl_actions`, `sys_acl_matrix` WHERE `sys_acl_matrix`.`IDAction` = `sys_acl_actions`.`ID` AND `sys_acl_actions`.`Name` IN ('EVO WALL - Comments and like view', 'EVO WALL - Comments and like add', 'EVO WALL - Post sharing', 'EVO WALL - Personal messages', 'EVO WALL - Photos', 'EVO WALL - Videos', 'EVO WALL - Groups', 'EVO WALL - Events', 'EVO WALL - Sites', 'EVO WALL - Polls', 'EVO WALL - Ads', 'EVO WALL - Blogs', 'EVO WALL - Sounds', 'EVO WALL - Pages');
DELETE FROM `sys_acl_actions` WHERE `Name` IN ('EVO WALL - Comments view', 'EVO WALL - Comments add', 'EVO WALL - Like view', 'EVO WALL - Like add', 'EVO WALL - Post sharing', 'EVO WALL - Personal messages', 'EVO WALL - Photos', 'EVO WALL - Videos', 'EVO WALL - Groups', 'EVO WALL - Events', 'EVO WALL - Sites', 'EVO WALL - Polls', 'EVO WALL - Ads', 'EVO WALL - Blogs', 'EVO WALL - Sounds', 'EVO WALL - Content Box', 'EVO WALL - Groups');
DELETE FROM `sys_cron_jobs` WHERE `name` = 'evowall';

-- email templates
DELETE FROM `sys_email_templates` WHERE `Name` LIKE '%_ibdw_evowall%';