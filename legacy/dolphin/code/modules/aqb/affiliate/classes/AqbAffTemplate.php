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

bx_import('BxTemplFormView');
bx_import('BxTemplSearchResult');
bx_import('BxTemplSearchProfile');
bx_import('BxDolTwigTemplate');
bx_import('BxDolParams');

define('BX_DOL_ADM_MP_JS_NAME', 'Item');
define('BX_DOL_ADM_MP_PER_PAGE', 32);
define('BX_DOL_ADM_MP_PER_PAGE_STEP', 16);

class AqbAffTemplate extends BxDolTwigTemplate {
   var $_ActiveMenuLink = '', $oSearchProfileTmpl;
	
	/**
	 * Constructor
	 */
	
	function AqbAffTemplate(&$oConfig, &$oDb) {
	    parent::BxDolTwigTemplate($oConfig, $oDb);
	    $this -> oSearchProfileTmpl = new BxTemplSearchProfile();
	}
	
	function addAdminCss ($sName) 
    {
     	parent::addAdminCss($sName);
    }
	
    function parseHtmlByName ($sName, $aVars) {
     	return parent::parseHtmlByName ($sName, $aVars);
    }
    
    function genWrapperInput($aInput, $sContent) {
       $oForm = new BxTemplFormView(array());
       
       $sAttr = isset($aInput['attrs_wrapper']) && is_array($aInput['attrs_wrapper']) ? $oForm -> convertArray2Attrs($aInput['attrs_wrapper']) : '';
       switch ($aInput['type']) {
            case 'textarea':
                $sCode = <<<BLAH
                        <div class="input_wrapper input_wrapper_{$aInput['type']}" $sAttr>
                            <div class="input_border">
                                $sContent
                            </div>
                            <div class="input_close_{$aInput['type']} left top"></div>
                            <div class="input_close_{$aInput['type']} left bottom"></div>
                            <div class="input_close_{$aInput['type']} right top"></div>
                            <div class="input_close_{$aInput['type']} right bottom"></div>
                        </div>
BLAH;
            break;
            
            default:
                $sCode = <<<BLAH
                        <div class="input_wrapper input_wrapper_{$aInput['type']}" $sAttr>
                            $sContent
                            <div class="input_close input_close_{$aInput['type']}"></div>
                        </div>
BLAH;
        }
        
        return $sCode;
    }
    
	function getReferralBlock($iMemberID){
		$aProfileMembershipInfo = getMemberMembershipInfo($iMemberID);
		
		$oForm = new BxTemplFormView(array());
			
		if ($this -> _oConfig -> isReferralsEnabled()){
			$sLinkMessage = _t('_aqb_aff_link_message_title');
			$aRefLink = array('type' => 'text',  'name' => 'referral_link', 'value' => $this -> _oConfig -> getReferralLink($iMemberID), 
						'attrs' => array('onclick' =>'javascript:$(this).focus().select();', 'class' => 'aqb-invitation-button', 'autofocus' => 'autofocus'), 
						'attrs_wrapper' => array('style' => 'width: auto;float: left;margin-right:0.5rem'));
			$sLinkTextBox = $this -> genWrapperInput($aRefLink, $oForm -> genInput($aRefLink));
		}

		if ($this -> _oConfig -> isAllowToSendInvitations() && $this -> _oConfig -> isReferralsEnabled())
		{
			$aRefButton = array('type' => 'button',  'name' => 'send_invitation', 'value' => _t('_aqb_aff_send_invintation'), 
				'attrs' => array('onclick' =>'javascript:AqbAffItem.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'get_invite_form/' . '\');', 'class' => 'bx-btn-small'));
	       	$sSendButton = $this -> genWrapperInput($aRefButton, $oForm -> genInput($aRefButton));
		}
		
		if ($this -> _oConfig -> isAffiliateEnabled())
			$sAffLink = _t('_aqb_aff_get_banner_link' , '<a href ="' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'affiliates/">' . _t('_aqb_aff_read_more') . '</a>');
		
		$aPrice = $this -> _oDb -> getBalance($iMemberID);

		if ($this -> _oDb -> isForcedMatrixEnabled($iMemberID))
			$aItems[]['items'] = _t('_aqb_aff_my_ref_in_matrix_title') . '&nbsp;:&nbsp;<b>' .  $this -> _oDb -> getReferralsNumber($iMemberID) . '</b>';
		
		$aItems[]['items'] = _t('_aqb_aff_my_ref_title') . '&nbsp;:&nbsp;<b>' .  $this -> _oDb -> getReferralsNumber($iMemberID, 0, false, true) . '</b>';
		
		$aItems[]['items'] = _t('_aqb_aff_referrals_upgraded') . '&nbsp;:&nbsp;<b>' . $this -> _oDb -> getReferralsNumber($iMemberID, 0, true) . '</b>';
		$aItems[]['items'] = _t('_aqb_aff_my_cash_balance') . '&nbsp;:&nbsp;<b>' . $this -> _oConfig -> getCurrencySign () . $aPrice['price'] . '</b>';
		
		if ($this -> _oDb -> isPointsSystemInstalled())
			$aItems[]['items'] = _t('_aqb_aff_my_points_balance') . '&nbsp;:&nbsp;<b>' . $aPrice['points'] . '</b>'; 
	
		return $this -> parseHtmlByName('referral_block.html', 
		array(
				'my_referral' => $this -> getMyReferral($iMemberID),
				'link_message' => $sLinkMessage,
				'link' => $sLinkTextBox,
				'send_invitation' => $sSendButton,
				'affiliate_banners_link' => $sAffLink,
				'bx_repeat:items' => $aItems				 
			 ));
	}	
    
	function isPageLinkAllowed($sPage){
		$sValues = $this -> _oConfig-> getPagesWithRefLink(); 
		if (!$sValues) return false;
		
		$aValues = split(',', $sValues);		
		$aPages = array(
						'ads' => 'ads', 
						'article' => 'articles_single', 
						'blog' => 'bx_blogs', 
						'event' => 'bx_events_view', 
						'group' => 'bx_groups_view', 
						'news' => 'news_single', 
						'photo' => 'bx_photos_view', 
						'video' => 'bx_videos_view',
						'sound' => 'bx_sounds_view',
						'site' => 'bx_sites_view',
						'store' => 'bx_store_view',
						'profile' => 'profile'
					   );
		$sKey = array_search($sPage, $aPages);
		
		if ($sKey && in_array($sKey, $aValues)) return true;	
		return false;
	}
	
	function getReferralLinkBlock($sPage, $iProfile){
		if (!$this -> isPageLinkAllowed($sPage)) return '';
		
		$oForm = new BxTemplFormView(array());
		
		if ($this -> _oConfig -> isReferralsEnabled()){
			$sRefUrl = $_SERVER['REQUEST_URI'];
			
			if (stripos($sRefUrl, '?') !== false) $sRefUrl = $_SERVER['REQUEST_URI'] . '&'; else $sRefUrl = $_SERVER['REQUEST_URI'] . '?';
			
			$sLinkMessage = _t('_aqb_aff_my_ref_link_message_title');
			$aRefLink = array('caption' => $sLinkMessage, 'type' => 'text',  'name' => 'referral_link', 'value' => substr(BX_DOL_URL_ROOT, 0, -1) . "{$sRefUrl}{$this -> _oConfig -> _sRefPrefix}={$iProfile}", 'attrs' => array('onfocus' =>'javascript:$(this).select();', 'onclick' =>'javascript:$(this).select();','readonly' => 'true'), 'attrs_wrapper' => array('style' => 'margin:10px;'));
			$sLinkTextBox = $this -> genWrapperInput($aRefLink, $oForm -> genInput($aRefLink));
		}
	
		return $this -> parseHtmlByName('my_referral_block', 
		array(
				'input' => $sLinkTextBox,
				'caption' => $sLinkMessage
			 ));
	}
	
	function getMyReferral($iMemberId){
		$iId = $this -> _oDb -> getMyReferral($iMemberId);
		if (!(int)$iId) return '';
		
		$oMain = BxDolModule::getInstance('AqbAffModule');
		bx_import ('SearchProfile', $oMain -> _aModule);
        $sClass = $oMain -> _aModule['class_prefix'] . 'SearchProfile';
        $oSearchProfile = new $sClass ($oMain);	
		
		$aProfile = getProfileInfo($iId);
        $sMainContent .= $oSearchProfile -> displaySearchUnit($aProfile, array('date' => _t('_aqb_aff_my_referral')));
       	   
		$sRet  = $GLOBALS['oFunctions']->centerContent($sMainContent, '.searchrow_block_simple');
        $sRet .= '<div class="clear_both"></div>'; 
			
		return $sMainContent;
	}
	
	function getInvitationForm(){
		$aForm = array(
     	'form_attrs' => array(
	            'id' => 'invite-from',
				'name' => 'invite-from',
	            'method' => 'post',
			    'enctype' => '',
				'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'send_invitation/',
	    		'onsubmit' => "javascript: AqbAffItem.onSubmitInvitation(this," . $this -> _oConfig -> getMaximumSymbolsForMessage() . ",'" . addslashes(_t('_aqb_aff_error_symbols_empty_emails')) . "','" . addslashes(_t('_aqb_aff_error_symbols_length', $this -> _oConfig -> getMaximumEmails())) . "'); return false;"
	        ),
	        'params' => array (
                'db' => array(
                    'submit_name' => 'send',
                )
			),
			
			'table_attrs' => array(
	        	'class' => 'aqb-aff-invite-form-table'
			), 
		
			'inputs' => array(
			  'emails' => array(
					'type' => 'textarea',
					'name' => 'aqb_emails',
					'caption' => _t('_aqb_aff_emails'),
					'html' => 0,
					'info' => _t('_aqb_aff_info', $this -> _oConfig -> getMaximumEmails()),
					'attrs_wrapper' => array('style' => 'width:300px;height:50px;'),
					'attrs' => array('style' => 'width:300px;height:50px;', 'id' => 'aqb_emails'),
				)
			)
		);
		
		if ($this -> _oConfig -> isAllowToAddMembersMessage())		
        $aForm['inputs']['message'] = array(
				'type' => 'textarea',
				'name' => 'aqb_message',
				'caption' => _t('_aqb_aff_messages'),
				'html' => 0,
				'info' => _t('_aqb_aff_messages_info', $this -> _oConfig -> getMaximumSymbolsForMessage()),
				'attrs_wrapper' => array('style' => 'width:300px;height:100px;'),
				'attrs' => array('style' => 'width:300px;height:100px;', 'id' => 'aqb_message'),
		);
		
        $aForm['inputs']['send'] = array(
                    'type' => 'submit',
                    'name' => 'send',
                    'value' => _t('_aqb_aff_send_button'),
        			'colspan' => true,
                    'attrs' => array('style' => 'width:150px;', 'id' => 'aqb_send_emails'),
					'attrs_wrapper' => array('style' => 'float:none;'),					
    	);

    	$oForm = new BxTemplFormView($aForm);
   		return $oForm -> getCode(); 	    	
  	}

