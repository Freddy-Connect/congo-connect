<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx News
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

function modzzz_news_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'news') {
        $oMain = BxDolModule::getInstance('BxNewsModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');

define ('BX_NEWS_PHOTOS_CAT', 'News');
define ('BX_NEWS_PHOTOS_TAG', 'news');

define ('BX_NEWS_VIDEOS_CAT', 'News');
define ('BX_NEWS_VIDEOS_TAG', 'news');

define ('BX_NEWS_SOUNDS_CAT', 'News');
define ('BX_NEWS_SOUNDS_TAG', 'news');

define ('BX_NEWS_FILES_CAT', 'News');
define ('BX_NEWS_FILES_TAG', 'news');


/*
 * News module
 *
 * This module allow users to create user's news, 
 * users can rate, comment and discuss news.
 * News can have photos, videos, sounds and files, uploaded
 * by news's admins.
 *
 * 
 *
 * Profile's Wall:
 * 'add news' event is displayed in profile's wall
 *
 *
 *
 * Spy:
 * The following qactivity is displayed for content_activity:
 * add - new news was created
 * change - news was chaned
 * rate - somebody rated news
 * commentPost - somebody posted comment in news
 *
 *
 *
 * Memberships/ACL:
 * news view news - BX_NEWS_VIEW_NEWS
 * news browse - BX_NEWS_BROWSE
 * news search - BX_NEWS_SEARCH
 * news add news - BX_NEWS_ADD_NEWS
 * news comments delete and edit - BX_NEWS_COMMENTS_DELETE_AND_EDIT
 * news edit any news - BX_NEWS_EDIT_ANY_NEWS
 * news delete any news - BX_NEWS_DELETE_ANY_NEWS
 * news mark as featured - BX_NEWS_MARK_AS_FEATURED
 * news approve news - BX_NEWS_APPROVE_NEWS
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different news
 * @see BxNewsModule::serviceHomepageBlock
 * BxDolService::call('news', 'homepage_block', array());
 *
 * Profile block with user's news
 * @see BxNewsModule::serviceProfileBlock
 * BxDolService::call('news', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for news (for internal usage only)
 * @see BxNewsModule::serviceGetMemberMenuItem
 * BxDolService::call('news', 'get_member_menu_item', array());
 *
 *
 *
 * Alerts:
 * Alerts type/unit - 'modzzz_news'
 * The following alerts are rised
 *
 
 *
 *  add - new news was added
 *      $iObjectId - news id
 *      $iSenderId - creator of a news
 *      $aExtras['Status'] - status of added news
 *
 *  change - news's info was changed
 *      $iObjectId - news id
 *      $iSenderId - editor user id
 *      $aExtras['Status'] - status of changed news
 *
 *  delete - news was deleted
 *      $iObjectId - news id
 *      $iSenderId - deleter user id
 *
 *  mark_as_featured - news was marked/unmarked as featured
 *      $iObjectId - news id
 *      $iSenderId - performer id
 *      $aExtras['Featured'] - 1 - if news was marked as featured and 0 - if news was removed from featured 
 *
 */
class BxNewsModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_aQuickCache = array ();

    function BxNewsModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_news';

        bx_import ('Privacy', $aModule);
        $this->_oPrivacy = new BxNewsPrivacy($this);

	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxNewsModule'] = &$this;

		//reloads subcategories on Add form
		if($_GET['ajax']=='cat') { 
			$iParentId = bx_get('parent');
			echo $this->_oDb->getAjaxCategoryOptions($iParentId);
			exit;
		}  

		if($_GET['ajax']=='state') { 
			$sCountryCode = bx_get('country');
			echo $this->_oDb->getStateOptions($sCountryCode);
			exit;
		} 
    }
  
    function actionBrowse ($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '')
    {
        if ('user' == $sMode || 'my' == $sMode) {
            $aProfile = getProfileInfo ($this->_iProfileId);
            if (0 == strcasecmp($sValue, $aProfile['NickName']) || 'my' == $sMode) {
                $this->_browseMy ($aProfile);
                return;
            }
        }

        if (!$this->isAllowedBrowse()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
        if ('tag' == $sMode || 'category' == $sMode)
            $sValue = uri2title($sValue);

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass(process_db_input($sMode, BX_TAGS_STRIP), process_db_input($sValue, BX_TAGS_STRIP), process_db_input($sValue2, BX_TAGS_STRIP), process_db_input($sValue3, BX_TAGS_STRIP));

        if ($o->isError) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        if (bx_get('rss')) {
            echo $o->rss();
            exit;
        }

        $this->_oTemplate->pageStart();
  
        if ($s = $o->processing()) { 
            echo $s;
        } else {
            $this->_oTemplate->displayNoData ();
            return;
        }

        $this->_oTemplate->addCss (array('unit.css', 'main.css', 'twig.css'));
        $this->_oTemplate->pageCode($o->aCurrent['title'], false, false);
    }
  
    function actionHome () {
        parent::_actionHome(_t('_modzzz_news_page_title_home'));
    }
  
    function actionFiles ($sUri) {
        parent::_actionFiles ($sUri, _t('_modzzz_news_page_title_files'));
    }

    function actionSounds ($sUri) {
        parent::_actionSounds ($sUri, _t('_modzzz_news_page_title_sounds'));
    }

    function actionVideos ($sUri) {
        parent::_actionVideos ($sUri, _t('_modzzz_news_page_title_videos'));
    }

    function actionPhotos ($sUri) {
        parent::_actionPhotos ($sUri, _t('_modzzz_news_page_title_photos'));
    }

    function actionComments ($sUri) {
        parent::_actionComments ($sUri, _t('_modzzz_news_page_title_comments'));
    }
  
    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_modzzz_news_msg_pending_approval'));
    }

    function actionUploadPhotos ($sUri) {        
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadPhotos', 'images', array ('images_choice', 'images_upload'), _t('_modzzz_news_page_title_upload_photos'));
    }

    function actionUploadVideos ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadVideos', 'videos', array ('videos_choice', 'videos_upload'), _t('_modzzz_news_page_title_upload_videos'));
    }

    function actionUploadSounds ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadSounds', 'sounds', array ('sounds_choice', 'sounds_upload'), _t('_modzzz_news_page_title_upload_sounds')); 
    }

    function actionUploadFiles ($sUri) {
        parent::_actionUploadMedia ($sUri, 'isAllowedUploadFiles', 'files', array ('files_choice', 'files_upload'), _t('_modzzz_news_page_title_upload_files')); 
    }
 
    function actionCalendar ($iYear = '', $iMonth = '') { 

        parent::_actionCalendar ($iYear, $iMonth, _t('_modzzz_news_page_title_calendar'));
    }
  
    function actionSearch ($sKeyword = '', $sParent = '-', $sCategory = '-') {

        if (!$this->isAllowedSearch()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();

        if ($sKeyword) 
            $_REQUEST['Keyword'] = $sKeyword;
        if ($sCategory)
            $_GET['Category'] = $sCategory;
        if ($sParent)
            $_GET['Parent'] = $sParent; 
 
        if ($sCategory || $sParent  || $sKeyword) {
            $_GET['submit_form'] = 1;
        }
        
        bx_import ('FormSearch', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormSearch';
        $oForm = new $sClass ();
        $oForm->initChecker();        

        if ($oForm->isSubmittedAndValid ()) {

            bx_import ('SearchResult', $this->_aModule);
            $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
            $o = new $sClass('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Parent'),$oForm->getCleanValue('Category'));

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
        $this->_oTemplate->pageCode(_t('_modzzz_news_page_title_search'));
    }

    function actionAdd () {
        parent::_actionAdd (_t('_modzzz_news_page_title_add'));
    }

    function _addForm ($sRedirectUrl) {

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $this->_iProfileId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = $this->isAutoApproved() ? 'approved' : 'pending';
 
            $sStatus = (!$oForm->getCleanValue('publish')) ? 'draft' : $sStatus;
  
			$sTitle = strtolower($oForm->getCleanValue('title'));
			$iLength = strlen($sTitle);
			for ($i=0; $i<$iLength; $i++) {
				if (preg_match("/^[a-z]$/i", $sTitle[$i])) {
					$sLetter = $sTitle[$i]; 
					break;
				}
			}

            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => $oForm->generateUri(),
                $this->_oDb->_sFieldStatus => $sStatus,
				$this->_oDb->_sFieldAuthorId => $this->_iProfileId,
                $this->_oDb->_sFieldLetter => $sLetter  
            );                        
     
            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {

                $this->isAllowedAdd(true); // perform action                 

				//rss processing
				//$this->_oDb->addRss($iEntryId);

                $oForm->processMedia($iEntryId, $this->_iProfileId);
				
				$this->_oDb->addYoutube($iEntryId);

                $aDataEntry = $this->_oDb->getEntryByIdAndOwner($iEntryId, $this->_iProfileId, $this->isAdmin());
                $this->onEventCreate($iEntryId, $sStatus, $aDataEntry);
                if (!$sRedirectUrl)
                    $sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri];
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
 
			$sStatus = $this->isAutoApproved() ? 'approved' : 'pending';
 
            $sStatus = (!$oForm->getCleanValue('publish')) ? 'draft' : $sStatus;
 
			$sTitle = strtolower($oForm->getCleanValue('title'));
			$iLength = strlen($sTitle);
			for ($i=0; $i<$iLength; $i++) {
				if (preg_match("/^[a-z]$/i", $sTitle[$i])) {
					$sLetter = $sTitle[$i]; 
					break;
				}
			}
 
            $aValsAdd = array (
				$this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldLetter => $sLetter  
			);
            if ($oForm->update ($iEntryId, $aValsAdd)) {
 
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
						$this->_oDb->removeYoutubeEntry($iYoutubeId);
					}
				} 
 
				$this->_oDb->addYoutube($iEntryId);
 				//[end] youtube 

/*
				$aFeeds2Keep = array();
				if(is_array($_POST['prev_feed'])){
					foreach ($_POST['prev_feed'] as $iFeedId){
						$aFeeds2Keep[$iFeedId] = $iFeedId;
					}
				}
				$aFeedIds = $this->_oDb->getRssIds($iEntryId);
			
				$aDeletedFeed = array_diff ($aFeedIds, $aFeeds2Keep);

				if ($aDeletedFeed) {
					foreach ($aDeletedFeed as $iFeedId) {
						$this->_oDb->removeRss($iEntryId, $iFeedId);
					}
				}
		  
				$this->_oDb->addRss($iEntryId);
*/ 
                $oForm->processMedia($iEntryId, $this->_iProfileId);

                $this->isAllowedEdit($aDataEntry, true); // perform action

                $this->onEventChanged ($iEntryId, $sStatus);
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri]);
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

    function actionEdit ($iEntryId) {
        $this->_actionEdit ($iEntryId, _t('_modzzz_news_page_title_edit'));
    }

    function actionDelete ($iEntryId) {
        parent::_actionDelete ($iEntryId, _t('_modzzz_news_msg_news_was_deleted'));
    }

    function actionMarkFeatured ($iEntryId) {
        parent::_actionMarkFeatured ($iEntryId, _t('_modzzz_news_msg_added_to_featured'), _t('_modzzz_news_msg_removed_from_featured'));
    }
 
    function actionSharePopup ($iEntryId) {
        parent::_actionSharePopup ($iEntryId, _t('_modzzz_news_caption_share_news'));
    }
 
    function actionTags() {
        parent::_actionTags (_t('_modzzz_news_page_title_tags'));
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


    /**
     * Category block with news for a specified Category
     * @param $sCategory Category
     * @return html to display all news for a particular Category in a block
     */     
    function serviceCategoryBlock ($sCategory, $iPerPage=10) {
	    bx_import ('PageMain', $this->_aModule);
        $o = new BxNewsPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . $_SERVER["REQUEST_URI"];
		$o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');  

        $this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse('category',$iPerPage,array(),$sCategory); 
    }

    /**
     * Tag block with news for a specified Tag
     * @param $sTag Tag
     * @return html to display all news for a particular Tag in a block
     */       
    function serviceTagBlock ($sTag, $iPerPage=10) {
	    bx_import ('PageMain', $this->_aModule);
        $o = new BxNewsPageMain ($this);        
        $o->sUrlStart = $GLOBALS['site']['url'] . $_SERVER["REQUEST_URI"];
		$o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');  

        $this->_oTemplate->addCss (array('unit.css', 'twig.css'));

        return $o->ajaxBrowse('tag',$iPerPage,array(),$sTag); 
    }




    // ================================== external actions

    /**
     * Homepage block with different news
     * @return html to display on homepage in a block
     */     
    function serviceHomepageBlock () {

        if (!$this->_oDb->isAnyPublicContent()){ 
			return '';
        } 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxNewsPageMain ($this);
        $o->sUrlStart = BX_DOL_URL_ROOT . 'index.php?';
        
        $this->_oTemplate->addCss (array('unit.css', 'twig.css'));
 
        $sDefaultHomepageTab = $this->_oDb->getParam('modzzz_news_homepage_default_tab');
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
            $this->_oDb->getParam('modzzz_news_perpage_homepage'), 
            array(
                _t('_modzzz_news_tab_featured') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=featured', 'active' => 'featured' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_news_tab_recent') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=recent', 'active' => 'recent' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_news_tab_top') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=top', 'active' => 'top' == $sBrowseMode, 'dynamic' => true),
                _t('_modzzz_news_tab_popular') => array('href' => BX_DOL_URL_ROOT . 'index.php?filter=popular', 'active' => 'popular' == $sBrowseMode, 'dynamic' => true),
            )
        );
    }

    /**
     * Profile block with user's news
     * @param $iProfileId profile id 
     * @return html to display on homepage in a block
     */     
    function serviceProfileBlock ($iProfileId) {
        $iProfileId = (int)$iProfileId;
        $aProfile = getProfileInfo($iProfileId);
        bx_import ('PageMain', $this->_aModule);
        $o = new BxNewsPageMain ($this);        
        $o->sUrlStart = getProfileLink($aProfile['ID']) . '?';
        
        $this->_oTemplate->addCss (array('unit.css', 'twig.css'));
 
        return $o->ajaxBrowse(
            'user', 
            $this->_oDb->getParam('modzzz_news_perpage_profile'), 
            array(),
            $aProfile['NickName'],
            true,
            false 
        );
    }

    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_news'), _t('_modzzz_news'), 'news.png');
    }

    function serviceGetWallPostOLD ($aEvent) {
         return parent::_serviceGetWallPost ($aEvent, _t('_modzzz_news_wall_object'), _t('_modzzz_news_wall_added_new'));
    }

    function serviceGetSpyPost($sAction, $iObjectId = 0, $iSenderId = 0, $aExtraParams = array()) {
        return $this->_serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, array(
            'add' => '_modzzz_news_spy_post',
            'change' => '_modzzz_news_spy_post_change', 
            'rate' => '_modzzz_news_spy_rate',
            'commentPost' => '_modzzz_news_spy_comment',
        ));
    }

    function _serviceGetSpyPost($sAction, $iObjectId, $iSenderId, $aExtraParams, $aLangKeys)
    {
        $aProfile = getProfileInfo($iSenderId);
        if (!($aDataEntry = $this->_oDb->getEntryByIdAndOwner ($iObjectId, 0, true)))
            return array();
        if (empty($aLangKeys[$sAction]))
            return array();

		if($aDataEntry[$this->_oDb->_sFieldAnonymous])
            return array();
         
		return array(
            'lang_key' => $aLangKeys[$sAction],
            'params' => array(
                'profile_link' => $aProfile ? getProfileLink($iSenderId) : 'javascript:void(0)',
                'profile_nick' => $aProfile ? getNickName($aProfile['ID']) : _t('_Guest'),
                'entry_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$this->_oDb->_sFieldUri],
                'entry_title' => $aDataEntry[$this->_oDb->_sFieldTitle],
            ),
            'recipient_id' => $aDataEntry[$this->_oDb->_sFieldAuthorId],
            'spy_type' => 'content_activity',
        );
    }


    function serviceGetSubscriptionParams ($sAction, $iEntryId) {

        $a = array (
            'change' => _t('_modzzz_news_sbs_change'),
            'commentPost' => _t('_modzzz_news_sbs_comment'),
            'rate' => _t('_modzzz_news_sbs_rate'), 
        );

        return parent::_serviceGetSubscriptionParams ($sAction, $iEntryId, $a);
    }

    // ================================== admin actions

    function actionAdministration ($sUrl = '',$sParam1 = '', $sParam2 = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
 
            'pending_approval' => array(
                'title' => _t('_modzzz_news_menu_admin_pending_approval'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_approval', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ), 
            'pending_publish' => array(
                'title' => _t('_modzzz_news_menu_admin_pending_publish'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/pending_publish', 
                '_func' => array ('name' => 'actionAdministrationPublish', 'params' => array()),
            ), 
            'admin_entries' => array(
                'title' => _t('_modzzz_news_menu_admin_entries'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/admin_entries',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),            
            'create' => array(
                'title' => _t('_modzzz_news_menu_admin_add_entry'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreateEntry', 'params' => array()),
            ),
           'categories' => array(
                'title' => _t('_modzzz_news_menu_admin_manage_categories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories',
                '_func' => array ('name' => 'actionAdministrationCategories', 'params' => array($sParam1)),
            ),
            'subcategories' => array(
                'title' => _t('_modzzz_news_menu_admin_manage_subcategories'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories',
                '_func' => array ('name' => 'actionAdministrationSubCategories', 'params' => array($sParam1,$sParam2)),
            ),  
            'settings' => array(
                'title' => _t('_modzzz_news_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'admin_entries';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_news_page_title_administration'), $aMenu);
        
		$this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
     
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_news_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('News');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_news_admin_delete', '_modzzz_news_admin_activate');
    }
  
    function actionAdministrationPublish () {
		$sKeyBtnDelete = '_modzzz_news_admin_delete';
		$sKeyBtnPublish = '_modzzz_news_admin_publish';

        if ($_POST['action_publish'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                if ($this->_oDb->publishEntry($iId)) {
                    $this->onEventChanged ($iId, 'publish');
                }
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {

                $aDataEntry = $this->_oDb->getEntryById($iId);
                if (!$this->isAllowedDelete($aDataEntry)) 
                    continue;

                if ($this->_oDb->deleteEntryByIdAndOwner($iId, 0, $this->isAdmin())) {
                    $this->onEventDeleted ($iId);
                }
            }
        }
 
		$sContent = $this->_manageEntries ('draft', '', true, 'bx_twig_admin_form', array(
			'action_publish' => $sKeyBtnPublish,
			'action_delete' => $sKeyBtnDelete,
		));
    
        return $sContent;
    }



    // ================================== events
 

 
    // ================================== permissions
    
    function isAllowedView ($aDataEntry, $isPerformAction = false) {

        // admin and owner always have access
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_VIEW_NEWS, $isPerformAction);
        if ($aCheck[CHECK_ACTION_RESULT] != CHECK_ACTION_RESULT_ALLOWED)
            return false;

        // check user news 
	    $isAllowed = $this->_oPrivacy->check('view_news', $aDataEntry['id'], $this->_iProfileId); 
       
        return $isAllowed && $this->_isAllowedViewByMembership ($aDataEntry);
    }

    function _isAllowedViewByMembership (&$aDataEntry) { 
        if (!$aDataEntry['news_membership_filter']) return true;

        require_once(BX_DIRECTORY_PATH_INC . 'membership_levels.inc.php');
        $aMemebrshipInfo = getMemberMembershipInfo($this->_iProfileId);
 
		$aAllowed = explode(';', $aDataEntry['news_membership_filter']);

		foreach($aAllowed as $aEachFilter){
			if($aMemebrshipInfo['DateExpires']){ 
				if($aEachFilter == $aMemebrshipInfo['ID'] && $aMemebrshipInfo['DateStarts'] < time() && $aMemebrshipInfo['DateExpires'] > time())
					return true;
			}else{
				if($aEachFilter == $aMemebrshipInfo['ID'] && $aMemebrshipInfo['DateStarts'] < time())
					return true; 
			}
		}

		return false;
    }
 
    function isAllowedBrowse ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_BROWSE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedSearch ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_SEARCH, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedAdd ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        if (!$GLOBALS['logged']['member']) 
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_ADD_NEWS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 

    function isAllowedEdit ($aDataEntry, $isPerformAction = false) {

        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;

        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_EDIT_ANY_NEWS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    } 
 
    function isAllowedMarkAsFeatured ($aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_NEWS_MARK_AS_FEATURED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;        
    }
  
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || $this->isEntryAdmin($aDataEntry)) 
            return true;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_DELETE_ANY_NEWS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
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
        if (getParam('modzzz_news_author_comments_admin') && $this->isEntryAdmin($aDataEntry))
            return false;
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_COMMENTS_DELETE_AND_EDIT, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function isAllowedManageAdmins($aDataEntry) {
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))
            return true;
        return false;
    }
   
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('modzzz_news_permalinks') == 'on'));
		 
        return $bEnabled;
    }

	function getConcatenator() { 
		return $this->isPermalinkEnabled() ? '?' : '&';
	}

    function isAutoApproved ($isPerformAction = false) {
        if ($this->isAdmin()) 
            return true;

        if($this->_oDb->getParam($this->_sPrefix.'_autoapproval') != 'on')
            return false;

        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_AUTOAPPROVE_NEWS, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

    function _defineActions () {
        defineMembershipActions(array('news view news', 'news browse', 'news search', 'news add news', 'news comments delete and edit', 'news edit any news', 'news delete any news', 'news mark as featured', 'news approve news','news autoapprove news', 'news allow embed'));
    }

    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_news_page_title_my_news'));
    } 
	
    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array()) {
  
		if ('approved' == $sStatus) {
            $this->reparseTags ($iEntryId);
            //$this->reparseCategories ($iEntryId);  
        }

        if (BxDolModule::getInstance('BxWmapModule')){
            BxDolService::call('wmap', 'response_entry_add', array($this->_oConfig->getUri(), $iEntryId));
		}

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

    function isEntryAdmin($aDataEntry, $iProfileId = 0)
    {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
        return $this->_oDb->isGroupAdmin ($aDataEntry['id'], $iProfileId) && isProfileActive($iProfileId);
    }
 
    function isEntryOwner($aDataEntry, $iIdProfile = 0) {
        if (!$iProfileId)
            $iProfileId = $this->_iProfileId;
        if (($GLOBALS['logged']['member'] || $GLOBALS['logged']['admin']) && $aDataEntry['author_id'] == $iProfileId && isProfileActive($iProfileId))
            return true;
    }


   //DOLPHIN 7.1

    function serviceGetMemberMenuItemAddContent ()
    {
      //  if (!$this->isAllowedAdd())
       //     return '';
       // return parent::_serviceGetMemberMenuItem (_t('_modzzz_news_news_single'), _t('_modzzz_news_news_single'), 'file', false, '&filter=add_news');
    }


	//remove old one first
    function serviceGetWallPost ($aEvent)
    {
        $aParams = array(
            'txt_object' => '_modzzz_news_wall_object',
            'txt_added_new_single' => '_modzzz_news_wall_added_new',
            'txt_added_new_plural' => '_modzzz_news_wall_added_new_items',
            'txt_privacy_view_event' => 'view_news',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallPost ($aEvent, $aParams);
    }
 
    function serviceGetWallPostComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_news',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallPostComment($aEvent, $aParams);
    }
 
    function serviceGetWallPostOutline($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_news',
            'obj_privacy' => $this->_oPrivacy,
            'templates' => array(
                'grouped' => 'wall_outline_grouped'
            )
        );
        return $this->_serviceGetWallPostOutline($aEvent, 'file', $aParams);
    }
 
    function _serviceGetWallPostComment($aEvent, $aParams)
    {
        $iId = (int)$aEvent['object_id'];
        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId))
            return '';

        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = ($iOwner) ? getNickName($iOwner) : _t('_modzzz_news_anonymous');

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || !isset($aContent['comment_id']))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iId);
        if(!$oCmts->isEnabled())
            return '';

        $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $iOwner, 1);

		if($aItem[$this->_oDb->_sFieldAnonymous]) return '';
             
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
 
     function _serviceGetWallPostOutline($aEvent, $sIcon, $aParams = array())
    {
        $iNoPhotoWidth = $iNoPhotoHeight = 140;
        $sNoPhoto = $this->_oTemplate->getImageUrl('no-image-thumb.png');
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/';

        $aOwner = db_assoc_arr("SELECT `ID` AS `id`, `NickName` AS `username` FROM `Profiles` WHERE `ID`='" . (int)$aEvent['owner_id'] . "' LIMIT 1");

        $aObjectIds = strpos($aEvent['object_id'], ',') !== false ? explode(',', $aEvent['object_id']) : array($aEvent['object_id']);
        rsort($aObjectIds);

        $iItems = count($aObjectIds);
        $iItemsLimit = isset($aParams['grouped']['items_limit']) ? (int)$aParams['grouped']['items_limit'] : 3;
        if($iItems > $iItemsLimit)
            $aObjectIds = array_slice($aObjectIds, 0, $iItemsLimit);

        $bSave = false;
        $aContent = array();
        if(!empty($aEvent['content']))
            $aContent = unserialize($aEvent['content']);

        if(!isset($aContent['idims']))
            $aContent['idims'] = array();

        $iDeleted = 0;
        $aItems = $aTmplItems = array();
        foreach($aObjectIds as $iId) {
            $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $aEvent['owner_id'], 1);
            if(empty($aItem))
                $iDeleted++;
            else if($aItem[$this->_oDb->_sFieldStatus] == 'approved' && $aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId)) {
                $aItem['thumb_file'] = '';
                $aItem['thumb_dims'] = array();
                if($aItem[$this->_oDb->_sFieldThumb]) {
                    $aImage = BxDolService::call('photos', 'get_entry', array($aItem[$this->_oDb->_sFieldThumb], 'browse'), 'Search');
                    if(!empty($aImage)) {
                        if(!isset($aContent['idims'][$iId])) {
                            $sPath = isset($aImage['file_path']) && file_exists($aImage['file_path']) ? $aImage['file_path'] : $aImage['file'];
                            $aContent['idims'][$iId] = BxDolImageResize::instance()->getImageSize($sPath);
                            $bSave = true;
                        }

                        $aItem['thumb_file'] = $aImage['file'];
                        $aItem['thumb_dims'] = $aContent['idims'][$iId];
                    }

                    $aImage = BxDolService::call('photos', 'get_entry', array($aItem[$this->_oDb->_sFieldThumb], 'browse2x'), 'Search');
                    $aItem['thumb_file_2x'] = !empty($aImage) ? $aImage['file'] : $aItem['thumb_file'];
                }

                $aItem[$this->_oDb->_sFieldUri] = $sBaseUrl . $aItem[$this->_oDb->_sFieldUri];
                $aItems[] = $aItem;

                $aTmplItems[] = array(
                    'mod_prefix' => $this->_sPrefix,
                    'item_width' => isset($aItem['thumb_dims']['w']) ? $aItem['thumb_dims']['w'] : $iNoPhotoWidth,
                    'item_height' => isset($aItem['thumb_dims']['h']) ? $aItem['thumb_dims']['h'] : $iNoPhotoHeight,
                    'item_icon' => !empty($aItem['thumb_file']) ? $aItem['thumb_file'] : $sNoPhoto,
                	'item_icon_2x' => !empty($aItem['thumb_file_2x']) ? $aItem['thumb_file_2x'] : $sNoPhoto,
                    'item_page' => $aItem[$this->_oDb->_sFieldUri],
                    'item_title' => $aItem[$this->_oDb->_sFieldTitle]
                );
            }
        }

        if($iDeleted == count($aObjectIds))
            return array('perform_delete' => true);

        if(empty($aOwner) || empty($aItems))
            return "";

        $aResult = array();
        if($bSave)
            $aResult['save']['content'] = serialize($aContent);

        $sCss = "";
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss('wall_outline.css', true);
        else
            $this->_oTemplate->addCss('wall_outline.css');

        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = getNickName($iOwner);
        $sOwnerLink = getProfileLink($iOwner);

		$aNews = $this->_oDb->getEntryByIdAndOwner($aEvent['object_id'], $aEvent['owner_id'], 1);

        $sOwner = $aNews[$this->_oDb->_sFieldAnonymous] ? _t('_modzzz_news_someone') : getNickName($iOwner);
        $sOwnerLink = $aNews[$this->_oDb->_sFieldAnonymous] ? 'javascript:void(0)' : getProfileLink($iOwner);
		$sOwnerIcon = $aNews[$this->_oDb->_sFieldAnonymous] ? $this->_oTemplate->parseHtmlByName('anonymous_small', $aVars= array()) : get_member_icon($aEvent['owner_id'], 'none');
 
        //--- Grouped events
        $iItems = count($aItems);
        if($iItems > 1) {
            $sTmplName = isset($aParams['templates']['grouped']) ? $aParams['templates']['grouped'] : 'modules/boonex/wall/|outline_item_image_grouped';
            $aResult['content'] = $sCss . $this->_oTemplate->parseHtmlByName($sTmplName, array(
                'mod_prefix' => $this->_sPrefix,
                'mod_icon' => $sIcon,
				'post_owner_icon' => $sOwnerIcon,
                'user_name' => $sOwner,
                'user_link' => $sOwnerLink,
                'bx_repeat:items' => $aTmplItems,
                'album_url' => '',
                'album_title' => '',
                'album_description' => '',
                'album_comments' => 0 ? _t('_wall_n_comments', 0) : _t('_wall_no_comments'),
                'album_comments_link' => '',
                'post_id' => $aEvent['id'],
                'post_ago' => $aEvent['ago']
            ));

            return $aResult;
        }

        //--- Single public event
        $aItem = $aItems[0];
        $aTmplItem = $aTmplItems[0];

        $sTmplName = isset($aParams['templates']['single']) ? $aParams['templates']['single'] : 'modules/boonex/wall/|outline_item_image';
        $aResult['content'] = $sCss . $this->_oTemplate->parseHtmlByName($sTmplName, array_merge($aTmplItem, array(
            'mod_prefix' => $this->_sPrefix,
            'mod_icon' => $sIcon,
			'post_owner_icon' => $sOwnerIcon,
            'user_name' => $sOwner,
            'user_link' => $sOwnerLink,
            'item_page' => $aItem[$this->_oDb->_sFieldUri],
            'item_title' => $aItem[$this->_oDb->_sFieldTitle],
            'item_description' => $this->_formatSnippetTextForOutline($aItem),
            'item_comments' => (int)$aItem[$this->_oDb->_sFieldCommentCount] > 0 ? _t('_wall_n_comments', $aItem[$this->_oDb->_sFieldCommentCount]) : _t('_wall_no_comments'),
            'item_comments_link' => $aItem[$this->_oDb->_sFieldUri] . '#cmta-' . $this->_sPrefix . '-' . $aItem[$this->_oDb->_sFieldId],
            'post_id' => $aEvent['id'],
            'post_ago' => $aEvent['ago']
        )));

        return $aResult;
    }

   function _serviceGetWallPost ($aEvent, &$aParams)
    {
        if (!($aProfile = getProfileInfo($aEvent['owner_id'])))
            return '';

        $aObjectIds = strpos($aEvent['object_id'], ',') !== false ? explode(',', $aEvent['object_id']) : array($aEvent['object_id']);
        rsort($aObjectIds);

        $iDeleted = 0;
        $aItems = array();
        foreach($aObjectIds as $iId) {
            $aItem = $this->_oDb->getEntryByIdAndOwner($iId, $aEvent['owner_id'], 1);
            if(empty($aItem))
                $iDeleted++;
            if(!$aItem[$this->_oDb->_sFieldAnonymous] && $aItem[$this->_oDb->_sFieldStatus] == 'approved' && $aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iId, $this->_iProfileId))
                $aItems[] = $aItem;
        }
  
        if($iDeleted == count($aObjectIds))
            return array('perform_delete' => true);

        if(empty($aItems))
            return '';

        $sCss = '';
        $sCssPrefix = str_replace('_', '-', $this->_sPrefix);
        $sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/';
        if($aEvent['js_mode'])
            $sCss = $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'), true);
        else
            $this->_oTemplate->addCss(array('wall_post.css', 'unit.css', 'twig.css'));

        $iItems = count($aItems);
        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = ($iOwner) ? getNickName($iOwner) : _t('_modzzz_news_anonymous');

        bx_import('Voting', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Voting';
        $oVoting = new $sClass ($this->_sPrefix, 0, 0);

        //--- Grouped events
        if($iItems > 1) {
            if($iItems > 4)
                $aItems = array_slice($aItems, 0, 4);

            $aTmplItems = array();
            foreach($aItems as $aItem)
                $aTmplItems[] = array(
                    'unit' => $this->_oTemplate->unit($aItem, 'unit', $oVoting, true),
                );

            $sTmplName = isset($aParams['templates']['grouped']) ? $aParams['templates']['grouped'] : 'modules/boonex/wall/|timeline_post_twig_grouped.html';
            return array(
                'title' => _t($aParams['txt_added_new_title_plural'], $sOwner, $iItems),
                'description' => '',
                'content' => $sCss . $this->_oTemplate->parseHtmlByName($sTmplName, array(
	            	'mod_prefix' => $sCssPrefix,
	            	'mod_icon' => $aParams['icon'],
	                'cpt_user_name' => $sOwner,
	                'cpt_added_new' => _t($aParams['txt_added_new_plural'], $iItems),
	                'bx_repeat:items' => $aTmplItems,
	            ))
            );
        }

        //--- Single public event
        $aItem = $aItems[0];

        $sTextWallObject = _t($aParams['txt_object']);

        $sTmplName = isset($aParams['templates']['single']) ? $aParams['templates']['single'] : 'modules/boonex/wall/|timeline_post_twig.html';
        return array(
            'title' => _t($aParams['txt_added_new_title_single'], $sOwner, $sTextWallObject),
            'description' => $aItem[$this->_oDb->_sFieldDescription],
            'content' => $sCss . $this->_oTemplate->parseHtmlByName($sTmplName, array(
				'mod_prefix' => $sCssPrefix,
				'mod_icon' => $aParams['icon'],
	            'cpt_user_name' => $sOwner,
	            'cpt_added_new' => _t($aParams['txt_added_new_single']),
	            'cpt_object' => $sTextWallObject,
	            'cpt_item_url' => $sBaseUrl . $aItem[$this->_oDb->_sFieldUri],
	            'content' => $this->_oTemplate->unit($aItem, 'unit', $oVoting, true),
	        ))
        );
    }

 
    function _formatSnippetTextForOutline($aEntryData)
    {
        return $this->_oTemplate->parseHtmlByName('wall_outline_extra_info', array(
            'desc' => $this->_formatSnippetText($aEntryData, 200) 
        ));
    }

    function _formatSnippetText ($aEntryData, $iMaxLen = 300, $sField='')
    {  $sField = ($sField) ? $sField : $aEntryData[$this->_oDb->_sFieldDescription];
        return strmaxtextlen($sField, $iMaxLen);
    }

   /**
     * Install map support
     */
    function serviceMapInstall()
    {
        if (!BxDolModule::getInstance('BxWmapModule'))
            return false;

        return BxDolService::call('wmap', 'part_install', array('news', array(
            'part' => 'news',
            'title' => '_modzzz_news',
            'title_singular' => '_modzzz_news_single',
            'icon' => 'modules/modzzz/news/|map_marker.png',
            'icon_site' => 'file',
            'join_table' => 'modzzz_news_main',
            'join_where' => "AND `p`.`status` = 'approved'",
            'join_field_id' => 'id',
            'join_field_country' => 'country',
            'join_field_city' => 'city',
            'join_field_state' => 'state',
            'join_field_zip' => 'zip',
            'join_field_address' => 'street',
            'join_field_title' => 'title',
            'join_field_uri' => 'uri',
            'join_field_author' => 'author_id',
            'join_field_privacy' => 'allow_view_news_to',
            'permalink' => 'modules/?r=news/view/',
        )));
    }
  
    function actionEmbed ($sUri) {
 		$sTitle = _t('_modzzz_news_page_title_embed_video');

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
            $sTitle => '',
        ));

        if (!$this->isAllowedEmbed($aDataEntry)) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        bx_import ('EmbedForm', $this->_aModule);
		$oForm = new BxNewsEmbedForm ($this, $aDataEntry);
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
	//[end modzzz] embed video modification


	//[begin modzzz] categories modification
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
 
		$sTitle = _t('_modzzz_news_page_title_categories');

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
 
		$sTitle = _t('_modzzz_news_page_title_subcategories');

		if($sUri){
			$aCategoryInfo = $this->_oDb->getCategoryInfoByUri($sUri); 
			$sCategoryName = $aCategoryInfo['name'];
 			$sTitle .= ' - ' . $sCategoryName;
		}

        $this->_oTemplate->pageCode($sTitle, false, false);
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
			'name' => _t('_modzzz_news_categories'),
			'bx_repeat:items' => $aCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/categories/'
		));


		$aCategory = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "categ` WHERE  `id` = '$iCategory'");
		 
		$sFormName = 'categories_form';
  
	    if($iCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_news_categ_btn_edit'),
				'action_delete' => _t('_modzzz_news_categ_btn_delete'), 
				'action_add' => _t('_modzzz_news_categ_btn_add'),
				'action_sub' => _t('_modzzz_news_categ_btn_subcategories'), 
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_news_categ_btn_save')
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
 				'action_add' => _t('_modzzz_news_categ_btn_add'),  
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
			$sContent = MsgBox(_t('_modzzz_news_manage_subcategories_msg')); 

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
			'name' => $sCategoryName .': '. _t('_modzzz_news_subcategories'),
			'bx_repeat:items' => $aSubCategory,
			'location_href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/subcategories/'.$iCategory.'/'
		));


		$aCategory = $this->_oDb->getRow("SELECT * FROM `" . $this->_oDb->_sPrefix . "categ` WHERE  `id` = '$iSubCategory'");
		 
		$sFormName = 'categories_form';
 
	    if($iSubCategory){
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_edit' => _t('_modzzz_news_categ_btn_edit'),
				'action_delete' => _t('_modzzz_news_categ_btn_delete'), 
				'action_add' => _t('_modzzz_news_categ_btn_add'),
			), 'pathes', false);
	    }else{
			$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
				'action_save' => _t('_modzzz_news_categ_btn_save')
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
 				'action_add' => _t('_modzzz_news_categ_btn_add'),  
			), 'pathes', false);  
			$sContent .= "</form>";
		}else{
			$sContent .= $this->_oTemplate->parseHtmlByName('admin_categories',$aVars);
		}

		return $sContent;
	} 
	//[end modzzz] categories modification


	function serviceAfterInstall(){
		$this->_oDb->sqlAfter(); 
		$this->_oDb->sqlStates(); 
	}
 
    function isAllowedEmbed(&$aDataEntry) {
		
        if( !$this->isAllowedEdit($aDataEntry) ) return false;   

        if ( $this->isAdmin() ) return true;
             
        // check acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_NEWS_ALLOW_EMBED, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED; 
    }

    function isAllowedActivate (&$aEvent, $isPerformAction = false)
    {
        if ($aEvent['status'] != 'pending')
            return false;
        if ($this->isAdmin())
            return true;
        $this->_defineActions();
        $aCheck = checkAction($this->_iProfileId, BX_NEWS_APPROVE, $isPerformAction);
        return $aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED;
    }

	function serviceGetWallAddComment($aEvent)
    {
        $aParams = array(
            'txt_privacy_view_event' => 'view_news',
            'obj_privacy' => $this->_oPrivacy
        );
        return $this->_serviceGetWallAddComment($aEvent, $aParams);
    }
   
    function _serviceGetWallAddComment($aEvent, $aParams)
    {
    	$iId = (int)$aEvent['object_id'];
        $iOwner = (int)$aEvent['owner_id'];
        $sOwner = getNickName($iOwner);

        $aContent = unserialize($aEvent['content']);
        if(empty($aContent) || empty($aContent['object_id']))
            return '';

		$iItem = (int)$aContent['object_id'];
        $aItem = $this->_oDb->getEntryByIdAndOwner($iItem, $iOwner, 1);
        if(empty($aItem) || !is_array($aItem))
        	return array('perform_delete' => true);

		if($aItem[$this->_oDb->_sFieldAnonymous]) return '';


        if(!$aParams['obj_privacy']->check($aParams['txt_privacy_view_event'], $iItem, $this->_iProfileId))
            return '';

        bx_import('Cmts', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'Cmts';
        $oCmts = new $sClass($this->_sPrefix, $iItem);
        if(!$oCmts->isEnabled())
            return '';

        $aComment = $oCmts->getCommentRow($iId);

        $sImage = '';
        if($aItem[$this->_oDb->_sFieldThumb]) {
            $a = array('ID' => $aItem[$this->_oDb->_sFieldAuthorId], 'Avatar' => $aItem[$this->_oDb->_sFieldThumb]);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }

        $sCss = '';
        $sCssPrefix = str_replace('_', '-', $this->_sPrefix);
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
 
        $sTextWallObject = _t('_modzzz_' . $sUri . '_wall_object');
 
        $sTmplName = isset($aParams['templates']['main']) ? $aParams['templates']['main'] : 'modules/boonex/wall/|timeline_comment.html';
        return array(
            'title' => _t('_bx_' . $sUri . '_wall_added_new_title_comment', $sOwner, $sTextWallObject),
            'description' => $aComment['cmt_text'],
            'content' => $sCss . $this->_oTemplate->parseHtmlByName($sTmplName, array(
        		'mod_prefix' => $sCssPrefix,
	            'cpt_user_name' => $sOwner,
	            'cpt_added_new' => _t('_modzzz_' . $sUri . '_wall_added_new_comment'),
	            'cpt_object' => $sTextWallObject,
	            'cpt_item_url' => $sBaseUrl . $aItem[$this->_oDb->_sFieldUri],
	            'cnt_comment_text' => $aComment['cmt_text'],
	            'snippet' => $this->_oTemplate->unit($aItem, 'unit', $oVoting)
        	))
        );
    }





}	

