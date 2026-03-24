SET @sPluginName = 'aqb_credits';
SET @sPluginTitle = 'Credits System';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_credits_admin', '{siteUrl}modules/?r=aqb_credits/administration/', 'Credits System', 'credit-card-alt', '', '', @iOrder + 1);

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES (@sPluginTitle, @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('aqb_credits_price_currency', '0.5', @iCategId, 'Price for one credit (<b>in default currency</b>)', 'digit', '', '', 0, ''),
('aqb_credits_price_points', '10', @iCategId, 'Price for one credit in points (<b>only if points system is installed</b>)', 'digit', '', '', 1, ''),
('aqb_credits_time', '0', @iCategId, 'Credits Lifetime period in days <b>(0 = unlimited)</b>', 'digit', '', '', 2, ''),
('aqb_credits_allow_to_buy', 'on', @iCategId, 'Allow to buy credits', 'checkbox', '', '', 3, ''),
('aqb_credits_allow_exchange_for_points', 'on', @iCategId, 'Allow to exchange points for credits', 'checkbox', '', '', 4, ''),
('aqb_credits_per_page_history', '20', @iCategId, 'Records number on history page', 'digit', '', '', 5, ''),
('aqb_credits_allow_to_hide_window', 'on', @iCategId, 'Allow members to block popup notification window with not available actions', 'checkbox', '', '', 6, ''),
('aqb_credits_hide_period', '20', @iCategId, 'How long don''t show popup notification if member chooses to block it, in days<b> (0 = never)</b>', 'digit', '', '', 7, ''),
('aqb_credits_enable_present_credits', 'on', @iCategId, 'Enable present credits feautre', 'checkbox', '', '', 8, ''),
('aqb_credits_actions_settings', '', 0, '', 'text', '', '', 0, '');

SET @iOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`) + 1;
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('aqb_credits_sys', 'Credits System', @iOrder);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('aqb_credits_sys', '1140px', 'Credits History', '_aqb_credits_history_block_title', 3, 0, 'History', '', 1, 66, 'memb', 0),
('aqb_credits_sys', '1140px', 'Credits Info', '_aqb_credits_stat_block_title', 2, 0, 'Info', '', 1, 34, 'memb', 0),
('aqb_credits_sys', '1140px', 'Available for Credits', '_aqb_credits_available_actions_block_title', 2, 1, 'Credits', '', 1, 34, 'memb', 0);

INSERT INTO `sys_menu_top` (`Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(118, 'My Credits', '_aqb_credits_my_credits_title', 'modules/?r=aqb_credits/my_credits/', 2, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, '');

CREATE TABLE IF NOT EXISTS `[db_prefix]history` (
  `id` int( 11 ) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL default '0',
  `member_id` int(11) NOT NULL default '0',
  `action` enum('spent','bought','exchanged', 'got', 'present', 'exchange') NOT NULL default 'spent',
  `number` bigint(20) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  `param` varchar(255) NOT NULL default '',
   PRIMARY KEY  (`id`)
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

CREATE TABLE `[db_prefix]transactions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `price` float NOT NULL default '0',
  `status` enum('pending', 'active') NOT NULL default 'pending', 
  `type` enum('buy', 'exchange') NOT NULL default 'buy',  
  `date` int(11) unsigned default NULL,
  `amount` int( 11 ) NOT NULL default '0',
  `buyer_id` int( 11 ) NOT NULL default '0',
  `tnx` varchar(20) NOT NULL default '',
  `credit_id` int(11) unsigned default NULL,
  `payment_status` varchar(10) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

INSERT INTO `sys_injections` SET 
	`name`       = 'aqb_credits_popup',
	`page_index` = '0',
	`key`        = 'injection_footer',
	`type`       = 'php',
	`data`       = 'return BxDolService::call(''aqb_credits'', ''get_popup_code'');',
	`replace`    = '0',
	`active`     = '1';
	
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
('{evalResult}', 'credit-card-alt', '', 'showPopupAnyHtml (''modules/?r=aqb_credits/get_present_credits_form/{ID}'');', 'return BxDolService::call(''aqb_credits'',''get_present_button'',array({ID}));', 10, 'Profile', 0);

INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `eval`) VALUES ('aqb_credits', '', '', 'BxDolService::call(''aqb_credits'', ''response'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES
(NULL, 'profile', 'delete', @iHandler);
	