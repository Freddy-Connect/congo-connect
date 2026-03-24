<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Article
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

class BxArticlesFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxArticlesFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
        $this->_oTemplate = $oMain->_oTemplate;
  
  		$sDefaultTitle = stripslashes($_REQUEST['title']);
  
		$aCategory = $this->_oDb->getFormCategoryArray();
		// freddy add $aCategory[''] = '--'._t('_Select').'--'; 
		$aCategory[''] = '--'._t('_Select_une_category').'--'; 
		$aSubCategory = array();
   
 		$sStateUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home', 'ajax=state&country=');
 
 		$sCatUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home', 'ajax=cat&parent=');
 
		if($iEntryId){
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);

			$iSelSubCategory = $aDataEntry['category_id']; 
			$iSelCategory = $aDataEntry['parent_category_id']; 
			$aSubCategory = $this->_oDb->getFormCategoryArray($iSelCategory);
 
			$iPublishDate = date('Y-m-d H:i', $aDataEntry['when']);

			$sSelState = ($_POST['state']) ? $_POST['state'] : $aDataEntry['state']; 
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : $aDataEntry['country'];  
			$aStates = $this->_oDb->getStateArray($sSelCountry); 

 			$sBgName = $aDataEntry['background']; 
		}else{

			$aSubCategory = ($_POST['parent_category_id']) ? $this->_oDb->getFormCategoryArray($_POST['parent_category_id']) : array();
 
			$iPublishDate = date('Y-m-d H:i');
 
			$sSelState = ($_POST['state']) ? $_POST['state'] : '';  
			$sSelCountry = ($_POST['country']) ? $_POST['country'] : ''; 
			$aStates = $this->_oDb->getStateArray($sSelCountry); 
		}
   
        $this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_ARTICLES_PHOTOS_TAG,
                'cat' => BX_ARTICLES_PHOTOS_CAT,
                'thumb' => 'thumb',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_modzzz_articles_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_ARTICLES_VIDEOS_TAG,
                'cat' => BX_ARTICLES_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_modzzz_articles_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_ARTICLES_SOUNDS_TAG,
                'cat' => BX_ARTICLES_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_modzzz_articles_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_ARTICLES_FILES_TAG,
                'cat' => BX_ARTICLES_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_modzzz_articles_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );

		require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
		$aMemberships = getMemberships ();
		unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
		$aMemberships = array('' => _t('_modzzz_articles_membership_filter_none')) + $aMemberships;
       

        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
		$aCountries = array(''=>_t('_Select')) + $aCountries;

        // generate templates for custom form's elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($oMain->_iProfileId, $iEntryId, $iThumb);

        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($oMain->_iProfileId, $iEntryId);

        // privacy

         $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9f]+)$/'),
        );

  
        $aInputPrivacyComment = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'articles', 'comment');
        $aInputPrivacyComment['values'] = $aInputPrivacyComment['values'];
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'articles', 'rate');
        $aInputPrivacyRate['values'] = $aInputPrivacyRate['values'];
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;
  
        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_articles',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'modzzz_articles_main',
                    'key' => 'id',
                    'uri' => 'uri',
                    'uri_title' => 'title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_info')
                ),                

                'title' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'caption' => _t('_modzzz_articles_form_caption_title'),
                    'value' => $sDefaultTitle,
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,$this->_oMain->_oConfig->getTitleLength()),
                        'error' => _t ('_modzzz_articles_form_err_title'),
                    ),
					
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    
                ),
				   
				
				'parent_category_id' => array(
                    'type' => 'select',
                    'name' => 'parent_category_id',
					'values'=> $aCategory,
                    'value' => $iSelCategory,
                    'caption' => _t('_modzzz_articles_form_caption_categories'),
					'attrs' => array(
					 'style' => 'width:250px',
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => true,
                   
				    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_articles_form_err_category'),
                    ), 
					
                    'db' => array (
                        'pass' => 'Int', 
                    ), 
                ), 
				/*
                'category_id' => array(
                    'type' => 'select',
                    'name' => 'category_id',
					'values'=> $aSubCategory,
                    'value' => $iSelSubCategory, 
                    'caption' => _t('_modzzz_articles_form_caption_subcategories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false,
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_articles_form_err_category'),
                    ),
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ), 
 
               
			    'snippet' => array(
                    'type' => 'textarea',
                    'html' => 0,
                    'name' => 'snippet',
                    'caption' => _t("_modzzz_articles_form_caption_snippet"),
                    'value' => '',
                	'required' => 1,
					'attrs' => array(
						'id' => "snip",
					), 
                    'checker' => array (  
                        'func' => 'length',
                        'params' => array(3, $this->_oMain->_oConfig->getSnippetLength()),
                        'error' => _t('_modzzz_articles_form_info_snippet', $this->_oMain->_oConfig->getSnippetLength()),
                    ),                    
                    'db' => array (
                        'pass' => 'Xss',
                    ),
                ),
				*/
					 
                'desc' => array(
                    'type' => 'textarea',
                    'name' => 'desc',
                    'caption' => _t('_modzzz_articles_form_caption_desc'),
                    'required' => false,
                    'html' => 2,
                   /*
				    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_modzzz_articles_form_err_desc'),
                    ), 
					*/                   
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
				/*
				'anonymous' => array(
                    'type' => 'select',
                    'name' => 'anonymous',
                    'caption' => _t('_modzzz_articles_form_caption_anonymous'),
                    'info' => _t('_modzzz_articles_anonymous_note'), 
                    'required' => false, 
                    'values' => array(
                        0 => _t('_modzzz_articles_disabled'),
						1 => _t('_modzzz_articles_enabled')  
                    ),  
                    'db' => array (
                        'pass' => 'Int',  
                    ),
                    'display' => false,
                    //'checked' => true,

                ),
				*/

               /*
				'country' => array(
                    'type' => 'select',
                    'name' => 'country',
                    'caption' => _t('_modzzz_articles_form_caption_country'),
                    'values' => $aCountries,
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),
                    'display' => true,
                ),
                'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_articles_form_caption_city'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),                
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_articles_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
				

				 
				
  
               */
              
			   'tags' => array(
                    'type' => 'text',
                    'name' => 'tags',
                    'caption' => _t('_modzzz_articles_form_caption_tag'),
                    'info' => _t('_sys_tags_note'),
                    'required' => false,
                    
					/*'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_articles_form_err_tags'),
                    ),
					*/
					
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),  
			  
			  /* 
                'website' => array(
                    'type' => 'text',
                    'name' => 'website',
                    'caption' => _t('_modzzz_articles_form_caption_website'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),

				


               'header_location' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_location')
                ), 
                'country' => array(
                    'type' => 'select',
                    'name' => 'country',
					'listname' => 'Country',
                    'caption' => _t('_modzzz_articles_form_caption_country'),
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
					'display' => 'parsePreValues', 
                ),
 				'state' => array(
					'type' => 'select',
					'name' => 'state',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_modzzz_articles_form_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
				    'db' => array (
						'pass' => 'Xss',  
					), 
                    'display' => 'getStateName',
				), 
                'city' => array(
                    'type' => 'text',
                    'name' => 'city',
                    'caption' => _t('_modzzz_articles_form_caption_city'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ), 
               
			   /*
			    'street' => array(
                    'type' => 'text',
                    'name' => 'street',
                    'caption' => _t('_modzzz_articles_form_caption_address'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'zip' => array(
                    'type' => 'text',
                    'name' => 'zip',
                    'caption' => _t('_modzzz_articles_form_caption_zip'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),   
				
				*/       

                // background 
              /*  'header_background' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_background'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				'presenticon' => array(
					'type' => 'custom',
					'name' => "presenticon", 
					'caption' => _t('_modzzz_articles_present_background'), 
					'content' =>  $this->_oDb->getBackground($sBgName, false, $iEntryId, true) 
				),  
				'iconfile' => array(
					'type' => 'file',
					'name' => "iconfile",
					'caption' => _t('_modzzz_articles_form_caption_background'), 
				), 
				*/
 
                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'thumb' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'thumb',
                    'caption' => _t('_modzzz_articles_form_caption_thumb_choice'),
                    'info' => _t('_modzzz_articles_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),                
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_modzzz_articles_form_caption_images_choice'),
                    'info' => _t('_modzzz_articles_form_info_images_choice'),
                    'required' => false,
                ),
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_modzzz_articles_form_caption_images_upload'),
                    'info' => _t('_modzzz_articles_form_info_images_upload'),
                    'required' => false,
                ),

				// embed video 
               'header_youtube' => array(
                   'type' => 'block_header',
                   'caption' => _t('_modzzz_articles_form_header_youtube'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'youtube_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['choice'],
                   'name' => 'youtube_choice[]',
                   'caption' => _t('_modzzz_articles_form_caption_youtube_choice'),
                   'info' => _t('_modzzz_articles_form_info_youtube_choice'),
                   'required' => false,
               ), 
               'youtube_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['upload'],
                   'name' => 'youtube_upload[]',
                   'caption' => _t('_modzzz_articles_form_caption_youtube_attach'),
                   'info' => _t('_modzzz_articles_form_info_youtube_attach'),
                   'required' => false,
               ),
 
                // videos 
				/*
                'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_modzzz_articles_form_caption_videos_choice'),
                    'info' => _t('_modzzz_articles_form_info_videos_choice'),
                    'required' => false,
                ),
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_modzzz_articles_form_caption_videos_upload'),
                    'info' => _t('_modzzz_articles_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_modzzz_articles_form_caption_sounds_choice'),
                    'info' => _t('_modzzz_articles_form_info_sounds_choice'),
                    'required' => false,
                ),
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_modzzz_articles_form_caption_sounds_upload'),
                    'info' => _t('_modzzz_articles_form_info_sounds_upload'),
                    'required' => false,
                ),

                // files

                'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_modzzz_articles_form_caption_files_choice'),
                    'info' => _t('_modzzz_articles_form_info_files_choice'),
                    'required' => false,
                ),
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_modzzz_articles_form_caption_files_upload'),
                    'info' => _t('_modzzz_articles_form_info_files_upload'),
                    'required' => false,
                ),
				
				*/
				'publish' => array(
                    'type' => 'select',
                    'name' => 'publish',
                    'caption' => _t('_modzzz_articles_form_caption_publish_now'),
                    'info' => _t('_modzzz_articles_form_info_publish_now'),
					'values' => array(1=>_t('_Yes'), 0=>_t('_No')),
                    'required' => false,  
					'db' => array (
                        'pass' => 'Int',  
                    ),
                 ),
  
                'when' => array(
                    'type' => 'datetime',
                    'name' => 'when',
                    'caption' => _t("_modzzz_articles_form_caption_publish_date"),
                    'value' => $iPublishDate, 
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),
                ),

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_modzzz_articles_form_header_privacy'),
                ),

                'allow_view_article_to' => $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'articles', 'view_article'),
 
                'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 
