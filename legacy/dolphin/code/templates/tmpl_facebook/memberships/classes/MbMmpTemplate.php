<?php
/***************************************************************************
Membership Management Pro v.3.0
Created by Martinboi
http://www.martinboi.com
***************************************************************************/
bx_import ('BxDolTwigTemplate');
require_once( BX_DIRECTORY_PATH_INC . 'design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'profiles.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'admin_design.inc.php' );
require_once( BX_DIRECTORY_PATH_INC . 'utils.inc.php' );
require_once( BX_DIRECTORY_PATH_PLUGINS . 'Services_JSON.php' );
bx_import('BxTemplSearchResult');
bx_import('BxTemplBrowse');
bx_import('BxTemplTags');
bx_import('BxTemplFunctions');
bx_import('BxDolAlerts');
bx_import('BxDolEmailTemplates');

require_once( 'MbMmpProcessorForm.php' );

class MbMmpTemplate extends BxDolTwigTemplate {
    
	function MbMmpTemplate(&$oConfig, &$oDb)
	{
	    parent::BxDolTwigTemplate($oConfig, $oDb);
		$this->postSaveProc = BX_DOL_URL_ROOT.'m/memberships/post_save_processor/';
		$this->sSettingsUrl = BX_DOL_URL_ROOT.'m/memberships/settings/';
		$this->sBase 		= BX_DOL_URL_ROOT.'m/memberships/';
		$this->aProcessors = $this->_oConfig->getPaymentProcessors();
		$sBase = BX_DOL_URL_ROOT; 
		$this->sSecureBase = str_replace('http://', 'https://', $sBase);
		
		// Setup in oConfig
		$this->iconsUrl = $oConfig->getIconsUrl();
		$this->_iPageIndex = 500;
    }
	
	function addAdminCss ($sName) 
    {
     	parent::addAdminCss($sName);
    }
	
    function parseHtmlByName ($sName, $aVars)
	{
     	return parent::parseHtmlByName ($sName, $aVars);
    }

	/**
	 * Display Available Memberships
	 *
	 */
	function availMemLevels()
	{
		$iId = getLoggedId();
		if (isset($_POST['free_form']) && $_POST['join_free_mem'] == 1) {
			$this->_oDb->clearMembershipInfo($iId);
			$this->_oDb->setFreeMembership($iId,$_POST['IDLevel']);				
			$this->_oDb->setUserStatus($iId,'Active');
			deleteUserDataFile($iId);
			Redirect($this->sBase,array('joined_free' => 'success'),post);
		}
		$sUrl		 = BX_DOL_URL_ROOT;
		$sUrlModules = BX_DOL_URL_MODULES;		
		$aLevels = $this->_oDb->getMemberships();
		$aLastItem = end($aLevels);
		
		$aMembershipOptions = unserialize(getParam('dol_subs_membership_options'));
		$aCurMemInfo = getMemberMembershipInfo($iId);
		
	
		// Loop thru levels
		if(is_array($aLevels)){
			foreach($aLevels as $aLevel){

				// Check current membership against allowed memberships				
				if( !empty($aMembershipOptions) ){
					if( is_array($aMembershipOptions['purchasable_memberships'.$aCurMemInfo['ID']])){
						if( !in_array($aLevel['ID'], $aMembershipOptions['purchasable_memberships'.$aCurMemInfo['ID']]) )
							continue;
					}					
				}
				
				$sCurSign		= getParam('currency_sign');
				$iMembId 		= $aLevel['ID'];
				$aPriceInfo		= $this->_oDb->getMembershipPriceInfo($iMembId);
				
				// Setup Price unit output
				if($aPriceInfo['Length'] > '1')
					$sPriceUnit	= ($aPriceInfo['Unit'] == 'Days') ? _t('_dol_subs_days') : _t('_dol_subs_months');
				else if($aPriceInfo['Length']  == 1)
					$sPriceUnit	= ($aPriceInfo['Unit'] == 'Days') ? _t('_dol_subs_day') : _t('_dol_subs_month');
				else if(!$aPriceInfo['Length'])
					$sPriceUnit	= _t('_dol_subs_Lifetime');
				
				// Price length
				$iPricingLength = $aPriceInfo['Length'] > 0 ? (int)$aPriceInfo['Length']  : '';

				$aSettings 		= unserialize(getParam('dol_subs_processors'));
				$iDefaultMembId = $aSettings['default_memID'];
				$aExcludeMembs 	= array(1,2,3,$iDefaultMembId);

				$iUserAcl = $this->_oDb->userAcl($iId);
				$iAclMembId = $this->_oDb->getMembershipLevelId($iId);
				$aAclPriceInfo	= $this->_oDb->getMembershipPriceInfo($iAclMembId);
				$iDowngrade = (int)$this->_oDb->getSetting('disable_downgrade');
		 		$iUpgrade = (int)$this->_oDb->getSetting('disable_upgrade');
				if($iUserAcl == '1' && $iDowngrade == '1'){
					if($aAclPriceInfo['Price'] > $aPriceInfo['Price']){
						$aExcludeMembs[] = $iMembId;// Add downgrade jQuery
					}
				}
				if($iUserAcl == '1' && $iUpgrade == '1'){
					if($aAclPriceInfo['Price'] < $aPriceInfo['Price']){
						$aExcludeMembs[] = $iMembId;// Add upgrade jQuery
					}
				}				
				
				if (!in_array($iMembId, $aExcludeMembs) && $aLevel['Active'] != 'no' && $aLevel['Purchasable'] == 'yes') {
			
					$aVars = array(
						'mlevel_id' => $iMembId,
					);
					if ($aLevel['Free'] == '1'){
						$sFormCode = $this->parseHtmlByName('free_form',$aVars);			
					}else{ 
						$sFormCode = $this->parseHtmlByName('choose_membership',$aVars);
						
					}
					$aItems[] = array(
						'explain_url' => BX_DOL_URL_ROOT.'explanation.php?explain=membership&amp;type='.$aLevel['ID'],
						'mlevel_id' => $aLevel['ID'],
						'mlevel_icon' => $this->iconsUrl.$aLevel['Icon'],
						'mlevel_desc' => $aLevel['Description'],		
						'mlevel_name' => $aLevel['Name'],
						
						// Freddy comment 05/11/2015
		//'mlevel_price' => $sPrice = ($aLevel['Free'] == '1') ? 'Free' : $sCurSign.number_format($aPriceInfo['Price'], 2),
		//'mlevel_price' =>  $sPrice = ($aLevel['Free'] == '1') ? _t('_dol_subs_Free') :  $sCurSign.number_format($aPriceInfo['Price']). ' '.'€',
		'mlevel_price' => $sPrice = ($aLevel['Free'] == '1') ? 'Gratuit' : '€'.'<span style=" font-weight:600; font-size:28px;">'.number_format($aPriceInfo['Price']).'</span>'.' '.'/'.' '.'<span style=" font-weight:normal; ">'.'an'.'</span>',
						
						//'mlevel_length' => $iPricingLength . ' ' . $sPriceUnit,
						 'mlevel_length' => '<i class="sys-icon calendar"></i>'. _t('_dol_subs_freddy_duree') .  '  '.'  '.'<strong>' .$iPricingLength . ' ' . $sPriceUnit . '</strong>' /*. ' ' . ' ' . '<span style="font-size:20px">'. '<i class="sys-icon trophy"></i>'.'</span>'*/,
						'form_code' => $sFormCode,
						'bx_if:last' => array(
		                    'condition' => ($aLevel['ID'] == $aLastItem['ID']),
		                    'content' => array(
		                        'last_tr' => 'last_tr'
		                    ),
		                ),
						
						
					);				
				}
			}
		}
		$aVars = array(
			'bx_repeat:memberships' => $aItems,
		);
		return $this->parseHtmlByName('avail_memberships',$aVars);	
	}
	
