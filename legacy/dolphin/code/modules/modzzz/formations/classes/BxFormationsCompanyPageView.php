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

bx_import('BxDolTwigPageView');

class BxFormationsCompanyPageView extends BxDolTwigPageView {	

	function BxFormationsCompanyPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_formations_company_view', $oMain, $aDataEntry);
        
		$this->sSearchResultClassName = 'BxFormationsSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'companyview/'. $this->aDataEntry['company_uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');   
	}
		
	function getBlockCode_Info() {
        return array($this->_blockInfo ($this->aDataEntry, $this->_oTemplate->blockCompanyFields($this->aDataEntry)));
    }

    function _blockInfo ($aData, $sFields = '') {

        $aAuthor = getProfileInfo($aData['company_author_id']);

        $aVars = array (
            'author_thumb' => $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']), 
            'fields' => $sFields, 
        );
        return $this->_oTemplate->parseHtmlByName('company_entry_view_block_info', $aVars);
    }
 
	function getBlockCode_Desc() {
        return array($this->_oTemplate->blockCompanyDesc ($this->aDataEntry));
    }

	function getBlockCode_Photo() {
	    
		if(!$this->aDataEntry['company_icon'])
		    return;

        $sImage = $this->_oMain->_oDb->getCompanyIcon($this->aDataEntry['id'], $this->aDataEntry['company_icon'],true,true);
 
	    if(!$sImage)
		    return;

        $aVars = array (
            'image_url' => $sImage,
        );
        return $this->_oTemplate->parseHtmlByName('block_photo', $aVars);
    }    
  
    function getBlockCode_Rate() {
        modzzz_formations_import('CompanyVoting');
        $o = new BxFormationsCompanyVoting ('modzzz_formations_company', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedCompanyRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_formations_import('CompanyCmts');
        $o = new BxFormationsCompanyCmts ('modzzz_formations_company', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['company_author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['company_uri'],
                'TitleEdit' => $this->_oMain->isAllowedCompanyEdit($this->aDataEntry) ? _t('_modzzz_formations_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedCompanyDelete($this->aDataEntry) ? _t('_modzzz_formations_action_title_delete') : '',
 				'TitleInvite' => $this->_oMain->isAllowedCompanySendInvitation($this->aDataEntry) ? _t('_modzzz_formations_action_title_invite') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_formations_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedCompanyBroadcast($this->aDataEntry) ? _t('_modzzz_formations_action_title_broadcast') : '',
                'AddToFeatured' => $this->_oMain->isAllowedCompanyMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_formations_action_remove_from_featured') : _t('_modzzz_formations_action_add_to_featured')) : '' 
			);

            if (!$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleInvite'] && !$aInfo['TitleShare'] && !$aInfo['TitleBroadcast'] && !$aInfo['AddToFeatured']) 
                return '';

            return $oFunctions->genObjectsActions($aInfo, 'modzzz_formations_company');
        } 

        return '';
    }    
  
	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }

	function getBlockCode_Contact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'contact');
    }

	function _blockCustomDisplay($aDataEntry, $sType) {
  
		switch($sType) { 
			case "contact":
				$aAllow = array('company_website','company_email','company_telephone','company_fax');
			break;
			case "location":
				$aAllow = array('company_address', 'company_city','company_state','company_country','company_zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockCompanyFields($aDataEntry, $aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
 
	function getBlockCode_Formations() {   
 
		return $this->ajaxBrowse('company_formations', $this->_oDb->getParam('modzzz_formations_perpage_main_recent'),array(),$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true) {
        $oMain = BxDolModule::getInstance('BxFormationsModule');

        bx_import ('SearchResult', $oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
            return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';


        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }   


   
    function getCode() {
 
        return parent::getCode();
    }

}
