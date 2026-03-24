<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Listing
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
 * Listing module View
 */
class BxListingTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    var $oDb;

	/**
	 * Constructor
	 */
	function BxListingTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);

		$this->oDb = $oDb;  
    }
  
    function claim_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
 
 		$aCategory = $this->oDb->getCategoryInfo($aData['category_id']);
		$sCategoryUrl = $this->oDb->getSubCategoryUrl($aCategory['uri']);
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
   		$iLimitChars = (int)getParam('modzzz_listing_max_preview');

        $aVars = array (
 		    'id' => $aData['id'],  
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'), 
            'listing_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'listing_title' => $aData['title'],
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
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
  
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['order_date']);
  		$sDueDate = $this->filterDate($aData['expiry_date']);
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
      
        $aVars = array (
 		    'id' => $aData['id'],  
            'listing_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'listing_title' => $aData['title'],
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
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
 
        $sAuthorName = getNickName($aData['author_id']); 
		$sAuthorLink = getProfileLink($aData['author_id']);  
 		$sCreateDate = $this->filterDate($aData['invoice_date']);
  		$sDueDate = $this->filterDate($aData['invoice_due_date']);
  		$sExpiryDate = $this->filterDate($aData['invoice_expiry_date']);
 
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
		
		// freddy ajout 
		 $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
		/////////////////////////////////////////////////////////////////
 
        $aVars = array (
 		    'id' => $aData['listing_id'],  

			'bx_if:pay' => array( 
				'condition' =>  $this->_oMain->isPaidPackage($aData['package_id']),
				'content' => array(
					'pay_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay/' . $aData['uri'],
				) 
			),
			
			//freddy ajout 'currency_sign' => $sCurrencySign, 
			'currency_sign' => $sCurrencySign, 

            'listing_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'listing_title' => $aData['title'],
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
            $this->_oMain = BxDolModule::getInstance('BxListingModule');

        if (!$this->_oMain->isAllowedView ($aData)) {            
            $aVars = array ('extra_css_class' => 'modzzz_listing_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
 		$aCategory = $this->_oDb->getCategoryById($aData['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		$sSubCategoryCode = ''; 
		if($aData['category_id']){

			$aSubCategoryLinks = array();
			$aSubCategories = explode(CATEGORIES_DIVIDER, $aData['category_id']);
			foreach($aSubCategories as $iSubCategoryId){
				$aSubCategory = $this->_oDb->getCategoryById($iSubCategoryId); 
				$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
				$sSubCategoryTitle = $aSubCategory['name'];
				
				
				$aSubCategoryLinks[] = "<a href='$sSubCategoryUrl'>$sSubCategoryTitle</a>";
				/*$aSubCategoryLinks[] = "<span style=' text-transform: capitalize;  background-color: #fefeff;  box-shadow: 5px 5px 5px rgb(0 0 0 / 30%);
    padding: 5px;
    display: inline-block;
    font-size: 14px;
	font-weight:500;
    margin-right: 3px;
    margin-bottom: 3px;
    overflow: hidden;
	color:  #666;
	
    border: 1px solid #fefeff;
	
    white-space: normal;
    text-overflow: ellipsis;'>$sSubCategoryTitle</span>";
	*/
				
			}
			
 
			$sSubCategoryCode = implode('<span>&nbsp;&#8901;&nbsp;</span>', $aSubCategoryLinks);
			//$sSubCategoryCode = implode('<span class="bullet">&#8594;</span>', $aSubCategoryLinks);
		}

       /* Freddy comment modif afficher le logo si logo vide afficher image sinon afficher no-image-thumb.png
	    $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }
		*/
		/////////////////////////////freddy ajout modif afficher le logo si logo vide afficher image sinon afficher no-image-thumb.png
		$sImage = '';
		
		if($aData['icon']){ 
		    $sImage = $this->_oDb->getLogo($aData['id'], $aData['icon'], true);
			
	   }
	    else if ($aData['thumb']){
			 $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
			  $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
			 $sImage = $aImage['file'] ? $aImage['file'] : $this->getImageUrl('no-image-thumb.png') ;
		  }
 		else{
			$sImage = $this->getImageUrl('no-image-thumb.png');
		} 
		///////////////////////////////////////////////////////////////////
		
		/* Freddy comment
		if($aData['icon']){ 
		    $sNoImage = $this->_oDb->getLogo($aData['id'], $aData['icon'], true);
 		}else{
			$sNoImage = $this->getImageUrl('no-image-thumb.png');
		}
		*/
		

   		$iLimitChars = (int)getParam('modzzz_listing_max_preview');
  
		$sJobEntries = '';
		$aEntries = array();
		if(getParam('modzzz_listing_jobs_active')=='on'){
			$oJob = BxDolModule::getInstance('BxJobsModule');
			$aJobs = $this->oDb->getBusinessJobsById($aData['id']);

			$iIter = 1;
			$iCountJobs = count($aJobs);
			foreach($aJobs as $aEachJob) {  
				$aEntries[] = array(  
					'job_url' => BX_DOL_URL_ROOT . $oJob->_oConfig->getBaseUri() . 'view/' . $aEachJob['uri'],
					'job_title' => $aEachJob['title'], 
					'spacer' => ($iCountJobs==$iIter) ? '' : '&#183;'
				);
				$iIter++;
			}

			if($iCountJobs){
				$aVars = array('bx_repeat:entries' => $aEntries); 
				$sJobEntries = $this->parseHtmlByName('job_entries', $aVars); 
			}
		}

		
		
		////freddy jobs integration
		$iId = $aData['id'];
		
		if(getParam('modzzz_listing_jobs_active')=='on'){
			$iJobsCount = $this->_oDb->getModzzzxJobsCount($iId);
		}
		
		if ($iJobsCount >=1){
			$iJobUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/browse/' . $aData['uri'];
		} else{
			$iJobUrl  ='javascript:void(0);';
		}
		
		if ($iJobsCount <=1){
			$iJobOfferkey =_t('_modzzz_listing_action_job_single');
		} else{
			$iJobOfferkey =_t('_modzzz_listing_action_job_plural');
		}
		
		////freddy classified integration
		if(getParam('modzzz_listing_classified_active')=='on'){
			$iClassifiedCount = $this->_oDb->getModzzzxClassifiedCount($iId);
		}
		
		if ($iClassifiedCount >=1){
			$iClassifiedUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/browse/' . $aData['uri'];
		} else{
			$iClassifiedUrl  ='javascript:void(0);';
		}
		
		if ($iClassifiedCount <=1){
			$iClassifiedkey =_t('_modzzz_listing_action_classified_single');
		} else{
			$iClassifiedkey =_t('_modzzz_listing_action_classified_plural');
		}
		///////////////////////freddy events integration
		
		if(getParam('modzzz_listing_boonex_events')=='on'){
			$iEventsCount = $this->_oDb->getBoonexEventsCount($iId);
		}else{
			$iEventsCount = (int)$this->aDataEntry['events_count'];
		}
		
		
		if ($iEventsCount >=1){
			$iEventdUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/browse/' . $aData['uri'];
		} else{
			$iEventdUrl  ='javascript:void(0);';
		}
		
		if ($iEventsCount <=1){
			$iEventKey =_t('_modzzz_listing_action_event_single');
		} else{
			$iEventKey =_t('_modzzz_listing_action_event_plural');
		}
   ////////////////////////////////////////////////////////////////
	    $sBusinessFlag = genFlag($aData['country']);
	    $sBusinessCountry= $aData['country'];
         $sBusinessProvince = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sBusinessCountry, $aData['state']) : '';


        $aVars = array (
 		    'id' => $aData['id'],  
           /* freddy comment modif
		    'thumb_url' => $sImage ? $sImage : $sNoImage,
			*/
			'thumb_url' => $sImage,
			////////////////////////////////////////////
			
			
            'listing_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['uri'],
            'listing_title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars),
            'comments_count' => $aData['comments_count'],
           // 'all_categories' => '<a href="'.$sCategoryUrl.'">'.$aCategory['name'].'</a>',  
			 'all_categories' => $aCategory['name'],  
            'post_tags' => $this->_oMain->parseTags($aData['tags']), 
            
			
			
			
			'jobs' => $sJobEntries, 
			
			
			
			
			'city' => $aData['city'],
			'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			'province' => $sBusinessProvince,
			'drapeau' => $sBusinessFlag,
			 'zip' => $aData['zip'],  
			'adresse' => $aData['address1'],
			
		    //'employees_count' => _t($GLOBALS['aPreValues']['ListingNombreEmployes'][$aData['employees_count']]['LKey']),
			
			
			 'bx_if:employees_count' => array( 
								'condition' =>$aData['employees_count'],
								'content' => array(
              'employees_count' => _t($GLOBALS['aPreValues']['ListingNombreEmployes'][$aData['employees_count']]['LKey']),  
								), 
							),
			
			 
			 //freddy ajout --- Afficher le nombre total des offres de formation, articles, events + lien
			/*
			'bx_if:jobs_count' => array( 
								'condition' => $iJobsCount,
								'content' => array(
			  'jobs_count_url'=>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/browse/' . $aData['uri'],
              'jobs_count' =>  $iJobsCount,  
			  'Job_Single_Plural' => $iJobOfferkey,
								), 
							), 
							
			*/
			// 'jobs_count_url'=>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/browse/' . $aData['uri'],
			'jobs_count_url'=> $iJobUrl ,
              'jobs_count' =>  $iJobsCount,  
			  'Job_Single_Plural' => $iJobOfferkey,
							
			/*
			'bx_if:events_count' => array( 
								'condition' => $iEventsCount,
								'content' => array(
			  'events_count_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/browse/' . $aData['uri'],
              'events_count' =>  $iEventsCount,  
								), 
							), 
			
			'bx_if:classified_count' => array( 
								'condition' => $iClassifiedCount,
								'content' => array(
			  'classified_count_url'=>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/browse/' . $aData['uri'],
              'classified_count' =>  $iClassifiedCount,  
								), 
							), 
							
				*/
				  'events_count_url' => $iEventdUrl,
              'events_count' =>  $iEventsCount,  
			   'Event_Single_Plural' => $iEventKey,
							
							
			 'classified_count_url'=> $iClassifiedUrl,
              'classified_count' =>  $iClassifiedCount,  
			   'classified_Single_Plural' => $iClassifiedkey,
				 ////////////////////[END] FREDDY FIN INTEGRATION///////////////////////////////////////////////
			
			
 
            'bx_if:parent_category' => array (
                'condition' => (!$aData['category_id']),
                'content' => array ( 
					'category_name' => $aCategory['name'],
					'category_url' => $sCategoryUrl,
				 ),
            ),
  
            'bx_if:sub_category' => array (
                'condition' => $aData['category_id'],
                'content' => array ( 
					'subcategories' => $sSubCategoryCode
                ),
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
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
 
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

		$sRet = '<table class="modzzz_listing_fields">';
        modzzz_listing_import ('FormAdd');        
        $oForm = new BxListingFormAdd ($GLOBALS['oBxListingModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            $sRet .= '<tr><td class="modzzz_listing_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_listing_field_value">';

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
	
		$sRet = '<table class="modzzz_listing_fields">';
 
		switch($sType){
			case 'news':
				modzzz_listing_import ('NewsFormAdd');        
				$oForm = new BxListingNewsFormAdd ($GLOBALS['oBxListingModule'], $_COOKIE['memberID']);
			break;
			case 'event':
				modzzz_listing_import ('EventFormAdd');        
				$oForm = new BxListingEventFormAdd ($GLOBALS['oBxListingModule'], $_COOKIE['memberID']);
			break;
			default:
				modzzz_listing_import ('FormAdd');        
				$oForm = new BxListingFormAdd ($GLOBALS['oBxListingModule'], $_COOKIE['memberID']);
			break; 
		}
 
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;
 
            $sRet .= '<tr><td class="modzzz_listing_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="modzzz_listing_field_value">';
 
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
				if($a['name'] == 'businesswebsite')
					$sRet .= "<a target=_blank href='".((substr($aDataEntry[$k],0,3)=="www") ? "//".$aDataEntry[$k] : $aDataEntry[$k])."'>"._t('_modzzz_listing_visit')."</a>";
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
				$sLangStatus = _t("_modzzz_listing_pending");
			break;
			case "paid":
				$sLangStatus = _t("_modzzz_listing_paid");
			break;
			case "active":
				$sLangStatus = _t("_modzzz_listing_active");
			break;
			case "inactive":
				$sLangStatus = _t("_modzzz_listing_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_modzzz_listing_approved");
			break;
			case "expired":
				$sLangStatus = _t("_modzzz_listing_expired");
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
                'days' => $aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_modzzz_listing_days') : _t('_modzzz_listing_expires_never') ,
                'price' => number_format($aValue['price'],2),
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
				'currency_sign' => $sCurrencySign,
				'videos' => ($aValue['videos']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
				'photos' => ($aValue['photos']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
				'sounds' => ($aValue['sounds']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
				'files' => ($aValue['files']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
				'coupons' => ($aValue['coupons']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
				'banner' => ($aValue['banner']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')), 
				'featured' => ($aValue['featured']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),

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
			'days' => $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_modzzz_listing_days') : _t('_modzzz_listing_expires_never') ,
			'price' => number_format($aPackage['price'],2),
			'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
			'currency_sign' => $sCurrencySign, 
			'videos' => ($aPackage['videos']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
			'photos' => ($aPackage['photos']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
			'sounds' => ($aPackage['sounds']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
			'files' => ($aPackage['files']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
			'coupons' => ($aPackage['coupons']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
			'banner' => ($aPackage['banner']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),
			'featured' => ($aPackage['featured']) ? ucwords(_t('_modzzz_listing_yes')) : ucwords(_t('_modzzz_listing_no')),

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
 
    function review_unit ($aData, $sTemplateName, &$oVotingView) {

        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
 
		$aReviewEntry = $this->_oDb->getReviewEntryById($aData['id']);

        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableReview,$aReviewEntry)) {       
            $aVars = array ('extra_css_class' => 'modzzz_listing_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

   		$aListing = $this->_oDb->getEntryById($aData['listing_id']);
 
		$sListingThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aData['author_id'], 'left');  

		$sDateTime = defineTimeInterval($aData['created']);
       
        $aVars = array (
			'id' => $aData['id'], 
            'review_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/view/' . $aData['uri'], 
            'review_title' => $aData['title'],
            'review_snippet' => $this->_oMain->_formatSnippetText($aData),
            'listing_title' => $aListing['title'],
            'listing_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aListing['uri'],
            'author' => getNickName($aData['author_id']),
            'author_url' => getProfileLink($aData['author_id']), 
            'created' => strtolower($sDateTime), 
            'post_uthumb' => $sListingThumb, 
        );

        $aVars['rating'] = $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['id'], $aData['rate']) : '&#160;';
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function blockSubProfileInfo (&$aData) {

		$this->_oMain = BxDolModule::getInstance('BxListingModule');

        $aAuthor = getProfileInfo($aData['author_id']);
 
        $aVars = array (
            'author_unit' => $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),   
        );
        return $this->parseHtmlByName('block_subprofile_info', $aVars);
    }
  
    function event_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
 
		$aEventEntry = $this->_oDb->getEventEntryById($aData['id']);
		$iEntryId = $aEventEntry['listing_id'];
 
        $aDataEntry = $this->_oDb->getEntryById($iEntryId);

        if (!$this->_oMain->isAllowedView ($aDataEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_listing_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
 		$iLimitChars = (int)getParam('modzzz_listing_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-event.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/view/' . $aData['uri'],
            'title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars),  
            'event_start' => date('M d, Y g:i A', $aData['event_start']),  
            'event_end' => date('M d, Y g:i A', $aData['event_end']),  
            'participants' => $aData['fans_count'],
			'country_city' => $this->_oMain->_formatLocation($aData),

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
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
 
		$aEntry = $this->_oDb->getNewsEntryById($aData['id']);
		$iEntryId = $aEntry['listing_id'];
 
        $aDataEntry = $this->_oDb->getEntryById($iEntryId);

        if (!$this->_oMain->isAllowedView ($aDataEntry)) {            
            $aVars = array ('extra_css_class' => 'modzzz_listing_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('modzzz_listing_max_preview');
 
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-news.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aData['uri'],
            'title' => $aData['title'],
            'snippet_text' => $this->_oMain->_formatSnippetText($aData, $iLimitChars), 

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

	function getEmployees($iId=0) {
		$arr = array();
		for($iter=1; $iter<=8; $iter++){
			$arr[$iter] = _t('_modzzz_listing_employee_option_'.$iter); 
		}

		return ($iId) ? $arr[$iId] : $arr;
	}
 
}
