SET @iKategId = (SELECT `id` FROM `sys_options_cats` WHERE `name` = 'peopleyoumayknow' LIMIT 1);
DELETE FROM `sys_options_cats` WHERE `id` = @iKategId;
DELETE FROM `sys_options` WHERE `kateg` = @iKategId;
DELETE FROM `sys_page_compose` WHERE `Caption` = '_ibdw_peopleyoumayknow_titlemodule';
DELETE FROM `sys_menu_admin` WHERE `title` = 'Peoples You May Know';