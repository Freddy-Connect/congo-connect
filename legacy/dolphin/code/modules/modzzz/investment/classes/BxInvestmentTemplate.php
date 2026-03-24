<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Investment
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
 * Investment module View
 */
class BxInvestmentTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    var $oDb;

	/**
	 * Constructor
	 */
	function BxInvestmentTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);

		$this->oDb = $oDb;  
    }
  
	function inquiry_unit($aData){
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxInvestmentModule');
  
		$iInquiryId = $aData['id']; 
		$iEntryId = $aData['id_entry'];  
 		$iMemberId = $aData['id_profile'];
		$sNickName = getNickName($iMemberId);
		$sNickLink = getProfileLink($iMemberId);
		$icoMember = $GLOBALS['oFunctions']->getMemberThumbnail($iMemberId, 'left');
		$sMessage = $aData['desc']; 
		$sDateTime = defineTimeInterval($aData['created']);
	  
		$sUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'];

		$aInvestment = $this->_oMain->_oDb->getEntryById($iEntryId); 
		$bAdmin = $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aInvestment);

        $aVars = array (            
			'entry_id' => $iEntryId, 
			'inquiry_id' => $iInquiryId,   
			'post_uthumb' => $icoMember, 
			'author_url' => $sNickLink,
			'author' => $sNickName,
            'created' => strtolower($sDateTime), 
			'snippet' => $sMessage,
			'bx_if:allowdelete' => array( 
				'condition' => $bAdmin,
				'content' => array(
					'entry_id' => $iEntryId, 
					'inquiry_id' => $iInquiryId, 
					'url' => $sUrl,   
				), 
			),
        ); 
 
        return $this->parseHtmlByName('inquiry_unit', $aVars);
    }
 
    function claim_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxInvestmentModule');
 
 		$aCategory = $this->oDb->getCategoryInfo($aData['category_id']);
		$sCategoryUrl = $this->oDb->getSubCategoryUrl($aCategory['uri']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
        modzzz_investment_import('Voting');
        $oRating = new BxInvestmentVoting ('modzzz_investment', $aData['id']);
    
		$iLimitChars = (int)getParam('modzzz_investment_max_preview');
  
        $aVars = array (
 		    'id' => $aData['id'],  
 
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
    
            'investment_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'investment_title' => $aData['title'],
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
            $this->_oMain = BxDolModule::getInstance('BxInvestmentModule');
  
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['order_date']);
  		$sDueDate = $this->filterDate($aData['expiry_date']);
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
      
        $aVars = array (
 		    'id' => $aData['id'],  
            'investment_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'investment_title' => $aData['title'],
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
 
         );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function invoice_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxInvestmentModule');
 
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['invoice_date']);
  		$sDueDate = $this->filterDate($aData['invoice_due_date']);
  		$sExpiryDate = $this->filterDate($aData['invoice_expiry_date']);
 
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
  
        $aVars = array (
 		    'id' => $aData['investment_id'],  

			'bx_if:pay' => array( 
				'condition' =>  $this->_oMain->isPaidPackage($aData['package_id']),
				'content' => array(
					'pay_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay/' . $aData['uri'],
				) 
			),

            'investment_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'investment_title' => $aData['title'],
            'create_date' => $sCreateDate,
            'due_date' => $sDueDate, 
            'expiry_date' => $sExpiryDate, 
            'author' => $sAuthorName,
            'author_url' => $sAuthorLink,
            'invoice_id' => $aData['id'], 
            'invoice_no' => $aData['invoice_no'],
            'package' => $sPackageName, 
            'invoice_status' => $this->getStatus($aData['invoice_status']),
            'total' => number_format($aData['price'],2),

         );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxInvestmentModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_investment_unit');
            return $this->parseHtmlByName('browse_unit_private', $aVars);
        }
 
        $aAuthor = getProfileInfo($aData['author_id']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		/* freddy modif*/
		//$sOwnerThumb = $GLOBALS['oFunctions']->getMemberIcon($aAuthor['ID'], 'left'); 
		$sOwnerThumb = $GLOBALS['oFunctions']->getMemberIcon($aAuthor['ID'], 'left');
		
		//$sOwnerThumb = get_member_thumbnail($aAuthor['ID'], 'left'); 
		//$sOwnerThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none');
		//$sOwnerThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true);
		
	 
		$iLimitChars = (int)getParam('modzzz_investment_max_preview');
 
		//freddy modif 
		$sMinAmt = ($aData['min_investment']) ? $this->oDb->getCurrencySign($aData['currency']). $aData['min_investment'] : '';
		//$sMinAmt = ($aData['min_investment']) ?  $aData['min_investment'] : '';
   
		// freddy modif
		$sMaxAmt = ($aData['max_investment']) ? $this->oDb->getCurrencySign($aData['currency']). $aData['max_investment'] : '';
		//$sMaxAmt = ($aData['max_investment']) ?  $aData['max_investment'] : '';

		 if($sMinAmt && $sMaxAmt){
			$sCapital = $sMinAmt .' - '. $sMaxAmt; 
		 }elseif($sMinAmt){
			$sCapital = _t('_modzzz_investment_minimum').' '.$sMinAmt; 
		 }elseif($sMaxAmt){
			$sCapital = _t('_modzzz_investment_maximum').' '.$sMaxAmt; 
		 }
		 
		  $sBusinessFlag = genFlag($aData['country']);
	    $sBusinessCountry= $aData['country'];
         $sBusinessProvince = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sBusinessCountry, $aData['state']) : '';
		 
		 if ( $aData['investment_type']=='entrepreneur'){
		
		          $sImageInvestorEntrepreneur=$sImage ? $sImage : $this->getImageUrl('no-image-thumb.png');
		 }else{
			 
			// $sImageInvestorEntrepreneur=$sImage ? $sImage : $this->getImageUrl('no-image-thumb-investor.png');
			 $sImageInvestorEntrepreneur = ' ';
			 
	      }

        $aVars = array (
		
		  
		    
 		    'id' => $aData['id'],   
           
            'bx_if:title' => array( 
				'condition' => $aData['investment_type']=='entrepreneur' ,
				'content' => array( 
			 'investment_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
			 'investment_title' => $aData['title'],
			
				), 
			), 
			
			
			
             
           // 'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
		      'thumb_url' =>  $sImageInvestorEntrepreneur ,
			'bx_if:why' => array( 
				'condition' => $aData['why'],
				'content' => array( 
					'why' => $aData['why'],
				), 
			), 
			
			'bx_if:entrepreneur' => array( 
				'condition' =>  $aData['investment_type']=='entrepreneur' ,
				'content' => array( 
					'stade' => _t($GLOBALS['aPreValues']['InvestmentStade'][$aData['stade']]['LKey']),
					'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			        'province' => $sBusinessProvince,
			         'drapeau' => $sBusinessFlag,
		               'city' => $aData['city'],
					   
					'budget' => _t($GLOBALS['aPreValues']['ProjetBudget'][$aData['financement_recu']]['LKey']),
					   
					    'all_categories' => $this->_oMain->parseCategories($aData['categories']),  
						
				), 
			), 
			
			'bx_if:investor' => array( 
				'condition' =>  $aData['investment_type']=='investor' ,
				'content' => array( 
					
					'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			        'province' => $sBusinessProvince,
			         'drapeau' => $sBusinessFlag,
		               'city' => $aData['city'],
					   'investment_type' => _t('_modzzz_investment_' . $aData['investment_type']),
					    
				'budget' => _t($GLOBALS['aPreValues']['ProjetBudget'][$aData['financement_recu']]['LKey']),
					   
					    'all_categories' => $this->_oMain->parseCategories($aData['categories']),  
						
				), 
			), 
			
			
                'investment_type' => _t('_modzzz_investment_' . $aData['investment_type']),
 
			// freddy modif 
			//'capital' => $sCapital,  
			 'bx_if:capital' => array( 
								'condition' =>$sCapital,
								'content' => array(
              'capital' => $sCapital,  
								), 
							),
			
			
            
            'post_uthumb' => $sOwnerThumb,
           
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
  
			

            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars),
			'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
			 'author' => getNickName($aData['author_id']),
             'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',

            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                   // 'author' => getNickName($aData['author_id']),
                   // 'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                   // 'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
				   'comments_count' => $aData['comments_count'], 
		           'views' => $aData['views'] ,
				    'fans_count' => $aData['fans_count'] ,
                ),
            ),
  
        );
  
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
		
		 $sBusinessFlag = genFlag($aDataEntry['country']);
	    $sBusinessCountry= $aData['country'];
         $sBusinessProvince = ($aDataEntry['state']) ? $this->_oDb->getStateName($sBusinessCountry, $aDataEntry['state']) : '';
          
		$aVars = array (
		
		   'country' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']),
			'province' => $sBusinessProvince,
			'drapeau' => $sBusinessFlag,
		     'city' => $aDataEntry['city'],
			// 'stade' => _t($GLOBALS['aPreValues']['InvestmentStade'][$aDataEntry['stade']]['LKey']),
			 
			 
			 'bx_if:stade' => array( 
				'condition' => $aData['stade'],
				'content' => array( 
					'stade' => _t($GLOBALS['aPreValues']['InvestmentStade'][$aData['stade']]['LKey']),
				), 
			), 
			
  
			 'bx_if:why' => array( 
				 'condition' => $aDataEntry['why'],
				 'content' => array( 
					'why' => $aDataEntry['why'],
				 ), 
			 ),
			 
			  'all_categories' => $this->parseCategories($aDataEntry['categories']), 
			  'comments_count' => $aDataEntry['comments_count'], 
			   'views' => $aDataEntry['views'] ,

             'description' => $aDataEntry['desc'] ,
			  'title' => $aDataEntry['title'] ,
			 
			  
			  ///////// freddy ajout
			  'bx_if:min' => array( 
				'condition' => $aDataEntry['min_investment'],
				'content' => array( 
					// freddy modif 
			//'min_investment' => 		 $this->_oDb->getCurrencySign($this->aDataEntry['currency']).number_format($this->aDataEntry['min_investment'],2),
			'min_investment' => 	 /*$this->_oDb->getCurrencySign($this->aDataEntry['currency']).*/$aDataEntry['min_investment'],
				), 
			), 

			'bx_if:max' => array( 
				'condition' => $aDataEntry['max_investment'],
				'content' => array( 
					'max_investment' => /*  $this->_oDb->getCurrencySign($this->aDataEntry['currency']).*/$aDataEntry['max_investment'],
				), 
			),

        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aDataEntry) {
        
		$bHasValues = false;

		$sRet = '<table class="modzzz_investment_fields">';
        modzzz_investment_import ('FormAdd');        
        $oForm = new BxInvestmentFormAdd ($GLOBALS['oBxInvestmentModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
	        $sRet .= '<tr><td class="modzzz_investment_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_investment_field_value">';
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
	
		$sRet = '<table class="modzzz_investment_fields">';
        modzzz_investment_import ('FormAdd');        
        $oForm = new BxInvestmentFormAdd ($GLOBALS['oBxInvestmentModule'], $_COOKIE['memberID'], $aDataEntry['id']);
        foreach ($oForm->aInputs as $k => $a) {
		  
			if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

	        $sRet .= '<tr><td class="modzzz_investment_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_investment_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){
				if($a['name'] == 'state')
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				else
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
			}else{
				if($a['name'] == 'sellerwebsite')
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
				$sLangStatus = _t("_modzzz_investment_pending");
			break;
			case "paid":
				$sLangStatus = _t("_modzzz_investment_paid");
			break;
			case "active":
				$sLangStatus = _t("_modzzz_investment_active");
			break;
			case "inactive":
				$sLangStatus = _t("_modzzz_investment_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_modzzz_investment_approved");
			break;
			case "expired":
				$sLangStatus = _t("_modzzz_investment_expired");
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
                'days' => $aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_modzzz_investment_days') : _t('_modzzz_investment_expires_never') ,
                'price' => $aValue['price'],
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
				'currency_sign' => $sCurrencySign, 
				'videos' => ($aValue['videos']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
				'photos' => ($aValue['photos']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
				'sounds' => ($aValue['sounds']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
				'files' => ($aValue['files']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
				'featured' => ($aValue['featured']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),

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
			'days' => $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_modzzz_investment_days') : _t('_modzzz_investment_expires_never') ,
			'price' => $aPackage['price'],
			'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
			'currency_sign' => $sCurrencySign, 
			'videos' => ($aPackage['videos']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
			'photos' => ($aPackage['photos']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
			'sounds' => ($aPackage['sounds']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
			'files' => ($aPackage['files']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),
			'featured' => ($aPackage['featured']) ? ucwords(_t('_modzzz_investment_yes')) : ucwords(_t('_modzzz_investment_no')),

		);
 
	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('form_package', $aVars);
	}



}
