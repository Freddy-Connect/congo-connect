
-- menu admin
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_find_friends', 'AdiInviter Admin Panel', '{siteUrl}adi_redirect.php', 'AdiInviter Admin Panel', 'modules/adiinviter/find_friends/|icon.png', @iMax+1);


-- menu top
SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Find/Invite Friends', '_bx_find_friends_top', 'find_friends.php#popup', @iMaxMenuOrder, 'non,memb', '', 'adi_open_popup_model(); return false;', 'global $dir; include($dir[''root'']. ''find_friends''.DIRECTORY_SEPARATOR.''dolphin_check_permissions.php''); return $include_adiinviter;', 1, 1, 1, 'top', 'comments', 'comments', 1, '');
SET @iId = (SELECT LAST_INSERT_ID());


INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iId, 'Find Friends', '_bx_find_friends_find_friends', 'find_friends.php#popup', 1, 'non,memb', '', 'adi_open_popup_model(); return false;', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iId, 'Invite History', '_bx_find_friends_invite_history', 'invite_history.php', 2, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');


-- Injection : Add Header links
INSERT INTO `sys_injections` (`id`, `name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
(NULL, 'adiinviter_links', 0, 'injection_head', 'php', 'global $dir; include_once($dir[''root'']. ''find_friends''.DIRECTORY_SEPARATOR.''dolphin_headinclude.php'');', 0, 1);


-- Action : Share blog link
INSERT INTO `sys_objects_actions` (`ID`, `Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`, `bDisplayInSubMenuHeader`) VALUES
(NULL, '_adiinviter_share_blog_link', 'envelope-alt', '', '{evalResult}', 'global $dir;  $adi_ret_val = null; $adi_post_id = {post_id}; $prefix = $this->_sPrefix;\ninclude_once($dir[''root'']. ''find_friends''.DIRECTORY_SEPARATOR.''dolphin_blog_share.php''); \nreturn $adi_ret_val; ', 11, 'bx_blogs', 0);


-- Registration Event Handler
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_adiinviter_registration_handler', 'AdiInviter_Registration_Handler', 'modules/adiinviter/find_friends/registration_handler.php', '');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'join', @iHandler);