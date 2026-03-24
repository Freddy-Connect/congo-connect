INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`)VALUES (NULL , 'member', '', 'all activities summary', '_ibdw_thirdcolumn_modulename', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/3col/richieste.php'');', '1', '0', 'memb', '0');

INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`)VALUES (NULL , 'index', '', 'all activities summary', '_ibdw_thirdcolumn_modulename', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/3col/richieste.php'');', '1', '0', 'memb', '0');

INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`)VALUES (NULL , 'profile', '', 'all activities summary', '_ibdw_thirdcolumn_modulename', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/3col/richieste.php'');', '1', '0', 'memb', '0');

CREATE TABLE IF NOT EXISTS `suggerimenti` (  `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `mioID` INT NOT NULL , `FriendID` INT NOT NULL , `Rifiutato` TINYINT NOT NULL , `Pertinenza` INT NOT NULL , `MutualF` INT NOT NULL) ENGINE = MYISAM ;

CREATE TABLE IF NOT EXISTS `logrichieste` (  `ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY , `IdUtente` INT NOT NULL , `Contaric` TINYINT NOT NULL DEFAULT '0' , `When` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE = MYISAM ;


CREATE TABLE IF NOT EXISTS `third_code` (`id` INT NOT NULL PRIMARY KEY ,`code` VARCHAR( 100 ) NOT NULL) ENGINE = MYISAM;

CREATE TABLE `3col_config` (

`friendrequest` VARCHAR( 3 ) NOT NULL ,
`watchprofile` VARCHAR( 3 ) NOT NULL ,
`birthdate` VARCHAR( 3 ) NOT NULL ,
`events` VARCHAR( 3 ) NOT NULL ,
`suggprofile` VARCHAR( 3 ) NOT NULL ,
`moreinfo` VARCHAR( 3 ) NOT NULL ,
`dayrange` VARCHAR( 5 ) NOT NULL ,
`timezone` VARCHAR( 5 ) NOT NULL ,
`refresh` VARCHAR( 10 ) NOT NULL ,
`maxnumberevent` VARCHAR( 5 ) NOT NULL ,
`maxnumberconsider` VARCHAR( 5 ) NOT NULL ,
`maxfriendrequest` VARCHAR( 5 ) NOT NULL ,
`maxnumonline` VARCHAR( 5 ) NOT NULL ,
`trsugg` VARCHAR( 5 ) NOT NULL ,
`trfriends` VARCHAR( 5 ) NOT NULL ,
`conditionton` VARCHAR( 5 ) NOT NULL ,
`defaultinviter` VARCHAR( 5 ) NOT NULL ,
`linktoinviter` VARCHAR( 100 ) NOT NULL ,
`dateFormatc` VARCHAR( 5 ) NOT NULL,
`timeminispy` VARCHAR( 10 ) NOT NULL
) ENGINE = MYISAM ;

INSERT INTO `3col_config` (`friendrequest`, `watchprofile`, `birthdate`, `events`, `suggprofile`, `moreinfo`, `dayrange`, `timezone`, `refresh`, `maxnumberevent`, `maxnumberconsider`, `maxfriendrequest`, `maxnumonline`, `trsugg`, `trfriends`, `conditionton`, `defaultinviter`, `linktoinviter`, `dateFormatc`,`timeminispy`) VALUES ('ON', 'ON', 'ON', 'ON', 'ON', 'ON', '60', '6', '15000', '20', '30', '20', '2', '49', '1', 'OR', 'ON', 'YOUR-INVITER-PATH', 'uni','60');


INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES (NULL, '0', 'Activation 3COL', 'Activation 3COL', '{siteUrl}modules/ibdw/3col/activation.php', 'Activation 3COL', 'modules/ibdw/3col/templates/base/images/|warning.png', '', '', '10');
