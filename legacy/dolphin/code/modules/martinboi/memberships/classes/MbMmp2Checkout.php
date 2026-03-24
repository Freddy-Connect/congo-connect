<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolModule');
bx_import('BxDolProfilesController');
class MbMmp2Checkout{

    function __construct(){
        $this->oMain = $this->getMain();
		$this->aSettings = unserialize(getParam('dol_subs_processors'));
		$this->_processor = '2Checkout';
	}
    function getMain() {
        return BxDolModule::getInstance('MbMmpModule');
    }
    function getLink(){
		// Setup singlepage/ multipage
          $sLink = 'https://www.2checkout.com/checkout/spurchase';
          return $sLink;
    }
	
	/**
	 * Displays main content for 2Checkout
	 *
	 */
	function showContent($IDLevel){ 		
		$sOutput.= $this->showForm($IDLevel);		
		return $sOutput;
	}
	
	function showForm($iMembId){
		//if(!isAdmin())return;
		$iId = $_COOKIE['memberID'];
		$aPriceInfo	= $this->oMain->_oDb->getMembershipPriceInfo($iMembId);
		$aLevel = $this->oMain->_oDb->getMembershipById($iMembId);
		$aMemberInfo = getProfileInfo($iId);		


/*
 
[Unit] => Days
[Length] => 30
# WEEK | MONTH | YEAR
<form action='https://www.2checkout.com/checkout/purchase' method='post'>
<input type='hidden' name='sid' value='1303908' >
<input type='hidden' name='mode' value='2CO' >
<input type='hidden' name='li_0_type' value='product' >
<input type='hidden' name='li_0_name' value='Example Product Name' >
<input type='hidden' name='li_0_price' value='1.00' >
<input type='hidden' name='li_0_quantity' value='1' >
<input type='hidden' name='li_0_recurrence' value='1 Month' ># WEEK | MONTH | YEAR
<input type='hidden' name='li_0_duration' value='Forever' >Forever or # WEEK | MONTH | YEAR
<input name='submit' type='submit' value='Buy from 2CO' >
</form





<form action='https://www.2checkout.com/checkout/purchase' method='post'>
<input type='hidden' name='sid' value='1303908' >
<input type='hidden' name='mode' value='2CO' >
<input type='hidden' name='li_0_type' value='product' >
<input type='hidden' name='li_0_name' value='Example Product Name' >
<input type='hidden' name='li_0_price' value='1.00' >
<input type='hidden' name='li_0_quantity' value='1' >
<input name='submit' type='submit' value='Buy from 2CO' >
</form
*/

		$aVars = array(
			'action' => $this->getLink(),
			'sid' => $this->aSettings['2co_account'],
			'mode' => '2CO',
			'li_0_type' => 'PRODUCT',
			'li_0_name' => $aLevel['Name'].' '._t('_dol_subs_membership'),
			'li_0_price' => $aPriceInfo['Price'],
			'li_0_quantity' => '1',
			'return_url' => BX_DOL_URL_ROOT.'m/memberships/',
			'x_receipt_link_url' => BX_DOL_URL_ROOT.'m/memberships/2co_response/',
			'merchant_order_id' => $_COOKIE['memberID'].'-'.$aLevel['ID'],
			'bx_if:auto' => array(
				'condition' => ($aPriceInfo['Auto'] == '1'),
		        'content' => array(
		            'li_0_recurrence' => $this->getRecurrance($aPriceInfo),
		            'li_0_duration' => 'Forever',
		        ),
			),
			'bx_if:demo' => array(
				'condition' => ($this->aSettings['2co_type'] == 'test'),
		        'content' => array(
		            'demo' => 'Y',
		        ),
			),
			'bx_if:startup_fee' => array(
				'condition' => ( 1 == 0 ),
		        'content' => array(
		            'li_0_startup_fee' => 1,
		        ),
			)			
		);
		
		
		return  $this->oMain->_oTemplate->parseHtmlByName('2co_payment_form',$aVars); 
    }
	
