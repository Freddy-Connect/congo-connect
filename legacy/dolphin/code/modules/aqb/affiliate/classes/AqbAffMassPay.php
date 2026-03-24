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

class AqbAffMassPay {
  /**
    * Error array
    * @var array
    */
   var $_aErrors = array();

  /**
    * Credential API Signature
    * @var array
    */

   /**
	* Real link - https://api-3t.paypal.com/nvp
	* sandbox - https://api-3t.sandbox.paypal.com/nvp
	* @var string
       */
   //var $_sEndPoint = 'https://api-3t.sandbox.paypal.com/nvp';
   var $_sEndPoint = 'https://api-3t.paypal.com/nvp';
   
	 /**
	    * Real link - www.paypal.com
	    * sandbox - www.sandbox.paypal.com
	    * @var string
	    */
   //var $_sValidateDataLink = 'www.sandbox.paypal.com';
   var $_sValidateDataLink = 'www.paypal.com';
   
   /**
    * Version API
    * @var string
    */
   var $_sVersion = '74.0';

   var $_bSSL = true;
   
   var $_aDefaultParams = array(
         'METHOD' => 'MassPay', 
		 'RECEIVERTYPE' => 'EmailAddress'
	);
   
   function AqbAffMassPay($aParams = array()){
		$this -> _aDefaultParams['CURRENCYCODE'] = $aParams['code'];
		$this -> _aDefaultParams['USER'] = $aParams['user'];
		$this -> _aDefaultParams['PWD'] = $aParams['pwd'];
		$this -> _aDefaultParams['SIGNATURE'] = $aParams['signature'];
		$this -> _aDefaultParams['VERSION'] = $this -> _sVersion;
	}   
  /**
    *  Build string query 
    * @param string $sMethod - Method
    * @param array $aParams  - Aditionall parameters 
    * @return array / boolean Response array / boolean false on failure
    */
   function request($aParams = array()) {
	 $this -> _aErrors = array();
	 $this -> _aDefaultParams['EMAILSUBJECT'] = _t('_aqb_commission_mass_payment_mail_subject');     
	 
	 $sRequest = http_build_query($this -> _aDefaultParams + $aParams);

     $curlOptions = array (
         CURLOPT_URL => $this -> _sEndPoint,
         CURLOPT_VERBOSE => 1,
         CURLOPT_SSL_VERIFYPEER => false,
         CURLOPT_SSL_VERIFYHOST => false,
         CURLOPT_RETURNTRANSFER => 1,
         CURLOPT_POST => 1,
         CURLOPT_POSTFIELDS => $sRequest
      );

      $ch = curl_init();
      curl_setopt_array($ch,$curlOptions);
	
      // Send request
      $response = curl_exec($ch);
    
	  // Check responds cURL
      if (curl_errno($ch)) {
         $this -> _aErrors = curl_error($ch);
         curl_close($ch);
         return false;
      } else  
	  {
         curl_close($ch);
         $responseArray = array();
         parse_str($response, $responseArray);
         return $responseArray;
      }
   }
   
  /**
    *  Build string query 
    * @param string $aData - $_POST
    * @return array
    */
   
   function validateData(&$aData){
	  	if($aData['payment_status'] != 'Completed' ) return array('code' => 0, 'message' => _t('_aqb_commission_mass_payment_is_not_completed'));
           
        $sRequest = 'cmd=_notify-validate';
        foreach($aData as $sKey => $sValue) 
      		$sRequest .= '&' . urlencode($sKey) . '=' . urlencode( process_pass_data($sValue));
			   
        $sHeader = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    	$sHeader .= "Host: " . $this -> _sValidateDataLink . "\r\n";
    	$sHeader .= "Content-Type: application/x-www-form-urlencoded\r\n";
    	$sHeader .= "Content-Length: " . strlen($sRequest) . "\r\n";
    	$sHeader .= "Connection: close\r\n\r\n";
    	
    	$iErrCode = 0;
    	$sErrMessage = "";
		
    	if($this -> _bSSL)
    		$rSocket = fsockopen("ssl://" . $this -> _sValidateDataLink, 443, $iErrCode, $sErrMessage, 60);
    	else
    		$rSocket = fsockopen("tcp://" . $this -> _sValidateDataLink, 80, $iErrCode, $sErrMessage, 60);

    	if(!$rSocket) return array('code' => 2, 'message' => _t('_aqb_commission_mass_payment_connect_problems', $sErrMessage));

    	fputs($rSocket, $sHeader);
        fputs($rSocket, $sRequest);

    	$sResponse = '';
        while(!feof($rSocket))
            $sResponse .= fread($rSocket, 1024);
    	fclose($rSocket);

    	list($sResponseHeader, $sResponseContent) = explode("\r\n\r\n", $sResponse);

		$aContent['content'] = explode("\n", $sResponseContent);
		
    	array_walk($aContent['content'], create_function('&$arg', "\$arg = trim(\$arg);"));
        
		if(strcmp($aContent['content'][0], "INVALID") == 0)	return array('code' => -1, 'message' => _t('_aqb_commission_mass_payment_wrong_transaction'));
			else 
		if(strcmp($aContent['content'][0], "VERIFIED") != 0) return array('code' => 3, 'message' => _t('_aqb_commission_mass_payment_wrong_verification_status'));
				
		return array('code' => 1);
   }
}
?>