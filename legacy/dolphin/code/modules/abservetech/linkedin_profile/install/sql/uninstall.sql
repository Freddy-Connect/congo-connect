-- tables
DROP TABLE IF EXISTS `abs_plinkin_certificate`, `abs_plinkin_education`, `abs_plinkin_education_types`, `abs_plinkin_endorse_data`, `abs_plinkin_experience`, `abs_plinkin_medias`, `abs_plinkin_profile_other_lang`,`abs_plinkin_project`,`abs_plinkin_skills`,`abs_plinkin_summary`,`abs_plinkin_user_lang`,`abs_plinkin_user_skills`,`abs_plinkin_wall_likes`,`abs_plinkin_user_profile_settings`;

ALTER TABLE `Profiles` DROP `abs_plinkin_user_title`;
ALTER TABLE `Profiles` DROP `selected_language`;
ALTER TABLE `Profiles` DROP `abs_plinkin_endorse_me`;
ALTER TABLE `Profiles` DROP `abs_plinkin_endorse_by_friends`;
ALTER TABLE `Profiles` DROP `abs_plinkin_can_endorse`;
ALTER TABLE `Profiles` DROP `abs_plinkin_endorse_notification`;

DELETE FROM `sys_pre_values` WHERE `Key` IN ('schoolDegree','languages_1_proficiency','LanguageProficiency','_abs_plinkin_month');

DELETE FROM `sys_page_compose_pages` WHERE `Name` = 'public-profile-settings';
DELETE FROM `sys_page_compose` WHERE `Page` = 'public-profile-settings';
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile' AND `Desc` IN ('Profile Certificates Block', 'Profile Projects Block','Profile Languages Block','Profile Skills Block','Profile Education Block','Profile Experience Block','_abs_plinkin_profile_view_summary','');
DELETE FROM `sys_page_compose` WHERE `Page` = 'pedit' AND `Desc` IN ('_abs_plink_profile_info_box', '_abs_plink_profile_experience','_abs_plink_profile_education','_abs_plinkin_prof_skill','_abs_plinkin_prof_langs','_language_selector','_abs_plinkin_profile_certificates','_abs_plinkin_prof_project','_abs_plinkin_profile_edit_summary');
DELETE FROM `sys_page_compose` WHERE `Page` = 'profile-recent-activity' AND `Desc` = '_recent_activity';
