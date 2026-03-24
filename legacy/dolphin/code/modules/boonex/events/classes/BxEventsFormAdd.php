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

class BxEventsFormAdd extends BxDolFormMedia {

    var $_oMain, $_oDb;

    function BxEventsFormAdd ($oMain, $iProfileId, $iEntryId = 0, $iThumb = 0) {

        $this->_oMain = $oMain;
        $this->_oDb = $oMain->_oDb;
        $this->_oTemplate = $oMain->_oTemplate;
		
		
			//14/06/2017 FREDDY integration business listing
		//if(getParam('modzzz_listing_boonex_events')=='on' )
			$aCompanies = $this->_oDb->getMergedCompanyList($this->_oMain->_iProfileId);   
		  
			//////////////////////////////////////////////////////////////////// 
			
			//// freddy ajout initialisation 
			$bListing = false;
			
			
 
 		if($iEntryId){
			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
			
			
				// Freddy Integration Business Listing and Schools 01/11/2016
			// //////////////////
			if($aDataEntry['listing_id']){ 
				$iListing = $aDataEntry['listing_id'];
				$bListing = true;
				if(getParam("modzzz_listing_boonex_events")=='on' && $aDataEntry['company_type']=='listing'){ 
					$oListing = BxDolModule::getInstance('BxListingModule'); 
					$aListing = $oListing->_oDb->getEntryById($aDataEntry['listing_id']);
					$sListingName = $aListing['title'];
 				} 
			
			}
			
			// Fin Freddy Integration Business Listing and Schools 01/11/2016
			//////////////////////////////////////////////////////////
			
	  
 			$iLocationId = ($_REQUEST['location_id']) ? $_REQUEST['location_id']  : $aDataEntry['location_id'];
  
   			$iBandId = ($_REQUEST['band_id']) ? $_REQUEST['band_id']  : $aDataEntry['band_id'];

			// FREDDY COMMENT
			//$iListingId = ($_REQUEST['listing_id']) ? $_REQUEST['listing_id']  : $aDataEntry['listing_id'];
			$iGroupId = ($_REQUEST['group_id']) ? $_REQUEST['group_id']  : $aDataEntry['group_id'];

 			$iClubId = ($_REQUEST['club_id']) ? $_REQUEST['club_id']  : $aDataEntry['club_id'];
 
 			$iCommunityId = ($_REQUEST['community_id']) ? $_REQUEST['community_id']  : $aDataEntry['community_id']; 
 
 			$iCharityId = ($_REQUEST['charity_id']) ? $_REQUEST['charity_id']  : $aDataEntry['charity_id']; 

 			$iSchoolId = ($_REQUEST['school_id']) ? $_REQUEST['school_id']  : $aDataEntry['school_id']; 

 			$sLogoName = $aDataEntry['icon'];//logo mod 
		}else {
			
			
				// freddy Integration Business Listing and Schools 01/11/2016
			if(getParam('modzzz_listing_boonex_events')=='on'){ 
				$iListing = bx_get('listing'); 
				if($iListing){
					$oListing = BxDolModule::getInstance('BxListingModule'); 
					$aListing = $oListing->_oDb->getEntryById($iListing);
					$sListingName = $aListing['title'];

					$bListing = true;
				}
 			}
			
			// fIN freddy Integration Business Listing and Schools 14/06/2017
			
			
			$iLocationId = ($_REQUEST['location_id']) ? $_REQUEST['location_id'] : $_REQUEST['location'];

			$iBandId = ($_REQUEST['band_id']) ? $_REQUEST['band_id'] : $_REQUEST['band'];

			$iClubId = ($_REQUEST['club_id']) ? $_REQUEST['club_id'] : $_REQUEST['club'];
			
			// FREDDY COMMENT
			//$iListingId = ($_REQUEST['listing_id']) ? $_REQUEST['listing_id'] : $_REQUEST['listing'];
			$iGroupId = ($_REQUEST['group_id']) ? $_REQUEST['group_id'] : $_REQUEST['group'];

			$iCommunityId = ($_REQUEST['community_id']) ? $_REQUEST['community_id'] : $_REQUEST['community'];
			
			$iCharityId = ($_REQUEST['charity_id']) ? $_REQUEST['charity_id'] : $_REQUEST['charity'];
			
			$iSchoolId = ($_REQUEST['school_id']) ? $_REQUEST['school_id'] : $_REQUEST['school'];
		}
  
		//[begin] location integration - modzzz 
		if($iLocationId){
			$oLocation = BxDolModule::getInstance('BxLocationModule'); 
			$aLocationEntry = $oLocation->_oDb->getEntryById($iLocationId);
			$sLocationName = $aLocationEntry[$oLocation->_oDb->_sFieldTitle];  
		}
		//[end] location integration - modzzz
        
		//[begin] groups integration - modzzz 
		if($iGroupId){
			$oGroups = BxDolModule::getInstance('BxGroupsModule'); 
			$aGroupsEntry = $oGroups->_oDb->getEntryById($iGroupId);
			$sGroupsName = $aGroupsEntry[$oGroups->_oDb->_sFieldTitle];  
		}
		//[end] groups integration - modzzz


		//[begin] community integration - modzzz
 		if($iCommunityId){
			$oCommunity = BxDolModule::getInstance('BxCommunityModule'); 
			$aCommunityEntry = $oCommunity->_oDb->getEntryById($iCommunityId);
			$sCommunityName = $aCommunityEntry[$oCommunity->_oDb->_sFieldTitle];  
		}
		//[end] community integration - modzzz

		//[begin] charity integration - modzzz
 		if($iCharityId){
			$oCharity = BxDolModule::getInstance('BxCharityModule'); 
			$aCharityEntry = $oCharity->_oDb->getEntryById($iCharityId);
			$sCharityName = $aCharityEntry[$oCharity->_oDb->_sFieldTitle];  
		}
		//[end] charity integration - modzzz
 
		//[begin] club integration - modzzz 
		if($iClubId){
			$oClub = BxDolModule::getInstance('BxClubModule'); 
			$aClubEntry = $oClub->_oDb->getEntryById($iClubId);
			$sClubName = $aClubEntry[$oClub->_oDb->_sFieldTitle];  
		}
		//[end] club integration - modzzz

		//[begin] band integration - modzzz 
		if($iBandId){
			$oBand = BxDolModule::getInstance('BxBandsModule'); 
			$aBandEntry = $oBand->_oDb->getEntryById($iBandId);
			$sBandName = $aBandEntry[$oBand->_oDb->_sFieldTitle];  
		}
		//[end] band integration - modzzz

		//[begin] listing integration - modzzz 
		/*  Freddy commentaire
		if($iListingId){
			$oListing = BxDolModule::getInstance('BxListingModule'); 
			$aListingEntry = $oListing->_oDb->getEntryById($iListingId);
			$sListingName = $aListingEntry[$oListing->_oDb->_sFieldTitle];  
		}
		*/
		//[end] listing integration - modzzz

  		//[begin] school integration - modzzz 
		if($iSchoolId){
			$oSchool = BxDolModule::getInstance('BxSchoolsModule'); 
			$aSchoolEntry = $oSchool->_oDb->getEntryById($iSchoolId);
			$sSchoolName = $aSchoolEntry[$oSchool->_oDb->_sFieldTitle];  
		}
		//[end] school integration - modzzz

   		$bPaidEvent = $this->_oMain->isAllowedPaidEvent (); 
		if($bPaidEvent){
			$iPackageId = ($iEntryId) ? (int)$this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']) : (int)$_POST['package_id']; 
			$sPackageName = $this->_oDb->getPackageName($iPackageId);
		}
 
 		$aPeriods = $this->_oDb->getRecurringPeriods();
		$aCount = $this->_oDb->getRecurringCount(1,100);
		$sDefaultTitle = stripslashes($_REQUEST['title']);

		if($iEntryId){
			$bPaidEvent = $aDataEntry['invoice_no'] ? $bPaidEvent : false;
		 
			$sSelState = ($_POST['State']) ? $_POST['State'] : $aDataEntry['State']; 
			$sSelCountry = ($_POST['Country']) ? $_POST['Country'] : $aDataEntry['Country'];  
 
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}else{
			$aProfile = getProfileInfo($this->_oMain->_iProfileId); 
			$sDefCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('bx_events_default_country'); 
			$sSelCountry = ($_POST['Country']) ? $_POST['Country'] : $sDefCountry;  
 
			$aStates = $this->_oDb->getStateArray($sSelCountry);  
		}
  
		$sCatUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home', 'ajax=cat&parent=');
 
		$sStateUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . 'home', 'ajax=state&country='); 
  
