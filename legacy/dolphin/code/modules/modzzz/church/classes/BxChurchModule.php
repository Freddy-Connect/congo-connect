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

function modzzz_church_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'church') {
        $oMain = BxDolModule::getInstance('BxChurchModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
bx_import('BxDolCategories');


define ('BX_CHURCH_PHOTOS_CAT', 'Church');
define ('BX_CHURCH_PHOTOS_TAG', 'church');

define ('BX_CHURCH_VIDEOS_CAT', 'Church');
define ('BX_CHURCH_VIDEOS_TAG', 'church');

define ('BX_CHURCH_SOUNDS_CAT', 'Church');
define ('BX_CHURCH_SOUNDS_TAG', 'church');

define ('BX_CHURCH_FILES_CAT', 'Church');
define ('BX_CHURCH_FILES_TAG', 'church');

define ('BX_CHURCH_MAX_FANS', 1000);
 
/*
 * Church module
 *
 * This module allow users to create user's church, 
 * users can rate, comment and discuss church.
 * Church can have photos, videos, sounds and files, uploaded
 * by church's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add church' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new church was created
 * change - church was chaned
 * rate - somebody rated church
 * commentPost - somebody posted comment in church
 *
 *
 *
 * Memberships/ACL:
 * church view church - BX_CHURCH_VIEW_CHURCH
 * church browse - BX_CHURCH_BROWSE
 * church search - BX_CHURCH_SEARCH
 * church add church - BX_CHURCH_ADD_CHURCH
 * church comments delete and edit - BX_CHURCH_COMMENTS_DELETE_AND_EDIT
 * church edit any church - BX_CHURCH_EDIT_ANY_CHURCH
 * church delete any church - BX_CHURCH_DELETE_ANY_CHURCH
 * church mark as featured - BX_CHURCH_MARK_AS_FEATURED
 * church approve church - BX_CHURCH_APPROVE_CHURCH
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different church
 * @see BxChurchModule::serviceHomepageBlock
 * BxDolService::call('church', 'homepage_block', array());
 *
 * Profile block with user's church
 * @see BxChurchModule::serviceProfileBlock
 * BxDolService::call('church', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for church (for internal usage only)
 * @see BxChurchModule::serviceGetMemberMenuItem
 * BxDolService::call('church', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_church'
 * The following alerts are rised
 *
 
 *
 *  add - new church was added
 *      $iObjectId - church id
 *      $iSenderId - creator of a church
 *      $aExtras['Status'] - status of added church
 *
 *  change - church's info was changed
 *      $iObjectId - church id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed church
 *
 *  delete - church was deleted
 *      $iObjectId - church id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - church was marked/unmarked as featured
 *      $iObjectId - church id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if church was marked as featured and 0 - if church was removed from featured 
 *
 */
class BxChurchModule extends BxDolTwigModule {

    var $_oPrivacy;
     
	var $_aQuickCache = array ();

    function BxChurchModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_church';

        bx_import ('Privacy', $aModule);
        bx_import ('SubPrivacy', $aModule);
		$this->_oPrivacy = new BxChurchPrivacy($this);
 
	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxChurchModule'] = &$this;

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
        parent::_actionHome(_t('_modzzz_church_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_church_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_church_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_church_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_church_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_church_page_title_comments'));
    }

    function actionBrowseFans ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('modzzz_church_perpage_browse_fans'), 'browse_fans/', _t('_modzzz_church_page_title_fans'));
    }
  
    function actionBrowseReviews ($sUri) {
        $this->_actionBrowseReviews ($sUri, _t('_modzzz_church_page_title_reviews'));
    }

    function _actionBrowseReviews ($sUri, $sTitle) {

		$iPerPage=$this->_oDb->getParam('modzzz_church_perpage_browse_reviews');

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

		$sTitle = _t('_modzzz_church_page_title_reviews');

		$iPerPage=$this->_oDb->getParam('modzzz_church_perpage_browse_reviews');
  
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
   
    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_modzzz_church_msg_pending_approval'));
    }

    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_church_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_church_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_church_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_church_page_title_upload_files')); 
    }
  
    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_church_page_title_calendar'));
    }

    function actionSearch ($sKeyword = '', $sCategory = '', $sCountry = '', $sState = '', $sCity = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_GET['Keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['Category'] = $sCategory;
		if ($sCity) 
            $_GET['City'] = $sCity;
		if ($sState) 
            $_GET['State'] = $sState; 
         if ($sCountry)
            $_GET['Country'] = explode(',', $sCountry);


        if (is_array($_GET['Country']) && 1 == count($_GET['Country']) && !$_GET['Country'][0]) {
            unset($_GET['Country']);
            unset($sCountry);
        }
  
        if ($sCountry || $sCategory || $sKeyword || $sState || $sCity ) {
            $_GET['submit_form'] = 1;  
        }
        
        modzzz_church_import ('FormSearch');
        $oForm = new BxChurchFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_church_import ('SearchResult');
            $o = new BxChurchSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );

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
        $this->_oTemplate->pageCode(_t('_modzzz_church_caption_search'));
    } 


    function actionSearchOLD ($sKeyword = '', $sCategory = '', $sCity = '', $sCountry = '') {
 
        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_REQUEST['Keyword'] = $sKeyword;
        if ($sKeyword) 
            $_REQUEST['City'] = $sCity;
        if ($sCategory)
            $_REQUEST['Category'] = explode(',', $sCategory); 
        if ($sCountry)
            $_REQUEST['Country'] = explode(',', $sCountry);

        if (is_array($_REQUEST['Country']) && 1 == count($_REQUEST['Country']) && !$_REQUEST['Country'][0]) {
            unset ($_REQUEST['Country']);
            unset($sCountry);
        }

        if (is_array($_REQUEST['Category']) && 1 == count($_REQUEST['Category']) && !$_REQUEST['Category'][0]) {
            unset ($_REQUEST['Category']);
            unset($sCategory);
        }
 
        if ($sCategory || $sKeyword || $sCity || $sCountry) {
            $_REQUEST['submit_form'] = 1;
        }
        
        bx_import ('FormSearch', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormSearch';
        $oForm = new $sClass ();
        $oForm->initChecker();        

        if ($oForm->isSubmittedAndValid ()) {
 
            bx_import ('SearchResult', $this->_aModule);
            $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
            $o = new $sClass('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('Country'));
 
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
        $this->_oTemplate->pageCode(_t('_modzzz_church_page_title_search'));
    }

    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_church_page_title_add'));
    }
  
    function _addForm ($sRedirectUrl) {
  
		$bPaidChurch = $this->isAllowedPaidChurches (); 
		if( $bPaidChurch && (!isset($_POST['submit_form'])) ){
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
                    'caption' => _t('_modzzz_church_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),	
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_church_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_church_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_church_continue'),
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
 
 			$bPaidListing = $this->isPaidPackage($oForm->getCleanValue('package_id')); 

			if($bPaidListing)
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

				if( ($GLOBALS['site']['ver']=='7.0') && ((int)$GLOBALS['site']['build']<3) ) {  
 					$oForm->processMedia($iEntryId, $this->_iProfileId); 
				}else{
 					$oForm->processAddMedia($iEntryId, $this->_iProfileId);
				}

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
				
				if($this->isAllowedPaidChurches()){
 
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
					$iNumActiveDays = (int)getParam("modzzz_church_free_expired");
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
  
    function actionEdit ($iEntryId) {
        parent::_actionEdit ($iEntryId, _t('_modzzz_church_page_title_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_church_msg_church_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_church_msg_added_to_featured'), _t('_modzzz_church_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_church_caption_share_church'));
    }
 
   function actionJoin ($iEntryId, $iProfileId) {

        parent::_actionJoin ($iEntryId, $iProfileId, _t('_modzzz_church_msg_joined_already'), _t('_modzzz_church_msg_joined_request_pending'), _t('_modzzz_church_msg_join_success'), _t('_modzzz_church_msg_join_success_pending'), _t('_modzzz_church_msg_leave_success'));
    }    
 
    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_modzzz_church_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_CHURCH_MAX_FANS);
    }

    function actionTags() {
        parent::_actionTags (_t('_modzzz_church_page_title_tags'));
    }    
 
    function actionPackages () { 
        $this->_oTemplate->pageStart();
        bx_import ('PagePackages', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PagePackages';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t('_modzzz_church_page_title_packages'), false, false);
    }

    function actionCategories ($sUri='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

         $this->_oTemplate->pageCode(_t('_modzzz_church_page_title_categories'), false, false);
    }

    function actionSubCategories ($sUri='') {
        $this->_oTemplate->pageStart();
        bx_import ('PageSubCategory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageSubCategory';
        $oPage = new $sClass ($this,$sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

         $this->_oTemplate->pageCode(_t('_modzzz_church_page_title_subcategories'), false, false);
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

    function actionMakeClaimPopup ($iEntryId) {
        parent::_actionMakeClaimPopup ($iEntryId, _t('_modzzz_church_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', modzzz_church_MAX_FANS);
    }

    function _actionMakeClaimPopup ($iEntryId, $sTitle, $sFuncGetFans = 'getFans', $sFuncIsAllowedManageFans = 'isAllowedManageFans', $sFuncIsAllowedManageAdmins = 'isAllowedManageAdmins', $iMaxFans = 1000) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById ($iEntryId))) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        if (!$this->$sFuncIsAllowedManageFans($aDataEntry)) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Access denied')));
            exit;
        }

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetFans($aProfiles, $iEntryId, true, 0, $iMaxFans);
        if (!$iNum) {
            echo $GLOBALS['oFunctions']->transBox(MsgBox(_t('_Empty')));
            exit;
        }

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . "view/" . $aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'fans_remove',
                'value' => _t('_sys_btn_fans_remove'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}remove&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
            ),
        );

        if ($this->$sFuncIsAllowedManageAdmins($aDataEntry)) {

            $aButtons = array_merge($aButtons, array (
                array (
                    'type' => 'submit',
                    'name' => 'fans_add_to_admins',
                    'value' => _t('_sys_btn_fans_add_to_admins'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}add_to_admins&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
                ),
                array (
                    'type' => 'submit',
                    'name' => 'fans_move_admins_to_fans',
                    'value' => _t('_sys_btn_fans_move_admins_to_fans'),
                    'onclick' => "onclick=\"getHtmlData('sys_manage_items_manage_fans_content', '{$sActionsUrl}admins_to_fans&ids=' + sys_manage_items_get_manage_fans_ids()); return false;\"",
                ),            
            ));
        };
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_manage_fans', $aButtons, 'sys_fan_unit');

        $aVarsContent = array (            
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
 
    function actionInvite ($iEntryId) {
        $this->_actionInvite ($iEntryId, 'modzzz_church_invitation', $this->_oDb->getParam('modzzz_church_max_email_invitations'), _t('_modzzz_church_invitation_sent'), _t('_modzzz_church_no_users_msg'), _t('_modzzz_church_caption_invite'));
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
                    $iSuccess += sendMail(trim($aRecipient['Email']), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
                }
            }

            // send invitation to additional emails
            $iMaxCount = $iMaxEmailInvitations;
            $aEmails = preg_split ("#[,\s\\b]+#", $_REQUEST['inviter_emails']);
            $aPlus = array_merge (array ('NickName' => ''), $aPlusOriginal);
            if ($aEmails && is_array($aEmails)) {
                foreach ($aEmails as $sEmail) {
                    if (strlen($sEmail) < 5) 
                        continue;
                    $iRet = sendMail(trim($sEmail), $aTemplate['Subject'], $aTemplate['Body'], '', $aPlus) ? 1 : 0;
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
        return array (
                'ChurchName' => $aDataEntry['title'],
                'ChurchLocation' => _t($GLOBALS['aPreValues']['country'][$aDataEntry['Country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : ''),
                'ChurchUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_modzzz_church_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_REQUEST['inviter_text'])),
            );        
    }
 
    function actionInquire ($iEntryId) {
        $this->_actionInquire ($iEntryId, 'modzzz_church_inquiry', _t('_modzzz_church_caption_make_inquiry'), _t('_modzzz_church_inquiry_sent'), _t('_modzzz_church_inquiry_not_sent'));
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
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        bx_import ('InquireForm', $this->_aModule);
		$oForm = new BxChurchInquireForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aInquirer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInquireParams ($aDataEntry, $aInquirer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to church owner
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
            $this->_oTemplate->pageCode ($sTitle, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getInquireParams ($aDataEntry, $aInquirer) {
        return array (
                'ListTitle' => $aDataEntry['title'], 
                'ListUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aInquirer ? getProfileLink($aInquirer['ID']) : 'javascript:void(0);',
                'SenderName' => $aInquirer ? $aInquirer['NickName'] : _t('_modzzz_church_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['inquire_text'])),
            );        
    }


/*[begin] claim*/
    function actionClaim ($iEntryId) {
        $this->_actionClaim ($iEntryId, 'modzzz_church_claim', _t('_modzzz_church_caption_make_claim'), _t('_modzzz_church_claim_sent'), _t('_modzzz_church_claim_not_sent'));
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
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        bx_import ('ClaimForm', $this->_aModule);
		$oForm = new BxChurchClaimForm ($this);
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
				 
				$arrAdmins = $this->_oDb->getProfileAdmins();

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
            $this->_oTemplate->pageCode ($sTitle, true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }

    function _getClaimParams ($aDataEntry, $aClaimer) {
        return array (
                'ListTitle' => $aDataEntry['title'], 
                'ListUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'SenderLink' => $aClaimer ? getProfileLink($aClaimer['ID']) : 'javascript:void(0);',
                'SenderName' => $aClaimer ? $aClaimer['NickName'] : _t('_modzzz_church_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['claim_text'])),
            );        
    }
/*[end] claim*/



    // ================================== external actions

    /**
     * Homepage block with different church
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent()){ 
			return '';
        } 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxChurchPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));
  
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_church_homepage_default_tab');
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
            $this->_oDb->getParam('modzzz_church_perpage_homepage'), 
            array(
                _t('_modzzz_church_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_church_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_church_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_church_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's church
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxChurchPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_church_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

     /**
     * Account block with different events
     * @return html to display area churches in account page a block
     */  
    function serviceAccountAreaBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sCity = $aProfileInfo['City'];

		if(!$sCity)
			return;

        bx_import ('PageMain', $this->_aModule);
        $o = new BxChurchPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . 'member.php?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('modzzz_church_perpage_accountpage'),
			array(),
			$sCity
        );
    }

    /**
     * Account block with different events
     * @return html to display member churches in account page a block
     */ 
    function serviceAccountPageBlock () {
  
        $aProfile = getProfileInfo($this->_iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxChurchPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . 'member.php?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_church_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }
 
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_church_spy_post',
            'change' => '_modzzz_church_spy_post_change', 
            'join' => '_modzzz_church_spy_join',
            'rate' => '_modzzz_church_spy_rate',
            'commentPost' => '_modzzz_church_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_church_sbs_change'),
            'commentPost' => _t('_modzzz_church_sbs_comment'),
            'rate' => _t('_modzzz_church_sbs_rate'), 
            'join' => _t('_modzzz_church_sbs_join'), 
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
			'name' => _t('_modzzz_church_packages'),
			'bx_repeat:items' => $aPackage,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages/'
		));


		$aPackage = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "packages` WHERE  `id` = '$iPackage'");
		  
		$sFormName = 'packages_form';
  
	    if($iPackage){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_church_categ_btn_edit'),
				'action_delete' => _t('_modzzz_church_categ_btn_delete'), 
				'action_add' => _t('_modzzz_church_categ_btn_add')  
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_church_categ_btn_save')
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
 				'action_add' => _t('_modzzz_church_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_packages',$aVars);
		}

		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));  
 
	}
 
    function actionAdministration ($sUrl = '',$sParam1='',$sParam2='') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
 
        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_church_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_church_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),   
           'categories' => array(
                'title' => _t('_modzzz_church_menu_admin_manage_categories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories',
                '_func' => array ('name' => 'actionAdministrationCategories', 'params' => array($sParam1)),
            ),
            'subcategories' => array(
                'title' => _t('_modzzz_church_menu_admin_manage_subcategories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories',
                '_func' => array ('name' => 'actionAdministrationSubCategories', 'params' => array($sParam1,$sParam2)),
            ), 
			'invoices' => array(
                'title' => _t('_modzzz_church_menu_manage_invoices'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/invoices',
                '_func' => array ('name' => 'actionAdministrationInvoices', 'params' => array($sParam1)),
            ), 			
			'orders' => array(
                'title' => _t('_modzzz_church_menu_manage_orders'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/orders',
                '_func' => array ('name' => 'actionAdministrationOrders', 'params' => array($sParam1)),
            ),
			'packages' => array(
                'title' => _t('_modzzz_church_menu_manage_packages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/packages',
                '_func' => array ('name' => 'actionAdministrationPackages', 'params' => array($sParam1)),
            ),  
			'claims' => array(
                'title' => _t('_modzzz_church_menu_manage_claims'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/claims',
                '_func' => array ('name' => 'actionAdministrationClaims', 'params' => array($sParam1)),
            ), 
			'donors' => array(
                'title' => _t('_modzzz_church_menu_manage_donors'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/donors',
                '_func' => array ('name' => 'actionAdministrationDonors', 'params' => array($sParam1)),
            ),  
            'create' => array(
                'title' => _t('_modzzz_church_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_church_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t(''), $aMenu);
		$this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
        
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_church_page_title_administration'));
    }

    function actionAdministrationDonors ($iEntryId=0) {
		
		$sContent = $this->loadAdministrationChurches($iEntryId);
  
		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		$sContent = $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));  

		$sContent .= $this->_manageDonorsEntries ('donors', $iEntryId, false, 'bx_twig_admin_form');
 
        return $sContent;
    }
  
   function loadAdministrationChurches ($sParam1='') {
   		$sChurch = process_db_input($sParam1);
  
		$aChurches = $this->_oDb->getChurches();
 
		$aChurch[] = array(
			'value' => '',
			'caption' => _t('_Select') ,
			'selected' => ''
		);

		foreach ($aChurches as $oChurch){ 
			$sKey = $oChurch['uri'];
			$sValue = $oChurch['title'];
   
			$aChurch[] = array(
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $sChurch) ? 'selected="selected"' : ''
			);
		}
 
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => _t('_modzzz_church_churches'),
			'bx_repeat:items' => $aChurch,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/donors/'
		));
 
		return $sContent;
	}


    function _manageDonorsEntries ($sMode, $sValue, $isFilter, $sFormName, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0) {

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'donors_unit';

       if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

		$o->sBrowseUrl='administration/donors/'.$sValue;
 
        $sPagination = $sActionsPanel = '';
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displaySubProfileResultBlock('donors'))) {
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination();
         }

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id', 'filter_checkbox_id', $this->_sFilterName) : '',
            'actions_panel' => '',
        );        
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
  
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Church');
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
			'name' => _t('_modzzz_church_categories'),
			'bx_repeat:items' => $aCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories/'
		));


		$aCategory = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "categ` WHERE  `id` = '$iCategory'");
		 
		$sFormName = 'categories_form';
  
	    if($iCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_church_categ_btn_edit'),
				'action_delete' => _t('_modzzz_church_categ_btn_delete'), 
				'action_add' => _t('_modzzz_church_categ_btn_add'),
				'action_sub' => _t('_modzzz_church_categ_btn_subcategories'), 
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_church_categ_btn_save')
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
 				'action_add' => _t('_modzzz_church_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_categories',$aVars);
		}

		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));  
	}

    function actionAdministrationSubCategories ($sParam1='', $sParam2='') {
 		$sMessage = "";
  		$iCategory = (int)process_db_input($sParam1);
   		$iSubCategory = (int)process_db_input($sParam2);
		$sCategoryName = $this->_oDb->getCategoryName($iCategory);
		
		if(!$iCategory){
			$sContent = MsgBox(_t('_modzzz_church_manage_subcategories_msg')); 

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
			'name' => $sCategoryName .': '. _t('_modzzz_church_subcategories'),
			'bx_repeat:items' => $aSubCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories/'.$iCategory.'/'
		));


		$aCategory = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "categ` WHERE  `id` = '$iSubCategory'");
		 
		$sFormName = 'categories_form';
 
	    if($iSubCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_church_categ_btn_edit'),
				'action_delete' => _t('_modzzz_church_categ_btn_delete'), 
				'action_add' => _t('_modzzz_church_categ_btn_add'),
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_church_categ_btn_save')
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
 				'action_add' => _t('_modzzz_church_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_categories',$aVars);
		}

		$GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));  
	}
  
    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_church_admin_delete', '_modzzz_church_admin_activate');
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
			'action_activate' => '_modzzz_church_admin_activate',
			'action_delete' => '_modzzz_church_admin_delete',
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
 			'action_delete' => '_modzzz_church_admin_delete',
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
 			'action_assign' => '_modzzz_church_admin_assign',
 			'action_delete' => '_modzzz_church_admin_delete',
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
    
	function isPaidChurch($iEntryId){
		$iEntryId = (int)$iEntryId;
         
		if (getParam('modzzz_church_paid_active')!='on') 
            return false;	
 
		$aEntry = $this->_oDb->getEntryById($iEntryId);
		$sInvoiceNo = $aEntry['invoice_no'];
 
 		$aInvoice = $this->_oDb->getInvoiceByNo($sInvoiceNo);
		$iPackageId = (int)$aInvoice['package_id'];

		return $this->_oDb->isPaidPackage($iPackageId);  
	}


 	function isPaidPackage($iPackageId){
 
		if(!$this->isAllowedPaidChurches())
			return false;

		return $this->_oDb->isPaidPackage($iPackageId);  
	}
 
    function isAllowedPaidChurches ($bCheckAdmin=true) {
  
		 if($bCheckAdmin && $this->isAdmin())
			return false;

        // admin always have access  
        if (getParam('modzzz_church_paid_active')=='on') 
            return true;	
            
		return false;
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
 
		//[begin] delete events
		$aEvents = $this->_oDb->getAllSubItems('event', $iEntryId);
		foreach($aEvents as $aEachEvent){
			
			$iId = (int)$aEachEvent['id'];
 
			// delete votings
			bx_import('EventVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'EventVoting';
			$oVoting = new $sClass ($this->_oDb->_sEventPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('EventCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'EventCmts';
			$oCmts = new $sClass ($this->_oDb->_sEventPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteEventByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());   
		}  
 		//[end] delete events

		//[begin] delete doctrines
		$aDoctrines = $this->_oDb->getAllSubItems('doctrine', $iEntryId);
		foreach($aDoctrines as $aEachDoctrine){
			
			$iId = (int)$aEachDoctrine['id'];
 
			// delete votings
			bx_import('DoctrineVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'DoctrineVoting';
			$oVoting = new $sClass ($this->_oDb->_sDoctrinePrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('DoctrineCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'DoctrineCmts';
			$oCmts = new $sClass ($this->_oDb->_sDoctrinePrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteDoctrineByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());   
		}  
 		//[end] delete doctrines

		//[begin] delete sermons
		$aSermons = $this->_oDb->getAllSubItems('sermon', $iEntryId);
		foreach($aSermons as $aEachSermon){
			
			$iId = (int)$aEachSermon['id'];
 
			// delete votings
			bx_import('SermonVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SermonVoting';
			$oVoting = new $sClass ($this->_oDb->_sSermonPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('SermonCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SermonCmts';
			$oCmts = new $sClass ($this->_oDb->_sSermonPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteSermonByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());   
		}  
 		//[end] delete sermons
 
		//[begin] delete staffs
		$aStaffs = $this->_oDb->getAllSubItems('staff', $iEntryId);
		foreach($aStaffs as $aEachStaff){
			
			$iId = (int)$aEachStaff['id'];
 
			// delete votings
			bx_import('StaffVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'StaffVoting';
			$oVoting = new $sClass ($this->_oDb->_sStaffPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('StaffCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'StaffCmts';
			$oCmts = new $sClass ($this->_oDb->_sStaffPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteStaffByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());   
		}  
 		//[end] delete staffs
 
		//[begin] delete news
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
		
 			$this->_oDb->deleteNewsByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());  
		}   
 		//[end] delete news
 
		//[begin] delete members
		$aMembers = $this->_oDb->getAllSubItems('members', $iEntryId);
		foreach($aMembers as $aEachMembers){
			
			$iId = (int)$aEachMembers['id'];

			// delete votings
			bx_import('MembersVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'MembersVoting';
			$oVoting = new $sClass ($this->_oDb->_sMembersPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('MembersCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'MembersCmts';
			$oCmts = new $sClass ($this->_oDb->_sMembersPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteMembersByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());  
		}   
 		//[end] delete members

		//[begin] delete ministries
		$aMinistries = $this->_oDb->getAllSubItems('ministries', $iEntryId);
		foreach($aMinistries as $aEachMinistries){
			
			$iId = (int)$aEachMinistries['id'];

			// delete votings
			bx_import('MinistriesVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'MinistriesVoting';
			$oVoting = new $sClass ($this->_oDb->_sMinistriesPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('MinistriesCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'MinistriesCmts';
			$oCmts = new $sClass ($this->_oDb->_sMinistriesPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteMinistriesByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());  
		}   
 		//[end] delete ministries

		//[begin] delete branches
		$aBranches = $this->_oDb->getAllSubItems('branches', $iEntryId);
		foreach($aBranches as $aEachBranches){
			
			$iId = (int)$aEachBranches['id'];

			// delete votings
			bx_import('BranchesVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'BranchesVoting';
			$oVoting = new $sClass ($this->_oDb->_sBranchesPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('BranchesCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'BranchesCmts';
			$oCmts = new $sClass ($this->_oDb->_sBranchesPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteBranchesByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());  
		}   
 		//[end] delete branches


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

		//[begin] delete faq 
		$aFAQ = $this->_oDb->getAllSubItems('faq', $iEntryId);
		foreach($aFAQ as $aEachFAQ){
			
			$iId = (int)$aEachFAQ['id'];

 			$this->_oDb->deleteFaqByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin()); 
		}  
 		//[end] delete faq 
  
        // delete views
        bx_import ('BxDolViews');
        $oViews = new BxDolViews($this->_sPrefix, $iEntryId, false);
        $oViews->onObjectDelete();

        // delete forum
        $this->_oDb->deleteForum ($iEntryId);
 
        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		$oAlert->alert();
    }       
      

    function onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_church_join_request', BX_CHURCH_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_church_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_church_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_church_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_church_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_church_join_confirm');
    }

    function isAllowedJoin (&$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;
        return $this->_oPrivacy->check('join', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
		if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_VIEW_CHURCH, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
        // check user group  
        $isAllowed =  $this->_oPrivacy->check('view_church', $aDataEntry['id'], $this->_iProfileId);   
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
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_ADD_CHURCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {
  
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_EDIT_ANY_CHURCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
  
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_DELETE_ANY_CHURCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     
  
    function isAllowedInquire (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_MAKE_INQUIRY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 
	
    function isAllowedClaim (&$aDataEntry, $isPerformAction = false) {
		if (!$this->_oDb->isOwnerAdmin($aDataEntry['author_id']))
            return false;
  
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_MAKE_CLAIM, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function isAllowedSendInvitation (&$aDataEntry) {
        return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) ? true : false;
    }
 
    function isAllowedShare (&$aDataEntry) {
    	return ($aDataEntry[$this->_oDb->_sFieldAllowViewTo] == BX_DOL_PG_ALL);
    }
  
    function isAllowedRate(&$aDataEntry) {        
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedComments(&$aDataEntry) {
      
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }
    
	function isAllowedViewFans(&$aDataEntry) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
        return $this->_oPrivacy->check('view_fans', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;	   
		if (!$this->_iProfileId) 
            return false;  
	 
        if ($this->isAdmin())
            return true;  
		if (!$this->_oDb->isPackageAllowedPhotos($aDataEntry['invoice_no']))
            return false;   
        if (!$this->isMembershipEnabledForImages())
            return false; 
        if ($this->isEntryAdmin($aDataEntry))
            return true; 
 
        return $this->_oPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideos(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if (!$this->_oDb->isPackageAllowedVideos($aDataEntry['invoice_no']))
            return false;  
        if (!$this->isMembershipEnabledForVideos())
            return false;     
        if ($this->isEntryAdmin($aDataEntry))
            return true; 
		 
        return $this->_oPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSounds(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if (!$this->_oDb->isPackageAllowedSounds($aDataEntry['invoice_no']))
            return false;    
        if (!$this->isMembershipEnabledForSounds())
            return false;       
        if ($this->isEntryAdmin($aDataEntry))
            return true; 
 
        return $this->_oPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFiles(&$aDataEntry) {

        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
		if (!$this->_oDb->isPackageAllowedFiles($aDataEntry['invoice_no']))
            return false;    
        if (!$this->isMembershipEnabledForFiles())
            return false;      
        if ($this->isEntryAdmin($aDataEntry))
            return true; 
 
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }
	
    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) return true;    
		
        if (getParam('modzzz_church_author_comments_admin') && $this->isEntryAdmin($aDataEntry))
            return true;
 
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {

        if ($this->isAdmin()) 
            return true;

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('modzzz_church_permalinks') == 'on'));
		 
        return $bEnabled;
    }
 
    function _defineActions () {
        defineMembershipActions(array('church make donation', 'church view donors', 'church purchase', 'church relist', 'church extend','church purchase featured', 'church view church', 'church browse', 'church search', 'church add church', 'church comments delete and edit', 'church edit any church', 'church delete any church', 'church mark as featured', 'church approve church', 'church make claim', 'church make inquiry','church broadcast message','church post reviews','church create faqs'));
    }
 
    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_church_page_title_my_church'));
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
 
    function actionPaypalProcess($iProfileId, $iChurchId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = $_POST;

        if($aData) {
 
			$aDataEntry = $this->_oDb->getEntryById ($iChurchId);
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

			// read the post from PayPal system and add 'cmd'
			$sRequest = 'cmd=_notify-validate';
			foreach ($_POST as $key => $value) {
				$value = urlencode(stripslashes($value));
				$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
				$sRequest .= "&$key=$value";
			}

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPostPurchase(_t('_modzzz_church_purchase_failed')); 
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
  
			//if (($aData['receiver_email'] != trim(getParam('modzzz_church_paypal_email'))) || ($aData['txn_type'] != 'web_accept')) {

			if(!isset($aData['txn_type'])) { 
				$this->actionPostPurchase(_t('_modzzz_church_purchase_failed'));
			}else{ 
				if($this->_oDb->isExistPaypalTransaction($iProfileId, $aData['txn_id'])) { 
					$this -> actionPostPurchase(_t('_modzzz_church_transaction_completed_already', $sRedirectUrl)); 
				} else {
					if( $this->_oDb->saveTransactionRecord($iProfileId, $iChurchId, $aData['txn_id'], 'Paypal Purchase')) { 
						
						$this->_oDb->setItemStatus($iChurchId, 'approved'); 
						
						$this->_oDb->setInvoiceStatus($iChurchId, 'paid');

						$this->actionPostPurchase(_t('_modzzz_church_purchase_success', $sRedirectUrl));
					} else {
						$this -> actionPostPurchase(_t('_modzzz_church_trans_save_failed'));
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

	function initializeCheckout($iChurchId, $fTotalCost, $iQuantity=1, $bFeatured=0) {
  
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	 
		if($bFeatured){
			$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iChurchId;
			$sItemDesc = getParam('modzzz_church_featured_purchase_desc');
 		}else{
			$sNotifyUrl = $this->_oConfig->getPurchaseCallbackUrl() . $this->_iProfileId .'/'. $iChurchId;
			$sItemDesc = getParam('modzzz_church_paypal_item_desc');
		}

		$aDataEntry = $this->_oDb->getEntryById($iChurchId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $aFormData = array_merge($aFormData, array(
			'business' => getParam('modzzz_church_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iChurchId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl() . $sUri,
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Church");
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
        $this->_oTemplate->pageCode(_t('_modzzz_church_post_purchase_header')); 
    }
 
 
    function actionPurchaseFeatured($iChurchId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('modzzz_church_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iChurchId);
		$sTitle = $aDataEntry['title'];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iChurchId,
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
					'caption'  => _t('_modzzz_church_form_caption_title'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_modzzz_church_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_modzzz_church_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_modzzz_church_featured_until')  . ' '. $this->_oTemplate->filterCustomDate($aDataEntry['featured_expiry_date']) : _t('_modzzz_church_not_featured'), 
                ), 
                'quantity' => array(
                    'caption'  => _t('_modzzz_church_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_church_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_modzzz_church_extend_featured') : _t('_modzzz_church_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 

			$fCost =  number_format($iPerDayCost, 2); 
  
			$this->initializeCheckout($iChurchId, $fCost, $oForm->getCleanValue('quantity'), true);  
			return;   
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_church_purchase_featured')); 
    }
 
    function actionPaypalFeaturedProcess($iProfileId, $iChurchId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iChurchId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_modzzz_church_purchase_featured_failed')); 
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
				$this->actionPurchaseFeatured($iChurchId, _t('_modzzz_church_purchase_featured_failed'));
			}else { 
				$fAmount = $this->_getReceivedAmount($aData);
			
				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iChurchId, _t('_modzzz_church_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iChurchId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iChurchId, $iQuantity); 
			   
						$this->actionPurchaseFeatured($iChurchId, _t('_modzzz_church_purchase_success',  $iQuantity));
					} else {
						$this -> actionPurchaseFeatured($iChurchId, _t('_modzzz_church_purchase_featured_failed'));
					}
				}
			}
            
        }
    }
 
    function isAllowedPurchaseFeatured ($aDataEntry, $isPerformAction = false) {
  
		if(getParam("modzzz_church_buy_featured")!='on')
			return false;
 
		if ($this->isAdmin())
            return false;

		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 

    function actionLocal ($sCountry='', $sState='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_church_page_title_local');

		if($sCountry){
			$sTitle .= ' - ' . $this->_oTemplate->getPreListDisplay('Country', $sCountry);
		}
 
		if($sState){
			$sTitle .= ' - ' . $this->_oDb->getStateName($sCountry, $sState); 
		}
 
        $this->_oTemplate->pageCode($sTitle, false, false); 
    } 



    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_CHURCH_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_CHURCH_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_CHURCH_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_CHURCH_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'church photos add', 'church sounds add', 'church videos add', 'church files add'));
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
            'read' => 1,
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
            'read' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
            'post' => $this->isAllowedPostInForum ($aDataEntry, $iMemberId) ? 1 : 0,
        );
  
        return $aTrue;
    }
 
     function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }

    function isAllowedManageFans($aDataEntry) {
        return ($this->isAdmin() || $this->isEntryAdmin($aDataEntry));
    }

    function isFan($aDataEntry, $iProfileId = 0, $isConfirmed = true) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->_oDb->isFan ($aDataEntry['id'], $iProfileId, $isConfirmed) ? true : false;
    }
  
    function isEntryAdmin($aDataEntry, $iProfileId = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }

	/*[BEGIN] premium */
  
    function isAllowedPremium (&$aDataEntry, $isPerformAction = false) {
  
        if (getParam('modzzz_church_paid_active')!='on') 
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
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_PURCHASE, $isPerformAction);
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
        $this->_oTemplate->pageCode(_t('_modzzz_church_page_title_premium'));
	}
 
    function _actionPremiumOLD ($iEntryId) {
  
		//$bPaidChurch = $this->isAllowedPaidChurch (); 
  
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
                    'caption' => _t('_modzzz_church_form_caption_package'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_church_form_err_package'),
                    ),   
                ),  
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_church_continue'),
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
                    'caption' => _t('_modzzz_church_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_church_form_err_package'),
                    ),   
                ),   
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_church_package_desc'),  
                ), 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_church_continue'),
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
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_RELIST, $isPerformAction);
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
        $this->_oTemplate->pageCode(_t('_modzzz_church_page_title_relist'));
	}
 
    function _actionRelistOLD ($iEntryId) {
  
		//$bPaidChurch = $this->isAllowedPaidChurch (); 
  
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
                    'caption' => _t('_modzzz_church_form_caption_package'), 
                    'required' => true,
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_church_form_err_package'),
                    ),   
                ),  
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_church_continue'),
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
                    'caption' => _t('_modzzz_church_form_caption_package'), 
                    'required' => true,
					'attrs' => array(
						'onchange' => "getHtmlData('pkginfo','$sPackageUrl'+this.value)",
					),
                    'checker' => array (
                        'func' => 'avail',
                        'error' => _t ('_modzzz_church_form_err_package'),
                    ),   
                ),  
 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => '<div id="pkginfo">'.$sPackageDesc.'</div>',  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_church_package_desc'),  
                ),  
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_church_continue'),
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
        
        if (getParam('modzzz_church_paid_active')!='on') 
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
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_EXTEND, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 


    function actionExtend ($iEntryId) {

        if (!$aDataEntry = $this->_oDb->getEntryById((int)$iEntryId)) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

 
        if (!$this->isPaidChurch($iEntryId)) {
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
        $this->_oTemplate->pageCode(_t('_modzzz_church_page_title_extend'));
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
                    'caption' => _t('_modzzz_church_form_caption_current_item'),  
					'content'=> $aEntry['title'],
                 ), 

                'package_name' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_church_form_caption_current_package'),  
					'content'=> $aPackage['name'],
                 ),  
 
				'package_price' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_church_form_caption_package_price'),  
					'content'=> getParam("modzzz_church_currency_sign") .' '. $aPackage['price'],
                 ), 

                'package_days' => array( 
                    'type' => 'custom',
                    'caption' => _t('_modzzz_church_form_caption_package_days'),  
					'content'=> $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_modzzz_church_days') : _t('_modzzz_church_expires_never')
                 ), 
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_church_extend'),
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
                    'caption' => _t('_modzzz_church_form_caption_current_item'),  
					'content'=> $aEntry['title'],
                 ), 

 				'package_desc' => array( 
					'type' => 'custom',
                    'content' => $sPackageDesc,  
                    'name' => 'package_desc',
                    'caption' => _t('_modzzz_church_package_desc'),  
                ),   
 
                'submit' => array(
                    'type'  => 'submit',
                    'value' => _t('_modzzz_church_extend'),
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
	/***[END] Extend***************/
 
 
 
 	//[begin] [broadcast]
    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {

        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function actionBroadcast ($iEntryId) {
        $this->_actionBroadcast ($iEntryId, _t('_modzzz_church_page_title_broadcast'), _t('_modzzz_church_msg_broadcast_no_recipients'), _t('_modzzz_church_msg_broadcast_message_sent'));
    }

    function _actionBroadcast ($iEntryId, $sTitle, $sMsgNoRecipients, $sMsgSent) {
		global $tmpl;
		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

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

        if (!$this->isAllowedBroadcast($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

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
            $this->_oTemplate->pageCode($sTitle, true, true);
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

		$oMailBox -> sendMessage($aTemplateVars['BroadcastTitle'], $sMessageBody, $aProfile['ID'], $aComposeSettings); 

    }
	//[begin] [broadcast]



	/******[BEGIN] Donation functions **************************/ 
    function actionDonation ($sAction, $sDonationIdUri, $iProfileId=0, $sMessage='') {
		switch($sAction){
			case 'process': 
				$this->actionDonationProcess ($iProfileId, $sDonationIdUri);
			break; 			
			case 'add': 
				$this->actionDonationAdd ($sDonationIdUri, '_modzzz_church_page_title_donation_add', $sMessage);
			break;  
			case 'browse':
				return $this->actionDonationBrowse ($sDonationIdUri, '_modzzz_church_page_title_donation_browse'); 
			break;  
		}
	}
    
    function actionDonationProcess($iProfileId, $iEntryId) {
        $sPostData = '';
        $sPageContent = '';
  
        $aData = $_POST;

        if($aData) {
  
			$aDataEntry = $this->_oDb->getEntryById ($iEntryId);
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

			// read the post from PayPal system and add 'cmd'
			$sRequest = 'cmd=_notify-validate';
			foreach ($_POST as $key => $value) {
				$value = urlencode(stripslashes($value));
				$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
				$sRequest .= "&$key=$value";
			}

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
				$this->actionDonation('add', $iEntryId, _t('_modzzz_church_donation_failed')); 
 				return;
			}

        	array_walk($aResponse['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        	if(strcmp($aResponse['content'][0], "INVALID") == 0){
				$this->actionDonation('add', $iEntryId, _t('_modzzz_church_donation_err_wrong_transaction'));
 				return; 
        	}

			if(strcmp($aResponse['content'][0], "VERIFIED") != 0){
				$this->actionDonation('add', $iEntryId, _t('_modzzz_church_donation_err_wrong_verification_status'));
 				return;  
			}
	   
			if(isset($aData['txn_type'])) { 
 
				$fAmount = $this->_getReceivedAmount($aData);

				if(!$this->_oDb->isExistDonationTransaction($iProfileId, $aData['txn_id'])){  
					if( $this->_oDb->saveDonationRecord($iEntryId, $iProfileId, $fAmount, $aData['txn_id'], $aData['business'], $aData['custom'], 'Paypal Donation')) {

						$this->_oDb->incrementDonation($iEntryId, $fAmount); 
			   
						$this->onEventDonate($iEntryId, $aDataEntry, $aData, $fAmount);

						header ('Location:' . $sRedirectUrl);  
			 
					}else{ 
						header ('Location:' . $sRedirectUrl); 
					}
				}else{ 
					header ('Location:' . $sRedirectUrl); 
				}
			}else{
				$this->actionDonation('add', $iEntryId, 0, _t('_modzzz_church_donation_failed')); 
			} 
        }else{
			$this->actionDonation('add', $iEntryId, 0, _t('_modzzz_church_donation_failed')); 
		}

    }
 
    function actionDonationBrowse ($sUri, $sTitle) {
      
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
			_t($sTitle) => '',
		));


        bx_import ('DonationPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DonationPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionDonationAdd ($iEntryId, $sTitle, $sMessage='') {
   
		if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        if (!$this->isAllowedDonate($aDataEntry)) {
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


        $this->_addDonationForm($iEntryId, $sMessage);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addDonationForm ($iEntryId, $sMessage='') { 
 
        bx_import ('DonationFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DonationFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iEntryId, $sMessage);
        $oForm->initChecker();
 
        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('amount')) { 
			 
			$sCustomStr = $oForm->getCleanValue('anonymous') .'|'. $oForm->getCleanValue('first_name') .'|'. $oForm->getCleanValue('last_name');
	  
			header('location: ' . $this->_oDb->generatePaymentUrl($iEntryId, $oForm->getCleanValue('amount'), $sCustomStr, $this->_iProfileId));
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
	function onEventDonate($iEntryId, $aDataEntry, $aData, $fAmount=0){
 
		//thank donor 
		$this->_oDb->onDonationAlert('thankyou', $iEntryId, $this->_iProfileId, $aDataEntry[$this->_oDb->_sFieldAuthorId], $aData);  
		
		//notify owner
		$this->_oDb->onDonationAlert('notify', $iEntryId, $this->_iProfileId, $aDataEntry[$this->_oDb->_sFieldAuthorId], $aData, $fAmount);   
	}
 
    function isAllowedDonate ($aDataEntry) {
        
		if(!$aDataEntry['paypal']) return false;
              
		return $this->isAllowedAcceptDonation ($aDataEntry['author_id']);
	}

    function isAllowedAcceptDonation ($iProfileId, $isPerformAction=false) {

		// admin always have access
        if ($this->isAdmin()) return true;
             
		$this->_defineActions();
		$aCheck = checkAction($iProfileId, BX_CHURCH_MAKE_DONATION, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;  
	} 

    function isAllowedViewDonors ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
		if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_VIEW_DONORS, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
        // check user group  
        return $this->_oPrivacy->check('view_donors', $aDataEntry['id'], $this->_iProfileId);  
    }

	/******[END] Donation functions **************************/ 


	/******[BEGIN] News functions **************************/ 
    function actionNews ($sAction, $sNewsIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionNewsAdd ($sNewsIdUri, '_modzzz_church_page_title_news_add');
			break;
			case 'edit':
				$this->actionNewsEdit ($sNewsIdUri, '_modzzz_church_page_title_news_edit');
			break;
			case 'delete':
				$this->actionNewsDelete ($sNewsIdUri, _t('_modzzz_church_msg_church_news_was_deleted'));
			break;
			case 'view':
				$this->actionNewsView ($sNewsIdUri, _t('_modzzz_church_msg_pending_news_approval')); 
			break; 
			case 'browse':
				return $this->actionNewsBrowse ($sNewsIdUri, '_modzzz_church_page_title_news_browse'); 
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
		
		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));



        bx_import ('NewsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
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
		$iEntryId = (int)$aNewsEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aNewsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aNewsEntry['title'] => '',
		));


        if ((!$this->_iProfileId || $aNewsEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableNews, $aNewsEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   

        $this->_oTemplate->pageStart();
  
        bx_import ('NewsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsPageView';
        $oPage = new $sClass ($this, $aNewsEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('NewsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aNewsEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aNewsEntry['title'], false, false); 
    }


    function actionNewsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aNewsEntry = $this->_oDb->getNewsEntryById($iEntryId);
		$iNewsId = (int)$aNewsEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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
			$aNewsEntry['title'] => '',
		));


        bx_import ('NewsFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'NewsFormEdit';
        $oForm = new $sClass ($this, $aNewsEntry['uri'], $iNewsId,  $iEntryId, $aNewsEntry);
  
        $oForm->initChecker($aNewsEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'news_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aNewsEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle, $aNewsEntry['title']));  
    }

    function actionNewsDelete ($iNewsId, $sMsgSuccess) {

		$aNewsEntry = $this->_oDb->getNewsEntryById($iNewsId);
		$iEntryId = (int)$aNewsEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteNewsByIdAndOwner($iNewsId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventNewsDeleted ($iNewsId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionNewsAdd ($iNewsId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
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
			_t($sTitle) => '',
		));


        $this->_addNewsForm($iNewsId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
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
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
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

        // delete associated tags and categories 
        //$this->reparseNewsTags ($iEntryId);
        //$this->reparseNewsCategories ($iEntryId);

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

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sNewsPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }    
 
    /*******[END - News Functions] ******************************/



	/******[BEGIN] Event functions **************************/ 
    function actionEvent ($sAction, $sChurchIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionEventAdd ($sChurchIdUri, '_modzzz_church_page_title_event_add');
			break;
			case 'edit':
				$this->actionEventEdit ($sChurchIdUri, '_modzzz_church_page_title_event_edit');
			break;
			case 'delete':
				$this->actionEventDelete ($sChurchIdUri, _t('_modzzz_church_msg_church_event_was_deleted'));
			break;
			case 'view':
				$this->actionEventView ($sChurchIdUri, _t('_modzzz_church_msg_pending_event_approval')); 
			break; 
			case 'browse':
				return $this->actionEventBrowse ($sChurchIdUri, '_modzzz_church_page_title_event_browse'); 
			break;  
		}
	}
    
    function actionEventBrowse ($sUri, $sTitle) {
       
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
			_t($sTitle) => '',
		));

        bx_import ('EventPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionEventView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aEventEntry = $this->_oDb->getEventEntryByUri($sUri);
		$iEntryId = (int)$aEventEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aEventEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

 		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aEventEntry['title'] => '',
		));

        if ((!$this->_iProfileId || $aEventEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableEvent, $aEventEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }  

        $this->_oTemplate->pageStart();
  
        bx_import ('EventPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventPageView';
        $oPage = new $sClass ($this, $aEventEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('EventCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aEventEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aEventEntry['title'], false, false); 
    }
 
    function actionEventEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aEventEntry = $this->_oDb->getEventEntryById($iEntryId);
		$iChurchId = (int)$aEventEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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
				
        bx_import ('EventFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormEdit';
        $oForm = new $sClass ($this, $aEventEntry['uri'], $iChurchId,  $iEntryId, $aEventEntry);
  
        $oForm->initChecker($aEventEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'event_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
  
  				$this->onEventSubItemChanged ('event', $iEntryId, $sStatus, $aDataEntry);

                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/' . $aEventEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle));  
    }

    function actionEventDelete ($iEventId, $sMsgSuccess) {

		$aEventEntry = $this->_oDb->getEventEntryById($iEventId);
		$iChurchId = (int)$aEventEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iChurchId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteEventByIdAndOwner($iEventId, $iChurchId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventEventDeleted ($iEventId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionEventAdd ($iChurchId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
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
			_t($sTitle) => '',
		));

        $this->_addEventForm($iChurchId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle));  
    }
 
    function _addEventForm ($iChurchId) { 
 
        bx_import ('EventFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iChurchId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'event_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getEventEntryById($iEntryId);
    
				$this->onEventSubItemCreate ('event', $iEntryId, $sStatus, $aDataEntry);
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventEventDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseEventTags ($iEntryId);
        //$this->reparseEventCategories ($iEntryId);

        // delete votings
        bx_import('EventVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventVoting';
        $oVoting = new $sClass ($this->_oDb->_sEventPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('EventCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventCmts';
        $oCmts = new $sClass ($this->_oDb->_sEventPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_event', $iEntryId));

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sEventPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }   
 
    /*******[END - EVENT Functions] ******************************/


	/******[BEGIN] Members functions **************************/ 
    function actionMembers ($sAction, $sMembersIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionMembersAdd ($sMembersIdUri, '_modzzz_church_page_title_members_add');
			break;
			case 'edit':
				$this->actionMembersEdit ($sMembersIdUri, '_modzzz_church_page_title_members_edit');
			break;
			case 'delete':
				$this->actionMembersDelete ($sMembersIdUri, _t('_modzzz_church_msg_church_members_was_deleted'));
			break;
			case 'view':
				$this->actionMembersView ($sMembersIdUri, _t('_modzzz_church_msg_pending_members_approval')); 
			break; 
			case 'browse':
				return $this->actionMembersBrowse ($sMembersIdUri, '_modzzz_church_page_title_members_browse'); 
			break;  
		}
	}
    
    function actionMembersBrowse ($sUri, $sTitle) {
      
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
			_t($sTitle) => '',
		));


        bx_import ('MembersPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MembersPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionMembersView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aMembersEntry = $this->_oDb->getMembersEntryByUri($sUri);
		$iEntryId = (int)$aMembersEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aMembersEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aMembersEntry['title'] => '',
		));

        if ((!$this->_iProfileId || $aMembersEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableMembers, $aMembersEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('MembersPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MembersPageView';
        $oPage = new $sClass ($this, $aMembersEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('MembersCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MembersCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aMembersEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aMembersEntry['title'], false, false); 
    }
 
    function actionMembersEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aMembersEntry = $this->_oDb->getMembersEntryById($iEntryId);
		$iMembersId = (int)$aMembersEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iMembersId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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
			$aMembersEntry['title'] => '',
		));		 

        bx_import ('MembersFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MembersFormEdit';
        $oForm = new $sClass ($this, $aMembersEntry['uri'], $iMembersId,  $iEntryId, $aMembersEntry);
  
        $oForm->initChecker($aMembersEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'members_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableMembersMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'members/view/' . $aMembersEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aMembersEntry['title']);  
    }

    function actionMembersDelete ($iMembersId, $sMsgSuccess) {

		$aMembersEntry = $this->_oDb->getMembersEntryById($iMembersId);
		$iEntryId = (int)$aMembersEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iMembersId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iMembersId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteMembersByIdAndOwner($iMembersId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventMembersDeleted ($iMembersId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iMembersId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iMembersId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionMembersAdd ($iMembersId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iMembersId))) {
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
			_t($sTitle) => '',
		));

        $this->_addMembersForm($iMembersId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addMembersForm ($iMembersId) { 
 
        bx_import ('MembersFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MembersFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iMembersId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'members_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableMembersMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getMembersEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'members/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventMembersDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseMembersTags ($iEntryId);
        //$this->reparseMembersCategories ($iEntryId);

        // delete votings
        bx_import('MembersVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MembersVoting';
        $oVoting = new $sClass ($this->_oDb->_sMembersPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('MembersCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MembersCmts';
        $oCmts = new $sClass ($this->_oDb->_sMembersPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sMembersPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Members Functions] ******************************/


	/******[BEGIN] Branches functions **************************/ 
    function actionBranches ($sAction, $sBranchesIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionBranchesAdd ($sBranchesIdUri, '_modzzz_church_page_title_branches_add');
			break;
			case 'edit':
				$this->actionBranchesEdit ($sBranchesIdUri, '_modzzz_church_page_title_branches_edit');
			break;
			case 'delete':
				$this->actionBranchesDelete ($sBranchesIdUri, _t('_modzzz_church_msg_church_branches_was_deleted'));
			break;
			case 'view':
				$this->actionBranchesView ($sBranchesIdUri, _t('_modzzz_church_msg_pending_branches_approval')); 
			break; 
			case 'browse':
				return $this->actionBranchesBrowse ($sBranchesIdUri, '_modzzz_church_page_title_branches_browse'); 
			break;  
		}
	}
    
    function actionBranchesBrowse ($sUri, $sTitle) {
      
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
			_t($sTitle) => '',
		));

        bx_import ('BranchesPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BranchesPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionBranchesView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aBranchesEntry = $this->_oDb->getBranchesEntryByUri($sUri);
		$iEntryId = (int)$aBranchesEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aBranchesEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aBranchesEntry['title'] => '',
		));

        if ((!$this->_iProfileId || $aBranchesEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableBranches, $aBranchesEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('BranchesPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BranchesPageView';
        $oPage = new $sClass ($this, $aBranchesEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('BranchesCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BranchesCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aBranchesEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aBranchesEntry['title'], false, false); 
    }
 
    function actionBranchesEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aBranchesEntry = $this->_oDb->getBranchesEntryById($iEntryId);
		$iBranchesId = (int)$aBranchesEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iBranchesId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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

        bx_import ('BranchesFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BranchesFormEdit';
        $oForm = new $sClass ($this, $aBranchesEntry['uri'], $iBranchesId,  $iEntryId, $aBranchesEntry);
  
        $oForm->initChecker($aBranchesEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'branches_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBranchesMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
  
 				$this->onEventSubItemChanged ('branches', $iEntryId, $sStatus, $aDataEntry);

                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'branches/view/' . $aBranchesEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aBranchesEntry['title']);  
    }

    function actionBranchesDelete ($iBranchesId, $sMsgSuccess) {

		$aBranchesEntry = $this->_oDb->getBranchesEntryById($iBranchesId);
		$iEntryId = (int)$aBranchesEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iBranchesId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iBranchesId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteBranchesByIdAndOwner($iBranchesId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventBranchesDeleted ($iBranchesId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iBranchesId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iBranchesId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionBranchesAdd ($iBranchesId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iBranchesId))) {
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
			_t($sTitle) => '',
		));

        $this->_addBranchesForm($iBranchesId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addBranchesForm ($iBranchesId) { 
 
        bx_import ('BranchesFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BranchesFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iBranchesId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'branches_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBranchesMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getBranchesEntryById($iEntryId);
    
				$this->onEventSubItemCreate ('branches', $iEntryId, $sStatus, $aDataEntry);
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'branches/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventBranchesDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseBranchesTags ($iEntryId);
        //$this->reparseBranchesCategories ($iEntryId);

        // delete votings
        bx_import('BranchesVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BranchesVoting';
        $oVoting = new $sClass ($this->_oDb->_sBranchesPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('BranchesCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'BranchesCmts';
        $oCmts = new $sClass ($this->_oDb->_sBranchesPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        if (BxDolModule::getInstance('BxWmapModule'))
            BxDolService::call('wmap', 'response_entry_delete', array($this->_oConfig->getUri().'_branches', $iEntryId));


        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sBranchesPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Branches Functions] ******************************/

	/******[BEGIN] Ministries functions **************************/ 
    function actionMinistries ($sAction, $sMinistriesIdUri) {
		switch($sAction){
			case 'add': 
				$this->actionMinistriesAdd ($sMinistriesIdUri, '_modzzz_church_page_title_ministries_add');
			break;
			case 'edit':
				$this->actionMinistriesEdit ($sMinistriesIdUri, '_modzzz_church_page_title_ministries_edit');
			break;
			case 'delete':
				$this->actionMinistriesDelete ($sMinistriesIdUri, _t('_modzzz_church_msg_church_ministries_was_deleted'));
			break;
			case 'view':
				$this->actionMinistriesView ($sMinistriesIdUri, _t('_modzzz_church_msg_pending_ministries_approval')); 
			break; 
			case 'browse':
				return $this->actionMinistriesBrowse ($sMinistriesIdUri, '_modzzz_church_page_title_ministries_browse'); 
			break;  
		}
	}
    
    function actionMinistriesBrowse ($sUri, $sTitle) {
      
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
			_t($sTitle) => '',
		));

        bx_import ('MinistriesPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MinistriesPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionMinistriesView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aMinistriesEntry = $this->_oDb->getMinistriesEntryByUri($sUri);
		$iEntryId = (int)$aMinistriesEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aMinistriesEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aMinistriesEntry['title'] => '',
		));

        if ((!$this->_iProfileId || $aMinistriesEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableMinistries, $aMinistriesEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('MinistriesPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MinistriesPageView';
        $oPage = new $sClass ($this, $aMinistriesEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('MinistriesCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MinistriesCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aMinistriesEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aMinistriesEntry['title'], false, false); 
    }
 
    function actionMinistriesEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aMinistriesEntry = $this->_oDb->getMinistriesEntryById($iEntryId);
		$iMinistriesId = (int)$aMinistriesEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iMinistriesId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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
			

        bx_import ('MinistriesFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MinistriesFormEdit';
        $oForm = new $sClass ($this, $aMinistriesEntry['uri'], $iMinistriesId,  $iEntryId, $aMinistriesEntry);
  
        $oForm->initChecker($aMinistriesEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'ministries_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableMinistriesMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'ministries/view/' . $aMinistriesEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aMinistriesEntry['title']);  
    }

    function actionMinistriesDelete ($iMinistriesId, $sMsgSuccess) {

		$aMinistriesEntry = $this->_oDb->getMinistriesEntryById($iMinistriesId);
		$iEntryId = (int)$aMinistriesEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iMinistriesId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iMinistriesId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteMinistriesByIdAndOwner($iMinistriesId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventMinistriesDeleted ($iMinistriesId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iMinistriesId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iMinistriesId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionMinistriesAdd ($iMinistriesId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iMinistriesId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));		 

        $this->_addMinistriesForm($iMinistriesId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addMinistriesForm ($iMinistriesId) { 
 
        bx_import ('MinistriesFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MinistriesFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iMinistriesId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'ministries_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableMinistriesMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId  
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getMinistriesEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'ministries/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function onEventMinistriesDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseMinistriesTags ($iEntryId);
        //$this->reparseMinistriesCategories ($iEntryId);

        // delete votings
        bx_import('MinistriesVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MinistriesVoting';
        $oVoting = new $sClass ($this->_oDb->_sMinistriesPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('MinistriesCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MinistriesCmts';
        $oCmts = new $sClass ($this->_oDb->_sMinistriesPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sMinistriesPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Ministries Functions] ******************************/

	/******[BEGIN] Sermon functions **************************/ 
    function actionSermon ($sAction, $sChurchIdUri) {
		
		switch($sAction){
			case 'add': 
				$this->actionSermonAdd ($sChurchIdUri, '_modzzz_church_page_title_sermon_add');
			break;
			case 'edit':
				$this->actionSermonEdit ($sChurchIdUri, '_modzzz_church_page_title_sermon_edit');
			break;
			case 'delete':
				$this->actionSermonDelete ($sChurchIdUri, _t('_modzzz_church_msg_church_sermon_was_deleted'));
			break;
			case 'view':
				$this->actionSermonView ($sChurchIdUri, _t('_modzzz_church_msg_pending_sermon_approval')); 
			break; 
			case 'browse':
				return $this->actionSermonBrowse ($sChurchIdUri, '_modzzz_church_page_title_sermon_browse'); 
			break;  
		}
	}
    
    function actionSermonBrowse ($sUri, $sTitle) {
       
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
			_t($sTitle) => '',
		));	

        bx_import ('SermonPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SermonPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionSermonView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aSermonEntry = $this->_oDb->getSermonEntryByUri($sUri);
		$iEntryId = (int)$aSermonEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aSermonEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aSermonEntry['title'] => '',
		));	

        if ((!$this->_iProfileId || $aSermonEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableSermon, $aSermonEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   

        $this->_oTemplate->pageStart();
   
        bx_import ('SermonPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SermonPageView';
        $oPage = new $sClass ($this, $aSermonEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }
 
        echo $oPage->getCode();

        bx_import('SermonCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SermonCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aSermonEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aSermonEntry['title'], false, false); 
    }
 
    function actionSermonEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aSermonEntry = $this->_oDb->getSermonEntryById($iEntryId);
		$iChurchId = (int)$aSermonEntry['church_id'];

        if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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

        bx_import ('SermonFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SermonFormEdit';
        $oForm = new $sClass ($this, $aSermonEntry['uri'], $iChurchId,  $iEntryId, $aSermonEntry);
  
        $oForm->initChecker($aSermonEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'sermon_main';
	    $this->_oDb->_sFieldId = 'id';
	    $this->_oDb->_sFieldUri = 'uri';
	    $this->_oDb->_sFieldTitle = 'title';
	    $this->_oDb->_sFieldDescription = 'desc'; 
	    $this->_oDb->_sFieldThumb = 'thumb';
	    $this->_oDb->_sFieldStatus = 'status'; 
	    $this->_oDb->_sFieldCreated = 'created';

	    $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSermonMediaPrefix;

            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sermon/view/' . $aSermonEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aSermonEntry['title']);  
    }

    function actionSermonDelete ($iSermonId, $sMsgSuccess) {

		$aSermonEntry = $this->_oDb->getSermonEntryById($iSermonId);
		$iChurchId = (int)$aSermonEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iChurchId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iSermonId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iSermonId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteSermonByIdAndOwner($iSermonId, $iChurchId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventSermonDeleted ($iSermonId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iSermonId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iSermonId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionSermonAdd ($iChurchId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

	if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
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
			_t($sTitle) => '',
		));		 

        $this->_addSermonForm($iChurchId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addSermonForm ($iChurchId) { 
 
        bx_import ('SermonFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SermonFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iChurchId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
	    $sStatus = 'approved';

            $this->_oDb->_sTableMain = 'sermon_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSermonMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
		$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getSermonEntryById($iEntryId);
    
		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sermon/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventSermonDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseSermonTags ($iEntryId);
        //$this->reparseSermonCategories ($iEntryId);

        // delete votings
        bx_import('SermonVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SermonVoting';
        $oVoting = new $sClass ($this->_oDb->_sSermonPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('SermonCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SermonCmts';
        $oCmts = new $sClass ($this->_oDb->_sSermonPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sSermonPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
	//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
	//$oAlert->alert();
    }        
 
    /*******[END] - Sermon Functions] ******************************/

    /******[BEGIN] Sermon functions **************************/ 
    function actionDoctrine ($sAction, $sChurchIdUri) {
		
		switch($sAction){
			case 'add': 
				$this->actionDoctrineAdd ($sChurchIdUri, '_modzzz_church_page_title_doctrine_add');
			break;
			case 'edit':
				$this->actionDoctrineEdit ($sChurchIdUri, '_modzzz_church_page_title_doctrine_edit');
			break;
			case 'delete':
				$this->actionDoctrineDelete ($sChurchIdUri, _t('_modzzz_church_msg_church_doctrine_was_deleted'));
			break;
			case 'view':
				$this->actionDoctrineView ($sChurchIdUri, _t('_modzzz_church_msg_pending_doctrine_approval')); 
			break; 
			case 'browse':
				return $this->actionDoctrineBrowse ($sChurchIdUri, '_modzzz_church_page_title_doctrine_browse'); 
			break;  
		}
	}
    
    function actionDoctrineBrowse ($sUri, $sTitle) {
       
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
			_t($sTitle) => '',
		));	

        bx_import ('DoctrinePageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DoctrinePageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionDoctrineView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aDoctrineEntry = $this->_oDb->getDoctrineEntryByUri($sUri);
		$iEntryId = (int)$aDoctrineEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aDoctrineEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aDoctrineEntry['title'] => '',
		));	

        if ((!$this->_iProfileId || $aDoctrineEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableDoctrine, $aDoctrineEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   

        $this->_oTemplate->pageStart();
   
        bx_import ('DoctrinePageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DoctrinePageView';
        $oPage = new $sClass ($this, $aDoctrineEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }
 
        echo $oPage->getCode();

        bx_import('DoctrineCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DoctrineCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aDoctrineEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aDoctrineEntry['title'], false, false); 
    }
 
    function actionDoctrineEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aDoctrineEntry = $this->_oDb->getDoctrineEntryById($iEntryId);
		$iChurchId = (int)$aDoctrineEntry['church_id'];

        if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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

        bx_import ('DoctrineFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DoctrineFormEdit';
        $oForm = new $sClass ($this, $aDoctrineEntry['uri'], $iChurchId,  $iEntryId, $aDoctrineEntry);
  
        $oForm->initChecker($aDoctrineEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'doctrine_main';
	    $this->_oDb->_sFieldId = 'id';
	    $this->_oDb->_sFieldUri = 'uri';
	    $this->_oDb->_sFieldTitle = 'title';
	    $this->_oDb->_sFieldDescription = 'desc'; 
	    $this->_oDb->_sFieldThumb = 'thumb';
	    $this->_oDb->_sFieldStatus = 'status'; 
	    $this->_oDb->_sFieldCreated = 'created';

	    $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableDoctrineMediaPrefix;

            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'doctrine/view/' . $aDoctrineEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDoctrineEntry['title']);  
    }

    function actionDoctrineDelete ($iDoctrineId, $sMsgSuccess) {

		$aDoctrineEntry = $this->_oDb->getDoctrineEntryById($iDoctrineId);
		$iChurchId = (int)$aDoctrineEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iChurchId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iDoctrineId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iDoctrineId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteDoctrineByIdAndOwner($iDoctrineId, $iChurchId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventDoctrineDeleted ($iDoctrineId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iDoctrineId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iDoctrineId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionDoctrineAdd ($iChurchId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

		if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
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
			_t($sTitle) => '',
		));		 

        $this->_addDoctrineForm($iChurchId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addDoctrineForm ($iChurchId) { 
 
        bx_import ('DoctrineFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DoctrineFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iChurchId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
	    $sStatus = 'approved';

            $this->_oDb->_sTableMain = 'doctrine_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableDoctrineMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
		$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getDoctrineEntryById($iEntryId);
    
		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'doctrine/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventDoctrineDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseDoctrineTags ($iEntryId);
        //$this->reparseDoctrineCategories ($iEntryId);

        // delete votings
        bx_import('DoctrineVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DoctrineVoting';
        $oVoting = new $sClass ($this->_oDb->_sDoctrinePrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('DoctrineCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DoctrineCmts';
        $oCmts = new $sClass ($this->_oDb->_sDoctrinePrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sDoctrinePrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
	//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
	//$oAlert->alert();
    }        
 
    /*******[END] - Sermon Functions] ******************************/

    /******[BEGIN] Sermon functions **************************/ 
    function actionStaff ($sAction, $sChurchIdUri) {
		
		switch($sAction){
			case 'add': 
				$this->actionStaffAdd ($sChurchIdUri, '_modzzz_church_page_title_staff_add');
			break;
			case 'edit':
				$this->actionStaffEdit ($sChurchIdUri, '_modzzz_church_page_title_staff_edit');
			break;
			case 'delete':
				$this->actionStaffDelete ($sChurchIdUri, _t('_modzzz_church_msg_church_staff_was_deleted'));
			break;
			case 'view':
				$this->actionStaffView ($sChurchIdUri, _t('_modzzz_church_msg_pending_staff_approval')); 
			break; 
			case 'browse':
				return $this->actionStaffBrowse ($sChurchIdUri, '_modzzz_church_page_title_staff_browse'); 
			break;  
		}
	}
    
    function actionStaffBrowse ($sUri, $sTitle) {
       
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
			_t($sTitle) => '',
		));	

        bx_import ('StaffPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionStaffView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aStaffEntry = $this->_oDb->getStaffEntryByUri($sUri);
		$iEntryId = (int)$aStaffEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aStaffEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aStaffEntry['title'] => '',
		));	

        if ((!$this->_iProfileId || $aStaffEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableStaff, $aStaffEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   

        $this->_oTemplate->pageStart();
   
        bx_import ('StaffPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffPageView';
        $oPage = new $sClass ($this, $aStaffEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }
 
        echo $oPage->getCode();

        bx_import('StaffCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aStaffEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aStaffEntry['title'], false, false); 
    }
 
    function actionStaffEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aStaffEntry = $this->_oDb->getStaffEntryById($iEntryId);
		$iChurchId = (int)$aStaffEntry['church_id'];

        if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedEdit($aDataEntry)) {
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

        bx_import ('StaffFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffFormEdit';
        $oForm = new $sClass ($this, $aStaffEntry['uri'], $iChurchId,  $iEntryId, $aStaffEntry);
  
        $oForm->initChecker($aStaffEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'staff_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableStaffMediaPrefix;

			if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
  
                $this->isAllowedEdit($aDataEntry, true); // perform action
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/view/' . $aStaffEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aStaffEntry['title']);  
    }

    function actionStaffDelete ($iStaffId, $sMsgSuccess) {

		$aStaffEntry = $this->_oDb->getStaffEntryById($iStaffId);
		$iChurchId = (int)$aStaffEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iChurchId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iStaffId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iStaffId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteStaffByIdAndOwner($iStaffId, $iChurchId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventStaffDeleted ($iStaffId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iStaffId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iStaffId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionStaffAdd ($iChurchId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

		if (!($aDataEntry = $this->_oDb->getEntryById($iChurchId))) {
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
			_t($sTitle) => '',
		));	
  
        $this->_addStaffForm($iChurchId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addStaffForm ($iChurchId) { 
 
        bx_import ('StaffFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iChurchId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
	    $sStatus = 'approved';

            $this->_oDb->_sTableMain = 'staff_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableStaffMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getStaffEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventStaffDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseStaffTags ($iEntryId);
        //$this->reparseStaffCategories ($iEntryId);

        // delete votings
        bx_import('StaffVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffVoting';
        $oVoting = new $sClass ($this->_oDb->_sStaffPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('StaffCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffCmts';
        $oCmts = new $sClass ($this->_oDb->_sStaffPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sStaffPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
	//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
	//$oAlert->alert();
    }        
 
    /*******[END] - Staff Functions] ******************************/

 
	/******[BEGIN] Faq functions **************************/ 
     
	function actionFaq ($sAction, $sIdUri) {
 
		switch($sAction){
			case 'add': 
				$this->actionFaqAdd ($sIdUri, '_modzzz_church_page_title_faq_add');
			break;
			case 'edit':
				$this->actionFaqEdit ($sIdUri, '_modzzz_church_page_title_faq_edit');
			break;
			case 'delete':
				$this->actionFaqDelete ($sIdUri, _t('_modzzz_church_msg_church_faq_was_deleted'));
			break;
			case 'view':
				$this->actionFaqView ($sIdUri, _t('_modzzz_church_msg_pending_faq_approval')); 
			break; 
			case 'browse': 
				return $this->actionFaqBrowse ($sIdUri, '_modzzz_church_page_title_faq_browse'); 
			break;  
		}
	}
    
    function actionFaqBrowse ($sUri, $sTitle) {
       
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
			_t($sTitle) => '',
		));	

        bx_import ('FaqPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FaqPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title'], false, false);  
    }
 
    function actionFaqView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aFaqEntry = $this->_oDb->getFaqEntryByUri($sUri);
		$iEntryId = (int)$aFaqEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aFaqEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
 		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aFaqEntry[$this->_oDb->_sFieldTitle] => '',
		));	

        bx_import ('FaqPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FaqPageView';
        $oPage = new $sClass ($this, $aFaqEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();
  
        $this->_oTemplate->setPageDescription (substr(strip_tags($aDataEntry[$this->_oDb->_sFieldDescription]), 0, 255));
        $this->_oTemplate->addPageKeywords ($aDataEntry[$this->_oDb->_sFieldTags]);

        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->addCss ('unit_fan.css');
 
        $this->_oTemplate->pageCode($aFaqEntry[$this->_oDb->_sFieldTitle], false, false); 
    }
 
    function actionFaqEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aFaqEntry = $this->_oDb->getFaqEntryById($iEntryId);
		$iFaqId = (int)$aFaqEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iFaqId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        if (!$this->isAllowedSubEdit($aDataEntry, $aFaqEntry)) {
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

        bx_import ('FaqFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FaqFormEdit';
        $oForm = new $sClass ($this, $aFaqEntry[$this->_oDb->_sFieldAuthorId], $iFaqId,  $iEntryId, $aFaqEntry);
  
        $oForm->initChecker($aFaqEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'faq_main';
  
            if ($oForm->update ($iEntryId, $aValsAdd)) {
   
                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventSubItemChanged ('faq', $iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'faq/view/' . $aFaqEntry[$this->_oDb->_sFieldUri]);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title']);  
    }

    function actionFaqDelete ($iFaqId, $sMsgSuccess) {

		$aFaqEntry = $this->_oDb->getFaqEntryById($iFaqId);
		$iEntryId = (int)$aFaqEntry['church_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iFaqId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aDataEntry, $aFaqEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iFaqId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteFaqByIdAndOwner($iFaqId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventFaqDeleted ($iFaqId, $aFaqEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
  
            $sJQueryJS = genAjaxyPopupJS($iFaqId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iFaqId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionFaqAdd ($iEntryId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
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
			_t($sTitle) => '',
		));	

        $this->_addFaqForm($iEntryId);

		$this->_oTemplate->addJs ('church.js'); 
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title']);  
    }
 
    function _addFaqForm ($iEntryId) { 
 
        bx_import ('FaqFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FaqFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iEntryId);
        $oForm->initChecker();

		$aDataEntry = $this->_oDb->getEntryById($iEntryId);

        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('church_id')) {
 
			$sStatus = 'approved';
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
  
			$iFaqEntryId = $this->_oDb->getFaqByChurch($oForm->getCleanValue('church_id'));
			if(!$iFaqEntryId){
				$this->_oDb->_sTableMain = 'faq_main'; 
				$iFaqEntryId = $oForm->insert ($aValsAdd);
				$this->_oDb->_sTableMain = 'main';
			}

			$this->_oDb->addFaqItems($iFaqEntryId, $oForm->getCleanValue('church_id'));
			$this->_oDb->removeFaqItem(); 

			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
		  
			header ('Location:' . $sRedirectUrl);
			exit;  
  		                          
        } else { 
            echo $oForm->getCode (); 
        }
    }
 
    function onEventFaqDeleted ($iEntryId, $aDataEntry = array()) {
  
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
   function isAllowedCreateFaqs (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
 
        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_CREATE_FAQS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    }

    /*******[END - Faq Functions] ******************************/

	/******[BEGIN] Review functions **************************/ 
     
	function actionReview ($sAction, $sIdUri ) {
 
		switch($sAction){
			case 'add': 
				$this->actionReviewAdd ($sIdUri, '_modzzz_church_page_title_review_add');
			break;
			case 'edit':
				$this->actionReviewEdit ($sIdUri, '_modzzz_church_page_title_review_edit');
			break;
			case 'delete':
				$this->actionReviewDelete ($sIdUri, _t('_modzzz_church_msg_church_review_was_deleted'));
			break;
			case 'view':
				$this->actionReviewView ($sIdUri, _t('_modzzz_church_msg_pending_review_approval')); 
			break; 
			case 'browse': 
				return $this->actionReviewBrowse ($sIdUri, '_modzzz_church_page_title_review_browse'); 
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
			_t($sTitle) => '',
		));	

        bx_import ('ReviewPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ReviewPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title'], false, false);  
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
		$iEntryId = (int)$aReviewEntry['church_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aReviewEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
  		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aReviewEntry[$this->_oDb->_sFieldTitle] => '',
		));	

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
 
        $this->_oTemplate->pageCode($aReviewEntry[$this->_oDb->_sFieldTitle], false, false);

        bx_import ('BxDolViews');
        new BxDolViews($this->_oDb->_sReviewPrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }


    function actionReviewEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aReviewEntry = $this->_oDb->getReviewEntryById($iEntryId);
		$iReviewId = (int)$aReviewEntry['church_id'];
  
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
  		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));	

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

                $this->onEventSubItemChanged ('review', $iEntryId, $sStatus);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry['title']);  
    }

    function actionReviewDelete ($iReviewId, $sMsgSuccess) {

		$aReviewEntry = $this->_oDb->getReviewEntryById($iReviewId);
		$iEntryId = (int)$aReviewEntry['church_id'];
  
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
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }


		if (!($aDataEntry = $this->_oDb->getEntryById($iReviewId))) {
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

                $this->isAllowedAdd(true); // perform action                 
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getReviewEntryById($iEntryId);
                $this->onEventSubItemCreate('review', $iEntryId, $sStatus, $aDataEntry);
   
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
        bx_import ('BxDolViews');
        $oViews = new BxDolViews($this->_oDb->_sReviewPrefix, $iEntryId, false);
        $oViews->onObjectDelete();

 
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
        $aCheck = checkAction($this->_iProfileId, BX_CHURCH_POST_REVIEWS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    }

    /*******[END - Review Functions] ******************************/
 


	//[begin] [subprofile]
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
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_VIEW_CHURCH, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        $this->_oSubPrivacy = new BxChurchSubPrivacy($this, $sTable); 
	    return $this->_oSubPrivacy->check('view', $aDataEntry['id'], $this->_iProfileId); 
    }

    function isAllowedRateSubProfile($sTable, &$aDataEntry) {       
        
		if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) )
            return true;
        
		$this->_oSubPrivacy = new BxChurchSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedCommentsSubProfile($sTable, &$aDataEntry) {
        
		if ( $this->isAdmin() || $this->isSubEntryAdmin($aDataEntry) ) 
            return true;

        $this->_oSubPrivacy = new BxChurchSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotosSubProfile($sTable, &$aDataEntry) {

        if (!BxDolRequest::serviceExists('photos', 'perform_photo_upload', 'Uploader'))
            return false;	

        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;

		$this->_oSubPrivacy = new BxChurchSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideosSubProfile($sTable, &$aDataEntry) {

        if (!BxDolRequest::serviceExists('videos', 'perform_video_upload', 'Uploader'))
            return false;

        if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;
        
		$this->_oSubPrivacy = new BxChurchSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSoundsSubProfile($sTable, &$aDataEntry) {

        if (!BxDolRequest::serviceExists('sounds', 'perform_music_upload', 'Uploader'))
            return false;

		if (!$this->_iProfileId) 
            return false;        
        if ( $this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;
        
		$this->_oSubPrivacy = new BxChurchSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFilesSubProfile($sTable, &$aDataEntry) {

        if (!BxDolRequest::serviceExists('files', 'perform_file_upload', 'Uploader'))
            return false;
 
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        if ($this->isSubEntryAdmin($aDataEntry))
            return true;

		$this->_oSubPrivacy = new BxChurchSubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function actionUploadPhotosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadPhotosSubProfile', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_church_page_title_upload_photos'));
    }

    function _actionUploadMediaSubProfile ($sType, $sUri, $sIsAllowedFuncName, $sMedia, $aMediaFields, $sTitle) {
   
		switch($sType){ 
			case 'event':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableEventMediaPrefix;
				$sTable = $this->_oDb->_sTableEvent ;
				$sDataFuncName = 'getEventEntryByUri';
			break; 
			case 'members':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableMembersMediaPrefix;
				$sTable = $this->_oDb->_sTableMembers ;
				$sDataFuncName = 'getMembersEntryByUri';
			break; 
			case 'news':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableNewsMediaPrefix;
				$sTable = $this->_oDb->_sTableNews ;
				$sDataFuncName = 'getNewsEntryByUri';
			break; 
			case 'ministries':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableMinistriesMediaPrefix;
				$sTable = $this->_oDb->_sTableMinistries ;
				$sDataFuncName = 'getMinistriesEntryByUri';
			break; 
			case 'branches':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBranchesMediaPrefix;
				$sTable = $this->_oDb->_sTableBranches ;
				$sDataFuncName = 'getBranchesEntryByUri';
			break; 
			case 'doctrine':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableDoctrineMediaPrefix;
				$sTable = $this->_oDb->_sTableDoctrine ;
				$sDataFuncName = 'getDoctrineEntryByUri';
			break; 
			case 'sermon':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSermonMediaPrefix;
				$sTable = $this->_oDb->_sTableSermon ;
				$sDataFuncName = 'getSermonEntryByUri';
			break; 
			case 'staff':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableStaffMediaPrefix;
				$sTable = $this->_oDb->_sTableStaff ;
				$sDataFuncName = 'getStaffEntryByUri';
			break;    
			case 'review':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableReviewMediaPrefix;
				$sTable = $this->_oDb->_sTableReview ;
				$sDataFuncName = 'getReviewEntryByUri';
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

        $aChurchEntry = $this->_oDb->getEntryById($aDataEntry['church_id']);
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aChurchEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aChurchEntry[$this->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomSubHeaderUrl(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
    	$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aChurchEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aChurchEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));

        $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];

        bx_import (ucwords($sType) . 'FormUploadMedia', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . ucwords($sType) . 'FormUploadMedia';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId],$aDataEntry['church_id'], $iEntryId, $aDataEntry, $sMedia, $aMediaFields);
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

		if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry))
            return true;
  
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_ADD_CHURCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedSubEdit ($aDataEntry, $aSubDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isSubEntryAdmin($aSubEntry)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aSubDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;
 
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_EDIT_ANY_CHURCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 
  
    function isAllowedSubDelete ($aDataEntry, $aSubDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isSubEntryAdmin($aSubEntry)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        if ($GLOBALS['logged']['member'] && $aSubDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId)) 
            return true;

        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHURCH_DELETE_ANY_CHURCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
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
	//[end] [subprofile]


	/* functions added for v7.1 */

    function serviceGetMemberMenuItem ()
    {
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_church'), _t('_modzzz_church'), 'home');
    }
 
    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_church_church_single'), _t('_modzzz_church_church_single'), 'home', false, '&filter=add_church');
    }
 
   /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('church', array(
            'part' => 'church',
            'title' => '_modzzz_church',
            'title_singular' => '_modzzz_church_single',
            'icon' => 'modules/modzzz/church/|event_map_marker.png',
            'icon_site' => 'home',
            'join_table' => 'modzzz_church_main',
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
            'join_field_privacy' => 'allow_view_church_to',
            'permalink' => 'modules/?r=church/view/',
        )));
    }

    function serviceBranchesMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('church_branches', array(
            'part' => 'church_branches',
            'title' => '_modzzz_church_branches',
            'title_singular' => '_modzzz_church_branches_single',
            'icon' => 'modules/modzzz/church/|map_marker.png',
            'icon_site' => 'home',
            'join_table' => 'modzzz_church_branches_main',
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
            'permalink' => 'modules/?r=church/branches/view/',
        )));
    }
 
    function serviceEventMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('church_event', array(
            'part' => 'church_event',
            'title' => '_modzzz_church_event',
            'title_singular' => '_modzzz_church_event_single',
            'icon' => 'modules/modzzz/church/|map_marker.png',
            'icon_site' => 'calendar',
            'join_table' => 'modzzz_church_event_main',
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
            'permalink' => 'modules/?r=church/event/view/',
        )));
    }
 
	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_church_wall_object',
            'txt_added_new_single' => '_modzzz_church_wall_added_new',
            'txt_added_new_plural' => '_modzzz_church_wall_added_new_items',
            'txt_privacy_view_event' => 'view_church',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_church',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallPostComment($aEvent, $aParams);
    }

    function _serviceGetWallPostComment($aEvent, $aParams)
    {
        $iId = (int)$aEvent['object_id'];
        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId))
            return '';

        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = getNickName($iOwner);

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || !isset($aContent['comment_id']))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iId);
        if(!$oCmts->isEnabled())
            return '';

        $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $iOwner, 1);
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
            'txt_privacy_view_event' => 'view_church',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'home', $aParams);
    }
 
    function _formatLocation (&$aDataEntry, $isCountryLink = false, $isFlag = false)
    {
        $sFlag = $isFlag ? ' ' . genFlag($aDataEntry['country']) : '';
        $sCountry = _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']);
        if ($isCountryLink)
            $sCountry = '<a href="' . $this->_oConfig->getBaseUri() . 'browse/country/' . strtolower($country['Country']) . '">' . $sCountry . '</a>';
        return (trim($aDataEntry['city']) ? $aDataEntry['city'] . ', ' : '') . $sCountry . $sFlag;
    }

    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc' => $this->_formatSnippetText($aEntryData, 200),
            'location' => $this->_formatLocation($aEntryData, false, false),
            'fans_count' => $aEntryData['fans_count'],
        ));
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 300, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }

    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        return true;
    }

    function serviceStatesInstall()
    {
		$this->_oDb->statesInstall(); 
	}	

    function isSubEntryAdmin($aSubEntry, $iProfileId = 0, $sIdField='id') {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
 
		$aDataEntry = $this->_oDb->getEntryById ((int)$aSubEntry['church_id']);

        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
	 
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aSubEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;

        return $this->_oDb->isGroupAdmin ($aDataEntry[$sIdField], $iProfileId) && isProfileActive($iProfileId);
    }

}
