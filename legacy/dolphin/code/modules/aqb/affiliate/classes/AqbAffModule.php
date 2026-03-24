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
require_once('AqbAffRegister.php');

class AqbAffModule extends BxDolTwigModule {
	
	/**
	 * Constructor
	 */
	function AqbAffModule($aModule) {
	    parent::BxDolModule($aModule);
		$this->_oConfig->init($this->_oDb);
		$this -> _sPrefix = $this -> _oConfig -> getUri();
		$this->iUserId = $GLOBALS['logged']['member'] || $GLOBALS['logged']['admin'] ? $_COOKIE['memberID'] : 0;
	}
	
	function isAdmin(){
		return isAdmin($this->iUserId);
	}
    
	function actionAdministration ($sUrl = '', $iID = '') {

        if (!$this->isAdmin()) {
            $this->_oTemplate->displayAccessDenied ();
            return;
        }        
	
		if (!empty($_POST['banners']))
		foreach($_POST['banners'] as $iId){
			if ($_POST['aqb-aff-delete']) $this -> _oDb -> deleteBanner((int)$iId);
			elseif ($_POST['aqb-aff-approve'])  $this -> _oDb -> approveBanner((int)$iId);
		}
		
		if (!empty($_POST['aqb-aff-delete-members']))
		foreach($_POST['members'] as $iId){
			profile_delete((int)$iId);			
		}
		
		if ((int)$_GET['delete']) $this -> _oDb -> deleteTransactionsItem((int)$_GET['delete']);
		
		if ((int)$_POST['transaction_id'] && $_POST['submit_transaction']) $this -> _oDb -> processCommissionManyally((int)$_POST['transaction_id'], $_POST['transaction']);
	
		if ($sUrl == 'membership' && !(int)$iID) $iID = 2; 
		
        $this->_oTemplate->pageStart();

        $aMenu = array(
			'membersinfo' => array(
                'title' => _t('_aqb_aff_admin_membersinfo'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/membersinfo',
                '_func' => array ('name' => 'getMembersInfo', 'params' => array()),
            ),
			'commissions' => array(
                'title' => _t('_aqb_aff_admin_commissions'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/commissions',
                '_func' => array ('name' => 'getCommissionsInfo', 'params' => array($iID)),
            ),			
            'membership' => array(
                'title' => _t('_aqb_aff_admin_membership_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/membership',
                '_func' => array ('name' => 'getMembershipPanel', 'params' => array($iID)),
            ),
			'aff_banners' => array(
                'title' => _t('_aqb_aff_admin_affiliates_banners'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/aff_banners',
                '_func' => array ('name' => 'createNewBannerSection', 'params' => array()),
            ),
			'matrix' => array(
                'title' => _t('_aqb_aff_admin_affiliates_forced_matrix'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/matrix',
                '_func' => array ('name' => 'getForcedMatrix', 'params' => array()),
            ),
            'settings' => array(
                'title' => _t('_aqb_aff_admin_settings'), 
                'href' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/settings',
                '_func' => array ('name' => 'getSettingsPanel', 'params' => array()),
            )
        );

		$this->_oTemplate->addAdminCss(array('admin.css', 'forms_extra.css','forms_adv.css', 'general.css', 'main.css', 'profiles.css'));
        $this->_oTemplate->addAdminJs(array('admin.js','main.js', 'jquery.ui.all.min.js', 'jquery.dolPopup.js'));
		
        if (empty($aMenu[$sUrl]))
            $sUrl = 'membersinfo';

        $aMenu[$sUrl]['active'] = 1;

        $sContent = call_user_func_array (array($this -> _oTemplate, $aMenu[$sUrl]['_func']['name']), $aMenu[$sUrl]['_func']['params']);
        echo $this->_oTemplate->adminBlock ($sContent, $aMenu[$sUrl]['title'], $aMenu);

	    
		if ($sUrl == 'membership'){ 
			echo $this -> _oTemplate -> getMemLevelsBlocks($iID);
			echo $this -> _oTemplate -> getMemLevelsUpgradeSettings($iID);
		}
		
		if ($sUrl == 'aff_banners') echo $this -> _oTemplate ->	getAffBannersPanel(); 
		if ($sUrl == 'matrix') echo $this -> _oTemplate ->	showMatrixPanel();      
        
		$this->_oTemplate->pageCodeAdmin(_t('_aqb_aff_administration'));
    }
    
	function actionEditBanner($iID){
	  if (!$this->isAdmin()) return '';
	  echo $this -> _oTemplate -> getEditBannerForm($iID);	
	  exit;
	}
    
	function actionRefreshMatrix(){
	  if (!$this->isAdmin()) return '';
	  $sContent = $this -> _oTemplate -> getRefreshMatrixPanel();
	  
	  echo PopupBox('aqb_popup', _t('_aqb_aff_matrix_refresh'), $sContent);
	  exit;
	}
	
	function actionStartRefresh(){
		if (!$this->isAdmin()) return '';
		
		ignore_user_abort(true);
		set_time_limit(0);
		
		$aResult = array('code' => $this -> _oDb -> applyNewMatrix() === false ? 0: 1, 'message' => _t('_aqb_aff_nothing_found'));
		header('Content-Type:text/javascript');
		$oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}	
	
	function actionViewInfo($iLevel = 0){
		if (!$this->isLogged()) return '';
	   $sContent = $this -> _oTemplate -> getPreviewMatrixInfo($iLevel);
	   
	   $iWidth = $this -> _oDb -> getForcedMatrixWidth();
	   
	   echo PopupBox('aqb_popup', _t('_aqb_aff_matrix_info_preview', (int)$iWidth ? '' : _t('_aqb_aff_forced_matrix')), $sContent);
	   exit; 	
	}
	
	function actionGetMyCommissions(){
		if (!$this->isLogged()) return '';
	
		$aPrice = $this -> _oDb -> getBalance($this -> iUserId);
		if ($aPrice['price'] > 0 && (float)$aPrice['price'] < $this -> _oConfig -> getCommissionLimitation()){
			$aResult = array('code' => 1, 'message' =>  _t('_aqb_aff_commission_limit_to_send_request', $this -> _oConfig -> getCommissionLimitation(), html_entity_decode(htmlspecialchars_decode($this -> _oConfig -> getCurrencySign()))));
			header('Content-Type:text/javascript');
			$oJson = new Services_JSON();
			echo $oJson->encode($aResult);
			exit;
		}

		if ((float)$aPrice['price'] || (int)$aPrice['points']){ 
			$aProfileInfo = getProfileInfo($this -> iUserId);
			$iResult = sendMail( $this -> _oDb -> getParam('site_email'),  _t('_aqb_aff_commission_request_email_subject'), _t('_aqb_aff_get_commission_admin_email', '<a href="'.getProfileLink($this -> iUserId). '">' . $aProfileInfo['NickName'] . '</a>', $this -> _oConfig -> getCurrencySign() . $aPrice['price']), $this -> iUserId);
			if ($iResult)
				$aResult = array('code' => 0, 'message' =>  _t('_aqb_aff_commission_to_pending_send_email'));
			else 
				$aResult = array('code' => 1, 'message' =>  _t('_aqb_aff_commission_not_to_pending_send_email'));
		}

		if ((($this -> _oDb -> isPointsSystemInstalled() && (int)$aPrice['points']) || (float)$aPrice['price']) && (isset($aResult['code']) && !(int)$aResult['code'])){ 
			if ($this -> _oDb -> makeTransaction($this -> iUserId)) $this -> _oDb -> clearJournal($this -> iUserId);			
		}
		elseif (!(int)$aPrice['points'] && !(float)$aPrice['price']) 
			 $aResult = array('code' => 1, 'message' => _t('_aqb_aff_commission_can_not_be_paid_empty'));
		else $aResult = array('code' => 1, 'message' => _t('_aqb_aff_commission_can_not_be_paid'));
		
		header('Content-Type:text/javascript');
		$oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}

	function actionViewBanner($iID){
	   if (!$this->isAdmin()) return '';
	   $sBannerForm = $this -> _oTemplate -> getBannerPreviewForm((int)$iID, $this -> iUserId);
	   echo PopupBox('aqb_popup', _t('_aqb_aff_banner_preview'), $sBannerForm);
	   exit;
	}
	
	function actionRegisterBanner($iId, $iMemberId){
		if (!$this -> _oDb -> isBannerActive((int)$iId) || !$this -> _oConfig -> isAffiliateEnabled()) return ''; 
		
		$aRegistr = new  AqbAffRegister();
		$aRegistr -> registerMember((int)$iId, (int)$iMemberId);
		exit;
	}	
	
	function serviceGetReferralBlock($iId){
		if (!$this->isLogged()) return '';
		
		$this -> _oTemplate -> addCss('main.css');
		$this -> _oTemplate -> addJs('main.js');
		return $this -> _oTemplate -> getReferralBlock($iId);
	}	
	
	function serviceGetMyReferralLink($sPage){
		if (!$this->isLogged()) return '';
		
		return $this -> _oTemplate -> getReferralLinkBlock($sPage, $this -> iUserId);
	}
	
	function actionSendInvitation(){
		if (!$this -> _oConfig -> isAllowToSendInvitations()) return '';

		if (!$this->isLogged()){
			$aResult = array('code' => 1, 'message' => $aUploadResult);
			header('Content-Type:text/javascript');
			$oJson = new Services_JSON();
			echo $oJson->encode($aResult);
			exit;	
		};
		
		$sEmails = bx_get('aqb_emails');
		$sMessage = bx_get('aqb_message');

		$aEmail = preg_split('/[\s,]+/', $sEmails, -1, PREG_SPLIT_NO_EMPTY);
	
		$iEmails = count($aEmail);
		$iAllowedNumber = (int)$this -> _oConfig -> getMaximumEmails();
		
		$iGetAleadySent = $this -> _oDb -> getSentNumber($this -> iUserId);
		$iWantToSend =  $iGetAleadySent >= 0 ? $iEmails + $iGetAleadySent : 1000;
		
		if ($iAllowedNumber < $iWantToSend){
			$aResult = array('code' => 1, 'message' => _t('_aqb_aff_max_email_error', $iAllowedNumber - $iGetAleadySent, $iGetAleadySent, $iAllowedNumber));
			header('Content-Type:text/javascript');
			$oJson = new Services_JSON();
			echo $oJson->encode($aResult);
			exit;	
		}
		
		$oEmailTemplates = new BxDolEmailTemplates();	
		if (strlen($sMessage)) $sMessage = substr(process_db_input($sMessage, BX_TAGS_STRIP), 0, $this -> _oConfig -> getMaximumSymbolsForMessage());
		
		
		$oEmailTemplates = new BxDolEmailTemplates();	
		$aKeys = array(
            'MembersMessage' => (!strlen($sMessage) || !$this -> _oConfig -> isAllowToAddMembersMessage()) ? _t('_aqb_aff_invitation_message', $this -> _oDb -> getParam('site_title')) : process_db_input($sMessage, BX_TAGS_SPECIAL_CHARS),
            'MembersInvitationLink' => '<a href="' . $this -> _oConfig -> getReferralLink($this -> iUserId) . '">' . $this -> _oConfig -> getReferralLink($this -> iUserId) . '</a>'
		);
		$aMessage = $oEmailTemplates->parseTemplate('t_AqbAffMemberInvitation', $aKeys, $this -> iUserId);
	
		$oChecker = new BxDolFormCheckerHelper();
		$bUsingQueue = $this -> _oConfig -> isUsingQueue();
		
		$iCounter = 0;
		
		foreach($aEmail as $k => $v){
				$bResult = false;
				if (!$oChecker -> checkEmail($v)) continue;
				
				if ($bUsingQueue) $bResult = $this -> _oDb -> addToQueue($v, $aMessage['body'], $aMessage['subject']);
				else $bResult = sendMail( $v,  $aMessage['subject'], $aMessage['body']);
				
				if ($bResult) {
					$this -> _oDb -> sendEmails($this -> iUserId, 1);				
					$iCounter++;
		
					$oZ = new BxDolAlerts('aqb_affiliate', 'send', $this -> iUserId, $this -> iUserId);
					$oZ->alert();
				}
		}
			
		if ($iCounter)
			$aResult = array('code' => 0, 'message' => _t('_aqb_aff_successfully_sent', $iCounter));
		else 
			$aResult = array('code' => 1, 'message' => _t('_aqb_aff_not_sent'));	
		
		header('Content-Type:text/javascript');
		$oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;	
	}
	
	function actionGetInviteForm(){
	   if (!$this->isLogged()) return '';
	   $sInviteForm = $this -> _oTemplate -> getInvitationForm();
	   echo PopupBox('aqb_popup', _t('_aqb_aff_send_invintation'), $sInviteForm);
	   exit; 
	}
	
	function actionUpdateBanner(){
		$aFileInfo = array();
		$aFileInfo['id'] = bx_get('banner_id');
		$aFileInfo['form_name'] = bx_get('form_name');
	
		if (!$this->isAdmin() || !(int)$aFileInfo['id']) return '';

		if ($aFileInfo['form_name'] == 'params'){
			$aFileInfo['name'] = bx_get('banner_name');
			
			$aFileInfo['width'] = bx_get('banner_width');	
			$aFileInfo['height'] = bx_get('banner_height');		
		
			$aFileInfo['link'] = bx_get('banner_link');		
		
			if (!empty($_FILES['new_banner']['tmp_name'])) {
				$aUploadResult = $this -> _oTemplate -> uploadImage();
				
				if (!is_array($aUploadResult)) {
					$aResult = array('code' => 1, 'message' => $aUploadResult);
					header('Content-Type:text/javascript');
			        $oJson = new Services_JSON();
			        echo $oJson->encode($aResult);
					exit;
				}
				$aFileInfo = array_merge($aFileInfo, $aUploadResult);	
			}
			if ($this -> _oDb -> updateBanner($aFileInfo)) $aResult = array('code' => 0, 'message' => _t('_aqb_aff_changes_applied'), 'form_name' => $aFileInfo['form_name']);
			else $aResult = array('code' => 1, 'message' => _t('_aqb_aff_changes_error'));
			
		}elseif ($aFileInfo['form_name'] == 'commission_points'){
			if ($this -> _oDb -> addPriceInPoints($aFileInfo)) $aResult = array('code' => 0, 'message' => _t('_aqb_aff_changes_applied'), 'form_name' => $aFileInfo['form_name']);
			else $aResult = array('code' => 1, 'message' => _t('_aqb_aff_changes_error'));
		}elseif ($aFileInfo['form_name'] == 'commission_price'){
			if ($this -> _oDb -> addPriceInCurrency($aFileInfo)) $aResult = array('code' => 0, 'message' => _t('_aqb_aff_changes_applied'), 'form_name' => $aFileInfo['form_name']);
			else $aResult = array('code' => 1, 'message' => _t('_aqb_aff_changes_error'));
		}		
		
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
        echo $oJson->encode($aResult);
		exit;
	}
	
	function actionReferrals($sType = 'table', $iUserId = 0){
		if (!$this -> _oConfig -> isReferralsEnabled()){
			$this -> _oTemplate-> pageStart();
			echo MsgBox(_t('_aqb_aff_modules_is_not_alowed'));
			$this -> _oTemplate -> pageCode(_t('_aqb_aff_my_referrals'), false, false);
			exit;
   		}
		
		if (!$this->isLogged()) {
			$this -> _oTemplate-> pageStart();
            echo MsgBox(_t('_aqb_aff_have_to_login'));
			$this -> _oTemplate -> pageCode(_t('_aqb_aff_my_referrals'), false, false);
			exit;
        }
		
		bx_import ('PageMyRef', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageMyRef';
        $oPage = new $sClass($this, (int)$iUserId && $this->isAdmin() ? (int)$iUserId : $this -> iUserId,  $sType);
		$this->_oTemplate->pageStart();
		
		if ($this -> isAdmin() && (int)$iUserId && (int)$iUserId != getLoggedId()){
			$iId = $this -> _oDb -> getSubMenuId('history'); 
			unset($GLOBALS['oTopMenu'] -> aTopMenu[$this -> _oDb -> getAffSubMenu()]);
			$GLOBALS['oTopMenu'] -> aTopMenu[$iId]['Link'] = BX_DOL_URL_ROOT . $this-> _oConfig-> getBaseUri() . 'history/' . (int)$iUserId;	
		}		
    	
		echo $oPage->getCode();
	
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_aqb_aff_my_referrals'), false, false);
	}
    
	function actionAffiliates(){
		if (!$this -> _oConfig -> isAffiliateEnabled()){
			$this -> _oTemplate-> pageStart();
			echo MsgBox(_t('_aqb_aff_modules_is_not_alowed'));
			$this -> _oTemplate -> pageCode(_t('_aqb_aff_my_referrals'), false, false);
			exit;
   		}
		
		if (!$this->isLogged()) {
			$this -> _oTemplate-> pageStart();
            echo MsgBox(_t('_aqb_aff_have_to_login'));
			$this -> _oTemplate -> pageCode(_t('_aqb_aff_affiliate_page'), false, false);
			exit;
        }
		
		bx_import ('PageMyAffiliate', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageMyAffiliate';
        $oPage = new $sClass ($this, $this -> iUserId);
		$this->_oTemplate->pageStart();
		   
    	echo $oPage->getCode();
	
        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_aqb_aff_affiliate_page'), false, false);
	}
	
	function actionHistory($iProfileId = 0){
		if (!$this->isLogged()) {
			$this -> _oTemplate-> pageStart();
            echo MsgBox(_t('_aqb_aff_have_to_login'));
			$this -> _oTemplate -> pageCode(_t('_aqb_aff_my_referrals'), false, false);
			exit;
        }
		
		bx_import ('PageHistory', $this->_aModule);
        $sClass = $this->_aModule['class_prefix'] . 'PageHistory';
        $oPage = new $sClass ($this, $this -> isAdmin() && (int)$iProfileId ? $iProfileId : $this -> iUserId);
		$this->_oTemplate->pageStart();
		
		if ($this -> isAdmin() && (int)$iProfileId && (int)$iProfileId != getLoggedId()){
			$iRefId = $this -> _oDb -> getSubMenuId(); 
			unset($GLOBALS['oTopMenu'] -> aTopMenu[$this -> _oDb -> getAffSubMenu()]);
			$GLOBALS['oTopMenu'] -> aTopMenu[$iRefId]['Link'] = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'referrals/table/'  . $iProfileId;	
		}
		
    	echo $oPage->getCode();

        $this->_oTemplate->addJs ('main.js');
        $this->_oTemplate->addCss ('main.css');
        $this->_oTemplate->pageCode(_t('_aqb_aff_my_history'), false, false);
    }
	
	function deleteHistoryItems(&$aArray){
	   foreach($aArray['history'] as $k => $v){
		  if ((int)$v) $this -> _oDb -> deleteHistoryItem($v);	
	   }	   
	}
    
	function actionAllBanners(){
		if (!$this->isAdmin()) {
            return '';
        }
   
		echo $this->_oTemplate-> getBannersTable($_REQUEST);
		exit;	
	}
	
	function actionCleanHistory($iProfileID){
		$aResult = array('code' => 1, 'message' => _t('_aqb_aff_can_not_clean_history'));
		
		if((int)$iProfileID && $this->isAdmin()){
			if ($this -> _oDb -> deleteProfileHistory($iProfileID)) $aResult = array('code' => 0, 'message' => _t('_aqb_aff_successfully_clean_history', getNickName($iProfileID)));
		}
				
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionCleanAllHistory(){
		$aResult = array('code' => 1, 'message' => _t('_aqb_aff_was_not_clean'));
		
		if($this->isAdmin())
		{
			$this -> _oDb -> cleanHistory();
			$aResult = array('code' => 0, 'message' => _t('_aqb_aff_all_history_cleaned'));
		}	
				
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function actionMembers(){
		if (!$this->isAdmin()) {
            return '';
        }
   
		echo $this -> _oTemplate -> getMembersPanel($_REQUEST);
		exit;	
	}
	
	function actionReferralsPanel(){
		if (!$this->isLogged()) {
            return '';
        }
   
		header('Content-Type: text/html; charset=UTF-8');
		echo $this -> _oTemplate -> getReferralsInfo($_REQUEST);
		exit;	
	}
	
	function actionCommissions(){
		if (!$this->isAdmin()) {
            return '';
        }
   
		echo $this -> _oTemplate -> getCommissionsPanel($_REQUEST);
		exit;	
	}
	
	function serviceIntegrateWithPoints(){
		$this -> _oDb -> pointsIntegration();
	}
	
	function serviceUninstallIntegration(){
		$this -> _oDb -> uninstallPointsIntegration();
	}
	
	function serviceGetReferralsIndexBlock(){
		$this -> _oTemplate -> addCss('main.css');
		$this -> _oTemplate -> addJs('main.js');
		return $this -> _oTemplate -> getReferralsIndexBlock($this);
	}	
	
	function serviceGetMyInvitedMembersBlock($iProfileId){
		$this -> _oTemplate -> addCss('main.css');	
		$this -> _oTemplate -> addJs('main.js');
		return $this -> _oTemplate -> getMyInvitedMembers($iProfileId);	
	}
	
	function serviceGetItems($iVendorId){
		$aTransactions = $this -> _oDb ->  getProfileTransactions($iVendorId);
			
		foreach($aTransactions as $k => $v){
		
		$aResult[] = array(
    	       'id' => $v['id'],
    	       'title' => _t('_aqb_aff_txt_commission_cart_title'),
    	       'description' => 'asdasda',
    	       'url' =>BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/commissions',
    	       'price' => $aTransactions['price']
           );
		}   
        
		return  $aResult;  
	}
	
	function serviceGetCartItem($iClientId, $iItemId) {
		if (!$iItemId) return array();
	
        $aItem = $this -> _oDb -> getTransactionInfo($iItemId);
		if(empty($aItem)) return array();

		return array (
			   'id' => $iItemId,
    	       'title' => _t('_aqb_aff_txt_commission_cart_title'),
    	       'description' => '',
    	       'url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/commissions',
    	       'price' => $aItem['price']
         );
 	}
	
	function serviceRegisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) {
		$aInfo = array();
		
		if ($this -> _oDb -> paidCommission($iItemId, $sOrderId)) {
			$aInfo = $this -> _oDb -> getTransactionInfo($iItemId);
			
			$aData = $this -> _oDb -> getEmailTemplate($aInfo);					
			$this -> _oDb -> addToQueue($aData['Email'], $aData['Subject'], $aData['Body']);
					
			if ($this -> _oDb -> isPointsSystemInstalled() && (int)$aInfo['points']){ 
			    $aAction = array('id' => 0, 'title' => '_aqb_aff_assign_points', 'points' => (int)$aInfo['points'], 'time' => $aInfo['date_end']);
				$this -> _oDb -> assignPoints((int)$aInfo['member_id'], $aAction);
			}	
		}
		
		return $aInfo;
	}
	
	function serviceUnregisterCartItem($iClientId, $iSellerId, $iItemId, $iItemCount, $sOrderId) {
		if(!$this -> isLogged()) return false;
		$this -> _oDb -> makeUnpaid($iItemId);
	}
	
	function actionPayForm($iTransactionID){
	  if (!$this -> isAdmin() || !$iTransactionID) return '';
	  
	  $sContent = $this -> _oTemplate -> getPayManuallyForm($iTransactionID);	  
	  echo PopupBox('aqb_popup', _t('_aqb_aff_txt_commission_process_manually'), $sContent);
	  exit;
	}
	
	function actionGetMassPayForm(){
	  if (!$this -> isAdmin()) return '';
	  
	  $sContnet = $this -> _oTemplate -> getMassPaymentForm();
	  
	  echo PopupBox('aqb_popup', _t('_aqb_aff_txt_paypal_mass_payment'), $sContnet ? $sContnet : MsgBox(_t('_aqb_commission_mass_payment_empty')));
	  exit;		 
	}
	
	function actionExecuteMassPayment(){
		$sItems = bx_get('items');
		if (!$this -> isAdmin() || !$sItems) return '';
				
		bx_import ('MassPay', $this->_aModule);
		$sClass = $this->_aModule['class_prefix'] . 'MassPay';
		
		$aCredentials = $this -> _oConfig -> getCredentials();
		$aCredentials['code'] = $this -> _oConfig -> _aCurrency['code'];
		$oMassPay = new $sClass($aCredentials);
		
		$aT = preg_split('/,/', $sItems, -1, PREG_SPLIT_NO_EMPTY);
		$sTransaction =  "('" . implode("','", $aT) . "')";
		$aTransaction = $this -> _oDb -> getAllAvailableTransactionForPayment($sTransaction);
		
		if (empty($aTransaction)){
			header('Content-Type:text/javascript');
			$oJson = new Services_JSON();
			echo $oJson -> encode(array('code' => 1, 'message' => _t('_aqb_commission_mass_payment_empty')));
			exit;
		}
		
		$aParams = array();

		$i = 0;
		foreach($aTransaction as $iKey => $aValue){
			$aParams['L_EMAIL'.$i] = $this -> _oDb -> getPaymentEmail($aValue['member_id']);
			$aParams['L_AMT'.$i] = $aValue['price'];
			if ($i++ > 250) break; 
		}

		if ($mixedResult = $oMassPay -> request($aParams)){
			if("SUCCESS" == strtoupper($mixedResult["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($mixedResult["ACK"])){ 
				$aResult = array('code' => 0, 'message' => _t('_aqb_commission_mass_payment_success')); 
				
				foreach($aTransaction as $iKey => $aValue){
					$this -> _oDb -> makeTransactionPending($aValue['id']);
					if ($i++ > 250) break; 
				}
			}	
			else
				$aResult = array('code' => 1, 'message' => _t('_aqb_commission_mass_payment_failed') . print_r($mixedResult, true)); 

		} else $aResult = array('code' => 1, 'message' => _t('_aqb_commission_mass_payment_curl_problem'));
				
		header('Content-Type:text/javascript');
        $oJson = new Services_JSON();
		echo $oJson->encode($aResult);
		exit;
	}
	
	function serviceGetCommissions($sAction = 'join', $iMembership = 2){
		$aResult = array();
		if (!($iMemberId = getLoggedId())) return $aResult;
		
		$bEmbeded = $this -> _oDb -> isPointsSystemInstalled();
		
		if (!$this -> _oDb -> isForcedMatrixEnabled($iMemberId)){ 
			 $aResult = array('price' => $this -> _oDb -> getPriceForAction($sAction, 'price', $iMemberId), 'points' => $this -> _oDb -> getPriceForAction($sAction, 'points', $iMemberId));
			 if ($sAction == 'upgrade') $aResult = array('price' => $this -> _oDb -> getPriceForUpgradedMember($iMembership), 'points' => $this -> _oDb -> getPriceForUpgradedMember($iMembership, 'points'));
		}	 
		else{ 
			$aData = array('membership' => $iMembership);
			$aResult = $this -> _oDb -> getPriceForLevel($aData, 1, $bEmbeded);
		}
		
		if (!empty($aResult)) $aResult['points_enabled'] = $bEmbeded;		
		return $aResult;	
	}
	
	function serviceGetMyRefLink($iProfileID){
		return $this -> _oConfig -> getReferralLink($iProfileID);
	}	
}
?>