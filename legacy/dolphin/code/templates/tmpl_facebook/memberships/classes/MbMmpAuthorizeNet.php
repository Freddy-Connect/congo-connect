<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolModule');
// require main class
require('include/config.inc.php');
require('include/AuthnetXML.class.php');

class MbMmpAuthorizeNet{

    function __construct(){
        $this->oMain = $this->getMain();
		$this->aSettings = unserialize(getParam('dol_subs_processors'));
		$this->aCreditCards = array('visa' => 'Visa','mastercard' => 'Mastercard','amex' => 'American Express', 'other' => 'Other');
		$this->_processor = 'Authorize.net';
	}

    function getMain() {
        return BxDolModule::getInstance('MbMmpModule');
    }
	
	/**
	 * Passthru
	 *
	 */
	function showContent($IDLevel){
		$sOutput = $this->showForm($IDLevel);
		return $sOutput;
	}

	/**
	 * Main form display
	 *
	 */
	function showForm($iMembId){
		
		$sUrl = $this->getSimUrl();
		$sAnLogin =  $this->aSettings['an_login'];
		$aMembInfo = $this->oMain->_oDb->getMembershipById($iMembId);
		$aMembPriceInfo = $this->oMain->_oDb->getMembershipPriceInfo($iMembId);
		$sTransactionKey = $this->aSettings['an_transkey'];
		$iSequence	= rand(1, 1000);
		$sTimeStamp	= time ();
		$sFingerPrint = $this->getFingerprint($sAnLogin,$sTransactionKey,$aMembPriceInfo['Price'],$iSequence,$sTimeStamp);
		$iInvoice	= date(YmdHis);
 		if($this->aSettings['an_api'] == 'sim'){
			return $this->getSimForm($iMembId);
		}
		if($this->aSettings['an_api'] == 'arb'){
			if($aMembPriceInfo['Auto'] == '1'){
				return $this->getArbForm($iMembId);
			}else{
				//return $this->getSimForm($iMembId);
				return $this->getArbForm($iMembId);
			}
		}
    }
	function getFingerprint($sAnLogin,$sTransactionKey,$iAmount,$iSequence,$sTimeStamp){
		if( phpversion() >= '5.1.2' ){	
			$sFingerPrint = hash_hmac("md5", $sAnLogin . "^" . $iSequence . "^" . $sTimeStamp . "^" . $iAmount . "^", $sTransactionKey);
		}else{ 
			$sFingerPrint = bin2hex(mhash(MHASH_MD5, $sAnLogin . "^" . $iSequence . "^" . $sTimeStamp . "^" . $iAmount . "^", $sTransactionKey)); 
		}
		return $sFingerPrint;
	}
	
	
	/**
	 * Process Payment from Silent Post
	 *
	 */
	function processPayment($aVars){
		
		foreach($aVars as $k=>$v){
			error_log("$k: $v");
		}
		
 		list($iUserId, $iMembLevel) = explode('-', $aVars['x_cust_id']);
		$aMembLevelInfo = $this->oMain->_oDb->getMembershipById($iMembLevel);
		$aMembLevelPriceInfo = $this->oMain->_oDb->getMembershipPriceInfo($iMembLevel);
		$iPrice = number_format($aMembLevelPriceInfo['Price'], 2);

		$sResponseText = urldecode($aVars['x_response_reason_text']);
		switch($aMembLevelPriceInfo['Unit']){
			case 'Days':
				$iMembershipDays = $aMembLevelPriceInfo['Length'];
			break;
			case 'Months':
				$iMembershipDays = $aMembLevelPriceInfo['Length']*30;
			break;
		}        

        if($aVars['x_response_code'] == '1'){
			
			// Check if payment has already been made
			if( !mysql_num_rows(mysql_query("SELECT `txn_id` FROM `dol_subs_payments` WHERE `txn_id` = '{$aVars['x_trans_id']}'"))){
				$rInsertPayment = db_res("INSERT INTO `dol_subs_payments` SET 
					`txn_id` 		= '{$aVars['x_trans_id']}',
					`membership_id` = '{$iMembLevel}',
					`user_id`		= '{$iUserId}',
					`amount` 		= '{$aVars['x_amount']}',
					`date` 			= NOW(),
					`status` 		= 'Completed',
					`processor`    	= '{$this->_processor}'
				");
			}
			
			$this->oMain->_oDb->clearMembershipInfo($iUserId);
			$this->oMain->_oDb->setUserStatus($iUserId,'Active');
			deleteUserDataFile($iUserId);
			setMembership($iUserId, $iMembLevel, $iMembershipDays, true, $sSubId );
			return '_dol_subs_2co_upgraded';
		
		} else {
			
			return '_dol_subs_2co_error1';
			
		}
 		
	}
	
	 
	/**
	 * New ARB process payment
	 *
	 */	 
	function processArbPayment($aVars){
		
		$sHost = $this->getArbUrl();
		list($iUserId, $iMembLevel) = explode('-', $aVars['cust_id']);
		$aProfile = getProfileInfo($iUserId);
		$aMembInfo = $this->oMain->_oDb->getMembershipById($iMembLevel);
		$aMembPriceInfo = $this->oMain->_oDb->getMembershipPriceInfo($iMembLevel);
		$aMembership = array_merge($aMembInfo, $aMembPriceInfo);

		// Set startdate after first membership interval
		if($aMembership['Unit'] == 'Days'){
			$time = mktime(0,0,0,date("m"),date("d")+$aMembership['Length'],date("Y"));
		}
		if($aMembership['Unit'] == 'Months'){
			$time = mktime(0,0,0,date("m")+$aMembership['Length'],date("d"),date("Y"));
		}		
		$sStartDate = date('Y-m-d', $time);
		$sUseDevServer = ($this->aSettings['an_type'] == 'test') ? 1 : 0;

		// Authorize initial Payment
		$initialPayment = $this->authInitialPayment($aVars);
		
		// Create subscription if initial payment is successful and if Membership is Auto
		if($initialPayment['successful'] == 'yes' && $aMembership['Auto'] == '1')
		{
			$xml = new AuthnetXML($this->aSettings['an_login'], $this->aSettings['an_transkey'], $sUseDevServer);
			$xml->ARBCreateSubscriptionRequest(array(
				'refId' => $aVars['cust_id'],
				'subscription' => array(
					'name' => $aMembership['Name'] . " " . _t('_dol_subs_membership'),
					'paymentSchedule' => array(
						'interval' => array(
							'length' => $aMembership['Length'],
							'unit' => strtolower($aMembership['Unit'])
						),
						'startDate' => $sStartDate,
						'totalOccurrences' => '9999',
						'trialOccurrences' => '0'
					),
					'amount' => $aMembership['Price'],
					'trialAmount' => $aMembership['Price'],
					'payment' => array(
						'creditCard' => array(
							'cardNumber' => $aVars['cc_num'],
							'expirationDate' => $aVars['cc_expy']."-".$aVars['cc_expm']
						)
					),
					'billTo' => array(
						'firstName' => $aVars['fname'],
						'lastName' => $aVars['lname'],
						'address' => $aVars['cc_address1'],
						'city' => $aVars['cc_city'],
						'state' => $aVars['cc_state'],
						'zip' => $aVars['cc_zip'],
						'country' => $aVars['cc_country']
					)
				)
			));
			$createSubResult = array(
				'resultCode' => $xml->messages->resultCode,
				'code' => $xml->messages->message->code,
				'text' => $xml->messages->message->text,
				'successful' => ($xml->isSuccessful()) ? 'yes' : 'no',
				'error' => ($xml->isError()) ? 'yes' : 'no',
				'subscription_id' => $xml->subscriptionId,
			);
			
			// Check if subscription was created
			if($createSubResult['successful'] == 'yes'){
				
				// Display proper result and redirect
				$redirect = BX_DOL_URL_ROOT . 'm/memberships/';
				$output = MsgBox(_t('_dol_subs_memb_upgraded'));
				$output.= <<<HTML
					<script type="text/javascript" language="javascript">
						setTimeout(function(){
							location.href = '{$redirect}';
						}, 2500 );
					</script>
HTML;
				echo $output;
				//echoDbg($createSubResult);exit;
				
			} else { // Subscription creation failed				
	
				echo MsgBox($createSubResult['text']);exit;
			}
			

		// If membership is not auto just echo the results and exit
		} else if($initialPayment['successful'] == 'yes' && $aMembership['Auto'] != 1){ 
					
			// Display proper result and redirect
			$redirect = BX_DOL_URL_ROOT . 'm/memberships/';
			$output = MsgBox(_t('_dol_subs_memb_upgraded'));
			$output.= <<<HTML
				<script type="text/javascript" language="javascript">
					setTimeout(function(){
						location.href = '{$redirect}';
					}, 2500 );
				</script>
HTML;
			echo $output;exit;

		} else { // initial payment not successful
		
			// Not sure how to handle errors	
			echo MsgBox($initialPayment['text']);exit;
		}
	}
	
	
	/**
	 * Authorize the initial Payment using AIM
	 *
	 */
	function authInitialPayment($aVars){

		list($iUserId, $iMembLevel) = explode('-', $aVars['cust_id']);
		$aProfile = getProfileInfo($iUserId);
		$aMembInfo = $this->oMain->_oDb->getMembershipById($iMembLevel);
		$aMembPriceInfo = $this->oMain->_oDb->getMembershipPriceInfo($iMembLevel);
		$aMembership = array_merge($aMembInfo, $aMembPriceInfo);
		$sUseDevServer = ($this->aSettings['an_type'] == 'test') ? 1 : 0;
		
		$xml = new AuthnetXML($this->aSettings['an_login'], $this->aSettings['an_transkey'], $sUseDevServer);
		$xml->createTransactionRequest(array(
			'refId' => $_COOKIE['memberID'].'-'.$iMembLevel,
			'transactionRequest' => array(
				'transactionType' => 'authCaptureTransaction',
				'amount' => $aMembership['Price'],
				'payment' => array(
					'creditCard' => array(
						'cardNumber' => $aVars['cc_num'],
						'expirationDate' => $aVars['cc_expm'].$aVars['cc_expy'],
						'cardCode' => $aVars['cc_csv'],
					),
				),
				'order' => array(
					'invoiceNumber' => time(),
					'description' => 'Membership Payment',
				),
				'customer' => array(
				   'id' => $iUserId.'-'.$iMembLevel,
				   'email' => $aVars['email'],
				),
				'billTo' => array(
					'firstName' => $aVars['fname'],
					'lastName' => $aVars['lname'],
			        'address' => $aVars['cc_address1'],
			        'city' => $aVars['cc_city'],
			        'state' => $aVars['cc_state'],
			        'zip' => $aVars['cc_zip'],
			        'country' => $aVars['cc_country']
				)
			),
		));		
	
		
		
		return array(
			'resultCode' => $xml->messages->resultCode, // must be "Ok"
			'code' => $xml->messages->message->code, 	// successful is "I00001"
			'text' => $xml->messages->message->text,
			'successful' => ($xml->isSuccessful()) ? 'yes' : 'no',
			'error' => ($xml->isError()) ? 'yes' : 'no',
			'authCode' => $xml->transactionResponse->authCode,
			'transID' => $xml->transactionResponse->transId,
		);

	}

	/**
	 * Get SIM Form
	 *
	 */
	function getSimForm($iMembId){

		$sUrl = $this->getSimUrl($this->aSettings);
		$sAnLogin = $this->aSettings['an_login'];
		$aMembInfo = $this->oMain->_oDb->getMembershipById($iMembId);
		$aMembPriceInfo = $this->oMain->_oDb->getMembershipPriceInfo($iMembId);
		$sTransactionKey = $this->aSettings['an_transkey'];
		$iSequence	= rand(1, 1000);
		$sTimeStamp	= time ();
		$sFingerPrint = $this->getFingerprint($sAnLogin,$sTransactionKey,$aMembPriceInfo['Price'],$iSequence,$sTimeStamp);
		$iInvoice	= date(YmdHis);

		$aVars = array(
			'action' => $sUrl,
			'x_login' => $sAnLogin,
			'x_amount' => $aMembPriceInfo['Price'],
			'x_description' => $aMembInfo['Description'],
			'x_invoice_num' => $iInvoice,
			'x_method' => 'CC',
			'x_receipt_link_url' => $this->oMain->_oTemplate->sBase,
			'x_receipt_link_text' => 'Return to '.getParam('site_title'),
			'x_receipt_link_method' => 'POST',
			'x_fp_sequence' => $iSequence,
			'x_fp_timestamp' => $sTimeStamp,
			'x_fp_hash' => $sFingerPrint,
			'x_cust_id' => $_COOKIE['memberID'] . '-' . $iMembId,
			'x_show_form' => 'PAYMENT_FORM',
			'x_cancel_url' => $this->oMain->_oTemplate->sBase,
			'x_cancel_url_text' => 'Cancel',
			'bx_if:an_test' => array(
				'condition' => ($this->aSettings['an_type'] == 'test'),
		        'content' => array(
		            'x_test_request' => 'TRUE',
		        ),
			),		
		);		
		return $this->oMain->_oTemplate->parseHtmlByName('authorize_sim',$aVars);	
	}
	
	
	/**
	 * Show ARB Payment form
	 *
	 */
	function showArbLargeForm($iMembId){
		$aProfile = getProfileInfo($_COOKIE['memberID']);
      	$aForm = array(
            'form_attrs' => array(
                'name'     => 'an_payment_form', 
                'method'   => 'post',
				'action' => NULL,
				'onsubmit' => 'return subs_core.submit_arb_payment(this);',
            ),
			'inputs' => array(
				'cust_id' => array(
					'type' => 'hidden',
					'name' => 'cust_id',
					'value' => $_COOKIE['memberID'].'-'.$iMembId,
					'attrs' => array(
						'id' => 'cust_id',
					),
				),

                'fname' => array(
                    'type' => 'text',
                    'name' => 'fname', 
                    'caption' => 'First Name',
                    'required' => true,
					'value' => $aProfile['FirstName'],
					'attrs' => array(
						'id' => 'fname',
					),                  
                ),
                'lname' => array(
                    'type' => 'text',
                    'name' => 'lname', 
                    'caption' => 'Last Name',
                    'required' => true,
					'value' => $aProfile['LastName'],
					'attrs' => array(
						'id' => 'lname',
					),                  
                ),
                'credit_card' => array(
                    'type' => 'select',
                    'name' => 'credit_card',
                	'values' => $this->aCreditCards,
                    'caption' => 'Choose Credit Card',
                    'required' => true,
					'attrs' => array(
						'id' => 'credit_card',
					), 
                ),
                'cc_num' => array(
                    'type' => 'text',
                    'name' => 'cc_num', 
                    'caption' => 'Credit Card Number',
                    'required' => true,
                    'info' => 'No dashes or spaces',
					'attrs' => array(
						'id' => 'cc_num',
					),                  
                ),
				'cc_expm' => array(
                    'type' => 'select',
                    'name' => 'cc_expm', 
                    'caption' => 'Expiry Month',
                    'required' => true,
					'values' => $this->oMain->_oConfig->getMonths(),
					'attrs' => array(
						'id' => 'cc_expm',
					),
                ),
                'cc_expy' => array(
                    'type' => 'text',
                    'name' => 'cc_expy', 
                    'caption' => 'Expiry Year (YYYY)',
                    'required' => true,
					'attrs' => array(
						'id' => 'cc_expy',
					), 
                ),
                'cc_csv' => array(
                    'type' => 'text',
                    'name' => 'cc_csv',
                    'caption' => 'Card Code',
                    'required' => true,
                    'info' => 'The last three numbers on the back of the card.',
					'attrs' => array(
						'id' => 'cc_csv',
					), 
                ),
				'email' => array(
					'type' => 'text',
					'name' => 'email',
                    'caption' => 'Email',
                    'required' => true,
					'value' => $aProfile['Email'],
					'attrs' => array(
						'id' => 'email',
					),
				),
                'cc_address1' => array(
                    'type' => 'text',
                    'name' => 'cc_address1', 
                    'caption' => 'Address 1',
                    'required' => true,
					'attrs' => array(
						'id' => 'cc_address1',
					), 
                ),
                'cc_city' => array(
                    'type' => 'text',
                    'name' => 'cc_city', 
                    'caption' => 'City',
                    'required' => true,
					'attrs' => array(
						'id' => 'cc_city',
					), 
                ),
                'cc_state' => array(
                    'type' => 'text',
                    'name' => 'cc_state', 
                    'caption' => 'State/Province',
                    'required' => true,
					'attrs' => array(
						'id' => 'cc_state',
					), 
                ),
                'cc_country' => array(
                    'type' => 'text',
                    'name' => 'cc_country', 
                    'caption' => 'Country',
					'attrs' => array(
						'id' => 'cc_country',
					), 
                ),
                'cc_zip' => array(
                    'type' => 'text',
                    'name' => 'cc_zip', 
                    'caption' => 'Zip Code',
                    'required' => true,
					'attrs' => array(
						'id' => 'cc_zip',
					), 
                ),
                'phone' => array(
                    'type' => 'text',
                    'name' => 'phone', 
                    'caption' => 'Phone Number',
					'attrs' => array(
						'id' => 'phone',
					), 
                ),
				'add_button' => array(
					'type' => 'submit',
					'name' => 'an_payment_btn',
					'value' => 'Make Payment',
					'attrs' => array(
						'id' => 'an_payment_btn',
					),
					'label' => ' | <a href="' . BX_DOL_URL_ROOT . 'm/memberships/">Cancel</a>'
				),
           	),	      
		);	
        $oForm = new BxTemplFormView ($aForm);
        $oForm->initChecker();
		$sCode = '<div id="an_payment_form">';
		$sCode = '<div id="submit_result"></div>';
		$sCode.= $oForm->getCode();
		$sCode.= '</div>';
    	return $sCode;
    }

	
	/**
	 * Get ARB form
	 *
	 */
	function getArbForm($iMembId){

		$aVars = array(
			'mlevel' => $iMembId,
		);		
		return $this->oMain->_oTemplate->parseHtmlByName('authorize_arb',$aVars);
	}

	/* Curl/Request Functions
	----------------------------------------------------------------------------------*/
	function send_request_via_curl($host,$content){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $host);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		return $response;
	}
	function parse_return($content){
		$refId = $this->substring_between($content,'<refId>','</refId>');
		$resultCode = $this->substring_between($content,'<resultCode>','</resultCode>');
		$code = $this->substring_between($content,'<code>','</code>');
		$text = $this->substring_between($content,'<text>','</text>');
		$subscriptionId = $this->substring_between($content,'<subscriptionId>','</subscriptionId>');
		return array ($refId, $resultCode, $code, $text, $subscriptionId);
	}
	function substring_between($haystack,$start,$end){
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false){
			return false;
		}else{
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}	
	function getSimUrl(){
		$sTest = $this->aSettings['an_type'];
		switch ($sTest) {
		    case 'test':
		        return 'https://test.authorize.net/gateway/transact.dll';
		        break;
		    case 'live':
		        return 'https://secure.authorize.net/gateway/transact.dll';
		        break;
		}
	}
	function getArbUrl(){
		$sTest = $this->aSettings['an_type'];
		switch ($sTest) {
		    case 'test':
		        return 'https://apitest.authorize.net/xml/v1/request.api';
		        break;
		    case 'live':
		        return 'https://api.authorize.net/xml/v1/request.api';
		        break;
		}
	}
}

?>