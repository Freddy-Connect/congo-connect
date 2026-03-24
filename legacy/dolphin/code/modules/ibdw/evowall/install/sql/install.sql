SET @iExtOrd = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id`='2');

INSERT INTO `sys_menu_admin` (`id`, `parent_id`, `name`, `title`, `url`, `description`, `icon`, `icon_large`, `check`, `order`) VALUES
(NULL, 2, 'EVO Wall', 'EVO Wall', '{siteUrl}modules/?r=evowall/administration/', 'Advanced News Feed - Settings', 'diamond', '', '', @iExtOrd+1);

SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('EVO Wall', @iMaxOrder);

SET @iKategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` SET   `Name` = 'LicenseKey',   `kateg` = @iKategId,   `desc`  = 'License Key (Activation code)<br><a target="_blank" href="ibdw/evowall/activation.php">Click here to get the code</a>',   `Type`  = 'digit',   `VALUE` = '',   `order_in_kateg` = 1;
INSERT INTO `sys_options` SET   `Name` = 'DefaultProfilePrivacy',   `kateg` = @iKategId,   `desc`  = 'Default profile wall privacy',   `Type`  = 'select',   `VALUE` = 'Members',   `order_in_kateg` = 2,   `AvailableValues` ='Members,Friends,Me Only,Public,Faves';
INSERT INTO `sys_options` SET   `Name` = 'DefaultAccountPrivacy',   `kateg` = @iKategId,   `desc`  = 'Display in the account page the post of',   `Type`  = 'select',   `VALUE` = 'Friends',   `order_in_kateg` = 3,   `AvailableValues` ='Friends,Members';
INSERT INTO `sys_options` SET   `Name` = 'DefaultHomePrivacy',   `kateg` = @iKategId,   `desc`  = 'Display in the home page the post of',   `Type`  = 'select',   `VALUE` = 'Members',   `order_in_kateg` = 4,   `AvailableValues` ='Members,Friends';
INSERT INTO `sys_options` SET   `Name` = 'TemplateColor',   `kateg` = @iKategId,   `desc`  = 'Use UNI or dark template',   `Type`  = 'select',   `VALUE` = 'UNI',   `order_in_kateg` = 5,   `AvailableValues` ='UNI,Dark';
INSERT INTO `sys_options` SET   `Name` = 'AllowPhotos',   `kateg` = @iKategId,   `desc`  = 'Allow post for photos',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 6;
INSERT INTO `sys_options` SET   `Name` = 'AllowVideos',   `kateg` = @iKategId,   `desc`  = 'Allow post for videos',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 7;
INSERT INTO `sys_options` SET   `Name` = 'AllowGroups',   `kateg` = @iKategId,   `desc`  = 'Allow post for groups',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 8;
INSERT INTO `sys_options` SET   `Name` = 'AllowPages',   `kateg` = @iKategId,   `desc`  = 'Allow post for pages',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 9;
INSERT INTO `sys_options` SET   `Name` = 'AllowEvents',   `kateg` = @iKategId,   `desc`  = 'Allow post for events',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 10;
INSERT INTO `sys_options` SET   `Name` = 'EventMod',   `kateg` = @iKategId,   `desc`  = 'Event module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 11,   `AvailableValues` ='Boonex,UE30';
INSERT INTO `sys_options` SET   `Name` = 'AllowSites',   `kateg` = @iKategId,   `desc`  = 'Allow post for sites',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 12;
INSERT INTO `sys_options` SET   `Name` = 'AllowPolls',   `kateg` = @iKategId,   `desc`  = 'Allow post for polls',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 13;
INSERT INTO `sys_options` SET   `Name` = 'PollsMod',   `kateg` = @iKategId,   `desc`  = 'Poll module',   `Type`  = 'select',   `VALUE` = 'Boonex',   `order_in_kateg` = 14,   `AvailableValues` ='Boonex,Modzzz';
INSERT INTO `sys_options` SET   `Name` = 'AllowAds',   `kateg` = @iKategId,   `desc`  = 'Allow post for ads',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 15;
INSERT INTO `sys_options` SET   `Name` = 'AllowBlogs',   `kateg` = @iKategId,   `desc`  = 'Allow post for blogs',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 16;
INSERT INTO `sys_options` SET   `Name` = 'AllowSounds',   `kateg` = @iKategId,   `desc`  = 'Allow post for sounds',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 17;
INSERT INTO `sys_options` SET   `Name` = 'ModzzzProperty',   `kateg` = @iKategId,   `desc`  = 'Allow post for Premium Real Estate by modzzz',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 18;
INSERT INTO `sys_options` SET   `Name` = 'UE30Locations',   `kateg` = @iKategId,   `desc`  = 'Allow post for Locations by UE30',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 19;
INSERT INTO `sys_options` SET   `Name` = 'AllowShareNotification',   `kateg` = @iKategId,   `desc`  = 'Allow email notification for sharing',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 20;
INSERT INTO `sys_options` SET   `Name` = 'AllowLikeNotification',   `kateg` = @iKategId,   `desc`  = 'Allow email notification for like',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 21;
INSERT INTO `sys_options` SET   `Name` = 'AllowCommentNotification',   `kateg` = @iKategId,   `desc`  = 'Allow email notification for comment',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 22;
INSERT INTO `sys_options` SET   `Name` = 'AllowMessageNotification',   `kateg` = @iKategId,   `desc`  = 'Allow email notification for profile messages',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 23;
INSERT INTO `sys_options` SET   `Name` = 'DaysMostPopular',   `kateg` = @iKategId,   `desc`  = 'Days considered for popular news',   `Type`  = 'digit',   `VALUE` = '7',   `order_in_kateg` = 24;
INSERT INTO `sys_options` SET   `Name` = 'MessageLenght',   `kateg` = @iKategId,   `desc`  = 'Messages maxlenght',   `Type`  = 'digit',   `VALUE` = '500',   `order_in_kateg` = 25;
INSERT INTO `sys_options` SET   `Name` = 'CommentLenght',   `kateg` = @iKategId,   `desc`  = 'Comments maxlenght',   `Type`  = 'digit',   `VALUE` = '500',   `order_in_kateg` = 26;
INSERT INTO `sys_options` SET   `Name` = 'CommentPrevNum',   `kateg` = @iKategId,   `desc`  = 'Number of comments in the preview',   `Type`  = 'digit',   `VALUE` = '4',   `order_in_kateg` = 27;
INSERT INTO `sys_options` SET   `Name` = 'CommentNum',   `kateg` = @iKategId,   `desc`  = 'Number of comments shown at a time',   `Type`  = 'digit',   `VALUE` = '8',   `order_in_kateg` = 28;
INSERT INTO `sys_options` SET   `Name` = 'NewLine',   `kateg` = @iKategId,   `desc`  = 'Allow new line on messages',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 29;
INSERT INTO `sys_options` SET   `Name` = 'DisplayProfileUpdate',   `kateg` = @iKategId,   `desc`  = 'Display profile updates',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 30;
INSERT INTO `sys_options` SET   `Name` = 'DisplayMessageStatusUpdate',   `kateg` = @iKategId,   `desc`  = 'Display Status Message',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 31;
INSERT INTO `sys_options` SET   `Name` = 'CommentOrder',   `kateg` = @iKategId,   `desc`  = 'Comments ordered by',   `Type`  = 'select',   `VALUE` = 'Last',   `order_in_kateg` = 32,   `AvailableValues` ='Last,First';
INSERT INTO `sys_options` SET   `Name` = 'WallPhotoName',   `kateg` = @iKategId,   `desc`  = 'Name for the Wall Photo Album',   `Type`  = 'digit',   `VALUE` = 'wall photo',   `order_in_kateg` = 33;
INSERT INTO `sys_options` SET   `Name` = 'WallVideoName',   `kateg` = @iKategId,   `desc`  = 'Name for the Wall Video Album',   `Type`  = 'digit',   `VALUE` = 'wall video',   `order_in_kateg` = 34;
INSERT INTO `sys_options` SET   `Name` = 'WallDefaultPrivacy',   `kateg` = @iKategId,   `desc`  = 'Default privacy for the wall albums',   `Type`  = 'select',   `VALUE` = 'Default',   `order_in_kateg` = 35,   `AvailableValues` ='Friends,Me Only,Public,Members,Faves';
INSERT INTO `sys_options` SET   `Name` = 'Grouping',   `kateg` = @iKategId,   `desc`  = 'Grouping of similar posts',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 36;
INSERT INTO `sys_options` SET   `Name` = 'TimingSimilar',   `kateg` = @iKategId,   `desc`  = 'Posts grouping for',   `Type`  = 'select',   `VALUE` = 'Day',   `order_in_kateg` = 37, `AvailableValues` ='Day,Week,Month';
INSERT INTO `sys_options` SET   `Name` = 'PhotoMaxPreview',   `kateg` = @iKategId,   `desc`  = 'Max number of photos in the grouping',   `Type`  = 'digit',   `VALUE` = '4',   `order_in_kateg` = 38;
INSERT INTO `sys_options` SET   `Name` = 'DisplayNewsNumber',   `kateg` = @iKategId,   `desc`  = 'Number of news loaded at time',   `Type`  = 'digit',   `VALUE` = '10',   `order_in_kateg` = 39;
INSERT INTO `sys_options` SET   `Name` = 'ProfileViewedBy',   `kateg` = @iKategId,   `desc`  = 'Display profile viewed by',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 40;
INSERT INTO `sys_options` SET   `Name` = 'DenyAccessToUnconfirmed',   `kateg` = @iKategId,   `desc`  = 'Deny access to the unconfirmed profiles',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 41;
INSERT INTO `sys_options` SET   `Name` = 'RefreshType',   `kateg` = @iKategId,   `desc`  = 'Refresh type',   `Type`  = 'select',   `VALUE` = 'Auto',   `order_in_kateg` = 42,   `AvailableValues` ='Auto,Manual';
INSERT INTO `sys_options` SET   `Name` = 'Refreshrate',   `kateg` = @iKategId,   `desc`  = 'Refresh rate (Minutes)',   `Type`  = 'digit',   `VALUE` = '1',   `order_in_kateg` = 43;
INSERT INTO `sys_options` SET   `Name` = 'AutoScroll',   `kateg` = @iKategId,   `desc`  = 'Autoscroll on page bottom',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 44;
INSERT INTO `sys_options` SET   `Name` = 'AutoScrollTime',   `kateg` = @iKategId,   `desc`  = 'Autoscroll after (millisecond)',   `Type`  = 'digit',   `VALUE` = '1000',   `order_in_kateg` = 45;
INSERT INTO `sys_options` SET   `Name` = 'HideMoreNewsButton',   `kateg` = @iKategId,   `desc`  = 'Hide the more news button',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 46;
INSERT INTO `sys_options` SET   `Name` = 'DateFormat',   `kateg` = @iKategId,   `desc`  = 'Format of the date',   `Type`  = 'select',   `VALUE` = 'mm/dd/yyyy 24H',   `order_in_kateg` = 47,   `AvailableValues` ='mm/dd/yyyy 24H,mm/dd/yyyy 24H (AM/PM),mm/dd/yyyy 12H,mm/dd/yyyy 12H (AM/PM),dd/mm/yyyy 24H,dd/mm/yyyy 24H (AM/PM),dd/mm/yyyy 12H,dd/mm/yyyy 12H (AM/PM),yyyy/mm/dd 24H,yyyy/mm/dd 24H (AM/PM),yyyy/mm/dd 12H,yyyy/mm/dd 12H (AM/PM)';
INSERT INTO `sys_options` SET   `Name` = 'Offset',   `kateg` = @iKategId,   `desc`  = 'Offset (seconds)',   `Type`  = 'digit',   `VALUE` = '0',   `order_in_kateg` = 48;
INSERT INTO `sys_options` SET   `Name` = 'WelcomeMessage',   `kateg` = @iKategId,   `desc`  = 'Use Welcome Message for new members',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 49;
INSERT INTO `sys_options` SET   `Name` = 'WelcomeNPost',   `kateg` = @iKategId,   `desc`  = 'Remove the message after (n. of posts)',   `Type`  = 'digit',   `VALUE` = '5',   `order_in_kateg` = 50;
INSERT INTO `sys_options` SET   `Name` = 'WelcomeCLife',   `kateg` = @iKategId,   `desc`  = 'Cookie life (days)',   `Type`  = 'digit',   `VALUE` = '5',   `order_in_kateg` = 51;
INSERT INTO `sys_options` SET   `Name` = 'UrlPlugin',   `kateg` = @iKategId,   `desc`  = 'Use the simplified Url method (FB)',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 52;
INSERT INTO `sys_options` SET   `Name` = 'PhotoRegularM',   `kateg` = @iKategId,   `desc`  = 'Use the regular method for the photos upload',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 53;
INSERT INTO `sys_options` SET   `Name` = 'PhotoFlashM',   `kateg` = @iKategId,   `desc`  = 'Use multi-uploader HTML5 method for the photos upload',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 54;
INSERT INTO `sys_options` SET   `Name` = 'PhotoOtherM',   `kateg` = @iKategId,   `desc`  = 'Use other methods for the photos upload',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 55;
INSERT INTO `sys_options` SET   `Name` = 'VideoYoutubeM',   `kateg` = @iKategId,   `desc`  = 'Use Youtube embedding for the videos upload',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 56;
INSERT INTO `sys_options` SET   `Name` = 'VideoFalshM',   `kateg` = @iKategId,   `desc`  = 'Use the flash method for the videos upload',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 57;
INSERT INTO `sys_options` SET   `Name` = 'VideoOtherM',   `kateg` = @iKategId,   `desc`  = 'Use other methods for the videos upload',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 58;
INSERT INTO `sys_options` SET   `Name` = 'DefaultPhotoM',   `kateg` = @iKategId,   `desc`  = 'Default upload method for photos',   `Type`  = 'select',   `VALUE` = 'Regular',   `order_in_kateg` = 59,   `AvailableValues` ='Regular,HTML5';
INSERT INTO `sys_options` SET   `Name` = 'DefaultVideoM',   `kateg` = @iKategId,   `desc`  = 'Default upload method for videos',   `Type`  = 'select',   `VALUE` = 'Youtube',   `order_in_kateg` = 60,   `AvailableValues` ='Youtube,Flash';
INSERT INTO `sys_options` SET   `Name` = 'AllowSocial',   `kateg` = @iKategId,   `desc`  = 'Allow Sharing on the Social Networks',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 61;
INSERT INTO `sys_options` SET   `Name` = 'AllowFacebook',   `kateg` = @iKategId,   `desc`  = 'Allow Facebook share',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 62;
INSERT INTO `sys_options` SET   `Name` = 'FacebookKID',   `kateg` = @iKategId,   `desc`  = 'Facebook App Id',   `Type`  = 'digit',   `VALUE` = '',   `order_in_kateg` = 63;
INSERT INTO `sys_options` SET   `Name` = 'AllowGoogle',   `kateg` = @iKategId,   `desc`  = 'Allow Google+ share',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 64;
INSERT INTO `sys_options` SET   `Name` = 'AllowTwitter',   `kateg` = @iKategId,   `desc`  = 'Allow Twitter share',   `Type`  = 'checkbox',   `VALUE` = 'on',   `order_in_kateg` = 65;
INSERT INTO `sys_options` SET   `Name` = 'AllowLinkedIn',   `kateg` = @iKategId,   `desc`  = 'Allow LinkedIn share',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 66;
INSERT INTO `sys_options` SET   `Name` = 'AllowPinterest',   `kateg` = @iKategId,   `desc`  = 'Allow Pinterest share',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 67;
INSERT INTO `sys_options` SET   `Name` = 'AllowBaidu',   `kateg` = @iKategId,   `desc`  = 'Allow Baidu share',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 68;
INSERT INTO `sys_options` SET   `Name` = 'AllowWeibo',   `kateg` = @iKategId,   `desc`  = 'Allow Weibo share',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 69;
INSERT INTO `sys_options` SET   `Name` = 'AllowQzone',   `kateg` = @iKategId,   `desc`  = 'Allow Qzone share',   `Type`  = 'checkbox',   `VALUE` = 'off',   `order_in_kateg` = 70;
INSERT INTO `sys_options` SET   `Name` = 'BadWordsFilter',   `kateg` = @iKategId,   `desc`  = 'Enable Bad Words Filter',   `Type`  = 'checkbox',   `VALUE` = '',   `order_in_kateg` = 71;
INSERT INTO `sys_options` SET   `Name` = 'BadWordsKeys',   `kateg` = @iKategId,   `desc`  = 'Bad Words list ((lower case, comma-separated)',   `Type`  = 'digit',   `VALUE` = '',   `order_in_kateg` = 72;


