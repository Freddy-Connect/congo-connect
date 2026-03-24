-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]categ` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `uri` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `active` int(11) NOT NULL default '1',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- Dumping data for table `[db_prefix]categ`  
INSERT INTO `[db_prefix]categ` (`id`, `parent`, `name`, `uri`, `icon`, `active`) VALUES
(1, 0, 'Business', 'Business', '', 1),
(2, 0, 'Careers', 'Careers', '', 1),
(3, 0, 'Travel & Leisure', 'Travel-Leisure', '', 1),
(4, 0, 'Education', 'Education', '', 1),
(5, 0, 'Finance', 'Finance', '', 1),
(6, 0, 'Technology', 'Technology', '', 1),
(7, 0, 'Arts & Enterainment', 'Arts-Enterainment', '', 1),
(8, 1, 'Accounting', 'Accounting', '', 1),
(9, 1, 'Affiliate Programs', 'Affiliate-Programs', '', 1),
(10, 1, 'Agriculture', 'Agriculture', '', 1),
(11, 1, 'Architecture and Interior Design', 'Architecture-and-Interior-Design', '', 1),
(12, 1, 'Branding', 'Branding', '', 1),
(13, 1, 'Business Ideas', 'Business-Ideas', '', 1),
(14, 1, 'Business Opporunity', 'Business-Opporunity', '', 1),
(15, 1, 'Consulting', 'Consulting', '', 1),
(16, 1, 'Customer Service', 'Customer-Service', '', 1),
(17, 1, 'Ecommerce', 'Ecommerce', '', 1),
(18, 1, 'Entrepreneurship', 'Entrepreneurship', '', 1),
(19, 1, 'Ethics', 'Ethics', '', 1),
(20, 1, 'Franchising', 'Franchising', '', 1),
(21, 1, 'Fundraising', 'Fundraising', '', 1),
(22, 1, 'Furnishings and Supplies', 'Furnishings-and-Supplies', '', 1),
(23, 1, 'Human Resources', 'Human-Resources', '', 1),
(24, 1, 'Industrial Mechanical', 'Industrial-Mechanical', '', 1),
(25, 1, 'International Business', 'International-Business', '', 1),
(26, 1, 'Leadership', 'Leadership', '', 1),
(27, 1, 'Management', 'Management', '', 1),
(28, 1, 'Marketing', 'Marketing', '', 1),
(29, 1, 'Negotiation', 'Negotiation', '', 1),
(30, 1, 'Networking', 'Networking', '', 1),
(31, 1, 'Non Profit', 'Non-Profit', '', 1),
(32, 1, 'Outsourcing', 'Outsourcing', '', 1),
(33, 1, 'Presentation', 'Presentation', '', 1),
(34, 1, 'Product Reviews', 'Product-Reviews', '', 1),
(35, 1, 'Productivity', 'Productivity', '', 1),
(36, 1, 'Retail', 'Retail', '', 1),
(37, 1, 'Risk Management', 'Risk-Management', '', 1),
(38, 1, 'Sales', 'Sales', '', 1),
(39, 1, 'Sales Management', 'Sales-Management', '', 1),
(40, 1, 'Sales Teleselling', 'Sales-Teleselling', '', 1),
(41, 1, 'Sales Training', 'Sales-Training', '', 1),
(42, 1, 'Security', 'Security', '', 1),
(43, 1, 'Services Reviews', 'Services-Reviews', '', 1),
(44, 1, 'Shipping', 'Shipping', '', 1),
(45, 1, 'Small Business', 'Small-Business', '', 1),
(46, 1, 'Strategic Planning', 'Strategic-Planning', '', 1),
(47, 1, 'Team Building', 'Team-Building', '', 1),
(48, 1, 'Venture Capital', 'Venture-Capital', '', 1),
(49, 1, 'Workplace Communication', 'Workplace-Communication', '', 1),
(50, 1, 'Workplace Safety', 'Workplace-Safety', '', 1),
(51, 2, 'Career Management', 'Career-Management', '', 1),
(52, 2, 'Interviews', 'Interviews', '', 1),
(53, 2, 'Recruitment', 'Recruitment', '', 1),
(54, 2, 'Resumes', 'Resumes', '', 1),
(55, 3, 'Airline Travel', 'Airline-Travel', '', 1),
(56, 3, 'Aviation Airplanes', 'Aviation-Airplanes', '', 1),
(57, 3, 'Bed Breakfast Inns', 'Bed-Breakfast-Inns', '', 1),
(58, 3, 'Budget Travel', 'Budget-Travel', '', 1),
(59, 3, 'Camping', 'Camping', '', 1),
(60, 3, 'Car Rentals', 'Car-Rentals', '', 1),
(61, 3, 'Charter Jets', 'Charter-Jets', '', 1),
(62, 3, 'City Guides and Information', 'City-Guides-and-Information', '', 1),
(63, 3, 'Cruise Ship Reviews', 'Cruise-Ship-Reviews', '', 1),
(64, 3, 'Cruising', 'Cruising', '', 1),
(65, 3, 'Destination Tips', 'Destination-Tips', '', 1),
(66, 3, 'Hotels and Lodging', 'Hotels-and-Lodging', '', 1),
(67, 3, 'Outdoors', 'Outdoors', '', 1),
(68, 3, 'Timeshare', 'Timeshare', '', 1),
(69, 3, 'Travel Reviews', 'Travel-Reviews', '', 1),
(70, 3, 'Travel Tips', 'Travel-Tips', '', 1),
(71, 3, 'Vacation Rentals', 'Vacation-Rentals', '', 1),
(72, 4, 'Astronomy', 'Astronomy', '', 1),
(73, 4, 'Biology', 'Biology', '', 1),
(74, 4, 'Childhood Education', 'Childhood-Education', '', 1),
(75, 4, 'College University', 'College-University', '', 1),
(76, 4, 'Continuing Education', 'Continuing-Education', '', 1),
(77, 4, 'Financial Aid', 'Financial-Aid', '', 1),
(78, 4, 'Homeschooling', 'Homeschooling', '', 1),
(79, 4, 'International Studies', 'International-Studies', '', 1),
(80, 4, 'Languages', 'Languages', '', 1),
(81, 4, 'Mathematics', 'Mathematics', '', 1),
(82, 4, 'Online Education', 'Online-Education', '', 1),
(83, 4, 'Philosophy', 'Philosophy', '', 1),
(84, 4, 'Science', 'Science', '', 1),
(85, 4, 'Special Education', 'Special-Education', '', 1),
(86, 4, 'Standardized Tests', 'Standardized-Tests', '', 1),
(87, 4, 'Study Techniques', 'Study-Techniques', '', 1),
(88, 4, 'Teaching', 'Teaching', '', 1),
(89, 4, 'Tutoring', 'Tutoring', '', 1),
(90, 4, 'Vocational Trade Schools', 'Vocational-Trade-Schools', '', 1),
(91, 5, 'Accounting', 'Accounting', '', 1),
(92, 5, 'Banking', 'Banking', '', 1),
(93, 5, 'Bankruptcy', 'Bankruptcy', '', 1),
(94, 5, 'Budgeting', 'Budgeting', '', 1),
(95, 5, 'Credit', 'Credit', '', 1),
(96, 5, 'Credit Counseling', 'Credit-Counseling', '', 1),
(97, 5, 'Credit Tips', 'Credit-Tips', '', 1),
(98, 5, 'Debt Consolidation', 'Debt-Consolidation', '', 1),
(99, 5, 'Debt Management', 'Debt-Management', '', 1),
(100, 5, 'Debt Relief', 'Debt-Relief', '', 1),
(101, 5, 'Estate Plan Trusts', 'Estate-Plan-Trusts', '', 1),
(102, 5, 'Insurance', 'Insurance', '', 1),
(103, 5, 'Investing', 'Investing', '', 1),
(104, 5, 'Loans', 'Loans', '', 1),
(105, 5, 'Mortgage', 'Mortgage', '', 1),
(106, 5, 'Personal Finance', 'Personal-Finance', '', 1),
(107, 5, 'Taxes', 'Taxes', '', 1),
(108, 5, 'Wealth Building', 'Wealth-Building', '', 1),
(109, 6, 'Biotechnology', 'Biotechnology', '', 1),
(110, 6, 'Cable and Satellite TV', 'Cable-and-Satellite-TV', '', 1),
(111, 6, 'Cell Phones', 'Cell-Phones', '', 1),
(112, 6, 'Communication', 'Communication', '', 1),
(113, 6, 'Electronics', 'Electronics', '', 1),
(114, 6, 'Gadgets and Gizmos', 'Gadgets-and-Gizmos', '', 1),
(115, 6, 'GPS', 'GPS', '', 1),
(116, 6, 'Information Technology', 'Information-Technology', '', 1),
(117, 6, 'Video Conferencing', 'Video-Conferencing', '', 1),
(118, 6, 'VoIP', 'VoIP', '', 1),
(119, 7, 'Antiques', 'Antiques', '', 1),
(120, 7, 'Arts', 'Arts', '', 1),
(121, 7, 'Dance', 'Dance', '', 1),
(122, 7, 'Humanities', 'Humanities', '', 1),
(123, 7, 'Literature', 'Literature', '', 1),
(124, 7, 'Movies', 'Movies', '', 1),
(125, 7, 'Music', 'Music', '', 1),
(126, 7, 'Painting', 'Painting', '', 1),
(127, 7, 'Photography', 'Photography', '', 1),
(128, 7, 'Poetry', 'Poetry', '', 1),
(129, 7, 'Television', 'Television', '', 1),
(130, 7, 'Visual Art', 'Visual-Art', '', 1);

CREATE TABLE IF NOT EXISTS `[db_prefix]youtube` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]entries` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `author_id` int(11) unsigned NOT NULL default '0',  
  `caption` varchar(64) NOT NULL default '',
  `snippet` text NOT NULL,
  `content` text NOT NULL,
  `when` int(11) NOT NULL default '0',
  `uri` varchar(64) NOT NULL default '',
  `tags` varchar(255) NOT NULL default '',
  `categories` varchar(255) NOT NULL default '',
  `comment` tinyint(0) NOT NULL default '0',
  `vote` tinyint(0) NOT NULL default '0',
  `date` int(11) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `featured` tinyint(4) NOT NULL default '0',
  `rate` int(11) NOT NULL default '0',
  `rate_count` int(11) NOT NULL default '0',
  `view_count` int(11) NOT NULL default '0',
  `cmts_count` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  FULLTEXT KEY `search_group` (`caption`, `content`, `tags`, `categories`),
  FULLTEXT KEY `search_caption` (`caption`),
  FULLTEXT KEY `search_content` (`content`),
  FULLTEXT KEY `search_tags` (`tags`),
  FULLTEXT KEY `search_categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `snippet` text NOT NULL,
  `desc` text NOT NULL,
  `language` int(10) NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(64) NOT NULL, 
  `state` varchar(10) NOT NULL default '', 
  `street` varchar(150) NOT NULL,
  `zip` varchar(16) NOT NULL,

  `category_id` int(11) NOT NULL default '0',
  `parent_category_id` int(11) NOT NULL default '0', 
 
  `letter` varchar(1) NOT NULL default '',

  `status` enum('approved','pending','draft') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `publish` int(11) NOT NULL default '1',
  `when` int(11) NOT NULL default '0',
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `news_membership_filter` varchar(100) NOT NULL default '',  
  `allow_view_news_to` int(11) NOT NULL,
  `allow_comment_to` int(11) NOT NULL,
  `allow_rate_to` int(11) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  `votes` int(11) NOT NULL default '0',  
  `video_embed` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc`,`tags`),
  FULLTEXT KEY `tags` (`tags`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]react_admin` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `Active` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;
 
 
INSERT INTO `[db_prefix]react_admin` (`ID`, `Name`, `Active`) VALUES
(1, 'lol', 1),
(2, 'wow', 1),
(3, 'sorry, hugs', 1),
(4, 'you rock', 1),
(5, 'I understand', 1); 
 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]react_tracking` (
  `ReactID` int(11) NOT NULL,
  `ConfessID` int(11) NOT NULL,
  `MemberID` int(11) NOT NULL,
  PRIMARY KEY  (`ReactID`,`ConfessID`,`MemberID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;
 
 
CREATE TABLE IF NOT EXISTS `[db_prefix]vote_tracking` (
  `ID` int(11) NOT NULL,
  `OwnerID` int(11) NOT NULL,
  PRIMARY KEY  (`ID`,`OwnerID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;
 
 
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

CREATE TABLE IF NOT EXISTS `[db_prefix]rss` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `sys_stat_member` CHANGE `Type` `Type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_news_view', 'News View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_news_main', 'News Home', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_news_my', 'News My', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_news_category', 'News Category', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_news_subcategory', 'News Sub-Category', @iMaxOrder+5); 


 
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_news_category', '1140px', 'News', '_modzzz_news_block_news', '2', '0', 'CategoryNews', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_news_category', '1140px', 'News Categories', '_modzzz_news_block_categories', '3', '0', 'Categories', '', '1', 28.1, 'non,memb', '0'), 
    ('modzzz_news_subcategory', '1140px', 'News Category News', '_modzzz_news_block_category_news', '2', '0', 'SubCategoryNews', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_news_subcategory', '1140px', 'News Sub-Categories', '_modzzz_news_block_subcategories', '3', '0', 'SubCategories', '', '1', 28.1, 'non,memb', '0'), 

    ('modzzz_news_view', '1140px', 'News''s description block', '_modzzz_news_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_news_view', '1140px', 'News''s photo block', '_modzzz_news_block_photo', '2', '1', 'Photo', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_news_view', '1140px', 'News''s Video Embed block', '_modzzz_news_block_video_embed', '2', '2', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_news_view', '1140px', 'News''s videos block', '_modzzz_news_block_video', '2', '3', 'Video', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_news_view', '1140px', 'News''s sounds block', '_modzzz_news_block_sound', '2', '4', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_news_view', '1140px', 'News''s files block', '_modzzz_news_block_files', '2', '5', 'Files', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_news_view', '1140px', 'News''s related block', '_modzzz_news_block_related', '2', '6', 'Related', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_news_view', '1140px', 'News''s similar category block', '_modzzz_news_block_related_category', '2', '7', 'Category', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_news_view', '1140px', 'News''s comments block', '_modzzz_news_block_comments', '2', '8', 'Comments', '', '1', '71.9', 'non,memb', '0'),
  
    ('modzzz_news_view', '1140px', 'News''s actions block', '_modzzz_news_block_actions', '3', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_news_view', '1140px', 'News''s rate block', '_modzzz_news_block_rate', '3', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_news_view', '1140px', 'News''s info block', '_modzzz_news_block_info', '3', '2', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_news_view', '1140px', 'News''s map view block', '_modzzz_news_block_map_view', 3, 3, 'Location', '', 1, 28.1, 'non,memb', 0), 
    ('modzzz_news_view', '1140px', 'News''s social sharing block', '_sys_block_title_social_sharing', 3, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0),
    ('modzzz_news_view', '1140px', 'News''s tags block', '_modzzz_news_tags', 3, 5, 'Tags', '', 1, 28.1, 'non,memb', 0),  
 
    ('modzzz_news_main', '1140px', 'Browse News Alphabetically', '_modzzz_news_browse_alphabetically', '1', '0', 'Alphabet', '', '1', '100', 'non,memb', '0'),

    ('modzzz_news_main', '1140px', 'Search News', '_modzzz_news_block_search', '2', '0', 'Search', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_news_main', '1140px', 'Latest Featured News', '_modzzz_news_block_latest_featured_news', '2', '1', 'LatestFeaturedNews', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_news_main', '1140px', 'Recent News', '_modzzz_news_block_recent', '2', '2', 'Recent', '', '1', '71.9', 'non,memb', '0'),
 
    ('modzzz_news_main', '1140px', 'News Create', '_modzzz_news_block_news_create', '3', '0', 'Create', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_news_main', '1140px', 'News Categories', '_modzzz_news_block_news_categories', '3', '1', 'NewsCategories', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_news_main', '1140px', 'News Archive', '_modzzz_news_block_archive', '3', '2', 'Archive', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_news_main', '1140px', 'News Tags', '_tags_plural', '3', '3', 'Tags', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_news_main', '1140px', 'News Comments', '_modzzz_news_block_comments', '3', '4', 'Comments', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_news_main', '1140px', 'News Calendar', '_modzzz_news_block_calendar', '3', '5', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
 
    ('modzzz_news_my', '1140px', 'Administration Owner', '_modzzz_news_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('modzzz_news_my', '1140px', 'User''s news', '_modzzz_news_block_users_news', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('profile', '1140px', 'User News', '_modzzz_news_block_my_news', 3, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''news'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0),
    ('index', '1140px', 'News', '_modzzz_news_block_homepage', 2, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''news'', ''homepage_block'');', 1, 71.9, 'non,memb', 0);

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=news/', 'm/news/', 'modzzz_news_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('News', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_news_permalinks', 'on', 26, 'Enable friendly permalinks in news', 'checkbox', '', '', '0', ''),
('modzzz_news_autoapproval', 'on', @iCategId, 'Activate all news after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_news_author_comments_admin', 'on', @iCategId, 'Allow news admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('category_auto_app_modzzz_news', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_news_perpage_main_recent', '10', @iCategId, 'Number of recently added NEWS to show on news home', 'digit', '', '', '0', ''),
('modzzz_news_perpage_browse', '14', @iCategId, 'Number of news to show on browse pages', 'digit', '', '', '0', ''),
('modzzz_news_perpage_profile', '4', @iCategId, 'Number of news to show on profile page', 'digit', '', '', '0', ''),
('modzzz_news_perpage_homepage', '5', @iCategId, 'Number of news to show on homepage', 'digit', '', '', '0', ''),
('modzzz_news_homepage_default_tab', 'featured', @iCategId, 'Default news block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),
('modzzz_news_title_length', '100', @iCategId, 'Max length of news Topic', 'digit', '', '', '0', ''),
('modzzz_news_snippet_length', '300', @iCategId, 'The length of news snippet for home and account pages', 'digit', '', '', '0', ''),
 
('modzzz_news_show_member', 'on', @iCategId, 'Show member thumbnail in news listings. If turned off, the main news photo will be shown instead', 'checkbox', '', '', '0', ''), 
('modzzz_news_show_letter', 'on', @iCategId, 'Show alphabet browse bar', 'checkbox', '', '', '0', ''), 

('modzzz_news_comments_max_preview', '150', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
('modzzz_news_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''), 

('modzzz_news_perpage_related', '5', @iCategId, 'Number of related news to show on news view page', 'digit', '', '', '0', ''),
('modzzz_news_perpage_rss_feed', '10', @iCategId, 'Number of rss items to show on news view page', 'digit', '', '', '0', '') ,
('modzzz_news_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', '');

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'modzzz_news', '_modzzz_news', 'BxNewsSearchResult', 'modules/modzzz/news/classes/BxNewsSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_news', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxNewsVoting', 'modules/modzzz/news/classes/BxNewsVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_news', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxNewsCmts', 'modules/modzzz/news/classes/BxNewsCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_news', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'modzzz_news', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_news_permalinks', 'm/news/browse/tag/{tag}', 'modules/?r=news/browse/tag/{tag}', '_modzzz_news');

-- category objects
-- INSERT INTO `sys_objects_categories` VALUES (NULL, 'modzzz_news', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_news_permalinks', 'm/news/browse/category/{tag}', 'modules/?r=news/browse/category/{tag}', '_modzzz_news');
  

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Arts & Entertainment', '0', 'modzzz_news', '0', 'active'), 
('Business', '0', 'modzzz_news', '0', 'active'),
('Education', '0', 'modzzz_news', '0', 'active'), 
('General', '0', 'modzzz_news', '0', 'active'),
('Finance', '0', 'modzzz_news', '0', 'active'),
('Health & Wellness', '0', 'modzzz_news', '0', 'active'), 
('News', '0', 'modzzz_news', '0', 'active'),
('Sport', '0', 'modzzz_news', '0', 'active'),
('Technology', '0', 'modzzz_news', '0', 'active'),
('Home and Family', '0', 'modzzz_news', '0', 'active'),
('Other', '0', 'modzzz_news', '0', 'active'), 
('Religion', '0', 'modzzz_news', '0', 'active');

 
-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxNewsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'modzzz_news'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'', true);return false;', '$oConfig = $GLOBALS[''oBxNewsModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'modzzz_news'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'modzzz_news'),
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxNewsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '6', 'modzzz_news'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '9', 'modzzz_news'),
    ('{TitleEmbed}', 'film', '{BaseUri}embed/{URI}', '', '', '10', 'modzzz_news'), 
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '10', 'modzzz_news'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '11', 'modzzz_news'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '12', 'modzzz_news'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '13', 'modzzz_news'),
    
    ('{TitleActivate}', 'check-circle-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxNewsModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''activate/{ID}'';', '30', 'modzzz_news'),
    ('{repostCpt}', 'repeat', '', '{repostScript}', '', 31, 'modzzz_news'),


    ('{evalResult}', 'plus', '{BaseUri}browse/my&filter=add_news', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_news_action_add_news'') : '''';', '1', 'modzzz_news_title'),
    ('{evalResult}', 'file', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_news_action_my_news'') : '''';', '2', 'modzzz_news_title');
    
-- top menu 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'News', '_modzzz_news_menu_root', 'modules/?r=news/view/|modules/?r=news/edit/|modules/?r=news/embed/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'file', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'News View', '_modzzz_news_menu_view_news', 'modules/?r=news/view/{modzzz_news_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'News View Comments', '_modzzz_news_menu_view_comments', 'modules/?r=news/comments/{modzzz_news_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');


SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'News', '_modzzz_news_menu_root', 'modules/?r=news/home/|modules/?r=news/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'file', 'file', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'News Main Page', '_modzzz_news_menu_main', 'modules/?r=news/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Recent News', '_modzzz_news_menu_recent', 'modules/?r=news/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Top Rated News', '_modzzz_news_menu_top_rated', 'modules/?r=news/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Popular News', '_modzzz_news_menu_popular', 'modules/?r=news/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Featured News', '_modzzz_news_menu_featured', 'modules/?r=news/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'News Tags', '_modzzz_news_menu_tags', 'modules/?r=news/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_news');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'News Categories', '_modzzz_news_menu_categories', 'modules/?r=news/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_news');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Calendar', '_modzzz_news_menu_calendar', 'modules/?r=news/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Search', '_modzzz_news_menu_search', 'modules/?r=news/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

 
-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_news', '_modzzz_news', '{siteUrl}modules/?r=news/administration/', 'News module by Modzzz','file', @iMax+1);

-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'modzzz_news', 'modzzz_news', 'modules/?r=news/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=news/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'briefcase', @iStatSiteOrder);

-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('modzzz_news', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('modzzz_newsp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_modzzz_news', '__modzzz_news__ __l_created__ (<a href="modules/?r=news/browse/my&filter=add_news">__l_add__</a>)');
 

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;


INSERT INTO `sys_acl_actions` VALUES (NULL, 'news autoapprove news', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
     (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'news view news', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'news browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'news search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
  

INSERT INTO `sys_acl_actions` VALUES (NULL, 'news add news', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'news comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'news edit any news', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'news delete any news', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'news mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'news approve news', NULL);
 
-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_news_profile_delete', '', '', 'BxDolService::call(''news'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_news_media_delete', '', '', 'BxDolService::call(''news'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);

-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'modzzz_news', `Eval` = 'return BxDolService::call(''news'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);


-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('news', 'view_news', '_modzzz_news_privacy_view_news', '3'),
('news', 'comment', '_modzzz_news_privacy_comment', '3'),
('news', 'rate', '_modzzz_news_privacy_rate', '3');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('modzzz_news', '', '', 'return BxDolService::call(''news'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_news', 'change', 'modzzz_news_sbs', 'return BxDolService::call(''news'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_news', 'commentPost', 'modzzz_news_sbs', 'return BxDolService::call(''news'', ''get_subscription_params'', array($arg2, $arg3));');
 
INSERT INTO `sys_cron_jobs` (`name`, `time`, `class`, `file`, `eval`) VALUES
('modzzz_news', '*/5 * * * *', 'BxNewsCron', 'modules/modzzz/news/classes/BxNewsCron.php', '');



 INSERT INTO `sys_pre_values` ( `Key`, `Order`, `Value`, `LKey`) VALUES 
('NewsLetter', 1, 'A', '_modzzz_news_letter_a'),
('NewsLetter', 2, 'B', '_modzzz_news_letter_b'),
('NewsLetter', 3, 'C', '_modzzz_news_letter_c'),
('NewsLetter', 4, 'D', '_modzzz_news_letter_d'),
('NewsLetter', 5, 'E', '_modzzz_news_letter_e'),
('NewsLetter', 6, 'F', '_modzzz_news_letter_f'),
('NewsLetter', 7, 'G', '_modzzz_news_letter_g'),
('NewsLetter', 8, 'H', '_modzzz_news_letter_h'),
('NewsLetter', 9, 'I', '_modzzz_news_letter_i'),
('NewsLetter', 10, 'J', '_modzzz_news_letter_j'),
('NewsLetter', 11, 'K', '_modzzz_news_letter_k'),
('NewsLetter', 12, 'L', '_modzzz_news_letter_l'),
('NewsLetter', 13, 'M', '_modzzz_news_letter_m'),
('NewsLetter', 14, 'N', '_modzzz_news_letter_n'),
('NewsLetter', 15, 'O', '_modzzz_news_letter_o'),
('NewsLetter', 16, 'P', '_modzzz_news_letter_p'),
('NewsLetter', 17, 'Q', '_modzzz_news_letter_q'),
('NewsLetter', 18, 'R', '_modzzz_news_letter_r'),
('NewsLetter', 19, 'S', '_modzzz_news_letter_s'),
('NewsLetter', 20, 'T', '_modzzz_news_letter_t'),
('NewsLetter', 21, 'U', '_modzzz_news_letter_u'),
('NewsLetter', 22, 'V', '_modzzz_news_letter_v'),
('NewsLetter', 23, 'W', '_modzzz_news_letter_w'),
('NewsLetter', 24, 'X', '_modzzz_news_letter_x'),
('NewsLetter', 25, 'Y', '_modzzz_news_letter_y'),
('NewsLetter', 26, 'Z', '_modzzz_news_letter_z')  
 ;  
 

-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('modzzz_news', '_modzzz_news', '0.8', 'auto', 'BxNewsSiteMaps', 'modules/modzzz/news/classes/BxNewsSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('modzzz_news', '_modzzz_news', 'modzzz_news_main', 'when', '', '', 1, @iMaxOrderCharts);
 

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_news_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''news'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'news allow embed', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);