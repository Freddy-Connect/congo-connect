CREATE TABLE IF NOT EXISTS `ml_passive_reminder_profiles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` varchar(128) NOT NULL default '',
  `date_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
);

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Modloaded Passive Member Reminder', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('ml_passive_reminder_notify_interval', '30', @iCategId, 'Remind member if not logged in number of days', 'digit', '', '', '10', ''),
('ml_passive_reminder_send_interval', '7', @iCategId, 'Send another reminder in number of days', 'digit', '', '', '', '');	
-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=passive_reminder/', 'm/passive_reminder/', 'ml_passive_reminder_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'ml_passive_reminder', '_ml_passive_reminder', '{siteUrl}modules/?r=passive_reminder/administration/', 'Passive Member Reminder', 'modules/modloaded/passive_reminder/|icon.png', @iMax+1);

SET @iPCOrder = (SELECT MAX(`Order`) FROM `sys_page_compose` WHERE `Page`='member' AND `Column`='1');
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('member', '998px', 'Show Passive Friends', '_ml_passive_reminder_friends', 1, @iPCOrder+1, 'PHP', 'return BxDolService::call(\'passive_reminder\', \'passive_reminder_friends_block\', array());', 1, 66, 'memb', 0);

INSERT INTO `sys_cron_jobs` SET `name` = 'passive_reminder', `time` = '0 * * * *', `class` = 'MlPMRCron', `file` = 'modules/modloaded/passive_reminder/classes/MlPMRCron.php';

INSERT INTO sys_email_templates SET `Subject`='We miss you!', `Body`='<html><head></head><body style=\"font: 12px Verdana; color:#000000\">
<p><b>Dear <NickName></b>,</p>

<p>We and your friends at <SiteName> miss you. Our community has been expanding and adding lots of features, please visit us and come back soon.</p>

<p><b>Thank you!</b></p>

<p>--</p>
<p style=\"font: bold 10px Verdana; color:red\"><SiteName> mail delivery system!!!
<br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', Name='t_PassiveMemberReminder',`Desc`='Email template passive member reminder', LangID=0;
