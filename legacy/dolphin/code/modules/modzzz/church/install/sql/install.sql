ALTER TABLE `sys_menu_top` CHANGE `Link` `Link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

-- top menu 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Church', '_modzzz_church_menu_root', 'modules/?r=church/view/|modules/?r=church/edit/|modules/?r=church/claim/|modules/?r=church/inquire/|modules/?r=church/invite/|modules/?r=church/map_edit/|forum/church/|modules/?r=church/relist/|modules/?r=church/extend/|modules/?r=church/premium/|modules/?r=church/purchase_featured/|modules/?r=church/broadcast/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'home', '', '0', '');

SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View', '_modzzz_church_menu_view_church', 'modules/?r=church/view/{modzzz_church_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Fans', '_modzzz_church_menu_view_fans', 'modules/?r=church/browse_fans/{modzzz_church_view_uri}', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Comments', '_modzzz_church_menu_view_comments', 'modules/?r=church/comments/{modzzz_church_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Branches', '_modzzz_church_menu_view_branches', 'modules/?r=church/branches/browse/{modzzz_church_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Staffs', '_modzzz_church_menu_view_staffs', 'modules/?r=church/staff/browse/{modzzz_church_view_uri}', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Members', '_modzzz_church_menu_view_members', 'modules/?r=church/members/browse/{modzzz_church_view_uri}', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Ministries', '_modzzz_church_menu_view_ministries', 'modules/?r=church/ministries/browse/{modzzz_church_view_uri}', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Doctrines', '_modzzz_church_menu_view_doctrines', 'modules/?r=church/doctrine/browse/{modzzz_church_view_uri}', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Sermons', '_modzzz_church_menu_view_sermons', 'modules/?r=church/sermon/browse/{modzzz_church_view_uri}', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View News', '_modzzz_church_menu_view_news', 'modules/?r=church/news/browse/{modzzz_church_view_uri}', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Events', '_modzzz_church_menu_view_events', 'modules/?r=church/event/browse/{modzzz_church_view_uri}', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Reviews', '_modzzz_church_menu_view_reviews', 'modules/?r=church/review/browse/{modzzz_church_view_uri}', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES (NULL, @iCatRoot, 'Church View FAQ', '_modzzz_church_menu_view_faqs', 'modules/?r=church/faq/browse/{modzzz_church_view_uri}', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church View Forum', '_modzzz_church_menu_view_forum', 'forum/church/forum/{modzzz_church_view_uri}-0.htm|forum/church/', 13, 'non,memb', '', '', '$oModuleDb = new BxDolModuleDb(); return $oModuleDb->getModuleByUri(''forum'') ? true : false;', 1, 1, 1, 'custom', '', '', 0, '');
 

-- *********[BEGIN] DONATIONS ************ 
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleDonate}', 'gift', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''donation/add/{ID}'';', '30', 'modzzz_church');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/donation/add/|modules/?r=church/donation/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church make donation', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church view donors', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_donation_thanks', 'Thank you for the Paypal Donation to Church - <ChurchName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <HeroName></b>,</p>\r\n\r\n<p>On behalf of <a href="<ChurchLink>"><ChurchName></a>, we would like to say <b>Thank You</b> for your generous donation. Keep up to good work and continue to touch the lives of others in a positive way</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Church Donation Thank You notification', '0');

 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_donation_notify', 'Donation made to Church - <ChurchName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <OwnerName></b>,</p>\r\n\r\n<p>Your Church, <a href="<ChurchLink>"><ChurchName></a>, received a Donation of <Amount> from <a href="<HeroLink>"><HeroName></a>. </p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Church Donation Received notification', '0');


CREATE TABLE IF NOT EXISTS `[db_prefix]paypal_trans` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `amount` float unsigned NOT NULL, 
  `church_id` int(11) unsigned NOT NULL,
  `donor_id` int(11) unsigned NOT NULL,

  `first_name` varchar(100) CHARACTER SET utf8 collate utf8_general_ci NOT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 collate utf8_general_ci NOT NULL,
  `anonymous` int(11) unsigned NOT NULL, 
  `paypal` varchar(50) NOT NULL default '',

  `trans_id` varchar(100) CHARACTER SET utf8 collate utf8_general_ci NOT NULL,
  `trans_type` varchar(100) CHARACTER SET utf8 collate utf8_general_ci NOT NULL,
  `created` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `paypal_id` (`donor_id`,`trans_id`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

-- *********[END] DONATIONS ************ 

 
-- *********[BEGIN] NEWS ************ 
 
CREATE TABLE IF NOT EXISTS `modzzz_church_news_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
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
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL, 
  `allow_upload_files_to` varchar(16) NOT NULL, 
  `allow_upload_sounds_to` varchar(16) NOT NULL,  
  PRIMARY KEY (`id`),
  UNIQUE KEY `loc_news_uri` (`uri`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_news_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_news_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_news_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_news_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_news_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_news_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_church_news_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_church_news_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_news', 'modzzz_church_news_rating', 'modzzz_church_news_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_news_main', 'rate', 'rate_count', 'id', 'BxChurchNewsVoting', 'modules/modzzz/church/classes/BxChurchNewsVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_news', 'modzzz_church_news_cmts', 'modzzz_church_news_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_news_main', 'id', 'comments_count', 'BxChurchNewsCmts', 'modules/modzzz/church/classes/BxChurchNewsCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/edit/{ID}'';', '0', 'modzzz_church_news'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/delete/{ID}'';', '1', 'modzzz_church_news'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/news/{URI}', '', '', '2', 'modzzz_church_news'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/news/{URI}', '', '', '4', 'modzzz_church_news'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/news/{URI}', '', '', '5', 'modzzz_church_news'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/news/{URI}', '', '', '6', 'modzzz_church_news');
 
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_news_view', 'Church News View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_news_browse', 'Church News Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_news_browse', '1140px', 'Church News''s browse block', '_modzzz_church_block_browse_news', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_news_view', '1140px', 'Church News''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_news_view', '1140px', 'Church News''s rate block', '_modzzz_church_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_news_view', '1140px', 'Church News''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_news_view', '1140px', 'Church News''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_news_view', '1140px', 'Church News''s comments block', '_modzzz_church_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');
 
 

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleNewsAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/add/{ID}'';', '14', 'modzzz_church');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/news/add/|modules/?r=church/news/edit/|modules/?r=church/news/view/|modules/?r=church/news/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';
  

-- *********[END] NEWS *********** 

-- *********[BEGIN] Ministries ************ 
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `icon` varchar(255) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL, 
  `allow_upload_files_to` varchar(16) NOT NULL, 
  `allow_upload_sounds_to` varchar(16) NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `church_ministries_uri` (`uri`),
  KEY `church_ministries_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_church_ministries_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_ministries', 'modzzz_church_ministries_rating', 'modzzz_church_ministries_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_ministries_main', 'rate', 'rate_count', 'id', 'BxChurchMinistriesVoting', 'modules/modzzz/church/classes/BxChurchMinistriesVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_ministries', 'modzzz_church_ministries_cmts', 'modzzz_church_ministries_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_ministries_main', 'id', 'comments_count', 'BxChurchMinistriesCmts', 'modules/modzzz/church/classes/BxChurchMinistriesCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''ministries/edit/{ID}'';', '0', 'modzzz_church_ministries'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''ministries/delete/{ID}'';', '1', 'modzzz_church_ministries'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/ministries/{URI}', '', '', '2', 'modzzz_church_ministries'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/ministries/{URI}', '', '', '4', 'modzzz_church_ministries'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/ministries/{URI}', '', '', '5', 'modzzz_church_ministries'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/ministries/{URI}', '', '', '6', 'modzzz_church_ministries');
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_ministries_view', 'Church Ministries View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_ministries_browse', 'Church Ministries Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_ministries_browse', '1140px', 'Church Ministries''s browse block', '_modzzz_church_block_browse_ministries', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_ministries_view', '1140px', 'Church Ministries''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_ministries_view', '1140px', 'Church Ministries''s rate block', '_modzzz_church_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_ministries_view', '1140px', 'Church Ministries''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_ministries_view', '1140px', 'Church Ministries''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_ministries_view', '1140px', 'Church Ministries''s comments block', '_modzzz_church_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');   
 
 

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleMinistriesAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''ministries/add/{ID}'';', '14', 'modzzz_church');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/ministries/add/|modules/?r=church/ministries/edit/|modules/?r=church/ministries/view/|modules/?r=church/ministries/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

-- *********[END] Ministries *********** 

-- *********[BEGIN] Branches ************ 
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `country` varchar(2) NOT NULL default 'US',
  `city` varchar(50) NOT NULL default '',
  `state` varchar(50) NOT NULL default '',
  `zip` varchar(20) NOT NULL default '',
  `address1` varchar(100) NOT NULL default '', 
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `icon` varchar(255) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL, 
  `allow_upload_files_to` varchar(16) NOT NULL, 
  `allow_upload_sounds_to` varchar(16) NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `church_branches_uri` (`uri`),
  KEY `church_branches_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_church_branches_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_branches', 'modzzz_church_branches_rating', 'modzzz_church_branches_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_branches_main', 'rate', 'rate_count', 'id', 'BxChurchBranchesVoting', 'modules/modzzz/church/classes/BxChurchBranchesVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_branches', 'modzzz_church_branches_cmts', 'modzzz_church_branches_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_branches_main', 'id', 'comments_count', 'BxChurchBranchesCmts', 'modules/modzzz/church/classes/BxChurchBranchesCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''branches/edit/{ID}'';', '0', 'modzzz_church_branches'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''branches/delete/{ID}'';', '1', 'modzzz_church_branches'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/branches/{URI}', '', '', '2', 'modzzz_church_branches'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/branches/{URI}', '', '', '3', 'modzzz_church_branches'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/branches/{URI}', '', '', '4', 'modzzz_church_branches'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/branches/{URI}', '', '', '5', 'modzzz_church_branches');
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_branches_view', 'Church Branches View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_branches_browse', 'Church Branches Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_branches_browse', '1140px', 'Church Branches''s browse block', '_modzzz_church_block_browse_branches', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_branches_view', '1140px', 'Church Branches''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_branches_view', '1140px', 'Church Branches''s rate block', '_modzzz_church_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_branches_view', '1140px', 'Church Branches''s location block', '_modzzz_church_block_location', '2', '2', 'Location', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_branches_view', '1140px', 'Church Branches''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_branches_view', '1140px', 'Church Branches''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_branches_view', '1140px', 'Church Branches''s comments block', '_modzzz_church_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');   
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleBranchesAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''branches/add/{ID}'';', '14', 'modzzz_church');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/branches/add/|modules/?r=church/branches/edit/|modules/?r=church/branches/view/|modules/?r=church/branches/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

