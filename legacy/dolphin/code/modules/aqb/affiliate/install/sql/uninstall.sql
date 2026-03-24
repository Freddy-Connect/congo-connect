SET @sPluginName = 'aqb_affiliate';
SET @sPluginTitle = 'AQB Forced Matrix System';
DELETE FROM `sys_menu_admin` WHERE `name` = @sPluginName;

DROP TABLE IF EXISTS `[db_prefix]banners`;
DROP TABLE IF EXISTS `[db_prefix]all_history`;
DROP TABLE IF EXISTS `[db_prefix]journal`;
DROP TABLE IF EXISTS `[db_prefix]referrals`;
DROP TABLE IF EXISTS `[db_prefix]memlevels_pricing`;
DROP TABLE IF EXISTS `[db_prefix]transactions`;
DROP TABLE IF EXISTS `[db_prefix]invitations`;
DROP TABLE IF EXISTS `[db_prefix]matrix`;
DROP TABLE IF EXISTS `[db_prefix]memlevels_matrix`;

DELETE  `sys_alerts_handlers`,`sys_alerts` 
FROM `sys_alerts_handlers`,`sys_alerts` 
WHERE `sys_alerts`.`handler_id` = `sys_alerts_handlers`.`id`  AND `sys_alerts_handlers`.`name` = 'aqb_aff_page_alert';

DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_aff_ref_title';
DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_aff_my_referrals';
DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_aff_my_affilate';
DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_aff_my_history';
DELETE FROM `sys_menu_top` WHERE `Caption` = '_aqb_aff_my_invitation';

DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'aqb_aff_my_ref_page';
DELETE FROM `sys_page_compose` WHERE `Page` = 'aqb_aff_my_ref_page';

DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'aqb_aff_my_history_page';
DELETE FROM `sys_page_compose` WHERE `Page` = 'aqb_aff_my_history_page';

DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'aqb_aff_my_aff_page';
DELETE FROM `sys_page_compose` WHERE `Page` = 'aqb_aff_my_aff_page';

DELETE FROM `sys_page_compose` WHERE `Caption` IN ('_aqb_aff_my_account_referrals','_aqb_aff_best_referrals_index', '_aqb_aff_my_invited_member', '_aqb_aff_my_account_referrals_link');

DELETE FROM `sys_email_templates` WHERE `Name` IN ('t_AqbAffMemberInvitation', 't_AqbAffCommissionPaid');


SET @iId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = @sPluginTitle);
DELETE FROM `sys_options` WHERE `kateg`= @iId;
DELETE FROM `sys_options_cats` WHERE `name`=@sPluginTitle;
DELETE FROM `sys_options` WHERE `Name`='permalinks_module_aqb_points';

DELETE FROM `sys_options` WHERE `Name` IN ('aqb_forced_matrix', 'aqb_matrix_width', 'aqb_matrix_income', 'aqb_matrix_spillover', 'aqb_matrix_timer');

DELETE FROM `sys_permalinks` WHERE `check` = CONCAT('permalinks_module_', @sPluginName);
DELETE FROM `sys_cron_jobs` WHERE `name` = 'aqb_aff_clean';                                      