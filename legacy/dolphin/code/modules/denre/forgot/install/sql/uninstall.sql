DELETE FROM `sys_alerts` WHERE `handler_id` IN (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'Db_Afo_Init');
DELETE FROM `sys_alerts_handlers` WHERE `name` = 'Db_Afo_Init';

DELETE FROM `sys_options` WHERE `name` = 'enable_extra_privacy';
DELETE FROM `sys_options` WHERE `name` = 'enable_password_generation';
DELETE FROM `sys_options` WHERE `name` = 'forgot_redirect';
DELETE FROM `sys_options` WHERE `name` = 'forgot_keep_rows_days';
DELETE FROM `sys_options` WHERE `name` = 'forgot_enable_captcha';

DELETE FROM `sys_options_cats` WHERE `name` = 'DB Advanced Forgot';

DELETE FROM `sys_menu_admin` WHERE  `name` = 'Advanced Password Forgot';

DELETE FROM `sys_email_templates` WHERE `name` = 't_ConfirmForgot';

DROP TABLE IF EXISTS `db_tmp_password`;
