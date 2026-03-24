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

function bx_events_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'events') {
        $oMain = BxDolModule::getInstance('BxEventsModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a);
}

bx_import('BxDolTwigModule');
bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxTemplSearchResult');
bx_import('BxDolInstallerUtils');

define ('BX_EVENTS_PHOTOS_CAT', 'Events');
define ('BX_EVENTS_PHOTOS_TAG', 'events');

define ('BX_EVENTS_VIDEOS_CAT', 'Events');
define ('BX_EVENTS_VIDEOS_TAG', 'events');

define ('BX_EVENTS_SOUNDS_CAT', 'Events');
define ('BX_EVENTS_SOUNDS_TAG', 'events');

define ('BX_EVENTS_FILES_CAT', 'Events');
define ('BX_EVENTS_FILES_TAG', 'events');

define ('BX_EVENTS_MAX_FANS', 1000);
  
/*
 * Events module
 *
 * This module allow users to post upcoming events, 
 * users can rate, comment, discuss it.
 * Event can have photo, video, sound and files.
 *
 * 
 *
 * Profile's Wall:
 * 'add event' event are displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new event was created
 * change - events was chaned
 * join - somebody joined event
 * rate - somebody rated event
 * commentPost - somebody posted comment in event
 *
 *
 *
 * Memberships/ACL:
 * events view - BX_EVENTS_VIEW
 * events browse - BX_EVENTS_BROWSE
 * events search - BX_EVENTS_SEARCH
 * events add - BX_EVENTS_ADD
 * events comments delete and edit - BX_EVENTS_COMMENTS_DELETE_AND_EDIT
 * events edit any event - BX_EVENTS_EDIT_ANY_EVENT
 * events delete any event - BX_EVENTS_DELETE_ANY_EVENT
 * events mark as featured - BX_EVENTS_MARK_AS_FEATURED
 * events approve - BX_EVENTS_APPROVE
 * events broadcast message - BX_EVENTS_BROADCAST_MESSAGE
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different events
 * @see BxEventsModule::serviceHomepageBlock
 * BxDolService::call('events', 'homepage_block', array());
 *
 * Profile block with user's events
 * @see BxEventsModule::serviceProfileBlock
 * BxDolService::call('events', 'profile_block', array($iProfileId));
 *
 * Event's forum permissions (for internal usage only)
 * @see BxEventsModule::serviceGetForumPermission
 * BxDolService::call('events', 'get_forum_permission', array($iMemberId, $iForumId));
 *
 * Member menu item for events (for internal usage only)
 * @see BxEventsModule::serviceGetMemberMenuItem
 * BxDolService::call('events', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'bx_events'
 * The following alerts are rised
 *
 *  join - user joined an event
 *      $iObjectId - event id
 *      $iSenderId - joined user
 *
 *  add - new event was added
 *      $iObjectId - event id
 *      $iSenderId - creator of an event
 *      $aExtras['Status'] - status of added event
 *
 *  change - event's info was changed
 *      $iObjectId - event id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed event
 *
 *  delete - event was deleted
 *      $iObjectId - event id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - event was marked/unmarked as featured
 *      $iObjectId - event id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if event was marked as featured and 0 - if event was removed from featured 
 *
 */
class BxEventsModule extends BxDolTwigModule {

    var $_iProfileId;
    var $_oPrivacy;
 
    function BxEventsModule(&$aModule) {

        parent::BxDolTwigModule($aModule);
        $this->_sFilterName = 'bx_events_filter';
        $this->_sPrefix = 'bx_events';

        $GLOBALS['oBxEventsModule'] = &$this;
        bx_import ('Privacy', $aModule);
        bx_import ('SubPrivacy', $aModule);
        $this->_oPrivacy = new BxEventsPrivacy($this);
 
	    $this->_oConfig->init($this->_oDb);
  
		//reloads states on Add form
		if($_GET['ajax']=='state')
		{
			$sCountryCode = $_GET['country'];
			echo $this->_oDb->getStateOptions($sCountryCode);
			exit;
		}		

		if($_GET['ajax']=='package') { 
			$iPackageId = (int)$_GET['package'];
			echo $this->_oTemplate->getFormPackageDesc($iPackageId);
			exit;
		}
 
    }

    function actionHome () {
        parent::_actionHome(_t('_bx_events_main'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_bx_events_caption_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_bx_events_caption_photos'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_bx_events_caption_sounds'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_bx_events_caption_files'));
    }

    function actionComments ($sUri) { 
		$this->sUri = $sUri;
        parent::_actionComments ($sUri, _t('_bx_events_caption_comments'));
    }

    function actionBrowseParticipants ($sUri) {

		$this->sUri = $sUri;

        parent::_actionBrowseFans ($sUri, 'isAllowedViewParticipants', 'getFansBrowse', $this->_oDb->getParam('bx_events_perpage_browse_participants'), 'browse_participants/', _t('_bx_events_caption_participants'));
    }
/*
    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_bx_events_msg_pending_approval'));        
    }
*/

    function _preProductTabs ($sUri, $sSubTab = '')
    {
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending' && !$this->isAdmin() && !($aDataEntry[$this->_oDb->_sFieldAuthorId] == $this->_iProfileId && $aDataEntry[$this->_oDb->_sFieldAuthorId]))  {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => $sSubTab ? BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri] : '',
            $sSubTab => '',
        ));
 
        if ((!$this->_iProfileId || $aDataEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedView($aDataEntry, true)) {

			$isFan = $this->_oDb->isFan((int)$aDataEntry[$this->_oDb->_sFieldId], $this->_iProfileId, 1);

			if( !($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) && $aDataEntry[$this->_oDb->_sFieldAllowViewTo]=='p' && (!$isFan)){ 
				return $aDataEntry;
			}else{
				$this->_oTemplate->displayAccessDenied ();
				return false;
			}
        }
 
        return $aDataEntry;
    }
 
	function actionView ($sUri) {
		$this->sUri = $sUri;

        $this->_actionView ($sUri, _t('_bx_events_msg_pending_approval'));
    }
 
    function _actionView ($sUri, $sMsgPendingApproval) {

        if (!($aDataEntry = $this->_preProductTabs($sUri)))
            return;

        $this->_oTemplate->pageStart();

		$isFan = $this->_oDb->isFan((int)$aDataEntry[$this->_oDb->_sFieldId], $this->_iProfileId, 1);

		if( !($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) && $aDataEntry[$this->_oDb->_sFieldAllowViewTo]=='p' && (!$isFan)){ 
		 
			bx_import ('PageViewPrivate', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'PageViewPrivate';

		}else{ 
			bx_import ('PageView', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'PageView';
        } 
        $oPage = new $sClass ($this, $aDataEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		// add event customizer 
		if (BxDolInstallerUtils::isModuleInstalled("event_customize"))
		{
			$sCustomBlock = '<div id="event_customize_page" style="display: none;">' .
				BxDolService::call('event_customize', 'get_customize_block', array()) . '</div>';
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('event_customize', 'get_event_style', array($aDataEntry[$this->_oDb->_sFieldId])) . '</style>';

			echo "
			<div id=\"custom_block\">
				$sCustomBlock
			</div>
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";

		}else{
			echo $sPageCode; 
		}


        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aDataEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        $this->_oTemplate->addPageKeywords ($aDataEntry[$this->_oDb->_sFieldTags]);

	    //[begin] seo modification 
		$sTitle = $aDataEntry[$this->_oDb->_sFieldTitle];
		$sDate = date('l, F dS, Y', $aDataEntry[$this->_oDb->_sFieldStart]);
		$sLocation = $this->_formatLocation($aDataEntry);
		$sSiteTitle = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');

        $this->_oTemplate->setPageTitle ($sTitle . ' | '. $sDate . ' | '. $sLocation . ' | '. $sSiteTitle);
 
		$sDescription = $sTitle .' '. _t('_bx_events_happening') .' '. $sDate . ' @ '. $sLocation; 

 
		$aImages = array();
		$aPhotos = $this->_oDb->getAllPhotos($aDataEntry[$this->_oDb->_sFieldId]);
		foreach($aPhotos as $aEachPhoto){
 
			$a = array ('ID' => $aDataEntry['ResponsibleID'], 'Avatar' => $aEachPhoto['thumb']);
			$aImage = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');
			$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			
			$aImages[] = array(
				'image' => ($sImage) ? $sImage : $this->_oTemplate->getImageUrl('no-image-thumb.png')
			); 
		}

		if(!count($aPhotos)){
			$aImages[] = array(
				'image' => $this->_oTemplate->getImageUrl('no-image-thumb.png')
			); 
		}
  
		if ($oMap = BxDolModule::getInstance('BxWmapModule')){
			$sPart = $this->_oConfig->getUri();
			if (isset($oMap->_aParts[$sPart])){ 
				$sParamPrefix = 'bx_wmap_entry_' . $sPart;
				$iEntryId = (int)$aDataEntry[$this->_oDb->_sFieldId];
				$r = $oMap->_oDb->getDirectLocation($iEntryId, $oMap->_aParts[$sPart]);
				$fLatitude = $r['lat'];
				$fLongitude = $r['lng'];
			} 
		}
 
	    $aVars = array (
			'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			'title' => $sTitle,
			'description' => $sDescription,
			'bx_repeat:images' => $aImages,	
			'street' => $aDataEntry['Street'],
			'city' => $aDataEntry['City'],
			'state' => $aDataEntry['State'],
			'zip' => $aDataEntry['Zip'],
			'country' => $aDataEntry['Country'],
			'site_name' => $sSiteTitle,
			'latitude' => $fLatitude, 
			'longitude' => $fLongitude,   
		); 
        $sInjection = $this->_oTemplate->parseHtmlByName ('head_injection', $aVars);
 
		$this->_oTemplate->addInjection('injection_head', 'text',  $sInjection); 
	   //[end] seo modification

        $this->_oTemplate->addJsTranslation(array('_are you sure?'));

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');
        $this->_oTemplate->pageCode($aDataEntry[$this->_oDb->_sFieldTitle], false, false);

        bx_import ('BxDolViews');
        new BxDolViews($this->_sPrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }
 
    function actionUploadPhotos ($sUri) {  
		

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_bx_events_caption_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
		$this->sUri = $sUri;

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_bx_events_caption_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
		$this->sUri = $sUri;

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_bx_events_caption_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
		$this->sUri = $sUri;

        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_bx_events_caption_upload_files')); 
    }

    function actionBroadcastOLD ($iEntryId) {
        parent::_actionBroadcast ($iEntryId, _t('_bx_events_caption_broadcast'), _t('_bx_events_msg_broadcast_no_participants'), _t('_bx_events_msg_broadcast_message_sent'));
    }    

    function actionInviteOLD ($iEntryId) {
        parent::_actionInvite ($iEntryId, 'bx_events_invitation', $this->_oDb->getParam('bx_events_max_email_invitations'), _t('_bx_events_invitation_sent'), _t('_bx_events_no_users_msg'), _t('_bx_events_caption_invite'));
    }

    function _getInviteParams ($aDataEntry, $aInviter) {
		// freddy ajout
			$aPoster = getProfileInfo($aDataEntry['author_id']);
		$sPosterName = getNickName($aPoster['ID']); 
		$sPosterLink = getProfileLink($aPoster['ID']); 
		// Freddy avatar
		//$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
		$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
		
		///////////////////////////////////
        return array (
                'EventName' => $aDataEntry['Title'],
                'EventLocation' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['Country']]['LKey']) . (trim($aDataEntry['City']) ? ', '.$aDataEntry['City'] : '') . ', ' . $aDataEntry['Place'],
                'EventStart' => getLocaleDate($aDataEntry['EventStart']),
                'EventUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
                'AcceptUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'accept/' . $aDataEntry['ID'],
				'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
               //Freddy modif
				//'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_bx_events_user_unknown'),
				'InviterNickName' => $aPoster['FirstName'].' '.$aPoster['LastName'],
				'PosterAvatar' => get_member_thumbnail($aPoster['ID'], 'none', true),
				
                'InvitationText' => nl2br(process_pass_data(strip_tags($_POST['inviter_text']))),
             );        
    }

    function actionCalendarOLD ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_bx_events_calendar'));
    }

    function actionSearchOLD ($sKeyword = '', $sCountry = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCountry)
            $_GET['Country'] = explode(',', $sCountry);

        if (is_array($_GET['Country']) && 1 == count($_GET['Country']) && !$_GET['Country'][0]) {
            unset($_GET['Country']);
            unset($sCountry);
        }

        if ($sCountry || $sKeyword) {
            $_GET['submit_form'] = 1;
        }
        
        bx_events_import ('FormSearch');
        $oForm = new BxEventsFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {

            bx_events_import ('SearchResult');
            $o = new BxEventsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Country'));

            if ($o->isError) {
                $this->_oTemplate->displayPageNotFound ();
                return;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->_oTemplate->displayNoData ();
                return;
            }

            $this->isAllowedSearch(true); // perform search action 

            $this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
            return;

        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_bx_events_caption_search'));
    }

    function actionAdd () {
        parent::_actionAdd (_t('_bx_events_caption_add'));
    }
 
    function _addForm ($sRedirectUrl) {
  
		$bPaidEvent = $this->isAllowedPaidEvent (); 
		if( $bPaidEvent && (!isset($_POST['submit_form'])) ){
			return $this->showPackageSelectForm();
		}else{
			$this->showAddForm($sRedirectUrl);
		}
	}
 
    function showPackageSelectForm() {
   
		$aPackage = $this->_oDb->getPackageList();

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=package&package=' ; 

		$iPackageId = ($_POST['package_id']) ? $_POST['package_id'] : $this->_oDb->getInitPackage();
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
  
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
				'title' => array(
                    'type' => 'hidden',
                    'name' => 'title',
                    'value' => stripslashes($_REQUEST['title']) 
                ),  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_bx_events_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),	
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_events_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_bx_events_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_bx_events_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {
			$this->showAddForm(false);
		}else{ 
			echo $oForm->getCode(); 
		}
    } 
   
    function showAddForm($sRedirectUrl) {
 
        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();
		
		/////FREDDY SI LE MEMBRE N'A PAS ENCORE INSCRIT UNE ENTREPRISE  ALORS AFFICHER LE MESSAGE POUR 
		$aCompanies = $this->_oDb->getMergedCompanyList($this->_iProfileId); 
		
		 if(count($aCompanies)==0){
			
			
				echo MsgBox (_t('_bx_events_add_business_buton_register'));
				/*
				echo MsgBox (_t('_bx_events_add_business_buton_register'));
			$aCustomForm['inputs']['header_message_biz']['type']='custom';
		    $aCustomForm['inputs']['header_message_biz']['content'] =_t('_bx_events_add_business_buton_register');
			*/
		}
		////////////////////////////////////

        if ($oForm->isSubmittedAndValid ()) {
 
 			$bPaidEvent = $this->isPaidPackage($oForm->getCleanValue('package_id')); 

			if($bPaidEvent)
				$sStatus = 'pending';
			else
				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId
            ); 
			
			
			/////////////////////////////////////////// FREDDY INTEGRATION BUSINESSLISTING  14/06/2017
			$sCompanyId = $oForm->getCleanValue('listing_id');
			if($sCompanyId && $sCompanyId!='0' && $sCompanyId!='-99'){
				$aCompanyInfo = explode('|', $oForm->getCleanValue('listing_id')); 
				$aValsAdd['company_type'] = $aCompanyInfo[0]; 
				$aValsAdd['listing_id'] = $aCompanyInfo[1]; 
 			} 
			
			if($aValsAdd['company_type'] == 'listing'){
					
					$oListing = BxDolModule::getInstance('BxListingModule'); 
					$aCompany = $oListing->_oDb->getEntryById($aValsAdd['listing_id']);
	
			} 
			
			//////////////////////////////////////////////FREDDY 14/06/2017
		   /////////////// integration business listing//// insertion de du champ listing_id en provenance de BxEventFormAdd
			                       
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
   
				//[begin] check for correct date span
				if($aDataEntry['EventEnd'] <= $aDataEntry['EventStart']){
					$this->_oDb->deleteEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
					echo MsgBox(_t('_bx_events_err_msg_date_span'));
					echo $oForm->getCode ();
					return;
				}
				//[end] check for correct date span

				$this->processLogo($iEntryId); //logo mod

                $this->isAllowedAdd(true); // perform action                 

				//rss mod
				//$this->_oDb->addRss($iEntryId);
 
 				$this->_oDb->addYoutube($iEntryId);
 
				$oForm->processAddMedia($iEntryId, $this->_iProfileId);
  
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
				
				if($this->isAllowedPaidEvent()){
 
					$iPackageId = $oForm->getCleanValue('package_id');
					$aPackage = $this->_oDb->getPackageById($iPackageId);
					$fPrice = $aPackage['price'];
					$iDays = $aPackage['days'];
  					$iFeatured = $aPackage['featured'];

					$sInvoiceStatus = ($fPrice) ? 'pending' : 'paid';
					$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);

					$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
						 
					//$sRedirectUrl = $this->_oDb->generatePaymentUrl($iEntryId, $fPrice);

					if($fPrice){
						$this->initializeCheckout($iEntryId, $fPrice);  
						return;  
					}else{
						if($iDays)
							$this->_oDb->updateEntryExpiration($iEntryId, $iDays); 
 
						if($iFeatured)
							$this->_oDb->updateFeaturedStatus($iEntryId);
						
						if (!$sRedirectUrl)  
							$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
					}
				}else{
					$iNumActiveDays = (int)getParam("bx_events_free_expired");
					if($iNumActiveDays && (!$this->isAdmin()))
						$this->_oDb->updateEntryExpiration($iEntryId, $iNumActiveDays); 
 
					if (!$sRedirectUrl)  
						$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
				} 
				 
                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                MsgBox(_t('_Error Occured'));
            } 

        } else { 
            echo $oForm->getCode (); 
        }
    }

    function _actionEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        bx_import ('FormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormEdit';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId], $iEntryId, $aDataEntry);
        if (isset($aDataEntry[$this->_oDb->_sFieldJoinConfirmation]))
            $aDataEntry[$this->_oDb->_sFieldJoinConfirmation] = (int)$aDataEntry[$this->_oDb->_sFieldJoinConfirmation];
        
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
 			$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']); 
			$bPaidListing = $this->isPaidPackage($iPackageId);   
			if($bPaidListing || $aDataEntry['status']=='expired'){
				$aValsAdd = array ();
			}else{ 
				$sStatus = $this->_oDb->getParam($this->_sPrefix . '_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
				$aValsAdd = array ($this->_oDb->_sFieldStatus => $sStatus);
			}
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
 
				/*
 				if( is_array($_POST['prev_feed']) && count($_POST['prev_feed'])){

					$aFeeds2Keep = array();
				 
					foreach ($_POST['prev_feed'] as $iFeedId){
						$aFeeds2Keep[$iFeedId] = $iFeedId;
					}

					$aFeedIds = $this->_oDb->getRssIds($iEntryId); 
					$aDeletedFeed = array_diff ($aFeedIds, $aFeeds2Keep);

					if ($aDeletedFeed) {
						foreach ($aDeletedFeed as $iFeedId) {
							$this->_oDb->removeRss($iEntryId, $iFeedId);
						}
					}
				}

				$this->_oDb->addRss($iEntryId); 
				*/

				//[begin] youtube
				$aYoutubes2Keep = array(); 
				if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){ 
					foreach ($_POST['prev_video'] as $iYoutubeId){
						$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
					}
				}
					
				$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId); 
				$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

				if ($aDeletedYoutube) {
					foreach ($aDeletedYoutube as $iYoutubeId) {
						$this->_oDb->removeYoutube($iEntryId, $iYoutubeId);
					}
				} 
 
				$this->_oDb->addYoutube($iEntryId);
 				//[end] youtube 
    
				$this->processLogo($iEntryId); //logo mod

                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventChanged ($iEntryId, $sStatus);
 
				if($bPaidListing && $aDataEntry[$this->_oDb->_sFieldStatus]=='pending'){
  
					$aPackage = $this->_oDb->getPackageById($iPackageId);
					$fPrice = $aPackage['price'];
				  
					$this->initializeCheckout($iEntryId, $fPrice);  
					return;   
				}else{  
					header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
				}  
                exit;
  
            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }
  
    function actionPackages () { 
        $this->_oTemplate->pageStart();
        bx_import ('PagePackages', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PagePackages';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t('_bx_events_page_title_packages'), false, false);
    }

    function actionAdministrationPackages ($sParam1='') {
 		$sMessage = "";
  		$iPackage = (int)process_db_input($sParam1);
 
		// check actions
		if(is_array($_POST)){
		
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
 				$this->_oDb->SavePackage();
				$sMessage = _t("Successfully Saved Package");
 			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{   
 				$this->_oDb->UpdatePackage();
				$sMessage = _t("Successfully Updated Package");
  			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 				$this->_oDb->DeletePackage();
				$sMessage = _t("Successfully Removed Package");
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iPackage = 0;  
			} 
 
		}
 
		$aPackages = $this->_oDb->getPackages();
		$aPackage[] = array(
			'value' => '',
			'caption' => ''  
		);
		foreach ($aPackages as $oPackage)
		{
			$sKey = $oPackage['id'];
			$sValue = $oPackage['name'];
 
			$aPackage[] = array(
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $iPackage) ? 'selected="selected"' : ''
			);
		}
		
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => _t('_bx_events_packages'),
			'bx_repeat:items' => $aPackage,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages/'
		));


		$aPackage = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "packages` WHERE  `id` = '$iPackage'");
		  
		$sFormName = 'packages_form';
  
	    if($iPackage){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_bx_events_categ_btn_edit'),
				'action_delete' => _t('_bx_events_categ_btn_delete'), 
				'action_add' => _t('_bx_events_categ_btn_add')  
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_bx_events_categ_btn_save')
			), 'pathes', false);	 
	    }
 
		$aVars = array(
 			'id'=> $aPackage['id'],  
			'name' => htmlspecialchars($aPackage['name']), 
   			'price' => $aPackage['price'],
			'days' => $aPackage['days'],
			'featured' => $aPackage['featured'],
			'description' => $aPackage['description'], 
			'photo_no_select' => $aPackage['photos'] ? '' : "selected='selected'",
			'photo_yes_select' => $aPackage['photos'] ? "selected='selected'" : '',
			'video_no_select' => $aPackage['videos'] ? '' : "selected='selected'",
			'video_yes_select' => $aPackage['videos'] ? "selected='selected'" : '',
			'file_no_select' => $aPackage['files'] ? '' : "selected='selected'",
			'file_yes_select' => $aPackage['files'] ? "selected='selected'" : '',
			
			'sound_no_select' => $aPackage['sounds'] ? '' : "selected='selected'", 
			'sound_yes_select' => $aPackage['sounds'] ? "selected='selected'" : '',

			'featured_no_select' => $aPackage['featured'] ? '' : "selected='selected'", 
			'featured_yes_select' => $aPackage['featured'] ? "selected='selected'" : '',

 			'form_name' => $sFormName, 
			'controls' => $sControls,   
		);

		if($sMessage){
 			$sContent .= MsgBox($sMessage) ;
			$sContent .= "<form method=post>";
			$sContent .= BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
 				'action_add' => _t('_bx_events_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_packages',$aVars);
		}

		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));
 
	}

    function actionAdministrationOrders () {

        if ($_POST['action_activate'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                $this->_oDb->activateOrder($iId, $this->isAdmin()); 
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {
  
            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteOrder($iId, $this->isAdmin()); 
            }
        }
 
		$sContent = $this->_manageOrders ('order', '', true, 'bx_twig_admin_form', array(
			'action_activate' => '_bx_events_admin_activate',
			'action_delete' => '_bx_events_admin_delete',
		));
     
        return $sContent;
    }

    function actionAdministrationInvoices () {

        if ($_POST['action_delete'] && is_array($_POST['entry'])) {
  
            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteInvoice($iId, $this->isAdmin()); 
            }
        }
 
		$sContent = $this->_manageOrders ('invoice', '', true, 'bx_twig_admin_form', array(
 			'action_delete' => '_bx_events_admin_delete',
		));
     
        return $sContent;
    }

    function _manageOrders ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 14, $bActionsPanel = true) {

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = $sMode . '_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayOrdersResultBlock($sMode))) {
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination();
			if($bActionsPanel)
				$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
        }

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id', 'filter_checkbox_id', $this->_sFilterName) : '',
            'actions_panel' => $sActionsPanel,
        );      
		
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }


	function isPaidPackage($iPackageId){
 
		if(!$this->isAllowedPaidEvent())
			return false;

		return $this->_oDb->isPaidPackage($iPackageId);  
	}

	function isPaidListing($iEntryId){
		$iEntryId = (int)$iEntryId;
         
		if (getParam('bx_events_paid_active')!='on') 
            return false;	
 
		$aEntry = $this->_oDb->getEntryById($iEntryId);
		$sInvoiceNo = $aEntry['invoice_no'];
 
 		$aInvoice = $this->_oDb->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

		return $this->_oDb->isPaidPackage($iPackageId);  
	}
 
    function isAllowedPaidEvent ($bCheckAdmin=true) {
  
		 if($bCheckAdmin && $this->isAdmin())
			return false;

        // admin always have access  
        if (getParam('bx_events_paid_active')=='on') 
            return true;	
            
		return false;
	}
  
    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_bx_events_caption_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_bx_events_event_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_bx_events_msg_added_to_featured'), _t('_bx_events_msg_removed_from_featured'));       
    }

    function actionJoinOLD ($iEntryId, $iProfileId) {
        parent::_actionJoin ($iEntryId, $iProfileId, _t('_bx_events_event_joined_already'), _t('_bx_events_event_joined_already_pending'), _t('_bx_events_event_join_success'), _t('_bx_events_event_join_success_pending'), _t('_bx_events_event_leave_success'));
    }    


    function actionAccept ($iEntryId, $iProfileId=0, $sCode='') {
 
		$sMsgJoinSuccess = _t('_bx_events_event_join_success');
		$sMsgJoinSuccessPending = _t('_bx_events_event_join_success_pending');
		$sMsgJoinRequestPending = _t('_bx_events_event_joined_already_pending');

        $this->_oTemplate->pageStart();

        $iEntryId = (int)$iEntryId;
        $iProfileId = (int)$iProfileId;

        if (!$this->_iProfileId) {  
		
			global $_page;
			global $_page_cont;
  
  			$sRedirect = BX_DOL_URL_ROOT . 'member.php';  
			$sRelocate = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'accept/' . $iEntryId .'/'. $iProfileId .'/'. $sCode;  

			$_page['name_index'] = 150;
			$_page['css_name'] = '';

			$_ni = $_page['name_index'];
			$_page_cont[$_ni]['page_main_code'] = MsgBox( _t( '_Please Wait' ) );
			$_page_cont[$_ni]['url_relocate'] = htmlspecialchars( $sUrlRelocate );
  
		    Redirect($sRedirect, array('ID' =>'', 'Password' => '', 'relocate' => $sRelocate));   
			PageCode();
			
			return;
        }
 
        if ($iProfileId && ($iProfileId != $this->_iProfileId)) {
            echo MsgBox(_t('_Access denied')); 
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode(_t('_bx_events_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
			return; 
        }
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

		$isFan = $this->_oDb->isFan ($iEntryId, $this->_iProfileId, true); 
		if ($isFan) { 
			//fan already 
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];  
			header('Location: ' . $sRedirect); 
			return;
		} 

		$isFan = $this->_oDb->isFan ($iEntryId, $this->_iProfileId, false); 
		if ($isFan) { 
			//fan pending 
			echo MsgBox($sMsgJoinRequestPending); 
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode(_t('_bx_events_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
			return;
		} 
 
        if (!$this->_oDb->isValidInvite($iEntryId, $sCode)) { 
            echo MsgBox(_t('_Access denied')); 
			$this->_oTemplate->addCss ('main.css');
			$this->_oTemplate->pageCode(_t('_bx_events_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
			return; 
        }
 
		$isConfirmed = ($this->isEntryAdmin($aDataEntry) || !$aDataEntry[$this->_oDb->_sFieldJoinConfirmation] ? true : false);

		if ($this->_oDb->joinEntry($iEntryId, $this->_iProfileId, $isConfirmed)) {
			if ($isConfirmed) {
				
				//$this->_oDb->flagActivity('join', $iEntryId, $this->_iProfileId);
	 
				$this->_oDb->removeInvite($iEntryId, $iProfileId);

				$this->onEventJoin ($iEntryId, $this->_iProfileId, $aDataEntry);
				$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

				header('Location: ' . $sRedirect);
			} else {
				$this->onEventJoinRequest ($iEntryId, $this->_iProfileId, $aDataEntry);
				
				$this->_oDb->removeInvite($iEntryId, $iProfileId);
 
				echo MsgBox($sMsgJoinSuccessPending); 
				$this->_oTemplate->addCss ('main.css');
				$this->_oTemplate->pageCode(_t('_bx_events_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]);
				return;
			}
		}
	 
        echo MsgBox(_t('_Error Occured'));
 
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_bx_events_accept_invitation') . $aDataEntry[$this->_oDb->_sFieldTitle]); 
    }    


    function actionParticipants ($iEventId) {
        $iEventId = (int)$iEventId;
        if (!($aEvent = $this->_oDb->getEntryByIdAndOwner ($iEventId, 0, true))) {
            echo MsgBox(_t('_Empty'));
            return;
        }

        bx_events_import ('PageView');
        $oPage = new BxEventsPageView ($this, $aEvent);
        $a = $oPage->getBlockCode_Participants();
        echo $a[0];
        exit;
    }

    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_bx_events_caption_share_event'));
    }

    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_bx_events_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_EVENTS_MAX_FANS);
    }

    function actionTags() {
        parent::_actionTags (_t('_bx_events_tags'));
    }

    function actionCategories() {
        parent::_actionCategories (_t('_bx_events_categories'));
    }    

    function actionDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEntryByIdAndOwner((int)$iEntryId, 0, true))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedView ($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    // ================================== external actions

    /**
     * Homepage block with different events
     * @return html to display on homepage in a block
     */ 
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

        bx_import ('PageMain', $this->_aModule);
        $o = new BxEventsPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

        $sDefaultHomepageTab = $this->_oDb->getParam('bx_events_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['bx_events_filter']) {            
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case 'upcoming':
            case $sDefaultHomepageTab:            
                $sBrowseMode = $_GET['bx_events_filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('bx_events_perpage_homepage'), 
            array(
                _t('_bx_events_tab_upcoming') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_events_filter=upcoming', 'active' => 'upcoming' == $sBrowseMode, 'dynamic' => true),
                _t('_bx_events_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_events_filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),                
                _t('_bx_events_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_events_filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_bx_events_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_events_filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_bx_events_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?bx_events_filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),                
            )
        );
    }

    /**
     * Profile block with user's events
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxEventsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('bx_events_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false
        );
    }

    /**
     * Profile block with events user joined
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlockJoined ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxEventsPageMain ($this);        
  
		if(strpos($_SERVER['REQUEST_URI'], 'member.php') !== false) {
			$o->sUrlStart = BX_DOL_URL_ROOT . 'member.php?';
		} else {
			$o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
		}

        return $o->ajaxBrowse(
            'joined', 
            $this->_oDb->getParam('bx_events_perpage_profile'), 
            array(),
            process_db_input ($aProfile['NickName'], BX_TAGS_NO_ACTION, BX_SLASHES_NO_ACTION),
            true,
            false
        );
    }

    /**
     * Event's forum permissions
     * @param $iMemberId profile id
     * @param $iForumId forum id
     * @return array with permissions
     */ 
    function serviceGetForumPermission($iMemberId, $iForumId) {

        $iMemberId = (int)$iMemberId;
        $iForumId = (int)$iForumId;

        $aFalse = array ( // default permissions, for visitors for example
            'admin' => 0,
            'read' => 0,
            'post' => 0,
        );

        if (!($aForum = $this->_oDb->getForumById ($iForumId))) {            
            return $aFalse;
        }

        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($aForum['entry_id'], 0, true))) {
            return $aFalse;
        }

        $aTrue = array (
            'admin' => (($aDataEntry[$this->_oDb->_sFieldAuthorId] == $iMemberId) || $this->isEntryAdmin($aDataEntry) || $this->isAdmin()) ? 1 : 0, // author is admin
            'read' => $this->isAllowedReadForum ($aDataEntry, $iMemberId) ? 1 : 0,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );
        return $aTrue;
    }

    /**
     * Member menu item for events
     * @return html to show in member menu
     */ 
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_bx_events'), _t('_bx_events'), 'calendar');
    }
  
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_bx_events_spy_post',
            'change' => '_bx_events_spy_post_change',
            'join' => '_bx_events_spy_join',
            'rate' => '_bx_events_spy_rate',
            'commentPost' => '_bx_events_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_bx_events_sbs_change'),
            'commentPost' => _t('_bx_events_sbs_comment'),
            'rate' => _t('_bx_events_sbs_rate'),
            'join' => _t('_bx_events_sbs_join'),
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions
 
  
    function actionAdministration ($sUrl = '',$sParam1='',$sParam2='') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
  
/*
        $aVars = array (
            'module_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
        );
        $sContent = $this->_oTemplate->parseHtmlByName ('admin_links', $aVars);
        echo $this->_oTemplate->adminBlock ($sContent, _t('_bx_events_admin_links'));
*/
        $aMenu = array(
            'home' => array(
                'title' => _t('_bx_events_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/home', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_bx_events_administration_admin_events'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ), 
			'invoices' => array(
                'title' => _t('_bx_events_menu_manage_invoices'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/invoices',
                '_func' => array ('name' => 'actionAdministrationInvoices', 'params' => array($sParam1)),
            ), 			
			'orders' => array(
                'title' => _t('_bx_events_menu_manage_orders'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/orders',
                '_func' => array ('name' => 'actionAdministrationOrders', 'params' => array($sParam1)),
            ),
			'packages' => array(
                'title' => _t('_bx_events_menu_manage_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages',
                '_func' => array ('name' => 'actionAdministrationPackages', 'params' => array($sParam1)),
            ),   
            'create' => array(
                'title' => _t('_bx_events_administration_create_event'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_bx_events_administration_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_bx_events_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'main.css', 'forms_extra.css', 'forms_adv.css', 'twig.css'));
      
        $this->_oTemplate->pageCodeAdmin (_t('_bx_events_administration'));
    }


    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Events');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_bx_events_admin_delete', '_bx_events_admin_activate');
    }

    // ================================== events


    function onEventUnJoin ($iEntryId, $iProfileId) {
 
		$oAlert = new BxDolAlerts($this->_sPrefix, 'unjoin', $iEntryId, $iProfileId);
		$oAlert->alert();
    }  
	
	function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'bx_events_join_request', BX_EVENTS_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'bx_events_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'bx_events_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'bx_events_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'bx_events_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'bx_events_join_confirm');
    }

    // ================================== permissions
    
    function isAllowedView ($aEvent, $isPerformAction = false) {
 
        // admins always have access
        if ($this->isAdmin() || $this->isEntryAdmin($aEvent)) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_VIEW, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        // check user group   
		$isAllowed =  $this->_oPrivacy->check('view_event', $aEvent[$this->_oDb->_sFieldId], $this->_iProfileId);  
		return $isAllowed && $this->_isAllowedViewByMembership ($aEvent); 
    }
  
    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_ADD, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aEvent, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isEntryAdmin($aEvent)) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_EDIT_ANY_EVENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aEvent, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        /*
		if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
		*/
        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function isAllowedDelete (&$aEvent, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isEntryAdmin($aEvent)) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_DELETE_ANY_EVENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     

    function isAllowedJoin (&$aDataEntry) {        
        if (!$this->_iProfileId) 
            return false;
 
		if(getParam('bx_events_join_after_start')!='on'){ 
			if ($aDataEntry['allow_join_after_start']!='yes'){ 
				if ($aDataEntry['EventStart'] < time())
					return false; 
			}else{
				if ($aDataEntry['EventEnd'] < time())
					return false;  
			}
		}else{
			if ($aDataEntry['EventEnd'] < time())
				return false;  
		}

        $isAllowed = $this->_oPrivacy->check('join', $aDataEntry['ID'], $this->_iProfileId);     
        return $isAllowed && $this->_isAllowedJoinByMembership ($aDataEntry);
    }
 
    function _isAllowedJoinByMembership (&$aEvent) {        
        if (!$aEvent['EventMembershipFilter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMemebrshipInfo = getMemberMembershipInfo($this->_iProfileId);

		if($aMemebrshipInfo['DateExpires'])
			return $aEvent['EventMembershipFilter'] == $aMemebrshipInfo['ID'] && $aMemebrshipInfo['DateStarts'] < time() && $aMemebrshipInfo['DateExpires'] > time() ? true : false;
		else 
			return $aEvent['EventMembershipFilter'] == $aMemebrshipInfo['ID'] && $aMemebrshipInfo['DateStarts'] < time() ? true : false;
    }
  
    function _isAllowedJoinByMembershipOLD (&$aEvent)
    {
        if (!$aEvent['EventMembershipFilter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMemebrshipInfo = getMemberMembershipInfo($this->_iProfileId);
        return $aEvent['EventMembershipFilter'] == $aMemebrshipInfo['ID'] && $aMemebrshipInfo['DateStarts'] < time() && $aMemebrshipInfo['DateExpires'] > time() ? true : false;
    }

    function isAllowedSendInvitation (&$aEvent) {
        return ($this->isAdmin() || $this->isEntryAdmin($aEvent));
    }

    function isAllowedShare (&$aEvent)
    {
    	return ($aEvent[$this->_oDb->_sFieldAllowViewTo] == BX_DOL_PG_ALL);
    }

    function isAllowedViewParticipants (&$aEvent) {

        if ($this->isAdmin() || $this->isEntryAdmin($aEvent))
            return true;

        return $this->_oPrivacy->check('view_participants', $aEvent['ID'], $this->_iProfileId);     
    }

    function isAllowedComments (&$aEvent) {

        if ($this->isAdmin() || $this->isEntryAdmin($aEvent))
            return true;

        return $this->_oPrivacy->check('comment', $aEvent['ID'], $this->_iProfileId);
    }

    function isAllowedUploadPhotos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;
 
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedPhotos($aDataEntry['invoice_no']))
            return false;     
        if ($this->isEntryAdmin($aDataEntry)) 
            return true;

        return $this->_oPrivacy->check('upload_photos', $aDataEntry['ID'], $this->_iProfileId);
    }

    function isAllowedUploadVideos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false; 
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedVideos($aDataEntry['invoice_no']))
            return false;  
        if ($this->isEntryAdmin($aDataEntry)) 
            return true;		

        return $this->_oPrivacy->check('upload_videos', $aDataEntry['ID'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;       
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedSounds($aDataEntry['invoice_no']))
            return false;    
        if ($this->isEntryAdmin($aDataEntry)) 
            return true;


        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['ID'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false; 
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedFiles($aDataEntry['invoice_no']))
            return false;    
        if ($this->isEntryAdmin($aDataEntry)) 
            return true;
 
        return $this->_oPrivacy->check('upload_files', $aDataEntry['ID'], $this->_iProfileId);
    }

    function isAllowedCreatorCommentsDeleteAndEdit (&$aEvent, $isPerformAction = false) {
        
        if ( $this->isAdmin() || $this->isEntryAdmin($aEvent) )
			return true;  
 
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
 
	function isAllowedRate (&$aEvent) {

		if ( $this->isAdmin() || $this->isEntryAdmin($aEvent) ) {
			return true;
		} else {
			return $this->_oPrivacy->check('rate', $aEvent['ID'], $this->_iProfileId);
		}
	}
 
    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['ID'], $iProfileId);
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }

    function isAllowedManageFans($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['ID'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isEntryAdmin($aDataEntry, $iProfileId = 0, $sAuthorField='ResponsibleID', $sIdField='ID') {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry[$sAuthorField] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry[$sIdField], $iProfileId) && isProfileActive($iProfileId);
    }

    function _defineActions () {
        defineMembershipActions(array('events purchase', 'events relist', 'events extend', 'events purchase featured', 'events view', 'events rss add', 'events browse', 'events search', 'events add', 'events comments delete and edit', 'events edit any event', 'events delete any event', 'events mark as featured', 'events approve', 'events broadcast message', 'events allow embed'));
    }

    // ================================== other function 

    function _browseMy (&$aProfile) {
        parent::_browseMy ($aProfile, _t('_bx_events_block_my_events'));
    }
	 
    //[begin] - ultimate events mod from modzzz  
    function _isAllowedViewByMembership (&$aDataEntry) { 
        if (!$aDataEntry['EventMembershipViewFilter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMembershipInfo = getMemberMembershipInfo($this->_iProfileId);
 
		if($aMembershipInfo['DateExpires']) 
			return $aDataEntry['EventMembershipViewFilter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() && $aMembershipInfo['DateExpires'] > time() ? true : false;
		else
			return $aDataEntry['EventMembershipViewFilter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() ? true : false; 
    }
   
    function actionBroadcast ($iEntryId) {
 
		$this->_actionBroadcast ($iEntryId, _t('_bx_events_caption_broadcast'), _t('_bx_events_msg_broadcast_no_participants'), _t('_bx_events_msg_broadcast_message_sent'));
    }    

    function actionInvite ($iEntryId) { 
		$this->_actionInvite ($iEntryId, 'bx_events_invitation', $this->_oDb->getParam('bx_events_max_email_invitations'), _t('_bx_events_invitation_sent'), _t('_bx_events_no_users_msg'), _t('_bx_events_caption_invite'));
    }
 
    function actionCalendar ($iYear = '', $iMonth = '') {
 
        parent::_actionCalendar ($iYear, $iMonth, _t('_bx_events_calendar'));
    }

    function actionSearch ($sKeyword = '', $sCategory = '', $sCountry = '', $sState = '', $sCity = '', $sEventStart = '', $sEventEnd = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['Category'] = explode(',', $sCategory);
        if ($sCountry)
            $_GET['Country'] = $sCountry;
		if ($sState) 
            $_GET['State'] = $sState;
		if ($sCity) 
            $_GET['City'] = $sCity;
		if ($sEventStart) 
            $_GET['EventStart'] = $sEventStart;
		if ($sEventEnd) 
            $_GET['EventEnd'] = $sEventEnd;
  
        if (is_array($_GET['Category']) && 1 == count($_GET['Category']) && !$_GET['Category'][0]) {
            unset($_GET['Category']);
            unset($sCategory);
        }
 
        if ($sCountry || $sCategory || $sKeyword || $sState || $sCity || $sEventStart || $sEventEnd) {
            $_GET['submit_form'] = 1;  
        }
        
        bx_events_import ('FormSearch');
        $oForm = new BxEventsFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			if($_GET['EventStart']) 
				$iStartDate = $oForm->getCleanValue('EventStart');
			if($_GET['EventEnd']) 
				$iEndDate = $oForm->getCleanValue('EventEnd');

            bx_events_import ('SearchResult');
            $o = new BxEventsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Categories'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('State'), $oForm->getCleanValue('City'), $iStartDate, $iEndDate);

            if ($o->isError) {
                $this->_oTemplate->displayPageNotFound ();
                return;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->_oTemplate->displayNoData ();
                return;
            }

            $this->isAllowedSearch(true); // perform search action 

            $this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

            $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
            return;

        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_bx_events_caption_search'));
    } 
 
    /**
     * Account block with different events
     * @return html to display on homepage in a block
     */ 
    function serviceAccountPageJoined () {
 
        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sNickname = $aProfileInfo['NickName'];
 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxEventsPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'joined',
            $this->_oDb->getParam('bx_events_perpage_accountpage'),
			array(),
			$sNickname
        );
    }
  
    /**
     * Account block with different events
     * @return html to display on homepage in a block
     */ 
    function serviceAccountPageBlock () {
 
        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sCity = $aProfileInfo['City'];

		if(!$sCity)
			return;

        bx_import ('PageMain', $this->_aModule);
        $o = new BxEventsPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('bx_events_perpage_accountpage'),
			array(),
			$sCity
        );
    }
 
    function onEventDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        $this->reparseTags ($iEntryId);
        $this->reparseCategories ($iEntryId);

        // delete votings
        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass ($this->_sPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        bx_import ('BxDolViews');
        $oViews = new BxDolViews($this->_sPrefix, $iEntryId, false);
        $oViews->onObjectDelete();

        // delete forum
        $this->_oDb->deleteForum ($iEntryId);

        // delete associated locations
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri(), $iEntryId));

		//$this->_oDb->flagActivity('delete', $iEntryId, $this->_iProfileId);


		//[begin] delete sponsors
		$aSponsors = $this->_oDb->getAllSubItems('sponsor', $iEntryId);
		foreach($aSponsors as $aEachSponsor){
			
			$iId = (int)$aEachSponsor['id'];
 
			// delete votings
			bx_import('SponsorVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SponsorVoting';
			$oVoting = new $sClass ($this->_oDb->_sSponsorPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('SponsorCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SponsorCmts';
			$oCmts = new $sClass ($this->_oDb->_sSponsorPrefix, $iId);
			$oCmts->onObjectDelete ();
		} 

		$this->_oDb->deleteSponsors($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		//[end] delete sponsors


		//[begin] delete venues
		$aVenues = $this->_oDb->getAllSubItems('venue', $iEntryId);
		foreach($aVenues as $aEachVenue){
			
			$iId = (int)$aEachVenue['id'];
 
			// delete votings
			bx_import('VenueVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'VenueVoting';
			$oVoting = new $sClass ($this->_oDb->_sVenuePrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('VenueCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'VenueCmts';
			$oCmts = new $sClass ($this->_oDb->_sVenuePrefix, $iId);
			$oCmts->onObjectDelete ();
		} 

		$this->_oDb->deleteVenues($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		//[end] delete venues
  
		//[begin] delete news
		if(getParam('bx_events_modzzz_news')=='on'){ 
			$oNews = BxDolModule::getInstance('BxNewsModule'); 
			$aNews = $this->_oDb->getModzzzNews($iEntryId);
			foreach($aNews as $aEachNews){ 
				if ($oNews->_oDb->deleteEntryByIdAndOwner($aEachNews['id'], 0, 0)) {
					$oNews->isAllowedDelete($aEachNews, true); // perform action
					$oNews->onNewsDeleted ($aEachNews['id'], $aEachNews);
				} 
			} 
		}else{
			$aNews = $this->_oDb->getAllSubItems('news', $iEntryId);
			foreach($aNews as $aEachNews){
				
				$iId = (int)$aEachNews['id'];

				// delete votings
				bx_import('NewsVoting', $this->_aModule);
				$sClass = $this->_aModule['class_prefix'] . 'NewsVoting';
				$oVoting = new $sClass ($this->_oDb->_sNewsPrefix, 0, 0);
				$oVoting->deleteVotings ($iId);

				// delete comments 
				bx_import('NewsCmts', $this->_aModule);
				$sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
				$oCmts = new $sClass ($this->_oDb->_sNewsPrefix, $iId);
				$oCmts->onObjectDelete ();
			}  

			$this->_oDb->deleteNews($iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		}
		//[end] delete news
 
        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		$oAlert->alert();
    }        
 
    function actionJoin ($iEntryId, $iProfileId) {
        $this->_actionJoin ($iEntryId, $iProfileId, _t('_bx_events_event_joined_already'), _t('_bx_events_event_joined_already_pending'), _t('_bx_events_event_join_success'), _t('_bx_events_event_join_success_pending'), _t('_bx_events_event_leave_success'));
    } 
	
    function _actionJoin ($iEntryId, $iProfileId, $sMsgAlreadyJoined, $sMsgAlreadyJoinedPending, $sMsgJoinSuccess, $sMsgJoinSuccessPending, $sMsgLeaveSuccess) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, 0, true))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedJoin($aDataEntry)) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

		$isFan = $this->_oDb->isFan ($iEntryId, $this->_iProfileId, true) || $this->_oDb->isFan ($iEntryId, $this->_iProfileId, false);

		if ($isFan) {

			if ($this->_oDb->leaveEntry($iEntryId, $this->_iProfileId)) {
				
				$this->onEventUnJoin ($iEntryId, $this->_iProfileId);

				//$this->_oDb->flagActivity('unjoin', $iEntryId, $this->_iProfileId);
		  
				$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
				echo MsgBox($sMsgLeaveSuccess) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
				exit;
			}

		} else {
		
			$isConfirmed = ($this->isEntryAdmin($aDataEntry) || !$aDataEntry[$this->_oDb->_sFieldJoinConfirmation] ? true : false);

			if ($this->_oDb->joinEntry($iEntryId, $this->_iProfileId, $isConfirmed)) {
				if ($isConfirmed) {
					
					//$this->_oDb->flagActivity('join', $iEntryId, $this->_iProfileId);
		 
					$this->onEventJoin ($iEntryId, $this->_iProfileId, $aDataEntry);
					$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
				} else {
					$this->onEventJoinRequest ($iEntryId, $this->_iProfileId, $aDataEntry);
					$sRedirect = '';
				}            
				echo MsgBox($isConfirmed ? $sMsgJoinSuccess : $sMsgJoinSuccessPending) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
				exit;
			}
		}

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;
    }    
 
    function actionLocal ($sCountry='', $sState='') { 
        $this->_oTemplate->pageStart();

		if($sCountry && !$this->_oDb->isValidCountry ($sCountry)){
			$this->_oTemplate->displayPageNotFound ();
			return; 
		}

		if($sState && !$this->_oDb->isValidState ($sState)){
			$this->_oTemplate->displayPageNotFound ();
			return; 
		}

        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_bx_events_page_title_local');

		if($sCountry){
			$sTitle .= ' - ' . $this->_oTemplate->getPreListDisplay('Country', $sCountry);
		}
 
		if($sState){
			$sTitle .= ' - ' . $this->_oDb->getStateName($sCountry, $sState); 
		}
  
        $this->_oTemplate->pageCode($sTitle, false, false);  
     } 

     function _actionBroadcast ($iEntryId, $sTitle, $sMsgNoRecipients, $sMsgSent) {
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedBroadcast($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
          		
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));
  
        $aRecipients = $this->_oDb->getBroadcastRecipients ($iEntryId);
        if (!$aRecipients) {
            echo MsgBox ($sMsgNoRecipients);
            $this->_oTemplate->pageCode($sMsgNoRecipients, true, true);
            return;
        }

        bx_import ('FormBroadcast', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormBroadcast';
        $oForm = new $sClass ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
            
            $oEmailTemplate = new BxDolEmailTemplates();
            if (!$oEmailTemplate) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }
			
			// freddy ajout
			$aPoster = getProfileInfo($aDataEntry['author_id']);
		$sPosterName = getNickName($aPoster['ID']); 
		$sPosterLink = getProfileLink($aPoster['ID']); 
		// Freddy avatar
		//$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
		$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
		
		///////////////////////////////////
		
		
		
            //$aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_broadcast'); 
            $aTemplateVars = array (
                'BroadcastTitle' => $this->_oDb->unescape($oForm->getCleanValue ('title')),
                'BroadcastMessage' => nl2br($this->_oDb->unescape($oForm->getCleanValue ('message'))),
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
				
				///////////freddy ajout
				'PosterAvatar' =>  $sPosterAvatar ,
				'PosterName' =>  $sPosterName ,
				              
            );
  
            $iSentMailsCounter = 0;            
            foreach ($aRecipients as $aProfile) {		        
       	        //$iSentMailsCounter += sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars);

				$aTemplate = $oEmailTemplate->parseTemplate($this->_sPrefix . '_broadcast', $aTemplateVars, $aProfile['ID']);

				$this->_oDb->queueMessage(trim($sEmail), $aTemplate['subject'], $aTemplate['body']);
 
				$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  

				$iSentMailsCounter++;
            }

            if (!$iSentMailsCounter) {
                $this->_oTemplate->displayErrorOccured();
                return;
            }

            echo MsgBox ($sMsgSent);

            $this->isAllowedBroadcast($aDataEntry, true); // perform send broadcast message action             
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 

        echo $oForm->getCode ();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle);
    }

    function _actionInvite ($iEntryId, $sEmailTemplate, $iMaxEmailInvitations, $sMsgInvitationSent, $sMsgNoUsers, $sTitle) {
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedSendInvitation($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 	
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle . $aDataEntry[$this->_oDb->_sFieldTitle] => '',
		));
	 
        bx_import('BxDolTwigFormInviter');
        $oForm = new BxDolTwigFormInviter ($this, $sMsgNoUsers);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        

            $aInviter = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInviteParams ($aDataEntry, $aInviter);
            
            $oEmailTemplate = new BxDolEmailTemplates();
            $iSuccess = 0;

			$sAcceptUrl = $aPlusOriginal['AcceptUrl'];

            // send invitation to registered members
            if (false !== bx_get('inviter_users') && is_array(bx_get('inviter_users'))) {
				$aInviteUsers = bx_get('inviter_users');
                foreach ($aInviteUsers as $iRecipient) {
                    $aRecipient = getProfileInfo($iRecipient);

 					$sInviteCode = $this->_oDb->addInvite($iEntryId, $iRecipient);
					$aPlusOriginal['AcceptUrl'] = $sAcceptUrl .'/'. $iRecipient .'/'. $sInviteCode; 

                   // freddy
				   // $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);
				   $aPlus = array_merge (array ('FullName' => ' ' . $aRecipient['FullName']), $aPlusOriginal);
            
					$aTemplate = $oEmailTemplate->parseTemplate($sEmailTemplate, $aPlus, $iRecipient);
 
					$this->_oDb->queueMessage(trim($aRecipient['Email']), $aTemplate['subject'], $aTemplate['body']);
 
					$this->_oDb->addInvite($iEntryId, $iRecipient);

					$this->inviteToInbox($aRecipient, $aTemplate, $aPlusOriginal); 

					$iSuccess++;
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", bx_get('inviter_emails'));
            $aPlus = array_merge (array ('NickName' => ''), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;

 					$sInviteCode = $this->_oDb->addInvite($iEntryId, 0);
					$aPlus['AcceptUrl'] = $sAcceptUrl .'/0/'. $sInviteCode; 
 
                    //$iRet = sendMail(trim($sEmail), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;

					$aTemplate = $oEmailTemplate->parseTemplate($sEmailTemplate, $aPlus, 0);
 
					$this->_oDb->queueMessage(trim($sEmail), $aTemplate['subject'], $aTemplate['body']);

                    $iSuccess ++;
                    if ($iRet && 0 == --$iMaxCount) 
                        break;
                }             
            }

            $sMsg = sprintf($sMsgInvitationSent, $iSuccess);
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle . $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function  inviteToInbox($aProfile, $aTemplate, $aPlusOriginal){
	
	$aMailBoxSettings = array
	(
		'member_id'	 =>  $this->_iProfileId, 
		'recipient_id'	 => $aProfile['ID'], 
		'messages_types'	 =>  'mail',  
	);

	$aComposeSettings = array
	(
		'send_copy' => false , 
		'send_copy_to_me' => false , 
		'notification' => false ,
	);
	$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);

	$sMessageBody = $aTemplate['Body'];
	$sMessageBody = str_replace("<EventName>", $aPlusOriginal['EventName'] , $sMessageBody);
	$sMessageBody = str_replace("<EventLocation>", $aPlusOriginal['EventLocation'] , $sMessageBody);
	$sMessageBody = str_replace("<EventStart>", $aPlusOriginal['EventStart'] , $sMessageBody);
	$sMessageBody = str_replace("<EventUrl>", $aPlusOriginal['EventUrl'] , $sMessageBody); 
	$sMessageBody = str_replace("<AcceptUrl>", $aPlusOriginal['AcceptUrl'] .'/'. $aProfile['ID'] , $sMessageBody); 
	$sMessageBody = str_replace("<InviterUrl>", $aPlusOriginal['InviterUrl'] , $sMessageBody);
	$sMessageBody = str_replace("<InviterNickName>", $aPlusOriginal['InviterNickName'] , $sMessageBody);
	$sMessageBody = str_replace("<InvitationText>", $aPlusOriginal['InvitationText'] , $sMessageBody);
	
	$oMailBox -> iWaitMinutes = 0;
	$oMailBox -> sendMessage($aTemplate['Subject'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

   }

   function  broadCastToInbox($aProfile, $aTemplate, $aTemplateVars){

	$aMailBoxSettings = array
	(
		'member_id'	 =>  $this->_iProfileId, 
		'recipient_id'	 => $aProfile['ID'], 
		'messages_types'	 =>  'mail',  
	);

	$aComposeSettings = array
	(
		'send_copy' => false , 
		'send_copy_to_me' => false , 
		'notification' => false ,
	);
	$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);

	$sMessageBody = $aTemplate['body'];
	$sMessageBody = str_replace("<NickName>", getNickName($this->_iProfileId), $sMessageBody);
	$sMessageBody = str_replace("<EntryUrl>", $aTemplateVars['EntryUrl'], $sMessageBody);
	$sMessageBody = str_replace("<EntryTitle>", $aTemplateVars['EntryTitle'], $sMessageBody);
	$sMessageBody = str_replace("<BroadcastMessage>", $aTemplateVars['BroadcastMessage'], $sMessageBody);
 
	$oMailBox -> iWaitMinutes = 0;
	$oMailBox -> sendMessage($aTemplateVars['BroadcastTitle'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

    }

    function parseTags($s)
    {
        return $this->_parseAnything($s, ',', BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/tag/');
    }

    function parseCategories($s)
    {
        return $this->_parseAnything($s, CATEGORIES_DIVIDER, BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/category/');
    }

    function _parseAnything($s, $sDiv, $sLinkStart, $sClassName = '')
    {
        $sRet = '';
        $a = explode ($sDiv, $s);
        $sClass = $sClassName ? 'class="'.$sClassName.'"' : '';
        
        foreach ($a as $sName)
            $sRet .= '<a '.$sClass.' href="' . $sLinkStart . urlencode(title2uri($sName)) . '">'.$sName.'</a>&#160';
        
        return $sRet;
    } 
 
    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_EVENTS_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return $this->_isMembershipEnabledFor ('BX_VIDEOS_ADD');
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_EVENTS_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_EVENTS_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_EVENTS_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'events photos add', 'events sounds add', 'events videos add', 'events files add'));
		if (!defined($sMembershipActionConstant))
			return false;
		$aCheck = checkAction($_COOKIE['memberID'], constant($sMembershipActionConstant));
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }  
    //[end] - ultimate events mod from modzzz 

    function actionPaypalProcess($iProfileId, $iEventId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
 
			$aDataEntry = $this->_oDb->getEntryById ($iEventId);
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
 
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPostPurchase(_t('_bx_events_purchase_failed')); 
 				return;
			}

        	array_walk($aResponse['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        	if(strcmp($aResponse['content'][0], "INVALID") == 0){
  				$this->actionPostPurchase(_t('_payment_pp_err_wrong_transaction'));
 				return; 
        	}

			if(strcmp($aResponse['content'][0], "VERIFIED") != 0){
  				$this->actionPostPurchase(_t('_payment_pp_err_wrong_verification_status'));
 				return;  
			}
  
			//if (($aData['receiver_email'] != trim(getParam('bx_events_paypal_email'))) || ($aData['txn_type'] != 'web_accept')) {

			if($aData['txn_type'] != 'web_accept') {
				$this->actionPostPurchase(_t('_bx_events_purchase_failed'));
			}else{ 
				if($this->_oDb->isExistPaypalTransaction($iProfileId, $aData['txn_id'])) { 
					$this -> actionPostPurchase(_t('_bx_events_transaction_completed_already', $sRedirectUrl)); 
				} else {
					if( $this->_oDb->saveTransactionRecord($iProfileId, $iEventId, $aData['txn_id'], 'Paypal Purchase')) { 
						
						if((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()){   
							$this->_oDb->setItemStatus($iEventId, 'approved'); 
						}

						$this->_oDb->setInvoiceStatus($iEventId, 'paid'); 
					} else {
						$this -> actionPostPurchase(_t('_bx_events_trans_save_failed'));
					}
				}
			}
           
        }
    }

	function _getReceivedAmount(&$aResultData) {
	    $fAmount = 0.00; 
		$sCurrencyCode = $this->_oConfig->getPurchaseCurrency();

    	if($aResultData['mc_currency'] == $sCurrencyCode && isset($aResultData['payment_gross']) && !empty($aResultData['payment_gross']))	
    		$fAmount = (float)$aResultData['payment_gross'];	
    	else if($aResultData['mc_currency'] == $sCurrencyCode && isset($aResultData['mc_gross']) && !empty($aResultData['mc_gross']))	
    		$fAmount = (float)$aResultData['mc_gross'];
    	else if($aResultData['settle_currency'] == $sCurrencyCode && isset($aResultData['settle_amount']) && !empty($aResultData['settle_amount']))	
    		$fAmount = (float)$aResultData['settle_amount'];

    	return $fAmount;
    }
 
    function _readValidationData($sRequest) {
        $sHeader = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    	$sHeader .= "Host: www.paypal.com\r\n";
    	$sHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
    	$sHeader .= "Content-Length: " . strlen($sRequest) . "\r\n";
    	$sHeader .= "Connection: close\r\n\r\n";
    	
    	$iErrCode = 0;
    	$sErrMessage = "";
		$rSocket = fsockopen("ssl://www.paypal.com", 443, $iErrCode, $sErrMessage, 60);
 
    	if(!$rSocket)
    		return array('code' => 2, 'message' => 'Can\'t connect to remote host for validation (' . $sErrMessage . ')');

    	fputs($rSocket, $sHeader . $sRequest);
    	$sResponse = '';
        while(!feof($rSocket))
            $sResponse .= fread($rSocket, 1024);
    	fclose($rSocket);
      
    	$aResponse = explode("\r\n\r\n", $sResponse);
    	$sResponseHeader = $aResponse[0];
    	$sResponseContent = $aResponse[1];

    	return array('code' => 0, 'content' => explode("\n", $sResponseContent));
    }

	function initializeCheckout($iEventId, $fTotalCost, $iQuantity=1, $bFeatured=0) {

		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	 
		if($bFeatured){
			$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iEventId;
			$sItemDesc = getParam('bx_events_featured_purchase_desc');
 		}else{
			$sNotifyUrl = $this->_oConfig->getPurchaseCallbackUrl() . $this->_iProfileId .'/'. $iEventId;
			$sItemDesc = getParam('bx_events_paypal_item_desc');
		}
 
		$aDataEntry = $this->_oDb->getEntryById($iEventId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];
  
        $aFormData = array_merge($aFormData, array(
			'business' => getParam('bx_events_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iEventId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl(),
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Event Listing");
    	exit();
	}

    function actionPostPurchase($sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }
 
		$sMessageOutput = MsgBox($sTransMessage);  
	  
        $this->_oTemplate->pageStart();
    
	    echo $sMessageOutput;
    
        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_bx_events_post_purchase_header')); 
    }
 

	//modzzz.com
    function actionPurchaseFeatured($iEventId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('bx_events_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iEventId);
		$sTitle = $aDataEntry['Title'];

        $this->_oTemplate->pageStart();
 
	    $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iEventId,
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_purchase',
                ),
            ),
            'inputs' => array( 
  
                'title' => array(
                    'type' => 'custom',
                    'name' => 'title',
					'caption'  => _t('_bx_events_caption_title'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_bx_events_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_bx_events_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_bx_events_featured_until') .' '. $this->_oTemplate->filterCustomDate($aDataEntry['featured_expiry_date']) : _t('_bx_events_not_featured'), 
                ), 
                'quantity' => array(
                    'caption'  => _t('_bx_events_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_bx_events_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_bx_events_extend_featured') : _t('_bx_events_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 

			$fCost =  number_format($iPerDayCost, 2); 
  
			$this->initializeCheckout($iEventId, $fCost, $oForm->getCleanValue('quantity'), true);  
			return;   
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->addCss ('paid.css'); 
        $this->_oTemplate->pageCode(_t('_bx_events_purchase_featured')); 
    }
 
    function actionPaypalFeaturedProcess($iProfileId, $iEventId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iEventId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_bx_events_purchase_failed')); 
 				return;
			}

        	array_walk($aResponse['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        	if(strcmp($aResponse['content'][0], "INVALID") == 0){
  				$this->actionPurchaseFeatured(_t('_payment_pp_err_wrong_transaction'));
 				return; 
        	}

			if(strcmp($aResponse['content'][0], "VERIFIED") != 0){
  				$this->actionPurchaseFeatured(_t('_payment_pp_err_wrong_verification_status'));
 				return;  
			}
 			
			if($aData['txn_type'] != 'web_accept') {
				$this->actionPurchaseFeatured($iEventId, _t('_bx_events_purchase_failed'));
			}else { 
				$fAmount = $this->_getReceivedAmount($aData);
			
				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iEventId, _t('_bx_events_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iEventId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iEventId, $iQuantity); 
			    
					} else {
						$this -> actionPurchaseFeatured($iEventId, _t('_bx_events_purchase_fail'));
					}
				}
			}
            
        }
    }
 
    function isAllowedPurchaseFeaturedOLD ($aDataEntry, $isPerformAction = false) {
  
		if(getParam("bx_events_buy_featured")!='on')
			return false;
   
		if ($this->isAdmin())
            return false;

		if($aDataEntry['Featured'] && !$aDataEntry['featured_expiry_date'])
            return false;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 
 
	/******[BEGIN] Sponsor functions **************************/ 
    function actionSponsor ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->sponsorDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionSponsorAdd ($sParam1, '_bx_events_page_title_sponsor_add');
			break;
			case 'edit':
				$this->actionSponsorEdit ($sParam1, '_bx_events_page_title_sponsor_edit');
			break;
			case 'delete':
				$this->actionSponsorDelete ($sParam1, _t('_bx_events_msg_event_was_sponsor_deleted'));
			break;
			case 'view':
				$this->actionSponsorView ($sParam1, _t('_bx_events_msg_pending_sponsor_approval')); 
			break; 
			case 'browse':
				return $this->actionSponsorBrowse ($sParam1, '_bx_events_page_title_sponsor_browse'); 
			break;  
		}
	}
    
    function actionSponsorBrowse ($sUri, $sTitle) {
       
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }	
		
		$this->sUri = $sUri;
 
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_events_menu_view_sponsors') => '',
        ));

        bx_import ('SponsorPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['Title']), false, false);  
    }
 
    function actionSponsorView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
		$aSponsorEntry = $this->_oDb->getSponsorEntryByUri($sUri);
		$iEntryId = (int)$aSponsorEntry['event_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];
  
        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aSponsorEntry['title'] => '',
        ));

        if ( !$this->isAllowedViewSubProfile($this->_oDb->_sTableSponsor, $aSponsorEntry, true) ) { 
			$this->_oTemplate->displayAccessDenied ();
			return false;
        }
 
        bx_import ('SponsorPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorPageView';
        $oPage = new $sClass ($this, $aSponsorEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		if (BxDolInstallerUtils::isModuleInstalled("event_customize"))
		{
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('event_customize', 'get_event_style', array($aDataEntry[$this->_oDb->_sFieldId])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";

		}else{
			echo $sPageCode; 
		}

        bx_import('SponsorCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aSponsorEntry['desc']), 0, 255));

        $this->_oTemplate->addJsTranslation(array('_are you sure?'));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aSponsorEntry['title'], false, false); 
    }


    function actionSponsorEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aSponsorEntry = $this->_oDb->getSponsorEntryById($iEntryId);
		$iSponsorId = (int)$aSponsorEntry['event_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iSponsorId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aSponsorEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aSponsorEntry['title'] => '',
        ));
 
        bx_import ('SponsorFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorFormEdit';
        $oForm = new $sClass ($this, $aSponsorEntry['uri'], $iSponsorId,  $iEntryId, $aSponsorEntry);
  
        $oForm->initChecker($aSponsorEntry);

        if ($oForm->isSubmittedAndValid ()) {
			
			$aValsAdd = array();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
				$this->onEventSubItemChanged ('sponsor', $iEntryId, 'approved', $aSponsorEntry);
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sponsor/view/' . $aSponsorEntry['uri']);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aSponsorEntry['title']));  
    }

    function actionSponsorDelete ($iSponsorId, $sMsgSuccess) {

		$aSponsorEntry = $this->_oDb->getSponsorEntryById($iSponsorId);
		$iEntryId = (int)$aSponsorEntry['event_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aSponsorEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteSponsorByIdAndOwner($iSponsorId, $iEntryId, $this->_iProfileId, $this->isAdmin())) { 
            $this->onEventSponsorDeleted ($iSponsorId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iSponsorId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionSponsorAdd ($iSponsorId, $sTitle) {
   
		if (!($aDataEntry = $this->_oDb->getEntryById($iSponsorId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if (!$this->isAllowedSubAdd($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
  		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));

        $this->_addSponsorForm($iSponsorId);

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['Title']));  
    }
 
    function _addSponsorForm ($iSponsorId) { 
 
        bx_import ('SponsorFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iSponsorId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'sponsor_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSponsorMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getSponsorEntryById($iEntryId);
    
				$this->onEventSubItemCreate ('sponsor', $iEntryId, $sStatus, $aDataEntry);
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sponsor/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventSponsorDeleted ($iEntryId, $aDataEntry = array()) {
  
        // delete votings
        bx_import('SponsorVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorVoting';
        $oVoting = new $sClass ($this->_oDb->_sSponsorPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('SponsorCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SponsorCmts';
        $oCmts = new $sClass ($this->_oDb->_sSponsorPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
          // delete associated locations
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_sponsor', $iEntryId));

        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        


    /*******[END - Sponsor Functions] ******************************/


	/******[BEGIN] Venue functions **************************/ 
    function actionVenue ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->venueDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionVenueAdd ($sParam1, '_bx_events_page_title_venue_add');
			break;
			case 'edit':
				$this->actionVenueEdit ($sParam1, '_bx_events_page_title_venue_edit');
			break;
			case 'delete':
				$this->actionVenueDelete ($sParam1, _t('_bx_events_msg_event_was_venue_deleted'));
			break;
			case 'view':
				$this->actionVenueView ($sParam1, _t('_bx_events_msg_pending_venue_approval')); 
			break; 
			case 'browse':
				return $this->actionVenueBrowse ($sParam1, '_bx_events_page_title_venue_browse'); 
			break;  
		}
	}
    
    function actionVenueBrowse ($sUri, $sTitle) {
       
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
 		$this->sUri = $sUri;

		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_events_menu_view_venues') => '',
        ));

        bx_import ('VenuePageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenuePageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['Title']), false, false);  
    }
 
    function actionVenueView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
		$aVenueEntry = $this->_oDb->getVenueEntryByUri($sUri);
		$iEntryId = (int)$aVenueEntry['event_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
   
 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aVenueEntry['title'] => '',
        ));

        if ( !$this->isAllowedViewSubProfile($this->_oDb->_sTableVenue, $aVenueEntry, true) ) { 
			$this->_oTemplate->displayAccessDenied ();
			return false;
        }

        bx_import ('VenuePageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenuePageView';
        $oPage = new $sClass ($this, $aVenueEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		if (BxDolInstallerUtils::isModuleInstalled("event_customize"))
		{
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('event_customize', 'get_event_style', array($aDataEntry[$this->_oDb->_sFieldId])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";

		}else{
			echo $sPageCode; 
		}

        bx_import('VenueCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aVenueEntry['desc']), 0, 255));
 
        $this->_oTemplate->addJsTranslation(array('_are you sure?'));

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aVenueEntry['title'], false, false); 
    }
 
    function actionVenueEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aVenueEntry = $this->_oDb->getVenueEntryById($iEntryId);
		$iVenueId = (int)$aVenueEntry['event_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iVenueId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aVenueEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aVenueEntry['title'] => '',
        ));


        bx_import ('VenueFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueFormEdit';
        $oForm = new $sClass ($this, $aVenueEntry['uri'], $iVenueId,  $iEntryId, $aVenueEntry);
  
        $oForm->initChecker($aVenueEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
   
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'venue/view/' . $aVenueEntry['uri']);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aVenueEntry['title']));  
    }

    function actionVenueDelete ($iVenueId, $sMsgSuccess) {

		$aVenueEntry = $this->_oDb->getVenueEntryById($iVenueId);
		$iEntryId = (int)$aVenueEntry['event_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aVenueEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteVenueByIdAndOwner($iVenueId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
          
            $this->onEventVenueDeleted ($iVenueId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iVenueId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionVenueAdd ($iVenueId, $sTitle) {
   
		if (!($aDataEntry = $this->_oDb->getEntryById($iVenueId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if (!$this->isAllowedSubAdd($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
  		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));

        $this->_addVenueForm($iVenueId);

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['Title']));  
    }
 
    function _addVenueForm ($iVenueId) { 
 
        bx_import ('VenueFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iVenueId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'venue_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
  			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableVenueMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

 				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getVenueEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'venue/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function onEventVenueDeleted ($iEntryId, $aDataEntry = array()) {
  
        // delete votings
        bx_import('VenueVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueVoting';
        $oVoting = new $sClass ($this->_oDb->_sVenuePrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('VenueCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'VenueCmts';
        $oCmts = new $sClass ($this->_oDb->_sVenuePrefix, $iEntryId);
        $oCmts->onObjectDelete ();
  
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        


    /*******[END - VENUE Functions] ******************************/


	/******[BEGIN] News functions **************************/ 
    function actionNews ($sAction, $sParam1, $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->newsDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionNewsAdd ($sParam1, '_bx_events_page_title_news_add');
			break;
			case 'edit':
				$this->actionNewsEdit ($sParam1, '_bx_events_page_title_news_edit');
			break;
			case 'delete':
				$this->actionNewsDelete ($sParam1, _t('_bx_events_msg_event_was_news_deleted'));
			break;
			case 'view':
				$this->actionNewsView ($sParam1, _t('_bx_events_msg_pending_news_approval')); 
			break; 
			case 'browse':
				return $this->actionNewsBrowse ($sParam1, '_bx_events_page_title_news_browse'); 
			break;  
		}
	}
    
    function actionNewsBrowse ($sUri, $sTitle) {
       
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
		
		$this->sUri = $sUri;

		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_bx_events_menu_view_news') => '',
        ));

        bx_import ('NewsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['Title']), false, false);  
    }
 
    function actionNewsView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
 
		$aNewsEntry = $this->_oDb->getNewsEntryByUri($sUri);
		$iEntryId = (int)$aNewsEntry['event_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
   		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aNewsEntry['title'] => '',
        ));


        if ( !$this->isAllowedViewSubProfile($this->_oDb->_sTableNews, $aNewsEntry, true) ) { 
			$this->_oTemplate->displayAccessDenied ();
			return false;
        }

        bx_import ('NewsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageView';
        $oPage = new $sClass ($this, $aNewsEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        $sPageCode = $oPage->getCode();

		if (BxDolInstallerUtils::isModuleInstalled("event_customize"))
		{
			$sMainCss = '<style type="text/css">' . 
				BxDolService::call('event_customize', 'get_event_style', array($aDataEntry[$this->_oDb->_sFieldId])) . '</style>';

			echo "
			<div id=\"divUnderCustomization\">
				$sMainCss
				$sPageCode
				<div class=\"clear_both\"></div>
			</div>
			";

		}else{
			echo $sPageCode; 
		}

        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aNewsEntry['desc']), 0, 255));
 
        $this->_oTemplate->addJsTranslation(array('_are you sure?'));

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aNewsEntry['title'], false, false); 
    }


    function actionNewsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aNewsEntry = $this->_oDb->getNewsEntryById($iEntryId);
		$iNewsId = (int)$aNewsEntry['event_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aNewsEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 		
		
		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];
 
        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aNewsEntry['title'] => '',
        ));

        bx_import ('NewsFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsFormEdit';
        $oForm = new $sClass ($this, $aNewsEntry['uri'], $iNewsId,  $iEntryId, $aNewsEntry);
  
        $oForm->initChecker($aNewsEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
   
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aNewsEntry['uri']);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aNewsEntry['title']));  
    }

    function actionNewsDelete ($iNewsId, $sMsgSuccess) {

		$aNewsEntry = $this->_oDb->getNewsEntryById($iNewsId);
		$iEntryId = (int)$aNewsEntry['event_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aNewsEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteNewsByIdAndOwner($iNewsId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
 
            $this->onEventNewsDeleted ($iNewsId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionNewsAdd ($iNewsId, $sTitle) {
   
		//[begin] news integration - modzzz
		if(getParam('bx_events_modzzz_news')=='on'){ 
			$oNews = BxDolModule::getInstance('BxNewsModule');
			$sRedirectUrl = BX_DOL_URL_ROOT . $oNews->_oConfig->getBaseUri() . 'browse/my&filter=add_news&event=' . $iNewsId;
		  
			header ('Location:' . $sRedirectUrl);
			exit;
		}
 		//[end] news integration - modzzz

		if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if (!$this->isAllowedSubAdd($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],  
        ));

        $this->_addNewsForm($iNewsId);

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['Title']));  
    }
 
    function _addNewsForm ($iNewsId) { 
 
        bx_import ('NewsFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iNewsId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'news_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 			$this->_oDb->_sFieldAuthorId = 'author_id';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getNewsEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventNewsDeleted ($iEntryId, $aDataEntry = array()) {
  
        // delete votings
        bx_import('NewsVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsVoting';
        $oVoting = new $sClass ($this->_oDb->_sNewsPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_oDb->_sNewsPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        


    /*******[END - News Functions] ******************************/


    /*******[BEGIN - PRINTING] ******************************/
    function actionPrint ($iEntryId) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

		if (!$this->isAllowedPrint($aDataEntry)){
            $this->_oTemplate->displayAccessDenied ();
            return;
		}
 
        $aProfiles = array ();
        $iNum = $this->_oDb->getFans($aProfiles, $iEntryId, true, 0, BX_EVENTS_MAX_FANS);

		$aVars = array (
			'bx_repeat:entries' => array (),
		);
 
		$iCounter = 1;
		foreach($aProfiles as $aEachProfile){
 
			$sEmail = $aEachProfile['Email'];
			$sFirstName = $aEachProfile['FirstName'];
   			$sLastName = $aEachProfile['LastName'];
			// Freddy ajout   $sFullName = $aEachProfile['FullName'];
			$sFullName = $aEachProfile['FullName'];
   			
			$sCity = $aEachProfile['City'];
   			$sCountry = _t($GLOBALS['aPreValues']["Country"][$aEachProfile["Country"]]['LKey']);
   
			$aVars['bx_repeat:entries'][] = array (
				'email' => $sEmail, 
				'firstname' => $sFirstName, 
  				'lastname' => $sLastName, 
				'fullname' => $sFullName,
				'city' => $sCity, 
				'country' => $sCountry, 
 				'pgbreak' => (($iCounter>=10) && ($iCounter%10==0)) ? '<hr>' : '',  
			);  
			$iCounter++; 
		}
 
		$sCode = $this->_oTemplate->parseHtmlByName('print_participants', $aVars); 
  
		$filename = $aDataEntry[$this->_oDb->_sFieldUri] . ".html";  
		header("Content-Disposition: attachment; filename=\"$filename\""); 
		header("Content-Type: text/html"); 
 		echo $sCode; 
		exit; 
	}
 
    function actionExcel ($iEntryId) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

		if (!$this->isAllowedExcel($aDataEntry)){
            $this->_oTemplate->displayAccessDenied ();
            return;
		}
 
		function cleanData(&$str) { 
			$str = preg_replace("/\t/", "\\t", $str); 
			$str = preg_replace("/\r?\n/", "\\n", $str); 
			if(strstr($str, '"')) 
			  $str = '"' . str_replace('"', '""', $str) . '"'; 
		}

		$data = array();  
        $aProfiles = array ();
        $iNum = $this->_oDb->getFans($aProfiles, $iEntryId, true, 0, BX_EVENTS_MAX_FANS);

		foreach($aProfiles as $aEachProfile)
			//Freddy modifremplace first name and lastname par fullname
			//$data[] = array("NickName" => $aEachProfile['NickName'], "FirstName" => $aEachProfile['FirstName'], "LastName" => $aEachProfile['LastName'], "City" => $aEachProfile['City'], "Country" => _t($GLOBALS['aPreValues']["Country"][$aEachProfile["Country"]]['LKey'])); 
			
			$data[] = array("Email" => $aEachProfile['Email'], "FullName" => $aEachProfile['FullName'],  "City" => $aEachProfile['City'], "Country" => _t($GLOBALS['aPreValues']["Country"][$aEachProfile["Country"]]['LKey'])); 
		 
		  
		// filename for download 
		$filename = $aDataEntry[$this->_oDb->_sFieldUri] .'_'. date('Ymd') . ".xls"; 
		header("Content-Disposition: attachment; filename=\"$filename\""); 
		header("Content-Type: application/vnd.ms-excel"); 
		$flag = false; 
		foreach($data as $row) { 
			if(!$flag) { 
				//display field/column names as first row 
				echo implode("\t", array_keys($row)) . "\n"; 
				$flag = true; 
			} 
			array_walk($row, 'cleanData'); 
			echo implode("\t", array_values($row)) . "\n"; 
		} 
		exit;
	}

    function isAllowedExcel (&$aDataEntry) {
        $aProfiles = array ();
        $iNum = $this->_oDb->getFans($aProfiles, $aDataEntry['ID'], true, 0, 1);
 
		if(!$iNum)
			return false;        

        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

		return false;        
    }

    function isAllowedPrint (&$aDataEntry) {
        
        $aProfiles = array ();
        $iNum = $this->_oDb->getFans($aProfiles, $aDataEntry['ID'], true, 0, 1);

		if(!$iNum)
			return false;     
		 
		if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

		return false;        
    }
 
    /*******[END - PRINTING] ******************************/

	/*relisting and extension */
  
    function isAllowedPremium (&$aDataEntry, $isPerformAction = false) {
  
        if (getParam('bx_events_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['Status'] != 'approved')
            return false;

        if ($this->isAdmin() && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_PURCHASE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function actionPremium ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedPremium($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 
        $this->_actionPremium($iEntryId);

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_bx_events_page_title_premium'));
	}
 
    function _actionPremiumOLD ($iEntryId) {
  
		//$bPaidEvents = $this->isAllowedPaidEvents (); 
  
		$aPackage = $this->_oDb->getPackageList();

		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_bx_events_form_caption_package'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_events_form_err_package'),
                    ),   
                ),  
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_bx_events_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {

			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];

			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays);
				
			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			$this->initializeCheckout($iEntryId, $fPrice);  
			return;  
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }

    function _actionPremium ($iEntryId) {
   
		$aPackage = $this->_oDb->getPackageList(true);

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=package&package=' ; 

		$iPackageId = ($_POST['package_id']) ? $_POST['package_id'] : $this->_oDb->getInitPackage();
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_bx_events_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_events_form_err_package'),
                    ),   
                ),   
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_bx_events_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_bx_events_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {

			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];
			$iFeatured = $aPackage['featured'];
			
			//premium
			$sInvoiceStatus = $this->isAdmin() ? 'paid' : 'pending';
			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);
  
			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			if(!$this->isAdmin()){ 
				$this->initializeCheckout($iEntryId, $fPrice);  
				return;  
			}else{
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());

				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

				if($iDays)
					$this->_oDb->updateEntryExpiration($iEntryId, $iDays, $sStatus); 

				if($iFeatured)
					$this->_oDb->updateFeaturedStatus($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                header ('Location:' . $sRedirectUrl);
                exit;
			}  
		}else{ 
			echo $oForm->getCode(); 
		}
    }
     /***/


    function isAllowedRelist (&$aDataEntry, $isPerformAction = false) {
  
		if($aDataEntry['Status'] != 'expired')
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_RELIST, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function actionRelist ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (!$this->isAllowedRelist($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 
        $this->_actionRelist($iEntryId);

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_bx_events_page_title_relist'));
	}
 
    function _actionRelistOLD ($iEntryId) {
  
		//$bPaidEvents = $this->isAllowedPaidEvents (); 
  
		$aPackage = $this->_oDb->getPackageList();

		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_bx_events_form_caption_package'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_events_form_err_package'),
                    ),   
                ),  
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_bx_events_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {

			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];

			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays);
				
			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			$this->initializeCheckout($iEntryId, $fPrice);  
			return;  
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }

    function _actionRelist ($iEntryId) {
   
		$aPackage = $this->_oDb->getPackageList();

		$sPackageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/?ajax=package&package=' ; 

		$iPackageId = ($_POST['package_id']) ? $_POST['package_id'] : $this->_oDb->getInitPackage();
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
                'package_id' => array( 
                    'type' => 'select',
                    'name' => 'package_id',
					'values'=> $aPackage,
                    'caption' => _t('_bx_events_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_bx_events_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_bx_events_package_desc'),  
                ),  
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_bx_events_continue'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {

			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];
			$iFeatured = $aPackage['featured'];
			
			//relist
			$sInvoiceStatus = ($fPrice && (!$this->isAdmin())) ? 'pending' : 'paid';
			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);
  
			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			if($fPrice && (!$this->isAdmin())){ 
				$this->initializeCheckout($iEntryId, $fPrice);  
				return;  
			}else{
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());

				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

				if($iDays)
					$this->_oDb->updateEntryExpiration($iEntryId, $iDays, $sStatus); 

				if($iFeatured)
					$this->_oDb->updateFeaturedStatus($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                header ('Location:' . $sRedirectUrl);
                exit;
			} 
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }

    function isAllowedExtend (&$aDataEntry, $isPerformAction = false) {
        
        if (getParam('bx_events_paid_active')!='on') 
            return false;	
	
		if($aDataEntry['Status'] != 'approved')
            return false;

		if(!$aDataEntry['expiry_date'])
            return false;
  
		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['ResponsibleID'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_EXTEND, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 


    function actionExtend ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

 
        if (!$this->isPaidListing($iEntryId)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        if (!$this->isAllowedExtend($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);


        $this->_actionExtend($iEntryId);

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_bx_events_page_title_extend'));
	}
 
    function _actionExtendOLD ($iEntryId) {
   
		$aEntry = $this->_oDb->getEntryById($iEntryId);
		$aPackage = $this->_oDb->getPackageByEntryId($iEntryId);
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
                'package_id' => array( 
                    'type' => 'hidden',
                    'name' => 'package_id',
					'value'=> $aPackage['id'] 
                ), 

                'item_title' => array( 
                    'type' => 'custom',
                    'caption' => _t('_bx_events_form_caption_current_item'),  
					'content'=> $aEntry['title'],
                 ), 

                'package_name' => array( 
                    'type' => 'custom',
                    'caption' => _t('_bx_events_form_caption_current_package'),  
					'content'=> $aPackage['name'],
                 ),  
 
				'package_price' => array( 
                    'type' => 'custom',
                    'caption' => _t('_bx_events_form_caption_package_price'),  
					'content'=> getParam("bx_events_currency_sign") .' '. $aPackage['price'],
                 ), 

                'package_days' => array( 
                    'type' => 'custom',
                    'caption' => _t('_bx_events_form_caption_package_days'),  
					'content'=> $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_bx_events_days') : _t('_bx_events_expires_never')
                 ), 
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_bx_events_extend'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {

			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];

			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays);
				
			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			$this->initializeCheckout($iEntryId, $fPrice);  
			return;  
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }

    function _actionExtend ($iEntryId) {
   
		$aEntry = $this->_oDb->getEntryById((int)$iEntryId);
 
		$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aEntry['invoice_no']);
		$sPackageDesc = $this->_oTemplate->getFormPackageDesc($iPackageId);
 
		$aForm = array(
            'form_attrs' => array(
                'name' => 'package_form',
                'method' => 'post', 
                'action' => '',
            ),
            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_package',
                ),
            ),
            'inputs' => array( 
  
                'package_id' => array( 
                    'type' => 'hidden',
                    'name' => 'package_id',
					'value' => $iPackageId 
                ), 

                'item_title' => array( 
                    'type' => 'custom',
                    'caption' => _t('_bx_events_form_caption_current_item'),  
					'content'=> $aEntry['Title'],
                 ), 

 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => $sPackageDesc,  
                    'name' => 'package_desc',
                    'caption' => _t('_bx_events_package_desc'),  
                ),   
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_bx_events_extend'),
                    'name'  => 'submit_package',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('package_id')) {
 
			$iPackageId = $oForm->getCleanValue('package_id');
			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
			$iDays = $aPackage['days'];
			$iFeatured = $aPackage['featured'];

			//extend 
			$sInvoiceStatus = ($fPrice && (!$this->isAdmin())) ? 'pending' : 'paid';
			$sInvoiceNo = $this->_oDb->createInvoice($iEntryId, $iPackageId, $fPrice, $iDays, $sInvoiceStatus);

			$this->_oDb->updateEntryInvoice($iEntryId, $sInvoiceNo); 
		 
			if($fPrice && (!$this->isAdmin())){
				$this->initializeCheckout($iEntryId, $fPrice);  
				return;  
			}else{
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());

				$sStatus = ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

				if($iDays)
					$this->_oDb->updateEntryExpiration($iEntryId, $iDays, $sStatus); 

				if($iFeatured)
					$this->_oDb->updateFeaturedStatus($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                header ('Location:' . $sRedirectUrl);
                exit;
			}
 
		}else{ 
			echo $oForm->getCode(); 
		}
    }
	/***END Extend***************/

	function serviceCalendar() {  
  
        $aDateParams = array(0, 0);
        $sDate = bx_get('date');
        if ($sDate)
            $aDateParams = explode('/', $sDate);

        bx_import ('Calendar', $this->_aModule);
        $oCalendar = bx_instance ($this->_aModule['class_prefix'] . 'Calendar', array ((int)$aDateParams[0], (int)$aDateParams[1], $this->_oDb, $this->_oConfig, $this->_oTemplate));

        $oCalendar->setBlockId($this->_oDb->getBlockId());
        $oCalendar->setDynamicUrl(BX_DOL_URL_ROOT);

        return $oCalendar->display(true);  
    }

	function actionPay ($sUri) { 
 
		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if (!preg_match($sReg, $sUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}

        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        $iEntryId = (int)$aDataEntry[$this->_oDb->_sFieldId];

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
 		$this->sUri = $sUri;

		$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']); 
		$bPaidListing = $this->isPaidPackage($iPackageId);   
	 
		if($bPaidListing && $aDataEntry[$this->_oDb->_sFieldStatus]=='pending'){

			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
	 
			$this->initializeCheckout($iEntryId, $fPrice);  
			return;   
		}else{  
			header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		}   
		exit;  
    }
   
    function _formatDateRichSnippet (&$aEvent)
    { 
		return date('c', $aEvent['EventStart']);
    }

    function _formatDateInBrowse (&$aEvent)
    {
        if ($aEvent['EventStart'] < time() && $aEvent['EventEnd'] > time())
            return _t('_bx_events_event_is_in_process');
        elseif ($aEvent['EventEnd'] < time())
            return defineTimeInterval($aEvent['EventEnd']);
        else
            return defineTimeInterval($aEvent['EventStart']);
    }
  
    function _formatLocation (&$aEvent, $isCountryLink = false, $isFlag = false)
    {
		$sCountryCode = ($aEvent['Country']) ? $aEvent['Country'] : $aEvent['country'];
		$sCity = ($aEvent['City']) ? $aEvent['City'] : $aEvent['city'];

        $sFlag = $isFlag ? ' ' . genFlag($sCountryCode) : '';
        $sCountry = _t($GLOBALS['aPreValues']['Country'][$sCountryCode]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($sCountryCode) . '">' . $sCountry . '</a>';
        // freddy 
	   // return (trim($aEvent['Place']) ? $aEvent['Place'] . ', ' : '') . (trim($sCity) ? $sCity . ', ' : '') . $sCountry . $sFlag;
		return (trim($aEvent['Place']) ? $aEvent['Place'] . ' ' : '') . '<br>'. '<br>'. ' <i class="sys-icon map-marker"> </i>'.  '&nbsp '. (trim($sCity) ? $sCity . ' ' : '') .'&nbsp;  '.$sFlag.' '. $sCountry  ;
    }

    function _formatEventLocation (&$aEvent, $isCountryLink = false, $isCityLink = false, $isFlag = false)
    {
		$sCountryCode = ($aEvent['Country']) ? $aEvent['Country'] : $aEvent['country'];

		$sCity = ($aEvent['City']) ? $aEvent['City'] : $aEvent['city']; 
        if ($isCityLink)
            $sCity = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/city/' . strtolower($sCity) . '">' . $sCity . '</a>';
 
        $sFlag = $isFlag ? ' ' . genFlag($sCountryCode) : '';

        $sCountry = _t($GLOBALS['aPreValues']['Country'][$sCountryCode]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($sCountryCode) . '">' . $sCountry . '</a>';
 
       // freddy 
	   // return (trim($aEvent['Place']) ? $aEvent['Place'] . ', ' : '') . (trim($sCity) ? $sCity . ', ' : '') . $sCountry . $sFlag;
		return (trim($aEvent['Place']) ? $aEvent['Place'] . ' ' : '') . '<br>'. ' <i class="sys-icon map-marker"> </i>'.  '&nbsp '. (trim($sCity) ? $sCity . ' ' : '').'&nbsp;  '.$sFlag.' '. $sCountry   ;
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 150, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }

    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc' => $this->_formatSnippetText($aEntryData, 200),
            'event_date' => $this->_formatDateInBrowse($aEntryData),
            'location' => $this->_formatLocation($aEntryData, false, false),
            'participants' => $aEntryData['FansCount'],
        ));
    }

    /**
     * Member menu item for adding event
     * @return html to show in member menu
     */
    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_bx_events_single'), _t('_bx_events_single'), 'calendar', false, '&bx_events_filter=add_event');
    }

    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_bx_events_wall_object',
            'txt_added_new_single' => '_bx_events_wall_added_new',
            'txt_added_new_plural' => '_bx_events_wall_added_new_items',
            'txt_privacy_view_event' => 'view_event',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost($aEvent, $aParams);
    }

	function serviceGetWallAddComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_event',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallAddComment($aEvent, $aParams);
    }


    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_event',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPostComment($aEvent, $aParams);
    }

    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_event',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'calendar', $aParams);
    }
 
 
    /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('events', array(
            'part' => 'events',
            'title' => '_bx_events',
            'title_singular' => '_bx_events_single',
            'icon' => 'modules/boonex/events/|map_marker.png',
            'icon_site' => 'calendar',
            'join_table' => 'bx_events_main',
            'join_where' => "AND `p`.`Status` = 'approved'",
            'join_field_id' => 'ID',
            'join_field_country' => 'Country',
            'join_field_city' => 'City',
            'join_field_state' => 'State',
            'join_field_zip' => 'Zip',
            'join_field_address' => 'Street',
            'join_field_title' => 'Title',
            'join_field_uri' => 'EntryUri',
            'join_field_author' => 'ResponsibleID',
            'join_field_privacy' => 'allow_view_event_to',
            'permalink' => 'modules/?r=events/view/',
        )));
    }

    function serviceSponsorMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('events_sponsor', array(
            'part' => 'events_sponsor',
            'title' => '_bx_events_sponsor',
            'title_singular' => '_bx_events_sponsor_single',
            'icon' => 'modules/boonex/events/|map_marker.png',
            'icon_site' => 'calendar',
            'join_table' => 'bx_events_sponsor_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => 'state',
            'join_field_zip' => 'zip',
            'join_field_address' => 'address1',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_to',
            'permalink' => 'modules/?r=events/sponsor/view/',
        )));
    }


    // ================================== admin actions

    function actionGatherLangKeys ()
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $a = array ();
        $sDir = BX_DIRECTORY_PATH_MODULES . $GLOBALS['aModule']['path'] . 'classes/';
        if ($h = opendir($sDir)) {
            while (false !== ($f = readdir($h))) {
                if ($f == "." || $f == ".." || substr($f, -4) != '.php')
                    continue;
                $s = file_get_contents ($sDir . $f);
                if (preg_match_all("/_t[\s]*\([\s]*['\"]{1}(.*?)['\"]{1}[\s]*\)/", $s, $m))
                    foreach ($m[1] as $sKey)
                        $a[] = $sKey;
            }
            closedir($h);
        }

        echo '<pre>';
        echo "\$aLangContent = array(\n";
        asort ($a);
        foreach ($a as $sKey)
            if (preg_match('/^_bx_events/', $sKey))
                echo "\t'$sKey' => '" . (_t($sKey) == $sKey ? '' : _t($sKey)) . "',\n";
        echo ');';
        echo '</pre>';
        exit;
    }


    function onEventSubItemCreate ($sType, $iEntryId, $sStatus, $aDataEntry = array())
    {
        if ('approved' == $sStatus) {
			//
        }

        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri().'_'.$sType, $iEntryId)); 
    }
 
    function onEventSubItemChanged ($sType, $iEntryId, $sStatus, $aDataEntry = array())
    {
        if ('approved' == $sStatus) {
			//
        }
 
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_change', array($this->_oConfig->getUri().'_'.$sType, $iEntryId));
 
    }

	//[begin modzzz] add participant modification 
    function isAllowedAddParticipant ($iEntryId, $isPerformAction = false) {
		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);

        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;
 
        return false;
    } 
    function actionAddParticipant ($iEntryId) {
 
        $this->_oTemplate->pageStart();

        bx_import ('ParticipantForm', $this->_aModule);
		$oForm = new BxEventsParticipantForm ($this, $iEntryId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
  
			$iEntryId = (int)$iEntryId;

			$aDataEntry = $this->_oDb->getEntryById($iEntryId);
			$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

			$sParticipants = trim($oForm->getCleanValue('participant'));
			if($sParticipants){
				$aParticipant = explode(',',$sParticipants);
				foreach($aParticipant as $sEachParticipant){
					$sEachParticipant = trim($sEachParticipant);
					$iParticipant = ($sEachParticipant) ? getID($sEachParticipant) : 0;
  
					if(!$iParticipant) continue;

					$isFan = $this->_oDb->isFan ($iEntryId, $iParticipant, true) || $this->_oDb->isFan ($iEntryId, $iParticipant, false);

					if(!$isFan){
						$this->_oDb->joinEntry($iEntryId, $iParticipant, true); 
					}
				}
			}

			$iGroupId = $oForm->getCleanValue('group'); 
			if($iGroupId){ 
				
				$oGroup = BxDolModule::getInstance('BxEventsModule');
	
				$aProfiles = array();
				$iGroupFans = $oGroup->_oDb->getFans($aProfiles, $iGroupId, true, 0, 5000);

				foreach($aProfiles as $aEachProfile){
					
					$iParticipant = (int)$aEachProfile['ID'];

					$isFan = $this->_oDb->isFan ($iEntryId, $iParticipant, true) || $this->_oDb->isFan ($iEntryId, $iParticipant, false);

					if(!$isFan){
						$this->_oDb->joinEntry($iEntryId, $iParticipant, true); 
					}
				}

				$this->_oDb->monitorGroup ($iEntryId, $iGroupId); 
			}
 
			header ('Location:' . $sUrl);
			exit; 
        } 

        $sForm = $oForm->getCode ();
 
        $aVarsPopup = array (
            'title' => _t('_bx_events_title_add_participants'),
            'content' => $sForm,
        );        
        
		echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
 	}

	//[end modzzz] add participant modification

    function actionManageAdminsPopup ($iEntryId) {
        $this->_actionManageAdminsPopup ($iEntryId, _t('_bx_events_caption_manage_admins'), 'isAllowedManageAdmins', BX_EVENTS_MAX_FANS);
    }

    function _actionManageAdminsPopup ($iEntryId, $sTitle, $sFuncIsAllowedManageAdmins = 'isAllowedManageAdmins', $iMaxFans = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($iEntryId, 0, true))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        if (!$this->$sFuncIsAllowedManageAdmins($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

        $aProfiles = array ();
        $iNum = $this->_oDb->getAdmins($aProfiles, $iEntryId, 0, $iMaxFans);
        if (!$iNum) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }
 
        $sActionsUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri],  'ajax_action=');
 
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_remove',
                'value' => _t('_sys_btn_fans_remove'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}remove_admin&ids=' + sys_manage_items_get_manage_fans_ids(), false, 'post'); return false;\"",
            ),
        );
 

        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_fans', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (
			'entry_id' => $iEntryId,
            'suffix' => 'manage_fans',
            'content' => $this->_profilesEdit($aProfiles, false, $aDataEntry),
            'control' => $sControl,
        );
        $aVarsPopup = array (
            'title' => $sTitle,
            'content' => $this->_oTemplate->parseHtmlByName('manage_items_form', $aVarsContent),
        );
        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
        exit;
    }