	function getMemLevelsBlocks($iLevelID)
	{
		$aMembershipInfo = getMembershipInfo($iLevelID);
		
		if (isset($_POST['aff-submit-settings']) && $this -> _oDb -> saveMemLevelsPricing()) $sMessageSettings = MsgBox(_t('_aqb_aff_successfully_saved')); 
		$aMemSettings = $this -> _oDb -> getMemLevelsPricing($iLevelID);
		$aArray = array();
		
		if ($this -> _oDb -> isPointsSystemInstalled()){ 
			$aArray = array(
			'reward_members_points' => array(
				'type' => 'text',
	            'name' => 'referral_points',
	            'caption' => _t('_aqb_aff_admin_points_for_one_referral', $aMembershipInfo['Name']),
	            'value' => (int)$aMemSettings['referral_points'],
				'info' =>  _t('_aqb_aff_admin_reward_points_info')
	        ),
			
			'percent_upgraded_membership' => array (
				'type' => 'text',
	            'name' => 'referral_upgrade_points',
	            'caption' => _t('_aqb_aff_points_upgraded_membership'),
	            'value' => (int)$aMemSettings['referral_upgrade_points'],
			)
		  );
		}	
				
		$aForm = array(
        'form_attrs' => array(
            'id' => 'mlevel-settings-form',
            'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri().'administration/membership/' . $iLevelID,
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ),
		
		'inputs' => array (
    	
		'reward_members_curency' => array (
				'type' => 'text',
	            'name' => 'referral_price',
	            'caption' => _t('_aqb_aff_admin_price_for_one_referral', $aMembershipInfo['Name']),
	            'value' => (float)$aMemSettings['referral_price'],
				'info' =>  _t('_aqb_aff_admin_reward_price_info')
	        )
		));
	            
		$aForm['inputs'] = array_merge($aForm['inputs'], 
		array(
				'price_upgraded_membership' => array (
				'type' => 'text',
	            'name' => 'referral_upgrade_price',
	            'caption' => _t('_aqb_aff_percent_upgraded_membership'),
	            'value' => (int)$aMemSettings['referral_upgrade_price'],
				'info' =>  _t('_aqb_aff_admin_percent_upgraded_info')
				),
				'hidden' => array(
					'type' => 'hidden',
		            'value' => (int)$iLevelID,
		   		    'name' => 'id_level'
				),
			), 
		$aArray , 
		array( 'submit' => array (
				'type' => 'submit',
		        'name' => 'aff-submit-settings',
				'value' => _t('_aqb_aff_save_button')
			  )
		));
        
	
		
	  $oForm = new BxTemplFormView($aForm);
	  return DesignBoxContent(_t('_aqb_aff_admin_melevel_seetings', $aMembershipInfo['Name']), '<div style="margin:20px;color:lightseagreen;">' . _t('_aqb_aff_membership_info') . '</div>' . '<div id="items-control-panel" class="items-control-wrapper">' . $sMessageSettings . $oForm -> getCode() . '</div>', 1); 
	}
		
	function getMemLevelsUpgradeSettings($iLevel){
	   if (isset($_POST['level_upload']) && $this -> _oDb -> updateMemLevelsPricing($iLevel)) $sMessage = MsgBox(_t('_aqb_aff_invited_upgrade_saved'), 2); 
	   $aItem = $this -> _oDb -> getMemLevelsPricing($iLevel);		   
	   
	   $oInputs = new BxTemplFormView(array());
	   
	   $aInvited = array('type' => 'text',  'name' => 'invited_members' , 'value' => $aItem['invited_members'], 'attrs' => array('style' => 'width:85px;'), 'attrs_wrapper' => array('style' => 'width:85px;margin-right:2%;float:left;'));
	   $sInvited = $this -> genWrapperInput($aInvited, $oInputs -> genInput($aInvited));

	   $aValues = getMemberships(); 
	   
	   $aResult = array('-1' => 'none'); 
	   foreach($aValues as $iKey => $sValue){
			if ($iKey < 2) continue;
			$aResult[] = array('key' => $iKey, 'value' => $sValue);
			
			$aM = getMembershipPrices($iKey);
			if (!empty($aM)){ 
				foreach($aM as $sDays => $sPrices)
				$aResult[] = array('key' => $iKey . ':' . $sDays , 'value' => $sValue . " ($sDays-days)");
			}
		}
	   
	   $aMembership = array('type' => 'select',  'name' => 'membership_level' , 'value' => $aItem['membership_level'] ,'values' => $aResult, 'attrs' => array('style' => 'width:150px;'), 'attrs_wrapper' => array('style' => 'width:85px;margin-left:2%;float:left;'));
	   $sMembership = $this -> genWrapperInput($aMembership, $oInputs -> genInput($aMembership));
		
	   $aItems = array();	   
				
	   $aForm = array(
			    'form_attrs' => array(
			            'id' => 'levels',
			            'name' => 'levels',
						'method' => 'post',
			            'enctype' => 'multipart/form-data'
			        ),
				
				'params' => array (
					'db' => array(
							'submit_name' => 'level_upload',
					)						
				),
		
				'inputs' => array(
	            				
				'values' => array(
	                'type' => 'custom',
					'content' => '<div>' . $sInvited . '<div style="float:left;">-</div>' . $sMembership . '</div>',
					'caption' =>  _t('_aqb_aff_invited_upgrade'),
					'attrs_wrapper' => array('style' => 'float:none;')
	     		),
							
			'submit' => array(
						        'type' => 'submit',
						        'name' => 'level_upload',
						        'value' => _t("_aqb_aff_add_membership_invited"),
						      )));
		
		$oForm = new BxTemplFormView($aForm);
			
		return DesignBoxAdmin('', $this -> parseHtmlByName('default_padding', array('content' => $sMessage . '<div style="margin:5px;color:lightseagreen;">' . _t('_aqb_aff_membership_ugrade_info') . '</div>' . $oForm->getCode())));
	}
		
	function getBlockSearch($isCommission = false, $sNickName = '') {
    
	$aForm = array(
        'form_attrs' => array(
            'id' => 'aff-search-form',
            'action' => $_SERVER['PHP_SELF'],
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ),
        'inputs' => array (
            'aff-filter-input' => array(
                'type' => 'text',
                'name' => 'aff-filter-input',
                'caption' => $isCommission ? _t('_aqb_aff_commission_filter') : _t('_aqb_aff_filter'),
                'value' => $sNickName,
				'info' =>  $isCommission ? '' : _t('_aqb_aff_filter_info')
 			),
            'search' => array(
                'type' => 'button',
                'name' => 'search',
                'value' => _t('_aqb_aff_search'),
				'attrs' => array(
                    'onclick' => 'javascript:' . BX_DOL_ADM_MP_JS_NAME . '.changeFilterSearch()'
                )
            ), 
       )
    );

    $oForm = new BxTemplFormView($aForm);
    return $oForm->getCode();
}

	function getMassPaymentForm(){
		$aTransaction = $this -> _oDb -> getAllAvailableTransactionForPayment();
		
		if (empty($aTransaction)) return '';
		
		$bIsEnabled = $this -> _oDb -> isPointsSystemInstalled();
		
		$aItems = array();
		
		$iPoints = 0;
		$fPrice = 0;
		foreach($aTransaction as $iKey => $aValue){
			$aItems[] = array(
								'id' => $aValue['id'],
								'username' => getNickName($aValue['member_id']),
								'points' => $bIsEnabled ? $aValue['points'] : '',
								'price' => $aValue['price'],
								'date_start' => $aValue['date_start']
							  );			
			$fPrice += (float)$aValue['price'];
			$iPoints += (int)$aValue['points'];
		}
		
	
	$aButtons = array(
    	'0' => array( 
					  'type' => 'submit',
					  'name' => 'aqb-commission-pay',
					  'value' => _t('_aqb_commission_mass_payment'),
					  'onclick' => 'onclick="javascript:Item.sendMassPayment(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() .'execute_mass_payment\'); return false;"'
		
		)		
    );    
    
	$sControls = BxTemplSearchResult::showAdminActionsPanel('mass_pay_form', $aButtons, 'members');
	
		return $this -> parseHtmlByName('mass_pay_form', array(
			'points_title' => $bIsEnabled ? _t('_aqb_aff_admin_commission_sum_points') : '',
			'currency' => $this -> _oConfig -> getCurrencySign(),
			'bx_repeat:items' => $aItems,
			'controls' => $sControls,
			'price_total' => $fPrice,
			'points_total' => 	$bIsEnabled ? $iPoints : '',
			'loading' => LoadingBox('div-loading-mass')	
			)
		); 			
	}
	
	function getCommissionsInfo($sNickName){
	$aButtons = array();
	
	$bCredentialsWasInstalled = $this -> _oConfig -> IsCredentialsInstalled();
	
	if ($bCredentialsWasInstalled){	
	$aButtons = array(
    	'0' => array( 
					  'type' => 'submit',
					  'name' => 'aqb-aff-mass-pay',
					  'value' => _t('_aqb_aff_mass_payment'),
					  'onclick' => 'onclick="javascript:AqbAffItem.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() .'get_mass_pay_form\'); return false;"'
		
			)			
		);    
    }

	$sControls = BxTemplSearchResult::showAdminActionsPanel('items-commissions-form', $aButtons, 'commissions', false);	
    
	$oPaginate = new BxDolPaginate(array(
        'per_page' => BX_DOL_ADM_MP_PER_PAGE,
        'per_page_step' => BX_DOL_ADM_MP_PER_PAGE_STEP,
        'on_change_per_page' => BX_DOL_ADM_MP_JS_NAME . '.changePerPage(this);'
    ));    
    
	$sInfo = !$bCredentialsWasInstalled ? _t('_aqb_commission_mass_payment_API_info', BX_DOL_URL_ROOT) : '';
	
	if (!$this -> _oDb -> isIPNInstalled()) $sInfo .= _t('_aqb_commission_mass_payment_IPN_info', BX_DOL_URL_ROOT);
	
	$aResult = array(
        'per_page' => $oPaginate->getPages(),
        'control' => $sControls . '<div style="margin:20px;">' . $sInfo . '</div>'  
    );

		
	$aParams = array();
	if ($sNickName) $aParams = array('filter' => $sNickName);
    $aResult = array_merge($aResult, array('style_common' => '', 'content_common' => $this -> getCommissionsPanel($aParams)));
	
	
	return $this -> parseHtmlByName('admin_members', 
	array(
				'search' => $this -> getBlockSearch(true, $sNickName), 
				'members' =>$this  -> parseHtmlByName('commissions', $aResult),
			    'obj_name' => BX_DOL_ADM_MP_JS_NAME,
				'actions_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(),
			    'sel_view' => '',
			    'per_page' => BX_DOL_ADM_MP_PER_PAGE,
			    'order_by' => '',
				'section' => 'commission',
				'member_id' => "'{$sNickName}'",
				'loading' => LoadingBox('div-loading')			
		));
			
}
		
