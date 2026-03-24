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

function modzzz_manager_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'manager') {
        $oMain = BxDolModule::getInstance('BxManagerModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
 
 
/*
 * Manager module
 * 
 * 
 *
 */
class BxManagerModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();

    function BxManagerModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_manager_filter';
        $this->_sPrefix = 'modzzz_manager';
        $this->_oDb->_sPrefix = 'modzzz_manager_';

        $this->sSearchResultClassName = 'BxManagerSearchResult'; 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'answer';
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&'); 
   
        $GLOBALS['oBxManagerModule'] = &$this; 
    }

    function actionHome () {  
		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/all';
		header ('Location:' . $sRedirectUrl);  
    }
   
    // ================================== admin actions
  
    function actionSort ($sType) {
  
        if (!$this->isAdmin()) { 
            return;
        } 
		
		$iOrder = 0;
		foreach ($_POST['item'] as $iEntryId) {
			$this->_oDb->sortActions($sType, $iEntryId, $iOrder); 
			$iOrder++;
		} 
	}
 
    function actionArchive ($iId) {
		$iId = (int)$iId;

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 
		
		$aDataEntry = $this->_oDb->getEntryById($iId);
 
		$this->_oDb->archiveEntry($iId);

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/archive';
		header ('Location:' . $sRedirectUrl); 
	}
 
    function actionDelete ($iId) {
		$iId = (int)$iId;

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 
 
		$this->_oDb->deleteEntryById($iId);

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/all';
		header ('Location:' . $sRedirectUrl); 
	}

    function actionAdministration ($sUrl = '', $sParam1 = '', $sParam2 = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
  
        $this->_oTemplate->pageStart();

        $aMenu = array(  
	       'all' => array(
                'title' => _t('_modzzz_manager_menu_manage_actions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/all',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array($sParam1, true)),
            ), 
	       'archive' => array(
                'title' => _t('_modzzz_manager_menu_archive_actions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/archive',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array($sParam1, false)),
            ), 			
            'create' => array(
                'title' => _t('_modzzz_manager_menu_admin_create_action'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreate', 'params' => array($sUrl, $sParam1)),
            ) 
        );

		if (empty($aMenu[$sUrl])){
            $sUrl = 'all';
		}
  
        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_manager_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('twig.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');       
        $this->_oTemplate->addCssAdmin ('https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');       
		//$this->_oTemplate->addJs ('http://code.jquery.com/jquery-1.9.1.js'); 
 		$this->_oTemplate->addJs (array('https://code.jquery.com/ui/1.10.3/jquery-ui.js')); 
 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_manager_page_title_administration'));
    }

    function actionAdministrationManage ($sValue, $isActiveEntries = false) {
        return $this->_actionAdministrationManage ($sValue, $isActiveEntries);
    }

    function _actionAdministrationManage ($sValue, $isActiveEntries) {

		$sKeyBtnDelete = '_modzzz_manager_admin_delete';
		$sKeyBtnActivate = '_modzzz_manager_admin_activate';

        if ($_POST['action_activate'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                $this->_oDb->activateEntry($iId);
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {

                $aDataEntry = $this->_oDb->getArchiveById($iId);
				 
                if (!$this->isAdmin()) 
                    continue;
			 
                $this->_oDb->deleteArchiveById($iId);
            }
        }


		//[begin] units 
		$aUnits = array();
		$aDbUnits = $this->_oDb->getUnits();
		 
		$sInitUnit = '';
		foreach ($aDbUnits as $aEachUnit) { 
			$sUnit = $aEachUnit['Type'];
 			$sInitUnit = ($sInitUnit) ? $sInitUnit : $sUnit;

			$aUnits[] = array(
				'value' => $sUnit,
				'caption' => $sUnit,
				'selected' => ($sValue == $sUnit) ? 'selected="selected"' : ''
			);
		}
		
		$sValue = ($sValue) ? $sValue : $sInitUnit;

		$sSelectContent = $this->_oTemplate->parseHtmlByName('select_box', $arr = array(
			'name' => _t('_modzzz_manager_form_action_select_action_type'),
			'object_name' => 'manager_unit',
			'action_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/' . ($isActiveEntries ? 'all/' : 'archive/'),
			'bx_repeat:items' => $aUnits,
		));
		//[end] units
 
        if ($isActiveEntries) {
            $sContent = $this->_manageActiveEntries ('active', $sValue, true, 'bx_twig_admin_form', array());
        } else {
            $sContent = $this->_manageArchiveEntries ('archive', $sValue, false, 'bx_twig_admin_form',  
			array(
                'action_activate' => $sKeyBtnActivate,
                'action_delete' => $sKeyBtnDelete,
            ));  
        }
 
        return $sSelectContent . $sContent;
    }

    function _manageActiveEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false) {
    
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'unit_admin';

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
            'actions_panel' => '',
        );
        return  $this->_oTemplate->parseHtmlByName ('manage', $aVars);
    }
 
    function _manageArchiveEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false)
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'archive_admin';

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
 
    function _addForm ($sRedirectUrl) {

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();                        
    
            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {
				
				$this->_oDb->cleanCache('sys_objects_actions');  

				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/all';
                header ('Location:' . $sRedirectUrl);
                exit;

            } else {

                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }
  
    function actionAdministrationCreate($sUrl='', $iEntryId=0) {

        $iEntryId = (int)$iEntryId;
  
		if($iEntryId) {
				
			 if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
				$this->_oTemplate->displayPageNotFound ();
				exit;
			 }
 		 
			ob_start();        
			$this->_editForm($aDataEntry, $iEntryId);
			$aVars = array (
				'content' => ob_get_clean(),
			); 
		}else{
  
			ob_start();        
			$this->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/all');
			$aVars = array (
				'content' => ob_get_clean(),
			);  
		}
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }

    function _editForm ($aDataEntry, $iEntryId) { 

        $iEntryId = (int)$iEntryId;
   
        bx_import ('FormEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormEdit';
        $oForm = new $sClass ($this, $iEntryId, $aDataEntry);
 
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
				
				$this->_oDb->cleanCache('sys_objects_actions');  
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/all' );
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
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('modzzz_manager_permalinks') == 'on'));
		 
        return $bEnabled;
    }
  
    function isAllowedAdd () {
        return $this->isAdmin();
    } 
   

}