	//--- Main Admin Methods ---//
    
	/**
	 * Payment processors block
	 *
	 */
	function paymentProcessorSettings(){
		
		// Setup Processor form class
		$this->oProcForm = new MbMmpProcessorForm;
		$sOutput = '<div class="bx-def-bc-margin">';
		
		$sOutput.= $this->oProcForm->getForm();
		
		$callback = $this->sBase.'callback';
				
		$sOutput.= 	<<<HAR
		<table class="custom" >
		<td class="custom_caption">Data return URL:</td>
		<td class="custom_value" colspan="5"><div id="callback_url">{$callback}</div></td>	
	</table>
HAR;
		
		$sOutput.= '</div>';
		
		return DesignBoxAdmin('Payment Processors', $sOutput);	
    }
	
	/**
	 * Data forwarding block
	 *
	 */
	function dataForwardSettings(){
        if(bx_get('save_data_forward')){
			$sCode = msgBox('Settings Successfully Saved',2);
			setParam('dol_subs_data_forwarding', serialize($_POST));
		}

		$aForm = $this->dataForwardForm();

		$aVars = array(
			'data_forward_form' => $aForm,
			'callback_url' => $this->sBase.'callback',
			'message' => $sCode
		);    		
    	return DesignBoxAdmin('Data Forwarding', $this->parseHtmlByName('admin_data_forward',$aVars));		

	}
	
	/**
	 * Module config block
	 *
	 */
    function userManagementSettings(){
       	if(bx_get('save_user_settings')){
			$sMsg = MsgBox('Settings Successfully Saved',2);
			setParam('dol_subs_config', serialize($_POST));
			
		}
		$aInputs = $this->getUserManagementInputs();
      	$aForm = array(
            'form_attrs' => array(
                'name'     => 'user_settings', 
                'method'   => 'post',
                'action'   => NULL,
            ),
			'inputs' => $aInputs,       
		);
	
        $oForm = new BxTemplFormView ($aForm);
        $oForm->initChecker();
		$sCode = $oForm->getCode();


		$aVars = array(
			'user_settings_form' => $sCode,
			'message' => $sMsg
		);    		
    	return DesignBoxAdmin('User Management', $this->parseHtmlByName('admin_user_settings',$aVars));	
    }
	
