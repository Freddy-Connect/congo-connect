-- create tables

 CREATE TABLE IF NOT EXISTS `[db_prefix]ratehelp` (
 `id` INT NOT NULL ,
 `author_id` INT NOT NULL ,
PRIMARY KEY  (`id`,`author_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]today_item` (
 `id` INT NOT NULL ,
 PRIMARY KEY  (`id` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]sel_daily_items` (
 `id` INT NOT NULL ,
 PRIMARY KEY  (`id` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `[db_prefix]today_item` SET `id`=1; 
INSERT INTO `[db_prefix]sel_daily_items` SET `id`=1; 

CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `snippet` text NOT NULL,
  `desc` text NOT NULL, 
  `status` enum('approved','pending') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate_up` INT NOT NULL ,
  `rate_down` INT NOT NULL ,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_faq_to` int(11) NOT NULL,
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
  FULLTEXT KEY `search` (`title`,`desc`,`tags`,`categories`),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
 
SET @iAdmin = (SELECT `ID` FROM `Profiles` WHERE `Role`=2 OR `Role`=3 ORDER BY `DateReg` ASC LIMIT 1);
INSERT INTO `[db_prefix]main` (`id`, `categories`, `title`, `snippet`, `desc`, `tags`, `uri`, `status`,   `created`, `author_id`,allow_view_faq_to,allow_comment_to,allow_rate_to) VALUES
(1, 'General', 'Is this site Free?', 'Yes, you can create a profile and access all site features absolutely free of charge !', '<p>Yes, you can create a profile and access all site features absolutely free of charge !</p>', 'free,site', 'Is-this-site-Free-', 'approved', UNIX_TIMESTAMP(now()), @iAdmin,3,1,1),
(2, 'Profile', 'How do I block someone?', 'Go to the person''s page that you want to block and look on the left hand navigation links for BLOCK.', '<p>Go to the person''s page that you want to block and look on the left hand navigation links for BLOCK. Click on the BLOCK link and approve the block. Blocking a member prohibits them from messaging you, commenting on your page, blogs, events, classifieds and blocks them from being able to rate your pictures or profile. Blocking a member does NOT stop them from coming on to your page to look around.</p>', 'profile,block,spam', 'How-do-I-block-someone-', 'approved', UNIX_TIMESTAMP(now()), @iAdmin,3,1,1);

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES  
('General', '1', 'modzzz_faq', @iAdmin, 'active'), 
('Profile', '2', 'modzzz_faq', @iAdmin, 'active');


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
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
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
 
UPDATE `sys_menu_bottom` SET `Link` = 'm/faq/home' WHERE `Link` ='faq.php';    

ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
 
 
 
-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_faq_view', 'FAQ View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_faq_main', 'FAQ Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_faq_my', 'FAQ My', @iMaxOrder+4);

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_faq_view', '1140px', 'FAQ''s description block', '_modzzz_faq_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_faq_view', '1140px', 'FAQ''s photo block', '_modzzz_faq_block_photo', '2', '1', 'Photo', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_faq_view', '1140px', 'FAQ''s Video Embed block', '_modzzz_faq_block_video_embed', '2', '2', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'),   
    ('modzzz_faq_view', '1140px', 'FAQ''s videos block', '_modzzz_faq_block_video', '2', '3', 'Video', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_faq_view', '1140px', 'FAQ''s sounds block', '_modzzz_faq_block_sound', '2', '4', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_faq_view', '1140px', 'FAQ''s files block', '_modzzz_faq_block_files', '2', '5', 'Files', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_faq_view', '1140px', 'FAQ''s comments block', '_modzzz_faq_block_comments', '2', '6', 'Comments', '', '1', '71.9', 'non,memb', '0'),
    
    ('modzzz_faq_view', '1140px', 'FAQ''s actions block', '_modzzz_faq_block_actions', '3', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_faq_view', '1140px', 'FAQ''s rate block', '_modzzz_faq_block_rate', '3', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_faq_view', '1140px', 'FAQ''s info block', '_modzzz_faq_block_info', '3', '2', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_faq_view', '1140px', 'FAQ''s help block', '_modzzz_faq_block_help', '3', '3', 'Help', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_faq_view', '1140px', 'FAQ''s social sharing block', '_sys_block_title_social_sharing', 3, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0),

    ('modzzz_faq_main', '1140px', 'Latest Featured FAQ', '_modzzz_faq_block_latest_featured_faq', '2', '0', 'LatestFeaturedFAQ', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_faq_main', '1140px', 'Recent FAQ', '_modzzz_faq_block_recent', '2', '1', 'Recent', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_faq_main', '1140px', 'Search Article', '_modzzz_faq_block_search', '3', '0', 'Search', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_faq_main', '1140px', 'FAQ Categories', '_modzzz_faq_block_faq_categories', '3', '1', 'FAQCategories', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_faq_main', '1140px', 'FAQ Tags', '_tags_plural', '3', '2', 'Tags', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_faq_main', '1140px', 'FAQ Calendar', '_modzzz_faq_block_calendar', '3', '3', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
  
    ('modzzz_faq_my', '1140px', 'Administration Owner', '_modzzz_faq_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('modzzz_faq_my', '1140px', 'User''s faq', '_modzzz_faq_block_users_faq', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    
    ('index', '1140px', 'FAQ', '_modzzz_faq_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''faq'', ''homepage_block'');', 1, 71.9, 'non,memb', 0),
    ('index', '1140px', 'FAQ', '_modzzz_faq_daily_tips', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''faq'', ''dailytip_block'', array(''index.php''));', 1, 71.9, 'non,memb', 0), 
    ('member', '1140px', 'FAQ', '_modzzz_faq_daily_tips', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''faq'', ''dailytip_block'', array(''member.php''));', 1, 71.9, 'non,memb', 0);
    
-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=faq/', 'm/faq/', 'modzzz_faq_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('FAQ', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_faq_permalinks', 'on', 26, 'Enable friendly permalinks in faq', 'checkbox', '', '', '0', ''),
('modzzz_faq_autoapproval', 'on', @iCategId, 'Activate all faq after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_faq_author_comments_admin', 'on', @iCategId, 'Allow faq admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('category_auto_app_modzzz_faq', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_faq_perpage_main_recent', '10', @iCategId, 'Number of recently added FAQS to show on faq home', 'digit', '', '', '0', ''),
('modzzz_faq_perpage_browse', '14', @iCategId, 'Number of faq to show on browse pages', 'digit', '', '', '0', ''),
('modzzz_faq_perpage_accountpage', '1', @iCategId, 'Number of faq to show in daily tips block', 'digit', '', '', '0', ''),
('modzzz_faq_perpage_homepage', '5', @iCategId, 'Number of faq to show on homepage', 'digit', '', '', '0', ''),
('modzzz_faq_homepage_default_tab', 'recent', @iCategId, 'Default faq block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),
('modzzz_faq_snippet_length', '300', @iCategId, 'The length of FAQ snippet for home and account pages', 'digit', '', '', '0', ''),
('modzzz_faq_title_length', '100', @iCategId, 'Max. length of FAQ Topic', 'digit', '', '', '0', ''), 
('modzzz_faq_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', '');

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'modzzz_faq', '_modzzz_faq', 'BxFAQSearchResult', 'modules/modzzz/faq/classes/BxFAQSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_faq', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxFAQVoting', 'modules/modzzz/faq/classes/BxFAQVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_faq', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxFAQCmts', 'modules/modzzz/faq/classes/BxFAQCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_faq', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'modzzz_faq', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_faq_permalinks', 'm/faq/browse/tag/{tag}', 'modules/?r=faq/browse/tag/{tag}', '_modzzz_faq');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'modzzz_faq', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_faq_permalinks', 'm/faq/browse/category/{tag}', 'modules/?r=faq/browse/category/{tag}', '_modzzz_faq');
  

INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Blogs', '0', 'modzzz_faq', '0', 'active'),
('Events', '0', 'modzzz_faq', '0', 'active'),
('Friends', '0', 'modzzz_faq', '0', 'active'), 
('General', '0', 'modzzz_faq', '0', 'active'),
('Groups', '0', 'modzzz_faq', '0', 'active'),
('Mail System', '0', 'modzzz_faq', '0', 'active'), 
('Photos', '0', 'modzzz_faq', '0', 'active'),
('Profile', '0', 'modzzz_faq', '0', 'active'), 
('Registration', '0', 'modzzz_faq', '0', 'active'),
('Search', '0', 'modzzz_faq', '0', 'active'),
('Other', '0', 'modzzz_faq', '0', 'active'), 
('Videos', '0', 'modzzz_faq', '0', 'active');

-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxFAQModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'modzzz_faq'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxFAQModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'modzzz_faq'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'modzzz_faq'),
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxFAQModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '3', 'modzzz_faq'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '4', 'modzzz_faq'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '5', 'modzzz_faq'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '6', 'modzzz_faq'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '7', 'modzzz_faq'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '8', 'modzzz_faq'),
    
    ('{evalResult}', 'plus', '{BaseUri}browse/my&modzzz_faq_filter=add_faq', '', '$oFAQ = BxDolModule::getInstance(''BxFAQModule'');return (($oFAQ!=null) && $oFAQ->isAllowedAdd()) ? _t(''_modzzz_faq_action_add_faq'') : '''';', '0', 'modzzz_faq_title'),
    ('{evalResult}', 'question-circle', '{BaseUri}', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_faq_action_faq_home'') : '''';', '1', 'modzzz_faq_title');
 
    
-- top menu 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'FAQ', '_modzzz_faq_menu_root', 'modules/?r=faq/view/|modules/?r=faq/edit/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', '', '', '0', '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'FAQ View', '_modzzz_faq_menu_view_faq', 'modules/?r=faq/view/{modzzz_faq_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'FAQ View Comments', '_modzzz_faq_menu_view_comments', 'modules/?r=faq/comments/{modzzz_faq_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');


SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'FAQ', '_modzzz_faq_menu_root', 'modules/?r=faq/home/|modules/?r=faq/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'question-circle', 'question-circle', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'FAQ Main Page', '_modzzz_faq_menu_main', 'modules/?r=faq/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Recent FAQ', '_modzzz_faq_menu_recent', 'modules/?r=faq/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Top Rated FAQ', '_modzzz_faq_menu_top_rated', 'modules/?r=faq/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Popular FAQ', '_modzzz_faq_menu_popular', 'modules/?r=faq/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Featured FAQ', '_modzzz_faq_menu_featured', 'modules/?r=faq/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'FAQ Tags', '_modzzz_faq_menu_tags', 'modules/?r=faq/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_faq');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'FAQ Categories', '_modzzz_faq_menu_categories', 'modules/?r=faq/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_faq');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Calendar', '_modzzz_faq_menu_calendar', 'modules/?r=faq/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Search', '_modzzz_faq_menu_search', 'modules/?r=faq/search', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');


-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_faq', '_modzzz_faq', '{siteUrl}modules/?r=faq/administration/', 'FAQ module by Modzzz','question-circle', @iMax+1);
 
 
-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'modzzz_faq', 'modzzz_faq', 'modules/?r=faq/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=faq/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'question-circle', @iStatSiteOrder);



-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq add faq', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq view faq', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq rate help', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq edit any faq', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq delete any faq', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq approve faq', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'faq rate help', NULL);
 
-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_faq_profile_delete', '', '', 'BxDolService::call(''faq'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_faq_media_delete', '', '', 'BxDolService::call(''faq'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);
 

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('faq', 'view_faq', '_modzzz_faq_privacy_view_faq', '3'),
('faq', 'comment', '_modzzz_faq_privacy_comment', 'f'),
('faq', 'rate', '_modzzz_faq_privacy_rate', 'f');

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('modzzz_faq', '', '', 'return BxDolService::call(''faq'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_faq', 'change', 'modzzz_faq_sbs', 'return BxDolService::call(''faq'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_faq', 'commentPost', 'modzzz_faq_sbs', 'return BxDolService::call(''faq'', ''get_subscription_params'', array($arg2, $arg3));');

-- email templates
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
 ('modzzz_faq_sbs', 'FAQ was changed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<ViewLink>"><EntryTitle></a> FAQ was changed: <br /> <ActionName> </p> <bx_include_auto:_email_footer.html />', 'FAQ subscription template', '0');

INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxFAQCron', '59 23 * * *', 'BxFAQCron', 'modules/modzzz/faq/classes/BxFAQCron.php', '');


UPDATE `sys_menu_top` SET `link` = 'modules/?r=faq/home/' WHERE `link` = 'faq.php'; 



-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('modzzz_faq', '_modzzz_faq', '0.8', 'auto', 'BxFAQSiteMaps', 'modules/modzzz/faq/classes/BxFAQSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('modzzz_faq', '_modzzz_faq', 'modzzz_faq_main', 'created', '', '', 1, @iMaxOrderCharts);