	function getRecurrance($data){
		$rec = '';
		switch($data['Unit']){
			case 'Days':
				$weeks = $data['Length'] / 7;
				$rec = (int)$weeks . ' Week';
			break;
			case 'Months':
				$rec = $data['Length'] . ' Month';
			break;
		}
		return $rec;
	}
	
	
	
	/**
	 * Process payment
	 *
	 */
	function processPayment($aVars){
		
		//foreach($aVars as $k=>$v)
			//error_log("From processPayment: $k = $v");
			
		$valid_order = false;
		
		// Order number has to be 1 for some reason when testing
		$aVars['order_number'] = ($this->aSettings['2co_type'] == 'test') ? 1 : $aVars['order_number'];
		$string_to_hash = $this->aSettings['2co_secret'];
		$string_to_hash .= $this->aSettings['2co_account'];
		$string_to_hash .= $aVars['order_number'];
		$string_to_hash .= $aVars['total'];
		$hash_to_check = strtoupper(md5($string_to_hash));
		error_log("String to Hash: $string_to_hash");
		error_log("Key : " . $aVars['key']);
		error_log("Hash: $hash_to_check");
		if (strcasecmp($aVars['key'], $hash_to_check) == 0) {
			$valid_order = true;
		}				

		if($valid_order === true){

			list($iUserId, $iMembLevel) = explode('-', urldecode($aVars['merchant_order_id']));
			$aMembLevelInfo = $this->oMain->_oDb->getMembershipById($iMembLevel);
			$aMembLevelPriceInfo = $this->oMain->_oDb->getMembershipPriceInfo($iMembLevel);
			$iPrice = number_format($aMembLevelPriceInfo['Price'], 2);
			$i2CheckoutSid = $this->aSettings['2co_account'];
			$iResponseSid = urldecode($aVars['sid']);
			$sSubId = urldecode($aVars['order_number']);
			switch($aMembLevelPriceInfo['Unit']){
				case 'Days':
					$iMembershipDays = $aMembLevelPriceInfo['Length'];
				break;
				case 'Months':
					$iMembershipDays = $aMembLevelPriceInfo['Length']*30;
				break;
			}
				
			// fraud_status_changed
			if($aVars['message_type'] == 'FRAUD_STATUS_CHANGED' && $aVars['fraud_status'] != 'pass'){
				$iMembLevel = MEMBERSHIP_ID_STANDARD; // Set user back to standard
				//db_res("UPDATE `dol_subs_payments` SET `status`='{$aVars['fraud_status']}' WHERE `txn_id` = '{$aVars['invoice_id']}'");
				// Setup sending a user and an admin an email with a report
			}
			
			// invoice status changed
			if($aVars['message_type'] == 'INVOICE_STATUS_CHANGED' && $aVars['invoice_status'] != 'deposited'){
				$iMembLevel = MEMBERSHIP_ID_STANDARD; // Set user back to standard
				// Setup sending a user and an admin an email with a report
			}

			// Check if payment has already been made
			// Freddy correction erreur n existe pas dans la BDD
			//if( !mysql_num_rows(mysql_query("SELECT `txn_id` FROM `dol_subs_payments` WHERE `txn_id` = '{$aVars['invoice_id']}'"))){
				if( !mysql_num_rows(mysql_query("SELECT `txn_id` FROM `mmp_payments` WHERE `txn_id` = '{$aVars['invoice_id']}'"))){
						
				
				// Freddy correction erreur n existe pas dans la BDD
				//$rInsertPayment = db_res("INSERT INTO `dol_subs_payments` SET 
				$rInsertPayment = db_res("INSERT INTO `mmp_payments` SET 
				
					`txn_id` 		= '{$aVars['invoice_id']}',
					`membership_id` = '{$iMembLevel}',
					`user_id`		= '{$iUserId}',
					`amount` 		= '{$aVars['li_0_price']}',
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

}