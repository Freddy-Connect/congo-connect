<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolModuleDb');

class MbMmpDb extends BxDolModuleDb {
    var $_oConfig;
	function MbMmpDb(&$oConfig) {
		parent::BxDolModuleDb();
        $this->_sPrefix = $oConfig->getDbPrefix();
		$this->_oConfig = &$oConfig;
    }
	
	function getMemberships() {
	    $sMethod = "getAll";      
        $sSql = "SELECT * FROM `sys_acl_levels` WHERE `Active`='yes' ORDER BY `Order` ASC";		
	   return $this->$sMethod($sSql);
	}
	function getMembershipsAdmin() {
	    $sMethod = "getAll";      
        $sSql = "SELECT * FROM `sys_acl_levels` ORDER BY `Order` ASC";		
	   return $this->$sMethod($sSql);
	}
	function getMembershipById($iMbsId) {
	    $sMethod = "getRow";      
        $sSql = "SELECT * FROM `sys_acl_levels` WHERE `ID`='{$iMbsId}' LIMIT 1";		
	   return $this->$sMethod($sSql);
	}
	function getMembershipPriceInfo($iMembId) {
	    $sMethod = "getRow";
        $sSql = "SELECT * FROM `sys_acl_level_prices` WHERE `IDLevel` = '{$iMembId}'";		
	   	return $this->$sMethod($sSql);
	}
	
