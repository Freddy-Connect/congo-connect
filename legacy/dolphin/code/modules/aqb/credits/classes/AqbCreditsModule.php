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

bx_import('BxDolTwigModule');
bx_import('BxDolPageView');
bx_import('BxDolEmailTemplates');
bx_import('BxDolAlerts');

require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );

class AqbCreditsModule extends BxDolTwigModule {
	
	/**
	 * Constructor
	 */
	function AqbCreditsModule($aModule) {
	    parent::BxDolModule($aModule);
		$this -> _oConfig -> init($this -> _oDb);
		$this -> _sPrefix = $this -> _oConfig -> getUri();
	}
	
	function actionAdministration($sUrl = '') {
        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
		
		$this -> _oTemplate -> addAdminCss('admin.css');		
        $this -> _oTemplate -> pageStart(); 
		
		$aMenu = array(
            'main' => array(
                'title' => _t('_aqb_credits_admin_credits_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/',
                '_func' => array ('name' => 'getActionsPanel', 'params' => array()),
            ), 	
			'settings' => array(
                'title' => _t('_aqb_credits_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'getSettingsPanel', 'params' => array()),
            ), 		
        );
    
	   if (empty($aMenu[$sUrl])) $sUrl = 'main';
        $aMenu[$sUrl]['active'] = 1;
		
        $sContent = call_user_func_array (array($this -> _oTemplate, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);		
        echo $this -> _oTemplate -> adminBlock($sContent, $aMenu[$sUrl]['title'], $aMenu);  
        $this -> _oTemplate -> pageCodeAdmin(_t('_aqb_credits_admin'));
	}
	
	function actionMyCredits(){
		$this -> _oTemplate -> addJs ('main.js');
		$this -> _oTemplate -> addCss ('main.css');
		
		bx_import ('ViewPage', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'ViewPage';
        $oPage = new $sClass($this);
		$this -> _oTemplate -> pageStart();
		    	
		echo $oPage->getCode();
	
        $this -> _oTemplate -> addJs ('main.js');
        $this -> _oTemplate -> addCss ('main.css');
        $this ->  _oTemplate -> pageCode(_t('_aqb_credits_my_credits_title'), false, false);
	}
   	
	function actionCreditsPanel(){
		if (!$this -> isLogged()) {
            return '';
        }
   
		header('Content-Type: text/html; charset=UTF-8');
		echo $this -> _oTemplate -> getCreditsInfo($_REQUEST);
		exit;	
	}
	
	function actionBuyForm($sType = 'price'){
	  if (!isLogged()) return '';
		
	   echo $this -> _oTemplate -> buyCreditsBlock($sType);
	   exit;	
	}
	
	function actionBuyCredits($sType, $iAmount){
	   header('Content-Type:text/javascript');
       $oJson = new Services_JSON();
	 
	   $iAmount = (int)$iAmount;
	   if (!($iProfileId = getLoggedId()) || !$iAmount) {
		$aResult = array('code' => 1, 'message' => _t('_aqb_credits_wron_credits_amount'));
		echo $oJson -> encode($aResult);
		exit;		
	  }
	     
	   if ($sType == 'points' && $this -> _oDb -> isPointsSystemInstalled()){
			$iPointsBalance = BxDolService::call('aqb_points', 'get_profile_points_num', array($iProfileId));
			$iRequiredPoints = (int)$this -> _oConfig -> priceInPoints() * $iAmount;
			if ($iRequiredPoints > $iPointsBalance)
				$aResult = array('code' => 1, 'message' => _t('_aqb_credits_not_enough_points_to_exchange'));
			else
			{ 
				$aAction = array('action' => $iAction, 'action' => 'exchange', 'number' => $iAmount, 'param' => $iRequiredPoints, 'member_id' => $iProfileId);
				if ($this -> _oDb -> assignCredits($aAction) && BxDolService::call('aqb_points', 'assign_points', array(-$iRequiredPoints, _t('_aqb_credtis_action_exchanged_points', $iRequiredPoints, $iAmount), 0, $iProfileId)))
					$aResult = array('code' => 0, 'message' => _t('_aqb_credits_successfully_exchanged', $iRequiredPoints, $iAmount));
				 else  
					$aResult = array('code' => 1, 'message' => _t('_aqb_credits_can_not_exchange_points_to_credits'));
			}
			
			echo $oJson -> encode($aResult);
			exit;			
	   }	   

		$aInfo = array('profile' => $iProfileId, 'price' => $this -> _oConfig -> priceInCurrency(), 'amount' => $iAmount);
		$iPandingId = $this -> _oDb -> createPendingTransaction($aInfo);	   
	   
	   if ($iPandingId)
			$aResult = array('code' => 0, 'link' => BX_DOL_URL_MODULES . '?r=payment/act_add_to_cart/' . $this -> _oConfig -> getSiteId() . '/' . $this -> _oConfig -> getId() . '/' . $iPandingId . '/' . $iAmount);
	   else 
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_not_successfully_added_to_cart', $iAmount));
			
	   echo $oJson->encode($aResult);
	   exit;
	}
	
	function serviceTotalCreditsAmount($iProfileId = 0){
		if(!(int)$iProfileId) $iProfileId = getLoggedId();
		
		return $this -> _oDb -> getTotalCredits($iProfileId);
	}
	
	function serviceAssignCredits($iAmount, $iAction = 0, $mixedReason = '', $sType = 'spent', $iProfileId = 0){
		if(!(int)$iProfileId) $iProfileId = getLoggedId();
		
		if (!(int)$iAmount || !$iProfileId) return 0;
			
		$aAction = array('action' => $iAction, 'action' => $sType, 'number' => $iAmount, 'param' => $mixedReason, 'member_id' => $iProfileId);
		return $this -> _oDb -> assignCredits($aAction);
	}
	
	function serviceGetCreditsNum($iProfileId = 0){
		if(!(int)$iProfileId) $iProfileId = getLoggedId();
			
		return $this -> _oDb -> getTotalCredits($iProfileId);
	}
	
	function serviceGetItems($iVendorId){
		$aTransactions = $this -> _oDb ->  getCreditsTransactions($iVendorId);
			
		foreach($aTransactions as $k => $v){
				$aResult[] = array(
		    	       'id' => $v['id'],
		    	       'title' => _t('_aqb_credits_cart_numer_title'),
		    	       'url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'my_credits/',
		    	       'price' => $this -> _oConfig -> priceInCurrency()
		           );
		}   
        
		return  $aResult;  
	}
	
	function serviceGetCartItem($iClientId, $iItemId) {
 		if (!$iItemId || !$iClientId)
            return array();
		
	    $aItem = $this -> _oDb -> getCreditsTransactionItem($iClientId, $iItemId);
		if(!count($aItem)) return array();

		return array (
	       'id' => $aItem['id'],
	       'title' => _t('_aqb_credits_cart_numer_title'),
    	   'url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'my_credits/',
		   'quantity' => $aItem['amount'],
    	   'price' => $aItem['price']
         );
 	}
	
	function serviceRegisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) {
		$aResult = $this -> _oDb -> boughtCredits($iClientId, $iItemId, $iItemCount, $sOrderId);
		
		if (empty($aResult)) return false;
		
		$aAction = array('action' => 0, 'action' => 'bought', 'number' => $aResult['amount'], 'member_id' => $iClientId);
		$iId = $this -> _oDb -> assignCredits($aAction);		
		$this -> _oDb -> updateCreditsId($iId, $iItemId);
		
		return $iId ? $aResult : array();
	}
	
	function serviceUnregisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) {
		if((int)$iClientId != getLoggedId() && !$this -> isAdmin())	return false;
		$this -> _oDb -> cancelCreditsOrder($sOrderId);
	}
	
	function actionCheckForExchange(){
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
	   
		if (!$_POST['items'] || !($aList = explode(',', trim($_POST['items'], ',')))){
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_no_actions_found'));
			echo $oJson -> encode($aResult);
			exit;		
		}
		
		$iMemberID = getLoggedId();
		$aMyClosedActions = $this -> _oDb -> getAvailableActionsForCredits($iMemberID);
		$sNames = '';
		$iTotal = 0;
		
		foreach($aList as $iKey => $iValue){
			if ($aMyClosedActions[$iValue]){ 
				$iTotal += $aMyClosedActions[$iValue];
				$sNames .= $this -> _oDb -> getMembershipActionName($iValue) . ', ';
			}
			else
				{
					$aResult = array('code' => 1, 'message' => _t('_aqb_credits_not_enought_credits_to_exchange'));
					echo $oJson -> encode($aResult);
					exit;	
				}
		}

		$iCreditsBalance = $this -> _oDb -> getTotalCredits($iMemberID);		
		if ($iTotal > 0 && $iTotal > $iCreditsBalance){
					$aResult = array('code' => 1, 'message' => _t('_aqb_credits_not_enought_credits_to_exchange'));
					echo $oJson -> encode($aResult);
					exit;		
		}
		
		$aResult = array('code' => 0, 'message' => _t('_aqb_credits_are_you_sure_to_exchange', $iTotal, trim($sNames, ', ')));		
		echo $oJson -> encode($aResult);
		exit;
	}
	
	function actionExchangeCreditsForActions(){
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
	   
		if (!$_POST['items'] || !($aList = explode(',', trim($_POST['items'], ',')))){
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_no_actions_found'));
			echo $oJson -> encode($aResult);
			exit;		
		}
		
		$iMemberID = getLoggedId();
		$aMyClosedActions = $this -> _oDb -> getAvailableActionsForCredits($iMemberID);
		
		foreach($aList as $iKey => $iValue){
			if ($aMyClosedActions[$iValue]){ 
				$iTotal += $aMyClosedActions[$iValue];
				$aAction = array('action_id' => $iValue, 'action' => 'spent', 'number' => $aMyClosedActions[$iValue], 'member_id' => $iMemberID);
				$this -> _oDb -> assignCredits($aAction);
			}
		}		
		
		$aResult = array('code' => 0, 'message' => _t('_aqb_credits_successfully_exchanged_for_actions', $iTotal));		
		echo $oJson -> encode($aResult);
		exit;
	}
	
	
	function serviceGetPopupCode(){
		if (!isLogged()) return '';
	
		$aList = $this -> _oDb -> getAvailableActionsForCredits(getLoggedId());		
		return (!empty($GLOBALS['aqb_credits_actions']) && !empty($aList) && !isset($_COOKIE['aqb_credits_interval'])) ? $this -> _oTemplate -> getPoupWindow() : '';
	}
	
	function actionGetPopupWindow(){
		if (!isLogged()) return '';
		$sContent = $this -> _oTemplate -> getPopupNotificationWindow(getLoggedId());
		echo PopupBox('aqb_popup', _t('_aqb_credits_popup_notification') , $sContent);
		exit;
	}
	
	function actionSetInterval(){
		if (!isLogged()) return '';
		
		
		$aUrl = parse_url($GLOBALS['site']['url']);
		$sPath = isset($aUrl['path']) && !empty($aUrl['path']) ? $aUrl['path'] : '/';
		$sHost = '';
		
		$iCookieTime = $this -> _oConfig -> getPopUpBlockPeriod();		
		
		if ($_GET['set'])
			$iCookieTime = $iCookieTime	? time() + 24 * 60 * 60 * $iCookieTime : time() + 24 * 60 * 60 * 365;
		else 	
			$iCookieTime = time() - 24 * 60 * 60;
			
		setcookie("aqb_credits_interval", 'on', $iCookieTime, $sPath, $sHost, false, true /* http only */);
	}
	
	function serviceCheckAction($iActionID){
		$iProfileID = getLoggedId();
		if (!($iProfileID && $iActionID)) return false;
		
		$bActive = $this -> _oDb -> isActionAvailable($iProfileID, $iActionID);
		
		$aList = $this -> _oDb -> getAvailableActionsForCredits($iProfileID);
		if (!$bActive && $aList[$iActionID]){
				$GLOBALS['aqb_credits_actions'][] = $iActionID;
		}
		
		return $bActive;
	}
	
	function serviceResponse($oAlert)
    {
        $oResponse = new AqbCreditsAlertsResponse($this);
        $oResponse -> response($oAlert);
    }
	
	function actionGetPresentCreditsForm($iProfileID){
		if (!isLogged() || (int)$iProfileID == getLoggedId()) return '';
		
		if (!$this -> _oConfig -> isPresentFeatureEnabled()) return '';
		
		echo $this -> _oTemplate -> presentCreditsBlock($iProfileID);
	    exit;		
	}
	
	function actionCheckForPresent($iProfileID){
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		
		$iMemberID = getLoggedId();
		if (!isLogged() || (int)$iProfileID == $iMemberID){ 
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_the_same_profile'));
			echo $oJson -> encode($aResult);
			exit;		
		}
		
		$iAmount = (int)$_GET['amount'];	
	    if ($iAmount <= 0){
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_empty_amount'));
			echo $oJson -> encode($aResult);
			exit;		
		}
		
		$iCreditsBalance = $this -> _oDb -> getTotalCredits($iMemberID);		
		if ($iAmount > $iCreditsBalance){
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_not_enought_credits_to_present', $iCreditsBalance));
			echo $oJson -> encode($aResult);
			exit;		
		}
		
		$aResult = array('code' => 0, 'message' => _t('_aqb_credits_are_you_sure_present', $iAmount));	
			
		echo $oJson -> encode($aResult);
		exit;
	}
	
	function actionPresentCredits($iProfileID){
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		
		
		$iMemberID = getLoggedId();
		if (!isLogged() || (int)$iProfileID == $iMemberID){ 
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_the_same_profile'));
			echo $oJson -> encode($aResult);
			exit;		
		}
		
		$iAmount = (int)$_POST['amount'];	
	    if ($iAmount <= 0){
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_empty_amount'));
			echo $oJson -> encode($aResult);
			exit;		
		}
		
		$iCreditsBalance = $this -> _oDb -> getTotalCredits($iMemberID);		
		if ($iAmount > $iCreditsBalance){
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_not_enought_credits_to_present', $iCreditsBalance));
			echo $oJson -> encode($aResult);
			exit;		
		}

		$aAction_First = array('action' => $iAction, 'action' => 'present', 'number' => -(int)$iAmount, 'param' => '<a href="'.getProfileLink($iProfileID). '">' . getNickName($iProfileID) . '</a>', 'member_id' => $iMemberID);
		$aAction_Second = array('action' => $iAction, 'action' => 'got', 'number' => (int)$iAmount, 'param' => '<a href="'.getProfileLink($iMemberID). '">' . getNickName($iMemberID) . '</a>', 'member_id' => $iProfileID);
	
		if ($this -> _oDb -> assignCredits($aAction_First) && $this -> _oDb -> assignCredits($aAction_Second))		
			$aResult = array('code' => 0, 'message' => _t('_aqb_credits_successfully_presented'));
		else  
			$aResult = array('code' => 1, 'message' => _t('_aqb_credits_were_not_presented'));
			
		echo $oJson -> encode($aResult);
		exit;
	}
	
	function serviceGetPresentButton($iProfileID){
		if (!isLogged() || (int)$iProfileID == getLoggedId() || !$this -> _oConfig -> isPresentFeatureEnabled()) return '';
		
		$this -> _oTemplate -> addJs ('main.js');
        $this -> _oTemplate -> addCss ('main.css');   
		
		return _t('_aqb_credits_present_action_title'); 
   }
	
	
}
?>