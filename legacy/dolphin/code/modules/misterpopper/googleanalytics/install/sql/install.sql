-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('[db_prefix]', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('[db_prefix]_injection', 'off', @iCategId, 'Inject Google Analytics Tracking Code?', 'checkbox', '', '', '0', ''),
('[db_prefix]_tracking_id', '', @iCategId, 'Tracking-ID (UA-????????-?)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '1', ''),
('[db_prefix]_opt_out', 'on', @iCategId, 'Should we use Google Analytics Opt-Out? (JS function gaOptout())', 'checkbox', '', '', '2', ''),
('[db_prefix]_anonymize_ip', 'on', @iCategId, 'Should we anonymize IP with Google Analytics Tracking?', 'checkbox', '', '', '3', ''),
('[db_prefix]_permalinks', 'on', 26, 'Enable friendly permalinks in Google Analytics', 'checkbox', '', '', '4', '');

-- permalinks
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=googleanalytics/', 'm/googleanalytics/', '[db_prefix]_permalinks');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, '[db_prefix]', '_mp_googleanalytics', '{siteUrl}modules/?r=googleanalytics/administration/', 'Google Analytics by paan : solution systems', 'globe', @iMax+1);

-- injections
INSERT INTO `sys_injections` (`name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
('[db_prefix]', 0, 'injection_head', 'php', '$oMain = BxDolModule::getInstance("MPGoogleAnalyticsModule");\r\nbx_import("MPGoogleAnalyticsModule", $oMain->_aModule);\r\n$oGoogleAnalytics = new MPGoogleAnalyticsModule($oMain->_aModule);\r\nreturn $oGoogleAnalytics->getAnalyticsInjection();', 0, 1);
