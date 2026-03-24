--pages
DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('mmp_main');
DELETE FROM `sys_page_compose` WHERE `Page` IN('mmp_main');

-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Membership Management Pro' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;
DELETE FROM `sys_options` WHERE `Name` = 'dol_subs_permalinks';

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=memberships/';

-- Injections
DELETE FROM `sys_injections` WHERE `name` = 'memberships';
DELETE FROM `sys_injections` WHERE `name` = 'dolphin_subs';

-- Upgrade link Key
SET @keyID = (SELECT `ID` FROM `sys_localization_keys` WHERE `Key`='_MEMBERSHIP_UPGRADE_FROM_STANDARD');
UPDATE `sys_localization_strings` SET `String`='<a href="modules/?r=membership/index/">Click here</a> to upgrade.' WHERE `IDKey`=@keyID;
SET @keyID2 = (SELECT `ID` FROM `sys_localization_keys` WHERE `Key`='_MEMBERSHIP_EXPIRES_NEVER');
UPDATE `sys_localization_strings` SET `String`='expires: never' WHERE `IDKey`=@keyID2;

--Upgrade subscriptions Link
DELETE FROM `sys_menu_top` WHERE `Name`='Memberships';

--Upgrade Error Messages
UPDATE `sys_localization_strings` SET `String`='<div style="width: 80%">You are not currently an active member. Please ask the site <a href="mailto:{7}">administrator</a> to make you an active member so you can use this feature.</div>' WHERE `IDKey`=840;

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'dol_subs';
DELETE FROM `sys_menu_admin` WHERE `name` = 'memberships';
DELETE FROM `sys_menu_admin` WHERE `name` = 'dolphin_subs_settings';
DELETE FROM `sys_menu_admin` WHERE `name` = 'dolphin_subs_memberships';
DELETE FROM `sys_menu_admin` WHERE `name` = 'dolphin_subs_membership_options';
DELETE FROM `sys_menu_admin` WHERE `name` = 'dolphin_subs_subscriptions';
DELETE FROM `sys_menu_admin` WHERE `name` = 'dolphin_subs_payments';

DROP TABLE `mmp_payments`;

ALTER TABLE `sys_acl_levels`
  DROP `Free`,
  DROP `Trial`,
  DROP `Trial_Length`;

ALTER TABLE `sys_acl_level_prices`
  DROP `Unit`,
  DROP `Length`,
  DROP `Auto`;

UPDATE sys_acl_levels SET Description='' WHERE ID='1'; 
UPDATE sys_acl_levels SET Description='' WHERE ID='2'; 
UPDATE sys_acl_levels SET Description='' WHERE ID='3'; 
DELETE FROM `sys_acl_levels` WHERE `Name` = 'Silver';
DELETE FROM `sys_acl_levels` WHERE `Name` = 'Gold';

--INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
--(22, 17, 'membership_levels', '_adm_mmi_membership_levels', '{siteAdminUrl}memb_levels.php', 'For setting up different membership levels, different actions for each membership level and action limits', 'mmi_membership_levels.gif', '', '', 5);

-- Remove bottom menu icon
DELETE FROM `sys_menu_member` WHERE `Name` = 'hm_dol_subs';

DELETE FROM `sys_profile_fields` WHERE `Name` = 'Choose Membership';

SET @iHandler = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'hm_dol_subs_alerts');
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iHandler;
DELETE FROM `sys_alerts` WHERE `handler_id` = @iHandler;
