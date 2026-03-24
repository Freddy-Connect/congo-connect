<?php
/***************************************************************************
*                            Dolphin Smart Charity Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Charity Builder
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

bx_import('BxDolTwigTemplate');
bx_import('BxDolCategories');

/*
 * Charity module View
 */
class BxCharityTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    var $oDb;

	/**
	 * Constructor
	 */
	function BxCharityTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);

		$this->oDb = $oDb; 

    }
  
    function blockEventFields (&$aDataEntry) {
        
		$bHasValues = false;

		$sRet = '<table class="modzzz_charity_fields">';
        modzzz_charity_import ('EventFormAdd');        
        $oForm = new BxCharityEventFormAdd ($GLOBALS['oBxCharityModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
 			if(!$aDataEntry[$k]) continue;

            $sRet .= '<tr><td class="modzzz_charity_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_charity_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){
 
				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{ 
					if($a['listname'])
						$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));  
					else
						$sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));  
				} 
			
			}else{
                $sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';

			$bHasValues = true; 
        }

		if(!$bHasValues) return;
			 
        $sRet .= '</table>';

        return $sRet;
    }
   
     function claim_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
  
        $aAuthor = getProfileInfo($aData['author_id']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$sOwnerThumb = $GLOBALS['oFunctions']->getMemberIcon($aAuthor['ID'], 'left'); 
 
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
  
        $aVars = array (
 		    'id' => $aData['id'],   
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'), 
    
            'charity_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'charity_title' => $aData['title'],
            'charity_description' => $sPostText,
            'comments_count' => $aData['comments_count'],
            'post_uthumb' => $sOwnerThumb, 
            'all_categories' => $this->_oMain->parseCategories($aData['categories']),  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
 
			'claim_message' => $aData['message'],
			'claimant_url' => getProfileLink($aData['member_id']),
			'claimant_name' => getNickName($aData['member_id']),
			'claim_date' => $this->filterDate($aData['claim_date'], true), 

			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 


        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
 
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
  
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
		
		
		 $sBusinessFlag = genFlag($aData['country']);
	    $sBusinessCountry= $aData['country'];
         $sBusinessProvince = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sBusinessCountry, $aData['state']) : '';
  
        $aVars = array (
 		    'id' => $aData['id'],  
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'), 
            'charity_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'charity_title' => $aData['title'],
            'comments_count' => $aData['comments_count'],
            'fans_count' => $aData['fans_count'],
            'views' => $aData['views'],
            
            'all_categories' => $this->_oMain->parseCategories($aData['categories']),  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
			'zip' => $aData['zip'],
			'city' => $aData['city'],
			
			'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			'state' => $sBusinessProvince,
			'drapeau' => $sBusinessFlag,
			
			 'bx_if:businesstelephone' => array( 
								'condition' =>$aData['businesstelephone'],
								'content' => array(
              'businesstelephone' =>  $aData['businesstelephone'],  
								), 
							),
							
			 'bx_if:businesswebsite' => array( 
								'condition' =>$aData['businesswebsite'],
								'content' => array(
              'businesswebsite' =>  $aData['businesswebsite'],  
								), 
							),
 
            'participants' => (int)$aData['fans_count'],  
            'country_city' => $this->_oMain->_formatLocation($aData), 
 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 			
			
		);
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry, $sField='desc') {
        $aVars = array (
             'description' => $aDataEntry[$sField],  
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aDataEntry) {
        
		$bHasValues = false;

		$sRet = '<table class="modzzz_charity_fields">';
        modzzz_charity_import ('FormAdd');        
        $oForm = new BxCharityFormAdd ($GLOBALS['oBxCharityModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            $sRet .= '<tr><td class="modzzz_charity_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_charity_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
            else
                $sRet .= $aDataEntry[$k];
            $sRet .= '<td></tr>';

			$bHasValues = true; 
        }

		if(!$bHasValues)
			return;

        $sRet .= '</table>';

        return $sRet;
    }

	function getPreListDisplay($sField, $sVal){ 
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sField][$sVal]['LKey']) );
	}
  
	function getStateName($sCountry, $sState){ 
 
		return $this->oDb->getStateName($sCountry, $sState);
	}
  
    function blockCustomFields (&$aDataEntry, $aShow=array()) {
        
		$bHasValues = false;
	
		$sRet = '<table class="modzzz_charity_fields">';
        modzzz_charity_import ('FormAdd');        
        $oForm = new BxCharityFormAdd ($GLOBALS['oBxCharityModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

            $sRet .= '<tr><td class="modzzz_charity_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_charity_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
				}
			}else{
				if($a['name'] == 'businesswebsite')
					$sRet .= "<a target=_blank href='".((substr($aDataEntry[$k],0,3)=="www") ? "http://".$aDataEntry[$k] : $aDataEntry[$k])."'>".$aDataEntry[$k]."</a>";
				else
					$sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';

			$bHasValues = true; 
        }

		if(!$bHasValues)
			return;

        $sRet .= '</table>';
        return $sRet;
    }
 
	function getOptionDisplay($sField='',$sVal='')
	{ 
		return ucwords($sVal);
	}
  
	function getStatus($sStatus){
		switch($sStatus){
			case "pending":
				$sLangStatus = _t("_modzzz_charity_pending");
			break;
			case "paid":
				$sLangStatus = _t("_modzzz_charity_paid");
			break;
			case "active":
				$sLangStatus = _t("_modzzz_charity_active");
			break;
			case "inactive":
				$sLangStatus = _t("_modzzz_charity_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_modzzz_charity_approved");
			break;
			case "expired":
				$sLangStatus = _t("_modzzz_charity_expired");
			break;

		}

		return $sLangStatus;
	}

    function filterDate ($i, $bLongFormat = false) {
		if($bLongFormat)
			return getLocaleDate($i, BX_DOL_LOCALE_DATE) . ' ('.defineTimeInterval($i) . ')'; 
		else
			return getLocaleDate($i, BX_DOL_LOCALE_DATE);
	}

    function filterCustomDate ($i, $bLongFormat = false) {
 		if($bLongFormat)
			return date('M d, Y g:i A', $i);
		else
			return date('M d, Y', $i);
	}

 
    function parsePreValues ($sName, $sVal='') {  
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$sVal]['LKey']) );
 	}

    function parseMultiPreValues ($sName, $sVal='') {  
		$sStr = '';
		$aVals = split(';',$sVal);
		foreach($aVals as $aEachVal){
			if($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])
 				$sStr .= htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])) . '<br>';
			else
 				$sStr .= $aEachVal . '<br>'; 
		}
 		return $sStr;
 	}
  
	function getYesListDisplay($sField='', $sVal=''){ 

		$aYesNo = array(
						0=>_t('_modzzz_charity_yes'),
						1=>_t('_modzzz_charity_no'),
					);
 
 		return $aYesNo[$sVal];
	}
 
    function parseSubCategories ($sType, $sVal='') {
		
		$sName = 'Charity'.ucwords($sType).'Categories';

		$sStr = '';
		$aVals = explode(';',$sVal);
		foreach($aVals as $aEachVal){
			$sStr .= htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sName][$aEachVal]['LKey'])) . '&#160';
 		}
 		return $sStr;
 	}
 
    function parseSubTags($s){ 
        $sRet = '';
        $a = explode (',', $s);
         
        foreach ($a as $sName)
            $sRet .= $sName.'&#160';
  
        return $sRet;
    }

    function event_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aEventEntry = $this->_oDb->getEventEntryById($aData['id']);
		$iEntryId = $aEventEntry['charity_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableEvent,$aEventEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

 		$iLimitChars = (int)getParam('modzzz_charity_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-event.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/' . $aData['uri'],
            'title' => $aData['title'],
            'event_start' => $this->filterCustomDate($aData['event_start']),
            'event_end' => $this->filterCustomDate($aData['event_end']),
            'participants' => $aData['fans_count'],
            'country_city' => $this->_oMain->_formatLocation($aData),
         
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 		
		
		); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function staff_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aStaffEntry = $this->_oDb->getStaffEntryById($aData['id']);
		$iEntryId = $aStaffEntry['charity_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableStaff,$aStaffEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
   
        $aVars = array (   
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-staff.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/view/' . $aData['uri'],
            'title' => $aData['title'], 
            'position' => $aData['position'],
 			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
  
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function supporter_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aSupporterEntry = $this->_oDb->getSupporterEntryById($aData['id']);
		$iEntryId = $aSupporterEntry['charity_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableSupporter,$aSupporterEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
  
        $aVars = array (   
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-supporter.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'supporter/view/' . $aData['uri'],
            'title' => $aData['title'],
 			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
         
		return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
   function news_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aNewsEntry = $this->_oDb->getNewsEntryById($aData['id']);
		$iEntryId = $aNewsEntry['charity_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableNews, $aNewsEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
  
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
 
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-news.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aData['uri'],
            'title' => $aData['title'],
 			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
   
   function members_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aMembersEntry = $this->_oDb->getMembersEntryById($aData['id']);
		$iEntryId = $aMembersEntry['charity_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableMembers,$aMembersEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
  
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'members/view/' . $aData['uri'],
            'title' => $aData['title'],
            'position' => $aData['position'],
 			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function branches_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aBranchesEntry = $this->_oDb->getBranchesEntryById($aData['id']);
		$iEntryId = $aBranchesEntry['charity_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableBranches,$aBranchesEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
 
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-branch.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'branches/view/' . $aData['uri'],
            'title' => $aData['title'],  
            'country_city' => $this->_oMain->_formatLocation($aData),
 			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),
 
			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function programs_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aProgramsEntry = $this->_oDb->getProgramsEntryById($aData['id']);
		$iEntryId = $aProgramsEntry['charity_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTablePrograms,$aProgramsEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
  
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-program.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'programs/view/' . $aData['uri'],
            'title' => $aData['title'],
 
 			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),

			'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ), 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
   function review_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
		$aReviewEntry = $this->_oDb->getReviewEntryById($aData['id']);

        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableReview,$aReviewEntry)) {  
            $aVars = array ('extra_css_class' => 'modzzz_charity_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

   		$aCharity = $this->_oDb->getEntryById($aData['charity_id']);
 
		$sCharityThumb = get_member_thumbnail($aData['author_id'], 'left');  

		$sDateTime = defineTimeInterval($aData['created']);
     
		$iLimitChars = (int)getParam('modzzz_charity_max_preview');
  
        $aVars = array (
			'id' => $aData['id'], 
            'review_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aData['uri'],
            'charity_name' => $aCharity['title'],
            'charity_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aCharity['uri'],
            'review_title' => $aData['title'], 
            'post_uthumb' => $sCharityThumb, 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),
 
			'author' => getNickName($aData['author_id']),
			'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
			'created' => defineTimeInterval($aData['created']),
			'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function faq_unit ($aData, $sTemplateName) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
 
        $aVars = array (
			'id' => $aData['id'], 
            'question' => $aData['question'],
            'answer' => $aData['answer'] 
        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function donors_unit ($aData, $sTemplateName) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxCharityModule');
  
   		$aCharity = $this->_oDb->getEntryById($aData['charity_id']);
 
		if($aData['anonymous']){  
			$sCharityThumb = $this->getAnonymousIcon();
			$sAuthorName = _t('_modzzz_charity_someone'); 
			$sAuthorLink = 'javascript:void(0)';
		}elseif($aData['donor_id']){
			$sCharityThumb = get_member_thumbnail($aData['donor_id'], 'left');   
			$sAuthorName = getNickName($aData['donor_id']);
			$sAuthorLink = getProfileLink($aData['donor_id']); 
		}else{
			$sCharityThumb = $this->getAnonymousIcon();
			$sAuthorLink = 'javascript:void(0)';
			if($aData['first_name'] || $aData['last_name'])
				$sAuthorName = $aData['first_name'].' '.$aData['last_name'];
			else	
				$sAuthorName = _t('_modzzz_charity_someone');
		}
  
		$sDateTime = defineTimeInterval($aData['created']);
      
        $aVars = array (
			'id' => $aData['id'],  
            'charity_name' => $aCharity['title'],
            'charity_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aCharity['uri'],
            'author_username' => $sAuthorName,
            'author_url' => $sAuthorLink,  
            'post_date' => strtolower($sDateTime), 
            'post_uthumb' => $sCharityThumb, 
            'amount' =>  number_format($aData['amount'],2), 
            'currency' => $this->_oConfig->getCurrencySign(),  
        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
  
	function getAnonymousIcon() {
 
		$icoSpacer = $GLOBALS['oFunctions']->getTemplateIcon("spacer.gif");
		$icoVisitor = $this->getIconUrl( 'anonymous.jpg') ;  

        $aVars = array (
					'spacer'=>$icoSpacer,
					'icon'=>$icoVisitor 
				);
		
		return $this->parseHtmlByName('anonymous_icon', $aVars);
	} 

    function blockSubProfileInfo (&$aData) {

		$this->_oMain = BxDolModule::getInstance('BxCharityModule');

        $aAuthor = getProfileInfo($aData['author_id']);
 
        $aVars = array (
            'author_unit' => get_member_thumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),   

			'bx_if:tag' => array (
                'condition' => $aData['tags'],
                'content' => array (
					'tags' => $this->parseSubTags($aData['tags']), 
                 ),
            ),

			'bx_if:location' => array (
                'condition' => $aData['country'],
                'content' => array (
					'location' => $this->_oMain->_formatLocation($aData, true, true),
                 ),
            ),

        );
        return $this->parseHtmlByName('block_subprofile_info', $aVars);
    }

    function blockSubProfileFields ($sType, &$aDataEntry) {
        
		$bHasValues = false;

		$sRet = '<table class="modzzz_charity_fields">';
	 
		switch($sType){
			case 'event';
				modzzz_charity_import ('EventFormAdd');   
				$oForm = new BxCharityEventFormAdd ($GLOBALS['oBxCharityModule'], $_COOKIE['memberID']);
			break;
			case 'branch';
				modzzz_charity_import ('BranchesFormAdd');   
				$oForm = new BxCharityBranchesFormAdd ($GLOBALS['oBxCharityModule'], $_COOKIE['memberID']);
			break;	 
		}
        
		if(($sType!='event') && ($sType!='branch'))return;
			 
		foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
            if (!$aDataEntry[$k]) continue;
            if ($a['listname']  && $aDataEntry[$k]=='S') continue;

            $sRet .= '<tr><td class="modzzz_charity_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_charity_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{
					if($a['listname'])
						$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
					else
						$sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
				}
            }else{
                $sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';

			$bHasValues = true; 
        }

		if(!$bHasValues)
			return;

        $sRet .= '</table>';

        return $sRet;
    }
  


}
