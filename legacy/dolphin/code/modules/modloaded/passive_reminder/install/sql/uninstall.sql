DROP TABLE IF EXISTS `ml_passive_reminder_profiles`;
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Modloaded Passive Member Reminder' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=passive_reminder/';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'ml_passive_reminder';

DELETE FROM `sys_cron_jobs` WHERE `name` = 'passive_reminder';
DELETE FROM `sys_page_compose` WHERE `Caption`='_ml_passive_reminder_friends_not_logged_in' AND `Func`='PHP';
DELETE FROM `sys_email_templates` WHERE `Name` = 't_PassiveMemberReminder';

