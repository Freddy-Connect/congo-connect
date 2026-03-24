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
 
bx_import('BxDolTwigModuleDb');
bx_import('BxDolAlerts');

/*
 * Listing module Data
 */
class BxAutoFriendDb extends BxDolTwigModuleDb {	
	
	var $_oConfig;

	/*
	 * Constructor.
	 */
	function BxAutoFriendDb(&$oConfig) {
        parent::BxDolTwigModuleDb($oConfig);

		$this->_oConfig = $oConfig; 
 
	}

	function getAdministrators(){
		$aAdmins = $this->getPairs("SELECT `ID`,`NickName`  FROM `Profiles` WHERE `Role`=3", 'ID', 'NickName');  

		return $aAdmins;
	}

	function isMultipleAdministrators(){
		return (int)$this->getOne("SELECT COUNT(`ID`) FROM `Profiles` WHERE `Role`=3"); 
	}
 
	function isAdmin($iProfileId){
		return (int)$this->getOne("SELECT `ID` FROM `Profiles` WHERE `ID` = '$iProfileId' AND `Role`=3"); 
	}
 
	function makeAdmin($iProfileId, $isAdmin){
		$iNewStatus = ($isAdmin) ? 3 : 1;
 		
//		$aPreference = $this->getPreferenceAdmins();

		$this->query("UPDATE `Profiles` SET `Role` = $iNewStatus WHERE `ID` = '$iProfileId'"); 
/*

		if($isAdmin) 
			$aPreference[] = $iProfileId;
		else
			unset($aPreference[$iProfileId]);

		$this->setPreferenceAdmins($aPreference);
*/
		createUserDataFile( $iProfileId );
	}
    
  	function setPreferenceAdmins($aPreference){
		 $sPreference = implode(';', $aPreference);
 		 $this->query("UPDATE `" . $this->_sPrefix . "preference` SET `admins`='$sPreference'"); 
  	}
    
  	function getPreferenceAdmins(){
 		$sAdmins = $this->getOne("SELECT `admins` FROM `" . $this->_sPrefix . "preference` LIMIT 1");

		$aAdmins = explode(';', $sAdmins);

		return $aAdmins; 
  	} 

	function getAllAdmins(){
		$aAdmins = $this->getPairs("SELECT `ID` FROM `Profiles` WHERE `Role`=3", 'ID', 'ID');  

		return $aAdmins;
	}

  	function initAutoFriend($aAdmins){
		$aMembers = $this->getAll("SELECT `ID` FROM `Profiles` WHERE `Role` = " . BX_DOL_ROLE_MEMBER );
		 
		foreach($aMembers as $aEachMember){
			$this->addAutoFriend($aEachMember['ID'], $aAdmins);
		}
  
		$this->query("UPDATE `" . $this->_sPrefix . "preference` SET `initialized`=1");
	}

