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

function modzzz_atool_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'atool') {
        $oMain = BxDolModule::getInstance('BxAToolModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
 
 
/*
 * ATool module
 * 
 * 
 *
 */
class BxAToolModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();

    function BxAToolModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_atool_filter';
        $this->_sPrefix = 'modzzz_atool';
        $this->_oDb->_sPrefix = 'modzzz_atool_';
  
		if($_GET['ajax']=='privacy_action')
		{
			$sModule = $_GET['module'];
			echo $this->_oDb->getModuleActions($sModule);
			exit;
		}		

		if($_GET['ajax']=='privacy_default')
		{
			$sModule = $_GET['module'];
			$sAction = $_GET['action'];
			echo $this->_oDb->getGroupChooser(0, $sModule, $sAction);
			exit;
		}		
 
        $GLOBALS['oBxAToolModule'] = &$this; 
    }

    function actionHome () {  
		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/privacy';
		header ('Location:' . $sRedirectUrl);  
    }
   
    // ================================== admin actions
 
    function actionSort ($sType='') {
  
        if (!$this->isAdmin()) { 
            return;
        } 
		
		$iOrder = 0;
		foreach ($_POST['item'] as $iEntryId) {

			if($sType=='sitemap')
				$this->_oDb->sortSiteMap($iEntryId, $iOrder); 
			else
				$this->_oDb->sortSiteStat($iEntryId, $iOrder); 
 
			$iOrder++;
		} 
	}
 
    function actionStatArchive ($iId) {
		$iId = (int)$iId;

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 
		
		$aDataEntry = $this->_oDb->getSiteStatEntryById($iId);
 
		$this->_oDb->archiveSiteStatEntry($iId);

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat/archive';
		header ('Location:' . $sRedirectUrl); 
	}
 
    function actionStatDelete ($iId) {
		$iId = (int)$iId;

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 
 
		$this->_oDb->deleteSiteStatEntryById($iId);

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat';
		header ('Location:' . $sRedirectUrl); 
	}
 
    function actionAdministrationManage ($sValue, $isActiveEntries = false) {
        return $this->_actionAdministrationManage ($sValue, $isActiveEntries);
    }
 
    function actionAdministrationPrivacy($sUrl='') {
 
		ob_start();

		$this->_managePrivacyForm($sUrl);
 
		$aVars = array (
			'form_name' => 'statfrm',
			'content' =>'',
			'pagination' =>'',
			'filter_panel' =>  '',
			'actions_panel' => $sActionsPanel,
		);
		echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
  
		$aVars = array (
			'content' => ob_get_clean(),
		);
		return $this->_oTemplate->parseHtmlByName('default_padding', $aVars); 
    }
   
    function _managePrivacyForm ($sRedirectUrl) {

        bx_import ('FormManagePrivacy', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormManagePrivacy';
        $oForm = new $sClass ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
  
			$sModuleUri = $oForm->getCleanValue('module_uri');
			$sActionName = $oForm->getCleanValue('action');
			$sDefault = $oForm->getCleanValue('default');

			$this->_oDb->setDefaultValue($sDefault, $sModuleUri, $sActionName);
  
			echo MsgBox(_t('_modzzz_atool_msg_privacy_default_updated'));
 
        }

		echo $oForm->getCode ();  
    } 
 
	//begin site map 
    function actionSiteMapArchive ($iId) {
		$iId = (int)$iId;

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 
		
		$aDataEntry = $this->_oDb->getSiteMapEntryById($iId);
 
		$this->_oDb->archiveSiteMapEntry($iId);

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap/archive';
		header ('Location:' . $sRedirectUrl); 
	}
 
    function actionSiteMapDelete ($iId) {
		$iId = (int)$iId;

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 
 
		$this->_oDb->deleteSiteMapEntryById($iId);

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap';
		header ('Location:' . $sRedirectUrl); 
	}
  

    function actionAdministrationSiteMapManage ($sValue, $isActiveEntries = false) {

		$sKeyBtnDelete = '_modzzz_atool_admin_delete';
		$sKeyBtnActivate = '_modzzz_atool_admin_activate';
		//$sKeyBtnAdd = '_modzzz_atool_admin_add';
		$sKeyBtnArchive = '_modzzz_atool_admin_archive';
		$sKeyBtnViewSiteMaps = '_modzzz_atool_admin_view_entries';

        if ($_POST['action_activate'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                $this->_oDb->activateSiteMapEntry($iId);
            }

			header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap'); 

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
 
                if (!$this->isAdmin()) 
                    continue;
			 
                $this->_oDb->deleteSiteMapById($iId);
            }

        } elseif ($_POST['action_archive_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
 
                if (!$this->isAdmin()) 
                    continue;
			 
                $this->_oDb->deleteSiteMapArchiveById($iId);
            }

        } elseif ($_POST['action_add']) {

			header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap/add');
 
        } elseif ($_POST['action_archive']) {

			header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap/archive'); 
        }
   
        if ($isActiveEntries) {
            $sContent = $this->_manageSiteMapActiveEntries ('sitemap_active', $sValue, true, 'bx_twig_admin_form', array(
				'action_archive' => $sKeyBtnArchive,
                'action_delete' => $sKeyBtnDelete,
				'action_view' => $sKeyBtnViewSiteMaps,  
				/*'action_add' => $sKeyBtnAdd,*/

			));
        } else {
            $sContent = $this->_manageSiteMapArchiveEntries ('sitemap_archive', $sValue, false, 'bx_twig_admin_form',  
			array(
                'action_activate' => $sKeyBtnActivate,
                'action_archive_delete' => $sKeyBtnDelete,
				'action_view' => $sKeyBtnViewSiteMaps,
				/*'action_add' => $sKeyBtnAdd,*/

            ));  
        }
 
        return $sContent;
    }

    function _manageSiteMapActiveEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false) {
    
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'sitemap_unit_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;
 
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayResultBlock())) {
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else { 
            $sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
        }

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => '',
            'filter_panel' => '',
            'actions_panel' => $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
    function _manageSiteMapArchiveEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false)
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'sitemap_archive_admin';

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
            'actions_panel' => $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
    function actionAdministrationSiteMapCreate($sUrl='', $iEntryId=0) {

        $iEntryId = (int)$iEntryId;
  
		if($iEntryId) {
				
			 if (!($aDataEntry = $this->_oDb->getSiteMapEntryById($iEntryId))) {
				$this->_oTemplate->displayPageNotFound ();
				exit;
			 }
 		 
 
			ob_start();

			$this->_editSiteMapForm($aDataEntry, $iEntryId);

			bx_import ('SearchResult', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SearchResult';
			$o = new $sClass('', '');

			$aButtons = array('action_view' => '_modzzz_atool_admin_view_entries'); 
			$sActionsPanel = $o->showAdminActionsPanel ('sitemapfrm', $aButtons, '', false);

			$aVars = array (
				'form_name' => 'sitemapfrm',
				'content' =>'',
				'pagination' =>'',
				'filter_panel' =>  '',
				'actions_panel' => $sActionsPanel,
			);
			echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
	  
			$aVars = array (
				'content' => ob_get_clean(),
			);
			return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
   
		}else{
  
			ob_start(); 
			
			$this->_addSiteMapForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap');
  
			bx_import ('SearchResult', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SearchResult';
			$o = new $sClass('', '');

			$aButtons = array('action_view' => '_modzzz_atool_admin_view_entries'); 
			$sActionsPanel = $o->showAdminActionsPanel ('sitemapfrm', $aButtons, '', false);

			$aVars = array (
				'form_name' => 'sitemapfrm',
				'content' =>'',
				'pagination' =>'',
				'filter_panel' =>  '',
				'actions_panel' => $sActionsPanel,
			);
			echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
	  
			$aVars = array (
				'content' => ob_get_clean(),
			);
			return $this->_oTemplate->parseHtmlByName('default_padding', $aVars); 
		}

     }
  
     function _addSiteMapForm ($sRedirectUrl) {

        bx_import ('FormSiteMapAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormSiteMapAdd';
        $oForm = new $sClass ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();                        
    
            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {
				
				$this->_oDb->cleanCache('sys_objects_site_maps');  

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap';
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }

    function _editSiteMapForm ($aDataEntry, $iEntryId) { 

        $iEntryId = (int)$iEntryId;
   
        bx_import ('FormSiteMapEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormSiteMapEdit';
        $oForm = new $sClass ($this, $iEntryId, $aDataEntry);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
				
				$this->_oDb->cleanCache('sys_objects_site_maps');  
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap' );
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }
	}
 
	//end site map
 
	//begin site stat
    function actionAdministrationStatManage ($sValue, $isActiveEntries = false) {

		$sKeyBtnDelete = '_modzzz_atool_admin_delete';
		$sKeyBtnActivate = '_modzzz_atool_admin_activate';
		$sKeyBtnAdd = '_modzzz_atool_admin_add_entry';
		$sKeyBtnArchive = '_modzzz_atool_admin_archive';
		$sKeyBtnViewStats = '_modzzz_atool_admin_view_entries';

        if ($_POST['action_activate'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                $this->_oDb->activateSiteStatEntry($iId);
            }

			header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat'); 


        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
 
                if (!$this->isAdmin()) 
                    continue;
			 
                $this->_oDb->deleteSiteStatById($iId);
            }

        } elseif ($_POST['action_archive_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
 
                if (!$this->isAdmin()) 
                    continue;
			 
                $this->_oDb->deleteSiteStatArchiveById($iId);
            }

        } elseif ($_POST['action_add']) {

			header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat/add');
 
        } elseif ($_POST['action_archive']) {

			header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat/archive'); 
        }
   
        if ($isActiveEntries) {
            $sContent = $this->_manageStatActiveEntries ('stat_active', $sValue, true, 'bx_twig_admin_form', array(
				'action_archive' => $sKeyBtnArchive,
                'action_delete' => $sKeyBtnDelete,
				'action_add' => $sKeyBtnAdd,
				'action_view_stat' => $sKeyBtnViewStats, 

			));
        } else {
            $sContent = $this->_manageStatArchiveEntries ('stat_archive', $sValue, false, 'bx_twig_admin_form',  
			array(
                'action_activate' => $sKeyBtnActivate,
                'action_archive_delete' => $sKeyBtnDelete,
				'action_add' => $sKeyBtnAdd,
				'action_view_stat' => $sKeyBtnViewStats,
            ));  
        }
 
        return $sContent;
    }

    function _manageStatActiveEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false) {
    
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'stat_unit_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;
 
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
        } elseif (!($sContent = $o->displayResultBlock())) {
            if ($isMsgBoxIfEmpty)
                $sContent = MsgBox(_t('_Empty'));
            else
                return '';
        } else { 
            $sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
        }

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => '',
            'filter_panel' => '',
            'actions_panel' => $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
    function _manageStatArchiveEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false)
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'stat_archive_admin';

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
            'actions_panel' => $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
    function actionAdministrationStatCreate($sUrl='', $iEntryId=0) {

        $iEntryId = (int)$iEntryId;
  
		if($iEntryId) {
				
			 if (!($aDataEntry = $this->_oDb->getSiteStatEntryById($iEntryId))) {
				$this->_oTemplate->displayPageNotFound ();
				exit;
			 }
 		 
 
			ob_start();

			$this->_editStatForm($aDataEntry, $iEntryId);

			bx_import ('SearchResult', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SearchResult';
			$o = new $sClass('', '');

			$aButtons = array('action_view_stat' => '_modzzz_atool_admin_view_entries'); 
			$sActionsPanel = $o->showAdminActionsPanel ('statfrm', $aButtons, '', false);

			$aVars = array (
				'form_name' => 'statfrm',
				'content' =>'',
				'pagination' =>'',
				'filter_panel' =>  '',
				'actions_panel' => $sActionsPanel,
			);
			echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
	  
			$aVars = array (
				'content' => ob_get_clean(),
			);
			return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
   
		}else{
  
			ob_start(); 
			
			$this->_addStatForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat');
  
			bx_import ('SearchResult', $this->_aModule);
			$sClass = $this->_aModule['class_prefix'] . 'SearchResult';
			$o = new $sClass('', '');

			$aButtons = array('action_view_stat' => '_modzzz_atool_admin_view_entries'); 
			$sActionsPanel = $o->showAdminActionsPanel ('statfrm', $aButtons, '', false);

			$aVars = array (
				'form_name' => 'statfrm',
				'content' =>'',
				'pagination' =>'',
				'filter_panel' =>  '',
				'actions_panel' => $sActionsPanel,
			);
			echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
	  
			$aVars = array (
				'content' => ob_get_clean(),
			);
			return $this->_oTemplate->parseHtmlByName('default_padding', $aVars); 
		}

     }
  
     function _addStatForm ($sRedirectUrl) {

        bx_import ('FormStatAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormStatAdd';
        $oForm = new $sClass ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();                        
    
            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {
				
				$this->_oDb->cleanCache('sys_stat_site');  

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat';
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }

    function _editStatForm ($aDataEntry, $iEntryId) { 

        $iEntryId = (int)$iEntryId;
   
        bx_import ('FormStatEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormStatEdit';
        $oForm = new $sClass ($this, $iEntryId, $aDataEntry);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
				
				$this->_oDb->cleanCache('sys_stat_site');  
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat' );
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }
	} 
	//end site stat

 
    function actionAdministration ($sUrl = '', $sParam1 = '', $sParam2 = '', $sParam3 = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
  
        $this->_oTemplate->pageStart();

        $aMenu = array(   
	        'privacy' => array(
                'title' => _t('_modzzz_atool_menu_manage_privacy'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/privacy',
                '_func' => array ('name' => 'actionAdministrationPrivacy', 'params' => array($sParam1, true)),
            ), 
	        'sitemap' => array(
                'title' => _t('_modzzz_atool_menu_manage_sitemap'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap',
                '_func' => array ('name' => 'actionAdministrationSiteMapManage', 'params' => array($sParam1, true)),
            ), 
	        'sitestat' => array(
                'title' => _t('_modzzz_atool_menu_manage_sitestat'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat',
                '_func' => array ('name' => 'actionAdministrationStatManage', 'params' => array($sParam1, true)),
            ),  
		    'comment' => array(
                'title' => _t('_modzzz_atool_menu_admin_comment'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/comment',
                '_func' => array ('name' => 'actionAdministrationComment', 'params' => array($sParam1,$sParam2)),
            ), 
		    'status' => array(
                'title' => _t('_modzzz_atool_menu_admin_status'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/status',
                '_func' => array ('name' => 'actionAdministrationStatus', 'params' => array($sParam1,$sParam2)),
            ),  
		    'message' => array(
                'title' => _t('_modzzz_atool_menu_admin_message'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message',
                '_func' => array ('name' => 'actionAdministrationMessage', 'params' => array($sParam1,$sParam2)),
            ), 
	        'email' => array(
                'title' => _t('_modzzz_atool_menu_email_templates'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/email',
                '_func' => array ('name' => 'actionAdministrationEmailTemplate', 'params' => array($sParam1,$sParam2)),
            ), 			
	       'page' => array(
                'title' => _t('_modzzz_atool_menu_pages'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/page',
                '_func' => array ('name' => 'actionAdministrationPage', 'params' => array($sParam1, false)),
            ), 
            'settings' => array(
                'title' => _t('_modzzz_atool_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ), 
        );

		if (empty($aMenu[$sUrl])){
            $sUrl = 'privacy';
		}
  
        $aMenu[$sUrl]['active'] = 1;
 

		if($sUrl=='sitestat'){ 
 
			if ($_POST['action_view_stat']) { 
				header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitestat'); 
			}

			if($sParam1=='archive')
				$sContent = call_user_func_array (array($this, 'actionAdministrationStatManage'), array($sParam2, false)); 
			elseif($sParam1=='add')
				$sContent = call_user_func_array (array($this, 'actionAdministrationStatCreate'), array($sUrl, $sParam2));
			else
				$sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']); 

		}elseif($sUrl=='sitemap'){ 
 
			if ($_POST['action_view']) { 
				header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/sitemap'); 
			}

			if($sParam1=='archive')
				$sContent = call_user_func_array (array($this, 'actionAdministrationSiteMapManage'), array($sParam2, false)); 
			elseif($sParam1=='manage')
				$sContent = call_user_func_array (array($this, 'actionAdministrationSiteMapCreate'), array($sUrl, $sParam2));
			else
				$sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']); 
 

		}elseif($sUrl=='message'){ 
 
			if ($_POST['action_view_message']) { 
				header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message'); 
			}

			if($sParam1=='add')
				$sContent = call_user_func_array (array($this, 'actionAdministrationCreateMessage'), array()); 
			elseif($sParam1=='respond')
				$sContent = call_user_func_array (array($this, 'actionAdministrationRespondMessage'), array($sParam2));
			else
				$sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']); 

		}else{
			$sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']); 
		}
  
        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_atool_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('twig.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');       
  
        $this->_oTemplate->addCssAdmin ('http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');       
		//$this->_oTemplate->addJs ('http://code.jquery.com/jquery-1.9.1.js'); 
 		$this->_oTemplate->addJs (array('http://code.jquery.com/ui/1.10.3/jquery-ui.js')); 
 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_atool_page_title_administration'));  
    }
 
    function showAdminFilterPanel($sBaseUrl)
    { 
  
        $sFilter = _t('_modzzz_atool_action_title_filter');
        $sApply = _t('_modzzz_atool_action_title_apply');
        $sUserName = _t('_modzzz_atool_caption_username');
        $sKeyword = _t('_modzzz_atool_caption_keyword');

		$sParam1 = bx_get('param1');
        $sFilterValue1 = bx_html_attribute($sParam1);
 
 		$sParam2 = bx_get('param2');
        $sFilterValue2 = bx_html_attribute($sParam2);
 
        $sJsContent = "";
    
		ob_start();
?>
    <script type="text/javascript">
 
        function on_filter_apply ()
        { 
            document.location = '<?=$sBaseUrl;?>?param1='+document.getElementById('param1').value+'&param2='+document.getElementById('param2').value;
        }

    </script>
<?php
		$sJsContent = ob_get_clean();
 
        $sContent = <<<EOF
        {$sJsContent}
                <table>
                    <tr>
                        <td>{$sFilter}</td> 
                        <td>{$sUserName}<br> 
                            <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border"> 
                                <input type="text" id="param1" name="param2" value="{$sParam1}" class="form_input_text bx-def-font" />  
                            </div>
                        </td> 
                        <td>{$sKeyword}<br>  
                            <div class="input_wrapper input_wrapper_text bx-def-round-corners-with-border"> 
                                <input type="text" id="param2" name="param2" value="{$sParam2}" class="form_input_text bx-def-font" />
                            </div>
                        </td>
                        <td>
							 <button class="bx-btn bx-btn-small bx-btn-img bx-btn-ifont" onClick="on_filter_apply()"> 
								<i class="sys-icon search"></i>
								{$sApply}
							</button>  						
					    </td> 
                    </tr>
                </table>
EOF;

        return $GLOBALS['oSysTemplate']->parseHtmlByName('designbox_top_controls.html', array(
            'top_controls' => $sContent
        ));
    }
   
    function actionAdministrationComment ($sParam1='',$sParam2='') {
   
		$sKeyBtnDelete = '_modzzz_atool_admin_delete';
 
		$sUrl = false;
  
        if ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
 
                if (!$this->isAdmin()) continue;
                     
                $this->_oDb->deleteCommentById($iId);
            }
        }
 
		$sContent = $this->_manageComment ('comment', '', true, 'bx_twig_admin_form', array(
		'action_delete' => $sKeyBtnDelete 
		), '', true, 0, $sUrl);
	

        return $sContent;
    }

    function _manageComment ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false)
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'comment_unit_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
  
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
			$aButtons = array();
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons, '', false);
         } elseif (!($sContent = $o->displayResultBlock())) { 
			$sContent = MsgBox(_t('_Empty'));
			$aButtons = array();
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons, '', false);
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination($sUrlAdmin);
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
		}
  
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/comment';

        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $this->showAdminFilterPanel($sBaseUrl), 
            /*'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id','filter_checkbox_id', $this->_sFilterName) : '',*/ 
            'actions_panel' => $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
    function actionAdministrationStatus ($sParam1='',$sParam2='') {
   
		$sKeyBtnDelete = '_modzzz_atool_admin_delete';
 
		$sUrl = false;
  
        if ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
 
                if (!$this->isAdmin()) continue;
                     
                $this->_oDb->deleteStatusById($iId);
            }
        }
 
		$sContent = $this->_manageStatus ('status', '', true, 'bx_twig_admin_form', array(
		'action_delete' => $sKeyBtnDelete 
		), '', true, 0, $sUrl);
	

        return $sContent;
    }

    function _manageStatus ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false)
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'status_unit_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
  
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
			$aButtons = array();
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons, '', false);
         } elseif (!($sContent = $o->displayResultBlock())) { 
			$sContent = MsgBox(_t('_Empty'));
			$aButtons = array();
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons, '', false);
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination($sUrlAdmin);
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
		}
  
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/status';
  
        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $this->showAdminFilterPanel($sBaseUrl), 
            /*'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id', 'filter_checkbox_id', $this->_sFilterName) : '',*/
            'actions_panel' => $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
  
    function actionAdministrationMessage ($sParam1='',$sParam2='') {
   
		$sKeyBtnDelete = '_modzzz_atool_admin_delete';
		$sKeyBtnAddMessage = '_modzzz_atool_admin_add_message';
		$sUrl = false;
  
        if ($_POST['action_add_message']) {

			header('Location:'. BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message/add');
 
        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
 
                if (!$this->isAdmin()) continue;
                     
                $this->_oDb->deleteMessageById($iId);
            }
        }
 
		$sContent = $this->_manageMessage ('message', '', true, 'bx_twig_admin_form', array(
		'action_delete' => $sKeyBtnDelete,
		'action_add_message' => $sKeyBtnAddMessage,
		), '', true, 0, $sUrl);
	

        return $sContent;
    }

    function _manageMessage ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false)
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'message_unit_admin';

        if ($iPerPage)
            $o->aCurrent['paginate']['perPage'] = $iPerPage;

        $sPagination = $sActionsPanel = '';
  
        if ($o->isError) {
            $sContent = MsgBox(_t('_Error Occured'));
			$aButtons = array('action_add_message' => '_modzzz_atool_admin_add_message');
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons, '', false);
         } elseif (!($sContent = $o->displayResultBlock())) { 
			$sContent = MsgBox(_t('_Empty'));
			$aButtons = array('action_add_message' => '_modzzz_atool_admin_add_message');
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons, '', false);
        } else {
            $sPagination = $sAjaxPaginationBlockId ? $o->showPaginationAjax($sAjaxPaginationBlockId) : $o->showPagination($sUrlAdmin);
			$sActionsPanel = $o->showAdminActionsPanel ($sFormName, $aButtons);
		}
   
		$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message';
   
        $aVars = array (
            'form_name' => $sFormName,
            'content' => $sContent,
            'pagination' => $sPagination,
            'filter_panel' => $this->showAdminFilterPanel($sBaseUrl), 
            /*'filter_panel' => $isFilter ? $o->showAdminFilterPanel(false !== bx_get($this->_sFilterName) ? bx_get($this->_sFilterName) : '', 'filter_input_id', 'filter_checkbox_id', $this->_sFilterName) : '',*/
            'actions_panel' => $sActionsPanel,
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
    function actionAdministrationCreateMessage ()
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        ob_start();

        $this->_addMessage();

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass('', '');

		$aButtons = array('action_view_message' => '_modzzz_atool_admin_view_message'); 
		$sActionsPanel = $o->showAdminActionsPanel ('viewfrm', $aButtons, '', false);

        $aVars = array (
            'form_name' => 'viewfrm',
            'content' =>'',
            'pagination' =>'',
            'filter_panel' =>  '',
            'actions_panel' => $sActionsPanel,
        );
        echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
  
		$aVars = array (
            'content' => ob_get_clean(),
        );
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }
  
    function _addMessage () { 
 
        bx_import ('FormMessageAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormMessageAdd';
        $oForm = new $sClass ($this);
        $oForm->initChecker();

		if ($oForm->isSubmittedAndValid ()) {
  
			global $tmpl;
			require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');
  
			$iRecipientId = $this->_oDb->getProfileIdByNickName ($oForm->getCleanValue('recipient'), false); 
			$sMessage = $oForm->getCleanValue('message');
			$sSubject = $oForm->getCleanValue('subject');
 
            if ($iRecipientId) {

				$aMailBoxSettings = array
				(
					'member_id'	 =>  $this->_iProfileId, 
					'recipient_id'	 => $iRecipientId, 
					'messages_types'	 =>  'mail',  
				);

				$aComposeSettings = array
				(
					'send_copy' => false , 
					'send_copy_to_me' => false , 
					'notification' => false ,
				);
				$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);
	 
				$oMailBox -> iWaitMinutes = 0;//turn off anti-spam
				$bSent = $oMailBox -> sendMessage($sSubject, $sMessage, $iRecipientId, $aComposeSettings); 

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message';
			  
				header ('Location:' . $sRedirectUrl);
				exit; 
			} else {   
				echo MsgBox(_t('_modzzz_atool_msg_error_no_recipient')) . $oForm->getCode ();
			}

        } else { 
            echo $oForm->getCode (); 
        }
    }

    function actionAdministrationRespondMessage ($iMessageId)
    {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        ob_start();

        $this->_respondMessage($iMessageId);

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass('', '');

		$aButtons = array('action_view_message' => '_modzzz_atool_admin_view_message'); 
		$sActionsPanel = $o->showAdminActionsPanel ('viewfrm', $aButtons, '', false);

        $aVars = array (
            'form_name' => 'viewfrm',
            'content' =>'',
            'pagination' =>'',
            'filter_panel' =>  '',
            'actions_panel' => $sActionsPanel,
        );
        echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
  
		$aVars = array (
            'content' => ob_get_clean(),
        );
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }
  
    function _respondMessage ($iMessageId) { 
 
        bx_import ('FormMessageRespond', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormMessageRespond';
        $oForm = new $sClass ($this, $iMessageId);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
  
			global $tmpl;
			require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');
 
			$iMessageId = $oForm->getCleanValue('id');
			$aMessage = $this->_oDb->getMessageEntryById($iMessageId);
 			$iSenderId = $aMessage['Sender'];

			$sMessage = $oForm->getCleanValue('message');
			$sSubject = _t('_modzzz_atool_form_caption_re') .': '. $aMessage['Subject'];
 
			$aMailBoxSettings = array
			(
				'member_id'	 =>  $this->_iProfileId, 
				'recipient_id'	 => $iSenderId, 
				'messages_types'	 =>  'mail',  
			);

			$aComposeSettings = array
			(
				'send_copy' => false , 
				'send_copy_to_me' => false , 
				'notification' => false ,
			);
			$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);
 
			$oMailBox -> iWaitMinutes = 0;//turn off anti-spam
			$bSent = $oMailBox -> sendMessage($sSubject, $sMessage, $iSenderId, $aComposeSettings); 
		   
            if ($bSent) {
				  
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message';
			  
                header ('Location:' . $sRedirectUrl);
                exit; 
            } else { 
                echo MsgBox(_t('_Error Occured')) . $oForm->getCode ();
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
 

 /*
    function actionAdministrationEditMessage ($iEntryId) {
    
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        ob_start();

        $this->_editMessage($iEntryId);

        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass('', '');

		$aButtons = array('action_view_message' => '_modzzz_atool_admin_view_message'); 
		$sActionsPanel = $o->showAdminActionsPanel ('viewfrm', $aButtons, '', false);

        $aVars = array (
            'form_name' => 'viewfrm',
            'content' =>'',
            'pagination' =>'',
            'filter_panel' =>  '',
            'actions_panel' => $sActionsPanel,
        );
        echo $this->_oTemplate->parseHtmlByName ('manage', $aVars);
 
        $aVars = array (
            'content' => ob_get_clean(),
        );
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }
 
    function _editMessage ($iEntryId) { 
   
   		$sMainUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/message';

	    $iEntryId = (int)$iEntryId;
        if (!($aDataEntry = $this->_oDb->getMessageEntryById($iEntryId))) {
            $this->_oTemplate->displayPageNotFound ();
            return;
        }

        bx_import ('MessageEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'MessageEdit';
        $oForm = new $sClass ($this, $iEntryId, $aDataEntry);
          
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
 			$aValsAdd = array($this->_oDb->_sFieldProfileId => $iUserId); 
 
            if ($oForm->update ($iEntryId, $aValsAdd)) {
 
				//$this->_oDb->deleteMessageFromCategories ($iEntryId);
  
                header ('Location:' . $sMainUrl);
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }  
    }
*/


    function actionAdministrationSettings () {
        $sSettingsCatName = 'ATool';
 
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $sSettingsCatName))
            return MsgBox(_t('_sys_request_page_not_found_cpt'));

        $iId = $this->_oDb->getSettingsCategory($sSettingsCatName);
        if(empty($iId))
           return MsgBox(_t('_sys_request_page_not_found_cpt'));

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {

			if($_POST['modzzz_atool_refresh_language']=='yes'){
				$this->_oDb->initialize(); 
			}

            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);
        }

        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();

        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;

        $aVars = array (
            'content' => $sResult,
        );
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }
  
    function actionAdministrationEmailTemplate ($sParam1='',$sParam2='') {

		$sTemplate = bx_get('template');
		$sEditor = bx_get('editor');
		$iLang = bx_get('language'); 
		$sLang = getParam( 'lang_default' );
		$iDefaultLang = ($iLang) ? $iLang : getLangIdByName($sLang);
 
		if(bx_get('language')==getLangIdByName('en'))
			$iLang = 0;
 
		$aTemplates = array();
		$aTemplates[] = array(
				'template_value' => '',
				'template_caption' => _t('_Select'),
				'template_selected' => ''
			);


		$aDbTemplates = $this->_oDb->getEmailTemplates($iLang); 
		foreach ($aDbTemplates as $aEachTemplate) { 
			$sTemplate = ($sTemplate) ? $sTemplate : $aEachTemplate['template'];
			$aTemplates[] = array(
				'template_value' => $aEachTemplate['Name'],
				'template_caption' => $aEachTemplate['Desc'],
				'template_selected' => ($aEachTemplate['Name'] == $sTemplate) ? 'selected="selected"' : ''
			);
		}

		$aLangs = array();
		$aDbLangs = $this->_oDb->getLanguages(); 
		foreach ($aDbLangs as $iEachLangId=>$sEachLangName) { 
			$aLangs[] = array(
				'language_value' => $iEachLangId,
				'language_caption' => $sEachLangName,
				'language_selected' => ($iEachLangId == $iDefaultLang) ? 'selected="selected"' : ''
			);
		}
		$iNumLanguages = count($aDbLangs);

		$aVars = array(  
			'action_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/email/',
			'tinymce_selected' => ($sEditor=='tinymce') ? 'selected="selected"' : '', 
			'plain_selected' =>  ($sEditor=='plain') ? 'selected="selected"' : '', 
			'bx_repeat:template_items' => $aTemplates,
			'bx_repeat:lang_items' => $aLangs,
		);
 
		$sSelectContent = $this->_oTemplate->parseHtmlByName('template_select_box', $aVars);

		$sSelectForm = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sSelectContent));
   
		$sEmailTemplateForm = $this->getEmailTemplateForm($sTemplate, $iLang, $sEditor, $iNumLanguages);

        return $sSelectForm . $sEmailTemplateForm;
    }
 
    function getEmailTemplateForm($sName, $iLang, $sEditor, $iNumLanguages) {

		if (!($aDataEntry = $this->_oDb->getEmailTemplateByName($sName, $iLang, $iNumLanguages))) {  
			//$this->_oTemplate->displayEmailNotFound ();
			//exit;
		}
  
		ob_start();        
		$this->_editEmailForm($iLang, $sEditor, $aDataEntry);
		$aVars = array (
			'content' => ob_get_clean(),
		); 
		 
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }
  
    function _editEmailForm ($iLang, $sEditor, $aDataEntry) { 
  
        bx_import ('FormEmailEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormEmailEdit';
        $oForm = new $sClass ($this, $iLang, $sEditor, $aDataEntry['ID']);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();
            if ($oForm->update ($aDataEntry['ID'], $aValsAdd)) {
   
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/email/');
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        } 
    }
 
    function actionAdministrationPage () {

		$sPage = bx_get('page');
		$sEditor = bx_get('editor');
		$iLang = bx_get('language'); 
		$sLang = getParam( 'lang_default' );
		$iLang = ($iLang) ? $iLang : getLangIdByName($sLang);

		$aPages = array();
		$aDbPages = $this->_oDb->getPages($iLang); 
		foreach ($aDbPages as $aEachPage) { 
			$sPage = ($sPage) ? $sPage : $aEachPage['page'];
			$aPages[] = array(
				'page_value' => $aEachPage['page'],
				'page_caption' => $aEachPage['identifier'],
				'page_selected' => ($aEachPage['page'] == $sPage) ? 'selected="selected"' : ''
			);
		}

		$aLangs = array();
		$aDbLangs = $this->_oDb->getLanguages(); 
 
		foreach ($aDbLangs as $iEachLangId=>$sEachLangName) {  
			$aLangs[] = array(
				'language_value' => $iEachLangId,
				'language_caption' => $sEachLangName,
				'language_selected' => ($iEachLangId == $iLang) ? 'selected="selected"' : ''
			);
		}
 
		$aVars = array(  
			'action_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/page/',
			'tinymce_selected' => ($sEditor=='tinymce') ? 'selected="selected"' : '', 
			'plain_selected' =>  ($sEditor=='plain') ? 'selected="selected"' : '', 
			'bx_repeat:page_items' => $aPages,
			'bx_repeat:lang_items' => $aLangs,
		);
 
		$sSelectContent = $this->_oTemplate->parseHtmlByName('page_select_box', $aVars);

		$sSelectForm = $GLOBALS['oAdmTemplate']->parseHtmlByName('design_box_content.html', array('content' => $sSelectContent));
  
   
		$sPageForm = $this->getPageForm($sPage, $iLang, $sEditor);

        return $sSelectForm . $sPageForm;
    }
 
    function getPageForm($sPage, $iLang, $sEditor) {
 
		if ($aDataEntry = $this->_oDb->getEntryByPage($sPage, $iLang)) {
  
			ob_start();        
			$this->_editPageForm($sPage, $iLang, $sEditor, $aDataEntry);
			$aVars = array (
				'content' => ob_get_clean(),
			); 

		}else{

			$bValidLanguage = $this->_oDb->isValidLanguage($iLang);
			if ($bValidLanguage && $aDataEntry = $this->_oDb->getFirstEntryByPage($sPage)) {
				ob_start();        
				$this->_addPageForm($sPage, $iLang, $sEditor, $aDataEntry);
				$aVars = array (
					'content' => ob_get_clean(),
				); 
			}/*else{
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/page/');
                exit;
			}*/ 
		}
		 
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }
  
    function _addPageForm ($sPage, $iLang, $sEditor, $aDataEntry) { 
  
        bx_import ('FormPageAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormPageAdd';
        $oForm = new $sClass ($this, $sPage, $iLang, $sEditor, $aDataEntry);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();
            if ($oForm->insert ($aValsAdd)) {
   
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/page/');
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }
 
    }
   
    function _editPageForm ($sPage, $iLang, $sEditor, $aDataEntry) { 
  
        bx_import ('FormPageEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormPageEdit';
        $oForm = new $sClass ($this, $sPage, $iLang, $sEditor, $aDataEntry);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();
            if ($oForm->update ($aDataEntry['id'], $aValsAdd)) {
   
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/page/');
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }
 
    }
  
  
    // ================================== permissions
  
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('modzzz_atool_permalinks') == 'on'));
		 
        return $bEnabled;
    }
  
    function isAllowedAdd () {
        return $this->isAdmin();
    } 

    function serviceInitialize () {
        return $this->_oDb->initialize();
    } 
 
    function serviceCleanup () {
        return $this->_oDb->cleanup();
    }   

	//[begin] featured
	function isActionFeatured($iProfileId){
        $iProfileId = (int)$iProfileId; 

		if(!$this->isAdmin()) return;
 
		return ($this->_oDb->isFeatured($iProfileId)) ? _t('_modzzz_atool_action_unfeature') : _t('_modzzz_atool_action_make_featured');
 	}

    function actionMarkFeatured ($iProfileId) {
        $iProfileId = (int)$iProfileId; 

		$sMsgSuccessAdd = _t('_modzzz_atool_msg_added_to_featured');
		$sMsgSuccessRemove = _t('_modzzz_atool_msg_removed_from_featured');
 
        $iProfileId = (int)$iProfileId; 
		$sRedirect = getProfileLink($iProfileId);
  
		if (0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
			exit;
		}
 
		$bMakeFeatured = $this->_oDb->isFeatured($iProfileId) ? false : true;

		if ($this->_oDb->makeFeatured($iProfileId, $bMakeFeatured)) {
 
			$isFeatured = $this->_oDb->isFeatured($iProfileId);

			$sJQueryJS = genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div', $sRedirect);
			echo MsgBox($isFeatured ? $sMsgSuccessAdd : $sMsgSuccessRemove) . $sJQueryJS;
			exit;
		}        
 
		echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
		exit;  
    }
	//[end] featured

 
	//[begin] banned
	function isProfileBanned($iProfileId){
        $iProfileId = (int)$iProfileId; 

		if(!$this->isAdmin()) return;
 
		return ($this->_oDb->isProfileBanned($iProfileId)) ? _t('_modzzz_atool_action_unban') : _t('_modzzz_atool_action_ban');
 	}

    function actionMarkBan ($iProfileId) {
        $iProfileId = (int)$iProfileId; 

		$sMsgSuccessBan = _t('_modzzz_atool_msg_profile_ban');
		$sMsgSuccessDeban = _t('_modzzz_atool_msg_profile_unban');
 
        $iProfileId = (int)$iProfileId; 
		$sRedirect = getProfileLink($iProfileId);
  
		if (0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
			exit;
		}
 
		$bBanProfile = $this->_oDb->isProfileBan($iProfileId) ? false : true;

		if ($this->_oDb->changeProfileStatus($iProfileId, $bBanProfile)) {
 
			$isProfileBanned = $this->_oDb->isProfileBan($iProfileId);

			$sJQueryJS = genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div', $sRedirect);
			echo MsgBox($isProfileBanned ? $sMsgSuccessBan : $sMsgSuccessDeban) . $sJQueryJS;
			exit;
		}        
 
		echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
		exit;  
    }
	//[end] banned

	//[begin] activated
	function isProfileActivated($iProfileId){
        $iProfileId = (int)$iProfileId; 

		if(!$this->isAdmin()) return;
 
		return ($this->_oDb->isProfileActivated($iProfileId)) ? _t('_modzzz_atool_action_deactivate') : _t('_modzzz_atool_action_activate');
 	}

    function actionChangeProfileStatus ($iProfileId) {
        $iProfileId = (int)$iProfileId; 

		$sMsgSuccessActivated = _t('_modzzz_atool_msg_profile_activated');
		$sMsgSuccessDeactivated = _t('_modzzz_atool_msg_profile_deactivated');
 
        $iProfileId = (int)$iProfileId; 
		$sRedirect = getProfileLink($iProfileId);
  
		if (0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
			exit;
		}
 
		$bActivate = $this->_oDb->isProfileActivated($iProfileId) ? false : true;

		if ($this->_oDb->changeProfileStatus($iProfileId, $bActivate)) {
 
			$isProfileActivated = $this->_oDb->isProfileActivated($iProfileId);

			$sJQueryJS = genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div', $sRedirect);
			echo MsgBox($isProfileActivated ? $sMsgSuccessActivated : $sMsgSuccessDeactivated) . $sJQueryJS;
			exit;
		}        
 
		echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
		exit;  
    }
	//[end] activated


	//[begin] unconfirmed
	function isProfileUnconfirmed($iProfileId){
        $iProfileId = (int)$iProfileId; 

		if(!$this->isAdmin()) return;
 
		return ($this->_oDb->isProfileUnconfirmed($iProfileId)) ? _t('_modzzz_atool_action_send_confirmation') : '';
 	}

    function actionSendConfirmation ($iProfileId) {
        $iProfileId = (int)$iProfileId; 

		$sMsgSuccess = _t('_modzzz_atool_msg_confirmation_email_sent');
		$sMsgFail = _t('_modzzz_atool_msg_confirmation_email_failed');
  
        $iProfileId = (int)$iProfileId; 
		$sRedirect = getProfileLink($iProfileId);
  
		if (0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
			exit;
		}
 
        $bSuccess = activation_mail((int)$iProfileId, 0);
 
		$sJQueryJS = genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div', $sRedirect);
		echo MsgBox($bSuccess ? $sMsgSuccess : $sMsgFail) . $sJQueryJS;
		exit; 
    }
	//[end] unconfirmed

    function actionProfileDelete ($iProfileId) {

		$iIdCurr = getLoggedId(); 
        $iProfileId = (int)$iProfileId;
        if ($iIdCurr != $iProfileId)
            $bResult = profile_delete($iProfileId, false);

		header('Location:'. BX_DOL_URL_ROOT . 'administration');
    }

    function actionProfileDeleteSpammer ($iProfileId) {

		$iIdCurr = getLoggedId(); 
        $iProfileId = (int)$iProfileId;
        if ($iIdCurr != $iProfileId)
            $bResult = profile_delete($iProfileId, true);

		header('Location:'. BX_DOL_URL_ROOT . 'administration');
    }

	//[begin] make administrator

	function isActionAdmin($iProfileId){
        $iProfileId = (int)$iProfileId; 
 
		if(!$this->isAdmin()) return false;
 
        if ($this->_iProfileId == $iProfileId) return false;

		return ( isAdmin($iProfileId) ) ? _t('_modzzz_atool_action_remove_from_admin') : _t('_modzzz_atool_action_make_admin');
 	}

    function actionMakeAdmin($iProfileId) {
        $iProfileId = (int)$iProfileId; 

		$sMsgSuccessAdd = _t('_modzzz_atool_msg_make_admin');
		$sMsgSuccessRemove = _t('_modzzz_atool_msg_removed_from_admin');
 
        $iProfileId = (int)$iProfileId; 
		$sRedirect = getProfileLink($iProfileId);
 
		if (0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
			exit;
		}
 
		$bMakeAdmin = isAdmin($iProfileId) ? false : true;

		if ($this->_oDb->makeAdmin($iProfileId, $bMakeAdmin)) {
 
			//$isAdmin = isAdmin($iProfileId);

			$sJQueryJS = genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div', $sRedirect);
			echo MsgBox($bMakeAdmin ? $sMsgSuccessAdd : $sMsgSuccessRemove) . $sJQueryJS;
			exit;
		}        
 
		echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iProfileId, 'ajaxy_popup_result_div');
		exit;  
    }
	//[end] make administrator


}