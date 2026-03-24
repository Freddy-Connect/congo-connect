<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolModule');
bx_import('BxDolProfilesController');

class MbMmpPayPal{

    function __construct(){
        $this->oMain = $this->getMain();
		$this->aSettings = unserialize(getParam('dol_subs_processors'));
		$this->_processor = 'PayPal';
	}
    function getMain() {
        return BxDolModule::getInstance('MbMmpModule');
    }
    function getLink(){
          $sUrl = $this->getPaypal();
          $sLink = 'https://' . $sUrl . '/cgi-bin/webscr';
          return $sLink;
    }
	
	/**
	 * Displays main content for paypal 
	 *
	 */
	function showContent($IDLevel){ 
		
		$sOutput.= $this->showForm($IDLevel);		
		return $sOutput;
	}
	
	function showForm($iMembId){ 
		$iId = $_COOKIE['memberID'];
		$aPriceInfo	= $this->oMain->_oDb->getMembershipPriceInfo($iMembId);
		$aLevel = $this->oMain->_oDb->getMembershipById($iMembId);
		$aMemberInfo = getProfileInfo($iId);		

		$aVars = array(
			'action' => $this->getLink(),
			'business' => $this->aSettings['paypal_account'],
			'auto' => $sAuto = ($aPriceInfo['Auto'] == '1') ? '_xclick-subscriptions' : '_xclick',
			'cur_code' => $this->aSettings['currency'],
			'item_name' => $aLevel['Name'].' '._t('_dol_subs_membership'),
			'item_number' => $_COOKIE['memberID'].'-'.$aLevel['ID'],
			'amount' => $aPriceInfo['Price'],
			'unit' => $sUnit = ($aPriceInfo['Unit'] == 'Days') ? 'D' : 'M',
			'length' => $aPriceInfo['Length'],
			'callback' => BX_DOL_URL_ROOT.'m/memberships/callback',
			'return' => BX_DOL_URL_ROOT.'m/memberships/',
			'custom' => '',
			'bx_if:trial' => array(
				'condition' => ($aLevel['Trial'] == '1'),
		        'content' => array(
		            't_length' => $aLevel['Trial_Length'],
		        ),
			),
			'bx_if:auto' => array(
				'condition' => ($aPriceInfo['Auto'] == '1'),
		        'content' => array(
		            'src' => '1',
		            'sr1' => '1',
		        ),
			),
			'bx_if:acl' => array(
				'condition' => ($this->oMain->_oDb->userAcl($iId) == '1'),
		        'content' => array(
		            'modify' => $this->modifyInput(),
		        ),
			),
		);
		return  $this->oMain->_oTemplate->parseHtmlByName('pp_payment_form',$aVars); 
    }
    function getPaypal(){
        if ($this->aSettings['paypal_type'] == 'test') {
            return 'www.sandbox.paypal.com';
        } else {
            return 'www.paypal.com';
        }
    }
	function processPayment($aVars){
		
		echoDbgLog(json_encode($aVars));
		
 		list($iUserId, $iMembLevel) = explode('-', $aVars['item_number']);
		$aMembLevelInfo = $this->oMain->_oDb->getMembershipById($iMembLevel);
		$aMembLevelPriceInfo = $this->oMain->_oDb->getMembershipPriceInfo($iMembLevel);
		$iPrice = number_format($aMembLevelPriceInfo['Price'], 2);

		switch($aMembLevelPriceInfo['Unit']){
			case 'Days':
				$iMembershipDays = $aMembLevelPriceInfo['Length'];
			break;
			case 'Months':
				$iMembershipDays = $aMembLevelPriceInfo['Length']*30;
			break;
		}

		$sReq = 'cmd=_notify-validate';
      	foreach ($aVars as $key => $value) {
          	$value = urlencode(stripslashes($value));
          	$sReq .= '&' . $key . '=' . $value;
      	}
		
		// Get processor data
        $aProcessorData = unserialize(getParam('dol_subs_processors'));
		
	
      	$sUrl = ($aProcessorData['paypal_type'] == 'test') ? 'www.sandbox.paypal.com' : 'www.paypal.com';      
      	$sHeader = "POST /cgi-bin/webscr HTTP/1.0\r\n";
      	$sHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
      	$sHeader .= "Content-Length: " . strlen($sReq) . "\r\n\r\n";
      	$fp = fsockopen($sUrl, 80, $iErrNo, $sErrStr, 30);
      	if (!$fp) {
          	echo $sErrStr . ' (' . $iErrNo . ')';
			error_log("$sErrStr ( $iErrNo )");
      	} else {
	
          	fputs($fp, $sHeader . $sReq);          
          	while (!feof($fp)) {
              	$sRes = fgets($fp, 1024);
              	if (strcmp($sRes, "VERIFIED") == 0) {
					
                  	if ($aVars['payment_status'] == 'Completed' && (($aVars['txn_type'] == 'subscr_payment') || ($aVars['txn_type'] == 'web_accept')) && $aVars['mc_gross'] == $iPrice) {
						
						// Check if payment has already been made
						if(!db_value("SELECT `txn_id` FROM `mmp_payments` WHERE `txn_id` = '{$aVars['txn_id']}'")){
							$rInsertPayment = db_res("INSERT INTO `mmp_payments` SET 
								`txn_id` 		= '{$aVars['txn_id']}',
								`subscr_id`		= '{$aVars['subscr_id']}',
								`membership_id` = '{$iMembLevel}',
								`user_id`		= '{$iUserId}',
								`amount` 		= '{$aVars['mc_gross']}',
								`date` 			= NOW(),
								`status` 		= '{$aVars['payment_status']}',
								`processor`    	= '{$this->_processor}'
							");
						}	
						$this->oMain->_oDb->clearMembershipInfo($iUserId);
						$this->oMain->_oDb->setUserStatus($iUserId,'Active');			
						deleteUserDataFile($iUserId);																																																												
						setMembership($iUserId, $iMembLevel, $iMembershipDays, true, $aVars['subscr_id']);
						return '_dol_subs_2co_upgraded';
											
					} else {
						
						return '_dol_subs_2co_error1';
					}
					
					// Trial sign up
                  	if ($aVars['txn_type'] == 'subscr_signup' &&  $aVars['amount1'] == '0.00' && $aVars['mc_amount1'] == '0.00') {
						$this->oMain->_oDb->clearMembershipInfo($iUserId);
						$this->oMain->_oDb->setUserStatus($iUserId,'Active');			
						deleteUserDataFile($iUserId);																																																														
						$rResult = setMembership($iUserId, $iMembLevel, $aMembLevelInfo['Trial_Length'], true, $aVars['subscr_id']);				
					}
					
				// Paypal test	
				} elseif($aProcessorData['paypal_type'] == 'test'){					
					
				
					
					if ( (in_array($aVars['payment_status'], array('Completed', 'Pending')))  && (($aVars['txn_type'] == 'subscr_payment') || ($aVars['txn_type'] == 'web_accept')) && $aVars['mc_gross'] == $iPrice) {
						
						// Check if payment has already been made
						if(!db_value("SELECT `txn_id` FROM `mmp_payments` WHERE `txn_id` = '{$aVars['txn_id']}'")){
							$rInsertPayment = db_res("INSERT INTO `mmp_payments` SET 
								`txn_id` 		= '{$aVars['txn_id']}',
								`subscr_id`		= '{$aVars['subscr_id']}',
								`membership_id` = '{$iMembLevel}',
								`user_id`		= '{$iUserId}',
								`amount` 		= '{$aVars['mc_gross']}',
								`date` 			= NOW(),
								`status` 		= '{$aVars['payment_status']}',
								`processor`    	= '{$this->_processor}'
							");
						}
						$this->oMain->_oDb->clearMembershipInfo($iUserId);
						$this->oMain->_oDb->setUserStatus($iUserId,'Active');			
						deleteUserDataFile($iUserId);																																																												
						setMembership($iUserId, $iMembLevel, $iMembershipDays, true, $aVars['subscr_id']);
						return '_dol_subs_2co_upgraded';
					
					} else {
						
						return '_dol_subs_2co_error1';
					}
					
				} // end paypal test
			}
		}
	}
	function modifyInput(){
		$sCode = '<input type="hidden" name="modify" value="1" />';
		return $sCode;
	}


}