        $this->_aMedia = array ();

        if (BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            $this->_aMedia['images'] = array (
                'post' => 'ready_images',
                'upload_func' => 'uploadPhotos',
                'tag' => BX_EVENTS_PHOTOS_TAG,
                'cat' => BX_EVENTS_PHOTOS_CAT,
                'thumb' => 'PrimPhoto',
                'module' => 'photos',
                'title_upload_post' => 'images_titles',
                'title_upload' => _t('_bx_events_form_caption_file_title'),
                'service_method' => 'get_photo_array',
            );

        if (BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            $this->_aMedia['videos'] = array (
                'post' => 'ready_videos',
                'upload_func' => 'uploadVideos',
                'tag' => BX_EVENTS_VIDEOS_TAG,
                'cat' => BX_EVENTS_VIDEOS_CAT,
                'thumb' => false,
                'module' => 'videos',
                'title_upload_post' => 'videos_titles',
                'title_upload' => _t('_bx_events_form_caption_file_title'),
                'service_method' => 'get_video_array',
            );

        if (BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            $this->_aMedia['sounds'] = array (
                'post' => 'ready_sounds',
                'upload_func' => 'uploadSounds',
                'tag' => BX_EVENTS_SOUNDS_TAG,
                'cat' => BX_EVENTS_SOUNDS_CAT,
                'thumb' => false,
                'module' => 'sounds',
                'title_upload_post' => 'sounds_titles',
                'title_upload' => _t('_bx_events_form_caption_file_title'),
                'service_method' => 'get_music_array',
            );

        if (BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            $this->_aMedia['files'] = array (
                'post' => 'ready_files',
                'upload_func' => 'uploadFiles',
                'tag' => BX_EVENTS_FILES_TAG,
                'cat' => BX_EVENTS_FILES_CAT,
                'thumb' => false,
                'module' => 'files',
                'title_upload_post' => 'files_titles',
                'title_upload' => _t('_bx_events_form_caption_file_title'),
                'service_method' => 'get_file_array',
            );


        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();

        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);

        // generate templates for form custom elements
        $aCustomMediaTemplates = $this->generateCustomMediaTemplates ($this->_oMain->_iProfileId, $iEntryId, $iThumb);
 
        $aCustomYoutubeTemplates = $this->generateCustomYoutubeTemplate ($oMain->_iProfileId, $iEntryId);
 
        // privacy

        $aInputPrivacyCustom = array ();
        $aInputPrivacyCustom[] = array ('key' => '', 'value' => '----');
        $aInputPrivacyCustom[] = array ('key' => 'p', 'value' => _t('_bx_events_privacy_participants_only'));
        $aInputPrivacyCustomPass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9p]+)$/'),
        );

        $aInputPrivacyCustom2 = array (
			array('key' => '', 'value' => '----'),
            array('key' => 'p', 'value' => _t('_bx_events_privacy_participants')),
            array('key' => 'a', 'value' => _t('_bx_events_privacy_admins_only'))
        );
        $aInputPrivacyCustom2Pass = array (
            'pass' => 'Preg', 
            'params' => array('/^([0-9pa]+)$/'),
        );

		$aInputPrivacyView = $GLOBALS['oBxEventsModule']->_oPrivacy->getGroupChooser($iProfileId, 'events', 'view_event');  
        $aInputPrivacyView['values'] = array_merge($aInputPrivacyView['values'], $aInputPrivacyCustom2);
        $aInputPrivacyView['db'] = $aInputPrivacyCustom2Pass;
 

