<?php
/***************************************************************************
*                            Dolphin Smart Charity Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Charity Builder
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

function modzzz_charity_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'charity') {
        $oMain = BxDolModule::getInstance('BxCharityModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
bx_import('BxDolCategories');


define ('BX_CHARITY_PHOTOS_CAT', 'Charity');
define ('BX_CHARITY_PHOTOS_TAG', 'charity');

define ('BX_CHARITY_VIDEOS_CAT', 'Charity');
define ('BX_CHARITY_VIDEOS_TAG', 'charity');

define ('BX_CHARITY_SOUNDS_CAT', 'Charity');
define ('BX_CHARITY_SOUNDS_TAG', 'charity');

define ('BX_CHARITY_FILES_CAT', 'Charity');
define ('BX_CHARITY_FILES_TAG', 'charity');

define ('BX_CHARITY_MAX_FANS', 1000);
 
 
/*
 * Charity module
 *
 * This module allow users to create user's charity, 
 * users can rate, comment and discuss charity.
 * Charity can have photos, videos, sounds and files, uploaded
 * by charity's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add charity' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new charity was created
 * change - charity was chaned
 * rate - somebody rated charity
 * commentPost - somebody posted comment in charity
 *
 *
 *
 * Memberships/ACL:
 * charity view charity - BX_CHARITY_VIEW_CHARITY
 * charity browse - BX_CHARITY_BROWSE
 * charity search - BX_CHARITY_SEARCH
 * charity add charity - BX_CHARITY_ADD_CHARITY
 * charity comments delete and edit - BX_CHARITY_COMMENTS_DELETE_AND_EDIT
 * charity edit any charity - BX_CHARITY_EDIT_ANY_CHARITY
 * charity delete any charity - BX_CHARITY_DELETE_ANY_CHARITY
 * charity mark as featured - BX_CHARITY_MARK_AS_FEATURED
 * charity approve charity - BX_CHARITY_APPROVE_CHARITY
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different charity
 * @see BxCharityModule::serviceHomepageBlock
 * BxDolService::call('charity', 'homepage_block', array());
 *
 * Profile block with user's charity
 * @see BxCharityModule::serviceProfileBlock
 * BxDolService::call('charity', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for charity (for internal usage only)
 * @see BxCharityModule::serviceGetMemberMenuItem
 * BxDolService::call('charity', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_charity'
 * The following alerts are rised
 *
 
 *
 *  add - new charity was added
 *      $iObjectId - charity id
 *      $iSenderId - creator of a charity
 *      $aExtras['Status'] - status of added charity
 *
 *  change - charity's info was changed
 *      $iObjectId - charity id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed charity
 *
 *  delete - charity was deleted
 *      $iObjectId - charity id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - charity was marked/unmarked as featured
 *      $iObjectId - charity id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if charity was marked as featured and 0 - if charity was removed from featured 
 *
 */
class BxCharityModule extends BxDolTwigModule {

    var $_oPrivacy;
     
	var $_aQuickCache = array ();

    function BxCharityModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_charity';

        bx_import ('Privacy', $aModule);
        bx_import ('SubPrivacy', $aModule);
		$this->_oPrivacy = new BxCharityPrivacy($this);
 

	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxCharityModule'] = &$this;

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
  
    }

    function actionHome () {
        parent::_actionHome(_t('_modzzz_charity_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_charity_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_charity_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_charity_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_charity_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_charity_page_title_comments'));
    }

    function actionBrowseFans ($sUri) {
        parent::_actionBrowseFans ($sUri, 'isAllowedViewFans', 'getFansBrowse', $this->_oDb->getParam('modzzz_charity_perpage_browse_fans'), 'browse_fans/', _t('_modzzz_charity_page_title_fans'));
    }
  
    function actionBrowseReviews ($sUri) {
        $this->_actionBrowseReviews ($sUri, _t('_modzzz_charity_page_title_reviews'));
    }

    function _actionBrowseReviews ($sUri, $sTitle) {

		$iPerPage=$this->_oDb->getParam('modzzz_charity_perpage_browse_reviews');

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

		$sTitle = _t('_modzzz_charity_page_title_reviews');

		$iPerPage=$this->_oDb->getParam('modzzz_charity_perpage_browse_reviews');
  
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
        parent::_actionView ($sUri, _t('_modzzz_charity_msg_pending_approval'));
    }

    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_charity_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_charity_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_charity_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_charity_page_title_upload_files')); 
    }
  
    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_charity_page_title_calendar'));
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
        
        modzzz_charity_import ('FormSearch');
        $oForm = new BxCharityFormSearch ();
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_charity_import ('SearchResult');
            $o = new BxCharitySearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );

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
        $this->_oTemplate->pageCode(_t('_modzzz_charity_caption_search'));
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
        $this->_oTemplate->pageCode(_t('_modzzz_charity_page_title_search'));
    }

    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_charity_page_title_add'));
    }
  
    function _addForm ($sRedirectUrl) {
  
        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
  
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
 
                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
  
				if (!$sRedirectUrl)  
					$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
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
        parent::_actionEdit ($iEntryId, _t('_modzzz_charity_page_title_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_charity_msg_charity_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_charity_msg_added_to_featured'), _t('_modzzz_charity_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_charity_caption_share_charity'));
    }
 
   function actionJoin ($iEntryId, $iProfileId) {

        parent::_actionJoin ($iEntryId, $iProfileId, _t('_modzzz_charity_msg_joined_already'), _t('_modzzz_charity_msg_joined_request_pending'), _t('_modzzz_charity_msg_join_success'), _t('_modzzz_charity_msg_join_success_pending'), _t('_modzzz_charity_msg_leave_success'));
    }    
 
    function actionManageFansPopup ($iEntryId) {
        parent::_actionManageFansPopup ($iEntryId, _t('_modzzz_charity_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', BX_CHARITY_MAX_FANS);
    }

    function actionTags() {
        parent::_actionTags (_t('_modzzz_charity_page_title_tags'));
    }    
 
    function actionCategories() {
        parent::_actionCategories (_t('_modzzz_charity_page_title_categories'));
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
        parent::_actionMakeClaimPopup ($iEntryId, _t('_modzzz_charity_caption_manage_fans'), 'getFans', 'isAllowedManageFans', 'isAllowedManageAdmins', modzzz_charity_MAX_FANS);
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
        $this->_actionInvite ($iEntryId, 'modzzz_charity_invitation', $this->_oDb->getParam('modzzz_charity_max_email_invitations'), _t('_modzzz_charity_invitation_sent'), _t('_modzzz_charity_no_users_msg'), _t('_modzzz_charity_caption_invite'));
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
            $this->_oTemplate->pageCode ($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], true, false);
            return;
        } 

        echo $oForm->getCode ();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('inviter.css');
        $this->_oTemplate->pageCode($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
    }
  
    function _getInviteParams ($aDataEntry, $aInviter) {
        return array (
                'CharityName' => $aDataEntry['title'],
                'CharityLocation' => _t($GLOBALS['aPreValues']['country'][$aDataEntry['Country']]['LKey']) . (trim($aDataEntry['city']) ? ', '.$aDataEntry['city'] : ''),
                'CharityUrl' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                'InviterUrl' => $aInviter ? getProfileLink($aInviter['ID']) : 'javascript:void(0);',
                'InviterNickName' => $aInviter ? $aInviter['NickName'] : _t('_modzzz_charity_user_unknown'),
                'InvitationText' => stripslashes(strip_tags($_REQUEST['inviter_text'])),
            );        
    }
 
    function actionInquire ($iEntryId) {
        $this->_actionInquire ($iEntryId, 'modzzz_charity_inquiry', _t('_modzzz_charity_caption_make_inquiry'), _t('_modzzz_charity_inquiry_sent'), _t('_modzzz_charity_inquiry_not_sent'));
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

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));

        bx_import ('InquireForm', $this->_aModule);
		$oForm = new BxCharityInquireForm ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {        
 
			$aInquirer = getProfileInfo($this->_iProfileId);
            $aPlusOriginal = $this->_getInquireParams ($aDataEntry, $aInquirer);
		  
			$iRecipient = $aDataEntry['author_id'];
 
            $oEmailTemplate = new BxDolEmailTemplates();
            $aTemplate = $oEmailTemplate->getTemplate($sEmailTemplate);
            $iSuccess = 0;
  
            // send message to charity owner
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
            $this->_oTemplate->pageCode ($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], true, false);
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
                'SenderName' => $aInquirer ? $aInquirer['NickName'] : _t('_modzzz_charity_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['inquire_text'])),
            );        
    }


