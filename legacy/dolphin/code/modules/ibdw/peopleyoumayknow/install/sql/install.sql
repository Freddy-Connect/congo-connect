SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');

INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'Peoples You May Know', 'Peoples You May Know', '{siteUrl}modules/?r=peopleyoumayknow/administration/', 'Peoples You May Know - Settings', 'heart', '', '', @iExtOrd+1);

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('peopleyoumayknow', @iMaxOrder);

SET @iKategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` SET   `Name` = 'KeyCodePYMK',   `kateg` = @iKategId,   `desc`  = 'License Key (Activation code)<br><a target="_blank" href="ibdw/peopleyoumayknow/activation.php">Click here to get the code</a>',   `Type`  = 'digit',   `VALUE` = '',   `order_in_kateg` = 1;
INSERT INTO `sys_options` SET   `Name` = 'pymktemplate',   `kateg` = @iKategId,   `desc`  = 'Choose scheme color',   `Type`  = 'select',   `VALUE` = 'UNI',   `order_in_kateg` = 2, `AvailableValues` ='UNI, DARK';
INSERT INTO `sys_options` SET   `Name` = 'minprofiletoload',   `kateg` = @iKategId,   `desc`  = 'Suggested profile number',   `Type`  = 'digit',   `VALUE` = '10',   `order_in_kateg` = 3;
INSERT INTO `sys_options` SET   `Name` = 'displaybefriend',   `kateg` = @iKategId,   `desc`  = 'Display Befriend button',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 4;
INSERT INTO `sys_options` SET   `Name` = 'displayfriends',   `kateg` = @iKategId,   `desc`  = 'Display Friends button',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 5;
INSERT INTO `sys_options` SET   `Name` = 'displayfmessage',   `kateg` = @iKategId,   `desc`  = 'Display Message button',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 6;
INSERT INTO `sys_options` SET   `Name` = 'IBDWProfileCover',   `kateg` = @iKategId,   `desc`  = 'ProfileCover Popup<br>(This feature is available only if you have already installed and activated the module <a href="https://www.boonex.com/m/profile-s-cover" target="_new">IBDW Profile Cover Plus</a>)',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 7;

CREATE TABLE IF NOT EXISTS `peopleyoumayknow_code_reminder` (`id` INT NOT NULL PRIMARY KEY ,`addressr` VARCHAR( 100 ) NOT NULL,`website` VARCHAR( 200 ) NOT NULL) ENGINE = MYISAM;

INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`)VALUES (NULL , 'member', '', 'Peoples You May Know', '_ibdw_peopleyoumayknow_titlemodule', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/peopleyoumayknow/main.php'');', '3', '0', 'memb', '0');
INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`)VALUES (NULL , 'index', '', 'Peoples You May Know', '_ibdw_peopleyoumayknow_titlemodule', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/peopleyoumayknow/main.php'');', '3', '0', 'memb', '0');