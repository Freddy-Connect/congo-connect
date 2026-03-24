<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
function classImport($sClassPostfix, $aModuleOverwright = array()) {
    global $aModule;
    $a = $aModuleOverwright ? $aModuleOverwright : $aModule;
    if (!$a || $a['uri'] != 'memberships') {
        $oMain = BxDolModule::getInstance('MbMmpModule');
        $a = $oMain->_aModule;
    }
    bx_import ($sClassPostfix, $a);
}
bx_import('BxDolModule');
bx_import('BxDolPaginate');
bx_import('BxDolAlerts');
bx_import('BxDolModule');
bx_import('BxDolAdminSettings');

require_once( BX_DIRECTORY_PATH_INC . 'db.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'languages.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );

class MbMmpModule extends BxDolModule {
    var $_iProfileId;
    function MbMmpModule(&$aModule) {      
        parent::BxDolModule($aModule);
        $this->_iProfileId = getLoggedId();
        $GLOBALS['oMbMmpModule'] = &$this;
		$processor = unserialize(getParam('dol_subs_processors'));
		$config = unserialize(getParam('dol_subs_config'));
		$this->aSettings = array_merge((array)$processor, (array)$config);
	}
	
	/**
	 * Home
	 *
	 */
	 
	 
	 
   	function actionHome () {

		if (!$this->_iProfileId) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }
        $this->_oTemplate->pageStart();   

		$message = '';
		$aProcessorData = unserialize(getParam('dol_subs_processors'));
		
		// Payment just made, upgrade membership immediately
		if(isset($_POST['business']) &&  $_POST['business'] == $aProcessorData['paypal_account']){
			self::upgradeMembership($_POST);
			$message = MsgBox(_t('_Membership_Successfully_Upgraded'), 5);
		}
		
        if (isset($_POST['login_text']) && $_POST['login_text']) {
            echo MsgBox(_t(strip_tags($_POST['login_text'])));
        }elseif(bx_get('hm_msg')){
			 echo MsgBox(_t(bx_get('hm_msg')));
		}
		
        classImport('PageMain');
        $oPage = new MbMmpPageMain ($this);
			echo $message;
			echo $oPage->getCode();
		
