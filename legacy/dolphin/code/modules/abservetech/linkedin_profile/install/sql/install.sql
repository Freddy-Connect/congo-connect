--
-- Table structure for table `abs_plinkin_certificate`
--
CREATE TABLE IF NOT EXISTS `abs_plinkin_certificate` (
  `cert_id` int(11) NOT NULL AUTO_INCREMENT,
  `cert_name` varchar(255) NOT NULL,
  `cert_authority` varchar(255) NOT NULL,
  `cert_license` varchar(255) NOT NULL,
  `cert_url` varchar(255) NOT NULL,
  `cert_period` text NOT NULL,
  `cert_end_month` int(11) NOT NULL,
  `cert_end_year` int(11) NOT NULL,
  `cert_user_id` int(11) NOT NULL,
  PRIMARY KEY (`cert_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;
-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_education`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_education` (
  `edu_id` int(11) NOT NULL AUTO_INCREMENT,
  `edu_school_name` varchar(255) NOT NULL,
  `edu_type` varchar(255) NOT NULL,
  `edu_period` text NOT NULL,
  `edu_field_of_study` varchar(255) NOT NULL,
  `edu_grade` varchar(255) NOT NULL,
  `edu_activities` varchar(255) NOT NULL,
  `edu_desc` text NOT NULL,
  `edu_user_id` int(11) NOT NULL,
  `edu_completed_year` int(11) NOT NULL,
  PRIMARY KEY (`edu_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_education_types`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_education_types` (
  `edu_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `edu_type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`edu_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `abs_plinkin_education_types`
--

INSERT INTO `abs_plinkin_education_types` (`edu_type_id`, `edu_type_name`) VALUES
(1, 'High School'),
(2, 'Associate’s Degree'),
(3, 'Bachelor’s Degree'),
(4, 'Master’s Degree'),
(5, 'Master of Business Administration (M.B.A.)'),
(6, 'Juris Doctor (J.D.)'),
(7, 'Doctor of Medicine (M.D.)'),
(8, 'Doctor of Philosophy (Ph.D.)'),
(9, 'Engineer’s Degree'),
(10, 'other');

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_endorse_data`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_endorse_data` (
  `endorse_id` int(11) NOT NULL AUTO_INCREMENT,
  `skill_id` int(11) NOT NULL,
  `skill_user_id` int(11) NOT NULL,
  `endorsed_by` int(11) NOT NULL,
  PRIMARY KEY (`endorse_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_experience`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_experience` (
  `exp_id` int(11) NOT NULL AUTO_INCREMENT,
  `exp_companyName` varchar(255) NOT NULL,
  `exp_title` varchar(255) NOT NULL,
  `exp_period` text NOT NULL,
  `exp_location` varchar(255) NOT NULL,
  `exp_desc` text NOT NULL,
  `exp_user_id` int(11) NOT NULL,
  `exp_end_month` int(11) NOT NULL,
  `exp_end_year` int(11) NOT NULL,
  PRIMARY KEY (`exp_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_medias`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_medias` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `media_name` varchar(255) NOT NULL,
  `media_image_name` varchar(255) NOT NULL,
  `media_title` varchar(255) NOT NULL,
  `media_desc` text NOT NULL,
  `media_type` varchar(255) NOT NULL,
  `media_for` varchar(255) NOT NULL,
  `media_for_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `updated_on` text NOT NULL,
  `media_file_type` varchar(255) NOT NULL,
  `media_pages_count` int(10) NOT NULL,
  PRIMARY KEY (`media_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_profile_other_lang`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_profile_other_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `prof_firstname` varchar(255) NOT NULL,
  `prof_lastname` varchar(255) NOT NULL,
  `prof_userlocation` varchar(255) NOT NULL,
  `prof_usertitle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_project`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_project` (
  `pro_id` int(11) NOT NULL AUTO_INCREMENT,
  `pro_name` varchar(255) NOT NULL,
  `pro_occupation` varchar(255) NOT NULL,
  `pro_url` varchar(255) NOT NULL,
  `pro_team` varchar(255) NOT NULL,
  `pro_desc` varchar(255) NOT NULL,
  `pro_end_month` int(11) NOT NULL,
  `pro_end_year` int(11) NOT NULL,
  `pro_user_id` int(11) NOT NULL,
  `pro_period` text NOT NULL,
  PRIMARY KEY (`pro_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_skills`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_skills` (
  `skill_id` int(11) NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(255) NOT NULL,
  PRIMARY KEY (`skill_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_summary`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_summary` (
  `summary_id` int(10) NOT NULL AUTO_INCREMENT,
  `summary_text` text NOT NULL,
  `summary_userid` int(10) NOT NULL,
  PRIMARY KEY (`summary_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_user_lang`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_user_lang` (
  `user_id` int(11) NOT NULL,
  `lang` varchar(255) NOT NULL,
  `Proficiency` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_user_skills`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_user_skills` (
  `user_skill_id` int(11) NOT NULL AUTO_INCREMENT,
  `skill_id` int(11) NOT NULL,
  `skill_user_id` int(11) NOT NULL,
  PRIMARY KEY (`user_skill_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `abs_plinkin_wall_likes`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_wall_likes` (
  `like_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `wall_id` int(11) NOT NULL,
  PRIMARY KEY (`like_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Table structure for table `abs_plinkin_user_profile_settings`
--

CREATE TABLE IF NOT EXISTS `abs_plinkin_user_profile_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

-- Profile Table Changes

ALTER TABLE `Profiles` ADD `abs_plinkin_user_title` varchar(255) NOT NULL;
ALTER TABLE `Profiles` ADD `selected_language` int(5) NOT NULL default '0';
ALTER TABLE `Profiles` ADD `abs_plinkin_endorse_me` int(5) NOT NULL default '0';
ALTER TABLE `Profiles` ADD `abs_plinkin_endorse_by_friends` int(5) NOT NULL default '0';
ALTER TABLE `Profiles` ADD `abs_plinkin_can_endorse` int(5) NOT NULL default '0'; 
ALTER TABLE `Profiles` ADD `abs_plinkin_endorse_notification` int(5) NOT NULL default '0';

-- --------------------------------------------------------
--
-- Dumping data for table `sys_pre_values`
--

INSERT INTO `sys_pre_values` (`Key`, `Value`, `Order`, `LKey`, `LKey2`, `LKey3`, `Extra`, `Extra2`, `Extra3`) VALUES
('schoolDegree', 'Master of Business Administration', 4, '_MBA', '', '', '', '', ''),
('schoolDegree', 'Juris Doctor', 5, '_Juris_Doctor', '', '', '', '', ''),
('schoolDegree', 'Doctor of Medicine', 6, '_Doctor_Medicine', '', '', '', '', ''),
('schoolDegree', 'Doctor of Philosophy', 7, '_Doctor_Philosophy', '', '', '', '', ''),
('schoolDegree', 'Engineer’s Degree', 8, '_Engineer’s_Degree', '', '', '', '', ''),
('schoolDegree', 'other', 9, '_other', '', '', '', '', ''),
('schoolDegree', 'none', 10, '_none', '', '', '', '', ''),
('languages_1_proficiency', 'Proficiency', 0, '_Proficiency', '', '', '', '', ''),
('languages_1_proficiency', 'Elementary proficiency', 1, '_elementary', '', '', '', '', ''),
('languages_1_proficiency', 'Limited working proficiency', 2, '_lim_working', '', '', '', '', ''),
('languages_1_proficiency', 'Professional working proficiency', 3, '_prof_working', '', '', '', '', ''),
('languages_1_proficiency', 'Full professional proficiency', 4, '_full_prof', '', '', '', '', ''),
('languages_1_proficiency', 'Native or bilingual proficiency', 5, '_nat_or_bilingual', '', '', '', '', ''),
('schoolDegree', 'Master’s Degree', 3, '_M_Degree', '', '', '', '', ''),
('schoolDegree', 'Bachelor’s Degree', 2, '_b_degree', '', '', '', '', ''),
('LanguageProficiency', '_nat_or_bilingual', 4, '_nat_or_bilingual', '', '', '', '', ''),
('LanguageProficiency', '_full_prof', 3, '_full_prof', '', '', '', '', ''),
('LanguageProficiency', '_elementary', 0, '_elementary', '', '', '', '', ''),
('LanguageProficiency', '_lim_work', 1, '_lim_work', '', '', '', '', ''),
('LanguageProficiency', '_prof_working', 2, '_prof_working', '', '', '', '', ''),
('schoolDegree', 'High School', 0, '_high_School', '', '', '', '', ''),
('schoolDegree', 'Associate’s Degree', 1, '_Ass_Degree', '', '', '', '', ''),
('_abs_plinkin_month', 'Dec', 11, '_abs_plinkin_December', '', '', '', '', ''),
('_abs_plinkin_month', 'Nov', 10, '_abs_plinkin_November', '', '', '', '', ''),
('_abs_plinkin_month', 'Sep', 8, '_abs_plinkin_September', '', '', '', '', ''),
('_abs_plinkin_month', 'Oct', 9, '_abs_plinkin_October', '', '', '', '', ''),
('_abs_plinkin_month', 'Aug', 7, '_abs_plinkin_August', '', '', '', '', ''),
('_abs_plinkin_month', 'July', 6, '_abs_plinkin_July', '', '', '', '', ''),
('_abs_plinkin_month', 'June', 5, '_abs_plinkin_June', '', '', '', '', ''),
('_abs_plinkin_month', 'May', 4, '_abs_plinkin_May', '', '', '', '', ''),
('_abs_plinkin_month', 'Apr', 3, '_abs_plinkin_April', '', '', '', '', ''),
('_abs_plinkin_month', 'Mar', 2, '_abs_plinkin_March', '', '', '', '', ''),
('_abs_plinkin_month', 'Feb', 1, '_abs_plinkin_February', '', '', '', '', ''),
('_abs_plinkin_month', 'Jan', 0, '_abs_plinkin_January', '', '', '', '', '');

SET @iPCPOrder = (SELECT `Order` FROM `sys_page_compose_pages` ORDER BY `Order` DESC LIMIT 1);
INSERT INTO `sys_page_compose_pages` (`Name`, `Title`, `Order`, `System`) VALUES
('public-profile-settings', 'Public Profile Settings', @iPCPOrder+1, 1);

--
-- Dumping data for table `sys_page_compose`
--

SET @Column = (SELECT `Column` FROM `sys_page_compose` WHERE `Desc` = 'Profile fields');
SET @Order = (SELECT `Order` FROM `sys_page_compose` WHERE `Desc` = 'Profile fields');

INSERT INTO `sys_page_compose` (`Page`, `PageWidth`, `Desc`, `Caption`, `Column`, `Order`, `Func`, `Content`, `DesignBox`, `ColWidth`, `Visible`, `MinWidth`, `Cache`) VALUES
('pedit', '1140px', '_abs_plink_profile_info_box', '_abs_plink_profile_info_box', @Column, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_info'',array((int)$_REQUEST[''ID'']));', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_abs_plinkin_profile_edit_summary', '_abs_plinkin_profile_edit_summary', @Column, @Order+1, 'PHP', 'return BxDolService::call(''linkedin_profile'', ''profile_summary'');', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_abs_plink_profile_experience', '_abs_plink_profile_experience', @Column, @Order+2, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_experience'',array((int)$_REQUEST[''ID'']));', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_abs_plink_profile_education', '_abs_plink_profile_education', @Column, @Order+3, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_education'',array((int)$_REQUEST[''ID'']));', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_abs_plinkin_prof_skill', '_abs_plinkin_prof_skill', @Column, @Order+4, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_skill'',array((int)$_REQUEST[''ID'']));', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_abs_plinkin_prof_langs', '_abs_plinkin_prof_langs', @Column, @Order+5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_langs'',array((int)$_REQUEST[''ID'']));', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_abs_plinkin_prof_project', '_abs_plinkin_prof_project', @Column, @Order+6, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''ProfileProject'',array((int)$_REQUEST[''ID'']));', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_abs_plinkin_profile_certificates', '_abs_plinkin_profile_certificates', @Column, @Order+7, 'PHP', 'return BxDolService::call(''linkedin_profile'', ''profile_certificate'');', 11, 71.9, 'non,memb', 0, 0),
('pedit', '1140px', '_language_selector', '_language_selector', 3, 0, 'PHP', 'return BxDolService::call(''linkedin_profile'', ''lang_selector'');', 13, 28.1, 'non,memb', 0, 0),
('public-profile-settings', '1291px', '_public_profile_settings_profile_info', '_public_profile_settings_profile_info', 1, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''public_profile_info'',array((int)$_REQUEST[''ID'']));', 11, 70.7, 'non,memb', 0, 0),
('public-profile-settings', '1291px', '_public_profile_settings_past_position', '_public_profile_settings_past_position', 1, 1, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''past_positions'',array((int)$_REQUEST[''ID'']));', 11, 70.7, 'non,memb', 0, 0),
('public-profile-settings', '1291px', '_public_profile_settings_skills', '_public_profile_settings_skills', 1, 2, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''skills'',array((int)$_REQUEST[''ID'']));', 11, 70.7, 'non,memb', 0, 0),
('public-profile-settings', '1291px', '_public_profile_settings_education', '_public_profile_settings_education', 1, 3, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''education'',array((int)$_REQUEST[''ID'']));', 11, 70.7, 'non,memb', 0, 0),
('public-profile-settings', '1291px', '_public_profile_settings_languages', '_public_profile_settings_languages', 1, 4, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''languages'',array((int)$_REQUEST[''ID'']));', 11, 70.7, 'non,memb', 0, 0),
('public-profile-settings', '1140px', '_public_profile_settings_public_checkbox_', '_public_profile_settings_public_checkbox_', 2, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''PublicProfileVisible'',array((int)$_REQUEST[''ID'']));', 11, 28.1, 'non,memb', 0, 0),
('public-profile-settings', '1140px', '_public_certificate_setting', '_public_certificate_setting', 1, 6, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''PublicSettingCertificates'',array((int)$_REQUEST[''ID'']));', 11, 70.7, 'non,memb', 0, 0),
('public-profile-settings', '1140px', '_public_setting_project', '_public_setting_project', 1, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''public_setting_project'',array((int)$_REQUEST[''ID'']));', 11, 70.7, 'non,memb', 0, 0),
('profile-recent-activity', '1140px', '_recent_activity', '_recent_activity', 1, 0, 'PHP', 'return BxDolService::call(''linkedin_profile'', ''profile_recent_activity'');', 11, 100, 'non,memb', 0, 0),
('profile', '1140px', 'Profile Certificates Block', '_abs_plinkin_profile_view_certificate', 3, 6, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_view_certificate'',array($this->oProfileGen->_iProfileID));', 11, 67.2, 'non,memb', 0, 0),
('profile', '1140px', 'Profile Projects Block', '_abs_plinkin_profile_view_project', 3, 5, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_view_project'',array($this->oProfileGen->_iProfileID));', 11, 67.2, 'non,memb', 0, 0),
('profile', '1140px', 'Profile Languages Block', '_abs_plinkin_profile_view_languages', 3, 4, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_view_languages'',array($this->oProfileGen->_iProfileID));', 11, 67.2, 'non,memb', 0, 0),
('profile', '1140px', 'Profile Skills Block', '_abs_plinkin_profile_view_skills', 3, 3, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_view_skills'',array($this->oProfileGen->_iProfileID));', 11, 67.2, 'non,memb', 0, 0),
('profile', '1140px', 'Profile Education Block', '_abs_plinkin_profile_view_education', 3, 2, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_view_education'',array($this->oProfileGen->_iProfileID));', 11, 67.2, 'non,memb', 0, 0),
('profile', '1140px', 'Profile Experience Block', '_abs_plinkin_profile_view_exp', 3, 1, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_view_experience'',array($this->oProfileGen->_iProfileID));', 11, 67.2, 'non,memb', 0, 0),
('profile', '1140px', '_abs_plinkin_profile_view_summary', '_abs_plinkin_profile_view_summary', 3, 0, 'PHP', 'bx_import(''BxDolService''); return BxDolService::call(''linkedin_profile'', ''profile_view_summary'',array($this->oProfileGen->_iProfileID));', 11, 67.2, 'non,memb', 0, 0);