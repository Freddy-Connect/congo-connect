ALTER TABLE `sys_menu_top` CHANGE `Link` `Link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
  
ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE `sys_stat_member` CHANGE `Type` `Type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

-- top menu 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Charity', '_modzzz_charity_menu_root', 'modules/?r=charity/view/|modules/?r=charity/edit/|modules/?r=charity/claim/|modules/?r=charity/inquire/|modules/?r=charity/invite/|modules/?r=charity/map_edit/|forum/charity/|modules/?r=charity/relist/|modules/?r=charity/extend/|modules/?r=charity/premium/|modules/?r=charity/purchase_featured/|modules/?r=charity/broadcast/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'money', '', '0', '');

SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View', '_modzzz_charity_menu_view_charity', 'modules/?r=charity/view/{modzzz_charity_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Fans', '_modzzz_charity_menu_view_fans', 'modules/?r=charity/browse_fans/{modzzz_charity_view_uri}', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Comments', '_modzzz_charity_menu_view_comments', 'modules/?r=charity/comments/{modzzz_charity_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Branches', '_modzzz_charity_menu_view_branches', 'modules/?r=charity/branches/browse/{modzzz_charity_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Staffs', '_modzzz_charity_menu_view_staffs', 'modules/?r=charity/staff/browse/{modzzz_charity_view_uri}', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Members', '_modzzz_charity_menu_view_members', 'modules/?r=charity/members/browse/{modzzz_charity_view_uri}', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Programs', '_modzzz_charity_menu_view_programs', 'modules/?r=charity/programs/browse/{modzzz_charity_view_uri}', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Supporters', '_modzzz_charity_menu_view_supporters', 'modules/?r=charity/supporter/browse/{modzzz_charity_view_uri}', 7, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View News', '_modzzz_charity_menu_view_news', 'modules/?r=charity/news/browse/{modzzz_charity_view_uri}', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Events', '_modzzz_charity_menu_view_events', 'modules/?r=charity/event/browse/{modzzz_charity_view_uri}', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Reviews', '_modzzz_charity_menu_view_reviews', 'modules/?r=charity/review/browse/{modzzz_charity_view_uri}', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES (NULL, @iCatRoot, 'Charity View FAQ', '_modzzz_charity_menu_view_faqs', 'modules/?r=charity/faq/browse/{modzzz_charity_view_uri}', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', 'modules/modzzz/location/|modzzz_charity.png', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charity View Forum', '_modzzz_charity_menu_view_forum', 'forum/charity/forum/{modzzz_charity_view_uri}-0.htm|forum/charity/', 13, 'non,memb', '', '', '$oModuleDb = new BxDolModuleDb(); return $oModuleDb->getModuleByUri(''forum'') ? true : false;', 1, 1, 1, 'custom', '', '', 0, '');
 

-- *********[BEGIN] DONATIONS ************ 
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleDonate}', 'gift', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''donation/add/{ID}'';', '30', 'modzzz_charity');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/donation/add/|modules/?r=charity/donation/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity make donation', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity view donors', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_donation_thanks', 'Thank you for the Paypal Donation to Charity - <CharityName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <HeroName></b>,</p>\r\n\r\n<p>On behalf of <a href="<CharityLink>"><CharityName></a>, we would like to say <b>Thank You</b> for your generous donation. Keep up to good work and continue to touch the lives of others in a positive way</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Charity Donation Thank You notification', '0');

 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_donation_notify', 'Donation made to Charity - <CharityName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <OwnerName></b>,</p>\r\n\r\n<p>Your Charity, <a href="<CharityLink>"><CharityName></a>, received a Donation of <Amount> from <a href="<HeroLink>"><HeroName></a>. </p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Charity Donation Received notification', '0');


CREATE TABLE IF NOT EXISTS `[db_prefix]paypal_trans` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `amount` float unsigned NOT NULL, 
  `charity_id` int(11) unsigned NOT NULL,
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
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
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
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_charity_news_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_news', 'modzzz_charity_news_rating', 'modzzz_charity_news_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_news_main', 'rate', 'rate_count', 'id', 'BxCharityNewsVoting', 'modules/modzzz/charity/classes/BxCharityNewsVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_news', 'modzzz_charity_news_cmts', 'modzzz_charity_news_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_news_main', 'id', 'comments_count', 'BxCharityNewsCmts', 'modules/modzzz/charity/classes/BxCharityNewsCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/edit/{ID}'';', '0', 'modzzz_charity_news'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/delete/{ID}'';', '1', 'modzzz_charity_news'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/news/{URI}', '', '', '2', 'modzzz_charity_news'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/news/{URI}', '', '', '4', 'modzzz_charity_news'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/news/{URI}', '', '', '5', 'modzzz_charity_news'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/news/{URI}', '', '', '6', 'modzzz_charity_news');
 
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_news_view', 'Charity News View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_news_browse', 'Charity News Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_charity_news_browse', '1140px', 'Charity News''s browse block', '_modzzz_charity_block_browse_news', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_charity_news_view', '1140px', 'Charity News''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_news_view', '1140px', 'Charity News''s rate block', '_modzzz_charity_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_news_view', '1140px', 'Charity News''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_news_view', '1140px', 'Charity News''s photos block', '_modzzz_charity_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_news_view', '1140px', 'Charity News''s video block', '_modzzz_charity_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_news_view', '1140px', 'Charity News''s files block', '_modzzz_charity_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_news_view', '1140px', 'Charity News''s sounds block', '_modzzz_charity_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_news_view', '1140px', 'Charity News''s comments block', '_modzzz_charity_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');
 
  

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleNewsAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/add/{ID}'';', '14', 'modzzz_charity');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/news/add/|modules/?r=charity/news/edit/|modules/?r=charity/news/view/|modules/?r=charity/news/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';
  

-- *********[END] NEWS *********** 

