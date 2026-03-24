<?php
/***************************************************************************
*                            Dolphin Smart Church Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Group
*     website              : http://www.boonex.com
* This file is part of Dolphin - Smart Church Builder
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
 * Church module View
 */
class BxChurchTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    var $oDb;

	/**
	 * Constructor
	 */
	function BxChurchTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);

		$this->oDb = $oDb; 

    }
  
    function blockEventFields (&$aDataEntry) {
        
		$bHasValues = false;

		$sRet = '<table class="modzzz_church_fields">';
        modzzz_church_import ('EventFormAdd');        
        $oForm = new BxChurchEventFormAdd ($GLOBALS['oBxChurchModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 			
			if(!$aDataEntry[$k]) continue;

            $sRet .= '<tr><td class="modzzz_church_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_church_field_value">';
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
   
     function claim_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
  
        $aAuthor = getProfileInfo($aData['author_id']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$sOwnerThumb = $GLOBALS['oFunctions']->getMemberIcon($aAuthor['ID'], 'left'); 
 
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
  
        $aVars = array (
 		    'id' => $aData['id'],   
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'), 
    
            'church_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'church_title' => $aData['title'],
            'church_description' => $sPostText,
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
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
 
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
  
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
  
 		$aCategory = $this->oDb->getCategoryInfo($aData['category_id']);
		$sCategoryUrl = $this->oDb->getSubCategoryUrl($aCategory['uri']);


        $aVars = array (
 		    'id' => $aData['id'],  
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'), 
            'church_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'church_title' => $aData['title'],
            'comments_count' => $aData['comments_count'],
            'fans_count' => $aData['fans_count'],
            'views' => $aData['views'],
            
            'all_categories' => '<a href="'.$sCategoryUrl.'">'.$aCategory['name'].'</a>',  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
 
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

		$sRet = '<table class="modzzz_church_fields">';
        modzzz_church_import ('FormAdd');        
        $oForm = new BxChurchFormAdd ($GLOBALS['oBxChurchModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            $sRet .= '<tr><td class="modzzz_church_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_church_field_value">';
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
	
		$sRet = '<table class="modzzz_church_fields">';
        modzzz_church_import ('FormAdd');        
        $oForm = new BxChurchFormAdd ($GLOBALS['oBxChurchModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

            $sRet .= '<tr><td class="modzzz_church_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_church_field_value">';
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
				$sLangStatus = _t("_modzzz_church_pending");
			break;
			case "paid":
				$sLangStatus = _t("_modzzz_church_paid");
			break;
			case "active":
				$sLangStatus = _t("_modzzz_church_active");
			break;
			case "inactive":
				$sLangStatus = _t("_modzzz_church_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_modzzz_church_approved");
			break;
			case "expired":
				$sLangStatus = _t("_modzzz_church_expired");
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
						0=>_t('_modzzz_church_yes'),
						1=>_t('_modzzz_church_no'),
					);
 
 		return $aYesNo[$sVal];
	}
 
    function parseSubCategories ($sType, $sVal='') {
		
		$sName = 'Church'.ucwords($sType).'Categories';

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
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aEventEntry = $this->_oDb->getEventEntryById($aData['id']);
		$iEntryId = $aEventEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableEvent,$aEventEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

 		$iLimitChars = (int)getParam('modzzz_church_max_preview');

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

    function sermon_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aSermonEntry = $this->_oDb->getSermonEntryById($aData['id']);
		$iEntryId = $aSermonEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableSermon,$aSermonEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
 
        $aVars = array (   
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-sermon.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sermon/view/' . $aData['uri'],
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

    function doctrine_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aDoctrineEntry = $this->_oDb->getDoctrineEntryById($aData['id']);
		$iEntryId = $aDoctrineEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableDoctrine,$aDoctrineEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
 
        $aVars = array (   
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-doctrine.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'doctrine/view/' . $aData['uri'],
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
 
    function staff_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aStaffEntry = $this->_oDb->getStaffEntryById($aData['id']);
		$iEntryId = $aStaffEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableStaff,$aStaffEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
   
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
 
    function news_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aNewsEntry = $this->_oDb->getNewsEntryById($aData['id']);
		$iEntryId = $aNewsEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableNews, $aNewsEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
  
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
 
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
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aMembersEntry = $this->_oDb->getMembersEntryById($aData['id']);
		$iEntryId = $aMembersEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableMembers,$aMembersEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
  
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-member.png'),
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
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aBranchesEntry = $this->_oDb->getBranchesEntryById($aData['id']);
		$iEntryId = $aBranchesEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableBranches,$aBranchesEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
 
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

   function ministries_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aMinistriesEntry = $this->_oDb->getMinistriesEntryById($aData['id']);
		$iEntryId = $aMinistriesEntry['church_id'];
  
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableMinistries,$aMinistriesEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
  
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-program.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'ministries/view/' . $aData['uri'],
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
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
		$aReviewEntry = $this->_oDb->getReviewEntryById($aData['id']);

        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableReview,$aReviewEntry)) {  
            $aVars = array ('extra_css_class' => 'modzzz_church_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

   		$aChurch = $this->_oDb->getEntryById($aData['church_id']);
 
		$sChurchThumb = get_member_thumbnail($aData['author_id'], 'left');  

		$sDateTime = defineTimeInterval($aData['created']);
     
		$iLimitChars = (int)getParam('modzzz_church_max_preview');
  
        $aVars = array (
			'id' => $aData['id'], 
            'review_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aData['uri'],
            'church_name' => $aChurch['title'],
            'church_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aChurch['uri'],
            'review_title' => $aData['title'], 
            'post_uthumb' => $sChurchThumb, 
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
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
        $aVars = array (
			'id' => $aData['id'], 
            'question' => $aData['question'],
            'answer' => $aData['answer'] 
        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

   function donors_unit ($aData, $sTemplateName) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
  
   		$aChurch = $this->_oDb->getEntryById($aData['church_id']);
 
		if($aData['anonymous']){  
			$sChurchThumb = $this->getAnonymousIcon();
			$sAuthorName = _t('_modzzz_church_someone'); 
			$sAuthorLink = 'javascript:void(0)';
		}elseif($aData['donor_id']){
			$sChurchThumb = get_member_thumbnail($aData['donor_id'], 'left');   
			$sAuthorName = getNickName($aData['donor_id']);
			$sAuthorLink = getProfileLink($aData['donor_id']); 
		}else{
			$sChurchThumb = $this->getAnonymousIcon();
			$sAuthorLink = 'javascript:void(0)';
			if($aData['first_name'] || $aData['last_name'])
				$sAuthorName = $aData['first_name'].' '.$aData['last_name'];
			else	
				$sAuthorName = _t('_modzzz_church_someone');
		}
  
		$sDateTime = defineTimeInterval($aData['created']);
      
        $aVars = array (
			'id' => $aData['id'],  
            'church_name' => $aChurch['title'],
            'church_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aChurch['uri'],
            'author_username' => $sAuthorName,
            'author_url' => $sAuthorLink,  
            'post_date' => strtolower($sDateTime), 
            'post_uthumb' => $sChurchThumb, 
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

		$this->_oMain = BxDolModule::getInstance('BxChurchModule');

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

		$sRet = '<table class="modzzz_church_fields">';
	 
		switch($sType){
			case 'event';
				modzzz_church_import ('EventFormAdd');   
				$oForm = new BxChurchEventFormAdd ($GLOBALS['oBxChurchModule'], $_COOKIE['memberID']);
			break;
			case 'branch';
				modzzz_church_import ('BranchesFormAdd');   
				$oForm = new BxChurchBranchesFormAdd ($GLOBALS['oBxChurchModule'], $_COOKIE['memberID']);
			break;	 
		}
        
		if(($sType!='event') && ($sType!='branch'))return;
			 
		foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
            if (!$aDataEntry[$k]) continue;
            if ($a['listname']  && $aDataEntry[$k]=='S') continue;

            $sRet .= '<tr><td class="modzzz_church_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_church_field_value">';
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
  

    function invoice_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxChurchModule');
 
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['invoice_date']);
  		$sDueDate = $this->filterDate($aData['invoice_due_date']);
  		$sExpiryDate = $this->filterDate($aData['invoice_expiry_date']);
 
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
 
        $aVars = array (
 		    'id' => $aData['church_id'],  

			'bx_if:pay' => array( 
				'condition' =>  $this->_oMain->isPaidPackage($aData['package_id']),
				'content' => array(
					'pay_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay/' . $aData['uri'],
				) 
			),

            'church_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'church_title' => $aData['title'],
            'create_date' => $sCreateDate,
            'due_date' => $sDueDate, 
            'expiry_date' => $sExpiryDate, 
            'author' => $sAuthorName,
            'author_url' => $sAuthorLink,
            'invoice_id' => $aData['id'], 
            'invoice_no' => $aData['invoice_no'],
            'package' => $sPackageName, 
            'invoice_status' => $this->getStatus($aData['invoice_status']),
            'total' => number_format($aData['price']), 
         );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

	function displayAvailableLevels($aValues) {
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
        $sCurrencySign = str_replace("\$", "&#36;", $sCurrencySign);

	    $aMemberships = array();
	    foreach($aValues as $aValue) { 
  
            $aMemberships[] = array(
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['id'],
                'title' => str_replace("\$", "&#36;", $aValue['name']),
                'description' => str_replace("\$", "&#36;", $aValue['description']),
                'days' => $aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_modzzz_church_days') : _t('_modzzz_church_expires_never') ,
                'price' => number_format($aValue['price'],2),
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
				'currency_sign' => $sCurrencySign,
				'videos' => ($aValue['videos']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
				'photos' => ($aValue['photos']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
				'sounds' => ($aValue['sounds']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
				'files' => ($aValue['files']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
				'featured' => ($aValue['featured']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),

 	        );
	    }

		$aVars = array('bx_repeat:levels' => $aMemberships);

	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('available_packages', $aVars);
	}
 
	function getFormPackageDesc($iPackageId) {
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
  
		$aPackage = $this->_oDb->getPackageById($iPackageId);

		$aVars = array(
			'url_root' => BX_DOL_URL_ROOT,
			'id' => $aPackage['id'],
			'title' => $aPackage['name'],
			'description' => str_replace("\$", "&#36;", $aPackage['description']),
			'days' => $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_modzzz_church_days') : _t('_modzzz_church_expires_never') ,
			'price' => number_format($aPackage['price'],2),
			'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
			'currency_sign' => $sCurrencySign, 
			'videos' => ($aPackage['videos']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
			'photos' => ($aPackage['photos']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
			'sounds' => ($aPackage['sounds']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
			'files' => ($aPackage['files']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),
			'featured' => ($aPackage['featured']) ? ucwords(_t('_modzzz_church_yes')) : ucwords(_t('_modzzz_church_no')),

		);
 
	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('form_package', $aVars);
	}
 
	function youtubeId($url) {
		$url = str_replace('&amp;', '&', $url); 

		if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
			$sVideoId = $match[1];  
		}else{  
			$sVideoId = substr( parse_url($url, PHP_URL_PATH), 1 );
			$sVideoId = ltrim( $sVideoId, '/' ); 
		} 

		return $sVideoId;  
	}


}