	/**
	 * Manage Memberships
	 *
	 */
	function adminCurrentMemberships(){
		if($_GET['adm_mlevels_activate'] && $_GET['membership']){
			$this->_oDb->activateMembershipLevel($_GET['membership']);
			Redirect($this->sBase.'memberships/');
		}
		if($_GET['adm_mlevels_deactivate'] && $_GET['membership']){
			$this->_oDb->deactivateMembershipLevel($_GET['membership']);
			Redirect($this->sBase.'memberships/');
		}
		if($_GET['adm_mlevels_delete'] && $_GET['membership']){
			if(in_array($_GET['membership'],array(1,2,3))){
				$sAlert = "alert('Cannot delete Membership Level');";
			}else{
				$this->_oDb->deleteMembershipLevel($_GET['membership']);
				Redirect($this->sBase.'memberships/');
			}
		}
		$aLevel = $this->_oDb->getMembershipById($_GET['membership']);

		if($_GET['adm_mlevels_edit']){
			
			$back ='<a type="submit" href="'.$this->sBase.'memberships" class="form_input_submit AbonnementPremium" >' . _t('_Back to Memberships') . '</a><br/><br/>';		
			$sEditBoxes = $back;
			
			if($_GET['membership'] != '2'){
	    		$sEditBoxes.= DesignBoxAdmin('Settings for "'.$aLevel['Name'].'" Membership', $this->editMembership($_GET['membership']));
			}
	    	$sEditBoxes.= DesignBoxAdmin('Actions for "'.$aLevel['Name'].'" Membership', $this->editMembershipActions($_GET['membership']));
			
			$sEditBoxes.= '<br/>'.$back;
			
			return $sEditBoxes;
		}

	    $aItems = array();
		$i=0;
        $aColors = array('light', 'dark');
		$aExcludeMembs 	= array(1,3);
		$aLevels = $this->_oDb->getMembershipsAdmin();
		$sCurSign = getParam('currency_sign');
	    foreach($aLevels as $aLevel){
			$aData = $this->_oDb->getMembershipPriceInfo($aLevel['ID']);
			if (!in_array($aLevel['ID'], $aExcludeMembs)){
		        $aItems[] = array(
		            'ID' 			=> $aLevel['ID'],
		            'Name' 			=> $aLevel['Name'],
		            'Icon' 			=> $this->iconsUrl . $aLevel['Icon'],
		            'Description' 	=> $aLevel['Description'],
		            'Active' 		=> $sActive = ($aLevel['Active'] == 'yes') ? 'Active' : 'Not-Active',
		            'Free' 			=> $aLevel['Free'],
					'tr_class'		=> $aColors[$i++ % 2],
					'Price'			=> $sPrice = (!$aData['Price']) ? 'Free' : $sCurSign.number_format($aData['Price'], 2),
					'Length'		=> $aData['Length'].' '.$aData['Unit'],
					'Trial'			=> $sTrial = ($aLevel['Trial']=='1') ? 'Yes' : 'No',
					'Auto'			=> $sAuto = ($aData['Auto'] == '1') ? 'Auto' : 'One-Time',
					'Display'		=> $sRemovable = ($aLevel['Removable'] == 'yes') ? 'block' : 'none' 
		        );
			}
   		}
	    $aButtons = array(
	        'adm_mlevels_edit' 			=> 'Edit Membership/Actions',
	        'adm_mlevels_activate' 		=> 'Activate',
	        'adm_mlevels_deactivate' 	=> 'Deactivate',
	        'adm_mlevels_delete' 		=> 'Delete'
	    );
		$sControls = BxTemplSearchResult::showAdminActionsPanel('mem_levels', $aButtons, 'membership', false,false); 

		$aVars = array(
			'bx_repeat:items' => $aItems,
			'controls' => $sControls,
			'alert' => $sAlert
	    );
	    $sResult = $this->parseHtmlByName('admin_mem_setup', $aVars);	 
		   
	    return DesignBoxAdmin('Membership Levels', $sResult);
	}
	
