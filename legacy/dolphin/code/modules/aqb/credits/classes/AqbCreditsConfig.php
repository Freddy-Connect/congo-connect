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

class AqbCreditsConfig extends BxDolConfig {
	var $_oDb = null;
	
	function AqbCreditsConfig($aModule) {
	    parent::BxDolConfig($aModule);
		$this -> _aCurrency = BxDolService::call('payment', 'get_currency_info');
	}
	
	function init(&$oDb) {
		$this -> _oDb = &$oDb;
	}
	
	function getCurrencyCode(){
		return $this -> _aCurrency['code'];		
	}
    
	function getCurrencySign(){
		return $this -> _aCurrency['sign'];
	}
	
	function priceInPoints(){
		return (int)$this -> _oDb -> getParam('aqb_credits_price_points');
	}
	
	function priceInCurrency(){
		return (float)$this -> _oDb -> getParam('aqb_credits_price_currency');
	}
	
	function lifetimePeriod(){
		return (int)$this -> _oDb -> getParam('aqb_credits_time');
	}
	
	function allowToBuyCredits(){
		return $this -> _oDb -> getParam('aqb_credits_allow_to_buy') == 'on';
	}
		
	function allowToExchangeCredits(){
		return $this -> _oDb -> getParam('aqb_credits_allow_exchange_for_points') == 'on';
	}
	
	function getSiteId() {
		$iId = 0;
		switch($GLOBALS['site']['ver'] . '.' . $GLOBALS['site']['build']) {
			case '7.0.0':
				$iId = -1;
		}

		return $iId;
	}
	
	function getPerPageOnHistory(){
		return (int)$this -> _oDb -> getParam('aqb_credits_per_page_history');
	}
	
	function allowToBlockPopUp(){
		return $this -> _oDb -> getParam('aqb_credits_allow_to_hide_window') == 'on';
	}
	
	function getPopUpBlockPeriod(){
		return (int)$this -> _oDb -> getParam('aqb_credits_hide_period');
	}

	function isPresentFeatureEnabled(){
		return $this -> _oDb -> getParam('aqb_credits_enable_present_credits') == 'on';
	}	
	

}
?>