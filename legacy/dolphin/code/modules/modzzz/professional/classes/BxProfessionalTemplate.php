<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Professional
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

bx_import('BxDolTwigTemplate');
bx_import('BxDolCategories');

/*
 * Professional module View
 */
class BxProfessionalTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    var $oDb;

	/**
	 * Constructor
	 */
	function BxProfessionalTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);

		$this->oDb = $oDb;  
    }
   
    function displayAccessDenied ()
    {
        $this->pageStart();
        echo MsgBox(_t('_modzzz_professional_msg_access_denied'));
        $this->pageCode (_t('_Access denied'), true, false);
    }
 
    function claim_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
 
 		$aCategory = $this->oDb->getCategoryInfo($aData['category_id']);
		$sCategoryUrl = $this->oDb->getSubCategoryUrl($aCategory['uri']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
   		$iLimitChars = (int)getParam('modzzz_professional_max_preview');

        $aVars = array (
 		    'id' => $aData['id'],  
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'), 
            'professional_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'professional_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars),
            'comments_count' => $aData['comments_count'],
            'all_categories' => '<a href="'.$sCategoryUrl.'">'.$aCategory['name'].'</a>',  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
  
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),
  
 			'claim_message' => $aData['message'],
			'claimant_url' => getProfileLink($aData['member_id']),
			'claimant_name' => getNickName($aData['member_id']),
			'claim_date' => $this->filterDate($aData['claim_date'], true), 

        );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function order_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
  
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['order_date']);
  		$sDueDate = $this->filterDate($aData['expiry_date']);
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
      
        $aVars = array (
 		    'id' => $aData['id'],  
            'professional_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'professional_title' => $aData['title'],
            'create_date' => $sCreateDate,
            'due_date' => $sDueDate, 
            'author' => $sAuthorName,
            'author_url' => $sAuthorLink,
            'invoice_no' => $aData['invoice_no'],
            'order_no' => $aData['order_no'],
            'package' => $sPackageName, 
            'product_status' => $this->getStatus($aData['status']),
            'order_status' => $this->getStatus($aData['order_status']),
			'payment_method' => $aData['payment_method'],
            'invoice_no' => $aData['invoice_no'], 
         );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function invoice_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
 
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['invoice_date']);
  		$sDueDate = $this->filterDate($aData['invoice_due_date']);
  		$sExpiryDate = $this->filterDate($aData['invoice_expiry_date']);
 
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
 
        $aVars = array (
 		    'id' => $aData['professional_id'],  

			'bx_if:pay' => array( 
				'condition' =>  $this->_oMain->isPaidPackage($aData['package_id']),
				'content' => array(
					'pay_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay/' . $aData['uri'],
				) 
			),

            'professional_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'professional_title' => $aData['title'],
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
 
    function unit ($aData, $sTemplateName, &$oVotingView, $isShort = false) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_professional_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
 
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

   		$iLimitChars = (int)getParam('modzzz_professional_max_preview');
 
   		$aCategory = $this->_oDb->getCategoryInfo($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
     
		if($aData['category_id']){
			$aSubCategory = $this->_oDb->getCategoryInfo($aData['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
		}

        $aVars = array (
 		    'id' => $aData['id'],  
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
            'professional_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'professional_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),
            'comments_count' => $aData['comments_count'],
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
        
            'category_url' => $sCategoryUrl,  
			'category_name' => $aCategory['name'],

			'bx_if:subcategory' => array( 
				'condition' => $aData['category_id'],
				'content' => array(
					'subcategory_name' => $aSubCategory['name'], 
					'subcategory_url' => $sSubCategoryUrl,
				) 
			), 
 
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

    function blockDesc (&$aDataEntry) {
        $aVars = array (
            'breadcrumb' => $this->genBreadcrumb($aDataEntry),
             'description' => $aDataEntry['desc'],  
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }
 
    function genBreadcrumb($aDataEntry)
    {
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
 
		$sSiteTitle = _t('_Home');//isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');

 		$aCategory = $this->_oDb->getCategoryInfo($aDataEntry['parent_category_id']);
		$sCategoryUri = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		$aSubCategory = $this->_oDb->getCategoryInfo($aDataEntry['category_id']);  
		$sSubCategoryUri = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
 
		$aCustomBreadcrumbs = array(
			$sSiteTitle => BX_DOL_URL_ROOT, 
			_t('_'.$this->_oMain->_sPrefix.'_plural') => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aCategory['name'] => $sCategoryUri,
			$aSubCategory['name'] => $sSubCategoryUri,
		);
  
		$iter=0;
		$aPath = array();
		foreach ($aCustomBreadcrumbs as $sTitle => $sLink){
			if($iter<2)
				$aPath[] = $sLink ? '<a href="' . $sLink . '">' . $sTitle . '</a>' : $sTitle;
			else
				$aPath[] = $sLink ? '<a itemprop="url" href="' . $sLink . '"><span itemprop="title">' . $sTitle . '</span></a>' : $sTitle;
			$iter++;
		}
    
        //--- Get breadcrumb path(left side) ---//
        $sDivider = '<div class="bc_divider bx-def-margin-sec-left">&#8250;</div>';
        $aPathLinks = array();
        foreach($aPath as $sLink)
            $aPathLinks[] = '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="bc_unit bx-def-margin-sec-left">' . $sLink . '</div>';
        $sPathLinks = implode($sDivider, $aPathLinks);
  
        return '<div class="sys_bc">' . $sPathLinks . '</div>';
    }
 
    function blockFields (&$aDataEntry) {
        
		$bHasValues = false;

		$sRet = '<table class="modzzz_professional_fields">';
        modzzz_professional_import ('FormAdd');        
        $oForm = new BxProfessionalFormAdd ($GLOBALS['oBxProfessionalModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            $sRet .= '<tr><td class="modzzz_professional_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_professional_field_value">';

            if (is_string($a['display']) && is_callable(array($this, $a['display'])))
                $sRet .= call_user_func_array(array($this, $a['display']), array($aDataEntry[$k]));
            else
                $sRet .= $aDataEntry[$k];
            $sRet .= '<td></tr>';

			$bHasValues = true; 
        }

		if(!$bHasValues) return;

        $sRet .= '</table>';

        return $sRet;
    }

	function getPreListDisplay($sField, $sVal){ 
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sField][$sVal]['LKey']) );
	}
  
	function getStateName($sCountry, $sState){ 
 
		return $this->oDb->getStateName($sCountry, $sState);
	}
   
    function blockCustomFields (&$aDataEntry, $aShow=array(), $sType='') {
        
		$bHasValues = false;
	
		$sRet = '<table class="modzzz_professional_fields">';

		if($sType=='client'){ 
			modzzz_professional_import ('ClientFormAdd');  
			$oForm = new BxProfessionalClientFormAdd ($GLOBALS['oBxProfessionalModule'], $_COOKIE['memberID']); 
		}else{
			modzzz_professional_import ('FormAdd');  
			$oForm = new BxProfessionalFormAdd ($GLOBALS['oBxProfessionalModule'], $_COOKIE['memberID']);
		}

        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;
 
            $sRet .= '<tr><td class="modzzz_professional_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_professional_field_value">';
 
            if (is_string($a['display']) && is_callable(array($this, $a['display']))) {

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
				}
			}else {
				if($a['name'] == 'website')
					$sRet .= "<a target=_blank href='".((substr($aDataEntry[$k],0,3)=="www") ? "http://".$aDataEntry[$k] : $aDataEntry[$k])."'>".$aDataEntry[$k]."</a>";
				else
					$sRet .= $aDataEntry[$k];
			}
            $sRet .= '<td></tr>';

			$bHasValues = true; 
        }

		if(!$bHasValues) return;
			 
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
				$sLangStatus = _t("_modzzz_professional_pending");
			break;
			case "paid":
				$sLangStatus = _t("_modzzz_professional_paid");
			break;
			case "active":
				$sLangStatus = _t("_modzzz_professional_active");
			break;
			case "inactive":
				$sLangStatus = _t("_modzzz_professional_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_modzzz_professional_approved");
			break;
			case "expired":
				$sLangStatus = _t("_modzzz_professional_expired");
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

	function displayAvailableLevels($aValues) {
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
 
	    $aMemberships = array();
	    foreach($aValues as $aValue) { 
  
            $aMemberships[] = array(
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['id'],
                'title' => $aValue['name'],
                'description' => str_replace("\$", "&#36;", $aValue['description']),
                'days' => $aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_modzzz_professional_days') : _t('_modzzz_professional_expires_never') ,
                'price' => number_format($aValue['price'],2),
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
				'currency_sign' => $sCurrencySign,
				'videos' => ($aValue['videos']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
				'photos' => ($aValue['photos']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
				'sounds' => ($aValue['sounds']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
				'files' => ($aValue['files']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
				'featured' => ($aValue['featured']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),

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
			'days' => $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_modzzz_professional_days') : _t('_modzzz_professional_expires_never') ,
			'price' => number_format($aPackage['price'],2),
			'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
			'currency_sign' => $sCurrencySign, 
			'videos' => ($aPackage['videos']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
			'photos' => ($aPackage['photos']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
			'sounds' => ($aPackage['sounds']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
			'files' => ($aPackage['files']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),
			'featured' => ($aPackage['featured']) ? ucwords(_t('_modzzz_professional_yes')) : ucwords(_t('_modzzz_professional_no')),

		);
 
	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('form_package', $aVars);
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

    function review_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
    
		if($aData['professional_id']){
			$aProfessional = $this->_oDb->getEntryById($aData['professional_id']);
		}

		if($aData['service_id']){
			$aService = $this->_oDb->getServiceEntryById($aData['service_id']);
		}
 
		$sProfessionalThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aData['author_id'], 'left');  
 
        $aVars = array (
			'id' => $aData['id'], 
            'review_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aData['uri'], 
            'review_title' => $aData['title'],
            'review_snippet' => $this->_oMain->_formatSnippetText($aData),
 
			'bx_if:professional' => array( 
				'condition' => (!$aData['service_id']),
				'content' => array(
					'professional_title' => $aProfessional['title'],
					'professional_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aProfessional['uri'],
				),  
			), 

			'bx_if:service' => array( 
				'condition' => ($aData['service_id']),
				'content' => array(
					'service_title' => $aService['title'],
					'service_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aService['uri'], 
				),  
			), 
 
            'author' => getNickName($aData['author_id']),
            'author_url' => getProfileLink($aData['author_id']), 
            'created' => date('M d, Y', $aData['created']), 
            'created_ago' => defineTimeInterval($aData['created']),
            'post_uthumb' => $sProfessionalThumb, 
        );

        $aVars['rating'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;';
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
  
    function request_unit ($aData, $sTemplateName, &$oVotingView) {
 
		$sDateTime = date('M d, Y', $aData['created']);
  
        $aVars = array (
			'id' => $aData['id'], 
            'note' => $aData['note'],
			'email' => $aData['email'],
			'telephone' => $aData['telephone'],
			'first_name' => $aData['first_name'],
			'last_name' => $aData['last_name'],
            'created' => $sDateTime, 
 
			'bx_if:respond' => array( 
				'condition' => (!$aData['responded']),
				'content' => array(
					'respond_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'respond/' . $aData['id'] .'/'. $aData['uri'],  				
				),  
			), 
        );
  
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function service_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
 
   		$aProfessional = $this->_oDb->getEntryById($aData['professional_id']);
  
		$sDateTime = defineTimeInterval($aData['created']);
 
		$sPrice = $this->getCurrencySign($aData['currency']) . number_format($aData['price'],2);  
		
		if($aData['price_type']=='minimum'){
			$sPrice .= ' ' . _t('_modzzz_professional_and_up');
		}

		if($aData['price_type']=='negotiable'){
			$sPrice .= ' ' . _t('_modzzz_professional_negotiable_lower');
		}
  
        $aVars = array (
			'id' => $aData['id'], 
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-service.png'),  
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'service/view/' . $aData['uri'], 
            'title' => $aData['title'],
            'snippet' => $this->_oMain->_formatSnippetText($aData),
            'professional_title' => $aProfessional['title'],
            'professional_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aProfessional['uri'],
            'author' => getNickName($aData['author_id']),
            'author_url' => getProfileLink($aData['author_id']), 
            'created' => strtolower($sDateTime), 
			'price' => $sPrice,
			'length' => $this->getPreListDisplay('ServiceLength', $aData['length']),

			'bx_if:booking' => array( 
				'condition' => $aData['booking'],
				'content' => array(
					'booking_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'booking/add/' . $aData['uri'],  				
				),  
			),
 
        );

        $aVars['rating'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;';
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function client_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
 
		$aClientEntry = $this->_oDb->getClientEntryById($aData['id']);

        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableClient,$aClientEntry)) {       
            $aVars = array ('extra_css_class' => 'modzzz_professional_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

   		$aProfessional = $this->_oDb->getEntryById($aData['professional_id']);
 
		$sDateTime = defineTimeInterval($aData['created']);
       
        $aVars = array (
			'id' => $aData['id'], 
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-client.png'), 
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'client/view/' . $aData['uri'], 
            'title' => $aData['title'],
            'snippet' => $this->_oMain->_formatSnippetText($aData),
            'professional_title' => $aProfessional['title'],
            'professional_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aProfessional['uri'],
            'author' => getNickName($aData['author_id']),
            'author_url' => getProfileLink($aData['author_id']), 
            'created' => strtolower($sDateTime), 
            'post_uthumb' => $sProfessionalThumb, 
        );

        $aVars['rating'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;';
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function blockSubProfileInfo (&$aData) {

		$this->_oMain = BxDolModule::getInstance('BxProfessionalModule');

        $aAuthor = getProfileInfo($aData['author_id']);
 
        $aVars = array (
            'author_thumb' => $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none'),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),   
            'tags' => $this->parseSubTags($aData['tags']),
            'author_username' => $aAuthor ? $aAuthor['NickName'] : _t('_modzzz_professional_admin'),
            'author_url' => $aAuthor ? getProfileLink($aAuthor['ID']) : 'javascript:void(0)',
        );
        return $this->parseHtmlByName('block_subprofile_info', $aVars);
    }
 
    function parseSubCategories ($sType, $sVal='') {
		
		$sName = 'Professional'.ucwords($sType).'Categories';

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

    function getCurrencySign($sKey){ 
		
		return ($sKey) ? $sKey : $this->_oConfig->_sCurrencySign; 
    }
 
	function booking_unit ($aData, $sTemplateName) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxProfessionalModule');
 

   		$iLimitChars = (int)getParam('modzzz_professional_max_preview');
  
        $aVars = array (
 		    'id' => $aData['id'],  
            'booking_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'booking/view/' . $aData['uri'],
            'booking_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),
            'start_time' => date('M d, Y g:i A', $aData['start_time']),
            'end_time' => date('M d, Y g:i A', $aData['end_time']), 
			'author' => getNickName($aData['author_id']),
			'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
			'created' => defineTimeInterval($aData['created']), 
		);
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }


}
