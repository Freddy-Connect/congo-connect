<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx JobsCover
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

function modzzz_jobscover_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'jobscover') {
        $oMain = BxDolModule::getInstance('BxJobsCoverModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');

define ('MODZZZ_BUSINESSCOVER_PHOTOS_TAG', 'Business Cover');
define ('MODZZZ_BUSINESSCOVER_PHOTOS_CAT', 'Business Cover');  

/*
 * JobsCover module   
 *
 */
class BxJobsCoverModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();
 
    function BxJobsCoverModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'jobscover_filter';
        $this->_sPrefix = 'modzzz_jobscover';

        $GLOBALS['oBxJobsCoverModule'] = &$this;
    }
  
    function actionHome () {
        parent::_actionHome(_t('_modzzz_jobscover_page_title_home'));
    }
  
    // ================================== admin actions

    function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array( 
            'settings' => array(
                'title' => _t('_modzzz_jobscover_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_jobscover_page_title_administration'), $aMenu);
		$this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css'));
       
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_jobscover_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('JobsCover');
    }

    function actionAdministrationManage ($isAdminEntries = false) {
        return parent::_actionAdministrationManage ($isAdminEntries, '_modzzz_jobscover_admin_delete', '_modzzz_jobscover_admin_activate');
    }

    // ================================== events
  
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_jobscover') == 'on'));
		 
        return $bEnabled;
    }
 
    function _defineActions () {
        defineMembershipActions(array('jobscover add cover'));
    }

    function _browseMy (&$aProfile) {        
        parent::_browseMy ($aProfile, _t('_modzzz_jobscover_page_title_my_jobscover'));
    } 
  
	function serviceCoverBlock($aDataEntry) {
 
        $sBackground = $sThumbnail = '';

		if(getParam("modzzz_jobscover_active")!='on') return;

		if(!$this->isAllowedUseCover($aDataEntry['author_id'])) return;

		$oJobs = BxDolModule::getInstance('BxJobsModule');
   
    	$sProfileNickname = getNickName($aDataEntry['author_id']);
    	$sProfileLink = getProfileLink($aDataEntry['author_id']);

    	$sProfileThumbnail = '';
        $sProfileThumbnail2x = '';
        $sProfileThumbnailHref = '';

    	$bProfileThumbnail = false;
    	$bProfileThumbnailHref = false;

	    $aProfileThumbnail = BxDolService::call('photos', 'profile_photo', array($aDataEntry['author_id'], 'browse', 'full'), 'Search');
    	if(!empty($aProfileThumbnail) && is_array($aProfileThumbnail)) {
    		$sProfileThumbnail = $aProfileThumbnail['file_url'];
    		$sProfileThumbnailHref = $aProfileThumbnail['view_url'];

    		$bProfileThumbnail = true;
    		$bProfileThumbnailHref = true;

    	    $aProfileThumbnail2x = BxDolService::call('photos', 'profile_photo', array($aDataEntry['author_id'], 'browse2x', 'full'), 'Search');
        	if(!empty($aProfileThumbnail2x) && is_array($aProfileThumbnail2x))
                $sProfileThumbnail2x = $aProfileThumbnail['file_url'];
    	}
 
 		if($aDataEntry['icon']){ 
		    $sThumbnail = $oJobs->_oDb->getLogo($aDataEntry['id'], $aDataEntry['icon'], true);
 		}elseif ($aDataEntry['thumb']) {
            $a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aDataEntry['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search'); 
			$sThumbnail = ($aImage['no_image']) ? '' : $aImage['file']; 
        } 

		if(!$sThumbnail){  
			$sThumbnail = $oJobs->_oTemplate->getImageUrl('no-image-thumb.png');
		}
 
        if ($aDataEntry['cover_id']) {
            $a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aDataEntry['cover_id']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');

			if($aImage['no_image']) return;

            $sBackground = $aImage['file'];
        } 
  
		$sBackgroundClass = ' sys-pcb-cover';
  
		$bShowProfileThumbnail = (getParam('modzzz_jobscover_show_administrator')=='on') ? true : false;

    	$aTmplVarsMenu = array();
    	$aMenuItems = $GLOBALS['oTopMenu']->getSubItems();
    	foreach($aMenuItems as $aMenuItem){
    		$aTmplVarsMenu[] = array(
    			'href' => $aMenuItem['Link'],
    			'bx_if:show_onclick' => array(
    				'condition' => !empty($aMenuItem['Onclick']),
    				'content' => array(
    					'onclick' => $aMenuItem['Onclick']
    				)
    			),
    			'bx_if:show_target' => array(
    				'condition' => !empty($aMenuItem['Target']),
    				'content' => array(
    					'target' => $aMenuItem['Target']
    				)
    			),
    			'caption' => _t($aMenuItem['Caption'])
    		);
		}

        $this->_oTemplate->addCss(array('unit.css','profile_view.css','profile_view_phone.css','profile_view_tablet.css'));
 
		$sContent = $this->_oTemplate->parseHtmlByName('unit_cover.html', array(
			'background_class' => $sBackgroundClass,
			'bx_if:show_background' => array(
				'condition' => !empty($sBackground),
				'content' => array(
					'background' => $sBackground
				)
			),
			'bx_if:show_actions' => array(
				'condition' => $this->isAllowedAdd($aDataEntry['id']),
				'content' => array(
					'href_cover_add' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'cover/add/' . $aDataEntry[$oJobs->_oDb->_sFieldId],
					'href_cover_change' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'cover/browse/' . $aDataEntry[$oJobs->_oDb->_sFieldUri],
					'href_cover_delete' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'cover/remove/' . $aDataEntry[$oJobs->_oDb->_sFieldUri],
				)
			),   
			'bx_if:show_profile_image' => array(
				'condition' => $bShowProfileThumbnail && $bProfileThumbnail,
				'content' => array(
					'thumbnail_href' => $sProfileLink,
					'thumbnail' => $sProfileThumbnail,
                    'thumbnail2x' => $sProfileThumbnail2x,
				)
			),
			'bx_if:show_thumbnail_letter_text' => array(
				'condition' => $bShowProfileThumbnail && !$bProfileThumbnail,
				'content' => array(
					'thumbnail_href' => $sProfileLink,
					'letter' => mb_substr($sProfileNickname, 0, 1)
				)
			),
 
			'nickname' => $sProfileNickname,
			'nicklink' => $sProfileLink,
  
			'bx_if:show_thumbnail_image' => array(
				'condition' => !$bShowProfileThumbnail,
				'content' => array(
					'thumbnail' => $sThumbnail,
				)
			), 
			'title' => $aDataEntry['title'],
			'bx_repeat:menu_items' => $aTmplVarsMenu,
		));

    	return array($sContent, array(), array(), true);
    }
 
    function isAllowedAdd ($iEntryId, $isPerformAction = false) {
		$iEntryId = (int)$iEntryId;

        $oModule = BxDolModule::getInstance('BxJobsModule');
 
		$aDataEntry = $oModule->_oDb->getEntryById($iEntryId);
   
        // admin always have access
        if ($this->isAdmin()) 
            return true;
 
        if (!($aDataEntry['author_id'] == $this->_iProfileId || $oModule->isEntryAdmin($aDataEntry))) 
            return false;
 
        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($this->_iProfileId, BX_JOBSCOVER_ADD_COVER, $isPerformAction);
        return ($aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED); 
    }

    function isAllowedUseCover ($iProfileId, $isPerformAction = false) {
 
        // admin always have access
        if ($this->isAdmin()) 
            return true;
 
        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($iProfileId, BX_JOBSCOVER_ADD_COVER, $isPerformAction);
        return ($aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED); 
    }

 
	function serviceInitialize(){
		$this->_oDb->initialize(); 
	}

	function serviceCleanup(){
		$this->_oDb->cleanup(); 
	}

	/******[BEGIN] Cover functions **************************/ 
 
    function actionCover ($sAction, $sParam1, $sParam2='') {
		switch($sAction){   
			case 'add': 
				$this->actionAdd ($sParam1);
			break; 
			case 'remove':
				$this->actionDelete ($sParam1);
			break; 
			case 'browse':
				return $this->actionBrowse ($sParam1); 
			break;  
		}
	}
    
    function actionBrowse ($sUri) {
       
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
  
        if ($GLOBALS['oTemplConfig']->bAllowUnicodeInPreg)
            $sReg = '/^[\pL\pN\-_]+$/u'; // unicode characters
        else
            $sReg = '/^[\d\w\-_]+$/u'; // latin characters only

        if (!preg_match($sReg, $sUri)) {
            $this->_oTemplate->displayPageNotFound ();
            return false;
        }
  
		$oModule = BxDolModule::getInstance('BxJobsModule');

        if (!($aDataEntry = $oModule->_oDb->getEntryByUri($sUri))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }		
  
		$sTitle = '_modzzz_jobscover_page_title_cover_browse';

		$this->_oTemplate->pageStart();

        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$oModule->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar('modzzz_jobs_view_uri', $aDataEntry[$oModule->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_modzzz_jobs') => BX_DOL_URL_ROOT . $oModule->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$oModule->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $oModule->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$oModule->_oDb->_sFieldUri],
            _t('_modzzz_jobscover_menu_browse_cover') => '',
        ));
 
        bx_import ('PageBrowse', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageBrowse';
        $oPage = new $sClass ($this, $sUri);
 
		$aButtons = array(
			'action_cover' => '_modzzz_jobscover_action_make_cover',
			'action_delete' => '_modzzz_jobscover_action_delete',
		);

        $aVars = array (
            'form_name' => 'bx_twig_admin_form',
            'content' => $oPage->getCode(),
            'pagination' => '',
            'filter_panel' => '',
            'actions_panel' => $this->_oDb->hasCovers($aDataEntry[$oModule->_oDb->_sFieldId]) ? $o->showAdminActionsPanel ('bx_twig_admin_form', $aButtons) : '',
        );

        echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
 
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t($sTitle, $aDataEntry[$oModule->_oDb->_sFieldTitle]), false, false);  
    }
     
    function actionDelete ($sUri) {
 
		$oJobsModule = BxDolModule::getInstance('BxJobsModule');
		$aJobsEntry = $oJobsModule->_oDb->getEntryByUri($sUri);
 
        if (!($this->isAdmin() || $oJobsModule->isEntryAdmin($aJobsEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
 
        $this->_oDb->removeCover($sUri);
 
		$sRedirectUrl = BX_DOL_URL_ROOT . $oJobsModule->_oConfig->getBaseUri() . 'view/' . $aJobsEntry['uri'];
		header ('Location:' . $sRedirectUrl);
		exit; 
    }
 
    function actionAdd ($iJobsId) {
  
		$oJobs = BxDolModule::getInstance('BxJobsModule');  
		 
		if (!($aDataEntry = $oJobs->_oDb->getEntryById((int)$iJobsId))) {
			$this->_oTemplate->displayPageNotFound ();
			return;
		}	

		$sTitle = '_modzzz_jobscover_page_title_cover_add';
 
        if (!($this->isAdmin() || $oJobs->isEntryAdmin($aDataEntry))) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
  
        $this->_oTemplate->pageStart();
    
        $GLOBALS['oTopMenu']->setCustomSubHeader($aDataEntry[$oJobs->_oDb->_sFieldTitle]);
        $GLOBALS['oTopMenu']->setCustomVar('modzzz_jobs_view_uri', $aDataEntry[$oJobs->_oDb->_sFieldUri]);
        $GLOBALS['oTopMenu']->setCustomBreadcrumbs(array(
            _t('_modzzz_jobs') => BX_DOL_URL_ROOT . $oJobs->_oConfig->getBaseUri() . 'home/',
            $aDataEntry[$oJobs->_oDb->_sFieldTitle] => BX_DOL_URL_ROOT . $oJobs->_oConfig->getBaseUri() . 'view/' . $aDataEntry[$oJobs->_oDb->_sFieldUri],
            _t($sTitle) => '',
        ));
 
        $this->_addCoverForm($iJobsId);

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('forms_extra.css');
        $this->_oTemplate->pageCode(_t($sTitle) .' - '. $aDataEntry[$oJobs->_oDb->_sFieldTitle]);  
    }
 
    function _addCoverForm ($iJobsId) { 
 
 		$oJobs = BxDolModule::getInstance('BxJobsModule');  
		$aJobsEntry = $oJobs->_oDb->getEntryById((int)$iJobsId);

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this, $iJobsId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
			$sStatus = 'approved';
 
            $aValsAdd = array (
                $this->_oDb->_sFieldCreated => time(),
                $this->_oDb->_sFieldUri => time(),
                $this->_oDb->_sFieldStatus => $sStatus,
                $this->_oDb->_sFieldAuthorId => $this->_iProfileId,
            );                        
 
            $iEntryId = $oForm->insert ($aValsAdd);
 
            if ($iEntryId) {
   
				$oForm->processMedia($iEntryId, $this->_iProfileId); 
	  
                $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
				if(!$aJobsEntry['cover_id']){
					$this->_oDb->activateCover($aDataEntry['jobs_id'], $aDataEntry['thumb']);
				}

				$this->onEventCreate ($iEntryId, $sStatus, $aDataEntry);

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'cover/browse/' . $aJobsEntry[$oJobs->_oDb->_sFieldUri];
			  
                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
	function isJobsAdmin ($aDataEntry, $iProfileId=0) {

		$oJobs = BxDolModule::getInstance('BxJobsModule');  
		$aJobsEntry = $oJobs->_oDb->getEntryById((int)$aDataEntry['jobs_id']);
 
		return ($oJobs->isEntryAdmin($aJobsEntry, $iProfileId));
	}
 
    function isAllowedDelete (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true; 

		return $this->isJobsAdmin($aDataEntry);
    }   
 
    function isAllowedEdit (&$aDataEntry, $isPerformAction = false) {
        if ($this->isAdmin() || ($GLOBALS['logged']['member'] && $aDataEntry['author_id'] == $this->_iProfileId && isProfileActive($this->_iProfileId))) 
            return true; 

		return $this->isJobsAdmin($aDataEntry);
    }   

    function onEventCreate ($iEntryId, $sStatus, $aDataEntry = array())
    {
        if ('approved' == $sStatus) {
            //$this->reparseTags ($iEntryId);
        }
 
        $oAlert = new BxDolAlerts($this->_sPrefix, 'add', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
        $oAlert->alert();
    }

    function onEventChanged ($iEntryId, $sStatus)
    {
        $oAlert = new BxDolAlerts($this->_sPrefix, 'change', $iEntryId, $this->_iProfileId, array('Status' => $sStatus));
        $oAlert->alert();
    }

    function onEventDeleted ($iEntryId, $aDataEntry = array()) {
  
        // arise alert
		$oAlert = new BxDolAlerts($this->_sPrefix, 'delete', $iEntryId, $this->_iProfileId);
		$oAlert->alert();
    }        
 
    function isAllowedView ($aDataEntry, $isPerformAction = false) {
 
        $oModule = BxDolModule::getInstance('BxJobsModule');

        return ($aDataEntry['author_id'] == $this->_iProfileId || $this->isAdmin() || $oModule->isEntryAdmin($aDataEntry));  

    }
 
    function serviceResponseParentDelete ($oAlert)
    {
        $iParentId = (int)$oAlert->iObject;
 
        if (!$iParentId) return;
 
        return $this->_oDb->deleteCovers ($iParentId);
    }




}