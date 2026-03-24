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
 * Notify module Data
 */
class BxNotifyDb extends BxDolTwigModuleDb {	
	
	var $oConfig;

	/*
	 * Constructor.
	 */
	function BxNotifyDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);
		$this->_oConfig = $oConfig;
	}
 
 	function getVoteUnit ($sTable) {  
		$sUnit = $this->getOne("SELECT `ObjectName` FROM `sys_objects_vote` WHERE `TriggerTable`='$sTable' LIMIT 1");
		
		return $sUnit;
	}

 	function getID ($sNick) {  
		$iId = $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName`='$sNick' LIMIT 1");
		
		return $iId;
	}
 
 	function getNotifyActions($bActiveOnly=true, $sActionType='') {
		
		if($bActiveOnly)
			$sWhere = "WHERE `active`=1 ";
 
		switch($sActionType){
			case "friend":
				$sWhere = ($sWhere) ? $sWhere . "AND `friend_action`='yes'" : "WHERE `friend_action`='yes'";
			break;
			case "member":
				$sWhere = ($sWhere) ? $sWhere . "AND `friend_action`='no'" : "WHERE `friend_action`='no'";
 			break;  
		}

		$arrActions = $this->getAll("SELECT * FROM `" . $this->_sPrefix . "main` {$sWhere} ORDER BY `group`, `unit`");
	 
		return $arrActions;
	}
  
  	function getMemberNotifySettings($iProfileId) {
 
		$aSettings = array();
	 	$aDbEntries = $this->getAll("SELECT `ActionId`, `Access` FROM `" . $this->_sPrefix . "member_settings` WHERE  `MemberID`='" . $iProfileId . "'");

		foreach($aDbEntries as $aEachEntry){
			$iActionId = $aEachEntry['ActionId'];
 			$sAccess = $aEachEntry['Access'];

 			$aSettings[$iActionId]['Access'] = $sAccess; 
		}

		return $aSettings;
	}

  	function saveMemberSettings($iProfileId) {
		
		foreach($_REQUEST['notify'] as $iActionId=>$sAccess){
			 
			if($bExists = $this->getOne("SELECT `ActionId` FROM `" . $this->_sPrefix . "member_settings` WHERE `ActionId`='$iActionId' AND `MemberID`='" . $iProfileId . "'")){
				$this->query("UPDATE `" . $this->_sPrefix . "member_settings` SET `Access`='$sAccess' WHERE `ActionId`='$iActionId' AND `MemberID`='" . $iProfileId . "'");
			}else{ 
				$this->query("INSERT INTO `" . $this->_sPrefix . "member_settings` SET `Access`='$sAccess', `ActionId`='$iActionId', `MemberID`='" . $iProfileId . "'");
			}
		}
	}
  
	function AllowNotify($sUnit, $sAction, $iOwnerId)
	{
		$iOwnerId = (int)$iOwnerId;
 
		$sAccess = $this->getOne("SELECT ms.`Access` FROM `" . $this->_sPrefix . "member_settings` ms, `" . $this->_sPrefix . "main` nt, `Profiles` p WHERE p.`ID`=ms.`MemberID` AND ms.ActionID = nt.`id` AND p.`EmailNotify` = 1 AND nt.`unit`='$sUnit' AND nt.`action`='$sAction' AND ms.`MemberID`=$iOwnerId AND nt.active=1 LIMIT 1");
	 
		switch($sAccess){ 
			case 'yes' : 
				return true;  
				break;
			case 'no' : 
				return false;  
				break;
			default :	
				return false;  
		} 
	}

	function AllFriendsNotify($sUnit, $sAction, $iOwnerId){
	
		$iOwnerId = (int)$iOwnerId;
 
		$iActionId = (int)$this->getOne("SELECT `id` FROM `" . $this->_sPrefix . "main` WHERE  `unit`='$sUnit' AND `action`='$sAction' AND `active`=1");
  
	    $aUniqueMembers = array();	
	    if(!$iActionId)
		   return $aUniqueMembers;

		$aFriends = $this->getAllFriends($iOwnerId, $iActionId); 
		$aFaves = $this->getAllFaves($iOwnerId, $iActionId); 

		$aMembers = array_merge($aFriends, $aFaves);
		$aUniqueMembers = array_unique($aMembers); 

		return $aUniqueMembers; 
	}

	function getAllFaves($iOwnerId, $iActionId) {
	  
		$sqlQuery = db_res("SELECT fav.`Profile` FROM `sys_fave_list` fav 
			LEFT JOIN `" . $this->_sPrefix . "member_settings` ms ON (ms.`MemberID` = fav.`Profile` AND ms.`Access` IN ('favorites','all'))
			WHERE  ms.ActionID = $iActionId 
			AND fav.`ID`='{$iOwnerId}'");
  
		$aFaves = array();
 
		while ($aProfiles = mysql_fetch_assoc($sqlQuery)) {
			$aFaves[] = $aProfiles['Profile'];
		}

		return $aFaves; 
	}
	 
	function getAllFriends($iID, $iActionId) {
  
		$sqlQuery = "
			SELECT p.`ID`
			FROM `Profiles` AS p
			LEFT JOIN `sys_friend_list` AS f1 ON (f1.`ID` = p.`ID` AND f1.`Profile` ='{$iID}' AND `f1`.`Check` = 1)
			LEFT JOIN `sys_friend_list` AS f2 ON (f2.`Profile` = p.`ID` AND f2.`ID` ='{$iID}' AND `f2`.`Check` = 1)
			LEFT JOIN `" . $this->_sPrefix . "member_settings` ms ON (ms.`MemberID` = p.`ID` AND ms.`Access` IN ('friends','all'))
			WHERE  ms.ActionID = $iActionId
			AND (f1.`ID` IS NOT NULL OR f2.`ID` IS NOT NULL)
 		";

		$aFriends = array();

		$vProfiles = db_res($sqlQuery);
		while ($aProfiles = mysql_fetch_assoc($vProfiles)) {
			$aFriends[] = $aProfiles['ID'];
		}

		return $aFriends;
	}

	function isOwnerFave($iOwnerId, $iActionMemberId) {
	
		if($iOwnerId == $iActionMemberId) 
			return true;
	  
		$cnt = db_arr("SELECT SUM(`Check`) AS 'cnt' FROM `sys_fave_list` WHERE `ID`='{$iOwnerId}' AND `Profile`='{$iActionMemberId}'");
 
		return ($cnt['cnt'] > 0 ? true : false);
	}
	 
	function isOwnerFriend($iOwnerId, $iActionMemberId)
	{
		if($iOwnerId == $iActionMemberId) 
			return true;
	  
		$cnt = db_arr("SELECT SUM(`Check`) AS 'cnt' FROM `sys_friend_list` WHERE `ID`='".$iOwnerId."' AND `Profile`='".$iActionMemberId."' OR `ID`='".$iActionMemberId."' AND `Profile`='".$iOwnerId."'");
 
		return ($cnt['cnt'] > 0 ? true : false);
	}

	//notify subscribers

	function alertForumOwner($sUnit, $sAction, $iOwnerId, $iActionMemberId, $sItemTopic, $sItemUrl) {
		
		$iOwnerId = (int)$iOwnerId;
		$iActionMemberId = (int)$iActionMemberId;
  
		if( !$iOwnerId )
			return false;
  
		$aItemData = $this->getRow("SELECT * FROM `" . $this->_sPrefix . "main`  WHERE `unit`='$sUnit' AND `action`='$sAction' AND `active`=1");
		if(!count($aItemData))
			return;

		$sItem = $aItemData['title'];
		$sTemplate = $aItemData['template'];
 
		$aPoster = getProfileInfo($iActionMemberId);
		$sPosterName = getNickName($aPoster['ID']); 
		$sPosterLink = getProfileLink($aPoster['ID']); 
		// Freddy avatar
		//$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
		$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
		
		
	 
		$aOwner = getProfileInfo($iOwnerId);
		$sOwnerName = getNickName($iOwnerId);
		$sOwnerEmail = $aOwner['Email'];
	   
		$oEmailTemplate = new BxDolEmailTemplates(); 
 		$aTemplate = $oEmailTemplate->getTemplate($sTemplate, $iOwnerId);
		
		$sSubject = str_replace("<SiteName>", $GLOBALS['site']['title'], $aTemplate['Subject']);
		$sSubject = str_replace("<Item>", $sItem, $sSubject);

		$sMessage = str_replace("<OwnerName>", $sOwnerName, $aTemplate['Body']); 
		$sMessage = str_replace("<PosterName>", $sPosterName, $sMessage); 
		// Freddy avatar
		$sMessage = str_replace("<PosterAvatar>", $sPosterAvatar, $sMessage); 
		
		$sMessage = str_replace("<PosterLink>", $sPosterLink, $sMessage); 
		$sMessage = str_replace("<ItemTopic>", $sItemTopic, $sMessage); 
		$sMessage = str_replace("<ItemUrl>", $sItemUrl, $sMessage); 
		$sMessage = str_replace("<Item>", $sItem, $sMessage); 
		$sMessage = str_replace("<SiteName>", $GLOBALS['site']['title'], $sMessage); 
		$sMessage = str_replace("<SiteUrl>", $GLOBALS['site']['url'], $sMessage); 
    
		$sNotifyFrequency = $this->getNotificationSetting($iOwnerId);
		$aPlus = array();

		switch($sNotifyFrequency){
			case 'daily': 
			case 'weekly': 
			case 'monthly': 
				$sContent = $oEmailTemplate->parseContent($sMessage, $aPlus, $iOwnerId);
				$aContent = explode('</p><p>', $sContent);
				$aContent = explode('</p>', $aContent[1]);
				$sMessage = $aContent[0];

				$this->saveNotification($iOwnerId, $sSubject, $sMessage, $sItemTopic);
			break;
			case 'immediately':
				sendMail( $sOwnerEmail, $sSubject, $sMessage, $iOwnerId, $aPlus , 'html'); 
			break;
			default:
				//do nothing
		}  
	}
  
	function trackViews($sUnit, $sAction, $iObject, $iSenderId){
		$iObject = (int)$iObject;
		$iSenderId = (int)$iSenderId;
		$iTime = time();

		if(!$this->isViewedToday($sUnit, $sAction, $iObject, $iSenderId)){
			$this->query("INSERT INTO `" . $this->_sPrefix . "track_views` SET `unit`='$sUnit', `action`='$sAction', `object_id`=$iObject, `sender_id`=$iSenderId, `created`=$iTime"); 
		}
	}
  
	function isViewedToday($sUnit, $sAction, $iObject, $iSenderId){
		$iObject = (int)$iObject;
		$iSenderId = (int)$iSenderId;
		$iTime = time();

 		$iCreated = $this->getOne("SELECT `created` FROM `" . $this->_sPrefix . "track_views` WHERE `unit`='$sUnit' AND `action`='$sAction' AND `object_id`=$iObject AND `sender_id`=$iSenderId LIMIT 1"); 

		list($iNowDay, $iNowMonth, $iNowYear) = explode(':', date('j:n:Y', $iTime));
		list($iDBDay, $iDBMonth, $iDBYear) = explode(':', date('j:n:Y', $iCreated));

		return ($iNowDay==$iDBDay && $iNowMonth==$iDBMonth && $iNowYear==$iDBYear);
	}

	function alertOwner($sUnit, $sAction, $iOwnerId, $iActionMemberId, $iItemId=0){
		
		$iOwnerId = (int)$iOwnerId;
		$iActionMemberId = (int)$iActionMemberId;
		$iItemId = (int)$iItemId;
 
		if( !$iOwnerId )
			return false;
  
		$aItemData = $this->getRow("SELECT * FROM `" . $this->_sPrefix . "main`  WHERE `unit`='$sUnit' AND `action`='$sAction' AND `active`=1");
		if(!count($aItemData))
			return;

		$sItem =  _t($aItemData['title']);
		$sTemplate = $aItemData['template'];

		if($iItemId){
			$aObject = $this->getObjectRecord($sUnit, $iItemId);
			$sItemTopic =$aObject['title'];
			$sUri = $aObject['uri'];
			$sClass = $aObject['class'];

			if($sClass){
				$oModule = BxDolModule::getInstance($sClass);   
				$sModuleUri = $GLOBALS['site']['url'] . $oModule->_oConfig->getBaseUri();
			} 
 
			$sItemUrl = $aObject['view_uri'];
			$sItemUrl = str_replace("{id}", $iItemId, $sItemUrl);
			$sItemUrl = str_replace("{uri}", $sUri, $sItemUrl);
			$sItemUrl = str_replace("{module_url}", $sModuleUri, $sItemUrl);
			$sItemUrl = str_replace("{site_url}", $GLOBALS['site']['url'], $sItemUrl);
		}else{
			$sItemUrl = getProfileLink($iOwnerId); 
			$sItemTopic = _t('_Profile');
		}
 
		$aPoster = getProfileInfo($iActionMemberId);
		$sPosterName = getNickName($aPoster['ID']);  
		$sPosterLink = getProfileLink($aPoster['ID']); 
		
		
		//freddy avatar get_member_thumbnail pour ne pas afficher demande ami
		//$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
		$sPosterAvatar = get_member_thumbnail($aPoster['ID'], 'none', true);
	 
		$aOwner = getProfileInfo($iOwnerId);
		$sOwnerName = getNickName($iOwnerId);  
		$sOwnerEmail = $aOwner['Email'];
	  
		$oEmailTemplate = new BxDolEmailTemplates(); 
 		$aTemplate = $oEmailTemplate->getTemplate($sTemplate, $iOwnerId);
		$sMessage = $aTemplate['Body'];
		$sOrigMessage = $aTemplate['Body'];
		$sSubject = $aTemplate['Subject'];
   
		$aPlus = array();
		$aPlus['OwnerName'] = $sOwnerName;
		$aPlus['PosterName'] = $sPosterName;
		
		// freddy avatar
		$aPlus['PosterAvatar'] = $sPosterAvatar;
		
		$aPlus['PosterLink'] = $sPosterLink;
		$aPlus['ItemTopic'] = $sItemTopic; 
		$aPlus['ItemUrl'] = $sItemUrl;
		$aPlus['Item'] = $sItem; 
		$aPlus['SiteName'] = $GLOBALS['site']['title'];
		$aPlus['SiteUrl'] = $GLOBALS['site']['url'];
 
		$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);
		$sSubject = str_replace("<Item>", $aPlus['Item'], $sSubject);
		
		$sNotifyFrequency = $this->getNotificationSetting($iOwnerId);

		switch($sNotifyFrequency){
			case 'daily': 
			case 'weekly': 
			case 'monthly': 
				$sContent = $oEmailTemplate->parseContent($sMessage, $aPlus, $iOwnerId);

				$aContent = explode('</p><p>', $sContent);
				$aContent = explode('</p>', $aContent[1]);
				$sMessage = $aContent[0];

				$this->saveNotification($iOwnerId, $sSubject, $sMessage, $sItemTopic);
			break;
			case 'immediately':
				sendMail( $sOwnerEmail, $sSubject, $sMessage, $iOwnerId, $aPlus , 'html'); 
			break;
			default:
				//do nothing
		}
	}
   
    //cron processing of daily, weekly and monthly notifications
	function processNotifications(){
		 
		$oMain = BxDolModule::getInstance('BxNotifyModule');
 		$oEmailTemplate = new BxDolEmailTemplates(); 

	    $iDayOfMonth = (int)date('j'); 
		$iDayOfWeek = (int)date('w');
	 		
		$sNotifyDate = date('M d, Y');
  		$sSentTime = date('g:i A');
   
		$iDayOfWeek=0;
  
		if($iDayOfWeek==0 && $iDayOfMonth==1){
			$aMembers = $this->getNotifyMembers(); 
		}elseif($iDayOfMonth==1){
			$aMembers = $this->getNotifyMembers(3); 
		}elseif($iDayOfWeek==0){
			$aMembers = $this->getNotifyMembers(2); 
		}else{
			$aMembers = $this->getNotifyMembers(1); 
		}
 
		foreach($aMembers as $aEachMember) {
			$sPeriod = $aEachMember['Period'];
			$iRecipientId = (int)$aEachMember['MemberID'];

			$aRecipientProfile = getProfileInfo($iRecipientId);
			$sRecipientNick = $aRecipientProfile['NickName'];
			$sRecipientEmail = $aRecipientProfile['Email'];
  
			$sData = '';
			$iOldPostDay = 0;
			$iOldPostMonth = 0;
			$aNotifications = $this->getNotifications($iRecipientId); 
			foreach($aNotifications as $aEachNotification) { 
				$iPosted = $aEachNotification['created'];
 
				$iPostDay = (int)date('j', $iPosted);
				$iPostMonth = (int)date('n', $iPosted);
				$sPostDate = date('M d, Y', $iPosted);
				
				$bShowHeader = false;
				if($iPostDay!=$iOldPostDay || $iOldPostMonth!=$iPostMonth)
					$bShowHeader = true;
					 
				if(trim(strip_tags($aEachNotification['subject']))){ 
					$sData .= $oMain->_oTemplate->email_unit($aEachNotification, 'email_unit', $bShowHeader, $sPostDate);
				}
				
				$iOldPostDay = $iPostDay;
				$iOldPostMonth = $iPostMonth;  
			}

			if(trim(strip_tags($sData))){ 
				$aTemplate = $oEmailTemplate->getTemplate('modzzz_notify_periodical', $iRecipientId);
				 
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
  
    function getNotifyMembers($iPeriod=1){

		switch($iPeriod){
			case 1:
				$sExtraSQL = "AND `Period`='daily'";
			break;
			case 2:
				$sExtraSQL = "AND `Period` IN ('daily', 'weekly')";
			break;
			case 3:
				$sExtraSQL = "AND `Period` IN ('daily', 'monthly')";
			break; 
		}
 
 		return $this->getAll("SELECT `MemberID`, `Period` FROM `" . $this->_sPrefix . "notification_settings` WHERE `Active`=1 {$sExtraSQL}");  	
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

	function getNotifications($iOwnerId){
		$iOwnerId = (int)$iOwnerId;
		
		return $this->getAll("SELECT `subject`, `message`, `title`, `created` FROM `" . $this->_sPrefix . "notifications` WHERE `member_id` = $iOwnerId ORDER BY `created`");
	}

  	function emptyNotifications($iOwnerId){
		$iOwnerId = (int)$iOwnerId;
		
		return $this->query("DELETE FROM `" . $this->_sPrefix . "notifications` WHERE `member_id` = $iOwnerId");
	}

	function initializeSettings(){
		$sDefaultFriendAccess = getParam("modzzz_notify_friend_default");
		$sDefaultMemberAccess = getParam("modzzz_notify_member_default");
		$sDefaultNotify = getParam("modzzz_notify_email_default");

		$this->query("TRUNCATE TABLE `" . $this->_sPrefix . "member_settings`");
		$this->query("TRUNCATE TABLE `" . $this->_sPrefix . "notification_settings`");

		$arrActions = $this->getAll("SELECT `id`, `friend_action` FROM `" . $this->_sPrefix . "main`");
	   
		$aProf  = $this->getAll("SELECT `ID` FROM `Profiles` WHERE `Status`='Active'");
		foreach($aProf as $aEachProf) {
		
			$iMemberID = (int)$aEachProf['ID'];
	 
			foreach($arrActions as $aEachAction) { 
				$iActionId = (int)$aEachAction['id'];
				$sDefaultAccess = ($aEachAction['friend_action']=='yes') ? $sDefaultFriendAccess : $sDefaultMemberAccess;

				$this->query("INSERT INTO `" . $this->_sPrefix . "member_settings` SET `ActionID`='{$iActionId}', `MemberID`='{$iMemberID}',  `Access`='$sDefaultAccess'"); 
			}
			
			$this->query("INSERT INTO `" . $this->_sPrefix . "notification_settings` SET `Period`='$sDefaultNotify', `MemberID`='{$iMemberID}',  `Active`=1"); 
 		} 
 
		//remove cron jobs
		$this->query("DELETE FROM `sys_cron_jobs` WHERE `name` = 'BxNotifyInit'");

        $bResult = $GLOBALS['MySQL']->cleanCache('sys_cron_jobs');  
	}

	function overrideMemberSettings() {

		$sDefaultFriendAccess = getParam("modzzz_notify_friend_default");
		$sDefaultMemberAccess = getParam("modzzz_notify_member_default");
		$sDefaultNotify = getParam("modzzz_notify_email_default");

		$this->query("UPDATE `" . $this->_sPrefix . "notification_settings` SET `Period`='$sDefaultNotify'"); 

		$this->query("UPDATE `" . $this->_sPrefix . "member_settings` SET  `Access`='$sDefaultMemberAccess' WHERE `Access` IN ('yes','no')"); 
		
		$this->query("UPDATE `" . $this->_sPrefix . "member_settings` SET  `Access`='$sDefaultFriendAccess' WHERE `Access` IN ('friends','favorites','all','none')");  
	}

	function InitMemberNotifier( $iMemberID = 0, $bAllMembers = false) {
	  
		$sDefaultFriendAccess = getParam("modzzz_notify_friend_default");
		$sDefaultMemberAccess = getParam("modzzz_notify_member_default");
		$sDefaultNotify = getParam("modzzz_notify_email_default");

		$arrActions = $this->getAll("SELECT `id`, `friend_action` FROM `" . $this->_sPrefix . "main`");
	  
		if($bAllMembers) {  
			$aProf  = $this->getAll("SELECT `ID` FROM `Profiles` WHERE `Status`='Active'");
			foreach($aProf as $aEachProf) {
			
				$iMemberID = (int)$aEachProf['ID'];
		 
				foreach($arrActions as $aEachAction) { 
					$iActionId = (int)$aEachAction['id'];
 					$sDefaultAccess = ($aEachAction['friend_action']=='yes') ? $sDefaultFriendAccess : $sDefaultMemberAccess;

					$this->query("INSERT INTO `" . $this->_sPrefix . "member_settings` SET `ActionID`='{$iActionId}', `MemberID`='{$iMemberID}',  `Access`='$sDefaultAccess'"); 
				} 

				$this->query("INSERT INTO `" . $this->_sPrefix . "notification_settings` SET `Period`='$sDefaultNotify', `MemberID`='{$iMemberID}',  `Active`=1");  
			} 
		}else{ 
			foreach($arrActions as $aEachAction) { 
				$iActionId = (int)$aEachAction['id'];
			
 				$sDefaultAccess = ($aEachAction['friend_action']=='yes') ? $sDefaultFriendAccess : $sDefaultMemberAccess;

				if($bExists = $this->getOne("SELECT `ActionID` FROM `" . $this->_sPrefix . "member_settings` WHERE `MemberID`='{$iMemberID}' AND `ActionID`={$iActionId} LIMIT 1")) {
					$this->query("UPDATE `" . $this->_sPrefix . "member_settings` SET `Access`='$sDefaultAccess' WHERE `ActionID`='{$iActionId}' AND `MemberID`='{$iMemberID}' ");
				}else{
					$this->query("INSERT INTO `" . $this->_sPrefix . "member_settings` SET `ActionID`='{$iActionId}', `MemberID`='{$iMemberID}',  `Access`='$sDefaultAccess'");
				}  
			}

			$this->query("INSERT INTO `" . $this->_sPrefix . "notification_settings` SET `Period`='$sDefaultNotify', `MemberID`='{$iMemberID}',  `Active`=1"); 
		}  
	}
	  
	function InitActionNotifier($iActionID = 0) {
	  
		$sDefaultFriendAccess = getParam("modzzz_notify_friend_default");
		$sDefaultMemberAccess = getParam("modzzz_notify_member_default");
 
		$sFriendAction = $this->getOne("SELECT `friend_action` FROM `" . $this->_sPrefix . "main` WHERE `id`='$iActionID'");
 
		$sDefaultAccess = ($sFriendAction=='yes') ? $sDefaultFriendAccess : $sDefaultMemberAccess;
 
		$aProfiles = $this->getAll("SELECT `ID` FROM `Profiles`");
		foreach ($aProfiles as $aEachProfile){
		
			 $iMemberId = (int)$aEachProfile['ID'];
	 
			 if(!$bExists = $this->getOne("SELECT `MemberID` FROM `" . $this->_sPrefix . "member_settings` WHERE `ActionID`='$iActionID' AND `MemberID`='" . $iMemberId . "' LIMIT 1") ){
			  
					 $this->query("INSERT INTO `" . $this->_sPrefix . "member_settings` SET `ActionID`='$iActionID', `MemberID`='" . $iMemberId . "',  `Access`='$sDefaultAccess'"); 
			 }
		}
	 
	}
  
	function getObjectRecord($sUnit, $iItemId) {
  		
		$aRecord =  array();  

		$aRow = $this->getRow("SELECT `table`, `id_field`, `owner_field`, `title_field`, `uri_field`,`view_uri`,`class` FROM `" . $this->_sPrefix . "field_mapping` WHERE `unit`='$sUnit' LIMIT 1");

		$sOwnerFld = $aRow['owner_field'];
		$sTitleFld = $aRow['title_field'];
		$sUriFld = $aRow['uri_field'];
		$sIDFld = $aRow['id_field'];
		$sTableFld = $aRow['table'];
		
		if($sIDFld) {
			$aRecord = $this->getRow("SELECT `".$sTitleFld."` as `title`, `".$sUriFld."` as `uri`, `".$sOwnerFld."` as `author_id` FROM `".$sTableFld."` WHERE `".$sIDFld."`='$iItemId' LIMIT 1");
		} 

		$aRecord['class'] = $aRow['class']; 
		$aRecord['view_uri'] = $aRow['view_uri']; 

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

 	function getCommentOwner ($sUnit, $iCmtId) {
		
		$sTable = $this-> getCommentTable ($sUnit);

		$iAuthorId = $this->getOne("SELECT `cmt_author_id` FROM `{$sTable}`  WHERE `cmt_id` = '$iCmtId' LIMIT 1");
	 
		return $iAuthorId;
	}
 
 	function getCommentAuthorFromObject ($sTable, $iObjectId, $iCmtId) {  
		$iAuthorId = $this->getOne("SELECT `cmt_author_id` FROM {$sTable}  WHERE `cmt_object_id` = '$iObjectId' AND `cmt_id` = '$iCmtId' LIMIT 1");
	 
		return $iAuthorId;
	}

 	function getCommentAuthor ($sTable, $iCmtId, $iObjectId=0 ) {  
		$iAuthorId = $this->getOne("SELECT `cmt_author_id` FROM {$sTable}  WHERE `cmt_id` = '$iCmtId' LIMIT 1");
	 
		return $iAuthorId;
	}

 	function getCommentTable ($sUnit) {  
		$sTableName = $this->getOne("SELECT `TableCmts` FROM `sys_objects_cmts` WHERE `ObjectName` = '$sUnit' LIMIT 1");
	 
		return $sTableName;
	}
 
	function removeProfileEntries($iProfileId) { 
		 $this->query("DELETE FROM `" . $this->_sPrefix . "member_settings` WHERE `MemberID` = '$iProfileId'"); 

 		 $this->query("DELETE FROM `" . $this->_sPrefix . "notification_settings` WHERE `MemberID` = '$iProfileId'");  	

 		 $this->query("DELETE FROM `" . $this->_sPrefix . "notifications` WHERE `member_id` = '$iProfileId'");   
	}

	function getNotificationSetting($iProfileId) { 
 
		return $this->getOne("SELECT `Period` FROM `" . $this->_sPrefix . "notification_settings` WHERE `MemberID` = '$iProfileId' LIMIT 1");   
	}

	function updateNotificationSettings($iProfileId, $sSetting) { 
 
		$this->query("DELETE FROM `" . $this->_sPrefix . "notification_settings` WHERE `MemberID` = '$iProfileId'");   

		$this->query("INSERT INTO `" . $this->_sPrefix . "notification_settings` SET `MemberID` = '$iProfileId', `Period`='$sSetting', `Active`=1");  
 	}

	/*******************************/

	function alertTopicParticpants($iForumId, $iTopicId, $sUser) {
		global $gConf;
   
		$iPosterId = $this->getID($sUser);

 		$arrTopic = $this->getTopic($iTopicId);
		$sTopicTitle = $arrTopic['topic_title'];
  		$sTopicURI = $arrTopic['topic_uri'];
		$sTopicUrl = $gConf['url']['base'] . sprintf($gConf['rewrite']['topic'], $sTopicURI);
     
		$a = $this->getAll("SELECT DISTINCT p.`ID`, p.`NickName`, p.`Email`  FROM " . TF_FORUM_POST . " tf, Profiles p  WHERE tf.user=p.NickName AND p.`Status`='Active' AND tf.`topic_id` = '$iTopicId' AND tf.`forum_id` = '$iForumId' AND tf.`user` != '$sUser'");
 
		foreach ($a as $aMember){ 
			  
			if( $this->isFlagged($iTopicId, $sUser) )
				continue; 

			if( $this->isTopicOwner($aMember['NickName'], $iTopicId, $iForumId) )
				continue; 
 
			if( $this->AllowNotify('bx_forum', 'post_participant', $aMember['ID']) ){	 
				$this->alertForumOwner('bx_forum', 'post_participant', $aMember['ID'], $iPosterId, $sTopicTitle, $sTopicUrl);  
			} 
		}
	}


	function alertTopicOwner($iForumId, $iTopicId, $sUser) {
 	    global $gConf;

 		if($this->isTopicOwner($sUser, $iTopicId, $iForumId))
			return;

		$iOwnerId = $this->getTopicOwner($iTopicId, $iForumId); 
 	    
		$iPosterId = $this->getID($sUser); 
		
		$arrTopic = $this->getTopic($iTopicId); 

		$sTopicTitle = $arrTopic['topic_title'];
  		$sTopicURI = $arrTopic['topic_uri'];
		$sTopicUrl = $gConf['url']['base'] . sprintf($gConf['rewrite']['topic'], $sTopicURI);
 
		if( $this->isFlagged($iTopicId, $sUser) )
			continue; 
 
		if( $this->AllowNotify('bx_forum', 'reply', $iOwnerId) ){	  
 			$this->alertForumOwner('bx_forum', 'reply', $iOwnerId, $iPosterId, $sTopicTitle, $sTopicUrl);
		}
 	}

	function isFlagged ($iTopicId, $u)
	{
		$sql = "SELECT `topic_id` FROM " . TF_FORUM_FLAG . " WHERE `user` = '$u' AND `topic_id` = '$iTopicId'";
		return $this->getOne ($sql);
	}

	function isTopicParticipant($sUser, $iTopicId, $iForumId) {
 
		$iID = (int)$this->getOne("SELECT `topic_id` FROM " . TF_FORUM_POST . " WHERE  tf.`topic_id` = '$iTopicId' AND tf.`forum_id` = '$iForumId' AND tf.`user` = '$sUser'");
 
		return $iID;		
	}

	function isTopicOwner($sUser, $iTopicId, $iForumId) {
	
		$iID = (int)$this->getOne("SELECT `topic_id` FROM " . TF_FORUM_TOPIC . "  WHERE first_post_user='$sUser' AND `topic_id` = '$iTopicId' AND `forum_id` = '$iForumId' LIMIT 1");

		return $iID;		
	}
 
	function getTopicOwner( $topic_id, $forum_id) {
	 
		$iOwner = $this->getOne("SELECT p.`ID` FROM " . TF_FORUM_TOPIC . " ft, Profiles p  WHERE first_post_user=p.NickName AND `topic_id` = '$topic_id' AND `forum_id` = '$forum_id' LIMIT 1");

		return $iOwner;		
	}

	function getTopic ($iTopicId) {
	
		return $this->getRow ( "SELECT `topic_id`, `topic_uri`, `topic_title`, `forum_title`, `forum_desc`, `forum_type`, `forum_uri`, f1.`forum_id`, `cat_id`, `first_post_user` FROM " . TF_FORUM_TOPIC . " AS f1 INNER JOIN " . TF_FORUM . " USING (`forum_id`) WHERE f1.`topic_id` = '$iTopicId' LIMIT 1");
    }
 
 	function exposeView($iProfileId) {  
 		$iProfileId = (int)$iProfileId;

		$oModuleDb = new BxDolModuleDb(); 
		if(!$oModuleDb->isModule('pview')) return true;
 
		return (int)$this->getOne("SELECT `id` FROM `modzzz_pview_privacy` WHERE `show_i_viewed`='yes' AND `id`=$iProfileId");
	}


}
