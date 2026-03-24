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
bx_import('BxDolAlerts');
require_once('AqbAffRegister.php');
require_once('AqbAffMassPay.php');

class AqbAffAlertsResponse extends BxDolAlertsResponse {
	var $oRegistr;
	
	function AqbAffAlertsResponse() {
	    parent::BxDolAlertsResponse();
		$this -> oRegistr = new AqbAffRegister();
	}
	
	function response($oAlert) {
    	$sMethodName = '_process' . ucfirst($oAlert->sUnit) . ucfirst($oAlert->sAction);
		if((stristr($_SERVER['PHP_SELF'], $GLOBALS['admin_dir']) === FALSE || $sMethodName == '_processProfileDelete') && method_exists($this, $sMethodName)) $this -> $sMethodName($oAlert);
    }
 	
	function _processSystemBegin(&$oAlert){
		$iAffNum = $this -> oRegistr -> getParam('aqb_affiliate_turn_on') == 'on' ? (int)bx_get('affaqb') : 0;
		$iBannerId = (int)bx_get('b');
		
		$iReferral = $this -> oRegistr -> getParam('aqb_referral_turn_on') == 'on' ? (int)bx_get('refaqb') : 0;
		$iReferral = $iReferral ? $iReferral : $iAffNum;
						
		if ($iAffNum && $iBannerId) 
			$this -> oRegistr -> registerMember($iBannerId, $iAffNum, 'banner', 'click');
				
		$aProfile = $this -> oRegistr -> getProfileInfo($iReferral); 

		if ($iReferral && !empty($aProfile)){ 
			$aUrl = parse_url($GLOBALS['site']['url']);
			$sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
			$sHost = '';
			
			$iCookieTime = (int)$this -> oRegistr -> getCookieTime(); 
			$iCookieTime = $iCookieTime	? time() + 24 * 60 * 60 * $iCookieTime : 0;
			
			if ((int)$iAffNum){
				setcookie("affaqb", $iAffNum, $iCookieTime, $sPath, $sHost, false, true /* http only */);
				setcookie("affb", $iBannerId, $iCookieTime, $sPath, $sHost, false, true /* http only */);
			}	
			else
				setcookie("refaqb", $iReferral, $iCookieTime, $sPath, $sHost, false, true /* http only */);
		}

		if (stripos($_SERVER['QUERY_STRING'], 'payment/act_finalize_checkout/paypal') !== false && $_POST['txn_type'] == 'masspay'){
			$oMassPay = new AqbAffMassPay();
			$aResult = $oMassPay -> validateData($_POST);
			if ((int)$aResult['code'] == 1) $this -> oRegistr -> acceptMassPayment($_POST);
		}				
  	
	}
	
	function _processProfileJoin(&$oAlert){
		$iReferral = (int)$_COOKIE['refaqb'];
		$iReferralByB = (int)$_COOKIE['affaqb'];
		$iBId = (int)$_COOKIE['affb'];

		if (!(int)$iReferral && !(int)$iReferralByB) return false;		

		$aProfile = $this -> oRegistr -> getProfileInfo($iReferral ? $iReferral : $iReferralByB); 
		if (($iReferral || $iReferralByB) && !empty($aProfile)){ 
		  $this -> oRegistr -> addToReferralList($iReferral ? $iReferral : $iReferralByB, $oAlert -> iObject, $iReferral ? 0 : $iBId);
		  
		  if ($iReferral){
			$this -> oRegistr -> registerMember($oAlert ->iObject, $iReferral, 'referral', 'join');
		    setcookie("refaqb", 0, time() - 24 * 60 * 60 , $sPath, $sHost, false, true /* http only */);
		  }	
		  elseif ($iReferralByB && $iBId){ 	
			$this -> oRegistr -> registerMember($iBId, $iReferralByB, 'banner', 'join');
			setcookie("affaqb", $iAffNum, time() - 24 * 60 * 60, $sPath, $sHost, false, true /* http only */);
			setcookie("affb", $iBannerId, time() - 24 * 60 * 60, $sPath, $sHost, false, true /* http only */);
		  }	
		
		$iParent = $iReferral ? $iReferral : $iReferralByB;
		if ($this -> oRegistr -> isForcedMatrixEnabled($iParent)) {
			  $this -> oRegistr -> addNode($iParent, $oAlert -> iObject);	
			  $this -> oRegistr -> applyCommissionToMyBranch($oAlert -> iObject);
		   }
		}
	}
	
	function _processProfileDelete(&$oAlert){
		if ((int)$oAlert->iObject) $this -> oRegistr -> deleteProfileHistory($oAlert->iObject);
	}
	
	function _processProfileSet_membership(&$oAlert){
		if (!(int)$oAlert -> iSender || isset($_POST['doSetMembership'])) return '';
		$aInviterInfo = $this -> oRegistr -> isInvited((int)$oAlert->iSender);
		
		$bResult = true;		
		/* Freddy commentaire modification for compatiblity with martinboi module begin
		$fPrice = (float)$GLOBALS['VoucherPriceWithDiscount'] ? (float)$GLOBALS['VoucherPriceWithDiscount'] : $this -> oRegistr -> getPriceByMemLevel((int)$oAlert-> aExtras['mlevel'], (int)$oAlert-> aExtras['days']);
		*/
		/* Freddy modification for compatiblity with martinboi module begin*/
		if(!(float)$_REQUEST['mc_amount1'] && (int)$this -> oRegistr -> getOne("SELECT COUNT(*) FROM `mmp_payments` WHERE `txn_id` = '{$_REQUEST['txn_id']}'") == 1) return false;
		
		$fPrice = (float)$_REQUEST['mc_amount1'] ? (float)$_REQUEST['mc_amount1'] : $this -> oRegistr -> getPriceByMemLevel((int)$oAlert-> aExtras['mlevel'], (int)$oAlert-> aExtras['days']);
		
		
		
		
		if (!(float)$fPrice) return false;
		
		if (!empty($aInviterInfo)){
			$bMarixEnabled = $this -> oRegistr -> isForcedMatrixEnabled((int)$aInviterInfo['referral']);
			if ($bMarixEnabled) {	
				  $this-> oRegistr -> addNode($aInviterInfo['referral'], (int)$oAlert->iSender);
                  $bResult &= $this -> oRegistr -> applyCommissionToMyBranch((int)$oAlert->iSender, 'upgrade', (float)$fPrice); 
			}else $bResult &= $this -> oRegistr -> addUpgradedMemberOnReferralBalance($aInviterInfo['referral'], (float)$fPrice, (int)$aInviterInfo['type'], (int)$oAlert->iSender);	
		}else return false;
	
		if ($bResult) return $this -> oRegistr -> updateReferrals($aInviterInfo['referral'], (int)$oAlert -> iSender, (int)$oAlert-> aExtras['mlevel']);			
		
		return false;
	}	
}
?>