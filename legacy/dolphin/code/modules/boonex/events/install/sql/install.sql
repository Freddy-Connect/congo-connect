ALTER TABLE `sys_menu_top` CHANGE `Link` `Link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

-- create tables 
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `Title` varchar(255) NOT NULL default '',
  `EntryUri` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Status` enum('approved', 'pending', 'expired', 'past') NOT NULL default 'approved',
  `Country` varchar(2) NOT NULL default 'US',
  `City` varchar(50) NOT NULL default '',
  `Zip` varchar(20) NOT NULL default '',
  `Place` varchar(100) NOT NULL default '',
  `PrimPhoto` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL default '', 
  `Date` int(11) NOT NULL,
  `EventStart` int(11) NOT NULL default '0',
  `EventEnd` int(11) NOT NULL default '0',
  `ResponsibleID` int(10) unsigned NOT NULL default '0',
  `EventMembershipFilter` varchar(100) NOT NULL default '',  
  `Tags` varchar(255) NOT NULL default '',
  `Categories` text NOT NULL,
  `Views` int(11) NOT NULL,
  `Rate` float NOT NULL,
  `RateCount` int(11) NOT NULL,
  `CommentsCount` int(11) NOT NULL,
  `FansCount` int(11) NOT NULL,
  `Featured` tinyint(4) NOT NULL,
  `allow_view_event_to` VARCHAR( 16 ) NOT NULL,
  `allow_view_participants_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,
  `allow_join_to` int(11) NOT NULL,
  `allow_post_in_forum_to` varchar(16) NOT NULL,
  `allow_view_forum_to` varchar(16) NOT NULL,
  `JoinConfirmation` tinyint(4) NOT NULL default '0',
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_videos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_sounds_to` varchar(16) NOT NULL default 'a',
  `allow_upload_files_to` varchar(16) NOT NULL default 'a', 
  `currency` varchar(255) NOT NULL default '', 
  `featured_expiry_date`  INT NOT NULL,
  `featured_date` INT NOT NULL, 
  `pre_expire_notify` int(11) NOT NULL, 
  `post_expire_notify` int(11) NOT NULL,   
  `expiry_date` int(11) NOT NULL default '0',
  `invoice_no` varchar(100) COLLATE utf8_general_ci NOT NULL, 
  `OrganizerName` varchar(255) NOT NULL default '',
  `OrganizerPhone` varchar(255) NOT NULL default '',
  `OrganizerEmail` varchar(255) NOT NULL default '',
  `OrganizerWebsite` varchar(255) NOT NULL default '',
  `Recurring`  ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
  `RecurringNum` int(11) unsigned NOT NULL default '0',
  `RecurringPeriod` varchar(255) NOT NULL default '',
  `Recurrence` INT( 11 ) NOT NULL, 
  `State` varchar(10) NOT NULL default '',
  `Street` varchar(200) NOT NULL default '',
  `Reminder` INT( 11 ) NOT NULL ,
  `ReminderDays` INT( 11 ) NOT NULL,
  `ReminderSent` INT( 11 ) NOT NULL,
  `EventMembershipViewFilter` varchar(100) NOT NULL default '',
  `VideoEmbed` TEXT NOT NULL, 
  `ParticipantsInfo`  TEXT NOT NULL,
  `allow_join_after_start` varchar(10) NOT NULL default '',  
  `Parent` int(11) NOT NULL, 
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EntryUri` (`EntryUri`),
  KEY `ResponsibleID` (`ResponsibleID`),
  KEY `EventStart` (`EventStart`),
  KEY `Date` (`Date`),
  FULLTEXT KEY `Title` (`Title`,`Description`,`City`,`Place`,`Tags`,`Categories`),
  FULLTEXT KEY `Tags` (`Tags`),
  FULLTEXT KEY `Categories` (`Categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]participants` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id_entry`,`id_profile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]admins` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]rating` (
  `gal_id` int(10) unsigned NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]rating_track` (
  `gal_id` int(10) unsigned NOT NULL default '0',
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
(1, 'Events', 'Events', '', 64);

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
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_view', 'Event View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_main', 'Main Events Page', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_my', 'My Events Page', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_packages', 'Listing Packages', @iMaxOrder+5);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_local', 'Local Events Page', @iMaxOrder+6); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_local_state', 'Local Events State Page', @iMaxOrder+7); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_sponsors_view', 'Event Sponsor View', @iMaxOrder+8); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_sponsors_browse', 'Event Sponsor Browse', @iMaxOrder+9);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_news_view', 'Event News View', @iMaxOrder+10); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_news_browse', 'Event News Browse', @iMaxOrder+11);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_venues_view', 'Event Venue View', @iMaxOrder+12);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('bx_events_venues_browse', 'Event Venue Browse', @iMaxOrder+13);

 

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('bx_events_view', '1140px', 'Event''s logo block', '_bx_events_block_logo', '2', '0', 'Logo', '', '1', '28.1', 'non,memb', '0'), 
    ('bx_events_view', '1140px', 'Event''s info block', '_bx_events_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_view', '1140px', 'Event''s actions block', '_bx_events_block_actions', '2', '2', 'Actions', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_view', '1140px', 'Event''s admins block', '_bx_events_block_admins', '2', '3', 'Admins', '', '1', '28.1', 'non,memb', '0'), 
    ('bx_events_view', '1140px', 'Event''s rate block', '_bx_events_block_rate', '2', '4', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_view', '1140px', 'Event''s social sharing block', '_sys_block_title_social_sharing', '2', '5', 'SocialSharing', '', 1, 28.1, 'non,memb', 0),
    ('bx_events_view', '1140px', 'Event''s files block', '_bx_events_block_files', '2', '6', 'Files', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s participants block', '_bx_events_block_participants', '2', '7', 'Participants', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s unconfirmed participants block', '_bx_events_block_participants_unconfirmed', '2', '8', 'ParticipantsUnconfirmed', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s Location', '_Location', '2', '9', 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''events'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
    ('bx_events_view', '1140px', 'Event''s organizer block', '_bx_events_block_organizer', '2', '10', 'Organizer', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s Participants Info block', '_bx_events_block_participants_info', '2', '11', 'ParticipantsInfo', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s Chat', '_Chat', '2', '12', 'PHP', 'return BxDolService::call(''shoutbox'', ''get_shoutbox'', array(''bx_events'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 11, 28.1, 'non,memb', 0),
    ('bx_events_view', '1140px', 'Event''s Owner RSS block', '_bx_events_block_owner_rss', '2', '13', 'EventRSS', '', '1', '28.1', 'non,memb', '0'),

    ('bx_events_view', '1140px', 'Event''s description block', '_bx_events_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s photos block', '_bx_events_block_photos', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s video embed block', '_bx_events_block_video_embed', '1', '2', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
    ('bx_events_view', '1140px', 'Event''s videos block', '_bx_events_block_videos', '1', '3', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s sounds block', '_bx_events_block_sounds', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s recurring block', '_bx_events_block_recurring', '1', '5', 'Recurring', '', '1', '71.9', 'non,memb', '0'), 
    ('bx_events_view', '1140px', 'Event''s local block', '_bx_events_block_local', '1', '6', 'Local', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_view', '1140px', 'Event''s other block', '_bx_events_block_other', '1', '7', 'Other', '', '1', '71.9', 'non,memb', '0'),	
    ('bx_events_view', '1140px', 'Event''s comments block', '_bx_events_block_comments', '1', '9', 'Comments', '', '1', '71.9', 'non,memb', '0'),    
    ('bx_events_view', '1140px', 'Event''s forum block', '_bx_events_block_forum_feed', '1', '10', 'ForumFeed', '', '1', '71.9', 'non,memb', '0'), 
    ('bx_events_view', '1140px', 'Event''s sponsors block', '_bx_events_block_sponsors', '0', '0', 'Sponsors', '', '1', '71.9', 'non,memb', '0'), 
    ('bx_events_view', '1140px', 'Event''s news block', '_bx_events_block_news', '0', '0', 'News', '', '1', '71.9', 'non,memb', '0'), 
    ('bx_events_view', '1140px', 'Event''s venue block', '_bx_events_block_venues', '0', '0', 'Venues', '', '1', '71.9', 'non,memb', '0'), 
	
	('bx_events_view', '1140px', 'Event''s titre block', '_bx_events_block_titre', '0', '0', 'TitreEvenement', '', '1', '71.9', 'non,memb', '0'), 
	
	('bx_events_view', '1140px', 'Event''s Manage event', '_bx_events_block_manage_event', '0', '0', 'ManageMyEvent', '', '1', '71.9', 'non,memb', '0'), 
	
	('bx_events_view', '1140px', 'Event''s Print Excel', '_bx_events_block_Print_Excel', '0', '0', 'ManagePrintExcel', '', '1', '71.9', 'non,memb', '0'),
	('bx_events_view', '1140px', 'Event''s Actions', '_bx_events_block_Action_Event', '0', '0', 'ManagePageUser', '', '1', '71.9', 'non,memb', '0'),
	

    ('bx_events_local', '1140px', 'Local Events', '_bx_events_block_browse_country', '1', '0', 'Region', '', '1', '100', 'non,memb', '0'),  
 
    ('bx_events_local_state', '1140px', 'Local State Events', '_bx_events_block_browse_state_events', '1', '0', 'StateEvents', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_local_state', '1140px', 'Local States', '_bx_events_block_browse_state', '2', '0', 'States', '', '1', '28.1', 'non,memb', '0'),
	
    ('bx_events_main', '1140px', 'Create Event', '_bx_events_block_create', '2', '0', 'Create', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Search Event', '_bx_events_block_search', '2', '1', 'Search', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Event Categories', '_bx_events_block_common_categories', '2', '2', 'Categories', '', '1', '28.1', 'non,memb', '0'),	 
    ('bx_events_main', '1140px', 'Calendar', '_bx_events_block_calendar', '2', '3', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Event Tags', '_tags_plural ', '2', '4', 'Tags', '', '1', '28.1', 'non,memb', '0'),
     
    ('bx_events_main', '1140px', 'Upcoming Events Photo', '_bx_events_block_upcoming_photo', '0', '0', 'UpcomingPhoto', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Featured Events', '_bx_events_block_featured', '1', '0', 'Featured', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Recently Added Events', '_bx_events_block_recently_added_list', '1', '1', 'RecentlyAddedList', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Map', '_Map', '1', '2', 'PHP', 'return BxDolService::call(''wmap'', ''homepage_part_block'', array (''events''));', 1, 71.9, 'non,memb', 0),
    ('bx_events_main', '1140px', 'Upcoming Events List', '_bx_events_block_upcoming_list', '1', '3', 'UpcomingList', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Events Forum Posts', '_bx_events_block_forum', '1', '4', 'Forum', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Event Comments', '_bx_events_block_latest_comments', '1', '6', 'Comments', '', '1', '71.9', 'non,memb', '0'),
 
    ('bx_events_main', '1140px', 'Top Events', '_bx_events_block_top_list', '0', '0', 'TopList', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_main', '1140px', 'Popular Events', '_bx_events_block_popular_list', '0', '0', 'PopularList', '', '1', '71.9', 'non,memb', '0'), 
    ('bx_events_main', '1140px', 'Events I Created', '_bx_events_block_created', '0', '0', 'Created', '', '1', '71.9', 'non,memb', '0'), 
    ('bx_events_main', '1140px', 'Events I Joined', '_bx_events_block_joined', '0', '0', 'Joined', '', '1', '71.9', 'non,memb', '0'),  

    ('bx_events_my', '1140px', 'Administration', '_bx_events_block_administration', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('bx_events_my', '1140px', 'User''s events', '_bx_events_block_user_events', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('bx_events_my', '1140px', 'User''s joined events', '_bx_events_block_joined_events', '1', '2', 'Joined', '', '0', '100', 'non,memb', '0'), 

    ('bx_events_packages', '1140px', 'Events Packages', '_bx_events_block_packages', '1', '0', 'Packages', '', '1', '100', 'non,memb', '0'),  
 
    ('index', '1140px', 'Events Calendar', '_bx_events_block_calendar', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''events'', ''calendar'');', 1, 71.9, 'non,memb', 0),   
    ('index', '1140px', 'Events', '_bx_events_block_home', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''events'', ''homepage_block'');', 1, 71.9, 'non,memb', 0),
    ('profile', '1140px', 'User Events', '_bx_events_block_my_events', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''events'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0),
    ('profile', '1140px', 'Joined Events', '_bx_events_block_joined_events', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''events'', ''profile_block_joined'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0),
    ('member', '1140px', 'Member Events', '_bx_events_block_account', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''events'', ''accountpage_block'');', 1, 71.9, 'non,memb', 0),  
    ('member', '1140px', 'Joined Events', '_bx_events_block_joined_events', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''events'', ''profile_block_joined'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0);

-- permalink
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=events/', 'm/events/', 'bx_events_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Events', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('category_auto_app_bx_events', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('bx_events_permalinks', 'on', 26, 'Enable friendly permalinks in events', 'checkbox', '', '', '0', ''),
('bx_events_autoapproval', 'on', @iCategId, 'Activate all events after creation automatically', 'checkbox', '', '', '0', ''),
('bx_events_max_email_invitations', '10', @iCategId, 'Max number of email invitation to send per one invite', 'digit', '', '', '0', ''),
('bx_events_perpage_main_upcoming', '10', @iCategId, 'Number of upcoming events to show on main page', 'digit', '', '', '0', ''),
('bx_events_perpage_main_recent', '4', @iCategId, 'Number of recently added events to show on main page', 'digit', '', '', '0', ''),
('bx_events_perpage_main_past', '6', @iCategId, 'Number of past events to show on main page', 'digit', '', '', '0', ''),
('bx_events_perpage_participants', '9', @iCategId, 'Number of participants to show on event view page', 'digit', '', '', '0', ''),
('bx_events_perpage_browse_participants', '30', @iCategId, 'Number of items to show on browse participants page', 'digit', '', '', '0', ''),
('bx_events_perpage_browse', '14', @iCategId, 'Number of events to show on browse pages', 'digit', '', '', '0', ''),
('bx_events_perpage_homepage', '5', @iCategId, 'Number of events to show on homepage', 'digit', '', '', '0', ''),
('bx_events_homepage_default_tab', 'upcoming', @iCategId, 'Default block tab on homepage', 'select', '', '', '0', 'upcoming,featured,recent,top,popular'),
('bx_events_perpage_profile', '5', @iCategId, 'Number of events to show on profile page', 'digit', '', '', '0', ''),
('bx_events_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', ''),
 
('bx_events_join_after_start', '', @iCategId, 'allow members to join an event after the event starts', 'checkbox', '', '', '0', ''), 
('bx_events_free_expired', '0', @iCategId, 'number of days before free events listings expires (0-never expires)', 'digit', '', '', '0', ''), 
('bx_events_paypal_email', '', @iCategId, 'Paypal Email', 'digit', '', '', 0, ''),
('bx_events_paid_active', '', @iCategId, 'Activate Paid Events listings',  'checkbox', '', '', 0, ''), 
('bx_events_currency_code', 'USD', @iCategId, 'Currency code for checkout system (eg. USD,EURO,GBP)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('bx_events_paypal_item_desc', 'Events Package purchase', @iCategId, 'Item decription displayed on PayPal Package Purchase', 'digit', '', '', 0, ''), 
('bx_events_invoice_valid_days', '100', @iCategId, 'Number of Days before pending Invoices expire<br>blank or zero means no expiration', 'digit', '', '', 0, ''),  
('bx_events_default_country', 'US', @iCategId, 'default country for location', 'digit', '', '', 0, ''),
('bx_events_max_preview', '300', @iCategId, 'Length of event description snippet to show in blocks', 'digit', '', '', '0', ''),
('bx_events_forum_max_preview', '200', @iCategId, 'length of forum post snippet to show on main page', 'digit', '', '', '0', ''),
('bx_events_comments_max_preview', '200', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
('bx_events_perpage_main_popular', '4', @iCategId, 'Number of popular events to show on main page', 'digit', '', '', '0', ''),
('bx_events_perpage_main_featured', '4', @iCategId, 'Number of featured events to show on main page', 'digit', '', '', '0', ''), 
('bx_events_perpage_main_top', '4', @iCategId, 'Number of top rated events to show on main page', 'digit', '', '', '0', ''),
('bx_events_perpage_main_forum', '5', @iCategId, 'Number of forum posts to show on main page', 'digit', '', '', '0', ''),
('bx_events_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''),
('bx_events_perpage_accountpage', '5', @iCategId, 'Number of events to show on account page', 'digit', '', '', '0', ''), 
 
('bx_events_icon_width', '240', @iCategId, 'Width of logo icon', 'digit', '', '', '0', ''),
('bx_events_icon_height', '240', @iCategId, 'Height of logo icon', 'digit', '', '', '0', ''),    

('bx_events_state_field', '', @iCategId, 'name of profile state field (if you added one)', 'digit', '', '', '0', ''), 
('bx_events_perpage_view_subitems', '6', @iCategId, 'Number of items (Sponsors,Venues etc) to show on event view page', 'digit', '', '', '0', ''),
('bx_events_perpage_browse_subitems', '30', @iCategId, 'Number of items (Sponsors,Venues etc) to show on the sub section browse page', 'digit', '', '', '0', ''); 

 
-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'bx_events', '_bx_events', 'BxEventsSearchResult', 'modules/boonex/events/classes/BxEventsSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_events', 'bx_events_rating', 'bx_events_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_events_main', 'Rate', 'RateCount', 'ID', 'BxEventsVoting', 'modules/boonex/events/classes/BxEventsVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_events', 'bx_events_cmts', 'bx_events_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_events_main', 'ID', 'CommentsCount', 'BxEventsCmts', 'modules/boonex/events/classes/BxEventsCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'bx_events', 'bx_events_views_track', 86400, 'bx_events_main', 'ID', 'Views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'bx_events', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `ID` = {iID} AND `Status` = ''approved''', 'bx_events_permalinks', 'm/events/browse/tag/{tag}', 'modules/?r=events/browse/tag/{tag}', '_bx_events');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'bx_events', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `ID` = {iID} AND `Status` = ''approved''', 'bx_events_permalinks', 'm/events/browse/category/{tag}', 'modules/?r=events/browse/category/{tag}', '_bx_events');

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Events', '0', 'bx_photos', '0', 'active'),
('Party', '0', 'bx_events', '0', 'active'),
('Expedition', '0', 'bx_events', '0', 'active'),
('Presentation', '0', 'bx_events', '0', 'active'),
('Last Friday', '0', 'bx_events', '0', 'active'),
('Birthday', '0', 'bx_events', '0', 'active'),
('Exhibition', '0', 'bx_events', '0', 'active'),
('Bushwalking', '0', 'bx_events', '0', 'active');

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'bx_events'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'', true); return false;', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'bx_events'),
    ('{TitleJoin}', '{IconJoin}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '2', 'bx_events'),
    ('{TitleInvite}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '3', 'bx_events'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '4', 'bx_events'),
    ('{TitleBroadcast}', 'envelope', '{BaseUri}broadcast/{ID}', '', '', '5', 'bx_events'),
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', 6, 'bx_events'),
 
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '8', 'bx_events'),
    ('{TitleEmbed}', 'film', '{BaseUri}embed/{URI}', '', '', '9', 'bx_events'), 
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '10', 'bx_events'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '11', 'bx_events'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '12', 'bx_events'),    

    ('{TitleRelist}', 'refresh', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''relist/{ID}'';', '13', 'bx_events'), 
    ('{TitleExtend}', 'wrench', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''extend/{ID}'';', '14', 'bx_events'),
    ('{TitlePremium}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''premium/{ID}'';', '15', 'bx_events'),  
 
    ('{TitleSponsorAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sponsor/add/{ID}'';', '16', 'bx_events'), 
    ('{TitleVenueAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''venue/add/{ID}'';', '17', 'bx_events'), 
    ('{TitleNewsAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/add/{ID}'';', '18', 'bx_events'), 
    
    ('{TitleManageAdmins}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_admins_popup/{ID}'');', '', '19', 'bx_events'),
    ('{TitleAdminAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''add_admin/{ID}'';', '20', 'bx_events'), 

    ('{TitleManageFans}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '21', 'bx_events'),
    ('{evalResult}', 'plus-circle', '', 'showPopupAnyHtml (site_url+''m/events/add_participant/{ID}'')', '$oEvent = BxDolModule::getInstance(''BxEventsModule''); return ($oEvent->isAllowedAddParticipant({ID})) ? _t(''_bx_events_action_title_add_participant'') : '''';', 22, 'bx_events'), 
  
    ('{TitlePurchaseFeatured}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''purchase_featured/{ID}'';', '23', 'bx_events'),

    ('{evalResult}', 'shopping-cart', '', 'showPopupAnyHtml (site_url+''m/events/pay_feature/{ID}'')', '$oMain = BxDolModule::getInstance(''BxEventsModule'');if ( $oMain->isAllowedPurchaseFeatured({ID}) ) return _t(''_bx_events_action_title_purchase_featured'');', 23, 'bx_events'),


    ('{TitleExcel}', 'table', '{BaseUri}excel/{ID}', '', '', 24, 'bx_events'),     
    ('{TitlePrint}', 'print', '{BaseUri}print/{ID}', '', '', 25, 'bx_events'),

    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', 26, 'bx_events'),

    ('{TitleActivate}', 'check-circle-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''activate/{ID}'';', '30', 'bx_events'),
    ('{repostCpt}', 'repeat', '', '{repostScript}', '', 31, 'bx_events'),


    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''venue/edit/{ID}'';', '0', 'bx_events_venue'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'', true);return false;', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''venue/delete/{ID}'';', '1', 'bx_events_venue'),
 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/edit/{ID}'';', '0', 'bx_events_news'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'', true);return false;', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/delete/{ID}'';', '1', 'bx_events_news'),
 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sponsor/edit/{ID}'';', '0', 'bx_events_sponsor'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'', true);return false;', '$oConfig = $GLOBALS[''oBxEventsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sponsor/delete/{ID}'';', '1', 'bx_events_sponsor'),
  
    ('{evalResult}', 'plus', '{BaseUri}browse/my&bx_events_filter=add_event', '', 'return ($GLOBALS[''logged''][''member''] && BxDolModule::getInstance(''BxEventsModule'')->isAllowedAdd()) || $GLOBALS[''logged''][''admin''] ? _t(''_bx_events_action_create_event'') : '''';', 1, 'bx_events_title'),
    ('{evalResult}', 'calendar', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_bx_events_action_my_events'') : '''';', '2', 'bx_events_title');
    
-- top menu
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Events', '_bx_events_menu_root', 'modules/?r=events/view/|modules/?r=events/broadcast/|modules/?r=events/invite/|modules/?r=events/edit/|modules/?r=events/upload_photos/|modules/?r=events/upload_videos/|modules/?r=events/upload_sounds/|modules/?r=events/upload_files/|modules/?r=events/add_admin/|modules/?r=events/embed/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'calendar', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Event View', '_bx_events_menu_view', 'modules/?r=events/view/{bx_events_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Event View Forum', '_bx_events_menu_view_forum', 'forum/events/forum/{bx_events_view_uri}-0.htm|forum/events/', 1, 'non,memb', '', '', '$oModuleDb = new BxDolModuleDb(); return $oModuleDb->getModuleByUri(''forum'') ? true : false;', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Event View Comments', '_bx_events_menu_view_comments', 'modules/?r=events/comments/{bx_events_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Event View Participants', '_bx_events_menu_view_participants', 'modules/?r=events/browse_participants/{bx_events_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Event View Venues', '_bx_events_menu_view_venues', 'modules/?r=events/venue/browse/{bx_events_view_uri}', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 
(NULL, @iCatRoot, 'Event View Sponsors', '_bx_events_menu_view_sponsors', 'modules/?r=events/sponsor/browse/{bx_events_view_uri}', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 
(NULL, @iCatRoot, 'Event View News', '_bx_events_menu_view_news', 'modules/?r=events/news/browse/{bx_events_view_uri}', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '') 
;


SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 0, 'Events', '_bx_events_menu_root', 'modules/?r=events/home/|modules/?r=events/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'calendar', 'calendar', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Events Main Page', '_bx_events_menu_main', 'modules/?r=events/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Upcoming Events', '_bx_events_menu_upcoming_events', 'modules/?r=events/browse/upcoming', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Past Events', '_bx_events_menu_past_events', 'modules/?r=events/browse/past', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Recently Added Events', '_bx_events_menu_recently_added', 'modules/?r=events/browse/recent', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Top Rated Events', '_bx_events_menu_top_rated', 'modules/?r=events/browse/top', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Popular Events', '_bx_events_menu_popular', 'modules/?r=events/browse/popular', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Featured Events', '_bx_events_menu_featured', 'modules/?r=events/browse/featured', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Events in my Area', '_bx_events_menu_local', 'modules/?r=events/browse/ilocal', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Drilldown Events', '_bx_events_menu_drilldown', 'modules/?r=events/local', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 
(NULL, @iCatRoot, 'Events Packages', '_bx_events_menu_packages', 'modules/?r=events/packages', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Events Tags', '_bx_events_menu_tags', 'modules/?r=events/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'bx_events'),
(NULL, @iCatRoot, 'Events Categories', '_bx_events_menu_categories', 'modules/?r=events/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'bx_events'),
(NULL, @iCatRoot, 'Calendar', '_bx_events_menu_calendar', 'modules/?r=events/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
(NULL, @iCatRoot, 'Search', '_bx_events_menu_search', 'modules/?r=events/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 9, 'Events', '_bx_events_menu_my_events_profile', 'modules/?r=events/browse/user/{profileUsername}|modules/?r=events/browse/joined/{profileUsername}', ifnull(@iCatProfileOrder,1), 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

SET @iCatProfileOrder := (SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, 4, 'Events', '_bx_events_menu_my_events_profile', 'modules/?r=events/browse/my', ifnull(@iCatProfileOrder,1), 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'bx_events', `Eval` = 'return BxDolService::call(''events'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'bx_events', '_bx_events', '{siteUrl}modules/?r=events/administration/', 'Events module by BoonEx', 'calendar', @iMax+1);

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('bx_events_invitation', 'Invitation to event: <EventName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to his event:</p> <pre><InvitationText></pre> <p> <b>Event Information:</b><br /> Name: <EventName><br /> Location: <EventLocation><br /> Date of beginning: <EventStart><br /> <a href="<EventUrl>">More details</a><br /><br /> <a href="<AcceptUrl>">Accept Invitation</a>\r\n</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Events invitation template', '0'), 

('bx_events_broadcast', '<BroadcastTitle>', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p><a href="<EntryUrl>"><EntryTitle></a> event admin message:</p> <hr><BroadcastMessage><hr> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Event Broadcast', 0),

('bx_events_sbs', 'Subscription: Event Details Changed', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p><a href="<ViewLink>"><EntryTitle></a> event details changed: <br /> <ActionName> </p> \r\n<hr>\r\n<p>Cancel this subscription: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Event Subscription', 0),

('bx_events_join_request', 'New Request To Join Your Event', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p>New request to join your event: <a href="<EntryUrl>"><EntryTitle></a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'New join request to an event', 0),

('bx_events_join_reject', 'Your Request To Join Event Was Rejected', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> \r\n\r\n<p>Your request to join <a href="<EntryUrl>"><EntryTitle></a> event was rejected by event admin.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Request To Join Event Was Rejected', 0),

('bx_events_join_confirm', 'Your Request To Join Event Was Approved', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> \r\n\r\n<p>Congratulations! Your request to join <a href="<EntryUrl>"><EntryTitle></a> event was approved by the event admin.</p>\r\n \r\n<bx_include_auto:_email_footer.html />', 'Request To Join Event Approved', 0),

('bx_events_fan_remove', 'You Were Removed From Event Participants', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>You were removed from participants of <a href="<EntryUrl>"><EntryTitle></a> event by the event admin.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Removed From Event Participants', 0),

('bx_events_fan_become_admin', 'You Are The Event Admin Now', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> \r\n<p>You are an admin of <a href="<EntryUrl>"><EntryTitle></a> event now.\r\n</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Event Admin Status Granted', 0),

('bx_events_admin_become_fan', 'You Are No Longer The Event Admin', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Your admin status was revoked from <a href="<EntryUrl>"><EntryTitle></a> event by the event creator.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Event admin status revoked.', 0);


-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'evs', 'bx_events', 'modules/?r=events/browse/recent', 'SELECT COUNT(`ID`) FROM `[db_prefix]main` WHERE `Status`=''approved''', 'modules/?r=events/administration', 'SELECT COUNT(`ID`) FROM `[db_prefix]main` WHERE `Status`=''pending''', 'calendar', @iStatSiteOrder);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('bx_events', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `ResponsibleID` = ''__member_id__'' AND `Status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('bx_eventsp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `ResponsibleID` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_bx_events', '__bx_events__ (<a href="modules/?r=events/browse/my&bx_events_filter=add_event">__l_add__</a>)');

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events view', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events edit any event', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events delete any event', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events approve', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events broadcast message', NULL);

-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_events_profile_delete', '', '', 'BxDolService::call(''events'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_events_media_delete', '', '', 'BxDolService::call(''events'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_events_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''events'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('events', 'view_event', '_bx_events_privacy_view_event', '3'),
('events', 'join', '_bx_events_privacy_join', '3'),
('events', 'comment', '_bx_events_privacy_comment', '3'),
('events', 'rate', '_bx_events_privacy_rate', '3'),
('events', 'view_forum', '_bx_events_privacy_view_forum', '3'), 
('events', 'view_participants', '_bx_events_privacy_view_participants', '3'),
('events', 'post_in_forum', '_bx_events_privacy_post_in_forum', 'p'),
('events', 'upload_photos', '_bx_events_privacy_upload_photos', 'a'),
('events', 'upload_videos', '_bx_events_privacy_upload_videos', 'a'),
('events', 'upload_sounds', '_bx_events_privacy_upload_sounds', 'a'),
('events', 'upload_files', '_bx_events_privacy_upload_files', 'a');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('bx_events', '', '', 'return BxDolService::call(''events'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_events', 'change', 'bx_events_sbs', 'return BxDolService::call(''events'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_events', 'commentPost', 'bx_events_sbs', 'return BxDolService::call(''events'', ''get_subscription_params'', array($arg2, $arg3));'),
('bx_events', 'join', 'bx_events_sbs', 'return BxDolService::call(''events'', ''get_subscription_params'', array($arg2, $arg3));');

-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('bx_events', '_bx_events', '0.8', 'auto', 'BxEventsSiteMaps', 'modules/boonex/events/classes/BxEventsSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('bx_events', '_bx_events', 'bx_events_main', 'Date', '', '', 1, @iMaxOrderCharts);


-- ADDITION OF PREMIUM STUFF
 
CREATE TABLE IF NOT EXISTS `bx_events_youtube` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `bx_events_activity` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `event_id` int(11) NOT NULL,
  `lang_key` varchar(100) collate utf8_general_ci NOT NULL,
  `params` text collate utf8_general_ci NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `type` enum('add','delete','change','commentPost','rate','join','unjoin','featured','unfeatured','makeAdmin','removeAdmin') collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
 
CREATE TABLE IF NOT EXISTS `bx_events_invite` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(11) NOT NULL,
  `id_profile` int(11) NOT NULL,
  `code` varchar(100) collate utf8_general_ci NOT NULL, 
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
   PRIMARY KEY  (`id`) 
 ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
   

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events photos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events sounds add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events videos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events files add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events purchase featured', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events extend', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events relist', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'events purchase', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
  
INSERT INTO `sys_acl_actions` VALUES (NULL, 'events broadcast message', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
 
CREATE TABLE IF NOT EXISTS `bx_events_packages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_general_ci NOT NULL,
  `price` float NOT NULL,
  `days` int(11) NOT NULL,
  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `videos` int(11) NOT NULL default '0', 
  `photos` int(11) NOT NULL default '0', 
  `sounds` int(11) NOT NULL default '0', 
  `files` int(11) NOT NULL default '0', 
  `featured` int(11) NOT NULL default '0', 
  `status` enum('active','pending') collate utf8_general_ci NOT NULL default 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
 
CREATE TABLE IF NOT EXISTS `bx_events_invoices` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `invoice_no` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `days` int(11) unsigned NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  `package_id` int(11) unsigned NOT NULL,
  `invoice_status` enum('pending','paid') NOT NULL default 'pending',
  `invoice_due_date` int(11) NOT NULL,
  `invoice_expiry_date` int(11) NOT NULL,
  `invoice_date` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_orders` (
  `id` int(11) unsigned NOT NULL auto_increment, 
  `invoice_no` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `order_no` varchar(100) COLLATE utf8_general_ci NOT NULL, 
  `buyer_id` int(11) unsigned NOT NULL,
  `payment_method` varchar(100) COLLATE utf8_general_ci NOT NULL, 
  `order_status` ENUM( 'approved', 'pending' ) NOT NULL DEFAULT 'approved',
  `order_date` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `profile_id` (`buyer_id`,`order_no`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `bx_events_featured_orders` (
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
('bx_events_recurr_notify', 'Event Notification at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <RecipientName></b>,</p>\r\n\r\n<p>An event which you attended, <a href="<EventUrl>"><b><EventTitle></b></a>, is scheduled to be held again on <EventStart>.</p>\r\n\r\n<p>You can view more details <a href="<EventUrl>">here</a>:<br></p>\r\n\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'participant notification of recurring event', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_events_remind_notify', 'Event Reminder at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <RecipientName></b>,</p>\r\n\r\n<p>This is a Reminder that an Event in which you are a participant, <a href="<EventUrl>"><b><EventTitle></b></a>, is scheduled to be held on <EventStart>.</p>\r\n\r\n<p>You can view more details <a href="<EventUrl>">here</a>:<br></p>\r\n\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'participant reminder of event start', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_events_expired', 'Your Event listing at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Event listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Expired Event listing notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_events_post_expired', 'Message about your expired Event listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Event listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired <b><Days></b> days ago</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Post-Expired Event listing notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_events_expiring', 'Message about your expiring Event listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Event listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> will expire in <b><Days></b> days<br></p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Expiring Event listing notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_events_featured_expire_notify', 'Your Featured Event Status at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is inform you that your Featured Status for the Event listing, <a href="<ListLink>"><ListTitle></a> at <SiteName> has expired. You may purchase Featured Status again at any time you desire <br></p>\r\n\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Event Status Expire Notification', '0');
   
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_events_featured_admin_notify', 'A member purchased Featured Event Status at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear Administrator</b>,</p>\r\n\r\n<p><a href="<NickLink>"><NickName></a> has just purchased Featured Status for the Event listing, <a href="<ListLink>"><ListTitle></a>, for <Days> days at <SiteName><br></p>\r\n\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Event Purchase Admin Notification', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('bx_events_featured_buyer_notify', 'Your Featured Event Status purchase at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is confirmation of your Featured Status purchase at <SiteName> for Event listing, <a href="<ListLink>"><ListTitle></a>. It will be Featured for <Days> days<br></p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Event Purchase Buyer Notification', '0');
 

 

-- [BEGIN] SPONSORS 
 
CREATE TABLE IF NOT EXISTS `bx_events_sponsor_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `event_id` int(11) NOT NULL,
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
  `allow_view_to` VARCHAR( 16 ) NOT NULL, 
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a', 

  `website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `telephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `fax` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
 
  `country` varchar(2) NOT NULL default 'US',
  `state` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `zip` varchar(16) NOT NULL default '',
  `address1` varchar(255) NOT NULL default '',

  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_sponsor_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_sponsor_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_sponsor_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `bx_events_sponsor_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_sponsor_cmts` (
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
  
CREATE TABLE IF NOT EXISTS `bx_events_sponsor_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('bx_events_sponsors_browse', '1140px', 'Event Sponsor''s browse block', '_bx_events_block_browse_sponsors', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s actions block', '_bx_events_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s info block', '_bx_events_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s rate block', '_bx_events_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s contact block', '_bx_events_block_contact', '2', '3', 'Contact', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s location block', '_bx_events_block_location', '2', '4', 'Location', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s map view', '_bx_events_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''events_sponsor'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s description block', '_bx_events_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s photos block', '_bx_events_block_photos', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_sponsors_view', '1140px', 'Event Sponsor''s comments block', '_bx_events_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');    
  
 
CREATE TABLE IF NOT EXISTS `bx_events_news_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `event_id` int(11) NOT NULL,
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
  `allow_view_to` VARCHAR( 16 ) NOT NULL, 
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a', 
  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_news_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_news_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_news_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_news_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `bx_events_news_cmts` (
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
 
CREATE TABLE IF NOT EXISTS `bx_events_news_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('bx_events_news_browse', '1140px', 'Event News''s browse block', '_bx_events_block_browse_news', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
    ('bx_events_news_view', '1140px', 'Event News''s actions block', '_bx_events_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_news_view', '1140px', 'Event News''s info block', '_bx_events_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_news_view', '1140px', 'Event News''s rate block', '_bx_events_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_news_view', '1140px', 'Event News''s description block', '_bx_events_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_news_view', '1140px', 'Event News''s photos block', '_bx_events_block_photos', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_news_view', '1140px', 'Event News''s comments block', '_bx_events_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');    
 
 
 
CREATE TABLE IF NOT EXISTS `bx_events_venue_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `event_id` int(11) NOT NULL,
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
  `allow_view_to` VARCHAR( 16 ) NOT NULL, 
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a', 
  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_venue_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_venue_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_venue_rating` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_venue_rating_track` (
  `gal_id` int(10) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `bx_events_venue_cmts` (
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
 
CREATE TABLE IF NOT EXISTS `bx_events_venue_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('bx_events_venues_browse', '1140px', 'Event Venue''s browse block', '_bx_events_block_browse_venues', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
    ('bx_events_venues_view', '1140px', 'Event Venue''s actions block', '_bx_events_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_venues_view', '1140px', 'Event Venue''s info block', '_bx_events_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('bx_events_venues_view', '1140px', 'Event Venue''s rate block', '_bx_events_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('bx_events_venues_view', '1140px', 'Event Venue''s description block', '_bx_events_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_venues_view', '1140px', 'Event Venue''s photos block', '_bx_events_block_photos', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('bx_events_venues_view', '1140px', 'Event Venue''s comments block', '_bx_events_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');    

CREATE TABLE IF NOT EXISTS `bx_events_rss` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=events/news/add/|modules/?r=events/news/edit/|modules/?r=events/news/view/|modules/?r=events/news/browse/') WHERE `Parent`=0 AND `Name`='Events' AND `Type`='system' AND `Caption`='_bx_events_menu_root'; 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=events/sponsor/add/|modules/?r=events/sponsor/edit/|modules/?r=events/sponsor/view/|modules/?r=events/sponsor/browse/') WHERE `Parent`=0 AND `Name`='Events' AND `Type`='system' AND `Caption`='_bx_events_menu_root';
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=events/venue/add/|modules/?r=events/venue/edit/|modules/?r=events/venue/view/|modules/?r=events/venue/browse/') WHERE `Parent`=0 AND `Name`='Events' AND `Type`='system' AND `Caption`='_bx_events_menu_root'; 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=events/relist/|modules/?r=events/extend/|modules/?r=events/premium/|modules/?r=events/purchase_featured/') WHERE `Parent`=0 AND `Name`='Events' AND `Type`='system' AND `Caption`='_bx_events_menu_root'; 
 
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_events_sponsor', 'bx_events_sponsor_rating', 'bx_events_sponsor_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_events_sponsor_main', 'rate', 'rate_count', 'id', 'BxEventsSponsorVoting', 'modules/boonex/events/classes/BxEventsSponsorVoting.php');
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_events_sponsor', 'bx_events_sponsor_cmts', 'bx_events_sponsor_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_events_sponsor_main', 'id', 'comments_count', 'BxEventsSponsorCmts', 'modules/boonex/events/classes/BxEventsSponsorCmts.php');
  
INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_events_venue', 'bx_events_venue_rating', 'bx_events_venue_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_events_venue_main', 'rate', 'rate_count', 'id', 'BxEventsVenueVoting', 'modules/boonex/events/classes/BxEventsVenueVoting.php');
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_events_venue', 'bx_events_venue_cmts', 'bx_events_venue_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_events_venue_main', 'id', 'comments_count', 'BxEventsVenueCmts', 'modules/boonex/events/classes/BxEventsVenueCmts.php');

INSERT INTO `sys_objects_vote` VALUES (NULL, 'bx_events_news', 'bx_events_news_rating', 'bx_events_news_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'bx_events_news_main', 'rate', 'rate_count', 'id', 'BxEventsNewsVoting', 'modules/boonex/events/classes/BxEventsNewsVoting.php');
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'bx_events_news', 'bx_events_news_cmts', 'bx_events_news_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'bx_events_news_main', 'id', 'comments_count', 'BxEventsNewsCmts', 'modules/boonex/events/classes/BxEventsNewsCmts.php');


INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxEvents', '*/5 * * * *', 'BxEventsCron', 'modules/boonex/events/classes/BxEventsCron.php', '');


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_events_map_sponsor_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''events'', ''sponsor_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);


CREATE TABLE IF NOT EXISTS `[db_prefix]monitored_groups` (
  `event_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`event_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- alert handlers
-- INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'bx_events', 'BxEventsResponse', 'modules/boonex/events/classes/BxEventsResponse.php', '');
-- SET @iHandler := LAST_INSERT_ID();

-- INSERT INTO `sys_alerts` VALUES
-- (NULL, 'bx_groups', 'join', @iHandler),
-- (NULL, 'bx_groups', 'join_confirm', @iHandler);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'events allow embed', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);