/*
 
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_fans', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (
			'entry_id' => $iEntryId,
            'suffix' => 'manage_fans',
            'content' => $this->_profilesEdit($aProfiles, false, $aDataEntry),
            'control' => $sControl,
        );
        $aVarsPopup = array (
            'title' => $sTitle,
            'content' => $this->_oTemplate->parseHtmlByName('manage_items_form', $aVarsContent),
        );
        echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
        exit;
    }
*/
	//[begin] - add administrator
    function actionAddAdmin ($iEventId) {
   
		$sTitle = '_bx_events_page_title_admin_add';
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iEventId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if ( !($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) ) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
        $this->_addAdminForm($iEventId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]));  
    }
 
    function _addAdminForm ($iEventId) { 
 
        bx_import ('AdminFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'AdminFormAdd';
        $oForm = new $sClass ($this, $iEventId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
   
			$aDataEntry = $this->_oDb->getEntryById($iEventId);
 
            $this->_oDb->_sTableMain = 'admins';
			
			$sProfileNick = $oForm->getCleanValue('profile_nick');
			$iAdministratorId = getID($sProfileNick);

            $aValsAdd = array (
                'when' => time(),
                'id_profile' => $iAdministratorId 
            );                        
  
			if (!$iAdministratorId) {

				echo MsgBox(_t('_bx_events_form_err_invalid_admin', $sProfileNick)) . '<br><br>' . $oForm->getCode ();  
 
			} elseif ( $this->_oDb->isGroupAdmin($iEventId, $iAdministratorId)) {

				echo MsgBox(_t('_bx_events_form_err_already_admin', $sProfileNick)) . '<br><br>' . $oForm->getCode ();   
 
 			} else {   

				$oForm->insert ($aValsAdd);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
	 
				header ('Location:' . $sRedirectUrl);
				exit;  
 			}  
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }

    function _processFansActions ($aDataEntry, $iMaxFans = 1000)
    {
        header('Content-type:text/html;charset=utf-8');

        if (false !== bx_get('ajax_action') && $this->isAllowedManageFans($aDataEntry) && 0 == strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {

            $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];
            $aIds = array ();
            if (false !== bx_get('ids'))
                $aIds = $this->_getCleanIdsArray (bx_get('ids'));

            $isShowConfirmedFansOnly = false;
            switch (bx_get('ajax_action')) {

				case 'remove_admin': 
					 
                    if ($this->_oDb->removeAdmins($iEntryId, $aIds)) {
						//
					}
					$aProfiles = array ();
					$iNum = $this->_oDb->getAdmins($aProfiles, $iEntryId, 0, $iMaxFans);
					if (!$iNum) {
						echo MsgBox(_t('_Empty'));
					} else {
						echo $this->_profilesEdit ($aProfiles, true, $aDataEntry);
					} 
					exit; 
                    break; 

                case 'remove':
                    $isShowConfirmedFansOnly = true;
                    if ($this->_oDb->removeFans($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break;
                case 'add_to_admins':
                    $isShowConfirmedFansOnly = true;
                    if ($this->isAllowedManageAdmins($aDataEntry) && $this->_oDb->addGroupAdmin($iEntryId, $aIds)) {
                        $aProfiles = array ();
                        $iNum = $this->_oDb->getAdmins($aProfiles, $iEntryId, 0, $iMaxFans, $aIds);
                        foreach ($aProfiles as $aProfile)
                            $this->onEventFanBecomeAdmin ($iEntryId, $aProfile['ID'], $aDataEntry);
                    }
                    break;
                case 'admins_to_fans':
                    $isShowConfirmedFansOnly = true;
                    $iNum = $this->_oDb->getAdmins($aGroupAdmins, $iEntryId, 0, $iMaxFans);
                    if ($this->isAllowedManageAdmins($aDataEntry) && $this->_oDb->removeGroupAdmin($iEntryId, $aIds)) {
                        foreach ($aGroupAdmins as $aProfile) {
                            if (in_array($aProfile['ID'], $aIds))
                                $this->onEventAdminBecomeFan ($iEntryId, $aProfile['ID'], $aDataEntry);
                        }
                    }
                    break;
                case 'confirm':
                    if ($this->_oDb->confirmFans($iEntryId, $aIds)) {
                        echo '<script type="text/javascript" language="javascript">
                            document.location = "' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '";
                        </script>';
                        $aProfiles = array ();
                        $iNum = $this->_oDb->getFans($aProfiles, $iEntryId, true, 0, $iMaxFans, $aIds);
                        foreach ($aProfiles as $aProfile) {
                            $this->onEventJoin ($iEntryId, $aProfile['ID'], $aDataEntry);
                            $this->onEventJoinConfirm ($iEntryId, $aProfile['ID'], $aDataEntry);
                        }
                    }
                    break;
                case 'reject':
                    if ($this->_oDb->rejectFans($iEntryId, $aIds)) {
                        foreach ($aIds as $iProfileId)
                            $this->onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry);
                    }
                    break;
                case 'list':
                    break;
            }

            $aProfiles = array ();
            $iNum = $this->_oDb->getFans($aProfiles, $iEntryId, $isShowConfirmedFansOnly, 0, $iMaxFans);
            if (!$iNum) {
               
			   echo MsgBox(_t('_Empty'));
			   
            } else {
                echo $this->_profilesEdit ($aProfiles, true, $aDataEntry);
            }
            exit;
        }
    }
 
    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
	{
		$oModuleDb = new BxDolModuleDb();  
		if (!$aForum = $oModuleDb->getModuleByUri('forum')) return false;

        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('view_forum', $aDataEntry[$this->_oDb->_sFieldId], $iProfileId);
    }

	function actionDiscount ($sEntryUri) {
 
		$sTitle = _t('_bx_events_page_title_discount');
		$sMsgSent = _t('_bx_events_msg_discount_added_success');
 
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sEntryUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
		$iEntryId = (int)$aDataEntry[$this->_oDb->_sFieldId];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 		
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));
  
        if (!$this->isEntryAdmin($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        bx_import ('FormDiscounts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormDiscounts';
        $oForm = new $sClass ($this, $iEntryId);
        $oForm->initChecker();


        if ($oForm->isSubmittedAndValid ()) {
              
			//[begin] discount
			$aDiscounts2Keep = array(); 
			if( is_array($_POST['prev_discount']) && count($_POST['prev_discount'])){
				foreach ($_POST['prev_discount'] as $iDiscountId){
					$aDiscounts2Keep[$iDiscountId] = $iDiscountId;
				}
			}
				
			$aDiscountIds = $this->_oDb->getDiscountIds($iEntryId); 
			$aDeletedDiscount = array_diff ($aDiscountIds, $aDiscounts2Keep);

			if ($aDeletedDiscount) { 
				foreach ($aDeletedDiscount as $iDiscountId) {  
					$this->_oDb->removeDiscountEntry($iEntryId, $iDiscountId);
				}
			} 
		    //[end] discount
 
            $this->_oDb->addDiscounts($iEntryId);

            echo MsgBox ($sMsgSent);
   
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode($sMsgSent, true, true);
            return;
        } 

        echo $oForm->getCode ();
 
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode($sTitle);
    }

	function newsDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'news_files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getNewsEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableNews, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }
  
	function blogDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'blog_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getBlogEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableBlog, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function sponsorDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'sponsor_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getSponsorEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableSponsor, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function venueDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'venue_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getVenueEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableVenue, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    function isSubProfileFan($sTable, $aDataEntry, $iProfileId = 0, $isConfirmed = true) {
 		
		if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isSubProfileFan ($sTable, $aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isAllowedViewSubProfile ($sTable, $aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_VIEW, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        $this->_oSubPrivacy = new BxEventsSubPrivacy($this, $sTable); 
	    return $this->_oSubPrivacy->check('view', $aDataEntry['id'], $this->_iProfileId); 
    }

	function isAllowedRateSubProfile($sTable, &$aDataEntry) {       
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
    
		$this->_oSubPrivacy = new BxEventsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedCommentsSubProfile($sTable, &$aDataEntry) {
    
        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;

        $this->_oSubPrivacy = new BxEventsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin() )
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
		 if ( $this->isSubEntryAdmin($aDataEntry) )
            return true;
   
		$this->_oSubPrivacy = new BxEventsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;
        if ( $this->isSubEntryAdmin($aDataEntry) )
			return true;
	
		$this->_oSubPrivacy = new BxEventsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSoundsSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin() )
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;
        if ( $this->isSubEntryAdmin($aDataEntry) )
			return true;

		$this->_oSubPrivacy = new BxEventsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFilesSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin() )
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false;
        if ( $this->isSubEntryAdmin($aDataEntry) )
			return true;

		$this->_oSubPrivacy = new BxEventsSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function actionUploadPhotosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadPhotosSubProfile', 'images', array ('images_choice', 'images_upload'), _t('_bx_events_page_title_upload_photos'));
    }

    function actionUploadVideosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadVideosSubProfile', 'videos', array ('videos_choice', 'videos_upload'), _t('_bx_events_page_title_upload_videos'));
    } 

    function actionUploadFilesSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadFilesSubProfile', 'files', array ('files_choice', 'files_upload'), _t('_bx_events_page_title_upload_files'));
    } 

    function actionUploadSoundsSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadSoundsSubProfile', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_bx_events_page_title_upload_sounds'));
    } 

    function _actionUploadMediaSubProfile ($sType, $sUri, $sIsAllowedFuncName, $sMedia, $aMediaFields, $sTitle) {
   
		switch($sType){
			case 'venue':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableVenueMediaPrefix;
				$sTable = $this->_oDb->_sTableVenue ;
				$sDataFuncName = 'getVenueEntryByUri';
			break; 
			case 'sponsor':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSponsorMediaPrefix;
				$sTable = $this->_oDb->_sTableSponsor ;
				$sDataFuncName = 'getSponsorEntryByUri';
			break; 
			case 'news':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
				$sTable = $this->_oDb->_sTableNews ;
				$sDataFuncName = 'getNewsEntryByUri';
			break; 
		}

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        if (!($aDataEntry = $this->_oDb->$sDataFuncName($sUri)))
            return;

        if (!$this->$sIsAllowedFuncName($sTable, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }


        $this->_oTemplate->pageStart();

        $aEventEntry = $this->_oDb->getEntryById($aDataEntry['event_id']);
  
		$this->sUri = $aEventEntry[$this->_oDb->_sFieldUri];
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aEventEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aEventEntry[$this->_oDb->_sFieldUri]);
  
        $iEntryId = $aDataEntry['id'];

        bx_import (ucwords($sType) . 'FormUploadMedia', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . ucwords($sType) . 'FormUploadMedia';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId],$aDataEntry['event_id'], $iEntryId, $aDataEntry, $sMedia, $aMediaFields);
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {

            $oForm->processMedia($iEntryId, $this->_iProfileId);

            $this->$sIsAllowedFuncName($sTable, $aDataEntry, true); // perform action

            header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . $sType . '/view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
            exit;

         } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');            
        $this->_oTemplate->pageCode($sTitle);
    }
   
    function isSubEntryAdmin($aSubEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;

		$aDataEntry = $this->_oDb->getEntryById ((int)$aSubEntry['event_id']);  

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aSubEntry[$this->_oDb->_sFieldSubAuthorId] == $iProfileId && isProfileActive($iProfileId))
            return true;

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry[$this->_oDb->_sFieldAuthorId] == $iProfileId && isProfileActive($iProfileId))
            return true;

        return $this->_oDb->isGroupAdmin ($aDataEntry[$this->_oDb->_sFieldId], $iProfileId) && isProfileActive($iProfileId);
    }

    function isAllowedSubEdit ($aDataEntry, $isPerformAction = false) {

        if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_EDIT_ANY_EVENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedSubAdd ($aDataEntry, $isPerformAction = false) {

		if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_ADD_EVENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 
  
    function isAllowedSubDelete ($aSubEntry, $isPerformAction = false) {
        
        if ($this->isAdmin() || $this->isSubEntryAdmin($aSubEntry)) 
            return true;
 
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_DELETE_ANY_EVENT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }   
 
    function serviceStatesInstall()
    {
		$this->_oDb->statesInstall(); 
	}	

	//[begin modzzz] embed video modification 
 
    function isAllowedEmbed(&$aDataEntry) {
		
        if( !$this->isAllowedEdit($aDataEntry) ) return false;   

        if ( $this->isAdmin() ) return true;
             
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_EVENTS_ALLOW_EMBED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED; 
    }

    function actionEmbed ($sUri) {
 
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
   
        if (!($aDataEntry = $this->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

 		$this->sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_bx_events_page_title_embed_video') => '',
		));

        if (!$this->isAllowedEmbed($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        bx_import ('EmbedForm', $this->_aModule);
		$oForm = new BxEventsEmbedForm ($this, $aDataEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid()) {        
  
			$iEntryId = $aDataEntry[$this->_oDb->_sFieldId];
			
			$aYoutubes2Keep = array(); 
 			if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){
 
				foreach ($_POST['prev_video'] as $iYoutubeId){
					$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
				}
			}
				
			$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId);
		
			$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

			if ($aDeletedYoutube) {
				foreach ($aDeletedYoutube as $iYoutubeId) {
					$this->_oDb->removeYoutube($iEntryId, $iYoutubeId);
				}
			} 
			  
			$this->_oDb->addYoutube($iEntryId);
 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			header ('Location:' . $sRedirectUrl);
            return;
        } 

        echo $oForm->getCode (); 
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css'); 
        $this->_oTemplate->pageCode(_t('_bx_events_page_title_embed_video') .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
	//[end modzzz] embed video modification
 
	function actionProcess() 
	{echo 1;
		$this -> _oDb -> process();echo 2;
	}
 
	function actionRecurring() 
	{
		$this -> _oDb -> processRecurring();
		
	}
 
	function isActiveMenuLink($sType='', $sUri=''){
		$oModuleDb = new BxDolModuleDb(); 
		return $oModuleDb->getModuleByUri('forum') ? true : false; 
	}
 
    function _manageEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false, $sTemplate = 'unit_admin')
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = $sTemplate;

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayResultBlock())) {
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination($sUrlAdmin);
            $sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
        }

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id', 'filter_checkbox_id', $this->_sFilterName) : '',
            'actions_panel' => ($sTemplate=='unit') ? '' : $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }

	function getEventLink($iEntryId){

		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

		return '<a href="'.$sUrl.'">'.$aDataEntry[$this->_oDb->_sFieldTitle].'</a>';
	}

   function actionPayFeature ($iEntryId) {
 
        header('Content-type:text/html;charset=utf-8');

		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById($iEntryId);
		$sTitle = $aDataEntry['title'];

		$sCode = $sBuyPointsLink = $sBuyCreditsLink = '';
		$iFeaturedCost = $iMoneyBalance = 0;
  
		if(getParam('bx_events_featured_credits')=='on'){ 
 
			$sPaymentUnit = 'credits'; 
			$sMoneyBalanceLabelC = _t("_bx_events_form_caption_credits_balance");
			$sMoneyTypeC = _t("_bx_events_credits");

			$oCredit = BxDolModule::getInstance('BxCreditModule');  
			$sBuyCreditsLink = BX_DOL_URL_ROOT . $oCredit->_oConfig->getBaseUri() . 'purchase_credits'; 
			$iMoneyBalance = (int)$oCredit->getMemberCredits($this->_iProfileId);
			$iFeaturedCost = (int)getParam('bx_events_credits_cost');
			if($iMoneyBalance < $iFeaturedCost){
				$sCode = _t('_bx_events_insufficient_credits_message', number_format($iMoneyBalance,0), number_format($iFeaturedCost,0));
				$sCode .= '&nbsp;'._t('_bx_events_buy_link', $sBuyCreditsLink);
 			}
		}elseif(getParam('bx_events_featured_points')=='on'){
			
 			$sPaymentUnit = 'points';
			$sMoneyBalanceLabelC = _t("_bx_events_form_caption_points_balance");
			$sMoneyTypeC = _t("_bx_events_points");

			$oPoint = BxDolModule::getInstance('BxPointModule');   
			$sBuyPointsLink = BX_DOL_URL_ROOT . $oPoint->_oConfig->getBaseUri() . 'purchase_points'; 
			$iMoneyBalance = $oPoint->_oDb->getMemberPoints($this->_iProfileId);
			$iFeaturedCost = (int)getParam('bx_events_points_cost');
			if($iMoneyBalance < $iFeaturedCost){
				$sCode = _t('_bx_events_insufficient_points_message', number_format($iMoneyBalance,0), number_format($iFeaturedCost,0));
				$sCode .= '&nbsp;'._t('_bx_events_buy_link', $sBuyPointsLink);
 			}
		}
 
		if($sCode) {
			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode));
	 
			$aVarsPopup = array (
				'title' => _t('_bx_events_page_title_feature_item'),
				'content' => $sCode,
			);        
			
			echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true);
			exit;
		}
  
		for($iter=1; $iter<=1000; $iter++)
			$aQuantity[$iter] = $iter;

		$aCustomForm = array(

			'form_attrs' => array(
				'id' => 'events_form',
				'name' => 'events_form',
				'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay_feature/' . $iEntryId,
				'method' => 'post',
				'onsubmit' => "return bx_ajax_form_check(this)",
			),

			'params' => array (
				'db' => array(
					'submit_name' => 'submit_form', 
				),
			),

			'inputs' => array (

				'header_info' => array(
					'type' => 'block_header',
					'caption' => _t("_bx_events_form_caption_feature", $sTitle),
				), 
				'money_balance' => array(
					'type' => 'custom',
					'caption' => $sMoneyBalanceLabelC,
					'content' => number_format($iMoneyBalance,0) .' '. $sMoneyTypeC,
				), 
				'feature_cost' => array(
					'type' => 'custom',
 					'caption' => _t("_bx_events_form_caption_cost_to_feature"),
					'content' => number_format($iFeaturedCost,0) .' '. $sMoneyTypeC .' '. _t("_bx_events_form_per_day"),
				),   
                'quantity' => array(
                    'caption'  => _t('_bx_events_caption_num_featured_days'),
                    'type'   => 'select',
                    'name' => 'quantity',
                    'values' => $aQuantity,
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_bx_events_caption_err_featured_days'),
                    ),
                ),
				'submit_form' => array(
					'type' => 'hidden',
					'name' => 'submit_form', 
					'value' => 1,
				),
				'submit' => array(
					'type' => 'submit',
					'name' => 'submit',
					'value' => _t("_bx_events_form_caption_submit"),
				),
			)
		);
		  
		$oForm = new BxTemplFormView($aCustomForm);
		$oForm->initChecker();

		if ( $oForm->isSubmittedAndValid() ) {
			
			$iQuantity = (int)$oForm->getCleanValue('quantity');
			$iTotalCost = $iQuantity * $iFeaturedCost;
 
			if(getParam('bx_events_featured_credits')=='on'){ 
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_bx_events_insufficient_credits_message', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_bx_events_buy_link', $sBuyCreditsLink);
					echo MsgBox($sCode);
					return;
				}
			}elseif(getParam('bx_events_featured_points')=='on'){
	 
				if($iMoneyBalance < $iTotalCost){
					$sCode = _t('_bx_events_insufficient_points_message', number_format($iMoneyBalance,0), number_format($iTotalCost,0));
					$sCode .= '&nbsp;'._t('_bx_events_buy_link', $sBuyPointsLink);
					echo MsgBox($sCode);
					return;
				}
			}
  
			$bSuccess = $this->_oDb->updateFeaturedStatus($iEntryId);
			$sResultMsg = ($bSuccess) ? _t("_bx_events_msg_feature_success") : _t("_bx_events_msg_feature_failure"); 
		  
			if($bSuccess){
				$this->_oDb->updateFeaturedEntryExpiration($iEntryId, $iQuantity); 
				$this->_oDb->deductPayment($sPaymentUnit, $this->_iProfileId, $iTotalCost);  
			} 
		 
			$sCode = MsgBox($sResultMsg);
		} else {
			$sCode = $oForm->getCode();
		}
 
		// check whether form submitted (AJAX) and show corresponding output
		if (bx_get('BxAjaxSubmit')) {
 
			$sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $aDataEntry['uri'];
			echo $sCode . genAjaxyPopupJS($aDataEntry['ID'], 'ajaxy_popup_result_div', $sRedirect);
 
		}else{

			$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_margin.html', array('content' => $sCode));
	 
			$aVarsPopup = array (
				'title' => _t('_bx_events_page_title_feature_item'),
				'content' => $sCode,
			);        
			
			echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
		}
 	}
  
    function isAllowedPurchaseFeatured ($iEntryId, $isPerformAction = false) {
 		
		if ($this->isAdmin())
            return false;

		//if(getParam("bx_events_buy_featured")!='on')
		$iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getEntryById ($iEntryId);

		if(getParam("bx_events_featured_credits")!='on' && getParam("bx_events_featured_points")!='on')
			return false;
  
		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;
 
        if (!( ($GLOBALS['logged']['admin']||$GLOBALS['logged']['member']) && $aDataEntry[$this->_oDb->_sFieldAuthorId] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 

    function isAllowedActivate (&$aEvent, $isPerformAction = false)
    {
        if ($aEvent['Status'] != 'pending')
            return false;
        if ($this->isAdmin())
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_EVENTS_APPROVE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }


	//logo mod
    function processLogo($iEntryId) {
 	    $iEntryId  = (int)$iEntryId;

 		$sIcon = $this->_actionUploadIcon($iEntryId);

		if($sIcon){	
			$this->_oDb->updatePostWithLogo($iEntryId, $sIcon);  
		}
	}

    function _actionUploadIcon ($iEntryId=0 ) {
		$iEntryId  = (int)$iEntryId;

		$iIconWidth = (int)getParam("bx_events_icon_width");
		$iIconHeight = (int)getParam("bx_events_icon_height"); 
		$sIcon = "";
	  
		$sFile = "iconfile";
 		$sPath = $this->_oConfig->getMediaPath();	
		if ( 0 < $_FILES[$sFile]['size'] && 0 < strlen( $_FILES[$sFile]['name'] ) ) {
			$sFileName = time();
			$sExt = $this->moveUploadedImage( $_FILES, $sFile, $sPath . $sFileName, '', false );
			if( strlen( $sExt ) && !(int)$sExt ) {
			 
				if($iEntryId)
					$this->_oDb->_actionRemoveIcon($iEntryId);
 
				$sFullPath = $sPath.$sFileName.$sExt;
 
				imageResize( $sFullPath, $sFullPath, $iIconWidth, $iIconHeight);
				
				chmod( $sFullPath, 0644 );
				 
				if ($sExt != '')
					$sIcon = $sFileName.$sExt;
			} 
		}
 
		return $sIcon;
	}
 
	function actionLogo($sAction, $iEntryId){
	    $iEntryId  = (int)$iEntryId;

		if($sAction=='remove'){
			if ($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin())) {			
				$this->_oDb->_actionRemoveIcon($iEntryId); 

				$this->_oDb->updatePostWithLogo($iEntryId);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

				header ('Location:' . $sRedirectUrl);
			}else{
                $this->_oTemplate->displayPageNotFound ();
                return; 
			}
		}else{
			$this->_oTemplate->displayPageNotFound ();
			return; 
		} 
	}

	function moveUploadedImage( $aFiles, $fname, $path_and_name, $maxsize='', $imResize='true' )
	{
		global $max_photo_height;
		global $max_photo_width;

		$height = $max_photo_height;
		if ( !$height )
			$height = 400;
		$width = $max_photo_width;
		if ( !$width )
			$width = 400;

		if ( $maxsize && ($aFiles[$fname]['size'] > $maxsize || $aFiles[$fname]['size'] == 0) ) {
			if ( file_exists($aFiles[$fname]['tmp_name']) ) {
				unlink($aFiles[$fname]['tmp_name']);
			}
			return false;
		} else {
			$scan = getimagesize($aFiles[$fname]['tmp_name']);

			if ( ($scan['mime'] == 'image/jpeg' && $ext = '.jpg' ) ||
				( $scan['mime'] == 'image/gif' && $ext = '.gif' ) ||
				( $scan['mime'] == 'image/png' && $ext = '.png' ) ) //deleted .bmp format
			{

				$path_and_name .= $ext;
				move_uploaded_file( $aFiles[$fname]['tmp_name'], $path_and_name );

				if ( $imResize )
					imageResize( $path_and_name, $path_and_name, $width, $height );

			} else {
				return IMAGE_ERROR_WRONG_TYPE;
			}
		}

		return $ext;
	}
	//end - logo modification

	//[begin modzzz] ban member modification  
    function actionBanMember ($iEntryId) {
 
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($iEntryId, 0, true))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }
 
		if (!$this->isEntryAdmin($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }
  
		switch (bx_get('ajax_action')) {

			case 'ban_member': 

				$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
	 
				$sMemberName = trim(bx_get('member'));  
				$iMemberId = ($sMemberName) ? getID($sMemberName) : 0;
 
				if( $iMemberId && $this->_oDb->banMember($iEntryId, $iMemberId, $this->_iProfileId) ){
					$sMsg =  MsgBox(_t('_bx_events_msg_ban_success'));

					echo $sMsg . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sUrl);

				}else{
					$sMsg = MsgBox(_t('_bx_events_msg_ban_fail'));

					echo $sMsg . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sUrl); 
				}
				exit; 
			break;  
		}
  
        $this->_oTemplate->pageStart();

        bx_import ('BanForm', $this->_aModule);
		$oForm = new BxEventsBanForm ($this, $iEntryId);
        $oForm->initChecker();
 
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sCode = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $oForm->getCode ()));
  
        $sForm = '<div id="event_member_content">' . $sCode . '</div>';
 
        $aVarsPopup = array (
            'title' => _t('_bx_events_popup_title_ban_member'),
            'content' => $sForm,
        );        
        
		echo $GLOBALS['oFunctions']->transBox($this->_oTemplate->parseHtmlByName('popup', $aVarsPopup), true); 
 	} 
	//[end modzzz] ban member modification
 
    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array())
    {
        if ('approved' == $sStatus) {
            $this->reparseTags ($iEntryId);
            $this->reparseCategories ($iEntryId);
        }

        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri(), $iEntryId));

        $this->_oDb->createForum ($aDataEntry, $this->_oDb->getProfileNickNameById($aDataEntry[$this->_oDb->_sFieldAuthorId]));
        $oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $aDataEntry[$this->_oDb->_sFieldAuthorId], array('Status' => $sStatus));
        $oAlert->alert();
    }

	function actionUpdate (){
		$this->_oDb->updateRecurring();
	}

    function serviceEventsRss()
    {
        $iPID = (int)bx_get('pid');
        $aRssUnits = $this->_oDb->getMemberPostsRSS($iPID);
        if (is_array($aRssUnits) && count($aRssUnits)>0) {

            foreach ($aRssUnits as $iUnitID => $aUnitInfo) {
                $sPostLink = '';
                $iPostID = (int)$aUnitInfo['UnitID'];

				$aDataEntry = $this->_oDb->getEntryById($iPostID);

				$sPostLink = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

                $aRssUnits[$iUnitID]['UnitLink'] = $sPostLink;
  
				if ($aUnitInfo['UnitIcon']) {
					$a = array ('ID' => $iPID, 'Avatar' => $aUnitInfo['UnitIcon']);
					$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
					$aRssUnits[$iUnitID]['UnitIcon'] = $aImage['no_image'] ? '' : $aImage['file'];
				} 
            }

            $sUnitTitleC = _t('_sys_module_events');
            $sMainLink = 'rss_factory.php?action=events&amp;pid=' . $iPID;

            bx_import('BxDolRssFactory');
            $oRssFactory = new BxDolRssFactory();

            header('Content-Type: text/xml; charset=utf-8');
            echo $oRssFactory->GenRssByData($aRssUnits, $sUnitTitleC, $sMainLink);exit;
        }
    }


}
