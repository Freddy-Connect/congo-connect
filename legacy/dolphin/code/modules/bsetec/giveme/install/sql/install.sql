SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('BSEgiveme', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());

-- permalinks

INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=giveme/', 'm/giveme/', 'giveme_permalinkss');

-- `sys_favicon`;
INSERT INTO `sys_options` SET `name` = 'Giveme_sys_main_favicon';


-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bsetec_giveme', '_giveme_title', '{siteUrl}modules/?r=giveme/administration/', 'BSEtec giveme Options', 'modules/bsetec/giveme/|icon.png', @iMax+1);

-- giveme settings table
CREATE TABLE IF NOT EXISTS `[db_prefix]_options` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  `Value` text NULL default '',
  `type` varchar(255) NULL default '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]_options` (`Name`, `Value`, `type`) VALUES 
('giveme_design_header_font', '', 'color'),
('giveme_header_font', '', 'color'),
('giveme_body_font', '', 'color'),
('giveme_body_header_font', '', 'color'),
('giveme_footer_font', '', 'color'),
('giveme_menu_font', '', 'color'),
('giveme_custom_color', '', 'color'),
('giveme_map', '', 'map'),
('goal_title', '', 'donation'),
('goal_desc', '', 'donation'),
('goal_amount', '', 'donation'),
('goal_date', '', 'donation'),
('goal_reached', '', 'donation'),
('donation_status', '', 'donation'),
('flag', '', 'donation'),
('giveme_slide_visibility', 'disable', 'slider'),
('giveme_slide_transition', 'fade', 'slider'),
('giveme_slide_trantime', '9', 'slider'),
('giveme_bse_skin', 'custom','color'),
('easy_menu_footer_content','<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys.</p>
<ul><li><i class="fa fa-home"></i>123 Eccles Old Road, New Salford Road, East London, United Kingdom, M6 7AF</li>
<li><i class="fa fa-envelope-o"></i>info@example.com</li>
<li><i class="fa fa-phone"></i>+000 123 45678</li>
</ul>','footer');

INSERT INTO `[db_prefix]_options` (`ID`, `Name`, `Value`, `type`) VALUES ('', 'enable_giveme_home', 'enable', 'giveme'),
('', 'giveme_visibiliy_guest', 'disable', 'giveme'),
('', 'giveme_visibiliy_user', 'disable', 'giveme'),
('','giveme_icon_visibility','disable','giveme_icon');
-- giveme slider table
CREATE TABLE IF NOT EXISTS `[db_prefix]_slider` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `image` varchar(255) NOT NULL default '',
  `text` text NULL default '',
  `link` varchar(255) NULL default '',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS  `[db_prefix]_donation` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `item_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `order_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Dumping data for table `bsetec_giveme_options`
--

--INSERT INTO `[db_prefix]_options` (`ID` , `Name` , `Value` , `type` ) VALUES (NULL , 'contact_info', NULL , 'homepage'), (NULL , 'about_us', NULL , 'homepage');

--INSERT INTO `[db_prefix]_options` (`ID` , `Name` , `Value` , `type` ) VALUES (NULL , 'block_one', NULL , 'homepage'), (NULL , 'block_two', NULL , 'homepage'), (NULL , 'block_three', NULL , 'homepage'),(NULL , 'block_four', NULL , 'homepage'),(NULL, 'homepage_bottom_block', NULL, 'homepage'),(NULL, 'school_splash', 'on', 'homepage'),(NULL, 'school_layout', 'fixed', 'homepage'),(NULL, 'splash_visibility', 'all', 'homepage'),(NULL, 'custom_color', '', 'color');

--
-- Dumping data for table `sys_injections`
--

INSERT INTO `sys_injections` (`id`, `name`, `page_index`, `key`, `type`, `data`, `replace`, `active`) VALUES
(NULL, 'bsetec_giveme_latest_blogs', 0, 'injection_bsetecgivemeblogs', 'php', 'return BxDolService::call("giveme", "blogs");', 0, 1),
(NULL, 'bsetec_giveme_join_today', 0, 'injection_jointoday', 'php', 'return BxDolService::call("giveme", "join");', 0, 1),
(NULL, 'bsetec_giveme_latest_events', 0, 'injection_bsetecgivemeevents', 'php', 'return BxDolService::call("giveme", "events");', 0, 1),
(NULL, 'bsetec_giveme_gallery', 0, 'injection_giveme_gallery', 'php', 'return BxDolService::call("giveme", "gallery");', 0, 1),
(NULL, 'bsetec_giveme_newsletter', 0, 'injection_giveme_newsletter', 'php', 'return BxDolService::call("giveme", "newsletter");', 0, 1),
(NULL, 'bsetec_giveme_donate', 0, 'injection_giveme_donation', 'php', 'return BxDolService::call("giveme", "donation");', 0, 1),
(NULL, 'bsetec_giveme_payment_success', 0, 'injection_giveme_paymentsuccess', 'php', 'return BxDolService::call("giveme", "paymentsuccess");', 0, 1),
(NULL, 'bsetec_giveme_photos', 0, 'injection_giveme_photos', 'php', 'return BxDolService::call("giveme", "photos");', 0, 1),
(NULL, 'bsetec_giveme_colorization', 0, 'injection_bsetecgivemecolorization', 'php', 'return BxDolService::call("giveme", "colorization");', 0, 1),
(NULL, 'bsetec_giveme_bodyclass', 0, 'injection_bsetecgivemebodyclass', 'php', 'return BxDolService::call("giveme", "bodyclass");', 0, 1),
(NULL, 'bsetec_giveme_slider', 0, 'injection_bsetecgivemeslider', 'php', 'return BxDolService::call("giveme", "slider");', 0, 1),
(NULL, 'bsetec_giveme_favicon', 0, 'injection_bsetecgivemefavicon', 'php', 'return BxDolService::call("giveme", "favicon");', 0, 1),
(NULL, 'bsetec_giveme_bsecolor', 0, 'injection_giveme_bsecolor', 'php', 'require_once(BX_DIRECTORY_PATH_MODULES.''bsetec/giveme/injection/''.''bse_colorsettings.php''); ', 0, 1),
(NULL, 'bsetec_giveme_combine_module', 0, 'injection_bsetecgivemecombinemodule', 'php', 'return BxDolService::call("giveme", "combine_module");', 0, 1),
(NULL, 'bsetec_giveme_footer_menu', 0, 'injection_bsetecgivemefootermenu', 'php', 'return BxDolService::call("giveme", "footermenu");', 0, 1),
(NULL, 'bsetec_giveme_fav_icon', 0, 'injection_bsegivemefavicon', 'php', 'return BxDolService::call("giveme", "favicon");', 0, 1),
(NULL, 'bsetec_giveme_footer', 0, 'injection_bsetecgivemefootetnew', 'php', 'return BxDolService::call("giveme", "easyfooter");', 0, 1);





--
-- Dumping data for table `sys_options` colorization purpose
--

INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('giveme_design_header_font', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_header_font', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_body_font', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_body_header_font', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_footer_font', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_menu_font', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_custom_color', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slideimg_five', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_visibility', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_trantime', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_navarrow', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_bullethori', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_bulletvert', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_transition', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_navtype', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_slide_navstyle', '', 0, '', 'digit', '', '', NULL, ''),
('giveme_bse_skin', 'custom', 0, '', 'digit', '', '', NULL, '');



CREATE TABLE IF NOT EXISTS `[db_prefix]_mastero` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `block` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `injection_key` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `bg_image` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `[db_prefix]_mastero` (`block`, `injection_key`, `bg_image`) VALUES
('GiveME Photos', 'injection_giveme_photos', ''),
('GiveME Donation', 'injection_giveme_donation', ''),
('GiveME Blogs', 'injection_bsetecgivemeblogs', ''),
('GiveME Join Today', 'injection_jointoday', '57907a61c4577.jpg'),
('GiveME Events', 'injection_bsetecgivemeevents', ''),
('GiveME Gallery', 'injection_giveme_gallery', '');




CREATE TABLE IF NOT EXISTS `[db_prefix]_splash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `block` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bg_image` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `[db_prefix]_splash` (`block_type`, `parent_id`, `block`, `bg_image`) VALUES
('giveme', 1, 'GiveME Photos', ''),
('giveme', 2, 'GiveME Donation', ''),
('giveme', 3, 'GiveME Blogs', ''),
('giveme', 4, 'GiveME Join Today', '579064f657776.jpg'),
('giveme', 5, 'GiveME Gallery', ''),
('giveme', 6, 'GiveME Events', '');



CREATE TABLE `bsetec_giveme_footer_menu_group` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `sort_order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `bsetec_giveme_footer_menu_group` (`id`, `title`, `slug`, `status`, `is_active`, `sort_order`) VALUES
(2, 'News letter', 'newsletter', NULL, 1, 3),
(3, 'Downloads', 'donwloads', NULL, 1, 6),
(4, 'Footer Content', 'footer_content', NULL, 1, 1),
(5, 'Tags', 'tags', '', 0, 7);


ALTER TABLE `bsetec_giveme_footer_menu_group`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bsetec_giveme_footer_menu_group`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

CREATE TABLE `bsetec_giveme_footer_menu` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `parent_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `class` varchar(255) NOT NULL DEFAULT '',
  `position` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `group_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



ALTER TABLE `bsetec_giveme_footer_menu`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bsetec_giveme_footer_menu`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;