/*
                'allow_rate_to' => array(
                    'type' => 'hidden',
                    'name' => 'allow_rate_to',
                    'value' => 4, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),   
                'allow_comment_to' => array(
                    'type' => 'hidden',
                    'name' => 'allow_comment_to',
                    'value' => 4, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ) 
                ),  
*/

      
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
		//[end] added 7.1


        $aFormInputsSubmit = array (
            'Submit' => array (
                'type' => 'submit',
                'name' => 'submit_form',
                'value' => _t('_Submit'),
             ),            
        );

        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxArticlesModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_modzzz_articles_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
                'article_membership_filter' => array(
                    'type' => 'select_box',
                    'name' => 'article_membership_filter',
                    'caption' => _t('_modzzz_articles_caption_membership_filter'), 
                    'info' => _t('_modzzz_articles_info_membership_filter'), 
                    'values' => $aMemberships, 
                    'db' => array (
						'pass' => 'Categories' 
                    ),
                    'attrs' => array (
						'add_other' => false 
                    ),
	 
                ),
            );
        }
 
		$aCustomForm['inputs'] = array_merge($aCustomForm['inputs'], $aFormInputsAdminPart);
		$aCustomForm['inputs'] = array_merge($aCustomForm['inputs'], $aFormInputsSubmit);
 
		if(!$sBgName){
			unset($aCustomForm['inputs']['presenticon']); 
		}

		if(getParam('modzzz_articles_bgswitcher_activated')!='on'){ 
			unset($aCustomForm['inputs']['header_background']); 
			unset($aCustomForm['inputs']['presenticon']); 
			unset($aCustomForm['inputs']['iconfile']);  
		}
 
        $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);
 
        parent::BxDolFormMedia ($aCustomForm);
    }
 
	function processMembershipChecksForMediaUploads (&$aInputs)
    {
        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'articles allow embed'));

        if (defined('BX_PHOTOS_ADD'))
            $aCheck = checkAction(getLoggedId(), BX_PHOTOS_ADD);
        if (!defined('BX_PHOTOS_ADD') || ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin)) {
            unset($aInputs['thumb']);
        }

        $a = array ('images' => 'PHOTOS', 'videos' => 'VIDEOS', 'sounds' => 'SOUNDS', 'files' => 'FILES');
        foreach ($a as $k => $v) {
            if (defined("BX_{$v}_ADD"))
                $aCheck = checkAction(getLoggedId(), constant("BX_{$v}_ADD"));
            if ((!defined("BX_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']);
            }
        }
 
		$aCheck = checkAction($_COOKIE['memberID'],  BX_ARTICLES_ALLOW_EMBED);
		if ( $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) { 
			unset ($aCustomForm['inputs']['header_youtube']);
			unset ($aCustomForm['inputs']['youtube_choice']);
			unset ($aCustomForm['inputs']['youtube_attach']);
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

		$aVarsChoice = array ( 
			'bx_if:empty' => array(
				'condition' => empty($aFeeds),
				'content' => array ()
			),

			'bx_repeat:videos' => $aFeeds,
		);                               
		$aTemplates['choice'] =  $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube_choice', $aVarsChoice);
		
		// upload form
		$aVarsUpload = array ();            
		$aTemplates['upload'] = $this->_oMain->_oTemplate->parseHtmlByName('form_field_youtube', $aVarsUpload);
 
		return $aTemplates;
	} 



}
