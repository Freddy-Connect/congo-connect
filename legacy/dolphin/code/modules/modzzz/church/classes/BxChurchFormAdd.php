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

class BxChurchFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxChurchFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

		$this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
     		
		$sDefaultTitle = stripslashes($_REQUEST['title']);

   		$bPaidChurch = $this->_oMain->isAllowedPaidChurches (); 

		$aCategory = $this->_oDb->getFormCategoryArray();
		$aSubCategory = array();
 
		if($iEntryId) {
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
			$iSelSubCategory = $aDataEntry['category_id']; 
			$iSelCategory = $this->_oDb->getParentCategoryById($iSelSubCategory); 
			$aSubCategory = $this->_oDb->getFormCategoryArray($iSelCategory);
  
			$sSelState = $aDataEntry['state']; 
			$sSelCountry = $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}else {
			$aProfile = getProfileInfo($this->_oMain->_iProfileId);
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aProfile['Country']; 
			$sSelState = ($_POST['state']) ? $_POST['state'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
  
			$aSubCategory = ($_POST['parent_categories']) ? $this->_oDb->getFormCategoryArray($_POST['parent_categories']) : array();
		  
		}
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/'.($this->_oMain->isPermalinkEnabled() ? '?' : '&').'ajax=state&country=' ; 
   
		$sCatUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home/'.($this->_oMain->isPermalinkEnabled() ? '?' : '&').'ajax=cat&parent=' ;
 
		if($bPaidChurch){
			$iPackageId = ($iEntryId) ? (int)$this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']) : (int)$_POST['package_id']; 
			$sPackageName = $this->_oDb->getPackageName($iPackageId);
		}

         $this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_CHURCH_PHOTOS_TAG,
                'cat' => BX_CHURCH_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_modzzz_church_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_CHURCH_VIDEOS_TAG,
                'cat' => BX_CHURCH_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_modzzz_church_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_CHURCH_SOUNDS_TAG,
                'cat' => BX_CHURCH_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_modzzz_church_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_CHURCH_FILES_TAG,
                'cat' => BX_CHURCH_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_modzzz_church_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );
        
 
		$aYesNo = array(
						1=>_t('_modzzz_church_yes'),
						0=>_t('_modzzz_church_no'),
					);


        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
      
        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);

        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'f', 'value' => _t('_modzzz_church_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );
 

        $aInputPrivacyCustom2 = array (
            array('key' => 'f', 'value' => _t('_modzzz_church_privacy_fans')),
            array('key' => 'a', 'value' => _t('_modzzz_church_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([fa]+)$/'),
        );


        $aInputPrivacyViewFans = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'view_fans');
        $aInputPrivacyViewFans['values'] = array_merge($aInputPrivacyViewFans['values'], $aInputPrivacyCustom);

		$aInputPrivacyDonors = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'view_donors');
        $aInputPrivacyDonors['values'] = array_merge($aInputPrivacyDonors['values'], $aInputPrivacyCustom);
        $aInputPrivacyDonors['db'] = $aInputPrivacyCustomPass;

		$aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'post_in_forum');
        $aInputPrivacyForum['values'] = array_merge($aInputPrivacyForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyForum['db'] = $aInputPrivacyCustomPass;

  
        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_church',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_church_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_info')
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
                    'caption' => _t('_modzzz_church_package'), 
                ),
 
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_church_form_caption_church_name'),
                    'value' => $sDefaultTitle,  
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_modzzz_church_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),                
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_church_form_caption_about_us'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(5,64000),
                        'error' => _t ('_modzzz_church_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'believe' => array(
                    'type' => 'textarea',
                    'name' => 'believe',
                    'caption' => _t('_modzzz_church_form_caption_believe'),
                    'required' => false,
                    'html' => 2, 
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),	 			
                'history' => array(
                    'type' => 'textarea',
                    'name' => 'history',
                    'caption' => _t('_modzzz_church_form_caption_history'),
                    'required' => false,
                    'html' => 0, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ),	
                'service_hours' => array(
                    'type' => 'textarea',
                    'name' => 'service_hours',
                    'caption' => _t('_modzzz_church_form_caption_service_hours'),
                    'required' => false,
                    'html' => 0, 
                    'db' => array (
                        'pass' => 'Xss', 
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
                        'error' => _t ('_modzzz_church_form_err_tags'),
                    ),
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),   
                'parent_categories' => array(
                    'type' => 'select',
                    'name' => 'parent_categories',
					'values'=> $aCategory,
                    'value' => $iSelCategory,
                    'caption' => _t('_modzzz_church_parent_categories'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_church_form_err_category'),
                    ), 
                ), 
                'category_id' => array(
                    'type' => 'select',
                    'name' => 'category_id',
					'values'=> $aSubCategory,
                    'value' => $iSelSubCategory, 
                    'caption' => _t('_Categories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_church_form_err_category'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ), 
                'capacity' => array(
                    'type' => 'text',
                    'name' => 'capacity',
                    'caption' => _t('_modzzz_church_form_caption_capacity'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
                'dress_code' => array(
                    'type' => 'text',
                    'name' => 'dress_code',
                    'caption' => _t('_modzzz_church_form_caption_dress_code'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
               'paypal' => array(
                    'type' => 'text',
                    'name' => 'paypal',
                    'caption' => _t('_modzzz_church_form_caption_paypal'),
                    'info' => _t('_modzzz_church_form_info_paypal'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'header_location' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_headquarters')
                ),  
				'country' => array(
                    'type' => 'select',
                    'name' => 'country',
					'listname' => 'Country',
                    'caption' => _t('_modzzz_church_form_caption_country'),
                    'values' => $aCountries,
 					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	 
                    'required' => false, 
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
					'caption' => _t('_modzzz_church_caption_state'),
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
                    'caption' => _t('_modzzz_church_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => false,
                ),    
                'address1' => array(
                    'type' => 'text',
                    'name' => 'address1',
                    'caption' => _t('_modzzz_church_form_caption_address1'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),   				
                'address2' => array(
                    'type' => 'text',
                    'name' => 'address2',
                    'caption' => _t('_modzzz_church_form_caption_address2'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_church_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
 
                'header_contact' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_contact')
                ), 
/*
                'businessname' => array(
                    'type' => 'text',
                    'name' => 'businessname',
                    'caption' => _t('_modzzz_church_form_caption_businessname'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
*/
                 'businesswebsite' => array(
                    'type' => 'text',
                    'name' => 'businesswebsite',
                    'caption' => _t('_modzzz_church_form_caption_businesswebsite'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'businessemail' => array(
                    'type' => 'email',
                    'name' => 'businessemail',
                    'caption' => _t('_modzzz_church_form_caption_businessemail'),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'businesstelephone' => array(
                    'type' => 'text',
                    'name' => 'businesstelephone',
                    'caption' => _t('_modzzz_church_form_caption_businesstelephone'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'businessfax' => array(
                    'type' => 'text',
                    'name' => 'businessfax',
                    'caption' => _t('_modzzz_church_form_caption_businessfax'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),  
 
                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_modzzz_church_form_caption_thumb_choice'),
                    'info' => _t('_modzzz_church_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ), 				 
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_modzzz_church_form_caption_images_choice'),
                    'info' => _t('_modzzz_church_form_info_images_choice'),
                    'required' => false,
                ),  
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_modzzz_church_form_caption_images_upload'),
                    'info' => _t('_modzzz_church_form_info_images_upload'),
                    'required' => false,
                ),

				// embed video
				'header_video_embed' => array(
					'type' => 'block_header',
					'caption' => _t('_modzzz_church_form_header_video_embed'),
					'collapsable' => true,
					'collapsed' => false,
				),
				'video_embed' => array(
					'type' => 'text',
					'name' => 'video_embed',
					'caption' => _t('_modzzz_church_caption_video_embed_code'),
					'info' => _t('_modzzz_church_form_info_video_embed_code'),
					'attrs'     => array('onclick' => 'this.focus();this.select();'),
					'required' => false,
					'html' => 2,      
					'db' => array (
					'pass' => 'XssHtml', 
					),  
				 ),
 
                // videos

                'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_modzzz_church_form_caption_videos_choice'),
                    'info' => _t('_modzzz_church_form_info_videos_choice'),
                    'required' => false,
                ),
  
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_modzzz_church_form_caption_videos_upload'),
                    'info' => _t('_modzzz_church_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_modzzz_church_form_caption_sounds_choice'),
                    'info' => _t('_modzzz_church_form_info_sounds_choice'),
                    'required' => false,
                ),
 
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_modzzz_church_form_caption_sounds_upload'),
                    'info' => _t('_modzzz_church_form_info_sounds_upload'),
                    'required' => false,
                ),

                // files

                'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
 
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_modzzz_church_form_caption_files_choice'),
                    'info' => _t('_modzzz_church_form_info_files_choice'),
                    'required' => false,
                ),
 
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_modzzz_church_form_caption_files_upload'),
                    'info' => _t('_modzzz_church_form_info_files_upload'),
                    'required' => false,
                ),

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_church_form_header_privacy'),
                ),
 
                'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 

                'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

                'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

                'allow_upload_files_to' => $aInputPrivacyUploadFiles, 
 
                'allow_view_church_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'view_church'),
                
				'allow_view_fans_to' => $aInputPrivacyViewFans,
 
                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 

                'allow_post_in_forum_to' => $aInputPrivacyForum,  
  
                'allow_join_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'church', 'join'),
 
                'allow_view_donors_to' => $aInputPrivacyDonors,  
            ),            
        );
 
        if ( !$this->_oMain->isAllowedAcceptDonation($iProfileId) ) {
            unset ($aCustomForm['inputs']['paypal']);
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
 
		if (!$bPaidChurch){
            unset ($aCustomForm['inputs']['package_name']);
            unset ($aCustomForm['inputs']['package_id']); 
		}

        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxChurchModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_modzzz_church_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
 
				'membership_view_filter' => array(
					'type' => 'select',
					'name' => 'membership_view_filter',
					'caption' => _t('_modzzz_church_caption_membership_view_filter'), 
					'info' => _t('_modzzz_church_info_membership_view_filter'), 
					'values' => $aMemberships,
					'value' => '', 
					'checker' => array (
					'func' => 'preg',
					'params' => array('/^[0-9a-zA-Z]*$/'),
					'error' => _t ('_modzzz_church_err_membership_view_filter'),
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

		if($bPaidChurch){
			 $this->processPackageChecksForMediaUploads ($iPackageId, $aCustomForm['inputs']);
		}

        parent::BxDolFormMedia ($aCustomForm);
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
					unset($aInputs['header_video_embed']);
					unset($aInputs['video_embed']); 
				} 
            }        
        }  
    }

    function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'church photos add', 'church sounds add', 'church videos add', 'church files add'));

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

				if(($k=='videos') && (getParam("modzzz_church_allow_embed")!="on")){
					unset($aInputs['header_video_embed']);
					unset($aInputs['video_embed']); 
				}  
            }        
        }

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
			if (defined("BX_CHURCH_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_CHURCH_{$v}_ADD"));
            if ((!defined("BX_CHURCH_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']);

				if(($k=='videos') && (getParam("modzzz_church_allow_embed")!="on")){
					unset($aInputs['header_video_embed']);
					unset($aInputs['video_embed']); 
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



}