-- *********[BEGIN] Programs ************ 
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
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
  UNIQUE KEY `charity_programs_uri` (`uri`),
  KEY `charity_programs_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_charity_programs_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_programs', 'modzzz_charity_programs_rating', 'modzzz_charity_programs_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_programs_main', 'rate', 'rate_count', 'id', 'BxCharityProgramsVoting', 'modules/modzzz/charity/classes/BxCharityProgramsVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_programs', 'modzzz_charity_programs_cmts', 'modzzz_charity_programs_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_programs_main', 'id', 'comments_count', 'BxCharityProgramsCmts', 'modules/modzzz/charity/classes/BxCharityProgramsCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''programs/edit/{ID}'';', '0', 'modzzz_charity_programs'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''programs/delete/{ID}'';', '1', 'modzzz_charity_programs'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/programs/{URI}', '', '', '2', 'modzzz_charity_programs'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/programs/{URI}', '', '', '4', 'modzzz_charity_programs'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/programs/{URI}', '', '', '5', 'modzzz_charity_programs'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/programs/{URI}', '', '', '6', 'modzzz_charity_programs');
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_programs_view', 'Charity Programs View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_programs_browse', 'Charity Programs Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_charity_programs_browse', '1140px', 'Charity Programs''s browse block', '_modzzz_charity_block_browse_programs', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s rate block', '_modzzz_charity_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s photos block', '_modzzz_charity_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s video block', '_modzzz_charity_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s files block', '_modzzz_charity_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s sounds block', '_modzzz_charity_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_programs_view', '1140px', 'Charity Programs''s comments block', '_modzzz_charity_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');   
 
 

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleProgramsAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''programs/add/{ID}'';', '14', 'modzzz_charity');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/programs/add/|modules/?r=charity/programs/edit/|modules/?r=charity/programs/view/|modules/?r=charity/programs/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

-- *********[END] Programs *********** 

-- *********[BEGIN] Branches ************ 
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
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
  UNIQUE KEY `charity_branches_uri` (`uri`),
  KEY `charity_branches_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_charity_branches_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_branches', 'modzzz_charity_branches_rating', 'modzzz_charity_branches_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_branches_main', 'rate', 'rate_count', 'id', 'BxCharityBranchesVoting', 'modules/modzzz/charity/classes/BxCharityBranchesVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_branches', 'modzzz_charity_branches_cmts', 'modzzz_charity_branches_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_branches_main', 'id', 'comments_count', 'BxCharityBranchesCmts', 'modules/modzzz/charity/classes/BxCharityBranchesCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''branches/edit/{ID}'';', '0', 'modzzz_charity_branches'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''branches/delete/{ID}'';', '1', 'modzzz_charity_branches'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/branches/{URI}', '', '', '2', 'modzzz_charity_branches'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/branches/{URI}', '', '', '3', 'modzzz_charity_branches'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/branches/{URI}', '', '', '4', 'modzzz_charity_branches'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/branches/{URI}', '', '', '5', 'modzzz_charity_branches');
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_branches_view', 'Charity Branches View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_branches_browse', 'Charity Branches Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_charity_branches_browse', '1140px', 'Charity Branches''s browse block', '_modzzz_charity_block_browse_branches', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s rate block', '_modzzz_charity_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s location block', '_modzzz_charity_block_location', '2', '2', 'Location', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s photos block', '_modzzz_charity_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s video block', '_modzzz_charity_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s files block', '_modzzz_charity_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s sounds block', '_modzzz_charity_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_charity_branches_view', '1140px', 'Charity Branches''s comments block', '_modzzz_charity_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');   
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleBranchesAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''branches/add/{ID}'';', '14', 'modzzz_charity');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/branches/add/|modules/?r=charity/branches/edit/|modules/?r=charity/branches/view/|modules/?r=charity/branches/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

-- *********[END] Branches *********** 


