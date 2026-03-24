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

function modzzz_autofriend_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'autofriend') {
        $oMain = BxDolModule::getInstance('BxAutoFriendModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import('BxTemplSearchResult');
 
class BxAutoFriendModule extends BxDolTwigModule {
  
	var $_aQuickCache = array ();

    function BxAutoFriendModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_autofriend_filter';
        $this->_sPrefix = 'modzzz_autofriend';
  
        $GLOBALS['oBxAutoFriendModule'] = &$this;   
    }

	function actionFriend($iNewMemID){
		$this->serviceAutoFriend($iNewMemID); 
	}

	//select all admin/moderators to auto add new members as friends
	function serviceAutoFriend($iNewMemID){
		$aAdmins = $this->_oDb->getPreferenceAdmins(); 
		if(in_array('all', $aAdmins)){ 
			$aAdmins = $this->_oDb->getAllAdmins();  
		}
 
		$this->_oDb->addAutoFriend($iNewMemID, $aAdmins); 
	}
	   
   function actionAdministration ($sUrl = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();

        $aMenu = array(
 			'manage' => array(
                'title' => _t('_modzzz_autofriend_menu_admin_manage_actions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array()),
            ),  
            'preference' => array(
                'title' => _t('_modzzz_autofriend_menu_admin_preference'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/preference',
                '_func' => array ('name' => 'actionAdministrationPreference', 'params' => array()),
            ),
			/*
            'init' => array(
                'title' => _t('_modzzz_autofriend_menu_admin_initialize'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/init',
                '_func' => array ('name' => 'actionAdministrationInit', 'params' => array()),
            ),
			*/
            'settings' => array(
                'title' => _t('_modzzz_autofriend_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_autofriend_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('twig.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');    
        $this->_oTemplate->addCssAdmin ('settings.css');    
   
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_autofriend_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('AutoFriend');
    }
 
    function actionAdministrationPreference () {
  
		$aDefPreference = $this->_oDb->getPreferenceAdmins();
 
		$aAdministrators = $this->_oDb->getAdministrators();
		$aAdministrators['all'] = _t('_modzzz_autofriend_all_administrators');
		asort($aAdministrators);

        $aForm = array(
            'form_attrs' => array(
                'action' => '',
                'method' => 'post',
            ), 
		    'params' => array (
			   'db' => array( 
				   'submit_name' => 'submit_form',      
                    'table' => 'modzzz_friend_preference',
                    'key' => 'id',    
			   ),
		   ), 
 
		);
   
        $aForm['inputs']["id"]  = array ( 
			'type' => 'hidden',
			'name' => 'id',
			'value' => 1,  
        );
	   
	    $aForm['inputs']["initialized"]  = array ( 
			'type' => 'hidden',
			'name' => 'initialized',
			'value' => 1,  
			'db' => array (
				'pass' => 'Int', 
			), 
        );   
	    
        $aForm['inputs']["header_info"] = array(
			'type' => 'block_header',
			'caption' => _t('_modzzz_autofriend_preference_message')
		);                
 
		$aForm['inputs']["admins"] = array(
			'type' => 'select_box',
			'name' => "admins",
			'caption' => _t('_modzzz_autofriend_administrators'),
			'values' => $aAdministrators, 
			'value' => $aDefPreference,
			'attrs' => array(
				'add_other' => false,
			),
			'db' => array (
				'pass' => 'Categories', 
			), 
		);
 
		$aForm['inputs']['Submit'] = array (
			'type' => 'submit',
			'name' => 'submit_form',
			'value' => _t('_Submit'),
			'colspan' => false,
		);  

		$oForm = new BxTemplFormView($aForm);
  
		$oForm->initChecker();  
 
        if ($oForm->isSubmittedAndValid ()) { 
		 
			$oForm->update (1); 
  
			$sCode .= MsgBox(_t('_modzzz_autofriend_preference_success'));
			//header ('Location:' . BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/preference');
			//exit;
		}
 
		$sCode .= '<div class="bx-def-bc-padding">' . $oForm->getCode() . '</div>';  
		return $sCode; 
	}
  
    function actionAdministrationManage () {
		$sMessage = "";
		$iSearchAdmin = 0;
		$sSearchNick = "";

		// check actions
		if(is_array($_POST))
		{
			if(isset($_POST['action_search']) && !empty($_POST['action_search'])) { 
			 
				$sSearchParam = process_db_input($_POST['search_nickname']);
 				 
				if($sSearchParam) {  
					$iId = (double)$sSearchParam ? $sSearchParam :  getID($sSearchParam) ;
				} 

				if($sSearchParam && $iId) {
					$iSearchAdmin = $this->_oDb->isAdmin($iId);
					$sSearchNick = getNickName($iId);  
				}else{
					$sMessage = MsgBox(_t("_modzzz_autofriend_member_not_found"));	
				} 
			} 

			if(isset($_POST['action_save']) && !empty($_POST['action_save'])) { 
			 
				$sName = process_db_input($_POST['nickname']);
 				$isAdmin = process_db_input($_POST['admin']);
				$iId = $this->_oDb->getID($sName);
				 
				if($iId) {  
					$this->_oDb->makeAdmin($iId, $isAdmin); 
					$iSearchAdmin = $this->_oDb->isAdmin($iId);
					$sSearchNick = getNickName($iId);  
					$sMessage = MsgBox(_t("_modzzz_autofriend_member_status_updated"));  
				}else{
					$sMessage = MsgBox(_t("_modzzz_autofriend_member_not_found"));	
				} 
			} 
 
		}
  
		$sFormName = 'alloc_searchform';
   
		$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
			'action_search' => _t('_modzzz_autofriend_btn_search')
		), 'pathes', false);	 
	   
		$aVars = array(
			'message' => $sMessage,  
  			'form_name' => $sFormName, 
			'controls' => $sControls
		);

		$sContent = $this->_oTemplate->parseHtmlByName('admin_allocate_search',$aVars);
	  

		/******************/

		$sMessage = ""; 
		$sFormName = 'alloc_form';
   
		$sControls = BxTemplSearchResult::showAdminActionsPanel($sFormName, array(
			'action_save' => _t('_modzzz_autofriend_btn_submit')
		), 'pathes', false);	 
	   
		$aVars = array(
			'message' => $sMessage, 
			'nick_value' => $sSearchNick, 
  			'form_name' => $sFormName, 
			'controls' => $sControls,
			'admin_checked' => $this->_oDb->isAdmin($iId) ? "checked='checked'" : '', 
			'bx_if:search' => array( 
				'condition' =>  $sSearchNick,
				'content' => array(
					'present_admin_value' => ($iSearchAdmin) ? _t('_modzzz_autofriend_member_is_admin') : _t('_modzzz_autofriend_member_not_admin'),
				) 
			), 
		);

		$sContent .= $this->_oTemplate->parseHtmlByName('admin_allocate',$aVars);
 
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sContent));    
 	}
 
    function serviceResponseProfileChange ($oAlert) {
 
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;
    
        if ($oAlert->aExtras['status'] != 'Active')
            return false;
  
		if($iProfileId && $this->isAllowedAutoFriend($iProfileId) && (getParam("auto_friend_activated")=="on")){
			$this->serviceAutoFriend($iProfileId); 
		}  
 
        return true;
    }


    function serviceResponseProfileJoin ($oAlert) {
 
        if (!($iProfileId = (int)$oAlert->iObject))
            return false;
    
		if($iProfileId && $this->isAllowedAutoFriend($iProfileId) && (getParam("auto_friend_activated")=="on")){
			$this->serviceAutoFriend($iProfileId); 
		}  
 
        return true;
    }
    
    function _defineActions () {
        defineMembershipActions(array('autofriend add friend'));
    }

    function isAllowedAutoFriend ($iProfileId, $isPerformAction = false) {
 
        // check admin acl
        $this->_defineActions();
		$aCheck = checkAction($iProfileId, BX_AUTOFRIEND_ADD_FRIEND, $isPerformAction);
        return ($aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED);
    }

    function serviceResponseMembershipChange ($oAlert)
    {
        if (!($iProfileId = (int)$oAlert->iSender))
            return false;

		if ($this->isAllowedAutoFriend($iProfileId))
            return false;
 
		$this->_oDb->updateMembershipLevel ($iProfileId);
	 
        return true;
    }
 


  
}