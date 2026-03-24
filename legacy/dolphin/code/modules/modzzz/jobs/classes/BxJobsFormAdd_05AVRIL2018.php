<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import ('BxDolProfileFields');
bx_import ('BxDolFormMedia');

class BxJobsFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxJobsFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
		$this->_oTemplate = $oMain->_oTemplate;

		if(getParam('modzzz_listing_jobs_active')=='on')
			$aCompanies = $this->_oDb->getMergedCompanyList($this->_oMain->_iProfileId);   
		else
			$aCompanies = $this->_oDb->getCompanyList($this->_oMain->_iProfileId);   

		$aCategory = $this->_oDb->getFormCategoryArray();
		//Freddy ajout
		$aCategory[''] = ''._t('_Select').''; 
		
		$aSubCategory = array();
 
   		$bPaidJob = $this->_oMain->isAllowedPaidJob (); 
 
		$bListing = false;

		if($iEntryId) {
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
			$bPaidJob = $aDataEntry['invoice_no'] ? $bPaidJob : false;
 
			$iSelSubCategory = $aDataEntry['category_id']; 
			$iSelCategory = $this->_oDb->getParentCategoryById($iSelSubCategory); 
			$aSubCategory = $this->_oDb->getFormCategoryArray($iSelCategory);
  
			$sSelState = ($_POST['state']) ? $_POST['state'] : $aDataEntry['state']; 
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  

			if($aDataEntry['company_id']){ 
				$iListing = $aDataEntry['company_id'];
				$bListing = true;
				if(getParam("modzzz_listing_jobs_active")=='on' && $aDataEntry['company_type']=='listing'){ 
					$oListing = BxDolModule::getInstance('BxListingModule'); 
					$aListing = $oListing->_oDb->getEntryById($aDataEntry['company_id']);
					$sListingName = $aListing['title'];
 				}else{
					$sListingName = $this->_oMain->_oDb->getCompanyName($aDataEntry['company_id']);
  				}
			}
 
  			$sLogoName = $aDataEntry['icon'];//logo mod 

		}else {

			if(getParam('modzzz_listing_jobs_active')=='on'){ 
				$iListing = bx_get('business'); 
				if($iListing){
					$oListing = BxDolModule::getInstance('BxListingModule'); 
					$aListing = $oListing->_oDb->getEntryById($iListing);
					$sListingName = $aListing['title'];

					$bListing = true;
				}
 			} 
  
			$aProfile = getProfileInfo($this->_oMain->_iProfileId);
 	
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : getParam('modzzz_jobs_default_country'); 
			$sSelState = ($_POST['state']) ? $_POST['state'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry);   
  			 
			$sSelCompCountry = ($_POST['company_country']) ? $_POST['company_country'] : ''; 
			$sSelCompState = ($_POST['company_state']) ? $_POST['company_state'] : ''; 
			$aCompStates = $this->_oDb->getStateArray($sSelCompCountry);   

			$aSubCategory = ($_POST['parent_category_id']) ? $this->_oDb->getFormCategoryArray($_POST['parent_category_id']) : array(); 
		
			$sSelCurrency = ($_POST['currency']) ? $_POST['currency'] : '&#8364;';  
		}
 
		if($bPaidJob){
			$iPackageId = ($iEntryId) ? (int)$this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']) : (int)$_POST['package_id']; 
			$sPackageName = $this->_oDb->getPackageName($iPackageId);
		}
 
		$sCatUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_jobs_permalinks') ? '?' : '&').'ajax=cat&parent=' ;
 
		$sStateUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'home/'.(getParam('modzzz_jobs_permalinks') ? '?' : '&').'ajax=state&country=' ; 


		$this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_JOBS_PHOTOS_TAG,
                'cat' => BX_JOBS_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_modzzz_jobs_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_JOBS_VIDEOS_TAG,
                'cat' => BX_JOBS_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_modzzz_jobs_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_JOBS_SOUNDS_TAG,
                'cat' => BX_JOBS_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_modzzz_jobs_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_JOBS_FILES_TAG,
                'cat' => BX_JOBS_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_modzzz_jobs_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
	    $aCountries[''] = '--'._t('_Select').'--';
        
		$aExperienceList = $oProfileFields->convertValues4Input('#!JobExperience');
        ksort($aExperienceList);
        
		$aQualificationList = $oProfileFields->convertValues4Input('#!JobEducation');
        ksort($aQualificationList);
        
		$aCareerLevelList = $oProfileFields->convertValues4Input('#!JobCareerLevel');
        ksort($aCareerLevelList);
        
		$aSalaryTypeList = $oProfileFields->convertValues4Input('#!JobSalaryType');
        ksort($aSalaryTypeList);

		
		
		$aCurrencyList = $oProfileFields->convertValues4Input('#!JobCurrency');
       // asort($aCurrencyList);

		
		

		
		$aJobTypeList = $oProfileFields->convertValues4Input('#!JobType');
        ksort($aJobTypeList);

		$aJobStatusList = $oProfileFields->convertValues4Input('#!JobStatus');
        ksort($aJobStatusList);
 

        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);

        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($oMain->_iProfileId, $iEntryId);

        // privacy

   
        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'f', 'value' => _t('_modzzz_jobs_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );

        $aInputPrivacyCustom2 = array (
            array('key' => 'f', 'value' => _t('_modzzz_jobs_privacy_fans')),
            array('key' => 'a', 'value' => _t('_modzzz_jobs_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([fa]+)$/'),
        );
 
        $aInputPrivacyViewFans = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'view_fans');
        $aInputPrivacyViewFans['values'] = array_merge($aInputPrivacyViewFans['values'], $aInputPrivacyCustom);
 
		$aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'post_in_forum');
        $aInputPrivacyForum['values'] = array_merge($aInputPrivacyForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyForum['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;
 
		if(!$GLOBALS['oBxJobsModule']->isValidAccess(0, $iProfileId, 'post')){ 
 
			$iPostCost = $iMoneyBalance = 0;
	 
			if(getParam('modzzz_jobs_post_credits')=='on'){ 
	 
				$sPaymentUnit = 'credits'; 
				$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_credits_balance");
				$sMoneyTypeC = _t("_modzzz_jobs_credits");

				$oCredit = BxDolModule::getInstance('BxCreditModule');  
				$iMoneyBalance = (int)$oCredit->getMemberCredits($iProfileId);
				$iPostCost = (int)$oCredit->_oDb->getActionValue($iProfileId, 'modzzz_jobs', 'post');

			}elseif(getParam('modzzz_jobs_post_points')=='on'){
				
				$sPaymentUnit = 'points';
				$sMoneyBalanceLabelC = _t("_modzzz_jobs_form_caption_points_balance");
				$sMoneyTypeC = _t("_modzzz_jobs_points");

				$oPoint = BxDolModule::getInstance('BxPointModule');   
				$iMoneyBalance = $oPoint->_oDb->getMemberPoints($iProfileId);
				$iPostCost = (int)$oPoint->_oDb->getActionValue($iProfileId, 'modzzz_jobs', 'post');
			}
		 
			$aQuantity = array(); 
			for($iter=1; $iter<=1000; $iter++)
				$aQuantity[$iter] = $iter;
		}

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_jobs',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_jobs_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
              
            'inputs' => array(

				'header_payment' => array(
					'type' => 'block_header',
					'caption' => _t("_modzzz_jobs_form_caption_post", $sTitle),
				), 
				'money_balance' => array(
					'type' => 'custom',
					'caption' => $sMoneyBalanceLabelC,
					'content' => number_format($iMoneyBalance,0) .' '. $sMoneyTypeC,
				), 
				'post_cost' => array(
					'type' => 'custom',
 					'caption' => _t("_modzzz_jobs_form_caption_cost_to_post"),
					'content' => number_format($iPostCost,0) .' '. $sMoneyTypeC .' '. _t("_modzzz_jobs_form_per_day"),
				),   
                'quantity' => array(
                    'caption'  => _t('_modzzz_jobs_caption_num_post_days'),
                    'type'   => 'select',
                    'name' => 'quantity',
                    'values' => $aQuantity,
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_jobs_caption_err_post_days'),
                    ),
                ),  
					
                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_info')
                ), 
                'display_business' => array(
                    'type' => 'custom',
                    'name' => 'display_business',
                    'caption' => _t('_modzzz_jobs_form_caption_listed_by'),
                    'content' => $sListingName,
                ), 
				'company_idd' => array(
                    'type' => 'hidden',
                    'name' => 'company_id',
                    'value' => 'listing|'.$iListing, 
                ), 			
                'package_id' => array(
                    'type' => 'hidden',
                    'name' => 'package_id',
                    'value' => $iPackageId,  
                ),  
				'package_name' => array( 
					'type' => 'custom',
                    'content' => $sPackageName,  
                    'name' => 'package_name',
                    'caption' => _t('_modzzz_jobs_package'), 
                ),  
                'post_type' => array(
                    'type' => 'select',
                    'name' => 'post_type',
					'values'=> array(
								'provider'=>_t('_modzzz_jobs_provider'),
								'seeker'=>_t('_modzzz_jobs_seeker'),
							), 
                    'caption' => _t('_modzzz_jobs_form_caption_post_type'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
				
				/*
				 'header_select_company' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_select_company'), 
                    'collapsable' => false,
                    'collapsed' => false,
                ),
				*/ 
                'company_id' => array(
                    'type' => 'select',
                    'name' => 'company_id',
                    'caption' => _t('_modzzz_jobs_form_caption_company'),
                    'info' => _t('_modzzz_jobs_form_info_company'),
					'attrs' => array(
						'onchange' => "toggleCompanyBlock(this.options[this.selectedIndex].value)",
						'id' => "company_id",
					),	 
                     'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_company'),
                    ),
                    'values' => $aCompanies,   
                ), 
				  
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_jobs_form_caption_title'),
					'info' => _t('_modzzz_jobs_form_info_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_jobs_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),   
				
				 'parent_category_id' => array(
                    'type' => 'select',
                    'name' => 'parent_category_id',
					'values'=> $aCategory,
                    'value' => $iSelCategory,
                    'caption' => _t('_modzzz_jobs_form_caption_parent_category'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_category'),
                    ),
					'db' => array (
                        'pass' => 'Int', 
                    ),			
                ), 
                'category_id' => array(
                    'type' => 'select',
                    'name' => 'category_id',
					'values'=> $aSubCategory,
                    'value' => $iSelSubCategory, 
                    'caption' => _t('_modzzz_jobs_form_caption_sub_category'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ), 
				
				 'experience' => array(
                    'type' => 'select',
                    'name' => 'experience',
                    'listname' => 'JobExperience',
					'values'=> $aExperienceList,
                    'caption' => _t('_modzzz_jobs_form_caption_experience'), 
				/*	'attrs' => array (
                        'style' => 'float:left',
						
                    ),
					*/
					
                     'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_experience'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),  
                'qualification' => array(
                    'type' => 'select',
                    'name' => 'qualification',
                    'listname' => 'JobEducation',
					'values'=> $aQualificationList,
                    'caption' => _t('_modzzz_jobs_form_caption_qualification'), 
					
					
                     'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_qualification'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ), 
				
				  'career_level' => array(
                    'type' => 'select',
                    'name' => 'career_level',
					'values'=> $aCareerLevelList,
                    'caption' => _t('_modzzz_jobs_form_caption_career_level'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display'=>true

                ), 
                'job_type' => array(
                    'type' => 'select',
                    'name' => 'job_type',
                    'listname' => 'JobType',
					'values'=> $aJobTypeList,
                    'caption' => _t('_modzzz_jobs_form_caption_job_type'), 
                     'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_job_type'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ),
                'job_status' => array(
                    'type' => 'select',
                    'name' => 'job_status',
                    'listname' => 'JobStatus',
					'values'=> $aJobStatusList,
                    'caption' => _t('_modzzz_jobs_form_caption_job_status'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display' => 'getPreListDisplay', 
                ), 
				
				 /* 'vacancies' => array(
                    'type' => 'text',
                    'name' => 'vacancies', 
                    'caption' => _t('_modzzz_jobs_form_caption_vacancies'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
					'display'=>true
                ), 
				*/
				
				 'header_select_salary' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_salary'), 
                    'collapsable' => false,
                    'collapsed' => false,
                ),
				
				  'currency' => array(
                    'type' => 'select',
                    'name' => 'currency',
					'values'=> $aCurrencyList,
					
					'value'=> $sSelCurrency, 
                    'caption' => _t('_modzzz_jobs_form_caption_currency'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                 ),
				
				  'min_salary' => array(
                    'type' => 'text',
                    'name' => 'min_salary', 
                    'caption' => _t('_modzzz_jobs_form_caption_min_salary'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                 ),
                'max_salary' => array(
                    'type' => 'text',
                    'name' => 'max_salary', 
                    'caption' => _t('_modzzz_jobs_form_caption_max_salary'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                 ), 
              
                'salary_type' => array(
                    'type' => 'select',
                    'name' => 'salary_type',
					'values'=> $aSalaryTypeList,
                    'caption' => _t('_modzzz_jobs_form_caption_salary_type'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ), 
                ),
              
               
				             
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_jobs_form_caption_desc'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(200,64000),
                        'error' => _t ('_modzzz_jobs_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
				  'skills' => array(
                    'type' => 'textarea',
                    'name' => 'skills',
                    'caption' => _t('_modzzz_jobs_form_caption_skills'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),  
                ),               
                /*
			    'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_Tags'),
                    'info' => _t('_sys_tags_note'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_jobs_form_err_tags'),
                    ),
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),   
               
               'role' => array(
                    'type' => 'text',
                    'name' => 'role', 
                    'caption' => _t('_modzzz_jobs_form_caption_role'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
					'display'=>true 
                ), 
				*/
             
				 'header_application' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_application'), 
                    'collapsable' => false,
                    'collapsed' => false,
                ),
              
                'application_link' => array(
                    'type' => 'text',
                    'name' => 'application_link', 
                    'caption' => _t('_modzzz_jobs_form_caption_application_link'), 
                    'info' => _t('_modzzz_jobs_form_info_application_link'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                 ),
               
				/*
					
                'header_company' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_company'),
					'attrs' => array(
						'id' => "company_block",
					),	 
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'company_name' => array(
                    'type' => 'text',
                    'name' => 'company_name',
                    'caption' => _t('_modzzz_jobs_form_caption_name'),
					'attrs' => array(
						'id' => "company_name",
					),	 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'company_desc' => array(
                    'type' => 'textarea',
                    'name' => 'company_desc',
                    'caption' => _t('_modzzz_jobs_form_caption_desc'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),    
                'company_country' => array(
                    'type' => 'select',
                    'name' => 'company_country',
                    'caption' => _t('_modzzz_jobs_caption_country'),
                    'values' => $aCountries,
                    'required' => false, 
					'attrs' => array(
						'onchange' => "getHtmlData('compstate','$sStateUrl'+this.value)",
					),	
                     'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),  
					'display' => true,  
                ), 
 				'company_state' => array(
					'type' => 'select',
					'name' => 'company_state',
					'values'=> $aCompStates, 
					'value' => $sSelCompState,  
					'caption' => _t('_modzzz_jobs_caption_state'),
					'attrs' => array(
						'id' => 'compstate',
					), 
					'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
					'display' => true, 
				), 
 	            'company_city' => array(
                    'type' => 'text',
                    'name' => 'company_city',
                    'caption' => _t('_modzzz_jobs_form_caption_city'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
	            'company_address' => array(
                    'type' => 'text',
                    'name' => 'company_address',
                    'caption' => _t('_modzzz_jobs_form_caption_address'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  				
                'company_zip' => array(
                    'type' => 'text',
                    'name' => 'company_zip',
                    'caption' => _t('_modzzz_jobs_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'company_telephone' => array(
                    'type' => 'text',
                    'name' => 'company_telephone',
                    'caption' => _t('_modzzz_jobs_form_caption_telephone'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'company_fax' => array(
                    'type' => 'text',
                    'name' => 'company_fax',
                    'caption' => _t('_modzzz_jobs_form_caption_fax'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'company_website' => array(
                    'type' => 'text',
                    'name' => 'company_website',
                    'caption' => _t('_modzzz_jobs_form_caption_website'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'company_email' => array(
                    'type' => 'text',
                    'name' => 'company_email',
                    'caption' => _t('_modzzz_jobs_form_caption_email'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'employee_count' => array(
                    'type' => 'text',
                    'name' => 'employee_count',
                    'caption' => _t('_modzzz_jobs_form_caption_employee_count'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'office_count' => array(
                    'type' => 'text',
                    'name' => 'office_count',
                    'caption' => _t('_modzzz_jobs_form_caption_office_count'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				'company_icon' => array(
					'type' => 'custom',
					'name' => "company_icon",
					'caption' => _t('_modzzz_jobs_form_icon'), 
					'content' =>  "<input type=file  name='company_icon'>",  
				), 
				*/

                'header_location' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_location'), 
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'country' => array(
                    'type' => 'select',
                    'name' => 'country',
                    'caption' => _t('_modzzz_jobs_caption_country'),
                    'values' => $aCountries,
                    'required' => false, 
 					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	
                     'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
					'display' => true, 
                 ), 
 				'state' => array(
					'type' => 'select',
					'name' => 'state',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_modzzz_jobs_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
					'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
					'display' => 'getStateName', 
				), 
 	            'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_jobs_form_caption_city'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
	            'address1' => array(
                    'type' => 'text',
                    'name' => 'address1',
                    'caption' => _t('_modzzz_jobs_form_caption_address'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  				
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_jobs_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
				
 
                // logo 
             /*
			    'header_logo' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_logo'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				'presenticon' => array(
					'type' => 'custom',
					'name' => "presenticon", 
					'caption' => _t('_modzzz_jobs_present_icon'), 
					'content' =>  $this->_oDb->getLogo($iEntryId, $sLogoName) 
				),  
				'iconfile' => array(
					'type' => 'file',
					'name' => "iconfile",
					'caption' => _t('_modzzz_jobs_form_caption_icon'), 
				), 
				

                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_modzzz_jobs_form_caption_thumb_choice'),
                    'info' => _t('_modzzz_jobs_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),                
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_modzzz_jobs_form_caption_images_choice'),
                    'info' => _t('_modzzz_jobs_form_info_images_choice'),
                    'required' => false,
                ),
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_modzzz_jobs_form_caption_images_upload'),
                    'info' => _t('_modzzz_jobs_form_info_images_upload'),
                    'required' => false,
                ),

              // youtube videos
              /* 'header_youtube' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_jobs_form_header_youtube'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
			   
               'youtube_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['choice'],
                   'name' => 'youtube_choice[]',
                   'caption' => _t('_modzzz_jobs_form_caption_youtube_choice'),
                   'info' => _t('_modzzz_jobs_form_info_youtube_choice'),
                   'required' => false,
               ), 
               'youtube_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['upload'],
                   'name' => 'youtube_upload[]',
                   'caption' => _t('_modzzz_jobs_form_caption_youtube_attach'),
                   'info' => _t('_modzzz_jobs_form_info_youtube_attach'),
                   'required' => false,
               ),
 
 
                // videos
              
                'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_modzzz_jobs_form_caption_videos_choice'),
                    'info' => _t('_modzzz_jobs_form_info_videos_choice'),
                    'required' => false,
                ),
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_modzzz_jobs_form_caption_videos_upload'),
                    'info' => _t('_modzzz_jobs_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_modzzz_jobs_form_caption_sounds_choice'),
                    'info' => _t('_modzzz_jobs_form_info_sounds_choice'),
                    'required' => false,
                ),
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_modzzz_jobs_form_caption_sounds_upload'),
                    'info' => _t('_modzzz_jobs_form_info_sounds_upload'),
                    'required' => false,
                ),

                // files

                'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_modzzz_jobs_form_caption_files_choice'),
                    'info' => _t('_modzzz_jobs_form_info_files_choice'),
                    'required' => false,
                ),
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_modzzz_jobs_form_caption_files_upload'),
                    'info' => _t('_modzzz_jobs_form_info_files_upload'),
                    'required' => false,
                ),
				*/

                // privacy
                
               
			    'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_jobs_form_header_privacy'),
                ),
				
 
               // 'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 

               // 'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

              //  'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

              //  'allow_upload_files_to' => $aInputPrivacyUploadFiles, 
  
                'allow_view_job_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'view_job'),
 
				//'allow_view_fans_to' => $aInputPrivacyViewFans,

               // 'allow_comment_to' => $aInputPrivacyComment,

                //'allow_rate_to' => $aInputPrivacyRate, 

               // 'allow_post_in_forum_to' => $aInputPrivacyForum, 

               // 'allow_join_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'jobs', 'join'),  
            ),            
        );
 
		if($GLOBALS['oBxJobsModule']->isValidAccess(0, $iProfileId, 'post')){ 
            unset ($aCustomForm['inputs']['header_payment']);
            unset ($aCustomForm['inputs']['money_balance']);
            unset ($aCustomForm['inputs']['post_cost']);
            unset ($aCustomForm['inputs']['quantity']); 
		}

		if(getParam('modzzz_jobs_seeker_activated') != 'on'){ 
			$aCustomForm['inputs']['post_type']['type'] = 'hidden';
			$aCustomForm['inputs']['post_type']['value'] = 'provider';  
		}

		if(count($aCompanies)==1){
            unset ($aCustomForm['inputs']['company_id']);
		} 

        if (!$aCustomForm['inputs']['images_choice']['content']) {
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
        }

        if (!$aCustomForm['inputs']['videos_choice']['content'])
            unset ($aCustomForm['inputs']['videos_choice']);

        if (!$aCustomForm['inputs']['sounds_choice']['content'])
            unset ($aCustomForm['inputs']['sounds_choice']);

        if (!$aCustomForm['inputs']['files_choice']['content'])
            unset ($aCustomForm['inputs']['files_choice']);
		 
        if (!$aCustomForm['inputs']['youtube_choice']['content'])
            unset ($aCustomForm['inputs']['youtube_choice']);

		//[begin] added 7.1
       if (!isset($this->_aMedia['images'])) {
            unset ($aCustomForm['inputs']['header_images']);
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
            unset ($aCustomForm['inputs']['images_upload']);
            unset ($aCustomForm['inputs']['allow_upload_photos_to']);
        }

        if (!isset($this->_aMedia['videos'])) {
            unset ($aCustomForm['inputs']['header_videos']);
            unset ($aCustomForm['inputs']['videos_choice']);
            unset ($aCustomForm['inputs']['videos_upload']);
            unset ($aCustomForm['inputs']['allow_upload_videos_to']);
        }

        if (!isset($this->_aMedia['sounds'])) {
            unset ($aCustomForm['inputs']['header_sounds']);
            unset ($aCustomForm['inputs']['sounds_choice']);
            unset ($aCustomForm['inputs']['sounds_upload']);
            unset ($aCustomForm['inputs']['allow_upload_sounds_to']);
        }

        if (!isset($this->_aMedia['files'])) {
            unset ($aCustomForm['inputs']['header_files']);
            unset ($aCustomForm['inputs']['files_choice']);
            unset ($aCustomForm['inputs']['files_upload']);
            unset ($aCustomForm['inputs']['allow_upload_files_to']);
        }

        $oModuleDb = new BxDolModuleDb();
        if (!$oModuleDb->getModuleByUri('forum'))
            unset ($aCustomForm['inputs']['allow_post_in_forum_to']);
		//[end] added 7.1
 
		if($iEntryId || $bListing){
            unset ($aCustomForm['inputs']['header_select_company']);  
            unset ($aCustomForm['inputs']['company_id']); 
            unset ($aCustomForm['inputs']['header_company']);
            unset ($aCustomForm['inputs']['company_name']);
            unset ($aCustomForm['inputs']['company_desc']);
            unset ($aCustomForm['inputs']['company_country']);
            unset ($aCustomForm['inputs']['company_state']);
            unset ($aCustomForm['inputs']['company_address']);
            unset ($aCustomForm['inputs']['company_city']);
            unset ($aCustomForm['inputs']['company_zip']);
            unset ($aCustomForm['inputs']['company_email']);
            unset ($aCustomForm['inputs']['company_telephone']);
            unset ($aCustomForm['inputs']['company_fax']); 
            unset ($aCustomForm['inputs']['company_website']);
            unset ($aCustomForm['inputs']['employee_count']);
            unset ($aCustomForm['inputs']['office_count']);
 
            unset ($aCustomForm['inputs']['company_icon']);   
		}

		if(!$bListing){
            unset ($aCustomForm['inputs']['display_business']); 
            unset ($aCustomForm['inputs']['company_idd']);  
		}

		if (!$bPaidJob){
            unset ($aCustomForm['inputs']['package_name']);
            unset ($aCustomForm['inputs']['package_id']); 
		}

        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxJobsModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_modzzz_jobs_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
 
				'membership_view_filter' => array(
					'type' => 'select',
					'name' => 'membership_view_filter',
					'caption' => _t('_modzzz_jobs_caption_membership_view_filter'), 
					'info' => _t('_modzzz_jobs_info_membership_view_filter'), 
					'values' => $aMemberships,
					'value' => '', 
					'checker' => array (
					'func' => 'preg',
					'params' => array('/^[0-9a-zA-Z]*$/'),
					'error' => _t ('_modzzz_jobs_err_membership_view_filter'),
					),                                        
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([0-9a-zA-Z]*)/'),
					),
					
				),
  
            );
        } 

        $aFormInputsSubmit = array (
            'Submit' => array (
                'type' => 'submit',
                'name' => 'submit_form',
                'value' => _t('_Submit'),
                'colspan' => false,
            ),            
        );

        $aCustomForm['inputs'] = array_merge($aCustomForm['inputs'], $aFormInputsAdminPart, $aFormInputsSubmit);
   
        $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);

		if($bPaidJob){
			 $this->processPackageChecksForMediaUploads ($iPackageId, $aCustomForm['inputs']);
		}

		//logo mod
		if(!$sLogoName){
			unset($aCustomForm['inputs']['presenticon']); 
		}
 
        parent::BxDolFormMedia ($aCustomForm);
    }


   function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'jobs photos add', 'jobs sounds add', 'jobs videos add', 'jobs files add', 'jobs allow embed'));

		if (defined("BX_PHOTOS_ADD"))
			$aCheck = checkAction($_COOKIE['memberID'], BX_PHOTOS_ADD);
        
		if ((!defined("BX_PHOTOS_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
            unset($aInputs['thumb']);
        }

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_{$v}_ADD"));
            if ((!defined("BX_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']); 
            }        
        }

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_JOBS_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_JOBS_{$v}_ADD"));
            if ((!defined("BX_JOBS_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']);  
            }        
        } 

		$aCheck = checkAction($_COOKIE['memberID'],  BX_JOBS_ALLOW_EMBED);
		if ( $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) { 
			unset($aInputs['header_youtube']);
			unset($aInputs['youtube_choice']); 
			unset($aInputs['youtube_attach']); 
 		}

    }

    function processPackageChecksForMediaUploads ($iPackageId, &$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

		if($isAdmin)
		   return;

		$aPackage = $this->_oDb->getPackageById($iPackageId);

        $a = array ('images', 'videos', 'sounds', 'files' );
        foreach ($a as $k ) {
			$isAllowedMedia = $aPackage[$k];
            if ( !$isAllowedMedia ) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']);
 
 				if($k=='images'){
					unset($aInputs[$k.'_thumb']);
					unset($aInputs['allow_upload_photos_to']); 
				}else{
					unset($aInputs['allow_upload_'.$k.'_to']); 
				} 

 				if($k=='videos'){
					unset($aInputs['header_youtube']);
					unset($aInputs['youtube_choice']); 
					unset($aInputs['youtube_attach']); 
				} 
            }        
        }  
    }

    /**
     * process media upload updates
     * call it after successful call $form->insert/update functions 
     * @param $iEntryId associated entry id
     * @return nothing
     */ 
    function processAddMedia ($iEntryId, $iProfileId) { 

        $aDataEntry = $this->_oDb->getEntryById($iEntryId);

        foreach ($this->_aMedia as $sName => $a) {
			 
            $aFiles = $this->_getFilesInEntry ($a['module'], $a['service_method'], $a['post'], $sName, (int)$iProfileId, $iEntryId);
            foreach ($aFiles as $aRow)
                $aFiles2Delete[$aRow['id']] = $aRow['id'];

            if (is_array($_REQUEST[$a['post']]) && $_REQUEST[$a['post']] && $_REQUEST[$a['post']][0]) {
                $this->updateMedia ($iEntryId, $_REQUEST[$a['post']], $aFiles2Delete, $sName);
            } else {
                $this->deleteMedia ($iEntryId, $aFiles2Delete, $sName);
            }

            $sUploadFunc = $a['upload_func'];
            if ($aMedia = $this->$sUploadFunc($a['tag'], $a['cat'])) {
                $this->_oDb->insertMedia ($iEntryId, $aMedia, $sName);
                if ($a['thumb'] && !$aDataEntry[$a['thumb']] && !$_REQUEST[$a['thumb']]) 
                    $this->_oDb->setThumbnail ($iEntryId, 0);
            }

            $aMediaIds = $this->_oDb->getMediaIds($iEntryId, $sName);

            if ($a['thumb']) { // set thumbnail to another one if current thumbnail is deleted                
                $sThumbFieldName = $a['thumb'];
                if ($aDataEntry[$sThumbFieldName] && !isset($aMediaIds[$aDataEntry[$sThumbFieldName]])) {
                    $this->_oDb->setThumbnail ($iEntryId, 0);
                } 
            }

            // process all deleted media - delete actual file
			if(is_array($aFiles2Delete)){
				$aDeletedMedia = array_diff ($aFiles2Delete, $aMediaIds);
				if ($aDeletedMedia) {
					foreach ($aDeletedMedia as $iMediaId) {
						if (!$this->_oDb->isMediaInUse($iMediaId, $sName))
							BxDolService::call($a['module'], 'remove_object', array($iMediaId));
					}
				}
			}
        }

    }    

    /**
     * @access private
     */ 
    function _getFilesInEntry ($sModuleName, $sServiceMethod, $sName, $sMediaType, $iIdProfile, $iEntryId)
    {             

        $aReadyMedia = array ();
        if ($iEntryId)
            $aReadyMedia = $this->_oDb->getMediaIds($iEntryId, $sMediaType);
        
        if (!$aReadyMedia)
            return array();

        $aDataEntry = $this->_oDb->getEntryById($iEntryId);

        $aFiles = array ();
        foreach ($aReadyMedia as $iMediaId)
        {
            switch ($sModuleName) {
            case 'photos':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'icon'), 'Search');
                break;
            case 'sounds':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'browse'), 'Search');
                break;
            default:
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId), 'Search');
            }
    
            if (!$this->_oMain->isEntryAdmin($aDataEntry, $iIdProfile) && $aRow['owner'] != $iIdProfile)
                continue;

            $aFiles[] = array (
                'name' => $sName,
                'id' => $iMediaId,
                'title' => $aRow['title'],
                'icon' => $aRow['file'],
                'owner' => $aRow['owner'],
                'checked' => 'checked',
            );
        }
        return $aFiles;
    }        
 
	function generateCustomYoutubeTemplate ($iProfileId, $iEntryId) {
	 
		$aTemplates = array ();
	
		$aYoutubes = $this->_oDb->getYoutubeVideos ($iEntryId); 
 
		$aFeeds = array();
		foreach ($aYoutubes as $k => $r) {
			$aFeeds[$k] = array();
			$aFeeds[$k]['id'] = $r['id'];
			$aFeeds[$k]['video_id'] = $this->_oTemplate->youtubeId($r['url']);
			$aFeeds[$k]['video_title'] = $r['title'];
		}

		if(!empty($aFeeds)){
			$aVarsChoice = array ( 
				'bx_if:empty' => array(
					'condition' => empty($aFeeds),
					'content' => array ()
				),

				'bx_repeat:videos' => $aFeeds,
			);                               
			$aTemplates['choice'] =  $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube_choice', $aVarsChoice);
		}

		// upload form
		$aVarsUpload = array ();            
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube', $aVarsUpload);
 
		return $aTemplates;
	} 

}
