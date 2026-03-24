CREATE TABLE IF NOT EXISTS `[db_prefix]member_settings` (
  `ID` int(9) NOT NULL auto_increment,
  `MemberID` int(11) NOT NULL default '0',
  `ActionID` int(11) NOT NULL default '0',
  `Access` enum('friends','favorites','all','none','yes','no') NOT NULL default 'all',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]track_views` (
  `id` int(9) NOT NULL auto_increment,
  `unit` varchar(255) COLLATE utf8_general_ci NOT NULL default '',
  `action` varchar(255) COLLATE utf8_general_ci NOT NULL default '',
  `object_id` int(11) NOT NULL default '0',
  `sender_id` int(11) NOT NULL default '0',
  `created` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]notification_settings` (
  `MemberID` int(11) NOT NULL default '0',
  `Period` ENUM( 'none', 'immediately', 'daily', 'weekly', 'monthly' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'daily',
  `Active` int(1) NOT NULL default '1',
  PRIMARY KEY  (`MemberID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
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
  `title` varchar(150) COLLATE utf8_general_ci NOT NULL default '',
  `desc` varchar(150) COLLATE utf8_general_ci NOT NULL default '',
  `template` varchar(150) COLLATE utf8_general_ci NOT NULL default '',
  `table` varchar(150) COLLATE utf8_general_ci NOT NULL default '',
  `friend_action` enum('yes','no') NOT NULL default 'yes',
  `active` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; 

-- UPDATE `[db_prefix]main` SET `group` = CONCAT('_modzzz_notify_', LOWER(`group`));

-- Dumping data for table `[db_prefix]main`
INSERT INTO `[db_prefix]main` (`group`, `unit`, `action`, `title`, `desc`, `template`, `table`, `friend_action`, `active`) VALUES
('_modzzz_notify_profile', 'profile', 'commentPost', '_modzzz_notify_profile_title', '_modzzz_notify_profile_commentPost_desc', 'modzzz_notify_profile_comment', '', 'no', 1),
('_modzzz_notify_profile', 'profile', 'rate', '_modzzz_notify_profile_title', '_modzzz_notify_profile_rate_desc', 'modzzz_notify_profile_rate', '', 'no', 1),
('_modzzz_notify_profile', 'profile', 'view', '_modzzz_notify_profile_title', '_modzzz_notify_profile_view_desc', 'modzzz_notify_profile_view', '', 'no', 1),
('_modzzz_notify_profile', 'profile', 'edit_status_message', '_modzzz_notify_profile_title', '_modzzz_notify_profile_status_message_desc', 'modzzz_notify_profile_status_message', '', 'yes', 1),
('_modzzz_notify_avatar', 'bx_avatar', 'add', '_modzzz_notify_avatar_title', '_modzzz_notify_avatar_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_ads', 'bx_ads', 'create', '_modzzz_notify_classifieds_title', '_modzzz_notify_classifieds_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_ads', 'ads', 'commentPost', '_modzzz_notify_classifieds_title', '_modzzz_notify_classifieds_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_ads', 'ads', 'rate', '_modzzz_notify_classifieds_title', '_modzzz_notify_classifieds_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_store', 'bx_store', 'add', '_modzzz_notify_store_title', '_modzzz_notify_store_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_store', 'bx_store', 'commentPost', '_modzzz_notify_store_title', '_modzzz_notify_store_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_store', 'bx_store', 'rate', '_modzzz_notify_store_title', '_modzzz_notify_store_rate_desc', 'modzzz_notify_rate', '', 'no', 1),


('_modzzz_notify_events', 'bx_events', 'add', '_modzzz_notify_event_title', '_modzzz_notify_event_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_events', 'bx_events', 'join', '_modzzz_notify_event_title', '_modzzz_notify_event_join_desc', 'modzzz_notify_event_join', '', 'no', 1),
('_modzzz_notify_events', 'bx_events', 'commentPost', '_modzzz_notify_event_title', '_modzzz_notify_event_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_events', 'bx_events', 'rate', '_modzzz_notify_event_title', '_modzzz_notify_event_rate_desc', 'modzzz_notify_rate', '', 'no', 1),


('_modzzz_notify_modzzz_classified', 'modzzz_classified', 'add', '_modzzz_notify_modzzz_classified_title', '_modzzz_notify_modzzz_classified_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_modzzz_classified', 'modzzz_classified', 'join', '_modzzz_notify_modzzz_classified_title', '_modzzz_notify_modzzz_classified_join_desc', 'modzzz_notify_modzzz_classified_join', '', 'no', 1),
('_modzzz_notify_modzzz_classified', 'modzzz_classified', 'commentPost', '_modzzz_notify_modzzz_classified_title', '_modzzz_notify_modzzz_classified_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_modzzz_classified', 'modzzz_classified', 'rate', '_modzzz_notify_modzzz_classified_title', '_modzzz_notify_modzzz_classified_rate_desc', 'modzzz_notify_rate', '', 'no', 1),


('_modzzz_notify_modzzz_articles', 'modzzz_articles', 'add', '_modzzz_notify_modzzz_articles_title', '_modzzz_notify_modzzz_articles_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_modzzz_articles', 'modzzz_articles', 'join', '_modzzz_notify_modzzz_articles_title', '_modzzz_notify_modzzz_articles_join_desc', 'modzzz_notify_modzzz_articles_join', '', 'no', 1),
('_modzzz_notify_modzzz_articles', 'modzzz_articles', 'commentPost', '_modzzz_notify_modzzz_articles_title', '_modzzz_notify_modzzz_articles_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_modzzz_articles', 'modzzz_articles', 'rate', '_modzzz_notify_modzzz_articles_title', '_modzzz_notify_modzzz_articles_rate_desc', 'modzzz_notify_rate', '', 'no', 1),


('_modzzz_notify_business_listings', 'modzzz_listing', 'add', '_modzzz_notify_business_listings_title', '_modzzz_notify_business_listings_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_business_listings', 'modzzz_listing', 'join', '_modzzz_notify_business_listings_title', '_modzzz_notify_business_listings_join_desc', 'modzzz_notify_business_listings_join', '', 'no', 1),
('_modzzz_notify_business_listings', 'modzzz_listing', 'commentPost', '_modzzz_notify_business_listings_title', '_modzzz_notify_business_listings_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_business_listings', 'modzzz_listing', 'rate', '_modzzz_notify_business_listings_title', '_modzzz_notify_business_listings_rate_desc', 'modzzz_notify_rate', '', 'no', 1),


('_modzzz_notify_jobs', 'modzzz_jobs', 'commentPost', '_modzzz_notify_job_title', '_modzzz_notify_job_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_jobs', 'modzzz_jobs', 'rate', '_modzzz_notify_job_title', '_modzzz_notify_job_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_jobs', 'modzzz_jobs', 'join', '_modzzz_notify_job_title', '_modzzz_notify_job_join_desc', 'modzzz_notify_job_join', '', 'no', 1),
('_modzzz_notify_jobs', 'modzzz_jobs', 'add', '_modzzz_notify_job_title', '_modzzz_notify_job_add_desc', 'modzzz_notify_add', '', 'yes', 1),


('_modzzz_notify_formations', 'modzzz_formations', 'commentPost', '_modzzz_notify_formation_title', '_modzzz_notify_formation_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_formations', 'modzzz_formations', 'rate', '_modzzz_notify_formation_title', '_modzzz_notify_formation_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_formations', 'modzzz_formations', 'join', '_modzzz_notify_formation_title', '_modzzz_notify_formation_join_desc', 'modzzz_notify_formation_join', '', 'no', 1),
('_modzzz_notify_formations', 'modzzz_formations', 'add', '_modzzz_notify_formation_title', '_modzzz_notify_formation_add_desc', 'modzzz_notify_add', '', 'yes', 1),

('_modzzz_notify_investment', 'modzzz_investment', 'commentPost', '_modzzz_notify_investment_title', '_modzzz_notify_investment_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_investment', 'modzzz_investment', 'rate', '_modzzz_notify_investment_title', '_modzzz_notify_investment_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_investment', 'modzzz_investment', 'join', '_modzzz_notify_investment_title', '_modzzz_notify_investment_join_desc', 'modzzz_notify_investment_join', '', 'no', 1),
('_modzzz_notify_investment', 'modzzz_investment', 'add', '_modzzz_notify_investment_title', '_modzzz_notify_investment_add_desc', 'modzzz_notify_add', '', 'yes', 1),



('_modzzz_notify_groups', 'bx_groups', 'add', '_modzzz_notify_group_title', '_modzzz_notify_group_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_groups', 'bx_groups', 'join', '_modzzz_notify_group_title', '_modzzz_notify_group_join_desc', 'modzzz_notify_group_join', '', 'no', 1),
('_modzzz_notify_groups', 'bx_groups', 'commentPost', '_modzzz_notify_group_title', '_modzzz_notify_group_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_groups', 'bx_groups', 'rate', '_modzzz_notify_group_title', '_modzzz_notify_group_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_sounds', 'bx_sounds', 'add', '_modzzz_notify_sound_title', '_modzzz_notify_sound_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_sounds', 'bx_sounds', 'commentPost', '_modzzz_notify_sound_title', '_modzzz_notify_sound_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_sounds', 'bx_sounds', 'rate', '_modzzz_notify_sound_title', '_modzzz_notify_sound_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_photos', 'bx_photos', 'add', '_modzzz_notify_photo_title', '_modzzz_notify_photo_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_photos', 'bx_photos', 'commentPost', '_modzzz_notify_photo_title', '_modzzz_notify_photo_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_photos', 'bx_photos', 'rate', '_modzzz_notify_photo_title', '_modzzz_notify_photo_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_videos', 'bx_videos', 'add', '_modzzz_notify_video_title', '_modzzz_notify_video_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_videos', 'bx_videos', 'commentPost', '_modzzz_notify_video_title', '_modzzz_notify_video_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_videos', 'bx_videos', 'rate', '_modzzz_notify_video_title', '_modzzz_notify_video_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_sites', 'bx_sites', 'add', '_modzzz_notify_site_title', '_modzzz_notify_site_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_sites', 'bx_sites', 'commentPost', '_modzzz_notify_site_title', '_modzzz_notify_site_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_sites', 'bx_sites', 'rate', '_modzzz_notify_site_title', '_modzzz_notify_site_rate_desc', 'modzzz_notify_rate', '', 'no', 1),


('_modzzz_notify_blogs', 'bx_blogs', 'create', '_modzzz_notify_blog_title', '_modzzz_notify_blog_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_blogs', 'bx_blogs', 'rate', '_modzzz_notify_blog_title', '_modzzz_notify_blog_rate_desc', 'modzzz_notify_rate', '', 'no', 1),



('_modzzz_notify_blogs', 'bx_blogs', 'commentPost', '_modzzz_notify_blog_title', '_modzzz_notify_blog_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_polls', 'bx_poll', 'add', '_modzzz_notify_poll_title', '_modzzz_notify_poll_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_polls', 'bx_poll', 'answered', '_modzzz_notify_poll_title', '_modzzz_notify_poll_vote_desc', 'modzzz_notify_poll_answered', '', 'no', 1),
('_modzzz_notify_polls', 'bx_poll', 'commentPost', '_modzzz_notify_poll_title', '_modzzz_notify_poll_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_polls', 'bx_poll', 'rate', '_modzzz_notify_poll_title', '_modzzz_notify_poll_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_files', 'bx_files', 'add', '_modzzz_notify_file_title', '_modzzz_notify_file_add_desc', 'modzzz_notify_add', '', 'yes', 1),
('_modzzz_notify_files', 'bx_files', 'commentPost', '_modzzz_notify_file_title', '_modzzz_notify_file_commentPost_desc', 'modzzz_notify_comment', '', 'no', 1),
('_modzzz_notify_files', 'bx_files', 'rate', '_modzzz_notify_file_title', '_modzzz_notify_file_rate_desc', 'modzzz_notify_rate', '', 'no', 1),
('_modzzz_notify_profile', 'friend', 'delete', '_modzzz_notify_friend_title', '_modzzz_notify_friend_delete_desc', 'modzzz_notify_friend_delete', '', 'no', 1),
('_modzzz_notify_profile', 'fave', 'add', '_modzzz_notify_hotlist_title', '_modzzz_notify_hotlisted_desc', 'modzzz_notify_hotlisted', '', 'no', 1),
('_modzzz_notify_profile', 'fave', 'delete', '_modzzz_notify_hotlist_title', '_modzzz_notify_hotlist_delete_desc', 'modzzz_notify_hotlist_delete', '', 'no', 1),
('_modzzz_notify_profile', 'block', 'add', '_modzzz_notify_blocklist_title', '_modzzz_notify_blocklisted_desc', 'modzzz_notify_blocklisted', '', 'no', 1),
('_modzzz_notify_profile', 'block', 'delete', '_modzzz_notify_blocklist_title', '_modzzz_notify_blocklist_delete_desc', 'modzzz_notify_blocklist_delete', '', 'no', 1),
 
('_modzzz_notify_mail', 'profile', 'send_mail_internal', '_modzzz_notify_mail_title', '_modzzz_notify_mail_receive_desc', 'modzzz_notify_receive_mail', '', 'no', 1),

('_modzzz_notify_wall', 'bx_wall', 'update', '_modzzz_notify_wall_title', '_modzzz_notify_wall_desc', 'modzzz_notify_wall_update', '', 'no', 1),
('_modzzz_notify_wall', 'bx_wall', 'comment', '_modzzz_notify_wall_title', '_modzzz_notify_wall_comment_desc', 'modzzz_notify_wall_comment', '', 'no', 1),

('_modzzz_notify_forum', 'bx_forum', 'post_participant', '_modzzz_notify_forum_topic_title', '_modzzz_notify_forum_participant_desc', 'modzzz_notify_forum_post_participant', '', 'no', 1),
('_modzzz_notify_forum', 'bx_forum', 'reply', '_modzzz_notify_forum_post_title', '_modzzz_notify_forum_reply_desc', 'modzzz_notify_forum_post_reply', '', 'no', 1);

-- ('Forum', 'bx_forum', 'new_topic', '_modzzz_notify_forum_post_title', '_modzzz_notify_forum_post_desc', 'modzzz_notify_forum_post', '', 'yes', 1),
 

CREATE TABLE IF NOT EXISTS `[db_prefix]field_mapping` (
  `unit` varchar(50) NOT NULL default '',
  `table` varchar(30) NOT NULL default '',
  `id_field` varchar(30) NOT NULL default '',
  `owner_field` varchar(30) NOT NULL default '',
  `title_field` varchar(30) NOT NULL default '',
  `uri_field` varchar(30) NOT NULL default '',
  `view_uri` varchar(100) NOT NULL default '',
  `class` varchar(100) NOT NULL,
  PRIMARY KEY  (`unit`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; 

 
-- insert field values
INSERT INTO `[db_prefix]field_mapping` (`unit`, `table`, `id_field`, `owner_field`, `title_field`, `uri_field`, `view_uri`, `class`) VALUES
('bx_ads', 'bx_ads_main', 'ID', 'IDProfile', 'Title', 'EntryUri', '{module_url}view/{uri}', 'BxAdsModule'),
('bx_blogs', 'bx_blogs_posts', 'PostID', 'OwnerID', 'PostCaption', 'PostUri', '{site_url}blogs/entry/{uri}', 'BxBlogsModule'),


('modzzz_jobs', 'modzzz_jobs_main', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxJobsModule'),
('modzzz_investment', 'modzzz_investment_main', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxInvestmentModule'),
('modzzz_formations', 'modzzz_formations_main', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxFormationsModule'),
('modzzz_classified', 'modzzz_classified_main', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxClassifiedModule'),
('modzzz_listing', 'modzzz_listing_main', 'id', 'author_id', 'title', 'uri',  '{module_url}view/{uri}', 'BxListingModule'),
('modzzz_articles', 'modzzz_articles_main', 'id', 'author_id', 'title', 'uri',  '{module_url}view/{uri}', 'BxArticlesModule'),

('bx_events', 'bx_events_main', 'ID', 'ResponsibleID', 'Title', 'EntryUri', '{module_url}view/{uri}', 'BxEventsModule'),


('bx_files', 'bx_files_main', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxFilesModule'),
('bx_groups', 'bx_groups_main', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxGroupsModule'),
('bx_photos', 'bx_photos_main', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxPhotosModule'),
('bx_poll', 'bx_poll_data', 'id_poll', 'id_profile', 'poll_question', 'id_poll', '{module_url}&action=show_poll_info&id={id}', 'BxPollModule'),
('bx_sites', 'bx_sites_main', 'id', 'ownerid', 'title', 'entryUri', '{module_url}view/{uri}', 'BxSitesModule'),
('bx_store', 'bx_store_products', 'id', 'author_id', 'title', 'uri', '{module_url}view/{uri}', 'BxStoreModule'),
('profile', 'Profiles', 'ID', 'ID', 'NickName', 'NickName', '{site_url}NickName', ''),
('bx_videos', 'RayVideoFiles', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxVideosModule'),
('bx_sounds', 'RayMp3Files', 'ID', 'Owner', 'Title', 'Uri', '{module_url}view/{uri}', 'BxSoundsModule');

 
-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_notify_my', 'Notify My', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_notify_main', 'Notify Home', @iMaxOrder+2);
 
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('modzzz_notify_my', '1140px', 'Notify description block', '_modzzz_notify_block_my_actions', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_notify_my', '1140px', 'Notify settings block', '_modzzz_notify_block_settings', '3', '0', 'Settings', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_notify_main', '1140px', 'Notify description block', '_modzzz_notify_block_friend_actions', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_notify_main', '1140px', 'Notify settings block', '_modzzz_notify_block_settings', '3', '0', 'Settings', '', '1', '28.1', 'non,memb', '0');

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=notify/', 'm/notify/', 'modzzz_notify_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Notify', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_notify_permalinks', 'on', 26, 'Enable friendly permalinks in notifications', 'checkbox', '', '', '0', ''),
('modzzz_notify_email_default', 'daily', @iCategId, 'Default period for receiving notifications', 'select', '', '', '0', 'none,immediately,daily,weekly,monthly'),
('modzzz_notify_friend_default', 'all', @iCategId, 'Default Notifications about Friends/Fave activities', 'select', '', '', '0', 'friends,favorites,all,none'),
('modzzz_notify_member_default', 'yes', @iCategId, 'Default setting for receiving notifications', 'select', '', '', '0', 'yes,no'),
('modzzz_notify_override', 'no', @iCategId, 'Override existing members settings', 'select', '', '', '0', 'yes,no')
;
  

-- email templates 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_add', 'Someone in your Friends or Faves list added a <Item> at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has added a <Item>, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'New Item Added Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_profile_view', 'Someone viewed your Profile at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has viewed your Profile at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Profile View Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_profile_status_message', 'Someone in your Friends or Faves list changed their Status Message at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has updated their Status Message at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Status Message Change Notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_poll_answered', 'Your Poll Question Answered at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> answered your Poll Question, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Poll Answered Notification', '0');
  
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_event_join', 'Someone in your Friends or Faves list is attending an Event at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> will be attending an Event, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Event Join Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_modzzz_articles_join', 'Someone in your Friends or Faves list is following your article at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> is following your article, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Article Join Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_modzzz_classified_join', 'Someone in your Friends or Faves list is watching your ad at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> is watching your ad, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Classified Join Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_business_listings_join', 'Someone in your Friends or Faves list is following your company at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> is following your company, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Business Listing Join Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_job_join', 'Someone in your Friends or Faves list is following your job post at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> is following your job post <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Job Join Notification', '0');


INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_formation_join', 'Someone in your Friends or Faves list is following your formation post at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> is following your formation post <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Formation Join Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_investment_join', 'Someone in your Friends or Faves list is following your Project post at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> is following your project post <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Investment Join Notification', '0');



INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_group_join', 'Someone in your Friends or Faves list joined a Group at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has joined a Group, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Group Join Notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_profile_comment', 'Comment posted on your Profile at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has commented on your Profile at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Profile Comment Posted Notification', '0');
  
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_comment', 'Comment posted on your <Item> at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has commented on your <Item>, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'General Comment Posted Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_profile_rate', 'Your Profile has been Rated at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has Rated your <a href="<ItemUrl>">Profile</a> at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'Profile Rated Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_rate', 'Rating left on your <Item> at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has Rated your <Item>, <a href="<ItemUrl>"><ItemTopic></a>, at <a href="<SiteUrl>"><SiteName></a></p><bx_include_auto:_email_footer.html />', 'General Item Rated Notification', '0');
   
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_hotlisted', 'You were Hotlisted at by a member on <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has added you to their Faves List</p><bx_include_auto:_email_footer.html />', 'Hotlisted Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_blocklisted', 'You were Blocked by a member at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has added you to their Block list</p><bx_include_auto:_email_footer.html />', 'Blocklisted Notification', '0');
   
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_wall_update', 'Your wall was updated at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has posted on your Wall</p><bx_include_auto:_email_footer.html />', 'Wall Post Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_wall_comment', 'Your wall post has a new comment on <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has posted a comment on your Wall</p><bx_include_auto:_email_footer.html />', 'Wall Comment Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_friend_delete', 'You are no longer Friends with a member on <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has removed you from their Friends List</p><bx_include_auto:_email_footer.html />', 'Friend Removal Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_hotlist_delete', 'You are no longer Hotlisted by a member on <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has removed you from their Faves List</p><bx_include_auto:_email_footer.html />', 'Hotlist Removal Notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_receive_mail', 'You received a message on <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p>You have received a message from <a href="<PosterLink>"><PosterName></a>!</p><p>To check this message login to your account here: <a href="<SiteUrl>member.php"><SiteUrl>member.php</a></p><bx_include_auto:_email_footer.html />', 'New Message', '0');
  
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_blocklist_delete', 'You are no longer Blocked by a member at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has removed you from their Block list</p><bx_include_auto:_email_footer.html />', 'Blocklist Removal Notification', '0');


-- INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
-- ('modzzz_notify_flagged_forum_reply', 'Reply to a flagged forum topic at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has replied to your flagged Topic, <a href="<ItemUrl>"><ItemTopic></a></p><p><br>***************</p><bx_include_auto:_email_footer.html />', 'Flagged Forum Reply Notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_forum_post_participant', 'Reply to a participating forum topic at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has replied to a Topic you have participated in ... <a href="<ItemUrl>"><ItemTopic></a></p><p><br>***************</p><bx_include_auto:_email_footer.html />', 'Participating Forum Topic Reply Notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_forum_post_reply', 'Reply to your forum post at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has replied to your post, <a href="<ItemUrl>"><ItemTopic></a></p><bx_include_auto:_email_footer.html />', 'Reply to a forum post Notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_forum_post', 'New forum topic at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <OwnerName></b>,</p><p><a href="<PosterLink>"><PosterName></a> has posted a Forum Topic <a href="<ItemUrl>"><ItemTopic></a></p><p><br>***************</p><bx_include_auto:_email_footer.html />', 'New Forum Topic Notification', '0');
 


-- top menu 
SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Notify', '_modzzz_notify_menu_root', 'modules/?r=notify/home/|modules/?r=notify/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'envelope', 'envelope', 1, '');
SET @iCatRoot := LAST_INSERT_ID();

INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Notify My Page', '_modzzz_notify_menu_my', 'modules/?r=notify/my/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Notify Main Page', '_modzzz_notify_menu_main', 'modules/?r=notify/home/', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
 
-- member menu
SET @iParentID = IFNULL((SELECT `ID` FROM `sys_menu_top` WHERE `Link` = 'member.php' AND `Type`='top' AND `Active`=1 LIMIT 1),1);


INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iParentID, 'Notify Activity', '_modzzz_notify_activity', 'm/notify/my/', 10, 'memb', '', '', '', 1, 1, 1, 'custom', '', 0, ''); 
  
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_notify', '_modzzz_notify', '{siteUrl}modules/?r=notify/administration/', 'Notify module by Modzzz','envelope', @iMax+1);


-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_notify_profile_delete', '', '', 'BxDolService::call(''notify'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_notify_profile_join', '', '', 'BxDolService::call(''notify'', ''response_profile_join'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'join', @iHandler);
 
-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_notify', 'BxNotifyResponse', 'modules/modzzz/notify/classes/BxNotifyResponse.php', '');
SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` VALUES 
(NULL, 'profile', 'send_mail_internal', @iHandler),


(NULL, 'bx_blogs', 'commentRated', @iHandler),
(NULL, 'bx_blogs', 'rate', @iHandler),
(NULL, 'bx_blogs', 'commentPost', @iHandler),
(NULL, 'bx_blogs', 'create', @iHandler),
(NULL, 'bx_sites', 'commentRated', @iHandler),
(NULL, 'bx_sites', 'rate', @iHandler),
(NULL, 'bx_sites', 'commentPost', @iHandler),
(NULL, 'bx_sites', 'add', @iHandler),
(NULL, 'bx_videos', 'commentRated', @iHandler),
(NULL, 'bx_videos', 'rate', @iHandler),
(NULL, 'bx_videos', 'commentPost', @iHandler),
(NULL, 'bx_videos', 'add', @iHandler),
(NULL, 'bx_photos', 'commentRated', @iHandler),
(NULL, 'bx_photos', 'rate', @iHandler),
(NULL, 'bx_photos', 'commentPost', @iHandler),
(NULL, 'bx_photos', 'add', @iHandler),
(NULL, 'bx_sounds', 'commentRated', @iHandler),
(NULL, 'bx_sounds', 'rate', @iHandler),
(NULL, 'bx_sounds', 'commentPost', @iHandler),
(NULL, 'bx_sounds', 'add', @iHandler),
(NULL, 'bx_groups', 'commentRated', @iHandler),
(NULL, 'bx_groups', 'rate', @iHandler),
(NULL, 'bx_groups', 'commentPost', @iHandler),
(NULL, 'bx_groups', 'join', @iHandler),
(NULL, 'bx_groups', 'add', @iHandler),


(NULL, 'bx_events', 'commentRated', @iHandler),
(NULL, 'bx_events', 'rate', @iHandler),
(NULL, 'bx_events', 'commentPost', @iHandler),
(NULL, 'bx_events', 'join', @iHandler),
(NULL, 'bx_events', 'add', @iHandler),

(NULL, 'modzzz_jobs', 'rate', @iHandler),
(NULL, 'modzzz_jobs', 'commentPost', @iHandler),
(NULL, 'modzzz_jobs', 'add', @iHandler),
(NULL, 'modzzz_jobs', 'join', @iHandler),

(NULL, 'modzzz_formations', 'rate', @iHandler),
(NULL, 'modzzz_formations', 'commentPost', @iHandler),
(NULL, 'modzzz_formations', 'add', @iHandler),
(NULL, 'modzzz_formations', 'join', @iHandler),

(NULL, 'modzzz_investment', 'rate', @iHandler),
(NULL, 'modzzz_investment', 'commentPost', @iHandler),
(NULL, 'modzzz_investment', 'add', @iHandler),
(NULL, 'modzzz_investment', 'join', @iHandler),



(NULL, 'modzzz_listing', 'rate', @iHandler),
(NULL, 'modzzz_listing', 'commentPost', @iHandler),
(NULL, 'modzzz_listing', 'add', @iHandler),
(NULL, 'modzzz_listing', 'join', @iHandler),

(NULL, 'modzzz_classified', 'rate', @iHandler),
(NULL, 'modzzz_classified', 'commentPost', @iHandler),
(NULL, 'modzzz_classified', 'add', @iHandler),
(NULL, 'modzzz_classified', 'join', @iHandler),

(NULL, 'modzzz_articles', 'rate', @iHandler),
(NULL, 'modzzz_articles', 'commentPost', @iHandler),
(NULL, 'modzzz_articles', 'add', @iHandler),
(NULL, 'modzzz_articles', 'join', @iHandler),




(NULL, 'bx_store', 'commentRated', @iHandler),
(NULL, 'bx_store', 'rate', @iHandler),
(NULL, 'bx_store', 'commentPost', @iHandler),
(NULL, 'bx_store', 'add', @iHandler),
(NULL, 'bx_news', 'commentRated', @iHandler),
(NULL, 'bx_news', 'rate', @iHandler),
(NULL, 'bx_news', 'commentPost', @iHandler),
(NULL, 'bx_arl', 'commentRated', @iHandler),
(NULL, 'bx_arl', 'rate', @iHandler),
(NULL, 'bx_arl', 'commentPost', @iHandler),
(NULL, 'ads', 'commentRated', @iHandler),
(NULL, 'ads', 'rate', @iHandler),
(NULL, 'ads', 'commentPost', @iHandler),
(NULL, 'ads', 'create', @iHandler),
(NULL, 'bx_avatar', 'add', @iHandler),
(NULL, 'profile', 'view', @iHandler),
(NULL, 'profile', 'commentRated', @iHandler),
(NULL, 'profile', 'rate', @iHandler),
(NULL, 'profile', 'commentPost', @iHandler),
 
(NULL, 'bx_poll', 'add', @iHandler),
(NULL, 'bx_poll', 'answered', @iHandler),
(NULL, 'bx_poll', 'commentPost', @iHandler),
(NULL, 'bx_poll', 'rate', @iHandler),
(NULL, 'bx_poll', 'commentRated', @iHandler),

(NULL, 'bx_wall', 'update', @iHandler),
(NULL, 'bx_wall', 'post', @iHandler),

(NULL, 'bx_forum', 'reply', @iHandler),
(NULL, 'bx_forum', 'new_topic', @iHandler),
 
(NULL, 'bx_files', 'add', @iHandler),
(NULL, 'bx_files', 'commentPost', @iHandler),
(NULL, 'bx_files', 'rate', @iHandler),
(NULL, 'bx_files', 'commentRated', @iHandler),
(NULL, 'friend', 'delete', @iHandler),
(NULL, 'block', 'add', @iHandler),
(NULL, 'block', 'delete', @iHandler),
(NULL, 'fave', 'add', @iHandler),
(NULL, 'fave', 'delete', @iHandler)
;


INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_notify_periodical', 'Notifications Report from <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p>Members whom you are friends/faves with on <a href="<SiteUrl>"><SiteName></a> performed the following activities:</p><p><Data></p><BR><BR><bx_include_auto:_email_footer.html />', 'Friends/Faves Activity Notification message', '0');
 
INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
( 'BxNotify', '1 0 * * *', 'BxNotifyCron', 'modules/modzzz/notify/classes/BxNotifyCron.php', '') ;
   
INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
( 'BxNotifyInit', '*/1 * * * *', 'BxNotifyInitCron', 'modules/modzzz/notify/classes/BxNotifyInitCron.php', '');
