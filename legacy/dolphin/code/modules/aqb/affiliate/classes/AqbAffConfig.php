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

bx_import('BxDolConfig');

class AqbAffConfig extends BxDolConfig {
    var $_oDb;
	var $_sDateFormat;
	var $_aCurrency;
	var $_sBannersFolder;
	var $_iDefaultDeep;
	var $_sRefPrefix;

	/**
	 * Constructor
	 */

    function AqbAffConfig($aModule) {
	    parent::BxDolConfig($aModule);
		$this -> _sDateFormat = '%d.%m.%y %H:%i';
		$this -> _aCurrency = BxDolService::call('payment', 'get_currency_info');
		$this -> _sBannersFolder = 'data/';
		$this -> _sRefPrefix = 'refaqb';
		$this -> _sBaseUrlLink = 'index.php';
		$this -> _iDefaultDeep = 15;
	}
	
	function getBasePageUrl(){
	    return BX_DOL_URL_ROOT . $this -> _sBaseUrlLink;	
	}
	
	function init(&$oDb) {
		$this->_oDb = &$oDb;
	}
	
	function getBannersPath(){
		return $this -> getHomePath() . $this -> _sBannersFolder;
	}
	
	function getBannersUrl(){
		return $this -> getHomeUrl() . $this -> _sBannersFolder;
	}
	
	function getReferralLink($iMemberID){
		$sLink = $this -> _oDb -> getParam('aqb_affiliate_referral_link');
		return $sLink . "?{$this -> _sRefPrefix}={$iMemberID}";
	}
	
	function getCurrencySign(){
		return $this -> _aCurrency['sign'];
	} 
	
	function getPerPageHistory(){
		return (int)$this -> _oDb -> getParam('aqb_aff_perpage_browse_history');
	}
	
	function getDateFormat(){
		return $this -> _sDateFormat;
	}
	
	function getPerPageReferrals(){
		return (int)$this -> _oDb -> getParam('aqb_aff_perpage_browse_referrals');
	}

	function getMaximumEmails(){
		return (int)$this -> _oDb -> getParam('aqb_aff_maximum_number_of_send_emails');
	}
	
	function isAllowToSendInvitations(){
		return $this -> _oDb -> getParam('aqb_aff_allow_sent_invitations');
	}
	
	function isAllowToAddMembersMessage(){
		return $this -> _oDb -> getParam('aqb_aff_allow_their_message');
	}
	
	function getMaximumSymbolsForMessage(){
		return (int)$this -> _oDb -> getParam('aqb_aff_maximum_number_of_emails_symbols');
	}
	
	function isUsingQueue(){
		return $this -> _oDb -> getParam('aqb_aff_use_queue');
	}
	
	function isPermalinkEnabled(){
	  return $this -> _oDb -> getParam('permalinks_module_aqb_points') == 'on';	
	}	
	
	function isReferralsEnabled(){
	    return $this -> _oDb -> getParam('aqb_referral_turn_on') == 'on';		
	}
	
	function isAffiliateEnabled(){
	    return $this -> _oDb -> getParam('aqb_affiliate_turn_on') == 'on';		
	}
	function getUniqueHistoryDays(){
		return (int)$this -> _oDb -> getParam('aqb_aff_unique_history_days');
	}
	
	function isForcedMatrixEnabled(){
		return $this -> _oDb -> getParam('aqb_forced_matrix') == 'on';
	}
	
	function getDefaultDeep(){
		return (int)$this -> _iDefaultDeep;
	}

	function isReferralsBlockForIndexEnabled(){
		return $this -> _oDb -> getParam('aqb_aff_enable_ref_index') == 'on';
	}
	
	function getPerPageOnRefIndexBlock(){
		return (int)$this -> _oDb -> getParam('aqb_aff_per_page_ref_index');
	}
	
	function getPerPageOnInvitedMemBlock(){
		return (int)$this -> _oDb -> getParam('aqb_aff_per_page_invited_profile');
	}
	
	function isMyInvitedMembersBlockForProfileEnabled(){
		return $this -> _oDb -> getParam('aqb_aff_enable_invited_profile') == 'on';
	}
	
	function showMyBranchMembersEnabled(){
		return $this -> _oDb -> getParam('aqb_aff_show_my_branch') == 'on';
	}
	
	function getCredentials(){
		return array(
					   'user' => $this -> _oDb -> getParam('aqb_aff_pp_api_user'), 
					   'pwd' => $this -> _oDb -> getParam('aqb_aff_pp_api_pwd'), 
					   'signature' => $this -> _oDb -> getParam('aqb_aff_pp_api_signature')
					);
	}
	
	function IsCredentialsInstalled(){
		$aCredentails = $this -> getCredentials();
		
		if ($aCredentails['user'] && $aCredentails['pwd'] && $aCredentails['signature']) return true;
		return false;
	}
	
	function getCommissionLimitation(){
		return (float)$this -> _oDb -> getParam('aqb_aff_limit_commission');
	}
	
	function getPagesWithRefLink(){
		return $this -> _oDb -> getParam('aqb_aff_block_with_invitation_links');
	}
}   
?>