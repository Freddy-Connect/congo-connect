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

class BxProfessionalFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxProfessionalFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

		$this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
        $this->_oTemplate = $oMain->_oTemplate;
     		 
		$sDefaultTitle = stripslashes($_REQUEST['title']);

   		$bPaidProfessional = $this->_oMain->isAllowedPaidProfessionals (); 

		$aCategory = $this->_oDb->getFormCategoryArray();
		$aSubCategory = array();

		$sDefaultTitle = process_db_input($_REQUEST['title']);

		if($iEntryId) {
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
 
			$bPaidProfessional = $aDataEntry['invoice_no'] ? $bPaidProfessional : false;
			 
			$iSelSubCategory = $aDataEntry['category_id']; 
			$iSelCategory = $this->_oDb->getParentCategoryById($iSelSubCategory); 
			$aSubCategory = $this->_oDb->getFormCategoryArray($iSelCategory);
  
			$sSelState =  ($_POST['state']) ? $_POST['state'] : $aDataEntry['state']; 
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}else {
			$sLocationType = ($_REQUEST['type']) ? $_REQUEST['type'] : $_REQUEST['location_type'];
 
			$aProfile = getProfileInfo($this->_oMain->_iProfileId);
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aProfile['Country']; 
			$sSelState = ($_POST['state']) ? $_POST['state'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
  
			$aSubCategory = ($_POST['parent_category_id']) ? $this->_oDb->getFormCategoryArray($_POST['parent_category_id']) : array();
		}
  
		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/?ajax=state&country=' ; 
   
		$sCatUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/?ajax=cat&parent=' ;
  
		if($bPaidProfessional){
			$iPackageId = ($iEntryId) ? (int)$this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']) : (int)$_POST['package_id']; 
			$sPackageName = $this->_oDb->getPackageName($iPackageId);
		}
 
        $this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_PROFESSIONAL_PHOTOS_TAG,
                'cat' => BX_PROFESSIONAL_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_modzzz_professional_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_PROFESSIONAL_VIDEOS_TAG,
                'cat' => BX_PROFESSIONAL_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_modzzz_professional_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_PROFESSIONAL_SOUNDS_TAG,
                'cat' => BX_PROFESSIONAL_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_modzzz_professional_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_PROFESSIONAL_FILES_TAG,
                'cat' => BX_PROFESSIONAL_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_modzzz_professional_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );
 

        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
		$aCountries = array(''=>_t('_Select')) + $aCountries;

        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);

        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($oMain->_iProfileId, $iEntryId);

        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'f', 'value' => _t('_modzzz_professional_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );
 

        $aInputPrivacyCustom2 = array (
            array('key' => 'f', 'value' => _t('_modzzz_professional_privacy_fans')),
            array('key' => 'a', 'value' => _t('_modzzz_professional_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([fa]+)$/'),
        );


        $aInputPrivacyViewServices = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'view_services');
        $aInputPrivacyViewServices['values'] = array_merge($aInputPrivacyViewServices['values'], $aInputPrivacyCustom);
 
        $aInputPrivacyViewFans = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'view_fans');
        $aInputPrivacyViewFans['values'] = array_merge($aInputPrivacyViewFans['values'], $aInputPrivacyCustom);
 
		$aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'post_in_forum');
        $aInputPrivacyForum['values'] = array_merge($aInputPrivacyForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyForum['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;
   
        $aCustomScheduleTemplates = $this->generateCustomScheduleTemplate ($oMain->_iProfileId, $iEntryId);

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_professional',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_professional_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_info')
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
                    'caption' => _t('_modzzz_professional_package'), 
                ),
  
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_professional_form_caption_title'),
                    'value' => $sDefaultTitle,  
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_professional_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),                
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_professional_form_caption_desc'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_professional_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ), 
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_Tags'),
                    'info' => _t('_sys_tags_note'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_professional_form_err_tags'),
                    ),
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),   
                'parent_category_id' => array(
                    'type' => 'select',
                    'name' => 'parent_category_id',
					'values'=> $aCategory,
                    'value' => $iSelCategory,
                    'caption' => _t('_modzzz_professional_form_caption_categories'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_professional_form_err_category'),
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
                    'caption' => _t('_modzzz_professional_form_caption_subcategories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_professional_form_err_category'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),
  
               'header_contact' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_business_contact')
                ),  
                'proprietor' => array(
                    'type' => 'text',
                    'name' => 'proprietor',
                    'caption' => _t('_modzzz_professional_form_caption_proprietor'),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'businessname' => array(
                    'type' => 'text',
                    'name' => 'businessname',
                    'caption' => _t('_modzzz_professional_form_caption_businessname'),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'businessemail' => array(
                    'type' => 'email',
                    'name' => 'businessemail',
                    'caption' => _t('_modzzz_professional_form_caption_email'),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'businesstelephone' => array(
                    'type' => 'text',
                    'name' => 'businesstelephone',
                    'caption' => _t('_modzzz_professional_form_caption_telephone'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'businessfax' => array(
                    'type' => 'text',
                    'name' => 'businessfax',
                    'caption' => _t('_modzzz_professional_form_caption_fax'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  

                'header_location' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_business_location')
                ),  
				'country' => array(
                    'type' => 'select',
                    'name' => 'country',
					'listname' => 'Country',
                    'caption' => _t('_modzzz_professional_form_caption_country'),
                    'values' => $aCountries,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	 
                    'required' => true, 
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{2}$/'),
                        'error' => _t ('_modzzz_professional_form_err_country'),
                    ), 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
					'display' => 'getPreListDisplay', 
                ),
				'state' => array(
					'type' => 'select',
					'name' => 'state',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_modzzz_professional_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([0-9a-zA-Z]+)/'),
					), 
					'display' => 'getStateName',  
				), 
                'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_professional_form_caption_city'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,50),
                        'error' => _t ('_modzzz_professional_form_err_city'),
                    ), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => false,
                ),    
                'address1' => array(
                    'type' => 'text',
                    'name' => 'address1',
                    'caption' => _t('_modzzz_professional_form_caption_address1'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),   				
                'address2' => array(
                    'type' => 'text',
                    'name' => 'address2',
                    'caption' => _t('_modzzz_professional_form_caption_address2'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_professional_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
					
                'header_online' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_online')
                ), 
                'website' => array(
                    'type' => 'text',
                    'name' => 'website',
                    'caption' => _t('_modzzz_professional_form_caption_website'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'blog' => array(
                    'type' => 'text',
                    'name' => 'blog',
                    'caption' => _t('_modzzz_professional_form_caption_blog'), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'facebook' => array(
                    'type' => 'text',
                    'name' => 'facebook',
                    'caption' => _t('_modzzz_professional_form_caption_facebook'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'twitter' => array(
                    'type' => 'text',
                    'name' => 'twitter',
                    'caption' => _t('_modzzz_professional_form_caption_twitter'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'linkedin' => array(
                    'type' => 'text',
                    'name' => 'linkedin',
                    'caption' => _t('_modzzz_professional_form_caption_linkedin'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'youtube' => array(
                    'type' => 'text',
                    'name' => 'youtube',
                    'caption' => _t('_modzzz_professional_form_caption_youtube'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'pinterest' => array(
                    'type' => 'text',
                    'name' => 'pinterest',
                    'caption' => _t('_modzzz_professional_form_caption_pinterest'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
  
				'header_schedule' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_schedule')
                ),  
               'schedule_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomScheduleTemplates['choice'],
                   'name' => 'schedule_choice[]',
                   'caption' => _t('_modzzz_professional_form_caption_schedule_choice'),
                   'info' => _t('_modzzz_professional_form_info_schedule_choice'),
                   'required' => false,
               ), 
               'schedule_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomScheduleTemplates['upload'],
                   'name' => 'schedule_upload[]',
                   'caption' => _t('_modzzz_professional_form_caption_schedule_attach'),
                   'info' => _t('_modzzz_professional_form_info_schedule_attach'),
                   'required' => false,
               ),

 
                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_modzzz_professional_form_caption_thumb_choice'),
                    'info' => _t('_modzzz_professional_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ), 				 
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_modzzz_professional_form_caption_images_choice'),
                    'info' => _t('_modzzz_professional_form_info_images_choice'),
                    'required' => false,
                ),  
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_modzzz_professional_form_caption_images_upload'),
                    'info' => _t('_modzzz_professional_form_info_images_upload'),
                    'required' => false,
                ),
 
                // youtube videos
               'header_youtube' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_professional_form_header_youtube'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'youtube_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['choice'],
                   'name' => 'youtube_choice[]',
                   'caption' => _t('_modzzz_professional_form_caption_youtube_choice'),
                   'info' => _t('_modzzz_professional_form_info_youtube_choice'),
                   'required' => false,
               ), 
               'youtube_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['upload'],
                   'name' => 'youtube_upload[]',
                   'caption' => _t('_modzzz_professional_form_caption_youtube_attach'),
                   'info' => _t('_modzzz_professional_form_info_youtube_attach'),
                   'required' => false,
               ),
 
                // videos

                'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ), 
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_modzzz_professional_form_caption_videos_choice'),
                    'info' => _t('_modzzz_professional_form_info_videos_choice'),
                    'required' => false,
                ), 
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_modzzz_professional_form_caption_videos_upload'),
                    'info' => _t('_modzzz_professional_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_modzzz_professional_form_caption_sounds_choice'),
                    'info' => _t('_modzzz_professional_form_info_sounds_choice'),
                    'required' => false,
                ),
 
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_modzzz_professional_form_caption_sounds_upload'),
                    'info' => _t('_modzzz_professional_form_info_sounds_upload'),
                    'required' => false,
                ),

                // files

                'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_modzzz_professional_form_caption_files_choice'),
                    'info' => _t('_modzzz_professional_form_info_files_choice'),
                    'required' => false,
                ),
 
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_modzzz_professional_form_caption_files_upload'),
                    'info' => _t('_modzzz_professional_form_info_files_upload'),
                    'required' => false,
                ),

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_privacy'),
                ),
 
                'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 

                'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

                'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

                'allow_upload_files_to' => $aInputPrivacyUploadFiles, 
 
                'allow_view_professional_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'view_professional'),
                
				'allow_view_services_to' => $aInputPrivacyViewServices,
 
				'allow_view_fans_to' => $aInputPrivacyViewFans,
 
                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 

                'allow_post_in_forum_to' => $aInputPrivacyForum,  
  
                'allow_join_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'professional', 'join'),

                          
            ),            
        );

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
 
		if (!$bPaidProfessional){
            unset ($aCustomForm['inputs']['package_name']);
            unset ($aCustomForm['inputs']['package_id']); 
		}
 
		if (!$aCustomScheduleTemplates['choice']){
            unset ($aCustomForm['inputs']['schedule_choice']); 
		}

		if (!$aCustomYoutubeTemplates['choice']){
            unset ($aCustomForm['inputs']['youtube_choice']); 
		}

        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxProfessionalModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_modzzz_professional_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
 
				'membership_view_filter' => array(
					'type' => 'select',
					'name' => 'membership_view_filter',
					'caption' => _t('_modzzz_professional_caption_membership_view_filter'), 
					'info' => _t('_modzzz_professional_info_membership_view_filter'), 
					'values' => $aMemberships,
					'value' => '', 
					'checker' => array (
					'func' => 'preg',
					'params' => array('/^[0-9a-zA-Z]*$/'),
					'error' => _t ('_modzzz_professional_err_membership_view_filter'),
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

		if($bPaidProfessional){
			 $this->processPackageChecksForMediaUploads ($iPackageId, $aCustomForm['inputs']);
		}

        parent::BxDolFormMedia ($aCustomForm);
    }

    function processPackageChecksForMediaUploads ($iPackageId, &$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

		if($isAdmin) return;

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
            }        
        }  
    }

    function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'professional photos add', 'professional sounds add', 'professional videos add', 'professional files add'));
 
		if (defined("BX_PHOTOS_ADD")){  
			$aCheck = checkAction($_COOKIE['memberID'], BX_PHOTOS_ADD);
			if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) {
				unset($aInputs['thumb']);
			}
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
			if (defined("BX_PROFESSIONAL_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_PROFESSIONAL_{$v}_ADD"));
            if ((!defined("BX_PROFESSIONAL_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']); 
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

		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => empty($aFeeds),
				'content' => array ()
			), 
			'bx_repeat:videos' => $aFeeds,
		);                               
		$aTemplates['choice'] = empty($aFeeds) ? '' : $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube_choice', $aVarsChoice);
		
		// upload form
		$aVarsUpload = array ();            
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube', $aVarsUpload);
 
		return $aTemplates;
	} 
 
	function generateCustomScheduleTemplate ($iProfileId, $iEntryId) {
	 
		$aTemplates = array ();
	
		$aSchedule = $this->_oDb->getScheduleFeed ($iEntryId, 'schedule'); 

		$aFeeds = array();
		foreach ($aSchedule as $k => $r) {  
 
			$sFromDay = $this->_oDb->getDays($r['from_day']);
			$sToDay = $this->_oDb->getDays($r['to_day']);

			$iFromHour = $r['from_hour'];
			$iToHour = $r['to_hour'];

			$iFromMinute = $r['from_minute'];
			$iToMinute = $r['to_minute'];

 			$sFromPeriod = $this->_oDb->getPeriods($r['from_period']);
			$sToPeriod = $this->_oDb->getPeriods($r['to_period']);
 
			$aFeeds[$k] = array();
			$aFeeds[$k]['id'] = $r['id'];
			$aFeeds[$k]['name'] = "$sFromDay ($iFromHour:$iFromMinute $sFromPeriod) "._t('_modzzz_professional_to')." $sToDay ($iToHour:$iToMinute $sToPeriod)";
 		}

		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => empty($aFeeds),
				'content' => array ()
			), 
			'bx_repeat:schedule' => $aFeeds,
		);  
		
		$aTemplates['choice'] = empty($aFeeds) ? '' : $this->_oMain->_oTemplate->parseHtmlByName('form_field_schedule_choice', $aVarsChoice);
		
		$sSchedule = '';
		if($_REQUEST['schedule_from_day']){
			foreach($_REQUEST['schedule_from_day'] as $iKey=>$sValue){ 
		 
				$iToDay = $_REQUEST['schedule_to_day'][$iKey];
				$iFromHour = $_REQUEST['schedule_from_hour'][$iKey];
				$iFromMinute = $_REQUEST['schedule_from_minute'][$iKey];
				$iFromPeriod = $_REQUEST['schedule_from_period'][$iKey];
				$iToHour = $_REQUEST['schedule_to_hour'][$iKey];
				$iToMinute = $_REQUEST['schedule_to_minute'][$iKey];
				$iToPeriod = $_REQUEST['schedule_to_period'][$iKey];

				// upload form
				$aVarsUpload = array ( 
					'display' => (!$iKey) ? 'display:none;' : '', 
					'from_day_options' => $this->_oDb->getDayOptions($sValue), 
					'to_day_options' => $this->_oDb->getDayOptions($iToDay), 
					'from_hour_options' =>  $this->_oDb->getHourOptions($iFromHour), 
					'from_minute_options' => $this->_oDb->getMinuteOptions($iFromMinute), 
					'from_period_options' => $this->_oDb->getPeriodOptions($iFromPeriod), 
					'to_hour_options' => $this->_oDb->getHourOptions($iToHour), 
					'to_minute_options' => $this->_oDb->getMinuteOptions($iToMinute), 
					'to_period_options' => $this->_oDb->getPeriodOptions($iToPeriod),  
				);            
				$sSchedule .= $this->_oMain->_oTemplate->parseHtmlByName('form_field_schedule', $aVarsUpload);
			}
		 }else{
  
			// upload form
			$aVarsUpload = array (
				'display' => 'display:none;', 
				'from_day_options' => $this->_oDb->getDayOptions(), 
				'to_day_options' => $this->_oDb->getDayOptions(), 
				'from_hour_options' =>  $this->_oDb->getHourOptions(), 
				'from_minute_options' => $this->_oDb->getMinuteOptions(), 
				'from_period_options' => $this->_oDb->getPeriodOptions(), 
				'to_hour_options' => $this->_oDb->getHourOptions(), 
				'to_minute_options' => $this->_oDb->getMinuteOptions(), 
				'to_period_options' => $this->_oDb->getPeriodOptions(),  
 			);            
			$sSchedule = $this->_oMain->_oTemplate->parseHtmlByName('form_field_schedule', $aVarsUpload);
		 }

		 $aVarsWrap = array (
			'schedule' => $sSchedule 
		 );  

		 $aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_schedule_wrapper', $aVarsWrap);

		 return $aTemplates;
	} 




}