-- *********[END] Branches *********** 


-- *********[BEGIN] Doctrine ************ 
  
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `church_doctrine_uri` (`uri`),
  KEY `church_doctrine_author_id` (`author_id`),
  KEY `church_doctrine_created` (`created`),
  FULLTEXT KEY `church_doctrine_title` (`title`,`desc`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 
   
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_cmts` (
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
   
CREATE TABLE IF NOT EXISTS `modzzz_church_doctrine_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_doctrine', 'modzzz_church_doctrine_rating', 'modzzz_church_doctrine_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_doctrine_main', 'rate', 'rate_count', 'id', 'BxChurchDoctrineVoting', 'modules/modzzz/church/classes/BxChurchDoctrineVoting.php');
   
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_doctrine', 'modzzz_church_doctrine_cmts', 'modzzz_church_doctrine_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_doctrine_main', 'id', 'comments_count', 'BxChurchDoctrineCmts', 'modules/modzzz/church/classes/BxChurchDoctrineCmts.php');
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''doctrine/edit/{ID}'';', '0', 'modzzz_church_doctrine'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''doctrine/delete/{ID}'';', '1', 'modzzz_church_doctrine'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/doctrine/{URI}', '', '', '2', 'modzzz_church_doctrine'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/doctrine/{URI}', '', '', '3', 'modzzz_church_doctrine'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/doctrine/{URI}', '', '', '4', 'modzzz_church_doctrine'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/doctrine/{URI}', '', '', '5', 'modzzz_church_doctrine'); 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_doctrines_view', 'Church Doctrine View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_doctrines_browse', 'Church Doctrine Browse', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_doctrines_browse', '1140px', 'Church Doctrine''s browse block', '_modzzz_church_block_browse_doctrines', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s info block', '_modzzz_church_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s rate block', '_modzzz_church_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s video block', '_modzzz_church_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s files block', '_modzzz_church_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s sounds block', '_modzzz_church_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s comments block', '_modzzz_church_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');   
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleDoctrineAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''doctrine/add/{ID}'';', '15', 'modzzz_church');
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/doctrine/add/|modules/?r=church/doctrine/edit/|modules/?r=church/doctrine/view/|modules/?r=church/doctrine/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

-- *********[END] Doctrine ************ 

-- *********[BEGIN] STAFF ************ 
 
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `position` varchar(100) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `church_staff_uri` (`uri`),
  KEY `church_staff_author_id` (`author_id`),
  KEY `church_staff_created` (`created`),
  FULLTEXT KEY `church_staff_title` (`title`,`desc`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 
   
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_cmts` (
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
   
CREATE TABLE IF NOT EXISTS `modzzz_church_staff_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_staff', 'modzzz_church_staff_rating', 'modzzz_church_staff_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_staff_main', 'rate', 'rate_count', 'id', 'BxChurchStaffVoting', 'modules/modzzz/church/classes/BxChurchStaffVoting.php');
   
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_staff', 'modzzz_church_staff_cmts', 'modzzz_church_staff_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_staff_main', 'id', 'comments_count', 'BxChurchStaffCmts', 'modules/modzzz/church/classes/BxChurchStaffCmts.php');
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''staff/edit/{ID}'';', '0', 'modzzz_church_staff'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''staff/delete/{ID}'';', '1', 'modzzz_church_staff'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/staff/{URI}', '', '', '2', 'modzzz_church_staff'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/staff/{URI}', '', '', '3', 'modzzz_church_staff'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/staff/{URI}', '', '', '4', 'modzzz_church_staff'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/staff/{URI}', '', '', '5', 'modzzz_church_staff'); 
    
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_staffs_view', 'Church Staff View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_staffs_browse', 'Church Staff Browse', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_staffs_browse', '1140px', 'Church Staff''s browse block', '_modzzz_church_block_browse_staffs', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s info block', '_modzzz_church_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s rate block', '_modzzz_church_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s video block', '_modzzz_church_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s files block', '_modzzz_church_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s sounds block', '_modzzz_church_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_staffs_view', '1140px', 'Church Staff''s comments block', '_modzzz_church_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');   
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleStaffAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''staff/add/{ID}'';', '15', 'modzzz_church');
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/staff/add/|modules/?r=church/staff/edit/|modules/?r=church/staff/view/|modules/?r=church/staff/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

-- *********[END] STAFF ************ 

-- *********[BEGIN] EVENTS************ 
 
INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('ChurchEventCategories', 1, 1, '_modzzz_church_event_anniversary'),
('ChurchEventCategories', 2, 2, '_modzzz_church_event_birthday'),
('ChurchEventCategories', 3, 3, '_modzzz_church_event_christening'),
('ChurchEventCategories', 4, 4, '_modzzz_church_event_community_event'), 
('ChurchEventCategories', 5, 5, '_modzzz_church_event_club'),
('ChurchEventCategories', 6, 6, '_modzzz_church_event_corporate_function'),
('ChurchEventCategories', 7, 7, '_modzzz_church_event_festival'), 
('ChurchEventCategories', 8, 8, '_modzzz_church_event_fund_raiser'), 
('ChurchEventCategories', 9, 9, '_modzzz_church_event_tour'),  
('ChurchEventCategories', 10, 10, '_modzzz_church_event_wedding'),
('ChurchEventCategories', 11, 11, '_modzzz_church_event_other');
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `country` varchar(2) NOT NULL default 'US',
  `state` varchar(50) NOT NULL default '',
  `city` varchar(50) NOT NULL default '',
  `address1` varchar(255) NOT NULL default '',
  `place` varchar(100) NOT NULL default '',
  `zip` varchar(20) NOT NULL default '', 
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
  `allow_view_to` varchar(16) NOT NULL default '3',
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
  UNIQUE KEY `church_event_uri` (`uri`),
  KEY `church_event_author_id` (`author_id`),
  KEY `church_event_event_start` (`event_start`),
  KEY `church_event_created` (`created`),
  FULLTEXT KEY `church_event_title` (`title`,`desc`,`city`,`place`,`tags`,`categories`),
  FULLTEXT KEY `church_event_tags` (`tags`),
  FULLTEXT KEY `church_event_categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_event_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_cmts` (
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
   
CREATE TABLE IF NOT EXISTS `modzzz_church_event_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_event', 'modzzz_church_event_rating', 'modzzz_church_event_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_event_main', 'rate', 'rate_count', 'id', 'BxChurchEventVoting', 'modules/modzzz/church/classes/BxChurchEventVoting.php');
   
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_event', 'modzzz_church_event_cmts', 'modzzz_church_event_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_event_main', 'id', 'comments_count', 'BxChurchEventCmts', 'modules/modzzz/church/classes/BxChurchEventCmts.php');
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/edit/{ID}'';', '0', 'modzzz_church_event'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/delete/{ID}'';', '1', 'modzzz_church_event'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/event/{URI}', '', '', '2', 'modzzz_church_event'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/event/{URI}', '', '', '3', 'modzzz_church_event'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/event/{URI}', '', '', '4', 'modzzz_church_event'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/event/{URI}', '', '', '5', 'modzzz_church_event');  


SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_events_view', 'Church Event View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_events_browse', 'Church Event Browse', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_events_browse', '1140px', 'Church Event''s browse block', '_modzzz_church_block_browse_events', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_events_view', '1140px', 'Church Event''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_events_view', '1140px', 'Church Event''s info block', '_modzzz_church_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_events_view', '1140px', 'Church Event''s rate block', '_modzzz_church_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_events_view', '1140px', 'Church Event''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_events_view', '1140px', 'Church Event''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_church_events_view', '1140px', 'Church Event''s video block', '_modzzz_church_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_events_view', '1140px', 'Church Event''s files block', '_modzzz_church_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_events_view', '1140px', 'Church Event''s sounds block', '_modzzz_church_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_events_view', '1140px', 'Church Event''s comments block', '_modzzz_church_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');   
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleEventAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/add/{ID}'';', '15', 'modzzz_church');
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/event/add/|modules/?r=church/event/edit/|modules/?r=church/event/view/|modules/?r=church/event/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

-- *********[END] EVENTS ************ 
  
-- *********[BEGIN] Sermon ************ 
  
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL,
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `church_sermon_uri` (`uri`),
  KEY `church_sermon_author_id` (`author_id`),
  KEY `church_sermon_created` (`created`),
  FULLTEXT KEY `church_sermon_title` (`title`,`desc`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 
   
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_cmts` (
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
   
CREATE TABLE IF NOT EXISTS `modzzz_church_sermon_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_sermon', 'modzzz_church_sermon_rating', 'modzzz_church_sermon_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_sermon_main', 'rate', 'rate_count', 'id', 'BxChurchSermonVoting', 'modules/modzzz/church/classes/BxChurchSermonVoting.php');
   
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_sermon', 'modzzz_church_sermon_cmts', 'modzzz_church_sermon_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_sermon_main', 'id', 'comments_count', 'BxChurchSermonCmts', 'modules/modzzz/church/classes/BxChurchSermonCmts.php');
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sermon/edit/{ID}'';', '0', 'modzzz_church_sermon'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sermon/delete/{ID}'';', '1', 'modzzz_church_sermon'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/sermon/{URI}', '', '', '2', 'modzzz_church_sermon'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/sermon/{URI}', '', '', '3', 'modzzz_church_sermon'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/sermon/{URI}', '', '', '4', 'modzzz_church_sermon'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/sermon/{URI}', '', '', '5', 'modzzz_church_sermon');   


SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_sermons_view', 'Church Sermon View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_sermons_browse', 'Church Sermon Browse', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_sermons_browse', '1140px', 'Church Sermon''s browse block', '_modzzz_church_block_browse_sermons', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s info block', '_modzzz_church_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s rate block', '_modzzz_church_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s video block', '_modzzz_church_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s files block', '_modzzz_church_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s sounds block', '_modzzz_church_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_sermons_view', '1140px', 'Church Sermon''s comments block', '_modzzz_church_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');   
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleSermonAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''sermon/add/{ID}'';', '15', 'modzzz_church');
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/sermon/add/|modules/?r=church/sermon/edit/|modules/?r=church/sermon/view/|modules/?r=church/sermon/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

-- *********[END] Sermon ************ 



-- *********[BEGIN] MEMBERS ************ 
CREATE TABLE IF NOT EXISTS `modzzz_church_members_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `position` varchar(100) NOT NULL,  
  `status` enum('approved','pending') NOT NULL default 'approved', 
  `icon` varchar(255) NOT NULL, 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,  
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3',
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL, 
  `allow_upload_videos_to` varchar(16) NOT NULL, 
  `allow_upload_files_to` varchar(16) NOT NULL, 
  `allow_upload_sounds_to` varchar(16) NOT NULL, 
  PRIMARY KEY (`id`),
  UNIQUE KEY `church_members_uri` (`uri`),
  KEY `church_members_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_members_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_church_members_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_members_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_members_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_members_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_church_members_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_church_members_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_church_members_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_members', 'modzzz_church_members_rating', 'modzzz_church_members_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_members_main', 'rate', 'rate_count', 'id', 'BxChurchMembersVoting', 'modules/modzzz/church/classes/BxChurchMembersVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_members', 'modzzz_church_members_cmts', 'modzzz_church_members_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_members_main', 'id', 'comments_count', 'BxChurchMembersCmts', 'modules/modzzz/church/classes/BxChurchMembersCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''members/edit/{ID}'';', '0', 'modzzz_church_members'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''members/delete/{ID}'';', '1', 'modzzz_church_members'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/members/{URI}', '', '', '2', 'modzzz_church_members'), 
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/members/{URI}', '', '', '3', 'modzzz_church_members'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/members/{URI}', '', '', '4', 'modzzz_church_members'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/members/{URI}', '', '', '5', 'modzzz_church_members');  
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_members_view', 'Church Members View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_members_browse', 'Church Members Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_church_members_browse', '1140px', 'Church Members''s browse block', '_modzzz_church_block_browse_members', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_church_members_view', '1140px', 'Church Members''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_members_view', '1140px', 'Church Members''s rate block', '_modzzz_church_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_members_view', '1140px', 'Church Members''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_members_view', '1140px', 'Church Members''s photos block', '_modzzz_church_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_members_view', '1140px', 'Church Members''s comments block', '_modzzz_church_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');   
 
 

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleMembersAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''members/add/{ID}'';', '14', 'modzzz_church');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/members/add/|modules/?r=church/members/edit/|modules/?r=church/members/view/|modules/?r=church/members/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

-- *********[END] MEMBERS *********** 

UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/upload_photos_subprofile') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';


-- [begin] review
CREATE TABLE IF NOT EXISTS `[db_prefix]review_main` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `church_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending','draft') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `publish` int(11) NOT NULL default '1',
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  `anonymous` int(11) NOT NULL default '0',
  `votes` int(11) NOT NULL default '0',  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_cmts` (
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
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_church_review_views_tk` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church_review', 'modzzz_church_review_rating', 'modzzz_church_review_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_church_review_main', 'rate', 'rate_count', 'id', 'BxChurchReviewVoting', 'modules/modzzz/church/classes/BxChurchReviewVoting.php');
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church_review', 'modzzz_church_review_cmts', 'modzzz_church_review_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_church_review_main', 'id', 'comments_count', 'BxChurchReviewCmts', 'modules/modzzz/church/classes/BxChurchReviewCmts.php');
 
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_church_review', 'modzzz_church_review_views_tk', 86400, 'modzzz_church_review_main', 'id', 'views', 1);
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/edit/{ID}'';', '0', 'modzzz_church_review'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/delete/{ID}'';', '1', 'modzzz_church_review'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/review/{URI}', '', '', '2', 'modzzz_church_review'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/review/{URI}', '', '', '4', 'modzzz_church_review'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/review/{URI}', '', '', '5', 'modzzz_church_review'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/review/{URI}', '', '', '6', 'modzzz_church_review'),
    ('{TitlePostReview}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/add/{ID}'';', '18', 'modzzz_church');
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church post reviews', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);



UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/review/add/|modules/?r=church/review/edit/|modules/?r=church/review/view/|modules/?r=church/review/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_reviews_browse', 'Church Reviews Browse', @iMaxOrder+11);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_reviews_view', 'Church Reviews View', @iMaxOrder+12);


INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 

    ('modzzz_church_reviews_browse', '1140px', 'Church Review''s browse block', '_modzzz_church_block_browse_reviews', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s rate block', '_modzzz_church_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s info block', '_modzzz_church_block_info', '2', '3', 'Info', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s description block', '_modzzz_church_block_details', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s photos block', '_modzzz_church_block_photo', '1', '2', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s video embed block', '_modzzz_church_block_video_embed', '1', '3', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s videos block', '_modzzz_church_block_videos', '1', '4', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s sounds block', '_modzzz_church_block_sounds', '1', '5', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s files block', '_modzzz_church_block_files', '1', '6', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_reviews_view', '1140px', 'Church Review''s comments block', '_modzzz_church_block_comments', '1', '7', 'Comments', '', '1', '71.9', 'non,memb', '0');   


-- [end] review

