<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx 
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

function modzzz_faq_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'faq') {
        $oMain = BxDolModule::getInstance('BxFAQModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');

define ('BX_FAQ_PHOTOS_CAT', 'FAQ');
define ('BX_FAQ_PHOTOS_TAG', 'faq');

define ('BX_FAQ_VIDEOS_CAT', 'FAQ');
define ('BX_FAQ_VIDEOS_TAG', 'faq');

define ('BX_FAQ_SOUNDS_CAT', 'FAQ');
define ('BX_FAQ_SOUNDS_TAG', 'faq');

define ('BX_FAQ_FILES_CAT', 'FAQ');
define ('BX_FAQ_FILES_TAG', 'faq');


/*
 * FAQ module
 *
 * This module allow users to create user's faq, 
 * users can rate, comment and discuss faq.
 * FAQ can have photos, videos, sounds and files, uploaded
 * by faq's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add faq' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new faq was created
 * change - faq was chaned
 * rate - somebody rated faq
 * commentPost - somebody posted comment in faq
 *
 *
 *
 * Memberships/ACL:
 * faq view faq - BX_FAQ_VIEW_FAQ
 * faq browse - BX_FAQ_BROWSE
 * faq search - BX_FAQ_SEARCH
 * faq add faq - BX_FAQ_ADD_FAQ
 * faq comments delete and edit - BX_FAQ_COMMENTS_DELETE_AND_EDIT
 * faq edit any faq - BX_FAQ_EDIT_ANY_FAQ
 * faq delete any faq - BX_FAQ_DELETE_ANY_FAQ
 * faq mark as featured - BX_FAQ_MARK_AS_FEATURED
 * faq rate help - BX_FAQ_RATE_HELP
 * faq approve faq - BX_FAQ_APPROVE_FAQ
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different faq
 * @see BxFAQModule::serviceHomepageBlock
 * BxDolService::call('faq', 'homepage_block', array());
 *
 * Profile block with user's faq
 * @see BxFAQModule::serviceProfileBlock
 * BxDolService::call('faq', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for faq (for internal usage only)
 * @see BxFAQModule::serviceGetMemberMenuItem
 * BxDolService::call('faq', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_faq'
 * The following alerts are rised
 *
 
 *
 *  add - new faq was added
 *      $iObjectId - faq id
 *      $iSenderId - creator of a faq
 *      $aExtras['Status'] - status of added faq
 *
 *  change - faq's info was changed
 *      $iObjectId - faq id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed faq
 *
 *  delete - faq was deleted
 *      $iObjectId - faq id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - faq was marked/unmarked as featured
 *      $iObjectId - faq id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if faq was marked as featured and 0 - if faq was removed from featured 
 *
 */
class BxFAQModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_aQuickCache = array ();

    function BxFAQModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_faq_filter';
        $this->_sPrefix = 'modzzz_faq';

        bx_import ('Privacy', $aModule);
        $this->_oPrivacy = new BxFAQPrivacy($this);

	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxFAQModule'] = &$this;
    }

    //[begin] added patch 2.0.5
    function actionBrowse ($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {

        //if (('my' == $sMode) && (!$this->isAdmin())) {
		//	 $this->actionHome(); 
        //}else{
			parent::actionBrowse($sMode,$sValue,$sValue2,$sValue3);
		//}
	}
    //[end] added patch 2.0.5


    function actionHome () {
        parent::_actionHome(_t('_modzzz_faq_page_title_home'));
    }

    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_faq_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_faq_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_faq_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_faq_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_faq_page_title_comments'));
    }
  
    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_modzzz_faq_msg_pending_approval'));
    }

    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_faq_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_faq_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_faq_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_faq_page_title_upload_files')); 
    }
  
    function actionCalendar ($iYear = '', $iMonth = '') {
        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_faq_page_title_calendar'));
    }

    function actionSearch ($sKeyword = '', $sCategory = '') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_REQUEST['Keyword'] = $sKeyword;
        if ($sCategory)
            $_REQUEST['Category'] = explode(',', $sCategory);

        if (is_array($_REQUEST['Category']) && 1 == count($_REQUEST['Category']) && !$_REQUEST['Category'][0]) {
            unset ($_REQUEST['Category']);
            unset($sCategory);
        }

        if ($sCategory || $sKeyword) {
            $_REQUEST['submit_form'] = 1;
        }
        
        bx_import ('FormSearch', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormSearch';
        $oForm = new $sClass ();
        $oForm->initChecker();        

        if ($oForm->isSubmittedAndValid ()) {

            bx_import ('SearchResult', $this->_aModule);
            $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
            $o = new $sClass('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'));

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
        $this->_oTemplate->pageCode(_t('_modzzz_faq_page_title_search'));
    }
 
    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_faq_page_title_add'));
    }

    function actionEdit ($iEntryId) {
        parent::_actionEdit ($iEntryId, _t('_modzzz_faq_page_title_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_faq_msg_faq_was_deleted'));
    }

	/*[begin] add AJAX action */
	function actionRateUp ($iEntryId) {
        $this->_actionRateHelp ($iEntryId, 'up', _t('_modzzz_faq_msg_rate_up'));
    }
	
	function actionRateDown ($iEntryId) {
        $this->_actionRateHelp ($iEntryId, 'down', _t('_modzzz_faq_msg_rate_down'));
    }
 
    function _actionRateHelp ($iEntryId, $sAction, $sMsgSuccessAdd ) {

        $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_help_div');
            exit;
        }
 
        if (!$this->isAllowedRateHelp($aDataEntry)) {
            echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_help_div');
            exit;
        }
 
        if ($this->_oDb->rateHelp($iEntryId, $sAction, $this->_iProfileId)) {
            $this->isAllowedRateHelp($aDataEntry, true); // perform action
            $this->onEventRateHelp ($iEntryId, $aDataEntry);
            $sRedirect = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
            $sJQueryJS = genAjaxyPopupJS($iEntryId, 'ajaxy_popup_help_div', $sRedirect);
            echo MsgBox($sMsgSuccessAdd) . $sJQueryJS;
            exit;
        }        

        echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iEntryId, 'ajaxy_popup_help_div');
        exit;        
    }
 
    function isAllowedRateHelp ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_FAQ_RATE_HELP, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
  
    function onEventRateHelp ($iEntryId, $aDataEntry) {

        // arise alert
		//$oAlert = new BxDolAlerts($this->_sPrefix, 'rate_help', $iEntryId, $this->_iProfileId, array('Featured' => $aDataEntry[$this->_oDb->_sFieldFeatured]));
		//$oAlert->alert();
    }   
	/*[end] add AJAX action */
 
    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_faq_msg_added_to_featured'), _t('_modzzz_faq_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_faq_caption_share_faq'));
    }
 
    function actionTags() {
        parent::_actionTags (_t('_modzzz_faq_page_title_tags'));
    }    

    function actionCategories() {
        parent::_actionCategories (_t('_modzzz_faq_page_title_categories'));
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
     * Homepage block with different faq
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent()){ 
			return '';
        } 

        bx_import ('PageMain', $this->_aModule);
        $o = new BxFAQPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';

        $this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_faq_homepage_default_tab');
        $sBrowseMode = $sDefaultHomepageTab;
        switch ($_GET['modzzz_faq_filter']) {            
            case 'featured':
            case 'recent':
            case 'top':
            case 'popular':
            case $sDefaultHomepageTab:            
                $sBrowseMode = $_GET['modzzz_faq_filter'];
                break;
        }

        return $o->ajaxBrowse(
            $sBrowseMode,
            $this->_oDb->getParam('modzzz_faq_perpage_homepage'), 
            array(
                _t('_modzzz_faq_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?modzzz_faq_filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_faq_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?modzzz_faq_filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_faq_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?modzzz_faq_filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_faq_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?modzzz_faq_filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

	function actionProcess() 
	{   
		 $this -> _oDb -> processDaily();
	}
 
    /**
     * Member accountpage block with different faq
     * @return html to display on accountpage in a block
     */     
    function serviceDailyTipBlock ($sPage) {
   
        bx_import ('PageMain', $this->_aModule);
        $o = new BxFAQPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . $sPage . '?';
		
		//$this->sFilterName
        $this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse(
            'daily',
            1/*$this->_oDb->getParam('modzzz_faq_perpage_accountpage')*/, 
            array(), '', false, false
        ); 
    }
 
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_faq'), _t('_modzzz_faq'), 'question-circle');
    }

    function serviceGetWallPost ($aEvent) {
         return parent::_serviceGetWallPost ($aEvent, _t('_modzzz_faq_wall_object'), _t('_modzzz_faq_wall_added_new'));
    }

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return parent::_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_faq_spy_post',
            'change' => '_modzzz_faq_spy_post_change', 
            'rate' => '_modzzz_faq_spy_rate',
            'commentPost' => '_modzzz_faq_spy_comment',
        ));
    }

    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_faq_sbs_change'),
            'commentPost' => _t('_modzzz_faq_sbs_comment'),
            'rate' => _t('_modzzz_faq_sbs_rate'), 
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions

    function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
            'pending_approval' => array(
                'title' => _t('_modzzz_faq_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'admin_entries' => array(
                'title' => _t('_modzzz_faq_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),            
            'create' => array(
                'title' => _t('_modzzz_faq_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_modzzz_faq_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'pending_approval';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_faq_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
     
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_faq_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('FAQ');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_faq_admin_delete', '_modzzz_faq_admin_activate');
    }

    // ================================== events
 

 
    // ================================== permissions
    
    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $aDataEntry['author_id'] == $this->_iProfileId) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FAQ_VIEW_FAQ, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;

        // check user faq 
	    return $this->_oPrivacy->check('view_faq', $aDataEntry['id'], $this->_iProfileId); 
    }

    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FAQ_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FAQ_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FAQ_ADD_FAQ, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FAQ_EDIT_ANY_FAQ, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_FAQ_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
  
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FAQ_DELETE_ANY_FAQ, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
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
        if (!$this->isMembershipEnabledForFiles())
            return false;                        
        return $this->_oPrivacy->check('upload_files', $aDataEntry['id'], $this->_iProfileId);
    }
 
    function isAllowedCreatorCommentsDeleteAndEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) return true;        
        if (!$GLOBALS['logged']['member'] || $aDataEntry['author_id'] != $this->_iProfileId)
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_FAQ_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_faq') == 'on'));
		 
        return $bEnabled;
    }

    function _defineActions () {
        defineMembershipActions(array('faq view faq', 'faq browse', 'faq search', 'faq add faq', 'faq comments delete and edit', 'faq edit any faq', 'faq delete any faq', 'faq mark as featured', 'faq approve faq','faq rate help' ));
    }

    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_faq_page_title_my_faq'));
    } 
	
    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array()) {
  
		if ('approved' == $sStatus) {
            $this->reparseTags ($iEntryId);
            $this->reparseCategories ($iEntryId);
        }
 		$oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
		$oAlert->alert();
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
 
        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
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

    function isEntryAdmin($aDataEntry, $iIdProfile = 0) {
        if (!$iIdProfile)
            $iIdProfile = $this->_iProfileId;
        return ($this->isAdmin() || $aDataEntry['author_id'] == $iIdProfile);
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 300, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }
 
}