		$this->_oTemplate->_iPageIndex = 1;
        $this->_oTemplate->addCss ('dolphin_subs.css');
        $this->_oTemplate->addJs ('functions.js');
        $this->_oTemplate->pageCode(_t('_dol_subs_title'), false, false);	
		
		
    }
	
	public function upgradeMembership($post){
		list($iUserId, $iMembLevel) = explode('-', $post['item_number']);
		$aMembLevelInfo = $this->_oDb->getMembershipById($iMembLevel);
		$aMembLevelPriceInfo = $this->_oDb->getMembershipPriceInfo($iMembLevel);
		$iPrice = number_format($aMembLevelPriceInfo['Price'], 2);
		switch($aMembLevelPriceInfo['Unit']){
			case 'Days':
				$iMembershipDays = $aMembLevelPriceInfo['Length'];
			break;
			case 'Months':
				$iMembershipDays = $aMembLevelPriceInfo['Length']*30;
			break;
		}
		
		$this->_oDb->clearMembershipInfo($iUserId);
		$this->_oDb->setUserStatus($iUserId,'Active');			
		deleteUserDataFile($iUserId);	
		self::setMembershipCustom($iUserId, $iMembLevel, $iMembershipDays, true, $post['subscr_id']);
	}

	/**
	 * Admin Settings
	 *
	 */
	function actionSettings(){
		$GLOBALS['iAdminPage'] = 1;

        if (!$GLOBALS['logged']['admin']) { 
            $this->_oTemplate->displayAccessDenied ();
            return;
        }	
        $sCode.= $this->_oTemplate->paymentProcessorSettings();
        $sCode.= $this->_oTemplate->dataForwardSettings();
        $sCode.= $this->_oTemplate->userManagementSettings();
        $this->_oTemplate->pageStart();
			echo $sCode;		
	    $this->_oTemplate->addAdminJs('functions.js');
	    $this->_oTemplate->addAdminCss('admin.css');
        $this->_oTemplate->pageCodeAdmin ('Membership Settings'); 
	}

	/**
	 * Manage Memberships
	 *
	 */
	function actionMemberships(){
		
		$GLOBALS['iAdminPage'] = 1;

        if (!$GLOBALS['logged']['admin']) { 
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart();	
		
			$sCode = $this->_oTemplate->adminCurrentMemberships(); 
			if(!$_GET['adm_mlevels_edit'])
				$sCode.= $this->_oTemplate->adminCreateMembership(); 
			
			echo $sCode;
			
		$aJs = array('functions.js','profiles.js');
	    $this->_oTemplate->addAdminJs($aJs);
	    $this->_oTemplate->addAdminCss('admin.css');
        $this->_oTemplate->pageCodeAdmin ('Membership Setup'); 
	}

	/**
	 * Manage Subscriptions
	 * 
	 */
	function actionSubscriptions(){
		$GLOBALS['iAdminPage'] = 1;

        if (!$GLOBALS['logged']['admin']) { 
            $this->_oTemplate->displayAccessDenied ();
            return;
        }

        $this->_oTemplate->pageStart(); 
			
			$sCode = $this->_oTemplate->subscribersMainCode(); 

			echo $sCode;

	    $this->_oTemplate->addAdminJs('functions.js');
	    $this->_oTemplate->addAdminCss('admin.css');
        $this->_oTemplate->pageCodeAdmin ('Subscriptions'); 
	} 

	/**
	 * Admin Membership Options
	 *
	 */
	function actionMembershipOptions(){
		$GLOBALS['iAdminPage'] = 1;
        if (!$GLOBALS['logged']['admin']) { 
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        $this->_oTemplate->pageStart(); 
			$sCode = $this->_oTemplate->adminMembershipOptions(); 
			echo $sCode;
	    $this->_oTemplate->addAdminJs('functions.js');
	    $this->_oTemplate->addAdminCss(array('admin.css','forms_adv.css'));
        $this->_oTemplate->pageCodeAdmin ('Membership Options'); 
	
	}
	
	/**
	 * Admin Payments Page
	 *
	 */
	function actionPayments(){
		$GLOBALS['iAdminPage'] = 1;
        if (!$GLOBALS['logged']['admin']) { 
            $this->_oTemplate->displayAccessDenied();
            return;
        }
        $this->_oTemplate->pageStart(); 
        	classImport('PagePayments');
        	$oPage = new MbMmpPagePayments($this);
        	$sCode = $oPage->getPaymentStats();
        	$sCode.= $oPage->getPaymentsPageCode();
			echo $sCode;
	    $this->_oTemplate->addAdminJs('functions.js');
	    $this->_oTemplate->addAdminCss(array('admin.css','forms_adv.css'));
        $this->_oTemplate->pageCodeAdmin ('Payments'); 
	}
	
	/**
	 * Show processor options
	 *
	 */
	function actionShowProcessorOptions($sProc){
		$sSettingsUrl = BX_DOL_URL_ROOT.'m/memberships/settings/';
		
		$sBase = BX_DOL_URL_ROOT;

		$sCode = $this->_oTemplate->getProcessorForm($sProc);	

		echo $sCode;exit;
	}
	
	/**
	 * Display payments page AJAX
	 *
	 */
	function actionDisplayPaymentsPage($IDLevel){
       

	   
		// Paypal
		$count = 0;
		$sOutput = '<table class="form_advanced_table">';

		if($this->aSettings['paypal_active'] == 'on'){
			classImport('PayPal');
			$oPaypal = new MbMmpPayPal;			
			$sOutput.= '<tr><td>' . _t('Checkout with Paypal') . '</td>';
			$sOutput.= '<td>'.$oPaypal->showContent($IDLevel) . '</td></tr>';
			
			
			
			
			$count++;
			
		}
		
		// 2Checkout
		if($this->aSettings['2co_active'] == 'on'){			
			classImport('2Checkout');
			$o2Checkout = new MbMmp2Checkout;
			
			$sOutput.= '<tr><td>' . _t('Checkout with 2Checkout') . '</td>';
			$sOutput.= '<td>'.$o2Checkout->showContent($IDLevel) . '</td></tr>';
			$count++;
		}
		
		// Authorize.net
		if($this->aSettings['an_active'] == 'on'){			
			classImport('AuthorizeNet');
			$oAuthorizeNet = new MbMmpAuthorizeNet;
			$sOutput.= '<tr><td>' . _t('Checkout with Authorize.net') . '</td>';
			$sOutput.= '<td>'.$oAuthorizeNet->showContent($IDLevel) . '</td></tr>';
			$count++;
		}
		
		// Empty
		if(!$count){
			$sOutput.= '<tr><td>';
			$sOutput.= MsgBox(_t('_payment_processer_not_setup'));
			$sOutput.= '</td></tr>';
		}
		
		$sOutput.= '</table>';
		
		$aMembership = $this->_oDb->getMembershipData($IDLevel);
		$aData = array(
			'auto' => ($aMembership[0]['Auto'] == '1') ? _t('_dol_subs_adm_mlevel_auto') : _t('_dol_subs_adm_mlevel_single'),
			'currency' => $this->aSettings['currency'],
			'membership_name' => $aMembership[0]['Name'],
			'membership_price' => $aMembership[0]['Price'],
			'membership_unit' => $aMembership[0]['Unit'],
			'membership_length' => $aMembership[0]['Length'],
			'payment_content' => $sOutput,
		);
		echo $this->_oTemplate->parseHtmlByName('choose_payment_method', $aData);
		exit;
		
		
		
	}
	
	
	
	function actionMainMenu(){
		$sAction = bx_get('action');
		$iMenuItemID = bx_get('menu_id');
		if(!isAdmin()) $this->_oTemplate->displayAccessDenied();
		if($sAction == 'edit'){
			echo $this->_oTemplate->showMenuAccessForm($sAction,$iMenuItemID);
		}
		if($sAction == 'save'){
			$this->_oDb->setMenuAccessLevels($_POST);
			echo PopupBox('menu_access', 'Edit Membership Access', MsgBox('Saved Menu Item'));
		}

	}
	
	/**
	 * ACTION: Callback
	 *
	 */
	function actionCallback(){

		foreach($_POST as $k=>$v){
			//error_log('actionCallback: ' . date('d h:m', time()) . ' | ' . $k . ' = ' . $v);
		}

		if($this->_oDb->getSetting('data_forward_1')){
			$this->forwardResponseData($this->_oDb->getSetting('data_forward_1'),$_POST);
		}
		if($this->_oDb->getSetting('data_forward_2')){
			$this->forwardResponseData($this->_oDb->getSetting('data_forward_2'),$_POST);
		}

		$sProcessor = $this->_oConfig->checkResponse($_POST);
		
		switch($sProcessor){
			case 'paypal':
				classImport('PayPal');
				$oPayPal = new MbMmpPayPal;
				return $oPayPal->processPayment($_POST);
			break;
			case '2checkout':
				classImport('2Checkout');
				$o2Checkout = new MbMmp2Checkout;
				return $o2Checkout->processPayment($_POST);
			break;
			case 'authorize':
				classImport('AuthorizeNet');
				$oAuthorizeNet = new MbMmpAuthorizeNet;
				return $oAuthorizeNet->processPayment($_POST);
			break;
			
		}

	}
	
	/**
	 * Displays ARB Order Form
	 *
	 */
	function actionOrder(){
		if(bx_get('action') == 'an_order'){
			classImport('AuthorizeNet');
			$oAuthorizeNet = new MbMmpAuthorizeNet;
			$sCode = $oAuthorizeNet->showArbLargeForm(bx_get('mlevel'));

		}
        $this->_oTemplate->pageStart(); 
			echo $sCode;
	    $this->_oTemplate->addJs('functions.js');
	    $this->_oTemplate->addCss(array('admin.css','forms_adv.css'));
        $this->_oTemplate->pageCode('Order'); 

	}
	
	/**
	 * Standard Ajax Handler
	 *
	 */
	function actionAjax(){
		if(bx_get('action') == 'an_order'){
			classImport('AuthorizeNet');
			$oAuthorizeNet = new MbMmpAuthorizeNet;
			echo PopupBox('order_form', 'Order Form',$oAuthorizeNet->showArbForm(bx_get('mlevel')));
		}
		if(bx_get('action') == 'an_process'){
			classImport('AuthorizeNet');
			$oAuthorizeNet = new MbMmpAuthorizeNet;
			echo $oAuthorizeNet->processArbPayment($_GET);
		}

	}
	
	/**
	 * Forward Response Data
	 * 
	 */
	function forwardResponseData($fullurl,$aVars){
		global $status;
		$fullurl = rtrim($fullurl);
		$newurl = stristr($fullurl, "://");
		$newurl = ltrim($newurl, ':/');
		if ($newurl == '') $newurl = $fullurl;
		$url =  stristr($newurl, "/");
		$host = substr($newurl, 0, strpos($newurl, "/"));
		$port = 80;
		if (ereg("https", $fullurl)) $port = 443;	
		$query_return = '';
		foreach ($aVars as $key => $value) {
	        	$query_return .= "&$key=".urlencode(stripslashes($value));
		};
		$response = '';	
		$fp = @fsockopen( $host, $port, $errno, $errstr, 90); 	
		if (!$fp) { 
	   		echo "socketerr: $errstr ($errno)\n";
		} else {
			fputs( $fp, "POST $url HTTP/1.0\r\n" ); 
			fputs( $fp, "Host: $host\r\n" ); 
			fputs( $fp, "Content-type: application/x-www-form-urlencoded\r\n" ); 
			fputs( $fp, "Content-length: " . strlen($query_return) . "\r\n\n" ); 
			fputs( $fp, "$query_return\n\r" ); 
			fputs( $fp, "\r\n" );
			while (!feof($fp)) { 
				$response .= fgets( $fp, 1024 ); 
			}
			fclose( $fp );
		}
		if (ereg("200 OK", $response)) { //forward accepted
			return 1;
		} 	
		$status = 0;
		return 0;
	}
	
	/**
	 * 2Checkout Redirect back to membership page
	 * Optionally update membership here
	 *
	 */
	function action2CoResponse(){
		//foreach($_REQUEST as $k=>$v)
			//error_log("From action2coResponse: $k: $v");
			
		classImport('2Checkout');
		$o2Checkout = new MbMmp2Checkout;
		$result_msg = $o2Checkout->processPayment($_REQUEST);

		Redirect(BX_DOL_URL_ROOT . 'm/memberships/', array('hm_msg'=>$result_msg), 'post');	
		
	}


	//--- SERVICES ---//
	
	function serviceCurrentMembership($iId){
		$aProfile = getProfileInfo($iId);
		$aMembershipInfo = getMemberMembershipInfo($iId);
		$sMembershipBadge = BxDolService::call('memberships', 'membership_badge', array($aMembershipInfo['ID']));
		$aForm = array(
	        'form_attrs' => array(
	            'id' => 'mem_details',
	            'action' => NULL,
	            'method' => 'post',
	            'enctype' => 'multipart/form-data',
	        ),
	        'params' => array (
	            'db' => array(
	                'table' => 'sys_acl_levels',
	                'key' => 'ID',
	                'uri' => '',
	                'uri_title' => '',
	                'submit_name' => 'create_mlevel'
	            ),
	        ),
			'inputs' => array(
	            'membership_icon' => array(
	                'type' => 'custom',
	                'caption' => '',
	                'content' => $sMembershipBadge,
	            ),
	            'membership_name' => array(
	                'type' => 'custom',
	                'caption' => _t('_dol_subs_membership'),
	                'content' => $aMembershipInfo['Name'],
	            ),
	            'membership_start' => array(
	                'type' => 'custom',
	                'caption' => _t('_dol_subs_start_date'),
	                'content' => ($aMembershipInfo['DateStarts']) ? date('d-m-Y', $aMembershipInfo['DateStarts']) : date('d-m-Y', strtotime($aProfile['DateReg'])),
	            ),
	            'membership_end' => array(
	                'type' => 'custom',
	                'caption' => _t('_dol_subs_exp_date'),
	                'content' => ($aMembershipInfo['DateExpires']) ? date('d-m-Y', $aMembershipInfo['DateExpires']) : _t('_dol_subs_never'),
	            ),
			)
	    );
	    $oForm = new BxTemplFormView($aForm);
	    $oForm->initChecker();
		$sCode = $oForm->getCode(); 		
	    return '<div class="bx-def-bc-margin">'.$sCode.'</div>';
	}
	function serviceMembershipBadge($iMembershipId, $sLarge = false){
		$sBadgeUrl = $this->_oConfig->getIconsUrl();	
		if($iMembershipId == MEMBERSHIP_ID_STANDARD || $iMembershipId == MEMBERSHIP_ID_PROMOTION){
			$sImageName = 'member.png';
		}else{
			$sImageName = $this->_oDb->getMembershipIcon($iMembershipId);
		}
		
		$aVars = array(
			'image' => $sBadgeUrl.$sImageName,
			'bx_if:large' => array(
				'condition' => ($sLarge == false),
				'content' => array(
					'height' => '60',
					'width' => '60',
				)
			)
		);
	    return $this->_oTemplate->parseHtmlByName('badge', $aVars);
	}
	

	function setMembershipCustom($iMemberId, $iMembershipId, $iDays = 0, $bStartsNow = false, $sTransactionId = '')
	{
		$iMemberId = (int)$iMemberId;
		$iMembershipId = (int)$iMembershipId;
		$iDays = (int)$iDays;
		$bStartsNow = $bStartsNow ? true : false;
	
		$SECONDS_IN_DAY = 86400;
	
		if(!$iMemberId)
			$iMemberId = -1;
	
		if(empty($sTransactionId))
			$sTransactionId = 'NULL';
	
		//check if member exists
		$aProfileInfo = getProfileInfo($iMemberId);
		if(!$aProfileInfo)
			return false;
	
		//check if membership exists
		$iRes = (int)db_value("SELECT COUNT(`ID`) FROM `sys_acl_levels` WHERE `ID`='" . $iMembershipId . "' LIMIT 1");
		if($iRes != 1)
			return false;
	
		if($iMembershipId == MEMBERSHIP_ID_NON_MEMBER)
			return false;
	
		$aMembershipCurrent = getMemberMembershipInfo($iMemberId);
		$aMembershipLatest = getMemberMembershipInfo_latest($iMemberId);
	
		/**
		 * Setting Standard membership level
		 */
		if($iMembershipId == MEMBERSHIP_ID_STANDARD) {
			if($aMembershipCurrent['ID'] == MEMBERSHIP_ID_STANDARD)
				return true;
	
			//delete any present and future memberships
			db_res("DELETE FROM `sys_acl_levels_members` WHERE `IDMember`='" . $iMemberId . "' AND (`DateExpires` IS NULL OR `DateExpires`>NOW())");
			if(db_affected_rows() <= 0)
				return false;
		}
	
		if($iDays < 0)
			return false;
	
		$iDateStarts = time();
		if(!$bStartsNow) {
			/**
			 * make the membership starts after the latest membership expires
			 * or return false if latest membership isn't Standard and is lifetime membership
			 */
			if(!is_null($aMembershipLatest['DateExpires']))
				$iDateStarts = $aMembershipLatest['DateExpires'];
			else if(is_null($aMembershipLatest['DateExpires']) && $aMembershipLatest['ID'] != MEMBERSHIP_ID_STANDARD)
				return false;
		} else
			db_res("DELETE FROM `sys_acl_levels_members` WHERE `IDMember`='" . $iMemberId . "'"); //delete previous profile's membership level
	
		/**
		 * set lifetime membership if 0 days is used.
		 */
		$iDateExpires = $iDays != 0 ? (int)$iDateStarts + $iDays * $SECONDS_IN_DAY : 'NULL';
		db_res("INSERT `sys_acl_levels_members` (`IDMember`, `IDLevel`, `DateStarts`, `DateExpires`, `TransactionID`) VALUES ('" . $iMemberId . "', '" . $iMembershipId . "', FROM_UNIXTIME(" . $iDateStarts . "), FROM_UNIXTIME(" . $iDateExpires . "), '" . $sTransactionId . "')");
		if(db_affected_rows() <= 0)
		   return false;
	
		//Set Membership Alert
		bx_import('BxDolAlerts');
		$oZ = new BxDolAlerts('profile', 'set_membership', '', $iMemberId, array('mlevel'=> $iMembershipId, 'days' => $iDays, 'starts_now' => $bStartsNow, 'txn_id' => $sTransactionId));
		$oZ->alert();
	
		bx_import('BxDolEmailTemplates');
		$oEmailTemplate = new BxDolEmailTemplates();
		$aTemplate = $oEmailTemplate->getTemplate('t_MemChanged', $iMemberId);
	
		$aMembershipInfo = getMembershipInfo($iMembershipId);
		$aTemplateVars = array(
			'MembershipLevel' => $aMembershipInfo['Name']
		);
	
		//sendMail( $aProfileInfo['Email'], $aTemplate['Subject'], $aTemplate['Body'], $iMemberId, $aTemplateVars);
	
		return true;
	}

}