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

function modzzz_memlog_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'memlog') {
        $oMain = BxDolModule::getInstance('BxMemLogModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a) ;
}

bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolTwigModule');
bx_import ('BxDolProfileFields');
  
/*
 * MemLog module 
 *
 */
class BxMemLogModule extends BxDolTwigModule {
 
    var $_aQuickCache = array ();

    function BxMemLogModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'memlog_filter';
        $this->_sPrefix = 'modzzz_memlog';
 
	    $this->_oConfig->init($this->_oDb);

        $GLOBALS['oBxMemLogModule'] = &$this; 
    }

	function isModerator($iProfileId, $iModeratorId){
		 $iProfileId = (int)$iProfileId;
		 $iModeratorId = (int)$iModeratorId;

		 if($iProfileId==0 || $iModeratorId==0) return false;

		 return $this->_oDb->isModerator($iProfileId, $iModeratorId);   
	}

	function actionMakeModerator ($iModeratedId, $iModeratorId) {
        $iModeratedId = (int)$iModeratedId;  
        $iModeratorId = (int)$iModeratorId; 
		$sRedirect = getProfileLink($iModeratorId);
  
		if (0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div');
			exit;
		}
 
 		if ($this->_oDb->isModerator($iModeratedId, $iModeratorId)) {
			echo MsgBox(_t('_modzzz_memlog_msg_already_moderator', getNickName($iModeratorId))) . genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div');
			exit;
		}
 
		if ($this->_oDb->addModerator($iModeratedId, $iModeratorId)) {
  
			$this->isAllowedAddModerator($iModeratedId, $iModeratorId, true);

			$sJQueryJS = genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div', $sRedirect);
			echo MsgBox(_t('_modzzz_memlog_msg_add_moderator_success')) . $sJQueryJS;
			exit;
		}        
 
		echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div');
		exit;  
    }

	function actionRemoveModerator ($iModeratedId, $iModeratorId) {
        $iModeratedId = (int)$iModeratedId;  
        $iModeratorId = (int)$iModeratorId; 
		$sRedirect = getProfileLink($iModeratorId);
  
		if (0 != strcasecmp($_SERVER['REQUEST_METHOD'], 'POST')) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div');
			exit;
		}
 
 		if (!$this->_oDb->isModerator($iModeratedId, $iModeratorId)) {
			echo MsgBox(_t('_Access denied')) . genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div');
			exit;
		}
 
		if ($this->_oDb->removeModerator($iModeratedId, $iModeratorId)) {
  
			$this->_defineActions();

			$this->_oDb->updateMembershipAction(BX_MEMLOG_ADD, $iModeratedId);

			$sJQueryJS = genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div', $sRedirect);
			echo MsgBox(_t('_modzzz_memlog_msg_remove_moderator_success')) . $sJQueryJS;
			exit;
		}        
 
		echo MsgBox(_t('_Error Occured')) . genAjaxyPopupJS($iModeratorId, 'ajaxy_popup_result_div');
		exit;  
    }
 
	function bx_login_moderator($iId, $bRememberMe = false)
	{ 
		$aUrl = parse_url($GLOBALS['site']['url']);
		$sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
		$sHost = '';
		$iCookieTime = $bRememberMe ? time() + 24*60*60*30 : 0;
		setcookie("moderatorID", $iId, $iCookieTime, $sPath, $sHost);
		$_COOKIE['moderatorID'] = $iId;

		$this->_oDb->insertBottomMenu($iId);
	}

	function bx_logout_moderator() {
		
		if(!$_COOKIE['moderatorID']) return;

		$aUrl = parse_url($GLOBALS['site']['url']);
		$sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';

		setcookie('moderatorID', '', time() - 96 * 3600, $sPath);
 
		unset($_COOKIE['moderatorID']);

		$this->_oDb->clearBottomMenu();
	}
 
  	function serviceGetReturnCaption() {
		$iModeratorId = (int)$_COOKIE['moderatorID'];
  
		if (getParam('modzzz_memlog_moderators_add') != 'on' && !isAdmin($iModeratorId)) return '';
 
		if ($iModeratorId && (isAdmin($iModeratorId) || $this->isModerator($this->_iProfileId, $iModeratorId))){  
			return _t('_modzzz_memlog_return_msg', getNickName($iModeratorId));
		}else{
			return '';
		}
	}

    function actionMember($iProfileId=0) {
		global $_page;
		global $_page_cont;

		if ($iProfileId && (isAdmin() || $this->isModerator($iProfileId, $this->_iProfileId))) {
 
			$this->bx_login_moderator($this->_iProfileId);

			$sProfileLink = getProfileLink($iProfileId);
			bx_login ((int)$iProfileId); // autologin here
			header( "Location:" . getProfileLink($iProfileId));
			exit; 
		} else { 
			$_page['name_index'] = 0;
			$_page['header'] = $_GLOBALS['site']['title'];
			$_page['header_text'] = $_GLOBALS['site']['title'];
			$_page_cont[0]['page_main_code'] = MsgBox(_t('_Access denied'));
			PageCode();
		} 
	}

    function actionReturn() {
		global $_page;
		global $_page_cont;

		$iModeratorId = (int)$_COOKIE['moderatorID'];
 
		if ($iModeratorId && (isAdmin($iModeratorId) || $this->isModerator($this->_iProfileId, $iModeratorId)) ) {
		 
			$this->bx_logout_moderator();

			bx_login ((int)$iModeratorId); // autologin here
			
			if(isAdmin($iModeratorId))
				header( "Location:" . BX_DOL_URL_ROOT . "administration/");
			else
				header( "Location:" . BX_DOL_URL_ROOT . "member.php");
			exit;
		} else {
			$_page['name_index'] = 0;
			$_page['header'] = $_GLOBALS['site']['title'];
			$_page['header_text'] = $_GLOBALS['site']['title'];
			$_page_cont[0]['page_main_code'] = MsgBox(_t('_Access denied'));
			PageCode();
		}  
    }
  
    function actionHome () {
        parent::_actionHome(_t('_modzzz_memlog_page_title_home'));
    }
  
	function serviceModeratorsBlock () {
 
        bx_import ('PageMain', $this->_aModule);
        $o = new BxMemLogPageMain ($this);        
        $o->sUrlStart = BX_DOL_URL_ROOT . 'member.php?';
        
        $this->_oTemplate->addCss (array('unit.css', 'twig.css'));
 
 		$sBrowseMode = 'moderators';
        switch ($_GET['memlog_filter']) {            
            case 'moderators':
            case 'moderating':   
                $sBrowseMode = $_GET['memlog_filter'];
                break;
        }
 
		$aMenu = array(
			 _t('_modzzz_memlog_page_tab_moderating') => array('href' => BX_DOL_URL_ROOT . 'member.php?memlog_filter=moderating', 'active' => 'moderating' == $sBrowseMode, 'dynamic' => true),  
			_t('_modzzz_memlog_page_tab_moderators') => array('href' => BX_DOL_URL_ROOT . 'member.php?memlog_filter=moderators', 'active' => 'moderators' == $sBrowseMode, 'dynamic' => true) 
		);
			 
        return $o->ajaxBrowse(
            $sBrowseMode, 
            5, 
            $aMenu,
            $this->_iProfileId 
        ); 
    }
 
  
    // ================================== admin actions

    function actionAdministration ($sUrl = '', $sParam1='') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        

        $this->_oTemplate->pageStart();
 
        $aMenu = array(  
            'settings' => array(
                'title' => _t('_modzzz_memlog_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';
 
        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
  
        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_memlog_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('twig.css'); 
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');        
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_memlog_page_title_administration'));
    }
 
    function actionAdministrationSettings () {
        return parent::_actionAdministrationSettings ('MemLog');
    }
   
    // ================================== events
 

 
    // ================================== permissions
  
	function isPermalinkEnabled() {
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_memlog') == 'on'));
		 
        return $bEnabled;
    }
     
    function  broadCastToInbox($aProfile, &$aTemplate, &$aTemplateVars){
		global $tmpl;

		require_once( BX_DIRECTORY_PATH_ROOT . 'templates/tmpl_' . $tmpl . '/scripts/BxTemplMailBox.php');

		$aMailBoxSettings = array
		(
			'member_id'	     => $this->_oDb->getAdmin(), 
			'recipient_id'	 => $aProfile['ID'], 
			'messages_types' => 'mail',  
		);

		$aComposeSettings = array
		(
			'send_copy' => false , 
			'send_copy_to_me' => false , 
			'notification' => false ,
		);
		$oMailBox = new BxTemplMailBox('mail_page', $aMailBoxSettings);
  
		$sMessageSubject = $aTemplate['Subject'];
		$sMessageSubject = str_replace("<NickName>", getNickName($aProfile['ID']), $sMessageSubject);
		$sMessageSubject = str_replace("<SiteName>", getParam('site_title'), $sMessageSubject);

		$sMessageBody = $aTemplate['Body'];
		$sMessageBody = str_replace("<SiteName>", getParam('site_title'), $sMessageBody);
		$sMessageBody = str_replace("<NickName>", $aTemplateVars['NickName'], $sMessageBody);
		$sMessageBody = str_replace("<MemberName>", $aTemplateVars['MemberName'], $sMessageBody);
		$sMessageBody = str_replace("<MemberUrl>", $aTemplateVars['MemberUrl'], $sMessageBody);
 
		$sMessageBody = str_replace("<bx_include_auto:_email_header.html />", '', $sMessageBody);
		$sMessageBody = str_replace("<bx_include_auto:_email_footer.html />", '', $sMessageBody);

		$oMailBox -> sendMessage($sMessageSubject, $sMessageBody, $aProfile['ID'], $aComposeSettings);  
    }
  
    function isAllowedLoginAsMember ($iProfileId, $iModeratorId) {
 		 $iProfileId = (int)$iProfileId;
		 $iModeratorId = (int)$iModeratorId;

		 if($iProfileId==0 || $iModeratorId==0) return false;


		if (!$this->_oDb->isModerator($iProfileId, $iModeratorId))
			return false;
 
		if (getParam('modzzz_memlog_moderators_add') != 'on') return false;

		return true; 
	}
 
    function isAllowedAddModerator ($iModeratedId, $iModeratorId, $isPerformAction=false) {
 		 $iModeratedId = (int)$iModeratedId;
		 $iModeratorId = (int)$iModeratorId;

		 if($iModeratedId==0 || $iModeratorId==0) return false;

		 if(!$isPerformAction){
			 if ($this->_oDb->isModerator($iModeratedId, $iModeratorId))
				return false;
		 }

         if ($this->isAdmin()) 
            return true;

		 if (getParam('modzzz_memlog_moderators_add') != 'on') return false;

         $this->_defineActions();
         $aCheck = checkAction($iModeratedId, BX_MEMLOG_ADD, $isPerformAction);
         return($aCheck[CHECK_ACTION_RESULT] == CHECK_ACTION_RESULT_ALLOWED); 
    }
  
    function _defineActions () {
        defineMembershipActions(array('memlog add'));
    }
 
    function serviceGetWallPostComment($aEvent)
    { 
        return;
    }
  
    function serviceGetWallPostOutline($aEvent)
    {  
        return;
    }
 
    function serviceGetWallPost ($aEvent) {
        return;
    }
  
    function serviceDeleteProfileData ($iProfileId) {

        $iProfileId = (int)$iProfileId;

        if (!$iProfileId)
            return false;
 
       $this->_oDb->removeProfileEntries($iProfileId); 
    }

    function serviceResponseProfileDelete ($oAlert) {

        if (!($iProfileId = (int)$oAlert->iObject))
            return false;

        $this->serviceDeleteProfileData ($iProfileId);
        
        return true;
    }
 
    function serviceResponseProfileLogout ($oAlert) {

        if (!($iProfileId = (int)$oAlert->iObject))
            return false;

		$this->bx_logout_moderator();
 
        return true;
    }
 
    function actionAccept ($iEntryId) {
  
        header('Content-type:text/html;charset=utf-8');

        $iEntryId = (int)$iEntryId;

        if (!($aDataEntry = $this->_oDb->getEntryById($iEntryId))) {
            echo MsgBox(_t('_sys_request_page_not_found_cpt')) . genAjaxyPopupJS($this->_iProfileId, 'ajaxy_popup_memlog_div');
            exit;
        }
  
 		$iProfileId = ($this->_iProfileId==$aDataEntry['member_id']) ? $aDataEntry['moderator_id'] : $aDataEntry['member_id'];
 
		if($this->_oDb->acceptRequest($iEntryId)){
   
			$oEmailTemplate = new BxDolEmailTemplates();
			if ($oEmailTemplate) {
				$aProfile = getProfileInfo($iProfileId);
				$aTemplateVars = array (
					'NickName' => getNickName($iProfileId),
					'MemberName' => getNickName($this->_iProfileId),
					'MemberUrl' => getProfileLink($this->_iProfileId),
				 );
			
				$aTemplate = $oEmailTemplate->getTemplate('modzzz_memlog_accept'); 
				sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars); 

				$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  

				if( $this->hasMetCount($this->_iProfileId) >= (int)getParam('modzzz_memlog_verify_threshold') && !$this->isVerified($this->_iProfileId) ){
					
					$aProfile = getProfileInfo($this->_iProfileId);
	
					$bVerified = $this->_oDb->changeVerifyStatus($this->_iProfileId, 1);
 
					$aTemplate = $oEmailTemplate->getTemplate('modzzz_memlog_verify'); 
					sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars); 

					$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  
				} 
				
				if( $this->hasMetCount($iProfileId) >= (int)getParam('modzzz_memlog_verify_threshold') && !$this->isVerified($iProfileId) ){
					
					$aProfile = getProfileInfo($iProfileId);
 
					$bVerified = $this->_oDb->changeVerifyStatus($iProfileId, 1);
 
					$aTemplate = $oEmailTemplate->getTemplate('modzzz_memlog_verify'); 
					sendMail($aProfile['Email'], $aTemplate['Subject'], $aTemplate['Body'], $aProfile['ID'], $aTemplateVars); 

					$this->broadCastToInbox($aProfile, $aTemplate, $aTemplateVars);  
				}  
			}
 
			$sRedirect = BX_DOL_URL_ROOT . 'member.php';
			$sJQueryJS = genAjaxyPopupJS($this->_iProfileId, 'ajaxy_popup_memlog_div', $sRedirect);
 
			$this->onEventAcceptRequest ($iProfileId, $iEntryId);
			echo MsgBox(_t('_modzzz_memlog_msg_request_accepted')) . $sJQueryJS; 
			exit;
	    }

		echo MsgBox(_t('_modzzz_memlog_msg_request_err_accepted')) . $sJQueryJS; 
		exit; 
    }
 
	 





}
