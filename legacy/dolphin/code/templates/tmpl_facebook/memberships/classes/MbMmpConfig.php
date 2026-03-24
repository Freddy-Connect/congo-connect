<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolConfig');

class MbMmpConfig extends BxDolConfig {

	function MbMmpConfig($aModule) {
	    parent::BxDolConfig($aModule);    
		$this->_sIconsFolder = 'harvest/memberships/images/icons/';
	}
	function getIconsUrl() {		
	    return BX_DOL_URL_ROOT . 'media/images/membership/';
	}
	function getIconsPath() {
	    //return BX_DIRECTORY_PATH_MODULES . $this->_sIconsFolder;
		return $sFilePath = BX_DIRECTORY_PATH_ROOT . 'media/images/membership/';
	}
	
	
	
	/**
	 * Get a list of the available processors
	 *
	 */
	function getPaymentProcessors(){
		$aProcessors = array(
			'paypal' => 'Paypal',
			'authorize' => 'Authorize.net',
			'2checkout' => '2Checkout'
		);
	 	return $aProcessors;
	}
	function checkResponse($aVars){
		$aKeys = array();
		if(is_array($aVars)){
		    foreach ($aVars as $key => $value) {
				$aKeys[] = $key;
				//db_res("INSERT INTO `dol_subs_payments` SET `txn_id` = '{$key}'");
				//error_log("$key = $value<br/>");
		    }
			if (in_array('txn_type', $aKeys)) {
				return 'paypal';
			} elseif (in_array('vendor_id', $aKeys )){
				return '2checkout';
			} elseif (in_array('x_response_code', $aKeys)){
				return 'authorize';
			}
			
		}

	}
	function safe_pages(){
		$aSafe = array('_Home','_Log Out','_Unregister','_About','_TERMS_OF_USE_H','_PRIVACY_H','_help','_FAQ','_Contact','_dol_subs_mem_info','_Account Home','_dol_subs_mem_info', '_dol_subs_title','_bx_ava_avatar');
		return $aSafe;
	}
	function safe_pages_guest(){
		$aSafeGuest = array('_Home','_Log Out','_Unregister','_About','_TERMS_OF_USE_H','_PRIVACY_H','_help','_FAQ','_Contact','_Account');
		return $aSafeGuest;
	}
	function getMonths(){
		$aMonths = array(
			'01' => '01 - Jan',
			'02' => '02 - Feb',
			'03' => '03 - Mar', 
			'04' => '04 - Apr', 
			'05' => '05 - May',
			'06' => '06 - Jun',
			'07' => '07 - Jul', 
			'08' => '08 - Aug', 
			'09' => '09 - Sep',
			'10' => '10 - Oct',
			'11' => '11 - Nov', 
			'12' => '12 - Dec'
		);
		return $aMonths;
	}
	
	/**
	 * Setup form options for each payment processor
	 *
	 */
	function getProcessorOptions($sProcessor){
		
		switch($sProcessor){
			
			// Paypal options
			case 'paypal':				
		        $aInputData = array(
					'paypal_header' => array(
						'type' => 'block_header',
						'caption' => 'Paypal Settings',
						'collapsable' => true,
						'collapsed' => true
					),
					'paypal_active' => array(
						'type' => 'checkbox',
						'name' => 'paypal_active',
						'value' => '',
						'caption' => 'Enable Paypal',
						'info' => 'If checked, paypal will be available when purchasing memberships'
					),
					'paypal_account' => array(
						'type' => 'text',
						'name' => 'paypal_account',
						'value' => '',
						'caption' => 'Account Email',
						'info' => 'Your paypal account email address',
						'attrs' => array('placeholder'=> 'name@domain.com')
					),
					'paypal_type' => array(
						'type' => 'select',
						'name' => 'paypal_type',
						'values' => array('live'=>'Live', 'test'=>'Test'),
						'value' => '',
						'caption' => 'Account Type',
						'info' => 'Live or Sandbox Account'
					),
				);				
			break;
		
			
			
		}
		
		return $aInputData;
		
	}
	
	
}