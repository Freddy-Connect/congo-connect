--
-- Database Dump For Dolphin: 
--


--
-- Table structure for table `1col_config`
--

DROP TABLE IF EXISTS `1col_config`;
CREATE TABLE `1col_config` (
  `foto` varchar(5) NOT NULL,
  `video` varchar(5) NOT NULL,
  `gruppi` varchar(5) NOT NULL,
  `eventi` varchar(5) NOT NULL,
  `siti` varchar(5) NOT NULL,
  `sondaggi` varchar(5) NOT NULL,
  `annunci` varchar(5) NOT NULL,
  `blog` varchar(5) NOT NULL,
  `file` varchar(5) NOT NULL,
  `suoni` varchar(5) NOT NULL,
  `pagine` varchar(5) NOT NULL,
  `emailad` varchar(5) NOT NULL,
  `status` varchar(5) NOT NULL,
  `city` varchar(5) NOT NULL,
  `slide` varchar(5) NOT NULL,
  `numbermaxfriend` varchar(5) NOT NULL,
  `timereload` varchar(15) NOT NULL,
  `mainmenuvar` varchar(5) NOT NULL,
  `mediavar` varchar(5) NOT NULL,
  `acceditvar` varchar(5) NOT NULL,
  `onlinefriendvar` varchar(5) NOT NULL,
  `deletebutton` varchar(5) NOT NULL,
  `mailurl` varchar(100) NOT NULL,
  `groupurl` varchar(100) NOT NULL,
  `addgroupurl` varchar(100) NOT NULL,
  `listingurl` varchar(100) NOT NULL,
  `addlistingurl` varchar(100) NOT NULL,
  `eventurl` varchar(100) NOT NULL,
  `addeventurl` varchar(100) NOT NULL,
  `pollurl` varchar(100) NOT NULL,
  `addpollurl` varchar(100) NOT NULL,
  `jobsurl` varchar(100) NOT NULL,
  `addjobsurl` varchar(100) NOT NULL,
  `blogurl` varchar(100) NOT NULL,
  `addblogurl` varchar(100) NOT NULL,
  `classifiedurl` varchar(100) NOT NULL,
  `fileurl` varchar(100) NOT NULL,
  `addfileurl` varchar(100) NOT NULL,
  `addclassifiedurl` varchar(100) NOT NULL,
  `photourl` varchar(100) NOT NULL,
  `videourl` varchar(100) NOT NULL,
  `soundurl` varchar(100) NOT NULL,
  `avatarurl` varchar(100) NOT NULL,
  `customlink1` varchar(600) NOT NULL,
  `customlink2` varchar(600) NOT NULL,
  `customlink3` varchar(600) NOT NULL,
  `customlink4` varchar(600) NOT NULL,
  `customlink5` varchar(600) NOT NULL,
  `customsectn` varchar(600) NOT NULL,
  `customsect1` varchar(600) NOT NULL,
  `customsect2` varchar(600) NOT NULL,
  `customsect3` varchar(600) NOT NULL,
  `customsect4` varchar(600) NOT NULL,
  `customsect5` varchar(600) NOT NULL,
  `avaset` varchar(5) NOT NULL,
  `privasett` varchar(5) NOT NULL,
  `sottoscrizione` varchar(5) NOT NULL,
  `mailset` varchar(5) NOT NULL,
  `amiciset` varchar(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `1col_config`
--

INSERT INTO `1col_config` VALUES ('OFF', 'OFF', 'OFF', 'ON', 'ON', 'ON', 'ON', 'ON', 'OFF', 'OFF', 'ON', 'ON', 'ON', 'OFF', 'ON', '10', '15000', 'ON', 'OFF', 'ON', 'ON', 'OFF', 'mail.php?mode=inbox', 'm/groups/browse/my', 'modules/?r=groups/browse/my&bx_groups_filter=add_group', 'm/listing/browse/my&filter=manage_listing', 'm/listing/browse/my&filter=add_listing', 'm/events/browse/my', 'modules/?r=events/browse/my&bx_events_filter=add_event', 'm/aqb_popularity/view/', 'm/aqb_popularity/view/', 'm/jobs/browse/my&filter=manage_jobs', 'm/jobs/browse/my&filter=add_job', 'modules/?r=notify/home/', 'modules/?r=notify/my/', 'm/classified/browse/my&filter=manage_classified', 'm/files/home/', 'm/files/albums/my/add_objects/', 'm/classified/browse/my&filter=add_classified', 'm/photos/albums/my/main/', 'm/videos/albums/my/main', 'm/sounds/home/', 'm/avatar/', 'find_friends.php', 'invite_history.php', '', '', '', '', '', '', '', '', '', 'ON', 'OFF', 'OFF', 'ON', 'ON');

-- --------------------------------------------------------
