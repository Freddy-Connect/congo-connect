ALTER TABLE `sys_menu_top` CHANGE `Link` `Link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

-- create tables

CREATE TABLE IF NOT EXISTS `[db_prefix]banned` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) NOT NULL default '0', 
  `profile_id` int(11) NOT NULL default '0',
  `banned_by` int(11) NOT NULL default '0',
  `reason` text NOT NULL,
  `date` int(11) NOT NULL default '0',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(64) NOT NULL,
  `zip` varchar(16) NOT NULL,
  `status` ENUM( 'approved', 'pending', 'expired') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'approved',   
  `thumb` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL default '',  
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_group_to` VARCHAR( 16 ) NOT NULL,
  `allow_view_fans_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_view_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,  
  `allow_post_in_forum_to` varchar(16) NOT NULL,
  `allow_view_forum_to` varchar(16) NOT NULL,
  `allow_join_to` int(11) NOT NULL,
  `join_confirmation` tinyint(4) NOT NULL default '0',
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL, 
 
  `state` varchar(10) NOT NULL default '' ,
  `group_membership_filter` varchar(100) NOT NULL default '',
  `group_membership_view_filter` varchar(100) NOT NULL default '',
  `video_embed` TEXT NOT NULL,
 
  `currency` varchar(255) NOT NULL default '', 
  `featured_expiry_date`  INT NOT NULL,
  `featured_date` INT NOT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc`,`tags`,`categories`),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]fans` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]admins` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]views_track` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- create forum tables

CREATE TABLE `[db_prefix]forum` (
  `forum_id` int(10) unsigned NOT NULL auto_increment,
  `forum_uri` varchar(255) NOT NULL default '',
  `cat_id` int(11) NOT NULL default '0',
  `forum_title` varchar(255) default NULL,
  `forum_desc` varchar(255) NOT NULL default '',
  `forum_posts` int(11) NOT NULL default '0',
  `forum_topics` int(11) NOT NULL default '0',
  `forum_last` int(11) NOT NULL default '0',
  `forum_type` enum('public','private') NOT NULL default 'public',
  `forum_order` int(11) NOT NULL default '0',
  `entry_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`forum_id`),
  KEY `cat_id` (`cat_id`),
  KEY `forum_uri` (`forum_uri`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_cat` (
  `cat_id` int(10) unsigned NOT NULL auto_increment,
  `cat_uri` varchar(255) NOT NULL default '',
  `cat_name` varchar(255) default NULL,
  `cat_icon` varchar(32) NOT NULL default '',
  `cat_order` float NOT NULL default '0',
  `cat_expanded` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`cat_id`),
  KEY `cat_order` (`cat_order`),
  KEY `cat_uri` (`cat_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]forum_cat` (`cat_id`, `cat_uri`, `cat_name`, `cat_icon`, `cat_order`) VALUES 
(1, 'Groups', 'Groups', '', 64);

CREATE TABLE `[db_prefix]forum_flag` (
  `user` varchar(32) NOT NULL default '',
  `topic_id` int(11) NOT NULL default '0',
  `when` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_post` (
  `post_id` int(10) unsigned NOT NULL auto_increment,
  `topic_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  `user` varchar(32) NOT NULL default '0',
  `post_text` mediumtext NOT NULL,
  `when` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',
  `reports` int(11) NOT NULL default '0',
  `hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user` (`user`),
  KEY `when` (`when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_topic` (
  `topic_id` int(10) unsigned NOT NULL auto_increment,
  `topic_uri` varchar(255) NOT NULL default '',
  `forum_id` int(11) NOT NULL default '0',
  `topic_title` varchar(255) NOT NULL default '',
  `when` int(11) NOT NULL default '0',
  `topic_posts` int(11) NOT NULL default '0',
  `first_post_user` varchar(32) NOT NULL default '0',
  `first_post_when` int(11) NOT NULL default '0',
  `last_post_user` varchar(32) NOT NULL default '',
  `last_post_when` int(11) NOT NULL default '0',
  `topic_sticky` int(11) NOT NULL default '0',
  `topic_locked` tinyint(4) NOT NULL default '0',
  `topic_hidden` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `forum_id_2` (`forum_id`,`when`),
  KEY `topic_uri` (`topic_uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user` (
  `user_name` varchar(32) NOT NULL default '',
  `user_pwd` varchar(32) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_join_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user_activity` (
  `user` varchar(32) NOT NULL default '',
  `act_current` int(11) NOT NULL default '0',
  `act_last` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_user_stat` (
  `user` varchar(32) NOT NULL default '',
  `posts` int(11) NOT NULL default '0',
  `user_last_post` int(11) NOT NULL default '0',
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_vote` (
  `user_name` varchar(32) NOT NULL default '',
  `post_id` int(11) NOT NULL default '0',
  `vote_when` int(11) NOT NULL default '0',
  `vote_point` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_name`,`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_actions_log` (
  `user_name` varchar(32) NOT NULL default '',
  `id` int(11) NOT NULL default '0',
  `action_name` varchar(32) NOT NULL default '',
  `action_when` int(11) NOT NULL default '0',
  KEY `action_when` (`action_when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `[db_prefix]forum_attachments` (
  `att_hash` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `att_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `att_type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `att_when` int(11) NOT NULL,
  `att_size` int(11) NOT NULL,
  `att_downloads` int(11) NOT NULL,
  PRIMARY KEY (`att_hash`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_signatures` (
  `user` varchar(32) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `when` int(11) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_view', 'Group View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_main', 'Groups Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_my', 'Groups My', @iMaxOrder+4);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 

('bx_groups_view', '1140px', 'Group''s logo block', '_bx_groups_block_logo', '2', '0', 'Logo', '', '1', '28.1', 'non,memb', '0'), 
('bx_groups_view', '1140px', 'Group''s info block', '_bx_groups_block_info', 2, 1, 'Info', '', '1', 28.1, 'non,memb', '0'),
('bx_groups_view', '1140px', 'Group''s actions block', '_bx_groups_block_actions', 2, 2, 'Actions', '', '1', 28.1, 'non,memb', '0'),    
('bx_groups_view', '1140px', 'Group''s admins block', '_bx_groups_block_admins', '2', '3', 'Admins', '', '1', '28.1', 'non,memb', '0'), 
('bx_groups_view', '1140px', 'Group''s rate block', '_bx_groups_block_rate', 2, 4, 'Rate', '', '1', 28.1, 'non,memb', '0'),    
('bx_groups_view', '1140px', 'Group''s social sharing block', '_sys_block_title_social_sharing', 2, 5, 'SocialSharing', '', 1, 28.1, 'non,memb', 0),
('bx_groups_view', '1140px', 'Group''s fans block', '_bx_groups_block_fans', 2, 6, 'Fans', '', '1', 28.1, 'non,memb', '0'),    
('bx_groups_view', '1140px', 'Group''s unconfirmed fans block', '_bx_groups_block_fans_unconfirmed', 2, 7, 'FansUnconfirmed', '', '1', 28.1, 'memb', '0'),
('bx_groups_view', '1140px', 'Group''s map view', '_bx_groups_block_map', 2, 8, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''groups'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
('bx_groups_view', '1140px', 'Group''s location block', '_bx_groups_block_location', '2', '9', 'Location', '', '1', '28.1', 'non,memb', '0'),   
('bx_groups_view', '1140px', 'Group''s chat', '_Chat', 2, 10, 'PHP', 'return BxDolService::call(''shoutbox'', ''get_shoutbox'', array(''bx_groups'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 11, 28.1, 'non,memb', 0),
('bx_groups_view', '1140px', 'Group''s banned members block', '_bx_groups_block_banned_members', 2, 11, 'Banned', '', '1', 28.1, 'memb', '0'),

('bx_groups_view', '1140px', 'Group''s description block', '_bx_groups_block_desc', 1, 0, 'Desc', '', '1', 71.9, 'non,memb', '0'),
('bx_groups_view', '1140px', 'Group''s photo block', '_bx_groups_block_photo', 1, 1, 'Photo', '', '1', 71.9, 'non,memb', '0'),
('bx_groups_view', '1140px', 'Group''s videos block', '_bx_groups_block_video', 1, 2, 'Video', '', '1', 71.9, 'non,memb', '0'),    
('bx_groups_view', '1140px', 'Group''s Video Embed block', '_bx_groups_block_video_embed', '1', '2', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_view', '1140px', 'Group''s sounds block', '_bx_groups_block_sound', 1, 3, 'Sound', '', '1', 71.9, 'non,memb', '0'),    
('bx_groups_view', '1140px', 'Group''s files block', '_bx_groups_block_files', 1, 4, 'Files', '', '1', 71.9, 'non,memb', '0'),    
('bx_groups_view', '1140px', 'Group''s comments block', '_bx_groups_block_comments', 1, 5, 'Comments', '', '1', 71.9, 'non,memb', '0'),
('bx_groups_view', '1140px', 'Group''s forum block', '_bx_groups_block_forum_feed', '1', '6', 'ForumFeed', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_view', '1140px', 'Group''s local block', '_bx_groups_block_local', '1', '7', 'Local', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_view', '1140px', 'Group''s other block', '_bx_groups_block_other', '1', '8', 'Other', '', '1', '71.9', 'non,memb', '0'),
  
('bx_groups_view', '1140px', 'Group''s Events block', '_bx_groups_block_events', '0', '0', 'Events', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_view', '1140px', 'Group''s News block', '_bx_groups_block_news', '0', '0', 'News', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_view', '1140px', 'Group''s Venues block', '_bx_groups_block_venues', '0', '0', 'Venues', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_view', '1140px', 'Group''s Sponsors block', '_bx_groups_block_sponsors', '0', '0', 'Sponsors', '', '1', '71.9', 'non,memb', '0'),  
('bx_groups_view', '1140px', 'Group''s Blogs block', '_bx_groups_block_blogs', '0', '0', 'Blogs', '', '1', '71.9', 'non,memb', '0'),  
 
('bx_groups_main', '1140px', 'Search Group', '_bx_groups_block_search', '2', '0', 'Search', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_main', '1140px', 'Groups Categories', '_bx_groups_block_categories', '2', '1', 'Categories', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_main', '1140px', 'Groups Calendar', '_bx_groups_block_calendar', '2', '3', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_main', '1140px', 'Group Tags', '_tags_plural', '2', '6', 'Tags', '', '1', '28.1', 'non,memb', '0'), 
('bx_groups_main', '1140px', 'Group States', '_bx_groups_block_states', '2', '7', 'States', '', '1', '28.1', 'non,memb', '0'),	 

('bx_groups_main', '1140px', 'Featured Groups', '_bx_groups_block_featured', '1', '0', 'Featured', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_main', '1140px', 'Recent Groups', '_bx_groups_block_recent', '1', '1', 'Recent', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_main', '1140px', 'Map', '_Map', '1', '2', 'PHP', 'return BxDolService::call(''wmap'', ''homepage_part_block'', array (''groups''));', 1, 71.9, 'non,memb', 0),
('bx_groups_main', '1140px', 'Groups Forum Posts', '_bx_groups_block_forum', '1', '3', 'Forum', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_main', '1140px', 'Group Comments', '_bx_groups_block_latest_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_main', '1140px', 'Groups I Created', '_bx_groups_block_created', '0', '0', 'Created', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_main', '1140px', 'Groups I Joined', '_bx_groups_block_joined', '0', '0', 'Joined', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_main', '1140px', 'Top Groups', '_bx_groups_block_top_list', '0', '0', 'TopList', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_main', '1140px', 'Popular Groups', '_bx_groups_block_popular_list', '0', '0', 'PopularList', '', '1', '28.1', 'non,memb', '0'), 
('bx_groups_main', '1140px', 'Latest Featured Group', '_bx_groups_block_latest_featured_group', '0', '0', 'LatestFeaturedGroup', '', '1', '71.9', 'non,memb', '0'),
 

('bx_groups_my', '1140px', 'Administration Owner', '_bx_groups_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
('bx_groups_my', '1140px', 'User''s groups', '_bx_groups_block_users_groups', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
('bx_groups_my', '1140px', 'User''s joined groups', '_bx_groups_block_joined_groups', '1', '2', 'Joined', '', '0', '100', 'non,memb', '0'), 

('index', '1140px', 'Groups', '_bx_groups_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''homepage_block'');', 1, 71.9, 'non,memb', 0),
('profile', '1140px', 'Joined Groups', '_bx_groups_block_my_groups_joined', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''profile_block_joined'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0),
('profile', '1140px', 'User Groups', '_bx_groups_block_my_groups', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0),

('member', '1140px', 'Groups', '_bx_groups_block_account', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''accountpage_block'');', 1, 71.9, 'non,memb', 0),
('member', '1140px', 'Joined Groups', '_bx_groups_block_my_groups_joined', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''groups'', ''profile_block_joined'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0);

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=groups/', 'm/groups/', 'bx_groups_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Groups', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('bx_groups_permalinks', 'on', 26, 'Enable friendly permalinks in groups', 'checkbox', '', '', '0', ''),
('bx_groups_autoapproval', 'on', @iCategId, 'Activate all groups after creation automatically', 'checkbox', '', '', '0', ''),
('bx_groups_author_comments_admin', 'on', @iCategId, 'Allow group admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('bx_groups_max_email_invitations', '10', @iCategId, 'Max number of email invitation to send per one invite', 'digit', '', '', '0', ''),
('category_auto_app_bx_groups', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('bx_groups_perpage_view_fans', '6', @iCategId, 'Number of fans to show on group view page', 'digit', '', '', '0', ''),
('bx_groups_perpage_browse_fans', '30', @iCategId, 'Number of fans to show on browse fans page', 'digit', '', '', '0', ''),
('bx_groups_perpage_main_recent', '10', @iCategId, 'Number of recently added GROUPS to show on groups home', 'digit', '', '', '0', ''),
('bx_groups_perpage_browse', '14', @iCategId, 'Number of groups to show on browse pages', 'digit', '', '', '0', ''),
('bx_groups_perpage_profile', '4', @iCategId, 'Number of groups to show on profile page', 'digit', '', '', '0', ''),
('bx_groups_perpage_homepage', '5', @iCategId, 'Number of groups to show on homepage', 'digit', '', '', '0', ''),
('bx_groups_homepage_default_tab', 'featured', @iCategId, 'Default groups block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),

('bx_groups_perpage_view_subitems', '6', @iCategId, 'Number of items (Events,Venues etc) to show on group view page', 'digit', '', '', '0', ''),
('bx_groups_perpage_browse_subitems', '30', @iCategId, 'Number of items (Events,Venues etc) to show on the sub section browse page', 'digit', '', '', '0', ''),  
('bx_groups_state_field', '', @iCategId, 'name of profile state field (if you added one)', 'digit', '', '', '0', ''), 

 ('bx_groups_forum_max_preview', '200', @iCategId, 'length of forum post snippet to show on main page', 'digit', '', '', '0', ''),
 ('bx_groups_comments_max_preview', '200', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
 ('bx_groups_perpage_main_popular', '4', @iCategId, 'Number of popular groups to show on main page', 'digit', '', '', '0', ''),
 ('bx_groups_perpage_main_top', '4', @iCategId, 'Number of top rated groups to show on main page', 'digit', '', '', '0', ''),
 ('bx_groups_perpage_main_featured', '5', @iCategId, 'Number of featured groups to show on main page', 'digit', '', '', '0', ''), 
 ('bx_groups_perpage_rss_feed', '10', @iCategId, 'Number of rss items to show on view page', 'digit', '', '', '0', ''),

 ('bx_groups_icon_width', '240', @iCategId, 'Width of logo icon', 'digit', '', '', '0', ''),
 ('bx_groups_icon_height', '240', @iCategId, 'Height of logo icon', 'digit', '', '', '0', ''),    
 
 ('bx_groups_default_country', 'US', @iCategId, 'default country for location', 'digit', '', '', 0, ''),
  
 ('bx_groups_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''),
 ('bx_groups_perpage_main_forum', '5', @iCategId, 'Number of forum posts to show on main page', 'digit', '', '', '0', ''),

 ('bx_groups_perpage_accountpage', '5', @iCategId, 'Number of groups to show on account page', 'digit', '', '', '0', ''), 
 ('bx_groups_max_preview', '300', @iCategId, 'Length of group description snippet to show in blocks', 'digit', '', '', '0', ''),
 
('bx_groups_featured_cost', '0', @iCategId, 'Cost per day for Featured Status', 'digit', '', '', 0, ''),
('bx_groups_buy_featured', '', @iCategId, 'Enable Paypal purchase of Featured Status', 'checkbox', '', '', 0, ''), 
('bx_groups_paypal_email', '', @iCategId, 'Paypal Email', 'digit', '', '', 0, ''),
('bx_groups_currency_code', 'USD', @iCategId, 'Currency code for checkout system (eg. USD,EURO,GBP)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),


('bx_groups_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', '');

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'bx_groups', '_bx_groups', 'BxGroupsSearchResult', 'modules/boonex/groups/classes/BxGroupsSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_groups', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxGroupsVoting', 'modules/boonex/groups/classes/BxGroupsVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_groups', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxGroupsCmts', 'modules/boonex/groups/classes/BxGroupsCmts.php');

 
-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'bx_groups', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'bx_groups', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_groups_permalinks', 'm/groups/browse/tag/{tag}', 'modules/?r=groups/browse/tag/{tag}', '_bx_groups');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'bx_groups', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'bx_groups_permalinks', 'm/groups/browse/category/{tag}', 'modules/?r=groups/browse/category/{tag}', '_bx_groups');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Groups', '0', 'bx_photos', '0', 'active'),
('Arts & Literature', '0', 'bx_groups', '0', 'active'),
('Animals & Pets', '0', 'bx_groups', '0', 'active'),
('Activities', '0', 'bx_groups', '0', 'active'),
('Automotive', '0', 'bx_groups', '0', 'active'),
('Business & Money', '0', 'bx_groups', '0', 'active'),
('Companies & Co-workers', '0', 'bx_groups', '0', 'active'),
('Cultures & Nations', '0', 'bx_groups', '0', 'active'),
('Dolphin Community', '0', 'bx_groups', '0', 'active'),
('Family & Friends', '0', 'bx_groups', '0', 'active'),
('Fan Clubs', '0', 'bx_groups', '0', 'active'),
('Fashion & Style', '0', 'bx_groups', '0', 'active'),
('Fitness & Body Building', '0', 'bx_groups', '0', 'active'),
('Food & Drink', '0', 'bx_groups', '0', 'active'),
('Health & Wellness', '0', 'bx_groups', '0', 'active'),
('Hobbies & Entertainment', '0', 'bx_groups', '0', 'active'),
('Internet & Computers', '0', 'bx_groups', '0', 'active'),
('Love & Relationships', '0', 'bx_groups', '0', 'active'),
('Mass Media', '0', 'bx_groups', '0', 'active'),
('Music & Cinema', '0', 'bx_groups', '0', 'active'),
('Places & Travel', '0', 'bx_groups', '0', 'active'),
('Politics', '0', 'bx_groups', '0', 'active'),
('Recreation & Sports', '0', 'bx_groups', '0', 'active'),
('Religion', '0', 'bx_groups', '0', 'active'),
('Science & Innovations', '0', 'bx_groups', '0', 'active'),
('Sex', '0', 'bx_groups', '0', 'active'),
('Teens & Schools', '0', 'bx_groups', '0', 'active'),
('Other', '0', 'bx_groups', '0', 'active');

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'bx_groups'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'', true); return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', 1, 'bx_groups'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'bx_groups'),
    ('{TitleBroadcast}', 'envelope', '{BaseUri}broadcast/{ID}', '', '', '3', 'bx_groups'),
    ('{TitleJoin}', '{IconJoin}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '4', 'bx_groups'),
    ('{TitleInvite}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '5', 'bx_groups'),
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '6', 'bx_groups'),
    ('{TitleManageFans}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '8', 'bx_groups'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '9', 'bx_groups'),
    ('{TitleEmbed}', 'film', '{BaseUri}embed/{URI}', '', '', '10', 'bx_groups'), 
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '10', 'bx_groups'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '11', 'bx_groups'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '12', 'bx_groups'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '13', 'bx_groups'),
    
    ('{TitleBan}', 'minus-circle', '', 'showPopupAnyHtml (''{BaseUri}ban_member/{ID}'');', '', '25', 'bx_groups'),

    ('{TitleManageAdmins}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_admins_popup/{ID}'');', '', '24', 'bx_groups'),
    ('{TitleAdminAdd}', 'plus-circle', '', 'showPopupAnyHtml (''{BaseUri}add_member/admin/{ID}'');', '', '25', 'bx_groups'),
    ('{TitleFanAdd}', 'plus-circle', '', 'showPopupAnyHtml (''{BaseUri}add_member/fan/{ID}'');', '', '26', 'bx_groups'),

    ('{TitleEventAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/add/{ID}'';', '14', 'bx_groups'), 
    ('{TitleNewsAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/add/{ID}'';', '15', 'bx_groups'),
    ('{TitleVenueAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''venue/add/{ID}'';', '16', 'bx_groups'),
    ('{TitleSponsorAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sponsor/add/{ID}'';', '17', 'bx_groups'),  
    ('{TitleBlogAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''blog/add/{ID}'';', '18', 'bx_groups'),  

    ('{TitlePurchaseFeatured}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''purchase_featured/{ID}'';', '16', 'bx_groups'), 

    ('{TitleActivate}', 'check-circle-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''activate/{ID}'';', '30', 'bx_groups'),
    ('{repostCpt}', 'repeat', '', '{repostScript}', '', 31, 'bx_groups'),


    ('{evalResult}', 'plus', '{BaseUri}browse/my&bx_groups_filter=add_group', '', 'return ($GLOBALS[''logged''][''member''] && BxDolModule::getInstance(''BxGroupsModule'')->isAllowedAdd()) || $GLOBALS[''logged''][''admin''] ? _t(''_bx_groups_action_add_group'') : '''';', 1, 'bx_groups_title'),
    ('{evalResult}', 'users', '{BaseUri}browse/my', '', 'return ($GLOBALS[''logged''][''member''] && BxDolModule::getInstance(''BxGroupsModule'')->isAllowedAdd()) || $GLOBALS[''logged''][''admin''] ? _t(''_bx_groups_action_my_groups'') : '''';', '2', 'bx_groups_title');
    
-- top menu 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Groups', '_bx_groups_menu_root', 'modules/?r=groups/view/|modules/?r=groups/broadcast/|modules/?r=groups/invite/|modules/?r=groups/edit/|forum/groups/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'users', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Group View', '_bx_groups_menu_view_group', 'modules/?r=groups/view/{bx_groups_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Group View Forum', '_bx_groups_menu_view_forum', 'forum/groups/forum/{bx_groups_view_uri}-0.htm|forum/groups/', 1, 'non,memb', '', '', '$oModuleDb = new BxDolModuleDb(); return $oModuleDb->getModuleByUri(''forum'') ? true : false;', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Group View Fans', '_bx_groups_menu_view_fans', 'modules/?r=groups/browse_fans/{bx_groups_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Group View Comments', '_bx_groups_menu_view_comments', 'modules/?r=groups/comments/{bx_groups_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Group View Blogs', '_bx_groups_menu_view_blogs', 'modules/?r=groups/blog/browse/{bx_groups_view_uri}', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 
(NULL, @iCatRoot, 'Group View Sponsors', '_bx_groups_menu_view_sponsors', 'modules/?r=groups/sponsor/browse/{bx_groups_view_uri}', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 
(NULL, @iCatRoot, 'Group View News', '_bx_groups_menu_view_news', 'modules/?r=groups/news/browse/{bx_groups_view_uri}', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '') ,
(NULL, @iCatRoot, 'Group View Venues', '_bx_groups_menu_view_venues', 'modules/?r=groups/venue/browse/{bx_groups_view_uri}', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 
(NULL, @iCatRoot, 'Group View Events', '_bx_groups_menu_view_events', 'modules/?r=groups/event/browse/{bx_groups_view_uri}', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');


SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Groups', '_bx_groups_menu_root', 'modules/?r=groups/home/|modules/?r=groups/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'users', 'users', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Groups Main Page', '_bx_groups_menu_main', 'modules/?r=groups/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Recent Groups', '_bx_groups_menu_recent', 'modules/?r=groups/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Top Rated Groups', '_bx_groups_menu_top_rated', 'modules/?r=groups/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Popular Groups', '_bx_groups_menu_popular', 'modules/?r=groups/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Featured Groups', '_bx_groups_menu_featured', 'modules/?r=groups/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Groups Tags', '_bx_groups_menu_tags', 'modules/?r=groups/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'bx_groups'),
(NULL, @iCatRoot, 'Groups Categories', '_bx_groups_menu_categories', 'modules/?r=groups/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'bx_groups'),
(NULL, @iCatRoot, 'Calendar', '_bx_groups_menu_calendar', 'modules/?r=groups/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Search', '_bx_groups_menu_search', 'modules/?r=groups/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 9, 'Groups', '_bx_groups_menu_my_groups_profile', 'modules/?r=groups/browse/user/{profileUsername}|modules/?r=groups/browse/joined/{profileUsername}', ifnull(@iCatProfileOrder,1), 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 4, 'Groups', '_bx_groups_menu_my_groups_profile', 'modules/?r=groups/browse/my', ifnull(@iCatProfileOrder,1), 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'bx_groups', `Eval` = 'return BxDolService::call(''groups'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_groups', '_bx_groups', '{siteUrl}modules/?r=groups/administration/', 'Groups module by BoonEx','users', @iMax+1);

-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'bx_groups', 'bx_groups', 'modules/?r=groups/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=groups/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'users', @iStatSiteOrder);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('bx_groups', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('bx_groupsp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_bx_groups', '__bx_groups__ (<a href="modules/?r=groups/browse/my&bx_groups_filter=add_group">__l_add__</a>)');

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('bx_groups_broadcast', '<BroadcastTitle>', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n<p>Message from <a href="<EntryUrl>"><EntryTitle></a> group admin:</p> <pre><hr><BroadcastMessage></pre> <hr> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Groups broadcast message', 0),

('bx_groups_join_request', 'Request To Join Your Group', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p>New request to join your group: <a href="<EntryUrl>"><EntryTitle></a>.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Join request to a group', 0),

('bx_groups_join_reject', 'Request To Join A Group Was Rejected', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Your request to join <a href="<EntryUrl>"><EntryTitle></a> group was rejected by group admin.</p> \r\n<bx_include_auto:_email_footer.html />', 'Join group request was rejected', 0),

('bx_groups_join_confirm', 'Your Request To Join A Group Was Confirmed', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n<p>Your request to join <a href="<EntryUrl>"><EntryTitle></a> group was confirmed by the group admin.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Join group request confirmed', 0),

('bx_groups_fan_remove', 'Your Profile Removed From Group Fans', '<bx_include_auto:_email_header.html /> \r\n\r\n<p>Hello <NickName>,</p> <p>Your profile was removed fans list of <a href="<EntryUrl>"><EntryTitle></a> group by the group admin.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Profile Removed From Group Fans', 0),

('bx_groups_fan_become_admin', 'You Are A Group Admin Now', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p>You are an admin of <a href="<EntryUrl>"><EntryTitle></a> group now.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Group admin status granted', 0),

('bx_groups_admin_become_fan', 'Your Group Admin Status Was Revoked', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p>Your admin status was revoked from <a href="<EntryUrl>"><EntryTitle></a> group by the group creator.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Group admin status revoked', 0),
 
('bx_groups_sbs', 'Subscription: Group Details Changed', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p><a href="<ViewLink>"><EntryTitle></a> group details changed: <br /> <ActionName> </p> \r\n<hr>\r\n<p>Cancel this subscription: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Subscription: group changes', 0);


-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups view group', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups add group', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups edit any group', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups delete any group', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups approve groups', NULL);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups broadcast message', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_profile_delete', '', '', 'BxDolService::call(''groups'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_media_delete', '', '', 'BxDolService::call(''groups'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''groups'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('groups', 'view_group', '_bx_groups_privacy_view_group', '3'),
('groups', 'view_fans', '_bx_groups_privacy_view_fans', '3'),
('groups', 'comment', '_bx_groups_privacy_comment', 'f'),
('groups', 'view_comment', '_bx_groups_privacy_view_comment', '3'),
('groups', 'rate', '_bx_groups_privacy_rate', 'f'),
('groups', 'view_forum', '_bx_groups_privacy_view_forum', '3'),
('groups', 'post_in_forum', '_bx_groups_privacy_post_in_forum', 'f'),
('groups', 'join', '_bx_groups_privacy_join', '3'),
('groups', 'upload_photos', '_bx_groups_privacy_upload_photos', 'a'),
('groups', 'upload_videos', '_bx_groups_privacy_upload_videos', 'a'),
('groups', 'upload_sounds', '_bx_groups_privacy_upload_sounds', 'a'),
('groups', 'upload_files', '_bx_groups_privacy_upload_files', 'a');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('bx_groups', '', '', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_groups', 'change', 'bx_groups_sbs', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_groups', 'commentPost', 'bx_groups_sbs', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_groups', 'join', 'bx_groups_sbs', 'return BxDolService::call(''groups'', ''get_subscription_params'', array($arg2, $arg3));');

-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('bx_groups', '_bx_groups', '0.8', 'auto', 'BxGroupsSiteMaps', 'modules/boonex/groups/classes/BxGroupsSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('bx_groups', '_bx_groups', 'bx_groups_main', 'created', '', '', 1, @iMaxOrderCharts);


--- NEW ADDITIONS PREMIUM GROUP
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('bx_groups_invitation', 'Invitation to group: <GroupName>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to this group:</p> <pre><InvitationText></pre> <p> <b>Group Information:</b><br /> Name: <GroupName><br /> Location: <GroupLocation><br /> <a href="<GroupUrl>">More details</a><br /><br /> <a href="<AcceptUrl>">Accept Invitation</a> </p> <bx_include_auto:_email_footer.html />', 'Group invitation template', '0'); 
  

CREATE TABLE IF NOT EXISTS `bx_groups_invite` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(11) NOT NULL,
  `id_profile` int(11) NOT NULL,
  `code` varchar(255) NOT NULL default '', 
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
   PRIMARY KEY  (`id`) 
 ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
  
CREATE TABLE IF NOT EXISTS `bx_groups_activity` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `lang_key` varchar(100) collate utf8_unicode_ci NOT NULL,
  `params` text collate utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('add','delete','change','commentPost','rate','join','unjoin','commentPost','featured','unfeatured','makeAdmin','removeAdmin') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
  
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_local', 'Local Groups Page', @iMaxOrder+1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_local_state', 'Local Groups State Page', @iMaxOrder+2);
 

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 

('bx_groups_local_state', '1140px', 'Local States', '_bx_groups_block_browse_state', '2', '0', 'States', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_local_state', '1140px', 'Local Categories Drilldown', '_bx_groups_block_browse_categories_drilldown', '2', '1', 'Categories', '', '1', '28.1', 'non,memb', '0'),  
('bx_groups_local_state', '1140px', 'Local State Groups', '_bx_groups_block_browse_state_groups', '1', '0', 'StateGroups', '', '1', '71.9', 'non,memb', '0'), 
 
('bx_groups_local', '1140px', 'Local Groups', '_bx_groups_block_browse_country', '1', '0', 'Region', '', '1', '100', 'non,memb', '0');  
  
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Caption` = '_bx_groups_menu_root' AND `Active`=1 AND `Type`='top' LIMIT 1);
 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES  
 (NULL, @iCatRoot, 'My Local Groups', '_bx_groups_menu_local', 'modules/?r=groups/browse/ilocal', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
 (NULL, @iCatRoot, 'Drilldown Groups', '_bx_groups_menu_drilldown', 'modules/?r=groups/local', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');  
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups photos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups sounds add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups videos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups files add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
  
CREATE TABLE IF NOT EXISTS `bx_groups_featured_orders` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `price` FLOAT UNSIGNED NOT NULL,
  `days` int(11) unsigned NOT NULL, 
  `item_id` int(11) unsigned NOT NULL,
  `buyer_id` int(11) unsigned NOT NULL,
  `trans_id` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `trans_type` varchar(100) COLLATE utf8_general_ci NOT NULL, 
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `featured_order_id` (`buyer_id`,`trans_id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_groups_featured_expire_notify', 'Your Featured Group Status at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is inform you that your Featured Status for the Group, <a href="<ListLink>"><ListTitle></a> at <SiteName> has expired. You may purchase Featured Status again at any time you desire <br></p>\r\n\r\n<p><bx_include_auto:_email_footer.html />', 'Featured Group Status Expire Notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_groups_featured_admin_notify', 'A member purchased Featured Group Status at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear Administrator</b>,</p>\r\n\r\n<p><a href="<NickLink>"><NickName></a> has just purchased Featured Status for the Group, <a href="<ListLink>"><ListTitle></a>, for <Days> days at <SiteName><br></p>\r\n\r\n<p><bx_include_auto:_email_footer.html />', 'Featured Group Purchase Admin Notification', '0');
   
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_groups_featured_buyer_notify', 'Your Featured Group Status purchase at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is confirmation of your Featured Status purchase at <SiteName> for Group, <a href="<ListLink>"><ListTitle></a>. It will be Featured for <Days> days<br></p>\r\n\r\n<p><bx_include_auto:_email_footer.html />', 'Featured Group Purchase Buyer Notification', '0');
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'groups purchase featured', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

 
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  `website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `telephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `fax` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `country` varchar(2) NOT NULL,
  `city` varchar(150) NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `state` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `zip` varchar(16) NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_sponsor_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_sponsor_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_groups_sponsor', 'bx_groups_sponsor_rating', 'bx_groups_sponsor_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_groups_sponsor_main', 'rate', 'rate_count', 'id', 'BxGroupsSponsorVoting', 'modules/boonex/groups/classes/BxGroupsSponsorVoting.php');
 

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_groups_sponsor', 'bx_groups_sponsor_cmts', 'bx_groups_sponsor_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_groups_sponsor_main', 'id', 'comments_count', 'BxGroupsSponsorCmts', 'modules/boonex/groups/classes/BxGroupsSponsorCmts.php');
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sponsor/edit/{ID}'';', '0', 'bx_groups_sponsor'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sponsor/delete/{ID}'';', '1', 'bx_groups_sponsor');
 
 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
   
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_sponsors_view', 'Group Sponsor View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_sponsors_browse', 'Group Sponsor Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES  

('bx_groups_sponsors_browse', '1140px', 'Group Sponsor''s browse block', '_bx_groups_block_browse_sponsors', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s actions block', '_bx_groups_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s info block', '_bx_groups_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s rate block', '_bx_groups_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s contact block', '_bx_groups_block_contact', '2', '3', 'Contact', '', '1', '28.1', 'non,memb', '0'),    
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s location block', '_bx_groups_block_location', '2', '4', 'Location', '', '1', '28.1', 'non,memb', '0'),    
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s description block', '_bx_groups_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s photos block', '_bx_groups_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s video block', '_bx_groups_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s files block', '_bx_groups_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s sounds block', '_bx_groups_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),  
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s comments block', '_bx_groups_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');    
 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=groups/sponsor/add/|modules/?r=groups/sponsor/edit/|modules/?r=groups/sponsor/view/|modules/?r=groups/sponsor/browse/') WHERE `Parent`=0 AND `Name`='Groups' AND `Type`='system' AND `Caption`='_bx_groups_menu_root'; 
 
-- BLOGS

CREATE TABLE IF NOT EXISTS `bx_groups_blog_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,

  `website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `telephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `fax` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',

  `country` varchar(2) NOT NULL,
  `city` varchar(150) NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `state` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `zip` varchar(16) NOT NULL,
 
  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_blog_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_blog_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_blog_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_blog_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_blog_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `bx_groups_blog_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_blog_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `bx_groups_blog_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_blog_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_groups_blog', 'bx_groups_blog_rating', 'bx_groups_blog_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_groups_blog_main', 'rate', 'rate_count', 'id', 'BxGroupsBlogVoting', 'modules/boonex/groups/classes/BxGroupsBlogVoting.php');
 

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_groups_blog', 'bx_groups_blog_cmts', 'bx_groups_blog_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_groups_blog_main', 'id', 'comments_count', 'BxGroupsBlogCmts', 'modules/boonex/groups/classes/BxGroupsBlogCmts.php');
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''blog/edit/{ID}'';', '0', 'bx_groups_blog'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''blog/delete/{ID}'';', '1', 'bx_groups_blog');
 
 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
   
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_blogs_view', 'Group Blog View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_blogs_browse', 'Group Blog Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES  

('bx_groups_blogs_browse', '1140px', 'Group Blog''s browse block', '_bx_groups_block_browse_blogs', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('bx_groups_blogs_view', '1140px', 'Group Blog''s actions block', '_bx_groups_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_blogs_view', '1140px', 'Group Blog''s info block', '_bx_groups_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_blogs_view', '1140px', 'Group Blog''s rate block', '_bx_groups_block_rate', '2', '3', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
('bx_groups_blogs_view', '1140px', 'Group Blog''s description block', '_bx_groups_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_blogs_view', '1140px', 'Group Blog''s photos block', '_bx_groups_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_blogs_view', '1140px', 'Group Blog''s video block', '_bx_groups_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_blogs_view', '1140px', 'Group Blog''s files block', '_bx_groups_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_blogs_view', '1140px', 'Group Blog''s sounds block', '_bx_groups_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_blogs_view', '1140px', 'Group Blog''s comments block', '_bx_groups_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');    
 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=groups/blog/add/|modules/?r=groups/blog/edit/|modules/?r=groups/blog/view/|modules/?r=groups/blog/browse/') WHERE `Parent`=0 AND `Name`='Groups' AND `Type`='system' AND `Caption`='_bx_groups_menu_root'; 
 

-- NEWS 
CREATE TABLE IF NOT EXISTS `bx_groups_news_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_news_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_news_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
 
CREATE TABLE IF NOT EXISTS `bx_groups_news_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_news_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_news_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `bx_groups_news_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
  
CREATE TABLE IF NOT EXISTS `bx_groups_news_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_news_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
  
CREATE TABLE IF NOT EXISTS `bx_groups_news_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_groups_news', 'bx_groups_news_rating', 'bx_groups_news_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_groups_news_main', 'rate', 'rate_count', 'id', 'BxGroupsNewsVoting', 'modules/boonex/groups/classes/BxGroupsNewsVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_groups_news', 'bx_groups_news_cmts', 'bx_groups_news_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_groups_news_main', 'id', 'comments_count', 'BxGroupsNewsCmts', 'modules/boonex/groups/classes/BxGroupsNewsCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/edit/{ID}'';', '0', 'bx_groups_news'),
('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/delete/{ID}'';', '1', 'bx_groups_news');
 
 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
   
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_news_view', 'Group News View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_news_browse', 'Group News Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('bx_groups_news_browse', '1140px', 'Group News''s browse block', '_bx_groups_block_browse_news', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('bx_groups_news_view', '1140px', 'Group News''s actions block', '_bx_groups_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_news_view', '1140px', 'Group News''s info block', '_bx_groups_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_news_view', '1140px', 'Group News''s rate block', '_bx_groups_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
('bx_groups_news_view', '1140px', 'Group News''s description block', '_bx_groups_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_news_view', '1140px', 'Group News''s photos block', '_bx_groups_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_news_view', '1140px', 'Group News''s video block', '_bx_groups_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_news_view', '1140px', 'Group News''s files block', '_bx_groups_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_news_view', '1140px', 'Group News''s sounds block', '_bx_groups_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_news_view', '1140px', 'Group News''s comments block', '_bx_groups_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');    
 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=groups/news/add/|modules/?r=groups/news/edit/|modules/?r=groups/news/view/|modules/?r=groups/news/browse/') WHERE `Parent`=0 AND `Name`='Groups' AND `Type`='system' AND `Caption`='_bx_groups_menu_root'; 
 
-- VENUE 
CREATE TABLE IF NOT EXISTS `bx_groups_venue_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(150) NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `state` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `zip` varchar(16) NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_venue_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_venue_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `bx_groups_venue_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_venue_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_venue_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_venue_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_venue_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
  
CREATE TABLE IF NOT EXISTS `bx_groups_venue_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
  
CREATE TABLE IF NOT EXISTS `bx_groups_venue_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_groups_venue', 'bx_groups_venue_rating', 'bx_groups_venue_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_groups_venue_main', 'rate', 'rate_count', 'id', 'BxGroupsVenueVoting', 'modules/boonex/groups/classes/BxGroupsVenueVoting.php');
 

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_groups_venue', 'bx_groups_venue_cmts', 'bx_groups_venue_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_groups_venue_main', 'id', 'comments_count', 'BxGroupsVenueCmts', 'modules/boonex/groups/classes/BxGroupsVenueCmts.php');
  
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''venue/edit/{ID}'';', '0', 'bx_groups_venue'),
('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''venue/delete/{ID}'';', '1', 'bx_groups_venue');
 
 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
   
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_venues_view', 'Group Venue View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_venues_browse', 'Group Venue Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('bx_groups_venues_browse', '1140px', 'Group Venue''s browse block', '_bx_groups_block_browse_venues', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('bx_groups_venues_view', '1140px', 'Group Venue''s actions block', '_bx_groups_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_venues_view', '1140px', 'Group Venue''s info block', '_bx_groups_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_venues_view', '1140px', 'Group Venue''s rate block', '_bx_groups_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
('bx_groups_venues_view', '1140px', 'Group Venue''s location block', '_bx_groups_block_location', '2', '3', 'Location', '', '1', '28.1', 'non,memb', '0'),  
('bx_groups_venues_view', '1140px', 'Group Venue''s description block', '_bx_groups_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_venues_view', '1140px', 'Group Venue''s photos block', '_bx_groups_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_venues_view', '1140px', 'Group Venue''s video block', '_bx_groups_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_venues_view', '1140px', 'Group Venue''s files block', '_bx_groups_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_venues_view', '1140px', 'Group Venue''s sounds block', '_bx_groups_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_venues_view', '1140px', 'Group Venue''s comments block', '_bx_groups_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');    
 

UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=groups/venue/add/|modules/?r=groups/venue/edit/|modules/?r=groups/venue/view/|modules/?r=groups/venue/browse/') WHERE `Parent`=0 AND `Name`='Groups' AND `Type`='system' AND `Caption`='_bx_groups_menu_root'; 
  
 
-- INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
-- ('GroupEventCategories', 1, 1, '__bx_groups_event_birthday'),
-- ('GroupEventCategories', 2, 2, '__bx_groups_event_sporting'),
-- ('GroupEventCategories', 3, 3, '__bx_groups_event_party'),
-- ('GroupEventCategories', 4, 4, '__bx_groups_event_convention'),
-- ('GroupEventCategories', 5, 5, '__bx_groups_event_social'),
-- ('GroupEventCategories', 6, 6, '__bx_groups_event_work'),
-- ('GroupEventCategories', 7, 7, '__bx_groups_event_exhibitions'),
-- ('GroupEventCategories', 9, 9, '__bx_groups_event_festivals'), 
-- ('GroupEventCategories', 10, 10, '__bx_groups_event_politics'), 
-- ('GroupEventCategories', 11, 11, '__bx_groups_event_benefits'), 
-- ('GroupEventCategories', 12, 12, '__bx_groups_event_meeting'),  
-- ('GroupEventCategories', 13, 13, '__bx_groups_event_other'); 
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `country` varchar(2) NOT NULL default 'US',
  `city` varchar(50) NOT NULL default '',
  `place` varchar(100) NOT NULL default '',
  `zip` varchar(16) NOT NULL default '',
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `state` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',  
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `event_start` int(11) NOT NULL default '0',
  `event_end` int(11) NOT NULL default '0',
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL,
  `allow_view_participants_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,
  `allow_join_to` int(11) NOT NULL,
  `allow_post_in_forum_to` varchar(16) NOT NULL,
  `join_confirmation` tinyint(4) NOT NULL default '0',
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grp_event_uri` (`uri`),
  KEY `grp_event_author_id` (`author_id`),
  KEY `grp_event_event_start` (`event_start`),
  KEY `grp_event_created` (`created`),
  FULLTEXT KEY `grp_event_title` (`title`,`desc`,`city`,`place`,`tags`,`categories`),
  FULLTEXT KEY `grp_event_tags` (`tags`),
  FULLTEXT KEY `grp_event_categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;  
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `bx_groups_event_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_cmts` (
  `cmt_id` int( 11 ) NOT NULL AUTO_INCREMENT ,
  `cmt_parent_id` int( 11 ) NOT NULL default '0',
  `cmt_object_id` int( 12 ) NOT NULL default '0',
  `cmt_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_text` text NOT NULL ,
  `cmt_mood` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate` int( 11 ) NOT NULL default '0',
  `cmt_rate_count` int( 11 ) NOT NULL default '0',
  `cmt_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `cmt_replies` int( 11 ) NOT NULL default '0',
  PRIMARY KEY ( `cmt_id` ),
  KEY `cmt_object_id` (`cmt_object_id` , `cmt_parent_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_groups_event_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_groups_event', 'bx_groups_event_rating', 'bx_groups_event_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_groups_event_main', 'rate', 'rate_count', 'id', 'BxGroupsEventVoting', 'modules/boonex/groups/classes/BxGroupsEventVoting.php');
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_groups_event', 'bx_groups_event_cmts', 'bx_groups_event_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_groups_event_main', 'id', 'comments_count', 'BxGroupsEventCmts', 'modules/boonex/groups/classes/BxGroupsEventCmts.php');
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/edit/{ID}'';', '0', 'bx_groups_event'),
('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxGroupsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/delete/{ID}'';', '1', 'bx_groups_event');
 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
   
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_events_view', 'Group Event View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_groups_events_browse', 'Group Event Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES  
('bx_groups_events_browse', '1140px', 'Group Event''s browse block', '_bx_groups_block_browse_events', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'), 
('bx_groups_events_view', '1140px', 'Group Event''s actions block', '_bx_groups_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_events_view', '1140px', 'Group Event''s info block', '_bx_groups_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
('bx_groups_events_view', '1140px', 'Group Event''s rate block', '_bx_groups_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
('bx_groups_events_view', '1140px', 'Group Event''s location block', '_bx_groups_block_location', '2', '3', 'Location', '', '1', '28.1', 'non,memb', '0'),  
('bx_groups_events_view', '1140px', 'Group Event''s description block', '_bx_groups_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_events_view', '1140px', 'Group Event''s photos block', '_bx_groups_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
('bx_groups_events_view', '1140px', 'Group Event''s video block', '_bx_groups_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_events_view', '1140px', 'Group Event''s files block', '_bx_groups_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_events_view', '1140px', 'Group Event''s sounds block', '_bx_groups_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
('bx_groups_events_view', '1140px', 'Group Event''s comments block', '_bx_groups_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');    
 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=groups/event/add/|modules/?r=groups/event/edit/|modules/?r=groups/event/view/|modules/?r=groups/event/browse/') WHERE `Parent`=0 AND `Name`='Groups' AND `Type`='system' AND `Caption`='_bx_groups_menu_root'; 
 
 
CREATE TABLE IF NOT EXISTS `bx_groups_rss` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `bx_groups_youtube` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=groups/embed/') WHERE `Parent` = 0 AND `Name`= 'Groups' AND `Type` = 'system';

 
 
 INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_map_event_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''groups'', ''event_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
  
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_map_venue_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''groups'', ''venue_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
 
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_groups_map_sponsor_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''groups'', ''sponsor_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);


INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
 
('bx_groups_events_view', '1140px', 'Group Event''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('bx_groups_events_view', '1140px', 'Group Event''s map view', '_bx_groups_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''groups_event'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),

('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('bx_groups_sponsors_view', '1140px', 'Group Sponsor''s map view', '_bx_groups_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''groups_sponsor'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
 
('bx_groups_venues_view', '1140px', 'Group Venue''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('bx_groups_venues_view', '1140px', 'Group Venue''s map view', '_bx_groups_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''groups_venue'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),

('bx_groups_blogs_view', '1140px', 'Group Blog''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 

('bx_groups_news_view', '1140px', 'Group News''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0);
  
CREATE TABLE IF NOT EXISTS `[db_prefix]memberships` (
  `id` int(11) NOT NULL auto_increment,
  `level_id` int(11) NOT NULL default '0', 
  `threshold` int(11) NOT NULL default '0',
  `days` int(11) NOT NULL default '0',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