	function addAutoFriend($iNewMemID, $aAdmins){
		$iNewMemID = (int)$iNewMemID;

  		$checked = 0;
		if(getParam("auto_friend_add") == "on"){
			$checked = 1;
		}
 
		foreach($aAdmins as $iAdminID){
 			
			if($iAdminID == $iNewMemID) continue;
			
			if($iAdminID == 0 || $iNewMemID==0 ) continue;
 
			if(!$bExists = $this->getOne("SELECT COUNT(`ID`) FROM `sys_friend_list` WHERE (`ID` = '$iNewMemID' AND `Profile` = '$iAdminID') or (`ID` = '$iAdminID' AND `Profile` = '$iNewMemID')"))
			{
				$this->query("INSERT INTO `sys_friend_list` SET `ID` = '$iAdminID', `Profile` = '$iNewMemID', `Check` = '$checked'");
			  		 
				$oZ = new BxDolAlerts('friend', 'accept', $iAdminID, $iNewMemID);
				$oZ -> alert();
 
				if(getParam("modzzz_autofriend_alert") == "on"){ 
					//send email notification
					$this->autoFriendAlert($iNewMemID, $iAdminID);  
				}
			}
		} 
	}

  
	//notify of friend requests  
	function autoFriendAlert($iNewMemberID, $iAdminID)
	{
 		$new_mem_arr = getProfileInfo($iNewMemberID, false, true);
		$sNewMemName = $new_mem_arr['NickName']; 
		$sNewMemEmail = $new_mem_arr['Email'];
		$sNewMemLink = getProfileLink($new_mem_arr['ID']);  
		  
		$admin_arr = getProfileInfo($iAdminID,false, true);
		$sAdminName = $admin_arr['NickName'];
		$sAdminEmail = $admin_arr['Email'];
		$sAdminLink = getProfileLink($admin_arr['ID']);
	   
		$oEmailTemplate = new BxDolEmailTemplates();
		$aTemplate = $oEmailTemplate -> getTemplate( 't_AutoFriend', $new_mem_arr['ID'] ) ;

		$sSubject = $aTemplate['Subject'];
		$sMessage = $aTemplate['Body'];

		$aPlus = array();
		if(getParam("auto_friend_add") == "on"){
	  
			$sendTo = $iNewMemberID;
			$sendToEmail = $sNewMemEmail;
			$itemDesc = _t("_autofriend_added_as_friend");
			$itemURL = $GLOBALS['site']['url'] . "viewFriends.php?iUser=$iNewMemberID";
			$itemAction = _t("_autofriend_new_friend_added");
			$aPlus['ViewType'] = _t("_autofriend_new_friends");
			$aPlus['FanName'] = $sAdminName; 
			$aPlus['FanLink'] = $sAdminLink;
			$aPlus['MemberName'] = $sNewMemName;  
	 
		}else{
			$sendTo = $iNewMemberID;
			$sendToEmail = $sNewMemEmail;
			$itemDesc = _t("_autofriend_sent_friend_request");
			$itemURL = $GLOBALS['site']['url'] . "communicator.php";
			$itemAction = _t("_autofriend_new_friend_request");
			$aPlus['ViewType'] = _t("_autofriend_friend_requests");
			$aPlus['FanName'] = $sAdminName; 
			$aPlus['FanLink'] = $sAdminLink;
			$aPlus['MemberName'] = $sNewMemName;  
		}

		$aPlus['FriendUrl'] = $itemURL;
		$aPlus['SiteName'] = $GLOBALS['site']['title'];
		$aPlus['ItemDesc'] = $itemDesc; 
		$sSubject = str_replace("<SiteName>", $aPlus['SiteName'], $sSubject);
		$sSubject = str_replace("<ItemDesc>", $aPlus['ItemDesc'], $sSubject);
		$sSubject = str_replace("<ActionType>", $itemAction, $sSubject);

		sendMail( $sendToEmail, $sSubject, $sMessage, $sendTo, $aPlus, "html" ); 
	}
 
 	function getID ($sNick) {  
		$iId = $this->getOne("SELECT `ID` FROM `Profiles` WHERE `NickName`='$sNick' LIMIT 1");
		
		return $iId;
	}
	
	function hasPreference () {  
		$bExists = (int)$this->getOne("SELECT `initialized` FROM `" . $this->_sPrefix . "preference` LIMIT 1");
		
		return $bExists;
	}

	function initFriends(){ 
		if($this->hasPreference())
			return;
 
		$aAdmins = $this->getPreferenceAdmins();

		$this->initAutoFriend($aAdmins);

		//remove cron jobs
		$this->query("DELETE FROM `sys_cron_jobs` WHERE `name` = 'BxAutoFriendInit'");

        $bResult = $GLOBALS['MySQL']->cleanCache('sys_cron_jobs');  
	}
   
	function updateMembershipLevel($iMemberId){
 
 		$aAdmins = $this->getPreferenceAdmins();

 		foreach($aAdmins as $iAdminId){

			$this->query("DELETE FROM `sys_friend_list`
				WHERE (`ID`='{$iAdminId}' AND `Profile`='{$iMemberId}')
					OR (`ID`='{$iMemberId}' AND `Profile` = '{$iAdminId}')");
		 
			$oZ = new BxDolAlerts('friend', 'delete', $iMemberId, $iAdminId);
			$oZ -> alert();
		}
	}

}