-- [begin] faqs
CREATE TABLE IF NOT EXISTS `[db_prefix]faq_main` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL, 
  `church_id` int(11) NOT NULL,
  `status` enum('approved','pending','draft') NOT NULL default 'approved', 
  `created` int(11) NOT NULL,
  `allow_view_faq_to` varchar(16) NOT NULL, 
  `author_id` int(10) unsigned NOT NULL default '0', 
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]faq_items` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `faq_id` int(11) NOT NULL,
  `church_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL default '',
  `answer` text NOT NULL default '', 
  `created` int(11) NOT NULL, 
  PRIMARY KEY (`id`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES    
('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''faq/delete/{ID}'';', '1', 'modzzz_church_faq'),  
('{TitlePostFaq}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''faq/add/{ID}'';', '17', 'modzzz_church');

INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('church', 'view_faq', '_modzzz_church_privacy_view_faq', '3');
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church create faqs', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=church/faq/add/|modules/?r=church/faq/edit/|modules/?r=church/faq/view/|modules/?r=church/faq/browse/') WHERE `Parent`=0 AND `Name`='Church' AND `Type`='system' AND `Caption`='_modzzz_church_menu_root';

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_faqs_browse', 'Church faqs Browse', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_faqs_view', 'Church faqs View', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
 
    ('modzzz_church_faqs_browse', '1140px', 'Church FAQ''s browse block', '_modzzz_church_block_browse_faqs', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
    ('modzzz_church_faqs_view', '1140px', 'Church FAQ''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_faqs_view', '1140px', 'Church FAQ''s items block', '_modzzz_church_block_faqs_items', '1', '0', 'FaqItems', '', '1', '71.9', 'non,memb', '0');
  
-- [end] faqs




-- create tables 
CREATE TABLE IF NOT EXISTS `[db_prefix]packages` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `days` int(11) NOT NULL,
  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `videos` int(11) NOT NULL default '0', 
  `photos` int(11) NOT NULL default '0', 
  `sounds` int(11) NOT NULL default '0', 
  `files` int(11) NOT NULL default '0', 
  `featured` int(11) NOT NULL default '0', 
  `status` enum('active','pending') collate utf8_unicode_ci NOT NULL default 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;

CREATE TABLE IF NOT EXISTS `[db_prefix]invoices` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `invoice_no` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `days` int(11) unsigned NOT NULL,
  `church_id` int(11) unsigned NOT NULL,
  `package_id` int(11) unsigned NOT NULL,
  `invoice_status` enum('pending','paid') NOT NULL default 'pending',
  `invoice_due_date` int(11) NOT NULL,
  `invoice_expiry_date` int(11) NOT NULL,
  `invoice_date` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `[db_prefix]orders` (
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
  
CREATE TABLE IF NOT EXISTS `[db_prefix]claim` (
  `ID` int(10) unsigned NOT NULL auto_increment, 
  `church_id` int(11) NOT NULL, 
  `member_id` int(11) NOT NULL,
  `message` text NOT NULL, 
  `claim_date` int(11) NOT NULL default '0',
  `assign_date` int(11) NOT NULL default '0',
  `processed` int(11) NOT NULL default '0', 
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `paypal` varchar(100) NOT NULL default '',
  `paypal_amount` float NOT NULL, 
  `history` text NOT NULL,
  `believe` text NOT NULL,
  `service_hours` text NOT NULL, 
  `country` varchar(2) NOT NULL,
  `city` varchar(150) NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `address2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `state` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `zip` varchar(20) NOT NULL,
  `status` ENUM( 'approved', 'pending', 'expired' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '', 
  `capacity` varchar(255) NOT NULL default '',  
  `dress_code` varchar(255) NOT NULL default '',  
  `categories` text NOT NULL,
  `category_id` int(11) NOT NULL, 
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `featured_expiry_date`  INT NOT NULL,
  `featured_date` INT NOT NULL, 
  `allow_post_in_forum_to` varchar(16) NOT NULL, 
  `allow_view_church_to` int(11) NOT NULL,
  `allow_view_fans_to` varchar(16) NOT NULL,
  `allow_join_to` int(11) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL, 
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_view_donors_to` varchar(16) NOT NULL,   
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  `membership_view_filter` varchar(100) NOT NULL default '', 
  `votes` int(11) NOT NULL default '0',    
  `pre_expire_notify` int(11) NOT NULL, 
  `post_expire_notify` int(11) NOT NULL,   
  `businessname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `businesswebsite` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `businessemail` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `businesstelephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `businessfax` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `expiry_date` int(11) NOT NULL default '0',
  `invoice_no` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `video_embed` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc`,`tags`),
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
   
CREATE TABLE IF NOT EXISTS `[db_prefix]categ` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `uri` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `active` int(11) NOT NULL default '1',
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]featured_orders` (
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
  
-- Dumping data for table `[db_prefix]categ`  
INSERT INTO `[db_prefix]categ` (`id`, `parent`, `name`, `uri`, `icon`, `active`) VALUES
(1, 0, 'Christianity', 'Christianity', '', 1),
(2, 0, 'East Asian religions', 'East-Asian-religions', '', 1),
(3, 0, 'Indian religions', 'Indian-religions', '', 1),
(4, 0, 'Indigenous traditional religions', 'Indigenous-traditional-religions', '', 1),
(5, 0, 'Iranian religions', 'Iranian-religions', '', 1),
(6, 0, 'Islam', 'Islam', '', 1),
(7, 0, 'Judaism', 'Judaism', '', 1),
(8, 0, 'New Age', 'New-Age', '', 1), 
(9, 0, 'Other', 'Other', '', 1);
 


INSERT INTO `[db_prefix]categ` ( `parent`, `name`, `uri`, `icon`, `active`) VALUES

(1, 'African Methodist Episcopal', 'African-Methodist-Episcopal', '', 1),
(1, 'African Methodist Episcopal Zion', 'African-Methodist-Episcopal-Zion', '', 1),
(1, 'African Orthodox Church', 'African-Orthodox-Church', '', 1),
(1, 'American Baptist Churches USA', 'American-Baptist-Churches-USA', '', 1),
(1, 'Amish', 'Amish', '', 1),
(1, 'Anabaptist', 'Anabaptist', '', 1),
(1, 'Anglican Catholic Church', 'Anglican-Catholic-Church', '', 1),
(1, 'Anglican Church', 'Anglican-Church', '', 1),
(1, 'Antiochian Orthodox', 'Antiochian-Orthodox', '', 1),
(1, 'Armenian Evangelical Church', 'Armenian-Evangelical-Church', '', 1),
(1, 'Armenian Orthodox', 'Armenian-Orthodox', '', 1),
(1, 'Assemblies of God', 'Assemblies-of-God', '', 1),
(1, 'Associated Gospel Churches of Canada', 'Associated-Gospel-Churches-of-Canada', '', 1),
(1, 'Association of Vineyard Churches', 'Association-of-Vineyard-Churches', '', 1),
(1, 'Baptist', 'Baptist', '', 1),
(1, 'Baptist Bible Fellowship', 'Baptist-Bible-Fellowship', '', 1),
(1, 'Branch Davidian', 'Branch-Davidian', '', 1),
(1, 'Brethren in Christ', 'Brethren-in-Christ', '', 1),
(1, 'Bruderhof Communities', 'Bruderhof-Communities', '', 1),
(1, 'Byzantine Catholic Church', 'Byzantine-Catholic-Church', '', 1),
(1, 'Calvary Chapel', 'Calvary-Chapel', '', 1),
(1, 'Calvinist', 'Calvinist', '', 1),
(1, 'Catholic', 'Catholic', '', 1),
(1, 'Cell Church', 'Cell-Church', '', 1),
(1, 'Celtic Orthodox', 'Celtic-Orthodox', '', 1),
(1, 'Charismatic Episcopal Church', 'Charismatic-Episcopal-Church', '', 1),
(1, 'Christadelphian', 'Christadelphian', '', 1),
(1, 'Christian and Missionary Alliance', 'Christian-and-Missionary-Alliance', '', 1),
(1, 'Christian Churches of God', 'Christian-Churches-of-God', '', 1),
(1, 'Christian Identity', 'Christian-Identity', '', 1),
(1, 'Christian Reformed Church', 'Christian-Reformed-Church', '', 1),
(1, 'Christian Science', 'Christian-Science', '', 1),
(1, 'Church of God (Anderson)', 'Church-of-God-(Anderson)', '', 1),
(1, 'Church of God (Cleveland)', 'Church-of-God-(Cleveland)', '', 1),
(1, 'Church of God (Seventh Day)', 'Church-of-God-(Seventh-Day)', '', 1),
(1, 'Church of God in Christ', 'Church-of-God-in-Christ', '', 1),
(1, 'Church of God of Prophecy', 'Church-of-God-of-Prophecy', '', 1),
(1, 'Church of Jesus Christ of Latter-day Saints', 'Church-of-Jesus-Christ-of-Latter-day-Saints', '', 1),
(1, 'Church of Scotland', 'Church-of-Scotland', '', 1),
(1, 'Church of South India', 'Church-of-South-India', '', 1),
(1, 'Church of the Brethren', 'Church-of-the-Brethren', '', 1),
(1, 'Church of the Lutheran Brethren of America', 'Church-of-the-Lutheran-Brethren-of-America', '', 1),
(1, 'Church of the Nazarene', 'Church-of-the-Nazarene', '', 1),
(1, 'Church of the New Jerusalem', 'Church-of-the-New-Jerusalem', '', 1),
(1, 'Church of the United Brethren in Christ', 'Church-of-the-United-Brethren-in-Christ', '', 1),
(1, 'Church Universal and Triumphant', 'Church-Universal-and-Triumphant', '', 1),
(1, 'Churches of Christ', 'Churches-of-Christ', '', 1),
(1, 'Churches of God General Conference', 'Churches-of-God-General-Conference', '', 1),
(1, 'Congregational Christian Churches', 'Congregational-Christian-Churches', '', 1),
(1, 'Coptic Orthodox', 'Coptic-Orthodox', '', 1),
(1, 'Cumberland Presbyterian Church', 'Cumberland-Presbyterian-Church', '', 1),
(1, 'Disciples of Christ', 'Disciples-of-Christ', '', 1),
(1, 'Episcopal Church', 'Episcopal-Church', '', 1),
(1, 'Ethiopian Orthodox Tewahedo Church', 'Ethiopian-Orthodox-Tewahedo-Church', '', 1),
(1, 'Evangelical Congregational Church', 'Evangelical-Congregational-Church', '', 1),
(1, 'Evangelical Covenant Church', 'Evangelical-Covenant-Church', '', 1),
(1, 'Evangelical Formosan Church', 'Evangelical-Formosan-Church', '', 1),
(1, 'Evangelical Free Church', 'Evangelical-Free-Church', '', 1),
(1, 'Evangelical Lutheran Church', 'Evangelical-Lutheran-Church', '', 1),
(1, 'Evangelical Methodist Church', 'Evangelical-Methodist-Church', '', 1),
(1, 'Evangelical Presbyterian', 'Evangelical-Presbyterian', '', 1),
(1, 'Family, The (aka Children of God)', 'Family,-The-(aka-Children-of-God)', '', 1),
(1, 'Fellowship of Christian Assemblies', 'Fellowship-of-Christian-Assemblies', '', 1),
(1, 'Fellowship of Grace Brethren', 'Fellowship-of-Grace-Brethren', '', 1),
(1, 'Fellowship of Independent Evangelical Churches', 'Fellowship-of-Independent-Evangelical-Churches', '', 1),
(1, 'Free Church of Scotland', 'Free-Church-of-Scotland', '', 1),
(1, 'Free Methodist', 'Free-Methodist', '', 1),
(1, 'Free Presbyterian', 'Free-Presbyterian', '', 1),
(1, 'Free Will Baptist', 'Free-Will-Baptist', '', 1),
(1, 'Gnostic', 'Gnostic', '', 1),
(1, 'Great Commission Association of Churches', 'Great-Commission-Association-of-Churches', '', 1),
(1, 'Greek Orthodox', 'Greek-Orthodox', '', 1),
(1, 'Hutterian Brethren', 'Hutterian-Brethren', '', 1),
(1, 'Independent Fundamental Churches of America', 'Independent-Fundamental-Churches-of-America', '', 1),
(1, 'Indian Orthodox', 'Indian-Orthodox', '', 1),
(1, 'International Church of the Foursquare Gospel', 'International-Church-of-the-Foursquare-Gospel', '', 1),
(1, 'International Churches of Christ', 'International-Churches-of-Christ', '', 1),
(1, 'Jehovah''s Witnesses', 'Jehovahs-Witnesses', '', 1),
(1, 'Living Church of God', 'Living-Church-of-God', '', 1),
(1, 'Local Church', 'Local-Church', '', 1),
(1, 'Lutheran', 'Lutheran', '', 1),
(1, 'Lutheran Church - Missouri Synod', 'Lutheran-Church---Missouri-Synod', '', 1),
(1, 'Mar Thoma Syrian Church', 'Mar-Thoma-Syrian-Church', '', 1),
(1, 'Mennonite', 'Mennonite', '', 1),
(1, 'Messianic Judaism', 'Messianic-Judaism', '', 1),
(1, 'Methodist', 'Methodist', '', 1),
(1, 'Moravian Church', 'Moravian-Church', '', 1),
(1, 'Nation of Yahweh', 'Nation-of-Yahweh', '', 1),
(1, 'New Frontiers International', 'New-Frontiers-International', '', 1),
(1, 'Old Catholic Church', 'Old-Catholic-Church', '', 1),
(1, 'Orthodox', 'Orthodox', '', 1),
(1, 'Orthodox Church in America', 'Orthodox-Church-in-America', '', 1),
(1, 'Orthodox Presbyterian', 'Orthodox-Presbyterian', '', 1),
(1, 'Pentecostal', 'Pentecostal', '', 1),
(1, 'Plymouth Brethren', 'Plymouth-Brethren', '', 1),
(1, 'Presbyterian', 'Presbyterian', '', 1),
(1, 'Presbyterian Church', 'Presbyterian-Church', '', 1),
(1, 'Presbyterian Church in America', 'Presbyterian-Church-in-America', '', 1),
(1, 'Primitive Baptist', 'Primitive-Baptist', '', 1),
(1, 'Protestant Reformed Church', 'Protestant-Reformed-Church', '', 1),
(1, 'Reformed', 'Reformed', '', 1),
(1, 'Reformed Baptist', 'Reformed-Baptist', '', 1),
(1, 'Reformed Church in America', 'Reformed-Church-in-America', '', 1),
(1, 'Reformed Church in the United States', 'Reformed-Church-in-the-United-States', '', 1),
(1, 'Reformed Churches of Australia', 'Reformed-Churches-of-Australia', '', 1),
(1, 'Reformed Episcopal Church', 'Reformed-Episcopal-Church', '', 1),
(1, 'Reformed Presbyterian Church', 'Reformed-Presbyterian-Church', '', 1),
(1, 'Reorganized Church of Jesus Christ of Latter Day Saints', 'Reorganized-Church-of-Jesus-Christ-of-Latter-Day-Saints', '', 1),
(1, 'Revival Centres International', 'Revival-Centres-International', '', 1),
(1, 'Romanian Orthodox', 'Romanian-Orthodox', '', 1),
(1, 'Rosicrucian', 'Rosicrucian', '', 1),
(1, 'Russian Orthodox', 'Russian-Orthodox', '', 1),
(1, 'Serbian Orthodox', 'Serbian-Orthodox', '', 1),
(1, 'Seventh Day Baptist', 'Seventh-Day-Baptist', '', 1),
(1, 'Seventh-Day Adventist', 'Seventh-Day-Adventist', '', 1),
(1, 'Shaker', 'Shaker', '', 1),
(1, 'Society of Friends', 'Society-of-Friends', '', 1),
(1, 'Southern Baptist Convention', 'Southern-Baptist-Convention', '', 1),
(1, 'Spiritist', 'Spiritist', '', 1),
(1, 'Syrian Orthodox', 'Syrian-Orthodox', '', 1),
(1, 'True and Living Church of Jesus Christ of Saints of the Last Days', 'True-and-Living-Church-of-Jesus-Christ-of-Saints-of-the-Last-Days', '', 1),
(1, 'Two-by-Twos', 'Two-by-Twos', '', 1),
(1, 'Unification Church', 'Unification-Church', '', 1),
(1, 'Unitarian-Universalism', 'Unitarian-Universalism', '', 1),
(1, 'United Church of Canada', 'United-Church-of-Canada', '', 1),
(1, 'United Church of Christ', 'United-Church-of-Christ', '', 1),
(1, 'United Church of God', 'United-Church-of-God', '', 1),
(1, 'United Free Church of Scotland', 'United-Free-Church-of-Scotland', '', 1),
(1, 'United Methodist Church', 'United-Methodist-Church', '', 1),
(1, 'United Reformed Church', 'United-Reformed-Church', '', 1),
(1, 'Uniting Church in Australia', 'Uniting-Church-in-Australia', '', 1),
(1, 'Unity Church', 'Unity-Church', '', 1),
(1, 'Unity Fellowship Church', 'Unity-Fellowship-Church', '', 1),
(1, 'Universal Fellowship of Metropolitan Community Churches', 'Universal-Fellowship-of-Metropolitan-Community-Churches', '', 1),
(1, 'Virtual Churches', 'Virtual-Churches', '', 1),
(1, 'Waldensian Church', 'Waldensian-Church', '', 1),
(1, 'Way International, The', 'Way-International,-The', '', 1),
(1, 'Web Directories', 'Web-Directories', '', 1),
(1, 'Wesleyan', 'Wesleyan', '', 1),
(1, 'Wesleyan Methodist', 'Wesleyan-Methodist', '', 1),
(1, 'Worldwide Church of God', 'Worldwide-Church-of-God', '', 1), 

(2, 'Confucianism', 'Confucianism', '', 1),
(2, 'Shinto', 'Shinto', '', 1),
(2, 'Taoism', 'Taoism', '', 1),
(2, 'Other', 'Other', '', 1),

(3, 'Ayyavazhi', 'Ayyavazhi', '', 1),
(3, 'Bhakti Movement', 'Bhakti-Movement', '', 1),
(3, 'Buddhism', 'Buddhism', '', 1),
(3, 'Din-i-Ilahi', 'Din-i-Ilahi', '', 1),
(3, 'Hinduism', 'Hinduism', '', 1),
(3, 'Jainism', 'Jainism', '', 1),
(3, 'Sikhism', 'Sikhism', '', 1),

(4, 'African', 'African', '', 1),
(4, 'American', 'American', '', 1),
(4, 'Eurasian', 'Eurasian', '', 1),
(4, 'Oceania/Pacific', 'Oceania-Pacific', '', 1),
(4, 'Cargo cults', 'Cargo-cults', '', 1),

(5, 'Manichaeism', 'Manichaeism', '', 1),
(5, 'Mazdakism', 'Mazdakism', '', 1),
(5, 'Mithraism', 'Mithraism', '', 1),
(5, 'Yazdanism', 'Yazdanism', '', 1),
(5, 'Zoroastrianism', 'Zoroastrianism', '', 1),

(6, 'Sunni Islam', 'Sunni-Islam', '', 1),
(6, 'Shia Islam', 'Shia-Islam', '', 1),
(6, 'Sufism', 'Sufism', '', 1),
(6, 'Kharijite Islam', 'Kharijite-Islam', '', 1),
(6, 'Other', 'Other', '', 1),

(7, 'Orthodox Judaism', 'Orthodox-Judaism', '', 1),
(7, 'Conservative Judaism', 'Conservative-Judaism', '', 1),	
(7, 'Reform Judaism', 'Reform-Judaism', '', 1),
(7, 'Reconstructionist Judaism', 'Reconstructionist-Judaism', '', 1),
(7, 'Humanistic Judaism', 'Humanistic-Judaism', '', 1),
 
(8, 'Esotericism', 'Esotericism', '', 1),
(8, 'Magical', 'magical', '', 1),
(8, 'Mysticism', 'mysticism', '', 1),
(8, 'Occult', 'Occult', '', 1),
(8, 'Left-Hand Path', 'Left-Hand-Path', '', 1),
 
(9, 'Babism', 'Babism', '', 1),
(9, 'Baha''i Faith', 'Bahai-Faith', '', 1),
(9, 'Gnosticism', 'Gnosticism', '', 1), 
(9, 'Mandaeans and Sabians', 'Mandaeans-and-Sabians', '', 1),
(9, 'Neopaganism', 'Neopaganism', '', 1),
(9, 'Rastafari movement', 'Rastafari-movement', '', 1); 
   


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
(1, 'Church', 'Church', '', 64);

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
  
ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE `sys_stat_member` CHANGE `Type` `Type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- page compose pages
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_view', 'Church View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_celendar', 'Church Calendar', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_main', 'Church Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_my', 'Church My', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_category', 'Church Category', @iMaxOrder+6);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_subcategory', 'Church Sub-Category', @iMaxOrder+7);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_local', 'Local Church Page', @iMaxOrder+8);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_local_state', 'Local Church State Page', @iMaxOrder+9);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_church_packages', 'Church Packages', @iMaxOrder+10);
   
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('modzzz_church_view', '1140px', 'Church''s actions block', '_modzzz_church_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_view', '1140px', 'Church''s listed by block', '_modzzz_church_block_listed_by', '2', '1', 'ListedBy', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s info block', '_modzzz_church_block_info', '2', '2', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s rate block', '_modzzz_church_block_rate', '2', '3', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_view', '1140px', 'Church''s business contact block', '_modzzz_church_business_contact', '2', '4', 'BusinessContact', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s location block', '_modzzz_church_location', '2', '5', 'Location', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s fans block', '_modzzz_church_block_fans', '2', '6', 'Fans', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_church_view', '1140px', 'Church''s unconfirmed fans block', '_modzzz_church_block_fans_unconfirmed', '2', '7', 'FansUnconfirmed', '', '1', '28.1', 'memb', '0'),

    ('modzzz_church_view', '1140px', 'Church''s description block', '_modzzz_church_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s believe block', '_modzzz_church_block_believe', '1', '1', 'Believe', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s service hours block', '_modzzz_church_block_service_hours', '1', '2', 'ServiceHours', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s history block', '_modzzz_church_block_history', '1', '3', 'History', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s photo block', '_modzzz_church_block_photo', '1', '5', 'Photo', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s Video Embed block', '_modzzz_church_block_video_embed', '1', '6', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_church_view', '1140px', 'Church''s videos block', '_modzzz_church_block_video', '1', '7', 'Video', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_church_view', '1140px', 'Church''s sounds block', '_modzzz_church_block_sound', '1', '8', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_church_view', '1140px', 'Church''s files block', '_modzzz_church_block_files', '1', '9', 'Files', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_church_view', '1140px', 'Church''s local block', '_modzzz_church_block_local', '1', '10', 'Local', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s other block', '_modzzz_church_block_other', '1', '11', 'Other', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s comments block', '_modzzz_church_block_comments', '1', '12', 'Comments', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_view', '1140px', 'Church''s donors block', '_modzzz_church_block_donors', '1', '13', 'Donors', '', '1', '71.9', 'non,memb', '0'), 
  
    ('modzzz_church_local_state', '1140px', 'Local States', '_modzzz_church_block_browse_state', '2', '0', 'States', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_local_state', '1140px', 'Local State Churches', '_modzzz_church_block_browse_state_churches', '1', '0', 'StateChurches', '', '1', '71.9', 'non,memb', '0'),

    ('modzzz_church_local', '1140px', 'Local Churches', '_modzzz_church_block_browse_country', '1', '0', 'Region', '', '1', '100', 'non,memb', '0'),  
 
    ('modzzz_church_category', '1140px', 'Church', '_modzzz_church_block_church', '1', '0', 'CategoryChurch', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_category', '1140px', 'Church Categories', '_modzzz_church_block_categories', '2', '0', 'Categories', '', '1', '28.1', 'non,memb', '0'),
     
    ('modzzz_church_subcategory', '1140px', 'Church Category Churches', '_modzzz_church_block_category_church', '1', '0', 'SubCategoryChurches', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_subcategory', '1140px', 'Church Sub-Categories', '_modzzz_church_block_subcategories', '2', '0', 'SubCategories', '', '1', '28.1', 'non,memb', '0'),

    ('modzzz_church_main', '1140px', 'Church Create', '_modzzz_church_block_church_create', '2', '0', 'Create', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_main', '1140px', 'Search Church', '_modzzz_church_block_search', '2', '1', 'Search', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_main', '1140px', 'Church Categories', '_modzzz_church_block_church_categories', '2', '2', 'ChurchCategories', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_main', '1140px', 'Church Tags', '_tags_plural', '2', '4', 'Tags', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_church_main', '1140px', 'Calendar', '_modzzz_church_block_calendar', '2', '5', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_church_main', '1140px', 'Church States', '_modzzz_church_block_church_states', '2', '6', 'States', '', '1', '28.1', 'non,memb', '0'), 
     
    ('modzzz_church_main', '1140px', 'Map', '_Map', '1', '0', 'PHP', 'return BxDolService::call(''wmap'', ''homepage_part_block'', array (''church''));', 1, 71.9, 'non,memb', 0), 
    ('modzzz_church_main', '1140px', 'Latest Featured Church', '_modzzz_church_block_latest_featured_church', '1', '1', 'LatestFeaturedChurch', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_main', '1140px', 'Recent Church', '_modzzz_church_block_recent', '1', '2', 'Recent', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_main', '1140px', 'Church Forum Posts', '_modzzz_church_block_forum', '1', '3', 'Forum', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_church_main', '1140px', 'Church Comments', '_modzzz_church_block_latest_comments', '1', '4', 'Comments', '', '1', '71.9', 'non,memb', '0'), 
  
    ('modzzz_church_packages', '1140px', 'Church Packages', '_modzzz_church_block_packages', '1', '0', 'Packages', '', '1', '100', 'non,memb', '0'),  
 
    ('modzzz_church_my', '1140px', 'Administration Owner', '_modzzz_church_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('modzzz_church_my', '1140px', 'User''s church', '_modzzz_church_block_users_church', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('index', '1140px', 'Churches', '_modzzz_church_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''church'', ''homepage_block'');', 1, 71.9, 'non,memb', 0),
    ('member', '1140px', 'My Churches', '_modzzz_church_block_my_churches', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''church'', ''accountpage_block'');', 1, 71.9, 'non,memb', 0),  
    ('member', '1140px', 'Local Churches', '_modzzz_church_block_area_churches', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''church'', ''accountarea_block'');', 1, 71.9, 'non,memb', 0),  
    ('profile', '1140px', 'User Churches', '_modzzz_church_block_my_church', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''church'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0);


-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=church/', 'm/church/', 'modzzz_church_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Church', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_church_permalinks', 'on', 26, 'Enable friendly permalinks in church', 'checkbox', '', '', '0', ''),
('modzzz_church_autoapproval', 'on', @iCategId, 'Activate all church after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_church_author_comments_admin', 'on', @iCategId, 'Allow church admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('modzzz_church_perpage_main_recent', '10', @iCategId, 'Number of recently added churches to show on church home', 'digit', '', '', '0', ''),
('modzzz_church_perpage_main_featured', '5', @iCategId, 'Number of featured churches to show on church home', 'digit', '', '', '0', ''),
('modzzz_church_perpage_browse', '14', @iCategId, 'Number of church to show on browse pages', 'digit', '', '', '0', ''),
('modzzz_church_perpage_profile', '4', @iCategId, 'Number of church to show on profile page', 'digit', '', '', '0', ''),
('modzzz_church_perpage_accountpage', '4', @iCategId, 'Number of church to show on account page', 'digit', '', '', '0', ''),
('modzzz_church_perpage_homepage', '5', @iCategId, 'Number of church to show on homepage', 'digit', '', '', '0', ''),
('modzzz_church_homepage_default_tab', 'featured', @iCategId, 'Default church block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),
('modzzz_church_perpage_view_fans', '6', @iCategId, 'Number of fans to show on church view page', 'digit', '', '', '0', ''),
('modzzz_church_perpage_browse_fans', '30', @iCategId, 'Number of fans to show on browse fans page', 'digit', '', '', '0', ''),
('modzzz_church_title_length', '100', @iCategId, 'Max length of title', 'digit', '', '', '0', ''),
('modzzz_church_snippet_length', '300', @iCategId, 'The length of description snippet', 'digit', '', '', '0', ''),   
('modzzz_church_max_preview', '200', @iCategId, 'Length of church description snippet to show in blocks', 'digit', '', '', '0', ''),
('modzzz_church_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', ''), 
('modzzz_church_free_expired', '0', @iCategId, 'number of days before free church expires (0-never expires)', 'digit', '', '', '0', ''), 
('modzzz_church_activate_expiring', 'on', @iCategId, 'activate sending email notification of soon to expire churches', 'checkbox', '', '', '0', ''),  
('modzzz_church_activate_expired', 'on', @iCategId, 'activate sending email notification of expired churches', 'checkbox', '', '', '0', ''), 
('modzzz_church_email_expiring', '3', @iCategId, 'number of days before expiry to send email notification (0=same day)', 'digit', '', '', '0', ''), 
('modzzz_church_email_expired', '3', @iCategId, 'number of days after expiry to send email notification (0=same day)', 'digit', '', '', '0', ''),  
('modzzz_church_max_email_invitations', '10', @iCategId, 'Max number of email to send per one promotion', 'digit', '', '', '0', ''),

('modzzz_church_forum_max_preview', '150', @iCategId, 'length of forum post snippet to show on main page', 'digit', '', '', '0', ''),
('modzzz_church_comments_max_preview', '150', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
('modzzz_church_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''), 
('modzzz_church_per_page', '7', @iCategId, 'Churches search results to show per page', 'digit', '', '', '0', ''),
 
('modzzz_church_perpage_view_subitems', '6', @iCategId, 'Number of sub-items (News,Events etc.) to show on church main/view page', 'digit', '', '', '0', ''),
('modzzz_church_perpage_browse_subitems', '30', @iCategId, 'Number of sub-items (News,Events etc.) to show on the sub section browse page', 'digit', '', '', '0', ''),   

('modzzz_church_default_country', 'US', @iCategId, 'default country for location', 'digit', '', '', 0, ''),
 
('modzzz_church_allow_embed', 'on', @iCategId, 'Allow video embed when video upload is disabled', 'checkbox', '', '', '0', ''),  

('modzzz_church_featured_cost', '0', @iCategId, 'Cost per day for Featured Status', 'digit', '', '', 0, ''),
('modzzz_church_featured_purchase_desc', 'Purchase Featured Church Status', @iCategId, 'Item decription displayed on PayPal Featured Church Purchase', 'digit', '', '', 0, ''),
('modzzz_church_buy_featured', '', @iCategId, 'Enable Paypal purchase of Featured Status', 'checkbox', '', '', 0, ''), 
 
('modzzz_church_paypal_email', '', @iCategId, 'Paypal Email', 'digit', '', '', 0, ''),
('modzzz_church_paid_active', '', @iCategId, 'Activate Paid Churches',  'checkbox', '', '', 0, ''), 
('modzzz_church_currency_code', 'USD', @iCategId, 'Currency code for checkout system (eg. USD,EURO,GBP)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_church_currency_sign', '&#36;', @iCategId, 'Currency sign (for display purposes only)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_church_paypal_item_desc', 'Church Package purchase', @iCategId, 'Item decription displayed on PayPal Package Purchase', 'digit', '', '', 0, ''), 
('modzzz_church_invoice_valid_days', '100', @iCategId, 'Number of Days before pending Invoices expire<br>(blank or zero means no expiration)', 'digit', '', '', 0, '') 
;
 

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'modzzz_church', '_modzzz_church', 'BxChurchSearchResult', 'modules/modzzz/church/classes/BxChurchSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_church', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxChurchVoting', 'modules/modzzz/church/classes/BxChurchVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_church', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxChurchCmts', 'modules/modzzz/church/classes/BxChurchCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_church', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'modzzz_church', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_church_permalinks', 'm/church/browse/tag/{tag}', 'modules/?r=church/browse/tag/{tag}', '_modzzz_church');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'modzzz_church', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_church_permalinks', 'm/church/browse/category/{tag}', 'modules/?r=church/browse/category/{tag}', '_modzzz_church');
   
 
-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'modzzz_church'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'modzzz_church'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'modzzz_church'),
    ('{TitleBroadcast}', 'envelope', '{BaseUri}broadcast/{ID}', '', '', '3', 'modzzz_church'),  
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '6', 'modzzz_church'),
    ('{TitleJoin}', '{IconJoin}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '7', 'modzzz_church'),
    ('{TitleManageFans}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '8', 'modzzz_church'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '9', 'modzzz_church'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '10', 'modzzz_church'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '11', 'modzzz_church'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '12', 'modzzz_church'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '13', 'modzzz_church'),
    ('{TitleInquire}', 'question-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''inquire/{ID}'';', '22', 'modzzz_church'), 
    ('{TitleClaim}', 'key', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''claim/{ID}'';', '23', 'modzzz_church'), 
    ('{TitleInvite}', 'hand-o-right', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '24', 'modzzz_church'), 
    ('{TitlePurchaseFeatured}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''purchase_featured/{ID}'';', '25', 'modzzz_church'),
 
    ('{TitleRelist}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''relist/{ID}'';', '26', 'modzzz_church'), 
    ('{TitleExtend}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''extend/{ID}'';', '27', 'modzzz_church'), 
    ('{TitlePremium}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxChurchModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''premium/{ID}'';', '28', 'modzzz_church'),  
 
    ('{evalResult}', 'plus', '{BaseUri}browse/my&filter=add_church', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_church_action_add_church'') : '''';', '1', 'modzzz_church_title'),
    ('{evalResult}', 'home', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_church_action_my_church'') : '''';', '2', 'modzzz_church_title');
   

SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Church', '_modzzz_church_menu_root', 'modules/?r=church/home/|modules/?r=church/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'home', 'home', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Churches Main Page', '_modzzz_church_menu_main', 'modules/?r=church/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Recent Churches', '_modzzz_church_menu_recent', 'modules/?r=church/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Top Rated Churches', '_modzzz_church_menu_top_rated', 'modules/?r=church/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Popular Churches', '_modzzz_church_menu_popular', 'modules/?r=church/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Featured Churches', '_modzzz_church_menu_featured', 'modules/?r=church/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Churches Tags', '_modzzz_church_menu_tags', 'modules/?r=church/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_church');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Churches Categories', '_modzzz_church_menu_categories', 'modules/?r=church/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_church');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Calendar', '_modzzz_church_menu_calendar', 'modules/?r=church/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Local Churches', '_modzzz_church_menu_local', 'modules/?r=church/local', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Search', '_modzzz_church_menu_search', 'modules/?r=church/search', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Church Packages', '_modzzz_church_menu_packages', 'modules/?r=church/packages', 13, 'non,memb', '', '', '$oMain = BxDolModule::getInstance(''BxChurchModule'');return $oMain->isAllowedPaidChurches(false);', 1, 1, 1, 'custom', '', '', 0, '');
 


SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1),1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 9, 'Church', '_modzzz_church_menu_my_church_profile', 'modules/?r=church/browse/user/{profileUsername}|modules/?r=church/browse/joined/{profileUsername}',  
ifnull(@iCatProfileOrder,1), 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1),1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 4, 'Church', '_modzzz_church_menu_my_church_profile', 'modules/?r=church/browse/my', ifnull(@iCatProfileOrder,1), 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_church', '_modzzz_church', '{siteUrl}modules/?r=church/administration/', 'Church module by Modzzz','home', @iMax+1);

-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'modzzz_church', 'modzzz_church', 'modules/?r=church/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=church/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'home', @iStatSiteOrder);


-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('modzzz_church', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('modzzz_churchp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_modzzz_church', '__modzzz_church__ (<a href="modules/?r=church/browse/my&filter=add_church">__l_add__</a>)');
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church broadcast message', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
    
INSERT INTO `sys_acl_actions` VALUES (NULL, 'church extend', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'church relist', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'church purchase', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church photos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'church sounds add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'church videos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'church files add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

  
INSERT INTO `sys_acl_actions` VALUES (NULL, 'church purchase featured', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'church view church', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church add church', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'church comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'church edit any church', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'church delete any church', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'church mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'church approve church', NULL);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'church make claim', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'church make inquiry', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_church_profile_delete', '', '', 'BxDolService::call(''church'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_church_media_delete', '', '', 'BxDolService::call(''church'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);

-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'modzzz_church', `Eval` = 'return BxDolService::call(''church'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES  
('church', 'view_fans', '_modzzz_church_privacy_view_fans', '3'), 
('church', 'view_church', '_modzzz_church_privacy_view_church', '3'), 
('church', 'view_donors', '_modzzz_church_privacy_view_donors', '3'), 
('church', 'comment', '_modzzz_church_privacy_comment', 'f'),
('church', 'rate', '_modzzz_church_privacy_rate', 'f'),
('church', 'post_in_forum', '_modzzz_church_privacy_post_in_forum', 'f'),
('church', 'join', '_modzzz_church_privacy_join', '3'),  
('church', 'upload_photos', '_modzzz_church_privacy_upload_photos', 'a'),
('church', 'upload_videos', '_modzzz_church_privacy_upload_videos', 'a'),
('church', 'upload_sounds', '_modzzz_church_privacy_upload_sounds', 'a'),
('church', 'upload_files', '_modzzz_church_privacy_upload_files', 'a');


-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('modzzz_church', '', '', 'return BxDolService::call(''church'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_church', 'change', 'modzzz_church_sbs', 'return BxDolService::call(''church'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_church', 'commentPost', 'modzzz_church_sbs', 'return BxDolService::call(''church'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_church', 'join', 'modzzz_church_sbs', 'return BxDolService::call(''church'', ''get_subscription_params'', array($arg2, $arg3));');
 

-- email templates 

INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_church_broadcast', '<BroadcastTitle>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<EntryUrl>"><EntryTitle></a> church admin has sent the following broadcast message:</p> <p><BroadcastMessage></p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Church broadcast message', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_inquiry', '<NickName> sent a message about your Church at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has sent a message about your Church, <b><a href="<ListUrl>"><ListTitle></a></b>:</p><pre><Message></pre><bx_include_auto:_email_footer.html />', 'Church Inquiry', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_claim', '<NickName> is claiming ownership of a Church at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has claimed ownership of a Church, <b><a href="<ListUrl>"><ListTitle></a></b>:</p><pre><Message></pre><bx_include_auto:_email_footer.html />', 'Church Claim', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_church_invitation', 'Check out this Church: <ChurchName>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to check out this church:</p> <pre><InvitationText></pre> <p> <b>Church Information:</b><br /> Name: <ChurchName><br /> Location: <ChurchLocation><br /> <a href="<ChurchUrl>">More details</a> </p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Church invitation template', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_claim_assign', 'Your Claim on a Church at <SiteName> is updated', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>A Church, <a href="<ListLink>"><ListTitle></a> that you claimed at <a href="<SiteUrl>"><SiteName></a> has been assigned to you. You now have ownership and administrative rights to the church</p><bx_include_auto:_email_footer.html />', 'Claimed Church assignment notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_expired', 'Your Church at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Church, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired</p><bx_include_auto:_email_footer.html />', 'Expired Church notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_post_expired', 'Message about your expired Church at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Church, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired <b><Days></b> days ago</p><bx_include_auto:_email_footer.html />', 'Post-Expired Church notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_expiring', 'Message about your expiring Church at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Church, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> will expire in <b><Days></b> days<br></p><bx_include_auto:_email_footer.html />', 'Expiring Church notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_featured_expire_notify', 'Your Featured Church Status at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is inform you that your Featured Status for the Church, <a href="<ListLink>"><ListTitle></a> at <SiteName> has expired. You may purchase Featured Status again at any time you desire <br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Church Status Expire Notification', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_featured_admin_notify', 'A member purchased Featured Church Status at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear Administrator</b>,</p>\r\n\r\n<p><a href="<NickLink>"><NickName></a> has just purchased Featured Status for the Church, <a href="<ListLink>"><ListTitle></a>, for <Days> days at <SiteName><br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Church Purchase Admin Notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_church_featured_buyer_notify', 'Your Featured Church Status purchase at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is confirmation of your Featured Status purchase at <SiteName> for Church, <a href="<ListLink>"><ListTitle></a>. It will be Featured for <Days> days<br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Church Purchase Buyer Notification', '0');
  
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
 ('modzzz_church_sbs', 'Church was changed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<ViewLink>"><EntryTitle></a> Church listing was changed: <br /> <ActionName> </p> <p>You may cancel the subscription by clicking the following link: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Church subscription template', '0');
  

INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_church_fan_remove', 'You were removed from fans of a Church', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>You was removed from fans of <a href="<EntryUrl>"><EntryTitle></a> church by church admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User was removed from fans of Church notification message', '0'),
('modzzz_church_fan_become_admin', 'You became admin of a Church', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Congratulations! You become admin of <a href="<EntryUrl>"><EntryTitle></a> church.</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User become admin of a Church notification message', '0'),
('modzzz_church_admin_become_fan', 'Your Church admin status was removed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Your admin status was removed from <a href="<EntryUrl>"><EntryTitle></a> church by church admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User Church admin status was removed notification message', '0'),
('modzzz_church_join_request', 'New join request to your church', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>New join request in your church <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'New join request to a church notification message', '0'),
('modzzz_church_join_reject', 'Your join request to a church was rejected', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Sorry, but your request to join <a href="<EntryUrl>"><EntryTitle></a> church was rejected by church admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Join request to a church was rejected notification message', '0'),
('modzzz_church_join_confirm', 'Your join request to a church was confirmed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Congratulations! Your request to join <a href="<EntryUrl>"><EntryTitle></a> church was confirmed by church admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Join request to a church was confirmed notification message', '0');
 

INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxChurch', '*/5 * * * *', 'BxChurchCron', 'modules/modzzz/church/classes/BxChurchCron.php', '') ;
 

-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('modzzz_church', '_modzzz_church', '0.8', 'auto', 'BxChurchSiteMaps', 'modules/modzzz/church/classes/BxChurchSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('modzzz_church', '_modzzz_church', 'modzzz_church_main', 'created', '', '', 1, @iMaxOrderCharts);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_church_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''church'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_church_event_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''church'', ''event_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
  
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_church_branches_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''church'', ''branches_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
 

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 

('modzzz_church_view', '1140px', 'Church''s social sharing block', '_sys_block_title_social_sharing', 2, 9, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_view', '1140px', 'Church''s Location', '_modzzz_church_block_map', 2, 6, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''church'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),

('modzzz_church_events_view', '1140px', 'Church Event''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_events_view', '1140px', 'Church Event''s Location', '_modzzz_church_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''church_event'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),

('modzzz_church_sermons_view', '1140px', 'Church Sermon''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_doctrines_view', '1140px', 'Church Doctrine''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_staffs_view', '1140px', 'Church Staff''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_faqs_view', '1140px', 'Church Faq''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_members_view', '1140px', 'Church Member''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_news_view', '1140px', 'Church Member''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_ministries_view', '1140px', 'Church Member''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
  
('modzzz_church_branches_view', '1140px', 'Church Branches''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_church_branches_view', '1140px', 'Church Branches''s Location', '_modzzz_church_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''church_branches'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0);
 