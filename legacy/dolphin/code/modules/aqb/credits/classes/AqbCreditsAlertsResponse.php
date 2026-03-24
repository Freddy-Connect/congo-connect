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

class AqbCreditsAlertsResponse extends BxDolAlertsResponse {
	var $_oModule;
	
	function AqbCreditsAlertsResponse(&$oModule) {
	    parent::BxDolAlertsResponse();
		$this -> _oModule = $oModule;
	}
	
	function response($oAlert) {
    	$sMethodName = '_process' . ucfirst($oAlert->sUnit) . ucfirst($oAlert->sAction);
		if(method_exists($this, $sMethodName)) $this -> $sMethodName($oAlert);
    }
 		
	function _processProfileDelete(&$oAlert){
		if ((int)$oAlert->iObject) 
			$this -> _oModule -> _oDb -> deleteProfileHistory($oAlert -> iObject);
	}
}
?>