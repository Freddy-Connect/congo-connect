-- tables
DROP TABLE IF EXISTS `[db_prefix]main`; 
DROP TABLE IF EXISTS `[db_prefix]action_types`;
DROP TABLE IF EXISTS `[db_prefix]actions`;
DROP TABLE IF EXISTS `[db_prefix]feedback_main`;


-- system objects
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=contact/';
 
-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'modzzz_contact';
DELETE FROM `sys_menu_top` WHERE `name` = 'My Contact';
 
  
-- settings
SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Contact' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

DELETE FROM `sys_options` WHERE `Name` IN ('modzzz_contact_permalinks' );


DELETE FROM `sys_page_compose_pages` WHERE `Name` IN('modzzz_contact_main');
DELETE FROM `sys_page_compose` WHERE `Page` IN('modzzz_contact_main');


DELETE FROM `sys_email_templates` WHERE `Name` IN ('modzzz_contact_msg');

UPDATE `sys_menu_top` SET `Link`='contact.php' WHERE `Caption` = '_Contact' AND `Link` = 'm/contact/home';

UPDATE `sys_menu_bottom` SET `Link`='contact.php' WHERE `Caption` = '_contact_us' AND `Link` = 'm/contact/home';