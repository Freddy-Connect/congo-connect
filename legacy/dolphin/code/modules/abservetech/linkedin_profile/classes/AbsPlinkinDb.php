<?php

/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once( BX_DIRECTORY_PATH_CLASSES . 'BxDolModuleDb.php' );

class AbsPlinkinDb extends BxDolModuleDb
{

	/*
     * Constructor.
     */
    function AbsPlinkinDb(&$oConfig)
    {
        parent::BxDolModuleDb($oConfig);
    }

    function getProfile($iId)
    {
    	return $this->getRow("SELECT * FROM `Profiles` WHERE `ID` =".$iId);
    }

    function insertData($table,$qryToInsert, $qUpdate = '')
    {
        $table = $this->_sPrefix .'_'.$table;
        if($qryToInsert == '')
            return false;

        if($qUpdate!=''){
            $this->query("UPDATE `".$table."` SET ". $qryToInsert . " WHERE ".$qUpdate);
        }
        else{
            $this->query("INSERT INTO `".$table."` SET ". $qryToInsert);
        }
    }

    function getUserExperiences($iId)
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_experience` WHERE `exp_user_id` = ".$iId." ORDER BY `exp_end_year` DESC, `exp_end_month` DESC");
    }

    function getExpInfo($iId)
    {
        return $this->getRow("SELECT * FROM `". $this->_sPrefix ."_experience` WHERE `exp_id` = ".$iId);
    }
    // function getEducationUser($iEId) 
    // {
    //     return $this->getOne("SELECT `edu_user_id` FROM `abs_plinkin_education` WHERE `edu_id` = ".$iEId);
    // }
    
    /*function i()
    {
        for($ij=1900;$ij<=2016;$ij++)
        {
            $this->query("INSERT INTO `". $this->_sPrefix ."_years` SET `year`=".$ij);
        }
    }*/

    function getProInfo($iId)
    {
      return $this->getRow("SELECT * FROM `". $this->_sPrefix ."_project` WHERE `pro_id` = ".$iId);  
    }

    function getUserProjects($iId)
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_project` WHERE `pro_user_id` = ".$iId." ORDER BY `pro_end_year` DESC, `pro_end_month` DESC");
    }

     function deleteProject($iId)
    {
        $this->query("DELETE FROM `". $this->_sPrefix ."_project` WHERE `pro_id`=".$iId);
    }

    function getYears()
    {
        return $this->getAll("SELECT `year` FROM `". $this->_sPrefix ."_years` ORDER BY `year` DESC");
    }

    function getEducationTypes()
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_education_types`");
    }

    function getUserEducations($iId)
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_education` WHERE `edu_user_id`=".$iId." ORDER BY `edu_completed_year` DESC");
    }
	
	function getSkills()
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_skills` ORDER BY `skill_id` ASC");
    }
	function getUserSkills($iId)
    {
        return $this->getAll("SELECT `skills`.*,`userskill`.`skill_user_id` as `skill_user_id`,`userskill`.`user_skill_id` as `user_skill_id` FROM `". $this->_sPrefix ."_user_skills` AS userskill JOIN  `". $this->_sPrefix ."_skills` AS skills ON skills.`skill_id` = userskill.`skill_id` WHERE userskill.`skill_user_id` = '".$iId."' ORDER BY `skill_id` ASC");
    }
	
	function addSkill($skill,$uid){
		$this->query("INSERT INTO `".$this->_sPrefix ."_skills` VALUES('','".$skill."')");
		$skill_row = $this->getRow("SELECT * FROM `". $this->_sPrefix ."_skills` WHERE `skill_name`='".$skill."'");
			$skil_id = $skill_row['skill_id'];
			$this->query("INSERT INTO `".$this->_sPrefix ."_user_skills` VALUES('','".$skil_id."','".$uid."')");
	}
	
	function updateUserSkills($skills,$uid){
		$this->query("DELETE FROM `". $this->_sPrefix ."_user_skills` WHERE `skill_user_id`=".$uid);
		foreach($skills as $skill){
			$skill_row = $this->getRow("SELECT * FROM `". $this->_sPrefix ."_skills` WHERE `skill_name`='".$skill."'");
			$skil_id = $skill_row['skill_id'];
			
			$this->query("INSERT INTO `".$this->_sPrefix ."_user_skills` VALUES('','".$skil_id."','".$uid."')");
		}
	}
	
	
	function getUserLanguages($iId)
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_user_lang` WHERE `user_id`='".$iId."' ORDER BY `user_id` ASC");
    }
	
	function updateUserLang($languages,$proficiency,$uid){
		$this->query("DELETE FROM `". $this->_sPrefix ."_user_lang` WHERE `user_id`=".$uid);
		for($i=0;$i<count($languages);$i++){
			if(!empty($languages[$i]))
			$this->query("INSERT INTO `".$this->_sPrefix ."_user_lang` VALUES('".$uid."','".$languages[$i]."','".$proficiency[$i]."')");
		}
	}

    function getEduInfo($iId)
    {
        return $this->getRow("SELECT * FROM `". $this->_sPrefix ."_education` WHERE `edu_id`=".$iId);
    }

    function getEduTypeName($iId)
    {
        return $this->getOne("SELECT `edu_type_name` FROM `". $this->_sPrefix ."_education_types` WHERE `edu_type_id`=".$iId);
    }

    function deleteExperience($iId)
    {
        $this->query("DELETE FROM `". $this->_sPrefix ."_experience` WHERE `exp_id`=".$iId);
    }

    function deleteEducation($iId)
    {
        $this->query("DELETE FROM `". $this->_sPrefix ."_education` WHERE `edu_id`=".$iId);
    }

    function getUserCurrentJob($uId)
    {
        return $this->getRow("SELECT `exp_title`,`exp_companyName` FROM `". $this->_sPrefix ."_experience` WHERE `exp_user_id`=".$uId." ORDER BY `exp_end_year` DESC, `exp_end_month` DESC LIMIT 1");
    }
    
    function getUserPrevCompanies($uId)
    {
        /*echo "SELECT `exp_companyName` FROM `". $this->_sPrefix ."_experience` WHERE `exp_user_id`=".$uId." AND `exp_period` NOT LIKE '%at_present%' ORDER BY `exp_end_year` DESC, `exp_end_month` DESC";*/
       
	   // freddy change to return the latest company    LIMIT 1
	   // return $this->getAll("SELECT `exp_companyName` FROM `". $this->_sPrefix ."_experience` WHERE `exp_user_id`=".$uId." AND `exp_period` NOT LIKE '%at_present%' ORDER BY `exp_end_year` DESC, `exp_end_month` DESC");
	   
	    return $this->getAll("SELECT `exp_companyName` FROM `". $this->_sPrefix ."_experience` WHERE `exp_user_id`=".$uId." AND `exp_period` NOT LIKE '%at_present%' ORDER BY `exp_end_year` DESC, `exp_end_month` DESC  LIMIT 1" );
    }

    function updateUserTitle($title, $uId)
    {
        //$this->query("UPDATE `Profiles` SET `". $this->_sPrefix ."_user_title`='".$title."' WHERE `ID`=".$uId);
        $this->query('UPDATE `Profiles` SET `'. $this->_sPrefix .'_user_title`="'.$title.'" WHERE `ID`='.$uId);
    }
	function updateInlineEdit($updateValues, $uId)
    {
        $this->query("UPDATE `Profiles` SET ".$updateValues." WHERE `ID`=".$uId);
    }

    function removeMedia($iMedia)
    {
        $this->query("DELETE FROM `". $this->_sPrefix ."_medias` WHERE `media_id` = '".$iMedia."'");
    }

    function getMedias($id,$media_for)
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_medias` WHERE `media_for_id` = '".$id."' AND `media_for` = '".$media_for."'");
    }

    function getSettingStatus($setting_name,$userid)
    {
        $status = $this->getOne("SELECT `status` FROM `". $this->_sPrefix ."_user_profile_settings` WHERE `user_id` = '".$userid."' AND `setting_name`= '".$setting_name."'");
        if($status == '')
        {
            if($setting_name == 'is_viewable')
            {
                $this->query('INSERT INTO `'. $this->_sPrefix .'_user_profile_settings` (`user_id`,`setting_name`,`status`) VALUES ("'.$userid.'", "'.$setting_name.'", "public") ');
                return "public";
            }
            else
            {
                $this->query('INSERT INTO `'. $this->_sPrefix .'_user_profile_settings` (`user_id`,`setting_name`,`status`) VALUES ("'.$userid.'", "'.$setting_name.'", "1") ');
                return "1";
            }
        }
        else
            return $status;
    }

    function getUserTitle($uid)
    {
        return $this->getOne("SELECT `". $this->_sPrefix ."_user_title` FROM `Profiles` WHERE `ID` = '".$uid."'");
    }

    function insertProfileSettings($aData, $userid)
    {
        $aSettings = array('picture','headline','currentposition','pastposition','education','skill','language','project','certificate','is_viewable');
        foreach ($aSettings as $setting) {
            $res = $this->getOne("SELECT `status` FROM `". $this->_sPrefix ."_user_profile_settings` WHERE `user_id` = '".$userid."' AND `setting_name`= '".$setting."'");
            if($res == '')
            {
                if($setting == 'is_viewable')
                    $this->query('INSERT INTO `'. $this->_sPrefix .'_user_profile_settings` (`user_id`,`setting_name`,`status`) VALUES ("'.$userid.'", "'.$setting.'", "'.$aData[$setting].'") ');
                else
                {
                    $value = isset($aData[$setting]) ? '1' : '0';
                    $this->query('INSERT INTO `'. $this->_sPrefix .'_user_profile_settings` (`user_id`,`setting_name`,`status`) VALUES ("'.$userid.'", "'.$setting.'", "'.$value.'") ');
                }
            }
            else
            {
                if($setting == 'is_viewable')
                    $this->query('UPDATE `'. $this->_sPrefix .'_user_profile_settings` SET `status` = "'.$aData[$setting].'" WHERE `user_id` = "'.$userid.'" AND `setting_name` = "'.$setting.'"');
                else
                {
                    $value = isset($aData[$setting]) ? '1' : '0';
                    $this->query('UPDATE `'. $this->_sPrefix .'_user_profile_settings` SET `status` = "'.$value.'" WHERE `user_id` = "'.$userid.'" AND `setting_name` = "'.$setting.'"');
                }
            }
        }
    }

    function getMediaById($id)
    {
        return $this->getRow("SELECT * FROM `". $this->_sPrefix ."_medias` WHERE `media_id` = '".$id."'");
    }

    function getExperienceTitles($id)
    {
        return $this->getAll("SELECT `exp_id`,`exp_title` FROM `". $this->_sPrefix ."_experience` WHERE `exp_user_id` = '".$id."'");
    }

    function getEducationTitles($id)
    {
        return $this->getAll("SELECT `edu_id`,`edu_school_name` FROM `". $this->_sPrefix ."_education` WHERE `edu_user_id` = '".$id."'");
    }

    function getLikesForEvent($iWall)
    {
        return $this->getAll("SELECT `user_id` FROM `". $this->_sPrefix ."_wall_likes` WHERE `wall_id` = '".$iWall."'");
    }

    function isUserLikedEvent($iWall, $iUser)
    {
        return $this->getRow("SELECT * FROM `". $this->_sPrefix ."_wall_likes` WHERE `wall_id` = '".$iWall."' AND `user_id` = '".$iUser."'");
    }

    function getUserNames($aUserIds)
    {
        $sUserIds = implode(',',$aUserIds);
        return $this->getAll("SELECT `ID`,`NickName` FROM `Profiles` WHERE `ID` IN ('".$sUserIds."')");
    }

    function updateLikeStatus($aVars)
    {
        $iUser = $aVars['user_id'];
        $iWall = $aVars['wall_id'];
        if($aVars['action'] == 'like')
            $this->query("INSERT INTO `". $this->_sPrefix ."_wall_likes` SET user_id = '".$iUser."',`wall_id` = '".$iWall."'");
        else
            $this->query("DELETE FROM `". $this->_sPrefix ."_wall_likes` WHERE user_id = '".$iUser."' AND `wall_id` = '".$iWall."'");
    }

    //Certificates
    function getCertInfo($iId)
    {
      return $this->getRow("SELECT * FROM `". $this->_sPrefix ."_certificate` WHERE `cert_id` = ".$iId);  
    }

    function getUserCertificates($iId)
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_certificate` WHERE `cert_user_id` = ".$iId." ORDER BY `cert_end_year` DESC, `cert_end_month` DESC");
    }

     function deleteCertificate($iId)
    {
        $this->query("DELETE FROM `". $this->_sPrefix ."_certificate` WHERE `cert_id`=".$iId);
    }

    //Other Language
    function insertProfileinOtherLang($table,$qryToInsert,$uid,$lang_id)
    {
        $status = $this->getOne("SELECT `lang_id` FROM `". $this->_sPrefix ."_profile_other_lang` WHERE `user_id` = '".$uid."' AND `lang_id` = '1'");
        if($status == '')
            $this->insertEnglishNames($uid);

        $status = $this->getOne("SELECT `lang_id` FROM `". $this->_sPrefix ."_profile_other_lang` WHERE `user_id` = '".$uid."' AND `lang_id` = '".$lang_id."'");
           //echo $status = $this->getOne("SELECT `proff_lang_name` FROM `proff_language` WHERE `proff_uid` = '".$uid."' AND `proff_lang_name` = '".$lang_name."'"); exit;     
        if($status == '')
        {
            $this->query("INSERT INTO `". $this->_sPrefix ."_profile_other_lang` SET ". $qryToInsert);
            $this->query("UPDATE `Profiles` SET `selected_language` = '".$lang_id."' WHERE `ID`=".$uid);
            return "1";
        }
        else
        {
         $this->query("UPDATE `". $this->_sPrefix ."_profile_other_lang` SET ".$qryToInsert." WHERE `user_id`='".$uid."' AND `lang_id` = '".$lang_id."'");
         $this->query("UPDATE `Profiles` SET `selected_language` = '".$lang_id."' WHERE `ID`=".$uid);
        }
    }

    function getOtherLangInfo($iId,$lang_id)
    {
        return $this->getRow("SELECT * FROM `". $this->_sPrefix ."_profile_other_lang` WHERE `user_id` = '".$iId."' AND `lang_id` = '".$lang_id."'");
    }

    function removeCert($iCert)
    {
        $this->query("DELETE FROM `". $this->_sPrefix ."_certificate` WHERE `cert_id`=".$iCert);
    }

    function updateEndorseSetting($setting_name, $value, $iUser)
    {
        $this->query("UPDATE `Profiles` SET `". $this->_sPrefix ."_".$setting_name."` = '".$value."' WHERE `ID`=".$iUser);
    }

    function getEndorseSettings($iUser)
    {
        return $this->getRow("SELECT `". $this->_sPrefix ."_endorse_me` as `endorse_me` ,`". $this->_sPrefix ."_endorse_by_friends` as `endorse_by_friends`,`". $this->_sPrefix ."_can_endorse` as `can_endorse`,`". $this->_sPrefix ."_endorse_notification` as `endorse_notification` FROM `Profiles` WHERE `ID` =".$iUser);
    }

    function isFriends($logged_id,$profile_id)
    {
        return $this->getOne("SELECT `ID` FROM `sys_friend_list` WHERE (`ID`='".$logged_id."' AND `Profile`='".$profile_id."') OR (`ID`='".$profile_id."' AND `Profile`='".$logged_id."') AND `Check` = 1");
    }

    function getEndorseData($iSkill)
    {
        return $this->getAll("SELECT * FROM `". $this->_sPrefix ."_endorse_data` WHERE `skill_id`=".$iSkill);
    }

    function isUserEndorsedThisSkill($iSkill,$iUser)
    {
        return $this->getOne("SELECT `endorse_id` FROM `". $this->_sPrefix ."_endorse_data` WHERE `skill_id`='".$iSkill."' AND `endorsed_by`=".$iUser);
    }

    function removeEndorseData($aVars)
    {
        $skill_id = process_db_input($aVars['skill_id']);
        $skill_user_id = process_db_input($aVars['skill_user_id']);
        $endorsed_by = process_db_input($aVars['logged_id']);

        $this->query("DELETE FROM `". $this->_sPrefix ."_endorse_data` WHERE `skill_id`= '".$skill_id."' AND `skill_user_id` = '".$skill_user_id."' AND `endorsed_by` = '".$endorsed_by."'");
    }

    function getProfilelangall($iId)
    {
        return $this->getAll("SELECT `lang_id` FROM `". $this->_sPrefix ."_profile_other_lang` WHERE `user_id` = ".$iId);
    }

    function PublicProfileInfoUpdate($uid,$lang_id)
    {
        return $this->query("UPDATE `Profiles` SET `selected_language`=".$lang_id." WHERE `ID`=".$uid);
    }

    function getUserLatestInstitute($iId)
    {
        return $this->getOne("SELECT `edu_school_name` FROM `". $this->_sPrefix ."_education` WHERE `edu_user_id` = '".$iId."' ORDER BY `edu_completed_year` DESC");
    }

    function updateInlineEditinOtherLang($aData,$iUser,$iLang)
    {
        $this->query("UPDATE `". $this->_sPrefix ."_profile_other_lang` SET ".$aData." WHERE `user_id`='".$iUser."' AND `lang_id` = '".$iLang."'");
    }

    function updateInProfiles($aData,$iUser)
    {
        $this->query("UPDATE `Profiles` SET ".$aData." WHERE `ID` = ".$iUser);
    }

