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
bx_import('BxDolParams');
bx_import('BxDolAdminSettings');
bx_import('BxDolTwigTemplate');

class AqbCreditsTemplate extends BxDolTwigTemplate {
   
	/**
	 * Constructor
	 */
	
	function AqbCreditsTemplate(&$oConfig, &$oDb) {
	    parent::BxDolTwigTemplate($oConfig, $oDb);
		$this -> _oConfig -> init($oDb);
	}
	
	function pageCodeAdmin ($sTitle)
    {
        global $_page;
        global $_page_cont;

        $_page['name_index'] = 9;

        $_page['header'] = $sTitle ? $sTitle : $GLOBALS['site']['title'];
        $_page['header_text'] = $sTitle;

        $_page_cont[$_page['name_index']]['page_main_code'] = $this->pageEnd();

        PageCodeAdmin();
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
	
	function getActionsPanel(){
	  if ($_POST['save'] && $this -> _oDb -> saveActions($_POST)) 
		$sSavedMessage = MsgBox(_t('_aqb_credits_successfully_saved'), 3);
	
	  $aSettings = $this -> _oDb -> getActions();
 
	  $aForm = array(
        'form_attrs' => array(
            'id' => 'settings-form',
            'action' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'administration/',
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));
	
		$iID = $this -> _oDb -> getSettingsCategory();
		$aForm['inputs'] = array();
		
		$aForm['inputs']['info'] = array(
										  'type' => 'custom',
										  'content' => '<center><font style="color:green;font-size:18px;">' . _t('_aqb_credits_info') . "</font></center>", 
										  'colspan' => true,
										  'collapsed' => false
										);
				
		
		$aActionsList = $this -> _oDb -> getMembershipActions();
		
		$bIsPointsSystemInstalled = $this -> _oDb -> isPointsSystemInstalled();
			
		$aItems = array();
		foreach($aActionsList as $iKey => $sValue){
				$aItems[] = array(
									'name' => $sValue,
									'id' => $iKey,
									'checked' => $aSettings[$iKey]['active'] ? 'checked="checked"' : '',
									'credits' => (int)$aSettings[$iKey]['credits'],	
									'active' => $aSettings[$iKey]['active'] ? 'active' : ''
								  );
			}
			
		
		$sTable = $this -> parseHtmlByName('mem_levels.html', array('bx_repeat:actions' => $aItems));
				
		$aForm['inputs'] = array_merge($aForm['inputs'], array(
            'items' => array(
					'type' => 'custom',
					'content' => $sTable,
					'colspan' => true,
					'collapsed' => false,
			),
			'cat' => array(
                'type' => 'hidden',
                'name' => 'cat',
                'value' => $iID
            ),
			
			'panel' => array(
				'type' => 'hidden',
				'name' => 'panel',
				'value' => $sPanel
			),
            'save' => array(
                'type' => 'submit',
                'name' => 'save',
                'value' => _t("_adm_btn_settings_save"),
            )
        ));
    		
		$oForm = new BxTemplFormView($aForm);
		return $sSavedMessage . $oForm -> getCode(); 
	}
	
	function getSettingsPanel() {
        $iId = $this -> _oDb -> getSettingsCategory();

        if(empty($iId))
           return MsgBox(_t('_aqb_credits_nothing_found'));

        bx_import('BxDolAdminSettings');

        $mixedResult = '';

        if(isset($_POST['save']) && isset($_POST['cat'])) {
            $oSettings = new BxDolAdminSettings($iId);
            $mixedResult = $oSettings -> saveChanges($_POST);
			$oSettings -> _onSavePermalinks();
        }
        
        $oSettings = new BxDolAdminSettings($iId);
        $sResult = $oSettings->getForm();
                   
			
        if($mixedResult !== true && !empty($mixedResult)) $sResult = $mixedResult . $sResult;
        return $sResult;
    }
	
	function adminBlock ($sContent, $sTitle, $aMenu){
        return DesignBoxAdmin($sTitle, $sContent, $aMenu, '', 11);
    }

	
	function getCreditsInfo($aParpoints){
		if (empty($aParpoints['view_type'])) $aParpoints['view_type'] = 'DESC';
		if(!isset($aParpoints['view_start']) || empty($aParpoints['view_start'])) $aParpoints['view_start'] = 0;
	    if(!isset($aParpoints['view_per_page']) || empty($aParpoints['view_per_page'])) $aParpoints['view_per_page'] = $this -> _oConfig -> getPerPageOnHistory();
	    if(!isset($aParpoints['view_order']) || empty($aParpoints['view_order'])) $aParpoints['view_order'] = 'time';

		
		$aTree = $this -> _oDb -> getCreditsTable($aParpoints);


		if (empty($aTree)) return MsgBox(_t('_aqb_credits_nothing_found'));

		$aItems = array();
		foreach($aTree as $iKey => $aVal){
			
			switch($aVal['action']){
			   case 'spent': $sAction = _t("_aqb_credtis_action_{$aVal['action']}", $this -> _oDb -> getMembershipActionName($aVal['action_id']) . '&nbsp;' . _t('_aqb_credtis_action_lifetime', ($this -> _oConfig -> lifetimePeriod() ? $this -> _oConfig -> lifetimePeriod() . '&nbsp;' . _t('_aqb_credtis_action_lifetime_days') : _t('_aqb_credtis_action_lifetime_unlimited')))); break;
			   case 'bought': $sAction = _t("_aqb_credtis_action_{$aVal['action']}"); break;
			   case 'exchanged': 
			   case 'got': 
			   case 'present': 
			   case 'exchange':
						default: $sAction = _t("_aqb_credtis_action_{$aVal['action']}", _t($aVal['param']));	
			}
			
			$aItems[] = array(
								'action_name' => $sAction,
								'date' => getLocaleDate($aVal['time']),
								'number' => $aVal['number'],
								'checkbox' => $bAdmin ? "<input type=\"checkbox\" class=\"form_input_checkbox\" id=\"{$aVal['member']}\" name=\"credits[]\" value=\"{$aVal['member']}\" />" : ''
							  );
		}
				
		//--- Get Paginate ---//
		    $oPaginate = new BxDolPaginate(array(
		        'start' => $aParpoints['view_start'],
		        'count' => (int)$aParpoints['count'],
		        'per_page' => $aParpoints['view_per_page'],
		        'page_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . '/credits_panel/?start={start}',
		        'on_change_page' =>'AqbMain.changePage({start})'
		    ));
		    
		$sPaginate = $oPaginate -> getPaginate(); 
			
		
		$sContent = $this -> parseHtmlByName('table.html', 
		array(
				'paginate' => $sPaginate,
				'bx_repeat:items' => $aItems,

				'item_action_function' => $this -> getOrderRef('action_id', $aParpoints),
				'action_title' => $this -> getArrowImage('action_id', $aParpoints),

				'item_date_function' => $this -> getOrderRef('time', $aParpoints),
				'date' => $this -> getArrowImage('time', $aParpoints),
				
				'item_number_function' => $this -> getOrderRef('number', $aParpoints),
				'number' => $this -> getArrowImage('number', $aParpoints)		
			));
			 
		return $sContent;		
	}
	
	function getOrderRef($sFName, &$aParpoints){
		return 'onclick="AqbMain.orderByField(\'' . (($aParpoints['view_order'] == $sFName && 'asc' == strtolower($aParpoints['view_type'])) ? 'desc' : 
			   ($aParpoints['view_order'] == $sFName && 'desc' == strtolower($aParpoints['view_type']) ? 'asc' : '')) . '\',\''.$sFName.'\')"';
    }
	
	function getArrowImage($sFName, &$aParpoints){
	    if ($aParpoints['view_order'] == $sFName && 'asc' == strtolower($aParpoints['view_type'])) return '<i class="sys-icon arrow-circle-o-up" />'; 
		if ($aParpoints['view_order'] == $sFName) return '<i class="sys-icon arrow-circle-o-down" />';
		
		return '';			
	}
	
	function ViewCreditsTable($iMember){
		return $this -> parseHtmlByName('credits_table.html', 
		array(
					'credits' => $this -> getCreditsInfo(array('member_id' => $iMember)),					
					'actions_url' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(),
					'sel_view' => '',
					'per_page' => $this -> _oConfig -> getPerPageOnHistory(),
					'order_by' => '',
					'member_id' => $iMember,
					'loading' => LoadingBox('div-loading')				
			));
	}
	
	function ViewActionWithCreditsTable($iMember){
		$aList = $this -> _oDb -> getAvailableActionsForCredits($iMember);
		if(empty($aList)) return MsgBox(_t('_aqb_credits_all_available'));
		
		$aItmes = array();
		$bShowButton = true;
		foreach($aList as $iKey => $iValue){
			$bActive = $this -> _oDb -> isActionAvailable($iMember, $iKey);			
		
			$bShowButton &= $bActive;			
			$aItems[] = array(
								'name' => $this -> _oDb -> getMembershipActionName($iKey),
								'price' => $iValue, 
								'active' => $bActive ? '<i class="sys-icon check-square-o" />' : '<input type="checkbox" name="actions[]" value="'. $iKey .'"/>',
							);
		}

		if (!$bShowButton){
			$oForm = new BxTemplFormView(array());		
			$aCreditsButton = array('type' => 'button',  
									'name' => 'buy_action', 'value' => _t('_aqb_credits_buy_action'), 
									'attrs' => array('onclick' =>'javascript:AqbCredit.showExchangeCreditsForm(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri(). '\');')									
									);
			$sShowButton = $this -> genWrapperInput($aCreditsButton, $oForm -> genInput($aCreditsButton));		 
		}	
		
		return $this -> parseHtmlByName('actions.html', 
		array(
					'bx_repeat:actions' => $aItems,
					'button' => $sShowButton
			));
	}
	
	 
	function buyCreditsBlock($sType = 'price'){
		$fPrice = $this -> _oConfig -> priceInPoints();
		$sCurCode = $this -> _oConfig -> getCurrencySign();
        	
		$aForm = array(
     	'form_attrs' => array(
								'name' => 'credits-form',
					            'method' => 'post',
							    'enctype' => 'multipart/form-data',
								'action' => '',
					    		'onsubmit' => "javascript: AqbCredit.onSubmitCredits('" . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "buy_credits/', '{$sType}'); return false;"
								),
		'inputs' => array(							
			'amount' =>	array(
	                'type' => 'text',
	                'name' => 'amount',				
					'caption' =>  _t('_aqb_credits_num_credits')
	    		),
			'price' =>	array(
	                'type' => 'hidden',
	                'name' => 'price',				
					'value' => $fPrice
	    		),	
	        'buy' => array(
                    'type' => 'submit',
                    'name' => 'buy_button',
                    'value' => $sType == 'price' ? _t('_aqb_credits_buy_button_title') : _t('_aqb_credits_exchange_button_title'),
        			'attrs' => array('id' => 'aqb_buy_button')
			)
		));
    
	$oForm = new BxTemplFormView($aForm);
	
	return PopupBox('aqb_popup', $sType == 'price' ? _t('_aqb_credits_buy_button_title') : _t('_aqb_credits_exchange_button_title') , '<div class="aqb-credits-block">' . $oForm -> getCode() . '</div>');
   }
   
   function presentCreditsBlock($iProfileID){
		$aForm = array(
     	'form_attrs' => array(
								'name' => 'credits-form',
					            'method' => 'post',
							    'enctype' => 'multipart/form-data',
								'action' => ''
								),
		'inputs' => array(							
			'amount' =>	array(
	                'type' => 'text',
	                'name' => 'amount',				
					'caption' =>  _t('_aqb_credits_num_credits')
	    		),
			'price' =>	array(
	                'type' => 'hidden',
	                'name' => 'price',				
					'value' => $fPrice
	    		),	
	        'present' => array(
                    'type' => 'button',
                    'name' => 'present_button',
                    'value' => _t('_aqb_credits_present_button_title'),
        			'attrs' => array('id' => 'aqb_present_button', 'onclick' => "javascript:AqbCredit.onPresentCredits({$iProfileID});")
			)
		));
    
	$oForm = new BxTemplFormView($aForm);
	
	return PopupBox('sys_popup_ajax', _t('_aqb_credits_present_button_title') , '<div class="aqb-credits-block">' . $oForm -> getCode() . '</div>');
   }
   
   function getPopupNotificationWindow($iMember){
		$aList = $this -> _oDb -> getAvailableActionsForCredits($iMember);
		
		if(empty($aList)) return MsqBox(_t('_aqb_credits_all_available'));
		
		$aItems = array();		
		foreach($aList as $iKey => $iValue){
			if (!$this -> _oDb -> isActionAvailable($iMember, $iKey))
			$aItems[] = array(
								'name' => $this -> _oDb -> getMembershipActionName($iKey),
								'price' => $iValue, 
							);
		}
		
		if (empty($aItems)) return '';

		$oForm = new BxTemplFormView(array());				
		$sCheckBox = '';
		if ($this -> _oConfig -> allowToBlockPopUp()){
			$aCheckBox = array(
								'type' => 'checkbox',  
								'name' => 'dont_show', 
								'label' => _t('_aqb_credits_dont_show_it_again'), 
								'attrs' => array('onclick' =>'javascript:AqbCredit.dontShow(this);')									
							  );		
			$sCheckBox = $this -> genWrapperInput($aCheckBox, $oForm -> genInput($aCheckBox));
		}

		
		return $this -> parseHtmlByName('popup_window.html', 
		array(
					'bx_repeat:actions' => $aItems,
					'checkbox' => $sCheckBox
			));
		
   }
   
   function getPoupWindow(){
		$this -> addJs ('main.js');
        $this -> addCss ('main.css');   
		$sUrl = BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . "get_popup_window/";
	
		$sHTML=<<<EOF
		<script language="javascript" type="text/javascript">
			$(document).ready(function(){
				setTimeout(function(){
					AqbCredit.showPopup('{$sUrl}');
				});	
			});
		</script>	
EOF;
	
	return $sHTML;   
   }
	
}
?>