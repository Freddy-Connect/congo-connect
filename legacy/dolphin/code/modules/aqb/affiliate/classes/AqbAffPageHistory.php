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

class AqbAffPageHistory extends BxDolPageView {

    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;
    var $_aProfile;
	var $_CurrencySign = '';
	var $_isAdminView = false;

	function AqbAffPageHistory(&$oMain, $iProfileId) {
		parent::BxDolPageView('aqb_aff_my_history_page');
		$this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
        $this->_aProfile = getProfileInfo($iProfileId);
		$this -> _isAdminView = $this->_oMain -> isAdmin() && $iProfileId != getLoggedId() ? true : false;
		$this->_CurrencySign = $this -> _oConfig -> getCurrencySign();
	}

	function getBlockCode_PaymentInfo(){
		$aProfileMembershipInfo = getMemberMembershipInfo($this->_aProfile['ID']);
			
		$aItems[]['items'] = _t('_aqb_aff_current_membership_level') . '&nbsp;:&nbsp;<b>' .  $aProfileMembershipInfo['Name'] . '</b>';
		
		if ($this -> _oDb -> isForcedMatrixEnabled($this->_aProfile['ID'])){
			$aItems[]['items'] = '<a href="javascript:void(0);" onclick="javascript:AqbAffItem.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'view_info/' . $aProfileMembershipInfo['ID'] . '\');">' . _t('_aqb_aff_price_matrix') . '</a>';
		}else{
			$aItems[]['items'] = _t('_aqb_aff_price_for_one_referral') . '&nbsp;:&nbsp;<b>' . $this->_CurrencySign .  $this -> _oDb -> getPriceForOneReferral($aProfileMembershipInfo['ID']) . '</b>';
		
			if ($this -> _oDb -> isPointsSystemInstalled())
				$aItems[]['items'] = _t('_aqb_aff_points_for_one_referral') . '&nbsp;:&nbsp;<b>' .  $this -> _oDb -> getPriceForOneReferral($aProfileMembershipInfo['ID'], 'points') . '</b>';
			
			$aItems[]['items'] = _t('_aqb_aff_referrals_mem_level_upgrade_percentage') . '&nbsp;:&nbsp;<b>' . $this -> _oDb -> getPriceForMembershipUpgrade($aProfileMembershipInfo['ID']) . '%</b>';
			
			if ($this -> _oDb -> isPointsSystemInstalled())
				$aItems[]['items'] = _t('_aqb_aff_referrals_mem_level_upgrade_points') . '&nbsp;:&nbsp;<b>' . $this -> _oDb -> getPriceForMembershipUpgrade($aProfileMembershipInfo['ID'],'points') . '</b>';
		}
		
		
		if ($this -> _oConfig -> isAffiliateEnabled()){
			$aItems[]['items'] = '--------------------------------------------';
			$aItems[]['items'] = '';
			$aItems[]['items'] = _t('_aqb_aff_referrals_banner_action_info' , BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'affiliates');
		}
		
		return $this -> _oTemplate -> parseHtmlByName('referral_info_block', 
		array(
				 'bx_repeat:items' => $aItems				 
			 ));
	}
	