function getUserSummary($iUser)
    {
        return $this->getRow("SELECT `DescriptionMe` as `summary_text` FROM `Profiles` WHERE `ID`='".$iUser."'");
    }

    //Freddy Modif
   // function updateUserSummary($aData)
   function updateUserSummary($aData,$iUser)
    {
        /*$user_id = $aData['summary_userid'];
        $text = $aData['summary_text'];
        $iSummary = $aData['summary_id'];
        if($aData['summary_id'] == '')
            $this->query("INSERT INTO `". $this->_sPrefix ."_summary` SET `summary_userid`= '".$user_id."',`summary_text` = '".$text."'");
        else
            $this->query("UPDATE `". $this->_sPrefix ."_summary` SET `summary_text` = '".$text."' WHERE `summary_id` = '".$iSummary."' AND `summary_userid`= '".$user_id."'");*/

      //Freddy Modif
		/*
		$text = process_db_input($aData['summary_text']);
        $this->query("UPDATE `Profiles` SET `DescriptionMe`= '".$text."'");
		*/
		$text = process_db_input($aData['summary_text']);
        $this->query("UPDATE `Profiles` SET `DescriptionMe`= '".$text."' WHERE `ID`='".$iUser."'");

    }

    function insertEnglishNames($uid)
    {

        $aVals = $this->getRow("SELECT * FROM `Profiles` WHERE `ID`=".$uid);
        $first = $aVals['FirstName'];
        $last = $aVals['LastName'];
        $location = $aVals['City'].','.$aVals['Country'];
        $title = $aVals['abs_plinkin_user_title'];
        $this->query("INSERT INTO `". $this->_sPrefix ."_profile_other_lang` SET `prof_firstname`= '".$first."', `prof_lastname` = '".$last."', `prof_userlocation` = '".$location."', `prof_usertitle` = '".$title."', `lang_id` = '1' , `user_id` = '".$uid."'");
    }

    function updateProfileWithOtherLangs($aVars)
    {
        $first = $aVars['prof_firstname'];
        $last = $aVars['prof_lastname'];
        $title = $aVars['prof_usertitle'];
        $this->query("UPDATE `Profiles` SET `FirstName`= '".$first."', `LastName` = '".$last."', `abs_plinkin_user_title` = '".$title."' 
            WHERE `ID` = ".$aVars['user_id']);
    }

    function getInfoOfThislang($lang_id ,$user_id)
    {
        $row = $this->getRow("SELECT * FROM `". $this->_sPrefix ."_profile_other_lang` WHERE `lang_id`= '".$lang_id."' AND `user_id` = ".$user_id);
        $this->updateProfileWithOtherLangs($row);
    }

    function getSkillName($iSkill)
    {
        $res = $this->getOne("SELECT `skill_id` FROM  `abs_plinkin_user_skills` WHERE `user_skill_id` = ".$iSkill);
        return $this->getOne("SELECT `skill_name` FROM `". $this->_sPrefix ."_skills` WHERE `skill_id` = ".$res);
    }
}
