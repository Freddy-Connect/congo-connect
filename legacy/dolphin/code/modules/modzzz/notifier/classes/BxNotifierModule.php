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

function modzzz_notifier_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'notifier') {
        $oMain = BxDolModule::getInstance('BxNotifierModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' ); 
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' ); 
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxDolAdminSettings'); 
bx_import('BxTemplSearchResult');  

/*
 * Notifier module
 *
 * This module allow users to create user's notifier, 
 * users can rate, comment and discuss notifier.
 * Notifier can have photos, videos, sounds and files, uploaded
 * by notifier's admins.
 *
 *  
 * 
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different notifier
 * @see BxNotifierModule::serviceHomepageBlock
 * BxDolService::call('notifier', 'homepage_block', array());
 *
 * Profile block with user's notifier
 * @see BxNotifierModule::serviceProfileBlock
 * BxDolService::call('notifier', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for notifier (for internal usage only)
 * @see BxNotifierModule::serviceGetMemberMenuItem
 * BxDolService::call('notifier', 'get_member_menu_item', array());
 *
 *
 

 *
 */
class BxNotifierModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_aQuickCache = array ();

    function BxNotifierModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_notifier_filter';
        $this->_sPrefix = 'modzzz_notifier';

        $GLOBALS['oBxNotifierModule'] = &$this; 
    }
 
    function actionHome ($sAction='') {
        $this->_oTemplate->pageStart();
        bx_import ('PageMain', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageMain';
        $oPage = new $sClass ($this);
 
		if(!$iLogged = getLoggedId()){
             $this->_oTemplate->displayAccessDenied ();
			return;
		}

		echo $oPage->getCode();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t('_modzzz_notifier_page_title_home'), false, false);
    }
	 
    

    // ================================== admin actions

    function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
 			'manage' => array(
                'title' => _t('_modzzz_notifier_menu_admin_manage_actions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array()),
            ),  
            'create' => array(
                'title' => _t('_modzzz_notifier_menu_admin_add_module'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create',
                '_func' => array ('name' => 'actionAdministrationCreate', 'params' => array($sUrl)),
            ), 
            'settings' => array(
                'title' => _t('_modzzz_notifier_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_notifier_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');    
        $this->_oTemplate->addCssAdmin ('settings.css');    
        //$this->_oTemplate->addJsAdmin ('notifier_templates.js');    
  
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_notifier_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('Notifier');
    }
  
    function actionAdministrationCreate($sUrl='') {
 		
		ob_start();        
		$this->_addForm();
		$aVars = array (
			'content' => ob_get_clean(),
		);  
        
		return $this->_oTemplate->parseHtmlByName('default_padding', $aVars);
    }

    function _addForm ($sRedirectUrl='') {
		global $db;

        bx_import ('FormAdd', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'FormAdd';
        $oForm = new $sClass ($this);
        $oForm->initChecker();

        if ($oForm->isSubmittedAndValid ()) {
			
			$sTable = $oForm->getCleanValue('table');

            $aValsAdd = array (
				'unit' => $oForm->getCleanValue('unit'),  
				'action' => $oForm->getCleanValue('action'),  
				'action_desc' => $oForm->getCleanValue('action_desc'), 
				'table' => $sTable, 
				'id_field' => $oForm->getCleanValue('id_field'), 
				'author_field' => $oForm->getCleanValue('author_field'), 
				'title_field' => $oForm->getCleanValue('title_field'), 
				'uri_field' => $oForm->getCleanValue('uri_field'), 
				'status_field' => $oForm->getCleanValue('status_field'), 
				'pending_value' => $oForm->getCleanValue('pending_value'), 
				'view_page_url' => $oForm->getCleanValue('view_page_url'), 
            );  
			
            $aChecker = array ( 
				$aValsAdd['id_field'] => '_modzzz_notifier_err_invalid_id_field', 
				$aValsAdd['author_field'] => '_modzzz_notifier_err_invalid_author_field', 
				$aValsAdd['title_field'] => '_modzzz_notifier_err_invalid_title_field',  
				$aValsAdd['uri_field'] => '_modzzz_notifier_err_invalid_uri_field',  
				$aValsAdd['status_field'] => '_modzzz_notifier_err_invalid_status_field',   
            );

			if($this->_oDb->isTableExists($sTable)){

				$fields = mysql_list_fields($db['db'], $sTable); 
				$columns = mysql_num_fields($fields);
				
				$field_array = array();
				for ($i = 0; $i < $columns; $i++) {
					$field_array[] = mysql_field_name($fields, $i);
				}

				$sErrorMsg = '';
				foreach($aChecker as $sField=>$sMessage){	   
					if (!in_array($sField, $field_array)) {
						 $sErrorMsg .= $this->_oTemplate->error_unit($sMessage);
					}
				}

				if($sErrorMsg){
 					echo $sErrorMsg . $oForm->getCode ();  
				}else{ 
					$this->_oDb->addNotifyAction($aValsAdd);
		  
					$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage/';
					header ('Location:' . $sRedirectUrl);
					exit;
				}
			}else{ 
				$sErrorMsg = $this->_oTemplate->error_unit('_modzzz_notifier_err_invalid_table_name');
				echo $sErrorMsg . $oForm->getCode ();  
			}
            
        } else { 
            echo $oForm->getCode (); 
        }
    }

    function actionAdministrationManage () {
	   
		if(isset($_POST['submit_form']) && !empty($_POST['submit_form'])){ 
			foreach($_POST['item'] as $iKey) {  
				$bStatus = (int)$_POST['active'][$iKey];
 
		 		$this->_oDb->updateModuleStatus($iKey, $bStatus);
			}  
		}
		
		$arrActions =  $this->_oDb->getNotifierActions();
 
        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ), 
		    'params' => array (
			   'db' => array( 
				   'submit_name' => 'submit_form',     
			   ),
		   ), 
		);
  
		$iter=1;
		$sOldGroup = "";
		foreach($arrActions as $aEachAction)
		{  

			if($aEachAction['unit']=='profile'){
				$sUnit='profile';
			}else{
				list($sPrefix, $sUnit) = explode('_', $aEachAction['unit']);
				$oModuleDb = new BxDolModuleDb();
				if(!$oModuleDb->isModule($sUnit)) continue;
			}
  
			$sNewGroup = _t($aEachAction['group']);

			if($sOldGroup != $sNewGroup) {
				
				if($sOldGroup != "") { 
					$aForm['inputs']["header{$iter}_end"] = array(
						'type' => 'block_end'
					);
				}

				$aForm['inputs']["header{$iter}"] = array(
					'type' => 'block_header',
					'caption' => "<b>{$sNewGroup}</b>",
					'collapsable' => true, 
					'collapsed' => ($iter==1) ? false : true,
				);
			}
			
			$iId = $aEachAction['id'];
			$sActionC = _t('_modzzz_notifier_add_a_item',_t($aEachAction['desc'])); 
			$sStatus = ($aEachAction['active']) ? "checked='checked'" : "";
  
			$aForm['inputs']["Item{$iter}"] = array(
				'type' => 'custom',
				'name' => "Item{$iter}",
				'content' =>  "<div style='width:100%'>
								<div style='float:left;width:90%'>{$sActionC}</div>  
								<div style='float:left;width:5%'><input type=checkbox name='active[$iId]' value='1' {$sStatus}></div>
								<input type=hidden name='item[$iId]' value='{$iId}'> 
							  </div>
							  <div class='clear_both'></div>",  
				'colspan' => true
			);
  
			$sOldGroup = $sNewGroup;
			$iter++;

		}//END

		if(count($arrActions)) {
			$aForm['inputs']["header{$iter}_end"] = array(
				'type' => 'block_end'
			);
	 
			$aForm['inputs']['Submit'] = array (
				'type' => 'submit',
				'name' => 'submit_form',
				'value' => _t('_Submit'),
				'colspan' => false,
			);  
		}else{
			 $aForm['inputs']["NoItem"] = array(
				'type' => 'custom',
				'name' => "NoItem",
				'content' =>  MsgBox(_t("_modzzz_notifier_no_actions")), 
				'colspan' => true
			);  
		}

		$oForm = new BxTemplFormView($aForm);
		$sContent .= '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
  
		$this->_oTemplate->addCss(array('unit.css', 'twig.css'));
		$sContent = $this->_oTemplate->parseHtmlByName('default_padding.html', array('content' => $sContent));

		return $sContent;
	}
 
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_notifier') == 'on'));
		 
        return $bEnabled;
    }


 }
