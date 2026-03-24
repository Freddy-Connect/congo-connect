DELETE FROM `sys_page_compose` WHERE `Caption` = '_idbw_1col_mod';

DELETE FROM `sys_menu_admin` WHERE `name` = 'Activation 1COL';
DELETE FROM `sys_menu_admin` WHERE `name` = '1Col Config';

DROP TABLE IF EXISTS `1col_config`;