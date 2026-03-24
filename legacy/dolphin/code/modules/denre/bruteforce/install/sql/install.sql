
CREATE TABLE `db_bruteforce_cnt` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE KEY,
    `counter` INT UNSIGNED NOT NULL,
    `login_time` TIMESTAMP NOT NULL,
    `added` INT UNSIGNED NOT NULL
);

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES 
    ('DB Bruteforce', @iMaxOrder);

SET @iPCPOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES
('bruteforce', 'Acccount Locked', @iPCPOrder+1);

SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
    ('db_bruteforce_cnt', '5', @iCategId, 'How many times can the user enter an incorrect password before the account is locked', 'digit', '', '', '0', ''),
    ('db_bruteforce_time', '15', @iCategId, 'How many minutes is the account locked for', 'digit', '', '', '1', ''),
    ('db_bruteforce_forgot', '', @iCategId, 'Unlock on password reset? (needs Advanced Password Forgot Module)', 'checkbox', '', '', '2', '');


-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
    (2, 'db_bruteforce', '_db_bruteforce', '{siteUrl}modules/?r=bruteforce/administration/', 'DB BRuteforece by Denre', 'modules/denre/bruteforce/|icon.png', @iMax+1);


-- alert handlers
INSERT INTO `sys_alerts_handlers` (`name`, `class`, `file`, `eval`) VALUES
    ('Db_Bruteforce_Init', '', '', 'BxDolService::call(''bruteforce'', ''bruteforce_init'', array());'),
    ('Db_Bruteforce_Login_Form', '', '', 'BxDolService::call(''bruteforce'', ''bruteforce_login_form'', array($this));'),
    ('Db_Bruteforce_Before_Login', '', '', 'BxDolService::call(''bruteforce'', ''bruteforce_before_login'', array($this));'),
    ('Db_Bruteforce_Login', '', '', 'BxDolService::call(''bruteforce'', ''bruteforce_login'', array($this));');

-- alerts
SET @iHandler = (SELECT `id` FROM `sys_alerts_handlers` WHERE `name` = 'Db_Bruteforce_Init' LIMIT 1);
INSERT INTO `sys_alerts` (`unit`, `action`, `handler_id`) VALUES
    ('system', 'design_included', @iHandler),
    ('profile', 'show_login_form', @iHandler +1),
    ('profile', 'before_login', @iHandler +2),
    ('profile', 'login', @iHandler +3);

-- sys_cron_jobs
INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`) VALUES
    ('db_bruteforce', '* * * * *', 'DbBruteforceCron', 'modules/denre/bruteforce/classes/DbBruteforceCron.php');