	/**
	 * Grabs all important membership data
	 *
	 */
	function getMembershipData($IDLevel){
		$aMembership = $this->getAll(
			"SELECT
				`sys_acl_levels`.`ID`,
				`sys_acl_levels`.`Name`,
				`sys_acl_levels`.`Description`,
				`sys_acl_level_prices`.`Price`,
				`sys_acl_level_prices`.`Unit`,
				`sys_acl_level_prices`.`Length`,
				`sys_acl_level_prices`.`Auto`
			FROM
				`sys_acl_levels`,
				`sys_acl_level_prices`
			WHERE
				`sys_acl_levels`.`ID` = `sys_acl_level_prices`.`IDLevel` AND
				`sys_acl_levels`.`ID` = '$IDLevel'");
		return $aMembership;
		
	}
	
	function getMembershipIcon($iMembershipId){
	    $sMethod = "getOne";
        $sSql = "SELECT `Icon` FROM `sys_acl_levels` WHERE `ID` = '{$iMembershipId}' LIMIT 1";
	   	return $this->$sMethod($sSql);
	}
	function getSettings(){
	    $sMethod = "getRow";
        $sSql = "SELECT * FROM `{$this->_sPrefix}settings` LIMIT 1";		
	   	return; //$this->$sMethod($sSql);
	}
	function getSetting($sSetting){
        //$sSetVal = db_value("SELECT `{$sSetting}` FROM `{$this->_sPrefix}settings` LIMIT 1");		
	   	return; // $sSetVal;
	}
	function userAcl($iId){
        $sSql = db_res("SELECT * FROM sys_acl_levels_members WHERE `IDMember` = '{$iId}'");
		$iRows = mysql_num_rows($sSql);
		return $iRows;
	}
	function getMembershipLevelId($iId){
	    $sMethod = "getRow";
        $sSql = "SELECT * FROM `sys_acl_levels_members` WHERE IDMember = '".$iId."' LIMIT 1";
		$aResult = $this->$sMethod($sSql);	
        return $aResult['IDLevel'];
    }
	function getMemId($iRate){
		$iMemId = db_value("SELECT `membership_id`"
		. "\n FROM `dol_subs_rates` WHERE `id` = '".$iRate."'"
		." \n LIMIT 1");
		return $iMemId;
	}
	function clearMembershipInfo($iId){
		$sMethod = "query";
		$sSql1 = "DELETE FROM `sys_acl_levels_members` WHERE `IDMember` = '".$iId."'";
		$sSql2 = "DELETE FROM `sys_acl_actions_track` WHERE `IDMember` = '".$iId."'";
		$this->$sMethod($sSql1);		
		$this->$sMethod($sSql2);	
		return;
	}
	function createMembership($iId, $iIdLevel){
		$sSql = db_res("
		INSERT `sys_acl_levels_members` (IDMember, IDLevel, DateStarts, DateExpires, TransactionID)
		VALUES ($iId, $iIdLevel, NOW(), NULL, '$transactionID')");
		return $sSql;		
	}
	function setFreeMembership($iId,$iMembId){
		$sSubId = 'FREE-'.rand(100,30000);
		$aFreeLevels = array(1,2,3);
		$aFreePriceInfo	= $this->getMembershipPriceInfo($iMembId);
		if($aFreePriceInfo['Unit'] == 'Months'){
			$iMembershipDays = $aFreePriceInfo['Length']*31;
		}else{
			$iMembershipDays = $aFreePriceInfo['Length'];
		}
		if (!in_array($iMembId, $aFreeLevels)){
			setMembership($iId, $iMembId, $iMembershipDays, true, $sSubId);
		}		
	}
	function getPageCaption($iObject){
		$iPageCaption = db_value("SELECT `Caption`"
		. "\n FROM `sys_menu_top` WHERE `ID` = '".$iObject."'"
		." \n LIMIT 1");
		return $iPageCaption;
	}
	function getMemSince(){
		$iId = $_COOKIE['memberID'];
		$sDateReg = $this->getOne("SELECT DateReg FROM `Profiles` WHERE ID='{$iId}'");
		$sFixedDate = strtotime( $sDateReg );
		return date( 'M/d/Y', $sFixedDate );
	}

	function getSubStart(){
		$iId = $_COOKIE['memberID'];
		$sStartDate = $this->getOne("SELECT DateStarts FROM `sys_acl_levels_members` WHERE IDMember='{$iId}' ORDER BY DateStarts DESC");
		if (empty($sStartDate)){
			return $this->getMemSince();
		}else{
			$iStartDateSecs = strtotime( $sStartDate );
			return date( 'M/d/Y', $iStartDateSecs );
		}
	}


	function getPaymentProc(){
		return;
		$aSettings = $this->getSettings();
		$sProcessor = $aSettings['payment_proc'];
		switch ($sProcessor)
			{ 
				case 'paypal':
					return 'Paypal';
					break;
				case 'authorize':
					return 'Authorize.net';
					break;
				case 'alertpay':
					return 'Alertpay';
					break;
				case 'moneybookers':
					return 'MoneyBookers';
					break;
				default: 'paypal';
			}

	}
	function getSubEnd(){
		$iId = $_COOKIE['memberID'];
		$sExpDate = $this->getOne("SELECT DateExpires FROM `sys_acl_levels_members` WHERE IDMember='{$iId}' ORDER BY DateExpires DESC");
		if (empty($sExpDate)){
			return 'Never';
		}else{
			$iExpDateSecs = strtotime( $sExpDate );
			return date( 'M/d/Y', $iExpDateSecs );
		}
		
	}
	function getMemLevel(){
		$iId = $_COOKIE['memberID'];
		$sResAcl = db_res("SELECT * FROM `sys_acl_levels_members` WHERE `IDMember` = '{$iId}'");
		$aRowACL = mysql_fetch_array($sResAcl);

		$iCheckAcl = mysql_num_rows($sResAcl);
	
		// User is not in ACL
		if ($iCheckAcl == '0'){
			return $this->getOne("SELECT `Name` FROM `sys_acl_levels` WHERE ID='2'");			
		// User is in ACL		
		}else{
			$iIdLevel = $aRowACL['IDLevel'];
			return $this->getOne("SELECT `Name` FROM `sys_acl_levels` WHERE ID='{$iIdLevel}'");	
		}

	}
	function getCurrentMembershipLevel($iId){
		$sql = $this->getOne("SELECT IDLevel FROM `sys_acl_levels_members` WHERE IDMember='{$iId}' ORDER BY `DateStarts` DESC LIMIT 1");
		$IDLevel = $sql;
		if (empty($IDLevel)){
			return '2';	
	    }else{
			return $IDLevel;
		}

	}
    function getSettingsCategory() {
        return $this->getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Dolphin Subscriptions' LIMIT 1");
    }    
	function getUserCase($iId){
		$iRole = db_value("SELECT `Role` FROM `Profiles` WHERE `ID` = '{$iId}' LIMIT 1");
		if($iId == '0')
			return 'guest';
		if($iRole == '3')
			return 'admin';
		if($iRole == '1')
			return 'member';
	}
	//--- Admin functions ---//

	function getProfileArr($iUserId){
		$aProfile = db_arr("SELECT * FROM `Profiles` WHERE `ID` = '{$iUserId}' LIMIT 1");
		return $aProfile;
	}
	function getUserRole($iUserId){
		$iRole = db_value("SELECT `Role` FROM `Profiles` WHERE `ID` = '{$iUserId}' LIMIT 1");
		return $iRole;
	}
	function setUserStatus($iId,$sStatus){
		$sRes = db_res("UPDATE `Profiles` SET `Status` = '{$sStatus}' WHERE `ID` = '{$iId}' LIMIT 1");
		return $sRes;
	}
	function updatePaymentProcessor($aVars){
		$sRes = db_res("UPDATE `dol_subs_settings` SET 
				`payment_proc` 		= '{$aVars['payment_proc']}',
				`alertpay_id` 		= '{$aVars['alertpay_id']}',
				`ap_securitycode` 	= '{$aVars['ap_securitycode']}',
				`an_login` 			= '{$aVars['an_login']}',
				`an_transkey` 		= '{$aVars['an_transkey']}',
				`an_test` 			= '{$aVars['an_test']}',
				`an_api` 			= '{$aVars['an_api']}',
				`an_ssl` 			= '{$aVars['an_ssl']}',
				`paypal_id` 		= '{$aVars['paypal_id']}', 
				`sandbox` 			= '{$aVars['sandbox']}',
				`pp_custom_field` 	= '{$aVars['pp_custom_field']}',
				`moneybookers_id` 	= '{$aVars['moneybookers_id']}'
				WHERE `ID` = '1' LIMIT 1");
		return $sRes;
	}
	function updateDataForwarding($aVars){
		$sRes = db_res("UPDATE `dol_subs_settings` SET 
			`data_forward_1` 	= '{$aVars['data_forward_1']}',
			`data_forward_2` 	= '{$aVars['data_forward_2']}'
			WHERE `ID` = '1' LIMIT 1");
		return $sRes;
	}
	function updateUserManagementSettings($aVars){
		$sRes = db_res("UPDATE `dol_subs_settings` SET 
				`default_memID` 	= '{$aVars['default_memID']}',
				`require_mem` 		= '{$aVars['require_mem']}',
				`redirect_guests` 	= '{$aVars['redirect_guests']}',
				`disable_downgrade` = '{$aVars['disable_downgrade']}',
				`disable_upgrade` 	= '{$aVars['disable_upgrade']}'
				WHERE `ID` = '1' LIMIT 1");
		return $sRes;
	}
	function updateMembershipInfo($aVars,$sIconPath){
		if(file_exists($sIconPath)){
			$sIcon = pathinfo($sIconPath,PATHINFO_BASENAME);
			$sUpdateLevel = db_res("UPDATE `sys_acl_levels` SET 
					`Name` 			= '{$aVars['Name']}',
					`Icon` 			= '{$sIcon}',
					`Description` 	= '{$aVars['Description']}',
					`Purchasable` 	= '{$aVars['Purchasable']}',
					`Free` 			= '{$aVars['Free']}',
					`Trial` 		= '{$aVars['Trial']}',
					`Trial_Length` 	= '{$aVars['Trial_Length']}',
					`Order` 		= '{$aVars['Order']}',
					`Active`		= 'yes',
					`Removable`		= 'yes'
					WHERE `ID` = '{$aVars['Membership']}' LIMIT 1");
		}else{
			$sUpdateLevel = db_res("UPDATE `sys_acl_levels` SET 
					`Name` 			= '{$aVars['Name']}',
					`Description` 	= '{$aVars['Description']}',
					`Purchasable` 	= '{$aVars['Purchasable']}',
					`Free` 			= '{$aVars['Free']}',
					`Trial` 		= '{$aVars['Trial']}',
					`Trial_Length` 	= '{$aVars['Trial_Length']}',
					`Order` 		= '{$aVars['Order']}',
					`Active`		= 'yes',
					`Removable`		= 'yes'
					WHERE `ID` = '{$aVars['Membership']}' LIMIT 1");
		}
		$sClearPrice 	= db_res("DELETE FROM `sys_acl_level_prices` WHERE `IDLevel` = '{$aVars['Membership']}'");
		$sUpdatePrice 	= db_res("INSERT INTO `sys_acl_level_prices` SET
				`IDLevel` 		= '{$aVars['Membership']}',
				`Price` 		= '{$aVars['Price']}',
				`Unit` 			= '{$aVars['Unit']}',
				`Length` 		= '{$aVars['Length']}',
				`Auto` 			= '{$aVars['Auto']}'");
	}
	function createMembershipLevel($aVars,$sIconPath){
		$sIcon = pathinfo($sIconPath,PATHINFO_BASENAME);
		$rCreateLevel = db_res("INSERT INTO `sys_acl_levels` SET 
			`Name` 			= '{$aVars['Name']}',
			`Icon` 			= '{$sIcon}',
			`Description` 	= '{$aVars['Description']}',
			`Purchasable` 	= '{$aVars['Purchasable']}',
			`Free` 			= '{$aVars['Free']}',
			`Active`		= 'yes',
			`Removable`		= 'yes'");
		$iMembId = db_value("SELECT LAST_INSERT_ID()");
		$rCreateLevelPrice  = db_res("INSERT INTO `sys_acl_level_prices` SET
			`IDLevel` 		= '{$iMembId}',
			`Days` 			= '{$aVars['Days']}',
			`Price` 		= '{$aVars['Price']}',
			`Unit` 			= '{$aVars['Unit']}',
			`Length` 		= '{$aVars['Length']}',
			`Auto` 			= '{$aVars['Auto']}'");	
		if(mysql_error()){
			return false;
		}	
	}
	function deleteMembershipLevel($iMembId){
		$this->query("DELETE FROM `sys_acl_levels` WHERE `ID` = '{$iMembId}'");
		$this->query("DELETE FROM `sys_acl_levels_members` WHERE `IDLevel` = '$iMembId'");
	}
	function activateMembershipLevel($iMembId){
		$this->query("UPDATE `sys_acl_levels` SET `Active` = 'yes' WHERE `ID` = '{$iMembId}'");
	}
	function deactivateMembershipLevel($iMembId){
		$this->query("UPDATE `sys_acl_levels` SET `Active` = 'no' WHERE `ID` = '{$iMembId}'");
	}
	function updateMembershipActions($aVars,$iMembId){
		$aActions = $aVars['actions'];
		$sClean = db_res("DELETE FROM `sys_acl_matrix` WHERE `IDLevel` = '{$iMembId}'");	
		if(is_array($aActions)){
			foreach($aActions as $k => $v){
				$sInsert = db_res("INSERT INTO `sys_acl_matrix` SET `IDLevel` = '{$iMembId}', `IDAction` = '{$v}'");
			}
		}
		if(is_array($aVars)){
			foreach($aVars as $k => $v){
		 		if (isset($k) && stristr ($k,'num') != FALSE){
					$iActionId = str_replace("num",' ',$k);
					if($v && $v != '0'){
						db_res("UPDATE `sys_acl_matrix` 
						SET `AllowedCount` = '{$v}'
						WHERE `IDLevel` = '{$iMembId}' AND `IDAction` = '{$iActionId}'");
					}			
				}
		 		if (isset($k) && stristr ($k,'res') != FALSE){
					$iActionId = str_replace("res",' ',$k);	
					if($v && $v != '0'){
						db_res("UPDATE `sys_acl_matrix` 
						SET `AllowedPeriodLen` = '{$v}'
						WHERE `IDLevel` = '{$iMembId}' AND `IDAction` = '{$iActionId}'");
					}
				}
			}
		}
	}
	function getActions(){
		$aActions = $GLOBALS['MySQL']->getAll(
					"SELECT `ta`.`ID` AS `id`,
						 	`ta`.`Name` AS `title` 
						FROM `sys_acl_actions` AS `ta` 
					ORDER BY `ta`.`Name`");
		return $aActions; 
	}
	function getActiveActions($iMembId){
		$aActions = $this->getAllWithKey(
				    "SELECT `ta`.`ID`   AS `id`,
				            `ta`.`Name` AS `title`,
				            `tm`.`AllowedCount`,
				            `tm`.`AllowedPeriodLen`
				       FROM `sys_acl_actions` AS `ta` 
				             LEFT JOIN `sys_acl_matrix` AS `tm` 
				             ON `ta`.`ID`=`tm`.`IDAction` 
				             LEFT JOIN `sys_acl_levels` AS `tl` 
				             ON `tm`.`IDLevel`=`tl`.`ID` 
				      WHERE `tl`.`ID`='" . $iMembId . "' 
				      ORDER BY `ta`.`Name`", "id");
		return $aActions;
	}
	function getNumAllowed($iLevelId,$iActionId){
		return db_value("SELECT `AllowedCount` FROM `sys_acl_matrix` WHERE `IDLevel` = '{$iLevelId}' AND `IDAction` = '{$iActionId}' LIMIT 1");
	}
	function getNumReset($iLevelId,$iActionId){}
	
	/**
	 * Gather all subscribers
	 */
	function getSubscriptions(){
		$aSubs = $this->getAll(
			"SELECT
				`Profiles`.`ID`,
				`Profiles`.`NickName`,
				`Profiles`.`Status`,
				`Profiles`.`Email`,
				`Profiles`.`DateReg`,
				`sys_acl_levels_members`.`DateStarts`,
				`sys_acl_levels_members`.`DateExpires`,
				`sys_acl_levels_members`.`TransactionID`,
				`sys_acl_levels`.`Name`
			FROM
				`Profiles`,
				`sys_acl_levels_members`,
				`sys_acl_levels`
			WHERE
				`Profiles`.`ID` = `sys_acl_levels_members`.`IDMember` AND
				`sys_acl_levels_members`.`IDLevel` = `sys_acl_levels`.`ID`");
		return $aSubs;
	}
	function removeSubscribers($aVars){
		foreach($aVars as $k => $v){
			$this->query("DELETE FROM `sys_acl_levels_members` WHERE `IDMember` = '{$v}'");
		}
	}
	function getTopMenuItems(){
		$rItems = db_res(
			"SELECT `ID`, `Name` 
			FROM `sys_menu_top` 
			WHERE `Active`= '1' 
			AND `Type`='top' 
			ORDER BY `Order`");
		return $rItems;
	}
	function getSysMenuItems(){
		$rItems = db_res(
			"SELECT `ID`, `Name` 
			FROM `sys_menu_top` 
			WHERE `Active` = '1 '
			AND `Type` = 'system' 
			ORDER BY `Order`");
		return $rItems;
	}
	function getMenuItemName($iMenuItemId){
		return db_value("SELECT `Name` FROM `sys_menu_top` WHERE `ID` = {$iMenuItemId} LIMIT 1");	
	}
	function getMenuAccessLevels($iMenuItemId) {		
		//$aLevels = $this->getOne("SELECT `mlevels` FROM `dol_subs_menu_access` WHERE `menu_id` = {$iMenuItemId} LIMIT 1");
		return; //unserialize($aLevels);
	}
	function setMenuAccessLevels($aVars) {
		if(!$aVars['mlevels']){
			$sMemLevels = serialize(array());
		}else{
			$sMemLevels = serialize($aVars['mlevels']);
		}		
		$this->query("REPLACE `dol_subs_menu_access` SET `menu_id` = '{$aVars['menu_item']}', `mlevels` = '{$sMemLevels}'");
	}
	function clearCache($sPrefix, $sPath) {
	    if($rHandler = opendir($sPath)) {
	        while(($sFile = readdir($rHandler)) !== false)
	            if(substr($sFile, 0, strlen($sPrefix)) == $sPrefix)
	                @unlink($sPath . $sFile);
	    }
	}
	//--- URL Rules ---//
	function addNewURLRule($aVars){
		$aMemLevels = serialize($aVars['mlevels']);		
		$sInsert = db_res("INSERT INTO `dol_subs_url_rules` SET `mlevels` = '{$aMemLevels}', `url` = '{$aVars['rule_url']}'");
	}
	function getURLRules(){
	    $sMethod = "getAll";
        $sSql = "SELECT * FROM `dol_subs_url_rules`";
        return $this->$sMethod($sSql);
	}
	function removeURLRules($aVars){
		foreach($aVars as $k => $v){
			$this->query("DELETE FROM `dol_subs_url_rules` WHERE `id` = '{$v}'");
		}
	}
	function getRestrictedMemLevelsForURL($url){
	    //$sMethod = "getOne";

		//$clean_url = $this->clean_for_sql($url);

        //$sSql = "SELECT `mlevels` FROM `dol_subs_url_rules` WHERE `url` = '{$clean_url}'";
        return;// $this->$sMethod($sSql);
	}

	function clean_for_sql($str)
	{
		
		$str = str_replace(array("\\n", "\\r"), array("\n", "\r"), $str);
	
		//$str = stripslashes($str);
		
		if (get_magic_quotes_runtime() == 1)
			$str = mysql_real_escape_string(stripslashes($str));
		else
			$str = mysql_real_escape_string((stripslashes($str)));
		
		
		return $str;
		
	}

}