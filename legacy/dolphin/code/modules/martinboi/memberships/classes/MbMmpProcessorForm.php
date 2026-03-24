<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import('BxDolModule');

/**
 * Class used to build payment processor form
 *
 */
class MbMmpProcessorForm{
    
    /**
     * Construct
     */
    function __construct(){       
        $this->oMain = $this->getMain();
        $this->_oDb = $this->oMain->_oDb;
    }
    
    /**
     * Grabs main module object
     */
    function getMain() {
        return BxDolModule::getInstance('HmSubsModule');
    }
    
    /**
     * Builds payment processor form
     *
     */
    function getForm(){
		
	
        // Save settings
        if(bx_get('save_processor_settings')){
			$clean_post = array();	
			foreach($_POST as $key=>$value){
				$clean_post[$key] = str_replace(' ', '', $value);
			}
            setParam('dol_subs_processors', serialize($clean_post));
            $sMessage = 'Payment Processors Successfully Saved';
        }
        
        // Get processor data
        $aProcessorData = unserialize(getParam('dol_subs_processors'));

        $aForm = array(
            'form_attrs' => array(
                'name'     => 'processor_form', 
                'method'   => 'post',
                'action'   => NULL,
            ),
			'inputs' => array(
                
                // Paypal 
				'paypal_header' => array(
                    'type' => 'block_header',
                    'caption' => 'Paypal',
                    'collapsable' => true,
                    'collapsed' => true
				),
				'paypal_active' => array(
				    'type' => 'checkbox',
				    'name' => 'paypal_active',
                    'caption' => 'Enable Paypal',
                    'info' => 'If checked, paypal will be available when purchasing memberships',
                    'attrs' =>  ($aProcessorData['paypal_active'] == 'on') ? array('checked'=>'checked') : array()
				),
                'paypal_account' => array(
				    'type' => 'text',
				    'name' => 'paypal_account',
				    'value' => $aProcessorData['paypal_account'],
                    'caption' => 'Account Email',
                    'info' => 'Your paypal account email address',
                    'attrs' => array('placeholder'=> 'name@domain.com')
				),
                'paypal_type' => array(
				    'type' => 'select',
				    'name' => 'paypal_type',
				    'values' => array('live'=>'Live', 'test'=>'Test'),
                    'value' => $aProcessorData['paypal_type'],
                    'caption' => 'Account Type',
                    'info' => 'Live or Sandbox Account'
				),
				
				// 2Checkout
				'2co_header' => array(
                    'type' => 'block_header',
                    'caption' => '2Checkout',
                    'collapsable' => true,
                    'collapsed' => true
				),
				'2co_active' => array(
				    'type' => 'checkbox',
				    'name' => '2co_active',
                    'caption' => 'Enable 2Checkout',
                    'info' => 'If checked, 2Checkout will be available when purchasing memberships',
                    'attrs' =>  ($aProcessorData['2co_active'] == 'on') ? array('checked'=>'checked') : array()
				),
                '2co_account' => array(
				    'type' => 'text',
				    'name' => '2co_account',
				    'value' => $aProcessorData['2co_account'],
                    'caption' => 'SellerID',
                    'info' => 'Your 2Checkout account number (sid)',
                    'attrs' => array('placeholder'=> '1234567')
				),
				'2co_secret' => array(
				    'type' => 'text',
				    'name' => '2co_secret',
				    'value' => $aProcessorData['2co_secret'],
                    'caption' => 'Secret Word',
                    'info' => 'Found in your account under Site Management',
				),
                '2co_type' => array(
				    'type' => 'select',
				    'name' => '2co_type',
				    'values' => array('live'=>'Live', 'test'=>'Demo'),
                    'value' => $aProcessorData['2co_type'],
                    'caption' => 'Account Type',
                    'info' => 'NOTE: Your account "Demo Setting" must be set to parameter for "test" to work'
				),
                '2co_header_end' => array(
                    'type' => 'block_end'
                ),
				
				
				// Authorize.net
				'an_header' => array(
                    'type' => 'block_header',
                    'caption' => 'Authorize.net (USD)',
                    'collapsable' => true,
                    'collapsed' => true
				),
				'an_active' => array(
				    'type' => 'checkbox',
				    'name' => 'an_active',
                    'caption' => 'Enable Authorize.net',
                    'info' => 'If checked, Authorize.net will be available when purchasing memberships (Only supports USD)',
                    'attrs' =>  ($aProcessorData['an_active'] == 'on') ? array('checked'=>'checked') : array()
				),
                'an_login' => array(
                    'type' => 'text',
                    'name' => 'an_login', 
                    'caption' => _t('_dol_subs_adm_an_login'),
					'value' => $aProcessorData['an_login'],
					'info' => 'Found in your Authorize.net account',
                    'checker' => array (
                        'func' => 'length',
						'params' => array(3,56),
                        'error' => _t('_dol_subs_adm_an_login_err'),
                    ),                  
                ),
				'an_transkey' => array(
                    'type' => 'text',
                    'name' => 'an_transkey', 
                    'caption' => _t('_dol_subs_adm_an_key'),
					'value' => $aProcessorData['an_transkey'],
					'info' => 'Found in your Authorize.net account',
                    'checker' => array (
                        'func' => 'length',
						'params' => array(3,56),
                        'error' => _t('_dol_subs_adm_an_key_err'),
                    ),
                ),
                'an_type' => array(
                    'type' => 'checkbox',
                    'name' => 'an_type', 
					'type' => 'select',
				    'name' => 'an_type',
				    'values' => array('live'=>'Live', 'test'=>'Test'),
                    'value' => $aProcessorData['an_type'],
                    'caption' => 'Account Type',
                    'info' => 'Process test transactions'
                ),
                'an_api' => array(
                    'type' => 'select',
                    'name' => 'an_api',
                	'values' => array(
						'sim' => 'Server Integration Method (SIM)',
						'arb' => 'Automatic Recurring Billing (ARB)',
					),
                    'caption' => 'Choose Integration Method',
                	'value' => $aProcessorData['an_api'],
                    'required' => true,
                    'info' => 'SIM uses single payments only.  To use ARB you must be subscribed to it and have an SSL certificate',
	                'checker' => array (
                        'func' => 'avail',
                        'error' => _t('_dol_subs_adm_payment_proc_err'),
	                ),
                ),
				
				// Currency
				'currency_header' => array(
                    'type' => 'block_header',
                    'caption' => 'Currency',
                    'collapsable' => false,
                    'collapsed' => false
				),
				'currency' => array(
				    'type' => 'select',
				    'name' => 'currency',
				    'values' => $this->getCurrencyArray(),
                    'value' => $aProcessorData['currency'],
                    'caption' => 'Currency',
                    'info' => 'Select the currency for memberships <br/><b>(NOTE: Does not apply to 2Checkout <br/>which is set in your account)</b>'
				),
	            'submit' => array(
	                'type' => 'submit',
	                'name' => 'save_processor_settings',
	                'value' => 'Save Processor Settings',
	            ),
			),
		);

        $oForm = new BxTemplFormView ($aForm);
        $oForm->initChecker();
        $sOutput = !empty($sMessage) ? MsgBox($sMessage,3) : '';
		$sOutput.= $oForm->getCode();

		
    	return $sOutput;
    }

	/**
	 * Generate currency list
	 *
	 */
	function getCurrencyArray($sProvider = 'paypal'){
		
		return array(
			'AUD' => 'Australian Dollar',
			'BGN' => 'Bulgarian Lev',
			'CAD' => 'Canadian Dollar',
			'CHF' => 'Swiss Franc',
			'CZK' => 'Czech Koruna',
			'DKK' => 'Danish Krone',
			'EUR' => 'Euro',
			'GBP' => 'British Pound (GBP)',
			'HKD' => 'Hong Kong Dollar',
			'HUF' => 'Hungarian Forint',
			'INR' => 'Indian Rupee',
			'LTL' => 'Lithuanian Litas',
			'MYR' => 'Malaysian Ringgit',
			'MKD' => 'Macedonian Denar',
			'NOK' => 'Norwegian Krone',
			'NZD' => 'New Zealand Dollar',
			'PLN' => 'Polish Zloty',
			'RON' => 'Romanian New Leu',
			'SEK' => 'Swedish Krona',
			'SGD' => 'Singapore Dollar',
			'USD' => 'U.S. Dollar',
			'ZAR' => 'South African Rand'
		);
		
	}

}