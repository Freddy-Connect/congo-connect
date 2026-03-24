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

class AqbAffPageMyAffiliate extends BxDolPageView {

    var $_oMain;
    var $_oTemplate;
    var $_oConfig;
    var $_oDb;
    var $_aProfile;

	function AqbAffPageMyAffiliate(&$oMain, $iProfileId) {
		parent::BxDolPageView('aqb_aff_my_aff_page');
		$this->_oMain = &$oMain;
        $this->_oTemplate = $oMain->_oTemplate;
        $this->_oConfig = $oMain->_oConfig;
        $this->_oDb = $oMain->_oDb;
        $this->_aProfile = getProfileInfo($iProfileId);
	}

	function getBlockCode_Info() {
    	$aItems[]['items'] = _t('_aqb_aff_my_invited_members_by_aff') . '&nbsp;:&nbsp;<b>' .  $this -> _oDb -> getReferralsNumber($this->_aProfile['ID'], true) . '</b>';
		$aItems[]['items'] = _t('_aqb_aff_referrals_upgraded') . '&nbsp;:&nbsp;<b>' . $this -> _oDb -> getReferralsNumber($this->_aProfile['ID'], true, true) . '</b>';
		
		$aResult = $this -> _oDb -> getBannersInfo($this->_aProfile['ID']);
				
		$aItems[]['items'] = _t('_aqb_aff_affiliate_banners_shows') . '&nbsp;:&nbsp;<b>' . (int)$aResult['impression'] . '</b>';
		$aItems[]['items'] = _t('_aqb_aff_affiliate_banners_clicks') . '&nbsp;:&nbsp;<b>' . (int)$aResult['click'] . '</b>';
		
		return $this -> _oTemplate -> parseHtmlByName('referral_info_block', 
		array(
				 'bx_repeat:items' => $aItems
			 ));
    }

    function getBlockCode_AvialBanners() {
		$this -> _oTemplate -> addJs ('main.js');
		$this -> _oTemplate -> addCss ('main.css');
		
		return $this -> _oTemplate -> getAvailableBannersPanel($this->_aProfile['ID']);
    }
}

?>