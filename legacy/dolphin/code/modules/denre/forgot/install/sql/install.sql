--
-- Table structure for table `Profiles`
--

CREATE TABLE IF NOT EXISTS `db_tmp_password` (
	`ID` int(10) unsigned NOT NULL,
	`Password` varchar(40) NOT NULL DEFAULT '',
	`Salt` varchar(10) NOT NULL DEFAULT '',
	`pwd_key` varchar(40) NOT NULL DEFAULT '',
    `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
	PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- alert handlers
INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `eval`) VALUES
	('Db_Afo_Init', '', '', 'BxDolService::call(''forgot'', ''InitAdvForgot'', array($this));');

-- alerts
SET @iHandler = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'Db_Afo_Init' LIMIT 1);
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
	('form', 'init', @iHandler);

-- settings
SET @iMaxMenuOrder = (SELECT MAX( `menu_order` ) FROM `sys_options_cats`) +1;
INSERT INTO `sys_options_cats` (`name`, `menu_order`)
	VALUES ('DB Advanced Forgot', @iMaxMenuOrder);

SET @iMaxOrder = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'DB Advanced Forgot');
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
	('enable_extra_privacy', 'on', @iMaxOrder, 'No "email was not found" message (password retrieval)', 'checkbox', '', '', 1, ''),
	('enable_password_generation', 'on', @iMaxOrder, 'Use temporary password to logon?', 'checkbox', '', '', 2, ''),
	('forgot_redirect', 'profile edit', @iMaxOrder, 'Page to redirect to, after confirmation?', 'select', '', '', 3, 'accueil,account,profile,profile edit'),
	('forgot_keep_rows_days', '10', @iMaxOrder, 'Days after which unconfirmed retrievals are deleted?', 'digit', '', '', 4, ''),
	('forgot_enable_captcha', '', @iMaxOrder, 'Enable Captcha?', 'checkbox', '', '', 5, '');

-- default email template
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
	('t_ConfirmForgot', 'Password Reset Confirmation Required', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RealName></b>,</p>\r\n\r\n<p>Your member ID: <b><recipientID></b></p>\r\n\r\n<p>Your password: <b><Password></b></p>\r\n\r\n<p>Please confirm your password change, using the following link: <VerifyURL></p>\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Confirm Password retrieval', 0);

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
	(2, 'Advanced Password Forgot', '_db_afo', '{siteUrl}modules/?r=forgot/administration/', 'Advanced password forgot module by Denre', 'lock', @iMax+1);

-- sys_cron_jobs
INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`) VALUES
    ('db_forgot', '0 0 * * *', 'DbForgotCron', 'modules/denre/forgot/classes/DbForgotCron.php');
