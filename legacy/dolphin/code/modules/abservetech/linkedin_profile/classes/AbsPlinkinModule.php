<?php
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

require_once dirname(__FILE__).'/../html2pdf/vendor/autoload.php';
require_once dirname(__FILE__).'/res/OpenGraph.php';
require_once dirname(__FILE__).'/res/autoembed.php';

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
//use res\OpenGraph;

bx_import('BxDolModule');

class AbsPlinkinModule extends BxDolModule
{
	var $aMonth = array();
	var $aModuleVars = array();
	/**
     * Constructor
     */
    function AbsPlinkinModule($aModule)
    {
        $aMonth = getPreValues('_abs_plinkin_month');
        foreach ($aMonth as $value) {
            array_push($this->aMonth,_t($value['LKey']));
        }
    	
        parent::BxDolModule($aModule);

        $this->aModuleVars = $aModule;
        $this->_oConfig->init($this->_oDb);
        $this->_oTemplate->init($this);
        $this->_oTemplate->addCss(array('plink.css','bootstrap-editable.css'));
        $this->_oTemplate->addJs(array('plink_module.js','plinkin.js'));
        $GLOBALS['oAdmTemplate']->addJsTranslation(array(
            '_abs_plinkin_basic_Info','_abs_plinkin_error_select_a_language','_abs_plinkin_error_not_his_friend_to_endorse_him','_abs_plinkin_error_can_not_endorse_yourself'
        ));
    }

    function serviceProfileInfo($iId = '')
    {
        if($iId == '')
            $iId = getLoggedId();

    	$oBaseFunctions = bx_instance("BxBaseFunctions");
    	$aProfile = $this->_oDb->getProfile($iId);

        $selected_lang_id = $aProfile['selected_language'];
    	$photo = $oBaseFunctions->getMemberAvatar($sId);
        bx_import('BxDolInstallerUtils');
        if(BxDolInstallerUtils::isModuleInstalled("ebProfileCover"))
    	   $photo = str_replace("thumb", "eb", $photo);

        $usrLocation = $aProfile['City'] .', '.' '._t($GLOBALS['aPreValues']['Country'][$aProfile['Country']]['LKey']);
        $aCompanies = $this->_oDb->getUserPrevCompanies($iId);
        $userLatestAcademyName = $this->_oDb->getUserLatestInstitute($iId);
        $aTmp = array();
        foreach ($aCompanies as $value) {
            $aTmp[] = $value['exp_companyName'];
        }
        $sCompanies = implode(','.' ', $aTmp);
        $user_image = $aProfile['Sex'] == '' || strtolower($aProfile['Sex']) == 'male' ? 'man_big.gif' : 'woman_big.gif';
        if($selected_lang_id == '' || $selected_lang_id == '0')
        {
            if($aProfile['abs_plinkin_user_title'] == '' )
            {
                $aJob = $this->_oDb->getUserCurrentJob($iId);
                if($aJob['exp_title'] == '' && $aJob['exp_companyName'] == '')
                    $usrTitle = '_abs_plink_profile_professional_headline';
                else
                    $usrTitle = $this->UpdateandgetUserTitle($iId);
            }
            else
                $usrTitle = $aProfile['abs_plinkin_user_title'];
        
            $cntry = $aProfile['Country'];
            $aVars = array(
                'Name' => $aProfile['FirstName'] . ' '. $aProfile['LastName'],
                'photo' => $photo == '' ? BX_DOL_URL_ROOT . 'templates/base/images/icons/'.$user_image : $photo,
                'usr_title' => $usrTitle == '_abs_plink_profile_professional_headline' ? _t('_abs_plink_profile_professional_headline') : $usrTitle,
                'usrLocation' => $usrLocation,
                'companies' => $sCompanies == '' ? '<span><a href="javascript:void(0)" data_href="prof_exp_main" id="user_companies" class="scroll_con_edit">'._t('_abs_plinkin_add_experience_empty').'</a>' : '<span>'/* freddy comment ._t("_abs_plink_Previous_Job")*/.'</span><span><a href="javascript:void(0)" data_href="prof_exp_main" id="user_companies" class="scroll_con_edit">'.$sCompanies.'</a>',
                'uid' => getLoggedId(),
                'school_name' => $userLatestAcademyName == '' ? '<span><a href="javascript:void(0)" data_href="prof_edu_main" id="user_companies" class="scroll_con_edit">' . _t('_abs_plinkin_add_education_empty') . '</a>' : '<span>'/*freddy comment . _t('_Education') */.'</span><span><a href="javascript:void(0)" data_href="prof_edu_main" id="user_companies" class="scroll_con_edit">'.$userLatestAcademyName.'</a>',
                'userlink' => getProfileLink(getLoggedId()),
                'action_content' => $photo == '' ? _t('_abs_plinkin_add_avatar') : _t('_abs_plinkin_change_avatar'),
                'class_while_add' => $photo == '' || !BxDolInstallerUtils::isModuleInstalled("ebProfileCover") ? 'addphoto' : '',
                'headline_empty' => $usrTitle == '_abs_plink_profile_professional_headline' ? 'ed-empty' : '',
            );
        }
        else
        {
            $aProfile = $this->_oDb->getOtherLangInfo($iId,$selected_lang_id);
            $aLocaArray = explode(','.' ',$aProfile['prof_userlocation']);
            $usrLocation = $aLocaArray[0] .', '._t($GLOBALS['aPreValues']['Country'][$aLocaArray[1]]['LKey']);
            $cntry = $aLocaArray[1];
            if($aProfile['prof_usertitle'] == '' )
            {
                $aJob = $this->_oDb->getUserCurrentJob($iId);
                if($aJob['exp_title'] == '' && $aJob['exp_companyName'] == '')
                    $usrTitle = '_abs_plink_profile_professional_headline';
                else
                    $usrTitle = $this->UpdateandgetUserTitle($iId,$selected_lang_id);
            }
            else
                $usrTitle = $aProfile['prof_usertitle'];

            $aVars = array(
                'Name' => $aProfile['prof_firstname'] . ' '. $aProfile['prof_lastname'],
                'photo' => $photo == '' ? BX_DOL_URL_ROOT . 'templates/base/images/icons/'.$user_image : $photo,
                'usr_title' => $usrTitle == '_abs_plink_profile_professional_headline' ? _t('_abs_plink_profile_professional_headline') : $usrTitle,
                'usrLocation' => $usrLocation,
                'companies' => $sCompanies == '' ? '<span><a href="javascript:void(0)" data_href="prof_exp_main" id="user_companies" class="scroll_con_edit">'._t('_abs_plinkin_add_experience_empty').'</a>' : '<span>'/*freddy comment ._t("_abs_plink_Previous_Job")*/.'</span><span><a href="javascript:void(0)" data_href="prof_exp_main" id="user_companies" class="scroll_con_edit">'.$sCompanies.'</a>',
                'uid' => getLoggedId(),
                'lang_id'=> $selected_lang_id,
                'school_name' => $userLatestAcademyName == '' ? '<span><a href="javascript:void(0)" data_href="prof_edu_main" id="user_companies" class="scroll_con_edit">' . _t('_abs_plinkin_add_education_empty') . '</a>' : '<span>'/* freddy comment . _t('_Education')*/ .'</span><span><a href="javascript:void(0)" data_href="prof_edu_main" id="user_companies" class="scroll_con_edit">'.$userLatestAcademyName.'</a>',
                'userlink' => getProfileLink(getLoggedId()),
                'action_content' => $photo == '' ? _t('_abs_plinkin_add_avatar') : _t('_abs_plinkin_change_avatar'),
                'class_while_add' => $photo == '' || !BxDolInstallerUtils::isModuleInstalled("ebProfileCover") ? 'addphoto' : '',
                'headline_empty' => $usrTitle == '_abs_plink_profile_professional_headline' ? 'ed-empty' : '',
            );
        }


        $aCountry = getPreValues('Country');
        foreach ($aCountry as $value) {
            $country[] = array(
                'country' => _t($value['LKey']),
                'val_country' => $value['Value'],
                'selected' => $value['Value'] == trim($cntry) ? 'selected' : '',
            );
        }
        $aVars['bx_repeat:countries'] = $country;
    	
    	echo $this->_oTemplate->parseHtmlByName('prof_info.html',$aVars);
    } 


	function serviceProfileSkill($iId = '')
    {
    	echo '<div class="prof_skill_main">'.$this->_ProfileSkill($iId).'</div>';
    }
	
	function serviceProfileLangs($iId = '')
    {
    	echo '<div class="prof_langs_main">'.$this->_ProfileLangs($iId).'</div>';
    }
	
    function serviceProfileExperience($iId = '')
    {
    	echo '<div class="prof_exp_main" id="prof_exp_main">'.$this->_ProfileExperience($iId).'</div>';
    }
	
    public function serviceProfileEducation($iId = '')
    {
        echo '<div class="prof_edu_main" id="prof_edu_main">'.$this->_profileEducation($iId).'</div>';
    }
	
