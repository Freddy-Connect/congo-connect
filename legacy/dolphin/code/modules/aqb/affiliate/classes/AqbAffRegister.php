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

require_once('AqbAffDb.php');

class AqbAffRegister extends AqbAffDb{
  var $_sPrefix = 'aqb_aff_', $sIP = '';
  
	function AqbAffRegister(){
		$oConfig = null;
		parent::AqbAffDb($oConfig);
		$this -> sIP = $_SERVER['REMOTE_ADDR'];
	}
	
	function registerMember($iId, $iMemberId, $sInviterType = 'banner', $sAction = 'impression'){
		$aGetSiteIp = parse_url($GLOBALS['site']['url']);
		
		if ($this -> sIP == $aGetSiteIp['host']) return false;
		
		if (!$this -> checkForExist($iId, $this -> sIP, $iMemberId, $sAction)){
			$bVal = $this -> query("REPLACE INTO `{$this->_sPrefix}all_history` SET `ip` = '{$this -> sIP}', `action_type` = '{$sAction}', `banner_id` = '{$iId}', `owner_id`='{$iMemberId}',`date` = UNIX_TIMESTAMP()");
			
			if ($sAction == 'join') $this -> upgradeMembership($iMemberId);
			
			if (!$this -> isForcedMatrixEnabled()) return $bVal && $this -> updateJournal($iId, $iMemberId, $sInviterType, $sAction);
			return $bVal;			
		}
		
		return false;	
	}	

	function addUpgradedMemberOnReferralBalance($iOwnerID, $fPrice, $iBannerId = 0, $iMemberId = 0){
		if (!(float)$fPrice || !(int)$iOwnerID) return false; 
		
		$iProcent = $this -> getPriceForAction('upgrade', 'price', $iOwnerID, $iBannerId);
						
		$fPrice = (float)((float)$fPrice * (int)$iProcent/100);
	
		$iPoints = 0;
		if ($this -> isPointsSystemInstalled()) 
			$iPoints = $this -> getPriceForAction('upgrade', 'points', $iOwnerID, $iBannerId);
		
		if (!(float)$fPrice && !(int)$iPoints) return false;
		
		$sInviterType = $iBannerId ? 'banner' : 'referral';
		$iId = $iBannerId ? $iBannerId : $iMemberId;
		
		return $this -> query("INSERT INTO `{$this->_sPrefix}journal` SET `member_id` = '{$iOwnerID}', `inviter_type` = '{$sInviterType}', `sum_price` = '{$fPrice}', `sum_points` = '{$iPoints}' , `inviter` = '{$iId}', `action_type` = 'upgrade', `last_update` = UNIX_TIMESTAMP()");
	}
	
   function updateJournal($iId, $iMemberId, $sInviterType = 'banner', $sAction = 'impression'){
		$fPrice = $this -> getPriceForAction($sAction, 'price', $iMemberId, $sInviterType == 'banner' ? $iId : 0);	
			
		if ($this -> isPointsSystemInstalled())
			$iPrice = $this -> getPriceForAction($sAction, 'points', $iMemberId, $sInviterType == 'banner' ? $iId : 0);	else $iPrice = 0;
		
		if (!(float)$fPrice && !(int)$iPrice)	return false;
		
		if ($sAction !== 'join' && $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}journal` WHERE `member_id` = '{$iMemberId}' AND `inviter_type` = '{$sInviterType}' AND `inviter` = '{$iId}' AND `action_type` = '{$sAction}' LIMIT 1") == 1){ 
				return $this -> query("UPDATE `{$this->_sPrefix}journal` SET `count` = `count` + 1, `sum_price` = `sum_price` + {$fPrice}, `sum_points` = `sum_points` + {$iPrice}, `last_update` = UNIX_TIMESTAMP() 
								   WHERE `member_id` = '{$iMemberId}' AND `inviter_type` = '{$sInviterType}' AND `inviter` = '{$iId}' AND `action_type` = '{$sAction}' LIMIT 1");
		}
		
		
		return $this -> query("INSERT INTO `{$this->_sPrefix}journal` SET `member_id` = '{$iMemberId}', `inviter_type` = '{$sInviterType}', `sum_price` = {$fPrice}, `sum_points` = '{$iPrice}' , `inviter` = '{$iId}', `action_type` = '{$sAction}', `last_update` = UNIX_TIMESTAMP()");
	}
	
  function checkForExist($iId, $sIp, $iMemberId, $sAction = 'impression'){
		return $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}all_history` WHERE `ip` = '{$this -> sIP}' AND `action_type` = '{$sAction}' AND `banner_id` = '{$iId}' LIMIT 1") == 1;
	}
  
  function getProfileInfo($iProfileId){
	 return $this -> getRow("SELECT `ID`, `Status` FROM `Profiles` WHERE `ID` = '{$iProfileId}' AND `Status` = 'Active' LIMIT 1");
  } 
	
  function addToReferralList($iRef, $iMember, $iBanner){
	 if ($this -> getParam('aqb_aff_make_invite_member_friends') == 'on') $this -> query("REPLACE INTO `sys_friend_list` SET `ID` = '{$iRef}', `Profile` = '{$iMember}', `Check` = '1'");
	 return $this -> query("REPLACE INTO `{$this->_sPrefix}referrals` SET `referral` = '{$iRef}', `member` = '{$iMember}', `date` = UNIX_TIMESTAMP(), `type` = '{$iBanner}'");
  }  
  
  function getCookieTime(){
	 return $this -> getParam('aqb_affiliate_cookie_days');
  }
  
  function isMembership($iID){
	 return $this -> getOne("SELECT COUNT(*) FROM `sys_modules` WHERE `id` = '{$iID}' AND `uri` = 'membership'") == 1;
  }
  
  function isInvited($iID){
	 return $this -> getRow("SELECT `referral`,`type` FROM `{$this->_sPrefix}referrals` WHERE `member` = '{$iID}'");
  }
  
  function updateReferrals($iReferral, $iMember, $iMembershipId){
	$aMembership = $this -> getRow("SELECT 
            `tl`.`ID` AS `ID`, 
            `tlp`.`Days` AS `Days`,
            `tl`.`Active` AS `Active`, 
            `tl`.`Purchasable` AS `Purchasable` 
        FROM `sys_acl_levels` AS `tl` 
        LEFT JOIN `sys_acl_level_prices` AS `tlp` ON `tl`.`ID`=`tlp`.`IDLevel` 
        WHERE `tlp`.`id`='{$iMembershipId}'");
	
	if(!is_array($aMembership) || empty($aMembership)) return false;
		
	return $this -> query("UPDATE `{$this->_sPrefix}referrals` SET `upgraded` = '{$aMembership['ID']}' WHERE `member` = '{$iMember}' AND `referral` = '{$iReferral}' LIMIT 1");
  }  
  
  function upgradeMembership($iMember){
		$iReferredNum = $this -> getOne("SELECT COUNT(*) FROM `{$this->_sPrefix}referrals` WHERE `referral` = '{$iMember}' LIMIT 1");
		$aMembership = $this -> getMemberMembershipInfo($iMember);
		if (empty($aMembership)) return false;
		
		$aInfo = $this -> getMemLevelsPricing($aMembership['ID']);
		
		if (!($aInfo['membership_level'] && ((int)$iReferredNum && (int)$aInfo['invited_members'] && ((int)$iReferredNum % (int)$aInfo['invited_members']) == 0))) return false;
		$aResult = split(':', $aInfo['membership_level']);

		return setMembership($iMember, (int)$aResult[0], (int)$aResult[1]);
  }
  
}
?>