CREATE TABLE IF NOT EXISTS `commenti_spy_data` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`data` VARCHAR( 11 ) NOT NULL ,`user` VARCHAR( 11 ) NOT NULL ,`commento` TEXT NOT NULL) ENGINE = MYISAM ;

CREATE TABLE IF NOT EXISTS `datacommenti` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,`IDCommento` VARCHAR( 11 ) NOT NULL , `date` timestamp NOT NULL default CURRENT_TIMESTAMP) ENGINE = MYISAM ;

CREATE TABLE IF NOT EXISTS `ibdw_likethis` (
  `ID` int(40) NOT NULL auto_increment,
  `id_notizia` varchar(40) collate utf8_unicode_ci NOT NULL,
  `id_utente` varchar(40) collate utf8_unicode_ci NOT NULL,
  `like` varchar(40) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `evowall_code_reminder` (`id` INT NOT NULL PRIMARY KEY ,`addressr` VARCHAR( 100 ) NOT NULL,`website` VARCHAR( 200 ) NOT NULL) ENGINE = MYISAM;

INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`) VALUES (NULL , 'member', '', 'News Feed about the users and their friends', '_ibdw_evowall_member_news', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/evowall/main.php'');', '1', '0', 'memb', '0');
INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`) VALUES (NULL , 'index', '', 'News Feed about the users and their friends', '_ibdw_evowall_member_news', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/evowall/main.php'');', '1', '0', 'memb', '0');
INSERT INTO `sys_page_compose` (`ID` ,`Page` ,`PageWidth` ,`Desc` ,`Caption` ,`Column` ,`Order` ,`Func` ,`Content` ,`DesignBox` ,`ColWidth` ,`Visible` ,`MinWidth`) VALUES (NULL , 'profile', '', 'News Feed about the user', '_ibdw_evowall_member_news', '0', '0', 'PHP', 'require_once(BX_DIRECTORY_PATH_MODULES .''ibdw/evowall/main.php'');', '1', '0', 'memb', '0'); 



