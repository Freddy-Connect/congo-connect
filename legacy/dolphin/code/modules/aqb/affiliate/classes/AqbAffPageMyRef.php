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

class AqbAffPageMyRef extends BxDolPageView {

    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;
    var $_aProfile;
	var $_sType;

	function AqbAffPageMyRef(&$oMain, $iProfileId, $sType) {
		parent::BxDolPageView('aqb_aff_my_ref_page');
		$this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
		$this-> _sType = $sType;
        $this->_aProfile = getProfileInfo($iProfileId);
	}

	function getBlockCode_Info() {
        $sLinkMessage = _t('_aqb_aff_link_message_title');
		$aProfileMembershipInfo = getMemberMembershipInfo($this->_aProfile['ID']);
			
		$aItems[]['items'] = _t('_aqb_aff_current_membership_level') . '&nbsp;:&nbsp;<b>' .  $aProfileMembershipInfo['Name'] . '</b>';
		
		if ($this -> _oDb -> isForcedMatrixEnabled($this->_aProfile['ID'])){
			$aItems[]['items'] = '<a href="javascript:void(0);" onclick="javascript:AqbAffItem.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'view_info/' . $aProfileMembershipInfo['ID'] . '\');">' . _t('_aqb_aff_price_matrix') . '</a>';
		}else{
			$aItems[]['items'] = _t('_aqb_aff_price_for_one_referral') . '&nbsp;:&nbsp;<b>' . $this -> _oConfig -> getCurrencySign () .  $this -> _oDb -> getPriceForOneReferral($aProfileMembershipInfo['ID']) . '</b>';

			if ($this -> _oDb -> isPointsSystemInstalled()) $aItems[]['items'] = _t('_aqb_aff_points_for_one_referral') . '&nbsp;:&nbsp;<b>' .  $this -> _oDb -> getPriceForOneReferral($aProfileMembershipInfo['ID'], 'points') . '</b>';
		}
		
		$aItems[]['items'] = '--------------------------------------------';
		
		if ($this -> _oDb -> isForcedMatrixEnabled($this->_aProfile['ID']))
			$aItems[]['items'] = _t('_aqb_aff_my_ref_in_matrix_title') . '&nbsp;:&nbsp;<b>' .  $this -> _oDb -> getReferralsNumber($this->_aProfile['ID']) . '</b>';
		
		$aItems[]['items'] = _t('_aqb_aff_my_ref_title') . '&nbsp;:&nbsp;<b>' .  $this -> _oDb -> getReferralsNumber($this->_aProfile['ID'], 0, false, true) . '</b>';
		$aItems[]['items'] = _t('_aqb_aff_referrals_upgraded') . '&nbsp;:&nbsp;<b>' . $this -> _oDb -> getReferralsNumber($this->_aProfile['ID'], 0, true) . '</b>';
				
		$oForm = new BxTemplFormView(array());
		$aRefLink = array('type' => 'text',  'name' => 'referral_link', 'value' => $this -> _oConfig -> getReferralLink($this->_aProfile['ID']), 'attrs' => array('style' => 'width:100%', 'onfocus' =>'javascript:$(this).select();', 'onclick' =>'javascript:$(this).select();','readonly' => 'true'), 'attrs_wrapper' => array('style' => 'width:80%;float:left;'));
       	$sLinkTextBox = $this -> _oTemplate -> genWrapperInput($aRefLink, $oForm -> genInput($aRefLink));
		$aItems[]['items'] = '--------------------------------------------';
		$aItems[]['items'] = _t('_aqb_aff_referral_link') . '&nbsp;:&nbsp;';
		$aItems[]['items'] = $sLinkTextBox;

		if ($this -> _oConfig -> isAllowToSendInvitations()){
			$aRefButton = array('type' => 'button',  'name' => 'send_invitation', 'value' => _t('_aqb_aff_send_invintation'), 'attrs' => array('onclick' =>'javascript:AqbAffItem.showPopup(\'' . BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'get_invite_form/' . '\');'), 'attrs_wrapper' => array('style' => 'margin-top:10px;'));
	       	$sSendButton = $this -> _oTemplate -> genWrapperInput($aRefButton, $oForm -> genInput($aRefButton));
			$aItems[]['items'] = $sSendButton;
		}	
		
		return $this -> _oTemplate -> parseHtmlByName('referral_info_block', 
		array(
				 'bx_repeat:items' => $aItems
			 ));
    }

    function getBlockCode_MyReferrals() {
		$this -> _oTemplate -> addJs ('main.js');
		$this -> _oTemplate -> addCss ('main.css');
		
		$aTopMenu = array();
		$bMatrixEnabled = $this -> _oDb -> isForcedMatrixEnabled($this->_aProfile['ID']);
		
		if ($bMatrixEnabled)
		$aTopMenu = array(            
            'aff-table' => array('href' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'referrals/table/' . $this -> _aProfile['ID'], 'title' => _t('_aqb_aff_table_view'), 'active' => 'table' == $this -> _sType ? 1 : 0),
			'aff-tree' => array('href' => BX_DOL_URL_ROOT . $this -> _oConfig -> getBaseUri() . 'referrals/tree/' . $this -> _aProfile['ID'], 'title' => _t('_aqb_aff_tree_view'), 'active' => 'tree' == $this -> _sType ? 1 : 0)
	    );
		
		if(!$bMatrixEnabled || $this -> _sType == 'table')  
			 $sContent = $this -> _oTemplate -> ViewReferralsTable((int)$this -> _aProfile['ID']);
		else 
			 $sContent = $this -> _oTemplate -> ViewMemberTree((int)$this -> _aProfile['ID']);
		
		return array('<div class="aqb-aff-wrapper">' . $sContent . '</div>', $aTopMenu, array(), ($this -> _oMain -> isAdmin() && (int)$this -> _aProfile['ID'] != (int)$this -> _oMain -> iUserId) ? _t('_aqb_aff_invited_members', getNickName($this -> _aProfile['ID'])) : '' , 'getBlockCaptionMenu');
    }
}

?>