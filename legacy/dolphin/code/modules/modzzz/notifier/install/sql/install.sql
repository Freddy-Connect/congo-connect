CREATE TABLE IF NOT EXISTS `[db_prefix]notifications` (
  `id` int(11) NOT NULL auto_increment,
  `member_id` int(11) NOT NULL default '0',
  `subject` varchar(255) COLLATE utf8_general_ci NOT NULL default '',
  `message` text COLLATE utf8_general_ci NOT NULL default '',
  `title` varchar(150) COLLATE utf8_general_ci NOT NULL default '',
  `created` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; 

CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(11) NOT NULL auto_increment,
  `group` varchar(50) NOT NULL,
  `unit` varchar(50) COLLATE utf8_general_ci NOT NULL default '',
  `action` varchar(50) COLLATE utf8_general_ci NOT NULL default '',
  `desc` varchar(150) COLLATE utf8_general_ci NOT NULL default '',
  `active` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ; 


-- Dumping data for table `[db_prefix]main`
INSERT INTO `[db_prefix]main` (`group`, `unit`, `action`, `desc`, `active`) VALUES
('_bx_ads', 'bx_ads', 'create', '_modzzz_notifier_classifieds_desc', 1),
('_bx_store', 'bx_store', 'add', '_modzzz_notifier_store_desc', 1),
('_bx_events', 'bx_events', 'add', '_modzzz_notifier_event_desc', 1),
('_bx_groups', 'bx_groups', 'add', '_modzzz_notifier_group_desc', 1),
('_bx_sounds', 'bx_sounds', 'add', '_modzzz_notifier_sound_desc', 1),
('_bx_photos', 'bx_photos', 'add', '_modzzz_notifier_photo_desc', 1),
('_bx_videos', 'bx_videos', 'add', '_modzzz_notifier_video_desc', 1),
('_bx_sites', 'bx_sites', 'add', '_modzzz_notifier_site_desc', 1),
('_bx_blog_post', 'bx_blogs', 'create', '_modzzz_notifier_blog_desc', 1),
('_bx_polls', 'bx_poll', 'add', '_modzzz_notifier_poll_desc', 1),
('_bx_files', 'bx_files', 'add', '_modzzz_notifier_file_desc', 1);
 

CREATE TABLE IF NOT EXISTS `[db_prefix]field_mapping` (
  `unit` varchar(50) NOT NULL default '',
  `table` varchar(30) NOT NULL default '',
  `id_field` varchar(30) NOT NULL default '',
  `owner_field` varchar(30) NOT NULL default '',
  `title_field` varchar(30) NOT NULL default '',
  `uri_field` varchar(30) NOT NULL default '',
  `view_uri` varchar(100) NOT NULL default '',
  `class` varchar(100) NOT NULL default '',
  `status_field` varchar(100) NOT NULL,
  `pending_value` varchar(20) NOT NULL,
  PRIMARY KEY  (`unit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; 

 
-- insert field values
INSERT INTO `[db_prefix]field_mapping` (`status_field`, `pending_value`, `unit`, `table`, `id_field`, `owner_field`, `title_field`, `uri_field`, `view_uri`, `class`) VALUES
('Status', 'inactive', 'bx_ads', 'bx_ads_main', 'ID', 'IDProfile', 'Title', 'EntryUri', '{module_url}view/{uri}', 'BxAdsModule'),
('PostStatus', 'disapproval', 'bx_blogs', 'bx_blogs_posts', 'PostID', 'OwnerID', 'PostCaption', 'PostUri', '{site_url}blogs/entry/{uri}', 'BxBlogsModule'),
('Status', 'pending', 'bx_events', 'bx_events_main', 'ID', 'ResponsibleID', 'Title', 'EntryUri', '{module_url}view/{uri}', 'BxEventsModule'),
('status', 'pending', 'bx_groups', 'bx_groups_main', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxGroupsModule'),
('poll_approval', '0', 'bx_poll', 'bx_poll_data', 'id_poll', 'id_profile', 'poll_question', 'id_poll', '{module_url}&action=show_poll_info&id={id}', 'BxPollModule'),
('status', 'pending', 'bx_sites', 'bx_sites_main', 'id', 'ownerid', 'title', 'entryUri', '{module_url}view/{uri}', 'BxSitesModule'),
('status', 'pending', 'bx_store', 'bx_store_products', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxStoreModule'),
('status', 'pending', 'profile', 'Profiles', 'ID', 'ID', 'NickName', 'NickName', '{site_url}NickName', ''),
('Status', 'pending', 'bx_files', 'bx_files_main', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxFilesModule'),
('Status', 'pending', 'bx_photos', 'bx_photos_main', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxPhotosModule'),
('Status', 'pending', 'bx_videos', 'RayVideoFiles', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxVideosModule'),
('Status', 'pending', 'bx_sounds', 'RayMp3Files', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxSoundsModule');

 
-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=notifier/', 'm/notifier/', 'modzzz_notifier_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Notifier', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_notifier_permalinks', 'on', 26, 'Enable friendly permalinks in notifications', 'checkbox', '', '', '0', ''),
('modzzz_notifier_only_pending', 'on', @iCategId, 'Notify only for pending items', 'checkbox', '', '', '0', ''),
('modzzz_notifier_email_default', 'immediately', @iCategId, 'Default period for receiving Notifications', 'select', '', '', '0', 'immediately,daily');
  

-- email templates 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notifier_add', 'A member added a <Item> at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has added a <Item>, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'New Item Added Notification', '0');


-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_notifier', '_modzzz_notifier', '{siteUrl}modules/?r=notifier/administration/', 'Admin Notifier module by Modzzz','envelope', @iMax+1);

 
-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_notifier', 'BxNotifierResponse', 'modules/modzzz/notifier/classes/BxNotifierResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` VALUES  
(NULL, 'bx_blogs', 'create', @iHandler), 
(NULL, 'bx_sites', 'add', @iHandler), 
(NULL, 'bx_videos', 'add', @iHandler), 
(NULL, 'bx_photos', 'add', @iHandler), 
(NULL, 'bx_sounds', 'add', @iHandler), 
(NULL, 'bx_groups', 'add', @iHandler), 
(NULL, 'bx_events', 'add', @iHandler), 
(NULL, 'bx_store', 'add', @iHandler), 
(NULL, 'ads', 'create', @iHandler),
(NULL, 'bx_poll', 'add', @iHandler), 
(NULL, 'bx_files', 'add', @iHandler);


INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notifier_periodical', 'Notifications Report from <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p>Members whom you are friends/faves with on <a href="<SiteUrl>"><SiteName></a> performed the following activities:</p><p><Data></p><BR><BR><bx_include_auto:_email_footer.html />', 'Admin Notification message', '0');


INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxNotifier', '55 23 * * *', 'BxNotifierCron', 'modules/modzzz/notifier/classes/BxNotifierCron.php', '') ;
  