-- *********[BEGIN] Supporter ************ 
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `type` enum('individual','organization') NOT NULL default 'individual',
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
  UNIQUE KEY `charity_supporter_uri` (`uri`),
  KEY `charity_supporter_author_id` (`author_id`),
  KEY `charity_supporter_created` (`created`),
  FULLTEXT KEY `charity_supporter_title` (`title`,`desc`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_cmts` (
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
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_supporter_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_supporter', 'modzzz_charity_supporter_rating', 'modzzz_charity_supporter_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_supporter_main', 'rate', 'rate_count', 'id', 'BxCharitySupporterVoting', 'modules/modzzz/charity/classes/BxCharitySupporterVoting.php');
   
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_supporter', 'modzzz_charity_supporter_cmts', 'modzzz_charity_supporter_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_supporter_main', 'id', 'comments_count', 'BxCharitySupporterCmts', 'modules/modzzz/charity/classes/BxCharitySupporterCmts.php');
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''supporter/edit/{ID}'';', '0', 'modzzz_charity_supporter'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''supporter/delete/{ID}'';', '1', 'modzzz_charity_supporter'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/supporter/{URI}', '', '', '2', 'modzzz_charity_supporter'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/supporter/{URI}', '', '', '3', 'modzzz_charity_supporter'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/supporter/{URI}', '', '', '4', 'modzzz_charity_supporter'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/supporter/{URI}', '', '', '5', 'modzzz_charity_supporter'); 

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_supporters_view', 'Charity Supporter View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_supporters_browse', 'Charity Supporter Browse', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_charity_supporters_browse', '1140px', 'Charity Supporter''s browse block', '_modzzz_charity_block_browse_supporters', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s info block', '_modzzz_charity_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s rate block', '_modzzz_charity_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s photos block', '_modzzz_charity_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s video block', '_modzzz_charity_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s files block', '_modzzz_charity_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s sounds block', '_modzzz_charity_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s comments block', '_modzzz_charity_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');   
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleSupporterAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''supporter/add/{ID}'';', '15', 'modzzz_charity');
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/supporter/add/|modules/?r=charity/supporter/edit/|modules/?r=charity/supporter/view/|modules/?r=charity/supporter/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

-- *********[END] Supporter ************ 

-- *********[BEGIN] STAFF ************ 
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
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
  UNIQUE KEY `charity_staff_uri` (`uri`),
  KEY `charity_staff_author_id` (`author_id`),
  KEY `charity_staff_created` (`created`),
  FULLTEXT KEY `charity_staff_title` (`title`,`desc`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_cmts` (
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
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_staff_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_staff', 'modzzz_charity_staff_rating', 'modzzz_charity_staff_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_staff_main', 'rate', 'rate_count', 'id', 'BxCharityStaffVoting', 'modules/modzzz/charity/classes/BxCharityStaffVoting.php');
   
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_staff', 'modzzz_charity_staff_cmts', 'modzzz_charity_staff_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_staff_main', 'id', 'comments_count', 'BxCharityStaffCmts', 'modules/modzzz/charity/classes/BxCharityStaffCmts.php');
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''staff/edit/{ID}'';', '0', 'modzzz_charity_staff'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''staff/delete/{ID}'';', '1', 'modzzz_charity_staff'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/staff/{URI}', '', '', '2', 'modzzz_charity_staff'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/staff/{URI}', '', '', '3', 'modzzz_charity_staff'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/staff/{URI}', '', '', '4', 'modzzz_charity_staff'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/staff/{URI}', '', '', '5', 'modzzz_charity_staff'); 
    
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_staffs_view', 'Charity Staff View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_staffs_browse', 'Charity Staff Browse', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_charity_staffs_browse', '1140px', 'Charity Staff''s browse block', '_modzzz_charity_block_browse_staffs', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s info block', '_modzzz_charity_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s rate block', '_modzzz_charity_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s photos block', '_modzzz_charity_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s video block', '_modzzz_charity_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s files block', '_modzzz_charity_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s sounds block', '_modzzz_charity_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s comments block', '_modzzz_charity_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');   
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleStaffAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''staff/add/{ID}'';', '15', 'modzzz_charity');
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/staff/add/|modules/?r=charity/staff/edit/|modules/?r=charity/staff/view/|modules/?r=charity/staff/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

-- *********[END] STAFF ************ 

-- *********[BEGIN] EVENTS************ 
 
INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('CharityEventCategories', 1, 1, '_modzzz_charity_event_auction'),  
('CharityEventCategories', 2, 2, '_modzzz_charity_event_concert'),   
('CharityEventCategories', 3, 3, '_modzzz_charity_event_charity_event'),  
('CharityEventCategories', 4, 4, '_modzzz_charity_event_corporate_function'), 
('CharityEventCategories', 5, 5, '_modzzz_charity_event_fund_raiser'),
('CharityEventCategories', 6, 6, '_modzzz_charity_event_luncheon'),  
('CharityEventCategories', 7, 7, '_modzzz_charity_event_shopping_fair'), 
('CharityEventCategories', 8, 8, '_modzzz_charity_event_sport_event'), 
('CharityEventCategories', 9, 9, '_modzzz_charity_event_tour'),   
('CharityEventCategories', 10, 10, '_modzzz_charity_event_other');
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
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
  UNIQUE KEY `charity_event_uri` (`uri`),
  KEY `charity_event_author_id` (`author_id`),
  KEY `charity_event_event_start` (`event_start`),
  KEY `charity_event_created` (`created`),
  FULLTEXT KEY `charity_event_title` (`title`,`desc`,`city`,`place`,`tags`,`categories`),
  FULLTEXT KEY `charity_event_tags` (`tags`),
  FULLTEXT KEY `charity_event_categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_cmts` (
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
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_event_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_event', 'modzzz_charity_event_rating', 'modzzz_charity_event_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_event_main', 'rate', 'rate_count', 'id', 'BxCharityEventVoting', 'modules/modzzz/charity/classes/BxCharityEventVoting.php');
   
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_event', 'modzzz_charity_event_cmts', 'modzzz_charity_event_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_event_main', 'id', 'comments_count', 'BxCharityEventCmts', 'modules/modzzz/charity/classes/BxCharityEventCmts.php');
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/edit/{ID}'';', '0', 'modzzz_charity_event'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/delete/{ID}'';', '1', 'modzzz_charity_event'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/event/{URI}', '', '', '2', 'modzzz_charity_event'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/event/{URI}', '', '', '3', 'modzzz_charity_event'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/event/{URI}', '', '', '4', 'modzzz_charity_event'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/event/{URI}', '', '', '5', 'modzzz_charity_event');  


SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_events_view', 'Charity Event View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_events_browse', 'Charity Event Browse', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_charity_events_browse', '1140px', 'Charity Event''s browse block', '_modzzz_charity_block_browse_events', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_charity_events_view', '1140px', 'Charity Event''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s info block', '_modzzz_charity_block_info', '2', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s rate block', '_modzzz_charity_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s photos block', '_modzzz_charity_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s video block', '_modzzz_charity_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s files block', '_modzzz_charity_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s sounds block', '_modzzz_charity_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_events_view', '1140px', 'Charity Event''s comments block', '_modzzz_charity_block_comments', '1', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');   
   
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleEventAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/add/{ID}'';', '15', 'modzzz_charity');
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/event/add/|modules/?r=charity/event/edit/|modules/?r=charity/event/view/|modules/?r=charity/event/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

-- *********[END] EVENTS ************ 
   

-- *********[BEGIN] MEMBERS ************ 
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
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
  UNIQUE KEY `charity_members_uri` (`uri`),
  KEY `charity_members_created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
  
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_cmts` (
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
    
CREATE TABLE IF NOT EXISTS `modzzz_charity_members_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_members', 'modzzz_charity_members_rating', 'modzzz_charity_members_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_members_main', 'rate', 'rate_count', 'id', 'BxCharityMembersVoting', 'modules/modzzz/charity/classes/BxCharityMembersVoting.php');
  

INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_members', 'modzzz_charity_members_cmts', 'modzzz_charity_members_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_members_main', 'id', 'comments_count', 'BxCharityMembersCmts', 'modules/modzzz/charity/classes/BxCharityMembersCmts.php');
   
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''members/edit/{ID}'';', '0', 'modzzz_charity_members'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''members/delete/{ID}'';', '1', 'modzzz_charity_members'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/members/{URI}', '', '', '2', 'modzzz_charity_members'), 
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/members/{URI}', '', '', '3', 'modzzz_charity_members'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/members/{URI}', '', '', '4', 'modzzz_charity_members'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/members/{URI}', '', '', '5', 'modzzz_charity_members');  
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_members_view', 'Charity Members View', @iMaxOrder+1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_members_browse', 'Charity Members Browse', @iMaxOrder+2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_charity_members_browse', '1140px', 'Charity Members''s browse block', '_modzzz_charity_block_browse_members', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_charity_members_view', '1140px', 'Charity Members''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_members_view', '1140px', 'Charity Members''s rate block', '_modzzz_charity_block_rate', '2', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_members_view', '1140px', 'Charity Members''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_members_view', '1140px', 'Charity Members''s photos block', '_modzzz_charity_block_photo', '1', '1', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_members_view', '1140px', 'Charity Members''s video block', '_modzzz_charity_block_video', '1', '2', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_members_view', '1140px', 'Charity Members''s files block', '_modzzz_charity_block_file', '1', '3', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_members_view', '1140px', 'Charity Members''s sounds block', '_modzzz_charity_block_sound', '1', '4', 'Sounds', '', '1', '71.9', 'non,memb', '0'),  
    ('modzzz_charity_members_view', '1140px', 'Charity Members''s comments block', '_modzzz_charity_block_comments', '1', '2', 'Comments', '', '1', '71.9', 'non,memb', '0');   
 
 

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleMembersAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''members/add/{ID}'';', '14', 'modzzz_charity');


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/members/add/|modules/?r=charity/members/edit/|modules/?r=charity/members/view/|modules/?r=charity/members/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

-- *********[END] MEMBERS *********** 

UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/upload_photos_subprofile') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';


-- [begin] review
CREATE TABLE IF NOT EXISTS `[db_prefix]review_main` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `charity_id` int(11) NOT NULL,
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
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_cmts` (
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
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_charity_review_views_tk` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity_review', 'modzzz_charity_review_rating', 'modzzz_charity_review_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_charity_review_main', 'rate', 'rate_count', 'id', 'BxCharityReviewVoting', 'modules/modzzz/charity/classes/BxCharityReviewVoting.php');
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity_review', 'modzzz_charity_review_cmts', 'modzzz_charity_review_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_charity_review_main', 'id', 'comments_count', 'BxCharityReviewCmts', 'modules/modzzz/charity/classes/BxCharityReviewCmts.php');
 
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_charity_review', 'modzzz_charity_review_views_tk', 86400, 'modzzz_charity_review_main', 'id', 'views', 1);
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/edit/{ID}'';', '0', 'modzzz_charity_review'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/delete/{ID}'';', '1', 'modzzz_charity_review'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/review/{URI}', '', '', '2', 'modzzz_charity_review'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/review/{URI}', '', '', '4', 'modzzz_charity_review'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/review/{URI}', '', '', '5', 'modzzz_charity_review'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/review/{URI}', '', '', '6', 'modzzz_charity_review'),
    ('{TitlePostReview}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/add/{ID}'';', '18', 'modzzz_charity');
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity post reviews', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);



UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/review/add/|modules/?r=charity/review/edit/|modules/?r=charity/review/view/|modules/?r=charity/review/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_reviews_browse', 'Charity Reviews Browse', @iMaxOrder+11);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_reviews_view', 'Charity Reviews View', @iMaxOrder+12);


INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 

    ('modzzz_charity_reviews_browse', '1140px', 'Charity Review''s browse block', '_modzzz_charity_block_browse_reviews', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
    
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s rate block', '_modzzz_charity_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s info block', '_modzzz_charity_block_info', '2', '3', 'Info', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s description block', '_modzzz_charity_block_details', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s photos block', '_modzzz_charity_block_photo', '1', '2', 'Photos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s video embed block', '_modzzz_charity_block_video_embed', '1', '3', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s videos block', '_modzzz_charity_block_videos', '1', '4', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s sounds block', '_modzzz_charity_block_sounds', '1', '5', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s files block', '_modzzz_charity_block_files', '1', '6', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_reviews_view', '1140px', 'Charity Review''s comments block', '_modzzz_charity_block_comments', '1', '7', 'Comments', '', '1', '71.9', 'non,memb', '0');   


-- [end] review

-- [begin] faqs
CREATE TABLE IF NOT EXISTS `[db_prefix]faq_main` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL, 
  `charity_id` int(11) NOT NULL,
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
  `charity_id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL default '',
  `answer` text NOT NULL default '', 
  `created` int(11) NOT NULL, 
  PRIMARY KEY (`id`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES    
('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''faq/delete/{ID}'';', '1', 'modzzz_charity_faq'),  
('{TitlePostFaq}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''faq/add/{ID}'';', '17', 'modzzz_charity');

INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('charity', 'view_faq', '_modzzz_charity_privacy_view_faq', '3');
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity create faqs', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=charity/faq/add/|modules/?r=charity/faq/edit/|modules/?r=charity/faq/view/|modules/?r=charity/faq/browse/') WHERE `Parent`=0 AND `Name`='Charity' AND `Type`='system' AND `Caption`='_modzzz_charity_menu_root';

SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_faqs_browse', 'Charity faqs Browse', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_faqs_view', 'Charity faqs View', @iMaxOrder+2);
 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
 
    ('modzzz_charity_faqs_browse', '1140px', 'Charity FAQ''s browse block', '_modzzz_charity_block_browse_faqs', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),
    
    ('modzzz_charity_faqs_view', '1140px', 'Charity FAQ''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_faqs_view', '1140px', 'Charity FAQ''s items block', '_modzzz_charity_block_faqs_items', '1', '0', 'FaqItems', '', '1', '71.9', 'non,memb', '0');
 
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
  `charity_id` int(11) unsigned NOT NULL,
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
  `charity_id` int(11) NOT NULL, 
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
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `featured_expiry_date`  INT NOT NULL,
  `featured_date` INT NOT NULL, 
  `allow_post_in_forum_to` varchar(16) NOT NULL, 
  `allow_view_charity_to` int(11) NOT NULL,
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
(1, 'Charity', 'Charity', '', 64);

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
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_view', 'Charity View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_celendar', 'Charity Calendar', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_main', 'Charity Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_my', 'Charity My', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_edit', 'Charity Map Edit Location', @iMaxOrder+5);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_category', 'Charity Category', @iMaxOrder+6);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_subcategory', 'Charity Sub-Category', @iMaxOrder+7);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_local', 'Local Charity Page', @iMaxOrder+8);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_local_state', 'Local Charity State Page', @iMaxOrder+9);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_charity_packages', 'Charity Packages', @iMaxOrder+10);
   
