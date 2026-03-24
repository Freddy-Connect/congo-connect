<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Classified
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
 * Classified module View
 */
class BxClassifiedTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    var $oDb;

	/**
	 * Constructor
	 */
	function BxClassifiedTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);

		$this->oDb = $oDb;  
    }
  
     function claim_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxClassifiedModule');
 
 		$aCategory = $this->oDb->getCategoryInfo($aData['category_id']);
		$sCategoryUrl = $this->oDb->getSubCategoryUrl($aCategory['uri']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
        modzzz_classified_import('Voting');
        $oRating = new BxClassifiedVoting ('modzzz_classified', $aData['id']);
    
		$iLimitChars = (int)getParam('modzzz_classified_max_preview');
  
        $aVars = array (
 		    'id' => $aData['id'],  
 
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
    
            'classified_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'classified_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars),
            'comments_count' => $aData['comments_count'],
            'all_categories' => '<a href="'.$sCategoryUrl.'">'.$aCategory['name'].'</a>',  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
             
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => getNickName($aData['author_id']),
                    'author_url' => getProfileLink($aData['author_id']),
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
            $this->_oMain = BxDolModule::getInstance('BxClassifiedModule');
  
        $sAuthorName = $aData['author_id'] ? getNickName($aData['author_id']) : $aData['author_name'];
		$sAuthorLink = $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);';
 
 		$sCreateDate = $this->filterDate($aData['order_date']);
  		$sDueDate = $this->filterDate($aData['expiry_date']);
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
      
        $aVars = array (
 		    'id' => $aData['id'],  
            'classified_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'classified_title' => $aData['title'],
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
            $this->_oMain = BxDolModule::getInstance('BxClassifiedModule');
 
        $sAuthorName = $aData['author_id'] ? getNickName($aData['author_id']) : $aData['author_name'];
		$sAuthorLink = $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);';
 		$sCreateDate = $this->filterDate($aData['invoice_date']);
  		$sDueDate = $this->filterDate($aData['invoice_due_date']);
  		$sExpiryDate = $this->filterDate($aData['invoice_expiry_date']);
 
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
		
		// freddy ajout 
		 $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
		/////////////////////////////////////////////////////////////////
  
        $aVars = array (
 		    'id' => $aData['classified_id'],  

			'bx_if:pay' => array( 
				'condition' =>  $this->_oMain->isPaidPackage($aData['package_id']),
				'content' => array(
					'pay_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay/' . $aData['uri'],
				) 
			),
			
			//freddy ajout 'currency_sign' => $sCurrencySign, 
			'currency_sign' => $sCurrencySign, 

            'classified_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'classified_title' => $aData['title'],
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

    function sale_pending_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxClassifiedModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_classified_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
 
 		$aCategory = $this->oDb->getCategoryInfo($aData['category_id']);
		$sCategoryUrl = $this->oDb->getSubCategoryUrl($aCategory['uri']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
        modzzz_classified_import('Voting');
        $oRating = new BxClassifiedVoting ('modzzz_classified', $aData['classified_id']);
  
		$iLimitChars = (int)getParam('modzzz_classified_max_preview');
  
        $sAuthorName = $aData['author_id'] ? '<a href="'.getProfileLink($aData['buyer_id']).'">'.getNickName($aData['buyer_id']).'</a>' : $aData['author_name'];
 
        $aVars = array (
 		    'id' => $aData['id'],  
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
            'classified_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'classified_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars), 
            'comments_count' => $aData['comments_count'],
            'all_categories' => '<a href="'.$sCategoryUrl.'">'.$aCategory['name'].'</a>',  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
 			'offer_date' => $this->filterDate($aData['offer_date']),
			'offer_member' => $sAuthorName, 
			
			'classified_type' => $this->getPreListDisplay('ClassifiedType', $aData['classified_type']),
  
            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => $aData['author_id'] ? getNickName($aData['author_id']) : $aData['author_name'],
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
                    'created' => defineTimeInterval($aData['created']),
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
                ),
            ),
 
			'bx_if:why' => array( 
				'condition' => $aData['why'],
				'content' => array( 
					'why' => $aData['why'],
				), 
			), 
 
			'bx_if:price' => array( 
				//Freddy modif
				//'condition' => trim($aData['price']),
				'condition' => $aData['price'],
				'content' => array( 
					// Freddy modif
					//'price' => $this->oDb->getCurrencySign($aData['currency']) .' '. number_format($aData['price'],2),
					'price' => $aData['price'],
				), 
			), 
	
			'bx_if:payment_type' => array( 
				'condition' => (trim($aData['price']) && $aData['payment_type']),
				'content' => array( 
					'payment_type' => $this->getPreListDisplay('ClassifiedPaymentType', $aData['payment_type']),
				), 
			), 		 
        );
 
        return $this->parseHtmlByName('sale_pending_admin', $aVars);
    }
 
    function unit ($aData, $sTemplateName, &$oVotingView, $isShort=false) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxClassifiedModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_classified_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
 
 		//$aCategory = $this->oDb->getCategoryInfo($aData['category_id']);
		//$sCategoryUrl = $this->oDb->getSubCategoryUrl($aCategory['uri']);
		
		$aCategory = $this->_oDb->getParentCategoryInfo($aData['category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']);
		 
		
		////Freddy Donner mettre un label pour cchaque categorie 
		/*
		$sCategoryParent =$aCategory['id']; 
          if ($sCategoryParent=1200) // PRODUITS
		  { 
		  $siconlabel= '<i class="sys-icon shopping-cart"></i>'; 
		  }
		  else if ($sCategoryParent=1201) // SERVICES
		  {
			$siconlabel= '<i class="sys-icon suitcase"></i>'; 
		  }
		  
		  else if ($sCategoryParent=1203) // OPPORTUNITES D'AFFAIRES
		  {
			$siconlabel= '<i class="sys-icon trophy"></i>'; 
		  }
		  
		   else if ($sCategoryParent=1295) // APPELS D'OFFRES
		  {
			$siconlabel= '<i class="sys-icon trophy"></i>'; 
		  }
		  else{ $siconlabel= ' '; 
		  }
		  */
	
		  ///////////////////
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
		
		// Fredd integration Business listing/companies

