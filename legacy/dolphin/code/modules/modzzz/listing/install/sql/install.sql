ALTER TABLE `sys_menu_top` CHANGE `Link` `Link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `sys_objects_actions` CHANGE `Type` `Type` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `sys_stat_member` CHANGE `Type` `Type` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 

-- create tables
CREATE TABLE IF NOT EXISTS `[db_prefix]youtube` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `[db_prefix]invoices` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `invoice_no` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `days` int(11) unsigned NOT NULL,
  `listing_id` int(11) unsigned NOT NULL,
  `package_id` int(11) unsigned NOT NULL,
  `invoice_status` enum('pending','paid') NOT NULL default 'pending',
  `invoice_due_date` int(11) NOT NULL,
  `invoice_expiry_date` int(11) NOT NULL,
  `invoice_date` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
  `listing_id` int(11) NOT NULL, 
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
  `country` varchar(2) NOT NULL,
  `city` varchar(150) NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `address2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `state` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `zip` varchar(16) NOT NULL,
  `status` ENUM( 'approved', 'pending', 'expired' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'approved',
  `thumb` int(11) NOT NULL,
  `icon` varchar(255) NOT NULL default '',
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `currency` varchar(255) NOT NULL default '', 
  `categories` text NOT NULL,
  `category_id` VARCHAR( 255 ) NOT NULL, 
  `parent_category_id` int(11) NOT NULL, 
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL, 
  `employees_count` int(11) NOT NULL,
  `allow_view_employees_to` varchar(16) NOT NULL,
  `allow_work_to` int(11) NOT NULL, 
  `fans_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `featured_expiry_date`  INT NOT NULL,
  `featured_date` INT NOT NULL, 
  `allow_post_in_forum_to` varchar(16) NOT NULL, 
  `allow_view_listing_to` int(11) NOT NULL,
  `allow_view_fans_to` varchar(16) NOT NULL,
  `allow_join_to` int(11) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL, 
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_videos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_sounds_to` varchar(16) NOT NULL default 'a',
  `allow_upload_files_to` varchar(16) NOT NULL default 'a',
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
  FULLTEXT KEY `search` (`title`,`desc`,`tags` ),
  FULLTEXT KEY `tags` (`tags`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]category` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_category` int(10) unsigned NOT NULL, 
  PRIMARY KEY (`id_entry`, `id_category`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]fans` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]favorites` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
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
   
CREATE TABLE IF NOT EXISTS `[db_prefix]categ` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `uri` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `active` int(11) NOT NULL default '1',
  `public` int(11) NOT NULL default '1',
  `language` int(10) NOT NULL,
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]categ_bak` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `uri` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `active` int(11) NOT NULL default '1',
  `public` int(11) NOT NULL default '1',
  `language` int(10) NOT NULL,
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Dumping data for table `[db_prefix]categ_bak`  
INSERT INTO `[db_prefix]categ_bak` (`id`, `parent`, `name`, `uri`, `icon`, `active`) VALUES
(1, 0, 'Arts & Entertainment', 'Arts-Entertainment', '', 1),
(2, 0, 'Automotive', 'Automotive', '', 1),
(3, 0, 'Business & Professional Services', 'Business-Professional-Services', '', 1),
(4, 0, 'Clothing & Accessories', 'Clothing-Accessories', '', 1),
(5, 0, 'Community & Government', 'Community-Government', '', 1),
(6, 0, 'Computers & Electronics', 'Computers-Electronics', '', 1),
(7, 0, 'Construction & Contractors', 'Construction-Contractors', '', 1),
(8, 0, 'Education', 'Education', '', 1),
(9, 0, 'Food & Dining', 'Food-Dining', '', 1),
(10, 0, 'Health & Medicine', 'Health-Medicine', '', 1),
(11, 0, 'Home & Garden', 'Home-Garden', '', 1),
(12, 0, 'Industry & Agriculture', 'Industry-Agriculture', '', 1),
(13, 0, 'Legal & Financial', 'Legal-Financial', '', 1),
(14, 0, 'Media & Communications', 'Media-Communications', '', 1),
(15, 0, 'Personal Care & Services', 'Personal-Care-Services', '', 1),
(16, 0, 'Real Estate', 'Real-Estate', '', 1),
(17, 0, 'Shopping', 'Shopping', '', 1),
(18, 0, 'Sports & Recreation', 'Sports-Recreation', '', 1),
(19, 0, 'Travel & Transportation', 'Travel-Transportation', '', 1),
(44, 1, 'Beads Retail', 'Beads-Retail', '', 1),
(43, 1, 'Bars', 'Bars', '', 1),
(42, 1, 'Banquet Services', 'Banquet-Services', '', 1),
(41, 1, 'Ballroom & Social Dance Instruction', 'Ballroom-Social-Dance-Instruction', '', 1),
(40, 1, 'Art Supplies Retail', 'Art-Supplies-Retail', '', 1),
(39, 1, 'Art Schools', 'Art-Schools', '', 1),
(38, 1, 'Art Galleries & Dealers', 'Art-Galleries-Dealers', '', 1),
(37, 1, 'Art Classes', 'Art-Classes', '', 1),
(36, 1, 'Arcades & Amusements', 'Arcades-Amusements', '', 1),
(35, 1, 'Aerial Photographers', 'Aerial-Photographers', '', 1),
(34, 1, 'Acting Schools', 'Acting-Schools', '', 1),
(45, 1, 'Cameras & Camera Supplies', 'Cameras-Camera-Supplies', '', 1),
(46, 1, 'Carnivals, Fairs, & Festivals', 'Carnivals,-Fairs,-Festivals', '', 1),
(47, 1, 'Cartoon & Caricature Artists', 'Cartoon-Caricature-Artists', '', 1),
(48, 1, 'Children''s & Family Entertainment', 'Children''s-Family-Entertainment', '', 1),
(49, 1, 'Clowns', 'Clowns', '', 1),
(50, 1, 'Comedy Clubs', 'Comedy-Clubs', '', 1),
(51, 1, 'Commercial & Graphic Artists', 'Commercial-Graphic-Artists', '', 1),
(52, 1, 'Commercial & Graphic Arts', 'Commercial-Graphic-Arts', '', 1),
(53, 1, 'Computer Aided Design Services', 'Computer-Aided-Design-Services', '', 1),
(54, 1, 'Concert Tickets', 'Concert-Tickets', '', 1),
(55, 1, 'Crafts & Craft Supplies Retail', 'Crafts-Craft-Supplies-Retail', '', 1),
(56, 1, 'Cultural Attractions, Events, & Facilities', 'Cultural-Attractions,-Events,-Facilities', '', 1),
(57, 1, 'DVD Rentals', 'DVD-Rentals', '', 1),
(58, 1, 'Dance Companies', 'Dance-Companies', '', 1),
(59, 1, 'Decorative & Art Posters', 'Decorative-Art-Posters', '', 1),
(60, 1, 'Drafting Equipment & Supplies', 'Drafting-Equipment-Supplies', '', 1),
(61, 1, 'Embroidery & Needlework', 'Embroidery-Needlework', '', 1),
(62, 1, 'Embroidery Service', 'Embroidery-Service', '', 1),
(63, 1, 'Entertainers', 'Entertainers', '', 1),
(64, 1, 'Event Planning', 'Event-Planning', '', 1),
(65, 1, 'Event Tickets', 'Event-Tickets', '', 1),
(66, 1, 'Graphics Services', 'Graphics-Services', '', 1),
(67, 1, 'Guitars', 'Guitars', '', 1),
(68, 1, 'Headshot & Portfolio Photographers', 'Headshot-Portfolio-Photographers', '', 1),
(69, 1, 'Instrumental Music Instruction', 'Instrumental-Music-Instruction', '', 1),
(70, 1, 'Libraries', 'Libraries', '', 1),
(71, 1, 'Live Theater', 'Live-Theater', '', 1),
(72, 1, 'Logos', 'Logos', '', 1),
(73, 1, 'Magicians', 'Magicians', '', 1),
(74, 1, 'Medical & Dental Photographers', 'Medical-Dental-Photographers', '', 1),
(75, 1, 'Modeling Agencies', 'Modeling-Agencies', '', 1),
(76, 1, 'Motion Picture Production Services & Facilities', 'Motion-Picture-Production-Services-Facilities', '', 1),
(77, 1, 'Movie Theaters', 'Movie-Theaters', '', 1),
(78, 1, 'Museums', 'Museums', '', 1),
(79, 1, 'Music', 'Music', '', 1),
(80, 1, 'Music Downloads', 'Music-Downloads', '', 1),
(81, 1, 'Music Instruction', 'Music-Instruction', '', 1),
(82, 1, 'Music Stores', 'Music-Stores', '', 1),
(83, 1, 'Music Venues', 'Music-Venues', '', 1),
(84, 1, 'Musical Instruments Retail', 'Musical-Instruments-Retail', '', 1),
(85, 1, 'Night Clubs', 'Night-Clubs', '', 1),
(86, 1, 'Party Planning', 'Party-Planning', '', 1),
(87, 1, 'Passport & Identification Photographers', 'Passport-Identification-Photographers', '', 1),
(88, 1, 'Performing Arts', 'Performing-Arts', '', 1),
(89, 1, 'Performing Arts Schools', 'Performing-Arts-Schools', '', 1),
(90, 1, 'Photo Film Developing', 'Photo-Film-Developing', '', 1),
(91, 1, 'Photofinishing Retail', 'Photofinishing-Retail', '', 1),
(92, 1, 'Photographers Commercial & Industrial', 'Photographers-Commercial-Industrial', '', 1),
(93, 1, 'Photographic Equipment & Supplies Retail', 'Photographic-Equipment-Supplies-Retail', '', 1),
(94, 1, 'Photography', 'Photography', '', 1),
(95, 1, 'Photography Schools', 'Photography-Schools', '', 1),
(96, 1, 'Photography Studios', 'Photography-Studios', '', 1),
(97, 1, 'Piano Instruction', 'Piano-Instruction', '', 1),
(98, 1, 'Piano Tuning, Repair, & Refinishing', 'Piano-Tuning,-Repair,-Refinishing', '', 1),
(99, 1, 'Pianos & Organs Retail', 'Pianos-Organs-Retail', '', 1),
(100, 1, 'Portrait & Commercial Photography', 'Portrait-Commercial-Photography', '', 1),
(101, 1, 'Portrait Photographers', 'Portrait-Photographers', '', 1),
(102, 1, 'Portrait Studios', 'Portrait-Studios', '', 1),
(103, 1, 'Professional Photographers', 'Professional-Photographers', '', 1),
(104, 1, 'Projection Equipment & Supplies', 'Projection-Equipment-Supplies', '', 1),
(105, 1, 'Public Libraries', 'Public-Libraries', '', 1),
(106, 1, 'Recording Studios', 'Recording-Studios', '', 1),
(107, 1, 'Scrapbooking', 'Scrapbooking', '', 1),
(108, 1, 'Special Occasion Photography', 'Special-Occasion-Photography', '', 1),
(109, 1, 'Talent Agencies & Casting Services', 'Talent-Agencies-Casting-Services', '', 1),
(110, 1, 'Teen Clubs', 'Teen-Clubs', '', 1),
(111, 1, 'Theater Tickets', 'Theater-Tickets', '', 1),
(112, 1, 'Theatrical Stage Sets Design & Construction', 'Theatrical-Stage-Sets-Design-Construction', '', 1),
(113, 1, 'Used Pianos', 'Used-Pianos', '', 1),
(114, 1, 'Video Production Services', 'Video-Production-Services', '', 1),
(115, 1, 'Video Production Services Commercial', 'Video-Production-Services-Commercial', '', 1),
(116, 1, 'Videography', 'Videography', '', 1),
(117, 1, 'Videotape & DVD Duplication Services Commercial', 'Videotape-DVD-Duplication-Services-Commercial', '', 1),
(118, 1, 'Videotape, CD, & DVD Duplication & Transfer Services', 'Videotape,-CD,-DVD-Duplication-Transfer-Services', '', 1),
(119, 1, 'Violins', 'Violins', '', 1),
(120, 1, 'Visual Arts', 'Visual-Arts', '', 1),
(121, 1, 'Vocal Music Instruction', 'Vocal-Music-Instruction', '', 1),
(122, 1, 'Wedding Photography & Videos', 'Wedding-Photography-Videos', '', 1),
(123, 1, 'Yarn', 'Yarn', '', 1),
(124, 1, 'Zoos', 'Zoos', '', 1),
(125, 2, 'ATV Dealers', 'ATV-Dealers', '', 1),
(126, 2, 'Airport Parking', 'Airport-Parking', '', 1),
(127, 2, 'Antique & Classic Auto Dealers', 'Antique-Classic-Auto-Dealers', '', 1),
(128, 2, 'Antique & Classic Auto Restoration & Repair', 'Antique-Classic-Auto-Restoration-Repair', '', 1),
(129, 2, 'Auto & Truck Brokers', 'Auto-Truck-Brokers', '', 1),
(130, 2, 'Auto & Truck Wrecking', 'Auto-Truck-Wrecking', '', 1),
(131, 2, 'Auto Accessories Retail', 'Auto-Accessories-Retail', '', 1),
(132, 2, 'Auto Air Conditioning & Heating Service & Repair', 'Auto-Air-Conditioning-Heating-Service-Repair', '', 1),
(133, 2, 'Auto Alarms & Security Systems', 'Auto-Alarms-Security-Systems', '', 1),
(134, 2, 'Auto Alignment, Frames, & Axles Service & Repair', 'Auto-Alignment,-Frames,-Axles-Service-Repair', '', 1),
(135, 2, 'Auto Body Repair', 'Auto-Body-Repair', '', 1),
(136, 2, 'Auto Buyers', 'Auto-Buyers', '', 1),
(137, 2, 'Auto Cleaning & Detailing', 'Auto-Cleaning-Detailing', '', 1),
(138, 2, 'Auto Customizing, Conversion, & Restoration', 'Auto-Customizing,-Conversion,-Restoration', '', 1),
(139, 2, 'Auto Electrical Systems Service & Repair', 'Auto-Electrical-Systems-Service-Repair', '', 1),
(140, 2, 'Auto Emissions Testing & Repair', 'Auto-Emissions-Testing-Repair', '', 1),
(141, 2, 'Auto Engine Rebuilding', 'Auto-Engine-Rebuilding', '', 1),
(142, 2, 'Auto Engines & Engine Parts', 'Auto-Engines-Engine-Parts', '', 1),
(143, 2, 'Auto Engines Service & Repair', 'Auto-Engines-Service-Repair', '', 1),
(144, 2, 'Auto Fleet Sales', 'Auto-Fleet-Sales', '', 1),
(145, 2, 'Auto Glass Coating & Tinting', 'Auto-Glass-Coating-Tinting', '', 1),
(146, 2, 'Auto Glass Repair', 'Auto-Glass-Repair', '', 1),
(147, 2, 'Auto Glass Retail', 'Auto-Glass-Retail', '', 1),
(148, 2, 'Auto Inspection', 'Auto-Inspection', '', 1),
(149, 2, 'Auto Leasing', 'Auto-Leasing', '', 1),
(150, 2, 'Auto Licenses', 'Auto-Licenses', '', 1),
(151, 2, 'Auto Locks & Lockout Services', 'Auto-Locks-Lockout-Services', '', 1),
(152, 2, 'Auto Oil & Lube', 'Auto-Oil-Lube', '', 1),
(153, 2, 'Auto Parts', 'Auto-Parts', '', 1),
(154, 2, 'Auto Racing', 'Auto-Racing', '', 1),
(155, 2, 'Auto Radiator Repair & Rebuilding', 'Auto-Radiator-Repair-Rebuilding', '', 1),
(156, 2, 'Auto Radiators Retail', 'Auto-Radiators-Retail', '', 1),
(157, 2, 'Auto Recycling & Dismantling', 'Auto-Recycling-Dismantling', '', 1),
(158, 2, 'Auto Registration, Tags, & Titles', 'Auto-Registration,-Tags,-Titles', '', 1),
(159, 2, 'Auto Rental Commercial', 'Auto-Rental-Commercial', '', 1),
(160, 2, 'Auto Repair', 'Auto-Repair', '', 1),
(161, 2, 'Auto Salvage Yards', 'Auto-Salvage-Yards', '', 1),
(162, 2, 'Auto Seat Covers, Tops, & Upholstery Retail', 'Auto-Seat-Covers,-Tops,-Upholstery-Retail', '', 1),
(163, 2, 'Auto Tire Shop Equipment & Supplies', 'Auto-Tire-Shop-Equipment-Supplies', '', 1),
(164, 2, 'Auto Tune Ups', 'Auto-Tune-Ups', '', 1),
(165, 2, 'Auto Washing & Polishing', 'Auto-Washing-Polishing', '', 1),
(166, 2, 'Auto Wheel Covers & Hubcaps', 'Auto-Wheel-Covers-Hubcaps', '', 1),
(167, 2, 'Auto Wheels Retail', 'Auto-Wheels-Retail', '', 1),
(168, 2, 'Boat Trailer Dealers', 'Boat-Trailer-Dealers', '', 1),
(169, 2, 'Brake Repair', 'Brake-Repair', '', 1),
(170, 2, 'Brake Shoe Bonding & Exchanging', 'Brake-Shoe-Bonding-Exchanging', '', 1),
(171, 2, 'Camping & Travel Trailer Dealers & Manufacturers', 'Camping-Travel-Trailer-Dealers-Manufacturers', '', 1),
(172, 2, 'Car Auctions', 'Car-Auctions', '', 1),
(173, 2, 'Car Dealers', 'Car-Dealers', '', 1),
(174, 2, 'Car Leasing', 'Car-Leasing', '', 1),
(175, 2, 'Car Loans', 'Car-Loans', '', 1),
(176, 2, 'Car Rentals', 'Car-Rentals', '', 1),
(177, 2, 'Car Stereo & Radio Installation & Repair', 'Car-Stereo-Radio-Installation-Repair', '', 1),
(178, 2, 'Car Stereos & Radios Retail', 'Car-Stereos-Radios-Retail', '', 1),
(179, 2, 'Car Storage', 'Car-Storage', '', 1),
(180, 2, 'Car Transports', 'Car-Transports', '', 1),
(181, 2, 'Cars Service & Repair', 'Cars-Service-Repair', '', 1),
(182, 2, 'Clutches & Facings', 'Clutches-Facings', '', 1),
(183, 2, 'Custom Car Parts & Accessories', 'Custom-Car-Parts-Accessories', '', 1),
(184, 2, 'Cylinder Heads & Blocks Rebuilding & Repairing', 'Cylinder-Heads-Blocks-Rebuilding-Repairing', '', 1),
(185, 2, 'Defensive Driving Instruction', 'Defensive-Driving-Instruction', '', 1),
(186, 2, 'Driver Education', 'Driver-Education', '', 1),
(187, 2, 'Exotic & Luxury Car Rental & Leasing', 'Exotic-Luxury-Car-Rental-Leasing', '', 1),
(188, 2, 'Gas Stations', 'Gas-Stations', '', 1),
(189, 2, 'Go Carts', 'Go-Carts', '', 1),
(190, 2, 'Junk Car Removal', 'Junk-Car-Removal', '', 1),
(191, 2, 'Lift Trucks', 'Lift-Trucks', '', 1),
(192, 2, 'Limousine Rentals', 'Limousine-Rentals', '', 1),
(193, 2, 'Motorcycle Insurance', 'Motorcycle-Insurance', '', 1),
(194, 2, 'Motorcycle, Motor Scooter, & Minibike Parts & Accessories Retail', 'Motorcycle,-Motor-Scooter,-Minibike-Parts-Accessories-Retail', '', 1),
(195, 2, 'Motorcycles', 'Motorcycles', '', 1),
(196, 2, 'Motorcycles, Motor Scooters, & ATVs Service & Repair', 'Motorcycles,-Motor-Scooters,-ATVs-Service-Repair', '', 1),
(197, 2, 'Mufflers', 'Mufflers', '', 1),
(198, 2, 'New & Used Auto Body Parts', 'New-Used-Auto-Body-Parts', '', 1),
(199, 2, 'Parking', 'Parking', '', 1),
(200, 2, 'Parking Garages', 'Parking-Garages', '', 1),
(201, 2, 'RV & Camper Storage', 'RV-Camper-Storage', '', 1),
(202, 2, 'RVs & Campers Service & Repair', 'RVs-Campers-Service-Repair', '', 1),
(203, 2, 'Race Car Dealers', 'Race-Car-Dealers', '', 1),
(204, 2, 'Roadside Assistance', 'Roadside-Assistance', '', 1),
(205, 2, 'Tire Customizing', 'Tire-Customizing', '', 1),
(206, 2, 'Tires', 'Tires', '', 1),
(207, 2, 'Tires Wholesale & Manufacturers', 'Tires-Wholesale-Manufacturers', '', 1),
(208, 2, 'Towing', 'Towing', '', 1),
(209, 2, 'Traffic Schools', 'Traffic-Schools', '', 1),
(210, 2, 'Trailer Dealers', 'Trailer-Dealers', '', 1),
(211, 2, 'Trailer Hitches Retail', 'Trailer-Hitches-Retail', '', 1),
(212, 2, 'Trailer Hitches Wholesale & Manufacturers', 'Trailer-Hitches-Wholesale-Manufacturers', '', 1),
(213, 2, 'Trailer Rental & Leasing Commercial & Industrial', 'Trailer-Rental-Leasing-Commercial-Industrial', '', 1),
(214, 2, 'Trailers Rental & Leasing', 'Trailers-Rental-Leasing', '', 1),
(215, 2, 'Transmission Repair', 'Transmission-Repair', '', 1),
(216, 2, 'Truck Body Dealers', 'Truck-Body-Dealers', '', 1),
(217, 2, 'Truck Caps & Tonneau Covers Retail', 'Truck-Caps-Tonneau-Covers-Retail', '', 1),
(218, 2, 'Truck Driving Schools', 'Truck-Driving-Schools', '', 1),
(219, 2, 'Truck Equipment, Parts, & Accessories Retail', 'Truck-Equipment,-Parts,-Accessories-Retail', '', 1),
(220, 2, 'Truck Insurance Commercial', 'Truck-Insurance-Commercial', '', 1),
(221, 2, 'Truck Rental', 'Truck-Rental', '', 1),
(222, 2, 'Truck Tires', 'Truck-Tires', '', 1),
(223, 2, 'Truck Transport', 'Truck-Transport', '', 1),
(224, 2, 'Trucks For Sale', 'Trucks-For-Sale', '', 1),
(225, 2, 'Trucks Service & Repair', 'Trucks-Service-Repair', '', 1),
(226, 2, 'Used & Rebuilt Auto Parts Retail', 'Used-Rebuilt-Auto-Parts-Retail', '', 1),
(227, 2, 'Used & Rebuilt Tires', 'Used-Rebuilt-Tires', '', 1),
(228, 2, 'Used Cars', 'Used-Cars', '', 1),
(229, 2, 'Used Cars Wholesale', 'Used-Cars-Wholesale', '', 1),
(230, 2, 'Used Motorcycles & Motor Scooters', 'Used-Motorcycles-Motor-Scooters', '', 1),
(231, 2, 'Used RVs', 'Used-RVs', '', 1),
(232, 2, 'Used Trucks', 'Used-Trucks', '', 1),
(233, 2, 'Utility Trailer Dealers', 'Utility-Trailer-Dealers', '', 1),
(234, 2, 'Valet Parking', 'Valet-Parking', '', 1),
(235, 2, 'Van Dealers', 'Van-Dealers', '', 1),
(236, 2, 'Van Rental', 'Van-Rental', '', 1),
(237, 2, 'Wheel & Tire Balancing', 'Wheel-Tire-Balancing', '', 1),
(238, 3, 'Advertising', 'Advertising', '', 1),
(239, 3, 'Advertising & Design Agencies', 'Advertising-Design-Agencies', '', 1),
(240, 3, 'Advertising & Promotional Gifts', 'Advertising-Promotional-Gifts', '', 1),
(241, 3, 'Advertising & Promotional Product Dealers', 'Advertising-Promotional-Product-Dealers', '', 1),
(242, 3, 'Advertising Agencies', 'Advertising-Agencies', '', 1),
(243, 3, 'Advertising Layout & Design Printing', 'Advertising-Layout-Design-Printing', '', 1),
(244, 3, 'Advertising Signs', 'Advertising-Signs', '', 1),
(245, 3, 'Air Duct Cleaning Commercial', 'Air-Duct-Cleaning-Commercial', '', 1),
(246, 3, 'Armored Car Services', 'Armored-Car-Services', '', 1),
(247, 3, 'Asbestos & Lead Abatement & Removal Services', 'Asbestos-Lead-Abatement-Removal-Services', '', 1),
(248, 3, 'Auctioneers & Auction Houses', 'Auctioneers-Auction-Houses', '', 1),
(249, 3, 'Auctions', 'Auctions', '', 1),
(250, 3, 'Audio Production & Recording', 'Audio-Production-Recording', '', 1),
(251, 3, 'Blinds', 'Blinds', '', 1),
(252, 3, 'Bookbinding', 'Bookbinding', '', 1),
(253, 3, 'Burglar Alarm Systems & Monitoring Commercial & Industrial', 'Burglar-Alarm-Systems-Monitoring-Commercial-Industrial', '', 1),
(254, 3, 'Business & Professional Associations, Organizations, & Foundations', 'Business-Professional-Associations,-Organizations,-Foundations', '', 1),
(255, 3, 'Business Brokers', 'Business-Brokers', '', 1),
(256, 3, 'Business Cards', 'Business-Cards', '', 1),
(257, 3, 'Business Consultants & Advisors', 'Business-Consultants-Advisors', '', 1),
(258, 3, 'Business Form & Card Printing', 'Business-Form-Card-Printing', '', 1),
(259, 3, 'Business Information', 'Business-Information', '', 1),
(260, 3, 'Business Management Services', 'Business-Management-Services', '', 1),
(261, 3, 'Business Opportunities', 'Business-Opportunities', '', 1),
(262, 3, 'Business Records Storage & Management', 'Business-Records-Storage-Management', '', 1),
(263, 3, 'Business Services', 'Business-Services', '', 1),
(264, 3, 'Card Access Control Systems', 'Card-Access-Control-Systems', '', 1),
(265, 3, 'Career & Vocational Counseling', 'Career-Vocational-Counseling', '', 1),
(266, 3, 'Career Planning & Employment Assistance Services', 'Career-Planning-Employment-Assistance-Services', '', 1),
(267, 3, 'Chambers of Commerce', 'Chambers-of-Commerce', '', 1),
(268, 3, 'Civil Engineers', 'Civil-Engineers', '', 1),
(269, 3, 'Classified Advertising', 'Classified-Advertising', '', 1),
(270, 3, 'Clerical Employment Agencies', 'Clerical-Employment-Agencies', '', 1),
(271, 3, 'Coffee Break Service & Supplies', 'Coffee-Break-Service-Supplies', '', 1),
(272, 3, 'College Placement', 'College-Placement', '', 1),
(273, 3, 'Commercial Carpet Cleaning', 'Commercial-Carpet-Cleaning', '', 1),
(274, 3, 'Commercial Landscaping', 'Commercial-Landscaping', '', 1),
(275, 3, 'Commercial Pest Control', 'Commercial-Pest-Control', '', 1),
(276, 3, 'Commercial Printers', 'Commercial-Printers', '', 1),
(277, 3, 'Commercial Tree Care', 'Commercial-Tree-Care', '', 1),
(278, 3, 'Consulting Engineers', 'Consulting-Engineers', '', 1),
(279, 3, 'Convention & Meeting Facilities & Services', 'Convention-Meeting-Facilities-Services', '', 1),
(280, 3, 'Convention Exhibits & Displays', 'Convention-Exhibits-Displays', '', 1),
(281, 3, 'Convention Visitors & Information Centers', 'Convention-Visitors-Information-Centers', '', 1),
(282, 3, 'Conventions, Conferences, & Trade Shows', 'Conventions,-Conferences,-Trade-Shows', '', 1),
(283, 3, 'Copiers', 'Copiers', '', 1),
(284, 3, 'Copiers Rental & Leasing', 'Copiers-Rental-Leasing', '', 1),
(285, 3, 'Copiers Service & Repair', 'Copiers-Service-Repair', '', 1),
(286, 3, 'Copying & Duplicating Services', 'Copying-Duplicating-Services', '', 1),
(287, 3, 'Copywriters', 'Copywriters', '', 1),
(288, 3, 'Corporate Advertising Specialties & Promotions', 'Corporate-Advertising-Specialties-Promotions', '', 1),
(289, 3, 'Desks', 'Desks', '', 1),
(290, 3, 'Detective Agencies', 'Detective-Agencies', '', 1),
(291, 3, 'Dictating Machines & Supplies', 'Dictating-Machines-Supplies', '', 1),
(292, 3, 'Direct Mail Advertising Printing', 'Direct-Mail-Advertising-Printing', '', 1),
(293, 3, 'Direct Mail Advertising Services', 'Direct-Mail-Advertising-Services', '', 1),
(294, 3, 'Direct Marketing', 'Direct-Marketing', '', 1),
(295, 3, 'Display Designers & Producers', 'Display-Designers-Producers', '', 1),
(296, 3, 'Display Equipment, Fixtures, & Materials', 'Display-Equipment,-Fixtures,-Materials', '', 1),
(297, 3, 'Drapery & Curtain Cleaning Services Commercial & Industrial', 'Drapery-Curtain-Cleaning-Services-Commercial-Industrial', '', 1),
(298, 3, 'Dryer Vent Cleaning', 'Dryer-Vent-Cleaning', '', 1),
(299, 3, 'Electric Signs', 'Electric-Signs', '', 1),
(300, 3, 'Electronic Commerce', 'Electronic-Commerce', '', 1),
(301, 3, 'Employment', 'Employment', '', 1),
(302, 3, 'Employment Agencies', 'Employment-Agencies', '', 1),
(303, 3, 'Employment Background Checks', 'Employment-Background-Checks', '', 1),
(304, 3, 'Employment Screening Services', 'Employment-Screening-Services', '', 1),
(305, 3, 'Energy & Environment', 'Energy-Environment', '', 1),
(306, 3, 'Engineers', 'Engineers', '', 1),
(307, 3, 'Environmental & Ecological Consultants', 'Environmental-Ecological-Consultants', '', 1),
(308, 3, 'Environmental Services', 'Environmental-Services', '', 1),
(309, 3, 'Environmental Site Assessment & Remediation', 'Environmental-Site-Assessment-Remediation', '', 1),
(310, 3, 'Executive Search Firms', 'Executive-Search-Firms', '', 1),
(311, 3, 'Fax Equipment & Supplies', 'Fax-Equipment-Supplies', '', 1),
(312, 3, 'Fax Services', 'Fax-Services', '', 1),
(313, 3, 'Fire Alarm Systems Commercial & Industrial', 'Fire-Alarm-Systems-Commercial-Industrial', '', 1),
(314, 3, 'Fire Extinguishers Commercial & Industrial', 'Fire-Extinguishers-Commercial-Industrial', '', 1),
(315, 3, 'Fire Protection Equipment, Systems, & Services', 'Fire-Protection-Equipment,-Systems,-Services', '', 1),
(316, 3, 'Fire Sprinkler Systems', 'Fire-Sprinkler-Systems', '', 1),
(317, 3, 'Flags, Flagpoles, Banners, & Pennants Commercial', 'Flags,-Flagpoles,-Banners,-Pennants-Commercial', '', 1),
(318, 3, 'Floor Waxing, Polishing & Cleaning', 'Floor-Waxing,-Polishing-Cleaning', '', 1),
(319, 3, 'Franchising', 'Franchising', '', 1),
(320, 3, 'GPS Navigation & Tracking', 'GPS-Navigation-Tracking', '', 1),
(321, 3, 'Garbage Removal Contractors'' Equipment & Supplies', 'Garbage-Removal-Contractors''-Equipment-Supplies', '', 1),
(322, 3, 'Global Positioning Surveyors', 'Global-Positioning-Surveyors', '', 1),
(323, 3, 'Global Positioning Systems Wholesale', 'Global-Positioning-Systems-Wholesale', '', 1),
(324, 3, 'High Tech Employment Agencies', 'High-Tech-Employment-Agencies', '', 1),
(325, 3, 'Home Based Businesses', 'Home-Based-Businesses', '', 1),
(326, 3, 'Home Designing & Planning Services', 'Home-Designing-Planning-Services', '', 1),
(327, 3, 'Home Security Systems', 'Home-Security-Systems', '', 1),
(328, 3, 'Human Resource Consultants', 'Human-Resource-Consultants', '', 1),
(329, 3, 'Identification Cards & Badges', 'Identification-Cards-Badges', '', 1),
(330, 3, 'Incorporation Services', 'Incorporation-Services', '', 1),
(331, 3, 'Indoor Air Quality', 'Indoor-Air-Quality', '', 1),
(332, 3, 'Information Bureaus & Services', 'Information-Bureaus-Services', '', 1),
(333, 3, 'Inks', 'Inks', '', 1),
(334, 3, 'Interior Building Cleaning', 'Interior-Building-Cleaning', '', 1),
(335, 3, 'Invention Marketing Services', 'Invention-Marketing-Services', '', 1),
(336, 3, 'Investigation Services', 'Investigation-Services', '', 1),
(337, 3, 'Janitorial Equipment & Supplies Retail', 'Janitorial-Equipment-Supplies-Retail', '', 1),
(338, 3, 'Janitorial Services', 'Janitorial-Services', '', 1),
(339, 3, 'Job Fairs', 'Job-Fairs', '', 1),
(340, 3, 'Job Listing Services', 'Job-Listing-Services', '', 1),
(341, 3, 'Junk Dealers', 'Junk-Dealers', '', 1),
(342, 3, 'Labels & Tags', 'Labels-Tags', '', 1),
(343, 3, 'Labor Organizations', 'Labor-Organizations', '', 1),
(344, 3, 'Laboratories', 'Laboratories', '', 1),
(345, 3, 'Laboratory Equipment & Supplies', 'Laboratory-Equipment-Supplies', '', 1),
(346, 3, 'Land Surveyors', 'Land-Surveyors', '', 1),
(347, 3, 'Laundry Equipment & Supplies Dealers', 'Laundry-Equipment-Supplies-Dealers', '', 1),
(348, 3, 'Linen Supply Services', 'Linen-Supply-Services', '', 1),
(349, 3, 'Locks & Locksmiths Commercial & Industrial', 'Locks-Locksmiths-Commercial-Industrial', '', 1),
(350, 3, 'Magnetic Signs', 'Magnetic-Signs', '', 1),
(351, 3, 'Mailing Equipment & Supplies', 'Mailing-Equipment-Supplies', '', 1),
(352, 3, 'Mailing List Services', 'Mailing-List-Services', '', 1),
(353, 3, 'Management Consultants', 'Management-Consultants', '', 1),
(354, 3, 'Market Research', 'Market-Research', '', 1),
(355, 3, 'Marketing & Public Relations', 'Marketing-Public-Relations', '', 1),
(356, 3, 'Marketing Consultants', 'Marketing-Consultants', '', 1),
(357, 3, 'Medical & Infectious Waste Disposal', 'Medical-Infectious-Waste-Disposal', '', 1),
(358, 3, 'Medical Personnel Employment Agencies', 'Medical-Personnel-Employment-Agencies', '', 1),
(359, 3, 'Medical Transcription', 'Medical-Transcription', '', 1),
(360, 3, 'Modular Offices', 'Modular-Offices', '', 1),
(361, 3, 'Mold Inspection', 'Mold-Inspection', '', 1),
(362, 3, 'Neon Signs', 'Neon-Signs', '', 1),
(363, 3, 'Newspaper Publishers', 'Newspaper-Publishers', '', 1),
(364, 3, 'Nurses Recruiting', 'Nurses-Recruiting', '', 1),
(365, 3, 'Office Cleaning', 'Office-Cleaning', '', 1),
(366, 3, 'Office Furniture', 'Office-Furniture', '', 1),
(367, 3, 'Office Furniture & Equipment Installation', 'Office-Furniture-Equipment-Installation', '', 1),
(368, 3, 'Office Furniture & Equipment Rental & Leasing', 'Office-Furniture-Equipment-Rental-Leasing', '', 1),
(369, 3, 'Office Furniture & Equipment Wholesale & Manufacturers', 'Office-Furniture-Equipment-Wholesale-Manufacturers', '', 1),
(370, 3, 'Office Movers & Relocators', 'Office-Movers-Relocators', '', 1),
(371, 3, 'Office Supplies', 'Office-Supplies', '', 1),
(372, 3, 'Packing & Crating', 'Packing-Crating', '', 1),
(373, 3, 'Paper Shredding', 'Paper-Shredding', '', 1),
(374, 3, 'Partitions & Dividers', 'Partitions-Dividers', '', 1),
(375, 3, 'Phone Directory & Guide Publishers', 'Phone-Directory-Guide-Publishers', '', 1),
(376, 3, 'Phone Directory Advertising', 'Phone-Directory-Advertising', '', 1),
(377, 3, 'Polygraph & Lie Detection Services', 'Polygraph-Lie-Detection-Services', '', 1),
(378, 3, 'Pressure & Chemical Cleaning Industrial', 'Pressure-Chemical-Cleaning-Industrial', '', 1),
(379, 3, 'Pressure Cleaning', 'Pressure-Cleaning', '', 1),
(380, 3, 'Printers', 'Printers', '', 1),
(381, 3, 'Printing & Publishing', 'Printing-Publishing', '', 1),
(382, 3, 'Printing Equipment & Supplies Dealers', 'Printing-Equipment-Supplies-Dealers', '', 1),
(383, 3, 'Private Detectives', 'Private-Detectives', '', 1),
(384, 3, 'Private Investigators', 'Private-Investigators', '', 1),
(385, 3, 'Professional Employer Organizations', 'Professional-Employer-Organizations', '', 1),
(386, 3, 'Professional Engineers', 'Professional-Engineers', '', 1),
(387, 3, 'Professional Organizers', 'Professional-Organizers', '', 1),
(388, 3, 'Publishers', 'Publishers', '', 1),
(389, 3, 'Radon Testing & Mitigation', 'Radon-Testing-Mitigation', '', 1),
(390, 3, 'Records Destruction', 'Records-Destruction', '', 1),
(391, 3, 'Records Management', 'Records-Management', '', 1),
(392, 3, 'Records Searchers', 'Records-Searchers', '', 1),
(393, 3, 'Recruiters', 'Recruiters', '', 1),
(394, 3, 'Recruitment & Staffing Services', 'Recruitment-Staffing-Services', '', 1),
(395, 3, 'Recycling Services, Equipment, & Supplies', 'Recycling-Services,-Equipment,-Supplies', '', 1),
(396, 3, 'Resume Services', 'Resume-Services', '', 1),
(397, 3, 'Safes & Vaults Dealers', 'Safes-Vaults-Dealers', '', 1),
(398, 3, 'Scales Industrial & Commercial', 'Scales-Industrial-Commercial', '', 1),
(399, 3, 'Scales Retail', 'Scales-Retail', '', 1),
(400, 3, 'Screen Printing', 'Screen-Printing', '', 1),
(401, 3, 'Screen Printing Equipment & Supplies', 'Screen-Printing-Equipment-Supplies', '', 1),
(402, 3, 'Security & Industrial Video Equipment', 'Security-Industrial-Video-Equipment', '', 1),
(403, 3, 'Security Consultants & Agencies', 'Security-Consultants-Agencies', '', 1),
(404, 3, 'Security Guards', 'Security-Guards', '', 1),
(405, 3, 'Sodding & Sodding Services Commercial & Industrial', 'Sodding-Sodding-Services-Commercial-Industrial', '', 1),
(406, 3, 'Solar Energy Systems & Services Retail', 'Solar-Energy-Systems-Services-Retail', '', 1),
(407, 3, 'Stationery Engraving & Imprinting', 'Stationery-Engraving-Imprinting', '', 1),
(408, 3, 'Stationery Retail', 'Stationery-Retail', '', 1),
(409, 3, 'Structural Engineers', 'Structural-Engineers', '', 1),
(410, 3, 'Surveillance Equipment & Services', 'Surveillance-Equipment-Services', '', 1),
(411, 3, 'Surveyors', 'Surveyors', '', 1),
(412, 3, 'Telemarketing Services', 'Telemarketing-Services', '', 1),
(413, 3, 'Temporary Jobs', 'Temporary-Jobs', '', 1),
(414, 3, 'Testing Laboratories', 'Testing-Laboratories', '', 1),
(415, 3, 'Time Clocks', 'Time-Clocks', '', 1),
(416, 3, 'Toner Cartridges', 'Toner-Cartridges', '', 1),
(417, 3, 'Transcription Services', 'Transcription-Services', '', 1),
(418, 3, 'Translators & Interpreters', 'Translators-Interpreters', '', 1),
(419, 3, 'Trash Containers', 'Trash-Containers', '', 1),
(420, 3, 'Trash Removal', 'Trash-Removal', '', 1),
(421, 3, 'Used Office Furniture & Equipment', 'Used-Office-Furniture-Equipment', '', 1),
(422, 3, 'Utilities', 'Utilities', '', 1),
(423, 3, 'Venetian Blinds', 'Venetian-Blinds', '', 1),
(424, 3, 'Vent Hood Cleaning', 'Vent-Hood-Cleaning', '', 1),
(425, 3, 'Videoconferencing Services', 'Videoconferencing-Services', '', 1),
(426, 3, 'Virtual Offices & Assistants', 'Virtual-Offices-Assistants', '', 1),
(427, 3, 'Waste Disposal Industrial', 'Waste-Disposal-Industrial', '', 1),
(428, 3, 'Waste Management', 'Waste-Management', '', 1),
(429, 3, 'Water & Sewage Companies', 'Water-Sewage-Companies', '', 1),
(430, 3, 'Water Filtration & Purification Equipment Retail', 'Water-Filtration-Purification-Equipment-Retail', '', 1),
(431, 3, 'Water Pressure Cleaning Services, Equipment, & Supplies', 'Water-Pressure-Cleaning-Services,-Equipment,-Supplies', '', 1),
(432, 3, 'Water Purification Services', 'Water-Purification-Services', '', 1),
(433, 3, 'Water Softening & Conditioning Equipment, Service, & Supplies', 'Water-Softening-Conditioning-Equipment,-Service,-Supplies', '', 1),
(434, 3, 'Water Testing Laboratories', 'Water-Testing-Laboratories', '', 1),
(435, 3, 'Water Treatment Service, Equipment, & Supplies', 'Water-Treatment-Service,-Equipment,-Supplies', '', 1),
(436, 3, 'Water Utility Companies', 'Water-Utility-Companies', '', 1),
(437, 3, 'Window Cleaning Commercial & Industrial', 'Window-Cleaning-Commercial-Industrial', '', 1),
(438, 4, 'Alterations & Tailoring', 'Alterations-Tailoring', '', 1),
(439, 4, 'Antique & Estate Jewelry Retail', 'Antique-Estate-Jewelry-Retail', '', 1),
(440, 4, 'Athletic & Dance Shoes Retail', 'Athletic-Dance-Shoes-Retail', '', 1),
(441, 4, 'Boots & Safety Shoes Retail', 'Boots-Safety-Shoes-Retail', '', 1),
(442, 4, 'Boots & Shoes Repair', 'Boots-Shoes-Repair', '', 1),
(443, 4, 'Boutiques', 'Boutiques', '', 1),
(444, 4, 'Bras & Girdles Retail', 'Bras-Girdles-Retail', '', 1),
(445, 4, 'Bridal Shops & Services', 'Bridal-Shops-Services', '', 1),
(446, 4, 'Bridal Supplies Retail', 'Bridal-Supplies-Retail', '', 1),
(447, 4, 'Children''s & Infants'' Accessories', 'Children''s-Infants''-Accessories', '', 1),
(448, 4, 'Children''s & Infants'' Clothing Retail', 'Children''s-Infants''-Clothing-Retail', '', 1),
(449, 4, 'Clothing & Accessories Consignment & Resale', 'Clothing-Accessories-Consignment-Resale', '', 1),
(450, 4, 'Clothing Accessories Retail', 'Clothing-Accessories-Retail', '', 1),
(451, 4, 'Clothing Stores', 'Clothing-Stores', '', 1),
(452, 4, 'Clothing Wholesale & Manufacturers', 'Clothing-Wholesale-Manufacturers', '', 1),
(453, 4, 'Costume Sales & Rental', 'Costume-Sales-Rental', '', 1),
(454, 4, 'Custom Embroidered Sportswear', 'Custom-Embroidered-Sportswear', '', 1),
(455, 4, 'Custom Jewelry Retail', 'Custom-Jewelry-Retail', '', 1),
(456, 4, 'Custom T-Shirts', 'Custom-T-Shirts', '', 1),
(457, 4, 'Dance Clothing Retail', 'Dance-Clothing-Retail', '', 1),
(458, 4, 'Diamonds Brokers & Buyers', 'Diamonds-Brokers-Buyers', '', 1),
(459, 4, 'Diamonds Retail', 'Diamonds-Retail', '', 1),
(460, 4, 'Dresses Retail', 'Dresses-Retail', '', 1),
(461, 4, 'Dry Cleaners', 'Dry-Cleaners', '', 1),
(462, 4, 'Eyeglasses, Sunglasses, & Goggles Retail', 'Eyeglasses,-Sunglasses,-Goggles-Retail', '', 1),
(463, 4, 'Formal Wear Sales & Rental', 'Formal-Wear-Sales-Rental', '', 1),
(464, 4, 'Garment Racks & Hangers', 'Garment-Racks-Hangers', '', 1),
(465, 4, 'Gemstones Retail', 'Gemstones-Retail', '', 1),
(466, 4, 'Gold, Silver, & Platinum Dealers', 'Gold,-Silver,-Platinum-Dealers', '', 1),
(467, 4, 'Hair Accessories Retail', 'Hair-Accessories-Retail', '', 1),
(468, 4, 'Handbags Retail', 'Handbags-Retail', '', 1),
(469, 4, 'Handbags Wholesale & Manufacturers', 'Handbags-Wholesale-Manufacturers', '', 1),
(470, 4, 'Jewelers'' Equipment & Supplies Retail', 'Jewelers''-Equipment-Supplies-Retail', '', 1),
(471, 4, 'Jewelry', 'Jewelry', '', 1),
(472, 4, 'Jewelry Brokers & Buyers', 'Jewelry-Brokers-Buyers', '', 1),
(473, 4, 'Jewelry Display Cases', 'Jewelry-Display-Cases', '', 1),
(474, 4, 'Jewelry Repair', 'Jewelry-Repair', '', 1),
(475, 4, 'Jewelry Wholesale & Manufacturers', 'Jewelry-Wholesale-Manufacturers', '', 1),
(476, 4, 'Laundries', 'Laundries', '', 1),
(477, 4, 'Mannequins', 'Mannequins', '', 1),
(478, 4, 'Masquerade & Theatrical Costumes Sales & Rental', 'Masquerade-Theatrical-Costumes-Sales-Rental', '', 1),
(479, 4, 'Maternity Clothes', 'Maternity-Clothes', '', 1),
(480, 4, 'Men''s Clothing Retail', 'Men''s-Clothing-Retail', '', 1),
(481, 4, 'Men''s Shoes', 'Men''s-Shoes', '', 1),
(482, 4, 'Riding Clothing & Equipment', 'Riding-Clothing-Equipment', '', 1),
(483, 4, 'Safety Clothing & Equipment', 'Safety-Clothing-Equipment', '', 1),
(484, 4, 'Shoe Stores', 'Shoe-Stores', '', 1),
(485, 4, 'Sportswear Retail', 'Sportswear-Retail', '', 1),
(486, 4, 'Swimwear Retail', 'Swimwear-Retail', '', 1),
(487, 4, 'T-Shirts Retail', 'T-Shirts-Retail', '', 1),
(488, 4, 'T-Shirts Wholesale & Manufacturers', 'T-Shirts-Wholesale-Manufacturers', '', 1),
(489, 4, 'Tailors', 'Tailors', '', 1),
(490, 4, 'Tuxedos Sales & Rental', 'Tuxedos-Sales-Rental', '', 1),
(491, 4, 'Uniforms Commercial & Industrial', 'Uniforms-Commercial-Industrial', '', 1),
(492, 4, 'Uniforms Retail', 'Uniforms-Retail', '', 1),
(493, 4, 'Used Clothing Retail', 'Used-Clothing-Retail', '', 1),
(494, 4, 'Watches Retail', 'Watches-Retail', '', 1),
(495, 4, 'Watches Service & Repair', 'Watches-Service-Repair', '', 1),
(496, 4, 'Wedding Dresses', 'Wedding-Dresses', '', 1),
(497, 4, 'Wedding Jewelry', 'Wedding-Jewelry', '', 1),
(498, 4, 'Women''s Clothing', 'Women''s-Clothing', '', 1),
(499, 4, 'Women''s Clothing Retail Plus Sizes', 'Women''s-Clothing-Retail-Plus-Sizes', '', 1),
(500, 4, 'Women''s Shoes', 'Women''s-Shoes', '', 1),
(501, 4, 'Women''s Underwear & Lingerie Retail', 'Women''s-Underwear-Lingerie-Retail', '', 1),
(502, 5, 'Adoption Agencies', 'Adoption-Agencies', '', 1),
(503, 5, 'Adult Day Care Centers', 'Adult-Day-Care-Centers', '', 1),
(504, 5, 'Ambulance Services', 'Ambulance-Services', '', 1),
(505, 5, 'Army & Navy Stores', 'Army-Navy-Stores', '', 1),
(506, 5, 'Assembly of God Churches', 'Assembly-of-God-Churches', '', 1),
(507, 5, 'Astrologers', 'Astrologers', '', 1),
(508, 5, 'Baptist Churches', 'Baptist-Churches', '', 1),
(509, 5, 'Bibles & Religious Books', 'Bibles-Religious-Books', '', 1),
(510, 5, 'Birth Records', 'Birth-Records', '', 1),
(511, 5, 'Catholic Churches', 'Catholic-Churches', '', 1),
(512, 5, 'Charitable & Nonprofit Organizations', 'Charitable-Nonprofit-Organizations', '', 1),
(513, 5, 'Charities', 'Charities', '', 1),
(514, 5, 'Child Care Centers', 'Child-Care-Centers', '', 1),
(515, 5, 'Child Care Services', 'Child-Care-Services', '', 1),
(516, 5, 'Child Support Collection & Enforcement', 'Child-Support-Collection-Enforcement', '', 1),
(517, 5, 'Christian Churches', 'Christian-Churches', '', 1),
(518, 5, 'Christian Science Practitioners', 'Christian-Science-Practitioners', '', 1),
(519, 5, 'Church & Religious Associations & Organizations', 'Church-Religious-Associations-Organizations', '', 1),
(520, 5, 'Church Furnishings', 'Church-Furnishings', '', 1),
(521, 5, 'Church Supplies', 'Church-Supplies', '', 1),
(522, 5, 'Church of Christ Churches', 'Church-of-Christ-Churches', '', 1),
(523, 5, 'Church of Jesus Christ of Latter Day Saints Churches', 'Church-of-Jesus-Christ-of-Latter-Day-Saints-Churches', '', 1),
(524, 5, 'Churches', 'Churches', '', 1),
(525, 5, 'Community Centers', 'Community-Centers', '', 1),
(526, 5, 'Community Organizations', 'Community-Organizations', '', 1),
(527, 5, 'Community Services', 'Community-Services', '', 1),
(528, 5, 'Correctional Institutions', 'Correctional-Institutions', '', 1),
(529, 5, 'Counseling Centers', 'Counseling-Centers', '', 1),
(530, 5, 'Counseling Services', 'Counseling-Services', '', 1),
(531, 5, 'County Government Offices', 'County-Government-Offices', '', 1),
(532, 5, 'Courts', 'Courts', '', 1),
(533, 5, 'Crisis Intervention Services', 'Crisis-Intervention-Services', '', 1),
(534, 5, 'Divorce Counseling & Mediation', 'Divorce-Counseling-Mediation', '', 1),
(535, 5, 'Earthquake Products & Services', 'Earthquake-Products-Services', '', 1),
(536, 5, 'Federal Government Offices', 'Federal-Government-Offices', '', 1),
(537, 5, 'Fire Departments', 'Fire-Departments', '', 1),
(538, 5, 'Flood Assistance', 'Flood-Assistance', '', 1),
(539, 5, 'Fortune Tellers', 'Fortune-Tellers', '', 1),
(540, 5, 'Fraternal Organizations', 'Fraternal-Organizations', '', 1),
(541, 5, 'Government Contractors', 'Government-Contractors', '', 1),
(542, 5, 'Government Offices', 'Government-Offices', '', 1),
(543, 5, 'Home Child Care', 'Home-Child-Care', '', 1),
(544, 5, 'Housing Authorities', 'Housing-Authorities', '', 1),
(545, 5, 'Immigration & Naturalization', 'Immigration-Naturalization', '', 1),
(546, 5, 'Individual & Family Services Organizations', 'Individual-Family-Services-Organizations', '', 1),
(547, 5, 'Individual Counseling', 'Individual-Counseling', '', 1),
(548, 5, 'Jehovah''s Witness Churches', 'Jehovah''s-Witness-Churches', '', 1),
(549, 5, 'Local Government Offices', 'Local-Government-Offices', '', 1),
(550, 5, 'Lutheran Churches', 'Lutheran-Churches', '', 1),
(551, 5, 'Marriage & Family Counseling', 'Marriage-Family-Counseling', '', 1),
(552, 5, 'Marriage & Family Counseling Information & Referral Services', 'Marriage-Family-Counseling-Information-Referral-Services', '', 1),
(553, 5, 'Marriage Records', 'Marriage-Records', '', 1),
(554, 5, 'Medical & Surgical Emergency Services', 'Medical-Surgical-Emergency-Services', '', 1),
(555, 5, 'Messianic Churches', 'Messianic-Churches', '', 1),
(556, 5, 'Methodist Churches', 'Methodist-Churches', '', 1),
(557, 5, 'Military & Veterans Organizations', 'Military-Veterans-Organizations', '', 1),
(558, 5, 'Military Recruiting', 'Military-Recruiting', '', 1),
(559, 5, 'Ministries Churches', 'Ministries-Churches', '', 1),
(560, 5, 'Mission Churches', 'Mission-Churches', '', 1),
(561, 5, 'Mosques', 'Mosques', '', 1),
(562, 5, 'Municipal Courts', 'Municipal-Courts', '', 1),
(563, 5, 'National Guard', 'National-Guard', '', 1),
(564, 5, 'Non-Denominational Churches', 'Non-Denominational-Churches', '', 1),
(565, 5, 'Palm Readers', 'Palm-Readers', '', 1),
(566, 5, 'Pentecostal Churches', 'Pentecostal-Churches', '', 1),
(567, 5, 'Police Departments', 'Police-Departments', '', 1),
(568, 5, 'Police Equipment & Supplies', 'Police-Equipment-Supplies', '', 1),
(569, 5, 'Post Offices', 'Post-Offices', '', 1),
(570, 5, 'Presbyterian Churches', 'Presbyterian-Churches', '', 1),
(571, 5, 'Psychic Life Readings', 'Psychic-Life-Readings', '', 1),
(572, 5, 'Psychics & Mediums', 'Psychics-Mediums', '', 1),
(573, 5, 'Public Health & Safety', 'Public-Health-Safety', '', 1),
(574, 5, 'Religious Goods Stores', 'Religious-Goods-Stores', '', 1),
(575, 5, 'Residential Care Facilities', 'Residential-Care-Facilities', '', 1),
(576, 5, 'Retirement & Life Care Centers', 'Retirement-Life-Care-Centers', '', 1),
(577, 5, 'Retirement Communities & Homes', 'Retirement-Communities-Homes', '', 1),
(578, 5, 'Salvation Army', 'Salvation-Army', '', 1),
(579, 5, 'Search & Rescue', 'Search-Rescue', '', 1),
(580, 5, 'Senior Citizens'' Centers & Organizations', 'Senior-Citizens''-Centers-Organizations', '', 1),
(581, 5, 'Senior Citizens'' Products & Services', 'Senior-Citizens''-Products-Services', '', 1),
(582, 5, 'Sheriff''s Department', 'Sheriff''s-Department', '', 1),
(583, 5, 'Singles Organizations', 'Singles-Organizations', '', 1),
(584, 5, 'Social & Human Services', 'Social-Human-Services', '', 1),
(585, 5, 'Social & Human Services Organizations', 'Social-Human-Services-Organizations', '', 1),
(586, 5, 'Social Security Counselors & Representatives', 'Social-Security-Counselors-Representatives', '', 1),
(587, 5, 'Social Security Services', 'Social-Security-Services', '', 1),
(588, 5, 'Social Workers', 'Social-Workers', '', 1),
(589, 5, 'State Government Offices', 'State-Government-Offices', '', 1),
(590, 5, 'Synagogues & Temples', 'Synagogues-Temples', '', 1),
(591, 5, 'Unitarian Universalist Churches', 'Unitarian-Universalist-Churches', '', 1),
(592, 5, 'Urgent Care Centers', 'Urgent-Care-Centers', '', 1),
(593, 5, 'Vehicle License Services', 'Vehicle-License-Services', '', 1),
(594, 5, 'Volunteer Services', 'Volunteer-Services', '', 1),
(595, 5, 'Women''s Crisis Intervention Services', 'Women''s-Crisis-Intervention-Services', '', 1),
(596, 5, 'Youth Organizations, Centers, & Clubs', 'Youth-Organizations,-Centers,-Clubs', '', 1),
(597, 6, 'Archives, Records, & Documents Storage', 'Archives,-Records,-Documents-Storage', '', 1),
(598, 6, 'Audio & Video Recording Equipment & Supplies', 'Audio-Video-Recording-Equipment-Supplies', '', 1),
(599, 6, 'Audiovisual Equipment Rental & Leasing', 'Audiovisual-Equipment-Rental-Leasing', '', 1),
(600, 6, 'Audiovisual Equipment, Parts, & Supplies Dealers', 'Audiovisual-Equipment,-Parts,-Supplies-Dealers', '', 1),
(601, 6, 'Big Screen Televisions Retail', 'Big-Screen-Televisions-Retail', '', 1),
(602, 6, 'Broadband Services', 'Broadband-Services', '', 1),
(603, 6, 'Business Computer Dealers', 'Business-Computer-Dealers', '', 1),
(604, 6, 'Business Software', 'Business-Software', '', 1),
(605, 6, 'Cable Television Equipment Retail', 'Cable-Television-Equipment-Retail', '', 1),
(606, 6, 'Camcorders', 'Camcorders', '', 1),
(607, 6, 'Computer & Software Stores', 'Computer-Software-Stores', '', 1),
(608, 6, 'Computer Cable & Wire Installation', 'Computer-Cable-Wire-Installation', '', 1),
(609, 6, 'Computer Consultants', 'Computer-Consultants', '', 1),
(610, 6, 'Computer Furniture', 'Computer-Furniture', '', 1),
(611, 6, 'Computer Hardware & Supplies', 'Computer-Hardware-Supplies', '', 1),
(612, 6, 'Computer Networking Installation', 'Computer-Networking-Installation', '', 1),
(613, 6, 'Computer Networks', 'Computer-Networks', '', 1),
(614, 6, 'Computer Printers', 'Computer-Printers', '', 1),
(615, 6, 'Computer Repair', 'Computer-Repair', '', 1),
(616, 6, 'Computer Security', 'Computer-Security', '', 1),
(617, 6, 'Computer Software', 'Computer-Software', '', 1),
(618, 6, 'Computer Stores', 'Computer-Stores', '', 1),
(619, 6, 'Computer Supplies, Parts, & Accessories', 'Computer-Supplies,-Parts,-Accessories', '', 1),
(620, 6, 'Computer Systems Consultants & Designers', 'Computer-Systems-Consultants-Designers', '', 1),
(621, 6, 'Computer Upgrade Services', 'Computer-Upgrade-Services', '', 1),
(622, 6, 'Computers & Equipment Rental & Leasing', 'Computers-Equipment-Rental-Leasing', '', 1),
(623, 6, 'Computers & Equipment Wholesale & Manufacturers', 'Computers-Equipment-Wholesale-Manufacturers', '', 1),
(624, 6, 'DSL Services', 'DSL-Services', '', 1),
(625, 6, 'Data Recovery', 'Data-Recovery', '', 1),
(626, 6, 'Digital Imaging', 'Digital-Imaging', '', 1),
(627, 6, 'Electronic Equipment & Supplies Retail', 'Electronic-Equipment-Supplies-Retail', '', 1),
(628, 6, 'Electronic Mail Services', 'Electronic-Mail-Services', '', 1),
(629, 6, 'Electronics', 'Electronics', '', 1),
(630, 6, 'Help Desk Services', 'Help-Desk-Services', '', 1),
(631, 6, 'Home Theater Services', 'Home-Theater-Services', '', 1),
(632, 6, 'Home Theaters & Entertainment Centers', 'Home-Theaters-Entertainment-Centers', '', 1),
(633, 6, 'Home Theaters Custom Installation', 'Home-Theaters-Custom-Installation', '', 1),
(634, 6, 'Information Technology Services', 'Information-Technology-Services', '', 1),
(635, 6, 'Internet Access Software & Services', 'Internet-Access-Software-Services', '', 1),
(636, 6, 'Internet Advertising', 'Internet-Advertising', '', 1),
(637, 6, 'Internet Marketing Services', 'Internet-Marketing-Services', '', 1),
(638, 6, 'Internet Products & Services', 'Internet-Products-Services', '', 1),
(639, 6, 'Internet Service Providers', 'Internet-Service-Providers', '', 1),
(640, 6, 'Internet Services', 'Internet-Services', '', 1),
(641, 6, 'Laser Printer Supplies', 'Laser-Printer-Supplies', '', 1),
(642, 6, 'Laser Printers', 'Laser-Printers', '', 1),
(643, 6, 'Laser Printers Service & Repair', 'Laser-Printers-Service-Repair', '', 1),
(644, 6, 'Multimedia Services', 'Multimedia-Services', '', 1),
(645, 6, 'Network Consultants', 'Network-Consultants', '', 1),
(646, 6, 'Network Solutions', 'Network-Solutions', '', 1),
(647, 6, 'Point of Sale Systems', 'Point-of-Sale-Systems', '', 1),
(648, 6, 'Printing Equipment Service & Repair', 'Printing-Equipment-Service-Repair', '', 1),
(649, 6, 'Satellite Dish Antennas & Equipment Dealers', 'Satellite-Dish-Antennas-Equipment-Dealers', '', 1),
(650, 6, 'Satellite Equipment', 'Satellite-Equipment', '', 1),
(651, 6, 'Satellite Television Equipment Dealers', 'Satellite-Television-Equipment-Dealers', '', 1),
(652, 6, 'Scanning Services', 'Scanning-Services', '', 1),
(653, 6, 'Software Design & Development', 'Software-Design-Development', '', 1),
(654, 6, 'Software Training', 'Software-Training', '', 1),
(655, 6, 'Sound Equipment & Systems Sales, Rental, & Leasing', 'Sound-Equipment-Systems-Sales,-Rental,-Leasing', '', 1),
(656, 6, 'Stereo Equipment Dealers', 'Stereo-Equipment-Dealers', '', 1),
(657, 6, 'Televisions Retail', 'Televisions-Retail', '', 1),
(658, 6, 'Televisions Service & Repair', 'Televisions-Service-Repair', '', 1),
(659, 6, 'Used & Recycled Computers', 'Used-Recycled-Computers', '', 1),
(660, 6, 'Video Cameras Sales, Service, & Repair', 'Video-Cameras-Sales,-Service,-Repair', '', 1),
(661, 6, 'Video Equipment & Supplies Installation, Service & Repair', 'Video-Equipment-Supplies-Installation,-Service-Repair', '', 1),
(662, 6, 'Video Equipment & Supplies Rental & Leasing', 'Video-Equipment-Supplies-Rental-Leasing', '', 1),
(663, 6, 'Video Equipment, Parts, & Supplies Retail', 'Video-Equipment,-Parts,-Supplies-Retail', '', 1),
(664, 6, 'Web Design', 'Web-Design', '', 1),
(665, 6, 'Web Site Developers', 'Web-Site-Developers', '', 1),
(666, 6, 'Web Site Hosting', 'Web-Site-Hosting', '', 1),
(667, 6, 'Web Sites', 'Web-Sites', '', 1),
(668, 7, 'Air Conditioner Parts', 'Air-Conditioner-Parts', '', 1),
(669, 7, 'Air Conditioning Contractors', 'Air-Conditioning-Contractors', '', 1),
(670, 7, 'Air Conditioning Units', 'Air-Conditioning-Units', '', 1),
(671, 7, 'Aluminum Windows', 'Aluminum-Windows', '', 1),
(672, 7, 'Architects', 'Architects', '', 1),
(673, 7, 'Architectural Services', 'Architectural-Services', '', 1),
(674, 7, 'Artificial Stone & Brick', 'Artificial-Stone-Brick', '', 1),
(675, 7, 'Asbestos Inspection & Consulting', 'Asbestos-Inspection-Consulting', '', 1),
(676, 7, 'Asphalt Pavement Repair & Maintenance', 'Asphalt-Pavement-Repair-Maintenance', '', 1),
(677, 7, 'Asphalt Paving Contractors', 'Asphalt-Paving-Contractors', '', 1),
(678, 7, 'Attic & Basement Finishing', 'Attic-Basement-Finishing', '', 1),
(679, 7, 'Backflow Device Installation, Testing, & Service', 'Backflow-Device-Installation,-Testing,-Service', '', 1),
(680, 7, 'Backhoe & Bulldozer Service', 'Backhoe-Bulldozer-Service', '', 1),
(681, 7, 'Basement Repair & Restoration', 'Basement-Repair-Restoration', '', 1),
(682, 7, 'Basement Waterproofing', 'Basement-Waterproofing', '', 1),
(683, 7, 'Bathroom Contractors', 'Bathroom-Contractors', '', 1),
(684, 7, 'Bathroom Fixtures & Accessories Retail', 'Bathroom-Fixtures-Accessories-Retail', '', 1),
(685, 7, 'Bathroom Remodeling', 'Bathroom-Remodeling', '', 1),
(686, 7, 'Boiler Cleaning & Repair', 'Boiler-Cleaning-Repair', '', 1),
(687, 7, 'Brick Dealers', 'Brick-Dealers', '', 1),
(688, 7, 'Brick Pavers Dealers', 'Brick-Pavers-Dealers', '', 1),
(689, 7, 'Brick Walkways & Patios', 'Brick-Walkways-Patios', '', 1),
(690, 7, 'Building Construction', 'Building-Construction', '', 1),
(691, 7, 'Building Contractors', 'Building-Contractors', '', 1),
(692, 7, 'Building Contractors Commercial & Industrial', 'Building-Contractors-Commercial-Industrial', '', 1),
(693, 7, 'Building Materials & Supplies Dealers', 'Building-Materials-Supplies-Dealers', '', 1),
(694, 7, 'Cabinet Dealers', 'Cabinet-Dealers', '', 1),
(695, 7, 'Cabinet Refacing, Refinishing, & Resurfacing', 'Cabinet-Refacing,-Refinishing,-Resurfacing', '', 1),
(696, 7, 'Cabinets & Cabinet Makers Residential', 'Cabinets-Cabinet-Makers-Residential', '', 1),
(697, 7, 'Carpenters', 'Carpenters', '', 1),
(698, 7, 'Carports', 'Carports', '', 1),
(699, 7, 'Ceramic Tile Contractors', 'Ceramic-Tile-Contractors', '', 1),
(700, 7, 'Ceramic Tile Installation & Repair', 'Ceramic-Tile-Installation-Repair', '', 1),
(701, 7, 'Ceramic Tiles', 'Ceramic-Tiles', '', 1),
(702, 7, 'Closet Design & Remodeling', 'Closet-Design-Remodeling', '', 1),
(703, 7, 'Closet Systems & Organizers', 'Closet-Systems-Organizers', '', 1),
(704, 7, 'Closets & Closet Accessories', 'Closets-Closet-Accessories', '', 1),
(705, 7, 'Commercial Air Conditioning', 'Commercial-Air-Conditioning', '', 1),
(706, 7, 'Commercial Heating', 'Commercial-Heating', '', 1),
(707, 7, 'Common & Face Brick Dealers', 'Common-Face-Brick-Dealers', '', 1),
(708, 7, 'Concrete & Concrete Products Dealers', 'Concrete-Concrete-Products-Dealers', '', 1),
(709, 7, 'Concrete Breaking, Coring, Cutting, Drilling, & Sawing', 'Concrete-Breaking,-Coring,-Cutting,-Drilling,-Sawing', '', 1),
(710, 7, 'Concrete Contractors', 'Concrete-Contractors', '', 1),
(711, 7, 'Concrete Driveways & Sidewalks', 'Concrete-Driveways-Sidewalks', '', 1),
(712, 7, 'Concrete Floor Coating', 'Concrete-Floor-Coating', '', 1),
(713, 7, 'Concrete Repair, Restoration, Cleaning, & Sealing Contractors', 'Concrete-Repair,-Restoration,-Cleaning,-Sealing-Contractors', '', 1),
(714, 7, 'Concrete Sealing & Waterproofing', 'Concrete-Sealing-Waterproofing', '', 1),
(715, 7, 'Construction Clean Up Contractors', 'Construction-Clean-Up-Contractors', '', 1),
(716, 7, 'Construction Companies', 'Construction-Companies', '', 1),
(717, 7, 'Construction Equipment Rental', 'Construction-Equipment-Rental', '', 1),
(718, 7, 'Construction Machinery & Equipment Dealers', 'Construction-Machinery-Equipment-Dealers', '', 1),
(719, 7, 'Construction Materials & Supplies', 'Construction-Materials-Supplies', '', 1),
(720, 7, 'Contractors', 'Contractors', '', 1),
(721, 7, 'Contractors'' Equipment & Supplies Dealers', 'Contractors''-Equipment-Supplies-Dealers', '', 1),
(722, 7, 'Countertops', 'Countertops', '', 1),
(723, 7, 'Crane Rental & Leasing', 'Crane-Rental-Leasing', '', 1),
(724, 7, 'Cranes, Hoisting, & Rigging Services', 'Cranes,-Hoisting,-Rigging-Services', '', 1),
(725, 7, 'Custom Cabinets', 'Custom-Cabinets', '', 1),
(726, 7, 'Custom Kitchen Cabinets', 'Custom-Kitchen-Cabinets', '', 1),
(727, 7, 'Deck Designers & Builders', 'Deck-Designers-Builders', '', 1),
(728, 7, 'Deck Maintenance & Restoration', 'Deck-Maintenance-Restoration', '', 1),
(729, 7, 'Deck Materials', 'Deck-Materials', '', 1),
(730, 7, 'Decorative Concrete Resurfacing Contractors', 'Decorative-Concrete-Resurfacing-Contractors', '', 1),
(731, 7, 'Decorative Painting', 'Decorative-Painting', '', 1),
(732, 7, 'Demolition Residential', 'Demolition-Residential', '', 1),
(733, 7, 'Design & Build Contractors', 'Design-Build-Contractors', '', 1),
(734, 7, 'Dirt Contractors', 'Dirt-Contractors', '', 1),
(735, 7, 'Door & Door Frame Dealers', 'Door-Door-Frame-Dealers', '', 1),
(736, 7, 'Door & Window Screen Sales & Repair', 'Door-Window-Screen-Sales-Repair', '', 1),
(737, 7, 'Door Glass & Mirrors', 'Door-Glass-Mirrors', '', 1),
(738, 7, 'Door Hanging & Installation', 'Door-Hanging-Installation', '', 1),
(739, 7, 'Door Repair', 'Door-Repair', '', 1),
(740, 7, 'Doors & Windows Installation', 'Doors-Windows-Installation', '', 1),
(741, 7, 'Driveway & Parking Lot Blacktop Contractors', 'Driveway-Parking-Lot-Blacktop-Contractors', '', 1),
(742, 7, 'Driveway Contractors & Construction', 'Driveway-Contractors-Construction', '', 1),
(743, 7, 'Drywall Contractors', 'Drywall-Contractors', '', 1),
(744, 7, 'Drywall Repair', 'Drywall-Repair', '', 1),
(745, 7, 'Duct Work', 'Duct-Work', '', 1),
(746, 7, 'Electric Contractors Residential', 'Electric-Contractors-Residential', '', 1),
(747, 7, 'Electric Equipment & Supplies Dealers', 'Electric-Equipment-Supplies-Dealers', '', 1),
(748, 7, 'Electric Equipment & Supplies Wholesale & Manufacturers', 'Electric-Equipment-Supplies-Wholesale-Manufacturers', '', 1),
(749, 7, 'Electricians', 'Electricians', '', 1),
(750, 7, 'Elevator Contractors', 'Elevator-Contractors', '', 1),
(751, 7, 'Elevator Dealers', 'Elevator-Dealers', '', 1),
(752, 7, 'Excavation Contractors', 'Excavation-Contractors', '', 1),
(753, 7, 'Fasteners', 'Fasteners', '', 1),
(754, 7, 'Faucets', 'Faucets', '', 1),
(755, 7, 'Fence Dealers', 'Fence-Dealers', '', 1),
(756, 7, 'Fence Materials', 'Fence-Materials', '', 1),
(757, 7, 'Fiberglass Windows', 'Fiberglass-Windows', '', 1),
(758, 7, 'Fire & Water Damage Restoration Commercial & Industrial', 'Fire-Water-Damage-Restoration-Commercial-Industrial', '', 1),
(759, 7, 'Fire & Water Damage Restoration Equipment & Supplies', 'Fire-Water-Damage-Restoration-Equipment-Supplies', '', 1),
(760, 7, 'Flood Cleanup', 'Flood-Cleanup', '', 1),
(761, 7, 'Floor Installation', 'Floor-Installation', '', 1),
(762, 7, 'Floor Installation, Refinishing, & Resurfacing Commercial & Industrial', 'Floor-Installation,-Refinishing,-Resurfacing-Commercial-Industrial', '', 1),
(763, 7, 'Flooring', 'Flooring', '', 1),
(764, 7, 'Floors & Flooring Contractors', 'Floors-Flooring-Contractors', '', 1),
(765, 7, 'Floors & Flooring Wholesale & Manufacturers', 'Floors-Flooring-Wholesale-Manufacturers', '', 1),
(766, 7, 'Foundation Drilling & Boring Contractors', 'Foundation-Drilling-Boring-Contractors', '', 1),
(767, 7, 'Foundation Services', 'Foundation-Services', '', 1),
(768, 7, 'Foundations Inspection', 'Foundations-Inspection', '', 1),
(769, 7, 'Furnace Cleaning & Repair', 'Furnace-Cleaning-Repair', '', 1),
(770, 7, 'Furnace Filters', 'Furnace-Filters', '', 1),
(771, 7, 'Garage Builders', 'Garage-Builders', '', 1),
(772, 7, 'Garage Doors', 'Garage-Doors', '', 1),
(773, 7, 'Gas Heating Contractors', 'Gas-Heating-Contractors', '', 1),
(774, 7, 'Generator Dealers', 'Generator-Dealers', '', 1),
(775, 7, 'Generator Parts, Service, & Repair', 'Generator-Parts,-Service,-Repair', '', 1),
(776, 7, 'Geothermal Heating and Cooling', 'Geothermal-Heating-and-Cooling', '', 1),
(777, 7, 'Glass & Glass Products Dealers', 'Glass-Glass-Products-Dealers', '', 1),
(778, 7, 'Glass & Glazing Contractors', 'Glass-Glazing-Contractors', '', 1),
(779, 7, 'Glass Block Windows', 'Glass-Block-Windows', '', 1),
(780, 7, 'Glass Coating & Tinting Commercial', 'Glass-Coating-Tinting-Commercial', '', 1),
(781, 7, 'Glass Doors', 'Glass-Doors', '', 1),
(782, 7, 'Glass Replacement & Repair', 'Glass-Replacement-Repair', '', 1),
(783, 7, 'Golf Courses Construction', 'Golf-Courses-Construction', '', 1),
(784, 7, 'Grading Contractors', 'Grading-Contractors', '', 1),
(785, 7, 'Granite & Marble Dealers', 'Granite-Marble-Dealers', '', 1),
(786, 7, 'Granite Dealers', 'Granite-Dealers', '', 1),
(787, 7, 'Guttering Service', 'Guttering-Service', '', 1),
(788, 7, 'Gutters', 'Gutters', '', 1),
(789, 7, 'HVAC Equipment & Supplies Dealers', 'HVAC-Equipment-Supplies-Dealers', '', 1),
(790, 7, 'Hand & Power Tools', 'Hand-Power-Tools', '', 1),
(791, 7, 'Hardware Dealers', 'Hardware-Dealers', '', 1),
(792, 7, 'Hardwood', 'Hardwood', '', 1),
(793, 7, 'Hardwood Floor Refinishing', 'Hardwood-Floor-Refinishing', '', 1),
(794, 7, 'Heat Pump Dealers', 'Heat-Pump-Dealers', '', 1),
(795, 7, 'Heat Pump Service & Repair', 'Heat-Pump-Service-Repair', '', 1),
(796, 7, 'Heater Parts', 'Heater-Parts', '', 1),
(797, 7, 'Heating Contractors', 'Heating-Contractors', '', 1),
(798, 7, 'Heating Repair', 'Heating-Repair', '', 1),
(799, 7, 'Heating Systems', 'Heating-Systems', '', 1),
(800, 7, 'Heating and Air Conditioning', 'Heating-and-Air-Conditioning', '', 1),
(801, 7, 'Heating and Air Conditioning', 'Heating-and-Air-Conditioning', '', 1),
(802, 7, 'Heating and Air Conditioning Supplies', 'Heating-and-Air-Conditioning-Supplies', '', 1),
(803, 7, 'Heating and Plumbing', 'Heating-and-Plumbing', '', 1),
(804, 7, 'Heating and Plumbing Supplies', 'Heating-and-Plumbing-Supplies', '', 1),
(805, 7, 'Heating and Ventilation Contractors', 'Heating-and-Ventilation-Contractors', '', 1),
(806, 7, 'Heavy Construction', 'Heavy-Construction', '', 1),
(807, 7, 'Heavy Construction Equipment Dealers', 'Heavy-Construction-Equipment-Dealers', '', 1),
(808, 7, 'Heavy Construction Equipment Rental', 'Heavy-Construction-Equipment-Rental', '', 1),
(809, 7, 'Heavy Equipment Movers', 'Heavy-Equipment-Movers', '', 1),
(810, 7, 'Home Developers', 'Home-Developers', '', 1),
(811, 7, 'Home Inspections', 'Home-Inspections', '', 1),
(812, 7, 'Hot Water Heat Contractors', 'Hot-Water-Heat-Contractors', '', 1),
(813, 7, 'House Paint', 'House-Paint', '', 1),
(814, 7, 'Hurricane Shutters & Protection', 'Hurricane-Shutters-Protection', '', 1),
(815, 7, 'Industrial Contractors', 'Industrial-Contractors', '', 1),
(816, 7, 'Industrial Electric Contractors', 'Industrial-Electric-Contractors', '', 1),
(817, 7, 'Insulation', 'Insulation', '', 1),
(818, 7, 'Kitchen Cabinet Refacing & Refinishing', 'Kitchen-Cabinet-Refacing-Refinishing', '', 1),
(819, 7, 'Kitchen Cabinet Remodeling & Repair', 'Kitchen-Cabinet-Remodeling-Repair', '', 1),
(820, 7, 'Kitchen Cabinets', 'Kitchen-Cabinets', '', 1),
(821, 7, 'Kitchen Cabinets Wholesale', 'Kitchen-Cabinets-Wholesale', '', 1),
(822, 7, 'Kitchen Fixtures & Accessories Dealers', 'Kitchen-Fixtures-Accessories-Dealers', '', 1),
(823, 7, 'Lake & Pond Construction & Maintenance', 'Lake-Pond-Construction-Maintenance', '', 1),
(824, 7, 'Laminate Flooring', 'Laminate-Flooring', '', 1),
(825, 7, 'Land Clearing & Leveling Contractors', 'Land-Clearing-Leveling-Contractors', '', 1),
(826, 7, 'Land Drainage Contractors', 'Land-Drainage-Contractors', '', 1),
(827, 7, 'Leak Detection Services', 'Leak-Detection-Services', '', 1),
(828, 7, 'Lighting Contractors', 'Lighting-Contractors', '', 1),
(829, 7, 'Lighting Fixtures Retail', 'Lighting-Fixtures-Retail', '', 1),
(830, 7, 'Log Homes, Buildings, & Cabins Dealers', 'Log-Homes,-Buildings,-Cabins-Dealers', '', 1),
(831, 7, 'Lumber Dealers', 'Lumber-Dealers', '', 1),
(832, 7, 'Mantels', 'Mantels', '', 1),
(833, 7, 'Manufactured, Modular, & Mobile Home Dealers', 'Manufactured,-Modular,-Mobile-Home-Dealers', '', 1),
(834, 7, 'Marble Tiles', 'Marble-Tiles', '', 1),
(835, 7, 'Masonry Contractors', 'Masonry-Contractors', '', 1),
(836, 7, 'Metal Building Contractors', 'Metal-Building-Contractors', '', 1),
(837, 7, 'Metal Buildings Dealers', 'Metal-Buildings-Dealers', '', 1),
(838, 7, 'Metal Roofing Contractors', 'Metal-Roofing-Contractors', '', 1),
(839, 7, 'Metal Roofs & Siding', 'Metal-Roofs-Siding', '', 1),
(840, 7, 'Mirrors Retail', 'Mirrors-Retail', '', 1),
(841, 7, 'Mobile Offices & Commercial Units', 'Mobile-Offices-Commercial-Units', '', 1),
(842, 7, 'Moldings', 'Moldings', '', 1),
(843, 7, 'Murals & Faux Finishes', 'Murals-Faux-Finishes', '', 1),
(844, 7, 'Natural & Cut Stone Dealers', 'Natural-Cut-Stone-Dealers', '', 1),
(845, 7, 'Natural Granite & Marble', 'Natural-Granite-Marble', '', 1),
(846, 7, 'Natural Landscaping Stone', 'Natural-Landscaping-Stone', '', 1),
(847, 7, 'Office Trailer Rental', 'Office-Trailer-Rental', '', 1),
(848, 7, 'Oil & Gas Well Drilling', 'Oil-Gas-Well-Drilling', '', 1),
(849, 7, 'Overhead Door Contractors', 'Overhead-Door-Contractors', '', 1),
(850, 7, 'Overhead Doors', 'Overhead-Doors', '', 1),
(851, 7, 'Paint Removal & Stripping', 'Paint-Removal-Stripping', '', 1),
(852, 7, 'Paint Stores', 'Paint-Stores', '', 1),
(853, 7, 'Painters', 'Painters', '', 1),
(854, 7, 'Painting Contractors Commercial & Industrial', 'Painting-Contractors-Commercial-Industrial', '', 1),
(855, 7, 'Patio Builders', 'Patio-Builders', '', 1),
(856, 7, 'Pavement Stripe Painters', 'Pavement-Stripe-Painters', '', 1),
(857, 7, 'Paving Contractors', 'Paving-Contractors', '', 1),
(858, 7, 'Piling & Pile Driving Contractors', 'Piling-Pile-Driving-Contractors', '', 1),
(859, 7, 'Plastering Contractors', 'Plastering-Contractors', '', 1),
(860, 7, 'Plate Glass Windows', 'Plate-Glass-Windows', '', 1),
(861, 7, 'Plumbing', 'Plumbing', '', 1),
(862, 7, 'Plumbing Contractors Commercial & Industrial', 'Plumbing-Contractors-Commercial-Industrial', '', 1),
(863, 7, 'Plumbing Equipment & Supplies Wholesale & Manufacturers', 'Plumbing-Equipment-Supplies-Wholesale-Manufacturers', '', 1),
(864, 7, 'Plumbing Equipment, Parts, & Supplies', 'Plumbing-Equipment,-Parts,-Supplies', '', 1),
(865, 7, 'Plumbing Fixtures', 'Plumbing-Fixtures', '', 1),
(866, 7, 'Pole & Post Frame Buildings', 'Pole-Post-Frame-Buildings', '', 1),
(867, 7, 'Portable Buildings', 'Portable-Buildings', '', 1),
(868, 7, 'Portable Toilets', 'Portable-Toilets', '', 1),
(869, 7, 'Precast Concrete', 'Precast-Concrete', '', 1),
(870, 7, 'Prefabricated & Modular Buildings', 'Prefabricated-Modular-Buildings', '', 1),
(871, 7, 'Radiant Heating and Cooling', 'Radiant-Heating-and-Cooling', '', 1),
(872, 7, 'Railings', 'Railings', '', 1),
(873, 7, 'Replacement Doors', 'Replacement-Doors', '', 1),
(874, 7, 'Replacement Windows', 'Replacement-Windows', '', 1),
(875, 7, 'Retaining Walls Contractors', 'Retaining-Walls-Contractors', '', 1),
(876, 7, 'Rock & Stone Dealers', 'Rock-Stone-Dealers', '', 1),
(877, 7, 'Roof Decks', 'Roof-Decks', '', 1),
(878, 7, 'Roof Repair', 'Roof-Repair', '', 1),
(879, 7, 'Roofers', 'Roofers', '', 1),
(880, 7, 'Roofing', 'Roofing', '', 1),
(881, 7, 'Roofing Contractors Commercial & Industrial', 'Roofing-Contractors-Commercial-Industrial', '', 1),
(882, 7, 'Roofing Contractors Referral & Information Services', 'Roofing-Contractors-Referral-Information-Services', '', 1),
(883, 7, 'Roofing Materials Dealers', 'Roofing-Materials-Dealers', '', 1),
(884, 7, 'Room Additions', 'Room-Additions', '', 1),
(885, 7, 'Rust & Paint Removal', 'Rust-Paint-Removal', '', 1),
(886, 7, 'Salvage Yards', 'Salvage-Yards', '', 1),
(887, 7, 'Sand & Gravel Dealers', 'Sand-Gravel-Dealers', '', 1),
(888, 7, 'Scaffolding & Aerial Lifts, Parts & Supplies', 'Scaffolding-Aerial-Lifts,-Parts-Supplies', '', 1),
(889, 7, 'Scaffolding Rental & Leasing', 'Scaffolding-Rental-Leasing', '', 1),
(890, 7, 'Screen Houses & Enclosures', 'Screen-Houses-Enclosures', '', 1),
(891, 7, 'Seamless Guttering', 'Seamless-Guttering', '', 1),
(892, 7, 'Security Doors & Windows', 'Security-Doors-Windows', '', 1),
(893, 7, 'Septic Tanks & Systems Consulting, Site Evaluation, & Design', 'Septic-Tanks-Systems-Consulting,-Site-Evaluation,-Design', '', 1),
(894, 7, 'Septic Tanks & Systems Contractors & Dealers', 'Septic-Tanks-Systems-Contractors-Dealers', '', 1),
(895, 7, 'Septic Tanks Cleaning', 'Septic-Tanks-Cleaning', '', 1),
(896, 7, 'Shutters Retail', 'Shutters-Retail', '', 1),
(897, 7, 'Siding Contractors', 'Siding-Contractors', '', 1),
(898, 7, 'Siding Material Dealers', 'Siding-Material-Dealers', '', 1),
(899, 7, 'Special Trades Contractors', 'Special-Trades-Contractors', '', 1),
(900, 7, 'Staircases, Stairs, & Railings', 'Staircases,-Stairs,-Railings', '', 1),
(901, 7, 'Stairs & Stairway Builders', 'Stairs-Stairway-Builders', '', 1),
(902, 7, 'Steel Buildings', 'Steel-Buildings', '', 1),
(903, 7, 'Steel Fabrication', 'Steel-Fabrication', '', 1),
(904, 7, 'Steel Fabricators & Erectors', 'Steel-Fabricators-Erectors', '', 1),
(905, 7, 'Storage Containers', 'Storage-Containers', '', 1),
(906, 7, 'Storage Sheds', 'Storage-Sheds', '', 1),
(907, 7, 'Storm Door & Window Dealers', 'Storm-Door-Window-Dealers', '', 1),
(908, 7, 'Storm Shelter Contractors & Dealers', 'Storm-Shelter-Contractors-Dealers', '', 1),
(909, 7, 'Structural Steel', 'Structural-Steel', '', 1),
(910, 7, 'Stucco & Exterior Coating Contractors', 'Stucco-Exterior-Coating-Contractors', '', 1),
(911, 7, 'Sun Rooms, Greenhouses, Solariums, & Atriums Builders', 'Sun-Rooms,-Greenhouses,-Solariums,-Atriums-Builders', '', 1),
(912, 7, 'Swimming Pool Coping, Plastering, & Tiling', 'Swimming-Pool-Coping,-Plastering,-Tiling', '', 1),
(913, 7, 'Synthetic & Cultured Marble Dealers', 'Synthetic-Cultured-Marble-Dealers', '', 1),
(914, 7, 'Tank Installation & Removal', 'Tank-Installation-Removal', '', 1),
(915, 7, 'Telecommunications Wiring & Cabling', 'Telecommunications-Wiring-Cabling', '', 1),
(916, 7, 'Telephone & Television Cable Contractors', 'Telephone-Television-Cable-Contractors', '', 1),
(917, 7, 'Tennis Courts Construction', 'Tennis-Courts-Construction', '', 1),
(918, 7, 'Terrazzo, Marble, & Tile Contractors', 'Terrazzo,-Marble,-Tile-Contractors', '', 1),
(919, 7, 'Tile Dealers', 'Tile-Dealers', '', 1),
(920, 7, 'Tile Floor Contractors', 'Tile-Floor-Contractors', '', 1),
(921, 7, 'Tiles Contractors', 'Tiles-Contractors', '', 1),
(922, 7, 'Tiles Refinishing, Cleaning, & Repair', 'Tiles-Refinishing,-Cleaning,-Repair', '', 1),
(923, 7, 'Toilets', 'Toilets', '', 1),
(924, 7, 'Tool Rental & Leasing', 'Tool-Rental-Leasing', '', 1),
(925, 7, 'Vinyl & Acrylic Windows', 'Vinyl-Acrylic-Windows', '', 1),
(926, 7, 'Vinyl Doors & Windows', 'Vinyl-Doors-Windows', '', 1),
(927, 7, 'Vinyl Fences', 'Vinyl-Fences', '', 1),
(928, 7, 'Water Damage Restoration', 'Water-Damage-Restoration', '', 1),
(929, 7, 'Water Heater Parts & Supplies', 'Water-Heater-Parts-Supplies', '', 1),
(930, 7, 'Water Heaters', 'Water-Heaters', '', 1),
(931, 7, 'Water Well Drilling & Service', 'Water-Well-Drilling-Service', '', 1),
(932, 7, 'Water Well Pumps Sales & Service', 'Water-Well-Pumps-Sales-Service', '', 1),
(933, 7, 'Waterproofing Contractors', 'Waterproofing-Contractors', '', 1),
(934, 7, 'Waterproofing Contractors Commercial & Industrial', 'Waterproofing-Contractors-Commercial-Industrial', '', 1),
(935, 7, 'Window Contractors', 'Window-Contractors', '', 1),
(936, 7, 'Window Glass Coating & Tinting', 'Window-Glass-Coating-Tinting', '', 1),
(937, 7, 'Window Installation', 'Window-Installation', '', 1),
(938, 7, 'Windows & Parts Dealers', 'Windows-Parts-Dealers', '', 1),
(939, 7, 'Windows Wholesale & Manufacturers', 'Windows-Wholesale-Manufacturers', '', 1),
(940, 7, 'Wood Windows', 'Wood-Windows', '', 1),
(941, 7, 'Woodworking', 'Woodworking', '', 1),
(942, 7, 'Wrecking & Demolition Contractors', 'Wrecking-Demolition-Contractors', '', 1),
(943, 8, 'Academic Specialty Schools', 'Academic-Specialty-Schools', '', 1),
(944, 8, 'Accounting & Bookkeeping Schools', 'Accounting-Bookkeeping-Schools', '', 1),
(945, 8, 'Adult Education Schools', 'Adult-Education-Schools', '', 1),
(946, 8, 'After School Programs', 'After-School-Programs', '', 1),
(947, 8, 'Allied Health Education', 'Allied-Health-Education', '', 1),
(948, 8, 'Auto Mechanic Schools', 'Auto-Mechanic-Schools', '', 1),
(949, 8, 'Aviation Schools', 'Aviation-Schools', '', 1),
(950, 8, 'Bartending Instruction', 'Bartending-Instruction', '', 1),
(951, 8, 'Beauty Schools', 'Beauty-Schools', '', 1),
(952, 8, 'Boards of Education', 'Boards-of-Education', '', 1),
(953, 8, 'Business & Personal Coaching', 'Business-Personal-Coaching', '', 1),
(954, 8, 'Business Colleges', 'Business-Colleges', '', 1),
(955, 8, 'Business Education', 'Business-Education', '', 1),
(956, 8, 'CPR Instruction', 'CPR-Instruction', '', 1),
(957, 8, 'Career & Workplace Education', 'Career-Workplace-Education', '', 1),
(958, 8, 'Child Development Centers', 'Child-Development-Centers', '', 1),
(959, 8, 'College Financial Planning Services', 'College-Financial-Planning-Services', '', 1),
(960, 8, 'College Preparatory Schools', 'College-Preparatory-Schools', '', 1),
(961, 8, 'Colleges', 'Colleges', '', 1),
(962, 8, 'Community Colleges', 'Community-Colleges', '', 1),
(963, 8, 'Computer Programming Instruction', 'Computer-Programming-Instruction', '', 1),
(964, 8, 'Computer Technology Schools', 'Computer-Technology-Schools', '', 1),
(965, 8, 'Computer Training', 'Computer-Training', '', 1),
(966, 8, 'Continuing Education', 'Continuing-Education', '', 1),
(967, 8, 'Court Reporting Schools', 'Court-Reporting-Schools', '', 1),
(968, 8, 'Culinary Schools', 'Culinary-Schools', '', 1),
(969, 8, 'Dance Studios', 'Dance-Studios', '', 1),
(970, 8, 'Dental Schools', 'Dental-Schools', '', 1),
(971, 8, 'Educational Administration', 'Educational-Administration', '', 1),
(972, 8, 'Educational Consultants', 'Educational-Consultants', '', 1),
(973, 8, 'Educational Equipment & Supplies', 'Educational-Equipment-Supplies', '', 1),
(974, 8, 'Educational Financing', 'Educational-Financing', '', 1),
(975, 8, 'Educational Services', 'Educational-Services', '', 1),
(976, 8, 'Elementary & Secondary Education', 'Elementary-Secondary-Education', '', 1),
(977, 8, 'Elementary Schools', 'Elementary-Schools', '', 1),
(978, 8, 'Fashion Design Schools', 'Fashion-Design-Schools', '', 1),
(979, 8, 'Graduate & Professional Schools', 'Graduate-Professional-Schools', '', 1),
(980, 8, 'Graphic Arts Schools', 'Graphic-Arts-Schools', '', 1),
(981, 8, 'High Schools', 'High-Schools', '', 1),
(982, 8, 'Home Schooling', 'Home-Schooling', '', 1),
(983, 8, 'Industrial & Trade Schools', 'Industrial-Trade-Schools', '', 1),
(984, 8, 'Interior Design Schools', 'Interior-Design-Schools', '', 1),
(985, 8, 'Junior High & Middle Schools', 'Junior-High-Middle-Schools', '', 1),
(986, 8, 'Language Schools & Instruction', 'Language-Schools-Instruction', '', 1),
(987, 8, 'Law Schools', 'Law-Schools', '', 1),
(988, 8, 'Learning Centers', 'Learning-Centers', '', 1),
(989, 8, 'Management Development Training', 'Management-Development-Training', '', 1),
(990, 8, 'Management Training & Development Consultants', 'Management-Training-Development-Consultants', '', 1),
(991, 8, 'Medical & Dental Assistant & Technician Schools', 'Medical-Dental-Assistant-Technician-Schools', '', 1),
(992, 8, 'Medical Records & Billing Schools', 'Medical-Records-Billing-Schools', '', 1),
(993, 8, 'Medical Schools', 'Medical-Schools', '', 1),
(994, 8, 'Medical Secretarial Schools', 'Medical-Secretarial-Schools', '', 1),
(995, 8, 'Military Schools', 'Military-Schools', '', 1),
(996, 8, 'Nursing Schools', 'Nursing-Schools', '', 1),
(997, 8, 'Online & Correspondence Schools', 'Online-Correspondence-Schools', '', 1),
(998, 8, 'Online College Courses', 'Online-College-Courses', '', 1),
(999, 8, 'Other Schools & Instruction', 'Other-Schools-Instruction', '', 1),
(1000, 8, 'Paralegal Schools', 'Paralegal-Schools', '', 1),
(1001, 8, 'Pet Grooming Schools', 'Pet-Grooming-Schools', '', 1),
(1002, 8, 'Preschools', 'Preschools', '', 1),
(1003, 8, 'Private Schools', 'Private-Schools', '', 1),
(1004, 8, 'Public Schools', 'Public-Schools', '', 1),
(1005, 8, 'Radiology Schools', 'Radiology-Schools', '', 1),
(1006, 8, 'Religious Education Schools', 'Religious-Education-Schools', '', 1),
(1007, 8, 'Sales Training', 'Sales-Training', '', 1),
(1008, 8, 'School Supplies & Equipment Retail', 'School-Supplies-Equipment-Retail', '', 1),
(1009, 8, 'Schools', 'Schools', '', 1),
(1010, 8, 'Secretarial Schools', 'Secretarial-Schools', '', 1),
(1011, 8, 'Self Defense Schools & Programs', 'Self-Defense-Schools-Programs', '', 1),
(1012, 8, 'Sports & Fitness Instruction', 'Sports-Fitness-Instruction', '', 1),
(1013, 8, 'Training Programs & Services', 'Training-Programs-Services', '', 1),
(1014, 8, 'Tutoring', 'Tutoring', '', 1),
(1015, 8, 'Vocational Education', 'Vocational-Education', '', 1),
(1016, 9, 'American Restaurants', 'American-Restaurants', '', 1),
(1017, 9, 'Asian Restaurants', 'Asian-Restaurants', '', 1),
(1018, 9, 'BBQ Restaurants', 'BBQ-Restaurants', '', 1),
(1019, 9, 'Bagel Shops', 'Bagel-Shops', '', 1),
(1020, 9, 'Baked Goods Wholesale & Manufacturers', 'Baked-Goods-Wholesale-Manufacturers', '', 1),
(1021, 9, 'Bakeries Retail', 'Bakeries-Retail', '', 1),
(1022, 9, 'Banquet Rooms', 'Banquet-Rooms', '', 1),
(1023, 9, 'Bar & Grill Restaurants', 'Bar-Grill-Restaurants', '', 1),
(1024, 9, 'Bar Equipment, Fixtures, & Supplies', 'Bar-Equipment,-Fixtures,-Supplies', '', 1),
(1025, 9, 'Bottled & Bulk Water Retail', 'Bottled-Bulk-Water-Retail', '', 1),
(1026, 9, 'Breakfast & Brunch Restaurants', 'Breakfast-Brunch-Restaurants', '', 1),
(1027, 9, 'Breweries', 'Breweries', '', 1),
(1028, 9, 'Buffets Restaurants', 'Buffets-Restaurants', '', 1),
(1029, 9, 'Burger Restaurants', 'Burger-Restaurants', '', 1),
(1030, 9, 'Cafes', 'Cafes', '', 1),
(1031, 9, 'Cajun & Creole Restaurants', 'Cajun-Creole-Restaurants', '', 1),
(1032, 9, 'Cake & Pie Shops', 'Cake-Pie-Shops', '', 1),
(1033, 9, 'Candy & Confectionery Retail', 'Candy-Confectionery-Retail', '', 1),
(1034, 9, 'Candy & Cookie Arrangements', 'Candy-Cookie-Arrangements', '', 1),
(1035, 9, 'Caterers', 'Caterers', '', 1),
(1036, 9, 'Chicken Restaurants', 'Chicken-Restaurants', '', 1),
(1037, 9, 'Chinese Food', 'Chinese-Food', '', 1),
(1038, 9, 'Chinese Foods', 'Chinese-Foods', '', 1),
(1039, 9, 'Cigar & Cigarette Accessories', 'Cigar-Cigarette-Accessories', '', 1),
(1040, 9, 'Cigar, Cigarette, & Tobacco Dealers', 'Cigar,-Cigarette,-Tobacco-Dealers', '', 1),
(1041, 9, 'Coffee & Tea Shops', 'Coffee-Tea-Shops', '', 1),
(1042, 9, 'Delicatessens Retail', 'Delicatessens-Retail', '', 1),
(1043, 9, 'Diners', 'Diners', '', 1),
(1044, 9, 'Dinner Theater Restaurants', 'Dinner-Theater-Restaurants', '', 1),
(1045, 9, 'Donuts Retail', 'Donuts-Retail', '', 1),
(1046, 9, 'Family Restaurants', 'Family-Restaurants', '', 1),
(1047, 9, 'Fast Food Restaurants', 'Fast-Food-Restaurants', '', 1),
(1048, 9, 'Fast Foods & Take Out', 'Fast-Foods-Take-Out', '', 1),
(1049, 9, 'Fine Dining Restaurants', 'Fine-Dining-Restaurants', '', 1),
(1050, 9, 'Fish & Seafood Retail', 'Fish-Seafood-Retail', '', 1),
(1051, 9, 'Food & Beverage Delivery Services', 'Food-Beverage-Delivery-Services', '', 1),
(1052, 9, 'Food & Beverage Services', 'Food-Beverage-Services', '', 1),
(1053, 9, 'Food Products Retail', 'Food-Products-Retail', '', 1),
(1054, 9, 'Food Products Wholesale & Manufacturers', 'Food-Products-Wholesale-Manufacturers', '', 1),
(1055, 9, 'French Restaurants', 'French-Restaurants', '', 1),
(1056, 9, 'German Restaurants', 'German-Restaurants', '', 1),
(1057, 9, 'Greek Restaurants', 'Greek-Restaurants', '', 1),
(1058, 9, 'Grocery Stores & Supermarkets', 'Grocery-Stores-Supermarkets', '', 1),
(1059, 9, 'Health & Diet Foods Retail', 'Health-Diet-Foods-Retail', '', 1),
(1060, 9, 'Ice Cream & Frozen Yogurt Shops', 'Ice-Cream-Frozen-Yogurt-Shops', '', 1),
(1061, 9, 'Indian Restaurants', 'Indian-Restaurants', '', 1),
(1062, 9, 'Irish Restaurants', 'Irish-Restaurants', '', 1),
(1063, 9, 'Italian Food', 'Italian-Food', '', 1),
(1064, 9, 'Japanese Restaurants', 'Japanese-Restaurants', '', 1),
(1065, 9, 'Kitchen Equipment Commercial', 'Kitchen-Equipment-Commercial', '', 1),
(1066, 9, 'Korean Restaurants', 'Korean-Restaurants', '', 1),
(1067, 9, 'Kosher Restaurants', 'Kosher-Restaurants', '', 1),
(1068, 9, 'Liquor Stores', 'Liquor-Stores', '', 1),
(1069, 9, 'Lunch Restaurants', 'Lunch-Restaurants', '', 1),
(1070, 9, 'Meats Retail', 'Meats-Retail', '', 1),
(1071, 9, 'Mexican Foods', 'Mexican-Foods', '', 1),
(1072, 9, 'Mexican Restaurants', 'Mexican-Restaurants', '', 1),
(1073, 9, 'Middle Eastern Restaurants', 'Middle-Eastern-Restaurants', '', 1),
(1074, 9, 'Oriental Goods & Foods Retail', 'Oriental-Goods-Foods-Retail', '', 1),
(1075, 9, 'Pizza Delivery', 'Pizza-Delivery', '', 1),
(1076, 9, 'Pizza Delivery Services', 'Pizza-Delivery-Services', '', 1),
(1077, 9, 'Pubs', 'Pubs', '', 1),
(1078, 9, 'Restaurant Equipment', 'Restaurant-Equipment', '', 1),
(1079, 9, 'Restaurant Equipment & Services', 'Restaurant-Equipment-Services', '', 1),
(1080, 9, 'Restaurant Equipment & Supplies Wholesale & Manufacturers', 'Restaurant-Equipment-Supplies-Wholesale-Manufacturers', '', 1),
(1081, 9, 'Restaurants', 'Restaurants', '', 1),
(1082, 9, 'Sandwich Shops', 'Sandwich-Shops', '', 1),
(1083, 9, 'Seafood Restaurants', 'Seafood-Restaurants', '', 1),
(1084, 9, 'Soul Food Restaurants', 'Soul-Food-Restaurants', '', 1),
(1085, 9, 'Spanish Restaurants', 'Spanish-Restaurants', '', 1),
(1086, 9, 'Specialty & Gourmet Foods Retail', 'Specialty-Gourmet-Foods-Retail', '', 1),
(1087, 9, 'Sports Bars', 'Sports-Bars', '', 1),
(1088, 9, 'Steak & Seafood Restaurants', 'Steak-Seafood-Restaurants', '', 1),
(1089, 9, 'Steak Houses', 'Steak-Houses', '', 1),
(1090, 9, 'Sushi Bars', 'Sushi-Bars', '', 1),
(1091, 9, 'Taverns', 'Taverns', '', 1),
(1092, 9, 'Thai Restaurants', 'Thai-Restaurants', '', 1),
(1093, 9, 'Used Restaurant Equipment & Supplies', 'Used-Restaurant-Equipment-Supplies', '', 1),
(1094, 9, 'Vegetarian Restaurants', 'Vegetarian-Restaurants', '', 1),
(1095, 9, 'Vending Machine Dealers', 'Vending-Machine-Dealers', '', 1),
(1096, 9, 'Water Coolers & Fountains Equipment & Supplies Dealers', 'Water-Coolers-Fountains-Equipment-Supplies-Dealers', '', 1),
(1097, 9, 'Wedding Caterers', 'Wedding-Caterers', '', 1),
(1098, 9, 'Wine & Distilled Beverages Retail', 'Wine-Distilled-Beverages-Retail', '', 1),
(1099, 10, 'Abortion Clinics', 'Abortion-Clinics', '', 1),
(1100, 10, 'Abortion Information & Referral Services', 'Abortion-Information-Referral-Services', '', 1),
(1101, 10, 'Acupuncture & Acupressure', 'Acupuncture-Acupressure', '', 1),
(1102, 10, 'Addiction Information & Treatment', 'Addiction-Information-Treatment', '', 1),
(1103, 10, 'Air Ambulance Services', 'Air-Ambulance-Services', '', 1),
(1104, 10, 'Alcoholics Anonymous', 'Alcoholics-Anonymous', '', 1),
(1105, 10, 'Allergy & Immunology Pediatrics Physicians & Surgeons', 'Allergy-Immunology-Pediatrics-Physicians-Surgeons', '', 1),
(1106, 10, 'Allergy & Immunology Physicians & Surgeons', 'Allergy-Immunology-Physicians-Surgeons', '', 1),
(1107, 10, 'Alternative Medicine', 'Alternative-Medicine', '', 1),
(1108, 10, 'Animal Health', 'Animal-Health', '', 1),
(1109, 10, 'Animal Hospitals', 'Animal-Hospitals', '', 1),
(1110, 10, 'Anti-Aging Medicine Physicians & Surgeons', 'Anti-Aging-Medicine-Physicians-Surgeons', '', 1),
(1111, 10, 'Audiologists', 'Audiologists', '', 1),
(1112, 10, 'Bariatric Medicine Physicians & Surgeons', 'Bariatric-Medicine-Physicians-Surgeons', '', 1),
(1113, 10, 'Blood Banks', 'Blood-Banks', '', 1),
(1114, 10, 'Blood Typing & Testing', 'Blood-Typing-Testing', '', 1),
(1115, 10, 'Cancer Information & Referral Services', 'Cancer-Information-Referral-Services', '', 1),
(1116, 10, 'Cardiology Physicians & Surgeons', 'Cardiology-Physicians-Surgeons', '', 1),
(1117, 10, 'Cemeteries & Crematories', 'Cemeteries-Crematories', '', 1),
(1118, 10, 'Cemeteries & Memorial Parks', 'Cemeteries-Memorial-Parks', '', 1),
(1119, 10, 'Chiropractic Clinics', 'Chiropractic-Clinics', '', 1),
(1120, 10, 'Chiropractors', 'Chiropractors', '', 1),
(1121, 10, 'Clinics', 'Clinics', '', 1),
(1122, 10, 'Colonic Therapy', 'Colonic-Therapy', '', 1),
(1123, 10, 'Contact Lenses Retail', 'Contact-Lenses-Retail', '', 1),
(1124, 10, 'Cosmetic & Reconstructive Pediatrics Surgeons', 'Cosmetic-Reconstructive-Pediatrics-Surgeons', '', 1),
(1125, 10, 'Cosmetic Dentists', 'Cosmetic-Dentists', '', 1),
(1126, 10, 'Cremation Services', 'Cremation-Services', '', 1),
(1127, 10, 'Cremation Urns', 'Cremation-Urns', '', 1),
(1128, 10, 'Dental Clinics', 'Dental-Clinics', '', 1),
(1129, 10, 'Dental Implant Dentists', 'Dental-Implant-Dentists', '', 1),
(1130, 10, 'Dental Information & Referral Services', 'Dental-Information-Referral-Services', '', 1),
(1131, 10, 'Dental Insurance', 'Dental-Insurance', '', 1),
(1132, 10, 'Dental Laboratories', 'Dental-Laboratories', '', 1),
(1133, 10, 'Dentists', 'Dentists', '', 1),
(1134, 10, 'Dentures', 'Dentures', '', 1),
(1135, 10, 'Dermatology Pediatrics Physicians & Surgeons', 'Dermatology-Pediatrics-Physicians-Surgeons', '', 1),
(1136, 10, 'Dermatology Physicians & Surgeons', 'Dermatology-Physicians-Surgeons', '', 1),
(1137, 10, 'Diabetes Physicians', 'Diabetes-Physicians', '', 1),
(1138, 10, 'Diabetic Equipment & Supplies', 'Diabetic-Equipment-Supplies', '', 1),
(1139, 10, 'Diabetic Services & Supplies', 'Diabetic-Services-Supplies', '', 1),
(1140, 10, 'Dialysis Clinics', 'Dialysis-Clinics', '', 1),
(1141, 10, 'Dietitians', 'Dietitians', '', 1),
(1142, 10, 'Disabilities & Special Needs Equipment & Supplies Retail', 'Disabilities-Special-Needs-Equipment-Supplies-Retail', '', 1),
(1143, 10, 'Disabled & Elderly Home Health Care', 'Disabled-Elderly-Home-Health-Care', '', 1),
(1144, 10, 'Doctors', 'Doctors', '', 1),
(1145, 10, 'Drug & Alcohol Detection & Testing', 'Drug-Alcohol-Detection-Testing', '', 1),
(1146, 10, 'Drug Treatment Centers', 'Drug-Treatment-Centers', '', 1),
(1147, 10, 'Drugs & Medications', 'Drugs-Medications', '', 1),
(1148, 10, 'Ear, Nose, & Throat Physicians & Surgeons', 'Ear,-Nose,-Throat-Physicians-Surgeons', '', 1),
(1149, 10, 'Elder Care', 'Elder-Care', '', 1),
(1150, 10, 'Elderly Companion Services', 'Elderly-Companion-Services', '', 1),
(1151, 10, 'Emergency Ambulance Services', 'Emergency-Ambulance-Services', '', 1),
(1152, 10, 'Emergency Services Dentists', 'Emergency-Services-Dentists', '', 1),
(1153, 10, 'Emergency Services Veterinarians', 'Emergency-Services-Veterinarians', '', 1),
(1154, 10, 'Endocrinology & Metabolism Physicians & Surgeons', 'Endocrinology-Metabolism-Physicians-Surgeons', '', 1),
(1155, 10, 'Endodontics Dentists', 'Endodontics-Dentists', '', 1),
(1156, 10, 'Eyewear', 'Eyewear', '', 1),
(1157, 10, 'Family & General Practice Physicians & Surgeons', 'Family-General-Practice-Physicians-Surgeons', '', 1),
(1158, 10, 'Family Planning', 'Family-Planning', '', 1),
(1159, 10, 'Family Planning & Birth Control Clinics', 'Family-Planning-Birth-Control-Clinics', '', 1),
(1160, 10, 'Family Practice Chiropractors', 'Family-Practice-Chiropractors', '', 1),
(1161, 10, 'Fertility & Infertility Physicians & Surgeons', 'Fertility-Infertility-Physicians-Surgeons', '', 1),
(1162, 10, 'Fertility Clinics', 'Fertility-Clinics', '', 1),
(1163, 10, 'First Aid Equipment & Supplies Retail', 'First-Aid-Equipment-Supplies-Retail', '', 1),
(1164, 10, 'Funeral Homes & Directors', 'Funeral-Homes-Directors', '', 1),
(1165, 10, 'Funeral Services', 'Funeral-Services', '', 1),
(1166, 10, 'Gastroenterology Physicians & Surgeons', 'Gastroenterology-Physicians-Surgeons', '', 1),
(1167, 10, 'General Anesthesia & Sedation Dentists', 'General-Anesthesia-Sedation-Dentists', '', 1),
(1168, 10, 'General Surgeons', 'General-Surgeons', '', 1),
(1169, 10, 'Group Medical Practice', 'Group-Medical-Practice', '', 1),
(1170, 10, 'Gynecology & Obstetrics Physicians & Surgeons', 'Gynecology-Obstetrics-Physicians-Surgeons', '', 1),
(1171, 10, 'Hair Loss Clinics', 'Hair-Loss-Clinics', '', 1),
(1172, 10, 'Health & Wellness Programs', 'Health-Wellness-Programs', '', 1),
(1173, 10, 'Health Associations', 'Health-Associations', '', 1),
(1174, 10, 'Health Care Plans', 'Health-Care-Plans', '', 1),
(1175, 10, 'Health Care Professionals', 'Health-Care-Professionals', '', 1),
(1176, 10, 'Health Care Providers', 'Health-Care-Providers', '', 1),
(1177, 10, 'Health Food Stores', 'Health-Food-Stores', '', 1),
(1178, 10, 'Hearing Aid Audiologists', 'Hearing-Aid-Audiologists', '', 1),
(1179, 10, 'Hearing Aids & Assistive Devices Retail', 'Hearing-Aids-Assistive-Devices-Retail', '', 1),
(1180, 10, 'Herbs Retail', 'Herbs-Retail', '', 1),
(1181, 10, 'Holistic Health Practitioners', 'Holistic-Health-Practitioners', '', 1),
(1182, 10, 'Home Health Care', 'Home-Health-Care', '', 1),
(1183, 10, 'Home Health Care Agencies', 'Home-Health-Care-Agencies', '', 1),
(1184, 10, 'Home Health Care Equipment & Supplies', 'Home-Health-Care-Equipment-Supplies', '', 1),
(1185, 10, 'Home Health Care Nurses', 'Home-Health-Care-Nurses', '', 1),
(1186, 10, 'Hospice Services', 'Hospice-Services', '', 1),
(1187, 10, 'Hospital & Medical Service Plans', 'Hospital-Medical-Service-Plans', '', 1),
(1188, 10, 'Hospital Equipment & Supplies Retail', 'Hospital-Equipment-Supplies-Retail', '', 1),
(1189, 10, 'Hospitals', 'Hospitals', '', 1),
(1190, 10, 'Hypnotherapy', 'Hypnotherapy', '', 1),
(1191, 10, 'Internal Medicine Physicians & Surgeons', 'Internal-Medicine-Physicians-Surgeons', '', 1),
(1192, 10, 'Laser Surgeons', 'Laser-Surgeons', '', 1),
(1193, 10, 'Laser Vision Correction', 'Laser-Vision-Correction', '', 1),
(1194, 10, 'Liposuction Physicians & Surgeons', 'Liposuction-Physicians-Surgeons', '', 1),
(1195, 10, 'MRI', 'MRI', '', 1),
(1196, 10, 'Mammography Clinics', 'Mammography-Clinics', '', 1),
(1197, 10, 'Massages', 'Massages', '', 1),
(1198, 10, 'Maxillofacial Physicians & Surgeons', 'Maxillofacial-Physicians-Surgeons', '', 1),
(1199, 10, 'Medical Alarms', 'Medical-Alarms', '', 1),
(1200, 10, 'Medical Billing Services', 'Medical-Billing-Services', '', 1),
(1201, 10, 'Medical Centers', 'Medical-Centers', '', 1),
(1202, 10, 'Medical Diagnostic Services', 'Medical-Diagnostic-Services', '', 1),
(1203, 10, 'Medical Equipment', 'Medical-Equipment', '', 1),
(1204, 10, 'Medical Imaging', 'Medical-Imaging', '', 1),
(1205, 10, 'Medical Laboratories', 'Medical-Laboratories', '', 1),
(1206, 10, 'Medical Services', 'Medical-Services', '', 1),
(1207, 10, 'Medical Services Organizations', 'Medical-Services-Organizations', '', 1),
(1208, 10, 'Medicare', 'Medicare', '', 1),
(1209, 10, 'Medicare Supplemental & Long Term Care Insurance', 'Medicare-Supplemental-Long-Term-Care-Insurance', '', 1),
(1210, 10, 'Mental Health', 'Mental-Health', '', 1),
(1211, 10, 'Mental Health Clinics', 'Mental-Health-Clinics', '', 1),
(1212, 10, 'Mental Health Practitioners', 'Mental-Health-Practitioners', '', 1),
(1213, 10, 'Monuments & Markers Dealers', 'Monuments-Markers-Dealers', '', 1),
(1214, 10, 'Natural Healing Products & Services', 'Natural-Healing-Products-Services', '', 1),
(1215, 10, 'Neurology Physicians & Surgeons', 'Neurology-Physicians-Surgeons', '', 1),
(1216, 10, 'Nurse Practitioners', 'Nurse-Practitioners', '', 1),
(1217, 10, 'Nurses', 'Nurses', '', 1),
(1218, 10, 'Nursing & Personal Care Facilities', 'Nursing-Personal-Care-Facilities', '', 1),
(1219, 10, 'Nursing Homes', 'Nursing-Homes', '', 1),
(1220, 10, 'Nutrition', 'Nutrition', '', 1),
(1221, 10, 'Nutritionists', 'Nutritionists', '', 1),
(1222, 10, 'Occupational & Industrial Health & Safety', 'Occupational-Industrial-Health-Safety', '', 1),
(1223, 10, 'Occupational & Industrial Medicine Physicians & Surgeons', 'Occupational-Industrial-Medicine-Physicians-Surgeons', '', 1),
(1224, 10, 'Occupational Therapy & Rehabilitation', 'Occupational-Therapy-Rehabilitation', '', 1),
(1225, 10, 'Oncology Physicians & Surgeons', 'Oncology-Physicians-Surgeons', '', 1),
(1226, 10, 'Ophthalmology Physicians & Surgeons', 'Ophthalmology-Physicians-Surgeons', '', 1),
(1227, 10, 'Optical Goods Retail', 'Optical-Goods-Retail', '', 1),
(1228, 10, 'Opticians', 'Opticians', '', 1),
(1229, 10, 'Optometrists', 'Optometrists', '', 1),
(1230, 10, 'Oral & Maxillofacial Pathology & Surgery Dentists', 'Oral-Maxillofacial-Pathology-Surgery-Dentists', '', 1),
(1231, 10, 'Oral Surgeons', 'Oral-Surgeons', '', 1),
(1232, 10, 'Orthodontics Dentists', 'Orthodontics-Dentists', '', 1),
(1233, 10, 'Orthopedics Pediatrics Physicians & Surgeons', 'Orthopedics-Pediatrics-Physicians-Surgeons', '', 1),
(1234, 10, 'Orthopedics Physicians & Surgeons', 'Orthopedics-Physicians-Surgeons', '', 1),
(1235, 10, 'Osteopathic Physicians & Surgeons', 'Osteopathic-Physicians-Surgeons', '', 1),
(1236, 10, 'Otolaryngology Physicians & Surgeons', 'Otolaryngology-Physicians-Surgeons', '', 1),
(1237, 10, 'Pain Management Physicians & Surgeons', 'Pain-Management-Physicians-Surgeons', '', 1),
(1238, 10, 'Paternity Testing', 'Paternity-Testing', '', 1),
(1239, 10, 'Pediatrics Dentists', 'Pediatrics-Dentists', '', 1),
(1240, 10, 'Pediatrics Physicians & Surgeons', 'Pediatrics-Physicians-Surgeons', '', 1),
(1241, 10, 'Periodontics Dentists', 'Periodontics-Dentists', '', 1),
(1242, 10, 'Pharmacies', 'Pharmacies', '', 1),
(1243, 10, 'Physical Therapists', 'Physical-Therapists', '', 1),
(1244, 10, 'Physical Therapy', 'Physical-Therapy', '', 1),
(1245, 10, 'Plastic Surgery', 'Plastic-Surgery', '', 1),
(1246, 10, 'Podiatry Physicians & Surgeons', 'Podiatry-Physicians-Surgeons', '', 1),
(1247, 10, 'Pregnancy Counseling & Information Services', 'Pregnancy-Counseling-Information-Services', '', 1),
(1248, 10, 'Prescription Services', 'Prescription-Services', '', 1),
(1249, 10, 'Psychiatric Hospitals', 'Psychiatric-Hospitals', '', 1),
(1250, 10, 'Psychiatry Physicians', 'Psychiatry-Physicians', '', 1),
(1251, 10, 'Psychologists', 'Psychologists', '', 1),
(1252, 10, 'Psychotherapists', 'Psychotherapists', '', 1),
(1253, 10, 'Pulmonary & Respiratory Physicians & Surgeons', 'Pulmonary-Respiratory-Physicians-Surgeons', '', 1),
(1254, 10, 'Radiology Physicians & Surgeons', 'Radiology-Physicians-Surgeons', '', 1),
(1255, 10, 'Rehabilitation Centers', 'Rehabilitation-Centers', '', 1),
(1256, 10, 'Rehabilitation Services', 'Rehabilitation-Services', '', 1),
(1257, 10, 'Respiratory Therapy', 'Respiratory-Therapy', '', 1),
(1258, 10, 'Sclerotherapy Physicians & Surgeons', 'Sclerotherapy-Physicians-Surgeons', '', 1),
(1259, 10, 'Sexually Transmitted Diseases Physicians & Surgeons', 'Sexually-Transmitted-Diseases-Physicians-Surgeons', '', 1),
(1260, 10, 'Small Animal Veterinarians', 'Small-Animal-Veterinarians', '', 1),
(1261, 10, 'Speech & Language Pathologists', 'Speech-Language-Pathologists', '', 1),
(1262, 10, 'Sperm Banks', 'Sperm-Banks', '', 1),
(1263, 10, 'Sports Medicine Chiropractors', 'Sports-Medicine-Chiropractors', '', 1),
(1264, 10, 'Sports Medicine Physicians & Surgeons', 'Sports-Medicine-Physicians-Surgeons', '', 1),
(1265, 10, 'Surgical Centers', 'Surgical-Centers', '', 1),
(1266, 10, 'TMJ Dentists', 'TMJ-Dentists', '', 1),
(1267, 10, 'Therapeutic Massage', 'Therapeutic-Massage', '', 1),
(1268, 10, 'Urology Physicians & Surgeons', 'Urology-Physicians-Surgeons', '', 1),
(1269, 10, 'Veterinarians', 'Veterinarians', '', 1),
(1270, 10, 'Vitamins & Food Supplements Retail', 'Vitamins-Food-Supplements-Retail', '', 1),
(1271, 10, 'Weight Loss & Control', 'Weight-Loss-Control', '', 1),
(1272, 10, 'Weight Loss & Control Consultants', 'Weight-Loss-Control-Consultants', '', 1),
(1273, 10, 'Weight Loss & Control Programs', 'Weight-Loss-Control-Programs', '', 1),
(1274, 10, 'Weight Loss Products', 'Weight-Loss-Products', '', 1),
(1275, 10, 'Wheelchair Lifts', 'Wheelchair-Lifts', '', 1),
(1276, 10, 'Wheelchair Rental & Leasing', 'Wheelchair-Rental-Leasing', '', 1),
(1277, 10, 'Wheelchairs Retail', 'Wheelchairs-Retail', '', 1),
(1278, 10, 'Women''s Health Physicians & Surgeons', 'Women''s-Health-Physicians-Surgeons', '', 1),
(1279, 10, 'Yoga Instruction & Therapy', 'Yoga-Instruction-Therapy', '', 1),
(1280, 11, 'ASID Interior Decorators & Designers', 'ASID-Interior-Decorators-Designers', '', 1),
(1281, 11, 'Adjustable Beds', 'Adjustable-Beds', '', 1),
(1282, 11, 'Air Cleaning & Purifying Equipment Dealers', 'Air-Cleaning-Purifying-Equipment-Dealers', '', 1),
(1283, 11, 'Air Conditioning Repair', 'Air-Conditioning-Repair', '', 1),
(1284, 11, 'Air Duct Cleaning', 'Air-Duct-Cleaning', '', 1),
(1285, 11, 'Alarm Services', 'Alarm-Services', '', 1),
(1286, 11, 'Alarm Systems Dealers', 'Alarm-Systems-Dealers', '', 1),
(1287, 11, 'Animal Shelters', 'Animal-Shelters', '', 1),
(1288, 11, 'Animal Trappers', 'Animal-Trappers', '', 1),
(1289, 11, 'Antique Dealers', 'Antique-Dealers', '', 1),
(1290, 11, 'Appliance Dealers', 'Appliance-Dealers', '', 1),
(1291, 11, 'Appliance Parts & Supplies Dealers', 'Appliance-Parts-Supplies-Dealers', '', 1),
(1292, 11, 'Appliance Repair', 'Appliance-Repair', '', 1),
(1293, 11, 'Appliance Repair', 'Appliance-Repair', '', 1),
(1294, 11, 'Aquariums & Supplies Retail', 'Aquariums-Supplies-Retail', '', 1),
(1295, 11, 'Arborists', 'Arborists', '', 1),
(1296, 11, 'Awnings, Canopies, & Shade Structures', 'Awnings,-Canopies,-Shade-Structures', '', 1),
(1297, 11, 'Baby Furniture Retail', 'Baby-Furniture-Retail', '', 1),
(1298, 11, 'Babysitting', 'Babysitting', '', 1),
(1299, 11, 'Bamboo, Rattan, & Wicker Home Furnishings', 'Bamboo,-Rattan,-Wicker-Home-Furnishings', '', 1),
(1300, 11, 'Bar Stools', 'Bar-Stools', '', 1),
(1301, 11, 'Barbecue Equipment & Supplies Retail', 'Barbecue-Equipment-Supplies-Retail', '', 1),
(1302, 11, 'Bathtubs & Sinks Repair & Refinishing', 'Bathtubs-Sinks-Repair-Refinishing', '', 1),
(1303, 11, 'Bathtubs Retail', 'Bathtubs-Retail', '', 1),
(1304, 11, 'Bedding Retail', 'Bedding-Retail', '', 1),
(1305, 11, 'Bedroom Furniture', 'Bedroom-Furniture', '', 1),
(1306, 11, 'Beds Retail', 'Beds-Retail', '', 1),
(1307, 11, 'Bee Control & Removal Services', 'Bee-Control-Removal-Services', '', 1),
(1308, 11, 'Birds & Supplies', 'Birds-Supplies', '', 1),
(1309, 11, 'Blinds Installation, Cleaning, & Repair', 'Blinds-Installation,-Cleaning,-Repair', '', 1),
(1310, 11, 'Blinds Retail & Custom', 'Blinds-Retail-Custom', '', 1),
(1311, 11, 'Burglar Alarms Installation, Service, & Repair', 'Burglar-Alarms-Installation,-Service,-Repair', '', 1),
(1312, 11, 'Cable TV', 'Cable-TV', '', 1),
(1313, 11, 'Carpet Cleaners', 'Carpet-Cleaners', '', 1),
(1314, 11, 'Carpet Installation', 'Carpet-Installation', '', 1),
(1315, 11, 'Carpet Stores', 'Carpet-Stores', '', 1),
(1316, 11, 'Carpet, Rug, & Upholstery Services', 'Carpet,-Rug,-Upholstery-Services', '', 1),
(1317, 11, 'Carpet, Rug, & Upholstery Storage & Repair', 'Carpet,-Rug,-Upholstery-Storage-Repair', '', 1),
(1318, 11, 'Carpets & Rugs Wholesale & Manufacturers', 'Carpets-Rugs-Wholesale-Manufacturers', '', 1),
(1319, 11, 'Ceiling Fans', 'Ceiling-Fans', '', 1),
(1320, 11, 'Children''s Furniture Stores', 'Children''s-Furniture-Stores', '', 1),
(1321, 11, 'Chimney Cleaning Services', 'Chimney-Cleaning-Services', '', 1),
(1322, 11, 'Cleaning Equipment & Supplies Retail', 'Cleaning-Equipment-Supplies-Retail', '', 1),
(1323, 11, 'Cleaning Services', 'Cleaning-Services', '', 1),
(1324, 11, 'Closed Circuit Systems', 'Closed-Circuit-Systems', '', 1),
(1325, 11, 'Contemporary & Modern Furniture Stores', 'Contemporary-Modern-Furniture-Stores', '', 1),
(1326, 11, 'Decorative & Specialty Concrete', 'Decorative-Specialty-Concrete', '', 1),
(1327, 11, 'Dinette Sets', 'Dinette-Sets', '', 1),
(1328, 11, 'Dishwasher Sales & Service', 'Dishwasher-Sales-Service', '', 1),
(1329, 11, 'Dog & Cat Grooming & Boarding', 'Dog-Cat-Grooming-Boarding', '', 1),
(1330, 11, 'Dog & Cat Supplies & Services', 'Dog-Cat-Supplies-Services', '', 1),
(1331, 11, 'Dog Training', 'Dog-Training', '', 1),
(1332, 11, 'Domestic Services', 'Domestic-Services', '', 1),
(1333, 11, 'Door & Gate Operating Devices Repair', 'Door-Gate-Operating-Devices-Repair', '', 1),
(1334, 11, 'Drain Cleaning', 'Drain-Cleaning', '', 1),
(1335, 11, 'Drain Services', 'Drain-Services', '', 1),
(1336, 11, 'Draperies & Curtains Retail', 'Draperies-Curtains-Retail', '', 1),
(1337, 11, 'Drapery & Curtain Fabrics Retail', 'Drapery-Curtain-Fabrics-Retail', '', 1),
(1338, 11, 'Electric Companies', 'Electric-Companies', '', 1),
(1339, 11, 'Fencing', 'Fencing', '', 1),
(1340, 11, 'Fire Alarm Sales & Service', 'Fire-Alarm-Sales-Service', '', 1),
(1341, 11, 'Fire Alarms Service & Repair', 'Fire-Alarms-Service-Repair', '', 1),
(1342, 11, 'Fire Extinguishers', 'Fire-Extinguishers', '', 1),
(1343, 11, 'Fireplace & Chimney Building & Repair', 'Fireplace-Chimney-Building-Repair', '', 1),
(1344, 11, 'Fireplaces & Accessories Retail', 'Fireplaces-Accessories-Retail', '', 1),
(1345, 11, 'Firewood', 'Firewood', '', 1),
(1346, 11, 'Flowers Wholesale', 'Flowers-Wholesale', '', 1),
(1347, 11, 'Furnaces', 'Furnaces', '', 1),
(1348, 11, 'Furniture Cleaning', 'Furniture-Cleaning', '', 1),
(1349, 11, 'Furniture Dealers'' Showrooms', 'Furniture-Dealers''-Showrooms', '', 1),
(1350, 11, 'Furniture Designers & Custom Builders', 'Furniture-Designers-Custom-Builders', '', 1),
(1351, 11, 'Furniture Refinishing & Repair', 'Furniture-Refinishing-Repair', '', 1),
(1352, 11, 'Furniture Rental & Leasing', 'Furniture-Rental-Leasing', '', 1),
(1353, 11, 'Furniture Stores', 'Furniture-Stores', '', 1),
(1354, 11, 'Furniture Wholesale & Manufacturers', 'Furniture-Wholesale-Manufacturers', '', 1),
(1355, 11, 'Futons & Accessories', 'Futons-Accessories', '', 1),
(1356, 11, 'Garage Door Repair', 'Garage-Door-Repair', '', 1),
(1357, 11, 'Garage Organizers', 'Garage-Organizers', '', 1),
(1358, 11, 'Garden Centers', 'Garden-Centers', '', 1),
(1359, 11, 'Gardeners', 'Gardeners', '', 1),
(1360, 11, 'Gas Companies', 'Gas-Companies', '', 1),
(1361, 11, 'Gate & Fence Repair', 'Gate-Fence-Repair', '', 1),
(1362, 11, 'Gates & Operating Devices', 'Gates-Operating-Devices', '', 1),
(1363, 11, 'Granite Countertops', 'Granite-Countertops', '', 1),
(1364, 11, 'Gutter & Downspout Cleaning & Repair', 'Gutter-Downspout-Cleaning-Repair', '', 1),
(1365, 11, 'Handyman', 'Handyman', '', 1),
(1366, 11, 'Heating Stove Dealers', 'Heating-Stove-Dealers', '', 1),
(1367, 11, 'Home Furnishings Stores', 'Home-Furnishings-Stores', '', 1),
(1368, 11, 'Home Improvement', 'Home-Improvement', '', 1),
(1369, 11, 'Home Improvement Referral & Information Services', 'Home-Improvement-Referral-Information-Services', '', 1),
(1370, 11, 'Home Improvement Stores', 'Home-Improvement-Stores', '', 1),
(1371, 11, 'Home Security Systems', 'Home-Security-Systems', '', 1),
(1372, 11, 'Horse & Livestock Trailer Dealers', 'Horse-Livestock-Trailer-Dealers', '', 1),
(1373, 11, 'Horse Trailers Wholesale & Manufacturers', 'Horse-Trailers-Wholesale-Manufacturers', '', 1),
(1374, 11, 'House Cleaning', 'House-Cleaning', '', 1),
(1375, 11, 'House Plans', 'House-Plans', '', 1),
(1376, 11, 'Housewares Stores', 'Housewares-Stores', '', 1),
(1377, 11, 'Humane Societies', 'Humane-Societies', '', 1),
(1378, 11, 'Hydroseeding Services', 'Hydroseeding-Services', '', 1),
(1379, 11, 'Interior Cleaning Services', 'Interior-Cleaning-Services', '', 1),
(1380, 11, 'Interior Decorators', 'Interior-Decorators', '', 1),
(1381, 11, 'Interior Plants Design & Maintenance', 'Interior-Plants-Design-Maintenance', '', 1),
(1382, 11, 'Kennels', 'Kennels', '', 1),
(1383, 11, 'Kitchen Gifts & Accessories', 'Kitchen-Gifts-Accessories', '', 1),
(1384, 11, 'Kitchen Planning & Remodeling Services', 'Kitchen-Planning-Remodeling-Services', '', 1),
(1385, 11, 'Kitchen Remodeling', 'Kitchen-Remodeling', '', 1),
(1386, 11, 'Kitchenware & Glassware Retail', 'Kitchenware-Glassware-Retail', '', 1),
(1387, 11, 'Knives & Cutlery Retail', 'Knives-Cutlery-Retail', '', 1),
(1388, 11, 'Lamps Retail', 'Lamps-Retail', '', 1),
(1389, 11, 'Landscape Contractors', 'Landscape-Contractors', '', 1),
(1390, 11, 'Landscape Designers', 'Landscape-Designers', '', 1),
(1391, 11, 'Landscape Equipment', 'Landscape-Equipment', '', 1),
(1392, 11, 'Landscape Gardening', 'Landscape-Gardening', '', 1),
(1393, 11, 'Landscape Lighting', 'Landscape-Lighting', '', 1),
(1394, 11, 'Landscape Maintenance', 'Landscape-Maintenance', '', 1),
(1395, 11, 'Landscaping', 'Landscaping', '', 1),
(1396, 11, 'Lawn & Garden Equipment & Supplies Retail', 'Lawn-Garden-Equipment-Supplies-Retail', '', 1),
(1397, 11, 'Lawn & Garden Outdoor Power Equipment', 'Lawn-Garden-Outdoor-Power-Equipment', '', 1),
(1398, 11, 'Lawn & Garden Sprinklers Installation & Service', 'Lawn-Garden-Sprinklers-Installation-Service', '', 1),
(1399, 11, 'Lawn & Garden Tractors Dealers', 'Lawn-Garden-Tractors-Dealers', '', 1),
(1400, 11, 'Lawn & Irrigation Sprinklers Retail', 'Lawn-Irrigation-Sprinklers-Retail', '', 1),
(1401, 11, 'Lawn Installation & Maintenance Services', 'Lawn-Installation-Maintenance-Services', '', 1),
(1402, 11, 'Lawn Mowers Retail', 'Lawn-Mowers-Retail', '', 1),
(1403, 11, 'Lawn Mowing Services', 'Lawn-Mowing-Services', '', 1),
(1404, 11, 'Lawn Services', 'Lawn-Services', '', 1),
(1405, 11, 'Lawn Spraying & Treatment', 'Lawn-Spraying-Treatment', '', 1),
(1406, 11, 'Licensed Plumbers', 'Licensed-Plumbers', '', 1),
(1407, 11, 'Lighting Fixtures Repair, Maintenance & Installation', 'Lighting-Fixtures-Repair,-Maintenance-Installation', '', 1),
(1408, 11, 'Lighting Retail', 'Lighting-Retail', '', 1),
(1409, 11, 'Local Phone Service', 'Local-Phone-Service', '', 1),
(1410, 11, 'Lockers', 'Lockers', '', 1),
(1411, 11, 'Locksmiths', 'Locksmiths', '', 1),
(1412, 11, 'Long Distance Phone Services', 'Long-Distance-Phone-Services', '', 1),
(1413, 11, 'Maids & Butlers', 'Maids-Butlers', '', 1),
(1414, 11, 'Major Appliance Dealers', 'Major-Appliance-Dealers', '', 1),
(1415, 11, 'Major Appliance Parts & Supplies', 'Major-Appliance-Parts-Supplies', '', 1),
(1416, 11, 'Major Electric Appliances Repair & Parts', 'Major-Electric-Appliances-Repair-Parts', '', 1),
(1417, 11, 'Marble & Terrazzo Cleaning & Service', 'Marble-Terrazzo-Cleaning-Service', '', 1),
(1418, 11, 'Mattresses', 'Mattresses', '', 1),
(1419, 11, 'Mulch', 'Mulch', '', 1),
(1420, 11, 'Nannies', 'Nannies', '', 1),
(1421, 11, 'Nurseries', 'Nurseries', '', 1),
(1422, 11, 'Oriental Carpet & Rug Dealers', 'Oriental-Carpet-Rug-Dealers', '', 1),
(1423, 11, 'Ornamental Shrub & Tree Services', 'Ornamental-Shrub-Tree-Services', '', 1),
(1424, 11, 'Outdoor Furniture Retail', 'Outdoor-Furniture-Retail', '', 1),
(1425, 11, 'Outdoor Lighting', 'Outdoor-Lighting', '', 1),
(1426, 11, 'Packaging & Shipping Materials Retail', 'Packaging-Shipping-Materials-Retail', '', 1),
(1427, 11, 'Painting & Decorating', 'Painting-Decorating', '', 1),
(1428, 11, 'Pasta & Pasta Machines', 'Pasta-Pasta-Machines', '', 1),
(1429, 11, 'Pest Control', 'Pest-Control', '', 1),
(1430, 11, 'Pesticides', 'Pesticides', '', 1),
(1431, 11, 'Pet Breeders', 'Pet-Breeders', '', 1),
(1432, 11, 'Pet Care Services', 'Pet-Care-Services', '', 1),
(1433, 11, 'Pet Cemeteries', 'Pet-Cemeteries', '', 1),
(1434, 11, 'Pet Food & Supplies Retail', 'Pet-Food-Supplies-Retail', '', 1),
(1435, 11, 'Pet Food & Supplies Wholesale & Manufacturers', 'Pet-Food-Supplies-Wholesale-Manufacturers', '', 1),
(1436, 11, 'Pet Grooming & Boarding', 'Pet-Grooming-Boarding', '', 1),
(1437, 11, 'Pet Related Services', 'Pet-Related-Services', '', 1),
(1438, 11, 'Pet Shops', 'Pet-Shops', '', 1),
(1439, 11, 'Pet Sitting & Day Care', 'Pet-Sitting-Day-Care', '', 1),
(1440, 11, 'Pet Training', 'Pet-Training', '', 1),
(1441, 11, 'Picture Frames Retail', 'Picture-Frames-Retail', '', 1),
(1442, 11, 'Picture Framing & Matting', 'Picture-Framing-Matting', '', 1),
(1443, 11, 'Plants & Trees Retail Nurseries', 'Plants-Trees-Retail-Nurseries', '', 1),
(1444, 11, 'Plants Retail', 'Plants-Retail', '', 1),
(1445, 11, 'Plumbers', 'Plumbers', '', 1),
(1446, 11, 'Pool Service', 'Pool-Service', '', 1),
(1447, 11, 'Pressure Cleaning Services Residential', 'Pressure-Cleaning-Services-Residential', '', 1),
(1448, 11, 'Range & Oven Parts & Supplies', 'Range-Oven-Parts-Supplies', '', 1),
(1449, 11, 'Range & Oven Sales & Service', 'Range-Oven-Sales-Service', '', 1),
(1450, 11, 'Ready-Mixed Concrete Dealers', 'Ready-Mixed-Concrete-Dealers', '', 1),
(1451, 11, 'Recycling Centers', 'Recycling-Centers', '', 1),
(1452, 11, 'Refrigerator & Freezer Sales & Service', 'Refrigerator-Freezer-Sales-Service', '', 1),
(1453, 11, 'Rent to Own', 'Rent-to-Own', '', 1),
(1454, 11, 'Rental Stores & Yards', 'Rental-Stores-Yards', '', 1),
(1455, 11, 'Sandblasting', 'Sandblasting', '', 1),
(1456, 11, 'Satellite TV', 'Satellite-TV', '', 1),
(1457, 11, 'Sauna Equipment & Supplies Dealers', 'Sauna-Equipment-Supplies-Dealers', '', 1),
(1458, 11, 'Security Cameras Dealers', 'Security-Cameras-Dealers', '', 1),
(1459, 11, 'Security Equipment Dealers', 'Security-Equipment-Dealers', '', 1),
(1460, 11, 'Self Service Laundries', 'Self-Service-Laundries', '', 1),
(1461, 11, 'Sewer & Drain Cleaning Contractors', 'Sewer-Drain-Cleaning-Contractors', '', 1),
(1462, 11, 'Shower Doors & Enclosures Retail', 'Shower-Doors-Enclosures-Retail', '', 1),
(1463, 11, 'Skylights Residential', 'Skylights-Residential', '', 1),
(1464, 11, 'Sodding & Seeding Services', 'Sodding-Seeding-Services', '', 1),
(1465, 11, 'Sodding Farms & Sales', 'Sodding-Farms-Sales', '', 1),
(1466, 11, 'Spa & Hot Tub Dealers', 'Spa-Hot-Tub-Dealers', '', 1),
(1467, 11, 'Spas & Hot Tubs Equipment, Parts, & Supplies Dealers', 'Spas-Hot-Tubs-Equipment,-Parts,-Supplies-Dealers', '', 1),
(1468, 11, 'Spas & Hot Tubs Service & Repair', 'Spas-Hot-Tubs-Service-Repair', '', 1),
(1469, 11, 'Swimming Pool Contractors', 'Swimming-Pool-Contractors', '', 1),
(1470, 11, 'Swimming Pool Cover & Enclosure Dealers', 'Swimming-Pool-Cover-Enclosure-Dealers', '', 1),
(1471, 11, 'Swimming Pool Dealers', 'Swimming-Pool-Dealers', '', 1),
(1472, 11, 'Swimming Pool Equipment & Supplies Dealers', 'Swimming-Pool-Equipment-Supplies-Dealers', '', 1),
(1473, 11, 'Swimming Pools Installers', 'Swimming-Pools-Installers', '', 1),
(1474, 11, 'Swimming Pools Remodeling & Renovation', 'Swimming-Pools-Remodeling-Renovation', '', 1),
(1475, 11, 'Table & Chair Rental & Leasing', 'Table-Chair-Rental-Leasing', '', 1),
(1476, 11, 'Television Service Providers', 'Television-Service-Providers', '', 1),
(1477, 11, 'Termite Control', 'Termite-Control', '', 1),
(1478, 11, 'Tile Flooring Retail', 'Tile-Flooring-Retail', '', 1),
(1479, 11, 'Tree Removal', 'Tree-Removal', '', 1),
(1480, 11, 'Tree Trimming Services', 'Tree-Trimming-Services', '', 1),
(1481, 11, 'Tropical & Saltwater Fish Retail', 'Tropical-Saltwater-Fish-Retail', '', 1),
(1482, 11, 'Unfinished Furniture', 'Unfinished-Furniture', '', 1),
(1483, 11, 'Used Appliances', 'Used-Appliances', '', 1),
(1484, 11, 'Used Furniture Dealers', 'Used-Furniture-Dealers', '', 1),
(1485, 11, 'Vacuum Cleaner Parts & Supplies Dealers', 'Vacuum-Cleaner-Parts-Supplies-Dealers', '', 1),
(1486, 11, 'Vacuum Cleaner Sales & Service', 'Vacuum-Cleaner-Sales-Service', '', 1),
(1487, 11, 'Vertical Blinds', 'Vertical-Blinds', '', 1),
(1488, 11, 'Vinyl Floor Installation', 'Vinyl-Floor-Installation', '', 1),
(1489, 11, 'Vinyl Flooring Retail', 'Vinyl-Flooring-Retail', '', 1),
(1490, 11, 'Wallpaper & Wallcovering Installation', 'Wallpaper-Wallcovering-Installation', '', 1),
(1491, 11, 'Wallpapers & Wallcoverings Equipment & Supplies Stores', 'Wallpapers-Wallcoverings-Equipment-Supplies-Stores', '', 1),
(1492, 11, 'Washing Machine & Dryer Parts & Supplies Dealers', 'Washing-Machine-Dryer-Parts-Supplies-Dealers', '', 1),
(1493, 11, 'Washing Machines & Dryers Service & Repair', 'Washing-Machines-Dryers-Service-Repair', '', 1),
(1494, 11, 'Water Damage Restoration', 'Water-Damage-Restoration', '', 1),
(1495, 11, 'Water Fountains', 'Water-Fountains', '', 1),
(1496, 11, 'Water Heater Repair', 'Water-Heater-Repair', '', 1),
(1497, 11, 'Waterbeds Retail', 'Waterbeds-Retail', '', 1),
(1498, 11, 'Weed Control Services', 'Weed-Control-Services', '', 1),
(1499, 11, 'Wholesale & Growers'' Nurseries', 'Wholesale-Growers''-Nurseries', '', 1),
(1500, 11, 'Window Cleaning', 'Window-Cleaning', '', 1),
(1501, 11, 'Window Coverings Retail', 'Window-Coverings-Retail', '', 1),
(1502, 12, 'Agricultural Equipment & Supplies Retail', 'Agricultural-Equipment-Supplies-Retail', '', 1),
(1503, 12, 'Air & Gas Compressor Dealers', 'Air-Gas-Compressor-Dealers', '', 1),
(1504, 12, 'Animal Feed', 'Animal-Feed', '', 1),
(1505, 12, 'Bags Wholesale & Manufacturers', 'Bags-Wholesale-Manufacturers', '', 1),
(1506, 12, 'Banners, Pennants, & Flags Wholesale & Manufacturers Supplies', 'Banners,-Pennants,-Flags-Wholesale-Manufacturers-Supplies', '', 1),
(1507, 12, 'Bar Code Scanning Equipment & Supplies', 'Bar-Code-Scanning-Equipment-Supplies', '', 1),
(1508, 12, 'Batteries Retail', 'Batteries-Retail', '', 1),
(1509, 12, 'Bearings Dealers', 'Bearings-Dealers', '', 1),
(1510, 12, 'Boxes & Bags Retail', 'Boxes-Bags-Retail', '', 1),
(1511, 12, 'CNC Machining, Turning, & Milling Shops', 'CNC-Machining,-Turning,-Milling-Shops', '', 1),
(1512, 12, 'Cash Registers & Supplies Dealers', 'Cash-Registers-Supplies-Dealers', '', 1),
(1513, 12, 'Chemicals Wholesale & Manufacturers', 'Chemicals-Wholesale-Manufacturers', '', 1),
(1514, 12, 'Chrome Plating', 'Chrome-Plating', '', 1),
(1515, 12, 'Containers Industrial', 'Containers-Industrial', '', 1),
(1516, 12, 'Corrugated & Fiber Boxes Retail', 'Corrugated-Fiber-Boxes-Retail', '', 1),
(1517, 12, 'Credit Card Equipment & Supplies', 'Credit-Card-Equipment-Supplies', '', 1),
(1518, 12, 'Diesel Engine Parts, Sales, & Service', 'Diesel-Engine-Parts,-Sales,-Service', '', 1),
(1519, 12, 'Electric Equipment Service & Repair', 'Electric-Equipment-Service-Repair', '', 1),
(1520, 12, 'Electric Motor Parts & Repair', 'Electric-Motor-Parts-Repair', '', 1),
(1521, 12, 'Exporters', 'Exporters', '', 1),
(1522, 12, 'Fabric Shops', 'Fabric-Shops', '', 1),
(1523, 12, 'Farm Equipment', 'Farm-Equipment', '', 1),
(1524, 12, 'Farms & Ranches', 'Farms-Ranches', '', 1),
(1525, 12, 'Flags & Flagpoles Wholesale & Manufacturers', 'Flags-Flagpoles-Wholesale-Manufacturers', '', 1),
(1526, 12, 'Forklift & Industrial Truck Dealers', 'Forklift-Industrial-Truck-Dealers', '', 1),
(1527, 12, 'Forklift & Industrial Truck Rental', 'Forklift-Industrial-Truck-Rental', '', 1),
(1528, 12, 'Fuel Oils', 'Fuel-Oils', '', 1),
(1529, 12, 'Fuels Retail', 'Fuels-Retail', '', 1),
(1530, 12, 'Gas Engines Retail', 'Gas-Engines-Retail', '', 1),
(1531, 12, 'General Merchandise Wholesale', 'General-Merchandise-Wholesale', '', 1),
(1532, 12, 'Generators Commercial & Industrial', 'Generators-Commercial-Industrial', '', 1),
(1533, 12, 'Importers', 'Importers', '', 1),
(1534, 12, 'Industrial Equipment & Supplies Dealers', 'Industrial-Equipment-Supplies-Dealers', '', 1),
(1535, 12, 'Industrial Equipment Rental & Leasing', 'Industrial-Equipment-Rental-Leasing', '', 1),
(1536, 12, 'Lathes', 'Lathes', '', 1),
(1537, 12, 'Machine Shops', 'Machine-Shops', '', 1),
(1538, 12, 'Magnets & Magnetic Devices', 'Magnets-Magnetic-Devices', '', 1),
(1539, 12, 'Manufacturing Production', 'Manufacturing-Production', '', 1),
(1540, 12, 'Materials Handling Equipment & Supplies Dealers', 'Materials-Handling-Equipment-Supplies-Dealers', '', 1),
(1541, 12, 'Metal Fabricators', 'Metal-Fabricators', '', 1),
(1542, 12, 'Metal Products Wholesale & Manufacturers', 'Metal-Products-Wholesale-Manufacturers', '', 1),
(1543, 12, 'Moving Boxes', 'Moving-Boxes', '', 1),
(1544, 12, 'Oil & Gas Companies', 'Oil-Gas-Companies', '', 1),
(1545, 12, 'Ornamental Iron Work', 'Ornamental-Iron-Work', '', 1),
(1546, 12, 'Packaging & Shipping Materials Wholesale & Manufacturers', 'Packaging-Shipping-Materials-Wholesale-Manufacturers', '', 1),
(1547, 12, 'Packing & Crating Materials', 'Packing-Crating-Materials', '', 1),
(1548, 12, 'Pallets & Skids Dealers', 'Pallets-Skids-Dealers', '', 1),
(1549, 12, 'Pallets & Skids Wholesale & Manufacturers', 'Pallets-Skids-Wholesale-Manufacturers', '', 1),
(1550, 12, 'Paper & Plastic Bags', 'Paper-Plastic-Bags', '', 1),
(1551, 12, 'Paper Products Manufacturers & Wholesalers', 'Paper-Products-Manufacturers-Wholesalers', '', 1),
(1552, 12, 'Paperboard Boxes Retail', 'Paperboard-Boxes-Retail', '', 1),
(1553, 12, 'Plastic & Plastic Products Retail', 'Plastic-Plastic-Products-Retail', '', 1),
(1554, 12, 'Plastic & Plastic Products Wholesale & Manufacturers', 'Plastic-Plastic-Products-Wholesale-Manufacturers', '', 1),
(1555, 12, 'Plastic Bags Wholesale & Manufacturers', 'Plastic-Bags-Wholesale-Manufacturers', '', 1),
(1556, 12, 'Plastic Injection Molding', 'Plastic-Injection-Molding', '', 1),
(1557, 12, 'Plastic Scrap', 'Plastic-Scrap', '', 1),
(1558, 12, 'Powder Coatings', 'Powder-Coatings', '', 1),
(1559, 12, 'Product Packaging, Labeling, & Shipping', 'Product-Packaging,-Labeling,-Shipping', '', 1),
(1560, 12, 'Propane', 'Propane', '', 1),
(1561, 12, 'Propane Gas Equipment & Supplies', 'Propane-Gas-Equipment-Supplies', '', 1),
(1562, 12, 'Pumps Service & Repair', 'Pumps-Service-Repair', '', 1),
(1563, 12, 'Pumps, Parts, & Supplies Dealers', 'Pumps,-Parts,-Supplies-Dealers', '', 1),
(1564, 12, 'Rack Dealers', 'Rack-Dealers', '', 1),
(1565, 12, 'Reptiles', 'Reptiles', '', 1),
(1566, 12, 'Rubber Products Retail', 'Rubber-Products-Retail', '', 1),
(1567, 12, 'Sawmills', 'Sawmills', '', 1),
(1568, 12, 'Scrap & Waste Materials', 'Scrap-Waste-Materials', '', 1),
(1569, 12, 'Scrap Iron & Metals', 'Scrap-Iron-Metals', '', 1),
(1570, 12, 'Scrap Metal Buyers', 'Scrap-Metal-Buyers', '', 1),
(1571, 12, 'Shelving', 'Shelving', '', 1),
(1572, 12, 'Shelving Commercial & Industrial', 'Shelving-Commercial-Industrial', '', 1),
(1573, 12, 'Specialty Bags', 'Specialty-Bags', '', 1),
(1574, 12, 'Store Fixtures, Counters, & Showcases', 'Store-Fixtures,-Counters,-Showcases', '', 1),
(1575, 12, 'Top Soil & Fill Soil Dealers', 'Top-Soil-Fill-Soil-Dealers', '', 1),
(1576, 12, 'Tractor Equipment & Parts Dealers', 'Tractor-Equipment-Parts-Dealers', '', 1),
(1577, 12, 'Upholstery Fabrics Retail', 'Upholstery-Fabrics-Retail', '', 1),
(1578, 12, 'Used & Rebuilt Industrial Equipment Dealers', 'Used-Rebuilt-Industrial-Equipment-Dealers', '', 1),
(1579, 12, 'Used Machine Tool Dealers', 'Used-Machine-Tool-Dealers', '', 1),
(1580, 12, 'Welding Contractors', 'Welding-Contractors', '', 1),
(1581, 12, 'Welding Equipment & Supplies Retail', 'Welding-Equipment-Supplies-Retail', '', 1),
(1582, 12, 'Welding Services', 'Welding-Services', '', 1),
(1583, 13, 'ATM Locations', 'ATM-Locations', '', 1),
(1584, 13, 'Accident & Health Insurance', 'Accident-Health-Insurance', '', 1),
(1585, 13, 'Accident Attorneys', 'Accident-Attorneys', '', 1),
(1586, 13, 'Accident Attorneys', 'Accident-Attorneys', '', 1),
(1587, 13, 'Accountants', 'Accountants', '', 1),
(1588, 13, 'Accounting & Tax Consultants', 'Accounting-Tax-Consultants', '', 1),
(1589, 13, 'Accounting, Auditing, & Bookkeeping Services', 'Accounting,-Auditing,-Bookkeeping-Services', '', 1),
(1590, 13, 'Accounts Receivable Loans', 'Accounts-Receivable-Loans', '', 1),
(1591, 13, 'Administrative & Governmental Law Attorneys', 'Administrative-Governmental-Law-Attorneys', '', 1),
(1592, 13, 'Adoption Attorneys', 'Adoption-Attorneys', '', 1),
(1593, 13, 'Appeals Attorneys', 'Appeals-Attorneys', '', 1),
(1594, 13, 'Appraisers', 'Appraisers', '', 1),
(1595, 13, 'Arbitration & Mediation Services Attorneys', 'Arbitration-Mediation-Services-Attorneys', '', 1),
(1596, 13, 'Arbitrators', 'Arbitrators', '', 1),
(1597, 13, 'Asbestos Diseases Attorneys', 'Asbestos-Diseases-Attorneys', '', 1),
(1598, 13, 'Attorneys'' Information & Referral Services', 'Attorneys''-Information-Referral-Services', '', 1),
(1599, 13, 'Auto Title Loans', 'Auto-Title-Loans', '', 1),
(1600, 13, 'Bail Bonds', 'Bail-Bonds', '', 1),
(1601, 13, 'Bail Bonds', 'Bail-Bonds', '', 1),
(1602, 13, 'Bail Bonds Referral Services', 'Bail-Bonds-Referral-Services', '', 1),
(1603, 13, 'Bail Bondsmen', 'Bail-Bondsmen', '', 1),
(1604, 13, 'Banking & Investment Law Attorneys', 'Banking-Investment-Law-Attorneys', '', 1),
(1605, 13, 'Bankruptcy Lawyers', 'Bankruptcy-Lawyers', '', 1),
(1606, 13, 'Bankruptcy Services', 'Bankruptcy-Services', '', 1),
(1607, 13, 'Banks', 'Banks', '', 1),
(1608, 13, 'Bookkeeping Services', 'Bookkeeping-Services', '', 1),
(1609, 13, 'Business Financing', 'Business-Financing', '', 1),
(1610, 13, 'Business Insurance', 'Business-Insurance', '', 1),
(1611, 13, 'Business Valuators', 'Business-Valuators', '', 1),
(1612, 13, 'Car Insurance', 'Car-Insurance', '', 1),
(1613, 13, 'Cash Advances', 'Cash-Advances', '', 1),
(1614, 13, 'Certified Public Accountants', 'Certified-Public-Accountants', '', 1),
(1615, 13, 'Check Cashing', 'Check-Cashing', '', 1),
(1616, 13, 'Civil Law Attorneys', 'Civil-Law-Attorneys', '', 1),
(1617, 13, 'Collection Agencies', 'Collection-Agencies', '', 1),
(1618, 13, 'Collection Law Attorneys', 'Collection-Law-Attorneys', '', 1),
(1619, 13, 'Commercial Loans', 'Commercial-Loans', '', 1),
(1620, 13, 'Consumer Protection Attorneys', 'Consumer-Protection-Attorneys', '', 1),
(1621, 13, 'Corporate Business Attorneys', 'Corporate-Business-Attorneys', '', 1),
(1622, 13, 'Court & Convention Reporters', 'Court-Convention-Reporters', '', 1),
(1623, 13, 'Court Reporting', 'Court-Reporting', '', 1),
(1624, 13, 'Credit Card Merchant Services', 'Credit-Card-Merchant-Services', '', 1),
(1625, 13, 'Credit Card Plans & Services', 'Credit-Card-Plans-Services', '', 1),
(1626, 13, 'Credit Card Terminal Systems', 'Credit-Card-Terminal-Systems', '', 1),
(1627, 13, 'Credit Cards', 'Credit-Cards', '', 1),
(1628, 13, 'Credit Rating Corrections Services', 'Credit-Rating-Corrections-Services', '', 1),
(1629, 13, 'Credit Reporting Agencies & Consultants', 'Credit-Reporting-Agencies-Consultants', '', 1),
(1630, 13, 'Credit Restoration', 'Credit-Restoration', '', 1),
(1631, 13, 'Credit Unions', 'Credit-Unions', '', 1),
(1632, 13, 'Criminal Defense Lawyers', 'Criminal-Defense-Lawyers', '', 1),
(1633, 13, 'Custody & Support Law Attorneys', 'Custody-Support-Law-Attorneys', '', 1),
(1634, 13, 'Debt Consolidation', 'Debt-Consolidation', '', 1),
(1635, 13, 'Debt Management', 'Debt-Management', '', 1),
(1636, 13, 'Debt Settlement', 'Debt-Settlement', '', 1),
(1637, 13, 'Disability Law Attorneys', 'Disability-Law-Attorneys', '', 1),
(1638, 13, 'Divorce & Mediation Services', 'Divorce-Mediation-Services', '', 1),
(1639, 13, 'Divorce Lawyers', 'Divorce-Lawyers', '', 1),
(1640, 13, 'Drug Charges Attorneys', 'Drug-Charges-Attorneys', '', 1),
(1641, 13, 'Dui Attorneys', 'Dui-Attorneys', '', 1),
(1642, 13, 'Elder Law Attorneys', 'Elder-Law-Attorneys', '', 1),
(1643, 13, 'Employee Benefits Insurance', 'Employee-Benefits-Insurance', '', 1),
(1644, 13, 'Employment & Labor Law Attorneys', 'Employment-Labor-Law-Attorneys', '', 1),
(1645, 13, 'Environmental & Natural Resources Attorneys', 'Environmental-Natural-Resources-Attorneys', '', 1),
(1646, 13, 'Estate Attorneys', 'Estate-Attorneys', '', 1),
(1647, 13, 'Factoring Services', 'Factoring-Services', '', 1),
(1648, 13, 'Family Law', 'Family-Law', '', 1),
(1649, 13, 'Financial Brokers', 'Financial-Brokers', '', 1),
(1650, 13, 'Financial Counselors', 'Financial-Counselors', '', 1),
(1651, 13, 'Financial Planning', 'Financial-Planning', '', 1),
(1652, 13, 'Financial Services', 'Financial-Services', '', 1),
(1653, 13, 'Fire Insurance', 'Fire-Insurance', '', 1),
(1654, 13, 'Flood Insurance', 'Flood-Insurance', '', 1),
(1655, 13, 'Foreign Currency Exchange & Brokers', 'Foreign-Currency-Exchange-Brokers', '', 1),
(1656, 13, 'Health Insurance', 'Health-Insurance', '', 1),
(1657, 13, 'Home Warranty Plans', 'Home-Warranty-Plans', '', 1),
(1658, 13, 'Immigration & Naturalization Consultants', 'Immigration-Naturalization-Consultants', '', 1),
(1659, 13, 'Immigration Law Attorneys', 'Immigration-Law-Attorneys', '', 1),
(1660, 13, 'Income Tax Consultants', 'Income-Tax-Consultants', '', 1),
(1661, 13, 'Income Tax Services', 'Income-Tax-Services', '', 1),
(1662, 13, 'Insurance', 'Insurance', '', 1),
(1663, 13, 'Insurance Adjusters', 'Insurance-Adjusters', '', 1),
(1664, 13, 'Insurance Agents', 'Insurance-Agents', '', 1),
(1665, 13, 'Insurance Annuities', 'Insurance-Annuities', '', 1),
(1666, 13, 'Insurance Claims & Services', 'Insurance-Claims-Services', '', 1),
(1667, 13, 'Insurance Consultants & Advisors', 'Insurance-Consultants-Advisors', '', 1),
(1668, 13, 'Insurance Information & Referral Services', 'Insurance-Information-Referral-Services', '', 1),
(1669, 13, 'Insurance Law Attorneys', 'Insurance-Law-Attorneys', '', 1),
(1670, 13, 'Intellectual Property Attorneys', 'Intellectual-Property-Attorneys', '', 1),
(1671, 13, 'Investment Advisory Services', 'Investment-Advisory-Services', '', 1),
(1672, 13, 'Investment Securities', 'Investment-Securities', '', 1),
(1673, 13, 'Investment Services', 'Investment-Services', '', 1),
(1674, 13, 'Investments', 'Investments', '', 1),
(1675, 13, 'Jewelry Appraisers', 'Jewelry-Appraisers', '', 1),
(1676, 13, 'Juvenile Law Attorneys', 'Juvenile-Law-Attorneys', '', 1),
(1677, 13, 'Landlord & Tenant Law Attorneys', 'Landlord-Tenant-Law-Attorneys', '', 1),
(1678, 13, 'Lawyers', 'Lawyers', '', 1),
(1679, 13, 'Legal Assistance', 'Legal-Assistance', '', 1),
(1680, 13, 'Legal Counsel & Prosecution', 'Legal-Counsel-Prosecution', '', 1),
(1681, 13, 'Legal Forms & Supplies', 'Legal-Forms-Supplies', '', 1),
(1682, 13, 'Legal Services', 'Legal-Services', '', 1),
(1683, 13, 'Life Insurance', 'Life-Insurance', '', 1),
(1684, 13, 'Loans', 'Loans', '', 1),
(1685, 13, 'Malpractice Lawyers', 'Malpractice-Lawyers', '', 1),
(1686, 13, 'Medical Malpractice Attorneys', 'Medical-Malpractice-Attorneys', '', 1),
(1687, 13, 'Mortgage & Loan Banks', 'Mortgage-Loan-Banks', '', 1),
(1688, 13, 'Mutual Funds & Brokers', 'Mutual-Funds-Brokers', '', 1),
(1689, 13, 'Notaries Public', 'Notaries-Public', '', 1),
(1690, 13, 'Paralegal Services', 'Paralegal-Services', '', 1),
(1691, 13, 'Patent & Trademark Attorneys', 'Patent-Trademark-Attorneys', '', 1),
(1692, 13, 'Payroll & Payroll Tax Preparation Services', 'Payroll-Payroll-Tax-Preparation-Services', '', 1),
(1693, 13, 'Payroll Services & Systems', 'Payroll-Services-Systems', '', 1),
(1694, 13, 'Personal Financial Services', 'Personal-Financial-Services', '', 1),
(1695, 13, 'Personal Financing', 'Personal-Financing', '', 1),
(1696, 13, 'Personal Injury Lawyers', 'Personal-Injury-Lawyers', '', 1),
(1697, 13, 'Personal Insurance', 'Personal-Insurance', '', 1),
(1698, 13, 'Personal Loans', 'Personal-Loans', '', 1),
(1699, 13, 'Process Service', 'Process-Service', '', 1),
(1700, 13, 'Product Liability Law Attorneys', 'Product-Liability-Law-Attorneys', '', 1),
(1701, 13, 'Property & Casualty Insurance', 'Property-Casualty-Insurance', '', 1),
(1702, 13, 'Public Accountants', 'Public-Accountants', '', 1),
(1703, 13, 'Public Insurance Adjusters', 'Public-Insurance-Adjusters', '', 1),
(1704, 13, 'Retirement Income Funds', 'Retirement-Income-Funds', '', 1),
(1705, 13, 'Retirement Planning Consultants & Services', 'Retirement-Planning-Consultants-Services', '', 1),
(1706, 13, 'Scholarships & Financial Aid', 'Scholarships-Financial-Aid', '', 1),
(1707, 13, 'Sexual Abuse Attorneys', 'Sexual-Abuse-Attorneys', '', 1),
(1708, 13, 'Social Security Attorneys', 'Social-Security-Attorneys', '', 1),
(1709, 13, 'Stock Brokers', 'Stock-Brokers', '', 1),
(1710, 13, 'Stocks & Bonds', 'Stocks-Bonds', '', 1),
(1711, 13, 'Stocks & Bonds Commodity Brokers', 'Stocks-Bonds-Commodity-Brokers', '', 1),
(1712, 13, 'Surety & Fidelity Bonds', 'Surety-Fidelity-Bonds', '', 1),
(1713, 13, 'Tax Consultants', 'Tax-Consultants', '', 1),
(1714, 13, 'Tax Lawyers', 'Tax-Lawyers', '', 1),
(1715, 13, 'Tax Return Preparation & Electronic Filing', 'Tax-Return-Preparation-Electronic-Filing', '', 1),
(1716, 13, 'Tax Return Preparation Accountants', 'Tax-Return-Preparation-Accountants', '', 1),
(1717, 13, 'Tax Returns', 'Tax-Returns', '', 1),
(1718, 13, 'Traffic Law Attorneys', 'Traffic-Law-Attorneys', '', 1),
(1719, 13, 'Travel Insurance', 'Travel-Insurance', '', 1),
(1720, 13, 'Trial Attorneys', 'Trial-Attorneys', '', 1),
(1721, 13, 'Workers Compensation Attorneys', 'Workers-Compensation-Attorneys', '', 1),
(1722, 13, 'Workers'' Compensation Insurance', 'Workers''-Compensation-Insurance', '', 1),
(1723, 13, 'Wrongful Death Attorneys', 'Wrongful-Death-Attorneys', '', 1),
(1724, 13, 'Wrongful Termination Attorneys', 'Wrongful-Termination-Attorneys', '', 1),
(1725, 14, '800 & 900 Phone Lines', '800-900-Phone-Lines', '', 1),
(1726, 14, 'Answering Services', 'Answering-Services', '', 1),
(1727, 14, 'Book Publishers', 'Book-Publishers', '', 1),
(1728, 14, 'Business Answering Services', 'Business-Answering-Services', '', 1),
(1729, 14, 'Business Cell Phone Equipment & Supplies', 'Business-Cell-Phone-Equipment-Supplies', '', 1),
(1730, 14, 'Business Phone Systems', 'Business-Phone-Systems', '', 1),
(1731, 14, 'Cell Phone Dealers', 'Cell-Phone-Dealers', '', 1),
(1732, 14, 'Cell Phone Equipment & Supplies', 'Cell-Phone-Equipment-Supplies', '', 1),
(1733, 14, 'Cell Phones', 'Cell-Phones', '', 1),
(1734, 14, 'Cell Phones', 'Cell-Phones', '', 1),
(1735, 14, 'Cellular PCS', 'Cellular-PCS', '', 1),
(1736, 14, 'Check Printing', 'Check-Printing', '', 1),
(1737, 14, 'Color Printing', 'Color-Printing', '', 1),
(1738, 14, 'Commercial Signs', 'Commercial-Signs', '', 1),
(1739, 14, 'Coupon & Ticket Printing', 'Coupon-Ticket-Printing', '', 1),
(1740, 14, 'Custom Signs', 'Custom-Signs', '', 1),
(1741, 14, 'Data Communications Cabling', 'Data-Communications-Cabling', '', 1),
(1742, 14, 'Etching & Engraving', 'Etching-Engraving', '', 1),
(1743, 14, 'Home Phone Services', 'Home-Phone-Services', '', 1),
(1744, 14, 'Legal & Financial Printing', 'Legal-Financial-Printing', '', 1),
(1745, 14, 'Lithographers', 'Lithographers', '', 1),
(1746, 14, 'Magazine & Journal Publishers', 'Magazine-Journal-Publishers', '', 1),
(1747, 14, 'Magazine Subscription Agents', 'Magazine-Subscription-Agents', '', 1),
(1748, 14, 'Outdoor Advertising & Billboards', 'Outdoor-Advertising-Billboards', '', 1),
(1749, 14, 'Phone Cards', 'Phone-Cards', '', 1),
(1750, 14, 'Phone Line Installation', 'Phone-Line-Installation', '', 1),
(1751, 14, 'Phone Repair', 'Phone-Repair', '', 1),
(1752, 14, 'Phone Service', 'Phone-Service', '', 1),
(1753, 14, 'Phone Systems', 'Phone-Systems', '', 1),
(1754, 14, 'Physicians'' & Surgeons'' Answering Services', 'Physicians''-Surgeons''-Answering-Services', '', 1),
(1755, 14, 'Radio Stations', 'Radio-Stations', '', 1),
(1756, 14, 'Ringtones', 'Ringtones', '', 1),
(1757, 14, 'Signs Wholesale & Manufacturers', 'Signs-Wholesale-Manufacturers', '', 1),
(1758, 14, 'Silk Screen Printing', 'Silk-Screen-Printing', '', 1),
(1759, 14, 'Telecommunications Equipment & Systems', 'Telecommunications-Equipment-Systems', '', 1),
(1760, 14, 'Telecommunications Equipment & Systems Service & Repair', 'Telecommunications-Equipment-Systems-Service-Repair', '', 1),
(1761, 14, 'Telecommunications Services', 'Telecommunications-Services', '', 1),
(1762, 14, 'Television Stations', 'Television-Stations', '', 1),
(1763, 14, 'Voice Mail Business Systems & Services', 'Voice-Mail-Business-Systems-Services', '', 1),
(1764, 14, 'Voice Mail Services', 'Voice-Mail-Services', '', 1),
(1765, 14, 'Voice Over Internet Protocol Equipment & Services', 'Voice-Over-Internet-Protocol-Equipment-Services', '', 1),
(1766, 14, 'Wireless Data Services', 'Wireless-Data-Services', '', 1),
(1767, 14, 'Wireless Internet', 'Wireless-Internet', '', 1),
(1768, 14, 'Yard Signs', 'Yard-Signs', '', 1),
(1769, 15, 'Barbers', 'Barbers', '', 1),
(1770, 15, 'Barbers'' Equipment & Supplies', 'Barbers''-Equipment-Supplies', '', 1),
(1771, 15, 'Beauticians', 'Beauticians', '', 1),
(1772, 15, 'Beauty Salon Equipment & Supplies Retail', 'Beauty-Salon-Equipment-Supplies-Retail', '', 1),
(1773, 15, 'Beauty Salon Equipment & Supplies Wholesale & Manufacturers', 'Beauty-Salon-Equipment-Supplies-Wholesale-Manufacturers', '', 1),
(1774, 15, 'Beauty Salons', 'Beauty-Salons', '', 1),
(1775, 15, 'Beauty Supplies & Equipment Retail', 'Beauty-Supplies-Equipment-Retail', '', 1),
(1776, 15, 'Body Art & Piercing', 'Body-Art-Piercing', '', 1),
(1777, 15, 'Body Wrap Salons', 'Body-Wrap-Salons', '', 1),
(1778, 15, 'Certified & Registered Massage Therapists', 'Certified-Registered-Massage-Therapists', '', 1),
(1779, 15, 'Cosmetic Tattoos', 'Cosmetic-Tattoos', '', 1),
(1780, 15, 'Cosmetics Retail', 'Cosmetics-Retail', '', 1),
(1781, 15, 'Dating Services', 'Dating-Services', '', 1),
(1782, 15, 'Day Spas', 'Day-Spas', '', 1),
(1783, 15, 'Delivery & Errand Services', 'Delivery-Errand-Services', '', 1),
(1784, 15, 'Diapers & Diaper Services', 'Diapers-Diaper-Services', '', 1),
(1785, 15, 'Ear & Body Piercing', 'Ear-Body-Piercing', '', 1),
(1786, 15, 'Electrolysis Treatments', 'Electrolysis-Treatments', '', 1),
(1787, 15, 'Escorts', 'Escorts', '', 1),
(1788, 15, 'Exercise Classes', 'Exercise-Classes', '', 1),
(1789, 15, 'Facial Treatments', 'Facial-Treatments', '', 1),
(1790, 15, 'Fitness Consultants', 'Fitness-Consultants', '', 1),
(1791, 15, 'Fitness Equipment', 'Fitness-Equipment', '', 1),
(1792, 15, 'Gymnastics Instruction', 'Gymnastics-Instruction', '', 1),
(1793, 15, 'Hair Care & Treatment', 'Hair-Care-Treatment', '', 1),
(1794, 15, 'Hair Care Products', 'Hair-Care-Products', '', 1),
(1795, 15, 'Hair Clippers', 'Hair-Clippers', '', 1),
(1796, 15, 'Hair Goods', 'Hair-Goods', '', 1),
(1797, 15, 'Hair Implants & Transplants', 'Hair-Implants-Transplants', '', 1),
(1798, 15, 'Hair Removal', 'Hair-Removal', '', 1),
(1799, 15, 'Hair Replacement', 'Hair-Replacement', '', 1),
(1800, 15, 'Hair Weaving', 'Hair-Weaving', '', 1),
(1801, 15, 'Health & Beauty Aids Retail', 'Health-Beauty-Aids-Retail', '', 1),
(1802, 15, 'Health Clubs', 'Health-Clubs', '', 1),
(1803, 15, 'Health Spas', 'Health-Spas', '', 1),
(1804, 15, 'Laser Hair Removal', 'Laser-Hair-Removal', '', 1),
(1805, 15, 'Licensed Massage Therapists', 'Licensed-Massage-Therapists', '', 1),
(1806, 15, 'Manicures', 'Manicures', '', 1),
(1807, 15, 'Martial Arts Equipment & Supplies', 'Martial-Arts-Equipment-Supplies', '', 1),
(1808, 15, 'Massage Equipment & Supplies', 'Massage-Equipment-Supplies', '', 1),
(1809, 15, 'Massage Schools', 'Massage-Schools', '', 1),
(1810, 15, 'Nail Salon Equipment & Supplies Wholesale', 'Nail-Salon-Equipment-Supplies-Wholesale', '', 1),
(1811, 15, 'Perfumes & Colognes Retail', 'Perfumes-Colognes-Retail', '', 1),
(1812, 15, 'Permanent Make Up', 'Permanent-Make-Up', '', 1),
(1813, 15, 'Personal Services', 'Personal-Services', '', 1),
(1814, 15, 'Personal Trainers', 'Personal-Trainers', '', 1),
(1815, 15, 'Sauna Baths', 'Sauna-Baths', '', 1),
(1816, 15, 'Skin Care Products', 'Skin-Care-Products', '', 1),
(1817, 15, 'Tanning Beds', 'Tanning-Beds', '', 1),
(1818, 15, 'Tanning Salon Equipment & Supplies Retail', 'Tanning-Salon-Equipment-Supplies-Retail', '', 1),
(1819, 15, 'Tanning Salons', 'Tanning-Salons', '', 1),
(1820, 15, 'Tattoo Removal', 'Tattoo-Removal', '', 1),
(1821, 15, 'Tattoos', 'Tattoos', '', 1),
(1822, 15, 'Waxing Hair Removal', 'Waxing-Hair-Removal', '', 1),
(1823, 15, 'Weight Training Centers', 'Weight-Training-Centers', '', 1),
(1824, 15, 'Weights', 'Weights', '', 1),
(1825, 15, 'Wigs & Hairpieces Retail', 'Wigs-Hairpieces-Retail', '', 1),
(1826, 16, 'Apartment Buildings', 'Apartment-Buildings', '', 1),
(1827, 16, 'Apartment Information & Referral Services', 'Apartment-Information-Referral-Services', '', 1),
(1828, 16, 'Apartment Rental Agencies', 'Apartment-Rental-Agencies', '', 1),
(1829, 16, 'Apartments', 'Apartments', '', 1),
(1830, 16, 'Architects Residential', 'Architects-Residential', '', 1),
(1831, 16, 'Assisted Living Facilities', 'Assisted-Living-Facilities', '', 1),
(1832, 16, 'Commercial & Industrial Rental & Leasing', 'Commercial-Industrial-Rental-Leasing', '', 1),
(1833, 16, 'Condominium & Townhouse Management', 'Condominium-Townhouse-Management', '', 1),
(1834, 16, 'Condominium & Townhouse Rental & Leasing', 'Condominium-Townhouse-Rental-Leasing', '', 1),
(1835, 16, 'Condominium Office Rental & Leasing', 'Condominium-Office-Rental-Leasing', '', 1),
(1836, 16, 'Condominiums & Townhouses', 'Condominiums-Townhouses', '', 1),
(1837, 16, 'Custom Home Builders', 'Custom-Home-Builders', '', 1),
(1838, 16, 'Executive Suites & Offices', 'Executive-Suites-Offices', '', 1),
(1839, 16, 'Extended Stay Hotels', 'Extended-Stay-Hotels', '', 1),
(1840, 16, 'Farm & Ranch Real Estate', 'Farm-Ranch-Real-Estate', '', 1),
(1841, 16, 'Furnished Apartments', 'Furnished-Apartments', '', 1),
(1842, 16, 'Golf Courses Architects', 'Golf-Courses-Architects', '', 1),
(1843, 16, 'Home Equity Loans', 'Home-Equity-Loans', '', 1),
(1844, 16, 'Homeowners Insurance', 'Homeowners-Insurance', '', 1),
(1845, 16, 'Homes & Residential Real Estate', 'Homes-Residential-Real-Estate', '', 1),
(1846, 16, 'Homes For Rent', 'Homes-For-Rent', '', 1),
(1847, 16, 'Land Companies', 'Land-Companies', '', 1),
(1848, 16, 'Landscape Plans', 'Landscape-Plans', '', 1),
(1849, 16, 'Mobile Home Insurance', 'Mobile-Home-Insurance', '', 1),
(1850, 16, 'Mobile Home Parks & Communities', 'Mobile-Home-Parks-Communities', '', 1),
(1851, 16, 'Mobile Home Service, Repair, & Improvements', 'Mobile-Home-Service,-Repair,-Improvements', '', 1),
(1852, 16, 'Mobile Homes Transporting', 'Mobile-Homes-Transporting', '', 1),
(1853, 16, 'Mortgage Amortization Schedules', 'Mortgage-Amortization-Schedules', '', 1),
(1854, 16, 'Mortgage Brokers', 'Mortgage-Brokers', '', 1),
(1855, 16, 'Mortgage Companies', 'Mortgage-Companies', '', 1),
(1856, 16, 'Mortgage Refinancing', 'Mortgage-Refinancing', '', 1),
(1857, 16, 'Mortgages', 'Mortgages', '', 1),
(1858, 16, 'Mortgages Commercial & Industrial', 'Mortgages-Commercial-Industrial', '', 1),
(1859, 16, 'Office Buildings & Parks', 'Office-Buildings-Parks', '', 1),
(1860, 16, 'Office Space', 'Office-Space', '', 1),
(1861, 16, 'Property Maintenance', 'Property-Maintenance', '', 1),
(1862, 16, 'Property Management', 'Property-Management', '', 1),
(1863, 16, 'Real Estate', 'Real-Estate', '', 1),
(1864, 16, 'Real Estate Agencies & Brokerage', 'Real-Estate-Agencies-Brokerage', '', 1),
(1865, 16, 'Real Estate Agents', 'Real-Estate-Agents', '', 1),
(1866, 16, 'Real Estate Appraisers', 'Real-Estate-Appraisers', '', 1),
(1867, 16, 'Real Estate Attorneys', 'Real-Estate-Attorneys', '', 1),
(1868, 16, 'Real Estate Brokers', 'Real-Estate-Brokers', '', 1),
(1869, 16, 'Real Estate Buyer Brokers', 'Real-Estate-Buyer-Brokers', '', 1),
(1870, 16, 'Real Estate Buyer Consultants', 'Real-Estate-Buyer-Consultants', '', 1),
(1871, 16, 'Real Estate Commercial & Industrial', 'Real-Estate-Commercial-Industrial', '', 1),
(1872, 16, 'Real Estate Consultants', 'Real-Estate-Consultants', '', 1),
(1873, 16, 'Real Estate Developers & Subdividers', 'Real-Estate-Developers-Subdividers', '', 1),
(1874, 16, 'Real Estate Foreclosure Assistance & Services', 'Real-Estate-Foreclosure-Assistance-Services', '', 1),
(1875, 16, 'Real Estate Inspection Services', 'Real-Estate-Inspection-Services', '', 1),
(1876, 16, 'Real Estate Investments', 'Real-Estate-Investments', '', 1),
(1877, 16, 'Real Estate Loans', 'Real-Estate-Loans', '', 1),
(1878, 16, 'Real Estate Rental & Leasing', 'Real-Estate-Rental-Leasing', '', 1),
(1879, 16, 'Real Estate Rental Information & Referral Services', 'Real-Estate-Rental-Information-Referral-Services', '', 1),
(1880, 16, 'Real Estate Schools', 'Real-Estate-Schools', '', 1),
(1881, 16, 'Real Estate Title Search & Abstract Services', 'Real-Estate-Title-Search-Abstract-Services', '', 1),
(1882, 16, 'Residential Real Estate Attorneys', 'Residential-Real-Estate-Attorneys', '', 1),
(1883, 16, 'Retirement Apartments & Hotels', 'Retirement-Apartments-Hotels', '', 1),
(1884, 16, 'Retirement Housing', 'Retirement-Housing', '', 1),
(1885, 16, 'Reverse Mortgages', 'Reverse-Mortgages', '', 1),
(1886, 16, 'Temporary Housing', 'Temporary-Housing', '', 1),
(1887, 16, 'Timeshares', 'Timeshares', '', 1),
(1888, 16, 'Title Insurance', 'Title-Insurance', '', 1),
(1889, 16, 'Unfurnished Apartments', 'Unfurnished-Apartments', '', 1),
(1890, 16, 'Used Modular & Mobile Homes', 'Used-Modular-Mobile-Homes', '', 1),
(1891, 17, 'Artificial Flowers & Trees Retail', 'Artificial-Flowers-Trees-Retail', '', 1),
(1892, 17, 'Baby Products & Services', 'Baby-Products-Services', '', 1),
(1893, 17, 'Balloons', 'Balloons', '', 1),
(1894, 17, 'Balloons Retail & Delivery', 'Balloons-Retail-Delivery', '', 1),
(1895, 17, 'Ballrooms', 'Ballrooms', '', 1),
(1896, 17, 'Birthday Parties', 'Birthday-Parties', '', 1),
(1897, 17, 'Bookstores', 'Bookstores', '', 1),
(1898, 17, 'Bridal Headpieces & Veils', 'Bridal-Headpieces-Veils', '', 1),
(1899, 17, 'Bridal Registries', 'Bridal-Registries', '', 1),
(1900, 17, 'Candles & Candle Equipment & Supplies', 'Candles-Candle-Equipment-Supplies', '', 1),
(1901, 17, 'Card Shops', 'Card-Shops', '', 1),
(1902, 17, 'Children''s Parties', 'Children''s-Parties', '', 1),
(1903, 17, 'Coin & Stamp Dealers & Supplies', 'Coin-Stamp-Dealers-Supplies', '', 1),
(1904, 17, 'Collectibles Dealers', 'Collectibles-Dealers', '', 1),
(1905, 17, 'Consignment Services & Shops', 'Consignment-Services-Shops', '', 1),
(1906, 17, 'Consumer Electronics Stores', 'Consumer-Electronics-Stores', '', 1),
(1907, 17, 'Convenience Stores', 'Convenience-Stores', '', 1),
(1908, 17, 'Corporate Gift Baskets', 'Corporate-Gift-Baskets', '', 1),
(1909, 17, 'Corporate Gifts Retail', 'Corporate-Gifts-Retail', '', 1),
(1910, 17, 'Department Stores', 'Department-Stores', '', 1),
(1911, 17, 'Disc Jockeys', 'Disc-Jockeys', '', 1),
(1912, 17, 'Discount Stores', 'Discount-Stores', '', 1),
(1913, 17, 'Factory Outlets', 'Factory-Outlets', '', 1),
(1914, 17, 'Flags, Flagpoles, Banners, & Pennants', 'Flags,-Flagpoles,-Banners,-Pennants', '', 1),
(1915, 17, 'Flea Markets', 'Flea-Markets', '', 1),
(1916, 17, 'Floral Wire Services', 'Floral-Wire-Services', '', 1),
(1917, 17, 'Florists', 'Florists', '', 1),
(1918, 17, 'Fruit Baskets', 'Fruit-Baskets', '', 1),
(1919, 17, 'Funeral Flowers', 'Funeral-Flowers', '', 1),
(1920, 17, 'Games & Supplies Dealers', 'Games-Supplies-Dealers', '', 1),
(1921, 17, 'General Stores', 'General-Stores', '', 1),
(1922, 17, 'Gift Baskets', 'Gift-Baskets', '', 1),
(1923, 17, 'Gift Shops', 'Gift-Shops', '', 1),
(1924, 17, 'Hair Braiding', 'Hair-Braiding', '', 1),
(1925, 17, 'Hobby & Model Stores', 'Hobby-Model-Stores', '', 1),
(1926, 17, 'Inflatables', 'Inflatables', '', 1),
(1927, 17, 'Invitations & Announcements', 'Invitations-Announcements', '', 1),
(1928, 17, 'Leather Goods Retail', 'Leather-Goods-Retail', '', 1),
(1929, 17, 'Luggage Stores', 'Luggage-Stores', '', 1),
(1930, 17, 'Maps & Globes', 'Maps-Globes', '', 1),
(1931, 17, 'Newspapers & Magazines', 'Newspapers-Magazines', '', 1),
(1932, 17, 'Notary & Corporation Seals Retail', 'Notary-Corporation-Seals-Retail', '', 1),
(1933, 17, 'Online Shopping', 'Online-Shopping', '', 1),
(1934, 17, 'Outlet Malls', 'Outlet-Malls', '', 1),
(1935, 17, 'Party Entertainment', 'Party-Entertainment', '', 1),
(1936, 17, 'Party Facilities', 'Party-Facilities', '', 1),
(1937, 17, 'Party Favors', 'Party-Favors', '', 1),
(1938, 17, 'Party Supplies', 'Party-Supplies', '', 1),
(1939, 17, 'Pawn Brokers & Shops', 'Pawn-Brokers-Shops', '', 1),
(1940, 17, 'Plaques Retail', 'Plaques-Retail', '', 1),
(1941, 17, 'Postcards', 'Postcards', '', 1),
(1942, 17, 'Resale, Second Hand, & Used Merchandise Stores', 'Resale,-Second-Hand,-Used-Merchandise-Stores', '', 1),
(1943, 17, 'Shopping Centers & Malls', 'Shopping-Centers-Malls', '', 1),
(1944, 17, 'Shopping Services', 'Shopping-Services', '', 1),
(1945, 17, 'Singing Telegrams', 'Singing-Telegrams', '', 1),
(1946, 17, 'Specialty Stores', 'Specialty-Stores', '', 1),
(1947, 17, 'Sports Cards & Memorabilia', 'Sports-Cards-Memorabilia', '', 1),
(1948, 17, 'Thrift Stores', 'Thrift-Stores', '', 1),
(1949, 17, 'Toy Stores', 'Toy-Stores', '', 1),
(1950, 17, 'Travel Accessories', 'Travel-Accessories', '', 1),
(1951, 17, 'Trophies, Medals, & Awards Engravers', 'Trophies,-Medals,-Awards-Engravers', '', 1),
(1952, 17, 'Trophy Shops', 'Trophy-Shops', '', 1),
(1953, 17, 'Used, Rare, & Out of Print Books', 'Used,-Rare,-Out-of-Print-Books', '', 1),
(1954, 17, 'Variety Stores', 'Variety-Stores', '', 1),
(1955, 17, 'Video Games', 'Video-Games', '', 1),
(1956, 17, 'Wedding Chapels', 'Wedding-Chapels', '', 1),
(1957, 17, 'Wedding Facilities', 'Wedding-Facilities', '', 1),
(1958, 17, 'Wedding Flowers', 'Wedding-Flowers', '', 1),
(1959, 17, 'Wedding Gifts & Favors', 'Wedding-Gifts-Favors', '', 1),
(1960, 17, 'Wedding Invitations & Announcements', 'Wedding-Invitations-Announcements', '', 1),
(1961, 17, 'Wedding Planners', 'Wedding-Planners', '', 1),
(1962, 17, 'Wedding Receptions & Parties', 'Wedding-Receptions-Parties', '', 1),
(1963, 17, 'Wedding Services', 'Wedding-Services', '', 1),
(1964, 17, 'Weddings', 'Weddings', '', 1),
(1965, 18, 'Baseball Clubs & Parks', 'Baseball-Clubs-Parks', '', 1),
(1966, 18, 'Baseball Equipment & Supplies', 'Baseball-Equipment-Supplies', '', 1),
(1967, 18, 'Baseball Instruction', 'Baseball-Instruction', '', 1),
(1968, 18, 'Basketball Clubs & Instruction', 'Basketball-Clubs-Instruction', '', 1),
(1969, 18, 'Batting Ranges', 'Batting-Ranges', '', 1),
(1970, 18, 'Bicycle Dealers', 'Bicycle-Dealers', '', 1),
(1971, 18, 'Billiard & Pool Halls', 'Billiard-Pool-Halls', '', 1),
(1972, 18, 'Billiard Table Dealers', 'Billiard-Table-Dealers', '', 1),
(1973, 18, 'Billiards Equipment & Supplies Dealers', 'Billiards-Equipment-Supplies-Dealers', '', 1),
(1974, 18, 'Bingo', 'Bingo', '', 1),
(1975, 18, 'Boarding Stables', 'Boarding-Stables', '', 1),
(1976, 18, 'Boat & Yacht Charters, Rental, & Leasing', 'Boat-Yacht-Charters,-Rental,-Leasing', '', 1),
(1977, 18, 'Boat & Yacht Insurance', 'Boat-Yacht-Insurance', '', 1),
(1978, 18, 'Boat Dealers', 'Boat-Dealers', '', 1),
(1979, 18, 'Bowling Alleys', 'Bowling-Alleys', '', 1),
(1980, 18, 'Bowling Equipment & Supplies Dealers', 'Bowling-Equipment-Supplies-Dealers', '', 1),
(1981, 18, 'Boxing Clubs & Instruction', 'Boxing-Clubs-Instruction', '', 1),
(1982, 18, 'Campgrounds', 'Campgrounds', '', 1),
(1983, 18, 'Camping & Backpacking Equipment & Supplies Dealers', 'Camping-Backpacking-Equipment-Supplies-Dealers', '', 1),
(1984, 18, 'Camps', 'Camps', '', 1),
(1985, 18, 'Canoe & Kayak Dealers', 'Canoe-Kayak-Dealers', '', 1),
(1986, 18, 'Casinos', 'Casinos', '', 1),
(1987, 18, 'Chess Instruction', 'Chess-Instruction', '', 1),
(1988, 18, 'Country Clubs', 'Country-Clubs', '', 1),
(1989, 18, 'Diving Equipment & Supplies', 'Diving-Equipment-Supplies', '', 1),
(1990, 18, 'Exercise & Fitness Equipment & Supplies Wholesale & Manufacturers', 'Exercise-Fitness-Equipment-Supplies-Wholesale-Manufacturers', '', 1),
(1991, 18, 'Fishing Bait & Tackle', 'Fishing-Bait-Tackle', '', 1),
(1992, 18, 'Fishing Equipment & Supplies Dealers', 'Fishing-Equipment-Supplies-Dealers', '', 1),
(1993, 18, 'Fishing Guides & Charters', 'Fishing-Guides-Charters', '', 1),
(1994, 18, 'Golf', 'Golf', '', 1),
(1995, 18, 'Golf Carts', 'Golf-Carts', '', 1),
(1996, 18, 'Golf Course Consultants', 'Golf-Course-Consultants', '', 1),
(1997, 18, 'Golf Courses', 'Golf-Courses', '', 1),
(1998, 18, 'Golf Driving & Practice Ranges', 'Golf-Driving-Practice-Ranges', '', 1),
(1999, 18, 'Golf Equipment & Supplies Retail', 'Golf-Equipment-Supplies-Retail', '', 1),
(2000, 18, 'Gun Safety & Marksmanship Instruction', 'Gun-Safety-Marksmanship-Instruction', '', 1),
(2001, 18, 'Guns & Ammunition Dealers', 'Guns-Ammunition-Dealers', '', 1),
(2002, 18, 'Gunsmiths', 'Gunsmiths', '', 1),
(2003, 18, 'Hobbies', 'Hobbies', '', 1),
(2004, 18, 'Horse Breeders & Dealers', 'Horse-Breeders-Dealers', '', 1),
(2005, 18, 'Horseback Riding', 'Horseback-Riding', '', 1),
(2006, 18, 'Hot Air Balloon Rides & Rental', 'Hot-Air-Balloon-Rides-Rental', '', 1),
(2007, 18, 'Ice Skating Rinks & Instruction', 'Ice-Skating-Rinks-Instruction', '', 1),
(2008, 18, 'Karate, Judo, & Kung Fu Instruction', 'Karate,-Judo,-Kung-Fu-Instruction', '', 1),
(2009, 18, 'Lottery Tickets', 'Lottery-Tickets', '', 1),
(2010, 18, 'Martial Arts Instruction', 'Martial-Arts-Instruction', '', 1),
(2011, 18, 'Metal Detectors', 'Metal-Detectors', '', 1),
(2012, 18, 'Miniature Golf Courses', 'Miniature-Golf-Courses', '', 1),
(2013, 18, 'Outdoor Sports & Recreation', 'Outdoor-Sports-Recreation', '', 1),
(2014, 18, 'Paintball Equipment & Supplies', 'Paintball-Equipment-Supplies', '', 1),
(2015, 18, 'Parks & Playgrounds', 'Parks-Playgrounds', '', 1),
(2016, 18, 'Personal Watercraft Sales & Rental', 'Personal-Watercraft-Sales-Rental', '', 1),
(2017, 18, 'Playground Equipment Dealers', 'Playground-Equipment-Dealers', '', 1),
(2018, 18, 'Private Golf Courses', 'Private-Golf-Courses', '', 1),
(2019, 18, 'Professional & Amateur Sports Teams', 'Professional-Amateur-Sports-Teams', '', 1),
(2020, 18, 'Public & Private Swimming Pools', 'Public-Private-Swimming-Pools', '', 1),
(2021, 18, 'Public Golf Courses', 'Public-Golf-Courses', '', 1),
(2022, 18, 'RV & Camper Equipment, Parts, & Supplies Retail', 'RV-Camper-Equipment,-Parts,-Supplies-Retail', '', 1),
(2023, 18, 'RV & Camper Rental', 'RV-Camper-Rental', '', 1),
(2024, 18, 'RV Dealers', 'RV-Dealers', '', 1),
(2025, 18, 'Recreation Centers', 'Recreation-Centers', '', 1),
(2026, 18, 'Roller Skating Rinks & Instruction', 'Roller-Skating-Rinks-Instruction', '', 1),
(2027, 18, 'Sailboat Charter & Rental', 'Sailboat-Charter-Rental', '', 1),
(2028, 18, 'Scuba & Skin Diving', 'Scuba-Skin-Diving', '', 1),
(2029, 18, 'Skate Shops', 'Skate-Shops', '', 1),
(2030, 18, 'Skateboard & Inline Skates Sales & Rental', 'Skateboard-Inline-Skates-Sales-Rental', '', 1),
(2031, 18, 'Ski Resorts', 'Ski-Resorts', '', 1),
(2032, 18, 'Skiing Clothing, Equipment & Supplies Retail', 'Skiing-Clothing,-Equipment-Supplies-Retail', '', 1),
(2033, 18, 'Skydiving', 'Skydiving', '', 1),
(2034, 18, 'Snorkeling', 'Snorkeling', '', 1),
(2035, 18, 'Snowmobile Dealers', 'Snowmobile-Dealers', '', 1),
(2036, 18, 'Snowmobile Rentals & Tours', 'Snowmobile-Rentals-Tours', '', 1),
(2037, 18, 'Sporting Goods', 'Sporting-Goods', '', 1),
(2038, 18, 'Sporting Goods Wholesale & Manufacturers', 'Sporting-Goods-Wholesale-Manufacturers', '', 1),
(2039, 18, 'Sports & Recreation Clubs & Organizations', 'Sports-Recreation-Clubs-Organizations', '', 1),
(2040, 18, 'Sports & Recreation Facilities', 'Sports-Recreation-Facilities', '', 1),
(2041, 18, 'Sports Camps', 'Sports-Camps', '', 1),
(2042, 18, 'Tennis Clubs', 'Tennis-Clubs', '', 1),
(2043, 18, 'Tennis Courts', 'Tennis-Courts', '', 1),
(2044, 18, 'Tennis Equipment & Supplies Retail', 'Tennis-Equipment-Supplies-Retail', '', 1),
(2045, 18, 'Tent Sales & Rental', 'Tent-Sales-Rental', '', 1),
(2046, 18, 'Theme Parks', 'Theme-Parks', '', 1),
(2047, 18, 'Water Sports', 'Water-Sports', '', 1),
(2048, 19, 'Accommodation Reservations', 'Accommodation-Reservations', '', 1),
(2049, 19, 'Air Cargo & Package Express Services', 'Air-Cargo-Package-Express-Services', '', 1),
(2050, 19, 'Air Transportation', 'Air-Transportation', '', 1),
(2051, 19, 'Aircraft Charter, Rental, & Leasing', 'Aircraft-Charter,-Rental,-Leasing', '', 1),
(2052, 19, 'Airline Flight Information', 'Airline-Flight-Information', '', 1),
(2053, 19, 'Airline Tickets', 'Airline-Tickets', '', 1),
(2054, 19, 'Airline Travel Agents', 'Airline-Travel-Agents', '', 1),
(2055, 19, 'Airports', 'Airports', '', 1),
(2056, 19, 'Auto Parts Warehouses', 'Auto-Parts-Warehouses', '', 1),
(2057, 19, 'Bed & Breakfasts', 'Bed-Breakfasts', '', 1),
(2058, 19, 'Bus Charter', 'Bus-Charter', '', 1),
(2059, 19, 'Bus Tours', 'Bus-Tours', '', 1),
(2060, 19, 'Bus Travel', 'Bus-Travel', '', 1),
(2061, 19, 'Cabin, Cottage, & Chalet Rental', 'Cabin,-Cottage,-Chalet-Rental', '', 1),
(2062, 19, 'Commodity & Merchandise Warehouses', 'Commodity-Merchandise-Warehouses', '', 1),
(2063, 19, 'Commuter & Public Transportation', 'Commuter-Public-Transportation', '', 1),
(2064, 19, 'Contract Haulers', 'Contract-Haulers', '', 1),
(2065, 19, 'Corporate Limousine Services', 'Corporate-Limousine-Services', '', 1),
(2066, 19, 'Courier & Delivery Services Commercial & Industrial', 'Courier-Delivery-Services-Commercial-Industrial', '', 1),
(2067, 19, 'Courier & Delivery Services Residential', 'Courier-Delivery-Services-Residential', '', 1),
(2068, 19, 'Cruise Lines & Agents', 'Cruise-Lines-Agents', '', 1),
(2069, 19, 'Cruises Travel Agents', 'Cruises-Travel-Agents', '', 1),
(2070, 19, 'Customs Brokers & Consultants', 'Customs-Brokers-Consultants', '', 1),
(2071, 19, 'Delivery Services', 'Delivery-Services', '', 1),
(2072, 19, 'Distribution Services', 'Distribution-Services', '', 1),
(2073, 19, 'Docks', 'Docks', '', 1),
(2074, 19, 'Fishing Boat Charters, Tours, & Rental', 'Fishing-Boat-Charters,-Tours,-Rental', '', 1),
(2075, 19, 'Freight & Cargo Containers', 'Freight-Cargo-Containers', '', 1),
(2076, 19, 'Freight & Cargo Transport', 'Freight-Cargo-Transport', '', 1),
(2077, 19, 'Freight Agents & Brokers', 'Freight-Agents-Brokers', '', 1),
(2078, 19, 'Freight Forwarding', 'Freight-Forwarding', '', 1),
(2079, 19, 'Freight Trucking', 'Freight-Trucking', '', 1),
(2080, 19, 'Full Service Moving & Storage', 'Full-Service-Moving-Storage', '', 1),
(2081, 19, 'Furniture Movers', 'Furniture-Movers', '', 1),
(2082, 19, 'Heavy Duty Trucking', 'Heavy-Duty-Trucking', '', 1),
(2083, 19, 'Helicopter Charter, Rental, & Leasing', 'Helicopter-Charter,-Rental,-Leasing', '', 1),
(2084, 19, 'Honeymoon Resorts', 'Honeymoon-Resorts', '', 1),
(2085, 19, 'Hotel Reservations', 'Hotel-Reservations', '', 1),
(2086, 19, 'Hotels', 'Hotels', '', 1),
(2087, 19, 'Household Goods & Furniture Storage', 'Household-Goods-Furniture-Storage', '', 1),
(2088, 19, 'Household Movers', 'Household-Movers', '', 1),
(2089, 19, 'Inns', 'Inns', '', 1),
(2090, 19, 'International Movers', 'International-Movers', '', 1),
(2091, 19, 'Limo Services', 'Limo-Services', '', 1),
(2092, 19, 'Local Bus Charters', 'Local-Bus-Charters', '', 1),
(2093, 19, 'Local Trucking', 'Local-Trucking', '', 1),
(2094, 19, 'Lodging', 'Lodging', '', 1),
(2095, 19, 'Logistics', 'Logistics', '', 1),
(2096, 19, 'Mail Box Rental', 'Mail-Box-Rental', '', 1),
(2097, 19, 'Mail Services & Package Shipping', 'Mail-Services-Package-Shipping', '', 1),
(2098, 19, 'Mailing Services', 'Mailing-Services', '', 1),
(2099, 19, 'Marinas', 'Marinas', '', 1),
(2100, 19, 'Marine Equipment & Supplies Retail', 'Marine-Equipment-Supplies-Retail', '', 1),
(2101, 19, 'Mini & Self Storage Warehouses', 'Mini-Self-Storage-Warehouses', '', 1),
(2102, 19, 'Motels', 'Motels', '', 1),
(2103, 19, 'Movers', 'Movers', '', 1),
(2104, 19, 'Movers', 'Movers', '', 1),
(2105, 19, 'Moving & Storage Commercial & Industrial', 'Moving-Storage-Commercial-Industrial', '', 1),
(2106, 19, 'Moving Equipment & Supplies Rental', 'Moving-Equipment-Supplies-Rental', '', 1),
(2107, 19, 'Moving Labor Services', 'Moving-Labor-Services', '', 1),
(2108, 19, 'Packaging', 'Packaging', '', 1),
(2109, 19, 'Passport & Visa Services', 'Passport-Visa-Services', '', 1),
(2110, 19, 'Piano & Organ Movers', 'Piano-Organ-Movers', '', 1),
(2111, 19, 'Portable Storage Rental', 'Portable-Storage-Rental', '', 1),
(2112, 19, 'Railroads', 'Railroads', '', 1),
(2113, 19, 'Resorts & Vacation Cottages', 'Resorts-Vacation-Cottages', '', 1),
(2114, 19, 'Self Service Moving & Storage', 'Self-Service-Moving-Storage', '', 1),
(2115, 19, 'Self Storage', 'Self-Storage', '', 1),
(2116, 19, 'Shuttle Transportation Services', 'Shuttle-Transportation-Services', '', 1),
(2117, 19, 'Snow Removal', 'Snow-Removal', '', 1),
(2118, 19, 'Specialty Warehousing & Storage', 'Specialty-Warehousing-Storage', '', 1),
(2119, 19, 'Storage Racks', 'Storage-Racks', '', 1),
(2120, 19, 'Taxi Services', 'Taxi-Services', '', 1),
(2121, 19, 'Taxis', 'Taxis', '', 1),
(2122, 19, 'Tourist Information & Attractions', 'Tourist-Information-Attractions', '', 1),
(2123, 19, 'Tours & Charters', 'Tours-Charters', '', 1),
(2124, 19, 'Transportation Equipment', 'Transportation-Equipment', '', 1),
(2125, 19, 'Transportation Services', 'Transportation-Services', '', 1),
(2126, 19, 'Travel Agents', 'Travel-Agents', '', 1),
(2127, 19, 'Travel Clubs & Services', 'Travel-Clubs-Services', '', 1),
(2128, 19, 'Travel Consultants', 'Travel-Consultants', '', 1),
(2129, 19, 'Travel Industry', 'Travel-Industry', '', 1),
(2130, 19, 'Trucking Companies', 'Trucking-Companies', '', 1),
(2131, 19, 'Vacation Home Rental', 'Vacation-Home-Rental', '', 1),
(2132, 19, 'Vacation Packages', 'Vacation-Packages', '', 1),
(2133, 19, 'Warehouses & Warehousing', 'Warehouses-Warehousing', '', 1),
(2134, 19, 'Water Taxis', 'Water-Taxis', '', 1);  

UPDATE `[db_prefix]categ_bak` SET `uri`=REPLACE(`uri`,'''','');

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
(1, 'Listing', 'Listing', '', 64);

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
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_view', 'Listing View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_main', 'Listing Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_my', 'Listing My', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_category', 'Listing Category', @iMaxOrder+6);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_subcategory', 'Listing Sub-Category', @iMaxOrder+7); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_local', 'Local Listing Page', @iMaxOrder+8); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_local_state', 'Local Listing State Page', @iMaxOrder+9); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_packages', 'Listing Packages', @iMaxOrder+10); 
  

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
     ('modzzz_listing_view', '1140px', 'Listing''s action block', '_modzzz_listing_block_gerer_action', '1', '0', 'ManageMyEntreprise', '', '1', 71.9, 'non,memb', '0'),

    ('modzzz_listing_view', '1140px', 'Listing''s description block', '_modzzz_listing_block_desc', '1', '0', 'Desc', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s operating hours block', '_modzzz_listing_form_caption_operation', '1', '3', 'OperatingHours', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s media block', '_modzzz_listing_block_media', '1', '3', 'Media', '', '1', 71.9, 'non,memb', '0'), 
    ('modzzz_listing_view', '1140px', 'Listing''s photo block', '_modzzz_listing_block_photo', '0', '0', 'Photo', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s video embed block', '_modzzz_listing_block_video_embed', '0', '0', 'VideoEmbed', '', '1', 71.9, 'non,memb', '0'), 
    ('modzzz_listing_view', '1140px', 'Listing''s videos block', '_modzzz_listing_block_video', '0', '0', 'Video', '', '1', 71.9, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s sounds block', '_modzzz_listing_block_sound', '0', '0', 'Sound', '', '1', 71.9, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s files block', '_modzzz_listing_block_file', '0', '0', 'Files', '', '1', 71.9, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s local block', '_modzzz_listing_block_local', '1', '8', 'Local', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s other block', '_modzzz_listing_block_other', '1', '9', 'Other', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s forum Posts', '_modzzz_listing_block_forum', '1', '11', 'Forum', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s jobs block', '_modzzz_listing_block_jobs', '1', '14', 'Jobs', '', '1', 71.9, 'non,memb', '0'),  
	
    ('modzzz_listing_view', '1140px', 'Listing''s reviews block', '_modzzz_listing_block_reviews', '1', '15', 'Reviews', '', '1', 71.9, 'non,memb', '0'),  
    ('modzzz_listing_view', '1140px', 'Listing''s deal finder block', '_modzzz_listing_block_deals', '1', '16', 'Deals', '', '1', 71.9, 'non,memb', '0'),  
    ('modzzz_listing_view', '1140px', 'Listing''s comments block', '_modzzz_listing_block_comments', '1', '17', 'Comments', '', '1', 71.9, 'non,memb', '0'),
	('modzzz_listing_view', '1140px', 'Listing''s Classified block', '_modzzz_listing_block_classified', '1', '18', 'Classified', '', '1', 71.9, 'non,memb', '0'), 


    ('modzzz_listing_view', '1140px', 'Listing''s logo block', '_modzzz_listing_block_logo', '2', '0', 'Logo', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_listing_view', '1140px', 'Listing''s info block', '_modzzz_listing_block_info', '2',  '1', 'Info', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s actions block', '_modzzz_listing_block_actions', '2',  '2', 'Actions', '', '1', 28.1, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s rate block', '_modzzz_listing_block_rate', '2',  '3', 'Rate', '', '1', 28.1, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s social sharing block', '_sys_block_title_social_sharing', 2, 4, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
    ('modzzz_listing_view', '1140px', 'Listing''s fans block', '_modzzz_listing_block_fans', '2',  '5', 'Fans', '', '1', 28.1, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s unconfirmed fans block', '_modzzz_listing_block_fans_unconfirmed', '2',  '6', 'FansUnconfirmed', '', '1', 28.1, 'memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s location on map block', '_modzzz_listing_block_map', 2, 7, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''listing'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
    ('modzzz_listing_view', '1140px', 'Listing''s location block', '_modzzz_listing_block_location', '2',  '8', 'Location', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s business contact block', '_modzzz_listing_business_contact', '2',  '9', 'BusinessContact', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_view', '1140px', 'Listing''s categories block', '_modzzz_listing_block_categories', '2',  '10', 'Categories', '', '1', 28.1, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s tags block', '_modzzz_listing_tags', 2, 11, 'Tags', '', 1, 28.1, 'non,memb', 0),  
    ('modzzz_listing_view', '1140px', 'Listing''s employees block', '_modzzz_listing_block_employees', '2',  '12', 'Employees', '', '1', 28.1, 'non,memb', '0'),    
    ('modzzz_listing_view', '1140px', 'Listing''s unconfirmed employees block', '_modzzz_listing_block_employees_unconfirmed', '2', '13', 'EmployeesUnconfirmed', '', '1', 28.1, 'memb', '0'),

    ('modzzz_listing_local_state', '1140px', 'Listing Local State Listings', '_modzzz_listing_block_browse_state_listings', '1', '0', 'StateListings', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_local_state', '1140px', 'Listing Categories Drilldown', '_modzzz_listing_block_browse_categories_drilldown', '2', '0', 'Categories', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_local_state', '1140px', 'Listing Local States', '_modzzz_listing_block_browse_state', '2', '1', 'States', '', '1', 28.1, 'non,memb', '0'),

    ('modzzz_listing_local', '1140px', 'Local Listings', '_modzzz_listing_block_browse_country', '1', '0', 'Region', '', '1', '100', 'non,memb', '0'),  
    
    ('modzzz_listing_category', '1140px', 'Listing', '_modzzz_listing_block_listing', '1', '0', 'CategoryListing', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_category', '1140px', 'Listing Categories', '_modzzz_listing_block_categories', '2', '0', 'Categories', '', '1', 28.1, 'non,memb', '0'),
      
    ('modzzz_listing_subcategory', '1140px', 'Listing Category Listings', '_modzzz_listing_block_category_listing', '1', '0', 'SubCategoryListings', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_subcategory', '1140px', 'Listing Sub-Categories', '_modzzz_listing_block_subcategories', '2', '0', 'SubCategories', '', '1', 28.1, 'non,memb', '0'),
	
	('modzzz_listing_main', '1140px', 'Block inscription et recherche', '_modzzz_listing_inscription', '2', '4', 'InscriptionEntreprise', '', '1', '71.9', 'non,memb', '0'),
	

    ('modzzz_listing_main', '1140px', 'Latest Featured Listing', '_modzzz_listing_block_latest_featured_listing', '1', '0', 'LatestFeaturedListing', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_main', '1140px', 'Map', '_Map', '1', '1', 'PHP', 'return BxDolService::call(''wmap'', ''homepage_part_block'', array (''listing''));', 1, 71.9, 'non,memb', 0),
    ('modzzz_listing_main', '1140px', 'Recent Listing', '_modzzz_listing_block_recent', '1', '2', 'Recent', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_main', '1140px', 'Listing Forum Posts', '_modzzz_listing_block_forum', '1', '3', 'Forum', '', '1', 71.9, 'non,memb', '0'),
    ('modzzz_listing_main', '1140px', 'Listing Comments', '_modzzz_listing_block_latest_comments', '1', '4', 'Comments', '', '1', 71.9, 'non,memb', '0'), 
    ('modzzz_listing_main', '1140px', 'Listing States', '_modzzz_listing_block_listing_states', '1', '5', 'States', '', '1', 71.9, 'non,memb', '0'), 

    ('modzzz_listing_main', '1140px', 'Listing Create', '_modzzz_listing_block_listing_create', '2', '0', 'Create', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_main', '1140px', 'Search Listing', '_modzzz_listing_block_search', '2', '1', 'Search', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_main', '1140px', 'Listing Categories', '_modzzz_listing_block_listing_categories', '2', '2', 'ListingCategories', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_main', '1140px', 'Listing Tags', '_tags_plural', '2', '4', 'Tags', '', '1', 28.1, 'non,memb', '0'), 
    ('modzzz_listing_main', '1140px', 'Listing Calendar', '_modzzz_listing_block_calendar', '2', '5', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
  
    ('modzzz_listing_packages', '1140px', 'Listing Packages', '_modzzz_listing_block_packages', '1', '0', 'Packages', '', '1', '100', 'non,memb', '0'),  
 
    ('modzzz_listing_my', '1140px', 'Administration Owner', '_modzzz_listing_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('modzzz_listing_my', '1140px', 'User''s listing', '_modzzz_listing_block_users_listing', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('index', '1140px', 'Listings', '_modzzz_listing_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''listing'', ''homepage_block'');', 1, 71.9, 'non,memb', 0),
    ('member', '1140px', 'My Listings', '_modzzz_listing_block_my_listings', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''listing'', ''accountpage_block'');', 1, 71.9, 'non,memb', 0),  
    ('member', '1140px', 'Local Listings', '_modzzz_listing_block_area_listings', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''listing'', ''accountarea_block'');', 1, 71.9, 'non,memb', 0), 
    ('profile', '1140px', 'User Employers', '_modzzz_listing_block_my_employers', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''listing'', ''profile_employers_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0),
    ('profile', '1140px', 'User Listings', '_modzzz_listing_block_my_listing', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''listing'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0);


 

-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=listing/', 'm/listing/', 'modzzz_listing_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Listing', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_listing_permalinks', 'on', 26, 'Enable friendly permalinks in listing', 'checkbox', '', '', '0', ''),
('modzzz_listing_autoapproval', 'on', @iCategId, 'Activate all listing after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_listing_author_comments_admin', 'on', @iCategId, 'Allow listing admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('modzzz_listing_perpage_main_recent', '10', @iCategId, 'Number of recently added listings to show on listing home', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_main_featured', '5', @iCategId, 'Number of featured listings to show on listing home', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_browse', '14', @iCategId, 'Number of listing to show on browse pages', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_profile', '4', @iCategId, 'Number of listing to show on profile page', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_accountpage', '4', @iCategId, 'Number of listing to show on account page', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_homepage', '5', @iCategId, 'Number of listing to show on homepage', 'digit', '', '', '0', ''),
('modzzz_listing_homepage_default_tab', 'featured', @iCategId, 'Default listing block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),

('modzzz_listing_icon_width', '240', @iCategId, 'Width of logo icon', 'digit', '', '', '0', ''),
('modzzz_listing_icon_height', '240', @iCategId, 'Height of logo icon', 'digit', '', '', '0', ''), 
 
('modzzz_listing_perpage_view_employees', '6', @iCategId, 'Number of employees to show on listing view page', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_browse_employees', '30', @iCategId, 'Number of employees to show on browse employees page', 'digit', '', '', '0', ''),
 
('modzzz_listing_perpage_view_fans', '6', @iCategId, 'Number of fans to show on listing view page', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_browse_fans', '30', @iCategId, 'Number of fans to show on browse fans page', 'digit', '', '', '0', ''),
('modzzz_listing_max_preview', '300', @iCategId, 'Length of listing description snippet to show in blocks', 'digit', '', '', '0', ''),
('modzzz_listing_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', ''), 
('modzzz_listing_free_expired', '0', @iCategId, 'number of days before free listing expires (0-never expires)', 'digit', '', '', '0', ''), 
('modzzz_listing_activate_expiring', 'on', @iCategId, 'activate sending email notification of soon to expire listings', 'checkbox', '', '', '0', ''),  
('modzzz_listing_activate_expired', 'on', @iCategId, 'activate sending email notification of expired listings', 'checkbox', '', '', '0', ''), 
('modzzz_listing_email_expiring', '3', @iCategId, 'number of days before expiry to send email notification (0=same day)', 'digit', '', '', '0', ''), 
('modzzz_listing_email_expired', '3', @iCategId, 'number of days after expiry to send email notification (0=same day)', 'digit', '', '', '0', ''),  
('modzzz_listing_max_email_invitations', '10', @iCategId, 'Max number of email to send per one promotion', 'digit', '', '', '0', ''),

('modzzz_listing_forum_max_preview', '300', @iCategId, 'length of forum post snippet to show on main page', 'digit', '', '', '0', ''),
('modzzz_listing_comments_max_preview', '300', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
('modzzz_listing_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''), 
('modzzz_listing_per_page', '7', @iCategId, 'Listings search results to show per page', 'digit', '', '', '0', ''),
 
('modzzz_listing_default_country', 'US', @iCategId, 'default country for location', 'digit', '', '', 0, ''),
 
('modzzz_listing_paypal_email', '', @iCategId, 'Paypal Email', 'digit', '', '', 0, ''),
('modzzz_listing_paid_active', '', @iCategId, 'Activate Paid Listings',  'checkbox', '', '', 0, ''), 
('modzzz_listing_currency_code', 'USD', @iCategId, 'Currency code for checkout system (eg. USD,EUR,GBP)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_listing_currency_sign', '&#36;', @iCategId, 'Currency sign (for display purposes only)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_listing_paypal_item_desc', 'Business Listing Package purchase', @iCategId, 'Item decription displayed on PayPal Package Purchase', 'digit', '', '', 0, ''), 
('modzzz_listing_invoice_valid_days', '100', @iCategId, 'Number of Days before pending Invoices expire<br>(blank or zero means no expiration)', 'digit', '', '', 0, ''),
('modzzz_listing_jobs_active', '', @iCategId, 'Jobs integration is activated', 'checkbox', '', '', '0', ''),  
('modzzz_listing_formations_active', '', @iCategId, 'Formations integration is activated', 'checkbox', '', '', '0', ''), 
('modzzz_listing_deals_active', '', @iCategId, 'Deal Finder integration is activated', 'checkbox', '', '', '0', ''),  
 ('modzzz_listing_classified_active', '', @iCategId, 'Classified integration is activated', 'checkbox', '', '', '0', ''),  
('modzzz_listing_perpage_view_subitems', '6', @iCategId, 'Number of sub-items (Coupons, Deals etc.) to show on listing view page', 'digit', '', '', '0', ''),
('modzzz_listing_perpage_browse_subitems', '30', @iCategId, 'Number of sub-items (Coupons, Deals etc.) to show on the listing sub section browse page', 'digit', '', '', '0', '')
;
 
 



-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'modzzz_listing', '_modzzz_listing', 'BxListingSearchResult', 'modules/modzzz/listing/classes/BxListingSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_listing', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxListingVoting', 'modules/modzzz/listing/classes/BxListingVoting.php');

-- comments objects  
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_listing', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxListingCmts', 'modules/modzzz/listing/classes/BxListingCmts.php');



-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_listing', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'modzzz_listing', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_listing_permalinks', 'm/listing/browse/tag/{tag}', 'modules/?r=listing/browse/tag/{tag}', '_modzzz_listing');
 
 
-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    
    ('{TitleAddDeal}', 'plus-circle', '{evalResult}', '', 'return $GLOBALS[''oBxListingModule'']->getModulePostUrl(''deals'', {ID});', 30, 'modzzz_listing'), 
 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'modzzz_listing'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( 'ajaxy_popup_result_div_{ID}', '{evalResult}', false, 'post', true);return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'modzzz_listing'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'modzzz_listing'),
    ('{TitleBroadcast}', 'envelope', '{BaseUri}broadcast/{ID}', '', '', '3', 'modzzz_listing'),  
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '6', 'modzzz_listing'),
    ('{AddToFavorite}', 'hand-o-right', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_favorite/{ID}'';', '6', 'modzzz_listing'), 
     
    ('{TitleEmployeeAdmin}', '{IconWork}', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''add_employee/{ID}'';', '7', 'modzzz_listing'), 
    ('{TitleEmployee}', '{IconWork}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''work/{ID}/{iViewer}'';', '7', 'modzzz_listing'),
    ('{TitleManageEmployees}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_employees_popup/{ID}'');', '', '8', 'modzzz_listing'),
    
    ('{TitleJoin}', '{IconJoin}', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''join/{ID}/{iViewer}'';', '9', 'modzzz_listing'),
    ('{TitleManageFans}', 'users', '', 'showPopupAnyHtml (''{BaseUri}manage_fans_popup/{ID}'');', '', '10', 'modzzz_listing'),
 
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '11', 'modzzz_listing'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '12', 'modzzz_listing'),
    ('{TitleEmbed}', 'film', '{BaseUri}embed/{URI}', '', '', '12', 'modzzz_listing'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '13', 'modzzz_listing'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '14', 'modzzz_listing'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '15', 'modzzz_listing'),
    ('{TitleInquire}', 'question-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''inquire/{ID}'';', '16', 'modzzz_listing'), 
    ('{TitleClaim}', 'key', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''claim/{ID}'';', '17', 'modzzz_listing'), 
    ('{TitleInvite}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '18', 'modzzz_listing'), 
    ('{TitlePurchaseFeatured}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''purchase_featured/{ID}'';', '19', 'modzzz_listing'),
 
     ('{evalResult}', 'shopping-cart', '', 'showPopupAnyHtml (site_url+''m/listing/pay_feature/{ID}'')', '$oMain = BxDolModule::getInstance(''BxListingModule'');if ( $oMain->isAllowedPurchaseFeatured({ID}) ) return _t(''_modzzz_listing_action_title_purchase_featured'');', 17, 'modzzz_listing'), 

    ('{TitleRelist}', 'refresh', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''relist/{ID}'';', '20', 'modzzz_listing'), 
    ('{TitleExtend}', 'wrench', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''extend/{ID}'';', '21', 'modzzz_listing'), 
    ('{TitlePremium}', 'money', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''premium/{ID}'';', '22', 'modzzz_listing'),  
 
    ('{evalResult}', 'plus', '{BaseUri}browse/my&filter=add_listing', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_listing_action_add_listing'') : '''';', '1', 'modzzz_listing_title'),
    ('{evalResult}', 'briefcase', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_listing_action_my_listing'') : '''';', '2', 'modzzz_listing_title');
   

-- top menu 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Listing', '_modzzz_listing_menu_root', 'modules/?r=listing/view/|modules/?r=listing/edit/|modules/?r=listing/claim/|modules/?r=listing/inquire/|modules/?r=listing/website/|modules/?r=listing/add_employee/|modules/?r=listing/invite/|forum/listing/|modules/?r=listing/relist/|modules/?r=listing/extend/|modules/?r=listing/premium/|modules/?r=listing/purchase_featured/|modules/?r=listing/broadcast/|modules/?r=listing/embed/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'briefcase', '', '0', '');
 

SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES

(NULL, @iCatRoot, 'Listing View', '_modzzz_listing_menu_view_listing', 'modules/?r=listing/view/{modzzz_listing_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),
 
(NULL, @iCatRoot, 'Listing View Employees', '_modzzz_listing_menu_view_employees', 'modules/?r=listing/browse_employees/{modzzz_listing_view_uri}', 1, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),

(NULL, @iCatRoot, 'Listing View Fans', '_modzzz_listing_menu_view_fans', 'modules/?r=listing/browse_fans/{modzzz_listing_view_uri}', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),

(NULL, @iCatRoot, 'Listing View Comments', '_modzzz_listing_menu_view_comments', 'modules/?r=listing/comments/{modzzz_listing_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),

(NULL, @iCatRoot, 'Listing View Reviews', '_modzzz_listing_menu_view_reviews', 'modules/?r=listing/review/browse/{modzzz_listing_view_uri}', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''), 

(NULL, @iCatRoot, 'Listing View Forum', '_modzzz_listing_menu_view_forum', 'forum/listing/forum/{modzzz_listing_view_uri}-0.htm|forum/listing/', 5, 'non,memb', '', '', '$oModuleDb = new BxDolModuleDb(); return $oModuleDb->getModuleByUri(''forum'') ? true : false;', 1, 1, 1, 'custom', '', '', 0, ''),

(NULL, @iCatRoot, 'Listing View Website', '_modzzz_listing_menu_view_website', 'modules/?r=listing/website/{modzzz_listing_view_uri}', 6, 'non,memb', '', '', '$oMain = BxDolModule::getInstance(''BxListingModule''); return ($oMain!=null) ? $oMain->hasWebsite() : false;', 1, 1, 1, 'custom', '', '', 0, '');
 



SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Listing', '_modzzz_listing_menu_root', 'modules/?r=listing/home/|modules/?r=listing/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'briefcase', 'briefcase', 1, '');
SET @iCatRoot := LAST_INSERT_ID();

INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Listings Main Page', '_modzzz_listing_menu_main', 'modules/?r=listing/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Recent Listings', '_modzzz_listing_menu_recent', 'modules/?r=listing/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Top Rated Listings', '_modzzz_listing_menu_top_rated', 'modules/?r=listing/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Popular Listings', '_modzzz_listing_menu_popular', 'modules/?r=listing/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Featured Listings', '_modzzz_listing_menu_featured', 'modules/?r=listing/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Favorite Listings', '_modzzz_listing_menu_favorite', 'modules/?r=listing/browse/favorite', 6, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Listings Tags', '_modzzz_listing_menu_tags', 'modules/?r=listing/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_listing');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Listings Categories', '_modzzz_listing_menu_categories', 'modules/?r=listing/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_listing');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Calendar', '_modzzz_listing_menu_calendar', 'modules/?r=listing/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES (NULL, @iCatRoot, 'Listings in my Area', '_modzzz_listing_menu_local', 'modules/?r=listing/browse/ilocal', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');  
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Local Listings', '_modzzz_listing_menu_drilldown', 'modules/?r=listing/local', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''); 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Listing Search', '_modzzz_listing_menu_search', 'modules/?r=listing/search', 13, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Listing Packages', '_modzzz_listing_menu_packages', 'modules/?r=listing/packages', 14, 'non,memb', '', '', '$oMain = BxDolModule::getInstance(''BxListingModule'');return $oMain->isAllowedPaidListings(false);', 1, 1, 1, 'custom', '', '', 0, '');
 

 
SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1),1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 9, 'Listing', '_modzzz_listing_menu_my_listing_profile', 'modules/?r=listing/browse/user/{profileUsername}|modules/?r=listing/browse/joined/{profileUsername}|modules/?r=listing/browse/worked/{profileUsername}', ifnull(@iCatProfileOrder, 1), 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
 

SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1),1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 4, 'Listing', '_modzzz_listing_menu_my_listing_profile', 'modules/?r=listing/browse/my', ifnull(@iCatProfileOrder, 1), 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_listing', '_modzzz_listing', '{siteUrl}modules/?r=listing/administration/', 'Listing module by Modzzz','briefcase', @iMax+1);

-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'modzzz_listing', 'modzzz_listing', 'modules/?r=listing/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=listing/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'briefcase', @iStatSiteOrder);


-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('modzzz_listing', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('modzzz_listingp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_modzzz_listing', '__modzzz_listing__ (<a href="modules/?r=listing/browse/my&filter=add_listing">__l_add__</a>)');
 

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing broadcast message', NULL);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing extend', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing relist', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing purchase', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing photos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing sounds add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing videos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing files add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

  
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing purchase featured', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing view listing', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing add listing', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing mark as favorite', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing edit any listing', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing delete any listing', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing approve listing', NULL);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing make claim', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing make inquiry', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_listing_profile_delete', '', '', 'BxDolService::call(''listing'', ''response_profile_delete'', array($this));');
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_listing_profile_join', '', '', 'BxDolService::call(''listing'', ''response_profile_join'', array($this));');

SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES 
(NULL , 'profile', 'delete', @iHandler),
(NULL, 'profile', 'join', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_listing_media_delete', '', '', 'BxDolService::call(''listing'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);
 

-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'modzzz_listing', `Eval` = 'return BxDolService::call(''listing'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);
 

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES  
('listing', 'view_fans', '_modzzz_listing_privacy_view_fans', '3'), 
('listing', 'view_employees', '_modzzz_listing_privacy_view_employees', '3'), 
('listing', 'view_listing', '_modzzz_listing_privacy_view_listing', '3'), 
('listing', 'comment', '_modzzz_listing_privacy_comment', 'f'),
('listing', 'rate', '_modzzz_listing_privacy_rate', 'f'),
('listing', 'post_in_forum', '_modzzz_listing_privacy_post_in_forum', 'f'),
('listing', 'join', '_modzzz_listing_privacy_join', '3'),  
('listing', 'upload_photos', '_modzzz_listing_privacy_upload_photos', 'a'),
('listing', 'upload_videos', '_modzzz_listing_privacy_upload_videos', 'a'),
('listing', 'upload_sounds', '_modzzz_listing_privacy_upload_sounds', 'a'),
('listing', 'upload_files', '_modzzz_listing_privacy_upload_files', 'a');


-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('modzzz_listing', '', '', 'return BxDolService::call(''listing'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_listing', 'change', 'modzzz_listing_sbs', 'return BxDolService::call(''listing'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_listing', 'commentPost', 'modzzz_listing_sbs', 'return BxDolService::call(''listing'', ''get_subscription_params'', array($arg2, $arg3));'),

('modzzz_listing', 'work', 'modzzz_listing_sbs', 'return BxDolService::call(''listing'', ''get_subscription_params'', array($arg2, $arg3));'), 

('modzzz_listing', 'join', 'modzzz_listing_sbs', 'return BxDolService::call(''listing'', ''get_subscription_params'', array($arg2, $arg3));'); 
   

-- email templates 

INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_listing_broadcast', '<BroadcastTitle>', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p><a href="<EntryUrl>"><EntryTitle></a> listing admin has sent the following broadcast message:</p> <p><BroadcastMessage></p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Listing broadcast message', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_inquiry', 'Inquiry about your Business Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has sent a message about your Business Listing, <b><a href="<ListUrl>"><ListTitle></a></b>:<br><br>Name: <Name><br>Company Name: <CompanyName><br>Industry: <Industry><br>Number of Employees: <Employees><br>Email: <Email><br>Phone Number: <Phone><br>Cellphone: <Cellphone></p><pre><Message></pre>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Business Listing Inquiry', '0');
  

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_claim', '<NickName> is claiming ownership of a Business Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has claimed ownership of a Business Listing, <b><a href="<ListUrl>"><ListTitle></a></b>:</p><pre><Message></pre>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Business Listing Claim', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_listing_invitation', 'Check out this Business: <ListingName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to check out this business listing:</p> <pre><InvitationText></pre> <p> <b>Business Information:</b><br /> Name: <ListingName><br /> Location: <ListingLocation><br /> <a href="<ListingUrl>">More details</a> </p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Business listing promotion template', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_claim_assign', 'Your Claim on a Business Listing at <SiteName> is updated', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>A Business Listing, <a href="<ListLink>"><ListTitle></a> that you claimed at <a href="<SiteUrl>"><SiteName></a> has been assigned to you. You now have ownership and administrative rights to the listing</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Claimed Business Listing assignment notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_expired', 'Your Business Listing at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Business Listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Expired Business Listing notification', '0');

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_post_expired', 'Message about your expired Business Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Business Listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired <b><Days></b> days ago</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Post-Expired Business Listing notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_expiring', 'Message about your expiring Business Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Business Listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> will expire in <b><Days></b> days<br></p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Expiring Business Listing notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_featured_expire_notify', 'Your Featured Listing Status at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is inform you that your Featured Status for the Business Listing, <a href="<ListLink>"><ListTitle></a> at <SiteName> has expired. You may purchase Featured Status again at any time you desire <br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>---</p>\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Listing Status Expire Notification', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_featured_admin_notify', 'A member purchased Featured Listing Status at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear Administrator</b>,</p>\r\n\r\n<p><a href="<NickLink>"><NickName></a> has just purchased Featured Status for the Business Listing, <a href="<ListLink>"><ListTitle></a>, for <Days> days at <SiteName><br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>---</p>\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Listing Purchase Admin Notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_listing_featured_buyer_notify', 'Your Featured Listing Status purchase at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is confirmation of your Featured Status purchase at <SiteName> for Business Listing, <a href="<ListLink>"><ListTitle></a>. It will be Featured for <Days> days<br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>---</p>\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Listing Purchase Buyer Notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_listing_fan_remove', 'You were removed from fans of a Business Listing', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>You were removed from fans of <a href="<EntryUrl>"><EntryTitle></a> listing by listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'User was removed from fans of Business Listing notification message', '0'),
('modzzz_listing_fan_become_admin', 'You became admin of a Business Listing', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Congratulations! You become admin of <a href="<EntryUrl>"><EntryTitle></a> listing.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'User become admin of a Business Listing notification message', '0'),
('modzzz_listing_admin_become_fan', 'Your Business Listing admin status was removed', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Your admin status was removed from <a href="<EntryUrl>"><EntryTitle></a> listing by listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'User Business Listing admin status was removed notification message', '0'),
('modzzz_listing_join_request', 'New join request to your listing', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>New join request in your listing <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'New join request to a listing notification message', '0'),
('modzzz_listing_join_reject', 'Your join request to a listing was rejected', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Sorry, but your request to join <a href="<EntryUrl>"><EntryTitle></a> listing was rejected by listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Join request to a listing was rejected notification message', '0'),
('modzzz_listing_join_confirm', 'Your join request to a listing was confirmed', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Congratulations! Your request to join <a href="<EntryUrl>"><EntryTitle></a> listing was confirmed by listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Join request to a listing was confirmed notification message', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_listing_employee_remove', 'You were removed as an employee of a Business Listing', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>You were removed from employees of <a href="<EntryUrl>"><EntryTitle></a> listing by listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'User was removed from employees of Business Listing notification message', '0'),
('modzzz_listing_employee_become_admin', 'You became admin of a Business Listing', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Congratulations! You become admin of <a href="<EntryUrl>"><EntryTitle></a> listing.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'User become admin of a Business Listing notification message', '0'),
('modzzz_listing_admin_become_employee', 'Your Business Listing admin status was removed', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Your admin status was removed from <a href="<EntryUrl>"><EntryTitle></a> listing by listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'User Business Listing admin status was removed notification message', '0'),
('modzzz_listing_work_request', 'New employee request to your listing', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>New employee request in your listing <a href="<EntryUrl>"><EntryTitle></a>. Please review this request and reject or confirm it.</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'New employee request to a listing notification message', '0'),
('modzzz_listing_work_reject', 'Your employee request to a listing was rejected', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Sorry, but your indication that you are an employee of <a href="<EntryUrl>"><EntryTitle></a> listing was rejected by the listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Employee request to a listing was rejected notification message', '0'),
('modzzz_listing_work_confirm', 'Your employee request to a listing was confirmed', '<bx_include_auto:_email_header.html />\r\n\r\n<p>Hello <NickName>,</p> <p>Congratulations! Your indication that you are an employee of <a href="<EntryUrl>"><EntryTitle></a> listing was confirmed by the listing admin(s).</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Employee request to a listing was confirmed notification message', '0');
 
INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxListing', '*/1 * * * *', 'BxListingCron', 'modules/modzzz/listing/classes/BxListingCron.php', '') ;
 


-- [begin] review
CREATE TABLE IF NOT EXISTS `[db_prefix]review_main` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `listing_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `pros` text NOT NULL,
  `cons` text NOT NULL,
  `quality` int(11) NOT NULL,
  `features` int(11) NOT NULL,
  `moneyvalue` int(11) NOT NULL,
  `customer_support` int(11) NOT NULL, 
  `status` enum('approved','pending','draft') NOT NULL default 'approved',
  `thumb` int(11) NOT NULL,
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
  `created` int(11) NOT NULL default '0',  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_cmts` (
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
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]review_views_tk` (
  `id` int(10) unsigned NOT NULL,
  `viewer` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `ts` int(10) unsigned NOT NULL,
  KEY `id` (`id`,`viewer`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_listing_review', 'modzzz_listing_review_rating', 'modzzz_listing_review_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_listing_review_main', 'rate', 'rate_count', 'id', 'BxListingReviewVoting', 'modules/modzzz/listing/classes/BxListingReviewVoting.php');
 
 
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_listing_review', '[db_prefix]review_cmts', '[db_prefix]review_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]review_main', 'id', 'comments_count', 'BxListingReviewCmts', 'modules/modzzz/listing/classes/BxListingReviewCmts.php');
  
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_listing_review', 'modzzz_listing_review_views_tk', 86400, 'modzzz_listing_review_main', 'id', 'views', 1);
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/edit/{ID}'';', '0', 'modzzz_listing_review'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/delete/{ID}'';', '1', 'modzzz_listing_review'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/review/{URI}', '', '', '2', 'modzzz_listing_review'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/review/{URI}', '', '', '4', 'modzzz_listing_review'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/review/{URI}', '', '', '5', 'modzzz_listing_review'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/review/{URI}', '', '', '6', 'modzzz_listing_review'),
    ('{TitlePostReview}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''review/add/{ID}'';', '18', 'modzzz_listing');
  
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing post reviews', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);
 
UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=listing/review/add/|modules/?r=listing/review/edit/|modules/?r=listing/review/view/|modules/?r=listing/review/browse/') WHERE `Parent`=0 AND `Name`='Listing' AND `Type`='system' AND `Caption`='_modzzz_listing_menu_root';
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_reviews_browse', 'Listing Reviews Browse', @iMaxOrder+11);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_reviews_view', 'Listing Reviews View', @iMaxOrder+12);
  
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
 
    ('modzzz_listing_reviews_view', '1140px', 'Listing Review''s description block', '_modzzz_listing_block_details', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_listing_reviews_view', '1140px', 'Listing Review''s comments block', '_modzzz_listing_block_comments', '2', '6', 'Comments', '', '1', '71.9', 'non,memb', '0'),   

    ('modzzz_listing_reviews_browse', '1140px', 'Listing Review''s browse block', '_modzzz_listing_block_browse_reviews', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_listing_reviews_view', '1140px', 'Listing Review''s actions block', '_modzzz_listing_block_actions', '3', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_listing_reviews_view', '1140px', 'Listing Review''s rate block', '_modzzz_listing_block_rate', '3', '1', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_listing_reviews_view', '1140px', 'Listing Review''s info block', '_modzzz_listing_block_info', '3', '2', 'Info', '', '1', '28.1', 'non,memb', '0');  

-- [end] review


-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('modzzz_listing', '_modzzz_listing', '0.8', 'auto', 'BxListingSiteMaps', 'modules/modzzz/listing/classes/BxListingSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('modzzz_listing', '_modzzz_listing', 'modzzz_listing_main', 'created', '', '', 1, @iMaxOrderCharts);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_listing_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''listing'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);


 CREATE TABLE IF NOT EXISTS `[db_prefix]employees` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  `confirmed` tinyint(4) UNSIGNED NOT NULL default '0',
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 


-- BEGIN EVENTS ADDING
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
 	('modzzz_listing_view', '1140px', 'Listing''s events block', '_modzzz_listing_block_events', '0', '0', 'Events', '', '1', '71.9', 'non,memb', '0');  
 
SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Caption`='_modzzz_listing_menu_root' AND `Type`='system' AND `Active`=1 LIMIT 1);
 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Listing View Events', '_modzzz_listing_menu_view_events', 'modules/?r=listing/event/browse/{modzzz_listing_view_uri}', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''); 
 
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `listing_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `event_start` int(11) NOT NULL default '0',
  `event_end` int(11) NOT NULL default '0',
  `country` varchar(2) NOT NULL default 'US',
  `city` varchar(50) NOT NULL default '',
  `state` varchar(10) NOT NULL default '',
  `address1` varchar(100) NOT NULL default '',
  `zip` varchar(100) NOT NULL default '', 
  `place` varchar(255) NOT NULL default '',
  `website` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `telephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `fax` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',   
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3', 
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_videos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_sounds_to` varchar(16) NOT NULL default 'a',
  `allow_upload_files_to` varchar(16) NOT NULL default 'a',
  PRIMARY KEY (`id`),
  UNIQUE KEY `listing_event_uri` (`uri`),
  KEY `listing_event_author_id` (`author_id`),
  KEY `listing_event_created` (`created`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;  
 
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 
    
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_cmts` (
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
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_event_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 

INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_listing_event', 'modzzz_listing_event_rating', 'modzzz_listing_event_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_listing_event_main', 'rate', 'rate_count', 'id', 'BxListingEventVoting', 'modules/modzzz/listing/classes/BxListingEventVoting.php');
  
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_listing_event', 'modzzz_listing_event_cmts', 'modzzz_listing_event_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_listing_event_main', 'id', 'comments_count', 'BxListingEventCmts', 'modules/modzzz/listing/classes/BxListingEventCmts.php');
 
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/edit/{ID}'';', '0', 'modzzz_listing_event'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/delete/{ID}'';', '1', 'modzzz_listing_event'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/event/{URI}', '', '', '2', 'modzzz_listing_event'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/event/{URI}', '', '', '3', 'modzzz_listing_event'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/event/{URI}', '', '', '4', 'modzzz_listing_event'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/event/{URI}', '', '', '5', 'modzzz_listing_event');
 
 
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_events_view', 'Listing Event View', @iMaxOrder + 1); 
  
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_events_browse', 'Listing Event Browse', @iMaxOrder + 2);

 
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
    ('modzzz_listing_events_browse', '1140px', 'Listing Event''s browse block', '_modzzz_listing_block_browse_events', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

    ('modzzz_listing_events_view', '1140px', 'Listing Event''s actions block', '_modzzz_listing_block_actions', '3', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s info block', '_modzzz_listing_block_info', '3', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s rate block', '_modzzz_listing_block_rate', '3', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s location block', '_modzzz_listing_block_event_location', '3', '4', 'Location', '', '1', 28.1, 'non,memb', '0'),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s map view', '_modzzz_listing_block_map_view', 3, 5, 'PHP', 'return BxDolService::call(''wmap'', ''location_block'', array(''listing_event'', $this->aDataEntry[$this->_oDb->_sFieldId]));', 1, 28.1, 'non,memb', 0),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s social sharing block', '_sys_block_title_social_sharing', 3, 6, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 

    ('modzzz_listing_events_view', '1140px', 'Listing Event''s description block', '_modzzz_listing_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s media block', '_modzzz_listing_block_media', '1', '3', 'Media', '', '1', 71.9, 'non,memb', '0'), 

    ('modzzz_listing_events_view', '1140px', 'Listing Event''s photos block', '_modzzz_listing_block_photo', '0', '0', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s video block', '_modzzz_listing_block_video', '0', '0', 'Videos', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s files block', '_modzzz_listing_block_file', '0', '0', 'Files', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s sounds block', '_modzzz_listing_block_sound', '0', '0', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_listing_events_view', '1140px', 'Listing Event''s comments block', '_modzzz_listing_block_comments', '2', '4', 'Comments', '', '1', '71.9', 'non,memb', '0');    
  
 

INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleEventAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''event/add/{ID}'';', '15', 'modzzz_listing'); 
 


UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=listing/event/add/|modules/?r=listing/event/edit/|modules/?r=listing/event/view/|modules/?r=listing/event/browse/') WHERE `Parent`=0 AND `Name`='Listing' AND `Type`='system' AND `Caption`='_modzzz_listing_menu_root';
 

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_listing_map_event_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''listing'', ''map_event_install'');'); 
 

SET @iHandler := LAST_INSERT_ID();

INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

 

 
-- BEGIN NEWS ADDING

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
 	('modzzz_listing_view', '1140px', 'Listing''s news block', '_modzzz_listing_block_news', '0', '0', 'News', '', '1', '71.9', 'non,memb', '0');  
 

SET @iCatRoot = (SELECT `ID` FROM `sys_menu_top` WHERE `Parent`=0 AND `Caption`='_modzzz_listing_menu_root' AND `Type`='system' AND `Active`=1 LIMIT 1);

 
INSERT INTO `sys_menu_top`(`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Listing View News', '_modzzz_listing_menu_view_news', 'modules/?r=listing/news/browse/{modzzz_listing_view_uri}', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
  

CREATE TABLE IF NOT EXISTS `modzzz_listing_news_main`  (
  `id` int(10) unsigned NOT NULL auto_increment,
  `listing_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `status` enum('approved','pending') NOT NULL default 'approved',
  `country` varchar(2) NOT NULL default 'US',
  `city` varchar(50) NOT NULL default '',
  `state` varchar(10) NOT NULL default '',
  `address1` varchar(100) NOT NULL default '',
  `zip` varchar(100) NOT NULL default '', 
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `allow_view_to` varchar(16) NOT NULL default '3', 
  `allow_comment_to` varchar(16) NOT NULL,
  `allow_rate_to` varchar(16) NOT NULL, 
  `allow_upload_photos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_videos_to` varchar(16) NOT NULL default 'a',
  `allow_upload_sounds_to` varchar(16) NOT NULL default 'a',
  `allow_upload_files_to` varchar(16) NOT NULL default 'a',
  PRIMARY KEY (`id`),
  UNIQUE KEY `listing_news_uri` (`uri`),
  KEY `listing_news_author_id` (`author_id`),
  KEY `listing_news_created` (`created`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;  
 
 
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_images` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
 
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_videos` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_sounds` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_files` (
  `entry_id` int(10) unsigned NOT NULL,
  `media_id` int(11) NOT NULL,
  UNIQUE KEY `entry_id` (`entry_id`,`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_rating` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_rating_count` int( 11 ) NOT NULL default '0',
  `gal_rating_sum` int( 11 ) NOT NULL default '0',
  UNIQUE KEY `gal_id` (`gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
   
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_rating_track` (
  `gal_id` smallint( 6 ) NOT NULL default '0',
  `gal_ip` varchar( 20 ) default NULL,
  `gal_date` datetime default NULL,
  KEY `gal_ip` (`gal_ip`, `gal_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_cmts` (
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
  
CREATE TABLE IF NOT EXISTS `modzzz_listing_news_cmts_track` (
  `cmt_system_id` int( 11 ) NOT NULL default '0',
  `cmt_id` int( 11 ) NOT NULL default '0',
  `cmt_rate` tinyint( 4 ) NOT NULL default '0',
  `cmt_rate_author_id` int( 10 ) unsigned NOT NULL default '0',
  `cmt_rate_author_nip` int( 11 ) unsigned NOT NULL default '0',
  `cmt_rate_ts` int( 11 ) NOT NULL default '0',
  PRIMARY KEY (`cmt_system_id` , `cmt_id` , `cmt_rate_author_nip`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;
 

INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_listing_news', 'modzzz_listing_news_rating', 'modzzz_listing_news_rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', 'modzzz_listing_news_main', 'rate', 'rate_count', 'id', 'BxListingNewsVoting', 'modules/modzzz/listing/classes/BxListingNewsVoting.php');
 
    
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_listing_news', 'modzzz_listing_news_cmts', 'modzzz_listing_news_cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', 'modzzz_listing_news_main', 'id', 'comments_count', 'BxListingNewsCmts', 'modules/modzzz/listing/classes/BxListingNewsCmts.php');
 
  
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/edit/{ID}'';', '0', 'modzzz_listing_news'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/delete/{ID}'';', '1', 'modzzz_listing_news'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos_subprofile/news/{URI}', '', '', '2', 'modzzz_listing_news'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos_subprofile/news/{URI}', '', '', '3', 'modzzz_listing_news'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds_subprofile/news/{URI}', '', '', '4', 'modzzz_listing_news'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files_subprofile/news/{URI}', '', '', '5', 'modzzz_listing_news');
 
 
 
SET @iMaxOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);

INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_news_view', 'Listing News View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_listing_news_browse', 'Listing News Browse', @iMaxOrder+2);

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 
    
('modzzz_listing_news_browse', '1140px', 'Listing News''s browse block', '_modzzz_listing_block_browse_news', '1', '0', 'Browse', '', '1', '100', 'non,memb', '0'),

('modzzz_listing_news_view', '1140px', 'Listing News''s actions block', '_modzzz_listing_block_actions', '3', '0', 'Actions', '', '1', '28.1', 'non,memb', '0'),
('modzzz_listing_news_view', '1140px', 'Listing News''s info block', '_modzzz_listing_block_info', '3', '1', 'Info', '', '1', '28.1', 'non,memb', '0'),
('modzzz_listing_news_view', '1140px', 'Listing News''s rate block', '_modzzz_listing_block_rate', '3', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),    
('modzzz_listing_news_view', '1140px', 'Listing Event''s social sharing block', '_sys_block_title_social_sharing', 3, 6, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 

('modzzz_listing_news_view', '1140px', 'Listing News''s description block', '_modzzz_listing_block_desc', '2', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
('modzzz_listing_news_view', '1140px', 'Listing News''s media block', '_modzzz_listing_block_media', '1', '3', 'Media', '', '1', 71.9, 'non,memb', '0'), 
('modzzz_listing_news_view', '1140px', 'Listing News''s photos block', '_modzzz_listing_block_photo', '0', '0', 'Photos', '', '1', '71.9', 'non,memb', '0'), 
('modzzz_listing_news_view', '1140px', 'Listing News''s video block', '_modzzz_listing_block_video', '0', '0', 'Videos', '', '1', '71.9', 'non,memb', '0'),
('modzzz_listing_news_view', '1140px', 'Listing News''s files block', '_modzzz_listing_block_file', '0', '0', 'Files', '', '1', '71.9', 'non,memb', '0'),
('modzzz_listing_news_view', '1140px', 'Listing News''s sounds block', '_modzzz_listing_block_sound', '0', '0', 'Sounds', '', '1', '71.9', 'non,memb', '0'),
('modzzz_listing_news_view', '1140px', 'Listing News''s comments block', '_modzzz_listing_block_comments', '2', '5', 'Comments', '', '1', '71.9', 'non,memb', '0');    
 
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES  
    ('{TitleNewsAdd}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxListingModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''news/add/{ID}'';', '16', 'modzzz_listing'); 
 

UPDATE `sys_menu_top` SET `Link` = CONCAT(`Link`, '|modules/?r=listing/news/add/|modules/?r=listing/news/edit/|modules/?r=listing/news/view/|modules/?r=listing/news/browse/') WHERE `Parent`=0 AND `Name`='Listing' AND `Type`='system' AND `Caption`='_modzzz_listing_menu_root';
 
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'listing allow embed', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

 
CREATE TABLE IF NOT EXISTS `[db_prefix]operation` ( 
  `id` int(10) unsigned NOT NULL auto_increment,
  `listing_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `from_hour` int(11) NOT NULL,
  `from_minute` varchar(10) collate utf8_general_ci NOT NULL,
  `from_period` varchar(100) collate utf8_general_ci NOT NULL,
  `to_hour` int(11) NOT NULL,
  `to_minute` varchar(10) collate utf8_general_ci NOT NULL,
  `to_period` varchar(100) collate utf8_general_ci NOT NULL, 
  `created` int(11) NOT NULL, 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
 