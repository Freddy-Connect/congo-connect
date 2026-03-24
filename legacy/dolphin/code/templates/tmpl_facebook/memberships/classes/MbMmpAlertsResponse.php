<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolAlerts');
require_once('MbMmpHandler.php');

class MbMmpAlertsResponse extends BxDolAlertsResponse {
	
	function MbMmpAlertsResponse() {
	    parent::BxDolAlertsResponse();
		$this -> _oHandleMember = new MbMmpHandler();
	}

    function response($oAlert) {
    	$sMethodName = '_process' . ucfirst($oAlert->sUnit) . str_replace(' ', '', ucwords(str_replace('_', ' ', $oAlert->sAction)));
    	if(method_exists($this, $sMethodName))
            $this->$sMethodName($oAlert);

		if($oAlert->sUnit == 'subs' && $oAlert->sAction == 'cur_page')
			$this ->_oHandleMember ->currentPage($oAlert);

		if($oAlert->sUnit == 'profile' && $oAlert->sAction == 'login')
			$this ->_oHandleMember->handleLogin($oAlert);

		if($oAlert->sUnit == 'profile' && $oAlert->sAction == 'join')
			$this ->_oHandleMember->handleJoin($oAlert);
    }

}