	function getBlockCode_CommissionInfo(){
		$aPrice = $this -> _oDb -> getBalance($this->_aProfile['ID']);
		
		$aItems[]['items'] = _t('_aqb_aff_my_cash_balance') . '&nbsp;:&nbsp;<b>' . $this->_CurrencySign . (float)$aPrice['price'] . '</b>';
				
		if ($this -> _oDb -> isPointsSystemInstalled())
			$aItems[]['items'] = _t('_aqb_aff_my_points_balance') . '&nbsp;:&nbsp;<b>' .  (int)$aPrice['points'] . '</b>'; 
		
		$aPrice = $this -> _oDb -> getBalance($this->_aProfile['ID']);
		
		$fLimit = $this -> _oConfig -> getCommissionLimitation();
		
		if ((float)$aPrice['price'] == 0 || ((float)$aPrice['price'] && (float)$aPrice['price'] < $fLimit))
			$aItems[]['items'] = '<a href="javascript:void(0);" onclick = "javascript:alert(\'' . addslashes(_t('_aqb_aff_commission_limit_to_send_request', $fLimit, $this->_CurrencySign)) . '\')"><b>'._t('_aqb_aff_get_comission').'</b></a>';
		else
			$aItems[]['items'] = '<a href="javascript:void(0);" onclick = "javascript:AqbAffItem.getCommission(\'' . addslashes(_t('_aqb_aff_get_comission_message')) . '\',\''. BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'get_my_commissions' .'\')"><b>'._t('_aqb_aff_get_comission').'</b></a>';
			
		$aPendingCommission = $this -> _oDb -> getPendingTransactions($this->_aProfile['ID']);
		
		if (!empty($aPendingCommission)){
			$aItems[]['items'] = '--------------------------------------------';
			$aItems[]['items'] = _t('_aqb_aff_my_pending_balance') . '&nbsp;:&nbsp;<b>' . $this->_CurrencySign . $aPendingCommission['price'] . '</b>';
			if ($this -> _oDb -> isPointsSystemInstalled())
				$aItems[]['items'] = _t('_aqb_aff_my_pending_points_balance') . '&nbsp;:&nbsp;<b>' . $aPendingCommission['points'] . '</b>';
		}	
		
		$aPaidPrice = $this -> _oDb -> getPaidTransactions($this->_aProfile['ID']);
		
		$aItems[]['items'] = '--------------------------------------------';
		
		$aItems[]['items'] = _t('_aqb_aff_my_paid_cash_commission') . '&nbsp;:&nbsp;<b>' . $this->_CurrencySign . (float)$aPaidPrice['price'] . '</b>';
		$aItems[]['items'] = _t('_aqb_aff_my_paid_points_commission') . '&nbsp;:&nbsp;<b>' . (int)$aPaidPrice['points'] . '</b>';
		
		return $this -> _oTemplate -> parseHtmlByName('referral_info_block', 
		array(
				 'bx_repeat:items' => $aItems
				 
			 ));
	}
	
	function getBlockCode_History(){
	   $aValues = array();
	  
	  $bIsEnabled = (bool)$this -> _oDb -> isPointsSystemInstalled();
	  
	  $iPerPage = $this -> _oConfig -> getPerPageHistory();
	  $iPage = (int)$_GET['page'];
      if( $iPage < 1)
            $iPage = 1;
      $iStart = ($iPage - 1) * $iPerPage;
		
	  $aItmes = $this -> _oDb -> getMyJournalItems($this->_aProfile['ID'], $iStart, $iPerPage);
     
	  $iNum = $this -> _oDb -> getMyJournalItemsCount($this->_aProfile['ID']);
		
		if (!$iNum || !$aItmes) {
			return array(MsgBox(_t('_aqb_aff_empty_result')), array(), array(), $this -> _isAdminView ? _t('_aqb_aff_member_history_block', getNickName($this -> _aProfile['ID'])) : '' , 'getBlockCaptionMenu');
        }

        $iPages = ceil($iNum / $iPerPage);

        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'history/' . ($this -> _isAdminView ? $this->_aProfile['ID'] . '/' : '' );
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}',
            'count' => $iNum,
            'per_page' => $iPerPage,
            'page' => $iPage,
            'per_page_changer' => false,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1'",
        ));

        $sPaginate = $oPaginate->getPaginate();
		
	   foreach($aItmes as $key => $Item){
		if ($Item['inviter_type'] == 'referral' && (int)$Item['inviter']) {
			$sTitle = '<a href="'. getProfileLink($Item['inviter']) .'">' . getNickName($Item['inviter']) . '</a>';
		}	
	
		$aValues[] = array(
								'date' => getLocaleDate($Item['last_update'], BX_DOL_LOCALE_DATE_SHORT),
								'action' => _t('_aqb_aff_action_name_'.$Item['action_type'], $sTitle),
								'method' => $Item['inviter_type'],
								'number' =>	$Item['count'],		
								'sum_price' => $Item['sum_price'],						
								'sum_points' =>  $bIsEnabled  ? '<td align="center" class="value">' . $Item['sum_points'] . '</td>' : '',								
							);		 
	  }
	 
	  return array($this -> _oTemplate -> parseHtmlByName('history', 
	  array(
				 'paginate' => $sPaginate,
				 'bx_repeat:info' => $aValues,
				 'bx_if:is_points_enabled' => array(
		         'condition' => $bIsEnabled,
		         'content' => array(
		            'points_sum_title' => _t('_aqb_aff_sum_points'),
		         )
				),                 
			 )), array(), array(), $this -> _isAdminView ? _t('_aqb_aff_member_history_block', getNickName($this -> _aProfile['ID'])) : '' , 'getBlockCaptionMenu');
	}
}

?>