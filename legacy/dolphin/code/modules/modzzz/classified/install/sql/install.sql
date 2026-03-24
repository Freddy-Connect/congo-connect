ALTER TABLE `sys_menu_top` CHANGE `Link` `Link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

-- create tables 

CREATE TABLE IF NOT EXISTS `[db_prefix]favorites` (
  `id_entry` int(10) unsigned NOT NULL,
  `id_profile` int(10) unsigned NOT NULL,
  `when` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_entry`, `id_profile`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `[db_prefix]youtube` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]promo` (
  `id` int(11) NOT NULL auto_increment,
  `details` text NOT NULL, 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci; 

INSERT INTO  `[db_prefix]promo` (`id`, `details`) VALUES (1, '');

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
  `classified_id` int(11) unsigned NOT NULL,
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
 
CREATE TABLE IF NOT EXISTS `[db_prefix]offers` (
  `id` int(11) unsigned NOT NULL auto_increment, 
  `classified_id` int(11) unsigned NOT NULL,
  `buyer_id` int(11) unsigned NOT NULL,
  `offer_status` ENUM( 'accepted', 'pending' ) NOT NULL DEFAULT 'pending',
  `offer_date` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]main` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(100) NOT NULL default '',
  `uri` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `author_name` varchar(150) NOT NULL,
  `country` varchar(2) NOT NULL,
  `city` varchar(150) NOT NULL,
  `address1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `address2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `state` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '', 
  `zip` varchar(16) NOT NULL,
  `status` ENUM( 'approved', 'pending', 'salepending', 'sold', 'expired' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'approved',
  `thumb` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `author_id` int(10) unsigned NOT NULL default '0',
  `tags` varchar(255) NOT NULL default '',
  `currency` varchar(255) NOT NULL default '',
  `categories` text NOT NULL,
  `category_id` int(11) NOT NULL,  
  `price` float NOT NULL,
  `saleprice` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `why` text NOT NULL,
  `classified_type` varchar(150) NOT NULL default '',
  `payment_type` varchar(150) NOT NULL default '',
 
  `custom_field1` varchar(255) NOT NULL default '',
  `custom_field2` varchar(255) NOT NULL default '',
  `custom_field3` varchar(255) NOT NULL default '',
  `custom_field4` varchar(255) NOT NULL default '',
  `custom_field5` varchar(255) NOT NULL default '',
  `custom_field6` varchar(255) NOT NULL default '',
  `custom_field7` varchar(255) NOT NULL default '',
  `custom_field8` varchar(255) NOT NULL default '',
  `custom_field9` varchar(255) NOT NULL default '',
  `custom_field10` varchar(255) NOT NULL default '',

  `custom_sub_field1` varchar(255) NOT NULL default '',
  `custom_sub_field2` varchar(255) NOT NULL default '',
  `custom_sub_field3` varchar(255) NOT NULL default '',
  `custom_sub_field4` varchar(255) NOT NULL default '',
  `custom_sub_field5` varchar(255) NOT NULL default '',
  `custom_sub_field6` varchar(255) NOT NULL default '',
  `custom_sub_field7` varchar(255) NOT NULL default '',
  `custom_sub_field8` varchar(255) NOT NULL default '',
  `custom_sub_field9` varchar(255) NOT NULL default '',
  `custom_sub_field10` varchar(255) NOT NULL default '',
 
  `views` int(11) NOT NULL,
  `rate` float NOT NULL,
  `rate_count` int(11) NOT NULL,
  `comments_count` int(11) NOT NULL,
  `featured` tinyint(4) NOT NULL,
  `featured_expiry_date`  INT NOT NULL,
  `featured_date` INT NOT NULL, 
  `allow_post_in_forum_to` varchar(16) NOT NULL, 
  `allow_view_classified_to` int(11) NOT NULL,
  `allow_comment_to` varchar(16) NOT NULL, 
  `allow_rate_to` varchar(16) NOT NULL,   
  `allow_upload_photos_to` varchar(16) NOT NULL,
  `allow_upload_videos_to` varchar(16) NOT NULL,
  `allow_upload_sounds_to` varchar(16) NOT NULL,
  `allow_upload_files_to` varchar(16) NOT NULL,
  `membership_view_filter` varchar(100) NOT NULL default '', 
  `votes` int(11) NOT NULL default '0',    
  `pre_expire_notify` int(11) NOT NULL, 
  `post_expire_notify` int(11) NOT NULL,   
  `sellername` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `sellerwebsite` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `selleremail` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `sellertelephone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `sellerfax` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL default '',
  `allow_view_contact` varchar(100) COLLATE utf8_general_ci NOT NULL, 
  `expiry_date` int(11) NOT NULL default '0',
  `invoice_no` varchar(100) COLLATE utf8_general_ci NOT NULL,
  `video_embed` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  KEY `author_id` (`author_id`),
  KEY `created` (`created`),
  FULLTEXT KEY `search` (`title`,`desc`,`tags` ),
  FULLTEXT KEY `tags` (`tags`),
  FULLTEXT KEY `categories` (`categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
    
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
  `type` enum('rent','sale','other') NOT NULL default 'other', 
  `custom_field1` varchar(255) NOT NULL default '',
  `custom_field2` varchar(255) NOT NULL default '',
  `custom_field3` varchar(255) NOT NULL default '',
  `custom_field4` varchar(255) NOT NULL default '',
  `custom_field5` varchar(255) NOT NULL default '',
  `custom_field6` varchar(255) NOT NULL default '',
  `custom_field7` varchar(255) NOT NULL default '',
  `custom_field8` varchar(255) NOT NULL default '',
  `custom_field9` varchar(255) NOT NULL default '',
  `custom_field10` varchar(255) NOT NULL default '',

  `custom_sub_field1` varchar(255) NOT NULL default '',
  `custom_sub_field2` varchar(255) NOT NULL default '',
  `custom_sub_field3` varchar(255) NOT NULL default '',
  `custom_sub_field4` varchar(255) NOT NULL default '',
  `custom_sub_field5` varchar(255) NOT NULL default '',
  `custom_sub_field6` varchar(255) NOT NULL default '',
  `custom_sub_field7` varchar(255) NOT NULL default '',
  `custom_sub_field8` varchar(255) NOT NULL default '',
  `custom_sub_field9` varchar(255) NOT NULL default '',
  `custom_sub_field10` varchar(255) NOT NULL default '',
 
   PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


-- Dumping data for table `[db_prefix]categ`  
INSERT INTO `[db_prefix]categ` ( `parent`, `name`, `uri`, `type`, `icon`, `active`) VALUES
( 0, 'Community', 'Community', 'other', '', 1),
( 0, 'For Sale', 'Sale', 'sale', '', 1), 
( 0, 'Houses', 'Houses', 'other', '', 1),
( 0, 'Jobs', 'Jobs', 'other', '', 1),
( 0, 'Personals', 'Personals', 'other', '', 1), 
( 0, 'Rentals', 'Rentals', 'rent', '', 1),
( 0, 'Services', 'Services', 'other', '', 1),
( 0, 'Tickets', 'Tickets', 'other', '', 1),
( 0, 'Vehicles', 'Vehicles', 'other', '', 1),
   
( 1, 'Activities', 'Activities', 'other', '', 1),
( 1, 'Artists', 'Artists', 'other', '', 1),
( 1, 'Childcare', 'Childcare', 'other', '', 1),
( 1, 'General', 'General', 'other', '', 1),
( 1, 'Groups', 'Groups', 'other', '', 1),
( 1, 'Pets', 'Pets', 'other', '', 1),
( 1, 'Events', 'Events', 'other', '', 1), 
( 1, 'Lost & Found', 'Lost-Found', 'other', '', 1),
( 1, 'Musicians', 'Musicians', 'other', '', 1),
( 1, 'Local News', 'Local-News', 'other', '', 1),
( 1, 'Politics', 'Politics', 'other', '', 1),
( 1, 'Rideshare', 'Rideshare', 'other', '', 1),
( 1, 'Volunteers', 'Volunteers', 'other', '', 1),
( 1, 'Classes', 'Classes', 'other', '', 1),
  
( 2, 'Garage & Yard Sales', 'Garage-Yard-Sales', 'other', '', 1),              
( 2, 'Books', 'Books', 'other', '', 1),                                
( 2, 'Clothes & Accessories', 'Clothes', 'other', '', 1),            
( 2, 'Collectibles', 'Collectibles', 'other', '', 1),                         
( 2, 'Computers', 'Computers', 'other', '', 1),                            
( 2, 'Electronics', 'Electronics', 'other', '', 1),                          
( 2, 'Movies, Music & Video Games', 'Movies', 'other', '', 1),      
( 2, 'Free', 'Free', 'other', '', 1),                                 
( 2, 'Furniture', 'Furniture', 'other', '', 1),                            
( 2, 'Home & Garden', 'Home-Garden', 'other', '', 1),                    
( 2, 'Baby & Kid Stuff', 'Baby-Kid-Stuff', 'other', '', 1),                 
( 2, 'Musical Instruments', 'Musical', 'other', '', 1),                  
( 2, 'Office & Biz', 'Office', 'other', '', 1),                     
( 2, 'Sporting Goods & Bicycles', 'Sporting', 'other', '', 1),        
( 2, 'Crafts & Hobbies', 'Crafts', 'other', '', 1),                 
( 2, 'Everything Else', 'Other', 'other', '', 1),                      
( 2, 'Adult', 'Adult', 'other', '', 1),                                
( 2, 'Health & Beauty', 'Health', 'other', '', 1),                 
  
( 3, 'Commercial', 'Commercial', 'other', '', 1),          
( 3, 'Condos', 'Condos', 'other', '', 1),          
( 3, 'Farm/Ranch', 'Farm-Ranch', 'other', '', 1),          
( 3, 'Foreclosures', 'Foreclosures', 'other', '', 1),          
( 3, 'Homes', 'Homes', 'other', '', 1),          
( 3, 'Land', 'Land', 'other', '', 1),          
( 3, 'Mobile Homes', 'Mobile-Homes', 'other', '', 1),          
( 3, 'Multi Family', 'Multi-Family', 'other', '', 1),          
( 3, 'Open Houses', 'Open-Houses', 'other', '', 1),          
( 3, 'Storage', 'Storage', 'other', '', 1),          
( 3, 'Vacation Property', 'Vacation-Property', 'other', '', 1),          
( 3, 'Other', 'Other', 'other', '', 1), 

( 4, 'Accounting & Finance', 'Accounting-Finance', 'other', '', 1),              
( 4, 'Admin & Support Services', 'Admin-Support-Services', 'other', '', 1),          
( 4, 'Advertising, Marketing & PR', 'Advertising-Marketing-PR', 'other', '', 1),       
( 4, 'Architecture & Design', 'Architecture-Design', 'other', '', 1),             
( 4, 'Art, Media & Writer', 'Art-Media-Writer', 'other', '', 1),               
( 4, 'Civil Service & Public Policy', 'Civil-Service', 'other', '', 1),     
( 4, 'Construction & Skilled Labor', 'Construction', 'other', '', 1),      
( 4, 'Customer Service & Call Center', 'Customer-Service', 'other', '', 1),    
( 4, 'Domestic Help & Child Care', 'Domestic-Help', 'other', '', 1),        
( 4, 'Engineering & Product Development', 'Engineering-Product-Development', 'other', '', 1), 
( 4, 'Facilities & Maintenance', 'Facilities-Maintenance', 'other', '', 1),          
( 4, 'General Labor & Warehouse', 'General-Labor-Warehouse', 'other', '', 1),         
( 4, 'Healthcare & Nurse', 'Healthcare-Nurse', 'other', '', 1),                
( 4, 'Hospitality, Tourism & Travel', 'Hospitality-Tourism-Travel', 'other', '', 1),     
( 4, 'Human Resources & Recruiting', 'Human-Resources-Recruiting', 'other', '', 1),      
( 4, 'Law Enforcement & Security', 'Law-Enforcement-Security', 'other', '', 1),        
( 4, 'Legal', 'Legal', 'other', '', 1),                                 
( 4, 'Management & Executive', 'Management-Executive', 'other', '', 1),            
( 4, 'Non-Profit & Fundraising', 'Non-Profit-Fundraising', 'other', '', 1),          
( 4, 'Production & Operation', 'Production-Operation', 'other', '', 1),            
( 4, 'Quality Assurance & Control', 'Quality-Assurance-Control', 'other', '', 1),       
( 4, 'Real Estate', 'Real-Estate', 'other', '', 1),                           
( 4, 'Research & Development', 'Research', 'other', '', 1),            
( 4, 'Retail, Grocery & Wholesale', 'Retail', 'other', '', 1),      
( 4, 'Sales & Business Development', 'Sales', 'other', '', 1),      
( 4, 'Salon, Fitness & Spa', 'Salon', 'other', '', 1),              
( 4, 'Science, Pharma & Biotech', 'Science', 'other', '', 1),         
( 4, 'Social Services & Counseling', 'Counseling', 'other', '', 1),      
( 4, 'Teaching, Training & Library', 'Teaching', 'other', '', 1),      
( 4, 'Computer & Software', 'Computer', 'other', '', 1),               
( 4, 'Transportation & Logistics', 'Transportation', 'other', '', 1),        
( 4, 'Veterinary & Animal Care', 'Veterinary', 'other', '', 1),          
( 4, 'Work at Home & Self Employed', 'Self-Employed', 'other', '', 1),      
( 4, 'Everything Else', 'Other', 'other', '', 1),                       
( 4, 'TV, Film & Musician', 'TV-Film-Musician', 'other', '', 1),               
( 4, 'Oil, Gas & Solar Power', 'Fuel', 'other', '', 1),       
 
( 5, 'Women looking for Men', 'Women-looking-for-Men', 'other', '', 1),          
( 5, 'Men looking for Women', 'Men-looking-for-Women', 'other', '', 1),           
( 5, 'Men looking for Men', 'Men-looking- for-Men', 'other', '', 1),           
( 5, 'Women looking for Women', 'Women-looking-for-Women', 'other', '', 1),           
( 5, 'Friendship - Activity Partners', 'Friendship-Activity-Partners', 'other', '', 1),           
( 5, 'Missed Connections', 'Missed-Connections', 'other', '', 1), 
  
( 6, 'Apartments', 'Apartments', 'other', '', 1),          
( 6, 'Commercial', 'Commercial', 'other', '', 1),          
( 6, 'Condos', 'Condos', 'other', '', 1),          
( 6, 'Garages', 'Garages', 'other', '', 1),          
( 6, 'Homes', 'Homes', 'other', '', 1),          
( 6, 'Mobile Homes', 'Mobile-Homes', 'other', '', 1),          
( 6, 'Open Houses', 'Open-Houses', 'other', '', 1),          
( 6, 'Roommates', 'Roommates', 'other', '', 1),          
( 6, 'Short Term', 'Short-Term', 'other', '', 1),          
( 6, 'Storage', 'Storage', 'other', '', 1),          
( 6, 'Vacation', 'Vacation', 'other', '', 1),          
( 6, 'Other', 'Other', 'other', '', 1), 
 
( 7, 'Auto', 'Auto', 'other', '', 1),                                  
( 7, 'Career', 'Career', 'other', '', 1),                                 
( 7, 'Child & Elderly Care', 'Child-Elderly-Care', 'other', '', 1),                           
( 7, 'Cleaning', 'Cleaning', 'other', '', 1),                               
( 7, 'Coupons', 'Coupons', 'other', '', 1),                                
( 7, 'Financial', 'Financial', 'other', '', 1),                              
( 7, 'Health & Beauty', 'Health-Beauty', 'other', '', 1),                    
( 7, 'Home', 'Home', 'other', '', 1),                                   
( 7, 'Lawn & Garden', 'Lawn-Garden', 'other', '', 1),                      
( 7, 'Legal', 'Legal', 'other', '', 1),                                  
( 7, 'Lessons', 'Lessons', 'other', '', 1),                                
( 7, 'Moving & Storage', 'Moving-Storage', 'other', '', 1),                   
( 7, 'Party & Entertain', 'Party-Entertain', 'other', '', 1),                  
( 7, 'Pet Services', 'Pet-Services', 'other', '', 1),                           
( 7, 'Pool & Spa Services', 'Pool-pa Services', 'other', '', 1),                
( 7, 'Real Estate', 'Real-Estate', 'other', '', 1),                            
( 7, 'Food & Restaurants', 'Food-Restaurants', 'other', '', 1),                 
( 7, 'Tech Help', 'Tech-Help', 'other', '', 1),                              
( 7, 'Travel & Transportation', 'Travel-Transportation', 'other', '', 1),            
( 7, 'Everything Else', 'Everything-Else', 'other', '', 1),                        
( 7, 'Psychic', 'Psychic', 'other', '', 1),                                
( 7, 'Creative', 'Creative', 'other', '', 1),  
   
( 8, 'Concerts', 'Concerts', 'other', '', 1), 
( 8, 'Group Events', 'Group-Events', 'other', '', 1),       
( 8, 'Sports', 'Sports', 'other', '', 1), 
( 8, 'Theater', 'Theater', 'other', '', 1), 
( 8, 'Other', 'Other', 'other', '', 1),  
  
( 9, 'Airplanes', 'Airplanes', 'other', '', 1),
( 9, 'Boats', 'Boats', 'other', '', 1),
( 9, 'Cars', 'Cars', 'other', '', 1),
( 9, 'Commercial Trucks', 'Commercial Trucks', 'other', '', 1),
( 9, 'Heavy Equipment', 'Heavy Equipment', 'other', '', 1),
( 9, 'Motorcycles', 'Motorcycles', 'other', '', 1),
( 9, 'RVs', 'RVs', 'other', '', 1),
( 9, 'Parts & Accessories', 'Parts-Accessories', 'other', '', 1),
( 9, 'Power Sports', 'Power-Sports', 'other', '', 1),
( 9, 'Everything Else', 'Everything-Else', 'other', '', 1) 
;  

UPDATE `[db_prefix]categ` SET `uri`=REPLACE(`uri`,'''','');

  

-- create forum tables

CREATE TABLE IF NOT EXISTS `[db_prefix]forum` (
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
 
CREATE TABLE IF NOT EXISTS `[db_prefix]forum_cat` (
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
(1, 'Classified', 'Classified', '', 64);

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_flag` (
  `user` varchar(32) NOT NULL default '',
  `topic_id` int(11) NOT NULL default '0',
  `when` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
 
CREATE TABLE IF NOT EXISTS `[db_prefix]forum_post` (
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


CREATE TABLE IF NOT EXISTS `[db_prefix]forum_topic` (
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

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_user` (
  `user_name` varchar(32) NOT NULL default '',
  `user_pwd` varchar(32) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_join_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `[db_prefix]forum_user_activity` (
  `user` varchar(32) NOT NULL default '',
  `act_current` int(11) NOT NULL default '0',
  `act_last` int(11) NOT NULL default '0',
  PRIMARY KEY  (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_user_stat` (
  `user` varchar(32) NOT NULL default '',
  `posts` int(11) NOT NULL default '0',
  `user_last_post` int(11) NOT NULL default '0',
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
CREATE TABLE IF NOT EXISTS `[db_prefix]forum_vote` (
  `user_name` varchar(32) NOT NULL default '',
  `post_id` int(11) NOT NULL default '0',
  `vote_when` int(11) NOT NULL default '0',
  `vote_point` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`user_name`,`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_actions_log` (
  `user_name` varchar(32) NOT NULL default '',
  `id` int(11) NOT NULL default '0',
  `action_name` varchar(32) NOT NULL default '',
  `action_when` int(11) NOT NULL default '0',
  KEY `action_when` (`action_when`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[db_prefix]forum_attachments` (
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
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_view', 'Classified View', @iMaxOrder+1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_celendar', 'Classified Calendar', @iMaxOrder+2);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_main', 'Classified Home', @iMaxOrder+3);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_my', 'Classified My', @iMaxOrder+4);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_category', 'Classified Category', @iMaxOrder+6);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_subcategory', 'Classified Sub-Category', @iMaxOrder+7); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_local', 'Local Classified Page', @iMaxOrder+8); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_local_state', 'Local Classified State Page', @iMaxOrder+9); 
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`) VALUES ('modzzz_classified_packages', 'Classified Packages', @iMaxOrder+10); 
 
 

-- page compose blocks
INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`) VALUES 

('modzzz_classified_view', '1140px', 'Classified''s manage block', '_modzzz_classified_block_actions_user', '2', '0', 'ManagePageUser', '', '1', '28.1', 'non,memb', '0'), 

('modzzz_classified_view', '1140px', 'Classified''s manage block', '_modzzz_classified_block_actions', '2', '0', 'ManageMyClassified', '', '1', '28.1', 'non,memb', '0'),   

    ('modzzz_classified_view', '1140px', 'Classified''s description block', '_modzzz_classified_block_desc', '1', '0', 'Desc', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_view', '1140px', 'Classified''s photo block', '_modzzz_classified_block_photo', '1', '3', 'Photo', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_view', '1140px', 'Classified''s Video Embed block', '_modzzz_classified_block_video_embed', '1', '4', 'VideoEmbed', '', '1', '71.9', 'non,memb', '0'), 
    ('modzzz_classified_view', '1140px', 'Classified''s videos block', '_modzzz_classified_block_video', '1', '5', 'Video', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_classified_view', '1140px', 'Classified''s sounds block', '_modzzz_classified_block_sound', '1', '6', 'Sound', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_classified_view', '1140px', 'Classified''s files block', '_modzzz_classified_block_files', '1', '7', 'Files', '', '1', '71.9', 'non,memb', '0'),    
    ('modzzz_classified_view', '1140px', 'Classified''s local block', '_modzzz_classified_block_local', '1', '8', 'Local', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_view', '1140px', 'Classified''s other block', '_modzzz_classified_block_other', '1', '9', 'Other', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_view', '1140px', 'Classified''s comments block', '_modzzz_classified_block_comments', '1', '10', 'Comments', '', '1', '71.9', 'non,memb', '0'),

    ('modzzz_classified_view', '1140px', 'Classified''s actions block', '_modzzz_classified_block_actions', '2', '1', 'Actions', '', '1', '28.1', 'non,memb', '0'),  
    ('modzzz_classified_view', '1140px', 'Classified''s rate block', '_modzzz_classified_block_rate', '2', '2', 'Rate', '', '1', '28.1', 'non,memb', '0'),  
    ('modzzz_classified_view', '1140px', 'Classified''s social sharing block', '_sys_block_title_social_sharing', 2, 3, 'SocialSharing', '', 1, 28.1, 'non,memb', 0), 
    ('modzzz_classified_view', '1140px', 'Classified''s info block', '_modzzz_classified_block_info', '2', '4', 'Info', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_classified_view', '1140px', 'Classified''s contact block', '_modzzz_classified_contact', '2', '5', 'Contact', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_classified_view', '1140px', 'Classified''s additional block', '_modzzz_classified_block_additional', '2', '6', 'Additional', '', '1', '28.1', 'non,memb', '0'),    
    ('modzzz_classified_view', '1140px', 'Classified''s location block', '_modzzz_classified_block_location', '2', '7', 'Location', '', '1', '28.1', 'non,memb', '0'),  
    ('modzzz_classified_view', '1140px', 'Classified''s map block', '_modzzz_classified_block_map', '2', '8', 'Map', '', '1', '28.1', 'non,memb', '0'),     

    ('modzzz_classified_local_state', '1140px', 'Local State Classifieds', '_modzzz_classified_block_browse_state_classifieds', '1', '0', 'StateClassifieds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_local_state', '1140px', 'Local States', '_modzzz_classified_block_browse_state', '2', '0', 'States', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_classified_local_state', '1140px', 'Classified Categories Drilldown', '_modzzz_classified_block_browse_categories_drilldown', '2', '1', 'Categories', '', '1', '28.1', 'non,memb', '0'), 

    ('modzzz_classified_local', '1140px', 'Local Classifieds', '_modzzz_classified_block_browse_country', '1', '0', 'Region', '', '1', '100', 'non,memb', '0'),  
 
    ('modzzz_classified_category', '1140px', 'Classified', '_modzzz_classified_block_classified', '1', '0', 'CategoryClassified', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_category', '1140px', 'Classified Categories', '_modzzz_classified_block_categories', '2', '0', 'Categories', '', '1', '28.1', 'non,memb', '0'),
     
    ('modzzz_classified_subcategory', '1140px', 'Classified Category Classifieds', '_modzzz_classified_block_category_classified', '1', '0', 'SubCategoryClassifieds', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_subcategory', '1140px', 'Classified Sub-Categories', '_modzzz_classified_block_subcategories', '2', '0', 'SubCategories', '', '1', '28.1', 'non,memb', '0'),

    ('modzzz_classified_main', '1140px', 'Map', '_Map', '1', '0', 'PHP', 'return BxDolService::call(''wmap'', ''homepage_part_block'', array (''classified''));', 1, 71.9, 'non,memb', 0),
    ('modzzz_classified_main', '1140px', 'Latest Featured Classified', '_modzzz_classified_block_latest_featured_classified', '1', '1', 'LatestFeaturedClassified', '', '1', '71.9', 'non,memb', '0'),
	
	
	('modzzz_classified_main', '1140px', 'Block Gesttion des annonces ', '_modzzz_classified_gestion_annonces', '2', '4', 'Annonces', '', '1', '71.9', 'non,memb', '0'),
	
    ('modzzz_classified_main', '1140px', 'Recent Classified', '_modzzz_classified_block_recent', '1', '2', 'Recent', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_main', '1140px', 'Classified Forum Posts', '_modzzz_classified_block_forum', '1', '3', 'Forum', '', '1', '71.9', 'non,memb', '0'),
    ('modzzz_classified_main', '1140px', 'Classified Comments', '_modzzz_classified_block_latest_comments', '1', '4', 'Comments', '', '1', '71.9', 'non,memb', '0'), 
 
    ('modzzz_classified_main', '1140px', 'Classified Create', '_modzzz_classified_block_classified_create', '2', '0', 'Create', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_classified_main', '1140px', 'Search Classified', '_modzzz_classified_block_search', '2', '1', 'Search', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_classified_main', '1140px', 'Classified Categories', '_modzzz_classified_block_classified_categories', '2', '2', 'ClassifiedCategories', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_classified_main', '1140px', 'Classified States', '_modzzz_classified_block_classified_states', '2', '5', 'States', '', '1', '28.1', 'non,memb', '0'), 
    ('modzzz_classified_main', '1140px', 'Classified Calendar', '_modzzz_classified_block_calendar', '2', '4', 'Calendar', '', '1', '28.1', 'non,memb', '0'),
    ('modzzz_classified_main', '1140px', 'Classified Tags', '_tags_plural', '2', '5', 'Tags', '', '1', '28.1', 'non,memb', '0'), 
 
    ('modzzz_classified_packages', '1140px', 'Classified Packages', '_modzzz_classified_block_packages', '1', '0', 'Packages', '', '1', '100', 'non,memb', '0'),  

    ('modzzz_classified_my', '1140px', 'Administration Owner', '_modzzz_classified_block_administration_owner', '1', '0', 'Owner', '', '1', '100', 'non,memb', '0'),
    ('modzzz_classified_my', '1140px', 'User''s classified', '_modzzz_classified_block_users_classified', '1', '1', 'Browse', '', '0', '100', 'non,memb', '0'),
    ('index', '1140px', 'Classifieds', '_modzzz_classified_block_homepage', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''classified'', ''index_block'');', 1, 71.9, 'non,memb', 0),
    ('member', '1140px', 'My Classifieds', '_modzzz_classified_block_my_classifieds', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''classified'', ''accountpage_block'');', 1, 71.9, 'non,memb', 0),  
    ('member', '1140px', 'Local Classifieds', '_modzzz_classified_block_area_classifieds', 0, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''classified'', ''area_block'');', 1, 71.9, 'non,memb', 0),  
    ('profile', '1140px', 'User Classifieds', '_modzzz_classified_block_my_classified', 3, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''classified'', ''profile_block'', array($this->oProfileGen->_iProfileID));', 1, 71.9, 'non,memb', 0);
 
 
-- permalinkU
INSERT INTO `sys_permalinks` VALUES (NULL, 'modules/?r=classified/', 'm/classified/', 'modzzz_classified_permalinks');

-- settings
SET @iMaxOrder = (SELECT `menu_order` + 1 FROM `sys_options_cats` ORDER BY `menu_order` DESC LIMIT 1);
INSERT INTO `sys_options_cats` (`name`, `menu_order`) VALUES ('Classified', @iMaxOrder);
SET @iCategId = (SELECT LAST_INSERT_ID());
INSERT INTO `sys_options` (`Name`, `VALUE`, `kateg`, `desc`, `Type`, `check`, `err_text`, `order_in_kateg`, `AvailableValues`) VALUES
('modzzz_classified_permalinks', 'on', 26, 'Enable friendly permalinks in classified', 'checkbox', '', '', '0', ''),
('modzzz_classified_autoapproval', 'on', @iCategId, 'Activate all classified after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_classified_author_comments_admin', 'on', @iCategId, 'Allow classified admin to edit and delete any comment', 'checkbox', '', '', '0', ''),
('category_auto_app_modzzz_classified', 'on', @iCategId, 'Activate all categories after creation automatically', 'checkbox', '', '', '0', ''),
('modzzz_classified_perpage_main_recent', '10', @iCategId, 'Number of recently added classifieds to show on classified home', 'digit', '', '', '0', ''),
('modzzz_classified_perpage_main_featured', '5', @iCategId, 'Number of featured classifieds to show on classified home', 'digit', '', '', '0', ''),
('modzzz_classified_perpage_browse', '14', @iCategId, 'Number of classified to show on browse pages', 'digit', '', '', '0', ''),
('modzzz_classified_perpage_profile', '4', @iCategId, 'Number of classified to show on profile page', 'digit', '', '', '0', ''),
('modzzz_classified_perpage_accountpage', '4', @iCategId, 'Number of classified to show on account page', 'digit', '', '', '0', ''),
('modzzz_classified_perpage_homepage', '5', @iCategId, 'Number of classified to show on homepage', 'digit', '', '', '0', ''),
('modzzz_classified_homepage_default_tab', 'featured', @iCategId, 'Default classified block tab on homepage', 'select', '', '', '0', 'featured,recent,top,popular'),
('modzzz_classified_max_preview', '300', @iCategId, 'Length of classified description snippet to show in blocks', 'digit', '', '', '0', ''),
('modzzz_classified_max_rss_num', '10', @iCategId, 'Max number of rss items to provide', 'digit', '', '', '0', ''), 
('modzzz_classified_free_expired', '0', @iCategId, 'number of days before free classified expires', 'digit', '', '', '0', ''), 
('modzzz_classified_activate_expiring', 'on', @iCategId, 'activate sending email notification of soon to expire classifieds', 'checkbox', '', '', '0', ''),  
('modzzz_classified_activate_expired', 'on', @iCategId, 'activate sending email notification of expired classifieds', 'checkbox', '', '', '0', ''), 
('modzzz_classified_email_expiring', '3', @iCategId, 'number of days before expiry to send email notification (0=same day)', 'digit', '', '', '0', ''), 
('modzzz_classified_email_expired', '3', @iCategId, 'number of days after expiry to send email notification (0=same day', 'digit', '', '', '0', ''),  
('modzzz_classified_max_email_invitations', '10', @iCategId, 'Max number of email to send per one promotion', 'digit', '', '', '0', ''),

('modzzz_classified_forum_max_preview', '200', @iCategId, 'length of forum post snippet to show on main page', 'digit', '', '', '0', ''),
('modzzz_classified_comments_max_preview', '200', @iCategId, 'length of comments snippet to show on main page', 'digit', '', '', '0', ''), 
('modzzz_classified_perpage_main_comment', '5', @iCategId, 'Number of comments to show on main page', 'digit', '', '', '0', ''), 
  
('modzzz_classified_featured_cost', '5', @iCategId, 'Cost per day for Featured Status', 'digit', '', '', 0, ''),
('modzzz_classified_buy_featured', '', @iCategId, 'Enable Paypal purchase of Featured Status', 'checkbox', '', '', 0, ''), 
('modzzz_classified_default_country', 'US', @iCategId, 'default country for location', 'digit', '', '', 0, ''),
  
('modzzz_classified_paypal_email', '', @iCategId, 'Paypal Email', 'digit', '', '', 0, ''),
('modzzz_classified_paid_active', '', @iCategId, 'Activate Paid Classifieds',  'checkbox', '', '', 0, ''), 
('modzzz_classified_currency_code', 'USD', @iCategId, 'Currency code for checkout system (eg USD,EUR,GBP)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_classified_currency_sign', '&#36;', @iCategId, 'Currency sign (for display purposes only)', 'digit', 'return strlen($arg0) > 0;', 'cannot be empty.', '0', ''),
('modzzz_classified_invoice_valid_days', '100', @iCategId, 'Number of Days before pending Invoices expire<br>blank or zero means no expiration', 'digit', '', '', 0, ''), 

('modzzz_classified_state_columns', '4', @iCategId, 'Number of columns for states listings on classified main page', 'select', '', '', '', '1,2,3,4') 
;
 

-- search objects
INSERT INTO `sys_objects_search` VALUES(NULL, 'modzzz_classified', '_modzzz_classified', 'BxClassifiedSearchResult', 'modules/modzzz/classified/classes/BxClassifiedSearchResult.php');

-- vote objects
INSERT INTO `sys_objects_vote` VALUES (NULL, 'modzzz_classified', '[db_prefix]rating', '[db_prefix]rating_track', 'gal_', '5', 'vote_send_result', 'BX_PERIOD_PER_VOTE', '1', '', '', '[db_prefix]main', 'rate', 'rate_count', 'id', 'BxClassifiedVoting', 'modules/modzzz/classified/classes/BxClassifiedVoting.php');

-- comments objects
INSERT INTO `sys_objects_cmts` VALUES (NULL, 'modzzz_classified', '[db_prefix]cmts', '[db_prefix]cmts_track', '0', '1', '90', '5', '1', '-3', 'none', '0', '1', '0', 'cmt', '[db_prefix]main', 'id', 'comments_count', 'BxClassifiedCmts', 'modules/modzzz/classified/classes/BxClassifiedCmts.php');
 

-- views objects
INSERT INTO `sys_objects_views` VALUES(NULL, 'modzzz_classified', '[db_prefix]views_track', 86400, '[db_prefix]main', 'id', 'views', 1);

-- tag objects
INSERT INTO `sys_objects_tag` VALUES (NULL, 'modzzz_classified', 'SELECT `Tags` FROM `[db_prefix]main` WHERE `id` = {iID} AND `status` = ''approved''', 'modzzz_classified_permalinks', 'm/classified/browse/tag/{tag}', 'modules/?r=classified/browse/tag/{tag}', '_modzzz_classified');
 
-- users actions
INSERT INTO `sys_objects_actions` (`Caption`, `Icon`, `Url`, `Script`, `Eval`, `Order`, `Type`) VALUES 
  
    ('{TitleEdit}', 'edit', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''edit/{ID}'';', '0', 'modzzz_classified'),
    ('{TitleDelete}', 'remove', '', 'getHtmlData( 'ajaxy_popup_result_div_{ID}', '{evalResult}', false, 'post', true);return false;', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return  BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''delete/{ID}'';', '1', 'modzzz_classified'),
    ('{TitleShare}', 'share-square-o', '', 'showPopupAnyHtml (''{BaseUri}share_popup/{ID}'');', '', '2', 'modzzz_classified'),
    ('{AddToFeatured}', 'star-o', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_featured/{ID}'';', '6', 'modzzz_classified'),
    ('{TitleUploadPhotos}', 'picture-o', '{BaseUri}upload_photos/{URI}', '', '', '9', 'modzzz_classified'),
    ('{TitleUploadVideos}', 'film', '{BaseUri}upload_videos/{URI}', '', '', '10', 'modzzz_classified'),
    ('{TitleEmbed}', 'film', '{BaseUri}embed/{URI}', '', '', '10', 'modzzz_classified'),
    ('{TitleUploadSounds}', 'music', '{BaseUri}upload_sounds/{URI}', '', '', '11', 'modzzz_classified'),
    ('{TitleUploadFiles}', 'save', '{BaseUri}upload_files/{URI}', '', '', '12', 'modzzz_classified'),
    ('{TitleSubscribe}', 'paperclip', '', '{ScriptSubscribe}', '', '13', 'modzzz_classified'),
    ('{TitleInquire}', 'question-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''inquire/{ID}'';', '14', 'modzzz_classified'), 
    ('{TitleInvite}', 'plus-circle', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''invite/{ID}'';', '15', 'modzzz_classified'), 
    ('{TitleBuy}', 'money', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''item_buy/{ID}'';', '16', 'modzzz_classified'), 
	
	('{AddToFavorite}', 'hand-o-right', '', 'getHtmlData( ''ajaxy_popup_result_div_{ID}'', ''{evalResult}'', false, ''post'');return false;', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''mark_favorite/{ID}'';', '10', 'modzzz_classified'),
  
    ('{TitlePurchaseFeatured}', 'shopping-cart', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''purchase_featured/{ID}'';', '16', 'modzzz_classified'),
    ('{TitleRelist}', 'refresh', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''relist/{ID}'';', '16', 'modzzz_classified'), 
    ('{TitleExtend}', 'wrench', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''extend/{ID}'';', '17', 'modzzz_classified'),
    ('{TitlePremium}', 'money', '{evalResult}', '', '$oConfig = $GLOBALS[''oBxClassifiedModule'']->_oConfig; return BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ''premium/{ID}'';', '18', 'modzzz_classified'),  

    ('{evalResult}', 'plus', '{BaseUri}browse/my&filter=add_classified', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_classified_action_add_classified'') : '''';', '1', 'modzzz_classified_title'),
    ('{evalResult}', 'shopping-cart', '{BaseUri}browse/my', '', 'return $GLOBALS[''logged''][''member''] || $GLOBALS[''logged''][''admin''] ? _t(''_modzzz_classified_action_my_classified'') : '''';', '2', 'modzzz_classified_title');
  
     

-- top menu 
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Classified', '_modzzz_classified_menu_root', 'modules/?r=classified/view/|modules/?r=classified/edit/|modules/?r=classified/inquire/|modules/?r=classified/item_buy/|modules/?r=classified/invite/|modules/?r=classified/map_edit/|forum/classified/|modules/?r=classified/relist/|modules/?r=classified/extend/|modules/?r=classified/premium/|modules/?r=classified/purchase_featured/|modules/?r=classified/embed/', '', 'non,memb', '', '', '', 1, 1, 1, 'system', 'shopping-cart', '', '0', '');


SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Classified View', '_modzzz_classified_menu_view_classified', 'modules/?r=classified/view/{modzzz_classified_view_uri}', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Classified View Comments', '_modzzz_classified_menu_view_comments', 'modules/?r=classified/comments/{modzzz_classified_view_uri}', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES (NULL, @iCatRoot, 'Classified View Forum', '_modzzz_classified_menu_view_forum', 'forum/classified/forum/{modzzz_classified_view_uri}-0.htm|forum/classified/', 3, 'non,memb', '', '', '$oModuleDb = new BxDolModuleDb(); return $oModuleDb->getModuleByUri(''forum'') ? true : false;', 1, 1, 1, 'custom', '', '', 0, '');

 

SET @iMaxMenuOrder := (SELECT `Order` + 1 FROM `sys_menu_top` WHERE `Parent` = 0 ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 0, 'Classified', '_modzzz_classified_menu_root', 'modules/?r=classified/home/|modules/?r=classified/', @iMaxMenuOrder, 'non,memb', '', '', '', 1, 1, 1, 'top', 'shopping-cart', '', 1, '');
SET @iCatRoot := LAST_INSERT_ID();
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Classifieds Main Page', '_modzzz_classified_menu_main', 'modules/?r=classified/home/', 0, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Recent Classifieds', '_modzzz_classified_menu_recent', 'modules/?r=classified/browse/recent', 2, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Top Rated Classifieds', '_modzzz_classified_menu_top_rated', 'modules/?r=classified/browse/top', 3, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Popular Classifieds', '_modzzz_classified_menu_popular', 'modules/?r=classified/browse/popular', 4, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Featured Classifieds', '_modzzz_classified_menu_featured', 'modules/?r=classified/browse/featured', 5, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Classifieds Tags', '_modzzz_classified_menu_tags', 'modules/?r=classified/tags', 8, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_classified');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Classifieds Categories', '_modzzz_classified_menu_categories', 'modules/?r=classified/categories', 9, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, 'modzzz_classified');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Calendar', '_modzzz_classified_menu_calendar', 'modules/?r=classified/calendar', 10, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Local Classifieds', '_modzzz_classified_menu_local', 'modules/?r=classified/local', 11, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Search', '_modzzz_classified_menu_search', 'modules/?r=classified/search', 12, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES
(NULL, @iCatRoot, 'Favorite Classified', '_modzzz_classified_menu_favorite', 'modules/?r=classified/browse/favorite', 14, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, ''),

INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, @iCatRoot, 'Classified Packages', '_modzzz_classified_menu_packages', 'modules/?r=classified/packages', 13, 'non,memb', '', '', '$oMain = BxDolModule::getInstance(''BxClassifiedModule'');return ($oMain==null) ? false : $oMain->isAllowedPaidClassifieds(false);', 1, 1, 1, 'custom', '', '', 0, '');

SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 9 ORDER BY `Order` DESC LIMIT 1),5);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 9, 'Classified', '_modzzz_classified_menu_my_classified_profile', 'modules/?r=classified/browse/user/{profileUsername}|modules/?r=classified/browse/joined/{profileUsername}', @iCatProfileOrder, 'non,memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

SET @iCatProfileOrder := IFNULL((SELECT MAX(`Order`)+1 FROM `sys_menu_top` WHERE `Parent` = 4 ORDER BY `Order` DESC LIMIT 1),5);
INSERT INTO `sys_menu_top` (`ID`, `Parent`, `Name`, `Caption`, `Link`, `Order`, `Visible`, `Target`, `Onclick`, `Check`, `Editable`, `Deletable`, `Active`, `Type`, `Picture`, `Icon`, `BQuickLink`, `Statistics`) VALUES(NULL, 4, 'Classified', '_modzzz_classified_menu_my_classified_profile', 'modules/?r=classified/browse/my', @iCatProfileOrder, 'memb', '', '', '', 1, 1, 1, 'custom', '', '', 0, '');

-- admin menu
SET @iMax = (SELECT MAX(`order`) FROM `sys_menu_admin` WHERE `parent_id` = '2');
INSERT IGNORE INTO `sys_menu_admin` (`parent_id`, `name`, `title`, `url`, `description`, `icon`, `order`) VALUES
(2, 'modzzz_classified', '_modzzz_classified', '{siteUrl}modules/?r=classified/administration/', 'Classified module by Modzzz','shopping-cart', @iMax+1);
  
-- site stats
SET @iStatSiteOrder := (SELECT `StatOrder` + 1 FROM `sys_stat_site` WHERE 1 ORDER BY `StatOrder` DESC LIMIT 1);
INSERT INTO `sys_stat_site` VALUES(NULL, 'modzzz_classified', 'modzzz_classified', 'modules/?r=classified/browse/recent', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''approved''', 'modules/?r=classified/administration', 'SELECT COUNT(`id`) FROM `[db_prefix]main` WHERE `status`=''pending''', 'shopping-cart', @iStatSiteOrder);
 
-- PQ statistics
INSERT INTO `sys_stat_member` VALUES ('modzzz_classified', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `status`=''approved''');
INSERT INTO `sys_stat_member` VALUES ('modzzz_classifiedp', 'SELECT COUNT(*) FROM `[db_prefix]main` WHERE `author_id` = ''__member_id__'' AND `Status`!=''approved''');
INSERT INTO `sys_account_custom_stat_elements` VALUES(NULL, '_modzzz_classified', '__modzzz_classified__ (<a href="modules/?r=classified/browse/my&filter=add_classified">__l_add__</a>)');
 

-- membership actions
SET @iLevelNonMember := 1;
SET @iLevelStandard := 2;
SET @iLevelPromotion := 3;

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified view contacts', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified purchase', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified extend', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified relist', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified photos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified sounds add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified videos add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified files add', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

  
INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified purchase featured', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified view classified', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified browse', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified search', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelNonMember, @iAction), (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified add classified', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified comments delete and edit', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified edit any classified', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified delete any classified', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified mark as featured', NULL);
INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified approve classified', NULL);
 
 
INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified make inquiry', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified buy item', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);


-- alert handlers
INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_classified_profile_delete', '', '', 'BxDolService::call(''classified'', ''response_profile_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'profile', 'delete', @iHandler);

INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_classified_media_delete', '', '', 'BxDolService::call(''classified'', ''response_media_delete'', array($this));');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_photos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_videos', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_sounds', 'delete', @iHandler);
INSERT INTO `sys_alerts` VALUES (NULL , 'bx_files', 'delete', @iHandler);
 
-- member menu
SET @iMemberMenuParent = (SELECT `ID` FROM `sys_menu_member` WHERE `Name` = 'AddContent');
SET @iMemberMenuOrder = (SELECT MAX(`Order`) + 1 FROM `sys_menu_member` WHERE `Parent` = IFNULL(@iMemberMenuParent, -1));
INSERT INTO `sys_menu_member` SET `Name` = 'modzzz_classified', `Eval` = 'return BxDolService::call(''classified'', ''get_member_menu_item_add_content'');', `Type` = 'linked_item', `Parent` = IFNULL(@iMemberMenuParent, 0), `Order` = IFNULL(@iMemberMenuOrder, 1);

-- privacy
INSERT INTO `sys_privacy_actions` (`module_uri`, `name`, `title`, `default_group`) VALUES
('classified', 'view_classified', '_modzzz_classified_privacy_view_classified', '3'),
('classified', 'comment', '_modzzz_classified_privacy_comment', '3'),
('classified', 'rate', '_modzzz_classified_privacy_rate', '3'),
('classified', 'post_in_forum', '_modzzz_classified_privacy_post_in_forum', '3') 
;

-- subscriptions
INSERT INTO `sys_sbs_types` (`unit`, `action`, `template`, `params`) VALUES
('modzzz_classified', '', '', 'return BxDolService::call(''classified'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_classified', 'change', 'modzzz_classified_sbs', 'return BxDolService::call(''classified'', ''get_subscription_params'', array($arg2, $arg3));'),
('modzzz_classified', 'commentPost', 'modzzz_classified_sbs', 'return BxDolService::call(''classified'', ''get_subscription_params'', array($arg2, $arg3));');
 

-- email templates 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_inquiry', '<NickName> sent a message about your Classified Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> has sent a message about your Classified Listing, <b><a href="<ClassifiedUrl>"><ClassifiedTitle></a></b>:</p><pre><Message></pre>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Classified Listing Inquiry', '0');
  
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_make_buy_offer', '<NickName> wants to purchase your Classified Item listed on <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Dear <RecipientName></b>,</p><p><a href="<SenderLink>"><SenderName></a> wants to purchase your Classified Item, <b><a href="<ClassifiedUrl>"><ClassifiedTitle></a></b>. Please make the necessary arrangements with them to complete the transaction.</p><pre><Message></pre>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Classified Item Buy', '0');


INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES 
('modzzz_classified_invitation', 'Check out this Classified Listing: <ClassifiedName>', '<bx_include_auto:_email_header.html />\r\n\r\n <p>Hello <NickName>,</p> <p><a href="<InviterUrl>"><InviterNickName></a> has invited you to check out this Classified listing <a href="<ClassifiedUrl>"><ClassifiedName></a>:</p> <pre><InvitationText></pre><p>--</p> \r\n\r\n<bx_include_auto:_email_footer.html />', 'Classified Listing promotion template', '0');
  

INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_expired', 'Your Classified Listing at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Classified Listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Expired Classified Listing notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_post_expired', 'Message about your expired Classified Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Classified Listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> has expired <b><Days></b> days ago</p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Post-Expired Classified Listing notification', '0');
 
INSERT INTO `sys_email_templates`(`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_expiring', 'Message about your expiring Classified Listing at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n<p><b>Hello <NickName></b>,</p><p>Your Classified Listing, <a href="<ListLink>"><ListTitle></a> at <a href="<SiteLink>"><SiteName></a> will expire in <b><Days></b> days<br></p>\r\n\r\n<bx_include_auto:_email_footer.html />', 'Expiring Classified Listing notification', '0');
  

INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_featured_expire_notify', 'Your Featured Classified Status at <SiteName> has expired', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is inform you that your Featured Status for the Classified Listing, <a href="<ListLink>"><ListTitle></a> at <SiteName> has expired. You may purchase Featured Status again at any time you desire <br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>---</p>\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Classified Status Expire Notification', '0');
 
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_featured_admin_notify', 'A member purchased Featured Classified Status at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear Administrator</b>,</p>\r\n\r\n<p><a href="<NickLink>"><NickName></a> has just purchased Featured Status for the Classified Listing, <a href="<ListLink>"><ListTitle></a>, for <Days> days at <SiteName><br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>---</p>\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Classified Purchase Admin Notification', '0');
  
INSERT INTO `sys_email_templates` (`Name`, `Subject`, `Body`, `Desc`, `LangID`) VALUES
('modzzz_classified_featured_buyer_notify', 'Your Featured Classified Status purchase at <SiteName>', '<bx_include_auto:_email_header.html />\r\n\r\n\r\n<p><b>Dear <NickName></b>,</p>\r\n\r\n<p>This is confirmation of your Featured Status purchase at <SiteName> for Classified Listing, <a href="<ListLink>"><ListTitle></a>. It will be Featured for <Days> days<br></p>\r\n\r\n<p><br>\r\n***************\r\n</p>\r\n\r\n<p><b>Thank you for using our services!</b></p>\r\n\r\n<p>---</p>\r\n\r\n\r\n<bx_include_auto:_email_footer.html />', 'Featured Classified Purchase Buyer Notification', '0');
  
 
INSERT INTO `sys_cron_jobs` ( `name`, `time`, `class`, `file`, `eval`) VALUES
 ( 'BxClassified', '*/5 * * * *', 'BxClassifiedCron', 'modules/modzzz/classified/classes/BxClassifiedCron.php', '') ;
 
INSERT INTO `sys_pre_values` ( `Key`, `Order`, `Value`, `LKey`) VALUES 
('ClassifiedType', 1, '', '_Select'),
('ClassifiedType', 2, 'wanted', '_modzzz_classified_type_wanted'),
('ClassifiedType', 3, 'rent', '_modzzz_classified_type_rent'),
('ClassifiedType', 4, 'sale', '_modzzz_classified_type_sale'),
('ClassifiedType', 5, 'free', '_modzzz_classified_type_free'),
('ClassifiedType', 8, 'hiring', '_modzzz_classified_type_hiring'),
('ClassifiedType', 9, 'seeking', '_modzzz_classified_type_seeking');
 
INSERT INTO `sys_pre_values` ( `Key`, `Value`, `Order`, `LKey`) VALUES 
('ClassifiedsCurrency', '&#65020;',1,  'Rials'),
('ClassifiedsCurrency', '&#8363;', 2,  'Dong'),
('ClassifiedsCurrency', '&#84;&#76;', 3,  'Lira'), 
('ClassifiedsCurrency', '&#8356;', 4,  'Liras'),  
('ClassifiedsCurrency', '&#3647;', 5,  'Baht'),
('ClassifiedsCurrency', '&#67;&#72;&#70;',   6,  'Francs'),
('ClassifiedsCurrency', '&#36;',   7,  'Dollars'),
('ClassifiedsCurrency', '&#8364;', 8,  'Euro'),
('ClassifiedsCurrency', '&#76;&#101;&#107;', 9,  'Leke'),
('ClassifiedsCurrency', '&#1547;', 10,  'Afghanis'),
('ClassifiedsCurrency', '&#402;', 11,  'Guilders'),
('ClassifiedsCurrency', '&#1084;&#1072;&#1085;', 12,  'New Manats'),
('ClassifiedsCurrency', '&#112;&#46;', 13,  'Rubles (Belarus)'),
('ClassifiedsCurrency', '&#1088;&#1091;&#1073;', 14,  'Rubles (Russia)'), 
('ClassifiedsCurrency', '&#80;', 15,  'Pulas'),
('ClassifiedsCurrency', '&#1083;&#1074;', 16,  'Leva'),
('ClassifiedsCurrency', '&#82;&#36;', 17,  'Reais'),
('ClassifiedsCurrency', '&#163;', 18,  'Pounds'),
('ClassifiedsCurrency', '&#8361;', 19,  'Won'),
('ClassifiedsCurrency', '&#8369;', 20,  'Pesos (Cuba)'),
('ClassifiedsCurrency', '&#82;&#68;&#36;', 21,  'Pesos (Dominican Republic)'),
('ClassifiedsCurrency', '&#80;&#104;&#112;', 22,  'Pesos (Philippines)'),
('ClassifiedsCurrency', '&#36;&#85;', 23,  'Pesos (Uruguay)'),  
('ClassifiedsCurrency', '&#6107;', 24,  'Riels'),
('ClassifiedsCurrency', '&#165;', 25,  'Yuan Renminbi'),
('ClassifiedsCurrency', '&#165;', 26,  'Yen'), 
('ClassifiedsCurrency', '&#1076;&#1077;&#1085;', 27,  'Denars'),
('ClassifiedsCurrency', '&#8360;', 28,  'Rupees'),
('ClassifiedsCurrency', '&#1044;&#1080;&#1085;', 29,  'Dinars'),
('ClassifiedsCurrency', '&#82;', 30,  'Rand'),
('ClassifiedsCurrency', '&#107;&#114;', 31,  'Tenge'),
('ClassifiedsCurrency', '&#1083;&#1074;', 32,  'Soms'),
('ClassifiedsCurrency', '&#66;&#47;&#46;', 33,  'Balboa'),
('ClassifiedsCurrency', '&#8366;', 34,  'Tugriks'),
('ClassifiedsCurrency', '&#8365;', 35,  'Kips'),
('ClassifiedsCurrency', '&#8362;', 36,  'New Shekels'),
('ClassifiedsCurrency', '&#76;', 37,  'Lempiras'),
('ClassifiedsCurrency', '&#81;', 38,  'Quetzales'),
('ClassifiedsCurrency', '&#162;', 39,  'Cedis'),
('ClassifiedsCurrency', '&#75;&#269;', 40,  'Koruny'),
('ClassifiedsCurrency', '&#107;&#110;', 41,  'Kuna'),
('ClassifiedsCurrency', '&#8353;', 42,  'Colón'),
('ClassifiedsCurrency', '&#76;&#115;', 43,  'Lati'),
('ClassifiedsCurrency', '&#76;&#116;', 44,  'Litai'), 
('ClassifiedsCurrency', '&#82;&#112;', 45,  'Rupiahs'),
('ClassifiedsCurrency', '&#70;&#116;', 46,  'Forint'),
('ClassifiedsCurrency', '&#8372;', 47,  'Hryvnia'),
('ClassifiedsCurrency', '&#83;', 48,  'Shillings'),
('ClassifiedsCurrency', '&#8358;', 49,  'Nairas'), 
('ClassifiedsCurrency', '&#66;&#115;', 50,  'Fuertes'),
('ClassifiedsCurrency', '&#1083;&#1074;', 51,  'Sums'),
('ClassifiedsCurrency', '&#108;&#101;&#105;', 52,  'New Lei'),
('ClassifiedsCurrency', '&#122;&#322;', 53,  'Zlotych'),
('ClassifiedsCurrency', '&#83;&#47;&#46;', 54,  'Nuevos Soles'),
('ClassifiedsCurrency', '&#71;&#115;', 55,  'Guarani'),
('ClassifiedsCurrency', '&#67;&#36;', 56,  'Cordobas'),
('ClassifiedsCurrency', '&#77;&#84;', 57,  'Meticais'),
('ClassifiedsCurrency', '&#82;&#77;', 58,  'Ringgits'),
('ClassifiedsCurrency', '&#107;&#114;', 59,  'Kroner (Denmark)')  
;  
 	
-- sitemap
SET @iMaxOrderSiteMaps = (SELECT MAX(`order`)+1 FROM `sys_objects_site_maps`);
INSERT INTO `sys_objects_site_maps` (`object`, `title`, `priority`, `changefreq`, `class_name`, `class_file`, `order`, `active`) VALUES
('modzzz_classified', '_modzzz_classified', '0.8', 'auto', 'BxClassifiedSiteMaps', 'modules/modzzz/classified/classes/BxClassifiedSiteMaps.php', @iMaxOrderSiteMaps, 1);

-- chart
SET @iMaxOrderCharts = (SELECT MAX(`order`)+1 FROM `sys_objects_charts`);
INSERT INTO `sys_objects_charts` (`object`, `title`, `table`, `field_date_ts`, `field_date_dt`, `query`, `active`, `order`) VALUES
('modzzz_classified', '_modzzz_classified', 'modzzz_classified_main', 'created', '', '', 1, @iMaxOrderCharts);


INSERT INTO `sys_alerts_handlers` VALUES (NULL, 'modzzz_classified_map_install', '', '', 'if (''wmap'' == $this->aExtras[''uri''] && $this->aExtras[''res''][''result'']) BxDolService::call(''classified'', ''map_install'');');
SET @iHandler := LAST_INSERT_ID();
INSERT INTO `sys_alerts` VALUES (NULL , 'module', 'install', @iHandler);

 

INSERT INTO `sys_pre_values` ( `Key`, `Order`, `Value`, `LKey`) VALUES 
('ClassifiedPaymentType', 1, '', '_Select'),
('ClassifiedPaymentType', 2, 'day', '_modzzz_classified_payment_per_day'),
('ClassifiedPaymentType', 3, 'week', '_modzzz_classified_payment_per_week'),
('ClassifiedPaymentType', 4, 'month', '_modzzz_classified_payment_per_month'),
('ClassifiedPaymentType', 5, 'session', '_modzzz_classified_payment_per_session'),
('ClassifiedPaymentType', 6, 'term', '_modzzz_classified_payment_per_term'),
('ClassifiedPaymentType', 7, 'year', '_modzzz_classified_payment_per_year');

INSERT INTO `sys_acl_actions` VALUES (NULL, 'classified allow embed', NULL);
SET @iAction := LAST_INSERT_ID();
INSERT INTO `sys_acl_matrix` (`IDLevel`, `IDAction`) VALUES 
    (@iLevelStandard, @iAction), (@iLevelPromotion, @iAction);