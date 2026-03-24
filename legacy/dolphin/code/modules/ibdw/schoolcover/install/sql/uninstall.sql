DELETE FROM `sys_page_compose` WHERE `Caption` = '_ibdw_schoolcover_modulename';
DELETE FROM `sys_menu_admin` WHERE `name` = 'School Cover';
SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'School Cover' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;
DELETE FROM `sys_options` WHERE `kateg` = @iKategId;
DELETE FROM `sys_cron_jobs` WHERE `name` = 'schoolcover';