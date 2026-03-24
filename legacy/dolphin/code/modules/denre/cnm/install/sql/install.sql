-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('DbCNM', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
    ('db_cnm_on', 'on', @iCategId, 'Enable Cookie Notifications', 'checkbox', '', '', '0', ''),
    ('db_cnm_display_privacy_btn', 'on', @iCategId, 'Show Privacy Policy button', 'checkbox', '', '', '1', ''),
    ('db_cnm_policy_uri', 'privacy.php', @iCategId, 'URI of Privacy Policy', 'text', '', '', '2', ''),
    ('db_cnm_auto_enable', '', @iCategId, 'Cookies accepted automatically (Banner still shows)', 'checkbox', '', '', '3', ''),
    ('db_cnm_accept_continue', 'on', @iCategId, 'Accept cookies when visitor moves to another page', 'checkbox', '', '', '4', ''),
    ('db_cnm_accept_scroll', '', @iCategId, 'Accept cookies when visitor scrolls', 'checkbox', '', '', '5', ''),
    ('db_cnm_accept_any_click', '', @iCategId, 'Accept cookies when visitor clicks anywhere on the page', 'checkbox', '', '', '6', ''),
    ('db_cnm_disable_click', '', @iCategId, 'Disable page interaction (overrides previous 3 options)', 'checkbox', '', '', '7', ''),
    ('db_cnm_cookie_expire', '365', @iCategId, 'Number of days to store cookie', 'digit', '', '', '8', ''),
    ('db_cnm_cookie_renew', '', @iCategId, 'Renew cookie when visitor revisits the site', 'checkbox', '', '', '9', ''),
    ('db_cnm_forse_show', '', @iCategId, 'Show message regardless of user cookie preference', 'checkbox', '', '', '10', ''),
    ('db_cnm_effect', 'slide', @iCategId, 'Message effect', 'select', '', '', '11', 'slide,fade,hide'),
    ('db_cnm_element', 'body', @iCategId, 'Element to append/prepend message to', 'text', '', '', '12', ''),
    ('db_cnm_element_append', 'prepend', @iCategId, 'Append/prepend message (top/bottom)', 'select', '', '', '13', 'append,prepend'),
    ('db_cnm_element_fixed', 'on', @iCategId, 'Set the fixed class', 'checkbox', '', '', '14', ''),
    ('db_cnm_element_bottom', '', @iCategId, 'Force mesage to bottom when fixed', 'checkbox', '', '', '15', ''),
    ('db_cnm_element_zindex', '999', @iCategId, 'Z-index of the element', 'text', '', '', '16', '');

-- alerts
INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `eval`) VALUES
    ('db_cnm', '', '', 'BxDolService::call(''cnm'', ''show_message'', array());');

SET @iHandler = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'db_cnm' LIMIT 1);
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
    ('system', 'design_included', @iHandler);

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
    (2, 'db_cnm', '_db_cnm', '{siteUrl}modules/?r=cnm/administration/', 'Cookie Notifications by Denre','legal', @iMax+1);
