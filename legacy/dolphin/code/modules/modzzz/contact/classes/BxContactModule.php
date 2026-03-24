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

function modzzz_contact_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'contact') {
        $oMain = BxDolModule::getInstance('BxContactModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
 
 
/*
 * Contact module
 * 
 * 
 *
 */
class BxContactModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();

    function BxContactModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'filter';
        $this->_sPrefix = 'modzzz_contact';
        $this->_oDb->_sPrefix = 'modzzz_contact_';

        $this->sSearchResultClassName = 'BxContactSearchResult'; 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'answer';
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&'); 
 
        $GLOBALS['oBxContactModule'] = &$this; 
    }

    function actionHome () {
        parent::_actionHome(_t('_modzzz_contact_page_title_home'));
    }
   
    // ================================== admin actions
 
    function actionDelete ($iId) {
		$iId = (int)$iId;

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        } 
 
		$this->_oDb->deleteEntryById($iId);

		$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/active';
		header ('Location:' . $sRedirectUrl); 
	}

    function actionAdministration ($sUrl = '', $sParam1 = '', $sParam2 = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
  
        $this->_oTemplate->pageStart();

        $aMenu = array(  
 	 
            'inactive' => array(
                'title' => _t('_modzzz_contact_menu_admin_inactive_departments'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/inactive', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(false)),
            ),
            'active' => array(
                'title' => _t('_modzzz_contact_menu_active_departments'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/active',
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array(true)),
            ),  
            'create' => array(
                'title' => _t('_modzzz_contact_menu_admin_add_department'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreate', 'params' => array($sUrl, $sParam1)),
            ), 
            'manage_fields' => array(
                'title' => _t('_modzzz_contact_menu_admin_manage_fields'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage_fields',
                '_func' => array ('name' => 'actionAdministrationManageFields', 'params' => array(true)),
            ),            
            'add_field' => array(
                'title' => _t('_modzzz_contact_menu_admin_add_field'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/add_field/'.$sParam1,
                '_func' => array ('name' => 'actionAdministrationAddField', 'params' => array($sParam1)),
            ),/*  
			'settings' => array(
                'title' => _t('_modzzz_contact_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),*/		

        );

		if (empty($aMenu[$sUrl])){
            $sUrl = 'inactive';
		}
  
        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_contact_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin (array('admin.css', 'unit.css', 'twig.css', 'main.css', 'forms_extra.css', 'forms_adv.css')); 
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_contact_page_title_administration'));
    }

    function actionAdministrationManageFields ($isActiveEntries = false) {
        return $this->_actionAdministrationManageFields ($isActiveEntries, '_modzzz_contact_admin_delete', '_modzzz_contact_admin_activate');
    }

   function _actionAdministrationManageFields ($isActiveEntries, $sKeyBtnDelete, $sKeyBtnActivate, $sUrl = false)
    {
        if ($_POST['action_activate'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                $this->_oDb->activateEntry($iId);
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) { 
                $this->_oDb->deleteType($iId);
            }
        }

        if ($isActiveEntries) {
            $sContent = $this->_manageFieldEntries ('admin_fields', '', true, 'bx_twig_admin_form', array(
                'action_delete' => $sKeyBtnDelete,
            ), '', true, 0, $sUrl);
        } else {
            $sContent = $this->_manageFieldEntries ('pending', '', true, 'bx_twig_admin_form', array(
                'action_activate' => $sKeyBtnActivate,
                'action_delete' => $sKeyBtnDelete,
            ), '', true, 0, $sUrl);
        }

        return $sContent;
    }


   function _manageFieldEntries ($sMode, $sValue, $isFilter, $sFormName, $aButtons, $sAjaxPaginationBlockId = '', $isMsgBoxIfEmpty = true, $iPerPage = 0, $sUrlAdmin = false)
    {
        bx_import ('SearchResult', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'SearchResult';
        $o = new $sClass($sMode, $sValue);
        $o->sUnitTemplate = 'type_unit_admin';

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
 
    function actionAdministrationAddField ($sParam1='') {
  
        ob_start();
		
		if($sParam1)
			$this->_editFieldForm($sParam1);
		else
			$this->_addFieldForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage_fields');
          
		$aVars = array (
            'content' => ob_get_clean(),
        );
        return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }

    function _addFieldForm ($sRedirectUrl) {

        bx_import ('FormFieldAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormFieldAdd';
        $oForm = new $sClass ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();                        
 
            $iEntryId = $oForm->insert ($aValsAdd);

            if ($iEntryId) {
    
				$this->_oDb->addActions($iEntryId);

				$sLangKey = '_modzzz_contact_custom_type_'.$iEntryId;
				addStringToLanguage($sLangKey, $oForm->getCleanValue('title'));
 				compileLanguage();

				$aValsAdd = array ('title'=>$sLangKey);
				$oForm->update ($iEntryId, $aValsAdd);

				$this->_oDb->addTypeToMain($iEntryId, $oForm->getCleanValue('select_type'));

                header ('Location:' . $sRedirectUrl);
                exit;

            } else { 
                MsgBox(_t('_Error Occured'));
            }
                         
        } else { 
            echo $oForm->getCode (); 
        }
    }

    function _editFieldForm ($iEntryId) { 

        $iEntryId = (int)$iEntryId;
		$aDataEntry = $this->_oDb->getTypeData($iEntryId);
 
        bx_import ('FormFieldEdit', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormFieldEdit';
        $oForm = new $sClass ($this, $iEntryId);
  
        $oForm->initChecker($aDataEntry);

        if ($oForm->isSubmittedAndValid ()) {
 
            $aValsAdd = array ();
            if ($oForm->update ($iEntryId, $aValsAdd)) {
   
				$sLangKey = '_modzzz_contact_custom_type_'.$iEntryId;
				updateStringInLanguage($sLangKey, $oForm->getCleanValue('edit_title'));  
				compileLanguage();

				$aActions2Keep = array(); 
				if( is_array($_POST['prev_action']) && count($_POST['prev_action'])){ 
					foreach ($_POST['prev_action'] as $iActionId){
						$aActions2Keep[$iActionId] = $iActionId;
					}
				}

				$aActionIds = $this->_oDb->getActionIds($iEntryId);
			
				$aDeletedAction = array_diff ($aActionIds, $aActions2Keep);

				if ($aDeletedAction) {
					foreach ($aDeletedAction as $iActionId) {
						$this->_oDb->removeAction($iEntryId, $iActionId);
					}
				} 
				 
				$this->_oDb->addActions($iEntryId);
 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage_fields');
                exit;

            } else { 
                echo MsgBox(_t('_Error Occured')); 
            }            

        } else { 
            echo $oForm->getCode (); 
        }  
    }
   
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Contact');
    }
 
    function actionAdministrationManage ($isActiveEntries = false) {
        return $this->_actionAdministrationManage ($isActiveEntries, '_modzzz_contact_admin_delete', '_modzzz_contact_admin_activate');
    }

    function _actionAdministrationManage ($isActiveEntries, $sKeyBtnDelete, $sKeyBtnActivate, $sUrl = false)
    {
        if ($_POST['action_activate'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {
                if ($this->_oDb->activateEntry($iId)) {
                    //
                }
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iId) {

                $aDataEntry = $this->_oDb->getEntryById($iId);
                if (!$this->isAdmin())
                    continue;

                if ($this->_oDb->deleteEntryByIdAndOwner($iId, 0, $this->isAdmin())) {
                    //
                }
            }
        }

        if ($isActiveEntries) {
            $sContent = $this->_manageEntries ('active', '', true, 'bx_twig_admin_form', array(
                'action_delete' => $sKeyBtnDelete,
            ), '', true, 0, $sUrl);
        } else {
            $sContent = $this->_manageEntries ('inactive', '', true, 'bx_twig_admin_form', array(
                'action_activate' => $sKeyBtnActivate,
                'action_delete' => $sKeyBtnDelete,
            ), '', true, 0, $sUrl);
        }

        return $sContent;
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
 
				$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/active';
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
			$this->_addForm(BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/active');
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
	 
                header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/active' );
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
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('modzzz_contact_permalinks') == 'on'));
		 
        return $bEnabled;
    }
  
    function isAllowedAdd () {
        return $this->isAdmin();
    } 
 

}