<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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

bx_import('BxDolTwigModuleDb');
 
define ('TF_FORUM',			 '`'.$gConf['db']['prefix'].'forum`');
define ('TF_FORUM_CAT',		 '`'.$gConf['db']['prefix'].'forum_cat`');
define ('TF_FORUM_POST',	 '`'.$gConf['db']['prefix'].'forum_post`');
define ('TF_FORUM_TOPIC',	 '`'.$gConf['db']['prefix'].'forum_topic`');
define ('TF_FORUM_VOTE',	 '`'.$gConf['db']['prefix'].'forum_vote`');
define ('TF_FORUM_REPORT',	 '`'.$gConf['db']['prefix'].'forum_report`');
define ('TF_FORUM_FLAG',	 '`'.$gConf['db']['prefix'].'forum_flag`');
 

/*
 * Notifier module Data
 */
class BxNotifierDb extends BxDolTwigModuleDb {	
	
	var $oConfig;

	/*
	 * Constructor.
	 */
	function BxNotifierDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		$this->_oConfig = $oConfig;
	}

 	function getID ($sNick) {  
		$iId = $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName`='$sNick' LIMIT 1");
		
		return $iId;
	}
 
 	function getNotifierActions() {

		return $this->getAll("SELECT * FROM `" . $this->_sPrefix . "main` {$sWhere} ORDER BY `group`, `unit`");
	}

	function alertAdministrators($sUnit, $sAction, $iRecipientId, $iActionMemberId, $iItemId=0){
		
		$iRecipientId = (int)$iRecipientId;
		$iActionMemberId = (int)$iActionMemberId;
		$iItemId = (int)$iItemId;
 
		if( !$iRecipientId )
			return false;
 
		$aItemData = $this->getRow("SELECT * FROM `" . $this->_sPrefix . "main`  WHERE `unit`='$sUnit' AND `action`='$sAction' AND `active`=1");
		
		if(empty($aItemData)) return;
 		 
		$sItem = _t($aItemData['desc']);
 
		if($iItemId){
			$aObject = $this->getObjectRecord($sUnit, $iItemId);
 
			if(empty($aObject)) return;

			$sItemTopic = $aObject['title'];
			$sUri = $aObject['uri'];
			$sClass = $aObject['class'];

			if($sClass){
				$oModule = BxDolModule::getInstance($sClass);   
 
				if(!$oModule) return;
 
				$sModuleUri = $GLOBALS['site']['url'] . $oModule->_oConfig->getBaseUri();
			} 
 
			$sItemUrl = $aObject['view_uri'];
			$sItemUrl = str_replace("{id}", $iItemId, $sItemUrl);
			$sItemUrl = str_replace("{uri}", $sUri, $sItemUrl);
			$sItemUrl = str_replace("{module_url}", $sModuleUri, $sItemUrl);
			$sItemUrl = str_replace("{site_url}", $GLOBALS['site']['url'], $sItemUrl);
		}else{
			$sItemUrl = getProfileLink($iRecipientId); 
			$sItemTopic = _t('_Profile');
		}
 
		$aPoster = getProfileInfo($iActionMemberId);
		$sPosterName = $aPoster['NickName']; 
		$sPosterLink = getProfileLink($aPoster['ID']); 
	 
		$aRecipient = getProfileInfo($iRecipientId);
		$sRecipientName = $aRecipient['NickName'];
		$sRecipientEmail = $aRecipient['Email'];
	  
		$oEmailTemplate = new BxDolEmailTemplates(); 
 		$aTemplate = $oEmailTemplate->getTemplate('modzzz_notifier_add', $iRecipientId);
		$sMessage = $aTemplate['Body'];
		$sOrigMessage = $aTemplate['Body'];
		$sSubject = $aTemplate['Subject'];
   
		$aPlus = array();
		$aPlus['RecipientName'] = $sRecipientName;
		$aPlus['PosterName'] = $sPosterName;
		$aPlus['PosterLink'] = $sPosterLink;
		$aPlus['ItemTopic'] = $sItemTopic; 
		$aPlus['ItemUrl'] = $sItemUrl;
		$aPlus['Item'] = $sItem; 
		$aPlus['SiteName'] = $GLOBALS['site']['title'];
		$aPlus['SiteUrl'] = $GLOBALS['site']['url'];
 
		$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);
		$sSubject = str_replace("<Item>", $aPlus['Item'], $sSubject);
		
		$sNotifierFrequency = getParam('modzzz_notifier_email_default');

		switch($sNotifierFrequency){
			case 'daily': 
				$sContent = $oEmailTemplate->parseContent($sMessage, $aPlus, $iRecipientId);

				$aContent = explode('</p><p>', $sContent);
				$aContent = explode('</p>', $aContent[1]);
				$sMessage = $aContent[0];

				$this->saveNotification($iRecipientId, $sSubject, $sMessage, $sItemTopic);
			break;
			case 'immediately':
				sendMail( $sRecipientEmail, $sSubject, $sMessage, $iRecipientId, $aPlus , 'html'); 
			break;
			default:
				//do nothing
		}
	}
   
    //cron processing of daily, weekly and monthly notifications
	function processNotifications(){
		 
		$oMain = BxDolModule::getInstance('BxNotifierModule');
 		$oEmailTemplate = new BxDolEmailTemplates(); 

		$aMembers = $this->getNotifierMembers(); 
 
		foreach($aMembers as $aEachMember) {
			$sPeriod = $aEachMember['Period'];
			$iRecipientId = (int)$aEachMember['MemberID'];
			$aRecipientProfile = getProfileInfo($iRecipientId);
			$sRecipientNick = $aRecipientProfile['NickName'];
			$sRecipientEmail = $aRecipientProfile['Email'];
 
			$sData = '';
			$iOldPostDay = 0;
			$aNotifications = $this->getNotifications($iRecipientId); 
			foreach($aNotifications as $aEachNotification) { 
				$iPosted = $aEachNotification['created'];

				$iPostDay = (int)date('j', $iPosted);
				$sPostDate = date('M d, Y', $iPosted);
				
				$bShowHeader = false;
				if($iPostDay!=$iOldPostDay)
					$bShowHeader = true;
					 
				$sData .= $oMain->_oTemplate->email_unit($aEachNotification, 'email_unit', $bShowHeader, $sPostDate);
				
				$iOldPostDay = $iPostDay;
			}
			
			if($sData) {
				$aTemplate = $oEmailTemplate->getTemplate('modzzz_notifier_periodical', $iRecipientId);
				 
				$sMessage = str_replace("<RecipientName>", $sRecipientNick, $aTemplate['Body']);  

				$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $aTemplate['Subject']);

				$sMessage = str_replace("<Data>", $sData, $sMessage);  
				$sMessage = str_replace("<SiteName>", $GLOBALS['site']['title'], $sMessage); 
				$sMessage = str_replace("<SiteUrl>", $GLOBALS['site']['url'], $sMessage); 
				
				$aPlus =array();
				sendMail($sRecipientEmail, $sSubject, $sMessage, $iRecipientId, $aPlus , 'html'); 
			}		
			 
			$this->emptyNotifications($iRecipientId);  
		}

	}

	function getNotifications($iOwnerId){
		$iOwnerId = (int)$iOwnerId;
		
		return $this->getAll("SELECT `subject`, `message`, `title`, `created` FROM `" . $this->_sPrefix . "notifications` WHERE `member_id` = $iOwnerId ORDER BY `created`");
	}

  	function emptyNotifications($iOwnerId){
		$iOwnerId = (int)$iOwnerId;
		
		return $this->query("DELETE FROM `" . $this->_sPrefix . "notifications` WHERE `member_id` = $iOwnerId");
	}

 
	function getObjectRecord($sUnit, $iItemId) {
  		
		$aRecord =  array();  

		$aRow = $this->getRow("SELECT `table`, `id_field`, `owner_field`, `title_field`, `uri_field`,`view_uri`,`class`, `status_field`, `pending_value` FROM `" . $this->_sPrefix . "field_mapping` WHERE `unit`='$sUnit' LIMIT 1");

		$sOwnerFld = $aRow['owner_field'];
		$sTitleFld = $aRow['title_field'];
		$sUriFld = $aRow['uri_field'];
		$sIDFld = $aRow['id_field'];
		$sTableFld = $aRow['table'];
		$sStatusFld = $aRow['status_field'];
		$sPendingValue = $aRow['pending_value']; 

		$sSqlExtra = "";
		if(getParam('modzzz_notifier_only_pending')=='on'){
			$sSqlExtra = "AND `{$sStatusFld}` = '{$sPendingValue}'"; 
		}

		if($sIDFld) {
			$aRecord = $this->getRow("SELECT `{$sTitleFld}` as `title`,  `{$sUriFld}` as `uri`, `{$sOwnerFld}` as `author_id` FROM `{$sTableFld}` WHERE `{$sIDFld}` = '$iItemId' {$sSqlExtra} LIMIT 1");
		} 

		if(count($aRecord)) { 
			$aRecord['class'] = $aRow['class']; 
			$aRecord['view_uri'] = $aRow['view_uri']; 
		}

		return $aRecord;  
 	} 

	function getObjectOwner($sUnit, $iItemId) {
	 
		$aRow = $this->getRow("SELECT `table`, `id_field`, `owner_field` FROM `" . $this->_sPrefix . "field_mapping` WHERE `unit`='$sUnit' LIMIT 1");
		$sOwnerFld = $aRow['owner_field'];
		$sIDFld = $aRow['id_field'];
		$sTableFld = $aRow['table'];
		 
		if($sIDFld) {
			$iOwner = $this->getOne("SELECT `".$sOwnerFld."` FROM `".$sTableFld."` WHERE `".$sIDFld."`='$iItemId' LIMIT 1");

			return $iOwner;
		} 

		return 0; 
 	}

    function getNotifierMembers(){

 		return $this->getAll("SELECT `ID` as `MemberID` FROM `Profiles` WHERE  `Role`=3 AND `Status`='Active'");  	
	}

  	function saveNotification($iOwnerId, $sSubject, $sMessage, $sItemTopic){
		$sSubject = process_db_input($sSubject);
		$sMessage = process_db_input($sMessage);
		$sItemTopic = process_db_input($sItemTopic);

		$iTime = time();
		$iOwnerId = (int)$iOwnerId;
		$this->query("INSERT INTO `" . $this->_sPrefix . "notifications` SET 
					`member_id` = $iOwnerId,	
					`subject` = '$sSubject',	
					`message` = '$sMessage',	
					`title` = '$sItemTopic',
					`created` = $iTime
		");
	}

	function getModules() {
	 
		$aOptions = array();
		$aModules = $this->getAll("SELECT * FROM `sys_modules` WHERE CONCAT(`class_prefix`, 'Module') NOT IN (SELECT `class` FROM `" . $this->_sPrefix . "field_mapping`)");           
		
		foreach($aModules as $aModule){
			$sClassName = $aModule['class_prefix'] . 'Module';
			$sClassPath = BX_DIRECTORY_PATH_MODULES . $aModule['path'] . '/classes/' . $sClassName . '.php';            
			if(!file_exists($sClassPath)) continue;
 
			$aDbPrefix = explode('_',$aModule['db_prefix']);
			$sUnit = $aDbPrefix[1]; 
			$aOptions[$sUnit] = strip_tags($aModule['title']);
		}

		return $aOptions;
	}

	function addNotifyAction($aValsAdd){

		$oModuleDb = new BxDolModuleDb();
		$aModule = $oModuleDb->getModuleByUri($aValsAdd['unit']);
 
		$aDbPrefix = explode('_',$aModule['db_prefix']); 
		$sModule = $aDbPrefix[0].'_'.$aDbPrefix[1]; 
		$sClassName = $aModule['class_prefix']. 'Module';

        $sCaption = $aValsAdd['action_desc'];  
		$sCaptionKey = '_modzzz_notifier_'.$aValsAdd['unit'].'_add_desc' ;
		addStringToLanguage($sCaptionKey, $sCaption);

		$this->query("INSERT INTO `" . $this->_sPrefix . "main` (`group`, `unit`, `action`, `desc`, `active`) VALUES
		('_{$sModule}', '".$aValsAdd['unit']."', '".$aValsAdd['action']."', '{$sCaptionKey}', 1)");

		$this->query("INSERT INTO `" . $this->_sPrefix . "field_mapping` (`status_field`, `pending_value`, `unit`, `table`, `id_field`, `owner_field`, `title_field`, `uri_field`, `view_uri`, `class`) VALUES
		('".$aValsAdd['status_field']."', '".$aValsAdd['pending_value']."', '".$aValsAdd['unit']."', '".$aValsAdd['table']."', '".$aValsAdd['id_field']."', '".$aValsAdd['author_field']."', '".$aValsAdd['title_field']."', '".$aValsAdd['uri_field']."', '{module_url}view/{uri}', '$sClassName')");
 
		$iHandler = $this->getOne("SELECT `id` FROM `sys_alerts_handlers` WHERE `name`='modzzz_notifier' LIMIT 1");

		$this->query("INSERT INTO `sys_alerts` VALUES (NULL, '{$sModule}', '".$aValsAdd['action']."', $iHandler)"); 
	}
  
	function isTableExists($sTable){
		global $db;

		return (int)$this->getOne("SELECT count(*) FROM information_schema.tables WHERE table_schema = '".$db['db']."' AND table_name = '$sTable'");
	}

	function updateModuleStatus($iKey, $bStatus){
		$iKey = (int)$iKey;

		$this->query("UPDATE `" . $this->_sPrefix . "main` SET  `active`='$bStatus' WHERE `id`=$iKey");
	}

}
