-- We don't drop this table during removal. Doing so will cause loss
-- of stats moved to inactive. Uncomment this if you want to
-- permanently remove the module. But move all stats back into
-- the active area before you do.
-- DO NOT uncomment if just doing a upgrade.
-- DROP TABLE IF EXISTS `dbcs_sys_stat_site`;

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'SiteStatManager';

