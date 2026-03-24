-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('[db_prefix]', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('[db_prefix]_permalinks', 'on', 26, 'Enable friendly permalinks in PageEditor', 'checkbox', '', '', NULL, '');

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=emailtmpleditor/', 'm/emailtmpleditor/', '[db_prefix]_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, '[db_prefix]', '_mp_emailtmpleditor', '{siteUrl}modules/?r=emailtmpleditor/administration/', 'Email Template Editor by paan : solution systems', 'edit', @iMax+1);

-- placeholder table
CREATE TABLE IF NOT EXISTS `[db_prefix]_placeholders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `placeholder` VARCHAR( 255 ) NOT NULL,
    INDEX ( `id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- insert placeholders
INSERT INTO `[db_prefix]_placeholders` (`id`, `placeholder`) VALUES
(1, 'Domain'),
(2, 'SiteName'),
(3, 'recipientID'),
(4, 'RealName'),
(5, 'NickName'),
(6, 'Email'),
(7, 'Password'),
(8, 'MediaType'),
(9, 'SenderNickName'),
(10, 'MediaUrl'),
(11, 'UserExplanation'),
(12, 'ViewLink'),
(13, 'EntryUrl'),
(14, 'EntryTitle'),
(15, 'ProfileReference'),
(16, 'VKissLink'),
(17, 'MatchProfileLink'),
(18, 'StrID'),
(19, 'MessageText'),
(20, 'ProfileUrl'),
(21, 'ConfCode'),
(22, 'ConfirmationLink'),
(23, 'profileNickName'),
(24, 'profileID'),
(25, 'profileContactInfo'),
(26, 'MembershipName'),
(27, 'ExpireDays'),
(28, 'MembershipLevel'),
(29, 'reporterID'),
(30, 'spamerID'),
(31, 'Link'),
(32, 'your message here'),
(33, 'Subscription'),
(34, 'SysUnsubscribeLink'),
(35, 'SenderLink'),
(36, 'RequestLink'),
(37, 'Sender'),
(38, 'Recipient'),
(39, 'SpammerUrl'),
(40, 'SpammerNickName'),
(41, 'Page'),
(42, 'Get'),
(43, 'SpamContent'),
(44, 'InviterUrl'),
(45, 'InviterNickName'),
(46, 'InvitationText'),
(47, 'EventName'),
(48, 'EventLocation'),
(49, 'EventStart'),
(50, 'EventUrl'),
(51, 'BroadcastMessage'),
(52, 'ActionName'),
(53, 'UnsubscribeLink'),
(54, 'PosterUrl'),
(55, 'PosterNickName'),
(56, 'TopicTitle'),
(57, 'ReplyText'),
(58, 'FromName'),
(59, 'PosterName');