<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Professional
*     website              : http://www.boonex.co
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

function modzzz_professional_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'professional') {
        $oMain = BxDolModule::getInstance('BxProfessionalModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
bx_import('BxDolCategories');


define ('BX_PROFESSIONAL_PHOTOS_CAT', 'Professional');
define ('BX_PROFESSIONAL_PHOTOS_TAG', 'professional');

define ('BX_PROFESSIONAL_VIDEOS_CAT', 'Professional');
define ('BX_PROFESSIONAL_VIDEOS_TAG', 'professional');

define ('BX_PROFESSIONAL_SOUNDS_CAT', 'Professional');
define ('BX_PROFESSIONAL_SOUNDS_TAG', 'professional');

define ('BX_PROFESSIONAL_FILES_CAT', 'Professional');
define ('BX_PROFESSIONAL_FILES_TAG', 'professional');

define ('BX_PROFESSIONAL_MAX_FANS', 1000);
  
 
/*
 * Professional module
 *
 * This module allow users to create user's professional, 
 * users can rate, comment and discuss professional.
 * Professional can have photos, videos, sounds and files, uploaded
 * by professional's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add professional' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new professional was created
 * change - professional was chaned
 * rate - somebody rated professional
 * commentPost - somebody posted comment in professional
 *
 *
 *
 * Memberships/ACL:
 * professional view professional - BX_PROFESSIONAL_VIEW_PROFESSIONAL
 * professional browse - BX_PROFESSIONAL_BROWSE
 * professional search - BX_PROFESSIONAL_SEARCH
 * professional add professional - BX_PROFESSIONAL_ADD_PROFESSIONAL
 * professional comments delete and edit - BX_PROFESSIONAL_COMMENTS_DELETE_AND_EDIT
 * professional edit any professional - BX_PROFESSIONAL_EDIT_ANY_PROFESSIONAL
 * professional delete any professional - BX_PROFESSIONAL_DELETE_ANY_PROFESSIONAL
 * professional mark as featured - BX_PROFESSIONAL_MARK_AS_FEATURED
 * professional approve professional - BX_PROFESSIONAL_APPROVE_PROFESSIONAL
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different professional
 * @see BxProfessionalModule::serviceHomepageBlock
 * BxDolService::call('professional', 'homepage_block', array());
 *
 * Profile block with user's professional
 * @see BxProfessionalModule::serviceProfileBlock
 * BxDolService::call('professional', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for professional (for internal usage only)
 * @see BxProfessionalModule::serviceGetMemberMenuItem
 * BxDolService::call('professional', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_professional'
 * The following alerts are rised
 *
 
 *
 *  add - new professional was added
 *      $iObjectId - professional id
 *      $iSenderId - creator of a professional
 *      $aExtras['Status'] - status of added professional
 *
 *  change - professional's info was changed
 *      $iObjectId - professional id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed professional
 *
 *  delete - professional was deleted
 *      $iObjectId - professional id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - professional was marked/unmarked as featured
 *      $iObjectId - professional id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if professional was marked as featured and 0 - if professional was removed from featured 
 *
 */
class BxProfessionalModule extends BxDolTwigModule {

    var $_oPrivacy; 
	var $_aQuickCache = array ();

    function BxProfessionalModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_professional';

        bx_import ('Privacy', $aModule);
        bx_import ('SubPrivacy', $aModule);
        $this->_oPrivacy = new BxProfessionalPrivacy($this);
 
	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxProfessionalModule'] = &$this;

		//reloads subcategories on Add form
		if($_GET['ajax']=='cat') { 
			$iParentId = $_GET['parent'];
			echo $this->_oDb->getAjaxCategoryOptions($iParentId);
			exit;
		}  

		if($_GET['ajax']=='state') { 
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
        parent::_actionHome(_t('_modzzz_professional_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_professional_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_professional_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_professional_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_professional_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_professional_page_title_comments'));
    }
  
	function actionView ($sUri) {
        $this->_actionView ($sUri, _t('_modzzz_professional_msg_pending_approval'));
    }
 
    function _actionView ($sUri, $sMsgPendingApproval) {

        if (!($aDataEntry = $this->_preProductTabs($sUri)))
            return;

        $this->_oTemplate->pageStart();

        bx_import ('PageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageView';
        $oPage = new $sClass ($this, $aDataEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();
   
        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aDataEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        $this->_oTemplate->addPageKeywords ($aDataEntry[$this->_oDb->_sFieldTags]);

	    //[begin] seo modification 
		$sTitle = $aDataEntry[$this->_oDb->_sFieldTitle];
		$sLocation = $this->_formatLocation($aDataEntry);
		$sSiteTitle = isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');

        $this->_oTemplate->setPageTitle ($sTitle . ' | '. $sLocation . ' | '. $sSiteTitle);
 
		$sDescription = $sTitle .' '. _t('_modzzz_location_located') . ' @ '. $sLocation; 

		$aImages = array();
		$aPhotos = $this->_oDb->getAllPhotos($aDataEntry[$this->_oDb->_sFieldId]);
		foreach($aPhotos as $aEachPhoto){
 
			$a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aEachPhoto['thumb']);
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
			'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
			'title' => $sTitle,
			'description' => $sDescription,
			'bx_repeat:images' => $aImages,	
			'street' => $aDataEntry['address1'],
			'city' => $aDataEntry['city'],
			'state' => $aDataEntry['state'],
			'zip' => $aDataEntry['zip'],
			'country' => $aDataEntry['country'],
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
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_professional_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_professional_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_professional_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_professional_page_title_upload_files')); 
    }
  
    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_professional_page_title_calendar'));
    }

    function actionSearch ($sKeyword = '', $sParent = '', $sCategory = '', $sCity = '', $sState = '', $sCountry = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['Category'] = $sCategory;
        if ($sParent)
            $_GET['Parent'] = $sParent; 
		if ($sCity) 
            $_GET['City'] = $sCity;
		if ($sState) 
            $_GET['State'] = $sState; 
         if ($sCountry)
            $_GET['Country'] = $sCountry;
  
        if ($sCountry || $sCategory || $sParent || $sKeyword || $sState || $sCity ) {
            $_GET['submit_form'] = 1;  
        }
        
        modzzz_professional_import ('FormSearch');
        $oForm = new BxProfessionalFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_professional_import ('SearchResult');
            $o = new BxProfessionalSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Parent'),$oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );

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

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t('_modzzz_professional_caption_search'));
    } 
 
    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_professional_page_title_add'));
    }
  
    function _addForm ($sRedirectUrl) {
  
		$bPaidProfessional = $this->isAllowedPaidProfessionals (); 
		if( $bPaidProfessional && (!isset($_POST['submit_form'])) ){
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
                    'caption' => _t('_modzzz_professional_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),	
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_professional_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_professional_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_professional_continue'),
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

        if ($oForm->isSubmittedAndValid ()) {
 
 			$bPaidProfessional = $this->isPaidPackage($oForm->getCleanValue('package_id')); 

			if($bPaidProfessional)
				$sStatus = 'pending';
			else
				$sStatus =  ((getParam($this->_sPrefix.'_autoapproval') == 'on') || $this->isAdmin()) ? 'approved' : 'pending';

            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId
            );                        
    
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
				$oForm->processAddMedia($iEntryId, $this->_iProfileId);
 
 				$this->_oDb->addYoutube($iEntryId);

 				$this->_oDb->addSchedule($iEntryId);

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
				
				if($this->isAllowedPaidProfessionals()){
 
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
					$iNumActiveDays = (int)getParam("modzzz_professional_free_expired");
					if($iNumActiveDays && (!$this->isAdmin()))
						$this->_oDb->updateEntryExpiration($iEntryId, $iNumActiveDays); 
 
					if (!$sRedirectUrl)  
						$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
				} 
				 
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_modzzz_professional_page_title_edit'));
    }

    function _actionEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
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
			$bPaidProfessional = $this->isPaidPackage($iPackageId);   
 			if($bPaidProfessional){ 
				$aValsAdd = array ();
			}else{ 
				$sStatus = $this->_oDb->getParam($this->_sPrefix . '_autoapproval') == 'on' || $this->isAdmin() ? 'approved' : 'pending';
				$aValsAdd = array ($this->_oDb->_sFieldStatus => $sStatus);
			}

            if ($oForm->update ($iEntryId, $aValsAdd)) {

                $oForm->processMedia($iEntryId, $this->_iProfileId);
 
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


				//[begin] add/manage schedule
				$aItems2Keep = array(); 
				if( is_array($_POST['prev_schedule']) && count($_POST['prev_schedule'])){ 
					foreach ($_POST['prev_schedule'] as $iScheduleId){
						$aItems2Keep[$iScheduleId] = $iScheduleId;
					}
				}
					
				$aScheduleIds = $this->_oDb->getScheduleIds($iEntryId);
			
				$aDeletedItem = array_diff ($aScheduleIds, $aItems2Keep);

				if ($aDeletedItem) {
					foreach ($aDeletedItem as $iItemId) {
						$this->_oDb->removeScheduleEntry($iEntryId, $iItemId);
					}
				} 
				  
				$this->_oDb->addSchedule($iEntryId);
				//[end] add/manage schedule
 
                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventChanged ($iEntryId, $sStatus);
 
				if($bPaidProfessional && $aDataEntry[$this->_oDb->_sFieldStatus]=='pending'){
  
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

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle);
    }
 
    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_professional_msg_professional_was_deleted'));
    }
  
    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_professional_msg_added_to_featured'), _t('_modzzz_professional_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_professional_caption_share_professional'));
    }
 
   function actionJoin ($iEntryId, $iProfileId) {

        parent::_actionJoin ($iEntryId, $iProfileId, _t('_modzzz_professional_msg_joined_already'), _t('_modzzz_professional_msg_joined_request_pending'), _t('_modzzz_professional_msg_join_success'), _t('_modzzz_professional_msg_join_success_pending'), _t('_modzzz_professional_msg_leave_success'));
    }    
 
    function actionTags() {
        parent::_actionTags (_t('_modzzz_professional_page_title_tags'));
    }    
 
    function actionPackages () { 
        $this->_oTemplate->pageStart();
        bx_import ('PagePackages', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PagePackages';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
        $this->_oTemplate->pageCode(_t('_modzzz_professional_page_title_packages'), false, false);
    }

    function actionCategories ($sUri='') { 

		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if ($sUri && !preg_match($sReg, $sUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}
 
        $this->_oTemplate->pageStart();
        bx_import ('PageCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_professional_page_title_categories');

		if($sUri){
			$aCategoryInfo = $this->_oDb->getCategoryInfoByUri($sUri); 
			$sCategoryName = $aCategoryInfo['name'];
			$sTitle .= ' - ' . $sCategoryName;
		}
		   
		$this->_oTemplate->pageCode($sTitle, false, false);
    }

    function actionSubCategories ($sUri='') {

		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if (!preg_match($sReg, $sUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}

        $this->_oTemplate->pageStart();
        bx_import ('PageSubCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageSubCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_professional_page_title_subcategories');

		if($sUri){
			$aCategoryInfo = $this->_oDb->getCategoryInfoByUri($sUri); 
			$sCategoryName = $aCategoryInfo['name'];
 			$sTitle .= ' - ' . $sCategoryName;
		}

        $this->_oTemplate->pageCode($sTitle, false, false);
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
 
    function actionInvite ($iEntryId) {
        $this->_actionInvite ($iEntryId, 'modzzz_professional_invitation', $this->_oDb->getParam('modzzz_professional_max_email_invitations'), _t('_modzzz_professional_invitation_sent'), _t('_modzzz_professional_no_users_msg'), _t('_modzzz_professional_caption_invite'));
    }

    function _actionInvite ($iEntryId, $sEmailTemplate, $iMaxEmailInvitations, $sMsgInvitationSent, $sMsgNoUsers, $sTitle) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));
 
        bx_import('BxDolTwigFormInviter');
        $oForm = new BxDolTwigFormInviter ($this, $sMsgNoUsers);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        

            $aInviter = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInviteParams ($aDataEntry, $aInviter);
            
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;

            // send invitation to registered members
            if (isset($_REQUEST['inviter_users']) && is_array($_REQUEST['inviter_users'])) {
                foreach ($_REQUEST['inviter_users'] as $iRecipient) {
                    $aRecipient = getProfileInfo($iRecipient);
                    $aPlus = array_merge (array ('NickName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);

					$sSubject = str_replace('<NickName>', $aPlus['NickName'], $aTemplate['Subject']);

                    $iSuccess += sendMail(trim($aRecipient['Email']), $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", $_REQUEST['inviter_emails']);
            $aPlus = array_merge (array ('NickName' => _t('_modzzz_professional_friend')), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;

					$sSubject = str_replace('<NickName>', _t('_modzzz_professional_friend'), $aTemplate['Subject']);

                    $iRet = sendMail(trim($sEmail), $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                    $iSuccess += $iRet;
                    if ($iRet && 0 == --$iMaxCount) 
                        break;
                }             
            }

            $sMsg = sprintf($sMsgInvitationSent, $iSuccess);
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sTitle, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
  
    function _getInviteParams ($aDataEntry, $aInviter) {

		$aLocation = array();
		$aLocation[] = _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']);
		 
		if(trim($aDataEntry['state']))
			$aLocation[] = $this->_oDb->getStateName($aDataEntry['country'], $aDataEntry['state']);
		 
		if(trim($aDataEntry['city']))
			$aLocation[] = $aDataEntry['city'];
 
		if(trim($aDataEntry['zip']))
			$aLocation[] = $aDataEntry['zip'];

        return array (
                'ProfessionalName' => $aDataEntry['title'],
                'ProfessionalLocation' => implode(', ', $aLocation),
                'ProfessionalUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_modzzz_professional_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_REQUEST['inviter_text'])),
            );        
    }
 
    function actionInquire ($iEntryId) {
        $this->_actionInquire ($iEntryId, 'modzzz_professional_inquiry', _t('_modzzz_professional_caption_make_inquiry'), _t('_modzzz_professional_inquiry_sent'), _t('_modzzz_professional_inquiry_not_sent'));
    }

    function _actionInquire ($iEntryId, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('InquireForm', $this->_aModule);
		$oForm = new BxProfessionalInquireForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aInquirer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInquireParams ($aDataEntry, $aInquirer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to professional owner
            if (isset($_REQUEST['inquire_text'])) { 
				 $aRecipient = getProfileInfo($iRecipient); 

				 $sContactEmail = trim($aDataEntry['selleremail']) ? trim($aDataEntry['selleremail']) : trim($aRecipient['Email']);
 
				 $sSubject = str_replace("<NickName>",$aInquirer['NickName'], $aTemplate['Subject']);
				 $sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

				 $aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);

                 $iSuccess = sendMail($sContactEmail, $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
			}
			
            $sMsg = ($iSuccess) ? $sMsgSuccess : $sMsgFail;
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle . ' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getInquireParams ($aDataEntry, $aInquirer) {
        return array (
                'ListTitle' => $aDataEntry['title'], 
                'ListUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aInquirer ? getProfileLink($aInquirer['ID']) : 'javascript:void(0);',
                'SenderName' => $aInquirer ? $aInquirer['NickName'] : _t('_modzzz_professional_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['inquire_text'])),
            );        
    }


	/*[begin] claim*/
    function actionClaim ($iEntryId) {
        $this->_actionClaim ($iEntryId, 'modzzz_professional_claim', _t('_modzzz_professional_caption_make_claim'), _t('_modzzz_professional_claim_sent'), _t('_modzzz_professional_claim_not_sent'));
    }

    function _actionClaim ($iEntryId, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('ClaimForm', $this->_aModule);
		$oForm = new BxProfessionalClaimForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aClaimer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getClaimParams ($aDataEntry, $aClaimer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
			$arrAdmins = $this->_oDb->saveClaimRequest($iEntryId, $this->_iProfileId,$_REQUEST['claim_text']);

            // send message to administrator
            if (isset($_REQUEST['claim_text'])) { 
				 
				$arrAdmins = $this->_oDb->getSiteAdmins();

				foreach($arrAdmins as $iRecipient) { 
					$aRecipient = getProfileInfo($iRecipient); 

					$sSubject = str_replace("<NickName>",$aClaimer['NickName'], $aTemplate['Subject']);
					$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

					$aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);

					$iSuccess += sendMail(trim($aRecipient['Email']), $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
				}
			}
			
            $sMsg = ($iSuccess) ? $sMsgSuccess : $sMsgFail;
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

    function _getClaimParams ($aDataEntry, $aClaimer) {
        return array (
                'ListTitle' => $aDataEntry['title'], 
                'ListUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aClaimer ? getProfileLink($aClaimer['ID']) : 'javascript:void(0);',
                'SenderName' => $aClaimer ? $aClaimer['NickName'] : _t('_modzzz_professional_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['claim_text'])),
            );        
    }
/*[end] claim*/



    // ================================== external actions

    /**
     * Homepage block with different professional
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent()){ 
			return '';
        } 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxProfessionalPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));
  
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_professional_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['filter']) {            
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case $sDefaultHomepageTab:            
                $sBrowseMode = $_GET['filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('modzzz_professional_perpage_homepage'), 
            array(
                _t('_modzzz_professional_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_professional_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_professional_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_professional_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's professional
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxProfessionalPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_professional_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

     /**
     * Account block with different events
     * @return html to display area professionals in account page a block
     */  
    function serviceAccountAreaBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sValue = ($aProfileInfo['zip']) ? $aProfileInfo['zip'] : $aProfileInfo['City'];
 
		if(!$sValue) return;
			 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxProfessionalPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('modzzz_professional_perpage_accountpage'),
			array(),
			$sValue
        );
    }

    /**
     * Account block with different events
     * @return html to display member professionals in account page a block
     */ 
    function serviceAccountPageBlock () {
  
        $aProfile = getProfileInfo($this->_iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxProfessionalPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . 'member.php?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_professional_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }


    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_professional'), _t('_modzzz_professional'), 'briefcase');
    }
 
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_professional_spy_post',
            'change' => '_modzzz_professional_spy_post_change', 
            'join' => '_modzzz_professional_spy_join',
            'work' => '_modzzz_professional_spy_work',
            'rate' => '_modzzz_professional_spy_rate',
            'commentPost' => '_modzzz_professional_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_professional_sbs_change'),
            'commentPost' => _t('_modzzz_professional_sbs_comment'),
            'rate' => _t('_modzzz_professional_sbs_rate'), 
            'join' => _t('_modzzz_professional_sbs_join'), 
            'work' => _t('_modzzz_professional_sbs_work'), 
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions

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
				$iPackage = 0; 
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
			'name' => _t('_modzzz_professional_packages'),
			'bx_repeat:items' => $aPackage,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages/'
		));


		$aPackage = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "packages` WHERE  `id` = '$iPackage'");
		  
		$sFormName = 'packages_form';
  
	    if($iPackage){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_professional_categ_btn_edit'),
				'action_delete' => _t('_modzzz_professional_categ_btn_delete'), 
				'action_add' => _t('_modzzz_professional_categ_btn_add')  
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_professional_categ_btn_save')
			), 'pathes', false);	 
	    }
 
		$aVars = array(
 			'id'=> $aPackage['id'],  
			'name' => $aPackage['name'], 
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
 				'action_add' => _t('_modzzz_professional_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_packages',$aVars);
		}

		return $sContent;
	}
 
    function actionAdministration ($sUrl = '',$sParam1='',$sParam2='') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
  
        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_professional_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_professional_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),   
           'categories' => array(
                'title' => _t('_modzzz_professional_menu_admin_manage_categories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories',
                '_func' => array ('name' => 'actionAdministrationCategories', 'params' => array($sParam1)),
            ),
            'subcategories' => array(
                'title' => _t('_modzzz_professional_menu_admin_manage_subcategories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories',
                '_func' => array ('name' => 'actionAdministrationSubCategories', 'params' => array($sParam1,$sParam2)),
            ), 
			'invoices' => array(
                'title' => _t('_modzzz_professional_menu_manage_invoices'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/invoices',
                '_func' => array ('name' => 'actionAdministrationInvoices', 'params' => array($sParam1)),
            ), 			
			'orders' => array(
                'title' => _t('_modzzz_professional_menu_manage_orders'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/orders',
                '_func' => array ('name' => 'actionAdministrationOrders', 'params' => array($sParam1)),
            ),
			'packages' => array(
                'title' => _t('_modzzz_professional_menu_manage_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages',
                '_func' => array ('name' => 'actionAdministrationPackages', 'params' => array($sParam1)),
            ),  
			'claims' => array(
                'title' => _t('_modzzz_professional_menu_manage_claims'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/claims',
                '_func' => array ('name' => 'actionAdministrationClaims', 'params' => array($sParam1)),
            ), 
            'create' => array(
                'title' => _t('_modzzz_professional_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_professional_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_professional_admin_block_administration'), $aMenu);
  
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_professional_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Professional');
    }
 
    function actionAdministrationCategories ($sParam1='') {
 		$sMessage = "";
  		$iCategory = (int)process_db_input($sParam1);
 
		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
 				$this->_oDb->SaveCategory();
				$sMessage = _t("Successfully Saved Category");
 			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{   
 				$this->_oDb->UpdateCategory();
				$sMessage = _t("Successfully Updated Category");
  			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 				$this->_oDb->DeleteCategory();
				$sMessage = _t("Successfully Removed Category");
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iCategory = 0;  
			} 
			if(isset($_POST['action_sub']) && !empty($_POST['action_sub']))
			{  
				$sRedirUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories/'.$iCategory;
				
				header("Location: " . $sRedirUrl);
			} 		

		}
 
		$aCategories = $this->_oDb->getParentCategories();
		$aCategory[] = array(
			'value' => '',
			'caption' => ''  
		);
		foreach ($aCategories as $oCategory)
		{
			$sKey = $oCategory['id'];
			$sValue = $oCategory['name'];
   
			$aCategory[] = array(
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $iCategory) ? 'selected="selected"' : ''
			);
		}
		
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => _t('_modzzz_professional_categories'),
			'bx_repeat:items' => $aCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories/'
		));


		$aCategory = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "categ` WHERE  `id` = '$iCategory'");
		 
		$sFormName = 'categories_form';
  
	    if($iCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_professional_categ_btn_edit'),
				'action_delete' => _t('_modzzz_professional_categ_btn_delete'), 
				'action_add' => _t('_modzzz_professional_categ_btn_add'),
				'action_sub' => _t('_modzzz_professional_categ_btn_subcategories'), 
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_professional_categ_btn_save')
			), 'pathes', false);	 
	    }
  
		$aVars = array(
			'name' => $aCategory['name'],
			'id'=> $aCategory['id'],  
 			'form_name' => $sFormName, 
			'controls' => $sControls,   
		);

		if($sMessage){
 			$sContent .= MsgBox($sMessage) ;
			$sContent .= "<form method=post>";
			$sContent .= BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
 				'action_add' => _t('_modzzz_professional_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_categories',$aVars);
		}

		return $sContent;
	}

    function actionAdministrationSubCategories ($sParam1='', $sParam2='') {
 		$sMessage = "";
  		$iCategory = (int)process_db_input($sParam1);
   		$iSubCategory = (int)process_db_input($sParam2);
		$sCategoryName = $this->_oDb->getCategoryName($iCategory);
		
		if(!$iCategory){
			$sContent = MsgBox(_t('_modzzz_professional_manage_subcategories_msg')); 

			return $sContent; 
		}

		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_save']) && !empty($_POST['action_save']))
			{  
 				$this->_oDb->SaveCategory($iCategory);
				$sMessage = _t("Successfully Saved Category");
 			} 
			if(isset($_POST['action_edit']) && !empty($_POST['action_edit']))
			{   
 				$this->_oDb->UpdateCategory();
				$sMessage = _t("Successfully Updated Category");
  			} 
			if(isset($_POST['action_delete']) && !empty($_POST['action_delete']))
			{  
 				$this->_oDb->DeleteCategory();
				$sMessage = _t("Successfully Removed Category");
			} 
			if(isset($_POST['action_add']) && !empty($_POST['action_add']))
			{  
				$iSubCategory = 0;  
			}  
		}
 
		$aSubCategories = $this->_oDb->getSubCategories($iCategory);

		$aSubCategory[] = array(
			'value' => '',
			'caption' => '--'._t('_Select').'--',
			'selected' =>  ''
		);

		foreach ($aSubCategories as $oSubCategory)
		{
			$sKey = $oSubCategory['id'];
			$sValue = $oSubCategory['name'];
   
			$aSubCategory[] = array(
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $iSubCategory) ? 'selected="selected"' : ''
			);
		}
		
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => $sCategoryName .': '. _t('_modzzz_professional_subcategories'),
			'bx_repeat:items' => $aSubCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories/'.$iCategory.'/'
		));


		$aCategory = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "categ` WHERE  `id` = '$iSubCategory'");
		 
		$sFormName = 'categories_form';
 
	    if($iSubCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_professional_categ_btn_edit'),
				'action_delete' => _t('_modzzz_professional_categ_btn_delete'), 
				'action_add' => _t('_modzzz_professional_categ_btn_add'),
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_professional_categ_btn_save')
			), 'pathes', false);	 
	    }
  
		$aVars = array(
			'name' => $aCategory['name'],
			'id'=> $aCategory['id'],  
 			'form_name' => $sFormName, 
			'controls' => $sControls,   
		);

		if($sMessage){
 			$sContent .= MsgBox($sMessage) ;
			$sContent .= "<form method=post>";
			$sContent .= BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
 				'action_add' => _t('_modzzz_professional_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_categories',$aVars);
		}

		return $sContent;
	} 
  
    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_professional_admin_delete', '_modzzz_professional_admin_activate');
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
			'action_activate' => '_modzzz_professional_admin_activate',
			'action_delete' => '_modzzz_professional_admin_delete',
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
 			'action_delete' => '_modzzz_professional_admin_delete',
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
 
	function actionAdministrationClaims () {

        if ($_POST['action_assign'] && is_array($_POST['entry'])) {
 
            foreach ($_POST['entry'] as $iId) {  
                $this->_oDb->assignClaim($iId, $this->isAdmin()); 
            } 
        }

        if ($_POST['action_delete'] && is_array($_POST['entry'])) {
  
            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteClaim($iId, $this->isAdmin()); 
            }
        }
 
		$sContent = $this->_manageClaims ('claim', '', true, 'bx_twig_admin_form', array(
 			'action_assign' => '_modzzz_professional_admin_assign',
 			'action_delete' => '_modzzz_professional_admin_delete',
		));
     
        return $sContent;
    }

    function _manageClaims ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 14, $bActionsPanel = true) {

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = $sMode . '_admin';
 

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayClaimResultBlock($sMode))) { 
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
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }


    // ================================== events
 

 
    // ================================== permissions
    
	function isPaidProfessional($iEntryId){
		$iEntryId = (int)$iEntryId;
         
		if (getParam('modzzz_professional_paid_active')!='on') 
            return false;	
 
		$aEntry = $this->_oDb->getEntryById($iEntryId);
		$sInvoiceNo = $aEntry['invoice_no'];
 
 		$aInvoice = $this->_oDb->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

		return $this->_oDb->isPaidPackage($iPackageId);  
	}
 
 	function isPaidPackage($iPackageId){
 
		if(!$this->isAllowedPaidProfessionals())
			return false;

		return $this->_oDb->isPaidPackage($iPackageId);  
	}
 
    function isAllowedPaidProfessionals ($bCheckAdmin=true) {
  
		 if($bCheckAdmin && $this->isAdmin())
			return false;

        if (getParam('modzzz_professional_paid_active')=='on') 
            return true;	
            
		return false;
	}

    function actionBrowseFans ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('modzzz_professional_perpage_browse_fans'), 'browse_fans/', _t('_modzzz_professional_page_title_fans'));
    }
 
    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_modzzz_professional_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_PROFESSIONAL_MAX_FANS);
    }
 
    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_professional_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_professional_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_professional_admin_become_fan');
    }

	function isAllowedViewFans(&$aDataEntry) {
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('view_fans', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedManageFans($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }
 
    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_professional_join_request', BX_PROFESSIONAL_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_professional_join_reject');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_professional_join_confirm');
    }

    function isAllowedJoin (&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;
        return $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_VIEW_PROFESSIONAL, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
        // check user group  
        $isAllowed =  $this->_oPrivacy->check('view_professional', $aDataEntry['id'], $this->_iProfileId);   
		return $isAllowed && $this->_isAllowedViewByMembership ($aDataEntry); 

    }

    function _isAllowedViewByMembership (&$aDataEntry) { 
        if (!$aDataEntry['membership_view_filter']) return true;
        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMembershipInfo = getMemberMembershipInfo($this->_iProfileId);
 
		if($aMembershipInfo['DateExpires']) 
			return $aDataEntry['membership_view_filter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() && $aMembershipInfo['DateExpires'] > time() ? true : false;
		else
			return $aDataEntry['membership_view_filter'] == $aMembershipInfo['ID'] && $aMembershipInfo['DateStarts'] < time() ? true : false; 
    }
  
    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_ADD_PROFESSIONAL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_EDIT_ANY_PROFESSIONAL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 
  
    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
   
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_DELETE_ANY_PROFESSIONAL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     
  
    function isAllowedInquire (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_MAKE_INQUIRY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 
	
    function isAllowedClaim (&$aDataEntry, $isPerformAction = false) {
		if ($this->isEntryAdmin($aDataEntry))
            return false;
  
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_MAKE_CLAIM, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function isAllowedSendInvitation (&$aDataEntry) {
        return true;
    }
 
    function isAllowedShare (&$aDataEntry) {
        return true;
    }
 

    function isAllowedRate(&$aDataEntry) {        
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedComments(&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        return $this->_oPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function isAllowedUploadPhotos(&$aDataEntry) {
       
        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;
  
		if (!$this->_iProfileId) 
            return false;  
	 
        if ($this->isAdmin())
            return true; 
		  
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedPhotos($aDataEntry['invoice_no']))
            return false;    

        if (!$this->isMembershipEnabledForImages())
            return false;
 
        return $this->_oPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;
 
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;

		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedVideos($aDataEntry['invoice_no']))
            return false;  
        if (!$this->isMembershipEnabledForVideos())
            return false;                
        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedSounds($aDataEntry['invoice_no']))
            return false;    
        if (!$this->isMembershipEnabledForSounds())
            return false;                        
        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if ($aDataEntry['invoice_no'] && !$this->_oDb->isPackageAllowedFiles($aDataEntry['invoice_no']))
            return false;    
        if (!$this->isMembershipEnabledForFiles())
            return false;                        
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }
	
    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) return true;        
        if (!$GLOBALS['logged']['member'] || $aDataEntry['author_id'] != $this->_iProfileId)
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }
 
    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_professional') == 'on'));
		 
        return $bEnabled;
    }
 
    function _defineActions () {
        defineMembershipActions(array('professional post reviews','professional mark as favorite', 'professional purchase', 'professional relist', 'professional extend','professional purchase featured', 'professional view professional', 'professional browse', 'professional search', 'professional add professional', 'professional comments delete and edit', 'professional edit any professional', 'professional delete any professional', 'professional mark as featured', 'professional approve professional', 'professional make claim', 'professional make inquiry', 'professional broadcast message', 'professional make booking'));
    }
 
    function _browseMy (&$aProfile) {        
        $this->_browseMyModified ($aProfile, _t('_modzzz_professional_page_title_my_professional'));
    } 
	
    function _browseMyModified (&$aProfile, $sTitle) {

        // check access
        if (!$this->_iProfileId) {
            $this->_oTemplate->displayAccessDenied();
            return;
        } 

        $bAjaxMode = isset($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? true : false;

        // process delete action 
        if (bx_get('action_delete') && is_array(bx_get('entry'))) {
			$aEntries = bx_get('entry');
            foreach ($aEntries as $iEntryId) {
                $iEntryId = (int)$iEntryId;
                $aDataEntry = $this->_oDb->getEntryById($iEntryId);
                if (!$this->isAllowedDelete($aDataEntry)) 
                    continue;
                if ($this->_oDb->deleteEntryByIdAndOwner($iEntryId, $this->_iProfileId, 0)) {
                    $this->onEventDeleted ($iEntryId);
                }
            }
        }

        bx_import ('PageMy', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageMy';
        $oPage = new $sClass ($this, $aProfile);

        // manage my data entries
        if ($bAjaxMode && ($_sPrefix . '_my_active') == bx_get('block')) {
            echo $oPage->getBlockCode_My();
            exit;
        }

        // manage my pending data entries 
        if ($bAjaxMode && ($_sPrefix . '_my_pending') == bx_get('block')) {
            echo $oPage->getBlockCode_Pending();
            exit;
        }

	   // manage my expired data entries 
		if ($bAjaxMode && ($_sPrefix . '_my_expired') == bx_get('block')) {
            echo $oPage->getBlockCode_Expired();
            exit;
        }

        $this->_oTemplate->pageStart();

        // display whole page
        if (!$bAjaxMode)
            echo $oPage->getCode();

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('form.css');
        $this->_oTemplate->addCss ('admin.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode($sTitle, false, false);
    } 

    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array()) {
   
		if ('approved' == $sStatus) {
            $this->reparseTags ($iEntryId);
            //$this->reparseCategories ($iEntryId);
        }
		
		//7.1
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri(), $iEntryId));


        $this->_oDb->createForum ($aDataEntry, $this->_oDb->getProfileNickNameById($this->_iProfileId));

 		$oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
    }

    function onEventChanged ($iEntryId, $sStatus) {

        $this->reparseTags ($iEntryId);
        //$this->reparseCategories ($iEntryId);
  
        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_change', array($this->_oConfig->getUri(), $iEntryId));

		$oAlert = new BxDolAlerts($this->_sPrefix, 'change', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
    } 

    function getTagLinks($sTagList, $sType = 'tag', $sDivider = ' ') {
        if (strlen($sTagList)) {
            $aTags = explode($sDivider, $sTagList);
            foreach ($aTags as $iKey => $sValue) {
                $sValue   = trim($sValue, ','); 
                $aRes[$sValue] = $sValue;
            }
        }
        return $aRes;
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

 
 
 
 
    // ================================== permissions  

    
  
    function isAdmin () {
        return $GLOBALS['logged']['admin'] || $GLOBALS['logged']['moderator'];
    }             

    // ================================== other 
 
    function actionPaypalProcess($iProfileId, $iProfessionalId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
 
			$aDataEntry = $this->_oDb->getEntryById ($iProfessionalId);
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPostPurchase(_t('_modzzz_professional_purchase_failed')); 
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
  
			//if (($aData['receiver_email'] != trim(getParam('modzzz_professional_paypal_email'))) || ($aData['txn_type'] != 'web_accept')) {

			if($aData['txn_type'] != 'web_accept') {
				$this->actionPostPurchase(_t('_modzzz_professional_purchase_failed'));
			}else{ 
				if($this->_oDb->isExistPaypalTransaction($iProfileId, $aData['txn_id'])) { 
					$this -> actionPostPurchase(_t('_modzzz_professional_transaction_completed_already', $sRedirectUrl)); 
				} else {
					if( $this->_oDb->saveTransactionRecord($iProfileId, $iProfessionalId, $aData['txn_id'], 'Paypal Purchase')) { 
						
						$this->_oDb->setItemStatus($iProfessionalId, 'approved'); 
						
						$this->_oDb->setInvoiceStatus($iProfessionalId, 'paid');

						$this->actionPostPurchase(_t('_modzzz_professional_purchase_success', $sRedirectUrl));
					} else {
						$this -> actionPostPurchase(_t('_modzzz_professional_trans_save_failed'));
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

	function initializeCheckout($iProfessionalId, $fTotalCost, $iQuantity=1, $bFeatured=0) {
  
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	 
		if($bFeatured){
			$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iProfessionalId;
			$sItemDesc = getParam('modzzz_professional_featured_purchase_desc');
 		}else{
			$sNotifyUrl = $this->_oConfig->getPurchaseCallbackUrl() . $this->_iProfileId .'/'. $iProfessionalId;
			$sItemDesc = getParam('modzzz_professional_paypal_item_desc');
		}

		$aDataEntry = $this->_oDb->getEntryById($iProfessionalId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $aFormData = array_merge($aFormData, array(
			'business' => getParam('modzzz_professional_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iProfessionalId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl(),
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Business Professional");
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
        $this->_oTemplate->pageCode(_t('_modzzz_professional_post_purchase_header')); 
    }
 
    function actionPurchaseFeatured($iProfessionalId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('modzzz_professional_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iProfessionalId);
		$sTitle = $aDataEntry['title'];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iProfessionalId,
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
					'caption'  => _t('_modzzz_professional_form_caption_title'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_modzzz_professional_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_modzzz_professional_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_modzzz_professional_featured_until') . ' '. $this->_oTemplate->filterCustomDate($aDataEntry['featured_expiry_date']) : _t('_modzzz_professional_not_featured'), 
                ), 
                'quantity' => array(
                    'caption'  => _t('_modzzz_professional_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_professional_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_modzzz_professional_extend_featured') : _t('_modzzz_professional_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 

			$fCost =  number_format($iPerDayCost, 2); 
  
			$this->initializeCheckout($iProfessionalId, $fCost, $oForm->getCleanValue('quantity'), true);  
			return;   
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_professional_purchase_featured')); 
    }
 
    function actionPaypalFeaturedProcess($iProfileId, $iProfessionalId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iProfessionalId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_modzzz_professional_purchase_failed')); 
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
				$this->actionPurchaseFeatured($iProfessionalId, _t('_modzzz_professional_purchase_failed'));
			}else { 
				$fAmount = $this->_getReceivedAmount($aData);
			
				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iProfessionalId, _t('_modzzz_professional_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iProfessionalId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iProfessionalId, $iQuantity); 
			   
						$this->actionPurchaseFeatured($iProfessionalId, _t('_modzzz_professional_purchase_success',  $iQuantity));
					} else {
						$this -> actionPurchaseFeatured($iProfessionalId, _t('_modzzz_professional_purchase_fail'));
					}
				}
			}
            
        }
    }
 
    function isAllowedPurchaseFeatured ($aDataEntry, $isPerformAction = false) {
  
		if(getParam("modzzz_professional_buy_featured")!='on')
			return false;
 
		if ($this->isAdmin())
            return false;

		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 

    function actionLocal ($sCountry='', $sState='', $sCategory='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState, $sCategory);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_professional_page_title_local');

		if($sCountry){
			$sTitle .= ' - ' . $this->_oTemplate->getPreListDisplay('Country', $sCountry);
		}
 
		if($sState){
			$sTitle .= ' - ' . $this->_oDb->getStateName($sCountry, $sState); 
		}

		if($sCategory){
			$sTitle .= ' - ' . $sCategory; 
		}
 
        $this->_oTemplate->pageCode($sTitle, false, false);
    } 
 
    function actionLocalCountry ($sCountry='', $sCategory='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, '', $sCategory);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_professional_page_title_local');

		if($sCountry){
			$sTitle .= ' - ' . $this->_oTemplate->getPreListDisplay('Country', $sCountry);
		}
  
		if($sCategory){
			$sTitle .= ' - ' . $sCategory; 
		}

        $this->_oTemplate->pageCode($sTitle, false, false);
    } 
 
    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_PROFESSIONAL_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_PROFESSIONAL_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_PROFESSIONAL_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_PROFESSIONAL_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'professional photos add', 'professional sounds add', 'professional videos add', 'professional files add'));
		if (!defined($sMembershipActionConstant))
			return false;
		$aCheck = checkAction($_COOKIE['memberID'], constant($sMembershipActionConstant));
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }  

     /**
     * forum permissions
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
  
        if (!($aDataEntry = $this->_oDb->getEntryById ($aForum['entry_id']))){
 			return $aFalse;
		}
 
        $aTrue = array (
            'admin' => $aDataEntry[$this->_oDb->_sFieldAuthorId] == $iMemberId || $this->isAdmin() ? 1 : 0, // author is admin
            'read' => $this->isAllowedView ($aDataEntry) ? 1 : 0,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );
  
        return $aTrue;
    }
 
     function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }
 
    function isEntryAdmin($aDataEntry, $iIdProfile = 0) {
        if (!$iIdProfile)
            $iIdProfile = $this->_iProfileId;
        return ($this->isAdmin() || $aDataEntry['author_id'] == $iIdProfile);
    }
 
	/*reprofessional and extension */
  
    function isAllowedPremium (&$aDataEntry, $isPerformAction = false) {
  
        if (getParam('modzzz_professional_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['status'] != 'approved')
            return false;

        if ($this->isAdmin() && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_PURCHASE, $isPerformAction);
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
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $this->_actionPremium($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_professional_page_title_premium'));
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
                    'caption' => _t('_modzzz_professional_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_professional_form_err_package'),
                    ),   
                ),   
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_professional_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_professional_continue'),
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
  
		if($aDataEntry['status'] != 'expired')
            return false;

		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_RELIST, $isPerformAction);
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
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $this->_actionRelist($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_professional_page_title_relist'));
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
                    'caption' => _t('_modzzz_professional_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_professional_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_professional_package_desc'),  
                ),  
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_professional_continue'),
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
        
        if (getParam('modzzz_professional_paid_active')!='on') 
            return false;	
		 
		if($aDataEntry['status'] != 'approved')
            return false;

		if(!$aDataEntry['expiry_date'])
            return false;
  
		if ($this->isAdmin())
            return true;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_EXTEND, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 


    function actionExtend ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        if (!$this->isPaidProfessional($iEntryId)) {
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
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);


        $this->_actionExtend($iEntryId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t('_modzzz_professional_page_title_extend'));
	}
 
    function _actionExtend ($iEntryId) {
   
		$aEntry = $this->_oDb->getEntryById($iEntryId);
 
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
                    'caption' => _t('_modzzz_professional_form_caption_current_item'),  
					'content'=> $aEntry['title'],
                 ), 

 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => $sPackageDesc,  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_professional_package_desc'),  
                ),   
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_professional_extend'),
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

	/***END Extend ***************/
 
	//[begin] [broadcast]
    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function actionBroadcast ($iEntryId) {
        $this->_actionBroadcast ($iEntryId, _t('_modzzz_professional_page_title_broadcast'), _t('_modzzz_professional_msg_broadcast_no_recipients'), _t('_modzzz_professional_msg_broadcast_message_sent'));
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
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

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
            $aTemplate = $oEmailTemplate->getTemplate($this->_sPrefix . '_broadcast'); 
            $aTemplateVars = array (
                'BroadcastTitle' => $this->_oDb->unescape($oForm->getCleanValue ('title')),
                'BroadcastMessage' => nl2br($this->_oDb->unescape($oForm->getCleanValue ('message'))),
                'EntryTitle' => $aDataEntry[$this->_oDb->_sFieldTitle],
                'EntryUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],                
            );
  
            $iSentMailsCounter = 0;            
            foreach ($aRecipients as $aProfile) {	
				$aTemplateVars['<NickName>'] = $aProfile['ID'];

       	        $iSentMailsCounter += sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars);

				$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  
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

		$sMessageBody = $aTemplate['Body'];
		$sMessageBody = str_replace("<NickName>", getNickName($aProfile['ID']), $sMessageBody);
		$sMessageBody = str_replace("<EntryUrl>", $aTemplateVars['EntryUrl'], $sMessageBody);
		$sMessageBody = str_replace("<EntryTitle>", $aTemplateVars['EntryTitle'], $sMessageBody);
		$sMessageBody = str_replace("<BroadcastMessage>", $aTemplateVars['BroadcastMessage'], $sMessageBody);

		$oMailBox -> iWaitMinutes = 0;//turn off anti-spam
		$oMailBox -> sendMessage($aTemplateVars['BroadcastTitle'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

    }
	//[begin] [broadcast]

	//[begin] favorites
    function isAllowedMarkAsFavorite ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_MARK_AS_FAVORITE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

	function isFavorite($iProfileId, $iEntryId){
		return $this->_oDb->isFavorite($iProfileId, $iEntryId);
	}
 
    function actionMarkFavorite ($iEntryId) {
        $this->_actionMarkFavorite ($iEntryId, _t('_modzzz_professional_msg_added_to_favorite'), _t('_modzzz_professional_msg_removed_from_favorite'));
    }

    function _actionMarkFavorite ($iEntryId, $sMsgSuccessAdd, $sMsgSuccessRemove) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedMarkAsFavorite($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->markAsFavorite($this->_iProfileId, $iEntryId)) {
            $this->isAllowedMarkAsFavorite($aDataEntry, true); // perform action
            $this->onEventMarkAsFavorite ($iEntryId, $aDataEntry);
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox($this->_oDb->isFavorite($this->_iProfileId, $iEntryId) ? $sMsgSuccessAdd : $sMsgSuccessRemove) . $sJQueryJS;
            exit;
        }        

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;        
    } 

    function onEventMarkAsFavorite ($iEntryId, $aDataEntry) {

        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'mark_as_favorite', $iEntryId, $this->_iProfileId, array('favorite' => $this->_oDb->isFavorite($this->_iProfileId, $iEntryId)));
		$oAlert->alert();
    }  
	//[end] favorites


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
 
        $iEntryId = (int)$aDataEntry['id'];

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		$iPackageId = $this->_oDb->getPackageIdByInvoiceNo($aDataEntry['invoice_no']); 
		$bPaidProfessional = $this->isPaidPackage($iPackageId);   
	 
		if($bPaidProfessional && $aDataEntry[$this->_oDb->_sFieldStatus]=='pending'){

			$aPackage = $this->_oDb->getPackageById($iPackageId);
			$fPrice = $aPackage['price'];
	 
			$this->initializeCheckout($iEntryId, $fPrice);  
			return;   
		}else{  
			header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		}   
		exit;  
    }


	/******[BEGIN] Review functions **************************/ 
     
	function actionReview ($sAction, $sIdUri ) {
 
		switch($sAction){
			case 'add': 
				$this->actionReviewAdd ($sIdUri, '_modzzz_professional_page_title_review_add');
			break;
			case 'edit':
				$this->actionReviewEdit ($sIdUri, '_modzzz_professional_page_title_review_edit');
			break;
			case 'delete':
				$this->actionReviewDelete ($sIdUri, _t('_modzzz_professional_msg_professional_review_was_deleted'));
			break;
			case 'view': 
				$this->actionReviewView ($sIdUri, _t('_modzzz_professional_msg_pending_review_approval')); 
			break; 
			case 'browse': 
				return $this->actionReviewBrowse ($sIdUri, '_modzzz_professional_page_title_review_browse'); 
			break;  
		}
	}
    
    function actionReviewBrowse ($sUri, $sTitle) {
       
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
		
		$this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_professional_menu_view_reviews') => '',
        ));
 
        bx_import ('ReviewPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']), false, false);  
    }
 
    function actionReviewView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aReviewEntry = $this->_oDb->getReviewEntryByUri($sUri);
		$iEntryId = (int)$aReviewEntry['professional_id'];
		$iServiceId = (int)$aReviewEntry['service_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aReviewEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
 
		if($iServiceId){
			$aServiceEntry = $this->_oDb->getServiceEntryById($iServiceId);
			$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
				_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
				$aServiceEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri], 
			));
		}else{
			$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
				_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
				$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri], 
			));
		}

        if ((!$this->_iProfileId || $aReviewEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableReview, $aReviewEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        } 

        $this->_oTemplate->pageStart();

        bx_import ('ReviewPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewPageView';
        $oPage = new $sClass ($this, $aReviewEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('ReviewCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aReviewEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        //$this->_oTemplate->addPageKeywords ($aReviewEntry[$this->_oDb->_sFieldTags]);

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');

		$sPageTitle = $aReviewEntry[$this->_oDb->_sFieldTitle] .' | '. $aDataEntry[$this->_oDb->_sFieldTitle];
 
        $this->_oTemplate->pageCode($sPageTitle, false, false); 
    }
 
    function actionReviewEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aReviewEntry = $this->_oDb->getReviewEntryById($iEntryId);
		$iReviewId = (int)$aReviewEntry['professional_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iReviewId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aReviewEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('ReviewFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewFormEdit';
        $oForm = new $sClass ($this, $aReviewEntry[$this->_oDb->_sFieldAuthorId], $iReviewId,  $iEntryId, $aReviewEntry);
  
        $oForm->initChecker($aReviewEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'review_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;


            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventSubProfileChanged ('review', $iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aReviewEntry[$this->_oDb->_sFieldUri]);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']));  
    }

    function actionReviewDelete ($iReviewId, $sMsgSuccess) {

		$aReviewEntry = $this->_oDb->getReviewEntryById($iReviewId);
		$iEntryId = (int)$aReviewEntry['professional_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aDataEntry, $aReviewEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteReviewByIdAndOwner($iReviewId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventReviewDeleted ($iReviewId, $aReviewEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iReviewId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionReviewAdd ($iReviewId, $sTitle) {
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iReviewId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	
 
        if (!$this->isAllowedPostReviews($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));

        $this->_addReviewForm($iReviewId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title']);  
    }
 
    function _addReviewForm ($iReviewId) { 
 
        bx_import ('ReviewFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iReviewId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'review_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getReviewEntryById($iEntryId);
                $this->onEventSubProfileCreate('review', $iEntryId, $sStatus, $aDataEntry);
   
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 
    function onEventReviewDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('ReviewVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewVoting';
        $oVoting = new $sClass ($this->_oDb->_sReviewPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('ReviewCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewCmts';
        $oCmts = new $sClass ($this->_oDb->_sReviewPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sReviewPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    function isAllowedPostReviews(&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;

        if ($this->isEntryAdmin($aDataEntry)) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_POST_REVIEWS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    }

    function actionBrowseReviews ($sUri) {
        $this->_actionBrowseReviews ($sUri, _t('_modzzz_professional_page_title_reviews'));
    }

    function _actionBrowseReviews ($sUri, $sTitle) {

		$iPerPage=$this->_oDb->getParam('modzzz_professional_perpage_browse_reviews');

        if (!($aDataEntry = $this->_preProductTabs($sUri, $sTitle))) { 
            return;
        }
 
        if (!$this->isAllowedViewReviews($aDataEntry)) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->getReviewsBrowse($aProfiles, $iStart, $iPerPage, $aDataEntry[$this->_oDb->_sFieldId]);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 
        $sRet = '';
        foreach ($aProfiles as $aProfile) {
            $sRet .= $this->_oTemplate->review_unit($aProfile);
        }
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse_reviews/' . $aDataEntry[$this->_oDb->_sFieldUri];
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => false,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        $sRet .= $oPaginate->getPaginate();

        $this->_oTemplate->pageStart();

        $this->_oTemplate->addCss('main.css');
        $this->_oTemplate->addCss('view.css');

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }

    function actionAllReviews () {

		$sTitle = _t('_modzzz_professional_page_title_reviews');

		$iPerPage=$this->_oDb->getParam('modzzz_professional_perpage_browse_reviews');
  
        if (!$this->isAllowedBrowse()) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->getReviewsBrowse($aProfiles, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 
        $sRet = '';
        foreach ($aProfiles as $aProfile) {
            $sRet .= $this->_oTemplate->review_unit($aProfile);
        }
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'all_reviews' ;
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => false,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        $sRet .= $oPaginate->getPaginate();

        $this->_oTemplate->pageStart();

        $this->_oTemplate->addCss('main.css');
         $this->_oTemplate->addCss('view.css');

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }

    /*******[END - Review Functions] ******************************/


	/******[BEGIN] Booking functions **************************/ 
     
	function actionBooking ($sAction, $sIdUri ) {
 
		switch($sAction){ 
			case 'add': 
				$this->actionBookingAdd ($sIdUri, '_modzzz_professional_page_title_booking_add');
			break;
			case 'edit':
				$this->actionBookingEdit ($sIdUri, '_modzzz_professional_page_title_booking_edit');
			break;
			case 'delete':
				$this->actionBookingDelete ($sIdUri, _t('_modzzz_professional_msg_professional_booking_was_deleted'));
			break;
			case 'view':
				$this->actionBookingView ($sIdUri, _t('_modzzz_professional_msg_pending_booking_approval')); 
			break;  
			case 'confirm':
				$this->actionBookingConfirm ($sIdUri); 
			break;   
		}
	}

    function actionBookingConfirm ($iEntryId)
    {
		$sMsgSuccess = _t('_modzzz_professional_msg_professional_booking_was_confirmed');
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getBookingEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedBookingManage($aDataEntry) || 0 !== strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->confirmBooking($iEntryId)) {

			$aProfessional = $this->_oDb->getEntryById($aDataEntry['professional_id']);

			$aService = $this->_oDb->getServiceEntryById($aDataEntry['service_id']);
 
			$this->_oDb->triggerAlert('modzzz_professional_booking_confirm', $this->_iProfileId, $aDataEntry['author_id'],  $aProfessional, $aService, $aDataEntry); 
 
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'booking/view/' . $aDataEntry['uri'];
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionBookingView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aBookingEntry = $this->_oDb->getBookingEntryByUri($sUri);
		$iEntryId = (int)$aBookingEntry['professional_id'];
   		$iServiceId = (int)$aBookingEntry['service_id'];
  
  		$aServiceEntry = $this->_oDb->getServiceEntryById($iServiceId);

        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aBookingEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aServiceEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri], 
        ));
 
        if (!isProfileActive($this->_iProfileId)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        } 

        if ( !($this->isAdmin() || ($aBookingEntry[$this->_oDb->_sFieldAuthorId] == $this->_iProfileId) || ($aServiceEntry[$this->_oDb->_sFieldAuthorId] == $this->_iProfileId))  ) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        } 

        $this->_oTemplate->pageStart();

        bx_import ('BookingPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BookingPageView';
        $oPage = new $sClass ($this, $aBookingEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('BookingCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BookingCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aBookingEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        //$this->_oTemplate->addPageKeywords ($aBookingEntry[$this->_oDb->_sFieldTags]);

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');

		$sPageTitle = $aBookingEntry[$this->_oDb->_sFieldTitle] .' | '. $aDataEntry[$this->_oDb->_sFieldTitle];
 
        $this->_oTemplate->pageCode($sPageTitle, false, false);

        //bx_import ('BxDolViews');
        //new BxDolViews($this->_oDb->_sBookingPrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }
 
    function actionBookingEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aBookingEntry = $this->_oDb->getBookingEntryById($iEntryId);
		$iProfessionalId = (int)$aBookingEntry['professional_id'];
		$iServiceId = (int)$aBookingEntry['service_id'];
  
  		$aServiceEntry = $this->_oDb->getServiceEntryById($iServiceId);

        if (!($aDataEntry = $this->_oDb->getEntryById($iProfessionalId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aBookingEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri], 
			_t($sTitle) => '' 
		));

        bx_import ('BookingFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BookingFormEdit';
        $oForm = new $sClass ($this, $aBookingEntry[$this->_oDb->_sFieldAuthorId], $iServiceId,  $iEntryId, $aBookingEntry);
  
        $oForm->initChecker($aBookingEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'booking_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBookingMediaPrefix;


            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
 				$this->_oDb->addYoutube($iEntryId, 'booking');
 
                $this->onEventSubProfileChanged ('booking', $iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'booking/view/' . $aBookingEntry[$this->_oDb->_sFieldUri]);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']));  
    }
 
    function actionBookingDelete ($iBookingId, $sMsgSuccess='') {
		$iBookingId = (int)$iBookingId;

        if (!($aBookingEntry = $this->_oDb->getBookingEntryById($iBookingId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iBookingId, 'ajaxy_popup_result_div');
            exit;
        }
		$iEntryId = (int)$aBookingEntry['professional_id'];
  		$iServiceId = (int)$aBookingEntry['service_id'];
 
        if (!($aServiceEntry = $this->_oDb->getServiceEntryById($iServiceId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iBookingId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if (!$this->isAllowedSubDelete($aServiceEntry, $aBookingEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iBookingId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteBookingByIdAndOwner($iBookingId, $this->_iProfileId, $this->isAdmin())) {
            $this->onEventBookingDeleted ($iBookingId, $aBookingEntry);
			
			$aProfessional = $this->_oDb->getEntryById($aBookingEntry['professional_id']);
			$aService = $this->_oDb->getServiceEntryById($aBookingEntry['service_id']);

			$this->_oDb->triggerAlert('modzzz_professional_booking_cancel', $this->_iProfileId, $aBookingEntry['author_id'],  $aProfessional, $aService, $aBookingEntry); 
  
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iBookingId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iBookingId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionBookingAdd ($sBookingUri, $sTitle) {

		if (!($aServiceEntry = $this->_oDb->getServiceEntryByUri($sBookingUri))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

		$aDataEntry = $this->_oDb->getEntryById($aServiceEntry['professional_id']);
 
        if (!$this->isAllowedBooking()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aServiceEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri], 
			_t($sTitle) => '' 
		));
  
        $this->_addBookingForm($aServiceEntry['id']);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aServiceEntry['title']);  
    }
 
    function _addBookingForm ($iServiceId) { 
 
        bx_import ('BookingFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BookingFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iServiceId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'booking_main';
  
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
  
                $aDataEntry = $this->_oDb->getBookingEntryById($iEntryId);

                $this->onEventSubProfileCreate('booking', $iEntryId, $sStatus, $aDataEntry);
    
                $aProfessional = $this->_oDb->getEntryById($aDataEntry['professional_id']);
                $aService = $this->_oDb->getServiceEntryById($aDataEntry['service_id']);
  
				$this->_oDb->triggerAlert('modzzz_professional_booking_notify', $this->_iProfileId, $aProfessional['author_id'],  $aProfessional, $aService, $aDataEntry); 
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'booking/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 
    function onEventBookingDeleted ($iEntryId, $aDataEntry = array()) {
  
        // delete comments 
        bx_import('BookingCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BookingCmts';
        $oCmts = new $sClass ($this->_oDb->_sBookingPrefix, $iEntryId);
        $oCmts->onObjectDelete ();
 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    function actionAllBookings () {

		$sTitle = _t('_modzzz_professional_page_title_bookings');

		$iPerPage=$this->_oDb->getParam('modzzz_professional_perpage_browse_bookings');
  
        if (!$this->isAllowedBrowse()) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->getBookingsBrowse($aProfiles, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 
        $sRet = '';
        foreach ($aProfiles as $aProfile) {
            $sRet .= $this->_oTemplate->booking_unit($aProfile);
        }
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'all_bookings' ;
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => false,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        $sRet .= $oPaginate->getPaginate();

        $this->_oTemplate->pageStart();

        $this->_oTemplate->addCss('main.css');
         $this->_oTemplate->addCss('view.css');

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }

    function isAllowedBookingCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) return true;        
        return ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId);
    }	
 
    function isAllowedBookingManage ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin()) 
            return true;
 
        $aServiceEntry = $this->_oDb->getServiceEntryById($aDataEntry['service_id']); 

        if ($aServiceEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true; 

		return false;
    }   
	
    function isAllowedBooking($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
  
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_MAKE_BOOKING, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 
    /*******[END - Booking Functions] ******************************/

	/******[BEGIN] Client functions **************************/ 
     
	function actionClient ($sAction, $sIdUri ) {
 
		switch($sAction){
			case 'embed': 
				$this->actionClientEmbed ($sIdUri);
			break;
			case 'add': 
				$this->actionClientAdd ($sIdUri, '_modzzz_professional_page_title_client_add');
			break;
			case 'edit':
				$this->actionClientEdit ($sIdUri, '_modzzz_professional_page_title_client_edit');
			break;
			case 'delete':
				$this->actionClientDelete ($sIdUri, _t('_modzzz_professional_msg_professional_client_was_deleted'));
			break;
			case 'view':
				$this->actionClientView ($sIdUri, _t('_modzzz_professional_msg_pending_client_approval')); 
			break; 
			case 'browse': 
				return $this->actionClientBrowse ($sIdUri, '_modzzz_professional_page_title_client_browse'); 
			break;  
		}
	}
    
    function actionClientEmbed ($sUri) {
 
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        if (!($aClientEntry = $this->_oDb->getClientEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

		$aDataEntry = $this->_oDb->getEntryById($aClientEntry['professional_id']);

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aClientEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('EmbedClientForm', $this->_aModule);
		$oForm = new BxProfessionalEmbedClientForm ($this, $aClientEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid()) {        
  
			$iEntryId = $aClientEntry[$this->_oDb->_sFieldId];
			
			$aYoutubes2Keep = array(); 
 			if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){
 
				foreach ($_POST['prev_video'] as $iYoutubeId){
					$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
				}
			}
				
			$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId, 'client');
		
			$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

			if ($aDeletedYoutube) {
				foreach ($aDeletedYoutube as $iYoutubeId) {
					$this->_oDb->removeYoutubeEntry($iYoutubeId);
				}
			} 
			 
			$this->_oDb->addYoutube($aClientEntry['professional_id'], $iEntryId,'client');
 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'client/view/' . $aClientEntry[$this->_oDb->_sFieldUri];
			header ('Location:' . $sRedirectUrl);
            return;
        } 

        echo $oForm->getCode (); 
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_professional_page_title_manage_youtube') .' - '. $aClientEntry[$this->_oDb->_sFieldTitle]);
    }
  
    function actionClientBrowse ($sUri, $sTitle) {
       
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
		
		$this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_professional_menu_view_clients') => '',
        ));
 
        bx_import ('ClientPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ClientPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']), false, false);  
    }
 
    function actionClientView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aClientEntry = $this->_oDb->getClientEntryByUri($sUri);
		$iEntryId = (int)$aClientEntry['professional_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aClientEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri], 
        ));
 
        if ((!$this->_iProfileId || $aClientEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableClient, $aClientEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        } 

        $this->_oTemplate->pageStart();

        bx_import ('ClientPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ClientPageView';
        $oPage = new $sClass ($this, $aClientEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('ClientCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ClientCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aClientEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        //$this->_oTemplate->addPageKeywords ($aClientEntry[$this->_oDb->_sFieldTags]);

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');

		$sPageTitle = $aClientEntry[$this->_oDb->_sFieldTitle] .' | '. $aDataEntry[$this->_oDb->_sFieldTitle];
 
        $this->_oTemplate->pageCode($sPageTitle, false, false);

        //bx_import ('BxDolViews');
        //new BxDolViews($this->_oDb->_sClientPrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }
 
    function actionClientEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aClientEntry = $this->_oDb->getClientEntryById($iEntryId);
		$iClientId = (int)$aClientEntry['professional_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iClientId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aClientEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        bx_import ('ClientFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ClientFormEdit';
        $oForm = new $sClass ($this, $aClientEntry[$this->_oDb->_sFieldAuthorId], $iClientId,  $iEntryId, $aClientEntry);
  
        $oForm->initChecker($aClientEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'client_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableClientMediaPrefix;


            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
				$aYoutubes2Keep = array(); 
				if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){
	 
					foreach ($_POST['prev_video'] as $iYoutubeId){
						$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
					}
				}
					
				$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId, 'client');
			
				$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

				if ($aDeletedYoutube) {
					foreach ($aDeletedYoutube as $iYoutubeId) {
						$this->_oDb->removeYoutubeEntry($iYoutubeId);
					}
				} 
	 
				$this->_oDb->addYoutube($aClientEntry['professional_id'], $iEntryId, 'client'); 



                $this->onEventSubProfileChanged ('client', $iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'client/view/' . $aClientEntry[$this->_oDb->_sFieldUri]);
                exit;

            } else {

                echo MsgBox(_t('_Error Occured'));
                
            }            

        } else {

            echo $oForm->getCode ();

        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']));  
    }

    function actionClientDelete ($iClientId, $sMsgSuccess) {

		$aClientEntry = $this->_oDb->getClientEntryById($iClientId);
		$iEntryId = (int)$aClientEntry['professional_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iClientId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aDataEntry, $aClientEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iClientId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteClientByIdAndOwner($iClientId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventClientDeleted ($iClientId, $aClientEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iClientId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iClientId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionClientAdd ($iClientId, $sTitle) {
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iClientId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	
 
        if (!$this->isAllowedPostClients($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
 
        $this->_addClientForm($iClientId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']));  
    }
 
    function _addClientForm ($iClientId) { 
 
        bx_import ('ClientFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ClientFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iClientId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'client_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableClientMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getClientEntryById($iEntryId);
                $this->onEventSubProfileCreate('client', $iEntryId, $sStatus, $aDataEntry);
   
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'client/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 
    function onEventClientDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('ClientVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ClientVoting';
        $oVoting = new $sClass ($this->_oDb->_sClientPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('ClientCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ClientCmts';
        $oCmts = new $sClass ($this->_oDb->_sClientPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sClientPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    function isAllowedPostClients(&$aDataEntry, $isPerformAction = false) {
 
        return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry));
    }
  
    function actionAllClients () {

		$sTitle = _t('_modzzz_professional_page_title_clients');

		$iPerPage=$this->_oDb->getParam('modzzz_professional_perpage_browse_clients');
  
        if (!$this->isAllowedBrowse()) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->getClientsBrowse($aProfiles, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 
        $sRet = '';
        foreach ($aProfiles as $aProfile) {
            $sRet .= $this->_oTemplate->client_unit($aProfile);
        }
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'all_clients' ;
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => false,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        $sRet .= $oPaginate->getPaginate();

        $this->_oTemplate->pageStart();

        $this->_oTemplate->addCss('main.css');
         $this->_oTemplate->addCss('view.css');

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }

    /*******[END - Client Functions] ******************************/
 

	/******[BEGIN] Service functions **************************/ 
     
	function actionService ($sAction, $sIdUri ) {
 
		switch($sAction){
			case 'embed': 
				$this->actionServiceEmbed ($sIdUri);
			break;
			case 'add': 
				$this->actionServiceAdd ($sIdUri, '_modzzz_professional_page_title_service_add');
			break;
			case 'edit':
				$this->actionServiceEdit ($sIdUri, '_modzzz_professional_page_title_service_edit');
			break;
			case 'delete':
				$this->actionServiceDelete ($sIdUri, _t('_modzzz_professional_msg_professional_service_was_deleted'));
			break;
			case 'view':
				$this->actionServiceView ($sIdUri, _t('_modzzz_professional_msg_pending_service_approval')); 
			break; 
			case 'browse': 
				return $this->actionServiceBrowse ($sIdUri, '_modzzz_professional_page_title_service_browse'); 
			break;  
		}
	}
    
    function actionServiceEmbed ($sUri) {
 
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

        if (!($aServiceEntry = $this->_oDb->getServiceEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

		$aDataEntry = $this->_oDb->getEntryById($aServiceEntry['professional_id']);
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aServiceEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aServiceEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_professional_page_title_manage_youtube') => '',
        ));


        bx_import ('EmbedServiceForm', $this->_aModule);
		$oForm = new BxProfessionalEmbedServiceForm ($this, $aServiceEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid()) {        
  
			$iEntryId = $aServiceEntry[$this->_oDb->_sFieldId];
			
			$aYoutubes2Keep = array(); 
 			if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){
 
				foreach ($_POST['prev_video'] as $iYoutubeId){
					$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
				}
			}
				
			$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId, 'service');
		
			$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

			if ($aDeletedYoutube) {
				foreach ($aDeletedYoutube as $iYoutubeId) {
					$this->_oDb->removeYoutubeEntry($iYoutubeId);
				}
			} 
			 
			$this->_oDb->addYoutube($aServiceEntry['professional_id'], $iEntryId, 'service');
 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri];
			header ('Location:' . $sRedirectUrl);
            return;
        } 

        echo $oForm->getCode (); 
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_professional_page_title_manage_youtube') .' - '. $aServiceEntry[$this->_oDb->_sFieldTitle]);
    }
 
    function actionServiceBrowse ($sUri, $sTitle) {
       
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
		
		$this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            _t('_modzzz_professional_menu_view_services') => '',
        ));
 
        bx_import ('ServicePageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ServicePageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();

		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']), false, false);  
    }
 
    function actionServiceView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aServiceEntry = $this->_oDb->getServiceEntryByUri($sUri);
		$iEntryId = (int)$aServiceEntry['professional_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aServiceEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri], 
        ));
 
        if ((!$this->_iProfileId || $aServiceEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableService, $aServiceEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        } 

        $this->_oTemplate->pageStart();

        bx_import ('ServicePageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ServicePageView';
        $oPage = new $sClass ($this, $aServiceEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('ServiceCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ServiceCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aServiceEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        //$this->_oTemplate->addPageKeywords ($aServiceEntry[$this->_oDb->_sFieldTags]);

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->addCss ('twig.css'); 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');

		$sPageTitle = $aServiceEntry[$this->_oDb->_sFieldTitle] .' | '. $aDataEntry[$this->_oDb->_sFieldTitle];
 
        $this->_oTemplate->pageCode($sPageTitle, false, false);

        //bx_import ('BxDolViews');
        //new BxDolViews($this->_oDb->_sServicePrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }
 
    function actionServiceEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;
 
        if (!($aServiceEntry = $this->_oDb->getServiceEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
  		$iProfessionalId = (int)$aServiceEntry['professional_id'];
		$aDataEntry = $this->_oDb->getEntryById($iProfessionalId);

        if (!$this->isAllowedSubEdit($aDataEntry, $aServiceEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
 		foreach($GLOBALS['aPreValues']['ServiceCurrency'] as $sKey=>$aVars) {
			if($aServiceEntry['currency'] == html_entity_decode($sKey)){
				$aServiceEntry['currency'] = $sKey; 
			}
	    } 


        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aServiceEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri], 
        ));

        bx_import ('ServiceFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ServiceFormEdit';
        $oForm = new $sClass ($this, $aServiceEntry[$this->_oDb->_sFieldAuthorId], $iProfessionalId,  $iEntryId, $aServiceEntry);
 
        $oForm->initChecker($aServiceEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'service_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;


            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
 
				$aYoutubes2Keep = array(); 
				if( is_array($_POST['prev_video']) && count($_POST['prev_video'])){
	 
					foreach ($_POST['prev_video'] as $iYoutubeId){
						$aYoutubes2Keep[$iYoutubeId] = $iYoutubeId;
					}
				}
					
				$aYoutubeIds = $this->_oDb->getYoutubeIds($iEntryId, 'service');
			
				$aDeletedYoutube = array_diff ($aYoutubeIds, $aYoutubes2Keep);

				if ($aDeletedYoutube) {
					foreach ($aDeletedYoutube as $iYoutubeId) {
						$this->_oDb->removeYoutubeEntry($iYoutubeId);
					}
				} 
	 
				$this->_oDb->addYoutube($aServiceEntry['professional_id'], $iEntryId, 'service');
 
                $this->onEventSubProfileChanged ('service', $iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri]);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry['title']));  
    }

    function actionServiceDelete ($iServiceId, $sMsgSuccess) {
 
  
        if (!($aServiceEntry = $this->_oDb->getServiceEntryById($iServiceId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iServiceId, 'ajaxy_popup_result_div');
            exit;
        }

		$iProfessionalId = (int)$aServiceEntry['professional_id'];
		$aDataEntry = $this->_oDb->getEntryById($iProfessionalId);

        if (!$this->isAllowedSubDelete($aDataEntry, $aServiceEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iServiceId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteServiceByIdAndOwner($iServiceId, $iProfessionalId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventServiceDeleted ($iServiceId, $aServiceEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iServiceId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iServiceId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionServiceAdd ($iServiceId, $sTitle) {
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iServiceId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	
 
        if (!$this->isAllowedPostServices($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
 
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri], 
			_t($sTitle) => '' 
		));
 
        $this->_addServiceForm($iServiceId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title']);  
    }
 
    function _addServiceForm ($iServiceId) { 
 
        bx_import ('ServiceFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ServiceFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iServiceId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
  
            $this->_oDb->_sTableMain = 'service_main';
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(), 
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getServiceEntryById($iEntryId);
                $this->onEventSubProfileCreate('service', $iEntryId, $sStatus, $aDataEntry);
   
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 
    function onEventServiceDeleted ($iEntryId, $aDataEntry = array()) {
 
        // delete votings
        bx_import('ServiceVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ServiceVoting';
        $oVoting = new $sClass ($this->_oDb->_sServicePrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('ServiceCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ServiceCmts';
        $oCmts = new $sClass ($this->_oDb->_sServicePrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sServicePrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    function isAllowedPostServices(&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;

        return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)); 
    }
  
    function actionAllServices () {

		$sTitle = _t('_modzzz_professional_page_title_services');

		$iPerPage=$this->_oDb->getParam('modzzz_professional_perpage_browse_services');
  
        if (!$this->isAllowedBrowse()) {            
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->getServicesBrowse($aProfiles, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles) {
            $this->_oTemplate->displayNoData ();
            return;
        }
        $iPages = ceil($iNum / $iPerPage);
 
        $sRet = '';
        foreach ($aProfiles as $aProfile) {
            $sRet .= $this->_oTemplate->service_unit($aProfile);
        }
        $sRet .= '<div class="clear_both"></div>';        

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'all_services' ;
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => false,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        $sRet .= $oPaginate->getPaginate();

        $this->_oTemplate->pageStart();

        $this->_oTemplate->addCss('main.css');
         $this->_oTemplate->addCss('view.css');

        echo DesignBoxContent ($sTitle, $sRet, 1);

        $this->_oTemplate->pageCode($sTitle, false, false);
    }

    /*******[END - Service Functions] ******************************/



	//[begin] [subprofile]
    function isSubProfileFan($sTable, $aDataEntry, $iProfileId = 0, $isConfirmed = true) {
 		
		if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isSubProfileFan ($sTable, $aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }

    function isAllowedViewSubProfile ($sTable, $aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_VIEW_PROFESSIONAL, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        $this->_oSubPrivacy = new BxProfessionalSubPrivacy($this, $sTable); 
	    return $this->_oSubPrivacy->check('view', $aDataEntry['id'], $this->_iProfileId); 
    }

    function isAllowedRateSubProfile($sTable, &$aDataEntry) {       
        if ($this->isAdmin())
            return true;
        
		$this->_oSubPrivacy = new BxProfessionalSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedCommentsSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;

        $this->_oSubPrivacy = new BxProfessionalSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedCommentsBooking($aServiceEntry, $aBookingEntry) {
        
        if ($this->isAdmin())
            return true;		

		if ($this->_iProfileId==$aServiceEntry['author_id'] || $this->_iProfileId==$aBookingEntry['author_id']) 
            return true;        
 
        return false;
    }
 
    function isAllowedUploadPhotosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxProfessionalSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxProfessionalSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSoundsSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxProfessionalSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFilesSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxProfessionalSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function actionUploadPhotosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadPhotosSubProfile', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_professional_page_title_upload_photos'));
    }

    function actionUploadVideosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadVideosSubProfile', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_professional_page_title_upload_videos'));
    } 

    function actionUploadFilesSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadFilesSubProfile', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_professional_page_title_upload_files'));
    } 

    function actionUploadSoundsSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadSoundsSubProfile', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_professional_page_title_upload_sounds'));
    } 
 
    function _actionUploadMediaSubProfile ($sType, $sUri, $sIsAllowedFuncName, $sMedia, $aMediaFields, $sTitle) {
   
		switch($sType){  
			case 'review':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;
				$sTable = $this->_oDb->_sTableReview ;
				$sDataFuncName = 'getReviewEntryByUri';
			break;  
			case 'client':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableClientMediaPrefix;
				$sTable = $this->_oDb->_sTableClient ;
				$sDataFuncName = 'getClientEntryByUri';
			break;
			case 'service':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableServiceMediaPrefix;
				$sTable = $this->_oDb->_sTableService ;
				$sDataFuncName = 'getServiceEntryByUri';
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

        $aProfessionalEntry = $this->_oDb->getEntryById($aDataEntry['professional_id']);
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aProfessionalEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aProfessionalEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];

        bx_import (ucwords($sType) . 'FormUploadMedia', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . ucwords($sType) . 'FormUploadMedia';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId],$aDataEntry['professional_id'], $iEntryId, $aDataEntry, $sMedia, $aMediaFields);
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
 
    function isAllowedSubAdd ($aDataEntry, $isPerformAction = false) {

		if ($this->isAdmin())
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_ADD_PROFESSIONAL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedSubEdit ($aDataEntry, $aSubDataEntry, $isPerformAction = false) {

        if ($this->isAdmin()) 
            return true;

        if ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aSubDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;
 
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_EDIT_ANY_PROFESSIONAL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 
  
    function isAllowedSubDelete ($aDataEntry, $aSubDataEntry, $isPerformAction = false) {

        if ($this->isAdmin()) 
            return true;

        if ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aSubDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_PROFESSIONAL_DELETE_ANY_PROFESSIONAL, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }    

    
    function onEventSubProfileCreate ($sType, $iEntryId, $sStatus, $aDataEntry = array()) {
         
		if ('approved' == $sStatus) {
			 //
        }
 
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		//$oAlert->alert();
    }
 
    function onEventSubProfileChanged($sType, $iEntryId, $sStatus) {
  
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'change', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		//$oAlert->alert();
    }

	//[end] [subprofile]


   function onEventDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        $this->reparseTags ($iEntryId);
        //$this->reparseCategories ($iEntryId);

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


		//[begin] delete review 
		$aReview = $this->_oDb->getAllSubItems('review', $iEntryId);
		foreach($aReview as $aEachReview){
			
			$iId = (int)$aEachReview['id'];

			// delete votings
			bx_import('ReviewVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ReviewVoting';
			$oVoting = new $sClass ($this->_oDb->_sReviewPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('ReviewCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ReviewCmts';
			$oCmts = new $sClass ($this->_oDb->_sReviewPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteReviewByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		} 
  		//[end] delete review 


		//[begin] delete client 
		$aClient = $this->_oDb->getAllSubItems('client', $iEntryId);
		foreach($aClient as $aEachClient){
			
			$iId = (int)$aEachClient['id'];

			// delete votings
			bx_import('ClientVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ClientVoting';
			$oVoting = new $sClass ($this->_oDb->_sClientPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('ClientCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ClientCmts';
			$oCmts = new $sClass ($this->_oDb->_sClientPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteClientByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		} 
  		//[end] delete client 


		//[begin] delete service 
		$aService = $this->_oDb->getAllSubItems('service', $iEntryId);
		foreach($aService as $aEachService){
			
			$iId = (int)$aEachService['id'];

			// delete votings
			bx_import('ServiceVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ServiceVoting';
			$oVoting = new $sClass ($this->_oDb->_sServicePrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('ServiceCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ServiceCmts';
			$oCmts = new $sClass ($this->_oDb->_sServicePrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteServiceByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		} 
  		//[end] delete service 
 
		$oModuleDb = new BxDolModuleDb();
 
		//[begin] delete associated jobs 
		if(getParam("modzzz_professional_jobs_active")=='on'){  
			if($oModuleDb->getModuleByUri('jobs')){ 
				$oJob = BxDolModule::getInstance('BxJobsModule');
				$aJobs = $this->_oDb->getBusinessJobs($iEntryId); 
				foreach($aJobs as $iJobId){  
					if ($oJob->_oDb->deleteEntryByIdAndOwner($iJobId, $this->_iProfileId, 0)) {
						$oJob->onEventDeleted ($iJobId);
					} 
				}
			}
		}
		//[end] delete associated jobs


		//[begin] delete associated coupons 
		if(getParam("modzzz_professional_coupons_active")=='on'){  
			if($oModuleDb->getModuleByUri('coupons')){ 
				$oCoupon = BxDolModule::getInstance('BxCouponsModule');
				$aCoupons = $this->_oDb->getBusinessCoupons($iEntryId); 
				foreach($aCoupons as $iCouponId){  
					if ($oCoupon->_oDb->deleteEntryByIdAndOwner($iCouponId, $this->_iProfileId, 0)) {
						$oCoupon->onEventDeleted ($iCouponId);
					} 
				}
			}
		}
		//[end] delete associated coupons
 
		//[begin] delete associated deals 
		if(getParam("modzzz_professional_deals_active")=='on'){ 
			if($oModuleDb->getModuleByUri('deals')){
				$oDeal = BxDolModule::getInstance('BxDealsModule');
				$aDeals = $this->_oDb->getBusinessDeals($iEntryId);
				foreach($aDeals as $iDealId){ 
					if ($oDeal->_oDb->deleteEntryByIdAndOwner($iDealId, $this->_iProfileId, 0)) {
						$oDeal->onEventDeleted ($iDealId);
					}
  				}
			}
		}
		//[end] delete associated deals
 
		// delete associated locations
		if (BxDolModule::getInstance('BxWmapModule')){ 
			BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri(), $iId));  
		}

        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		$oAlert->alert();
    }        

	/* functions added for v7.1 */

    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_professional_professional_single'), _t('_modzzz_professional_professional_single'), 'briefcase', false, '&filter=add_professional');
    }


   /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('professional', array(
            'part' => 'professional',
            'title' => '_modzzz_professional',
            'title_singular' => '_modzzz_professional_single',
            'icon' => 'modules/modzzz/professional/|map_marker.png',
            'icon_site' => 'briefcase',
            'join_table' => 'modzzz_professional_main',
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
            'join_field_privacy' => 'allow_view_professional_to',
            'permalink' => 'modules/?r=professional/view/',
        )));
    }
 
	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_professional_wall_object',
            'txt_added_new_single' => '_modzzz_professional_wall_added_new',
            'txt_added_new_plural' => '_modzzz_professional_wall_added_new_items',
            'txt_privacy_view_event' => 'view_professional',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_professional',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallPostComment($aEvent, $aParams);
    }
 
    function _serviceGetWallPostComment($aEvent, $aParams)
    {
        $iId = (int)$aEvent['object_id'];
        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = getNickName($iOwner);

        $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $iOwner, 1);
        if(empty($aItem) || !is_array($aItem))
        	return array('perform_delete' => true);

        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId))
            return '';

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || !isset($aContent['comment_id']))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iId);
        if(!$oCmts->isEnabled())
            return '';

        $aComment = $oCmts->getCommentRow((int)$aContent['comment_id']);

        $sImage = '';
        if($aItem[$this->_oDb->_sFieldThumb]) {
            $a = array('ID' => $aItem[$this->_oDb->_sFieldAuthorId], 'Avatar' => $aItem[$this->_oDb->_sFieldThumb]);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }

        $sCss = '';
        $sUri = $this->_oConfig->getUri();
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/';
        $sNoPhoto = $this->_oTemplate->getIconUrl('no-photo.png');
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'), true);
        else
            $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'));

        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);

        $sTextAddedNew = _t('_modzzz_' . $sUri . '_wall_added_new_comment');
        $sTextWallObject = _t('_modzzz_' . $sUri . '_wall_object');
        $aTmplVars = array(
            'cpt_user_name' => $sOwner,
            'cpt_added_new' => $sTextAddedNew,
            'cpt_object' => $sTextWallObject,
            'cpt_item_url' => $sBaseUrl . $aItem[$this->_oDb->_sFieldUri],
            'cnt_comment_text' => $aComment['cmt_text'],
            'unit' => $this->_oTemplate->unit($aItem, 'unit', $oVoting),
            'post_id' => $aEvent['id'],
        );
        return array(
            'title' => $sOwner . ' ' . $sTextAddedNew . ' ' . $sTextWallObject,
            'description' => $aComment['cmt_text'],
            'content' => $sCss . $this->_oTemplate->parseHtmlByName('wall_post_comment', $aTmplVars)
        );
    }


    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_professional',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'briefcase', $aParams);
    }
 
    function _formatLocation (&$aDataEntry, $isCountryLink = false, $isFlag = false)
    {
        $sFlag = $isFlag ? ' ' . genFlag($aDataEntry['country']) : '';
        $sCountry = _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($aDataEntry['country']) . '">' . $sCountry . '</a>';
        return (trim($aDataEntry['city']) ? $aDataEntry['city'] . ', ' : '') . $sCountry . $sFlag;
    }

    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc' => $this->_formatSnippetText($aEntryData, 200),
            'location' => $this->_formatLocation($aEntryData, false, false),
            'fans_count' => $aEntryData['fans_count'],
            'services_count' => $aEntryData['services_count'],
        ));
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 300, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }
 
    function serviceDeleteProfileData ($iProfileId)
    {
        $iProfileId = (int)$iProfileId;

        if (!$iProfileId)
            return false;

        // delete entries which belongs to particular author
        $aDataEntries = $this->_oDb->getEntriesByAuthor ($iProfileId);
        foreach ($aDataEntries as $iEntryId) {
            if ($this->_oDb->deleteEntryByIdAndOwner($iEntryId, $iProfileId, false))
                $this->onEventDeleted ($iEntryId);
        }

        // delete from list of fans/services/admins
        $this->_oDb->removeFanFromAllEntries ($iProfileId);
        $this->_oDb->removeAdminFromAllEntries ($iProfileId);
    }
 
    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        return true;
    }

	function actionWebsite ($sUri) {
       
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
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $sUrl = strncasecmp($aDataEntry['website'], 'http://', 7) != 0 && strncasecmp($aDataEntry['website'], 'https://', 8) != 0 ? 'http://' . $aDataEntry['website'] : $aDataEntry['website'];
 
		$aVars = array (  
            'site_url' => $sUrl  
        );
 
        echo $this->_oTemplate->parseHtmlByName('block_external', $aVars); 
   
		$sTitle = _t('_modzzz_professional_title_website', $aDataEntry[$this->_oDb->_sFieldTitle]);
        $this->_oTemplate->pageCode($sTitle , false, false);  
    }

	function hasWebsite(){
 
		if($_REQUEST["r"] || $_REQUEST["orca_integration"]=='professional'){ 
 
			$pattern = '/^professional\/view\//';
			preg_match($pattern, $_REQUEST["r"], $matches);
			$aDataEntry = array();
			if ($matches[0]){ 
				$sUrl = str_replace($matches[0],'',$_REQUEST["r"]);	  
				if($sUrl){
					$aDataEntry = $this->_oDb->getEntryByUri($sUrl);  
				}
			}  
  
			return ($aDataEntry['website']) ? true : false;  

		}else{
			return false;
		} 
	}
 
    function isAllowedEmbed(&$aDataEntry) { 
     
		if( getParam('modzzz_professional_allow_embed') != 'on' ) return false;
 
		return $this->isAllowedUploadVideos($aDataEntry);  
    }
 
    function isAllowedSubEmbed($sTable, &$aDataEntry) { 
     
		if( getParam('modzzz_professional_allow_embed') != 'on' ) return false;
 
		return $this->isAllowedUploadVideosSubProfile($sTable, $aDataEntry);  
    }

    function actionEmbed ($sUri, $sType='') {
		$sTitle = _t('_modzzz_professional_page_title_embed_video');

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

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));

        bx_import ('EmbedForm', $this->_aModule);
		$oForm = new BxProfessionalEmbedForm ($this, $aDataEntry);
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
					$this->_oDb->removeYoutubeEntry($iYoutubeId);
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
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
   
    function actionRequest ($sServiceUri) {
        $this->_actionRequest ($sServiceUri, 'modzzz_professional_request', _t('_modzzz_professional_caption_request_quote'), _t('_modzzz_professional_request_sent'), _t('_modzzz_professional_request_not_sent'));
    }

    function _actionRequest ($sServiceUri, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) { 

		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if (!preg_match($sReg, $sServiceUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}

		$aServiceEntry = $this->_oDb->getServiceEntryByUri($sServiceUri);
 
        $iEntryId = (int)$aServiceEntry['professional_id'];
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aServiceEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aServiceEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));


        bx_import ('RequestForm', $this->_aModule);
		$oForm = new BxProfessionalRequestForm ($this, $aServiceEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
            $aValsAdd = array (
                'service_id' => $aServiceEntry[$this->_oDb->_sFieldId],
                'professional_id' => $aDataEntry[$this->_oDb->_sFieldId],
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId,
                $this->_oDb->_sFieldCreated => time(),
            );                        
 
            $oForm->insert ($aValsAdd);
  
			$aRequestr = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getRequestParams ($aDataEntry, $aRequestr, $oForm->getCleanValue('note'));
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to professional owner
 
			 $aRecipient = getProfileInfo($iRecipient); 

			 $sContactEmail = trim($aDataEntry['email']) ? trim($aDataEntry['email']) : trim($aRecipient['Email']);

			 $sSubject = str_replace("<NickName>",$aRequestr['NickName'], $aTemplate['Subject']);
			 $sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

			 $aPlus = array_merge (array ('RecipientName' => ' ' . $aRecipient['NickName']), $aPlusOriginal);

			 $iSuccess = sendMail($sContactEmail, $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
			 
            $sMsg = ($iSuccess) ? $sMsgSuccess : $sMsgFail;
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getRequestParams ($aDataEntry, $aRequestr, $sNote) {
        return array (
                'ServiceTitle' => $aDataEntry['title'], 
                'ServiceUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aDataEntry['uri'],
                'SenderLink' => $aRequestr ? getProfileLink($aRequestr['ID']) : 'javascript:void(0);',
                'SenderName' => $aRequestr ? $aRequestr['NickName'] : _t('_modzzz_professional_user_unknown'),
                'Message' => stripslashes(strip_tags($sNote)),
            );        
    }
  
    function actionRespond ($iRequestId, $sServiceUri) {
        $this->_actionRespond ($iRequestId, $sServiceUri, 'modzzz_professional_respond', _t('_modzzz_professional_caption_respond_quote'), _t('_modzzz_professional_respond_sent'), _t('_modzzz_professional_respond_not_sent'));
    }

    function _actionRespond ($iRequestId, $sServiceUri, $sEmailTemplate, $sTitle, $sMsgSuccess, $sMsgFail) { 

		if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
			$sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
		else
			$sReg = '/^[\d\w\-_]+$/u'; // latin characters only

		if (!preg_match($sReg, $sServiceUri)) {
			$this->_oTemplate->displayPageNotFound ();
			return false;
		}
 
        if (!($aServiceEntry = $this->_oDb->getServiceEntryByUri($sServiceUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        if (!($aRequestEntry = $this->_oDb->getRequestEntryById($iRequestId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $iEntryId = (int)$aServiceEntry['professional_id'];
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aServiceEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
            $aServiceEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aServiceEntry[$this->_oDb->_sFieldUri],
            $sTitle => '',
        ));


        bx_import ('RespondForm', $this->_aModule);
		$oForm = new BxProfessionalRespondForm ($this, $aServiceEntry, $aRequestEntry);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
            $aValsAdd = array ();                        
 
            $oForm->update ($iRequestId, $aValsAdd);
  
			$aResponder = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getRespondParams ($aDataEntry, $aResponder, $oForm->getCleanValue('note'));
		   
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to professional owner
  
			 $sRecipientEmail = $aRequestEntry['email'];
			 $sRecipientName = $aRequestEntry['first_name'] .' '. $aRequestEntry['last_name'];

			 $sSubject = str_replace("<NickName>", $sRecipientName, $aTemplate['Subject']);
			 $sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $sSubject);

			 $aPlus = array_merge (array ('RecipientName' => ' ' . $sRecipientName), $aPlusOriginal);

			 $iSuccess = sendMail($sRecipientEmail, $sSubject, $aTemplate['Body'], '', $aPlus) ? 1 : 0;  
			 
            $sMsg = ($iSuccess) ? $sMsgSuccess : $sMsgFail;
            echo MsgBox($sMsg);
            $this->_oTemplate->addCss ('main.css');
            $this->_oTemplate->pageCode ($sMsg, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getRespondParams ($aDataEntry, $aResponder, $sNote) {
        return array (
                'ServiceTitle' => $aDataEntry['title'], 
                'ServiceUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aDataEntry['uri'],
                'SenderLink' => getProfileLink($aResponder['ID']),
                'SenderName' => $aResponder['NickName'],
                'Message' => stripslashes(strip_tags($sNote)),
            );        
    }
 
 
    function serviceStatesInstall()
    {
		$this->_oDb->statesInstall(); 
	}

 

}
