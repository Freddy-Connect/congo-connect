DELETE FROM `sys_page_compose` WHERE `Caption` = '_ibdw_thirdcolumn_modulename';
DELETE FROM `sys_menu_admin` WHERE `name` = 'Activation 3COL';
DELETE FROM `sys_menu_admin` WHERE `name` = '3Col Config';

DROP TABLE IF EXISTS `3col_config`;