
DROP TABLE `db_bruteforce_cnt`;

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Db Bruteforce' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

-- sys_page_compose_pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` = "bruteforce";

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'db_bruteforce';

-- alert handlers
DELETE FROM `sys_alerts` WHERE `handler_id` IN (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` LIKE "Db_Bruteforce_%");

-- alerts
DELETE FROM `sys_alerts_handlers` WHERE `name` LIKE "Db_Bruteforce_%";

-- sys_cron_jobs
DELETE FROM `sys_cron_jobs` WHERE `name` = "db_bruteforce";
