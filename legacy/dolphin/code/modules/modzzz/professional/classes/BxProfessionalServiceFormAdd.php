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

class BxProfessionalServiceFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxProfessionalServiceFormAdd ($oMain, $iProfileId, $iProfessionalId = 0, $iEntryId = 0, $iThumb = 0) { 
        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
        $this->_oTemplate = $oMain->_oTemplate;

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

		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;
  		bx_import ('SubPrivacy', $this->_oMain->_aModule);
		$GLOBALS['oBxProfessionalModule']->_oSubPrivacy = new BxProfessionalSubPrivacy($this->_oMain, $this->_oDb->_sTableService); 
   
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
  
        $aInputPrivacyView = $GLOBALS['oBxProfessionalModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'professional', 'view');
        $aInputPrivacyView['values'] = array_merge($aInputPrivacyView['values'], $aInputPrivacyCustom);
        $aInputPrivacyView['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyComment = $GLOBALS['oBxProfessionalModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'professional', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $GLOBALS['oBxProfessionalModule']->_oSubPrivacy->getGroupChooser($iProfileId, 'professional', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;
  
        // generate templates for form custom elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($this->_oMain->_iProfileId, $iEntryId, $iThumb);

        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($this->_oMain->_iProfileId, $iEntryId);

        $oProfileFields = new BxDolProfileFields(0); 
        $aServiceLength = $oProfileFields->convertValues4Input('#!ServiceLength');
        $aServiceCurrency = array(''=>_t("_Select")) + $oProfileFields->convertValues4Input('#!ServiceCurrency'); 
   
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_services',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_professional_service_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_service_header_info')
                ),                
                'professional_id' => array(
                    'type' => 'hidden',
                    'name' => 'professional_id', 
                    'value' => $iProfessionalId,
                    'db' => array (
                        'pass' => 'Int' 
                    ) 
                 ),
                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_professional_form_caption_title'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3, $this->_oMain->_oConfig->getTitleLength()),
                        'error' => _t ('_modzzz_professional_form_err_title'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => false,
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
                        'error' => _t ('_modzzz_professional_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),  
                'length' => array(
                    'type' => 'select',
                    'name' => 'length',
                    'caption' => _t('_modzzz_professional_form_caption_length'),
                    'values' => $aServiceLength, 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1, 100),
                        'error' => _t ('_modzzz_professional_form_err_length'),
                    ),  
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),   
                'currency' => array(
                    'type' => 'select',
                    'name' => 'currency',
                    'caption' => _t('_modzzz_professional_form_caption_currency'),
                    'values' => $aServiceCurrency, 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(1, 100),
                        'error' => _t ('_modzzz_professional_form_err_currency'),
                    ), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),
                'price' => array(
                    'type' => 'text',
                    'name' => 'price',
                    'caption' => _t('_modzzz_professional_form_caption_price'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'Int', 
                        'error' => _t ('_modzzz_professional_form_err_price'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'price_type' => array(
                    'type' => 'select',
                    'name' => 'price_type',
                    'caption' => _t('_modzzz_professional_form_caption_price_type'),
                    'values' => array(
									'fixed'=>_t('_modzzz_professional_fixed'),
									'minimum'=>_t('_modzzz_professional_minimum'),
									'negotiable'=>_t('_modzzz_professional_negotiable')
								), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'status' => array(
                    'type' => 'select',
                    'name' => 'status',
                    'caption' => _t('_modzzz_professional_form_caption_display_service'),
                    'values' => array(
									'approved'=>_t('_modzzz_professional_yes'),
									'pending'=>_t('_modzzz_professional_no')
								), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ), 
                'booking' => array(
                    'type' => 'select',
                    'name' => 'booking',
                    'caption' => _t('_modzzz_professional_form_caption_book_service'),
                    'values' => array(
									'1'=>_t('_modzzz_professional_no'),
									'0'=>_t('_modzzz_professional_yes')
								), 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
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
 
                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_professional_form_header_privacy'),
                ),

                'allow_view_to' => $aInputPrivacyView,
  
				'Submit' => array (
					'type' => 'submit',
					'name' => 'submit_form',
					'value' => _t('_Submit'),
					'colspan' => false,
				),  

            ),            
        );
 
 		if (!$aCustomYoutubeTemplates['choice']){
            unset ($aCustomForm['inputs']['youtube_choice']); 
		}

        if (!$aCustomForm['inputs']['images_choice']['content']) {
            unset ($aCustomForm['inputs']['thumb']);
            unset ($aCustomForm['inputs']['images_choice']);
        }

        if (!$aCustomForm['inputs']['videos_choice']['content'])
            unset ($aCustomForm['inputs']['videos_choice']);
 
    
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


        parent::BxDolFormMedia ($aCustomForm);
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

        $aDataEntry = $this->_oDb->getServiceEntryById($iEntryId);

        $aFiles = array ();
        foreach ($aReadyMedia as $iMediaId)
        {
            switch ($sModuleName) {
            case 'photos':
                $aRow = BxDolService::call($sModuleName, $sServiceMethod, array($iMediaId, 'icon'), 'Search');
                break;
            case 'videos':
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

    /**
     * process media upload updates
     * call it after successful call $form->insert/update functions 
     * @param $iEntryId associated entry id
     * @return nothing
     */ 
    function processMedia ($iEntryId, $iProfileId) { 

        $aDataEntry = $this->_oDb->getServiceEntryById($iEntryId);
		
		$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;

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
            $aDeletedMedia = array_diff ($aFiles2Delete, $aMediaIds);
            if ($aDeletedMedia) {
                foreach ($aDeletedMedia as $iMediaId) {
                    if (!$this->_oDb->isMediaInUse($iMediaId, $sName))
                        BxDolService::call($a['module'], 'remove_object', array($iMediaId));
                }
            }
        }

    }

 
	function generateCustomYoutubeTemplate ($iProfileId, $iEntryId) {
	 
		$aTemplates = array ();
	
		$aYoutubes = $this->_oDb->getYoutubeVideos ($iEntryId, 'service'); 
 
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


}

