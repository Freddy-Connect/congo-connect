SET @sPluginName = 'aqb_affiliate';
SET @sPluginTitle = 'AQB Forced Matrix System';

SET @iOrder = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');
INSERT INTO `sys_menu_admin`(`parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(2, @sPluginName, '_aqb_aff_admin', '{siteUrl}modules/?r=aqb_affiliate/administration/', 'Affiliate System''s admin panel', 'modules/aqb/affiliate/|admin_menu_icon.png', '', '', @iOrder + 1);

INSERT INTO `sys_permalinks`(`standard`, `permalink`, `check`) VALUES
('modules/?r=aqb_affiliate/', 'm/aqb_affiliate/', 'permalinks_module_aqb_affiliate');

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES (@sPluginTitle, @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('aqb_affiliate_turn_on', 'on', @iCategId, 'Affiliate feature is enabled', 'checkbox', '', '', 1),
('aqb_referral_turn_on', 'on', @iCategId, 'Referral feature is enabled', 'checkbox', '', '', 2),
('permalinks_module_aqb_affiliate', 'on', @iCategId, 'Enable friendly permalinks in Affiliate\\Referrals System', 'checkbox', '', '', 3),
('aqb_affiliate_enable_points', 'on', @iCategId, 'Allow to use points in Affiliate System', 'checkbox', '', '', 4),
('aqb_affiliate_use_one_price', '', @iCategId, 'Use one price for membership upgrades and for joined persons (no matter how person joined: by referral or affiliate banner)', 'checkbox', '', '', 5),
('aqb_affiliate_referral_link', '[db_index_link]', @iCategId, 'Default referral link for members', 'digit', '', '', 6),
('aqb_aff_make_invite_member_friends', 'on', @iCategId, 'Automatically add invited member to inviter frinds list', 'checkbox', '', '', 7),
('aqb_aff_unique_hsitory_days', '30', @iCategId, 'Keeps history unique during these number of days', 'digit', '', '', 8),
('aqb_affiliate_cookie_days', '30', @iCategId, 'When a person opens your site for the first time using affiliate or referral link, keeps his/her cookie active during these number of days', 'digit', '', '', 9),
('aqb_aff_perpage_browse_referrals', '20', @iCategId, 'Number of invited members on member''s referral page', 'digit', '', '', 10),
('aqb_aff_perpage_browse_history', '15', @iCategId, 'Number of records on history page', 'digit', '', '', 11),
('aqb_aff_allow_sent_invitations', 'on', @iCategId, 'Allow members to send invitations to their frineds through the site', 'checkbox', '', '', 12),
('aqb_aff_allow_their_message', 'on', @iCategId, 'Allow members to add message to invitation message''s body', 'checkbox', '', '', 13),
('aqb_aff_maximum_number_of_send_emails', '10', @iCategId, 'Maximum invitations emails number which can be sent by member during <b>24 hours</b>', 'digit', '', '', 14),
('aqb_aff_maximum_number_of_emails_symbols', '300', @iCategId, 'Maximum symbols number for members invitation email''s message', 'digit', '', '', 15),
('aqb_aff_use_queue', 'on', @iCategId, 'Send members invitation emails through mass mailer queue', 'checkbox', '', '', 16),
('aqb_aff_enable_ref_index', 'on', @iCategId, 'Enable block with Top referrals on index page', 'checkbox', '', '', 17),
('aqb_aff_per_page_ref_index', 6, @iCategId, 'Number of members in Top Referrals Block on index page', 'digit', '', '', 18),
('aqb_aff_enable_invited_profile', 'on', @iCategId, 'Enable My Invited Members block on profile page', 'checkbox', '', '', 19),
('aqb_aff_per_page_invited_profile', 6, @iCategId, 'Number of members in My Invited Members block on profile page', 'digit', '', '', 20),
('aqb_aff_show_my_branch', '', @iCategId, 'Show invited members from member\'s matrix on profile page block', 'checkbox', '', '', 21),
('aqb_aff_limit_commission', '0.01', @iCategId, 'The minimum amount to send payment request is <b>(in currency)</b>', 'digit', '', '', 22),
('aqb_aff_pp_api_user', '', @iCategId, 'Credential\'s API Username', 'digit', '', '', 23),
('aqb_aff_pp_api_pwd', '', @iCategId, 'Credential\'s API Password', 'digit', '', '', 24),
('aqb_aff_pp_api_signature', '', @iCategId, 'Credential\'s	API Signature', 'digit', '', '', 25);

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('aqb_aff_block_with_invitation_links', '', @iCategId, 'Place block with invitation link to the next pages', 'list', '', '', 26, 'ads,article,blog,event,group,news,photo,site,sound,store,video,profile');



SET @iOrder = (SELECT `order_in_kateg` FROM `sys_options` ORDER BY `order_in_kateg` DESC LIMIT 1);
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('aqb_forced_matrix', '1', 0, 'Enable forced matrix', 'checkbox', '', '', @iOrder + 1),
('aqb_matrix_width', '0', 0, 'Forced matrix width (maximum <b>5</b>) and <b>0</b> for limitless', 'digit', '', '', @iOrder + 2),
('aqb_matrix_income', '0', 0, 'Income', 'digit', '', '', @iOrder + 3),
('aqb_matrix_spillover', 'on', 0, 'Spillover', 'digit', '', '', @iOrder + 4),
('aqb_matrix_timer', 0, 0, 'timer', 'digit', '', '', @iOrder + 5);


INSERT INTO `sys_email_templates` (`Name`,`Subject`,`Body`,`Desc`,`LangID`) VALUES
('t_AqbAffMemberInvitation', 'Invitation from <RealName>', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<p><b>Hello!</b></p>\r\n\r\n<p><MembersMessage></p>\r\n\r\n<p>Simply follow the link below:<br /><MembersInvitationLink></p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>--</p>\r\n<p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!!\r\n<br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Affiliate/Referrals Systems Invitation', 0),
('t_AqbAffCommissionPaid', 'Commissions had been Paid!', '<html><head></head><body style="font: 12px Verdana; color:#000000">\r\n<p><b>Hello, <RealName>!</b></p>\r\n\r\n<p>Commission in sum <Sum> was paid on <Date>!</p>\r\n\r\n<p>Simply follow the link below:<br /><HistoryPage></p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>--</p>\r\n<p style="font: bold 10px Verdana; color:red"><SiteName> mail delivery system!!!\r\n<br />Auto-generated e-mail, please, do not reply!!!</p></body></html>', 'Commission had been paid', 0);


CREATE TABLE IF NOT EXISTS `[db_prefix]memlevels_matrix` (
  `id_level` int( 11 ) NOT NULL default '0',
  `deep` tinyint(2) unsigned NOT NULL default '0',
  `points` text NOT NULL default '',
  `currency` text NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '0',
  PRIMARY KEY ( `id_level` )
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]banners` (
  `id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `name` varchar( 255 ) default NULL,
  `img` varchar( 20 ) NOT NULL default '',
  `size` varchar( 10 ) NOT NULL default '',
  `link` varchar( 255 ) default NULL,
  `impression_price` float NOT NULL default '0',
  `impression_price_points` int( 11 ) NOT NULL default '0',
  `click_price` float NOT NULL default '0',
  `click_price_points` int( 11 ) NOT NULL default '0',
  `join_price` float NOT NULL default '0',
  `join_price_points` int( 11 ) NOT NULL default '0',
  `upgrade_price` float NOT NULL default '0',
  `upgrade_price_points` int( 11 ) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `active` enum('0','1') NOT NULL default '0',
   PRIMARY KEY ( `id` )
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]all_history` (
  `date` int(11) NOT NULL default '0',
  `ip` varchar( 20 ) NOT NULL default '',
  `action_type` enum('impression','click','join') NOT NULL default 'impression',
  `banner_id` int(11) NOT NULL default '0',
  `owner_id` int(11) NOT NULL default '0',
   UNIQUE KEY  `id` (`ip`,`action_type`, `banner_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]journal` (
  `id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `member_id` int(11) NOT NULL default '0',
  `last_update` int(11) NOT NULL default '0',
  `action_type` enum('impression','click','join','upgrade') NOT NULL default 'impression',
  `inviter_type` enum('banner','referral') NOT NULL default 'banner', 	
  `inviter` int(11) NOT NULL default '0',
  `sum_price` float NOT NULL default '0',
  `sum_points` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '1',
   PRIMARY KEY ( `id` )
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]referrals` (
  `referral` int(11) NOT NULL default '0',
  `member` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '0',
  `upgraded` int(11) unsigned default '0', 
  UNIQUE KEY  `id` (`referral`,`member`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]matrix` (
 `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `member` INT(11) NOT NULL DEFAULT  '0',
 `lft` INT(11) NOT NULL,
 `rgt` INT(11) NOT NULL,
 `level` INT(11) NOT NULL,
 `root_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`) ,
  UNIQUE KEY `attrs` (`lft`,`rgt`,`level`, `root_id`)
) ENGINE=MYISAM DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]memlevels_pricing` (
  `id_level` int( 11 ) NOT NULL default '0',
  `referral_price` float NOT NULL default '0',
  `referral_points` int( 11 ) NOT NULL default '0',
  `referral_upgrade_price` float NOT NULL default '0',
  `referral_upgrade_points` int( 11 ) NOT NULL default '0',
  `invited_members` int( 11 ) NOT NULL default '0',
  `membership_level` varchar(20) NOT NULL default '', 
   PRIMARY KEY ( `id_level` ),
   UNIQUE KEY (`id_level`)
)ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]transactions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `price` float NOT NULL default '0',
  `points` int(11) unsigned default NULL,
  `status` enum('unpaid', 'paid') NOT NULL default 'unpaid', 
  `member_id` int( 11 ) NOT NULL default '0',
  `join_num` int(11) unsigned NOT NULL default '0',
  `click_num` int(11) unsigned NOT NULL default '0',
  `impression_num` int(11) unsigned NOT NULL default '0',
  `upgrade_num` int(11) unsigned NOT NULL default '0',
  `date_start` int(11) unsigned NOT NULL default '0',
  `date_end` int(11) unsigned NOT NULL default '0',
  `tnx` varchar(20) NOT NULL default '',
  `payment_status` varchar(10) NOT NULL default '',
  PRIMARY KEY (`id`)
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]invitations` (
  `owner_id` int(11) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  UNIQUE KEY  `id` (`owner_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'aqb_aff_page_alert', 'AqbAffAlertsResponse', 'modules/aqb/affiliate/classes/AqbAffAlertsResponse.php', '');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES
(NULL, 'system', 'begin', @iHandler),
(NULL, 'profile', 'join', @iHandler),
(NULL, 'profile', 'delete', @iHandler),
(NULL, 'profile', 'set_membership', @iHandler);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'member', '998px', 'Affiliate\Referrals info', '_aqb_aff_my_account_referrals', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_referral_block'', array($this->iMember));', 1, 66, 'non,memb', 0);

SET @iTopMenuLastOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Home', '_aqb_aff_ref_title', 'modules/?r=aqb_affiliate/referrals', @iTopMenuLastOrder, 'memb', '', '', '', 1, 1, 1, 'top', 'modules/aqb/affiliate/|affiliates.png', 1, '');
SET @menu_id = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(NULL, @menu_id, 'Referrals', '_aqb_aff_my_referrals', 'modules/?r=aqb_affiliate/referrals', 2, 'memb', '', '', '', 1, 1, 1, 'custom', 'modules/aqb/affiliate/|referrals.png', 0, ''),
(NULL, @menu_id, 'Affilate', '_aqb_aff_my_affilate', 'modules/?r=aqb_affiliate/affiliates', 3, 'memb', '', '', '', 1, 1, 1, 'custom', 'modules/aqb/affiliate/|affiliates_big.png', 0, ''),
(NULL, @menu_id, 'History', '_aqb_aff_my_history', 'modules/?r=aqb_affiliate/history', 4, 'memb', '', '', '', 1, 1, 1, 'custom', 'modules/aqb/affiliate/|history.png', 0, '');

SET @iOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`) + 1;
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('aqb_aff_my_ref_page', 'Member''s Referrals', @iOrder);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('aqb_aff_my_ref_page', '998px', 'Info', '_aqb_aff_my_referrals_info', 1, 0, 'Info', '', 1, 34, 'memb', 0),
('aqb_aff_my_ref_page', '998px', 'Member''s referrals page', '_aqb_aff_my_referrals_my_ref', 2, 0, 'MyReferrals', '', 1, 66, 'memb', 0);

SET @iOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`) + 1;
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('aqb_aff_my_history_page', 'Member''s History page', @iOrder);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('aqb_aff_my_history_page', '998px', 'Payment Info', '_aqb_aff_my_payment_info', 1, 1, 'PaymentInfo', '', 1, 34, 'memb', 0),
('aqb_aff_my_history_page', '998px', 'Commissions Info', '_aqb_aff_my_commissions_info', 1, 2, 'CommissionInfo', '', 1, 34, 'memb', 0),
('aqb_aff_my_history_page', '998px', 'History', '_aqb_aff_my_history_block', 2, 0, 'History', '', 1, 66, 'memb', 0);

SET @iOrder = (SELECT MAX(`Order`) FROM `sys_page_compose_pages`) + 1;
INSERT INTO `sys_page_compose_pages`(`Name`, `Title`, `Order`) VALUES ('aqb_aff_my_aff_page', 'Member''s Affiliate', @iOrder);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
('aqb_aff_my_aff_page', '998px', 'Info', '_aqb_aff_my_affiliate_info', 1, 0, 'Info', '', 1, 34, 'memb', 0),
('aqb_aff_my_aff_page', '998px', 'Member''s referrals page', '_aqb_aff_my_affiliate_banners', 2, 0, 'AvialBanners', '', 1, 66, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'index', '998px', 'The best referrals', '_aqb_aff_best_referrals_index', 2, 2, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_referrals_index_block'');', 1, 66, 'non,memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'profile', '998px', 'My invited members', '_aqb_aff_my_invited_member', 3, 1, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_invited_members_block'', array($this->oProfileGen->_iProfileID));', 1, 66, 'non,memb', 0);

INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('aqb_aff_clean', '0 0 * * *', 'AqbAffCron', 'modules/aqb/affiliate/classes/AqbAffCron.php', '');

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'ads', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''ads''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'articles_single', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 3, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''articles_single''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_blogs', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_blogs''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_events_view', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_events_view''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_groups_view', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_groups_view''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'news_single', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 3, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''news_single''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_photos_view', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_photos_view''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_videos_view', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_videos_view''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_sounds_view', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_sounds_view''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_sites_view', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_sites_view''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'bx_store_view', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link', 2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''bx_store_view''));', 1, 34, 'memb', 0);

INSERT INTO `sys_page_compose` (`ID`, `Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES
(NULL, 'profile', '998px', 'Member''s referral link', '_aqb_aff_my_account_referrals_link',2, 0, 'PHP', 'return BxDolService::call(''aqb_affiliate'', ''get_my_referral_link'', array(''profile''));', 1, 34, 'memb', 0);