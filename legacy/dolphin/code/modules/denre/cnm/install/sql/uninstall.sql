DELETE FROM `sys_menu_admin` WHERE `name` = "db_cnm";
DELETE FROM `sys_options` WHERE `Name` LIKE "db_cnm_%";
DELETE FROM `sys_options_cats` WHERE `name` = 'DbCNM';
DELETE FROM `sys_alerts` WHERE `handler_id` IN (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'db_cnm');
DELETE FROM `sys_alerts_handlers` WHERE `name` = 'db_cnm';
