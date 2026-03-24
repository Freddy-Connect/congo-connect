-- create tables
 
CREATE TABLE IF NOT EXISTS `[db_prefix]preference` ( 
  `id` int(11) NOT NULL, 
  `admins` varchar(50) NOT NULL default '',
  `initialized` INT NOT NULL DEFAULT '0',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
  
INSERT INTO `[db_prefix]preference` SET `id`=1,`admins`='all', `initialized`=0;
  
 -- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_autofriend_profile_join', '', '', 'BxDolService::call(''autofriend'', ''response_profile_join'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'join', @iHandler);
   
 -- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_autofriend_profile_change', '', '', 'BxDolService::call(''autofriend'', ''response_profile_change'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'change_status', @iHandler);
 



-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'autofriend add friend', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_autofriend_membership_change', '', '', 'BxDolService::call(''autofriend'', ''response_membership_change'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'set_membership', @iHandler);

-- settings

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('AutoFriend', @iMaxOrder);
 
SET @iGlCategID = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`) VALUES
('auto_friend_activated', 'on', @iGlCategID, 'Auto Friend feature activated', 'checkbox', '', '', 1),
('modzzz_autofriend_alert', 'on', @iGlCategID, 'Activate email notification for new members', 'checkbox', '', '', 2),
('auto_friend_add', 'on', @iGlCategID, 'Auto add admins as friends', 'checkbox', '', '', 3) 
;
    
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_autofriend', '_modzzz_autofriend', '{siteUrl}modules/?r=autofriend/administration/', 'Auto Friend module by Modzzz','group', @iMax+1);


-- email templates
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('t_AutoFriend', '<ActionType> at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <MemberName></b>,</p><p><a href="<FanLink>"><FanName></a> <ItemDesc>.</p> <p>You can view your <ViewType> <a href="<FriendUrl>">here</a>:<br></p><p><b>Thank you for using our services!</b></p><p>---</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Notification about auto friends', '0');
 
 INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxAutoFriendInit', '*/1 * * * *', 'BxAutoFriendCron', 'modules/modzzz/autofriend/classes/BxAutoFriendCron.php', '');
