SET @iCategId = (SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'BSEgiveme' LIMIT 1);
DELETE FROM `sys_options` WHERE `kateg` = @iCategId;
DELETE FROM `sys_options_cats` WHERE `ID` = @iCategId;

-- permalinks
DELETE FROM `sys_permalinks` WHERE `standard` = 'modules/?r=giveme/';

-- `sys_favicon`;
DELETE FROM `sys_options` WHERE `name` = 'Giveme_sys_main_favicon';

-- admin menu
DELETE FROM `sys_menu_admin` WHERE `name` = 'bsetec_giveme';

-- giveme settings table
DROP TABLE IF EXISTS `[db_prefix]_options`;

-- giveme slider table
DROP TABLE IF EXISTS `[db_prefix]_slider`;


DROP TABLE IF EXISTS `[db_prefix]_mastero`;
DROP TABLE IF EXISTS `[db_prefix]_splash`;
DROP TABLE IF EXISTS `[db_prefix]_donation`;
DROP TABLE IF EXISTS `bsetec_giveme_footer_menu`;
DROP TABLE IF EXISTS `bsetec_giveme_footer_menu_group`;

-- `sys_injections`;
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_colorization';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_bodyclass';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_slider';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_latest_events';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_gallery';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_favicon';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_newsletter';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_donate';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_payment_success';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_photos';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_join_today';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_bsecolor';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_combine_module';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_footer_menu';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_latest_blogs';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_fav_icon';
DELETE FROM `sys_injections` WHERE `name`='bsetec_giveme_footer';




DELETE FROM `sys_options` WHERE `name` = 'giveme_design_header_font';
DELETE FROM `sys_options` WHERE `name` = 'giveme_header_font';
DELETE FROM `sys_options` WHERE `name` = 'giveme_body_font';
DELETE FROM `sys_options` WHERE `name` = 'giveme_body_header_font';
DELETE FROM `sys_options` WHERE `name` = 'giveme_footer_font';
DELETE FROM `sys_options` WHERE `name` = 'giveme_menu_font';
DELETE FROM `sys_options` WHERE `name` = 'giveme_custom_color';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slideimg_five';
DELETE FROM `sys_options` WHERE `name` = 'giveme_bse_skin';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_visibility';      
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_trantime';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_navarrow';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_bullethori';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_bulletvert';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_transition';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_navtype';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slide_navstyle';
DELETE FROM `sys_options` WHERE `name` = 'giveme_slideimg_two';