-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    ('modzzz_charity_view', '1140px', 'Charity''s actions block', '_modzzz_charity_block_actions', '2', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_view', '1140px', 'Charity''s listed by block', '_modzzz_charity_block_listed_by', '2', '1', 'ListedBy', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s info block', '_modzzz_charity_block_info', '2', '2', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s rate block', '_modzzz_charity_block_rate', '2', '3', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_view', '1140px', 'Charity''s business contact block', '_modzzz_charity_business_contact', '2', '4', 'BusinessContact', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s location block', '_modzzz_charity_location', '2', '5', 'Location', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s fans block', '_modzzz_charity_block_fans', '2', '7', 'Fans', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_charity_view', '1140px', 'Charity''s unconfirmed fans block', '_modzzz_charity_block_fans_unconfirmed', '2', '8', 'FansUnconfirmed', '', '1', '28.1', 'memb', '0'),

    ('modzzz_charity_view', '1140px', 'Charity''s description block', '_modzzz_charity_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s believe block', '_modzzz_charity_block_believe', '1', '1', 'Believe', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s service hours block', '_modzzz_charity_block_service_hours', '1', '2', 'ServiceHours', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s history block', '_modzzz_charity_block_history', '1', '3', 'History', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s photo block', '_modzzz_charity_block_photo', '1', '5', 'Photo', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s Video Embed block', '_modzzz_charity_block_video_embed', '1', '6', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_charity_view', '1140px', 'Charity''s videos block', '_modzzz_charity_block_video', '1', '7', 'Video', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_charity_view', '1140px', 'Charity''s sounds block', '_modzzz_charity_block_sound', '1', '8', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_charity_view', '1140px', 'Charity''s files block', '_modzzz_charity_block_files', '1', '9', 'Files', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_charity_view', '1140px', 'Charity''s local block', '_modzzz_charity_block_local', '1', '10', 'Local', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s other block', '_modzzz_charity_block_other', '1', '11', 'Other', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s comments block', '_modzzz_charity_block_comments', '1', '12', 'Comments', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_view', '1140px', 'Charity''s donors block', '_modzzz_charity_block_donors', '1', '13', 'Donors', '', '1', '71.9', 'non,memb', '0'), 
 
    ('modzzz_charity_local_state', '1140px', 'Local States', '_modzzz_charity_block_browse_state', '1', '0', 'States', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_local_state', '1140px', 'Local State Charities', '_modzzz_charity_block_browse_state_charities', '2', '0', 'StateCharities', '', '1', '71.9', 'non,memb', '0'),

    ('modzzz_charity_local', '1140px', 'Local Charities', '_modzzz_charity_block_browse_country', '1', '0', 'Region', '', '1', '100', 'non,memb', '0'),  
 
    ('modzzz_charity_category', '1140px', 'Charity', '_modzzz_charity_block_charity', '1', '0', 'CategoryCharity', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_category', '1140px', 'Charity Categories', '_modzzz_charity_block_categories', '2', '0', 'Categories', '', '1', '28.1', 'non,memb', '0'),
     
    ('modzzz_charity_subcategory', '1140px', 'Charity Category Charities', '_modzzz_charity_block_category_charity', '1', '0', 'SubCategoryCharities', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_subcategory', '1140px', 'Charity Sub-Categories', '_modzzz_charity_block_subcategories', '2', '0', 'SubCategories', '', '1', '28.1', 'non,memb', '0'),

    ('modzzz_charity_main', '1140px', 'Charity Create', '_modzzz_charity_block_charity_create', '0', '0', 'Create', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_main', '1140px', 'Search Charity', '_modzzz_charity_block_search', '2', '1', 'Search', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_main', '1140px', 'Charity Categories', '_modzzz_charity_block_charity_categories', '2', '2', 'CharityCategories', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_charity_main', '1140px', 'Charity Tags', '_tags_plural', '2', '4', 'Tags', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_charity_main', '1140px', 'Latest Featured Charity', '_modzzz_charity_block_latest_featured_charity', '1', '0', 'LatestFeaturedCharity', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_main', '1140px', 'Recent Charity', '_modzzz_charity_block_recent', '1', '1', 'Recent', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_main', '1140px', 'Charity Forum Posts', '_modzzz_charity_block_forum', '1', '2', 'Forum', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_charity_main', '1140px', 'Charity Comments', '_modzzz_charity_block_latest_comments', '1', '3', 'Comments', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_charity_main', '1140px', 'Charity States', '_modzzz_charity_block_charity_states', '1', '5', 'States', '', '1', '71.9', 'non,memb', '0'), 

    ('modzzz_charity_packages', '1140px', 'Charity Packages', '_modzzz_charity_block_packages', '1', '0', 'Packages', '', '1', '100', 'non,memb', '0'),  
 
    ('modzzz_charity_my', '1140px', 'Administration Owner', '_modzzz_charity_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('modzzz_charity_my', '1140px', 'User''s charity', '_modzzz_charity_block_users_charity', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('index', '1140px', 'Charities', '_modzzz_charity_block_homepage', 1, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''charity'', ''homepage_block'');', 1, 66, 'non,memb', 0),
    ('member', '1140px', 'My Charities', '_modzzz_charity_block_my_charities', 1, 4, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''charity'', ''accountpage_block'');', 1, 66, 'non,memb', 0),  
    ('member', '1140px', 'Local Charities', '_modzzz_charity_block_area_charities', 1, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''charity'', ''accountarea_block'');', 1, 66, 'non,memb', 0),  
    ('profile', '1140px', 'User Charities', '_modzzz_charity_block_my_charity', 2, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''charity'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 66, 'non,memb', 0);


-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=charity/', 'm/charity/', 'modzzz_charity_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Charity', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_charity_permalinks', 'on', 26, 'Enable friendly permalinks in charity', 'checkbox', '', '', '0', ''),
('modzzz_charity_autoapproval', 'on', @iCategId, 'Activate all charity after creation automatically', 'checkbox', '', '', '0', ''),
('category_auto_app_modzzz_charity', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_charity_author_comments_admin', 'on', @iCategId, 'Allow charity admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('modzzz_charity_perpage_main_recent', '10', @iCategId, 'Number of recently added charities to show on charity home', 'digit', '', '', '0', ''),
('modzzz_charity_perpage_main_featured', '5', @iCategId, 'Number of featured charities to show on charity home', 'digit', '', '', '0', ''),
('modzzz_charity_perpage_browse', '14', @iCategId, 'Number of charity to show on browse pages', 'digit', '', '', '0', ''),
('modzzz_charity_perpage_profile', '4', @iCategId, 'Number of charity to show on profile page', 'digit', '', '', '0', ''),
('modzzz_charity_perpage_accountpage', '4', @iCategId, 'Number of charity to show on account page', 'digit', '', '', '0', ''),
('modzzz_charity_perpage_homepage', '5', @iCategId, 'Number of charity to show on homepage', 'digit', '', '', '0', ''),
('modzzz_charity_homepage_default_tab', 'featured', @iCategId, 'Default charity block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),
('modzzz_charity_perpage_view_fans', '6', @iCategId, 'Number of fans to show on charity view page', 'digit', '', '', '0', ''),
('modzzz_charity_perpage_browse_fans', '30', @iCategId, 'Number of fans to show on browse fans page', 'digit', '', '', '0', ''),
('modzzz_charity_title_length', '100', @iCategId, 'Max length of title', 'digit', '', '', '0', ''),
('modzzz_charity_max_preview', '300', @iCategId, 'Length of charity description snippet to show in blocks', 'digit', '', '', '0', ''),
('modzzz_charity_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', ''), 
('modzzz_charity_free_expired', '0', @iCategId, 'number of days before free charity expires (0-never expires)', 'digit', '', '', '0', ''), 
('modzzz_charity_activate_expiring', 'on', @iCategId, 'activate sending email notification of soon to expire charities', 'checkbox', '', '', '0', ''),  
('modzzz_charity_activate_expired', 'on', @iCategId, 'activate sending email notification of expired charities', 'checkbox', '', '', '0', ''), 
('modzzz_charity_email_expiring', '3', @iCategId, 'number of days before expiry to send email notification (0=same day)', 'digit', '', '', '0', ''), 
('modzzz_charity_email_expired', '3', @iCategId, 'number of days after expiry to send email notification (0=same day)', 'digit', '', '', '0', ''),  
('modzzz_charity_max_email_invitations', '10', @iCategId, 'Max number of email to send per one promotion', 'digit', '', '', '0', ''),

('modzzz_charity_forum_max_preview', '150', @iCategId, 'length of forum post snippet to show on main page', 'digit', '', '', '0', ''),
('modzzz_charity_comments_max_preview', '150', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
('modzzz_charity_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''), 
('modzzz_charity_per_page', '7', @iCategId, 'Charities search results to show per page', 'digit', '', '', '0', ''),
 
('modzzz_charity_perpage_view_subitems', '6', @iCategId, 'Number of sub-items (News,Events etc.) to show on charity main/view page', 'digit', '', '', '0', ''),
('modzzz_charity_perpage_browse_subitems', '30', @iCategId, 'Number of sub-items (News,Events etc.) to show on the sub section browse page', 'digit', '', '', '0', ''),   

('modzzz_charity_default_country', 'US', @iCategId, 'default country for location', 'digit', '', '', 0, ''),
 
('modzzz_charity_allow_embed', 'on', @iCategId, 'Allow video embed when video upload is disabled', 'checkbox', '', '', '0', ''),  

('modzzz_charity_featured_cost', '0', @iCategId, 'Cost per day for Featured Status', 'digit', '', '', 0, ''),
('modzzz_charity_featured_purchase_desc', 'Purchase Featured Charity Status', @iCategId, 'Item decription displayed on PayPal Featured Charity Purchase', 'digit', '', '', 0, ''),
('modzzz_charity_buy_featured', '', @iCategId, 'Enable Paypal purchase of Featured Status', 'checkbox', '', '', 0, ''), 
 
('modzzz_charity_paypal_email', '', @iCategId, 'Paypal Email', 'digit', '', '', 0, ''),
('modzzz_charity_currency_code', 'USD', @iCategId, 'Currency code for checkout system (eg. USD,EURO,GBP)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_charity_currency_sign', '&#36;', @iCategId, 'Currency sign (for display purposes only)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', '') 
 ;
 

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'modzzz_charity', '_modzzz_charity', 'BxCharitySearchResult', 'modules/modzzz/charity/classes/BxCharitySearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_charity', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxCharityVoting', 'modules/modzzz/charity/classes/BxCharityVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_charity', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxCharityCmts', 'modules/modzzz/charity/classes/BxCharityCmts.php');

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_charity', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'modzzz_charity', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_charity_permalinks', 'm/charity/browse/tag/{tag}', 'modules/?r=charity/browse/tag/{tag}', '_modzzz_charity');

-- category objects
INSERT INTO `sys_objects_categories` VALUES (NULL, 'modzzz_charity', 'SELECT `Categories` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_charity_permalinks', 'm/charity/browse/category/{tag}', 'modules/?r=charity/browse/category/{tag}', '_modzzz_charity');
    
INSERT INTO `sys_categories` (`Category`, `ID`, `Type`, `Owner`, `Status`) VALUES 
('Animals', '0', 'modzzz_charity', '0', 'active'), 
('Arts', '0', 'modzzz_charity', '0', 'active'),
('Civic Engagement', '0', 'modzzz_charity', '0', 'active'),
('Climate Change', '0', 'modzzz_charity', '0', 'active'),
('Disaster Relief', '0', 'modzzz_charity', '0', 'active'),
('Education', '0', 'modzzz_charity', '0', 'active'),
('Environmental Conservation', '0', 'modzzz_charity', '0', 'active'),
('Food and Farming', '0', 'modzzz_charity', '0', 'active'),
('Health and Wellness', '0', 'modzzz_charity', '0', 'active'),
('Human Rights', '0', 'modzzz_charity', '0', 'active'),
('International Development', '0', 'modzzz_charity', '0', 'active'),
('Poverty', '0', 'modzzz_charity', '0', 'active'),
('Veterans', '0', 'modzzz_charity', '0', 'active'), 
('World Religions', '0', 'modzzz_charity', '0', 'active');


 
-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'modzzz_charity'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'modzzz_charity'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'modzzz_charity'),
    ('{TitleBroadcast}', 'envelope', '{BaseUri}broadcast/{ID}', '', '', '3', 'modzzz_charity'),  
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '6', 'modzzz_charity'),
    ('{TitleJoin}', '{IconJoin}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '7', 'modzzz_charity'),
    ('{TitleManageFans}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '8', 'modzzz_charity'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '9', 'modzzz_charity'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '10', 'modzzz_charity'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '11', 'modzzz_charity'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '12', 'modzzz_charity'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '13', 'modzzz_charity'),
    ('{TitleInquire}', 'question-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''inquire/{ID}'';', '22', 'modzzz_charity'), 
    ('{TitleClaim}', 'key', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''claim/{ID}'';', '23', 'modzzz_charity'), 
    ('{TitleInvite}', 'hand-o-right', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '24', 'modzzz_charity'), 
    ('{TitlePurchaseFeatured}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''purchase_featured/{ID}'';', '25', 'modzzz_charity'),
  
    ('{TitleForum}', 'globe', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxCharityModule'']->_oConfig; return BX_DOL_URL_ROOT . ''forum/charity/forum/{URI}-0.htm'';', '29', 'modzzz_charity'),  
   
    ('{evalResult}', 'plus', '{BaseUri}browse/my&filter=add_charity', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_charity_action_add_charity'') : '''';', '1', 'modzzz_charity_title'),
    ('{evalResult}', 'money', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_charity_action_my_charity'') : '''';', '2', 'modzzz_charity_title');
   

SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Charity', '_modzzz_charity_menu_root', 'modules/?r=charity/home/|modules/?r=charity/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'money', 'money', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charities Main Page', '_modzzz_charity_menu_main', 'modules/?r=charity/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Recent Charities', '_modzzz_charity_menu_recent', 'modules/?r=charity/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Top Rated Charities', '_modzzz_charity_menu_top_rated', 'modules/?r=charity/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Popular Charities', '_modzzz_charity_menu_popular', 'modules/?r=charity/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Featured Charities', '_modzzz_charity_menu_featured', 'modules/?r=charity/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charities Tags', '_modzzz_charity_menu_tags', 'modules/?r=charity/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_charity');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Charities Categories', '_modzzz_charity_menu_categories', 'modules/?r=charity/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_charity');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Calendar', '_modzzz_charity_menu_calendar', 'modules/?r=charity/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Local Charities', '_modzzz_charity_menu_local', 'modules/?r=charity/local', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Search', '_modzzz_charity_menu_search', 'modules/?r=charity/search', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
  
 
SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1),1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 9, 'Charity', '_modzzz_charity_menu_my_charity_profile', 'modules/?r=charity/browse/user/{profileUsername}|modules/?r=charity/browse/joined/{profileUsername}',  
ifnull(@iCatProfileOrder,1), 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1),1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 4, 'Charity', '_modzzz_charity_menu_my_charity_profile', 'modules/?r=charity/browse/my', ifnull(@iCatProfileOrder,1), 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_charity', '_modzzz_charity', '{siteUrl}modules/?r=charity/administration/', 'Charity module by Modzzz','money', @iMax+1);

-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'modzzz_charity', 'modzzz_charity', 'modules/?r=charity/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=charity/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'money', @iStatSiteOrder);


-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('modzzz_charity', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('modzzz_charityp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_modzzz_charity', '__modzzz_charity__ (<a href="modules/?r=charity/browse/my&filter=add_charity">__l_add__</a>)');
 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity broadcast message', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction); 

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity photos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity sounds add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity videos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity files add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

  
INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity purchase featured', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity view charity', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity add charity', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity edit any charity', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity delete any charity', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity approve charity', NULL);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity make claim', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'charity make inquiry', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_charity_profile_delete', '', '', 'BxDolService::call(''charity'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_charity_media_delete', '', '', 'BxDolService::call(''charity'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);


-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'modzzz_charity', `Eval` = 'return BxDolService::call(''charity'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);
 

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES  
('charity', 'view_fans', '_modzzz_charity_privacy_view_fans', '3'), 
('charity', 'view_charity', '_modzzz_charity_privacy_view_charity', '3'), 
('charity', 'view_donors', '_modzzz_charity_privacy_view_donors', '3'), 
('charity', 'comment', '_modzzz_charity_privacy_comment', 'f'),
('charity', 'rate', '_modzzz_charity_privacy_rate', 'f'),
('charity', 'post_in_forum', '_modzzz_charity_privacy_post_in_forum', 'f'),
('charity', 'join', '_modzzz_charity_privacy_join', '3'),  
('charity', 'upload_photos', '_modzzz_charity_privacy_upload_photos', 'a'),
('charity', 'upload_videos', '_modzzz_charity_privacy_upload_videos', 'a'),
('charity', 'upload_sounds', '_modzzz_charity_privacy_upload_sounds', 'a'),
('charity', 'upload_files', '_modzzz_charity_privacy_upload_files', 'a');


-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('modzzz_charity', '', '', 'return BxDolService::call(''charity'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_charity', 'change', 'modzzz_charity_sbs', 'return BxDolService::call(''charity'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_charity', 'commentPost', 'modzzz_charity_sbs', 'return BxDolService::call(''charity'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_charity', 'join', 'modzzz_charity_sbs', 'return BxDolService::call(''charity'', ''get_subscription_params'', array($arg2, $arg3));');
 

-- email templates 

INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_charity_broadcast', '<BroadcastTitle>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<EntryUrl>"><EntryTitle></a> charity admin has sent the following broadcast message:</p> <p><BroadcastMessage></p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Charity broadcast message', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_inquiry', '<NickName> sent a message about your Charity at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has sent a message about your Charity, <b><a href="<ListUrl>"><ListTitle></a></b>:</p><pre><Message></pre><bx_include_auto:_email_footer.html />', 'Charity Inquiry', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_claim', '<NickName> is claiming ownership of a Charity at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has claimed ownership of a Charity, <b><a href="<ListUrl>"><ListTitle></a></b>:</p><pre><Message></pre><bx_include_auto:_email_footer.html />', 'Charity Claim', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_charity_invitation', 'Check out this Charity: <CharityName>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to check out this charity:</p> <pre><InvitationText></pre> <p> <b>Charity Information:</b><br /> Name: <CharityName><br /> Location: <CharityLocation><br /> <a href="<CharityUrl>">More details</a> </p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Charity invitation template', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_claim_assign', 'Your Claim on a Charity at <SiteName> is updated', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>A Charity, <a href="<ListLink>"><ListTitle></a> that you claimed at <a href="<SiteUrl>"><SiteName></a> has been assigned to you. You now have ownership and administrative rights to the charity</p><bx_include_auto:_email_footer.html />', 'Claimed Charity assignment notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_expired', 'Your Charity at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Charity, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired</p><bx_include_auto:_email_footer.html />', 'Expired Charity notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_post_expired', 'Message about your expired Charity at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Charity, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired <b><Days></b> days ago</p><bx_include_auto:_email_footer.html />', 'Post-Expired Charity notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_expiring', 'Message about your expiring Charity at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Charity, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> will expire in <b><Days></b> days<br></p><bx_include_auto:_email_footer.html />', 'Expiring Charity notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_featured_expire_notify', 'Your Featured Charity Status at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is inform you that your Featured Status for the Charity, <a href="<ListLink>"><ListTitle></a> at <SiteName> has expired. You may purchase Featured Status again at any time you desire <br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Charity Status Expire Notification', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_featured_admin_notify', 'A member purchased Featured Charity Status at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear Administrator</b>,</p>\r\n\r\n<p><a href="<NickLink>"><NickName></a> has just purchased Featured Status for the Charity, <a href="<ListLink>"><ListTitle></a>, for <Days> days at <SiteName><br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Charity Purchase Admin Notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_charity_featured_buyer_notify', 'Your Featured Charity Status purchase at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is confirmation of your Featured Status purchase at <SiteName> for Charity, <a href="<ListLink>"><ListTitle></a>. It will be Featured for <Days> days<br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Charity Purchase Buyer Notification', '0');
  
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
 ('modzzz_charity_sbs', 'Charity was changed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<ViewLink>"><EntryTitle></a> Charity listing was changed: <br /> <ActionName> </p> <p>You may cancel the subscription by clicking the following link: <a href="<UnsubscribeLink>"><UnsubscribeLink></a></p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Charity subscription template', '0');
  

INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_charity_fan_remove', 'You were removed from fans of a Charity', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>You was removed from fans of <a href="<EntryUrl>"><EntryTitle></a> charity by charity admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User was removed from fans of Charity notification message', '0'),
('modzzz_charity_fan_become_admin', 'You became admin of a Charity', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Congratulations! You become admin of <a href="<EntryUrl>"><EntryTitle></a> charity.</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User become admin of a Charity notification message', '0'),
('modzzz_charity_admin_become_fan', 'Your Charity admin status was removed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Your admin status was removed from <a href="<EntryUrl>"><EntryTitle></a> charity by charity admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'User Charity admin status was removed notification message', '0'),
('modzzz_charity_join_request', 'New join request to your charity', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>New join request in your charity <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'New join request to a charity notification message', '0'),
('modzzz_charity_join_reject', 'Your join request to a charity was rejected', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Sorry, but your request to join <a href="<EntryUrl>"><EntryTitle></a> charity was rejected by charity admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Join request to a charity was rejected notification message', '0'),
('modzzz_charity_join_confirm', 'Your join request to a charity was confirmed', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p>Congratulations! Your request to join <a href="<EntryUrl>"><EntryTitle></a> charity was confirmed by charity admin(s).</p> <p>--</p> <bx_include_auto:_email_footer.html />', 'Join request to a charity was confirmed notification message', '0');
 

INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxCharity', '*/1 * * * *', 'BxCharityCron', 'modules/modzzz/charity/classes/BxCharityCron.php', '') ;
 
 

-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('modzzz_charity', '_modzzz_charity', '0.8', 'auto', 'BxCharitySiteMaps', 'modules/modzzz/charity/classes/BxCharitySiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('modzzz_charity', '_modzzz_charity', 'modzzz_charity_main', 'created', '', '', 1, @iMaxOrderCharts);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_charity_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''charity'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_charity_event_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''charity'', ''event_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
  
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_charity_branches_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''charity'', ''branches_map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);
 

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 

('modzzz_charity_view', '1140px', 'Charity''s social sharing block', '_sys_block_title_social_sharing', 2, 9, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_view', '1140px', 'Charity''s Location', '_modzzz_charity_block_map', 2, 6, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''charity'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),

('modzzz_charity_events_view', '1140px', 'Charity Event''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_events_view', '1140px', 'Charity Event''s Location', '_modzzz_charity_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''charity_event'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),

('modzzz_charity_supporters_view', '1140px', 'Charity Supporter''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_staffs_view', '1140px', 'Charity Staff''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_faqs_view', '1140px', 'Charity Faq''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_members_view', '1140px', 'Charity Member''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_news_view', '1140px', 'Charity Member''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_programs_view', '1140px', 'Charity Member''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
 
('modzzz_charity_branches_view', '1140px', 'Charity Branches''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
('modzzz_charity_branches_view', '1140px', 'Charity Branches''s Location', '_modzzz_charity_block_map', 2, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''charity_branches'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
 
('modzzz_charity_main', '1140px', 'Calendar', '_modzzz_charity_block_calendar', '2', '5', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
('modzzz_charity_main', '1140px', 'Map', '_Map', '1', '2', 'PHP', 'return BxDolService::call(''wmap'', ''homepage_part_block'', array (''charity''));', 1, 71.9, 'non,memb', 0); 
 
 UPDATE `sys_page_compose` SET `Column`=`Column`+1 WHERE `Page` = 'modzzz_charity_main' AND  `Caption` = '_Map' AND `Column` != 0 AND `ColWidth` != '100';

 UPDATE `sys_page_compose` SET `Column`=`Column`+1 WHERE `Page` = 'modzzz_charity_view' AND  `Func` = 'SocialSharing' AND `Column` != 0 AND `ColWidth` != '100';
 
 UPDATE `sys_page_compose` SET `Column`=`Column`+1 WHERE `Caption` LIKE '_modzzz_charity_%' AND `Column` != 0 AND `ColWidth` != '100';