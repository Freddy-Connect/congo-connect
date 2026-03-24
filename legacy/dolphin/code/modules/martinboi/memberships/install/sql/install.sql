----------------------------------------------------------
-- Injections
----------------------------------------------------------
INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES ('memberships', '0', 'injection_head', 'php', 'require_once(BX_DIRECTORY_PATH_MODULES.''martinboi/memberships/include.php'');', '0', '1');

----------------------------------------------------------
-- Pages
----------------------------------------------------------
SET @iMaxOrder = (SELECT `Order` + 1 FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('mmp_main', 'Membership Management Pro', @iMaxOrder+2);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
  	('mmp_main', '1140px', 'Tight block', '_dol_subs_block_tight', '1', '0', 'Tight', '', '1', '28.1', 'non,memb', '0'),
    ('mmp_main', '1140px', 'Wide block', '_dol_subs_block_wide', '2', '0', 'Wide', '', '1', '71.9', 'non,memb', '0');

----------------------------------------------------------
-- Options
----------------------------------------------------------

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Membership Management Pro', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('dol_subs_permalinks', 'on', 26, 'Enable friendly permalinks in Membership Management Pro', 'checkbox', '', '', '0', ''),
('dol_subs_processors', '', @iCategId, 'Settings for payment processors', 'digit', '', '', '0', ''),
('dol_subs_data_forwarding', '', @iCategId, 'Settings for response data forwarding', 'digit', '', '', '0', ''),
('dol_subs_config', '', @iCategId, 'General configuration for the module', 'digit', '', '', '0', ''),
('dol_subs_membership_options', '', @iCategId, 'Membership Options', 'digit', '', '', '0', '');

UPDATE `sys_options` SET `VALUE` = '' WHERE `Name` = 'enable_promotion_membership';


----------------------------------------------------------
-- Permalinks
----------------------------------------------------------

INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=memberships/', 'm/memberships/', 'mmp_permalinks');

----------------------------------------------------------
-- Languages/Strings
----------------------------------------------------------
-- Upgrade Membership Default Link 
SET @keyID = (SELECT `ID` FROM `sys_localization_keys` WHERE `Key`='_MEMBERSHIP_UPGRADE_FROM_STANDARD');
UPDATE `sys_localization_strings` SET `String`='<a href="m/memberships/"><div style="margin:8px 0px">Manage Membership</div></a>' WHERE `IDKey`=@keyID;
SET @keyID2 = (SELECT `ID` FROM `sys_localization_keys` WHERE `Key`='_MEMBERSHIP_EXPIRES_NEVER');
UPDATE `sys_localization_strings` SET `String`='<a href="m/memberships/"><div style="margin:8px 0px">Manage Membership</div></a>' WHERE `IDKey`=@keyID2;

--Update new membership items
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 118, 'Memberships', '_dol_subs_title', 'm/memberships/', 4, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
UPDATE sys_menu_top SET Link='m/memberships/' WHERE Name='Memberships';

--Upgrade Error Messages
--UPDATE `sys_localization_strings` SET `String`='<div style="width: 80%">Your current membership doesn\'t allow this action. <a href="m/memberships/">Click Here</a> to upgrade your account.</div>' WHERE `IDKey`=840;

----------------------------------------------------------
-- Removals
----------------------------------------------------------
DELETE FROM `sys_menu_top` WHERE `Name`='My Membership' LIMIT 1;
DELETE FROM `sys_menu_member` WHERE `Name`='bx_membership' LIMIT 1;

----------------------------------------------------------
-- Admin Menu
----------------------------------------------------------

-- Admin Main Menu
INSERT INTO `sys_menu_admin` (`ID`,`parent_id`,`name`,`Title`,`Url`,`Description`,`Icon`,`Icon_large`,`Check`,`Order`)VALUES
(NULL, '0', 'memberships', 'Memberships', '{siteUrl}m/memberships/settings/&menu=main', 'Dolphin Subscriptions', 'modules/martinboi/memberships/|memberships-icon.png', 'modules/martinboi/memberships/|memberships-icon.png', '', '255');

SET @iParentIDSubs = (SELECT `id` FROM `sys_menu_admin` WHERE `name` = 'memberships');
INSERT INTO `sys_menu_admin` (`ID`,`parent_id`,`name`,`Title`,`Url`,`Description`,`Icon`,`Icon_large`,`Check`,`Order`)VALUES
(NULL, @iParentIDSubs, 'dolphin_subs_settings', 'Settings', '{siteUrl}m/memberships/settings/', 'Dolphin Subs Settings', 'modules/martinboi/memberships/|settings-icon.png', 'modules/martinboi/memberships/|settings-icon.png', '', '0');

INSERT INTO `sys_menu_admin` (`ID`,`parent_id`,`name`,`Title`,`Url`,`Description`,`Icon`,`Icon_large`,`Check`,`Order`)VALUES
(NULL, @iParentIDSubs, 'dolphin_subs_memberships', 'Manage Memberships', '{siteUrl}m/memberships/memberships/', 'Dolphin Subs Manage Memberships', 'modules/martinboi/memberships/|memberships-icon.png', 'modules/martinboi/memberships/|memberships-icon.png', '', '1');

INSERT INTO `sys_menu_admin` (`ID`,`parent_id`,`name`,`Title`,`Url`,`Description`,`Icon`,`Icon_large`,`Check`,`Order`)VALUES
(NULL, @iParentIDSubs, 'dolphin_subs_membership_options', 'Membership Options', '{siteUrl}m/memberships/membership_options/', 'Dolphin Subs Membership Options', 'modules/martinboi/memberships/|settings-icon.png', 'modules/martinboi/memberships/|settings-icon.png', '', '2');

INSERT INTO `sys_menu_admin` (`ID`,`parent_id`,`name`,`Title`,`Url`,`Description`,`Icon`,`Icon_large`,`Check`,`Order`)VALUES
(NULL, @iParentIDSubs, 'dolphin_subs_subscriptions', 'Subscribers', '{siteUrl}m/memberships/subscriptions/', 'Dolphin Subs Subscriptions', 'modules/martinboi/memberships/|subscriptions-icon.png', 'modules/martinboi/memberships/|subscriptions-icon.png', '', '3');

INSERT INTO `sys_menu_admin` (`ID`,`parent_id`,`name`,`Title`,`Url`,`Description`,`Icon`,`Icon_large`,`Check`,`Order`)VALUES
(NULL, @iParentIDSubs, 'dolphin_subs_payments', 'Payments', '{siteUrl}m/memberships/payments/', 'Dolphin Subs Subscriptions', 'money', '', '', '4');

----------------------------------------------------------
-- ACL
----------------------------------------------------------
-- Alter ACL Table
ALTER TABLE `sys_acl_levels` 
ADD `Free` INT(11) NOT NULL,
ADD `Trial` tinyint(1) NOT NULL,
ADD `Trial_Length` int(11) NOT NULL;

INSERT INTO `sys_acl_levels` (`ID`, `Name`, `Icon`, `Description`, `Active`, `Purchasable`, `Removable`, `Free`) VALUES
(NULL, 'Silver', 'non-member.png', 'A sample membership allowing some features.', 'yes', 'yes', 'yes', 0),
(NULL, 'Gold', 'promotion.png', 'A full-access Sample membership.', 'yes', 'yes', 'yes', 0);

UPDATE sys_acl_levels SET Free='0'; 
UPDATE sys_acl_levels SET Description='Cannot be removed.' WHERE ID='1'; 
UPDATE sys_acl_levels SET Description='Cannot be removed.' WHERE ID='2'; 
UPDATE sys_acl_levels SET Description='Cannot be removed.' WHERE ID='3'; 

-- Insert Prices
ALTER TABLE `sys_acl_level_prices` 
ADD `Unit` VARCHAR( 11 ) NOT NULL,
ADD `Length` VARCHAR( 11 ) NOT NULL,
ADD `Auto` INT( 11 ) NOT NULL;

SET @iSilverID = (SELECT `ID` FROM `sys_acl_levels` WHERE `Name` = 'Silver');
SET @iGoldID = (SELECT `ID` FROM `sys_acl_levels` WHERE `Name` = 'Gold');
INSERT INTO `sys_acl_level_prices` (`id`, `IDLevel`, `Days`, `Price`, `Unit`, `Length`, `Auto`) VALUES
(NULL, @iSilverID, '', '0.99','Days','30', 1),
(NULL, @iGoldID, '', '1.99','Months','2', 1);

----------------------------------------------------------
-- Tables
----------------------------------------------------------

CREATE TABLE IF NOT EXISTS `[db_prefix]payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txn_id` varchar(100) DEFAULT NULL,
  `subscr_id` varchar(100) DEFAULT NULL,
  `auth_code` varchar(100) DEFAULT NULL,
  `membership_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` varchar(15) NOT NULL,
  `date` datetime NOT NULL,
  `status` varchar(25) NOT NULL,
  `processor` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



----------------------------------------------------------
-- Bottom Menu
----------------------------------------------------------

INSERT INTO `sys_menu_member` (`ID`, `Caption`, `Name`, `Icon`, `Link`, `Script`, `Eval`, `PopupMenu`, `Order`, `Active`, `Editable`, `Deletable`, `Target`, `Position`, `Type`, `Parent`, `Bubble`, `Description`) VALUES
(NULL, '_dol_subs_title', 'hm_dol_subs', 'trophy', 'm/memberships/', '', '', '', 0, '1', 0, 0, '', 'top_extra', 'link', 0, '', '_dol_subs_title');
-- modules/martinboi/memberships/|menu-bar-icon.png

----------------------------------------------------------
-- Alerts
----------------------------------------------------------

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'hm_dol_subs_alerts', 'MbMmpAlertsResponse', 'modules/martinboi/memberships/classes/MbMmpAlertsResponse.php', '');

SET @iHandler = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'hm_dol_subs_alerts');

INSERT INTO `sys_alerts` VALUES
(NULL, 'subs', 'cur_page', @iHandler),
(NULL, 'profile', 'join', @iHandler),
(NULL, 'profile', 'delete', @iHandler),
(NULL, 'profile', 'login', @iHandler),
(NULL, 'profile', 'logout', @iHandler);