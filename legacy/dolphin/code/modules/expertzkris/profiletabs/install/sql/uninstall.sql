DROP TABLE IF EXISTS `rw_ptabs_profile_tabs`;
DROP TABLE IF EXISTS `rw_ptabs_tabs`;

DELETE FROM `sys_menu_admin` WHERE `url` = '{siteUrl}modules/?r=ptabs/administration/';

SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Profile Tabs' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `name` = 'Profile Tabs';

DELETE FROM `sys_page_compose` WHERE `Caption` = 'rw_ptabs_profiletabs';