	function getCommissionsPanel($aParpoints = array()) {
		if (empty($aParpoints['view_type'])) $aParpoints['view_type'] = 'ASC';
		if(!isset($aParpoints['view_start']) || empty($aParpoints['view_start'])) $aParpoints['view_start'] = 0;
	    if(!isset($aParpoints['view_per_page']) || empty($aParpoints['view_per_page'])) $aParpoints['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;
	
		    $aParpoints['view_order_way'] = $aParpoints['view_type'];
		    
		    if(!isset($aParpoints['view_order']) || empty($aParpoints['view_order'])) $aParpoints['view_order'] = 'ID';
			
		    $aProfiles = $this -> _oDb -> getCommissions($aParpoints);
		   	
			$sBaseUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
		    
			$bIsEnabled = $this -> _oDb -> isPointsSystemInstalled();
			
			if (count($aProfiles) == 0) return MsgBox(_t('_aqb_aff_empty_result'));
			
		    $aItems = array();
		    foreach($aProfiles as $aProfile){
		        $aItems[] = array(
		            'username' => $aProfile['username'],
					'payment_status' => $aProfile['payment_status'],
					'status' => $aProfile['status'],
		            'date_start' => $aProfile['date_start'],
					'price' => $aProfile['price'],
					'points' => $bIsEnabled ? $aProfile['points'] : '',	
		    		'joins_num' => $aProfile['join_num'],
					'impressions_num' => $aProfile['impression_num'],
					'clicks_num' => $aProfile['click_num'],
					'upgrades_num' => $aProfile['upgrade_num'],
					'color' => $aProfile['status'] == 'unpaid' ? "background-color:red;" : '',
					'remove' => '<a href="javascript:void(0);" onclick="javascript: if (confirm(\''.addslashes(_t('_aqb_aff_admin_confirm_del_transactions')).'\')) window.location = \'' . $sBaseUrl . 'administration/commissions/?delete=' . $aProfile['id']  . '\'; " title="'._t('_aqb_aff_txt_commission_remove').'"><img src="' . $this -> getIconUrl('clean.png') . '" /></a>',
					'pay' => $this -> _oDb -> isPaymentInstalled($aProfile['member_id']) && (!$bIsEnabled || ($bIsEnabled && (int)$aProfile['points']))? '<a onclick="javascript:$.post(\''. BX_DOL_URL_MODULES . '?r=payment/act_add_to_cart/' . $aProfile['member_id'] . "/" . $this -> _oConfig -> getId() . "/{$aProfile['id']}/1" . '\', \'\',function(oData){try{alert(oData.message);window.location = \''. BX_DOL_URL_MODULES . '?r=payment/cart/\';}catch(e){}},\'json\');" href="javascript:void(0);" title="'._t('_aqb_aff_txt_commission_pay_cart').'"><img src="' . $this -> getIconUrl('shoppingcart.gif') . '" /></a>' : '<img title="'._t('_aqb_aff_txt_commission_pay_cart_not_installed').'" src="' . $this -> getIconUrl('shoppingcart_dis.gif') . '" />',
					'manually' =>  '<a href="javascript:void(0);" onclick="javascript:AqbAffItem.showPopup(\'' . $sBaseUrl . 'pay_form/' . $aProfile['id'] .'\');" title="'._t('_aqb_aff_txt_commission_manually').'"><img src="' . $this -> getIconUrl('pay.png') . '" /></a>',

		        );
		    }
		  
			//--- Get Paginate ---//
		    $oPaginate = new BxDolPaginate(array(
		        'start' => $aParpoints['view_start'],
		        'count' => $this -> _oDb -> _iMembersCount,
		        'per_page' => $aParpoints['view_per_page'],
		        'page_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '?start={start}',
		        'on_change_page' => BX_DOL_ADM_MP_JS_NAME . '.changePage({start})'
		    ));
		    
			$sPaginate = $oPaginate -> getPaginate(); 
			
		    return $this -> parseHtmlByName('commissions_table', array(
		        'points_title' => $bIsEnabled ? _t('_aqb_aff_admin_commission_sum_points') : '',
				'currency' => $this -> _oConfig -> getCurrencySign(),
				'bx_repeat:items' => $aItems,

				'username_function' => $this -> getOrderParpoints('NickName', $aParpoints),
				'username_arrow' => $this -> getArrowImage('NickName', $aParpoints),

				'status_function' => $this -> getOrderParpoints('status', $aParpoints),
				'status_arrow' => $this -> getArrowImage('status', $aParpoints),
				
				'date_start_function' => $this -> getOrderParpoints('date_start', $aParpoints),
				'date_start_arrow' => $this -> getArrowImage('date_start', $aParpoints),

				'date_end_function' => $this -> getOrderParpoints('date_end', $aParpoints),
				'date_end_arrow' => $this -> getArrowImage('date_end', $aParpoints),

				'price_function' => $this -> getOrderParpoints('price', $aParpoints),
				'price_arrow' => $this -> getArrowImage('price', $aParpoints),

				'points_function' => $bIsEnabled ? $this -> getOrderParpoints('points', $aParpoints) : '',
				'points_arrow' => $bIsEnabled ? $this -> getArrowImage('points', $aParpoints) : '',

				'last_login_function' => $this -> getOrderParpoints('DateLastNav', $aParpoints),  
				'last_login_arrow' => $this -> getArrowImage('DateLastNav', $aParpoints),
				
				'joins_num_function' => $this -> getOrderParpoints('join_num', $aParpoints), 
				'joins_num_arrow' => $this -> getArrowImage('join_num', $aParpoints),

				'impressions_num_function' => $this -> getOrderParpoints('impression_num', $aParpoints), 
				'impressions_num_arrow' => $this -> getArrowImage('impression_num', $aParpoints),

				'clicks_num_function' => $this -> getOrderParpoints('click_num', $aParpoints), 
				'clicks_num_arrow' => $this -> getArrowImage('click_num', $aParpoints),

				'upgrades_num_function' => $this -> getOrderParpoints('upgrade_num', $aParpoints), 
				'upgrades_num_arrow' => $this -> getArrowImage('upgrade_num', $aParpoints),
	
				'payment_status_function' => $this -> getOrderParpoints('payment_status', $aParpoints), 
				'payment_status_arrow' => $this -> getArrowImage('payment_status', $aParpoints),
				
				'paginate' => $sPaginate
		    ));                                                                                                             
	
   }
   

	function getMembersInfo(){
	    $aButtons = array(
	    	'aqb-aff-delete-members' => _t('_aqb_aff_block_delete')
	    );    
	    
		$sControls = BxTemplSearchResult::showAdminActionsPanel('items-members-form', $aButtons, 'members');
	       
	    $oPaginate = new BxDolPaginate(array(
	        'per_page' => BX_DOL_ADM_MP_PER_PAGE,
	        'per_page_step' => BX_DOL_ADM_MP_PER_PAGE_STEP,
	        'on_change_per_page' => BX_DOL_ADM_MP_JS_NAME . '.changePerPage(this);'
	    ));    

	    $aResult = array(
	        'per_page' => $oPaginate->getPages(),
	        'control' => $sControls,
			'links' => '<b><a href="javascript:void(0);" onclick="javascript:Item.showAll();">'._t('_aqb_aff_show_all_members').'</a></b>&nbsp;&nbsp;|&nbsp;&nbsp;<b><a href="javascript:void(0);" onclick="javascript:Item.cleanMemberHistory(\''.BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'clean_all_history/'.'\',\''.addslashes(_t('_aqb_aff_confirm_clean')).'\');">'._t('_aqb_aff_clean_all_members').'</a></b>'
	    );

	    $aResult = array_merge($aResult, array('style_common' => '', 'content_common' => $this -> getMembersPanel()));
	   	 
		return $this -> parseHtmlByName('admin_members', 
		array(
					'search' => $this -> getBlockSearch(), 
					'members' =>$this  -> parseHtmlByName('members', $aResult),
				    'obj_name' => BX_DOL_ADM_MP_JS_NAME,
					'actions_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(),
				    'sel_view' => '',
				    'per_page' => BX_DOL_ADM_MP_PER_PAGE,
				    'order_by' => '',
					'section' => 'members',
					'member_id' => 0,
					'loading' => LoadingBox('div-loading')			
			));
	}
		
	function getMembersPanel($aParpoints = array()) {
	if (empty($aParpoints['view_type'])) $aParpoints['view_type'] = 'ASC';
	if(!isset($aParpoints['view_start']) || empty($aParpoints['view_start'])) $aParpoints['view_start'] = 0;
    if(!isset($aParpoints['view_per_page']) || empty($aParpoints['view_per_page'])) $aParpoints['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;
	
    $aParpoints['view_order_way'] = $aParpoints['view_type'];
    
    if(!isset($aParpoints['view_order']) || empty($aParpoints['view_order'])) $aParpoints['view_order'] = 'ID';
	
    $aProfiles = $this -> _oDb -> getMembers($aParpoints);
   	
    $sBaseUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();

	$bIsEnabled = $this -> _oDb -> isPointsSystemInstalled();
    
    $aItems = array();
    foreach($aProfiles as $aProfile){
        $aItems[$aProfile['id']] = array(
            'id' => $aProfile['id'],
            'username' => $aProfile['username'],
            'edit_link' => BX_DOL_URL_ROOT . 'pedit.php?ID=' . $aProfile['id'],
            'last_login' => $aProfile['last_login'],            
    		'joins_num' => $aProfile['joins'],
			'impressions_num' => $aProfile['impressions'],
			'clicks_num' => $aProfile['clicks'],
			'upgrades_num' => $aProfile['upgraded'],
			'paid' => $aProfile['paid_price'],
			'unpaid' => $aProfile['unpaid_price'],
			'paid_points' => $bIsEnabled ? $aProfile['paid_points'] : '',
			'unpaid_points' => $bIsEnabled ? $aProfile['unpaid_points'] : '',
	    	'view_details' => '<a href="'.$sBaseUrl . 'administration/commissions/' . $aProfile['username'] .'" title="'._t('_aqb_aff_txt_view_commission').'"><img src="' . $this -> getIconUrl('commission.png') . '" /></a>',
			'view_ref' => '<a href="'.$sBaseUrl . 'referrals/standard/' . $aProfile['id'] .'" title="'._t('_aqb_aff_txt_view_invited').'"><img src="' . $this -> getIconUrl('users.png') . '" /></a>',
			'view_history' => '<a href="'.$sBaseUrl . 'history/' . $aProfile['id'] .'" title="'._t('_aqb_aff_txt_view_history').'"><img src="' . $this -> getIconUrl('history_admin.png') . '" /></a>',
			'clean_history' => '<a href="javascript:void(0);" onclick="javascript:Item.cleanMemberHistory(\''.$sBaseUrl . 'clean_history/' . $aProfile['id'] .'\',\''.addslashes(_t('_aqb_aff_admin_confirm_clean')).'\');" title="'._t('_aqb_aff_txt_history_clean').'"><img src="' . $this -> getIconUrl('clean.png') . '" /></a>',
        );
    }
  
	//--- Get Paginate ---//
    $oPaginate = new BxDolPaginate(array(
        'start' => $aParpoints['view_start'],
        'count' => $this -> _oDb -> _iMembersCount,
        'per_page' => $aParpoints['view_per_page'],
        'page_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '?start={start}',
        'on_change_page' => BX_DOL_ADM_MP_JS_NAME . '.changePage({start})'
    ));
    
	$sPaginate = $oPaginate -> getPaginate(); 
	    
	$this->_CurrencySign = $this -> _oConfig -> getCurrencySign();

    return $this -> parseHtmlByName('members_table', array(
        'bx_repeat:items' => array_values($aItems),
		'aqb_aff_admin_txt_paid' => _t('_aqb_aff_admin_txt_paid') . $this->_CurrencySign,
		'aqb_aff_admin_txt_unpaid' => _t('_aqb_aff_admin_txt_unpaid') . $this->_CurrencySign,
		'aqb_aff_admin_txt_paid_points' => $bIsEnabled ? _t('_aqb_aff_admin_txt_paid_points') : '' ,
		'aqb_aff_admin_txt_unpaid_points' => $bIsEnabled ? _t('_aqb_aff_admin_txt_unpaid_points') : '' ,
		'username_function' => $this -> getOrderParpoints('NickName', $aParpoints),
		'username_arrow' => $this -> getArrowImage('NickName', $aParpoints),
		'last_login_function' => $this -> getOrderParpoints('DateLastNav', $aParpoints),  
		'last_login_arrow' => $this -> getArrowImage('DateLastNav', $aParpoints),
		'joins_num_function' => $this -> getOrderParpoints('joins', $aParpoints), 
		'joins_num_arrow' => $this -> getArrowImage('joins', $aParpoints),
		
		'impressions_num_function' => $this -> getOrderParpoints('impressions', $aParpoints), 
		'impressions_num_arrow' => $this -> getArrowImage('impressions', $aParpoints),

		'clicks_num_function' => $this -> getOrderParpoints('clicks', $aParpoints), 
		'clicks_num_arrow' => $this -> getArrowImage('clicks', $aParpoints),

		'upgrades_num_function' => $this -> getOrderParpoints('upgrades', $aParpoints), 
		'upgrades_num_arrow' => $this -> getArrowImage('upgrades', $aParpoints),
		
		'paid_function' => $this -> getOrderParpoints('paid_price', $aParpoints), 
		'paid_arrow' => $this -> getArrowImage('paid_price', $aParpoints),
				
		'unpaid_function' => $this -> getOrderParpoints('unpaid_price', $aParpoints), 
		'unpaid_arrow' => $this -> getArrowImage('unpaid_price', $aParpoints),

		'paid_points_function' => $bIsEnabled ? $this -> getOrderParpoints('paid_points', $aParpoints) : '', 
		'paid_points_arrow' => $bIsEnabled? $this -> getArrowImage('paid_points', $aParpoints) : '',
				
		'unpaid_points_function' => $bIsEnabled ? $this -> getOrderParpoints('unpaid_points', $aParpoints) : '', 
		'unpaid_points_arrow' => $bIsEnabled ? $this -> getArrowImage('unpaid_points', $aParpoints) : '',

		'paginate' => $sPaginate
    ));                                                                                                             
	
   }
	
	function getOrderParpoints($sFName, &$aParpoints){
		return 'onclick="'.BX_DOL_ADM_MP_JS_NAME.'.orderByField(\'' . (($aParpoints['view_order'] == $sFName && 'asc' == strtolower($aParpoints['view_type'])) ? 'desc' : 
			   ($aParpoints['view_order'] == $sFName && 'desc' == strtolower($aParpoints['view_type']) ? 'asc' : '')) . '\',\''.$sFName.'\')"';
    }
	
	function getArrowImage($sFName, &$aParpoints){
	    if ($aParpoints['view_order'] == $sFName && 'asc' == strtolower($aParpoints['view_type'])) return '<img class="items-sort-arrow" src="' . $this->getIconUrl('arrow_up.png') . '" />'; 
		if ($aParpoints['view_order'] == $sFName) return '<img class="items-sort-arrow" src="' .  $this->getIconUrl('arrow_down.png'). '" />';
		
		return '';			
	}	
	
	function getRefreshMatrixPanel(){
		$iNum =  $this -> _oDb -> getReferralsNum();
		$sInfo = _t('_aqb_aff_matrix_refresh_info', $iNum );

		$oForm = new BxTemplFormView(array());
		$aStartButton = array('type' => 'button',  'name' => 'start', 'value' => _t('_aqb_aff_matrix_refresh_start'), 'attrs' => array('onclick' => 'javascript:Item.startRefreshing(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '\',\'' . addslashes(_t('_aqb_aff_matrix_refresh_stop_message')) . '\');'), 'attrs_wrapper' => array('style' => 'float:right;'));
	    $sStartButton = $this -> genWrapperInput($aStartButton, $oForm -> genInput($aStartButton));

		$sLoadingBox = LoadingBox('div-loading');
		
		$sHtml =<<<EOF
			<div class="contentblock">
				<h3 style="margin-top:10px;margin-left:10px;margin-right:10px;margin-bottom:20px;">{$sInfo}</h3>
				<table width="100%" cellpadding="0" cellspacing="0" class="form_advanced_table aqb-aff-invite-form-table">
					<tr>
						<td width="10%"></td>
						<td width="10%" style="vertical-align: bottom;">{$sStartButton}</td>
						<td width="*" height="30" style="vertical-align: top;">
								<div style="position:relative;">$sLoadingBox</div>
						</td>
					</tr>
				</table>
			</div>
EOF;
	
		$aContent = array('type' => 'custom',  'name' => 'progressbar', 'content' => $sHtml);
	    $sContent = $this -> genWrapperInput($aContent, $oForm -> genInput($aContent));
	
	
		return $sContent;
	}
	
	function getForcedMatrix(){
		$aSettings = isset($_POST['matrix_create']) ? $_POST : $this -> _oDb -> getMatrixSettings();
	
		$aInfoValues = array(1 => _t('_aqb_aff_income_percentage'), 2 => _t('_aqb_aff_income_currency', $this -> _oConfig -> getCurrencySign()));
		
		if ($this -> _oDb -> isPointsSystemInstalled()){ 
			$aInfoValues[3] = _t('_aqb_aff_income_upgrade_points');
			$aInfoValues[4] = _t('_aqb_aff_income_join_points');
		}
		
		$oForm = new BxTemplFormView(array());
	
		$aRefreshButton = array('type' => 'button',  'name' => 'refresh_matrix', 'value' => _t('_aqb_aff_matrix_refresh'), 'attrs' => array('onclick' => 'javascript:AqbAffItem.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "refresh_matrix');"), 'attrs_wrapper' => array('style' => 'float:right;'));
	    $sRefreshButton = $this -> genWrapperInput($aRefreshButton, $oForm -> genInput($aRefreshButton));

		$aSubmitButton = array('type' => 'submit',  'name' => 'matrix_create', 'value' => _t('_aqb_aff_matrix_create'));
	    $sSubmitButton = $this -> genWrapperInput($aSubmitButton, $oForm -> genInput($aSubmitButton));
		
		$aForm = array(
			    'form_attrs' => array(
			            'id' => 'matrix',
			            'name' => 'matrix',
						'method' => 'post',
			            'enctype' => 'multipart/form-data'						
			        ),
				
				'params' => array (
                'db' => array(
                    'submit_name' => 'matrix_create',
					)
				),
		
				'inputs' => array(
	            
				'matrix_enable' => array(
	                'type' => 'checkbox',
	                'name' => 'enable',
	                'checked' => $aSettings['enable'],
					'caption' =>  _t('_aqb_aff_matrix_enable'),
		 		),
			
				'matrix_levels_width' => array(
	                'type' => 'text',
	                'name' => 'width',
	                'value' => $aSettings['width'],
					'caption' =>  _t('_aqb_aff_matrix_levels_width'),
					'info' =>  _t('_aqb_aff_matrix_levels_width_info'),
					'db' => array (
                        'pass' => 'Int', 
                    ),
					'attrs' => array('style' => 'width:50px;'),
					'attrs_wrapper' => array('style' => 'width:50px;')						
	     		),
				
				'matrix_spillover' => array(
	                'type' => 'checkbox',
	                'name' => 'spillover',
	                'checked' => $aSettings['spillover'],
					'caption' =>  _t('_aqb_aff_matrix_spillover'),
					'info' => _t('_aqb_aff_matrix_spillover_info')
		 		),
				
				'income_options' => array(
	                'type' => 'checkbox_set',
	                'name' => 'income',
	                'caption' => _t('_aqb_aff_income'),
	                'values' => $aInfoValues,
					'value' => $aSettings['income'],
					'dv' => '<br/>'
	            ),
	            
				'buttons' => array(
					'type' => 'custom',
					'colspan' => false,
					'content' => $sSubmitButton . '<div style="position:relative;float:right">' . $sRefreshButton . '</div>',
					'attrs_wrapper' => array('style' => 'width:100%;')
				)
			)	
        );
		
		$oForm = new BxTemplFormView($aForm);
		
		if ($oForm -> isSubmittedAndValid()) $this -> _oDb -> setMatrixSettings($_POST);
		return  '<div id="items-control-panel" class="items-control-wrapper">' . $oForm -> getCode() . '</div>';
	}
	
	function getPayManuallyForm($iTransactionID){
		$aInfo = $this -> _oDb -> getTransactionInfo($iTransactionID);
				
		$aForm = array(
			    'form_attrs' => array(
			            'id' => 'add_transaction',
			            'name' => 'add_transaction',
						'method' => 'post',
						'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/commissions',
			            'enctype' => 'multipart/form-data'						
			        ),
			
				'params' => array (
                'db' => array(
                    'submit_name' => 'submit_transaction',
					)
				),
				
				'inputs' => array(
	            
				'hidden' => array(
	                'type' => 'hidden',
	                'name' => 'transaction_id',
	                'value' => $iTransactionID,
	     		),
				
				'transaction_id' => array(
	                'type' => 'text',
	                'name' => 'transaction',
	                'value' => $aInfo['tnx'],
					'caption' =>  _t('_aqb_aff_txt_commission_form_transaction_id'),
					'info' =>  _t('_aqb_aff_txt_commission_form_transaction_id_info'),
	     		)));
			
	$aPayments = $this -> _oDb -> getAvailablePayament((int)$aInfo['member_id']);
	
	if (!empty($aPayments)){
	$aForm['inputs'] = array_merge($aForm['inputs'], array(		
     			'payments' => array(
	                'type' => 'radio_set',
				    'name'   => 'payments',
					'value'  => $this -> _oDb -> getTransactionsPaymentProvider($aInfo['tnx']),
                    'values' => $aPayments,
                    'caption'   => _t('_aqb_aff_txt_payment_providers'),

	     		)));
	}			
	
	$aForm['inputs'] = array_merge($aForm['inputs'], array(
				'status' => array(
	                'type' => 'radio_set',
				    'name'  => 'status',
					'value' => $aInfo['status'],  
                    'values' => array('paid' => 'paid','unpaid' => 'unpaid'),
                    'caption'   => _t('_aqb_aff_txt_transaction_status'),

	     		),
				'buttons' => array(
					'type' => 'submit',
					'name' => 'submit_transaction',
					'value' =>  _t('_aqb_aff_txt_commission_form_transaction_id_button')					
				)
			)	
        );
		
		$oForm = new BxTemplFormView($aForm);
		return '<div class="items-control-wrapper">' . $oForm->getCode() . '</div>';
	}
	
	function showMatrixPanel(){
		$aMembershipLevels = $this -> _oDb -> getMembershipLevels();
	    $bPointsEnabled = $this -> _oDb -> isPointsSystemInstalled();
	   
		if (!$this -> _oConfig -> isForcedMatrixEnabled()) return MsgBox(_t('_aqb_aff_matrix_is_not_enabled'));
		
		if (empty($aMembershipLevels)) return MsgBox(_t('_aqb_aff_nothing_found'));
	
		if (isset($_POST['save_settings']))
		foreach($aMembershipLevels as $k => $v){	
				$sStringLevels = $sStringLevelsUpgrade = $sStringPoints = $sStringPointsUpgrade = '';			
		
				$bEnabled = isset($_POST['use_matrix_'. $v['ID']]) ? 1 : 0 ;
				$iDeep = $_POST['levels_deep_' . $v['ID']];
								
				for($i = 0; $i < $iDeep; $i++){
					$sStringLevels .= $i . ":" . (float)$_POST['level_' . $i . '_' . $v['ID']] . ";";
					$sStringLevelsUpgrade .= $i . ":" . (int)$_POST['level_upgrade_' . $i . '_' . $v['ID']] . ";";
					
						if ($bPointsEnabled){
							$sStringPoints .= $i . ":" . (int)$_POST['points_' . $i . '_' . $v['ID']] . ";";
							$sStringPointsUpgrade .= $i . ":" . (int)$_POST['points_upgrade_' . $i . '_' . $v['ID']] . ";";				
						}	
				}
				
				if ($this -> _oDb -> setMemLevelSettings($v['ID'], $iDeep, $bEnabled, '{' . $sStringLevels . '}', '{' . $sStringPoints . '}','{' . $sStringLevelsUpgrade . '}', '{' . $sStringPointsUpgrade . '}')) $sMessage = MsgBox(_t('_aqb_aff_successfully_saved'), 2);
		}
	
		$aForm = array(
			'form_attrs' => array(
			            'id' => 'memberships',
			            'name' => 'memberships',
						'method' => 'post',
			            'enctype' => 'multipart/form-data',
			        ),
				
				'params' => array (
                'db' => array(
                    'submit_name' => 'save_settings',
					)
				),
		);
	
		$aForm['inputs'] = array();
		foreach($aMembershipLevels as $k => $v){
			$aForm['inputs'] = array_merge($aForm['inputs'], array( 
    	    	"{$v['ID']}_section_begin" => array(
                    'type' => 'block_header',
                    'caption' => '<b>'.$v['Name']. '</b>',
                    'collapsable' => true,
                    'collapsed' => true
        	 	),
				"{$v['ID']}_area" => array(
                    'type' => 'custom',
                    'content' => $this -> getGrid($v['ID'], $bPointsEnabled),
        	 	    'colspan' => true,
        	 	    'attrs_wrapper' => array('style' => 'float:none;')                                  
				),
				"{$v['ID']}_section_end" => array(
                    'type' => 'block_end'
        	 	)
    		));
		}
	
		$aForm['inputs'] = array_merge($aForm['inputs'], array(
				'upload' => array(
	                'type' => 'submit',
	                'name' => 'save_settings',
	                'value' => _t('_aqb_aff_matrix_create'),					
	            )
			)	
        );
		
		$oForm = new BxTemplFormView($aForm);
		return DesignBoxAdmin('', $oForm -> getCode()); 
   	}
	
	function getGrid($iLevelID, $bPointsEnabled){
	   $sStringLevels = '';
	   $sStringPoints = '';
	   $sStringLevelsUpgrade = '';
	   $sStringPointsUpgrade = '';
	   
	   $oForm = new BxTemplFormView(array());
	   $aItems = array();	   
	    
	   $aValues = $this -> _oDb -> getMemLevelMatrixSettings($iLevelID, $bPointsEnabled);	
	   $iNum = ((int)$aValues['deep'] < 15  && (int)$aValues['deep'] > 0) ? (int)$aValues['deep'] : 15;
		
	   for($i = 0; $i < $iNum; $i++){
		  $aLevels = array('type' => 'text',  'name' => 'level_' . $i . '_' . $iLevelID , 'value' => $aValues['levels']['levels_' . $i . '_' . $iLevelID], 'attrs' => array('style' => 'width:60px;'), 'attrs_wrapper' => array('style' => 'width:60px;margin-left: 10%;'));
		  $sLevel = $this -> genWrapperInput($aLevels, $oForm -> genInput($aLevels));
		  
		  $aLevelsUpgrade = array('type' => 'text',  'name' => 'level_upgrade_' . $i . '_' . $iLevelID , 'value' => $aValues['levels_upgrade']['levels_upgrade_' . $i . '_' . $iLevelID], 'attrs' => array('style' => 'width:60px;'), 'attrs_wrapper' => array('style' => 'width:60px;margin-left: 10%;'));
		  $sLevelUpgrade = $this -> genWrapperInput($aLevelsUpgrade, $oForm -> genInput($aLevelsUpgrade));
		
		 if ($bPointsEnabled){
			$aPointsUpgrade = array('type' => 'text',  'name' => 'points_upgrade_' . $i . '_' . $iLevelID , 'value' => $aValues['points_upgrade']['points_upgrade_' . $i . '_' . $iLevelID], 'attrs' => array('style' => 'width:60px;'), 'attrs_wrapper' => array('style' => 'width:60px;margin-left: 10%;'));
			$sPointsUpgrade = $this -> genWrapperInput($aPointsUpgrade, $oForm -> genInput($aPointsUpgrade));

			$aPoints = array('type' => 'text',  'name' => 'points_' . $i . '_' . $iLevelID , 'value' => $aValues['points']['points_' . $i . '_' . $iLevelID], 'attrs' => array('style' => 'width:60px;'), 'attrs_wrapper' => array('style' => 'width:60px;margin-left: 10%;'));
			$sPoints = $this -> genWrapperInput($aPoints, $oForm -> genInput($aPoints));
			
		}
		
		$aItems[] = array(
							 'level' => $i + 1,
							 'id' => $iLevelID,
							 'points' => $bPointsEnabled ? '<td>' . $sPoints . '</td>' : '',
							 'income' => $sLevel, 	
							 'income_upgrade' => $sLevelUpgrade,
							 'points_upgrade' => $bPointsEnabled ? '<td>' . $sPointsUpgrade . '</td>' : ''							 
						);
	   }	   
		
		$sLinkInfo = '<a href="javascript:void(0);" onclick="javascript:AqbAffItem.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "view_info/{$iLevelID}');\">" . _t('_aqb_aff_matrix_view_info_mem_level') . '</a>';
		
	   return  $sMessage . $this -> parseHtmlByName('matrix_table', 
																		array(
																				'bx_repeat:items' => $aItems,
																				'cel_count' => $bPointsEnabled ? 3 : 1,
																				'id' => $iLevelID,
																				'aqb_aff_admin_txt_income' => _t('_aqb_aff_admin_txt_income', $this -> _oConfig -> getCurrencySign()),
																				'points_title' => ($bPointsEnabled ? '<th>' . _t('_aqb_aff_admin_txt_points') . '</th>' : ''),
																				'points_title_upgrade' => ($bPointsEnabled ? '<th>' . _t('_aqb_aff_admin_txt_points_upgrade') . '</th>' : ''),																				
																				'points_cel' => '<td width="*" ></td>', 
																				'points_upgrade_title' => ($bPointsEnabled ? '<th>' . _t('_aqb_aff_admin_txt_points') . '</th>' : ''), 
																				'points_upgrade_cel' => '<td width="*" ></td>',
																				'link_info' => $sLinkInfo,
																				'checked' => (int)$aValues['enabled'] ? 'checked="checked"' : '',
																				'deep' => $iNum
																		     )
																		);
	}
	
	function getPreviewMatrixInfo($iMemLevel = 0){
		$bPointsEnable = $this -> _oDb -> isPointsSystemInstalled();
		$aValues =	$this -> _oDb -> getMemLevelMatrixSettings($iMemLevel, $bPointsEnable);		 
	
		if (!$this -> _oConfig -> isForcedMatrixEnabled() || !(int)$iMemLevel || !(int)$aValues['enabled']) return MsgBox(_t('_aqb_aff_matrix_is_not_enabled_for_mem'));
	    
		$iWidth = $this -> _oDb -> getForcedMatrixWidth();
	    $iWidth = !$iWidth ? 3 : $iWidth;
			
		
		$iNum = (int)$aValues['deep'];
		
		$sCurrency  = $this -> _oConfig -> getCurrencySign();
		
		$isIncomePercentEnabled = $this -> _oDb -> isPercentageEnabled();
		$isIncomePriceEnabled = $this -> _oDb -> isPriceEnabled();

		$isPointsForUpgrade = $this -> _oDb -> isPointsForUpgradeEnabled();
		$isPointsForJoin = $this -> _oDb -> isPointsForJoinEnabled();

		
		$iPointsSum = 0;
		$iIncomeSum = 0;
	    
		for($i = 0; $i < $iNum; $i++){
		 $iPeople = pow($iWidth,($i + 1));
		 $aItems[$i] = array(
							 'level' => $i + 1,
							 'people' => $iPeople,
						     'amount_join' => $isIncomePriceEnabled ? '<td>' . $sCurrency . (float)$aValues['levels']['levels_' . $i . '_' . $iMemLevel] . '</td>' : '', 
							 'amount_upgrade' => $isIncomePercentEnabled ? '<td>' . (int)$aValues['levels_upgrade']['levels_upgrade_' . $i . '_' . $iMemLevel] . '%</td>' : '',
							 'amount_points_join' => $isPointsForJoin ? '<td>' . (int)$aValues['points']['points_' . $i . '_' . $iMemLevel] . '</td>' : '',
							 'amount_points_upgrade' => $isPointsForUpgrade ? '<td>' . (int)$aValues['points_upgrade']['points_upgrade_' . $i . '_' . $iMemLevel] . '</td>' : '',
							 'points' => $isPointsForJoin ? '<td>' . (int)($aValues['points']['points_' . $i . '_' . $iMemLevel] * $iPeople) . '</td>' : '',
							 'points_upgrade' => $isPointsForUpgrade ? '<td>' . (int)($aValues['points_upgrade']['points_upgrade_' . $i . '_' . $iMemLevel] * $iPeople) . '</td>' : '',		
							 'income' => $isIncomePriceEnabled ? '<td>' . $sCurrency . ((float)$aValues['levels']['levels_' . $i . '_' . $iMemLevel] * $iPeople) . '</td>' : '',
							);
		 
		 $iIncomeSum += (float)$aValues['levels']['levels_' . $i . '_' . $iMemLevel] * $iPeople;
		 
		if ($isPointsForJoin){
				$aItems[$i]['points'] = '<td>' . ((int)$aValues['points']['points_' . $i. '_' . $iMemLevel] * $iPeople) . '</td>';
				$iPointsSum += (int)$aValues['points']['points_' . $i. '_' . $iMemLevel] * $iPeople; 		
		}
	
		if ($isPointsForUpgrade){
				$aItems[$i]['points_upgrade'] = '<td>' . ((int)$aValues['points_upgrade']['points_upgrade_' . $i . '_' . $iMemLevel] * $iPeople) . '</td>';
				$iPointsSumUpgrade += (int)$aValues['points_upgrade']['points_upgrade_' . $i . '_' . $iMemLevel] * $iPeople; 		
			}
	  	}	
		
	    $iCel = 2;
	    if ($isIncomePriceEnabled) $iCel+=1;
		if ($isIncomePercentEnabled) $iCel+=1;
		if ($isPointsForUpgrade) $iCel+=1;
		if ($isPointsForJoin) $iCel+=1;
		
		return 	$this -> parseHtmlByName('info_table', 
													array(
														'bx_repeat:items' => $aItems, 
														'amount_for_join' => $isIncomePriceEnabled ? '<th>' . _t('_aqb_aff_admin_txt_amount_join') . '</th>' : '', 
														'amount_for_upgrade' => $isIncomePercentEnabled ? '<th>' . _t('_aqb_aff_admin_txt_amount_upgrade') . '</th>' : '', 
														'amount_points_for_join' => $isPointsForJoin ? '<th>' . _t('_aqb_aff_admin_txt_amount_points_join') . '</th>' : '',  
														'amount_points_for_upgrade' => $isPointsForUpgrade ? '<th>' . _t('_aqb_aff_admin_txt_amount_points_upgrade') . '</th>' : '', 														
														'points_title' => ($isPointsForJoin) ? '<th>' . _t('_aqb_aff_admin_txt_points') . '</th>' : '', 
														'total_points' => ($isPointsForJoin) ? '<td>' . $iPointsSum . '</td>' : '', 
														'total_upgrade_points' => ($isPointsForUpgrade) ? '<td>' . $iPointsSumUpgrade . '</td>' : '', 
														'total_income' => ($isIncomePriceEnabled) ? '<td>' . $sCurrency . $iIncomeSum . '</td>' : '',
														'level_title' =>   $isIncomePriceEnabled ? '<th>' . _t('_aqb_aff_admin_txt_income', $sCurrency) . '</th>' : '', 
														'points_upgrade_title' => ($isPointsForUpgrade) ? '<th>' . _t('_aqb_aff_admin_txt_points_upgrade') . '</th>' : '',
														'cel' => $iCel
													)
										);
	}
	
	function createNewBannerSection(){
		$aForm = array(
			    'form_attrs' => array(
			            'id' => 'aff-banner',
			            'name' => 'aff-banner',
						'method' => 'post',
			            'enctype' => 'multipart/form-data'
			        ),
				
				'params' => array (
                'db' => array(
                    'submit_name' => 'aff_upload',
					)
				),
		
				'inputs' => array(
	            
				'banner_name' => array(
	                'type' => 'text',
	                'name' => 'banner_name',
	                'value' => '',
					'caption' =>  _t('_aqb_aff_banner_name'),
	     		),
				
				'new_banner' => array(
	                'type' => 'file',
	                'name' => 'new_banner',
	                'caption' => _t('_aqb_aff_add_new_banner'),
	                'value' => '',
	            ),
	            
				'upload' => array(
	                'type' => 'submit',
	                'name' => 'aff_upload',
	                'value' => _t("_aqb_aff_upload"),
	            )
			)	
        );
		
		$oForm = new BxTemplFormView($aForm);
		
		if ($oForm -> isSubmitted() && !empty($_FILES['new_banner']['tmp_name'])){
			$aFileInfo['name'] = bx_get('banner_name');	
			$aUploadResult = $this -> uploadImage();
			if (!is_array($aUploadResult)) return $aUploadResult;
			$aFileInfo = array_merge($aFileInfo, $aUploadResult);
			$this -> _oDb -> createNewBanner($aFileInfo); 
		}		
		
		return '<div id="items-control-panel" class="items-control-wrapper">' . $oForm->getCode() . '</div>';
	}
	
	function uploadImage(){
			$aFileInfo = array();
			$aFileInfo = getimagesize($_FILES['new_banner']['tmp_name']);
			if(empty($aFileInfo)) return MsgBox(_t('_aqb_aff_upload_error'));

			$sExt = '';
			
			switch( $aFileInfo['mime'] ) {
				case 'image/jpeg': $sExt = 'jpg'; break;
				case 'image/gif':  $sExt = 'gif'; break;
				case 'image/png':  $sExt = 'png'; break;
			}
			
			if(empty($sExt)) return MsgBox(_t('_aqb_aff_upload_error'));

		    $sFileName = mktime() . '.' . $sExt;
			$sFilePath = $this -> _oConfig -> getBannersPath() . $sFileName;
			
			if(!file_exists($this -> _oConfig -> getBannersPath())) {
				mkdir($this -> _oConfig -> getBannersPath(), 0777, true); @chmod($this -> _oConfig -> getBannersPath(), 0777);
			};
	    
			if (!move_uploaded_file($_FILES['new_banner']['tmp_name'], $sFilePath)) return false;
			else $aFileInfo['img'] = $sFileName;

		return $aFileInfo; 	
	}
		
	function getBannerMainItems($aBanner){
		$aBannerSize = split('x',$aBanner['size']);
		
		$aForm = array(
     	'form_attrs' => array(
            'id' => 'banner-form',
			'name' => 'banner-form',
            'method' => 'post',
		    'enctype' => 'multipart/form-data',
			'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'update_banner/' . $aBanner['id'],
    		'onsubmit' => "javascript: Item.onFromSubmit(this); return false;"
        ),
		
		'inputs' => array(
        
		'new_banner' => array(
	                'type' => 'file',
	                'name' => 'new_banner',
	                'caption' => _t('_aqb_aff_replace_new_banner'),
	                'value' => '',
	            ),

		'banner_width' => array(
	                'type' => 'text',
	                'name' => 'banner_width',
	                'value' => $aBannerSize['0'],
					'caption' =>  _t('_aqb_aff_banner_width'),
			),

		'banner_height' => array(
	                'type' => 'text',
	                'name' => 'banner_height',
	                'value' => $aBannerSize['1'],
					'caption' =>  _t('_aqb_aff_banner_height'),
		),

		'banner_name' => array(
	                'type' => 'text',
	                'name' => 'banner_name',
	                'value' => $aBanner['name'],
					'caption' =>  _t('_aqb_aff_banner_name'),
			),

		'banner_link' => array(
	                'type' => 'text',
	                'name' => 'banner_link',
	                'caption' => _t('_aqb_aff_admin_link'),
	                'value' => $aBanner['link'],
	            ),
        
		'hidden' => array(
					'type' => 'hidden',
		            'value' => (int)$aBanner['id'],
		   		    'name' => 'banner_id',
					'attrs' => array('id' => 'banner_id')
				),
		'hidden_form' => array(
					'type' => 'hidden',
		            'value' => 'params',
		   		    'name' => 'form_name'
				),		
		'save' => array(
                    'type' => 'submit',
                    'name' => 'aqb_banner_parameters',
                    'value' => _t('_aqb_aff_save_button'),
        			'attrs' => array('id' => 'aqb_param_save')
    	)));
    	
	$oForm = new BxTemplFormView($aForm);
	$aVars = array (
            'form' => $oForm -> getCode(),
			'tab_id' => 'banner_tab'
	  );
	  
	   return  $this -> parseHtmlByName('tabs', $aVars);
	}

	function getCommissionItems(&$aBanner){
	   $aForm = array(
     	'form_attrs' => array(
            'id' => 'commission-form',
			'name' => 'commission-form',
            'method' => 'post',
		    'enctype' => 'multipart/form-data',
			'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'update_banner/' . $aBanner['id'],
    		'onsubmit' => "javascript: Item.onFromSubmit(this); return false;"
        ),
	
        'inputs' => array(
        
		'impression_price' => array(
	                'type' => 'text',
	                'name' => 'impression_price',
	                'caption' => _t('_aqb_aff_impression_price'),
	                'value' => $aBanner['impression_price'],
	            ),
		'click_price' => array(
	                'type' => 'text',
	                'name' => 'click_price',
	                'caption' => _t('_aqb_aff_click_price'),
	                'value' => $aBanner['click_price'],
	            ),	
		'join_price' => array(
	                'type' => 'text',
	                'name' => 'join_price',
	                'caption' => _t('_aqb_aff_join_price'),
	                'value' => $aBanner['join_price'],
	            ),	
		'upgrade_price' => array(
	                'type' => 'text',
	                'name' => 'upgrade_price',
	                'caption' => _t('_aqb_aff_percent_upgraded_membership'),
	                'value' => $aBanner['upgrade_price'],
	            ),				
		'hidden' => array(
					'type' => 'hidden',
		            'value' => (int)$aBanner['id'],
		   		    'name' => 'banner_id',
					'attrs' => array('id' => 'banner_id')
				),
	
		'hidden_form' => array(
					'type' => 'hidden',
		            'value' => 'commission_price',
		   		    'name' => 'form_name'
				),		
		
		'save' => array(
                    'type' => 'submit',
                    'name' => 'commission_save',
                    'value' => _t('_aqb_aff_save_button'),
        			'attrs' => array('id' => 'aqb_commission_save')
    	)));
    
	$oForm = new BxTemplFormView($aForm);
	$aVars = array (
            'form' => $oForm -> getCode(),
			'tab_id' => 'commission_tab'
	  );
	  
	   return  $this -> parseHtmlByName('tabs', $aVars);	
	}

	function getPointsItems(&$aBanner){
	   $aForm = array(
     	'form_attrs' => array(
			'name' => 'form',
            'method' => 'post',
		    'enctype' => 'multipart/form-data',
			'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'update_banner/' . $aBanner['id'],
    		'onsubmit' => "javascript: Item.onFromSubmit(this); return false;"
        ),
		
		'inputs' => array(
        
		'impression_price_points' => array(
	                'type' => 'text',
	                'name' => 'impression_price_points',
	                'caption' => _t('_aqb_aff_impression_points_price'),
	                'value' => $aBanner['impression_price_points'],
	            ),
		'click_price_points' => array(
	                'type' => 'text',
	                'name' => 'click_price_points',
	                'caption' => _t('_aqb_aff_click_price_points'),
	                'value' => $aBanner['click_price_points'],
	            ),	
		'join_price_points' => array(
	                'type' => 'text',
	                'name' => 'join_price_points',
	                'caption' => _t('_aqb_aff_join_price_points'),
	                'value' => $aBanner['join_price_points'],
	            ),
		'upgrade_price_points' => array(
	                'type' => 'text',
	                'name' => 'upgrade_price_points',
	                'caption' => _t('_aqb_aff_points_upgraded_membership'),
	                'value' => $aBanner['upgrade_price_points'],
	            ),		
        'hidden' => array(
					'type' => 'hidden',
		            'value' => (int)$aBanner['id'],
		   		    'name' => 'banner_id',
					'attrs' => array('id' => 'banner_id')
				),
		'hidden_form' => array(
					'type' => 'hidden',
		            'value' => 'commission_points',
		   		    'name' => 'form_name'
				),		
		'save' => array(
                    'type' => 'submit',
                    'name' => 'aqb_pc_rss_save',
                    'value' => _t('_aqb_aff_save_button'),
        			'attrs' => array('id' => 'aqb_pc_rss_save')
    	)));
    	
	$oForm = new BxTemplFormView($aForm);
	$aVars = array (
            'form' => $oForm -> getCode(),
			'tab_id' => 'points_tab'
	  );
	  
	   return  $this -> parseHtmlByName('tabs', $aVars);	
	}
	
	function getEditBannerForm($iID){
		$aBanner = $this -> _oDb -> getBanner($iID);
		if (empty($aBanner)) return MsgBox(_t('_aqb_aff_banner_not_found'));
		
		$sWrongPointsMessage = addslashes(_t('_aqb_points_wrong_present_points'));
		$sConfirm = addslashes(_t('_aqb_points_confirm_present', '{0}'));
   	
	    $sTitle = '';
	   
	  	$sTabs .= $this -> getBannerMainItems($aBanner);
		$sTitle .= '<li><a href="#banner_tab">'._t('_aqb_aff_edit_parameters').'</a></li>';
	  
		$sTabs .= $this -> getCommissionItems($aBanner);
		$sTitle .= '<li><a href="#commission_tab">'._t('_aqb_aff_edit_commission').'</a></li>';
		
		if ($this -> _oDb -> isPointsSystemInstalled())	{ 
			$sTabs .= $this -> getPointsItems($aBanner);
			$sTitle .= '<li><a href="#points_tab">'._t('_aqb_aff_edit_points').'</a></li>';
		}
	
	  if ($sTitle) $sTabsTitles ="<ul>{$sTitle}</ul>"; 
             
      return PopupBox('aqb_popup', _t('_aqb_aff_edit_banner'), $this -> parseHtmlByName('new_banner_form', array('titles' => $sTabsTitles, 'content' => $sTabs)));
    }
	
	function getBannerPreviewForm($iID, $iMemberID){
		$aBanner = $this -> _oDb -> getBanner($iID);
		$aBannerSize = split('x',$aBanner['size']);
		
		$aForm = array(
     	'form_attrs' => array(
			'name' => 'form',
		    'enctype' => 'multipart/form-data'
        ),

		'inputs' => array(
		'banner' => array(
                'type' => 'custom',
                'content' => '<div class="aff-auto-banner"><center><img src=' . $this -> _oConfig -> getBannersUrl() . $aBanner['img'] .' width="'.$aBannerSize['0'].'" height="'.$aBannerSize['1'].'" /></center></div>',
      			'colspan' => true,
       	),
		'textarea' => array(
	                'type' => 'textarea',
	                'name' => 'banner_text',
					'html' => 0,
	                'caption' => _t('_aqb_aff_code'),
	                'value' => $this -> getBannerCode($iID, $iMemberID),
					'attrs_wrapper' => array('style' => 'width:100%;height:100px;'),
					'attrs' => array('style' => 'width:100%;height:100px;'),
	            )
		));
    	
		$oForm = new BxTemplFormView($aForm);
		return  '<div id="items-control-panel" class="items-control-wrapper">' . $oForm -> getCode() . '</div>';
	}
	
	function getAvailableBannersPanel($iMemberId){
		$aBanners = $this -> _oDb -> getBanners(true);
	
		if (empty($aBanners)) return MsgBox(_t('_aqb_aff_empty_result'));
		$this->_CurrencySign = $this -> _oConfig -> getCurrencySign();
	
		$aForm = array(
     	'form_attrs' => array(
			'name' => 'banners-form',
		    'enctype' => 'multipart/form-data'
        ));
    	
		$aForm['inputs'] = array();
		
		$oForm = new BxTemplFormView(array());
				
		foreach($aBanners as $iKey => $aValue ){
			$aPointsItems = $aCurrencyItems = $aInfoItems = array();
			
			$aRefLink = array('type' => 'button',  'name' => 'button_' . $aValue['id'], 'value' => _t('_aqb_aff_button_show_banner_code'), 'attrs' => array('onclick' =>"javascript:if ($('#text_area_{$aValue['id']}').is(':visible')) $('#text_area_{$aValue['id']}').hide(); else $('#text_area_{$aValue['id']}').show('slow');"));
			$sButton = $this -> genWrapperInput($aRefLink, $oForm -> genInput($aRefLink));
		
			$aTextArea = array(
				                'type' => 'textarea',
				                'name' => 'banner_text_'.$aValue['id'],
								'html' => 0,
								'colspan' => true,
				                'value' => $this -> getBannerCode($aValue['id'], $iMemberId),
								'attrs_wrapper' => array('style' => 'width:400px;height:100px;'),
								'attrs' => array('style' => 'width:400px;height:100px;' , 'onfocus' => 'javascript:$(this).select();'),
				            );
			
			$sTextArea = $this -> genWrapperInput($aTextArea, $oForm -> genInputTextarea($aTextArea));				
					
					
			if ($aValue['name'])
				$aInfoItems[]['items'] = _t('_aqb_aff_affiliate_banners_name') . '&nbsp;:&nbsp;<b>' . $aValue['name'] . '</b>';
			
			$aInfoItems[]['items'] = _t('_aqb_aff_affiliate_banners_size') . '&nbsp;:&nbsp;<b>' . $aValue['size'] . '</b>';
			
			$fProcentJoinPrice = $this -> _oDb -> getPriceForAction('join', 'price', $iMemberId, $aValue['id']);
			$iProcentUpgradePrice = $this -> _oDb -> getPriceForAction('upgrade', 'price', $iMemberId, $aValue['id']);
		
			$aCurrencyItems[]['items'] = '<b>' . _t('_aqb_aff_commission_title') . '</b>:';
			$aCurrencyItems[]['items'] = _t('_aqb_aff_affiliate_banner_price_per_impression') . '&nbsp;:&nbsp;<b>' . $this->_CurrencySign . $aValue['impression_price'] . '</b>';
			$aCurrencyItems[]['items'] = _t('_aqb_aff_affiliate_banner_price_per_click') . '&nbsp;:&nbsp;<b>' . $this->_CurrencySign . $aValue['click_price'] . '</b>';
			$aCurrencyItems[]['items'] = _t('_aqb_aff_affiliate_banner_price_per_join') . '&nbsp;:&nbsp;<b>' . $this->_CurrencySign . $fProcentJoinPrice . '</b>';
			$aCurrencyItems[]['items'] = _t('_aqb_aff_affiliate_banner_price_per_upgrade') . '&nbsp;:&nbsp;<b>' . $iProcentUpgradePrice . '%' .'</b>';
		
			if ($bIsEnabled = $this -> _oDb -> isPointsSystemInstalled()){
				$iJoinPoints = $this -> _oDb -> getPriceForAction('join', 'points', $iMemberId, $aValue['id']);
				$iUpgradePoints = $this -> _oDb -> getPriceForAction('upgrade', 'points', $iMemberId, $aValue['id']);

				$aPointsItems[]['items'] = '<b>' . _t('_aqb_aff_points_title') . '</b>:';
				$aPointsItems[]['items'] = _t('_aqb_aff_affiliate_banner_points_per_impression') . '&nbsp;:&nbsp;<b>' . $aValue['impression_price_points'] . '</b>';
				$aPointsItems[]['items'] = _t('_aqb_aff_affiliate_banner_points_per_click') . '&nbsp;:&nbsp;<b>' . $aValue['click_price_points'] . '</b>';
				$aPointsItems[]['items'] = _t('_aqb_aff_affiliate_banner_points_per_join') . '&nbsp;:&nbsp;<b>' . $iJoinPoints . '</b>';
				$aPointsItems[]['items'] = _t('_aqb_aff_affiliate_banner_points_per_upgrade') . '&nbsp;:&nbsp;<b>' . $iUpgradePoints . '</b>';
			}		
			
			
			$aForm['inputs'] = array_merge($aForm['inputs'],
			array(
					'banner_' . $aValue['id'] => array(
				               'type' => 'custom',
				               'content' => $this -> parseHtmlByName('avail_banners_panel', 
													 array(
														    'info' => $this -> parseHtmlByName('referral_info_block', 
																											array(
																													 'bx_repeat:items' => $aInfoItems
																												 )),
															'currency' => $this -> parseHtmlByName('referral_info_block', 
																											array(
																													 'bx_repeat:items' => $aCurrencyItems
																													)),	
															'bx_if:is_points_enabled' => array(
																						         'condition' => $bIsEnabled,
																						         'content' => array(
																						         'points' => $this -> parseHtmlByName('referral_info_block', 
																											array(
																													 'bx_repeat:items' => $aPointsItems
																												 )),	
																						         )
																								),													 
														    'image' => $this -> _oConfig -> getBannersUrl() . $aValue['img'],
															'src' => $this -> getIconUrl('spacer.gif'),
															'button' => $sButton,
															'textarea' => '<div id="text_area_'.$aValue['id'].'" class="aqb-aff-textarea">'. $sTextArea . '</div>',
														  )),
							  'colspan' => true,
							  'attrs_wrapper' => array('style' => 'width:100%;'),
						),
			));				
		}
		
		$oForm = new BxTemplFormView($aForm);
		return  $oForm -> getCode();	
	}
	
	function getBannerCode($iID, $iMemberID){
		if (!(int)$iID || !(int)$iMemberID) return '';
		
		$aBanner = $this -> _oDb -> getBanner($iID);
		$aBannerSize = split('x',$aBanner['size']);
		
		$sPhpLink = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'register_banner/' . $iID . '/' . $iMemberID;
		
		$sBannerImg =<<<EOF
	<a href="{$aBanner['link']}?affaqb={$iMemberID}&b={$iID}" onclick="javascript:void(0);"><img width="{$aBannerSize['0']}" height="{$aBannerSize['1']}" src="{$this -> _oConfig -> getBannersUrl()}{$aBanner['img']}"/></a><script src="$sPhpLink" /></script>
EOF;
		return $sBannerImg;
	}

	function showBannerPanel(&$aBanner){
		$aResult = array(

        $aBanner['id'] => array(
                    'type' => 'custom',
                    'content' => '<div class="aff-auto-banner"><img src=' . $this -> _oConfig -> getBannersUrl() . $aBanner['img'] .' /></div>',
        			'colspan' => true,
       		),

		'impression_price_'.$aBanner['id'] => array(
	                'type' => 'text',
	                'name' => 'impression_price_'.$aBanner['id'],
	                'caption' => _t('_aqb_aff_impression_price'),
	                'value' => $aBanner['impression_price'],
	            ),
		'impression_points_price_'.$aBanner['id'] => array(
	                'type' => 'text',
	                'name' => 'impression_points_price_'.$aBanner['id'],
	                'caption' => _t('_aqb_aff_impression_points_price'),
	                'value' => $aBanner['impression_points_price'],
	            ),
		'click_price_'.$aBanner['id'] => array(
	                'type' => 'text',
	                'name' => 'click_price_'.$aBanner['id'],
	                'caption' => _t('_aqb_aff_click_price'),
	                'value' => $aBanner['click_price'],
	            ),	
		
		'click_price_points_'.$aBanner['id'] => array(
	                'type' => 'text',
	                'name' => 'click_price_points_'.$aBanner['id'],
	                'caption' => _t('_aqb_aff_click_price_points'),
	                'value' => $aBanner['click_price_points'],
	            ),	
		
		'join_price_'.$aBanner['id'] => array(
	                'type' => 'text',
	                'name' => 'join_price_'.$aBanner['id'],
	                'caption' => _t('_aqb_aff_join_price'),
	                'value' => $aBanner['join_price'],
	            ),	
		
		'join_price_points_'.$aBanner['id'] => array(
	                'type' => 'text',
	                'name' => 'join_price_points_'.$aBanner['id'],
	                'caption' => _t('_aqb_aff_join_price_points'),
	                'value' => $aBanner['join_price_points'],
	            ),					
				
		);
 
		return $aResult;
	}
	
	function getAffBannersPanel(){
	    $aButtons = array(
	    	'aqb-aff-delete' => _t('_aqb_aff_banner_delete'),
			'aqb-aff-approve' => _t('_aqb_aff_banner_approve')
	    );    
    
		$sControls = BxTemplSearchResult::showAdminActionsPanel('items-form', $aButtons, 'banners');
	       
	    $oPaginate = new BxDolPaginate(array(
	        'per_page' => BX_DOL_ADM_MP_PER_PAGE,
	        'per_page_step' => BX_DOL_ADM_MP_PER_PAGE_STEP,
	        'on_change_per_page' => BX_DOL_ADM_MP_JS_NAME . '.changePerPage(this);'
	    ));    
	    
		$aResult = array(
	        'per_page' => $oPaginate->getPages(),
	        'control' => $sControls,	    
		);

	    $aResult = array_merge($aResult, array('content_common' => $this -> getBannersTable()));
			
		return $this -> parseHtmlByName('admin_main', 
		array(
					'banners' =>$this  -> parseHtmlByName('banners', $aResult),
				    'obj_name' => BX_DOL_ADM_MP_JS_NAME,
					'actions_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(),
				    'per_page' => '32',
				    'order_by' => '',
					'sel_view' => '',
					'section' => 'banners',
					'loading' => LoadingBox('div-loading')			
			));
	}
	
	function getBannersTable($aParams = array()) {
		if (empty($aParams['view_type'])) $aParams['view_type'] = 'DESC';
		if(!isset($aParams['view_start']) || empty($aParams['view_start'])) $aParams['view_start'] = 0;
	    if(!isset($aParams['view_per_page']) || empty($aParams['view_per_page'])) $aParams['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;
		
	    $aParams['view_order_way'] = $aParams['view_type'];
	    
	    if(!isset($aParams['view_order']) || empty($aParams['view_order'])) $aParams['view_order'] = 'date';
	    
		$aBanners = $this -> _oDb -> getBannersItems($aParams);
	   	
	    $sBaseUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri();
				
	    $aItems = array();

	    foreach($aBanners as $aBanner){

			$aItems[$aBanner['id']] = array(
	            'id' => $aBanner['id'],
	            'banner_name' => strlen($aBanner['name']) > 20 ? substr($aBanner['name'],0,20) . '...' : $aBanner['name'],    
				'banner_size' => $aBanner['size'],
				'banner_link' => $aBanner['link'],
				'banner_created' =>getLocaleDate($aBanner['date'], BX_DOL_LOCALE_DATE_SHORT),
				'banner_active' => (int)$aBanner['active'] ? _t('_aqb_aff_admin_yes') : _t('_aqb_aff_admin_no'),
				'edit_banner' => _t('_aqb_aff_admin_edit'), 
				'view_banner' => _t('_aqb_aff_admin_view'), 
				'view_banner_link' => "javascript:AqbAffItem.showPopup('{$sBaseUrl}view_banner/{$aBanner['id']}');",
				'edit_banner_link' => "javascript:AqbAffItem.showPopup('{$sBaseUrl}edit_banner/{$aBanner['id']}');",
			);
	    }
	  
		//--- Get Paginate ---//
	    $oPaginate = new BxDolPaginate(array(
	        'start' => $aParams['view_start'],
	        'count' => $this -> _oDb -> _iMembersCount,
	        'per_page' => $aParams['view_per_page'],
	        'page_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '?start={start}',
	        'on_change_page' => BX_DOL_ADM_MP_JS_NAME . '.changePage({start})'
	    ));
	    
		$sPaginate = $oPaginate -> getPaginate(); 
		    
	    return $this -> parseHtmlByName('banners_table', array(
	        'bx_repeat:items' => array_values($aItems),
			'item_id_function'=> $this -> getOrderParpoints('id', $aParams),
			'banner_id_arrow'   => $this -> getArrowImage('id', $aParams),
	     	'item_name_function'=> $this -> getOrderParpoints('name', $aParams),
			'banner_name_arrow'   => $this -> getArrowImage('name', $aParams),
		    'item_size_function' => $this -> getOrderParpoints('size', $aParams),
			'banner_size_arrow'    => $this -> getArrowImage('size', $aParams),
			'item_link_function'=> $this -> getOrderParpoints('link', $aParams),
			'banner_link_arrow'   => $this -> getArrowImage('link', $aParams),
			'item_date_function'=> $this -> getOrderParpoints('date', $aParams),
			'banner_date_arrow'   => $this -> getArrowImage('date', $aParams),
			'item_active_function'=> $this -> getOrderParpoints('active', $aParams),
			'banner_active_arrow'   => $this -> getArrowImage('active', $aParams),
			'paginate' => $sPaginate
	    ));                                                                                                             
	
	}
	 	
	function getMembershipPanel($iLevel){
		$aResult = array();
	    $aItems = $this -> _oDb -> getMembershipLevels();
		
		foreach($aItems as $aItem){
			if ((int)$iLevel == (int)$aItem['ID']) $sTitle = '<b>' . strtolower($aItem['Name']) .'</b>'; else $sTitle = strtolower($aItem['Name']);
			$aResult[] = array('link' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/membership/' . $aItem['ID'], 'on_click' => '', 'title' => $sTitle, 'class' => ($aItem['Active'] == 'yes' ? 'active' : 'not_active'));
		}	
	
		$sMemBlock = $this->parseHtmlByName('memberships', array('bx_repeat:membership_links' =>  $aResult));
	
		return $sMemBlock ;	
	}
	
	function ViewReferralsTable($iMember){
	    $bAdmin = isAdmin();
		
		if (!empty($_POST['referrals']) && $bAdmin){
			foreach($_POST['referrals'] as $iKey => $iValue)
				$this -> _oDb -> deleteNode($iValue);
		}
	
	   $oForm = new BxTemplFormView(array());
		
   	   $aButton = array('type' => 'text',  'name' => 'search', 'value' => '', 'attrs_wrapper' => array('style' => 'margin:10px;'), 'attrs' => array('id' => 'aqb_ref_search_item'));
	   $sSearchText = $this -> genWrapperInput($aButton, $oForm -> genInput($aButton)) . '</form>';

		   
	    $aButton = array('type' => 'button', 'name' => 'search_item', 'attrs_wrapper' => array('style' => 'margin:10px;'), 'attrs' => array('onclick' => 'javascript:AqbMain.changeFilterSearch();'), 'value' => _t('_aqb_ref_search_item'));
	    $sSearchButton = $this -> genWrapperInput($aButton, $oForm -> genInput($aButton)) . '</form>';
		
		if ($bAdmin){
		   $oForm = new BxTemplFormView(array());
		   $aButton = array('type' => 'submit',  'onclick' => 'return $(\'div.aqb-aff-matrix-table\').find(\'input:checkbox:checked\').length > 0;', 'name' => 'delete_item', 'value' => _t('_aqb_aff_detele_ref'), 'attrs_wrapper' => array('style' => 'margin:10px;'));
	       $sForm = $this -> genWrapperInput($aRefButton, $oForm -> genInput($aButton));
		}
		
		$sRefreshLink = '<a href="javascript:void(0);" onclick="javascript:AqbMain.refresh();">' . _t('_aqb_ref_referrals_refresh') . '</a>';
				
		return $this -> parseHtmlByName('ref_table_main', 
		array(
					'referrals' => $this -> getReferralsInfo(array('member_id' => $iMember)),
					'actions_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(),
				    'sel_view' => '',
				    'per_page' => BX_DOL_ADM_MP_PER_PAGE,
				    'order_by' => '',
					'member_id' => $iMember,
					'loading' => LoadingBox('div-loading'),
					'search' => $sSearchText . '&nbsp;&nbsp;' . $sSearchButton,
					'FORM_BEGIN'=> $bAdmin ? '<form id="referrals-matrix-table" enctype="multipart/form-data" method="post" action="" class="form_advanced" onsubmit="return $(\'div.aqb-aff-matrix-table\').find(\'input:checkbox:checked\').length > 0 && confirm(\'' . bx_js_string(_t('_aqb_ref_delete_confirmation')) . '\');">' : '',
					'FORM_END'=> $bAdmin ? '</form>' : '',
					'form' => $bAdmin ? $sRefreshLink . '&nbsp;&nbsp;' . $sForm : $sRefreshLink,					
			));
	}
	
	function getReferralsInfo($aParpoints){
		$bAdmin = isAdmin();
	
		if (empty($aParpoints['view_type'])) $aParpoints['view_type'] = 'ASC';
		if(!isset($aParpoints['view_start']) || empty($aParpoints['view_start'])) $aParpoints['view_start'] = 0;
	    if(!isset($aParpoints['view_per_page']) || empty($aParpoints['view_per_page'])) $aParpoints['view_per_page'] = BX_DOL_ADM_MP_PER_PAGE;
		
	    $aParpoints['view_order_way'] = $aParpoints['view_type'];
	    
	    if(!isset($aParpoints['view_order']) || empty($aParpoints['view_order'])) $aParpoints['view_order'] = 'ID';

		
		$aTree = $this -> _oDb -> getMemberReferralsTable($aParpoints);
		
		if (empty($aTree)) return MsgBox(_t('_aqb_aff_nothing_found'));
		
		$iMatrixLevel = $this -> _oDb -> getMatrixLevel($iMember);
	
		$aItems = array();
		foreach($aTree as $iKey => $aVal){
			$aItems[] = array(
								'first' => $aVal['FullName'], 
								'username' => $aVal['NickName'],
								'link' => getProfileLink($aVal['member']),
								'reg_date' => $aVal['DateReg'],
								'referred' => $aVal['referral'], //$this -> _oDb -> getMyReferralNickName($aVal['member']),
								'level' => $aVal['level'],
								'id' => $aVal['member'],
								'checkbox' => $bAdmin ? "<input type=\"checkbox\" class=\"form_input_checkbox\" id=\"{$aVal['member']}\" name=\"referrals[]\" value=\"{$aVal['member']}\" />" : ''
							  );
		}	

				
		//--- Get Paginate ---//
		    $oPaginate = new BxDolPaginate(array(
		        'start' => $aParpoints['view_start'],
		        'count' => (int)$aParpoints['count'],
		        'per_page' => $aParpoints['view_per_page'],
		        'page_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '/referrals/table/?start={start}',
		        'on_change_page' =>'AqbMain.changePage({start})'
		    ));
		    
		$sPaginate = $oPaginate -> getPaginate(); 
		
		$sContent = $this -> parseHtmlByName('ref_table', 
		array(
				'paginate' => $sPaginate,
				'bx_repeat:items' => $aItems,

				'item_first_function' => $this -> getOrderRef('FullName', $aParpoints),
				'first_arrow' => $this -> getArrowImage('FullName', $aParpoints),

				'item_username_function' => $this -> getOrderRef('NickName', $aParpoints),
				'username_arrow' => $this -> getArrowImage('NickName', $aParpoints),

				'item_date_function' => $this -> getOrderRef('DateReg', $aParpoints),
				'date_arrow' => $this -> getArrowImage('DateReg', $aParpoints),
				
				'item_level_function' => $this -> getOrderRef('Level', $aParpoints),
				'level_arrow' => $this -> getArrowImage('Level', $aParpoints),
				
				'item_referred_function' => $this -> getOrderRef('referral', $aParpoints),
				'referred_arrow' => $this -> getArrowImage('referral', $aParpoints),
		
			));
			 
		return $sContent;		
	}
	
	function getOrderRef($sFName, &$aParpoints){
		return 'onclick="AqbMain.orderByField(\'' . (($aParpoints['view_order'] == $sFName && 'asc' == strtolower($aParpoints['view_type'])) ? 'desc' : 
			   ($aParpoints['view_order'] == $sFName && 'desc' == strtolower($aParpoints['view_type']) ? 'asc' : '')) . '\',\''.$sFName.'\')"';
    }
	
	function ViewMemberTree($iMember){
		$bAdmin = isAdmin();
		
		$sProfileNickName = getNickName($iMember);
		$aTree = $this -> _oDb -> getMembersReferralsTree($iMember);
		if (empty($aTree)) return  MsgBox(_t('_aqb_aff_nothing_found'));
		
		$iMatrixLevel = $this -> _oDb -> getMatrixLevel($iMember);
	
		
		$sArray = '';
		foreach($aTree as $iKey => $aVal){
			$sOwnerName = bx_js_string($aVal['NickName']);
			$sRealRefName = bx_js_string($this -> _oDb -> getMyReferralNickName($aVal['member']));
			$sRefName = bx_js_string($this -> _oDb -> getMyParent($aVal['member']));
						
			if ($aVal['Status'] != 'Active') $sStatus = "<div class=\"aqb-tree-member-status\">" . bx_js_string(_t('_aqb_aff_matrix_tree_status', _t('_'.$aVal['Status']))) . '</div>'; else $sStatus = '';
			if ($sRefName != $sRealRefName) $sNick = "{v:'{$sOwnerName}', f:'{$sOwnerName}<div class=\"aqb-real-ref\">" . _t('_aqb_aff_matrix_tree_real_ref', $sRealRefName) . "</div>{$sStatus}'}"; else $sNick = "'{$sOwnerName}'";
			
			$sArray .= "[{$sNick}, '{$sRefName}','{$aVal['member']}'],";
		}	
		
		if ($sArray) $sArray = substr($sArray,0,-1);
		
		$sNickName =  bx_js_string(getNickName($iMember)); 
		$sParentTitle = bx_js_string(_t('_aqb_aff_matrix_tree_parent_node'));
		$sBoxLoading = '<div id="box-loading-div" style="overflow:hidden;height:100px;position:relative;">' . LoadingBox('div-loading') . '</div>';
		
$sContent =<<<EOF
	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
	  $('#div-loading').bx_loading();	
      google.load('visualization', '1', {packages:['orgchart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
		$('#div-loading').bx_loading();
		$('#box-loading-div').css('display','none');
        var data = new google.visualization.DataTable();
		data.addColumn('string', 'NickName');
        data.addColumn('string', 'Status');
        data.addColumn('string', 'ToolTip');
        data.addRows([['{$sNickName}', '', '{$sParentTitle}'],{$sArray}]);
        var chart = new google.visualization.OrgChart(document.getElementById('aqb-tree-area'));
        chart.draw(data, {allowHtml:true, size:'medium', allowCollapse:true});
      }
    </script>
	<div id="aqb-tree-area"></div>
EOF;
		return  $sBoxLoading . '<div class="aqb-aff-matrix-tree">' . $sContent . '</div>';
	}
	
	function getSettingsPanel() {
        $iId = $this -> _oDb ->getSettingsCategory();
		
        if(empty($iId))
           return MsgBox(_t('_aqb_aff_nothing_found'));
        bx_import('BxDolAdminSettings');
    		
        $mixedResult = '';
        if(isset($_POST['save']) && isset($_POST['cat'])) {
       		$oSettings = new BxDolAdminSettings($iId);			
			
			if (!isset($_POST['aqb_referral_turn_on'])){
				$this -> _oDb -> hideMenu();
				$this -> _oDb -> seDefaultPage('affiliate');
			}	
			elseif (isset($_POST['aqb_referral_turn_on'])){
				$this -> _oDb -> hideMenu('show');
				$this -> _oDb -> seDefaultPage();
			}	

			if (!isset($_POST['aqb_affiliate_turn_on'])) $this -> _oDb -> hideMenu('hide','affiliate'); 
			elseif (isset($_POST['aqb_affiliate_turn_on'])) $this -> _oDb -> hideMenu('show','affiliate');
				
					
			
			if (!isset($_POST['aqb_referral_turn_on']) && !isset($_POST['aqb_affiliate_turn_on'])) $this -> _oDb -> seDefaultPage('history');
			
            $mixedResult = $oSettings -> saveChanges($_POST);
			$oSettings -> _onSavePermalinks();
        }
        
        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();
                   
        if($mixedResult !== true && !empty($mixedResult))
            $sResult = $mixedResult . $sResult;
        return  '<div id="items-control-panel" class="items-control-wrapper">' . $sResult . '</div>';
    }
	
	function getReferralsIndexBlock(&$oMain){
		if (!$this -> _oConfig -> isReferralsBlockForIndexEnabled())
			return '';
		
		$iPerPage = $this -> _oConfig -> getPerPageOnRefIndexBlock();
		
		$iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $this -> _oDb -> getReferrals($aProfiles, $iStart, $iPerPage);
		$iNum = $this -> _oDb -> getReferralsNum();		
		
		if (!$iNum || !$aProfiles) {
            return '';
        }
		
		bx_import ('SearchProfile', $oMain -> _aModule);
        $sClass = $oMain -> _aModule['class_prefix'] . 'SearchProfile';
        $oSearchProfile = new $sClass ($oMain);	
		
        $sMainContent = '';        
		foreach ($aProfiles as $aProfile) {
			$sMainContent .= $oSearchProfile -> displaySearchUnit($aProfile, array('date' => _t('_aqb_aff_count_index' ,$aProfile['count'])));
        }
	   
		$sRet  = $GLOBALS['oFunctions']->centerContent($sMainContent, '.searchrow_block_simple');
        $sRet .= '<div class="clear_both"></div>';    

        bx_import('BxDolPaginate');
	    
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'index.php',
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $_SERVER['PHP_SELF'] .'?page={page}\')',
            'on_change_per_page' => '',
        ));

		return array($sRet, array(), $oPaginate->getPaginate($iStart, $iPerPage));
	}
	
	function getMyInvitedMembers($iProfile){
		if (!$this -> _oConfig -> isMyInvitedMembersBlockForProfileEnabled()) return '';
		
		$iPerPage = $this -> _oConfig -> getPerPageOnInvitedMemBlock();
		
		$iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $this -> _oDb -> getMyInvitedMembers($aProfiles, $iProfile, $iStart, $iPerPage);
		$iNum = $this -> _oDb -> getMyInvitedMembersCount($iProfile);		
		
		if (!$iNum || !$aProfiles) {
            return '';
        }
		
        $sMainContent = '';        
		foreach ($aProfiles as $aProfile) {
            $sMainContent .= $this -> oSearchProfileTmpl -> displaySearchUnit($aProfile);
        }
	   
		$sRet  = $GLOBALS['oFunctions']->centerContent($sMainContent, '.searchrow_block_simple');
        $sRet .= '<div class="clear_both"></div>';    

        bx_import('BxDolPaginate');
	    
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'index.php',
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $_SERVER['PHP_SELF'] .'?page={page}\')',
            'on_change_per_page' => '',
        ));

		return array($sRet, array(), $oPaginate->getPaginate($iStart, $iPerPage));
	}
}
?>