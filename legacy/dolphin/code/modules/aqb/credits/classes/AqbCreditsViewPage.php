<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Community Builder
*
* Dolphin is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the
* License, or  any later version.
*
* Dolphin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
* You should have received a copy of the GNU General Public License along with Dolphin,
* see license.txt file; if not, write to marketing@boonex.com
***************************************************************************/

bx_import('BxDolPageView');
bx_import('BxTemplFormView');

class AqbCreditsViewPage extends BxDolPageView {

    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;
	var $_fPrice;
	var $_sSign;
	var $_iProfileId;

	function AqbCreditsViewPage(&$oMain) {
		parent::BxDolPageView('aqb_credits_sys');
		$this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
		$this->_sType = $sType;
		$this->_sSign = $this -> _oConfig -> getCurrencySign();
		$this -> _iProfileId = getLoggedId();
	}

	function getBlockCode_Info() {
        
		$aItems[]['items'] = _t('_aqb_credits_balance') . '&nbsp;:&nbsp;<b>' . $this -> _oDb -> getTotalCredits($this -> _iProfileId) . '</b>';
		
		$oForm = new BxTemplFormView(array());
		
		if ($this -> _oDb -> isPointsSystemInstalled()){
			$iPointsBalance = BxDolService::call('aqb_points', 'get_profile_points_num', array($this->_iProfileId)); 
			$aItems[]['items'] = _t('_aqb_wallet_points_balance') . '&nbsp;:&nbsp;<b>' . '<a href="m/aqb_points/history" title="' . _t('_aqb_credits_view_history_page') . '">' .  $iPointsBalance . '</a></b>';
			$aItems[]['items'] = _t('_aqb_credits_price_credit') . '&nbsp;:&nbsp;<b>' .  $this -> _oConfig -> priceInPoints() . '</b>';
			
			if ($this -> _oDb -> isPointsSystemInstalled() && $this -> _oConfig -> allowToExchangeCredits()){
				$aCreditsExchangeButton = array('type' => 'button',  'name' => 'exchange_button', 'value' => _t('_aqb_credits_exchange_credits'), 'attrs' => array('onclick' =>'javascript:AqbCredit.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'buy_form/points' . '\');'));
				$sCreditsExchangeButton = $this -> _oTemplate -> genWrapperInput($aCreditsExchangeButton, $oForm -> genInput($aCreditsExchangeButton));
			}
		}
		
		if ($this -> _oConfig -> allowToBuyCredits()){		
			$aCreditsBuyButton = array('type' => 'button',  'name' => 'buy_credits', 'value' => _t('_aqb_credits_buy_credits'), 'attrs' => array('onclick' =>'javascript:AqbCredit.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'buy_form/price' . '\');'));
			$sCreditsBuyButton = $this -> _oTemplate -> genWrapperInput($aCreditsBuyButton, $oForm -> genInput($aCreditsBuyButton));
			
			$aItems[]['items'] = _t('_aqb_credits_points_credits') . '&nbsp;:&nbsp;<b>' . $this->_sSign . $this -> _oConfig -> priceInCurrency() . '</b>';
		}

		
		
		if ($sCreditsBuyButton || $sCreditsExchangeButton){
			$aItems[]['items'] = '--------------------------------------------';
			$aItems[]['items'] = $sCreditsExchangeButton;	
			$aItems[]['items'] = $sCreditsBuyButton;		
		}
		
	
	
		return $this -> _oTemplate -> parseHtmlByName('info_block', 
		array(
				 'bx_repeat:items' => $aItems
			 ));
    }

	function getBlockCode_History() {
		return $this -> _oTemplate -> ViewCreditsTable($this -> _iProfileId);
    }  

	function getBlockCode_Credits() {
		return $this -> _oTemplate -> ViewActionWithCreditsTable($this -> _iProfileId);
    }  	
}

?>