/*[begin] claim*/
    function actionClaim ($iEntryId) {
        $this->_actionClaim ($iEntryId, 'modzzz_charity_claim', _t('_modzzz_charity_caption_make_claim'), _t('_modzzz_charity_claim_sent'), _t('_modzzz_charity_claim_not_sent'));
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

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$sTitle => '',
		));

        bx_import ('ClaimForm', $this->_aModule);
		$oForm = new BxCharityClaimForm ($this);
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
            $this->_oTemplate->pageCode ($sTitle .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], true, false);
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
                'SenderName' => $aClaimer ? $aClaimer['NickName'] : _t('_modzzz_charity_user_unknown'),
                'Message' => stripslashes(strip_tags($_REQUEST['claim_text'])),
            );        
    }
	/*[end] claim*/



    // ================================== external actions

    /**
     * Homepage block with different charity
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent()){ 
			return '';
        } 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxCharityPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));
  
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_charity_homepage_default_tab');
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
            $this->_oDb->getParam('modzzz_charity_perpage_homepage'), 
            array(
                _t('_modzzz_charity_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_charity_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_charity_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_charity_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's charity
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxCharityPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_charity_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

     /**
     * Account block with different events
     * @return html to display area charities in account page a block
     */  
    function serviceAccountAreaBlock () {

        if (!$this->_oDb->isAnyPublicContent())
            return '';

		$aProfileInfo = getProfileInfo($this->_iProfileId);
		$sCity = $aProfileInfo['City'];

		if(!$sCity)
			return;

        bx_import ('PageMain', $this->_aModule);
        $o = new BxCharityPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . '?';
 
        return $o->ajaxBrowse(
            'local',
            $this->_oDb->getParam('modzzz_charity_perpage_accountpage'),
			array(),
			$sCity
        );
    }

    /**
     * Account block with different events
     * @return html to display member charities in account page a block
     */ 
    function serviceAccountPageBlock () {
  
        $aProfile = getProfileInfo($this->_iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxCharityPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . 'member.php?';
        
		$this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_charity_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }
 
    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_charity_spy_post',
            'change' => '_modzzz_charity_spy_post_change', 
            'join' => '_modzzz_charity_spy_join',
            'rate' => '_modzzz_charity_spy_rate',
            'commentPost' => '_modzzz_charity_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_charity_sbs_change'),
            'commentPost' => _t('_modzzz_charity_sbs_comment'),
            'rate' => _t('_modzzz_charity_sbs_rate'), 
            'join' => _t('_modzzz_charity_sbs_join'), 
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

        $aVars = array (
            'module_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri(),
        );
  
        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_charity_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_charity_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),   
			'claims' => array(
                'title' => _t('_modzzz_charity_menu_manage_claims'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/claims',
                '_func' => array ('name' => 'actionAdministrationClaims', 'params' => array($sParam1)),
            ), 
			'donors' => array(
                'title' => _t('_modzzz_charity_menu_manage_donors'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/donors',
                '_func' => array ('name' => 'actionAdministrationDonors', 'params' => array($sParam1)),
            ),  
            'create' => array(
                'title' => _t('_modzzz_charity_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_charity_menu_admin_settings'), 
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
 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_charity_page_title_administration'));
    }

    function actionAdministrationDonors ($iEntryId=0) {
		
		$sContent = $this->loadAdministrationCharities($iEntryId);
 
		$sContent .= $this->_manageDonorsEntries ('donors', $iEntryId, false, 'bx_twig_admin_form');
 
        return $sContent;
    }
  
   function loadAdministrationCharities ($sParam1='') {
   		$sCharity = process_db_input($sParam1);
  
		$aCharities = $this->_oDb->getCharities();
 
		$aCharity[] = array(
			'value' => '',
			'caption' => _t('_Select') ,
			'selected' => ''
		);

		foreach ($aCharities as $oCharity){ 
			$sKey = $oCharity['uri'];
			$sValue = $oCharity['title'];
   
			$aCharity[] = array(
				'value' => $sKey,
				'caption' => $sValue ,
				'selected' => ($sKey == $sCharity) ? 'selected="selected"' : ''
			);
		}
 
		$sContent = $GLOBALS['oAdmTemplate']->parseHtmlByName('top_block_select.html', array(
			'name' => _t('_modzzz_charity_charities'),
			'bx_repeat:items' => $aCharity,
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
        return parent::_actionAdministrationSettings ('Charity');
    }
  
    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_charity_admin_delete', '_modzzz_charity_admin_activate');
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
 			'action_assign' => '_modzzz_charity_admin_assign',
 			'action_delete' => '_modzzz_charity_admin_delete',
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
 
		//[begin] delete supporters
		$aSupporters = $this->_oDb->getAllSubItems('supporter', $iEntryId);
		foreach($aSupporters as $aEachSupporter){
			
			$iId = (int)$aEachSupporter['id'];
 
			// delete votings
			bx_import('SupporterVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SupporterVoting';
			$oVoting = new $sClass ($this->_oDb->_sSupporterPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);
 
			// delete comments 
			bx_import('SupporterCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SupporterCmts';
			$oCmts = new $sClass ($this->_oDb->_sSupporterPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteSupporterByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());   
		}  
 		//[end] delete supporters
 
		//[begin] delete staff
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
 		//[end] delete staff
 
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
 
		//[begin] delete programs
		$aPrograms = $this->_oDb->getAllSubItems('programs', $iEntryId);
		foreach($aPrograms as $aEachPrograms){
			
			$iId = (int)$aEachPrograms['id'];

			// delete votings
			bx_import('ProgramsVoting', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ProgramsVoting';
			$oVoting = new $sClass ($this->_oDb->_sProgramsPrefix, 0, 0);
			$oVoting->deleteVotings ($iId);

			// delete comments 
			bx_import('ProgramsCmts', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'ProgramsCmts';
			$oCmts = new $sClass ($this->_oDb->_sProgramsPrefix, $iId);
			$oCmts->onObjectDelete ();
		
 			$this->_oDb->deleteProgramsByIdAndOwner($iId, $iEntryId,  $this->_iProfileId, $this->isAdmin());  
		}   
 		//[end] delete programs
 
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
        parent::_onEventJoinRequest ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_charity_join_request', BX_CHARITY_MAX_FANS);
    }

    function onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinReject ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_charity_join_reject');
    }

    function onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanRemove ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_charity_fan_remove');
    }

    function onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventFanBecomeAdmin ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_charity_fan_become_admin');
    }

    function onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry) {        
        parent::_onEventAdminBecomeFan ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_charity_admin_become_fan');
    }

    function onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry) {
        parent::_onEventJoinConfirm ($iEntryId, $iProfileId, $aDataEntry, 'modzzz_charity_join_confirm');
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
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_VIEW_CHARITY, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
        // check user group  
        $isAllowed =  $this->_oPrivacy->check('view_charity', $aDataEntry['id'], $this->_iProfileId);   
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
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_ADD_CHARITY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_EDIT_ANY_CHARITY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHARITY_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
  
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_DELETE_ANY_CHARITY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }     
  
    function isAllowedInquire (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHARITY_MAKE_INQUIRY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 
	
    function isAllowedClaim (&$aDataEntry, $isPerformAction = false) {
		if (!$this->_oDb->isOwnerAdmin($aDataEntry['author_id']))
            return false;
  
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHARITY_MAKE_CLAIM, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    } 

    function isAllowedSendInvitation (&$aDataEntry) {
        return getLoggedId();
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
        if (!$this->isMembershipEnabledForFiles())
            return false;     
        if ($this->isEntryAdmin($aDataEntry)) 
            return true;   
 
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }
	
    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) return true;        
        if (getParam('modzzz_charity_author_comments_admin') && $this->isEntryAdmin($aDataEntry))
            return true;        
		$this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('modzzz_charity_permalinks') == 'on'));
		 
        return $bEnabled;
    }


    function _defineActions () {
        defineMembershipActions(array('charity make donation', 'charity view donors', 'charity add review', 'charity purchase', 'charity relist', 'charity extend','charity purchase featured', 'charity view charity', 'charity browse', 'charity search', 'charity add charity', 'charity comments delete and edit', 'charity edit any charity', 'charity delete any charity', 'charity mark as featured', 'charity approve charity', 'charity make claim', 'charity make inquiry','charity broadcast message','charity post reviews','charity create faqs'));
    }

 
    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_charity_page_title_my_charity'));
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

	function initializeCheckout($iCharityId, $fTotalCost, $iQuantity=1, $bFeatured=0) {
  
		$aFormData = array(
			'cmd' => '_xclick',
			'amount' => sprintf( "%.2f", (float)$fTotalCost)
		);
    	  
		$aDataEntry = $this->_oDb->getEntryById($iCharityId);

		$sNotifyUrl = $this->_oConfig->getFeaturedCallbackUrl() . $this->_iProfileId .'/'. $iCharityId;
		$sItemDesc = $aDataEntry['title'];
   
		$aDataEntry = $this->_oDb->getEntryById($iCharityId);
 		$sUri = $aDataEntry[$this->_oDb->_sFieldUri];

        $aFormData = array_merge($aFormData, array(
			'business' => getParam('modzzz_charity_paypal_email'), 
            'item_name' => $sItemDesc,
			'amount' => $fTotalCost, 
            'item_number' => $iCharityId,
            'quantity' => $iQuantity,
            'currency_code' => $this->_oConfig->getPurchaseCurrency(),
            'no_note' => '1',
            'no_shipping' => '1',  
			'return' => $this->_oConfig->getReturnUrl() . $sUri,
			'notify_url' => $sNotifyUrl,  
			'rm' => '1'
        ));
  
    	Redirect($this->_oConfig->getPurchaseBaseUrl(), $aFormData, 'post', "Charity");
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
        $this->_oTemplate->pageCode(_t('_modzzz_charity_post_purchase_header')); 
    }
 
 
    function actionPurchaseFeatured($iCharityId, $sTransMessage = '') {
 
        if(! $this->_iProfileId) {
            header('location: ' . BX_DOL_URL_ROOT . 'member.php');
        }

	    if($sTransMessage){
			$sMessageOutput = MsgBox($sTransMessage);  
		}
 
		$iPerDayCost = getParam('modzzz_charity_featured_cost');

		$aDataEntry = $this->_oDb->getEntryById($iCharityId);
		$sTitle = $aDataEntry['title'];

        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_modzzz_charity_purchase_featured') => '',
		));

		$aForm = array(
            'form_attrs' => array(
                'name' => 'buy_featured_form',
                'method' => 'post', 
                'action' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'purchase_featured/'.$iCharityId,
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
					'caption'  => _t('_modzzz_charity_form_caption_title'),
                    'content' => $sTitle,
                ), 
                'cost' => array(
                    'type' => 'custom',
                    'name' => 'cost',
					'caption'  => _t('_modzzz_charity_featured_cost_per_day'),
                    'content' => $iPerDayCost .' '. $this->_oConfig->getPurchaseCurrency(),
                ), 
                'status' => array(
                    'type' => 'custom',
                    'name' => 'status',
					'caption'  => _t('_modzzz_charity_featured_status'),
                    'content' => ($aDataEntry['featured']) ? _t('_modzzz_charity_featured_until')  . ' '. $this->_oTemplate->filterCustomDate($aDataEntry['featured_expiry_date']) : _t('_modzzz_charity_not_featured'), 
                ), 
                'quantity' => array(
                    'caption'  => _t('_modzzz_charity_caption_num_featured_days'),
                    'type'   => 'text',
                    'name' => 'quantity',
                    'required' => true,
                    'checker' => array (  
                        'func'   => 'Preg',
                        'params' => array('/^[0-9]+$/'),
                        'error'  => _t('_modzzz_charity_caption_err_featured_days'),
                    ),
                ),
                'submit' => array(
                    'type'  => 'submit',
                    'value' => ($aDataEntry['featured']) ? _t('_modzzz_charity_extend_featured') : _t('_modzzz_charity_get_featured'),
                    'name'  => 'submit_purchase',
                ),
            ),
        );
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker();  

        if ($oForm->isSubmittedAndValid() && $oForm->getCleanValue('quantity')) { 

			$fCost =  number_format($iPerDayCost, 2); 
  
			$this->initializeCheckout($iCharityId, $fCost, $oForm->getCleanValue('quantity'), true);  
			return;   
        } else {
             echo $sMessageOutput . $oForm->getCode();
        }

        $this->_oTemplate->addCss ('main.css'); 
        $this->_oTemplate->pageCode(_t('_modzzz_charity_purchase_featured')); 
    }
 
    function actionPaypalFeaturedProcess($iProfileId, $iCharityId) {
        $sPostData = '';
        $sPageContent = '';

        $aData = &$_REQUEST;

        if($aData) {
			$iQuantity = (int)$aData['quantity'];

			$aDataEntry = $this->_oDb->getEntryById($iCharityId); 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
 
        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
 				$this->actionPurchaseFeatured(_t('_modzzz_charity_purchase_featured_failed')); 
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
				$this->actionPurchaseFeatured($iCharityId, _t('_modzzz_charity_purchase_featured_failed'));
			}else { 
				$fAmount = $this->_getReceivedAmount($aData);
			
				if($this->_oDb->isExistFeaturedTransaction($iProfileId, $aData['txn_id'])) {
					$this -> actionPurchaseFeatured($iCharityId, _t('_modzzz_charity_transaction_completed_already')); 
				} else {
					if( $this->_oDb->saveFeaturedTransactionRecord($iProfileId, $iCharityId,  $iQuantity, $fAmount, $aData['txn_id'], 'Paypal Purchase')) {

						$this->_oDb->updateFeaturedEntryExpiration($iCharityId, $iQuantity); 
			   
						$this->actionPurchaseFeatured($iCharityId, _t('_modzzz_charity_purchase_success',  $iQuantity));
					} else {
						$this -> actionPurchaseFeatured($iCharityId, _t('_modzzz_charity_purchase_featured_failed'));
					}
				}
			}
            
        }
    }
 
    function isAllowedPurchaseFeatured ($aDataEntry, $isPerformAction = false) {
  
		if(getParam("modzzz_charity_buy_featured")!='on')
			return false;
 
		if ($this->isAdmin())
            return false;

		if($aDataEntry['featured'] && !$aDataEntry['featured_expiry_date'])
            return false;

        if (!($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return false;
 
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHARITY_PURCHASE_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    } 

    function actionLocal ($sCountry='', $sState='') { 
        $this->_oTemplate->pageStart();
        bx_import ('PageLocal', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageLocal';
        $oPage = new $sClass ($this, $sCountry, $sState);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
 
		$sTitle = _t('_modzzz_charity_page_title_local');

		if($sCountry){
			$sTitle .= ' - ' . $this->_oTemplate->getPreListDisplay('Country', $sCountry);
		}
 
		if($sState){
			$sTitle .= ' - ' . $this->_oDb->getStateName($sCountry, $sState); 
		}
 
        $this->_oTemplate->pageCode($sTitle, false, false); 
    } 
 
    function isMembershipEnabledForImages () {
        return ($this->_isMembershipEnabledFor ('BX_PHOTOS_ADD') && $this->_isMembershipEnabledFor ('BX_CHARITY_PHOTOS_ADD'));
    }

    function isMembershipEnabledForVideos () {
        return ($this->_isMembershipEnabledFor ('BX_VIDEOS_ADD') && $this->_isMembershipEnabledFor ('BX_CHARITY_VIDEOS_ADD')); 
    }

    function isMembershipEnabledForSounds () {
        return ($this->_isMembershipEnabledFor ('BX_SOUNDS_ADD') && $this->_isMembershipEnabledFor ('BX_CHARITY_SOUNDS_ADD'));
    }

    function isMembershipEnabledForFiles () {
        return ($this->_isMembershipEnabledFor ('BX_FILES_ADD') && $this->_isMembershipEnabledFor ('BX_CHARITY_FILES_ADD'));
    }
 
    function _isMembershipEnabledFor ($sMembershipActionConstant) { 
        defineMembershipActions (array('photos add', 'sounds add', 'videos add', 'files add', 'charity photos add', 'charity sounds add', 'charity videos add', 'charity files add'));
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
 
    function isAllowedViewForum(&$aDataEntry, $isPerformAction = false) {
        return true;
    }

    function isAllowedPostInForum(&$aDataEntry, $iProfileId = -1) {
        if (-1 == $iProfileId)
            $iProfileId = $this->_iProfileId;
        return $this->isAdmin() || $this->isEntryAdmin($aDataEntry) || $this->_oPrivacy->check('post_in_forum', $aDataEntry['id'], $iProfileId);
    }

    function isAllowedManageFans($aDataEntry) {
        return $this->isEntryAdmin($aDataEntry);
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
  
 	//[begin] [broadcast]
    function isAllowedBroadcast ($aDataEntry, $isPerformAction = false) {
        
        if (!($this->isAdmin() || $this->isEntryAdmin($aDataEntry))) 
            return false;

        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_CHARITY_BROADCAST_MESSAGE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }

    function actionBroadcast ($iEntryId) {
        $this->_actionBroadcast ($iEntryId, _t('_modzzz_charity_page_title_broadcast'), _t('_modzzz_charity_msg_broadcast_no_recipients'), _t('_modzzz_charity_msg_broadcast_message_sent'));
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
				$this->actionDonationAdd ($sDonationIdUri, '_modzzz_charity_page_title_donation_add', $sMessage);
			break;  
			case 'browse':
				return $this->actionDonationBrowse ($sDonationIdUri, '_modzzz_charity_page_title_donation_browse'); 
			break;  
		}
	}
    
    function actionDonationProcess($iProfileId, $iEntryId) {
        $sPostData = '';
        $sPageContent = '';
  
        $aData = &$_REQUEST;

        if($aData) {
  
			$aDataEntry = $this->_oDb->getEntryById ($iEntryId);
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];

        	$sRequest = 'cmd=_notify-validate';
        	foreach($aData as $sKey => $sValue)
        		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));

        	$aResponse = $this->_readValidationData($sRequest);
    
        	if((int)$aResponse['code'] !== 0){
				$this->actionDonation('add', $iEntryId, _t('_modzzz_charity_donation_failed')); 
 				return;
			}

        	array_walk($aResponse['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        	if(strcmp($aResponse['content'][0], "INVALID") == 0){
				$this->actionDonation('add', $iEntryId, _t('_modzzz_charity_donation_err_wrong_transaction'));
 				return; 
        	}

			if(strcmp($aResponse['content'][0], "VERIFIED") != 0){
				$this->actionDonation('add', $iEntryId, _t('_modzzz_charity_donation_err_wrong_verification_status'));
 				return;  
			}
	   
			if($aData['txn_type'] == 'web_accept') {
 
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
				$this->actionDonation('add', $iEntryId, 0, _t('_modzzz_charity_donation_failed')); 
			} 
        }else{
			$this->actionDonation('add', $iEntryId, 0, _t('_modzzz_charity_donation_failed')); 
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

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t('_modzzz_charity_purchase_featured') => '',
		));

        bx_import ('DonationPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'DonationPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$this->_oDb->_sFieldTitle]), false, false);  
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
			
			$sCustomStr = '';
			if($this->_iProfileId) { 
				$sCustomStr = $oForm->getCleanValue('anonymous');
			}else{
				$sCustomStr = $oForm->getCleanValue('anonymous') .'|'. $oForm->getCleanValue('first_name') .'|'. $oForm->getCleanValue('last_name');
			}
 
			header('location: ' . $this->_oDb->generatePaymentUrl($iEntryId, $oForm->getCleanValue('amount'), $sCustomStr));
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
 
    function isAllowedDonate (&$aDataEntry) {
        
		if(!$aDataEntry['paypal'])
            return false;                        
  
		// admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;
			 
		$this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_MAKE_DONATION, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED; 
	
	} 

    function isAllowedViewDonors ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_VIEW_DONORS, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
 
        // check user group  
        return $this->_oPrivacy->check('view_donors', $aDataEntry['id'], $this->_iProfileId);  
    }

	/******[END] Donation functions **************************/ 


	/******[BEGIN] News functions **************************/ 
    function actionNews ($sAction, $sParam1='', $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->newsDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionNewsAdd ($sParam1, '_modzzz_charity_page_title_news_add');
			break;
			case 'edit':
				$this->actionNewsEdit ($sParam1, '_modzzz_charity_page_title_news_edit');
			break;
			case 'delete':
				$this->actionNewsDelete ($sParam1, _t('_modzzz_charity_msg_charity_news_was_deleted'));
			break;
			case 'view':
				$this->actionNewsView ($sParam1, _t('_modzzz_charity_msg_pending_news_approval')); 
			break; 
			case 'browse':
				return $this->actionNewsBrowse ($sParam1, '_modzzz_charity_page_title_news_browse'); 
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
		$iEntryId = (int)$aNewsEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aNewsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

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
		$iNewsId = (int)$aNewsEntry['charity_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
   
        $this->_oTemplate->pageStart();
 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        if (!$this->isAllowedEdit($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aNewsEntry['title']);  
    }

    function actionNewsDelete ($iNewsId, $sMsgSuccess) {

		$aNewsEntry = $this->_oDb->getNewsEntryById($iNewsId);
		$iEntryId = (int)$aNewsEntry['charity_id'];
  
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
            $this->onEventNewsDeleted ($iNewsId, $aNewsEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iNewsId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionNewsAdd ($iNewsId, $sTitle) {
   
		if (!($aDataEntry = $this->_oDb->getEntryById($iNewsId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
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
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getNewsEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured'));
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
    function actionEvent ($sAction, $sParam1='', $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->eventDownload ($sParam1, $sParam2);
			break; 
			case 'add': 
				$this->actionEventAdd ($sParam1, '_modzzz_charity_page_title_event_add');
			break;
			case 'edit':
				$this->actionEventEdit ($sParam1, '_modzzz_charity_page_title_event_edit');
			break;
			case 'delete':
				$this->actionEventDelete ($sParam1, _t('_modzzz_charity_msg_charity_event_was_deleted'));
			break;
			case 'view':
				$this->actionEventView ($sParam1, _t('_modzzz_charity_msg_pending_event_approval')); 
			break; 
			case 'browse':
				return $this->actionEventBrowse ($sParam1, '_modzzz_charity_page_title_event_browse'); 
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
		$iEntryId = (int)$aEventEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aEventEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 
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
		$iCharityId = (int)$aEventEntry['charity_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iCharityId))) {
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
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aEventEntry['title'] => '',
		));


        bx_import ('EventFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormEdit';
        $oForm = new $sClass ($this, $aEventEntry['uri'], $iCharityId,  $iEntryId, $aEventEntry);
  
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
			
			$aValsAdd = array();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
   
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
        $this->_oTemplate->pageCode(_t($sTitle, $aEventEntry['title']));  
    }

    function actionEventDelete ($iEventId, $sMsgSuccess) {

		$aEventEntry = $this->_oDb->getEventEntryById($iEventId);
		$iCharityId = (int)$aEventEntry['charity_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iCharityId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEventId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteEventByIdAndOwner($iEventId, $iCharityId, $this->_iProfileId, $this->isAdmin())) {
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
 
    function actionEventAdd ($iCharityId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iCharityId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        $this->_addEventForm($iCharityId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addEventForm ($iCharityId) { 
 
        bx_import ('EventFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'EventFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iCharityId);
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
    function actionMembers ($sAction, $sParam1='', $sParam2='') {
		switch($sAction){ 
			case 'download': 
				$this->membersDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionMembersAdd ($sParam1, '_modzzz_charity_page_title_members_add');
			break;
			case 'edit':
				$this->actionMembersEdit ($sParam1, '_modzzz_charity_page_title_members_edit');
			break;
			case 'delete':
				$this->actionMembersDelete ($sParam1, _t('_modzzz_charity_msg_charity_members_was_deleted'));
			break;
			case 'view':
				$this->actionMembersView ($sParam1, _t('_modzzz_charity_msg_pending_members_approval')); 
			break; 
			case 'browse':
				return $this->actionMembersBrowse ($sParam1, '_modzzz_charity_page_title_members_browse'); 
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
		$iEntryId = (int)$aMembersEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aMembersEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

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
		$iMembersId = (int)$aMembersEntry['charity_id'];
  
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
		$iEntryId = (int)$aMembersEntry['charity_id'];
 
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
    function actionBranches ($sAction, $sParam1='', $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->branchesDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionBranchesAdd ($sParam1, '_modzzz_charity_page_title_branches_add');
			break;
			case 'edit':
				$this->actionBranchesEdit ($sParam1, '_modzzz_charity_page_title_branches_edit');
			break;
			case 'delete':
				$this->actionBranchesDelete ($sParam1, _t('_modzzz_charity_msg_charity_branches_was_deleted'));
			break;
			case 'view':
				$this->actionBranchesView ($sParam1, _t('_modzzz_charity_msg_pending_branches_approval')); 
			break; 
			case 'browse':
				return $this->actionBranchesBrowse ($sParam1, '_modzzz_charity_page_title_branches_browse'); 
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
		$iEntryId = (int)$aBranchesEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aBranchesEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

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
		$iBranchesId = (int)$aBranchesEntry['charity_id'];
  
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
        $this->_oTemplate->pageCode(_t($sTitle)  .' - '. $aBranchesEntry['title']);  
    }

    function actionBranchesDelete ($iBranchesId, $sMsgSuccess) {

		$aBranchesEntry = $this->_oDb->getBranchesEntryById($iBranchesId);
		$iEntryId = (int)$aBranchesEntry['charity_id'];
 
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

	/******[BEGIN] Programs functions **************************/ 
    function actionPrograms ($sAction, $sParam1='', $sParam2='') {
		switch($sAction){
			case 'download': 
				$this->programsDownload ($sParam1, $sParam2);
			break; 
			case 'add': 
				$this->actionProgramsAdd ($sParam1, '_modzzz_charity_page_title_programs_add');
			break;
			case 'edit':
				$this->actionProgramsEdit ($sParam1, '_modzzz_charity_page_title_programs_edit');
			break;
			case 'delete':
				$this->actionProgramsDelete ($sParam1, _t('_modzzz_charity_msg_charity_programs_was_deleted'));
			break;
			case 'view':
				$this->actionProgramsView ($sParam1, _t('_modzzz_charity_msg_pending_programs_approval')); 
			break; 
			case 'browse':
				return $this->actionProgramsBrowse ($sParam1, '_modzzz_charity_page_title_programs_browse'); 
			break;  
		}
	}
    
    function actionProgramsBrowse ($sUri, $sTitle) {
      
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

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        bx_import ('ProgramsPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ProgramsPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionProgramsView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aProgramsEntry = $this->_oDb->getProgramsEntryByUri($sUri);
		$iEntryId = (int)$aProgramsEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aProgramsEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aProgramsEntry['title'] => '',
		));

        if ((!$this->_iProfileId || $aProgramsEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTablePrograms, $aProgramsEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   
 
        $this->_oTemplate->pageStart();

        bx_import ('ProgramsPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ProgramsPageView';
        $oPage = new $sClass ($this, $aProgramsEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }

        echo $oPage->getCode();

        bx_import('ProgramsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ProgramsCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aProgramsEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aProgramsEntry['title'], false, false); 
    }
 
    function actionProgramsEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aProgramsEntry = $this->_oDb->getProgramsEntryById($iEntryId);
		$iProgramsId = (int)$aProgramsEntry['charity_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryById($iProgramsId))) {
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
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        bx_import ('ProgramsFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ProgramsFormEdit';
        $oForm = new $sClass ($this, $aProgramsEntry['uri'], $iProgramsId,  $iEntryId, $aProgramsEntry);
  
        $oForm->initChecker($aProgramsEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'programs_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableProgramsMediaPrefix;
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
   
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'programs/view/' . $aProgramsEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aProgramsEntry['title']);  
    }

    function actionProgramsDelete ($iProgramsId, $sMsgSuccess) {

		$aProgramsEntry = $this->_oDb->getProgramsEntryById($iProgramsId);
		$iEntryId = (int)$aProgramsEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iProgramsId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iProgramsId, 'ajaxy_popup_result_div');
            exit;
        }
 
        if ($this->_oDb->deleteProgramsByIdAndOwner($iProgramsId, $iEntryId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventProgramsDeleted ($iProgramsId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
 
            $sJQueryJS = genAjaxyPopupJS($iProgramsId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS; 
            exit;
        }
 
        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iProgramsId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionProgramsAdd ($iProgramsId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
		if (!($aDataEntry = $this->_oDb->getEntryById($iProgramsId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        $this->_addProgramsForm($iProgramsId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addProgramsForm ($iProgramsId) { 
 
        bx_import ('ProgramsFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ProgramsFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iProgramsId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'programs_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableProgramsMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getProgramsEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'programs/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventProgramsDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseProgramsTags ($iEntryId);
        //$this->reparseProgramsCategories ($iEntryId);

        // delete votings
        bx_import('ProgramsVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ProgramsVoting';
        $oVoting = new $sClass ($this->_oDb->_sProgramsPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('ProgramsCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ProgramsCmts';
        $oCmts = new $sClass ($this->_oDb->_sProgramsPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sProgramsPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		//$oAlert->alert();
    }        
 
    /*******[END - Programs Functions] ******************************/


    /******[BEGIN] Supporter functions **************************/ 
    function actionSupporter ($sAction, $sParam1='', $sParam2='') {
		
		switch($sAction){
			case 'download': 
				$this->supporterDownload ($sParam1, $sParam2);
			break; 
			case 'add': 
				$this->actionSupporterAdd ($sParam1, '_modzzz_charity_page_title_supporter_add');
			break;
			case 'edit':
				$this->actionSupporterEdit ($sParam1, '_modzzz_charity_page_title_supporter_edit');
			break;
			case 'delete':
				$this->actionSupporterDelete ($sParam1, _t('_modzzz_charity_msg_charity_supporter_was_deleted'));
			break;
			case 'view':
				$this->actionSupporterView ($sParam1, _t('_modzzz_charity_msg_pending_supporter_approval')); 
			break; 
			case 'browse':
				return $this->actionSupporterBrowse ($sParam1, '_modzzz_charity_page_title_supporter_browse'); 
			break;  
		}
	}
    
    function actionSupporterBrowse ($sUri, $sTitle) {
       
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

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        bx_import ('SupporterPageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SupporterPageBrowse';
        $oPage = new $sClass ($this, $sUri);
        echo $oPage->getCode();
		$this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));

        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);  
    }
 
    function actionSupporterView ($sUri, $sMsgPendingApproval) {

        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }

		$aSupporterEntry = $this->_oDb->getSupporterEntryByUri($sUri);
		$iEntryId = (int)$aSupporterEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aSupporterEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			$aSupporterEntry['title'] => '',
		));

        if ((!$this->_iProfileId || $aSupporterEntry[$this->_oDb->_sFieldAuthorId] != $this->_iProfileId) && !$this->isAllowedViewSubProfile($this->_oDb->_sTableSupporter, $aSupporterEntry, true)) {
            $this->_oTemplate->displayAccessDenied ();
            return false;
        }   

        $this->_oTemplate->pageStart();
   
        bx_import ('SupporterPageView', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SupporterPageView';
        $oPage = new $sClass ($this, $aSupporterEntry);

        if ($aDataEntry[$this->_oDb->_sFieldStatus] == 'pending') {
            $aVars = array ('msg' => $sMsgPendingApproval); // this product is pending approval, please wait until it will be activated
            echo $this->_oTemplate->parseHtmlByName ('pending_approval_plank', $aVars);
        }
 
        echo $oPage->getCode();

        bx_import('SupporterCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SupporterCmts';
        $oCmts = new $sClass ($this->_sPrefix, 0);

        $this->_oTemplate->setPageDescription (substr(strip_tags($aSupporterEntry['desc']), 0, 255));
 
        $this->_oTemplate->addCss ('view.css');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('entry_view.css');
        $this->_oTemplate->pageCode($aSupporterEntry['title'], false, false); 
    }
 
    function actionSupporterEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aSupporterEntry = $this->_oDb->getSupporterEntryById($iEntryId);
		$iCharityId = (int)$aSupporterEntry['charity_id'];

        if (!($aDataEntry = $this->_oDb->getEntryById($iCharityId))) {
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
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        bx_import ('SupporterFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SupporterFormEdit';
        $oForm = new $sClass ($this, $aSupporterEntry['uri'], $iCharityId,  $iEntryId, $aSupporterEntry);
  
        $oForm->initChecker($aSupporterEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $this->_oDb->_sTableMain = 'supporter_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';

			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSupporterMediaPrefix;

            if ($oForm->update ($iEntryId, $aValsAdd)) {
  
				$oForm->processMedia($iEntryId, $this->_iProfileId);
   
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'supporter/view/' . $aSupporterEntry['uri']);
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
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aSupporterEntry['title']);  
    }

    function actionSupporterDelete ($iSupporterId, $sMsgSuccess) {

	$aSupporterEntry = $this->_oDb->getSupporterEntryById($iSupporterId);
	$iCharityId = (int)$aSupporterEntry['charity_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iCharityId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iSupporterId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iSupporterId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteSupporterByIdAndOwner($iSupporterId, $iCharityId, $this->_iProfileId, $this->isAdmin())) {
            $this->isAllowedDelete($aDataEntry, true); // perform action
            $this->onEventSupporterDeleted ($iSupporterId, $aDataEntry);            
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'];
  
            $sJQueryJS = genAjaxyPopupJS($iSupporterId, 'ajaxy_popup_result_div', $sRedirect);
            echo MsgBox(_t($sMsgSuccess)) . $sJQueryJS;
            exit;
        }

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iSupporterId, 'ajaxy_popup_result_div');
        exit;
    }
 
    function actionSupporterAdd ($iCharityId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

	if (!($aDataEntry = $this->_oDb->getEntryById($iCharityId))) {
		$this->_oTemplate->displayPageNotFound ();
		return;
	}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        $this->_addSupporterForm($iCharityId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addSupporterForm ($iCharityId) { 
 
        bx_import ('SupporterFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SupporterFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iCharityId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';

            $this->_oDb->_sTableMain = 'supporter_main';
			$this->_oDb->_sFieldId = 'id';
			$this->_oDb->_sFieldUri = 'uri';
			$this->_oDb->_sFieldTitle = 'title';
			$this->_oDb->_sFieldDescription = 'desc'; 
			$this->_oDb->_sFieldThumb = 'thumb';
			$this->_oDb->_sFieldStatus = 'status'; 
			$this->_oDb->_sFieldCreated = 'created';
 
			$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSupporterMediaPrefix;
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {

 				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getSupporterEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'supporter/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

    function onEventSupporterDeleted ($iEntryId, $aDataEntry = array()) {

        // delete associated tags and categories 
        //$this->reparseSupporterTags ($iEntryId);
        //$this->reparseSupporterCategories ($iEntryId);

        // delete votings
        bx_import('SupporterVoting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SupporterVoting';
        $oVoting = new $sClass ($this->_oDb->_sSupporterPrefix, 0, 0);
        $oVoting->deleteVotings ($iEntryId);

        // delete comments 
        bx_import('SupporterCmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SupporterCmts';
        $oCmts = new $sClass ($this->_oDb->_sSupporterPrefix, $iEntryId);
        $oCmts->onObjectDelete ();

        // delete views
        //bx_import ('BxDolViews');
        //$oViews = new BxDolViews($this->_oDb->_sSupporterPrefix, $iEntryId, false);
        //$oViews->onObjectDelete();

 
        // arise alert
	//$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
	//$oAlert->alert();
    }        
 
    /*******[END] - Supporter Functions] ******************************/

    /******[BEGIN] Staff functions **************************/ 
    function actionStaff ($sAction, $sParam1='', $sParam2='') {
		
		switch($sAction){
			case 'download': 
				$this->staffDownload ($sParam1, $sParam2);
			break;
			case 'add': 
				$this->actionStaffAdd ($sParam1, '_modzzz_charity_page_title_staff_add');
			break;
			case 'edit':
				$this->actionStaffEdit ($sParam1, '_modzzz_charity_page_title_staff_edit');
			break;
			case 'delete':
				$this->actionStaffDelete ($sParam1, _t('_modzzz_charity_msg_charity_staff_was_deleted'));
			break;
			case 'view':
				$this->actionStaffView ($sParam1, _t('_modzzz_charity_msg_pending_staff_approval')); 
			break; 
			case 'browse':
				return $this->actionStaffBrowse ($sParam1, '_modzzz_charity_page_title_staff_browse'); 
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
		$iEntryId = (int)$aStaffEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        } 
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle] .' - '. $aStaffEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);

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
		$iCharityId = (int)$aStaffEntry['charity_id'];

        if (!($aDataEntry = $this->_oDb->getEntryById($iCharityId))) {
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
		 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        bx_import ('StaffFormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffFormEdit';
        $oForm = new $sClass ($this, $aStaffEntry['uri'], $iCharityId,  $iEntryId, $aStaffEntry);
  
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
		$iCharityId = (int)$aStaffEntry['charity_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iCharityId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iStaffId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedDelete($aDataEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iStaffId, 'ajaxy_popup_result_div');
            exit;
        }

        if ($this->_oDb->deleteStaffByIdAndOwner($iStaffId, $iCharityId, $this->_iProfileId, $this->isAdmin())) {
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
 
    function actionStaffAdd ($iCharityId, $sTitle) {
  
        if (!$this->isAllowedAdd()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

		if (!($aDataEntry = $this->_oDb->getEntryById($iCharityId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
		
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));	 

        $this->_addStaffForm($iCharityId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);  
    }
 
    function _addStaffForm ($iCharityId) { 
 
        bx_import ('StaffFormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'StaffFormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId, $iCharityId);
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
 
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getStaffEntryById($iEntryId);
    
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/view/' . $aDataEntry[$this->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured'));
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
				$this->actionFaqAdd ($sIdUri, '_modzzz_charity_page_title_faq_add');
			break;
			case 'edit':
				$this->actionFaqEdit ($sIdUri, '_modzzz_charity_page_title_faq_edit');
			break;
			case 'delete':
				$this->actionFaqDelete ($sIdUri, _t('_modzzz_charity_msg_charity_faq_was_deleted'));
			break;
			case 'view':
				$this->actionFaqView ($sIdUri, _t('_modzzz_charity_msg_pending_faq_approval')); 
			break; 
			case 'browse': 
				return $this->actionFaqBrowse ($sIdUri, '_modzzz_charity_page_title_faq_browse'); 
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
		$iEntryId = (int)$aFaqEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
 
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aFaqEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 
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
 
        $this->_oTemplate->pageCode($aFaqEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false); 
    }
 
    function actionFaqEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aFaqEntry = $this->_oDb->getFaqEntryById($iEntryId);
		$iFaqId = (int)$aFaqEntry['charity_id'];
  
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
		$iEntryId = (int)$aFaqEntry['charity_id'];
  
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin()))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
            exit;
        }

        if (!$this->isAllowedSubDelete($aDataEntry, $aFaqEntry) || 0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_result_div');
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
 
		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        $this->_addFaqForm($iEntryId);

		$this->_oTemplate->addJs ('charity.js'); 
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

        if ($oForm->isSubmittedAndValid () && $oForm->getCleanValue('charity_id')) {
 
			$sStatus = 'approved';
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId 
            );                        
  
			$iFaqEntryId = $this->_oDb->getFaqByCharity($oForm->getCleanValue('charity_id'));
			if(!$iFaqEntryId){
				$this->_oDb->_sTableMain = 'faq_main'; 
				$iFaqEntryId = $oForm->insert ($aValsAdd);
				$this->_oDb->_sTableMain = 'main';
			}

			$this->_oDb->addFaqItems($iFaqEntryId, $oForm->getCleanValue('charity_id'));
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
        $aCheck = checkAction($this->_iProfileId, BX_CHARITY_CREATE_FAQS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;     
    }

    /*******[END - Faq Functions] ******************************/

	/******[BEGIN] Review functions **************************/ 
     
	function actionReview ($sAction, $sParam1='', $sParam2='' ) {
 
		switch($sAction){
			case 'download': 
				$this->reviewDownload ($sParam1, $sParam2);
			break; 
			case 'add': 
				$this->actionReviewAdd ($sParam1, '_modzzz_charity_page_title_review_add');
			break;
			case 'edit':
				$this->actionReviewEdit ($sParam1, '_modzzz_charity_page_title_review_edit');
			break;
			case 'delete':
				$this->actionReviewDelete ($sParam1, _t('_modzzz_charity_msg_charity_review_was_deleted'));
			break;
			case 'view':
				$this->actionReviewView ($sParam1, _t('_modzzz_charity_msg_pending_review_approval')); 
			break; 
			case 'browse': 
				return $this->actionReviewBrowse ($sParam1, '_modzzz_charity_page_title_review_browse'); 
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
		$iEntryId = (int)$aReviewEntry['charity_id'];
 
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }
  
        $this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aReviewEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aDataEntry[$this->_oDb->_sFieldUri]);
 
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
 
        $this->_oTemplate->pageCode($aReviewEntry[$this->_oDb->_sFieldTitle] .' - '. $aDataEntry[$this->_oDb->_sFieldTitle], false, false);

        bx_import ('BxDolViews');
        new BxDolViews($this->_oDb->_sReviewPrefix, $aDataEntry[$this->_oDb->_sFieldId]);
    }
 
    function actionReviewEdit ($iEntryId, $sTitle) { 

        $iEntryId = (int)$iEntryId;

		$aReviewEntry = $this->_oDb->getReviewEntryById($iEntryId);
		$iReviewId = (int)$aReviewEntry['charity_id'];
  
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
		$iEntryId = (int)$aReviewEntry['charity_id'];
  
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
        $aCheck = checkAction($this->_iProfileId, BX_CHARITY_POST_REVIEWS, $isPerformAction);
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
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_VIEW_CHARITY, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;
  
        $this->_oSubPrivacy = new BxCharitySubPrivacy($this, $sTable); 
	    return $this->_oSubPrivacy->check('view', $aDataEntry['id'], $this->_iProfileId); 
    }

    function isAllowedRateSubProfile($sTable, &$aDataEntry) {       
        if ($this->isAdmin())
            return true;
        
		$this->_oSubPrivacy = new BxCharitySubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('rate', $aDataEntry['id'], $this->_iProfileId);        
    }

    function isAllowedCommentsSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;

        $this->_oSubPrivacy = new BxCharitySubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('comment', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadPhotosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForImages())
            return false;
        
		$this->_oSubPrivacy = new BxCharitySubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_photos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadVideosSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForVideos())
            return false;
        
		$this->_oSubPrivacy = new BxCharitySubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_videos', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadSoundsSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForSounds())
            return false;
        
		$this->_oSubPrivacy = new BxCharitySubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_sounds', $aDataEntry['id'], $this->_iProfileId);
    }

    function isAllowedUploadFilesSubProfile($sTable, &$aDataEntry) {
        if (!$this->_iProfileId) 
            return false;        
        if ($this->isAdmin())
            return true;
        if (!$this->isMembershipEnabledForFiles())
            return false;
        
		$this->_oSubPrivacy = new BxCharitySubPrivacy($this, $sTable); 
        return $this->_oSubPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }

    function actionUploadVideosSubProfile ($sType, $sUri) {
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadVideosSubProfile', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_charity_page_title_upload_videos'));
    }

    function actionUploadSoundsSubProfile ($sType, $sUri) {
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadSoundsSubProfile', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_charity_page_title_upload_sounds')); 
    }

    function actionUploadFilesSubProfile ($sType, $sUri) {
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadFilesSubProfile', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_charity_page_title_upload_files')); 
    }
 
    function actionUploadPhotosSubProfile ($sType, $sUri) {   
 
        $this->_actionUploadMediaSubProfile ($sType, $sUri, 'isAllowedUploadPhotosSubProfile', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_charity_page_title_upload_photos'));
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
			case 'programs':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableProgramsMediaPrefix;
				$sTable = $this->_oDb->_sTablePrograms ;
				$sDataFuncName = 'getProgramsEntryByUri';
			break; 
			case 'branches':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBranchesMediaPrefix;
				$sTable = $this->_oDb->_sTableBranches ;
				$sDataFuncName = 'getBranchesEntryByUri';
			break; 
			case 'supporter':
				$this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSupporterMediaPrefix;
				$sTable = $this->_oDb->_sTableSupporter ;
				$sDataFuncName = 'getSupporterEntryByUri';
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

        $aCharityEntry = $this->_oDb->getEntryById($aDataEntry['charity_id']);
  
        $GLOBALS['oTopMenu']->setCustomSubHeader($aCharityEntry[$this->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar($this->_sPrefix.'_view_uri', $aCharityEntry[$this->_oDb->_sFieldUri]);
  
  		$GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
			_t('_'.$this->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
			_t($sTitle) => '',
		));

        $iEntryId = $aDataEntry[$this->_oDb->_sFieldId];

        bx_import (ucwords($sType) . 'FormUploadMedia', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . ucwords($sType) . 'FormUploadMedia';
        $oForm = new $sClass ($this, $aDataEntry[$this->_oDb->_sFieldAuthorId],$aDataEntry['charity_id'], $iEntryId, $aDataEntry, $sMedia, $aMediaFields);
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
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_ADD_CHARITY, $isPerformAction);
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
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_EDIT_ANY_CHARITY, $isPerformAction);
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
		$aCheck = checkAction($this->_iProfileId, BX_CHARITY_DELETE_ANY_CHARITY, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }    

	//[end] [subprofile]


	/* functions added for v7.1 */

    function serviceGetMemberMenuItem ()
    {
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_charity'), _t('_modzzz_charity'), 'money');
    }
 
    function serviceGetMemberMenuItemAddContent ()
    {
        if (!$this->isAllowedAdd())
            return '';
        return parent::_serviceGetMemberMenuItem (_t('_modzzz_charity_charity_single'), _t('_modzzz_charity_charity_single'), 'money', false, '&filter=add_charity');
    }
 
   /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('charity', array(
            'part' => 'charity',
            'title' => '_modzzz_charity',
            'title_singular' => '_modzzz_charity_single',
            'icon' => 'modules/modzzz/charity/|map_marker.png',
            'icon_site' => 'money',
            'join_table' => 'modzzz_charity_main',
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
            'join_field_privacy' => 'allow_view_charity_to',
            'permalink' => 'modules/?r=charity/view/',
        )));
    }

    function serviceBranchesMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('charity_branches', array(
            'part' => 'charity_branches',
            'title' => '_modzzz_charity_branches',
            'title_singular' => '_modzzz_charity_branches_single',
            'icon' => 'modules/modzzz/charity/|map_marker.png',
            'icon_site' => 'money',
            'join_table' => 'modzzz_charity_branches_main',
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
            'permalink' => 'modules/?r=charity/branches/view/',
        )));
    }
 
    function serviceEventMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('charity_event', array(
            'part' => 'charity_event',
            'title' => '_modzzz_charity_event',
            'title_singular' => '_modzzz_charity_event_single',
            'icon' => 'modules/modzzz/charity/|map_marker.png',
            'icon_site' => 'money',
            'join_table' => 'modzzz_charity_event_main',
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
            'permalink' => 'modules/?r=charity/event/view/',
        )));
    }
 
	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_charity_wall_object',
            'txt_added_new_single' => '_modzzz_charity_wall_added_new',
            'txt_added_new_plural' => '_modzzz_charity_wall_added_new_items',
            'txt_privacy_view_event' => 'view_charity',
            'obj_privacy' => $this->_oPrivacy
        );
        return parent::_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_charity',
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
            'txt_privacy_view_event' => 'view_charity',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return parent::_serviceGetWallPostOutline($aEvent, 'money', $aParams);
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

	function branchesDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'branches_files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getBranchesEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableBranches, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }
 
	function programsDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'programs_files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getProgramsEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTablePrograms, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function membersDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'members_files');

        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getProgramsEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }

        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTablePrograms, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }
 
	function eventDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'event_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getEventEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableEvent, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function reviewDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'review_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getReviewEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableReview, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

	function staffDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'staff_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getStaffEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableStaff, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }


	function supporterDownload ($iEntryId, $iMediaId) {

        $aFileInfo = $this->_oDb->getMedia ((int)$iEntryId, (int)$iMediaId, 'supporter_files');
 
        if (!$aFileInfo || !($aDataEntry = $this->_oDb->getSupporterEntryById((int)$iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            exit;
        }
 
        if (!$this->isAllowedViewSubProfile($this->_oDb->_sTableSupporter, $aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            exit;
        }

        parent::_actionDownload($aFileInfo, 'media_id');
    }

    function isAllowedReadForum(&$aDataEntry, $iProfileId = -1)
    {
        return true;
    }




}

 