--- Default privacy for the profile actions
--- possible values 1: Default, 2: Me Only, 3: Public, 4: Members, 5: Friends, 6: Faves

INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowview', '_ibdw_evowall_Privacy_View', '4');
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowcomment', '_ibdw_evowall_Privacy_Comment', '4');
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowlike', '_ibdw_evowall_Privacy_Like', '4');
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowcontentbox', '_ibdw_evowall_Privacy_ContentBox', '4');
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowshare', '_ibdw_evowall_Allow_share', '4');
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowfbshare', '_ibdw_evowall_facebook_share_p', '4');
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowtwshare', '_ibdw_evowall_twitter_share_p', '4');
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('evowall', 'allowgoogleshare', '_ibdw_evowall_google_share_p', '4');



-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Comments view', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Like view', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Comments add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Like add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Post sharing', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Content Box', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Personal messages', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Photos', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Videos', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Groups', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Events', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Sites', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Polls', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Ads', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Blogs', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Sounds', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'EVO WALL - Pages', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

-- email templates

INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('_ibdw_evowall_bx_ads_add_condivisione', '<SenderNickName> shared your Ads', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Ad<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Ad Post Shared', 0),
('_ibdw_evowall_bx_blogs_add_condivisione', '<SenderNickName> shared your Blog', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Blog<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Blog Post Shared', 0),
('_ibdw_evowall_bx_sounds_add_condivisione', '<SenderNickName> shared your Sound', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Sound<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Sound Post Shared', 0),
('_ibdw_evowall_bx_video_add_condivisione', '<SenderNickName> shared your Video', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Video<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Video Post Shared', 0),
('_ibdw_evowall_bx_gruppo_add_condivisione', '<SenderNickName> shared your Group', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Group<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Group Post Shared', 0),
('_ibdw_evowall_bx_pagina_add_condivisione', '<SenderNickName> shared your Page', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Page<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Page Post Shared', 0),
('_ibdw_evowall_bx_photo_add_condivisione', '<SenderNickName> shared your Photo', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Photo<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Photo Post Shared', 0),
('_ibdw_evowall_bx_poll_add_condivisione', '<SenderNickName> shared your Poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Poll<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Shared', 0),
('_ibdw_evowall_bx_site_add_condivisione', '<SenderNickName> shared your Site', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Site<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Site Post Shared', 0),
('_ibdw_evowall_bx_event_add_condivisione', '<SenderNickName> shared your Event', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Event<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Event Post Shared', 0),
('_ibdw_evowall_bx_url_share', '<SenderNickName> shared your url', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> shared your Url<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: URL Post Shared', 0),
('_ibdw_evowall_notify_comment_photo', '<SenderNickName> commented your photo', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your photo<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Photo Post Commented', 0),
('_ibdw_evowall_notify_comment_video', '<SenderNickName> commented your video', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your video<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Video Post Commented', 0),
('_ibdw_evowall_notify_comment_poll', '<SenderNickName> commented your poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your poll<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Commented', 0),
('_ibdw_evowall_notify_comment_groups', '<SenderNickName> commented your group', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your group<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Group Post Commented', 0),
('_ibdw_evowall_notify_comment_events', '<SenderNickName> commented your event', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your event<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Event Post Commented', 0),
('_ibdw_evowall_notify_comment_sites', '<SenderNickName> commented your site', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your site<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Site Post Commented', 0),
('_ibdw_evowall_notify_comment_ads', '<SenderNickName> commented your ad', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your ad<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Ad Post Commented', 0),
('_ibdw_evowall_notify_comment', '<SenderNickName> commented your post', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your post<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: General Post Commented', 0),
('_ibdw_evowall_notify_comment_blogs', '<SenderNickName> commented your blog', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your blog<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Blog Post Commented', 0),
('_ibdw_evowall_notify_comment_sounds', '<SenderNickName> commented your sound', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your sound<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Sound Post Commented', 0),
('_ibdw_evowall_notify_comment_url', '<SenderNickName> commented your url', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> commented your url<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Url Post Commented', 0),
('_ibdw_evowall_notify_like_photo', '<SenderNickName> Likes your photo', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your photo<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Photo Post Like', 0),
('_ibdw_evowall_notify_like_video', '<SenderNickName> Likes your video', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your video<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Video Post Like', 0),
('_ibdw_evowall_notify_like_poll', '<SenderNickName> Likes your poll', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your poll<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Poll Post Like', 0),
('_ibdw_evowall_notify_like_groups', '<SenderNickName> Likes your group', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your group<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Group Post Like', 0),
('_ibdw_evowall_notify_like_events', '<SenderNickName> Likes your event', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your event<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Event Post Like', 0),
('_ibdw_evowall_notify_like_sites', '<SenderNickName> Likes your site', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your site<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Site Post Like', 0),
('_ibdw_evowall_notify_like_ads', '<SenderNickName> Likes your ad', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your ad<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Ad Post Like', 0),
('_ibdw_evowall_notify_like', '<SenderNickName> Likes your post', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your post<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: General Post Like', 0),
('_ibdw_evowall_notify_like_blogs', '<SenderNickName> Likes your blog', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your blog<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Blog Post Like', 0),
('_ibdw_evowall_notify_like_url', '<SenderNickName> Likes your url', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your url<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Url Post Like', 0),
('_ibdw_evowall_notify_like_sounds', '<SenderNickName> Likes your sound', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> likes your sound<br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Sound Post Like', 0),
('_ibdw_evowall_bx_evowall_message', '<SenderNickName> sent a message to you', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <RecipientNickName>,</b> <br/> <br/><SenderNickName> sent this message on your wall<br/> <br/><hr><PersonalMessage><hr><br/> <br/> <a href="{post}">Follow this link for more details</a>.</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'EvoWall: Message to a profile', 0);


INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'evowall', '0 0 * * *', 'evowallCron', 'modules/ibdw/evowall/classes/evowallCron.php', '') ;
