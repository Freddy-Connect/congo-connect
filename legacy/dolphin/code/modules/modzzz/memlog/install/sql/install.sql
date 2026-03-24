-- create tables 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(11) NOT NULL auto_increment, 
  `member_id` int(11) NOT NULL default '0',
  `moderator_id`  int(11) NOT NULL default '0', 
  `created` int(11) NOT NULL, 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
  
 
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('member', '1140px', 'My Moderators', '_modzzz_memlog_block_moderation', 2, 3, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''memlog'', ''moderators_block'');', 1, 28.1, 'non,memb', 0);
     

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=memlog/', 'm/memlog/', 'modzzz_memlog_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('MemLog', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_memlog_moderators_add', 'on', @iCategId, 'Allow members to add others as moderators', 'checkbox', '', '', '0', ''), 
('modzzz_memlog_perpage_browse', '5', @iCategId, 'Number of entries to show in page blocks', 'digit', '', '', '1', '');
 
 
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_memlog', '_modzzz_memlog', '{siteUrl}modules/?r=memlog/administration/', 'Login as Member module by Modzzz','user', @iMax+1);



SET @iMaxOrder = (SELECT `Order` + 1 FROM `sys_objects_actions` WHERE `Type`='Profile' ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_objects_actions` ( `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES 

( '{evalResult}', 'wrench', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', site_url+''modules/?r=memlog/make_moderator/{member_id}/{ID}'', false, ''post'');return false;', '$oMain = BxDolModule::getInstance(''BxMemLogModule'');if (({ID} != {member_id}) && $oMain->isAllowedAddModerator( {member_id},{ID}) ) return _t(''_modzzz_memlog_action_title_make_moderator'');', @iMaxOrder, 'Profile', 0), 

( '{evalResult}', 'wrench', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', site_url+''modules/?r=memlog/remove_moderator/{member_id}/{ID}'', false, ''post'');return false;', '$oMain = BxDolModule::getInstance(''BxMemLogModule'');if (({ID} != {member_id}) && $oMain->isModerator( {member_id},{ID})) return _t(''_modzzz_memlog_action_title_remove_moderator'');', @iMaxOrder, 'Profile', 0), 
 
( '{evalResult}', 'user', 'm/memlog/member/{ID}', '', '$oMain = BxDolModule::getInstance(''BxMemLogModule'');if ( $GLOBALS[''logged''][''admin''] && ({ID} != {member_id})) return _t(''_modzzz_memlog_action_title_login_as_member'');', @iMaxOrder, 'Profile', 0), 

( '{evalResult}', 'user', 'm/memlog/member/{ID}', '', '$oMain = BxDolModule::getInstance(''BxMemLogModule'');if ( !$GLOBALS[''logged''][''admin''] && ({ID} != {member_id}) && $oMain->isAllowedLoginAsMember({ID}, {member_id}) ) return _t(''_modzzz_memlog_action_title_login_as_member'');', @iMaxOrder, 'Profile', 0);
 
 

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_memlog_profile_delete', '', '', 'BxDolService::call(''memlog'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_memlog_profile_logout', '', '', 'BxDolService::call(''memlog'', ''response_profile_logout'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'logout', @iHandler);

 
-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'memlog add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_memlog_moderator', 'You are a moderator for <MickName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p><a href="<MemberUrl>"><MemberName></a> has made you a moderator of their Profile. You are now able to visit their Profile page and login as that member.</p> <bx_include_auto:_email_footer.html />', 'Moderator (Login as member) notification message', '0');
  
DELETE FROM `sys_menu_member` WHERE `Link` LIKE 'm/memlog/return%';