        $aInputPrivacyViewParticipants = $GLOBALS['oBxEventsModule']->_oPrivacy->getGroupChooser($iProfileId, 'events', 'view_participants');
        $aInputPrivacyViewParticipants['values'] = array_merge($aInputPrivacyViewParticipants['values'], $aInputPrivacyCustom);


        $aInputPrivacyComment = $GLOBALS['oBxEventsModule']->_oPrivacy->getGroupChooser($iProfileId, 'events', 'comment');
        $aInputPrivacyComment['values'] = array_merge($aInputPrivacyComment['values'], $aInputPrivacyCustom);
        $aInputPrivacyComment['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyRate = $GLOBALS['oBxEventsModule']->_oPrivacy->getGroupChooser($iProfileId, 'events', 'rate');
        $aInputPrivacyRate['values'] = array_merge($aInputPrivacyRate['values'], $aInputPrivacyCustom);
        $aInputPrivacyRate['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyViewForum = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'events', 'view_forum');
        $aInputPrivacyViewForum['values'] = array_merge($aInputPrivacyViewForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyViewForum['db'] = $aInputPrivacyCustomPass;
 
        $aInputPrivacyPostForum = $GLOBALS['oBxEventsModule']->_oPrivacy->getGroupChooser($iProfileId, 'events', 'post_in_forum');
        $aInputPrivacyPostForum['values'] = array_merge($aInputPrivacyPostForum['values'], $aInputPrivacyCustom);
        $aInputPrivacyPostForum['db'] = $aInputPrivacyCustomPass;

        $aInputPrivacyUploadPhotos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'events', 'upload_photos');
        $aInputPrivacyUploadPhotos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadPhotos['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadVideos = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'events', 'upload_videos');
        $aInputPrivacyUploadVideos['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadVideos['db'] = $aInputPrivacyCustom2Pass;        

        $aInputPrivacyUploadSounds = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'events', 'upload_sounds');
        $aInputPrivacyUploadSounds['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadSounds['db'] = $aInputPrivacyCustom2Pass;

        $aInputPrivacyUploadFiles = $this->_oMain->_oPrivacy->getGroupChooser($iProfileId, 'events', 'upload_files');
        $aInputPrivacyUploadFiles['values'] = $aInputPrivacyCustom2;
        $aInputPrivacyUploadFiles['db'] = $aInputPrivacyCustom2Pass;
 
		//[begin] - Group Events modzzz
		// Freddy comment
		/*
		$iGroupId = 0;
		$aGroup = array();
		if(getParam("modzzz_gevent_event_active")=='on'){ 
		 
			if($iEntryId)
				$iGroupId = ($_REQUEST['group_id']) ? $_REQUEST['group_id'] : $aDataEntry['group_id'];
			else
 				$iGroupId = ($_REQUEST['group_id']) ? $_REQUEST['group_id']  : bx_get('group');
  
			$oGEventModule = BxDolModule::getInstance('BxGEventModule');
			$aGroup = ($iGroupId) ? array() : $oGEventModule->_oDb->getGroupList($iProfileId);   
		}
		*/
		//[end] - Group Events modzzz


        $aCustomForm = array(

            'form_attrs' => array(
                'name'     => 'form_events',
                'action'   => '',
                'method'   => 'post',
                'enctype' => 'multipart/form-data',
            ),      

            'params' => array (
                'db' => array(
                    'table' => 'bx_events_main',
                    'key' => 'ID',
                    'uri' => 'EntryUri',
                    'uri_title' => 'Title',
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(

                'header_info' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_info')
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
                    'caption' => _t('_bx_events_package'), 
                ), 

				//[begin] band integration - modzzz 
				'band_id' => array(
					'type' => 'hidden',
					'name' => 'band_id',
					'value' => $iBandId,   
					'db' => array (
					'pass' => 'Xss', 
					) 
				), 
				'band_name' => array(
					'name' => 'band_name',
					'type' => 'custom', 
					'caption' => _t('_bx_events_caption_event_for_band'),
					'content' => $sBandName,    
				),
				//[end] band integration - modzzz

				//[begin] listing integration - modzzz 
				/* Freddy commentaire
				'listing_id' => array(
					'type' => 'hidden',
					'name' => 'listing_id',
					'value' => $iListingId,   
					'db' => array (
					'pass' => 'Xss', 
					) 
				), 
				'listing_name' => array(
					'name' => 'listing_name',
					'type' => 'custom', 
					'caption' => _t('_bx_events_caption_event_for_listing'),
					'content' => $sListingName,    
				),
				
				*/
				//[end] listing integration - modzzz

				//[begin] club integration - modzzz 
			   'club_id' => array(
					'type' => 'hidden',
					'name' => 'club_id',
					'value' => $iClubId,   
					'db' => array (
					'pass' => 'Xss', 
					) 
				), 
				'club_name' => array(
					'name' => 'club_name',
					'type' => 'custom', 
					'caption' => _t('_bx_events_caption_event_for_club'),
					'content' => $sClubName,    
				),
				//[end] club integration - modzzz
  
			   //[begin] groups integration - modzzz 
			   'group_id' => array(
					'type' => 'hidden',
					'name' => 'group_id',
					'value' => $iGroupId,   
					'db' => array (
						'pass' => 'Xss', 
					) 
				), 
				'group_name' => array(
					'name' => 'group_name',
					'type' => 'custom', 
					'caption' => _t('_bx_events_caption_event_for_group'),
					'content' => $sGroupsName,    
				),
				//[end] groups integration - modzzz

				//[begin] location integration - modzzz 
				   'location_id' => array(
					'type' => 'hidden',
					'name' => 'location_id',
					'value' => $iLocationId,   
					'db' => array (
					'pass' => 'Xss', 
					) 
				), 
				'location_name' => array(
					'name' => 'location_name',
					'type' => 'custom', 
					'caption' => _t('_bx_events_caption_event_for_location'),
					'content' => $sLocationName,    
				),
				//[end] location integration - modzzz

				//[begin] charity integration - modzzz 
				   'charity_id' => array(
					'type' => 'hidden',
					'name' => 'charity_id',
					'value' => $iCharityId,   
					'db' => array (
					'pass' => 'Xss', 
					) 
				), 
				'charity_name' => array(
					'name' => 'charity_name',
					'type' => 'custom', 
					'caption' => _t('_modzzz_charity_caption_event_for_charity'),
					'content' => $sCharityName,    
				),
				//[end] charity integration - modzzz

				//[begin] school integration - modzzz 
			   'school_id' => array(
					'type' => 'hidden',
					'name' => 'school_id',
					'value' => $iSchoolId,   
					'db' => array (
					'pass' => 'Xss', 
					) 
				), 
				'school_name' => array(
					'name' => 'school_name',
					'type' => 'custom', 
					'caption' => _t('_modzzz_schools_caption_event_for_school'),
					'content' => $sSchoolName,    
				),
				//[end] school integration - modzzz

				//[begin] community integration - modzzz 
				   'community_id' => array(
					'type' => 'hidden',
					'name' => 'community_id',
					'value' => $iCommunityId,   
					'db' => array (
					'pass' => 'Xss', 
					) 
				), 
				'community_name' => array(
					'name' => 'community_name',
					'type' => 'custom', 
					'caption' => _t('_modzzz_community_caption_event_for_community'),
					'content' => $sCommunityName,    
				),
				//[end] community integration - modzzz
				//[begin] - Group Events modzzz
				
				
				// Freddy comment
				/*
				'group' => array(
					'type' => 'hidden',
					'name' => 'group',
					'caption' => _t('_modzzz_gevent_form_caption_for_group'),
				),	
				'group_id' => array(
					'type' => 'select',
					'name' => 'group_id',
					'caption' => _t('_modzzz_gevent_form_caption_for_group'), 
					'required' => false, 
					'values' => $aGroup,  
					'db' => array (
					'pass' => 'Int', 
					) 
				),
				*/
				//[end] - Group Events modzzz
				
				  //// Freddy Integration Business Listing 14/06/2017
			   'display_business' => array(
                    'type' => 'custom',
                    'name' => 'display_business',
                    'caption' => _t('_bx_events_form_caption_listed_by'),
                    'content' => $sListingName,
                ), 
				'company_idd' => array(
                    'type' => 'hidden',
                    'name' => 'listing_id',
                    'value' => 'listing|'.$iListing, 
                ), 			
				
				
	           //// FIN Freddy Integration Business Listing 14/06/2017
					 
					 
					// fredd integration business listing 14/06/2017
					'listing_id' => array(
                    'type' => 'select',
                    'name' => 'listing_id',
                    'caption' => _t('_bx_events_caption_company'),
                    'info' => _t('_bx_events_form_info_company'),
						 
                    'required' => false, 
                    'values' => $aCompanies,   
                ),  
                

                
				
                'Title' => array(
                    'type' => 'text',
                    'name' => 'Title',
                    'caption' => _t('_bx_events_caption_title_form'),
					  'info' => _t('_bx_events_info_title_form'),
					'value' => $sDefaultTitle, 
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_bx_events_err_title_event'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ), 
                ),
				
				
				/*
				  'header_start_end_date' => array(
                   'type' => 'block_header',
                   'caption' => _t('_bx_events_form_header_start_end_date'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
			   */
				
				 'EventStart' => array(
                    'type' => 'datetime',
                    'name' => 'EventStart',
                    'caption' => _t('_bx_events_caption_event_start'),
					'info' => _t('_bx_events_info_start_form'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => _t ('_bx_events_err_event_start'),
                    ),
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),    
                    'infodisplay' => 'filterDate',
                ),                                
                'EventEnd' => array(
                    'type' => 'datetime',
                    'name' => 'EventEnd',
                    'caption' => _t('_bx_events_caption_event_end'),
					'info' => _t('_bx_events_info_end_form'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'DateTime',
                        'error' => _t ('_bx_events_err_event_end'),
                    ),
                    'db' => array (
                        'pass' => 'DateTime', 
                    ),                    
                    'infodisplay' => 'filterDate',
                ), 
				
				
					//LOCATION
               'header_location' => array(
                   'type' => 'block_header',
                   'caption' => _t('_bx_events_form_header_location'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
                'Place' => array(
                    'type' => 'text',
                    'name' => 'Place',
                    'caption' => _t('_bx_events_caption_place'),
					 'info' => _t('_bx_events_info_place_form'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_events_err_place'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),
                'Country' => array(
                    'type' => 'select',
				   //'type' => 'hidden',
                    'name' => 'Country',
                    'listname' => 'Country',
                    'caption' => _t('_bx_events_form_caption_country'),
                    'values' => $aCountries,
                    'required' => true,
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[a-zA-Z]{2}$/'),
                        'error' => _t ('_bx_events_err_country'),
                    ), 
					'value' => $sSelCountry,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					),	
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{2})/'),
                    ),   
                    'display' => 'getPreListDisplay',
                ),
				'State' => array(
					'type' => 'select',
					'name' => 'State',
					'value' => $sSelState,  
					'values'=> $aStates,
					'caption' => _t('_bx_events_caption_state'),
					 'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_events_err_state'),
                    ),
					'attrs' => array(
					// Freddy  ajout 'attrs' => array(						'style' => 'width:240px',),
					'style' => 'width:240px',
						'id' => 'substate',
					), 
					
					
					
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([a-zA-Z]+)/'),
					),
					'display' => true, 
				),
                'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_bx_events_form_caption_city'),
                    'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,50),
                        'error' => _t ('_bx_events_err_city'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),  
                    'display' => true,
                ), 
				
				
				'Zip' => array(
                    'type' => 'text',
                    'name' => 'Zip',
                    'caption' => _t('_bx_events_caption_zip'),
                    'required' => true, 
					 'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_events_err_zip'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),  
                    'display' => true,
                ),  
				              