	/**
	 * Create membership level
	 *
	 */
	function adminCreateMembership(){	
	    $aForm = array(
	        'form_attrs' => array(
	            'id' => 'create_mlevel',
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
			'inputs' => $this->getCreateMembershipInputs(),
	    );
	    $oForm = new BxTemplFormView($aForm);
	    $oForm->initChecker();	
	    $bFile = true;
	    $sFilePath = $this->_oConfig->getIconsPath();
	    $sFileName = time();
	    $sFileExt = '';    

	    if($oForm->isSubmittedAndValid()) {
			if($this->isImage($_FILES['Icon']['type'], $sFileExt) && !empty($_FILES['Icon']['tmp_name'])){
				move_uploaded_file($_FILES['Icon']['tmp_name'],  $sFilePath . $sFileName . '.' . $sFileExt);
			}else if(!$this->isImage($_FILES['Icon']['type'], $sFileExt) && !empty($_FILES['Icon']['tmp_name'])){
	    		$oForm->aInputs['Icon']['error'] = $oForm->aInputs['Icon']['checker']['error'];
			}
			$sPath = $sFilePath . $sFileName . '.' . $sFileExt;
		    imageResize($sPath, $sPath, 110, 110);
			$sIcon = $sFileName . '.' . $sFileExt;
			$this->_oDb->createMembershipLevel($_POST,$sPath); 
		   	Redirect($this->sBase.'memberships/');  
	    } else {
			$sCode = $oForm->getCode(); 		
	        return DesignBoxAdmin('Create Membership Level', '<div class="bx-def-bc-margin">'.$sCode.'</div>');
	    }


	}
	
	/**
	 * Admin Membership Options
	 *
	 */
	function adminMembershipOptions(){
				
		// Setup Save Options
		$sMsg = '';
		if(bx_get('savemoptions')){
			$sMsg = MsgBox('Successfully Saved',3);
			setParam('dol_subs_membership_options', serialize($_POST));
		}		
		$aMembershipOptions = unserialize(getParam('dol_subs_membership_options'));

		// Setup Main form data
		$aForm = array(
	        'form_attrs' => array(
	            'id' => 'ml_options',
	            'action' => NULL,
	            'method' => 'post',
	        ),		
	    );
		
		// Build array for each membership level
		$aMemberships = getMemberships();
		foreach($aMemberships as $id=>$name){
			if(in_array($id, array(1,3)))
				unset($aMemberships[$id]); // unset non-member and promotion
		}
		
		// Loop thru memberships to create form inputs
		foreach ($aMemberships as $id =>$name) {			
			$aForm['inputs']['header'.$id] = array(
				'type' => 'block_header',
				'caption' => 'Available to <strong>' . $name . '</strong> Members',
				'collapsable' => true,
				'collapsed' => false
			);
	        $aForm['inputs']['section'.$id] = array(
				'type' => 'checkbox_set',
				'name' => 'purchasable_memberships'.$id,
				'values' => getMemberships(true),
				// Account for initial parameters to be empty
				'value' => ($aMembershipOptions['purchasable_memberships'.$id]) ? $aMembershipOptions['purchasable_memberships'.$id] : array_keys($aMemberships),
				'info' => 'Check memberships to make available',
			);
			$aForm['inputs']['header'.$id.'_end'] = array(
                'type' => 'block_end'
            );
		}
		
		// Submit Button
		$aForm['inputs']['submit'] = array(
			'type' => 'submit',
			'name' => 'savemoptions',
			'value' => 'Save',
	    );

		// Setup form output
		$oForm = new BxTemplFormView($aForm);
	    $oForm->initChecker();		

		$aResult = array(
			'output' => $oForm->getCode(),
			'message' => $sMsg
		);

		return DesignBoxAdmin('Upgrade/Downgrade Options', $this->parseHtmlByName('admin_membership_options', $aResult));
	}
	
	
	/**
	 * Subscribers Page
	 *
	 */
	function subscribersMainCode(){

		if($_POST['adm_subs_del'] && is_array($_POST['subscribers'])){
			$this->_oDb->removeSubscribers($_POST['subscribers']);
			$sMsg = MsgBox('Successfully Removed',2);			
		}
	
		$aSubs = $this->_oDb->getSubscriptions();// Input optional filter arg to retrieve subs
		$sMsg = (count($aSubs) > '0') ?  '' : MsgBox('No Active Subscriptions');


		if($_POST['adm_subs_del'] && !$_POST['subscribers'])
			$sMsg = MsgBox('Nothing Selected');

		$iStart = ($_GET['start']) ? $_GET['start'] : 0;
		$iPerPage = ($_GET['per_page']) ? $_GET['per_page'] : 10;
		$sDateFormat = getLocaleFormat(BX_DOL_LOCALE_DATE, BX_DOL_LOCALE_DB);
		$iCount = count($aSubs);
		$iCounter = '0';
		$aRange = range($iStart,$iStart+($iPerPage-1));
		foreach($aSubs as $aSub){			

			if(!in_array($iCounter++,$aRange)) continue;	

			$sDateStartFixed = $this->getTimeStamp($aSub['DateStarts']);
			$sDateExpFixed = $this->getTimeStamp($aSub['DateExpires']);
			$sEditLink = BX_DOL_URL_ROOT.'pedit.php?ID='.$aSub['ID'];			

		   	$aItems[] = array(
		      	'id' 			=> $aSub['ID'],
		        'nickname' 		=> $aSub['NickName'],
		        'email' 		=> $this->trimString($aSub['Email'],20),
		        'status' 		=> $aSub['Status'],
		        'date_start' 	=> $sDateStart = $sDateStartFixed ? date('m-d-Y',$sDateStartFixed) : 'N/A',
		        'date_exp' 		=> $sDateExp = $sDateExpFixed ? date('m-d-Y',$sDateExpFixed) : 'N/A',
		        'txn_id' 		=> $sSubId = (empty($aSub['TransactionID']) || $aSub['TransactionID'] == 'NULL') ? 'One-Time' : $aSub['TransactionID'],
		        'mem_name' 		=> $sMemName = $this->trimString($aSub['Name'],20),
				'edit_link'     => $sEditLink,
				'action_url'    => $this->sBase.'process_controls',
		    );
		}

	    $oPaginate = new BxDolPaginate(array(
	      	'start' => $iStart,
			'count' => $iCount,
			'per_page' => $iPerPage,
			'sorting'    => 'last',
			'per_page_changer'   => true,
			'page_reloader'      => true,
		    'page_url' => $this->sBase.'subscriptions/?start={start}&per_page={per_page}',
	    ));
	    $sPaginate = $oPaginate->getPaginate();   

		$aButtons = array(
	        'adm_subs_del' 		=> 'Remove Subscription'
	    ); 
		$sControls = BxTemplSearchResult::showAdminActionsPanel('subscribers_form', $aButtons, 'subscribers', true ,false); 

		$aResult = array(
			'bx_repeat:items' => $aItems,
			'paginate' => $sPaginate,
			'controls' => $sControls,
			'message' => $sMsg
		);
	    return DesignBoxAdmin('Subscribed Members', $this->parseHtmlByName('admin_subs', $aResult));
	}

	/**
	 * Get Free Form
	 *
	 */
	function getFreeForm(){
      	$aForm = array(
            'form_attrs' => array(
                'name'     => 'settings', 
                'method'   => 'post',
                'action'   => NULL,
            ),
			'inputs' => $aInputs,       
		);
	
        $oForm = new BxTemplFormView ($aForm);
        $oForm->initChecker();
		$sCode = $oForm->getCode();    		
    	return $sCode;
	}

	/**
	 * Generates data forwarding form
	 *
	 */
	function dataForwardForm(){
		
		// Get form data
		$aFormData = unserialize(getParam('dol_subs_data_forwarding'));
		
      	$aForm = array(
            'form_attrs' => array(
                'name'     => 'data_forward', 
                'method'   => 'post',
                'action'   => NULL,
            ),
			'inputs' => array(
				'block_header' => array(
                    'type' => 'block_header',
                    'caption' => 'Optional Payment Response Data Forwarding',
                    'collapsable' => 'true',
                    'collapsed' => 'true'
				),
				'data_forward_1' => array(
	                'type' => 'text',
	                'name' => 'data_forward_1',
	                'caption' => 'Data Forward URL 1',
	                'value' => $aFormData['data_forward_1'],
	                'info' => 'Example: http://www.domain.com/other/ipn/',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ), 
				'data_forward_2' => array(
	                'type' => 'text',
	                'name' => 'data_forward_2',
	                'caption' => 'Data Forward URL 2',
	                'value' => $aFormData['data_forward_2'],
	                'info' => 'Example: http://www.domain.com/other/ipn/',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ), 

	            'submit' => array(
	                'type' => 'submit',
	                'name' => 'save_data_forward',
	                'value' => 'Save',
	            ),
				'block_end' => array(
                        'type' => 'block_end'
                )
			)   
		);
	
        $oForm = new BxTemplFormView ($aForm);
        $oForm->initChecker();
		$sCode = $oForm->getCode();    		
    	return $sCode;
	}
	
	
	/**
	 * Create Membership Inputs
	 *
	 */
	function getCreateMembershipInputs(){
			$aUnits = array('Days' => 'Days', 'Months' => 'Months');
	        $aInputs =  array(          
	            'Name' => array(
	                'type' => 'text',
	                'name' => 'Name',
	                'caption' => _t('_adm_txt_mlevels_name'),
	                'value' => '',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	                'checker' => array (
						'func' => 'length',
						'params' => array(3,100),
						'error' => _t('_adm_txt_mlevels_name_err'),
					),
	            ),
	            'Icon' => array(
	                'type' => 'file',
	                'name' => 'Icon',
	                'caption' => _t('_dol_subs_adm_upload_image'),
	            	'value' => '',
					'label' => $sIcon,
	                'checker' => array (
						'error' => _t('_adm_txt_mlevels_icon_err'),
					),
	            ),
	            'Description' => array(
	                'type' => 'textarea',
	                'name' => 'Description',
	                'caption' => _t('_adm_txt_mlevels_description'),
	                'value' => '',
					'attrs' => array(
						'id' => 'mlevel_desc',
					),
	                'db' => array (
	                    'pass' => 'XssHtml',
	                ),
	            ),
	            'Purchasable' => array(
	                'type' => 'checkbox',
	                'name' => 'Purchasable',
	                'value' => 'yes',
					'caption' => _t('_dol_subs_adm_mlevel_purchasable'),
					'checked' => $sChecked = ($aLevel['Purchasable'] == 'yes') ? '1' : '0',
					'label' => _t('_dol_subs_adm_mlevel_purchasable_label'),
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
	            'Free' => array(
	                'type' => 'checkbox',
	                'name' => 'Free',
	                'value' => '1',
					'caption' => _t('_dol_subs_adm_mlevel_free'),
					'checked' => $aLevel['Free'],
					'label' => _t('_dol_subs_adm_mlevel_free_label'),
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),	
	            'Price' => array(
	                'type' => 'text',
	                'name' => 'Price',
	                'value' => $aPriceInfo['Price'],
					'caption' => _t('_dol_subs_adm_mlevel_price').' ('.getParam('currency_sign').')',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
                'Unit' => array(
                    'type' => 'select',
                    'name' => 'Unit',
                	'values' => $aUnits,
                    'caption' => 'Membership Interval',
					'info'	=> 'If over 90 days, Choose months',
                ),
	            'Length' => array(
	                'type' => 'text',
	                'name' => 'Length',
	                'value' => '',
					'caption' => 'Interval Length',
					'info'	=> 'The length of the selected interval between payments (0 for Lifetime)',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
	            'Auto' => array(
	                'type' => 'checkbox',
	                'name' => 'Auto',
	                'value' => '1',
					'caption' => _t('_dol_subs_adm_mlevel_auto'),
					'checked' => $aPriceInfo['Auto'],
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
	            'submit' => array(
	                'type' => 'submit',
	                'name' => 'create_mlevel',
	                'value' => 'Create',
	            ),
			);
		return $aInputs;
	}


	/**
	 * Generates module config form
	 *
	 */
	function getUserManagementInputs(){
		
		// Determines memberships
		$aMbs = $this->_oDb->getMemberships();
		foreach($aMbs as $aMemb){
			$ignoreArray = array(1);
			if (!in_array($aMemb['ID'],$ignoreArray)){ 
				$aMemberships[$aMemb['ID']] = $aMemb['Name'];
			}
		}

		// Get form data
		$aFormData = unserialize(getParam('dol_subs_config'));

        $aInputs = array(
                'default_memID' => array(
                    'type' => 'select',
                    'name' => 'default_memID',
                	'values' => $aMemberships,
                    'caption' => _t('_dol_subs_adm_dft_mem'),
                	'value' => $aFormData['default_memID'],
					'info' => 'NOTE: Will overrule Promotional Membership',
                ),
				'require_mem' => array(
                    'type' => 'checkbox',
                    'name' => 'require_mem',
                    'caption' => _t('_dol_subs_adm_require_mem'),
					'checked' =>  ($aFormData['require_mem'] == 'on') ? true : false,
					'info' => 'Force users to choose membership when joining',
                ),
				'redirect_guests' => array(
                    'type' => 'checkbox',
                    'name' => 'redirect_guests',
                    'caption' => _t('_dol_subs_adm_redirect_guests'),
					'checked' =>  ($aFormData['redirect_guests'] == 'on') ? true : false,
					'info' => 'Force guests to join before using site.',
                ),
				'add_button' => array(
					'type' => 'submit',
					'name' => 'save_user_settings',
					'value' => 'Save Settings',
				),

           	);		
    	return $aInputs;
	}

	/**
	 * Displays Edit Membership
	 *
	 */
	function editMembership($iMembId){
		$aUnits = array('Days' => 'Days', 'Months' => 'Months');
		$aLevel = $this->_oDb->getMembershipById($iMembId);
		$aPriceInfo = $this->_oDb->getMembershipPriceInfo($iMembId);
		$sIcon = '<img src="'.$this->iconsUrl.$aLevel['Icon'].'" alt="noimage" title="'.$aLevel['Name'].'" height="40" width="40" />';
	    $aForm = array(
	        'form_attrs' => array(
	            'id' => 'edit_mlevel',
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
	                'submit_name' => 'update_mlevel'
	            ),
	        ),
	        'inputs' => array (  
				'Membership' => array(
					'type' => 'hidden',
					'name' => 'Membership',
					'value' => $iMembId,
					'db' => array (
						'pass' => 'Xss',
					),
				), 
				'settings_header' => array(
                    'type' => 'block_header',
                    'caption' => 'Settings'
				),         
	            'Name' => array(
	                'type' => 'text',
	                'name' => 'Name',
	                'caption' => _t('_adm_txt_mlevels_name'),
	                'value' => $aLevel['Name'],
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	                'checker' => array (
						'func' => 'length',
						'params' => array(3,100),
						'error' => _t('_adm_txt_mlevels_name_err'),
					),
	            ),
	            'Icon' => array(
	                'type' => 'file',
	                'name' => 'Icon',
	                'caption' => _t('_dol_subs_adm_chg_image'),
	            	'value' => '',
					'label' => $sIcon,
	                'checker' => array (
						'error' => _t('_adm_txt_mlevels_icon_err'),
					),
	            ),
	            'Description' => array(
	                'type' => 'textarea',
	                'name' => 'Description',
	                'caption' => _t('_adm_txt_mlevels_description'),
	                'value' => $aLevel['Description'],
					'attrs' => array(
						'id' => 'mlevel_desc',
					),
	                'db' => array (
	                    'pass' => 'XssHtml',
	                ),
	            ),

	            'Purchasable' => array(
	                'type' => 'checkbox',
	                'name' => 'Purchasable',
	                'value' => 'yes',
					'caption' => _t('_dol_subs_adm_mlevel_purchasable'),
					'checked' => $sChecked = ($aLevel['Purchasable'] == 'yes') ? '1' : '0',
					'label' => _t('_dol_subs_adm_mlevel_purchasable_label'),
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
	            'Free' => array(
	                'type' => 'checkbox',
	                'name' => 'Free',
	                'value' => '1',
					'caption' => _t('_dol_subs_adm_mlevel_free'),
					'checked' => $aLevel['Free'],
					'label' => _t('_dol_subs_adm_mlevel_free_label'),
					'info' => 'Price settings will not apply',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
	            'Order' => array(
	                'type' => 'text',
	                'name' => 'Order',
	                'caption' => _t('_dol_subs_adm_txt_mlevels_order'),
	                'value' => $aLevel['Order'],
	                'info' => 'In Ascending Order (0,1,2,3)',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
				'settings_end' => array(
                        'type' => 'block_end'
                ),	
				'pricing_header' => array(
                    'type' => 'block_header',
                    'caption' => 'Pricing Settings'
				),

	            'Price' => array(
	                'type' => 'text',
	                'name' => 'Price',
	                'value' => $aPriceInfo['Price'],
					'caption' => 'Recurring Price ('.getParam('currency_sign').')',
	                'info' => 'The price of every recurrance',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
				
				/*
	            'FirstPaymentPrice' => array(
	                'type' => 'text',
	                'name' => 'FirstPaymentPrice',
	                'value' => $aPriceInfo['FirstPaymentPrice'],
					'caption' => 'First Payment ('.getParam('currency_sign').')',
	                'info' => 'The price of the first payment',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),*/
                'Unit' => array(
                    'type' => 'select',
                    'name' => 'Unit',
                	'values' => $aUnits,
                    'caption' => 'Membership Interval',
					'value'	=> $aPriceInfo['Unit'],
					'info'	=> 'If over 90 days, Choose months',
                ),
	            'Length' => array(
	                'type' => 'text',
	                'name' => 'Length',
	                'value' => $aPriceInfo['Length'],
					'caption' => 'Interval Length',
					'info'	=> 'The length of the selected interval between payments',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
	            'Auto' => array(
	                'type' => 'checkbox',
	                'name' => 'Auto',
	                'value' => '1',
					'caption' => _t('_dol_subs_adm_mlevel_auto'),
					'checked' => $aPriceInfo['Auto'],
					'info' => 'Enables subscription creation. Disable for non-recurring memberships',
	                'db' => array (
	                    'pass' => 'Xss',
	                ),
	            ),
				/*
                'Trial' => array(
                    'type' => 'checkbox',
                    'name' => 'Trial', 
					'value' => '1',
                    'caption' => _t('_dol_subs_adm_trial'),
					'checked' => $aLevel['Trial'],
					'label' => _t('_dol_subs_adm_trial_label'), 
					'info' => 'Subscription will be created and will be free for the lenth of the trial.  Requires Auto-Recurring to be Enabled',                   
                ),
				'Trial_Length' => array(
                    'type' => 'text',
                    'name' => 'Trial_Length', 
                    'caption' => _t('_dol_subs_adm_trial_length'),
					'value' => $aLevel['Trial_Length'],
                ),*/
				'prcing_end' => array(
                        'type' => 'block_end'
                ),
	            'submit' => array(
	                'type' => 'submit',
	                'name' => 'update_mlevel',
	                'value' => 'Update',
	            ),                
	        )
	    );
		
		if($this->_oDb->getSetting('payment_proc') == 'authorize'){
			unset($aForm['inputs']['Trial_Length']);
			$aForm['inputs']['Trial']['info'] = 'First membership period will be a trial';
		}
	    $oForm = new BxTemplFormView($aForm);
	    $oForm->initChecker();
	
	    $bFile = true;
	    $sFilePath = $this->_oConfig->getIconsPath();
	    $sFileName = time();
	    $sFileExt = '';    

	    if($oForm->isSubmittedAndValid()) {
			if($this->isImage($_FILES['Icon']['type'], $sFileExt) && !empty($_FILES['Icon']['tmp_name'])){
				move_uploaded_file($_FILES['Icon']['tmp_name'],  $sFilePath . $sFileName . '.' . $sFileExt);
			}else if(!$this->isImage($_FILES['Icon']['type'], $sFileExt) && !empty($_FILES['Icon']['tmp_name'])){
	    		$oForm->aInputs['Icon']['error'] = $oForm->aInputs['Icon']['checker']['error'];
			}

			$sPath = $sFilePath . $sFileName . '.' . $sFileExt;
		    imageResize($sPath, $sPath, 110, 110);
			$sIcon = $sFileName . '.' . $sFileExt;
			$this->_oDb->updateMembershipInfo($_POST,$sPath); 

			// $_GET['membership'] == '2' && ($_GET['adm_mlevels_edit']
		   	Redirect($this->sBase.'memberships/',array('membership' => $iMembId, 'adm_mlevels_edit' => 'saved_edit'), get);
  
	    } else {
			$sCode = $oForm->getCode();
			if($_GET['adm_mlevels_edit'] == 'saved_edit')
				$sCode.= MsgBox('Successfully Updated Membership',3); 

			if($_GET['adm_mlevels_edit'] == 'saved_actions')
				$sCode.= MsgBox('Actions Successfully Updated',3);
 		
	        return '<div class="bx-def-bc-margin">' . $sCode . '</div>';
	    }
	}
	
	/**
	 * Is image function
	 *
	 */
	function isImage($sMimeType, &$sFileExtension) {
		$bResult = true;
		switch($sMimeType) {
			case 'image/jpeg':
			case 'image/pjpeg':
				$sFileExtension = 'jpg';
				break;
			case 'image/png':
			case 'image/x-png':
				$sFileExtension = 'png';
				break;
			case 'image/gif':
				$sFileExtension = 'gif';
				break;
			default:
				$bResult = false;
		}
		return $bResult;
	}
	
	/**
	 * Edit Membership Actions
	 *
	 */
	function editMembershipActions($iMembId){

	    $aItems = array();	
	    $aActions = $this->_oDb->getActions();
	    $aActionsActive = $this->_oDb->getActiveActions($iMembId);


	    foreach($aActions as $aAction) {
	        $bEnabled = array_key_exists($aAction['id'], $aActionsActive);

			if($bEnabled){
				$iNumAllowed = $aActionsActive[$aAction['id']]['AllowedCount'];
				$iResetHours = $aActionsActive[$aAction['id']]['AllowedPeriodLen'];
			}	
	        $aItems[] = array(
	            'action_name' => ucwords($aAction['title']),
	            'action_id' => $aAction['id'],
	            'title' => $aAction['title'],
	            'checked' => $bEnabled ? 'checked="checked"' : '',
                'level_id' => $iMembId,
				'num_allowed' => $bEnabled ? $iNumAllowed : '',
				'reset_hours' => $bEnabled ? $iResetHours : '',
	        );
	    }   	
		$aButtons = array(
	        'adm_mlevels_update' 		=> 'Update Actions',
	        'adm_mlevels_reset' 		=> 'Reset'
	    ); 
		$sControls = BxTemplSearchResult::showAdminActionsPanel('mem_actions', $aButtons, 'actions', true ,false);
	
		$aVars = array(
	        'id' => $iMembId,
	        'bx_repeat:items' => $aItems,
	        'url_admin' => $GLOBALS['site']['url_admin'],
			'controls' => $sControls
	    );

	    $sResult = $this->parseHtmlByName('admin_mem_actions', $aVars);
	    
		if($_POST['adm_mlevels_update'] == 'Update Actions'){
			$this->_oDb->updateMembershipActions($_POST,$iMembId);
			Redirect($this->sBase.'memberships/',array('membership' => $iMembId, 'adm_mlevels_edit' => 'saved_actions'), get);
		}	
	    return $sResult;
	}
	
	/**
	 * Timestamp
	 *
	 */
	function getTimeStamp($sDateTime){
		if($sDateTime){
			list($sDate, $sTime) = explode(' ', $sDateTime);
			list($iYear, $iMonth, $iDay) = explode('-', $sDate);
			list($iHour, $iMin, $iSec) = explode(':', $sTime);	
			$sResult = mktime($iHour, $iMin, $iSec, $iMonth, $iDay, $iYear);	
		}		
		return $sResult;
	}
	function trimString($sString,$iChars){
		if(mb_strlen($sString) > $iChars){
			mb_substr($sString, 0, $iChars) . '...';
		}
		return $sString;
	}
	
	/**
	 * Custom Access Denied
	 *
	 */
    function customDisplayAccessDenied () {
        $sTitle = _t('_Access denied');	
 		if($_COOKIE['memberID']){
			$sRedirect = $this->sBase;
			$sText1 = _t('_dol_subs_denied_upgrade1');
			$sText2 = _t('_dol_subs_denied_upgrade2');
		}else{
			$sRedirect = BX_DOL_URL_ROOT.'join.php';
			$sText1 = _t('_dol_subs_denied_join1');
			$sText2 = _t('_dol_subs_denied_join2');
		}

        $GLOBALS['_page'] = array(
            'name_index' => 0,
            'header' => $sTitle,
            'header_text' => $sTitle
        );
		
		$aVars = array(
			'message' => MsgBox($sTitle),
			'redirect_url' => $sRedirect,
			'text_link'	=> $sText1,
			'text_after' => $sText2,
		);
		
        $GLOBALS['_page_cont'][0]['page_main_code'] = $this->parseHtmlByName('access_denied',$aVars);
       	$this->addCss('dolphin_subs.css');
		
        PageCode();
        exit;
    }


}