	function _ProfileLangs($iId ='')
    {
		$LanguageProficiency = getPreValues('LanguageProficiency');
		
		$aLangs = $this->_oDb->getUserLanguages(getLoggedId());
		$aInlineVars = array();
		/*var_dump($LanguageProficiency);
		echo '<br><br>';
		var_dump($aLangs);
		exit;*/
		foreach ($aLangs as $aLang) {
			
			$lang_key = $LanguageProficiency[$aLang['Proficiency']]["LKey"];
            $aInlineVars[] = array(
                'lang' => $aLang['lang'],
                'Proficiency' => _t($lang_key)
            );
        }
		
		$aVarsInfo = array(
            'bx_repeat:languages' => $aInlineVars,
            'lang_form' => $this->_getLanguageForm(),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_language_info.html',$aVarsInfo);
        return $res;
	}
	function _ProfileSkill($iId ='')
    {
        $aSkills = $this->_oDb->getUserSkills($iId);
        foreach ($aSkills as $aSkill) {
	       $aSkillVars[] = array(
                'skill_name' => $aSkill['skill_name']
            );
        }

		$aSkills = $this->_oDb->getSkills();
				foreach ($aSkills as $aSkill) {
		$all_skills[] = $aSkill['skill_name'];			
		        }
				//echo json_encode($all_skills);
				
		//$aInlineVars[] = array('skills_string' => $skills_string);
        $aVarsInfo = array(
            'bx_repeat:skills' => $aSkillVars,
			'listskills'=>json_encode($all_skills),
            'skill_form' => $this->_getSkillsForm(),
        );
		
		$this->_oTemplate->addCss(array('bootstrap-tagsinput.css','bootstrap-tagsinput-typeahead.css'));
        $this->_oTemplate->addJs(array('bootstrap-tagsinput.js'));
		
        $res = $this->_oTemplate->parseHtmlByName('prof_skill_info.html',$aVarsInfo);
        return $res;
    }

    function actionViewProfile()
    {
$profileID = !isset($_GET['profile']) ? getID( $_GET['ID'] ) : $_GET['profile'];
$_GET['profile'] = $profileID;
$memberID = getLoggedId();

// make profile view alert and record profile view event
if ($profileID != $memberID) {
    require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolAlerts.php');
    $oAlert = new BxDolAlerts('profile', 'view', $profileID, $memberID);
    $oAlert->alert();

    bx_import ('BxDolViews');
    new BxDolViews('profiles', $profileID);
}
        $this->_oTemplate->pageStart();
        bx_import('BxDolPageView');
        $oPage = new BxDolPageView('public-profile-settings');
        echo $oPage->getCode();
        $this->_oTemplate->addJs(array('view_profile_page.js'));
        $this->_oTemplate->pageCode(_t('_public_profile_settings'), false, false);
    }

	
    function _ProfileExperience($iId ='')
    {
        $aExps = $this->_oDb->getUserExperiences($iId);
		foreach ($aExps as $aExp) {
			$ex_title[$aExp['exp_id']] = $aExp['exp_title'];
		}

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
			foreach($ex_title as $exp_key=>$ex_tit){
				$media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
			}

			$aMedias = $this->_oDb->getMedias($aExp['exp_id'],'experience');
			$aMediaVars = array();
			foreach ($aMedias as $aMedia) {
                if( ($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] !='' )
                    $img_path = $aMedia['media_image_name'];
                elseif(($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] =='')
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/img_not_avail.png';
                else
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/'.$aMedia['media_image_name'];

				$aMediaVars[] = array(
					'media_id' => $aMedia['media_id'],
					'media_title' => $aMedia['media_title'],
					'media_image_path' => $img_path,
				);
			}

            $aInlineVars[] = array(
                'comp_name' => $aExp['exp_companyName'],
                'title' => $aExp['exp_title'],
                'location' => $aExp['exp_location'],
                'desc' => $aExp['exp_desc'],
                'period' => $textPeriod,
                'expid' => $aExp['exp_id'],
				'bx_repeat:media_select' => $media_select,
				'bx_repeat:abs_medias' => $aMediaVars,
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'exp_form' => $this->_getExperienceForm(),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_exp_info.html',$aVarsInfo);
        return $res;
    }

    function _profileEducation($iId = '')
    {
        $aEdus = $this->_oDb->getUserEducations($iId);
		
		foreach ($aEdus as $aEdu) {
			$ed_title[$aEdu['edu_id']] = $aEdu['edu_school_name'];
		}

        foreach ($aEdus as $aEdu) {
            //$textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
			foreach($ed_title as $ed_key=>$ed_tit){
				$media_select[] = array('edu_id'=>$ed_key,'edu_school_name'=>$ed_tit,'ed_selected'=>$ed_key == $aEdu['edu_id'] ? 'selected="selected"' : '');
			}

			$aMedias = $this->_oDb->getMedias($aEdu['edu_id'],'education');
			$aMediaVars = array();
			foreach ($aMedias as $aMedia) {
                if( ($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] !='' )
                    $img_path = $aMedia['media_image_name'];
                elseif(($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] == '')
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/img_not_avail.png';
                else
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/'.$aMedia['media_image_name'];

				$aMediaVars[] = array(
					'media_id' => $aMedia['media_id'],
					'media_title' => $aMedia['media_title'],
					'media_image_path' => $img_path,
				);
			}

            $aInlineVars[] = array(
                'school_name' => $aEdu['edu_school_name'],
               
				// freddy change
			   //  'type' => $this->getEduTypeName($aEdu['edu_type']),
				'bx_if:type' => array( 
								'condition' =>$aEdu['edu_type'],
								'content' => array(
                'type' => $this->getEduTypeName($aEdu['edu_type']),
								), 
							),
				// freddy add 
				// 'type' => $this->getEduTypeName($aEdu['edu_type']),
				'bx_if:NoType' => array( 
								'condition' => !$this->getEduTypeName($aEdu['edu_type']),
								'content' => array(
                'NoType' =>_t('_abs_plinkin_No_Diploma'),
								), 
							),
				
				
               
				// freddy change
			   //   'period' => $aEdu['edu_period'],
				'bx_if:period' => array( 
								'condition' =>$aEdu['edu_period'],
								'content' => array(
                'period' => $aEdu['edu_period'],
								), 
							),
				// freddy add 
				// 'period' => $aEdu['edu_period'],
				'bx_if:NoPeriod' => array( 
								'condition' => !$aEdu['edu_period'],
								'content' => array(
                'NoPeriod' =>_t('_abs_plinkin_view_No_Period'),
								), 
							),
			
				
               
				// freddy change
			   //   'fieldofstudy' => $aEdu['edu_field_of_study'],
				'bx_if:fieldofstudy' => array( 
								'condition' =>$aEdu['edu_field_of_study'],
								'content' => array(
                'fieldofstudy' => $aEdu['edu_field_of_study'],
								), 
							),
				// freddy add 
				// 'fieldofstudy' => $aEdu['edu_field_of_study'],
				'bx_if:No_field_of_study' => array( 
								'condition' => !$aEdu['edu_field_of_study'],
								'content' => array(
                'No_field_of_study' =>_t('_abs_plinkin_No_field_of_study'),
								), 
							),
				
				
				
               
			   // freddy change
			   // 'desc' => $aEdu['edu_desc'],
				'bx_if:desc' => array( 
								'condition' =>$aEdu['edu_desc'],
								'content' => array(
                'desc' => $aEdu['edu_desc'],
								), 
							),
                
				
				// freddy change 
				//'activities' => $aEdu['edu_activities'],
				'bx_if:activities' => array( 
								'condition' =>$aEdu['edu_activities'],
								'content' => array(
                'activities' => $aEdu['edu_activities'],
								), 
							),
							
                'eduid' => $aEdu['edu_id'],
               
			   
			   
			    
				// freddy change 
				//'grade' => $aEdu['edu_grade'] == '' ? '' : _t('_abs_plinkin_view_Grade').' : '.$aEdu['edu_grade'],
				'bx_if:grade' => array( 
								'condition' =>$aEdu['edu_grade'],
								'content' => array(
                'grade' =>$aEdu['edu_grade'] == '' ? '' : _t('_abs_plinkin_view_Grade').' : '.$aEdu['edu_grade'],
								), 
							),
				
				'bx_repeat:media_select' => $media_select,
				'bx_repeat:abs_meds' => $aMediaVars,
            );
        }

        $aVarsInfo = array(
            'bx_repeat:edus' => $aInlineVars,
            'edu_form' => $this->_getEducationForm(),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_education_info.html',$aVarsInfo);
        return $res;
    }
	
	function _getSkillsForm($iSkill = '')
    {
        if($iSkill != '') 
        {
		}
        else
        {
			$aSkills = $this->_oDb->getUserSkills(getLoggedId());
            $skills_string = '';
            foreach ($aSkills as $aSkill) {
    			$skills_string .= $aSkill['skill_name'].',';
            }

            $aEndorseSetting = $this->_oDb->getEndorseSettings(getLoggedId());
    		$skills_string = rtrim($skills_string,',');
                $aVars = array(
                    'usr_id' => getLoggedId(),
                    'skills' => $skills_string,
                    'tobe_endorsed' => $aEndorseSetting['endorse_me'] == '1' ? 'checked' : '',
                    'not_tobe_endorsed' => $aEndorseSetting['endorse_me'] == '0' || $aEndorseSetting['endorse_me'] == '0' ? 'checked' : '',
                    'endorse_by_frd' => $aEndorseSetting['endorse_by_friends'] == '1' ? 'checked' : '',
                    'can_endorse' => $aEndorseSetting['can_endorse'] == '1' ? 'checked' : '',
                    'endorse_notification' => $aEndorseSetting['endorse_notification'] == '1' ? 'checked' : '',
            );
        }
        $this->_oTemplate->addCss(array('bootstrap-tagsinput.css','bootstrap-tagsinput-typeahead.css'));
        $this->_oTemplate->addJs(array('bootstrap-tagsinput.js'));

        return $this->_oTemplate->parseHtmlByName('prof_skill_form.html',$aVars);
    }
	
	function _getLanguageForm($iLang = ''){
		/*global $aPreValues;
		$LanguageProficiency = $aPreValues['Proficiency'];*/

        $LanguageProficiency = getPreValues('LanguageProficiency');

		$aLangs = $this->_oDb->getUserLanguages(getLoggedId());
		$aInlineVars = array();
        $lang_count =0;
		foreach ($aLangs as $aLang) {
			$first_lang = '';
			$lang_count++;
			unset($proficiency);
			foreach($LanguageProficiency as $key=>$langProfi){
				$value = $langProfi['Value'];
				$proficiency[] = array(
                            'proficiency_key' => $langProfi['LKey'],
                            'proficiency_text' => _t($value),
                            'selected' => $langProfi['LKey'] == $aLang['Proficiency'] ? 'selected="selected"' : '',
                        );
			}
			
			if($lang_count==1){
				$first_lang = 'firstlang';
			}
			$aInlineVars[] = array(
                'lang' => $aLang['lang'],
				'first_lang' => $first_lang,
                'bx_repeat:proficiency' => $proficiency
            );
        }

		$new_proficiency = array();
		foreach($LanguageProficiency as $key=>$langProfi){
				$value = $langProfi['Value'];
				$new_proficiency[] = array(
                            'proficiency_key' => $langProfi['LKey'],
                            'proficiency_text' => _t($value),
                            'selected' => $langProfi['LKey'] == $aLang['Proficiency'] ? 'selected="selected"' : '',
                        );
			}
		$aVarsInfo = array(
            'bx_repeat:languages' => $aInlineVars,
			'bx_repeat:newlang' => $new_proficiency,
            'user_id' => getLoggedId(),
        );
		
        return $this->_oTemplate->parseHtmlByName('prof_language_form.html',$aVarsInfo);
	}

    function _getExperienceForm($iExp = '')
    {
        if($iExp != '') 
        {
            $aExp = $this->_oDb->getExpInfo($iExp);

            $aDates = explode('-', $aExp['exp_period']);
            $aStart = explode(',', trim($aDates[0]));
            if(trim($aDates[1]) != 'at present')
                $aEnd = explode(',', trim($aDates[1]));

            foreach ($this->aMonth as $key => $value) {
                $aStMonData[] = array(
                    'month_int' => $key,
                    'month_text' => $value,
                    'selected' => $key == $aStart[0] ? 'selected="selected"' : '',
                );

                /*if(is_array($aEnd))
                {*/
                    /*if(trim($aEnd) != 'at present')
                    {*/
                        $aEndMonData[] = array(
                            'emonth_int' => $key,
                            'emonth_text' => $value,
                            'eselected' => $key == $aEnd[0] ? 'selected="selected"' : '',
                        );
                    //}
                //}
             }

            $aVars = array(
                'comp_name' => $aExp['exp_companyName'],
                'title' => $aExp['exp_title'],
                'location' => $aExp['exp_location'],
                'desc' => $aExp['exp_desc'],
                'st_month' => $aStart[0],
                'st_yr' => trim($aStart[1]),
                'end_month' => trim($aEnd[0]),
                'end_year' => trim($aEnd[1]),
                'id' => $aExp['exp_id'],
                'usr_id' => $aExp['exp_user_id'],
                'bx_if:ishide' => array(
                    'condition' => !is_array($aEnd), //== 'at present',
                    'content' => array(
                        'hide' => 'style="display:none;"',
                        ),
                    ),
                'bx_repeat:stmonths' => $aStMonData,
                'bx_repeat:end_months' => $aEndMonData,
                'checked' => !is_array($aEnd) ? 'checked="checked"' : '',
            );
        }
        else
        {
            foreach ($this->aMonth as $key => $value) {
                $aStMonData[] = array(
                    'month_int' => $key,
                    'month_text' => $value,
                    'selected' => '',
                );

                if(trim($aEnd) != 'at present')
                {
                    $aEndMonData[] = array(
                        'emonth_int' => $key,
                        'emonth_text' => $value,
                        'eselected' => '',
                    );
                }
             }

            $aVars = array(
                'usr_id' => getLoggedId(),
                'bx_repeat:stmonths' => $aStMonData,
                'bx_repeat:end_months' => $aEndMonData,
                'comp_name' => '',
                'title' => '',
                'location' => '',
                'desc' => '',
                'st_yr' => '',
                'end_year' => '',
                'bx_if:ishide' => array(
                    'condition' => 1 != 2,
                    'content' => array(
                        ),
                    ),
            );
        }
        return $this->_oTemplate->parseHtmlByName('prof_exp_form.html',$aVars);
    }

    function _getEducationForm($iEdu = '')
    {
        //$aYears = $this->_oDb->getYears();
        $aEduTypes = getPreValues('schoolDegree');
        
        if($iEdu != '') 
        {
            $aEdu = $this->_oDb->getEduInfo($iEdu);
            $aResYears = explode('-', $aEdu['edu_period']);

            /*foreach ($aEduTypes as $aEduType) {
               $aEduTypeDisp[] = array(
                    'opt_val' => $aEduType['edu_type_id'],
                    'opt_name' => $aEduType['edu_type_name'],
                    'selected' => $aEduType['edu_type_id'] == $aEdu['edu_type'] ? 'selected="selected"' : '',
                );
            }*/

            foreach ($aEduTypes as $aEduType)
            {
                $aEduTypeDisp[] = array(       
                   'opt_val' =>  $aEduType['LKey'],
                   'opt_name' => _t($aEduType['LKey']),
                   'selected' => $aEduType['LKey'] == $aEdu['edu_type'] ? 'selected="selected"' : '',
                 );
            }

            /*foreach ($aYears as $value) {
                    $startYear[] = array(
                        'year' => $value['year'],
                        'selected' => trim($aResYears[0]) == $value['year'] ? 'selected="selected"' : '',
                    );

                    $aEndYear[] = array(
                        'end_year' => $value['year'],
                        'selected' => trim($aResYears[1]) == $value['year'] ? 'selected="selected"' : '',
                    );
            }*/

            $curYear = date('Y');
            $hunYearAgo = (int) $curYear - 100;
            for($i=$curYear;$i>=$hunYearAgo;$i--)
            {
                $startYear[] = array(
                    'year' => $i,
                    'selected' => trim($aResYears[0]) == $i ? 'selected="selected"' : '',
                );

                $aEndYear[] = array(
                    'end_year' => $i,
                    'selected' => trim($aResYears[1]) == $i ? 'selected="selected"' : '',
                );
            }
            $aVars = array(
                //'id' => $aEdu['edu_id'],

                'education_id' => $aEdu['edu_user_id'],
                'usr_id' => $aEdu['edu_id'],
                'bx_repeat:years' => $startYear,
                'bx_repeat:toyears' => $aEndYear,
                'bx_repeat:edutypes' => $aEduTypeDisp,
                'school_name' => $aEdu['edu_school_name'] != '' ? $aEdu['edu_school_name'] : '',
				 'city' => $aEdu['city'] != '' ? $aEdu['city'] : '',
				 'country' => $aEdu['country'] != '' ? $aEdu['country'] : '',
                'desc' => $aEdu['edu_desc'] != '' ? $aEdu['edu_desc'] : '',
                'activities' => $aEdu['edu_activities'] != '' ? $aEdu['edu_activities'] : '',
                'main_subject' => $aEdu['edu_field_of_study'] != '' ? $aEdu['edu_field_of_study'] : '',
                'grade' => $aEdu['edu_grade'] != '' ? $aEdu['edu_grade'] : '',
            );
        }
        else
        {
            foreach ($aEduTypes as $aEduType) {
               $aEduTypeDisp[] = array(       
                   'opt_val' =>  $aEduType['LKey'],
                   'opt_name' => _t($aEduType['LKey']),
                   'selected' => $aEduType['LKey'] == $aEdu['edu_type'] ? 'selected="selected"' : '',
                 );
            }

            $curYear = date('Y');
            $hunYearAgo = (int) $curYear - 100;
            for($i=$curYear;$i>=$hunYearAgo;$i--)
            {
                $startYear[] = array(
                    'year' => $i,
                    'selected' => '',
                );

                $aEndYear[] = array(
                    'end_year' => $i,
                    'selected' => '',
                );
            }

            $aVars = array(
                'education_id' => getLoggedId(),
                'bx_repeat:years' => $startYear,
                'bx_repeat:toyears' => $aEndYear,
                'bx_repeat:edutypes' => $aEduTypeDisp,
                'school_name' => '',
                'desc' => '',
                'activities' => '',
                'main_subject' => '',
                'grade' => '',
            );
        }
        return $this->_oTemplate->parseHtmlByName('prof_education_form.html', $aVars);
    }

    function actionGetexpeditform()
    {
        echo $this->_getExperienceForm($_POST['expid']);
    }

    function actionGetedueditform()
    {
        echo $this->_getEducationForm($_POST['eduid']);   
    }
	
	function actionAddSkill()
    {
		$aVars = $_POST;
		$skill = $_POST['tag'];
		$uid = getLoggedId();
		$this->_oDb->addSkill($skill,$uid);
	}



    function actionServiceGeneratePDF()
     {

        $uid = getLoggedId();
        
        
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $aProfile = $this->_oDb->getProfile($uid);

        $photo = $oBaseFunctions->getMemberAvatar($sId);
        $photo = str_replace("thumb", "eb", $photo);
        $usrTitle = $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($uid) : $aProfile['abs_plinkin_user_title'];
        
        $usrLocation = $aProfile['City'] .', '.' '.$aProfile['Country'];
        $aCompanies = $this->_oDb->getUserPrevCompanies($uid);
        $aTmp = array();
        foreach ($aCompanies as $value) {
            $aTmp[] = $value['exp_companyName'];
        }
        $sCompanies = implode(',', $aTmp);

        //Education    
        $aEdus = $this->_oDb->getUserEducations($uid);  

         foreach ($aEdus as $aEdu) {
            $ed_title[$aEdu['edu_id']] = $aEdu['edu_school_name'];
         }

                /*$aInlineVars[] = array(
                'school_name' => $aEdu['edu_school_name'],
                'type' => $this->_oDb->getEduTypeName($aEdu['edu_type']),
                'period' => $aEdu['edu_period'],
                'fieldofstudy' => $aEdu['edu_field_of_study'],
                'desc' => $aEdu['edu_desc'],
                'activities' => $aEdu['edu_activities'],
                'edu_id' => $aEdu['edu_id'],
                'grade' => 'working on',
                'bx_repeat:media_select' => $media_select,
                'bx_repeat:medias' => $aMediaVars,
            );
        }   */


        //user Experience
          $aExps = $this->_oDb->getUserExperiences($uid);

            foreach ($aExps as $aExp) {
            $ex_title[$aExp['exp_id']] = $aExp['exp_title'];
        }



            
        //skills

        $aSkills = $this->_oDb->getUserSkills($uid);
        $skills_string='';
                foreach ($aSkills as $aSkill) {
        $skills_string .= $aSkill['skill_name'].',';            
                }
        $skills_string=rtrim($skills_string,',');

        $aSkills = $this->_oDb->getSkills();
                foreach ($aSkills as $aSkill) {
        $all_skills[] = $aSkill['skill_name'];          
                }
         
        // user languages
        
        $LanguageProficiency = getPreValues('LanguageProficiency');
        
        $aLangs = $this->_oDb->getUserLanguages(getLoggedId());

        $aInlineVars = array();



        //Basic Information 
        $firstname = $aProfile['FirstName'];
        $lastname = $aProfile['LastName'];
        $gender = $aProfile['Sex'];
        $dob = $aProfile['DateOfBirth'];
        $country = $aProfile['Country'];
        $city = $aProfile['City'];
        $postcode =$aProfile['zip'];
        $relation = $aProfile['RelationshipStatus'];
        $featured = $aProfile['Featured'];




        // printing statements

        $res = "ID =".$uid."<br/>";
        $res .= "USER TITLE =".$usrTitle."<br/>";
       
       
        $res .="USER LOCATION =".$usrLocation."<br/>";
        $res .="PREVIOUS COMP =".$sCompanies."<br/>"; 
        $res .="PHOTO URL=".$photo."<br/>";


        $res .= "<b>Education</b>";
        foreach ($aEdus as $aEdu) {
            //$textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ed_title as $ed_key=>$ed_tit){
                $media_select[] = array('edu_id'=>$ed_key,'edu_school_name'=>$ed_tit,'ed_selected'=>$ed_key == $aEdu['edu_id'] ? 'selected="selected"' : '');

            }

        $res .= "<p>";
        $res .= "INSTITUTE =".$aEdu['edu_school_name']."<br/>";               
        $res .= "BETWEEN =".$aEdu['edu_period']."<br/>";
        $res .= "MAJOR SUB =".$aEdu['edu_field_of_study']."<br/>";

        $res .=  $aEdu['edu_desc']."<br/>";
        $res .= "ACTIVITIES =".$aEdu['edu_activities']."<br/>";
		
        $res .= "</p>";
        }



 
        $res .= "<b>Experience</b>";
        foreach ($aExps as $aExp) {
                    $textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
                    foreach($ex_title as $exp_key=>$ex_tit){
                        $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
                    }
            $res .= "<p>";
            $res .= "COMPANY NAME =".$aExp['exp_companyName']."<br/>";
            $res .= "WORKED AT =".$aExp['exp_title']."<br/>";
            $res .= "ADDRESS =".$aExp['exp_location']."<br/>";
            $res .= "DESCRIPTION =".$aExp['exp_desc']."<br/>";
            $res .= "WORKING PERIOD =".$textPeriod."<br/>";
            
            $res .= "</p>";
        }




    
        $rEmailTemplates .= "<b>Skills</b>";

        $res .= $skills_string;

        $res .= "</br>";



        $res .= "<b>".Languages."</b>";
        foreach ($aLangs as $aLang) {
                    
                    $lang_key = $LanguageProficiency[$aLang['Proficiency']]["LKey"];
                    $aInlineVars[] = array(
                        'lang' => $aLang['lang'],
                        'Proficiency' => _t($lang_key)

                    );
                     $res .= "<br/>";
                     $res .= $aLang['lang'];
                     $res .= $LanguageProficiency[$aLang['Proficiency']]["LKey"];

                    
                } 



        //Basic information print statements
                $res .= "<b>"."<br/>"."Basic Information"."<br/>"."</b>";
                $res .= $firstname."<br/>";
                $res .= $lastname."<br/>";
                $res .= $gender."<br/>";
                $res .= $dob."<br/>";
                $res .= $country."<br/>";
                $res .= $city."<br/>";
                $res .= $postcode."<br/>";
                $res .= $relation."<br/>";
                $res .= $featured."<br/>";



        define('RELATIVE_PATH',BX_DIRECTORY_PATH_MODULES .'abservetech/linkedin_profile/pdf_lib/');
        require_once(BX_DIRECTORY_PATH_MODULES .'abservetech/linkedin_profile/pdf_lib/html2fpdf.php');

        $pdf=new HTML2FPDF();
        $pdf->AddPage();
        $strContent = $res;

           $pdf->WriteHTML($strContent);
        $pdf->Output("sample.pdf",'D');

        echo "success";
                    


}


	
	function actionPostskil()
    {
        $aEndorseCols = array('endorse_me','endorse_by_friends','can_endorse','endorse_notification');
		//$aSkills = $this->_oDb->saveSkills();
		$skills_edit = explode(',',$_POST['skills_edit']);
		$uid = getLoggedId();

		if($skills_edit != '')
		{
			$this->_oDb->updateUserSkills($skills_edit,$uid);
		}

        $aVals = $_POST['val'];
        foreach ($aEndorseCols as $sEndorseCol) {
            if(isset($aVals[$sEndorseCol]))
                $this->_oDb->updateEndorseSetting($sEndorseCol,$aVals[$sEndorseCol],$uid);
            else
                $this->_oDb->updateEndorseSetting($sEndorseCol,'0',$uid);
        }
        echo $this->_ProfileSkill($uid);
	}
	
	function actionPostlangs()
    {
		//$aSkills = $this->_oDb->saveSkills();
		$languages = $_POST['language'];
		//var_dump($languages); exit;
		$proficiency = $_POST['proficiency'];
		$uid = getLoggedId();
		if(!empty($languages)){
			$this->_oDb->updateUserLang($languages,$proficiency,$uid);
		}
		
		/*global $aPreValues;
		$LanguageProficiency = $aPreValues['Proficiency'];*/

        $LanguageProficiency = getPreValues('LanguageProficiency');
		
		$aLangs = $this->_oDb->getUserLanguages(getLoggedId());
		$aInlineVars = array();
		$html = '';
		foreach ($aLangs as $aLang) {
			$lang_key = $LanguageProficiency[$aLang['Proficiency']]["LKey"];
            /*$aInlineVars[] = array(
                'lang' => $aLang['lang'],
                'Proficiency' => _t($lang_key)
            );*/
			
			$html .= '<div class="each_container">
	    <div class="lang_info hover_show">
		<h3 class="edit_lang">'.$aLang['lang'].'</h3><i class="fa fa-pencil edit_lang"></i>
        <p><span class="hover_bor edit_lang">'._t($lang_key).'</span><i class="fa fa-pencil edit_lang"></i></p>
	    </div>
        </div>';
        }
		
		echo $html;

	}

	function actionListSkill()
    {
		$aSkills = $this->_oDb->getSkills();
		foreach ($aSkills as $aSkill) {
        $all_skills[] = $aSkill['skill_name'];			
        }
		echo json_encode($all_skills);
	}

    function actionPostExp()
    {
    	$aVars = $_POST;
    	if($_POST['EndDateMonth'] == '' && $_POST['EndYear'] == '')
        {
    		$end = 'at present';
            $end_year = date("Y");
            $end_month = date("m");
        }
    	else if($_POST['EndDateMonth'] == '' && $_POST['EndYear'] != '') //If user gives only year not month, then we will take it as last month....
    		$_POST['EndDateMonth'] == 12;
    	else if($_POST['EndDateMonth'] != '' && $_POST['EndYear'] == '') //If user gives only month not year, then we will take it as current year....
    		$_POST['EndYear'] == date("Y");
    	else
    		$end = $_POST['EndDateMonth'] . ',' . $_POST['EndYear'];

        $end = $end == '' ?  $_POST['EndDateMonth'] . ',' . $_POST['EndYear'] : $end;
        $end_year = $end_year == '' ? $_POST['EndYear'] : $end_year;
        $end_month = $end_month == '' ? $_POST['EndDateMonth'] : $end_month;

    	$aVars['period'] = $_POST['startDateMonth'] . ',' . $_POST['StartYear'] . ' - '. $end;
        $expid = $aVars['exp_id'];

        //Extra values to add
        $aVars['end_year'] = $end_year;
        $aVars['end_month'] = $end_month;

    	//Remove unwanted post values or add extra to insert before we prepare
    	$aVars = $this->doCustomizeValues($aVars,array('period','companyName','title','location','desc','user_id','end_year','end_month'));

    	//Prepare query to insert values
    	$qryToInsert = $this->prepareQueryToInsert($aVars, 'exp_');

    	//echo $qryToInsert; 
        //table name without prefix
        //if its update, have to give id as last param to update in query format such as `id` = $id
        $qUpdate = $expid == '' || $expid == '__id__' ? '' : "`exp_id`=".$expid;
        $table = 'experience';
        $this->_oDb->insertData($table,$qryToInsert, $qUpdate);
        echo $this->_ProfileExperience(getLoggedId());
    }

    function actionPostedu()
    {
        $aVars = array();
        if( $_POST['startDate'] != '' && $_POST['endDate'] != '')
        {
            if( $_POST['startDate'] > $_POST['endDate'] )
            {
                $error = 'Start year is after the end year';
                return false;
            }

            $aVars['completed_year'] = $_POST['endDate'];
            $aVars['period'] = $_POST['startDate'] .' - '.$_POST['endDate'];
        }

        $edu_id = $_POST['edu_id'];
        $_POST['user_id'] = getLoggedId();
        $aVars = array_merge($aVars,$_POST);
       
	  
	    $aVars = $this->doCustomizeValues($aVars,array('period','school_name','desc','activities','grade','type','user_id','field_of_study','completed_year'));
	
        $qryToInsert = $this->prepareQueryToInsert($aVars, 'edu_');

        $qUpdate = $edu_id == '' || $edu_id == '__usr_id__' ? '' : "`edu_id`=".$edu_id;
        $table = 'education';
        $this->_oDb->insertData($table,$qryToInsert,$qUpdate);
        echo $this->_profileEducation(getLoggedId());
    }

    function prepareQueryToInsert($aVars, $prefix = '')
    {
    	$i = 1;$sQuery = '';
   		foreach ($aVars as $key => $value) {
   			$value = process_db_input($value);
   			$sQuery .= $i == 1 ? '' : ',';
   			$sQuery .= '`'.$prefix.$key.'`="' .$value. '"';
   			$i++;
   		}
   		return $sQuery;
    }

    function doCustomizeValues($aVars,$required_vals = array())
    {
    	$fin_vals = array();
    	foreach ($aVars as $key => $value) {
    		if(in_array($key,$required_vals))
    			$fin_vals[$key] = $value; 
    	}
    	return $fin_vals;
    }

    function getperiodToDisplay($period ='')
    {
    	$aSplitedVars = explode('-',$period);
    	$start =  $aSplitedVars[0];
    	$end = trim($aSplitedVars[1]);
    	$aSplitedStartVars = explode(',',$start);
    	$tStartMonth = $this->getMonthinEnglish(trim($aSplitedStartVars[0]));
    	if($end != 'at present')
    	{
    		$aSplitedEndVars = explode(',',$end);
	    	$tEndMonth = $this->getMonthinEnglish(trim($aSplitedEndVars[0]));
	    	$textPeriod = $this->getPeriodInText($aSplitedStartVars[0],$aSplitedStartVars[1],$aSplitedEndVars[0],$aSplitedEndVars[1]);

	    	return $tStartMonth . ' '. $aSplitedStartVars[1] . '--'.' ' . $tEndMonth . ' '. $aSplitedEndVars[1] . ' (' .$textPeriod . ')';
    	}
    	else
    	{
    		$textPeriod = $this->getPeriodInText($aSplitedStartVars[0],$aSplitedStartVars[1]);
    		// freddy change
			//return $tStartMonth . ' '. $aSplitedStartVars[1] . '-- present (' .$textPeriod . ')';
			return $tStartMonth . ' '. $aSplitedStartVars[1] . '--'.' '. _t('_abs_plinkin_to_present') .'(' .$textPeriod . ')';
    	}
    }

    function getMonthinEnglish($mon)
    {
        /*echo '<pre>';
        var_dump($this->aMonth);
        echo $mon;
        exit;*/
    	return $this->aMonth[$mon];
    }

    function getPeriodInText($st_month,$st_year,$end_month = '',$end_year = '')
    {
        $st_month = (int) $st_month;
        $st_year = (int) $st_year;
        $end_month = (int) $end_month;
        $end_year = (int) $end_year;
    	if($end_month == '' || $end_year == '')
    	{
    		$end_month = date('m');
    		$end_year = date('Y');
    	}

    	if( $end_year == $st_year && $st_month <= $end_month)
		{
			if($st_month == $end_month)
				return 'Now only has joined';

			$Diff = (int)$end_month - (int)$st_month;
    		$months = $Diff == 1 ? $Diff . ' '._t('_abs_plinkin_pdf_months') : $Diff . ' '._t('_abs_plinkin_pdf_months');
    		return $months;
		}
    	else if($end_year > $st_year)
    	{
    		if( $st_month > $end_month )
    		{
	    		$DiffinYears = (int) $end_year - ((int) $st_year+1);
	    		
	    		$PreviousMonthsCount = 12 - $st_month;
	    		$ThisMonthsCount = $end_month;
	    		$totalMonths = $PreviousMonthsCount + $ThisMonthsCount;
                $totalMonths = $totalMonths+1;
	    		$years = $DiffinYears == 0 ? '' : ($DiffinYears == 1 ? $DiffinYears . ' '._t('_abs_plinkin_pdf_years') : $DiffinYears . ' '._t('_abs_plinkin_pdf_years'));
	    		$months = $totalMonths == 0 ? '' : ($totalMonths == 1 ? $totalMonths . ' '._t('_abs_plinkin_pdf_months') : $totalMonths . ' '._t('_abs_plinkin_pdf_months'));
	    		return $years .' '. $months;
	    	}
	    	else if($st_month < $end_month)
	    	{
	    		$DiffinYears = (int) $end_year - (int) $st_year;

	    		$totalMonths = $end_month - $st_month;
                $totalMonths = $totalMonths+1;
	    		$years = $DiffinYears == 0 ? '' : ($DiffinYears == 1 ? $DiffinYears . ' '._t('_abs_plinkin_pdf_years') : $DiffinYears . ' '._t('_abs_plinkin_pdf_years'));
	    		$months = $totalMonths == 0 ? '' : ($totalMonths == 1 ? $totalMonths . ' '._t('_abs_plinkin_pdf_months') : $totalMonths . ' '._t('_abs_plinkin_pdf_months'));
	    		return $years .' '. $months;
	    	}
            else if($st_month == $end_month)
            {
                $DiffinYears = (int) $end_year - (int) $st_year;
                $years = $DiffinYears == 0 ? '' : ($DiffinYears == 1 ? $DiffinYears . ' '._t('_abs_plinkin_pdf_years') : $DiffinYears . ' '._t('_abs_plinkin_pdf_years'));
                return $years .' '._t('_abs_plinkin_pdf_months')  ;
            }
    	}

    	else
    	{
    		return "Invalid Date Given";
    	}
    }

    function actionRemoveexp()
    {
        $expid = $_POST['exp_id'];
        $this->_oDb->deleteExperience($expid);
        echo $this->_ProfileExperience(getLoggedId());
    }

    function actionRemoveedu()
    {
        $expid = $_POST['edu_id'];
        $this->_oDb->deleteEducation($expid);
        echo $this->_profileEducation(getLoggedId());
    }

    function UpdateandgetUserTitle($uId,$lang_id)
    {

        $aJob = $this->_oDb->getUserCurrentJob($uId);
        if($aJob['exp_title'] == '' && $aJob['exp_companyName'] == '')
            return '';

        //$userTitle = $aJob['exp_title'] . _t('_abs_plinkin_headline_at') .$aJob['exp_companyName'];
        $userTitle = $aJob['exp_title'] .' '._t('_abs_plinkin_headline_at') .' '.$aJob['exp_companyName'];

        $this->_oDb->updateUserTitle($userTitle,$uId);
        if($lang_id != '')
        {
            $updateValues = '`prof_usertitle` = "'.$userTitle.'"';
            $this->_oDb->updateInlineEditinOtherLang($updateValues,$uId,$lang_id);
        }
        return $userTitle;
    }

    function getNameChangerForm()
    {
        $aVars = array(
            'fName' => $_POST['fname'],
            'lName' => $_POST['lname'],
        );

        echo $this->_oTemplate->parseHtmlByName('name_changer_form.html',$aVars);
    }

    function actionSaveTitle()
    {
        $postvalue=$_POST['value'];
	$postname=$_POST['name'];
        $aProfile = $this->_oDb->getProfile(getLoggedId());

        $selected_lang_id = $aProfile['selected_language'];
        /*if($selected_lang_id == 0 || $selected_lang_id == '')
        {*/
    		if(is_array($_POST['value'])){
    			$arrayupdateValues = '';
    			foreach($_POST['value'] as $key=>$postval){
    				$arrayupdateValues .= '`'.$key.'`="'.$postval.'",';
    			}
    			$updateValues = rtrim($arrayupdateValues,',');
    		}else{
    			$updateValues= '`'.$postname.'`="'.$postvalue.'"';
    		}
            $this->_oDb->updateInlineEdit($updateValues,$_POST['pk']);
        /*}
        else
        {*/
            switch ($_POST['name']) {
                case 'username':
                    $updateValues = '`prof_firstname` = "'.$_POST['value']['FirstName'].'",`prof_lastname` = "'.$_POST['value']['LastName'].'"';
                    $updateValues2 = '`FirstName` = "'.$_POST['value']['FirstName'].'",`LastName` = "'.$_POST['value']['LastName'].'"';
                    break;
                case 'abs_plinkin_user_title':
                    $updateValues = '`prof_usertitle` = "'.$_POST['value'].'"';
                    $updateValues2 = '`abs_plinkin_user_title` = "'.$_POST['value'].'"';
                    break;
                case 'user_location':
                    $val = trim($_POST['value']['City']).','.trim($_POST['value']['Country']);
                    $updateValues = '`prof_userlocation` = "'.$val.'"';
                    $updateValues2 = '`Country` = "'.trim($_POST['value']['Country']).'",`City` = "'.trim($_POST['value']['City']).'"';
                    break;
            }
            $this->_oDb->updateInlineEditinOtherLang($updateValues,$_POST['pk'],$selected_lang_id);
            $this->_oDb->updateInProfiles($updateValues2,$_POST['pk']);
        //}
    }

    function actionAddMedia()
    {
        $aData = $_POST;

        if( ( $aData['media_type'] == 'link' || $aData['media_type'] == 'video') && !isset($_POST['media_image_name']) )
        {

        	$aData['media_name'] = $aData['upload_link'];
        	$this->uploadLink($aData);
        }
        else
        {

        	switch($aData['upload_type'])
	        {
	            case 'link':
	                $this->uploadMediaByUrl($aData);
	            break;
	            case 'browse':
	                $this->uploadMediaByFile($aData);
	            break;
	            case 'browse_done':
	            	$this->doupdateDb($aData);
	        }
        }
        
    }

    function uploadLink($aData)
    {
        $aTags = $this->getUrlData($aData['media_name']); 
        $image_url=""; 
        $media_title="";
        $media_desc="";
        if(($aData['media_type']=='video' or $aData['media_type']=='link' )and $aData['upload_type']=='link')
        {

            $graph = OpenGraph::fetch($aData['upload_link']);
            $datas=array();
            if(is_object($graph) or is_array($graph))
            {
                foreach ($graph as $key => $value) {
                    $datas[$key]=$value;
                }
                $image_url=$datas['image'];
                $media_title=$datas['title'];
                $media_desc=$datas['description'];
            }
            if($image_url=="")
            {
                $parsed     = parse_url($aData['upload_link']);
                $hostname   = $parsed['host'];  
                $query      = $parsed['query']; 
                $path       = $parsed['path']; 
                $Arr = explode('v=',$query);
                if(count($Arr)>0)
                {
                    $videoIDwithString = $Arr[1];
                    $videoID = substr($videoIDwithString,0,11);
                    if( (isset($videoID)) && (isset($hostname)) && ($hostname=='www.youtube.com' || $hostname=='youtube.com')){
                        $image_url='https://img.youtube.com/vi/'.$videoID.'/0.jpg';

                    }
                }
                
            }
            

        }
        if($image_url=="")
        $aData['image_path'] = $image_url !="" ? $image_url : $aTags['image'] != '' ? $aTags['image'] : ( $aTags['metaTags']['image']['value'] != '' ? $aTags['metaTags']['image']['value'] : ( $aTags['metaTags']['twitter:image']['value'] != '' ? $aTags['metaTags']['twitter:image']['value'] : ( $aTags['metaProperties']['og:image']['value'] != '' ? $aTags['metaProperties']['og:image']['value'] : '' )));
        else
        $aData['image_path'] =$image_url;

        if($media_title=="")
        $aData['media_title'] = $aTags['title'] != '' ? $aTags['title'] : ( $aTags['metaTags']['title']['value'] != '' ? $aTags['metaTags']['title']['value'] : ( $aTags['metaTags']['twitter:title']['value'] != '' ? $aTags['metaTags']['twitter:title']['value'] : ( $aTags['metaProperties']['og:title']['value'] != '' ? $aTags['metaProperties']['og:title']['value'] : '' ) ));
        else
        $aData['media_title'] =$media_title;
         
        if($media_desc=="") 
        $aData['media_desc'] = $aTags['description'] != '' ? $aTags['description'] : ( $aTags['metaTags']['description']['value'] != '' ? $aTags['metaTags']['description']['value'] : ( $aTags['metaTags']['twitter:description']['value'] != '' ? $aTags['metaTags']['twitter:description']['value'] : ( $aTags['metaProperties']['og:description']['value'] != '' ? $aTags['metaProperties']['og:description']['value'] : '' ) ));
        else
        $aData['media_desc'] =$media_desc;

        $aData['media_file_type'] = 'link';
        $aData['media_pages_count'] = 1;
        echo json_encode( array( 'iserror' => 'none', 'result' => $this->getImageForm($aData) ) );
    }

    function uploadMediaByUrl($aData)
    {
    	$url = trim($aData["upload_link"]); 
		if($url)
		{ 
			$file = fopen($url,"rb"); 
			if($file)
			{ 
				$directory = BX_DIRECTORY_PATH_MODULES . $this->aModuleVars['path'] . "media_uploads/";
				// Directory to upload files to. 
				$valid_exts = array("jpg","jpeg","gif","png","pdf","pptx","docx","xlsx","xls","doc","ppt"); 
                $doc_array = array("pptx","docx","xlsx","xls","doc","ppt");
				// default image only extensions 
				$ext = end(explode(".",strtolower(basename($url)))); 
				if(in_array($ext,$valid_exts))
				{ 
					$rand = rand(1000,9999); 
					$filename = $rand . basename($url);
					$aData['media_name'] = $filename;
					$newfile = fopen($directory . $filename, "wb"); 
					// creating new file on local server 

					chmod($directory . $filename, 0777);
					if($newfile)
					{ 
						while(!feof($file)){ 
							// Write the url file to the directory. 
							fwrite($newfile,fread($file,1024 * 8),1024 * 8); 
							// write the file to the new directory at a rate of 8kb/sec. until we reach the end. 
						}
                        if($ext == 'pdf')
                        {
                            list($imgName, $page_count ) = $this->grabImageFromPdf($directory, $filename);
                            $aData['media_image_name'] = $imgName;
                            $aData['media_file_type'] = $ext;
                            $aData['media_pages_count'] = $page_count;
                        }
                        elseif(in_array($ext,$doc_array))
                        {
                            $convertedFileName = $this->convertFileToPDF($directory, $filename);
                            list($imgName, $page_count ) = $this->grabImageFromPdf($directory, $convertedFileName);
                            $aData['media_image_name'] = $imgName;
                            $aData['media_file_type'] = $ext;
                            $aData['media_pages_count'] = $page_count;
                        }
                        else
                        {
                            $aData['media_image_name'] = $filename;
                            $aData['media_file_type'] = $ext;
                            $aData['media_pages_count'] = 1;
                        }
						$status = "ok"; 
					} 
					else 
					{ 
						$status = 'Technical Error. Contact system administrator';//'Could not establish new file ('.$directory.$filename.') on local server. Be sure to CHMOD your directory to 777.'; 
					} 
				} 
				else 
				{ 
					$status = 'Invalid file type. Please try another file.'; 
				} 
			} 
			else 
			{ 
				$status = 'Could not locate the file: '.$url.''; 
			} 
		} 
		else 
		{ 
			$status = 'Invalid URL entered. Please try again.'; 
		}

		if($status != '')
		{
			if($status == 'ok')
			{
				echo json_encode( array( 'iserror' => 'none', 'result' => $this->getImageForm($aData) ) );
				//$this->updateDb($aData);
				//echo 'File uploaded successfully! You can access the file here:'."\n"; 
				//echo ''.$directory.$filename.'';
			}
			else
			{
				echo $status;
			}
		}
    }

    function updateDb($aData)
    {
    	$aData['user_id'] = getLoggedId();
    	$aData['updated_on'] = time();

    	$aVars = $this->doCustomizeValues($aData,array('media_type','media_name','media_for_id','user_id','updated_on','media_for','media_title','media_desc','media_image_name','media_file_type','media_pages_count'));

    	$qryToInsert = $this->prepareQueryToInsert($aVars);

        if($aData['media_id'])
            $qUpdate = "`media_id` = ".$aData['media_id'];
        else
            $qUpdate = '';

    	$this->_oDb->insertData('medias',$qryToInsert, $qUpdate);
    }

    function uploadMediaByFile($aData)
    {
    	$aFile = $_FILES['upload_file'];
        $file_types = array('application/vnd.openxmlformats-officedocument.presentationml.presentation','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/msword','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.openxmlformats-officedocument.spreadsheetml.template','application/vnd.ms-excel','application/vnd.ms-powerpoint');
    	$img_name = $aFile['name'];
    	$tmp_name = $aFile['tmp_name'];
        $ImageExt = substr($img_name, strrpos($img_name, '.'));
        $ImageExt = str_replace('.','',$ImageExt);
    	$upload_directory = BX_DIRECTORY_PATH_MODULES . $this->aModuleVars['path'] . "media_uploads/";

    	$rand = time(); 
		$img_name = $rand;
    	move_uploaded_file($tmp_name, $upload_directory . $img_name .'.'.$ImageExt);
        if($aFile['type'] == 'application/pdf')
        {
            list($imgName, $page_count ) = $this->grabImageFromPdf($upload_directory, $img_name .'.'.$ImageExt);
            $aData['media_image_name'] = $imgName;
            $aData['media_file_type'] = $ImageExt;
            $aData['media_pages_count'] = $page_count;
        }
        elseif(in_array($aFile['type'],$file_types))
        {
            $convertedFileName = $this->convertFileToPDF($upload_directory, $img_name .'.'.$ImageExt);
            list($imgName, $page_count ) = $this->grabImageFromPdf($upload_directory, $convertedFileName);
            $aData['media_image_name'] = $imgName;
            $aData['media_file_type'] = $ImageExt;
            $aData['media_pages_count'] = $page_count;
        }
        else
        {
            $aData['media_image_name'] = $img_name.'.'.$ImageExt;
            $aData['media_file_type'] = $ImageExt;
            $aData['media_pages_count'] = 1;
        }

    	$aData['media_name'] = $aFile['name'];
    	echo json_encode( array( 'iserror' => 'none', 'result' => $this->getImageForm($aData) ) );
    }

    function getImageForm($aData)
    {
        if( ( $aData['media_type'] == 'link' || $aData['media_type'] == 'video') && $aData['image_path'] == '')
        {
            $aData['media_image_name'] = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/img_not_avail.png';
            $aData['media_image_path'] = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/img_not_avail.png';
        }
        else if( ( $aData['media_type'] == 'link' || $aData['media_type'] == 'video') && $aData['image_path'] != '')
        {
            $aData['media_image_name'] = $aData['image_path'];
            $aData['media_image_path'] = $aData['image_path'];
        }
        else
        {
    	   $aData['media_image_name'] = $aData['media_image_name']; //BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/'.
           $aData['media_image_path'] = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/'.$aData['media_image_name'];
        }
    	$aData['media_title'] = !isset($aData['media_title']) || $aData['media_title'] == '' ? '' : $aData['media_title'];
    	$aData['media_desc'] = !isset($aData['media_desc']) || $aData['media_desc'] == '' ? '' : $aData['media_desc'];
        $tmpl = $aData['edit_mode'] == 'yes' ? 'media_edit_form.html' : 'media_save_form.html';
        if($aData['edit_mode'] == 'yes')
        {
            if( $aData['media_for'] == 'experience')
            {
                $aTitles = $this->_oDb->getExperienceTitles(getLoggedId());

                foreach ($aTitles as $aTitle) {
                    $aTits[] = array(
                        'id' => $aTitle['exp_id'],
                        'value' => $aTitle['exp_title'],
                        'select' => $aTitle['exp_id'] == $aData['media_for_id'] ? 'selected' : '',
                        );
                }
            }
            else
            {
                $aTitles = $this->_oDb->getEducationTitles(getLoggedId());

                foreach ($aTitles as $aTitle) {
                    $aTits[] = array(
                        'id' => $aTitle['edu_id'],
                        'value' => $aTitle['edu_school_name'],
                        'select' => $aTitle['edu_id'] == $aData['media_for_id'] ? 'selected' : '',
                        );
                }
            }

            $aData['bx_if:require_file_upload'] = array(
                'condition' => ($aData['media_type'] == 'link' || $aData['media_type'] == 'video') && $aData['image_path'] == '' ,
                'content' => array(
                    ),
                );

            $aData['bx_repeat:lists'] = $aTits;
        }

    	return $this->_oTemplate->parseHtmlByName($tmpl,$aData);
    }

    function doupdateDb($aData)
    {
        if( !empty($_FILES['upload_file']) )
        {
            $aFile = $_FILES['upload_file'];
            $img_name = $aFile['name'];
            $tmp_name = $aFile['tmp_name'];
            $upload_directory = BX_DIRECTORY_PATH_MODULES . $this->aModuleVars['path'] . "media_uploads/";

            $rand = rand(1000,9999); 
            $img_name = $rand . $img_name;
            move_uploaded_file($tmp_name, $upload_directory . $img_name);
            $aData['media_name'] = $img_name;
            $aData['media_image_name'] = $img_name;
        }

    	$this->updateDb($aData);
    	echo $aData['media_for'] == 'experience' ? $this->_ProfileExperience(getLoggedId()) : $this->_ProfileEducation(getLoggedId());
    }

    function actionRemoveMedia()
    {
    	error_reporting(0);
    	if( $_POST['media_id'] != '' )
    		$this->_oDb->removeMedia($_POST['media_id']);

    	unlink(BX_DIRECTORY_PATH_MODULES . $this->aModuleVars['path'] . "media_uploads/".$_POST['img_name']);
    	echo $_POST['media_for'] == 'experience' ? $this->_ProfileExperience(getLoggedId()) : $this->_ProfileEducation(getLoggedId());
    }

    function actionPublicProfileSettings()
    {
        $aVars = array(
            'user_id' => $_GET['ID'],
            );

        echo $this->_oTemplate->parseHtmlByName('profile_settings.html',$aVars);
    }





  function actionPicture()
 {
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $photo = $oBaseFunctions->getMemberAvatar($sId);
        $photo = str_replace("thumb", "eb", $photo);


         $aVars = array(
            'photo' => $photo,
            );

        echo $this->_oTemplate->parseHtmlByName('prof_picture.html',$aVars);
 }

/*function actionHeadLine()
{

    $uid = getLoggedId();
    $usrTitle = $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($uid) : $aProfile['abs_plinkin_user_title'];
  
    echo $this->_oTemplate->parseHtmlByName('profile_settings.html',$usrTitle);


  }
*/

  function actionHeadline()
   {

    $uid = getLoggedId();
    $usrTitle = $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($uid) : $aProfile['abs_plinkin_user_title'];
    
    $aVars = array(
            'usertitle' => $usrTitle,
            );


    echo $this->_oTemplate->parseHtmlByName('prof_currentposition.html',$aVars);

   }


  function actionPastPositions()
  {
        $uid = getLoggedId();
        $aExps = $this->_oDb->getUserExperiences($uid);
        foreach ($aExps as $aExp) {
            $ex_title[$aExp['exp_id']] = $aExp['exp_title'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
            }

            $aInlineVars[] = array(
                'comp_name' => $aExp['exp_companyName'],
                'title' => $aExp['exp_title'],
                'location' => $aExp['exp_location'],
                'desc' => $aExp['exp_desc'],
                'period' => $textPeriod,
                'expid' => $aExp['exp_id'],
                'pos_class' => strpos($aExp['exp_period'], 'present') !== false ? 'current_position' : 'past_position',
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_pastposition.html',$aVarsInfo);
        return $res ;  


        } 

     function serviceLanguages()
      {
        $uid = $_GET['profile'];
        $lang_status = $this->_oDb->getSettingStatus('language',$uid);
        $queryStirng = $_GET['r'];
        $arr = explode('/', $queryStirng);
        if( $arr[1] == 'viewprofile')
        {
            $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
            if($is_viewable == 'private' || $lang_status != '1')
                return '';
        }

        $LanguageProficiency = getPreValues('LanguageProficiency');
        
        $aLangs = $this->_oDb->getUserLanguages(getLoggedId());
        $aInlineVars = array();
        /*var_dump($LanguageProficiency);
        echo '<br><br>';
        var_dump($aLangs);
        exit;*/

        if(empty($aLangs))
            return '';

        foreach ($aLangs as $aLang) {
            
            $lang_key = $LanguageProficiency[$aLang['Proficiency']]["LKey"];
            $aInlineVars[] = array(
                'lang' => $aLang['lang'],
                'Proficiency' => _t($lang_key)
            );
        }
        
        $aVarsInfo = array(
            'bx_repeat:languages' => $aInlineVars,
            'style' => $lang_status == '1' ? '' : ($lang_status == '0' ? 'hideit' : 'showit' ),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_languages.html',$aVarsInfo);
        return $res;
    }

    
     
       function serviceEducation()
       {
            $uid = $_GET['profile'];
            $education_status = $this->_oDb->getSettingStatus('education',$uid);
            $queryStirng = $_GET['r'];
            $arr = explode('/', $queryStirng);
            if( $arr[1] == 'viewprofile')
            {
                $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
                if($is_viewable == 'private' || $education_status != '1') 
                    return '';
            }

           $aEdus = $this->_oDb->getUserEducations($uid);
            
            if(empty($aEdus))
                return '';

           foreach ($aEdus as $aEdu) {
            $ed_title[$aEdu['edu_id']] = $aEdu['edu_school_name'];
           }

            foreach ($aEdus as $aEdu) {
            //$textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ed_title as $ed_key=>$ed_tit){
                $media_select[] = array('edu_id'=>$ed_key,'edu_school_name'=>$ed_tit,'ed_selected'=>$ed_key == $aEdu['edu_id'] ? 'selected="selected"' : '');
            }

            

            $aInlineVars[] = array(
                'school_name' => $aEdu['edu_school_name'],
               
               
			    // freddy change
			   //   'type' => $this->getEduTypeName($aEdu['edu_type']),
				'bx_if:type' => array( 
								'condition' =>$aEdu['edu_type'],
								'content' => array(
                'type' => $this->getEduTypeName($aEdu['edu_type']),
								), 
							),
                 
				 
				 // freddy change
			   //   'period' => $aEdu['edu_period'],
				'bx_if:period' => array( 
								'condition' =>$aEdu['edu_period'],
								'content' => array(
                'period' => $aEdu['edu_period'],
								), 
							),
               
			   // freddy change
			   //  'fieldofstudy' => $aEdu['edu_field_of_study'],
				'bx_if:fieldofstudy' => array( 
								'condition' =>$aEdu['edu_field_of_study'],
								'content' => array(
                'fieldofstudy' => $aEdu['edu_field_of_study'],
								), 
							),
               
				
				
				// freddy change
			   //   'desc' => $aEdu['edu_desc'],
				'bx_if:desc' => array( 
								'condition' =>$aEdu['edu_desc'],
								'content' => array(
                'desc' => $aEdu['edu_desc'],
								), 
							),
               
				
				// freddy change
			   //  'activities' => $aEdu['edu_activities'],
				'bx_if:activities' => array( 
								'condition' =>$aEdu['edu_activities'],
								'content' => array(
               'activities' => $aEdu['edu_activities'],
								), 
							),
                
				// freddy change
			   // 'grade' => $aEdu['edu_grade'] == '' ? '' : $aEdu['edu_grade'] . ' Grade',
				'bx_if:grade' => array( 
								'condition' =>$aEdu['edu_grade'],
								'content' => array(
                'grade' => $aEdu['edu_grade'] == '' ? '' : $aEdu['edu_grade'] . ' Grade',
								), 
							),
				
            );
        }

        $education_status = $this->_oDb->getSettingStatus('education',$uid);
        $aVarsInfo = array(
            'bx_repeat:edus' => $aInlineVars,
            'style' => $education_status == '1' ? 'showit' : ($education_status == '0' ? 'hideit' : 'showit'),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_education.html',$aVarsInfo);
        return $res;
    }

function actionProfile()
{
 
        $uid = getLoggedId();
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $aProfile = $this->_oDb->getProfile($uid);

        $photo = $oBaseFunctions->getMemberAvatar($sId);
        $photo = str_replace("thumb", "eb", $photo);
        $usrTitle = $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($uid) : $aProfile['abs_plinkin_user_title'];
        
       //freddy change  $usrLocation = $aProfile['City'] .', '.' '.$aProfile['Country'];
		$usrLocation = $aProfile['City'] .', '.' '._t($GLOBALS['aPreValues']['Country'][$aProfile['Country']]['LKey']);
	   
        $aCompanies = $this->_oDb->getUserPrevCompanies($uid);
        $userLatestAcademyName = $this->_oDb->getUserLatestInstitute($uid);
        $aTmp = array();
        foreach ($aCompanies as $value) {
            $aTmp[] = $value['exp_companyName'];
        }
        $sCompanies = implode(','.' ', $aTmp);
        $aVars = array(
            'Name' => $aProfile['FirstName'] . ' '. $aProfile['LastName'],
            'photo' => $photo,
            'usr_title' => $usrTitle,
            'usrLocation' => $usrLocation,
            'companies' => $sCompanies,
            'uid' => getLoggedId(),
            'school_name' => $userLatestAcademyName,
        );

        echo $this->_oTemplate->parseHtmlByName('prof_profile.html',$aVars);

}



    function serviceSkills()
    {
        $uid = $_GET['profile'];
        $skill_status = $this->_oDb->getSettingStatus('skill',$uid);
        $queryStirng = $_GET['r'];
        $arr = explode('/', $queryStirng);
        if( $arr[1] == 'viewprofile')
        {
            $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
            if($is_viewable == 'private' || $skill_status != '1')
                return '';

            $aProfileUserEndorseSettings = $this->_oDb->getEndorseSettings($_GET['profile']);
            $aLoggedUserEndorseSettings = $this->_oDb->getEndorseSettings(getLoggedId());
            if(getLoggedId() == $_GET['profile'])
            {
                if($aLoggedUserEndorseSettings['endorse_me'] == '1' && $aLoggedUserEndorseSettings['endorse_by_friends'] == '1')
                    return $this->viewSkills($_GET['profile']);
            }
            else if(getLoggedId() != $_GET['profile'])
            {
                $isFriend = $this->_oDb->isFriends(getLoggedId(),$_GET['profile']);
                if($aProfileUserEndorseSettings['endorse_by_friends'] == '1' && $aProfileUserEndorseSettings['endorse_me'] == '1' && $aLoggedUserEndorseSettings['can_endorse'] == '1')
                    return $this->viewSkills($_GET['profile'], $isFriend);
            }
        }

        $uid = $_GET['profile'];
        $aSkills = $this->_oDb->getUserSkills($uid);
        if(empty($aSkills))
            return '';

        foreach ($aSkills as $aSkill) 
        {
            $skills_array[] = $aSkill['skill_name'];    
        }

        $arrlength = count($skills_array);
        for($x = 0; $x <  $arrlength; $x++) {
            $aInlineVars[] = array(
                'skills' =>  $skills_array[$x],       
            );
        }

        $skill_status = $this->_oDb->getSettingStatus('skill',$uid);

        $aVarsInfo = array(
            'bx_repeat:edus' => $aInlineVars,
        );

        $style = $skill_status == '1' ? 'showit' : ($skill_status == '0' ? 'hideit' : 'showit');
        
        return '<div class="skill_display_area '.$style.'" data-div="skill_display_area">'.$this->_oTemplate->parseHtmlByName('prof_skills.html',$aVarsInfo).'</div>';
    }

    function servicePublicSettingProject()
    {
        $iId = $_GET['profile'];
        $project_status = $this->_oDb->getSettingStatus('project',$iId);
        $queryStirng = $_GET['r'];
        $arr = explode('/', $queryStirng);
        if( $arr[1] == 'viewprofile')
        {
            $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$iId);
            if($is_viewable == 'private'  || $project_status != '1')
                return '';
        }

        $aExps = $this->_oDb->getUserProjects($iId);
        if(empty($aExps))
            return '';

        foreach ($aExps as $aExp) {
            $ex_title[$aExp['pro_id']] = $aExp['pro_name'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['pro_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
            }

            $role = explode('-',$aExp['pro_occupation']);
            if(strtolower($role[0]) == 'student')
                $sRole = _t('_abs_plinkin_'.$role[0]) .' '._t('_abs_plinkin_at') .' '.$role[1];
            else
               //freddy change $sRole = $role[0] .' '._t('_abs_plinkin_at') .' '.$role[1];
			   $sRole = $role[0] .' '.'<i class="sys-icon certificate"></i>' .' '.$role[1];


            $aInlineVars[] = array(
                'projectname' => $aExp['pro_name'],
                'desc' => $aExp['pro_desc'],
                'period' => $textPeriod,
                'id' => $aExp['pro_id'],
               
                'projectOccupation'=> $sRole,
				
				 // freddy change  'projectTeam' => $aExp['pro_team'],
				'bx_if:projectTeam' => array( 
								'condition' =>$aExp['pro_team'],
								'content' => array(
                'projectTeam' => $aExp['pro_team'],
								), 
							),
				'projectOccupation'=> $sRole,
                
				
				// freddy change  'projectUrl' => $aExp['pro_url'],
				'bx_if:projectUrl' => array( 
								'condition' =>$aExp['pro_url'],
								'content' => array(
                'projectUrl' => $aExp['pro_url'],
								), 
							),
				
               
                'end_month' =>$aExp['pro_end_month'],
                'end_year' => $aExp['pro_end_year'],
                'usr_id' =>$aExp['pro_user_id'],
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'style' => $project_status != '1' ? 'hideit' : 'showit', 
        );
        $res = $this->_oTemplate->parseHtmlByName('public_project_setting.html',$aVarsInfo);
        return $res;
    }

    function servicePublicSettingCertificates()
    {
        $iId = $_GET['profile'];
        $cert_status = $this->_oDb->getSettingStatus('certificate',$iId);
        $queryStirng = $_GET['r'];
        $arr = explode('/', $queryStirng);
        if( $arr[1] == 'viewprofile')
        {
            $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$iId);
            if($is_viewable == 'private' || $cert_status != '1')
                return '';
        }

        $aExps = $this->_oDb->getUserCertificates($iId);

        if(empty($aExps))
            return '';

        foreach ($aExps as $aExp) {
            $ex_title[$aExp['cert_id']] = $aExp['cert_name'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['cert_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('cert_id'=>$exp_key,'cert_title'=>$ex_tit,'cert_selected'=>$exp_key == $aExp['cert_id'] ? 'selected="selected"' : '');
            }

            

            $aInlineVars[] = array(
                'certificatename' => $aExp['cert_name'],
				 'period' => $textPeriod,
				  'id' => $aExp['cert_id'],
                
				// freddy change 'certificateauthority' => $aExp['cert_authority'],
				'bx_if:certificateauthority' => array( 
								'condition' =>$aExp['cert_authority'],
								'content' => array(
                'certificateauthority' => $aExp['cert_authority'],
								), 
							),
               
                // freddy change 'certificatelicense' => $aExp['cert_license'],
				'bx_if:certificatelicense' => array( 
								'condition' =>$aExp['cert_license'],
								'content' => array(
                'certificatelicense' => $aExp['cert_license'],
								), 
							),
				
                // fredd change 'certificateurl'=> $aExp['cert_url'],
			    'bx_if:certificateurl' => array( 
								'condition' =>$aExp['cert_url'],
								'content' => array(
                'certificateurl' => $aExp['cert_url'],
								), 
							),
				
                'end_month' =>$aExp['cert_end_month'],
                'end_year' => $aExp['cert_end_year'],
                'usr_id' =>$aExp['cert_user_id'],
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'style' => $cert_status != '1' ? 'hideit' : 'showit', 
        );
        $res = $this->_oTemplate->parseHtmlByName('public_certificate_setting.html',$aVarsInfo);
        return $res;
    }

    function viewSkills($iUser = '', $isFriend = '')
    {
        return '<div class="skill_display_area">'.$this->_viewskills($iUser,$isFriend).'</div>';
    }

    function _viewskills($iUser = '', $isFriend = '')
    {
        if($iUser)
            MsgBox("No User Found");

        $aSkills = $this->_oDb->getUserSkills($iUser);
        if(empty($aSkills))
        	return '';

        foreach ($aSkills as $key => $value) {
            $aEndorseDatas = $this->_oDb->getEndorseData($value['user_skill_id']);
            if(!empty($aEndorseDatas))
            {	
                $i=0;
                $aEndVars = array();
                foreach ($aEndorseDatas as $aEndorseData) {
                    $i++;
                    $aEndVars[] = array(
                        'endorser_name' => getNickName($aEndorseData['endorsed_by']),
                    );
                }
            }

            $aSkills[$key]['bx_if:isendorses'] = array(
                'condition' => !empty($aEndorseDatas),
                'content' => array(
                    'bx_repeat:endorses' => $aEndVars,
                    'endorses_count' => $i,
                )
            );
            if($iUser != getLoggedId())
            {
                $iUserEndorsed = $this->_oDb->isUserEndorsedThisSkill($value['user_skill_id'],getLoggedId());
                $aSkills[$key]['icon_class'] = $iUserEndorsed == '' ? 'fa-plus-square endorse_skill' : 'fa-minus-square remove_endorse';
            }
            else
            {
                $aSkills[$key]['icon_class'] = 'fa-plus-square endorse_skill';
            }
        }
        $skill_status = $this->_oDb->getSettingStatus('skill',$iUser);

        $isFriend = $isFriend == '' ? $this->_oDb->isFriends(getLoggedId(),$iUser) : $isFriend;

        $aVarsInfo = array(
            'bx_repeat:skills' => $aSkills,
            'style' => $skill_status == '1' ? '' : ($skill_status == '0' ? 'style="display:none;"' : ''),
            'logged_id' => getLoggedId(),
            'isfriends' => $isFriend == '' ? 'no' : 'yes',
        );

        return $this->_oTemplate->parseHtmlByName('view_prof_skills.html',$aVarsInfo);
    }

    function actionPublicSettings()
    {
    	if(isset($_POST['button1']))
    	{
    		$this->_oDb->insertProfileSettings($_POST, getLoggedId());
    	}

        $this->_oTemplate->pageStart();
        bx_import('BxDolPageView');
        $oPage = new BxDolPageView('public-profile-settings');
        echo $oPage->getCode();
        $this->_oTemplate->addJs(array('public_settings_page.js'));
        $this->_oTemplate->pageCode(_t('_public_profile_settings'), false, false);
    }


    function servicePublicProfileInfo()
    {
        $uid = $_GET['profile'];
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $aProfile = $this->_oDb->getProfile($uid);

        $selected_lang_id = $aProfile['selected_language'];
        $photo = $oBaseFunctions->getMemberAvatar($uid);
        $photo = str_replace("thumb", "eb", $photo);
        $usrTitle = $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($uid) : $aProfile['abs_plinkin_user_title'];
        
        $usrLocation = $aProfile['City'] .', '.' '._t($GLOBALS['aPreValues']['Country'][$aProfile['Country']]['LKey']);
        $aCompanies = $this->_oDb->getUserPrevCompanies($uid);
        $userLatestAcademyName = $this->_oDb->getUserLatestInstitute($uid);
        $aTmp = array();
        foreach ($aCompanies as $value) {
            $aTmp[] = $value['exp_companyName'];
        }
        $sCompanies = implode(',', $aTmp);

        $picture_status = $this->_oDb->getSettingStatus('picture',$uid);
        $headline_status = $this->_oDb->getSettingStatus('headline',$uid);
        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
        $user_image = $aProfile['Sex'] == '' || strtolower($aProfile['Sex']) == 'male' ? 'man_big.gif' : 'woman_big.gif';
        if($selected_lang_id == '' || $selected_lang_id == '0')
        {
            $aVars = array(
                'Name' => $aProfile['FirstName'] . ' '. $aProfile['LastName'],
                'photo' => $photo == '' ? BX_DOL_URL_ROOT . 'templates/base/images/icons/'.$user_image : $photo,
                'picture_style' => $picture_status == '1' ? '' : ( $picture_status == '0' ? 'style="display:none;"' : ''),
                'usr_title' => $usrTitle,
                'usertitle_style' => $headline_status == '1' ? '' : ( $headline_status == '0' ? 'style="display:none;"' : ''),
                'usrLocation' => $usrLocation,
                'companies' => $sCompanies,
                'uid' => $aProfile['ID'],
                'school_name' => $userLatestAcademyName,
                'pub_style' => $is_viewable == 'public' || $is_viewable == '' ? '' : 'style="display:none;"',
                'prv_style' => $is_viewable == 'private' ? '' : 'style="display:none;"',
            );
        }
        else
        {
            $aOtherLangProfile = $this->_oDb->getOtherLangInfo($uid,$selected_lang_id);
            $aVars = array(
                'Name' => $aOtherLangProfile['prof_firstname'] . ' '. $aOtherLangProfile['prof_lastname'],
                'photo' => $photo == '' ? BX_DOL_URL_ROOT . 'templates/base/images/icons/'.$user_image : $photo,
                'picture_style' => $picture_status == '1' ? '' : ( $picture_status == '0' ? 'style="display:none;"' : ''),
                'usr_title' => $aOtherLangProfile['prof_usertitle'],
                'usertitle_style' => $headline_status == '1' ? '' : ( $headline_status == '0' ? 'style="display:none;"' : ''),
                'usrLocation' => $aOtherLangProfile['prof_userlocation'],
                'companies' => $sCompanies,
                'uid' => $aProfile['ID'],
                'school_name' => $userLatestAcademyName,
                'pub_style' => $is_viewable == 'public' || $is_viewable == '' ? '' : 'style="display:none;"',
                'prv_style' => $is_viewable == 'private' ? '' : 'style="display:none;"',
            );
        }

        return $this->_oTemplate->parseHtmlByName('prof_profile.html',$aVars);
    }


  function servicePastPositions()
  {
        $uid = $_GET['profile'];
        $queryStirng = $_GET['r'];
        $arr = explode('/', $queryStirng);
        if( $arr[1] == 'viewprofile')
        {
            $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
            if($is_viewable == 'private' || ($this->_oDb->getSettingStatus('currentposition',$uid) != '1' && $this->_oDb->getSettingStatus('pastposition',$uid) != '1') )
                return '';
        }
        $aExps = $this->_oDb->getUserExperiences($uid);
        foreach ($aExps as $aExp) {
            $ex_title[$aExp['exp_id']] = $aExp['exp_title'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
            }

            if(strpos($aExp['exp_period'], 'present') !== false)
            {
            	$style = $this->_oDb->getSettingStatus('currentposition',$uid);
            	$style = $style == '1' ? '' : ($style == '0' ? 'style="display:none;"' : '');
            }
            else
            {
            	$style = $this->_oDb->getSettingStatus('pastposition',$uid);
            	$style = $style == '1' ? '' : ($style == '0' ? 'style="display:none;"' : '');
            }


            $aInlineVars[] = array(
                'comp_name' => $aExp['exp_companyName'],
                'title' => $aExp['exp_title'],
                'location' => $aExp['exp_location'],
                'desc' => $aExp['exp_desc'],
                'period' => $textPeriod,
                'expid' => $aExp['exp_id'],
                'pos_class' => strpos($aExp['exp_period'], 'present') !== false ? 'current_position' : 'past_position',
                'style' => $style,
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'hide' => $this->_oDb->getSettingStatus('currentposition',$uid) == 0 && $this->_oDb->getSettingStatus('pastposition',$uid) == 0 ? 'hideit' : 'showit',
            
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_pastposition.html',$aVarsInfo);
        return $res;  


        } 


	function servicePublicProfileVisible()
	{
		$uid = getLoggedId();
		$oBaseFunctions = bx_instance("BxBaseFunctions");

		//Main Condition
		$photo = $oBaseFunctions->getMemberAvatar($uid);
		$headline = $this->_oDb->getUserTitle($uid);
		$aPosition = $this->getPositionDetails($uid);
		if($aPosition == '')
			$hideall = true;
		else
		{
			$hide_cur_pos = $aPosition['cur_position'] == 'yes' ? true : false;
			$past_position = $aPosition['past_position'] == 'yes' ? true : false;
		}
		$edu = $this->_oDb->getUserEducations($uid);
		$skills = $this->_oDb->getUserSkills($uid);
		$langs = $this->_oDb->getUserLanguages($uid);
        $projects = $this->_oDb->getUserProjects($uid);
        $certs = $this->_oDb->getUserCertificates($uid);

		//Checkbox checked ot not condition
		$picture_status = $this->_oDb->getSettingStatus('picture',$uid);
		$headline_status = $this->_oDb->getSettingStatus('headline',$uid);
		$curpos_status = $this->_oDb->getSettingStatus('currentposition',$uid);
		$pastpos_status = $this->_oDb->getSettingStatus('pastposition',$uid);
		$education_status = $this->_oDb->getSettingStatus('education',$uid);
		$skill_status = $this->_oDb->getSettingStatus('skill',$uid);
		$lang_status = $this->_oDb->getSettingStatus('language',$uid);
        $project_status = $this->_oDb->getSettingStatus('project',$uid);
        $cert_status = $this->_oDb->getSettingStatus('certificate',$uid);

        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);

		$aInlineVars = array(
			'bx_if:is_picture' => array(
				'condition' => $photo !='',
				'content' => array(
					'picture' => "Picture",
					'picture_style' => $picture_status == '1' ? 'checked' : ( $picture_status == '0' ? '' : 'checked'),
					),
				),
			'bx_if:is_headline' => array(
				'condition' => $headline !='',
				'content' => array(
					'headline' => "Head line",
					'headline_style' => $headline_status == '1' ? 'checked' : ( $headline_status == '0' ? '' : 'checked'),
					),
				),
            'bx_if:is_curpos' => array(
				'condition' => $hide_cur_pos,
				'content' => array(
					'currentposition' => "Current position",
					'curpos_style' => $curpos_status == '1' ? 'checked' : ( $curpos_status == '0' ? '' : 'checked'),
					),
				),
            'bx_if:is_pastpos' => array(
				'condition' => $past_position,
				'content' => array(
					'pastposition' => "Past Position",
					'pastpos_style' => $pastpos_status == '1' ? 'checked' : ( $pastpos_status == '0' ? '' : 'checked'),
					),
				),
            'bx_if:is_edu' => array(
				'condition' => !empty($edu),
				'content' => array(
					'education' => "Education",
					'education_style' => $education_status == '1' ? 'checked' : ( $education_status == '0' ? '' : 'checked'),
					),
				),
            'bx_if:is_skill' => array(
				'condition' => !empty($skills),
				'content' => array(
					'skills' => "Skills",
					'skill_style' => $skill_status == '1' ? 'checked' : ( $skill_status == '0' ? '' : 'checked'),
					),
				),
            'bx_if:is_lang' => array(
				'condition' => !empty($langs),
				'content' => array(
					'languages' => "Languages",
					'lang_style' => $lang_status == '1' ? 'checked' : ( $lang_status == '0' ? '' : 'checked'),
					),
				),
            'bx_if:is_project' => array(
                'condition' => !empty($projects),
                'content' => array(
                    'projects' => "Projects",
                    'project_style' => $project_status == '1' ? 'checked' : ( $project_status == '0' ? '' : 'checked'),
                    ),
                ),
            'bx_if:is_certs' => array(
                'condition' => !empty($certs),
                'content' => array(
                    'certs' => "Certificates",
                    'cert_style' => $cert_status == '1' ? 'checked' : ( $cert_status == '0' ? '' : 'checked'),
                    ),
                ),
            'pub_checked' => $is_viewable == 'public' || $is_viewable == '' ? 'checked' : '',
            'prv_checked' => $is_viewable == 'private' ? 'checked' : '',
        );

		return $this->_oTemplate->parseHtmlByName('publicprof_checkbox.html',$aInlineVars);
	}

	function getPositionDetails($uid)
	{
		$aExp = $this->_oDb->getUserExperiences($uid);
		if(empty($aExp))
			return '';
		else
		{
			$res = array();
			foreach ($aExp as $value) {
				if(strpos($value['exp_period'], 'present') !== false)
					$res['cur_position'] = 'yes';
				else
					$res['past_position'] = 'yes';
			}
		}

		return $res;
	}

    function getUrlData($url, $raw=false) // $raw - enable for raw display
    {

        $result = false;
       
        $contents = $this->getUrlContents($url);

        if (isset($contents) && is_string($contents))
        {
            $title = null;
            $metaTags = null;
            $metaProperties = null;
           
            preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );

            if (isset($match) && is_array($match) && count($match) > 0)
            {
                $title = strip_tags($match[1]);
            }
           
            preg_match_all('/<[\s]*meta[\s]*(name|property)="?' . '([^>"]*)"?[\s]*' . 'content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
           
            if (isset($match) && is_array($match) && count($match) == 4)
            {
                $originals = $match[0];
                $names = $match[2];
                $values = $match[3];
               
                if (count($originals) == count($names) && count($names) == count($values))
                {
                    $metaTags = array();
                    $metaProperties = $metaTags;
                    if ($raw) {
                        if (version_compare(PHP_VERSION, '5.4.0') == -1)
                             $flags = ENT_COMPAT;
                        else
                             $flags = ENT_COMPAT | ENT_HTML401;
                    }
                   
                    for ($i=0, $limiti=count($names); $i < $limiti; $i++)
                    {
                        if ($match[1][$i] == 'name')
                             $meta_type = 'metaTags';
                        else
                             $meta_type = 'metaProperties';
                        if ($raw)
                            ${$meta_type}[$names[$i]] = array (
                                'html' => htmlentities($originals[$i], $flags, 'UTF-8'),
                                'value' => $values[$i]
                            );
                        else
                            ${$meta_type}[$names[$i]] = array (
                                'html' => $originals[$i],
                                'value' => $values[$i]
                            );
                    }
                }
            }
           
            $result = array (
                'title' => $title,
                'metaTags' => $metaTags,
                'metaProperties' => $metaProperties,
            );
        }
       
        return $result;
    }

    function getUrlContents($url, $maximumRedirections = null, $currentRedirection = 0)
    {
        $result = false;
       
        //$contents = @file_get_contents($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        curl_close($ch);
       
        // Check if we need to go somewhere else
       
        if (isset($contents) && is_string($contents))
        {
            preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);
           
            if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1)
            {
                if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections)
                {
                    return $this->getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
                }
               
                $result = false;
            }
            else
            {
                $result = $contents;
            }
        }
       
        return $contents;
    }

    function actionUploadlink()
    {
        $aTags = $this->getUrlData('https://www.youtube.com/watch?v=yAoLSRbwxL8');
        //$aTags = get_meta_tags('http://www.abservetech.com/');
        echo '<pre>'; var_dump($aTags); echo '</pre>';
        exit;
    }

    function actionGetMediaEditForm()
    {
        $mediaid = $_POST['media_id'];
        $aMedia = $this->_oDb->getMediaById($mediaid);
        $aMedia['edit_mode'] = 'yes';
        echo $this->getImageForm($aMedia);
    }

    function actionCheck()
    {
        $aTitles = $this->_oDb->getExperienceTitles(getLoggedId());
        var_dump($aTitles);
        exit;
    }

//starting point of project category

    function _getProjectForm($iExp = '')
    {
       

          $uid = getLoggedId();

           $aEdus = $this->_oDb->getUserEducations($uid);
        
           foreach ($aEdus as $aEdu) {
            $ed_title[$aEdu['edu_id']] = $aEdu['edu_school_name'];
           }

            foreach ($aEdus as $aEdu) {
            //$textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ed_title as $ed_key=>$ed_tit){
                $media_select[] = array('edu_id'=>$ed_key,'edu_school_name'=>$ed_tit,'ed_selected'=>$ed_key == $aEdu['edu_id'] ? 'selected="selected"' : '');
            }

           $aInlineVars[] = array(
                'school_name' => _t('_abs_plinkin_student') . " ". _t('_abs_plinkin_at') . " ". $aEdu['edu_school_name'],
                'school_value' => "student-".$aEdu['edu_school_name'],
            );
 }


         $aExps = $this->_oDb->getUserExperiences($uid);
        foreach ($aExps as $aExp) {
            $ex_title[$aExp['exp_id']] = $aExp['exp_title'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
            }

            $aInlineVars2[] = array(

                'compname' => $aExp['exp_title'] . " ". _t('_abs_plinkin_at') . " ".$aExp['exp_companyName'],
                'compvalue' => $aExp['exp_title'] . '-' .$aExp['exp_companyName'],
            );
        }


        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $aProfile = $this->_oDb->getProfile($uid);

        $photo = $oBaseFunctions->getMemberAvatar($sId);
        $photo = str_replace("thumb", "eb", $photo);

    


        //echo 'projectId'.$iExp;
        if($iExp != '') 
        {
            $aExp = $this->_oDb->getProInfo($iExp);

            $aDates = explode('-', $aExp['pro_period']);
            $aStart = explode(',', trim($aDates[0]));
            if(trim($aDates[1]) != 'at present')
                $aEnd = explode(',', trim($aDates[1]));

            foreach ($this->aMonth as $key => $value) {
                $aStMonData[] = array(
                    'month_int' => $key,
                    'month_text' => $value,
                    'selected' => $key == $aStart[0] ? 'selected="selected"' : '',
                );

                /*if(is_array($aEnd))
                {*/
                    /*if(trim($aEnd) != 'at present')
                    {*/
                        $aEndMonData[] = array(
                            'emonth_int' => $key,
                            'emonth_text' => $value,
                            'eselected' => $key == $aEnd[0] ? 'selected="selected"' : '',
                        );
                    //}
                //}
             }

             foreach ($aInlineVars as $value) {
                    $aInlineVar[] = array(
                            'selected' => $value['school_name'] == $aExp['pro_occupation'] ? 'selected' : '',
                            'school_name' => $value['school_name'],
                        );
             }

             foreach ($aInlineVars2 as $value) {
                    $aInlineVar2[] = array(
                            'selected' => $value['compname'] == $aExp['pro_occupation'] ? 'selected' : '',
                            'compname' => $value['compname'],
                        );
             }

            $aVars = array(

                'firstname' => $aProfile['FirstName'],
                'lastname' => $aProfile['LastName'] ,
                'photo'   => $photo,
                
                'projectname' => $aExp['pro_name'],
            
            
                'desc' => $aExp['pro_desc'],
                'st_month' => $aStart[0],
                'st_yr' => trim($aStart[1]),
                'end_month' => trim($aEnd[0]),
                'end_year' => trim($aEnd[1]),
                'id' => $aExp['pro_id'],
                'usr_id' => $aExp['pro_user_id'],
                'bx_if:ishide' => array(
                    'condition' => !is_array($aEnd), //== 'at present',
                    'content' => array(
                        'hide' => 'style="display:none;"',
                        ),
                    ),
                'bx_repeat:schoolname' =>$aInlineVar,
                'bx_repeat:companyname' =>$aInlineVar2,
                'bx_repeat:stmonths' => $aStMonData,
                'bx_repeat:end_months' => $aEndMonData,
                'checked' => !is_array($aEnd) ? 'checked="checked"' : '',
                'project_url' => $aExp['pro_url'],
                'project_team' => $aExp['pro_team'],
            );
        }
        else
        {
            foreach ($this->aMonth as $key => $value) {
                $aStMonData[] = array(
                    'month_int' => $key,
                    'month_text' => $value,
                    'selected' => '',
                );

                if(trim($aEnd) != 'at present')
                {
                    $aEndMonData[] = array(
                        'emonth_int' => $key,
                        'emonth_text' => $value,
                        'eselected' => '',
                    );
                }
             }

            $aVars = array(

                  'firstname' => $aProfile['FirstName'],
                  'lastname' => $aProfile['LastName'] ,
                  'photo'   => $photo,
                

                'usr_id' => getLoggedId(),
                'bx_repeat:stmonths' => $aStMonData,
                'bx_repeat:end_months' => $aEndMonData,
                'projectname' => '',
                
                
                'desc' => '',
                'st_yr' => '',
                'end_year' => '',
                'bx_if:ishide' => array(
                    'condition' => 1 != 2,
                    'content' => array(
                        ),
                    ),
                'bx_repeat:schoolname' =>$aInlineVars,
                'bx_repeat:companyname' =>$aInlineVars2,
                'project_url' => '',
                'project_team' => '',
            );
        }

        return $this->_oTemplate->parseHtmlByName('prof_project_form.html',$aVars);
    }


    function _ProfileProject($iId ='')
    {
        $aExps = $this->_oDb->getUserProjects($iId);
        foreach ($aExps as $aExp) {
            $ex_title[$aExp['pro_id']] = $aExp['pro_name'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['pro_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
            }

            $role = explode('-',$aExp['pro_occupation']);
            if(strtolower($role[0]) == 'student')
                $sRole = _t('_abs_plinkin_'.$role[0]) .' '._t('_abs_plinkin_at') .' '.$role[1];
            else
              // freddy change  $sRole = $role[0] .' '._t('_abs_plinkin_at') .' '.$role[1];
			  $sRole = $role[0] .' '.'<i class="sys-icon certificate"></i> ' .' '.$role[1];

            $aInlineVars[] = array(
                'projectname' => $aExp['pro_name'],
                'desc' => $aExp['pro_desc'],
                'period' => $textPeriod,
                'id' => $aExp['pro_id'],
                'projectTeam' => $aExp['pro_team'],
                'projectOccupation'=> $sRole,
                'projectUrl' => $aExp['pro_url'],
                'end_month' =>$aExp['pro_end_month'],
                'end_year' => $aExp['pro_end_year'],
                'usr_id' =>$aExp['pro_user_id'],
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'exp_form' => $this->_getProjectForm(),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_project_info.html',$aVarsInfo);
        return $res;
    }

    function actionPostProject()
    {

        if(isset($_POST['projectName']))
        {  
            $aVars = $_POST;
            /*var_dump($aVars);
            exit;*/
            if($_POST['EndDateMonth'] == '' && $_POST['EndYear'] == '')
            {
                $end = 'at present';
                $end_year = date("Y");
                $end_month = date("m");
            }
            else if($_POST['EndDateMonth'] == '' && $_POST['EndYear'] != '') //If user gives only year not month, then we will take it as last month....
                $_POST['EndDateMonth'] == 12;
            else if($_POST['EndDateMonth'] != '' && $_POST['EndYear'] == '') //If user gives only month not year, then we will take it as current year....
                $_POST['EndYear'] == date("Y");
            else
                $end = $_POST['EndDateMonth'] . ',' . $_POST['EndYear'];

            $end = $end == '' ?  $_POST['EndDateMonth'] . ',' . $_POST['EndYear'] : $end;
            $end_year = $end_year == '' ? $_POST['EndYear'] : $end_year;
            $end_month = $end_month == '' ? $_POST['EndDateMonth'] : $end_month;

            $aVars['period'] = $_POST['startDateMonth'] . ',' . $_POST['StartYear'] . ' - '. $end;
            $proid = $aVars['pro_id'];

            //Extra values to add
            $aVars['end_year'] = $end_year;
            $aVars['end_month'] = $end_month;

           $aVars['name'] = $_POST['projectName'];
           $aVars['projectTeam'] = $_POST['team'];
           $aVars['desc'] = $_POST['desc'];

           $aVars['url'] =$_POST['projecturl'];
           

           $aVars['occupation'] =$_POST['occupation'];
           $aVars['user_id'] =$_POST['user_id'];







            //Remove unwanted post values or add extra to insert before we prepare
            $aVars = $this->doCustomizeValues($aVars,array('period','name','team','url','occupation','desc','user_id','end_year','end_month'));

            //Prepare query to insert values
            $qryToInsert = $this->prepareQueryToInsert($aVars, 'pro_');

            //echo $qryToInsert; 
            //table name without prefix
            //if its update, have to give id as last param to update in query format such as `id` = $id
            $qUpdate = $proid == '' || $proid == '__id__' ? '' : "`pro_id`=".$proid;
            $table = 'project';
            $this->_oDb->insertData($table,$qryToInsert, $qUpdate);
        }
        
        echo $this->_ProfileProject(getLoggedId());
    }

    
    function actionRemoveproject()
    {
        $proid = $_POST['pro_id'];
        $this->_oDb->deleteProject($proid);
        echo $this->_ProfileProject(getLoggedId());
    }



    function serviceProfileProject($iId = '')
    {
        return '<div class="prof_pro_main">'.$this->_ProfileProject($iId).'</div>';
    }
    
     function actionGetprojectform()
    {
        echo $this->_getProjectForm($_POST['id']);
    }

// ending point

    public function actionUserRecentActivity($id = '')
    {
        $this->_oTemplate->pageStart();
        bx_import('BxDolPageView');
        $oPage = new BxDolPageView('profile-recent-activity');
        echo $oPage->getCode();
        $this->_oTemplate->addJs(array('recent_activity.js'));
        $this->_oTemplate->pageCode(_t('_profile_recent_activity'), false, false);
    }

    public function serviceProfileRecentActivity()
    {
        $id = getLoggedId();

        bx_import('BxDolModule');
        $oWall = BxDolModule::getInstance('BxWallModule');
        $aContent = $oWall->serviceViewBlockIndexTimeline();
        $aVars = array(
            'content' => $aContent[0],
            );
        return $this->_oTemplate->parseHtmlByName('profile_recent_activity.html',$aVars);
    }

    public function serviceGetWallLikeAction($iWall = '')
    {
        return '<span class="like-content">'. $this->getLikeAction($iWall) .'</span>';
    }

    function getLikeAction($iWall)
    {
    	$iUser = getLoggedId();
        $aLikes = $this->_oDb->getLikesForEvent($iWall);
        $isUserLiked = $this->_oDb->isUserLikedEvent($iWall, $iUser);

        if(!empty($aLikes))
        {
        	$i=0;
            foreach ($aLikes as $value) {
            	$i++;
                $userArray[] = array(
                    'url' => getProfileLink($value['user_id']),
                    'name' => getNickName($value['user_id']),
                    );
            }
        }

        $aVars = array(
            'like_content' => empty($isUserLiked) ? _t(_abs_plinkin_like) : _t(_unlike),
            'bx_if:is_liked' => array(
            	'condition' => !empty($aLikes),
            	'content' => array(
            		'member_count' => $i,
            		'bx_repeat:liked_users' => !empty($aLikes) ? $userArray : '', 
            		),
            	),
            );

        return $this->_oTemplate->parseHtmlByName('profile_wall_like.html',$aVars);
    }

    public function actionUpdateUserlike()
    {
    	$aVars = $_POST;
    	$aVars['user_id'] = getLoggedId();;
    	$this->_oDb->updateLikeStatus($aVars);
    	echo $this->getLikeAction($aVars['wall_id']);
    }

    //Certificate Starts Here

    public function serviceProfileCertificate()
    {
    	return '<div class="prof_certificate_main">'.$this->_ProfileCertificate(getLoggedId()).'</div>';
    }

    public function actionPostCertificate()
    {
    	if(isset($_POST['certificateName']))
        {  
            $aVars = $_POST;
            /*var_dump($aVars);
            exit;*/
            if($_POST['EndDateMonth'] == '' && $_POST['EndYear'] == '')
            {
                $end = 'at present';
                $end_year = date("Y");
                $end_month = date("m");
            }
            else if($_POST['EndDateMonth'] == '' && $_POST['EndYear'] != '') //If user gives only year not month, then we will take it as last month....
                $_POST['EndDateMonth'] == 12;
            else if($_POST['EndDateMonth'] != '' && $_POST['EndYear'] == '') //If user gives only month not year, then we will take it as current year....
                $_POST['EndYear'] == date("Y");
            else
                $end = $_POST['EndDateMonth'] . ',' . $_POST['EndYear'];

            $end = $end == '' ?  $_POST['EndDateMonth'] . ',' . $_POST['EndYear'] : $end;
            $end_year = $end_year == '' ? $_POST['EndYear'] : $end_year;
            $end_month = $end_month == '' ? $_POST['EndDateMonth'] : $end_month;

            $aVars['period'] = $_POST['startDateMonth'] . ',' . $_POST['StartYear'] . ' - '. $end;
            $certid = $aVars['cert_id'];

            //Extra values to add
            $aVars['end_year'] = $end_year;
            $aVars['end_month'] = $end_month;

            $aVars['name'] = $_POST['certificateName'];
            $aVars['authority'] = $_POST['certificateAuthority'];
            $aVars['license'] = $_POST['certificateLicense'];

            $aVars['url'] =$_POST['certificateUrl'];
           

           
            $aVars['user_id'] =$_POST['user_id'];

            //Remove unwanted post values or add extra to insert before we prepare
            $aVars = $this->doCustomizeValues($aVars,array('period','name','authority','url','license','user_id','end_year','end_month'));

            //Prepare query to insert values
            $qryToInsert = $this->prepareQueryToInsert($aVars, 'cert_');

            //echo $qryToInsert; 
            //table name without prefix
            //if its update, have to give id as last param to update in query format such as `id` = $id
            $qUpdate = $certid == '' || $certid == '__id__' ? '' : "`cert_id`=".$certid;
            $table = 'certificate';
            $this->_oDb->insertData($table,$qryToInsert, $qUpdate);
        }
        
        echo $this->_ProfileCertificate(getLoggedId());
    }

    function _ProfileCertificate($iId ='')
    {
        $aExps = $this->_oDb->getUserCertificates($iId);
        foreach ($aExps as $aExp) {
            $ex_title[$aExp['cert_id']] = $aExp['cert_name'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['cert_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('cert_id'=>$exp_key,'cert_title'=>$ex_tit,'cert_selected'=>$exp_key == $aExp['cert_id'] ? 'selected="selected"' : '');
            }

            

            $aInlineVars[] = array(
                'certificatename' => $aExp['cert_name'],
                'certificateauthority' => $aExp['cert_authority'],
                'period' => $textPeriod,
                'id' => $aExp['cert_id'],
                'certificatelicense' => $aExp['cert_license'],
                'certificateurl'=> $aExp['cert_url'],
                
                'end_month' =>$aExp['cert_end_month'],
                'end_year' => $aExp['cert_end_year'],
                'usr_id' =>$aExp['cert_user_id'],
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'exp_form' => $this->_getCertificateForm(),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_certifi_info.html',$aVarsInfo);
        return $res;
    }

    function _getCertificateForm($iCert = '')
    {
       
        //echo $iExp;
        if($iCert != '') 
        {
            $aExp = $this->_oDb->getCertInfo($iCert);

            $aDates = explode('-', $aExp['cert_period']);
            $aStart = explode(',', trim($aDates[0]));
            if(trim($aDates[1]) != 'at present')
                $aEnd = explode(',', trim($aDates[1]));

            foreach ($this->aMonth as $key => $value) {
                $aStMonData[] = array(
                    'month_int' => $key,
                    'month_text' => $value,
                    'selected' => $key == $aStart[0] ? 'selected="selected"' : '',
                );

                /*if(is_array($aEnd))
                {*/
                    /*if(trim($aEnd) != 'at present')
                    {*/
                        $aEndMonData[] = array(
                            'emonth_int' => $key,
                            'emonth_text' => $value,
                            'eselected' => $key == $aEnd[0] ? 'selected="selected"' : '',
                        );
                    //}
                //}
             }

            $aVars = array(

                
                
                'certificatename' => $aExp['cert_name'],
                'certificateauthority' =>$aExp['cert_authority'],
                'certificatelicense' =>$aExp['cert_license'],
                'certificateurl' =>$aExp['cert_url'],

            
                'st_month' => $aStart[0],
                'st_yr' => trim($aStart[1]),
                'end_month' => trim($aEnd[0]),
                'end_year' => trim($aEnd[1]),
                'id' => $aExp['cert_id'],
                'usr_id' => $aExp['cert_user_id'],
                'bx_if:ishide' => array(
                    'condition' => !is_array($aEnd), //== 'at present',
                    'content' => array(
                        'hide' => 'style="display:none;"',
                        ),
                    ),
                
                'bx_repeat:stmonths' => $aStMonData,
                'bx_repeat:end_months' => $aEndMonData,
                'checked' => !is_array($aEnd) ? 'checked="checked"' : '',
            );
        }
        else
        {
            foreach ($this->aMonth as $key => $value) {
                $aStMonData[] = array(
                    'month_int' => $key,
                    'month_text' => $value,
                    'selected' => '',
                );

                if(trim($aEnd) != 'at present')
                {
                    $aEndMonData[] = array(
                        'emonth_int' => $key,
                        'emonth_text' => $value,
                        'eselected' => '',
                    );
                }
             }

            $aVars = array(

                
                'certificatename' => '',
                'certificateauthority' => '',
                'certificatelicense' => '',
                'certificateurl' => '',
                'usr_id' => getLoggedId(),
                'bx_repeat:stmonths' => $aStMonData,
                'bx_repeat:end_months' => $aEndMonData,
                'certificatename' => '',
            
                'st_yr' => '',
                'end_year' => '',
                'bx_if:ishide' => array(
                    'condition' => 1 != 2,
                    'content' => array(
                        ),
                    ),
                'bx_repeat:schoolname' =>$aInlineVars,
                'bx_repeat:companyname' =>$aInlineVars2,


            );
        }

        return $this->_oTemplate->parseHtmlByName('prof_certifi_form.html',$aVars);
    }

    function actionGetCertificateForm()
    {
    	echo $this->_getCertificateForm($_POST['id']);
    }

    function actionCreateinOtherLanguage()
    {
    	$this->_oTemplate->pageStart();
        echo $this->getOtherLanguageForm();
        $this->_oTemplate->addJs(array('recent_activity.js'));
        $this->_oTemplate->pageCode(_t('_abs_plinkin_create_profile_in_other_language'), false, false);
    }

    function getOtherLanguageForm()
    {
    	$iId= getLoggedId();
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $aProfile = $this->_oDb->getProfile($iId);       
        $usrTitle = $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($iId) : $aProfile['abs_plinkin_user_title'];
        $usrLocation = $aProfile['City'] .$aProfile['Country'];
        $lang_id=$aProfile['lang_id'];
       
        $LanguageProficiency = getPreValues('Language');
        foreach ($LanguageProficiency as $value) 
        {
            $aVars[] = array(       
               'lang_id' =>  $value['Value'],
               'lang_name' => _t($value['LKey']),
               'selected' => '',
             );
        }

        $aVarsInfo = array(
	        'bx_repeat:exps' => $aVars,
	        'uid'=>$iId,
	        'FirstName'=>$aProfile['FirstName'],
	        'LastName'=>$aProfile['LastName'],
	        'usr_title' => $usrTitle,
	        'usrLocation' => $usrLocation,
            'site_url'=>BX_DOL_URL_ROOT.'pedit.php?ID='.$iId,
	    );

     	echo '<div id="status_text">'.$this->_oTemplate->parseHtmlByName('prof_anotherlangform.html',$aVarsInfo).'</div>';
    }

    function actionPostCreateProfInOtherLang()
    {
    	$aVars['lang_id'] = $_POST['lang_id'];
       	$aVars['user_id'] = getLoggedId();
       	$aVars['prof_firstname'] =$_POST['FirstName'];
       	$aVars['prof_lastname'] =$_POST['LastName'];
       	$aVars['prof_userlocation'] =$_POST['usrLocation'];
       	$aVars['prof_usertitle'] =$_POST['usr_title'];
      
        $lang_id=$_POST['lang_id'];
        /*$this->_oDb->updateEnglishNames($aVars['user_id']);
        */

        $uid=getLoggedId();
        $aVars = $this->doCustomizeValues($aVars,array('lang_id','user_id','prof_firstname','prof_lastname','prof_userlocation','prof_usertitle'));        
        $qryToInsert = $this->prepareQueryToInsert($aVars);

        $table = 'profile_other_lang';

        $this->_oDb->insertProfileinOtherLang($table,$qryToInsert,$uid,$lang_id);
        $this->_oDb->updateProfileWithOtherLangs($aVars);
    }

    function actionGetOtherLangInfo()
    {
    	$lang_id =$_POST['lang_id'];
        $iId = $_POST['uid'];        
        /*$iId = getLoggedId();*/
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $aProfile = $this->_oDb->getOtherLangInfo($iId,$lang_id);

        $LanguageProficiency = getPreValues('Language');
        foreach ($LanguageProficiency as $value) 
        {
          $aVarss[] = array(       
               'lang_id' =>  $value['Value'],
               'lang_name' => _t($value['LKey']),
               'selected' => $value['Value'] == $lang_id ? 'selected' : '',
          );  
        }

        if( empty( $aProfile ) )
        {
            $aProfile = $this->_oDb->getProfile($iId);
            $usrTitle = $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($iId) : $aProfile['abs_plinkin_user_title'];
            $usrLocation = $aProfile['City'] .', '.' '.$aProfile['Country'];
            
            $aVars = array(
                'uid'  => $aProfile['ID'],
                'FirstName'  => $aProfile['FirstName'],
                'LastName' => $aProfile['LastName'], 
                'usr_title' => $usrTitle,
                'usrLocation' => $usrLocation,  
                'bx_repeat:exps' => $aVarss,          
            );
        }
        else
        {
            $iId= getLoggedId();

            $aVars = array(
                'uid'  => $aProfile['user_id'],
	            'FirstName' => $aProfile['prof_firstname'],
	            'LastName' =>  $aProfile['prof_lastname'],
	            'usr_title' => $aProfile['prof_usertitle'],           
	            'usrLocation' => $aProfile['prof_userlocation'],
	            'bx_repeat:exps' => $aVarss,          
	        );
        }

       echo $this->_oTemplate->parseHtmlByName('prof_anotherlangform.html',$aVars);
    }

    function actionRemoveCertificate()
    {
        $cert_id = $_POST['cert_id'];
        $this->_oDb->removeCert($cert_id);
        echo $this->_ProfileCertificate(getLoggedId());
    }

    function actionEndorseUser()
    {
        $aVars['skill_id'] = process_db_input($_POST['skill_id']);
        $aVars['skill_user_id'] = process_db_input($_POST['skill_user_id']);
        $aVars['endorsed_by'] = process_db_input($_POST['logged_id']);

        $qryToInsert = $this->prepareQueryToInsert($aVars);

        $table = 'endorse_data';
        $this->_oDb->insertData($table,$qryToInsert);
        $rEmailTemplate = new BxDolEmailTemplates();
        $ID = $_POST['skill_user_id'];
        $aTemplate = $rEmailTemplate -> getTemplate( '_abs_plinkin_endorse_notification', $ID ) ;
        $aProfile = getProfileInfo($ID);
        $aPlus['Skill'] = $this->_oDb->getSkillName($_POST['skill_id']);
        $aPlus['EndorserName'] = getNickName($_POST['logged_id']);
        $mail_ret = sendMail( $aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $ID, $aPlus, 'html', false, true );
        //echo $aPlus['Skill'];

        echo $this->_viewskills($aVars['skill_user_id']);
    }

    function actionRemoveEndorse()
    {
        $this->_oDb->removeEndorseData($_POST);
        echo $this->_viewskills($_POST['skill_user_id']);
    }

    function serviceLangSelector()
    {
        $iId= getLoggedId();
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        $aProfile = $this->_oDb->getProfile($iId);

        $lang_id=$aProfile['selected_language'];

        $aLangall = $this->_oDb->getProfilelangall($iId);
        foreach ($aLangall as $key => $value) {
            $aSelLangs[] = $value['lang_id'];
        }

        $LanguageProficiency = getPreValues('Language');        
        foreach ($LanguageProficiency as  $value)
        {
            if(in_array($value['Value'], $aSelLangs))
            {
              $aVars[] = array(
                'lang_id' =>  $value['Value'],
                'pro' => _t($value['LKey']),
                'selected' => $value['Value'] == $lang_id ? 'selected' : '',
                );
            }
        }
        $aVarsInfo = array(
            'bx_repeat:langs' => $aVars,
            'uid'=>$iId,
        );               

       return $this->_oTemplate->parseHtmlByName('lang_selector.html',$aVarsInfo);
    }

    function actionPublicProfileInfoUpdate()
    {
         $lang_id =$_POST['lang_id'];
         $uid =$_POST['uid'];
         $this->_oDb->getInfoOfThislang($lang_id, $uid);
         echo $this->_oDb->PublicProfileInfoUpdate($uid,$lang_id);   
    }

    function actionPdfCheck()
    {
        try {
            ob_start();
            include dirname(__FILE__).'/res/exemple00.php';
            $content = ob_get_clean();

            $html2pdf = new Html2Pdf('P', 'A4', 'fr');
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->writeHTML($content);
            $html2pdf->Output('exemple00.pdf','D');
        } catch (Html2PdfException $e) {
            $formatter = new ExceptionFormatter($e);
            echo $formatter->getHtmlMessage();
        }
    }

    function actionGenerateResume()
    {
        $iId = $_GET['profile'];
        $aVars = array();

        //Profile Info
        $aProfile = $this->_oDb->getProfile($iId);
        $selected_lang_id = $aProfile['selected_language'];
        if($selected_lang_id == '' || $selected_lang_id == '0')
        {
            $nickname = $aProfile['FirstName'] .' '. $aProfile['LastName'];
            $aProfileInfoVars = array(
                'Name' => $aProfile['FirstName'] . ' '. $aProfile['LastName'],
                'usr_title' => $aProfile['abs_plinkin_user_title'] == '' ? $this->UpdateandgetUserTitle($iId) : $aProfile['abs_plinkin_user_title'],
                'usr_email' => $aProfile['Email'],
            );
        }
        else
        {
            $aLangProfile = $this->_oDb->getOtherLangInfo($iId,$selected_lang_id);
            $nickname = $aLangProfile['prof_firstname'].$aLangProfile['prof_lastname'];
            $aProfileInfoVars = array(
                'Name' => $aLangProfile['prof_firstname'] . ' '. $aLangProfile['prof_lastname'],
                'usr_title' => $aLangProfile['prof_usertitle'] == '' ? $this->UpdateandgetUserTitle($iId) : $aLangProfile['prof_usertitle'],
                'usr_email' => $aProfile['Email'],
            );
        }
   
        //Profile Summary
        $aSummaryInfo = $this->_oDb->getUserSummary($iId);

        //Profile Experience
        $aExps = $this->_oDb->getUserExperiences($iId);
        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            $aExpVars[] = array(
                'comp_name' => $aExp['exp_companyName'],
                'title' => $aExp['exp_title'],
                'period' => $textPeriod,
            );
        }

        //Profile Skills
        $aSkills = $this->_oDb->getUserSkills($iId);

        //Profile Education
        $aEdus = $this->_oDb->getUserEducations($iId);
        foreach ($aEdus as $aEdu) {
           $aEduVars[] = array(
                'school_name' => $aEdu['edu_school_name'],
                'type' => $this->getEduTypeName($aEdu['edu_type']),
                'period' => $aEdu['edu_period'],
                'fieldofstudy' => $aEdu['edu_field_of_study'],
                'grade' => $aEdu['edu_grade'] == '' ? '' :  _t('_abs_plinkin_view_Grade').' : '.$aEdu['edu_grade'],
            );
        }

        //Profile Certifications
        $aCerts = $this->_oDb->getUserCertificates($iId);
        if(!empty($aCerts))
        {
            foreach ($aCerts as $aCert) {
               $textPeriod = $this->getperiodToDisplay($aCert['cert_period']);
               $aCertVars[] = array(
                    'cert_name' => $aCert['cert_name'],
                    'cert_authority' => $aCert['cert_authority'],
                    'cert_period' => $textPeriod,
                    'cert_license' => $aExp['cert_license'],
               );
            }
        }

        //Profile Languages
        $aLangs = $this->_oDb->getUserLanguages($iId);
        $LanguageProficiency = getPreValues('LanguageProficiency');
        foreach ($aLangs as $aLang) {
            $lang_key = $LanguageProficiency[$aLang['Proficiency']]["LKey"];
            $aLangVars[] = array(
                'lang' => $aLang['lang'],
                'Proficiency' => _t($lang_key)
            );
        }

        //Profile Projects
        $aProjects = $this->_oDb->getUserProjects($iId);

        foreach ($aProjects as $aProject) {
            $textPeriod = $this->getperiodToDisplay($aProject['pro_period']);
            $role = explode('-',$aProject['pro_occupation']);
            if(strtolower($role[0]) == 'student')
                $sRole = _t('_abs_plinkin_'.$role[0]) .' '._t('_abs_plinkin_at') .' '.$role[1];
            else
                $sRole = $role[0] .' '._t('_abs_plinkin_at') .' '.$role[1];
            $aProjectVars[] = array(
                'project_name' => $aProject['pro_name'],
                'project_desc' => $aProject['pro_desc'],
                'project_period' => $textPeriod,
                'project_team' => $aProject['pro_team'],
                'project_occupation' => $sRole
            );
        }

        $aVars = array(
            'bx_if:profile' => array(
                'condition' => !empty($aProfile),
                'content' => $aProfileInfoVars,
            ),
            'bx_if:summary' => array(
                'condition' => $aSummaryInfo['summary_text'] != '',
                'content' => array(
                    'summary_txt' => $aSummaryInfo['summary_text'],
                ),
            ),
            'bx_if:experience' => array(
                'condition' => !empty($aExps),
                'content' => array(
                    'bx_repeat:exps' => $aExpVars,
                ),
            ),
            'bx_if:skills' => array(
                'condition' => !empty($aSkills),
                'content' => array(
                    'bx_repeat:skills' => $aSkills,
                ),
            ),
            'bx_if:education' => array(
                'condition' => !empty($aEdus),
                'content' => array(
                    'bx_repeat:edus' => $aEduVars,
                ),
            ),
            'bx_if:certificate' => array(
                'condition' => !empty($aCerts),
                'content' => array(
                    'bx_repeat:certs' => $aCertVars,
                ),
            ),
            'bx_if:language' => array(
                'condition' => !empty($aLangs),
                'content' => array(
                    'bx_repeat:langs' => $aLangVars,
                ),
            ),
            'bx_if:project' => array(
                'condition' => !empty($aProjects),
                'content' => array(
                    'bx_repeat:projects' => $aProjectVars,
                ),
            ),
            'nickname' => $nickname,
            'profile_url' => getProfileLink($iId),
            'site_title'=>$GLOBALS['site']['title'],
        );
        $content = $this->_oTemplate->parseHtmlByName('resume.html',$aVars);

        try {
            /*ob_start();
            include dirname(__FILE__).'/res/exemple00.php';
            $content = ob_get_clean();*/

            $html2pdf = new Html2Pdf('P', 'A4', 'fr');
            $html2pdf->setDefaultFont();
            $html2pdf->writeHTML($content);
            $html2pdf->Output(trim($nickname).'.pdf','D');
            //$html2pdf->Output($nickname.'.pdf');
        } catch (Html2PdfException $e) {
            $formatter = new ExceptionFormatter($e);
            echo $formatter->getHtmlMessage();
        }
    }

    /* Profile View Page Info */

    public function serviceProfileViewExperience($iId)
    {
        $uid = $iId;
        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
        if($is_viewable == 'private' || ($this->_oDb->getSettingStatus('currentposition',$uid) != '1' && $this->_oDb->getSettingStatus('pastposition',$uid) != '1') )
            return '';

        $aExps = $this->_oDb->getUserExperiences($uid);
        foreach ($aExps as $aExp) {
            $ex_title[$aExp['exp_id']] = $aExp['exp_title'];
        }
        $allMediaVars=array();
        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
            }

            if(strpos($aExp['exp_period'], 'present') !== false)
            {
                $style = $this->_oDb->getSettingStatus('currentposition',$uid);
                $style = $style == '1' ? '' : ($style == '0' ? 'style="display:none;"' : '');
            }
            else
            {
                $style = $this->_oDb->getSettingStatus('pastposition',$uid);
                $style = $style == '1' ? '' : ($style == '0' ? 'style="display:none;"' : '');
            }

            $aMedias = $this->_oDb->getMedias($aExp['exp_id'],'experience');
            $aMediaVars = array();
            foreach ($aMedias as $aMedia) {
                if( ($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] !='' )
                    $img_path = $aMedia['media_image_name'];
                elseif(($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] =='')
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/img_not_avail.png';
                else
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/'.$aMedia['media_image_name'];

                

                $parsed     = parse_url($aMedia['media_name']);
                $hostname   = $parsed['host'];  
                $query      = $parsed['query']; 
                $path       = $parsed['path']; 
                $videoID="";
                $Arr = explode('v=',$query);
                if(count($Arr)>0)
                {
                    $videoIDwithString = $Arr[1];
                    $videoID = substr($videoIDwithString,0,11);

                }
                $url=$img_path;
                $image_info=pathinfo($url);
                $image_extension=$image_info['extension'];
                $image_name=str_replace('-0', '', $image_info['filename']);
                $image_dir=$image_info['dirname'];
                if($aMedia['media_type']=='video')
                {
                    $autoembed = new AutoEmbed();
                    $video_content = $autoembed->parse($aMedia['media_name']);
                }
                else
                {
                    $video_content="";
                }
                $aMediaVars[] = array(
                    'media_id' => $aMedia['media_id'],
                    'media_title' => $aMedia['media_title'],
                    'media_image_path' => $img_path,
                    'media_video_id'=>$videoID,
                    'media_video_content'=>$video_content,
                    'media_pages_count'=>$aMedia['media_pages_count'],
                    'media_image_name'=>$image_name,
                    'image_extension'=>$image_extension,
                    'image_dir'=>$image_dir,
                    'media_type'=>$aMedia['media_type'],

                );

                $allMediaVars[]=array(
                    'media_id' => $aMedia['media_id'],
                    'media_title' => $aMedia['media_title'],
                    'media_image_path' => $img_path,
                    'media_video_id'=>$videoID,
                    'media_video_content'=>$video_content,
                    'media_pages_count'=>$aMedia['media_pages_count'],
                    'media_image_name'=>$image_name,
                    'image_extension'=>$image_extension,
                    'image_dir'=>$image_dir,
                    'media_type'=>$aMedia['media_type'],
                );
            }


            $aInlineVars[] = array(
                'comp_name' => $aExp['exp_companyName'],
                'title' => $aExp['exp_title'],
                'location' => $aExp['exp_location'],
                'desc' => $aExp['exp_desc'],
                'period' => $textPeriod,
                'expid' => $aExp['exp_id'],
                'pos_class' => strpos($aExp['exp_period'], 'present') !== false ? 'current_position' : 'past_position',
                'style' => $style,
                'bx_repeat:abs_medias' => $aMediaVars,
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'bx_repeat:all_medias' => $allMediaVars,
            'hide' => $this->_oDb->getSettingStatus('currentposition',$uid) == 0 && $this->_oDb->getSettingStatus('pastposition',$uid) == 0 ? 'hideit' : 'showit',
            
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_view_pastposition.html',$aVarsInfo);
        return $res;  
    }

    public function serviceProfileViewEducation($iId)
    {
        $uid = $iId;
        $education_status = $this->_oDb->getSettingStatus('education',$uid);
        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
            if($is_viewable == 'private' || $education_status != '1') 
                return '';

        $aEdus = $this->_oDb->getUserEducations($uid);
        
        if(empty($aEdus))
            return '';

        foreach ($aEdus as $aEdu) {
         $ed_title[$aEdu['edu_id']] = $aEdu['edu_school_name'];
        }
        $allMediaVars=array();
        foreach ($aEdus as $aEdu) {
            //$textPeriod = $this->getperiodToDisplay($aExp['exp_period']);
            foreach($ed_title as $ed_key=>$ed_tit){
                $media_select[] = array('edu_id'=>$ed_key,'edu_school_name'=>$ed_tit,'ed_selected'=>$ed_key == $aEdu['edu_id'] ? 'selected="selected"' : '');
            }

            $aMedias = $this->_oDb->getMedias($aEdu['edu_id'],'education');
            $aMediaVars = array();
            foreach ($aMedias as $aMedia) {
                if( ($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] !='' )
                    $img_path = $aMedia['media_image_name'];
                elseif(($aMedia['media_type'] == 'link' || $aMedia['media_type'] == 'video') && $aMedia['media_image_name'] == '')
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/img_not_avail.png';
                else
                    $img_path = BX_DOL_URL_ROOT . 'modules/abservetech/linkedin_profile/media_uploads/'.$aMedia['media_image_name'];

                $parsed     = parse_url($aMedia['media_name']);
                $hostname   = $parsed['host'];  
                $query      = $parsed['query']; 
                $path       = $parsed['path']; 
                $videoID="";
                $Arr = explode('v=',$query);
                if(count($Arr)>0)
                {
                    $videoIDwithString = $Arr[1];
                    $videoID = substr($videoIDwithString,0,11);

                }
                $url=$img_path;
                $image_info=pathinfo($url);
                $image_extension=$image_info['extension'];
                $image_name=str_replace('-0', '', $image_info['filename']);
                $image_dir=$image_info['dirname'];
                if($aMedia['media_type']=='video')
                {
                    $autoembed = new AutoEmbed();
                    $video_content = $autoembed->parse($aMedia['media_name']);
                }
                else
                {
                    $video_content="";
                }
                $aMediaVars[] = array(
                    'media_id' => $aMedia['media_id'],
                    'media_title' => $aMedia['media_title'],
                    'media_image_path' => $img_path,
                    'media_video_id'=>$videoID,
                    'media_video_content'=>$video_content,
                    'media_pages_count'=>$aMedia['media_pages_count'],
                    'media_image_name'=>$image_name,
                    'image_extension'=>$image_extension,
                    'image_dir'=>$image_dir,
                    'media_type'=>$aMedia['media_type'],
                );
                 $allMediaVars[]=array(
                    'media_id' => $aMedia['media_id'],
                    'media_title' => $aMedia['media_title'],
                    'media_image_path' => $img_path,
                    'media_video_id'=>$videoID,
                    'media_video_content'=>$video_content,
                    'media_pages_count'=>$aMedia['media_pages_count'],
                    'media_image_name'=>$image_name,
                    'image_extension'=>$image_extension,
                    'image_dir'=>$image_dir,
                    'media_type'=>$aMedia['media_type'],
                );
            }

            

            $aInlineVars[] = array(
                'school_name' => $aEdu['edu_school_name'],
               
               // freddy change
			   //   'type' => $this->getEduTypeName($aEdu['edu_type']),
				'bx_if:type' => array( 
								'condition' =>$aEdu['edu_type'],
								'content' => array(
               'type' => $this->getEduTypeName($aEdu['edu_type']),
								), 
							),
				
				// freddy change
			   //  'period' => $aEdu['edu_period'],
				'bx_if:period' => array( 
								'condition' =>$aEdu['edu_period'],
								'content' => array(
                'period' => $aEdu['edu_period'],
								), 
							),
                
				// freddy change
			   // 'fieldofstudy' => $aEdu['edu_field_of_study'],
				'bx_if:fieldofstudy' => array( 
								'condition' =>$aEdu['edu_field_of_study'],
								'content' => array(
                'fieldofstudy' => $aEdu['edu_field_of_study'],
								), 
							),
                
				
				
				// freddy change
			   // 'desc' => $aEdu['edu_desc'],
				'bx_if:desc' => array( 
								'condition' =>$aEdu['edu_desc'],
								'content' => array(
                'desc' => $aEdu['edu_desc'],
								), 
							),
							
			    // freddy change
			   //  'activities' => $aEdu['edu_activities'],
				'bx_if:activities' => array( 
								'condition' =>$aEdu['edu_activities'],
								'content' => array(
                'activities' => $aEdu['edu_activities'],
								), 
							),		
							
                
				 // freddy change
			   //  'grade' => $aEdu['edu_grade'] == '' ? '' :  _t('_abs_plinkin_view_Grade').' : '.$aEdu['edu_grade'],
				'bx_if:grade' => array( 
								'condition' =>$aEdu['edu_grade'],
								'content' => array(
                'grade' => $aEdu['edu_grade'] == '' ? '' :  _t('_abs_plinkin_view_Grade').' : '.$aEdu['edu_grade'],
								), 
							),		
				
				
                'eduid' => $aEdu['edu_id'],
               
                'bx_repeat:abs_meds' => $aMediaVars,
            );
        }

        $education_status = $this->_oDb->getSettingStatus('education',$uid);
        $aVarsInfo = array(
            'bx_repeat:edus' => $aInlineVars,
            'bx_repeat:all_medias' => $allMediaVars,
            'style' => $education_status == '1' ? 'showit' : ($education_status == '0' ? 'hideit' : 'showit'),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_view_education.html',$aVarsInfo);
        return $res;
    }

    public function serviceProfileViewSkills($iId)
    {
        $uid = $iId;
        $skill_status = $this->_oDb->getSettingStatus('skill',$uid);
        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
        if($is_viewable == 'private' || $skill_status != '1')
            return '';

        $this->_oTemplate->addCss(array('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css','bootstrap-combined.min.css'));
        $aProfileUserEndorseSettings = $this->_oDb->getEndorseSettings($uid);
        $aLoggedUserEndorseSettings = $this->_oDb->getEndorseSettings(getLoggedId());
        if(getLoggedId() == $uid)
        {
            if($aLoggedUserEndorseSettings['endorse_me'] == '1' && $aLoggedUserEndorseSettings['endorse_by_friends'] == '1')
                return $this->viewSkills($uid);
        }
        else if(getLoggedId() != $uid)
        {
            $isFriend = $this->_oDb->isFriends(getLoggedId(),$uid);
            if($aProfileUserEndorseSettings['endorse_by_friends'] == '1' && $aProfileUserEndorseSettings['endorse_me'] == '1' && $aLoggedUserEndorseSettings['can_endorse'] == '1')
                return $this->viewSkills($uid, $isFriend);
        }

        $aSkills = $this->_oDb->getUserSkills($uid);
        if(empty($aSkills))
            return '';

        foreach ($aSkills as $aSkill) 
        {
            $skills_array[] = $aSkill['skill_name'];    
        }

        $arrlength = count($skills_array);
        for($x = 0; $x <  $arrlength; $x++) {
            $aInlineVars[] = array(
                'skills' =>  $skills_array[$x],       
            );
        }

        $skill_status = $this->_oDb->getSettingStatus('skill',$uid);

        $aVarsInfo = array(
            'bx_repeat:edus' => $aInlineVars,
        );

        $style = $skill_status == '1' ? 'showit' : ($skill_status == '0' ? 'hideit' : 'showit');

        return '<div class="skill_display_area '.$style.'" data-div="skill_display_area">'.$this->_oTemplate->parseHtmlByName('prof_skills.html',$aVarsInfo).'</div>';
    }

    public function serviceProfileViewLanguages($iId)
    {
        $uid = $iId;
        $lang_status = $this->_oDb->getSettingStatus('language',$uid);
        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$uid);
            if($is_viewable == 'private' || $lang_status != '1')
                return '';

        $LanguageProficiency = getPreValues('LanguageProficiency');
        
        $aLangs = $this->_oDb->getUserLanguages($uid);
        $aInlineVars = array();
        /*var_dump($LanguageProficiency);
        echo '<br><br>';
        var_dump($aLangs);
        exit;*/

        if(empty($aLangs))
            return '';

        foreach ($aLangs as $aLang) {
            
            $lang_key = $LanguageProficiency[$aLang['Proficiency']]["LKey"];
            $aInlineVars[] = array(
                'lang' => $aLang['lang'],
                'Proficiency' => _t($lang_key)
            );
        }
        
        $aVarsInfo = array(
            'bx_repeat:languages' => $aInlineVars,
            'style' => $lang_status == '1' ? '' : ($lang_status == '0' ? 'hideit' : 'showit' ),
        );
        $res = $this->_oTemplate->parseHtmlByName('prof_languages.html',$aVarsInfo);
        return $res;
    }

    public function serviceProfileViewProject($iId)
    {
        $project_status = $this->_oDb->getSettingStatus('project',$iId);
        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$iId);
        if($is_viewable == 'private'  || $project_status != '1')
            return '';

        $aExps = $this->_oDb->getUserProjects($iId);
        if(empty($aExps))
            return '';

        foreach ($aExps as $aExp) {
            $ex_title[$aExp['pro_id']] = $aExp['pro_name'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['pro_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('exp_id'=>$exp_key,'exp_title'=>$ex_tit,'exp_selected'=>$exp_key == $aExp['exp_id'] ? 'selected="selected"' : '');
            }

            $role = explode('-',$aExp['pro_occupation']);
            if(strtolower($role[0]) == 'student')
                $sRole = _t('_abs_plinkin_'.$role[0]) .' '._t('_abs_plinkin_at') .' '.$role[1];
            else
                
            //freddy change $sRole = $role[0] .' '._t('_abs_plinkin_at') .' '.$role[1];
			   $sRole = $role[0] .' '.'<i class="sys-icon certificate"></i>' .' '.$role[1];

            $aInlineVars[] = array(
                'projectname' => $aExp['pro_name'],
                'desc' => $aExp['pro_desc'],
                'period' => $textPeriod,
                'id' => $aExp['pro_id'],
               
                // freddy change  'projectTeam' => $aExp['pro_team'],
				'bx_if:projectTeam' => array( 
								'condition' =>$aExp['pro_team'],
								'content' => array(
                'projectTeam' => $aExp['pro_team'],
								), 
							),
				'projectOccupation'=> $sRole,
                
				
				// freddy change  'projectUrl' => $aExp['pro_url'],
				'bx_if:projectUrl' => array( 
								'condition' =>$aExp['pro_url'],
								'content' => array(
                'projectUrl' => $aExp['pro_url'],
								), 
							),
                'end_month' =>$aExp['pro_end_month'],
                'end_year' => $aExp['pro_end_year'],
                'usr_id' =>$aExp['pro_user_id'],
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'style' => $project_status != '1' ? 'hideit' : 'showit', 
        );
        $res = $this->_oTemplate->parseHtmlByName('public_project_setting.html',$aVarsInfo);
        return $res;
    }

    public function serviceProfileViewCertificate($iId)
    {
        $cert_status = $this->_oDb->getSettingStatus('certificate',$iId);
        $is_viewable = $this->_oDb->getSettingStatus('is_viewable',$iId);
        if($is_viewable == 'private' || $cert_status != '1')
            return '';

        $aExps = $this->_oDb->getUserCertificates($iId);

        if(empty($aExps))
            return '';

        foreach ($aExps as $aExp) {
            $ex_title[$aExp['cert_id']] = $aExp['cert_name'];
        }

        foreach ($aExps as $aExp) {
            $textPeriod = $this->getperiodToDisplay($aExp['cert_period']);
            foreach($ex_title as $exp_key=>$ex_tit){
                $media_select[] = array('cert_id'=>$exp_key,'cert_title'=>$ex_tit,'cert_selected'=>$exp_key == $aExp['cert_id'] ? 'selected="selected"' : '');
            }

            

            $aInlineVars[] = array(
                'certificatename' => $aExp['cert_name'],
                'period' => $textPeriod,
                'id' => $aExp['cert_id'],
               
               
			   
			   // freddy change 'certificateauthority' => $aExp['cert_authority'],
				'bx_if:certificateauthority' => array( 
								'condition' =>$aExp['cert_authority'],
								'content' => array(
                'certificateauthority' => $aExp['cert_authority'],
								), 
							),
               
                // freddy change 'certificatelicense' => $aExp['cert_license'],
				'bx_if:certificatelicense' => array( 
								'condition' =>$aExp['cert_license'],
								'content' => array(
                'certificatelicense' => $aExp['cert_license'],
								), 
							),
                 // fredd change 'certificateurl'=> $aExp['cert_url'],
			    'bx_if:certificateurl' => array( 
								'condition' =>$aExp['cert_url'],
								'content' => array(
                'certificateurl' => $aExp['cert_url'],
								), 
							),
                
                'end_month' =>$aExp['cert_end_month'],
                'end_year' => $aExp['cert_end_year'],
                'usr_id' =>$aExp['cert_user_id'],
            );
        }

        $aVarsInfo = array(
            'bx_repeat:exps' => $aInlineVars,
            'style' => $cert_status != '1' ? 'hideit' : 'showit', 
        );
        $res = $this->_oTemplate->parseHtmlByName('public_certificate_setting.html',$aVarsInfo);
        return $res;
    }
/* Profile View Page Info */

/* summary */

    function serviceProfileSummary()
    {
        return '<div class="prof_summary_main">'.$this->_ProfileSummary($iId).'</div>';
    }

    function _ProfileSummary()
    {
        $iUser = getLoggedId();
        $aSummaryInfo = $this->_oDb->getUserSummary($iUser);
        $aVarsInfo = array(
            'summary_text' => $aSummaryInfo['summary_text'],
            'summary_form' => $this->getSummaryForm(),
            'bx_if:summary_action' => array(
                'condition' => $aSummaryInfo['summary_text'] == '',
                'content' => array(
                ),
            ),
        );

        return $this->_oTemplate->parseHtmlByName('prof_summary_main.html',$aVarsInfo);
    }

    function getSummaryForm()
    {

        $aSummaryInfo = $this->_oDb->getUserSummary(getLoggedId());
        $aVarsInfo = array(
            'summary_text' => $aSummaryInfo['summary_text'] == '' ? '' : $aSummaryInfo['summary_text'],
            'summary_id' => $aSummaryInfo['summary_id'] == '' ? '' : $aSummaryInfo['summary_id'],
        );

        return $this->_oTemplate->parseHtmlByName('prof_summary_form.html',$aVarsInfo);

    }

    function actionGetsummaryform()
    {
        echo $this->getSummaryForm();
    }

    function actionpostSummary()
    {
		
		// Freddy add   >>>   $uid=getLoggedId();
		$uid=getLoggedId();
		
        $_POST['summary_userid'] = getLoggedId();
		
		
		 // Freddy change 
		// $this->_oDb->updateUserSummary($_POST);
        $this->_oDb->updateUserSummary($_POST,$uid);
		
		
       
        echo $this->_ProfileSummary();
    }

    function serviceProfileViewSummary($iUser)
    {
        $aSummaryInfo = $this->_oDb->getUserSummary($iUser);
        $aVarsInfo = array(
            'summary_text' => $aSummaryInfo['summary_text'],
        );

        return $this->_oTemplate->parseHtmlByName('profile_summary_view.html',$aVarsInfo);
    }

    function grabImageFromPdf($up_directory, $imgName)
    {
        $location   = "/usr/local/bin/convert";
        $name       = $up_directory . $imgName;
        $num = $this->count_pages($name);
        $rand = time();
        $nameto     = $up_directory . $rand.".jpg";
        $convert    = $location . " " . $name . " ".$nameto;
        exec($convert);
        if($num > 1)
            return array( $rand.'-0.jpg', $num);
        else
            return array( $rand.'.jpg', 1);
    }

    function count_pages($pdfname) {
          $pdftext = file_get_contents($pdfname);
          $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
          return $num;
    }

    function convertFileToPDF($up_directory, $imgName)
    {
        if( exec("libreoffice --headless --convert-to pdf ".$up_directory.$imgName." --outdir ".$up_directory, $output, $return_var) )
        {
            $ImageExt = substr($imgName, strrpos($imgName, '.'));
            $imgName = str_replace($ImageExt,'.pdf',$imgName);
            return $imgName;
        }
    }

    function actionpostTest()
    {
        $output = array();
        $return_var = 0;
        /*$directory = 'modules/abservetech/linkedin_profile/media_uploads/';
        $filename = 'letterlegal5';
        $extension = '.doc';*/
        exec("libreoffice --headless --convert-to pdf /modules/abservetech/linkedin_profile/media_uploads/4th_pahse.xlsx --outdir /modules/abservetech/linkedin_profile/media_uploads", $output, $return_var);

        var_dump($output);
        echo '<br>'.$return_var;
    }

    function getEduTypeName($eId)
    {
        return _t($eId);
    }

    function serviceGetProfileViewInfo($iId)
    {
        $aProfile = $this->_oDb->getProfile($iId);
        $usrTitle = $aProfile['abs_plinkin_user_title'];
        $aCompanies = $this->_oDb->getUserPrevCompanies($iId);
        $userLatestAcademyName = $this->_oDb->getUserLatestInstitute($iId);
        $aTmp = array();
        foreach ($aCompanies as $value) {
            $aTmp[] = $value['exp_companyName'];
        }
        $sCompanies = implode(',', $aTmp);
		
		
		// ajout freddy
		
        $aVars = array(
                'Name' => $aProfile['FirstName'] . ' '. $aProfile['LastName'],
                'bx_if:summary_action' => array(
                    'condition' => ($this->_oDb->getSettingStatus('headline',$iId) == '1' && $this->_oDb->getSettingStatus('is_viewable',$iId) == 'public'),
                    'content' => array(
                        'usr_title' => $usrTitle,
                    ),
                ),
                'city' => $aProfile['City'],
                'country' => _t($GLOBALS['aPreValues']['Country'][$aProfile['Country']]['LKey']),
                'companies' => $sCompanies,
               /* freddy change 
			    'uid' => getLoggedId(),
				*/
				
				 
				   'bx_if:TelechargerPDF' => array( 
								'condition' => getLoggedId(),
								'content' => array(
                     'uid' =>  $aProfile['ID'],
								), 
							),
			   
                'school_name' => $userLatestAcademyName,
                'userlink' => getProfileLink(getLoggedId()),
                'gender' => $aProfile['Sex'],
        );
        return $this->_oTemplate->parseHtmlByName('prof_view_page_info.html',$aVars);
    }

    function actionChangeAvatar()
    {
        error_reporting(0);
        require_once(BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/classes/BxAvaPageMain.php');
        $oAvatar = BxDolModule::getInstance('BxAvaModule');
        $oAvatarMain = new BxAvaPageMain($oAvatar);
        $res = $oAvatarMain->getBlockCode_Wide();
        echo $res[0];
        /*$aVars = array(
            'thumb_width' => "150",                   
            'thumb_height' => "150",
        );

        echo $this->_oTemplate->parseHtmlByName('avatar_upload_form.html', $aVars);*/
    }

    function actionUploadAvatar()
    {
        error_reporting(0);
        require_once(BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/classes/BxAvaPageMain.php');
        $oAvatar = BxDolModule::getInstance('BxAvaModule');
        $oAvatarMain = new BxAvaPageMain($oAvatar);
        $res = $oAvatarMain->getBlockCode_Wide();
        echo $res[0];
        /*$file_formats = array("jpg", "jpeg", "png", "gif", "bmp");

        $filepath = BX_DIRECTORY_PATH_MODULES ."abservetech/linkedin_profile/upload_images/";

        $name = $_FILES['imagefile']['name']; // filename to get file's extension
        $size = $_FILES['imagefile']['size'];

        if (strlen($name)) {
            $extension = substr($name, strrpos($name, '.')+1);
            if (in_array($extension, $file_formats)) { // check it if it's a valid format or not
                if ($size < (2048 * 1024)) { // check it if it's bigger than 2 mb or no
                    $imagename = md5(uniqid() . time()) . "." . $extension;
                    $tmp = $_FILES['imagefile']['tmp_name'];
                        if (move_uploaded_file($tmp, $filepath . $imagename)) {
                            echo $imagename;
                        } else {
                            echo "Could not move the file";
                        }
                } else {
                    echo "Your image size is bigger than 2MB";
                }
            } else {
                    echo "Invalid file format";
            }
        }*/
    }

    function actionCropAvatar()
    {
        error_reporting(0);
        $oBaseFunctions = bx_instance("BxBaseFunctions");
        require_once(BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/classes/BxAvaPageMain.php');
        $oAvatar = BxDolModule::getInstance('BxAvaModule');
        if($_POST['action'] == 'set_avatar')
        {
            if (!$oAvatar->isAllowedAdd ())
            $aVars = array ('msg' => _t('_bx_ava_msg_access_denied'));
            elseif (!$oAvatar->_cropAvatar ())
                $aVars = array ('msg' => _t('_bx_ava_set_avatar_error'));

            if (!empty($aVars))
                echo $aVars['msg'];
            else
            {
                $photo = $oBaseFunctions->getMemberAvatar(getLoggedId());
                $photo = str_replace("thumb", "eb", $photo);
                echo $photo;
            }
        }
        if ($_POST['action'] == 'remove_avatar') {
            $sImagePath = BX_DIRECTORY_PATH_MODULES . 'boonex/avatar/data/tmp/'. getLoggedId() . BX_AVA_EXT;
            if (@unlink($sImagePath)) {
                //$aVars = array ('msg' => _t('_bx_ava_msg_avatar_was_deleted'));
                $photo = $oBaseFunctions->getMemberAvatar(getLoggedId());
                $photo = str_replace("thumb", "eb", $photo);
                echo $photo;
            }
        }
    }

}
?>