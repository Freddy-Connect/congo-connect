-- menu admin
DELETE FROM `sys_menu_admin` WHERE `name` = 'bx_find_friends' LIMIT 1;


-- menu top
SET @iId = (SELECT `ID` FROM `sys_menu_top` WHERE `Name` = 'Find/Invite Friends' AND `Parent` = 0 LIMIT 1);
DELETE FROM `sys_menu_top` WHERE `Parent` = @iId;
DELETE FROM `sys_menu_top` WHERE `ID` = @iId;


-- Injection : Add links in head tag
DELETE FROM `sys_injections` WHERE `name` = 'adiinviter_links' LIMIT 1;

-- Action : Share Blog
DELETE FROM `sys_objects_actions` WHERE `Caption` = '_adiinviter_share_blog_link' LIMIT 1;


-- Registration Event Handler
SET @iMaxMenuOrder := (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'bx_adiinviter_registration_handler' LIMIT 1);
DELETE FROM `sys_alerts` WHERE `handler_id` = @iMaxMenuOrder LIMIT 1;
DELETE FROM `sys_alerts_handlers` WHERE `id` = @iMaxMenuOrder LIMIT 1;


-- AdiInviter Tables
DROP TABLE IF EXISTS `adiinviter`, `adiinviter_conts`, `adiinviter_guest`, `adiinviter_lang`, `adiinviter_queue`, `adiinviter_settings`;
