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

function modzzz_notify_import ($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'notify') {
        $oMain = BxDolModule::getInstance('BxNotifyModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a);
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
 * Notify module
 *
 * This module allow users to create user's notify, 
 * users can rate, comment and discuss notify.
 * Notify can have photos, videos, sounds and files, uploaded
 * by notify's admins.
 *
 *  
 * 
 *
 * 
 *
 * Service methods:
 *
 * Homepage block with different notify
 * @see BxNotifyModule::serviceHomepageBlock
 * BxDolService::call('notify', 'homepage_block', array());
 *
 * Profile block with user's notify
 * @see BxNotifyModule::serviceProfileBlock
 * BxDolService::call('notify', 'profile_block', array($iProfileId));
 *

 *
 * Member menu item for notify (for internal usage only)
 * @see BxNotifyModule::serviceGetMemberMenuItem
 * BxDolService::call('notify', 'get_member_menu_item', array());
 *
 *
 

 *
 */
class BxNotifyModule extends BxDolTwigModule {

    var $_oPrivacy;
    var $_aQuickCache = array ();

    function BxNotifyModule(&$aModule) {

        parent::BxDolTwigModule($aModule);        
        $this->_sFilterName = 'modzzz_notify_filter';
        $this->_sPrefix = 'modzzz_notify';

        bx_import ('Privacy', $aModule);
        $this->_oPrivacy = new BxNotifyPrivacy($this);

        $GLOBALS['oBxNotifyModule'] = &$this; 
    }
 
    function actionSettings ($sAction='') {
		$this->saveSettings();
		$this->actionHome();
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
/*
		if($sAction=='settings'){
			$this->saveSettings();
		}
*/
		echo $oPage->getCode();

        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t('_modzzz_notify_page_title_home'), false, false);
    }
	 
    function saveSettings () {
		$sSetting = process_db_input($_POST['setting']);
		
		if($sSetting && $this->_iProfileId){
			$this->_oDb->updateNotificationSettings($this->_iProfileId, $sSetting);
		}
	}

    function actionMy () {
  
        $this->_oTemplate->pageStart();
        bx_import ('PageMy', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageMy';
        $oPage = new $sClass ($this);

		if(!$iLogged = getLoggedId()){
             $this->_oTemplate->displayAccessDenied ();
			return;
		}

        echo $oPage->getCode();
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->addCss ('unit.css');
        $this->_oTemplate->pageCode(_t('_modzzz_notify_page_title_my'), false, false);
    }
  
    function actionView ($sUri) {
        parent::_actionView ($sUri, _t('_modzzz_notify_msg_pending_approval'));
    }
 
    function actionSearch ($sKeyword = '', $sCategory = '') {
        parent::_actionSearch ($sKeyword, $sCategory, _t('_modzzz_notify_page_title_search'));
    }
    
    // ================================== external actions
 
	function getVoteUnit ($sTable) { 
		return $this->_oDb->getVoteUnit($sTable);  
	}
   
    function serviceGetMemberMenuItem () {
        parent::_serviceGetMemberMenuItem (_t('_modzzz_notify'), _t('_modzzz_notify'), 'notify.png');
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
                'title' => _t('_modzzz_notify_menu_admin_manage_actions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/manage', 
                '_func' => array ('name' => 'actionAdministrationManage', 'params' => array()),
            ),  
 			'templates' => array(
                'title' => _t('_modzzz_notify_menu_admin_manage_templates'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/templates', 
                '_func' => array ('name' => 'actionAdministrationTemplates', 'params' => array()),
            ), 
			/*
            'reset' => array(
                'title' => _t('_modzzz_notify_menu_admin_initialize'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/reset',
                '_func' => array ('name' => 'actionAdministrationReset', 'params' => array()),
            ),
			*/
            'settings' => array(
                'title' => _t('_modzzz_notify_menu_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'actionAdministrationSettings', 'params' => array()),
            ),
        );

        if (empty($aMenu[$sUrl]))
            $sUrl = 'settings';

        $aMenu[$sUrl]['active'] = 1;
        $sContent = call_user_func_array (array($this, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);

        echo $this->_oTemplate->adminBlock ($sContent, _t('_modzzz_notify_page_title_administration'), $aMenu);
        $this->_oTemplate->addCssAdmin ('admin.css');
        $this->_oTemplate->addCssAdmin ('unit.css');
        $this->_oTemplate->addCssAdmin ('main.css');
        $this->_oTemplate->addCssAdmin ('forms_extra.css'); 
        $this->_oTemplate->addCssAdmin ('forms_adv.css');    
        $this->_oTemplate->addCssAdmin ('settings.css');    
        //$this->_oTemplate->addJsAdmin ('notify_templates.js');    
  
        $this->_oTemplate->pageCodeAdmin (_t('_modzzz_notify_page_title_administration'));
    }

    function actionAdministrationSettings () {
        return $this->_actionAdministrationSettings ('Notify');
    }

    function _actionAdministrationSettings ($sSettingsCatName)
    {
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $sSettingsCatName))
            return MsgBox(_t('_sys_request_page_not_found_cpt'));

        $iId = $this->_oDb->getSettingsCategory($sSettingsCatName);
        if(empty($iId))
           return MsgBox(_t('_sys_request_page_not_found_cpt'));

        bx_import('BxDolAdminSettings');

        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings->saveChanges($_POST);

			if($_POST['modzzz_notify_override']=='yes')
				$this->_oDb->overrideMemberSettings();
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
  
    function actionAdministrationReset () {
	 
		if(isset($_POST['submit_form']) && !empty($_POST['submit_form']))
		{    
			$this->_oDb->InitMemberNotifier(0, true);
 		}  
 
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
 	   
        $aForm['inputs']["header_info"] = array(
			'type' => 'block_header',
			'caption' => _t('_modzzz_notify_reset_message')
		);                
  
		if(isset($_POST['submit_form']) && !empty($_POST['submit_form']))
		{  
			 $aForm['inputs']["Item1"] = array(
				'type' => 'custom',
				'name' => "Item1",
				'content' =>  MsgBox(_t("_modzzz_notify_reset_success")), 
				'colspan' => true
			); 
		}

		$aForm['inputs']['Submit'] = array (
			'type' => 'submit',
			'name' => 'submit_form',
			'value' => _t('_Submit'),
			'colspan' => false,
		);  

		$oForm = new BxTemplFormView($aForm);
		$sCode .= '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
 
		return $sCode;
	}
  
	function actionAdministrationTemplates($mixedResult='') {
	 
		$oSettings = new BxDolAdminSettings(24); 
	  
		$sPageUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/templates';

		//--- Process submit ---//
		$mixedResultSettings = '';
		$mixedResultTemplates = '';
		if(isset($_POST['save']) && isset($_POST['cat'])) {
			$mixedResultSettings = $oSettings->saveChanges($_POST);
		} elseif(isset($_POST['action']) && $_POST['action'] == 'get_translations') {
			$aTranslation = $GLOBALS['MySQL']->getRow("SELECT `Subject` AS `subject`, `Body` AS `body` FROM `sys_email_templates` WHERE `Name`='" . process_db_input($_POST['templ_name']) . "' AND `LangID`='" . (int)$_POST['lang_id'] . "' LIMIT 1");
			if(empty($aTranslation))
				$aTranslation = $GLOBALS['MySQL']->getRow("SELECT `Subject` AS `subject`, `Body` AS `body` FROM `sys_email_templates` WHERE `Name`='" . process_db_input($_POST['templ_name']) . "' AND `LangID`='0' LIMIT 1");
				
			$oJson = new Services_JSON();   
			echo $oJson->encode(array('subject' => $aTranslation['subject'], 'body' => $aTranslation['body']));
			exit;
		}
	 
		$aForm = array(
			'form_attrs' => array(
				'id' => 'adm-email-templates',
				'action' => '',
				'method' => 'post',
				'enctype' => 'multipart/form-data',
			),
			'params' => array (
					'db' => array(
						'table' => 'sys_email_templates',
						'key' => 'ID',
						'uri' => '',
						'uri_title' => '',
						'submit_name' => 'adm-emial-templates-save'
					),
				),
			'inputs' => array ()
		);

		$aLanguages = $GLOBALS['MySQL']->getAll("SELECT `ID` AS `id`, `Title` AS `title` FROM `sys_localization_languages`");
		
		$aLanguageChooser = array(array('key' => 0, 'value' => 'default'));
		foreach($aLanguages as $aLanguage)
			$aLanguageChooser[] = array('key' => $aLanguage['id'], 'value' => $aLanguage['title']);
		
		$sLanguageCpt = _t('_adm_txt_email_language');
		$sSubjectCpt = _t('_adm_txt_email_subject');
		$sBodyCpt = _t('_adm_txt_email_body');

		$aEmails = $GLOBALS['MySQL']->getAll("SELECT DISTINCT tmpl.`ID` AS `id`, tmpl.`Name` AS `name`, tmpl.`Subject` AS `subject`, tmpl.`Body` AS `body`, tmpl.`Desc` AS `description` FROM `sys_email_templates` tmpl, `".$this->_oDb->_sPrefix . "main` mn WHERE mn.`template` =tmpl.`Name`  AND `LangID`='0' ORDER BY `ID`");
		foreach($aEmails as $aEmail) {
			$aForm['inputs'] = array_merge($aForm['inputs'], array(
				$aEmail['name'] . '_Beg' => array(
					'type' => 'block_header',
					'caption' => $aEmail['description'],
					'collapsable' => true,
					'collapsed' => true
				),
				$aEmail['name'] . '_Language' => array(
					'type' => 'select',
					'name' => $aEmail['name'] . '_Language',
					'caption' => $sLanguageCpt,
					'value' =>  0,
					'values' => $aLanguageChooser,
					'db' => array (
						'pass' => 'Int',
					),
					'attrs' => array(
						'onchange' => "javascript:getNotifyTranslations(this, '{$sPageUrl}')"
					)
				),
				$aEmail['name'] . '_Subject' => array(
					'type' => 'text',
					'name' => $aEmail['name'] . '_Subject',
					'caption' => $sSubjectCpt,
					'value' => $aEmail['subject'],
					'db' => array (
						'pass' => 'Xss',
					),
				),
				$aEmail['name'] . '_Body' => array(
					'type' => 'textarea',
					'name' => $aEmail['name'] . '_Body',
					'caption' => $sBodyCpt,
					'value' => $aEmail['body'],
					'db' => array (
						'pass' => 'XssHtml',
					),
				),
				$aEmail['name'] . '_End' => array(
					'type' => 'block_end'
				)
			));
		}
		
		$aForm['inputs']['adm-emial-templates-save'] = array(
			'type' => 'submit',
			'name' => 'adm-emial-templates-save',
			'value' => _t('_adm_btn_email_save'),
		);

		$oForm = new BxTemplFormView($aForm);
		$oForm->initChecker();

		$sResult = "";
		if($oForm->isSubmittedAndValid()) {
			$iResult = 0;
			foreach($aEmails as $aEmail) {
				$iEmailId = (int)$GLOBALS['MySQL']->getOne("SELECT `ID` FROM `sys_email_templates` WHERE `Name`='" . process_db_input($aEmail['name']) . "' AND `LangID`='" . (int)$_POST[$aEmail['name'] . '_Language'] . "' LIMIT 1");
				if($iEmailId != 0)
					$iResult += (int)$GLOBALS['MySQL']->query("UPDATE `sys_email_templates` SET `Subject`='" . process_db_input($_POST[$aEmail['name'] . '_Subject']) . "', `Body`='" . process_db_input($_POST[$aEmail['name'] . '_Body']) . "' WHERE `ID`='" . $iEmailId . "'");
				else
					$iResult += (int)$GLOBALS['MySQL']->query("INSERT INTO `sys_email_templates` SET `Name`='" . process_db_input($aEmail['name']) . "', `Subject`='" . process_db_input($_POST[$aEmail['name'] . '_Subject']) . "', `Body`='" . process_db_input($_POST[$aEmail['name'] . '_Body']) . "', `LangID`='" . (int)$_POST[$aEmail['name'] . '_Language'] . "'");
			}
			
			$sResult .= MsgBox(_t($iResult > 0 ? "_adm_txt_email_success_save" : "_adm_txt_email_nothing_changed"), 3);
		}
		$sResult .= $oForm->getCode();

		return DesignBoxAdmin(_t('_adm_box_cpt_email_templates'), $GLOBALS['oAdmTemplate']->parseHtmlByName('email_templates_list.html', array(
			'content' => stripslashes($sResult),
			'loading' => LoadingBox('adm-email-loading')
		)));
	}

    function actionAdministrationTemplatesOLD () {
		if(isset($_POST['submit_form']) && !empty($_POST['submit_form'])){ 
			foreach($_POST['item'] as $iKey) {  
				$sSubject = $_POST['subject'][$iKey];
 				$sMessage = $_POST['message'][$iKey];

		 		//$this->_oDb->query("UPDATE `" . $this->_oDb->_sPrefix . "main` SET  `active`='$bStatus' WHERE `id`=$iKey");
			}  
		}
		
		$arrActions =  $this->_oDb->getNotifyActions(false);
 
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
 			$iId = $aEachAction['id'];  
			$sNewGroup = $aEachAction['group'];
			$sTemplate = $aEachAction['template'];
		
			$oEmailTemplate = new BxDolEmailTemplates();
			$aTemplate = $oEmailTemplate -> getTemplate( $sTemplate, 0 ) ;

			$sSubject = $aTemplate['Subject'];
			$sMessage = $aTemplate['Body'];			
			 
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
			 
			$aForm['inputs']["Item{$iter}"] = array(
				'type' => 'custom',
				'name' => "Item{$iter}",
				'content' =>  "<div style='width:100%'>
								<div style='float:left;width:90%'>
									<input name='subject[$iId]' type=text value='{$sSubject}'><br>
									<textarea name='message[$iId]' cols=5 rows=40>{$sMessage}</textarea>
								</div>   
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
				'content' =>  MsgBox(_t("_modzzz_notify_no_templates")), 
				'colspan' => true
			);  
		}

		$oForm = new BxTemplFormView($aForm);
		$sCode .= '<div class="dbContent">' . $oForm->getCode() . '</div>'; 
 
		return $sCode;
	}

    function actionAdministrationManage () {
	   
		if(isset($_POST['submit_form']) && !empty($_POST['submit_form'])){ 
			foreach($_POST['item'] as $iKey) {  
				$bStatus = (int)$_POST['active'][$iKey];
 
		 		$this->_oDb->query("UPDATE `" . $this->_oDb->_sPrefix . "main` SET  `active`='$bStatus' WHERE `id`=$iKey");
			}  
		}
		
		$arrActions =  $this->_oDb->getNotifyActions(false);
 
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
			if(in_array($aEachAction['unit'], array('profile','friend','fave','block'))){
				$sUnit=$aEachAction['unit'];
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
			$sActionC = _t($aEachAction['desc']); 
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
				'content' =>  MsgBox(_t("_modzzz_notify_no_actions")), 
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
		$bEnabled = isset($this->_isPermalinkEnabled) ? $this->_isPermalinkEnabled : ($this->_isPermalinkEnabled = (getParam('permalinks_notify') == 'on'));
		 
        return $bEnabled;
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

    function serviceResponseProfileJoin ($oAlert) {

        if (!($iProfileId = (int)$oAlert->iObject))
            return false;

        $this->_oDb->InitMemberNotifier($iProfileId);
        
        return true;
    }

	function actionProcess() 
	{
		$this ->_oDb->processNotifications();
	}



}