if($aData['company_id']){
 			if(getParam("modzzz_listing_classified_active")=='on' && $aData['company_type']=='listing'){
				
				$oListing = BxDolModule::getInstance('BxListingModule');

				$aCompany = $oListing->_oDb->getEntryById($aData['company_id']);
				$sCompanyName = $aCompany['title'];
				$sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompany['uri'];  

			}
		}
 // Fin Fredd integration Business listing/companies
 
 
             $sauthor = $aData['author_id'] ? getNickName($aData['author_id']) : $aData['author_name'];
             $sauthor_url = $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);';
 
		if(trim($aData['price'])) { 
			$sSalePriceVal = ($aData['saleprice']) ? $this->oDb->getCurrencySign($aData['currency']) . number_format($aData['saleprice'],2) : '';
			$sPriceVal = $this->oDb->getCurrencySign($aData['currency']) . number_format($aData['price'],2) ; 
			$sPrice = ($sSalePriceVal) ? '<strike><span style="color:red">'.$sPriceVal .'</span></strike> ' . $sSalePriceVal : $sPriceVal;
		}

		$iLimitChars = (int)getParam('modzzz_classified_max_preview');
		
		$sdrapeau = genFlag($aData['country']);
		

        $aVars = array (
		
		  // Freddy label
		 
		
 		    'id' => $aData['id'],  
			'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			'Drapeau' => $sdrapeau ,
			'created' => defineTimeInterval($aData['created']),
			
			//'AddToFavorite_Url'=>$AddToFavorite_Url,
			'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? _t('_modzzz_classified_action_supprimer_favorite') :  _t('_modzzz_classified_action_add_to_favorite')) : '',
			'Sauvegardee' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? '<i class="sys-icon heart"></i>' . ' ' .  _t('_modzzz_classified_action_afficher_classified_favorite') : '') : '',
	 
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
             'city' =>  $aData['city'], 
			'zip' =>  $aData['zip'], 
            'classified_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'classified_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars),
            'comments_count' => $aData['comments_count'], 
            'post_uthumb' => $sOwnerThumb,
            'all_categories' => '<a href="'.$sCategoryUrl.'">'.'<i class="sys-icon suitcase"></i>'. ' '._t('_modzzz_classified_type_annonce_unit'). ' :'. ' ' .$aCategory['name'].'</a>',  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
			 
			
			
			'classified_type' => $this->getPreListDisplay('ClassifiedType', $aData['classified_type']),
			
			
			 'author' => $aData['author_id'] ? getNickName($aData['author_id']) : $aData['author_name'],
                    'author_url' => $aData['author_id'] ? getProfileLink($aData['author_id']) : 'javascript:void(0);',
					// Freddy
			'company' => $sCompanyName ?   '<strong>'. '<i class="sys-icon building"></i>'.' ' . $sCompanyName .'</strong>' : ' ' ,
			
			'company_url' => ($sCompanyUrl) ? $sCompanyUrl :  $sauthor_url,
			//
			
			
  
			'bx_if:why' => array( 
				'condition' => $aData['why'],
				'content' => array( 
					'why' => $aData['why'],
				), 
			), 
 
			  


          'bx_if:price' => array( 
				//Freddy modif
				//'condition' => trim($aData['price']),
				'condition' => $aData['price'],
				'content' => array( 
					// Freddy modif
					//''price' => $sPrice,
					'price' => $aData['price'],
				), 
			), 
			
			
			
			
			
			
			'bx_if:payment_type' => array( 
				'condition' => (trim($aData['price']) && $aData['payment_type']),
				'content' => array( 
					'payment_type' => $this->getPreListDisplay('ClassifiedPaymentType', $aData['payment_type']),
				), 
			), 		

            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                  
					
                   
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;',
					
					
					 
 
                ),
            ),
 

        );
  
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    // ======================= ppage compose block functions 

    function blockDesc (&$aDataEntry) {
         
		if($aDataEntry['price']) {
			$sSalePriceVal = ($aDataEntry['saleprice']) ? $this->oDb->getCurrencySign($aDataEntry['currency']) . number_format($aDataEntry['saleprice'],2) : '';
			$sPriceVal = $this->oDb->getCurrencySign($aDataEntry['currency']) . number_format($aDataEntry['price'],2) ; 
			$sPrice = ($sSalePriceVal) ? '<strike><span style="color:red">'.$sPriceVal .'</span></strike> ' . $sSalePriceVal : $sPriceVal;
		}

 		$aCategory = $this->_oDb->getParentCategoryInfo($aDataEntry['category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		if($aDataEntry['category_id']){
			$aSubCategory = $this->_oDb->getCategoryById($aDataEntry['category_id']); 
			$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
		}
         
		
		$sJobCountry= $aDataEntry['country'];	
		$sJobstate = ($aDataEntry['state']) ? $this->_oDb->getStateName($sJobCountry, $aDataEntry['state']) : '';
		$sdrapeau = genFlag($aDataEntry['country']);
 
		$aVars = array (
             'title' => $aDataEntry['title'],
			 
			
			'category_name' => $aCategory['name'],
			'category_url' => $sCategoryUrl,

			'classified_type' => $this->getPreListDisplay('ClassifiedType', $aDataEntry['classified_type']),

			'bx_if:price' => array( 
			// freddy modif
			//'condition' => trim($aDataEntry['price']),
			'condition' => $aDataEntry['price'],
				'content' => array( 
					// freddy modif
					//'price' => $sPrice,
					'price' => $aDataEntry['price'],
				), 
			),
			
			 'city' => $aDataEntry['city'],
			 'zip' => $aDataEntry['zip'],
			 'region' => $sJobstate,
			 'country' => _t($GLOBALS['aPreValues']['Country'][$aDataEntry['country']]['LKey']),
			'Drapeau' => $sdrapeau ,
			
			
			//Freddy ajout
			 'bx_if:address1' => array( 
				
				'condition' => $aDataEntry['address1'],
				'content' => array( 
					
					'Commune' => $aDataEntry['address1'],
				), 
			), 
			
			//Freddy ajout
			'bx_if:address2' => array( 
				
				'condition' => $aDataEntry['address2'],
				'content' => array( 
					
					'Adresse' => $aDataEntry['address2'],
				), 
			), 
			
			
			
            'bx_if:sub_category' => array (
                'condition' => $aDataEntry['category_id'],
                'content' => array ( 
					'subcategory_name' => $aSubCategory['name'],
					'subcategory_url' => $sSubCategoryUrl,
                ),
            ),  

			'bx_if:payment_type' => array( 
				'condition' => (trim($aDataEntry['price']) && $aDataEntry['payment_type']),
				'content' => array( 
					'payment_type' => $this->getPreListDisplay('ClassifiedPaymentType', $aDataEntry['payment_type']),
				), 
			), 

			 'bx_if:why' => array( 
				 'condition' => $aDataEntry['why'],
				 'content' => array( 
					'why' => $aDataEntry['why'],
				 ), 
			 ),

             'description' => $aDataEntry['desc'] 
        );
        return $this->parseHtmlByName('block_description', $aVars);
    }

    function blockFields (&$aDataEntry) {
        
		$bHasValues = false;

		$sRet = '<table class="modzzz_classified_fields">';
        modzzz_classified_import ('FormAdd');        
        $oForm = new BxClassifiedFormAdd ($GLOBALS['oBxClassifiedModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
  
	       $sRet .= '<tr><td class="modzzz_classified_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_classified_field_value">';
		 
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
	
		$sRet = '<table class="modzzz_classified_fields">';
        modzzz_classified_import ('FormAdd');        
        $oForm = new BxClassifiedFormAdd ($GLOBALS['oBxClassifiedModule'], $_COOKIE['memberID'], $aDataEntry['id']);
        foreach ($oForm->aInputs as $k => $a) {
		  
			if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

	       $sRet .= '<tr><td class="modzzz_classified_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_classified_field_value">';
            
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
				$sLangStatus = _t("_modzzz_classified_pending");
			break;
			case "paid":
				$sLangStatus = _t("_modzzz_classified_paid");
			break;
			case "active":
				$sLangStatus = _t("_modzzz_classified_active");
			break;
			case "inactive":
				$sLangStatus = _t("_modzzz_classified_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_modzzz_classified_approved");
			break;
			case "expired":
				$sLangStatus = _t("_modzzz_classified_expired");
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
        $sCurrencySign = str_replace("\$", "&#36;", $sCurrencySign);

	    $aMemberships = array();
	    foreach($aValues as $aValue) { 
  
            $aMemberships[] = array(
                'url_root' => BX_DOL_URL_ROOT,
                'id' => $aValue['id'],
                'title' => str_replace("\$", "&#36;", $aValue['name']),
                'description' => str_replace("\$", "&#36;", $aValue['description']),
                'days' => $aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_modzzz_classified_days') : _t('_modzzz_classified_expires_never') ,
                'price' => $aValue['price'],
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
				'currency_sign' => $sCurrencySign, 
				'videos' => ($aValue['videos']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
				'photos' => ($aValue['photos']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
				'sounds' => ($aValue['sounds']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
				'files' => ($aValue['files']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
				'featured' => ($aValue['featured']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),

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
			'days' => $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_modzzz_classified_days') : _t('_modzzz_classified_expires_never') ,
			'price' => $aPackage['price'],
			'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
			'currency_sign' => $sCurrencySign, 
			'videos' => ($aPackage['videos']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
			'photos' => ($aPackage['photos']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
			'sounds' => ($aPackage['sounds']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
			'files' => ($aPackage['files']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),
			'featured' => ($aPackage['featured']) ? ucwords(_t('_modzzz_classified_yes')) : ucwords(_t('_modzzz_classified_no')),

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
