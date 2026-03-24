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

class BxGroupsFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxGroupsFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
		$this->_oTemplate = $oMain->_oTemplate;
 
		if($iEntryId){
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
 
			$sSelState = ($_POST['state']) ? $_POST['state'] : $aDataEntry['state']; 
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry); 
 
 			$sLogoName = $aDataEntry['icon'];//logo mod 
		}else{ 
			$aProfile = getProfileInfo($this->_oMain->_iProfileId); 
			$sSelCountry = ($_POST['country']) ? $_POST['country']  : getParam('bx_groups_default_country');  
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}
	 
 		$sStateUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home', 'ajax=state&country=');

        $this->_aMedia = array ();
        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_GROUPS_PHOTOS_TAG,
                'cat' => BX_GROUPS_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_bx_groups_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_GROUPS_VIDEOS_TAG,
                'cat' => BX_GROUPS_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_bx_groups_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_GROUPS_SOUNDS_TAG,
                'cat' => BX_GROUPS_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_bx_groups_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_GROUPS_FILES_TAG,
                'cat' => BX_GROUPS_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_bx_groups_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );


        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();        
 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
       
	    $aCountries = array(''=>_t('_Select')) + $aCountries;

        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);
 
        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($oMain->_iProfileId, $iEntryId);

        $aCustomRssTemplates = $this->generateCustomRssTemplate ($oMain->_iProfileId, $iEntryId);

        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'f', 'value' => _t('_bx_groups_privacy_fans_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );

        $aInputPrivacyCustom2 = array (
			array('key' => '', 'value' => '----'),
            array('key' => 'f', 'value' => _t('_bx_groups_privacy_fans')),
            array('key' => 'a', 'value' => _t('_bx_groups_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9fa]+)$/'),
        );

        $aInputPrivacyView = $GLOBALS['oBxGroupsModule']->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'view_group');
        $aInputPrivacyView['values'] = array_merge($aInputPrivacyView['values'], $aInputPrivacyCustom2);
        $aInputPrivacyView['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyViewFans = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'view_fans');
        $aInputPrivacyViewFans['values'] = array_merge($aInputPrivacyViewFans['values'], $aInputPrivacyCustom);

        $aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyViewComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'view_comment');
        $aInputPrivacyViewComment['values'] = array_merge($aInputPrivacyViewComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyViewComment['db'] = $aInputPrivacyCustomPass;


        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyViewForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'view_forum');
        $aInputPrivacyViewForum['values'] = array_merge($aInputPrivacyViewForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyViewForum['db'] = $aInputPrivacyCustomPass;
  
        $aInputPrivacyPostForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'post_in_forum');
        $aInputPrivacyPostForum['values'] = array_merge($aInputPrivacyPostForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyPostForum['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;

        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_groups',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'bx_groups_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_groups_form_header_info')
                ),                

                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_bx_groups_form_caption_title_form'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_bx_groups_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ),                
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_bx_groups_form_caption_desc'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_bx_groups_form_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'country' => array(
                    // 'type' => 'select',
				   'type' => 'hidden',
                    'name' => 'country',
                    'listname' => 'Country',
                    'caption' => _t('_bx_groups_form_caption_country'),
                    'values' => $aCountries, 
					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	
					'required' => false,
                    /*'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{2}$/'),
                        'error' => _t ('_bx_groups_form_err_country'),
                    ),*/                                     
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
					'caption' => _t('_bx_groups_caption_state'),
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
                    'caption' => _t('_bx_groups_form_caption_city'),
                    'required' => false,
                    /*'checker' => array (
                        'func' => 'length',
                        'params' => array(2,50),
                        'error' => _t ('_bx_groups_form_err_city'),
                    ),*/
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),                
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_bx_groups_form_caption_zip'),
                    'required' => false,
                    /*'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_groups_form_err_zip'),
                    ),*/
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),                                
                'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_Tags'),
                    'info' => _t('_sys_tags_note'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_groups_form_err_tags'),
                    ),
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),                

                'categories' => $oCategories->getGroupChooser ('bx_groups', (int)$iProfileId, true), 
 
				//RSS
               'header_rss' => array(
                   'type' => 'block_header',
                   'caption' => _t('_bx_groups_form_header_rss'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'rss_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomRssTemplates['choice'],
                   'name' => 'rss_choice[]',
                   'caption' => _t('_bx_groups_form_caption_rss_choice'),
                   'info' => _t('_bx_groups_form_info_rss_choice'),
                   'required' => false,
               ), 
               'rss_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomRssTemplates['upload'],
                   'name' => 'rss_upload[]',
                   'caption' => _t('_bx_groups_form_caption_rss_attach'),
                   'info' => _t('_bx_groups_form_info_rss_attach'),
                   'required' => false,
               ), 
	
                // logo 
                'header_logo' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_groups_form_header_logo'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				'presenticon' => array(
					'type' => 'custom',
					'name' => "presenticon", 
					'caption' => _t('_bx_groups_present_icon'), 
					'content' =>  $this->_oDb->getLogo($iEntryId, $sLogoName) 
				),  
				'iconfile' => array(
					'type' => 'file',
					'name' => "iconfile",
					'caption' => _t('_bx_groups_form_caption_icon'), 
				), 

                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_groups_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_bx_groups_form_caption_thumb_choice'),
                    'info' => _t('_bx_groups_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),                
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_bx_groups_form_caption_images_choice'),
                    'info' => _t('_bx_groups_form_info_images_choice'),
                    'required' => false,
                ),
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_bx_groups_form_caption_images_upload'),
                    'info' => _t('_bx_groups_form_info_images_upload'),
                    'required' => false,
                ),
   
                // youtube videos
               'header_youtube' => array(
                   'type' => 'block_header',
                   'caption' => _t('_bx_groups_form_header_youtube'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'youtube_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['choice'],
                   'name' => 'youtube_choice[]',
                   'caption' => _t('_bx_groups_form_caption_youtube_choice'),
                   'info' => _t('_bx_groups_form_info_youtube_choice'),
                   'required' => false,
               ), 
               'youtube_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['upload'],
                   'name' => 'youtube_upload[]',
                   'caption' => _t('_bx_groups_form_caption_youtube_attach'),
                   'info' => _t('_bx_groups_form_info_youtube_attach'),
                   'required' => false,
               ),

                // videos 
             /*
			    'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_groups_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_bx_groups_form_caption_videos_choice'),
                    'info' => _t('_bx_groups_form_info_videos_choice'),
                    'required' => false,
                ),
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_bx_groups_form_caption_videos_upload'),
                    'info' => _t('_bx_groups_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_groups_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_bx_groups_form_caption_sounds_choice'),
                    'info' => _t('_bx_groups_form_info_sounds_choice'),
                    'required' => false,
                ),
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_bx_groups_form_caption_sounds_upload'),
                    'info' => _t('_bx_groups_form_info_sounds_upload'),
                    'required' => false,
                ),
				*/

                // files

                'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_groups_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_bx_groups_form_caption_files_choice'),
                    'info' => _t('_bx_groups_form_info_files_choice'),
                    'required' => false,
                ),
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_bx_groups_form_caption_files_upload'),
                    'info' => _t('_bx_groups_form_info_files_upload'),
                    'required' => false,
                ),

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_groups_form_header_privacy'),
                ),

                //'allow_view_group_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'view_group'),

				//[begin] - ultimate groups mod from modzzz   
				'allow_view_group_to' => $aInputPrivacyView,
				//[end] - ultimate groups mod from modzzz  
 
                'allow_view_fans_to' => $aInputPrivacyViewFans,

                'allow_comment_to' => $aInputPrivacyComment,
                'allow_view_comment_to' => $aInputPrivacyViewComment,

                'allow_rate_to' => $aInputPrivacyRate, 

                'allow_post_in_forum_to' => $aInputPrivacyPostForum, 

                'allow_view_forum_to' => $aInputPrivacyViewForum, 
 
                'allow_join_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'groups', 'join'),

                'join_confirmation' => array (
                    'type' => 'select',
                    'name' => 'join_confirmation',
                    'caption' => _t('_bx_groups_form_caption_join_confirmation'),
                    'info' => _t('_bx_groups_form_info_join_confirmation'),
                    'values' => array(
                        0 => _t('_bx_groups_form_join_confirmation_disabled'),
                        1 => _t('_bx_groups_form_join_confirmation_enabled'),
                    ),
                    'checker' => array (
                        'func' => 'int',
                        'error' => _t ('_bx_groups_form_err_join_confirmation'),
                    ),                                        
                    'db' => array (
                        'pass' => 'Int', 
                    ),                    
                ),

                'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 

                'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

                'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

                'allow_upload_files_to' => $aInputPrivacyUploadFiles,  
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

        if (!$aCustomForm['inputs']['youtube_choice']['content'])
            unset ($aCustomForm['inputs']['youtube_choice']);

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


        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxGroupsModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_bx_groups_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
 
				'group_membership_view_filter' => array(
					'type' => 'select',
					'name' => 'group_membership_view_filter',
					'caption' => _t('_bx_groups_caption_membership_view_filter'), 
					'info' => _t('_bx_groups_info_membership_view_filter'), 
					'values' => $aMemberships,
					'value' => '', 
					'checker' => array (
					'func' => 'preg',
					'params' => array('/^[0-9a-zA-Z]*$/'),
					'error' => _t ('_bx_groups_err_membership_view_filter'),
					),                                        
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([0-9a-zA-Z]*)/'),
					),
					
				),
 
                'group_membership_filter' => array(
                    'type' => 'select',
                    'name' => 'group_membership_filter',
                    'caption' => _t('_bx_groups_caption_membership_join_filter'), 
                    'info' => _t('_bx_groups_info_membership_join_filter'), 
                    'values' => $aMemberships,
                    'value' => '', 
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[0-9a-zA-Z]*$/'),
                        'error' => _t ('_bx_groups_err_membership_join_filter'),
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
 
		//logo mod
		if(!$sLogoName){
			unset($aCustomForm['inputs']['presenticon']); 
		}

        parent::BxDolFormMedia ($aCustomForm);
    }
   
    function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'groups photos add', 'groups sounds add', 'groups videos add', 'groups files add'));
	
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
			if (defined("BX_GROUPS_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_GROUPS_{$v}_ADD"));
            if ((!defined("BX_GROUPS_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']); 
            }        
        } 
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

	function generateCustomRssTemplate ($iProfileId, $iEntryId) {
	 
		$aTemplates = array ();
	
		$aStaff = $this->_oDb->getRss ($iEntryId); 
 
		$aFeeds = array();
		foreach ($aStaff as $k => $r) {
			$aFeeds[$k] = array();
			$aFeeds[$k]['id'] = $r['id'];
			$aFeeds[$k]['name'] = $r['name'];
		}

		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => empty($aFeeds),
				'content' => array ()
			),

			'bx_repeat:feeds' => $aFeeds,
		);                               
		$aTemplates['choice'] =  $this->_oMain->_oTemplate->parseHtmlByName('form_field_rss_choice', $aVarsChoice);
		
		// upload form
		$aVarsUpload = array ();            
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_rss', $aVarsUpload);
 
		return $aTemplates;
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
        foreach ($aReadyMedia as $iMediaId) {
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

            if (!$this->_oMain->isAdmin() && !$this->_oMain->isEntryAdmin($aDataEntry, $iIdProfile) && $aRow['owner'] != $iIdProfile)
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