				'Street' => array(
                    'type' => 'text',
                    'name' => 'Street',
                    'caption' => _t('_bx_events_caption_street'),
                    'required' => true, 
					 'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_events_err_street'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),  
                    'display' => true,
                ),  
				
				
				
					
					
			 'Categories' => $oCategories->getGroupChooser ('bx_events', (int)$iProfileId, true), 						

                        
                'Tags' => array(
                    'type' => 'text',
                    'name' => 'Tags',
                   // 'caption' => _t('_Tags'),
				   'caption' => _t('_bx_events_form_tags'),
				   
                    // 'info' => _t('_sys_tags_note'),
					'info' => _t('_bx_events_info_tags'),
                    'required' => false,
                    /*'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_events_err_tags'),
                    ),
					*/
                    'db' => array (
                        'pass' => 'Tags', 
                    ),
                ),                                      
				              
				                
                'Description' => array(
                    'type' => 'textarea',
                    'name' => 'Description',
                    'caption' => _t('_bx_events_caption_event_desc'),
                    'required' => true,
                    'html' => 2,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(20,64000),
                        'error' => _t ('_bx_events_err_desc'),
                    ),                    
                    'db' => array (
                        'pass' => 'XssHtml', 
                    ),                    
                ),
                'ParticipantsInfo' => array(
                    'type' => 'textarea',
                    'name' => 'ParticipantsInfo',
                    'caption' => _t('_bx_events_caption_participants_info'),
                    'info' => _t('_bx_events_info_participants_info'), 
                    'required' => false,
                    'html' => 0, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),                    
                ), 
				
				
			

				
			
               /*   
				//RSS
               'header_rss' => array(
                   'type' => 'block_header',
                   'caption' => _t('_bx_events_form_header_rss'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
               'rss_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomRssTemplates['choice'],
                   'name' => 'rss_choice[]',
                   'caption' => _t('_bx_events_form_caption_rss_choice'),
                   'info' => _t('_bx_events_form_info_rss_choice'),
                   'required' => false,
               ), 
               'rss_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomRssTemplates['upload'],
                   'name' => 'rss_upload[]',
                   'caption' => _t('_bx_events_form_caption_rss_attach'),
                   'info' => _t('_bx_events_form_info_rss_attach'),
                   'required' => false,
               ),
				*/
				// reminder 
				'header_reminder' => array(
					'type' => 'block_header',
					'caption' => _t('_bx_events_form_header_reminder'),
					'collapsable' => true,
					'collapsed' => false,
				),
				'Reminder' => array(
					'type' => 'select',
					'name' => 'Reminder',
					'caption' => _t('_bx_events_caption_reminder'),
					// Freddy  ajout 'attrs' => array(						'style' => 'width:240px',),
				'attrs' => array(
						
						'style' => 'width:240px',
					),
				////////	
					'values' => array('0'=>_t('_bx_events_no'),'1'=>_t('_bx_events_yes')),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
				 ), 
				'ReminderDays' => array(
					'type' => 'select',
					'name' => 'ReminderDays',
					'caption' => _t('_bx_events_caption_reminder_days'),
					// Freddy  ajout 'attrs' => array(						'style' => 'width:240px',),
				'attrs' => array(
						
						'style' => 'width:240px',
					),
				////////	
					'values' => $aCount,
					'required' => false, 
					'db' => array (
					'pass' => 'int', 
					),
				), 
				
				// organizer 
				'header_organizer' => array(
					'type' => 'block_header',
					'caption' => _t('_bx_events_form_header_organizer'),
					'collapsable' => true,
					'collapsed' => false,
				),
				'OrganizerName' => array(
					'type' => 'text',
					'name' => 'OrganizerName',
					'caption' => _t('_bx_events_caption_organizer_name'),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
                    'display' => true,
				 ), 
				'OrganizerPhone' => array(
					'type' => 'text',
					'name' => 'OrganizerPhone',
					'caption' => _t('_bx_events_caption_organizer_phone'),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
                    'display' => true,
				 ),
				'OrganizerEmail' => array(
					'type' => 'text',
					'name' => 'OrganizerEmail',
					'caption' => _t('_bx_events_caption_organizer_email'),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
                    'display' => true,
				 ),
				'OrganizerWebsite' => array(
					'type' => 'text',
					'name' => 'OrganizerWebsite',
					'caption' => _t('_bx_events_caption_organizer_website'),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
                    'display' => 'getWebsiteUrl',
				 ),

				// recurring 
				'header_recurring' => array(
					'type' => 'block_header',
					'caption' => _t('_bx_events_form_header_recurring'),
					'collapsable' => true,
					'collapsed' => false,
				),
				'Recurring' => array(
					'type' => 'select',
					'name' => 'Recurring',
					'caption' => _t('_bx_events_caption_recurring'),
					// Freddy  ajout 'attrs' => array(						'style' => 'width:240px',),
				'attrs' => array(
						
						'style' => 'width:240px',
					),
				////////	
					'values' => array('no'=>_t('_bx_events_recurring_no'),'yes'=>_t('_bx_events_recurring_yes')),
					'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
				 ), 
				'RecurringNum' => array(
					'type' => 'select',
					'name' => 'RecurringNum',
					'caption' => _t('_bx_events_caption_recurring_number'),
					// Freddy  ajout 'attrs' => array(						'style' => 'width:240px',),
				'attrs' => array(
						
						'style' => 'width:240px',
					),
				////////	
					'values' => $aCount,
							'required' => false, 
					'db' => array (
					'pass' => 'int', 
					),
				 ), 
				'RecurringPeriod' => array(
					'type' => 'select',
					'name' => 'RecurringPeriod',
					'caption' => _t('_bx_events_caption_recurring_period'),
					// Freddy  ajout 'attrs' => array(						'style' => 'width:240px',),
				'attrs' => array(
						
						'style' => 'width:240px',
					),
				////////	
					'values' => $aPeriods,
							'required' => false, 
					'db' => array (
					'pass' => 'Xss', 
					),
				 ),
				//[end] - ultimate events mod from modzzz 


                // logo 
              /*  'header_logo' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_logo'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				'presenticon' => array(
					'type' => 'custom',
					'name' => "presenticon", 
					'caption' => _t('_bx_events_present_icon'), 
					'content' =>  $this->_oDb->getLogo($iEntryId, $sLogoName) 
				),  
				'iconfile' => array(
					'type' => 'file',
					'name' => "iconfile",
					'caption' => _t('_bx_events_form_caption_icon'), 
				), 
				*/

                // images

                'header_images' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_images'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'PrimPhoto' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['thumb_choice'],
                    'name' => 'PrimPhoto',
                    'caption' => _t('_bx_events_form_caption_thumb_choice'),
                    'info' => _t('_bx_events_form_info_thumb_choice'),
                    'required' => false,
                    'db' => array (
                        'pass' => 'Int',
                    ),
                ),                
                'images_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['choice'],
                    'name' => 'images_choice[]',
                    'caption' => _t('_bx_events_form_caption_images_choice'),
                    'info' => _t('_bx_events_form_info_images_choice'),
                    'required' => false,
                ),
                'images_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['images']['upload'],
                    'name' => 'images_upload[]',
                    'caption' => _t('_bx_events_form_caption_images_upload'),
                    'info' => _t('_bx_events_form_info_images_upload'),
                    'required' => false,
                ),
 
                // youtube videos
             /*
			   'header_youtube' => array(
                   'type' => 'block_header',
                   'caption' => _t('_bx_events_form_header_youtube'),
                   'collapsable' => true,
                   'collapsed' => false,
               ), 
			   */
               'youtube_choice' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['choice'],
                   'name' => 'youtube_choice[]',
                   'caption' => _t('_bx_events_form_caption_youtube_choice'),
                   'info' => _t('_bx_events_form_info_youtube_choice'),
                   'required' => false,
               ), 
               'youtube_attach' => array(
                   'type' => 'custom',
                   'content' => $aCustomYoutubeTemplates['upload'],
                   'name' => 'youtube_upload[]',
                   'caption' => _t('_bx_events_form_caption_youtube_attach'),
                   'info' => _t('_bx_events_form_info_youtube_attach'),
                   'required' => false,
               ),
  
                // videos 
              /*
			    'header_videos' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_videos'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'videos_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['choice'],
                    'name' => 'videos_choice[]',
                    'caption' => _t('_bx_events_form_caption_videos_choice'),
                    'info' => _t('_bx_events_form_info_videos_choice'),
                    'required' => false,
                ),
                'videos_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['videos']['upload'],
                    'name' => 'videos_upload[]',
                    'caption' => _t('_bx_events_form_caption_videos_upload'),
                    'info' => _t('_bx_events_form_info_videos_upload'),
                    'required' => false,
                ),

                // sounds

                'header_sounds' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_sounds'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
                'sounds_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['choice'],
                    'name' => 'sounds_choice[]',
                    'caption' => _t('_bx_events_form_caption_sounds_choice'),
                    'info' => _t('_bx_events_form_info_sounds_choice'),
                    'required' => false,
                ),
                'sounds_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['sounds']['upload'],
                    'name' => 'sounds_upload[]',
                    'caption' => _t('_bx_events_form_caption_sounds_upload'),
                    'info' => _t('_bx_events_form_info_sounds_upload'),
                    'required' => false,
                ),
				*/

                // files

              /*  'header_files' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_files'),
                    'collapsable' => true,
                    'collapsed' => false,
                ),
				*/
                'files_choice' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['choice'],
                    'name' => 'files_choice[]',
                    'caption' => _t('_bx_events_form_caption_files_choice'),
                    'info' => _t('_bx_events_form_info_files_choice'),
                    'required' => false,
                ),
                'files_upload' => array(
                    'type' => 'custom',
                    'content' => $aCustomMediaTemplates['files']['upload'],
                    'name' => 'files_upload[]',
                    'caption' => _t('_bx_events_form_caption_files_upload'),
                   // 'info' => _t('_bx_events_form_info_files_upload'),
                    'required' => false,
                ),

                // privacy
                
                'header_privacy' => array(
                    'type' => 'block_header',
                    'caption' => _t('_bx_events_form_header_privacy'),
                ),

                //'allow_view_event_to' => $GLOBALS['oBxEventsModule']->_oPrivacy->getGroupChooser($iProfileId, 'events', 'view_event'),

				//[begin] - ultimate events mod from modzzz   
				'allow_view_event_to' => $aInputPrivacyView,
				//[end] - ultimate events mod from modzzz  
				
				 'allow_join_to' => $GLOBALS['oBxEventsModule']->_oPrivacy->getGroupChooser($iProfileId, 'events', 'join'),
 
                'allow_view_participants_to' => $aInputPrivacyViewParticipants,

              // 'allow_comment_to' => $aInputPrivacyComment,

                'allow_rate_to' => $aInputPrivacyRate, 

              // 'allow_post_in_forum_to' => $aInputPrivacyPostForum, 

              //  'allow_view_forum_to' => $aInputPrivacyViewForum, 

               

                'JoinConfirmation' => array (
                    'type' => 'select',
                    'name' => 'JoinConfirmation',
                    'caption' => _t('_bx_events_form_caption_join_confirmation'),
                    'info' => _t('_bx_events_form_info_join_confirmation'),
                    'values' => array(
                        0 => _t('_bx_events_form_join_confirmation_disabled'),
                        1 => _t('_bx_events_form_join_confirmation_enabled'),
                    ),
                    'checker' => array (
                        'func' => 'int',
                        'error' => _t ('_bx_events_form_err_join_confirmation'),
                    ),                                        
                    'db' => array (
                        'pass' => 'Int', 
                    ),                    
                ),

               // 'allow_upload_photos_to' => $aInputPrivacyUploadPhotos, 

               // 'allow_upload_videos_to' => $aInputPrivacyUploadVideos, 

              //  'allow_upload_sounds_to' => $aInputPrivacyUploadSounds, 

              // 'allow_upload_files_to' => $aInputPrivacyUploadFiles,   
					
				'allow_join_after_start' => array(
					'type' => 'select',
					'name' => 'allow_join_after_start',
					'caption' => _t('_bx_events_caption_allow_join_after_start'),
					'values' => array(
									'no'=>_t('_No'),
									'yes'=>_t('_Yes')
								),
					'required' => false, 
					'db' => array (
						'pass' => 'Xss', 
					),
				 ),

            ),            
        );

		if(getParam('bx_events_join_after_start')!='on'){
            unset ($aCustomForm['inputs']['allow_join_after_start']); 
		}

		//[begin] - Group Events modzzz
		// Frddy comment
		/*
		if(getParam("modzzz_gevent_event_active")=='on'){
			 
			$oGroupModule = BxDolModule::getInstance('BxGroupsModule');
			$oGEventModule = BxDolModule::getInstance('BxGEventModule');
			if($iGroupId){
				$aGroupEntry = $oGroupModule->_oDb->getEntryById($iGroupId);

				$aCustomForm['inputs']['group_id']['type'] = 'hidden';
				$aCustomForm['inputs']['group_id']['value'] = $iGroupId;
				$aCustomForm['inputs']['group_id']['values'] = null;
				$aCustomForm['inputs']['group']['type'] = 'custom';
				$aCustomForm['inputs']['group']['caption'] = _t('_modzzz_gevent_form_caption_for_group') .' - '. $aGroupEntry[$oGroupModule->_oDb->_sFieldTitle];

			}elseif(!$oGEventModule->_oDb->hasGroupListings($iProfileId)){  
				unset ($aCustomForm['inputs']['group_id']);
			} 
		}else{
			unset ($aCustomForm['inputs']['group_id']);
		}
		
		*/
		//[end] - Group Events modzzz


        if (!$aCustomForm['inputs']['images_choice']['content']) {
            unset ($aCustomForm['inputs']['PrimPhoto']);
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
            unset ($aCustomForm['inputs']['PrimPhoto']);
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


 		if (!$bPaidEvent){
            unset ($aCustomForm['inputs']['package_name']);
            unset ($aCustomForm['inputs']['package_id']); 
		}

		//[begin] band integration - modzzz
		if(!$iBandId){
			unset ($aCustomForm['inputs']['band_id']); 
			unset ($aCustomForm['inputs']['band_name']);
		}
		//[end] band integration - modzzz
  
		//[begin] listing integration - modzzz
		/* Freddy commentaire 
		if(!$iListingId){
			unset ($aCustomForm['inputs']['listing_id']); 
			unset ($aCustomForm['inputs']['listing_name']);
		}
		*/
		//[end] listing integration - modzzz

		//[begin] club integration - modzzz
		if(!$iClubId){
			unset ($aCustomForm['inputs']['club_id']);
			unset ($aCustomForm['inputs']['club_name']);
		}
		//[end] club integration - modzzz
 
		//[begin] location integration - modzzz
		if(!$iLocationId){
			unset ($aCustomForm['inputs']['location_id']);
			unset ($aCustomForm['inputs']['location_name']);
		}
		//[end] location integration - modzzz

		//[begin] community integration - modzzz
		if(!$iCommunityId){
			unset ($aCustomForm['inputs']['community_id']);
			unset ($aCustomForm['inputs']['community_name']);
		}
		//[end] community integration - modzzz

		//[begin] charity integration - modzzz
		if(!$iCharityId){
			unset ($aCustomForm['inputs']['charity_id']);
			unset ($aCustomForm['inputs']['charity_name']);
		}
		//[end] charity integration - modzzz

		//[begin] school integration - modzzz
		if(!$iSchoolId){
			unset ($aCustomForm['inputs']['school_id']);
			unset ($aCustomForm['inputs']['school_name']);
		}
		//[end] school integration - modzzz
		
		//[begin] groups integration - modzzz
		if(!$iGroupId){
			unset ($aCustomForm['inputs']['group_id']);
			unset ($aCustomForm['inputs']['group_name']);
		}
		//[end] groups integration - modzzz
		
		// Freddy    Business listing integration
		//if(count($aCompanies)==0){
			// MASQUEE R TOUS LES CHAMPS SI MEMBRE N'A PAS CREEE UNE PAGE ENTRE ENTREPRISE ET AFFICHER UN MESSAGE AVEC LIEN VERS              ADD BUSINESS LISTING
			if(count($aCompanies)==0){
            unset ($aCustomForm['inputs']['listing_id']);
			unset ($aCustomForm['inputs']['header_start_end_date']);
			unset ($aCustomForm['inputs']['header_info']);
			unset ($aCustomForm['inputs']['Title']);
			unset ($aCustomForm['inputs']['Description']);
			unset ($aCustomForm['inputs']['ParticipantsInfo']);
			unset ($aCustomForm['inputs']['EventStart']);
			unset ($aCustomForm['inputs']['EventEnd']);
			unset ($aCustomForm['inputs']['Tags']);
			unset ($aCustomForm['inputs']['Categories']);
			unset ($aCustomForm['inputs']['header_location']);
			unset ($aCustomForm['inputs']['Place']);
			unset ($aCustomForm['inputs']['Country']);
			unset ($aCustomForm['inputs']['State']);
			unset ($aCustomForm['inputs']['City']);
			unset ($aCustomForm['inputs']['Street']);
			unset ($aCustomForm['inputs']['header_reminder']);
			unset ($aCustomForm['inputs']['Reminder']);
			unset ($aCustomForm['inputs']['ReminderDays']);
			unset ($aCustomForm['inputs']['header_organizer']);
			unset ($aCustomForm['inputs']['OrganizerName']);
			unset ($aCustomForm['inputs']['OrganizerPhone']);
			
			unset ($aCustomForm['inputs']['Zip']);
			
			
			unset ($aCustomForm['inputs']['OrganizerEmail']);
			unset ($aCustomForm['inputs']['OrganizerWebsite']);
			unset ($aCustomForm['inputs']['header_recurring']);
			unset ($aCustomForm['inputs']['Recurring']);
			unset ($aCustomForm['inputs']['RecurringNum']);
			unset ($aCustomForm['inputs']['RecurringPeriod']);
			unset ($aCustomForm['inputs']['header_logo']);
			unset ($aCustomForm['inputs']['presenticon']);
			unset ($aCustomForm['inputs']['iconfile']);
			unset ($aCustomForm['inputs']['header_images']);
			
			
			unset ($aCustomForm['inputs']['PrimPhoto']);
			unset ($aCustomForm['inputs']['images_choice']);
			unset ($aCustomForm['inputs']['images_upload']);
			unset ($aCustomForm['inputs']['header_youtube']);
			unset ($aCustomForm['inputs']['youtube_choice']);
			unset ($aCustomForm['inputs']['youtube_attach']);
			unset ($aCustomForm['inputs']['header_files']);
			unset ($aCustomForm['inputs']['files_choice']);
			unset ($aCustomForm['inputs']['files_upload']);
			
			
			
			
			
			
			unset ($aCustomForm['inputs']['header_privacy']);
			unset ($aCustomForm['inputs']['allow_view_event_to']);
			unset ($aCustomForm['inputs']['allow_view_event_to']);
			unset ($aCustomForm['inputs']['allow_view_participants_to']);
			unset ($aCustomForm['inputs']['allow_comment_to']);
			unset ($aCustomForm['inputs']['allow_rate_to']);
			unset ($aCustomForm['inputs']['allow_post_in_forum_to']);
			unset ($aCustomForm['inputs']['allow_view_forum_to']);
			unset ($aCustomForm['inputs']['allow_join_to']);
			unset ($aCustomForm['inputs']['JoinConfirmation']);
			unset ($aCustomForm['inputs']['allow_upload_photos_to']);
			
			
			
			
			
			unset ($aCustomForm['inputs']['allow_upload_videos_to']);
			unset ($aCustomForm['inputs']['allow_upload_sounds_to']);
			unset ($aCustomForm['inputs']['allow_upload_files_to']);
			unset ($aCustomForm['inputs']['allow_join_after_start']);
	//echo MsgBox (_t('_bx_events_add_business_buton_register'));
			  // echo MsgBox(_t('_bx_events_add_BusinessListing_message'));
			  	/* echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
				  $aVarsPopup = array (
            'title' => _t('_modzzz_articles_add_BusinessListing_message'),
            'content' => _t('_modzzz_articles_add_business_buton_register'),
                ); 
				*/ 
		} 
		/////-------FIN FREDDY--------------/////
		
		

        $aFormInputsAdminPart = array ();
        if ($GLOBALS['oBxEventsModule']->isAdmin()) {

            require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
            $aMemberships = getMemberships ();
            unset ($aMemberships[MEMBERSHIP_ID_NON_MEMBER]); // unset Non-member
            $aMemberships = array('' => _t('_bx_events_membership_filter_none')) + $aMemberships;
            $aFormInputsAdminPart = array (
 
				//[begin] - ultimate events mod from modzzz  
				'EventMembershipViewFilter' => array(
					'type' => 'select',
					'name' => 'EventMembershipViewFilter',
					'caption' => _t('_bx_events_caption_membership_view_filter'), 
					'info' => _t('_bx_events_info_membership_view_filter'), 
					'values' => $aMemberships,
					'value' => '', 
					'checker' => array (
					'func' => 'preg',
					'params' => array('/^[0-9a-zA-Z]*$/'),
					'error' => _t ('_bx_events_err_membership_view_filter'),
					),                                        
					'db' => array (
					'pass' => 'Preg', 
					'params' => array('/([0-9a-zA-Z]*)/'),
					),
					
				),
  
                'EventMembershipFilter' => array(
                    'type' => 'select',
                    'name' => 'EventMembershipFilter',
                    'caption' => _t('_bx_events_caption_membership_filter'), 
                    'info' => _t('_bx_events_info_membership_filter'), 
                    'values' => $aMemberships,
                    'value' => '', 
                    'checker' => array (
                        'func' => 'preg',
                        'params' => array('/^[0-9a-zA-Z]*$/'),
                        'error' => _t ('_bx_events_err_membership_filter'),
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
		
		
		
		
		 //Freddy [begin] listing  integration - modzzz
	
	       if($iEntryId || $bListing){
            unset ($aCustomForm['inputs']['listing_id']); 
	       }
		   
		   if(!$bListing){
            unset ($aCustomForm['inputs']['display_business']); 
            unset ($aCustomForm['inputs']['company_idd']);  
		}

         //[end] freddy business integration - modzzz
		
		

        $aCustomForm['inputs'] = array_merge($aCustomForm['inputs'], $aFormInputsAdminPart, $aFormInputsSubmit);
		
		
		
		
		// Freddy   --- Masquer submit si membre n'a pas encore creer une page entreprise// Business listing
			
		
			if(count($aCompanies)==0){
				 unset ($aCustomForm['inputs']['Submit']); 
				 unset ($aCustomForm['inputs']['EventMembershipViewFilter']);
			     unset ($aCustomForm['inputs']['EventMembershipFilter']);
				// echo MsgBox (_t('_bx_events_add_business_buton_register'));
		
		  
			}
			
		/////Fin Freddy
		
		

/*
		$aCustomForm['inputs']['Categories']['attrs'] = array(
			'add_other' => false,
		);	
*/


        $this->processMembershipChecksForMediaUploads ($aCustomForm['inputs']);
 
		if($bPaidEvent){
			 $this->processPackageChecksForMediaUploads ($iPackageId, $aCustomForm['inputs']);
		}
 
 		//logo mod
		if(!$sLogoName){
			unset($aCustomForm['inputs']['presenticon']); 
		}

        parent::BxDolFormMedia ($aCustomForm);
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
            }        
        }  
    }
    
    function processMembershipChecksForMediaUploads (&$aInputs) {

        $isAdmin = $GLOBALS['logged']['admin'] && isProfileActive($this->_iProfileId);

        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'events photos add', 'events sounds add', 'events videos add', 'events files add', 'events allow embed'));

		if (defined("BX_PHOTOS_ADD")) {
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
			if (defined("BX_EVENTS_{$v}_ADD"))
				$aCheck = checkAction($_COOKIE['memberID'], constant("BX_EVENTS_{$v}_ADD"));
            if ((!defined("BX_EVENTS_{$v}_ADD") || $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED) && !$isAdmin) {
                unset($this->_aMedia[$k]);
                unset($aInputs['header_'.$k]);
                unset($aInputs[$k.'_choice']);
                unset($aInputs[$k.'_upload']);  
            }        
        } 
 
		$aCheck = checkAction($_COOKIE['memberID'],  BX_EVENTS_ALLOW_EMBED);
		if ( $aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED && !$isAdmin) { 
			unset($aInputs['header_youtube']);
			unset($aInputs['youtube_choice']); 
			unset($aInputs['youtube_attach']); 
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
 
 

}
