<?php
/***************************************************************************
* 
*     copyright            : (C) 2009 AQB Soft
*     website              : http://www.aqbsoft.com
*      
* IMPORTANT: This is a commercial product made by AQB Soft. It cannot be modified for other than personal usage.
* The "personal usage" means the product can be installed and set up for ONE domain name ONLY. 
* To be able to use this product for another domain names you have to order another copy of this product (license).
* 
* This product cannot be redistributed for free or a fee without written permission from AQB Soft.
* 
* This notice may not be removed from the source code.
* 
***************************************************************************/

bx_import('BxDolModuleDb');

class AqbCreditsDb extends BxDolModuleDb {	
	/*
	 * Constructor.
	 */
	
	function AqbCreditsDb(&$oConfig) {
		parent::BxDolModuleDb($oConfig);
		$this -> _oConfig = &$oConfig;
	}
	
	function isPointsSystemInstalled(){
		return (int)$this -> getOne("SELECT COUNT(*) FROM `sys_modules` WHERE `uri` = 'aqb_points' LIMIT 1") == 1; 
	}
	
	function getActionsList($iMembershipID){
		return $this -> getPairs("SELECT `ta`.`ID` AS `id`, `ta`.`Name` AS `title`,
									   `tm`.`IDAction` 
									   FROM `sys_acl_actions` AS `ta`					   
									   LEFT JOIN `sys_acl_matrix` AS `tm` ON `ta`.`ID`=`tm`.`IDAction` 
									   LEFT JOIN `sys_acl_levels` AS `tl` ON `tm`.`IDLevel`=`tl`.`ID` 
									   WHERE `tm`.`IDLevel` = '{$iMembershipID}'", 'id', 'title');
	}
	
	function getMembershipActions(){
		$aMemberships = getMemberships(); 
		unset($aMemberships[1]); 
		
		$aFullActionsList = $this -> getPairs("SELECT `ta`.`ID` AS `id`, `ta`.`Name` AS `title` FROM `sys_acl_actions` AS `ta` ", 'id', 'title');
		$aResultActionsList = array();
		foreach($aMemberships as $iKey => $sValue){ 
			$aMembershipInfo = getMembershipInfo($iKey);
	
			if ($aMembershipInfo['Active'] == 'yes'){
				$aList = $this -> getActionsList($iKey);
				$aResultActionsList += array_diff_key($aFullActionsList, $aList);
			}				
		}
	
		if (!empty($aResultActionsList)) ksort($aResultActionsList);
		return $aResultActionsList;
	}
	
	function getMembershipActionsNotAvailForMe($iProfileID){
		$aMemberMembershipInfo = getMemberMembershipInfo($iProfileID);
		$aMembershipInfo = getMembershipInfo($aMemberMembershipInfo['ID']);
	
		if ($aMembershipInfo['Active'] != 'yes') return array();
		
		$aList = $this -> getActionsList($aMemberMembershipInfo['ID']);
		$aFullList = $this -> getPairs("SELECT `ta`.`ID` AS `id`, `ta`.`Name` AS `title` FROM `sys_acl_actions` AS `ta` ", 'id', 'title');
		return array_diff_key($aFullList, $aList);		
	}
	
	function getAvailableActionsForCredits($iProfileID){
		$aList = $this -> getMembershipActionsNotAvailForMe($iProfileID);
		$aAllActons = $this -> getActions();
		
		$aActiveActions = array();
		foreach($aAllActons as $iKey => $aValue){
			if ($aValue['active'] && $aValue['credits'] && $aList[$iKey]) $aActiveActions[$iKey] = $aValue['credits'];
		}

		return $aActiveActions;		
	}
	
	function isActionAvailable($iProfileID, $iActionID){
		$iTime = $this -> _oConfig -> lifetimePeriod();
		
		if ($iTime)
			$sWHERE = " AND `time` >= UNIX_TIMESTAMP(SUBDATE(NOW(), INTERVAL {$iTime} DAY))";

		return (int)($this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}history` WHERE `action_id` = '{$iActionID}' AND `action` = 'spent' AND `member_id` = '{$iProfileID}' {$sWHERE}") > 0);
	}
		
	function getActions(){
		$sActions = $this -> getOne("SELECT `VALUE` FROM `sys_options` WHERE `Name` = 'aqb_credits_actions_settings'");
		return $sActions ? unserialize($sActions) : array(); 
	}
	
	function saveActions(&$aActions){
		$aMemActions = array();
		$aForm['inputs'] = array();
		$aMembership = array();
		
		$aMembershipActions = $this -> getMembershipActions(); 
		
		$aMembershipInfo = array();		
		foreach($aMembershipActions as $iKey => $sValue){ 
			$aActionsList["{$iKey}"] = array('active' => (int)($aActions["active_{$iKey}"] <> 0), 'credits' => (float)$aActions["credits_{$iKey}"]); 
		}
		
		$sActionsList = process_db_input(serialize($aActionsList));
		return $this -> query("UPDATE `sys_options` SET `VALUE` = '{$sActionsList}' WHERE `Name` = 'aqb_credits_actions_settings'");		
	}
	
	function getSettingsCategory() {
        return (int)$this -> getOne("SELECT `ID` FROM `sys_options_cats` WHERE `name` = 'Credits System' LIMIT 1");
    }
	
	function getCreditsTable(&$aParams){
		if (!($iMember = (int)$aParams['member_id'])) return '';
	
		if (empty($aParams['view_order'])) $sOrderBy = '`date` ASC'; 
			else $sOrderBy = "`{$aParams['view_order']}` {$aParams['view_type']}"; 
   

		$sQuery = " 
			SELECT *
		    FROM `{$this -> _sPrefix}history` 
	    	WHERE `member_id` = '{$iMember}'
			ORDER BY  {$sOrderBy}			
	    	LIMIT " . $aParams['view_start'] . ", " . $aParams['view_per_page'].'';

		$aParams['count'] = (int)$this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}history` WHERE `member_id` = '{$iMember}'");
		
		
		return $this -> getAll($sQuery);
	}
	
	function getMembershipActionName($iId){
		$sName = $this -> getOne("SELECT `Name` FROM `sys_acl_actions` WHERE `id` = '{$iId}'");
		if (!$sName) return _t('_aqb_credtis_action_doesnt_exist');
		
		$sName= str_replace(' ', '_', $sName);
		
		return _t("_mma_{$sName}");
	}
	
	function getTotalCredits($iMemberID){
		return (int)$this -> getOne("SELECT SUM(`Number`) FROM `{$this->_sPrefix}history` WHERE `member_id` = '{$iMemberID}'");
	}

	function assignCredits(&$aInfo){
		if (empty($aInfo)) return false;
		
		$sSet = 'SET `time` = UNIX_TIMESTAMP(), ';
		foreach($aInfo as $sKey => $mixedVal){
			$sSet .= "`{$sKey}` = '{$mixedVal}',";
		}
		
		$sSet = trim($sSet, ',');		
		return $this -> query("INSERT INTO `{$this->_sPrefix}history` {$sSet}") ? $this -> lastId() : 0;
	}
	
	function createPendingTransaction(&$aParams){
		if (!(int)$aParams['profile'] || !(int)$aParams['amount'] || !(float)$aParams['price']) return false;
		
		if (isset($aParams['type'])) $sType = ", `type` = '{$aParams['type']}'";
		$iResult = $this -> query("INSERT INTO `{$this->_sPrefix}transactions` SET `price` = '{$aParams['price']}', `date` = UNIX_TIMESTAMP(), `amount` = '{$aParams['amount']}', `buyer_id` = '{$aParams['profile']}' {$sType}");
		return $iResult ? $this -> lastId() : 0;
	}

   function getCreditsTransactions($iProfileId){
   	   return $this -> getAll("SELECT * FROM `{$this->_sPrefix}transactions` WHERE `buyer_id` = '{$iProfileId}' AND `status` = 'active' AND `type` = 'buy'");
   }
   
   function getCreditsTransactionItem($iProfileId, $iItem){
   	   return $this -> getRow("SELECT * FROM `{$this->_sPrefix}transactions` WHERE `buyer_id` = '{$iProfileId}' AND `id` ='{$iItem}' AND `type` = 'buy' LIMIT 1");
   }
   
   function boughtCredits($iClientId, $iItemId, $iItemCount, $sOrderId){
		$aInfo = $this -> getCreditsTransactionItem($iClientId, $iItemId);		
		$iTransactionId = (int)$this -> query("UPDATE `{$this->_sPrefix}transactions` SET `date` =  'UNIX_TIMESTAMP()', `status` = 'active', `tnx` = '{$sOrderId}'  WHERE `id` = '{$iItemId}' AND `status` = 'pending'");
		
		$aResult = array('price' => $aInfo['price'], 'amount' => $aInfo['amount']);		
		return $iTransactionId ? $aResult : array(); 
	}
	
  function updateCreditsId($CreditsID, $iId){
		return $this -> query("UPDATE `{$this->_sPrefix}transactions` SET `credit_id` =  '{$CreditsID}' WHERE `id` = '{$iId}'");
  }  
  
  function cancelCreditsOrder($sOrderId){
		$aOrders = $this -> getRow("SELECT * FROM `{$this->_sPrefix}transactions` WHERE `tnx` = '{$sOrderId}'");
	    if(empty($aOrders)) return false;
		
		return $this -> query("DELETE FROM `{$this->_sPrefix}history` WHERE `id` = '{$aOrders['credit_id']}'") && $this -> query("DELETE FROM `{$this->_sPrefix}transactions` WHERE `tnx` = '{$sOrderId}'");		
  }
  
  function deleteProfileHistory($iProfileId){
		$this -> query("DELETE FROM `{$this->_sPrefix}history` WHERE `member_id` = '{$iProfileId}'") && $this -> query("DELETE FROM `{$this->_sPrefix}transactions` WHERE `buyer_id` = '{$iProfileId}'");
  }
   
}
?>