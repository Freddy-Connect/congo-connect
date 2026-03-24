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

bx_import ('BxDolTwigTemplate');

/*
 * Events module View
 */
class BxEventsTemplate extends BxDolTwigTemplate {
    
	var $_oMain;

	/**
	 * Constructor
	 */
	function BxEventsTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
        $this->_iPageIndex = 500; 
     }
 
    // ======================= ppage compose block functions 
    

    function blockInfo (&$aEvent)
    {
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');

        $aAuthor = getProfileInfo($aEvent['ResponsibleID']);
 
		$sState = $this->getStateName($aEvent['Country'], $aEvent['State']);
		$sStateCity = $aEvent['City'] . ($sState ? ', '.$sState : '');

	    $bOwner = ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aEvent)) ? true : false;
		
		//Freddy ajout Befirend
		$iUserId = getLoggedId();
		$iId = $aEvent['ResponsibleID'];
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);
		// [END] Freddy ajout Befirend

        $aVars = array (
			
            'author_unit' => $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aEvent['Date'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aEvent['Date']),
            'cats' => $this->parseCategories($aEvent['Categories']),
            'tags' => $this->parseTags($aEvent['Tags']),
            'views' => $aEvent['Views'],
            'location' => $this->_oMain->_formatLocation($aEvent, true, true),
            'fields' => $this->blockFields($aEvent),
            'author_username' => $aAuthor ? $aAuthor['NickName'] : _t('_bx_events_admin'),
            'author_url' => $aAuthor ? getProfileLink($aAuthor['ID']) : 'javascript:void(0)',
			'bx_if:owner' => array( 
				'condition' => $bOwner,
				'content' => array(   
					'featured' => $aEvent['Featured'] ? ($aEvent['featured_expiry_date'] ? _t('_bx_events_featured_until') .' '. $this->filterCustomDate($aEvent['featured_expiry_date']) : _t('_bx_events_featured_listing')) : _t('_bx_events_not_featured_listing'),
				),  		
			),
			'bx_if:showexpiry' => array( 
				'condition' => ($bOwner && $aEvent['expiry_date']),
				'content' => array(   
					'expire' => date('M d, Y', $aEvent['expiry_date']),
				),  		
			), 
            'start_date' => getLocaleDate($aEvent['EventStart'], BX_DOL_LOCALE_DATE),
            'end_date' => getLocaleDate($aEvent['EventEnd'], BX_DOL_LOCALE_DATE),

            //'start_date' => date('F dS, Y g:i A', $aEvent['EventStart']),
            //'end_date' => date('F dS, Y g:i A', $aEvent['EventEnd']),
            'start_date_snippet' => date('c', $aEvent['EventStart']),
            'end_date_snippet' => date('c', $aEvent['EventEnd']), 
            
			'event_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aEvent['EntryUri'],
            'event_title' => $aEvent['Title'],

            'place' => $aEvent['Place'],

			'bx_if:street' => array (
				'condition' => $aEvent['Street'],
				'content' => array (
					'street' => $aEvent['Street'],
				),
			),	

			'bx_if:zipcode' => array (
				'condition' => $aEvent['Zip'],
				'content' => array (
					'zip' => $aEvent['Zip'],
				),
			),			
			  
            'country' => _t($GLOBALS['aPreValues']['Country'][$aEvent['Country']]['LKey']), 
            'state_city' => $sStateCity, 
			
			// Freddy ajout
				'bx_if:OrganizerName' => array (
				'condition' => $aEvent['OrganizerName'],
				'content' => array (
					'OrganizerName' => $aEvent['OrganizerName'],
				),
			),	
			
			
			'bx_if:OrganizerPhone' => array (
				'condition' => $aEvent['OrganizerPhone'],
				'content' => array (
					'OrganizerPhone' => $aEvent['OrganizerPhone'],
				),
			),	
			
			'bx_if:OrganizerEmail' => array (
				'condition' => $aEvent['OrganizerEmail'],
				'content' => array (
					'OrganizerEmail' => $aEvent['OrganizerEmail'],
				),
			),	
			
			'bx_if:OrganizerWebsite' => array (
				'condition' => $aEvent['OrganizerWebsite'],
				'content' => array (
					'OrganizerWebsite' => $aEvent['OrganizerWebsite'],
				),
			),	
		
		
		 'bx_if:street1' => array (
				'condition' => $aEvent['Street1'],
				'content' => array (
					'street1' => $aEvent['Street1'],
				),
			),	
			
			
			 'bx_if:participants' => array (
				'condition' => $aEvent['FansCount'],
				'content' => array (
					'participants' => $aEvent['FansCount'],
				),
			),	
			
			'drapeau' => genFlag($aEvent['Country']),
			//'participants' => $aEvent['FansCount'],
			
			
				
			 //Freddy ajout Befirend
	        'bx_if:befriend' => array(
	        'condition' => !in_array($iId, $aFriendsIds) && !isFriendRequest($iUserId, $iId) &&  !$this->_oMain->isEntryAdmin($aEvent),
	        'content' => array(
	        'id' => $iId
					    ),
				           ),
		// [END] Freddy ajout Befirend
		
		// Freddy Contacter Organisateur
		 'bx_if:contacter_url' => array(
	                'condition' =>  !$this->_oMain->isEntryAdmin($aEvent),
					
	                'content' => array(
	                
					'contacter_url' => BX_DOL_URL_ROOT . 'mail.php?mode=compose&recipient_id=' . $aEvent['ResponsibleID'],
					    ),
				           ),
			
		);

        return $this->parseHtmlByName('entry_view_block_info', $aVars);
    }
  
    function blockDesc (&$aEvent) {
        $aVars = array (
            'breadcrumb' => $this->genBreadcrumb($aEvent),
            'description' => $aEvent['Description'], 
        );

        return array($this->parseHtmlByName('block_description', $aVars));
    }

    function genBreadcrumb($aDataEntry)
    {
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');
 
		$sSiteTitle = _t('_Home');//isset($GLOBALS['site']['title']) ? $GLOBALS['site']['title'] : getParam('site_title');

		$aCustomBreadcrumbs = array(
			$sSiteTitle => BX_DOL_URL_ROOT, 
			_t('_'.$this->_oMain->_sPrefix) => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home/',
			$aDataEntry[$this->_oDb->_sFieldCity] => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'browse/city/' . $aDataEntry[$this->_oDb->_sFieldCity],
		);
  
		$aPath = array();
		foreach ($aCustomBreadcrumbs as $sTitle => $sLink)
			$aPath[] = $sLink ? '<a itemprop="url" href="' . $sLink . '"><span itemprop="title">' . $sTitle . '</span></a>' : $sTitle;
    
        //--- Get breadcrumb path(left side) ---//
        $sDivider = '<div class="bc_divider bx-def-margin-sec-left">&#8250;</div>';
        $aPathLinks = array();
        foreach($aPath as $sLink)
            $aPathLinks[] = '<div itemscope itemtype="http://data-vocabulary.org/Breadcrumb" class="bc_unit bx-def-margin-sec-left">' . $sLink . '</div>';
        $sPathLinks = implode($sDivider, $aPathLinks);
  
        return '<div class="sys_bc">' . $sPathLinks . '</div>';
    }
 
    function blockParticipantsInfo (&$aEvent) {
        $aVars = array (
            'description' => $aEvent['ParticipantsInfo'],
        );
        return array($this->parseHtmlByName('block_participants', $aVars));
    }  
     
    function blockFields (&$aEvent) {
        $sRet = '<table class="bx_events_fields">';
        bx_events_import ('FormAdd');
        $oForm = new BxEventsFormAdd ($GLOBALS['oBxEventsModule'], $this->_iProfileId);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['infodisplay'])) continue;
            $sRet .= '<tr><td class="bx_events_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">' . $a['caption'] . '</td><td class="bx_events_field_value">';
            if (is_string($a['infodisplay']) && is_callable(array($this, $a['infodisplay'])))
                $sRet .= call_user_func_array(array($this, $a['infodisplay']), array($aEvent[$k]));
            else
                $sRet .= $aEvent[$k];
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';
        return $sRet;
    }

    // ======================= output display filters functions

    function filterDate ($i) {
        return getLocaleDate($i, BX_DOL_LOCALE_DATE) . ' ('.defineTimeInterval($i) . ')';
    }

    function filterCustomDate ($i, $bLongFormat = false) {
 		if($bLongFormat)
			return date('M d, Y g:i A', $i);
		else
			return date('M d, Y', $i);
	}


	function getOptionDisplay($sField='',$sVal='')
	{ 
		return ucwords($sVal);
	}

	function getStatus($sStatus){
		switch($sStatus){
			case "pending":
				$sLangStatus = _t("_bx_events_pending");
			break;
			case "paid":
				$sLangStatus = _t("_bx_events_paid");
			break;
			case "active":
				$sLangStatus = _t("_bx_events_active");
			break;
			case "inactive":
				$sLangStatus = _t("_bx_events_inactive");
			break;
			case "approved":
				$sLangStatus = _t("_bx_events_approved");
			break;
			case "expired":
				$sLangStatus = _t("_bx_events_expired");
			break;

		}

		return $sLangStatus;
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
                'days' => $aValue['days'] > 0 ?  $aValue['days'] . ' ' . _t('_membership_txt_days') : _t('_membership_txt_expires_never') ,
                'price' => $aValue['price'],
                'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
				'currency_sign' => $sCurrencySign,

				'videos' => ($aValue['videos']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
				'photos' => ($aValue['photos']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
				'sounds' => ($aValue['sounds']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
				'files' => ($aValue['files']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
				'featured' => ($aValue['featured']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
 	        );
	    }

		$aVars = array('bx_repeat:levels' => $aMemberships);

	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('available_packages', $aVars);
	}
 
    function order_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');

        $sAuthorName = getNickName($aData['ResponsibleID']); 
		$sAuthorLink = getProfileLink($aData['ResponsibleID']);  
 		$sCreateDate = $this->filterCustomDate($aData['order_date']);
  		$sDueDate = $this->filterCustomDate($aData['expiry_date']);
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);
      
        $aVars = array (
 		    'id' => $aData['ID'],  
            'event_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['EntryUri'],
            'event_title' => $aData['Title'],
            'create_date' => $sCreateDate,
            'due_date' => $sDueDate, 
            'author' => $sAuthorName,
            'author_url' => $sAuthorLink,
            'invoice_no' => $aData['invoice_no'],
            'order_no' => $aData['order_no'],
            'package' => $sPackageName, 
            'product_status' => $this->getStatus($aData['Status']),
            'order_status' => $this->getStatus($aData['order_status']),
			'payment_method' => $aData['payment_method'],
            'invoice_no' => $aData['invoice_no'],
		

         );
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }

    function invoice_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');
 
        $sAuthorName = getNickName($aData['ResponsibleID']); 
		$sAuthorLink = getProfileLink($aData['ResponsibleID']);  
 		$sCreateDate = $this->filterCustomDate($aData['invoice_date']);
  		$sDueDate = $this->filterCustomDate($aData['invoice_due_date']);
  		$sExpiryDate = $this->filterCustomDate($aData['invoice_expiry_date']);
 
		$sPackageName = $this->_oMain->_oDb->getPackageName($aData['package_id']);

        $aVars = array (
 		    'id' => $aData['event_id'],  

			'bx_if:pay' => array( 
				'condition' =>  $this->_oMain->isPaidPackage($aData['package_id']),
				'content' => array(
					'pay_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'pay/' . $aData['EntryUri'],
				) 
			),

            'event_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['EntryUri'],
            'event_title' => $aData['Title'],
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
  
    function unit ($aData, $sTemplateName, &$oVotingView, $isShort = false)
    {
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');

        if (!$this->_oMain->isAllowedView ($aData) && $aData[$this->_oDb->_sFieldAllowViewTo]!='p' ) {
            $aVars = array ();
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }

        $sImage = '';
        if ($aData['PrimPhoto']) {
            $a = array ('ID' => $aData['ResponsibleID'], 'Avatar' => $aData['PrimPhoto']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        }

		/*[begin] location integration (modzzz)*/
		if(getParam('modzzz_location_boonex_events')=='on'){
			$sLocationLink = BxDolModule::getInstance('BxLocationModule')->getLocationLink($aData['location_id']);
		}
	    /*[end] location integration (modzzz)*/

		/*[begin] club integration (modzzz)*/
		if(getParam('modzzz_club_boonex_events')=='on'){
			$sClubLink = BxDolModule::getInstance('BxClubModule')->getClubLink($aData['club_id']);
		}
	    /*[end] club integration (modzzz)*/
 
		/*[begin] band integration (modzzz)*/
		if(getParam('modzzz_bands_boonex_events')=='on'){
			$sBandLink = BxDolModule::getInstance('BxBandsModule')->getBandLink($aData['band_id']);
		}
	    /*[end] band integration (modzzz)*/
 
		/*[begin] group integration (modzzz)*/
		if(getParam('bx_groups_boonex_events')=='on'){
			$sGroupLink = BxDolModule::getInstance('BxGroupsModule')->getGroupLink($aData['group_id']);
		}
	    /*[end] group integration (modzzz)*/
  
  
  
  	/////////  FREDDY //////////[END] FREDDY 14/06/2017 INTEGRATION BUSINESS LISTING 
		if($aData['listing_id']){
 			if(getParam("modzzz_listing_boonex_events")=='on' && $aData['company_type']=='listing'){
				$oListing = BxDolModule::getInstance('BxListingModule');

				$aCompany = $oListing->_oDb->getEntryById($aData['listing_id']);
				$sCompanyName = $aCompany['title'];
				$sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompany['uri'];
				  
			}

		}
		//////////[END] FREDDY 14/06/2017 INTEGRATION BUSINESS LISTING 
		
  
		/*[begin] listing integration (modzzz)*/
		/* Freddy commentaire
		if(getParam('modzzz_listing_boonex_events')=='on'){
			$sListingLink = BxDolModule::getInstance('BxListingModule')->getListingLink($aData['listing_id']);
			
		}
		*/
	    /*[end] listing integration (modzzz)*/

		/*[begin] school integration (modzzz)*/
		if(getParam('modzzz_schools_boonex_events')=='on'){ 
			$sSchoolLink = BxDolModule::getInstance('BxSchoolsModule')->getSchoolLink($aData['school_id']);
		}
	    /*[end] school integration (modzzz)*/
  
		if($aData['icon']){ 
		    $sNoImage = $this->_oDb->getLogo($aData['ID'], $aData['icon'], true);
 		}else{
			$sNoImage = $this->getImageUrl('no-image-thumb.png');
		}

        $aVars = array (
		
		 
            'id' => $aData['ID'],
            'thumb_url' => $sImage ? $sImage : $sNoImage,
            'event_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/' . $aData['EntryUri'],
            'event_title' => $aData['Title'],
            'event_start' => $this->_oMain->_formatDateInBrowse($aData),
            'event_start_rsnippet' => $this->_oMain->_formatDateRichSnippet($aData),
            'participants' => $aData['FansCount'],
            'country_city' => $this->_oMain->_formatEventLocation($aData, true, true, true),
		   
		  //  'country' => _t($GLOBALS['aPreValues']['Country'][$aData['Country']]['LKey']), 
			//'city' => $aData['City'],
			
            'snippet_text' => $this->_oMain->_formatSnippetText($aData), 
			'views_count' => $aData['Views'],  
			'start_date' => getLocaleDate($aData['EventStart'], BX_DOL_LOCALE_DATE),
			'end_date' => getLocaleDate($aData['EventEnd'], BX_DOL_LOCALE_DATE),
  
			/*[begin] school integration (modzzz)*/
			'bx_if:school_event' => array (
				'condition' => (getParam('modzzz_schools_boonex_events')=='on' && $aData['school_id']),
				'content' => array (
					'school' => $sSchoolLink, 
				),
			),
		   /*[end] school integration (modzzz)*/

			/*[begin] location integration (modzzz)*/
			'bx_if:location_event' => array (
				'condition' => (getParam('modzzz_location_boonex_events')=='on' && $aData['location_id']),
				'content' => array (
					'location' => $sLocationLink,
				),
			),
		   /*[end] location integration (modzzz)*/

			/*[begin] club integration (modzzz)*/
			'bx_if:club_event' => array (
				'condition' => (getParam('modzzz_club_boonex_events')=='on' && $aData['club_id']),
				'content' => array (
					'club' => $sClubLink,
				),
			),
		   /*[end] club integration (modzzz)*/
 
			/*[begin] band integration (modzzz)*/
			'bx_if:band_event' => array (
				'condition' => (getParam('modzzz_bands_boonex_events')=='on' && $aData['band_id']),
				'content' => array (
					'band' => $sBandLink,
				),
			),
		   /*[end] band integration (modzzz)*/

			/*[begin] listing integration (modzzz)*/
			'bx_if:listing_event' => array (
				'condition' => (getParam('modzzz_listing_boonex_events')=='on' && $aData['listing_id']),
				'content' => array (
					'listing' => $sListingLink,
				),
			),
		   /*[end] listing integration (modzzz)*/

			/*[begin] group integration (modzzz)*/
			'bx_if:group_event' => array (
				'condition' => (getParam('bx_groups_boonex_events')=='on' && $aData['group_id']),
				'content' => array (
					'group' => $sGroupLink,
				),
			),
		   /*[end] group integration (modzzz)*/


            'bx_if:full' => array (
                'condition' => !$isShort,
                'content' => array (
                    'author' => $aData['ResponsibleID'] ? getNickName($aData['ResponsibleID']) : _t('_bx_events_admin'),
                    'author_url' => $aData['ResponsibleID'] ? getProfileLink($aData['ResponsibleID']) : 'javascript:void(0);',
                    'rate' => $oVotingView ? $oVotingView->getJustVotingElement(0, $aData['ID'], $aData['Rate']) : '&#160;',
					  ///FREDDY 01/10/2016 INTEGRATION BUSINESS LISTING AND SCHOOLS
		            'company_event' => ($sCompanyName) ? $sCompanyName : _t('_bx_events_na'),
		            'company_event_url' => ($sCompanyUrl) ? $sCompanyUrl : 'javascript:void(0);',
	
	              	////////////////////
                ),
            ),
        );

        $aVars = array_merge ($aVars, $aData);
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
 
    function blockCustomFields (&$aDataEntry, $aShow=array()) {
		$bHasValues = false;

        $sRet = '<table class="bx_events_fields">';
        bx_events_import ('FormAdd');        
        $oForm = new BxEventsFormAdd ($GLOBALS['oBxEventsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;
			
			$bHasValues = true;

            $sRet .= '<tr><td class="bx_events_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">' . $a['caption'] . '</td><td class="bx_events_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){
	 
				$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
			  
			}else{ 
				if($a['name'] == 'State'){
					$sRet .= $this->getStateName($aDataEntry['Country'], $aDataEntry[$k]);
				}elseif($a['name'] == 'OrganizerWebsite'){
					$sRet .= "<a target=_blank href='".((substr($aDataEntry[$k],0,3)=="www") ? "http://".$aDataEntry[$k] : $aDataEntry[$k])."'>".$aDataEntry[$k]."</a>";
				}else{
					$sRet .= $aDataEntry[$k];
				}
			}
            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';

		if($bHasValues) 
			return $sRet;
		else
			return '';

    }
     
   function venue_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');
 
		$aEntry = $this->_oDb->getVenueEntryById($aData['id']); 

        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableVenue, $aEntry)) {            
            $aVars = array ('extra_css_class' => 'bx_events_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_events_max_preview');

        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-venue.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'venue/view/' . $aData['uri'],
            'title' => $aData['title'],
			'country_city' => $this->_oMain->_formatLocation($aData), 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars, $aData['desc']),

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
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');
 
		$aEntry = $this->_oDb->getNewsEntryById($aData['id']);
	 
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableNews, $aEntry)) {            
            $aVars = array ('extra_css_class' => 'bx_events_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
		$iLimitChars = (int)getParam('bx_events_max_preview');
  
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-news.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/view/' . $aData['uri'],
            'title' => $aData['title'],

			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars, $aData['desc']),

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
  
    function sponsor_unit ($aData, $sTemplateName, &$oVotingView) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxEventsModule');
 
		$aEntry = $this->_oDb->getSponsorEntryById($aData['id']);
 
        if (!$this->_oMain->isAllowedViewSubProfile ($this->_oDb->_sTableSponsor, $aEntry)) {            
            $aVars = array ('extra_css_class' => 'bx_events_unit');
            return $this->parseHtmlByName('twig_unit_private', $aVars);
        }
  
        $sImage = '';
        if ($aData['thumb']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['thumb']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 

		$iLimitChars = (int)getParam('bx_events_max_preview');
 
        $aVars = array (            
            'id' => $aData['id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-sponsor.png'),
            'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'sponsor/view/' . $aData['uri'],
            'title' => $aData['title'],

			'country_city' => $this->_oMain->_formatLocation($aData), 
			'snippet_text' => $this->_oMain->_formatSnippetText($aData,$iLimitChars, $aData['desc']),

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
 
	function getFormPackageDesc($iPackageId) {
	    $sCurrencyCode = strtolower($this->_oConfig->getCurrencyCode());
	    $sCurrencySign = $this->_oConfig->getCurrencySign();
  
		$aPackage = $this->_oDb->getPackageById($iPackageId);

		$aVars = array(
			'url_root' => BX_DOL_URL_ROOT,
			'id' => $aPackage['id'],
			'title' => $aPackage['name'],
			'description' => str_replace("\$", "&#36;", $aPackage['description']),
			'days' => $aPackage['days'] > 0 ?  $aPackage['days'] . ' ' . _t('_bx_events_days') : _t('_bx_events_expires_never') ,
			'price' => $aPackage['price'],
			'currency_icon' => $this->getIconUrl($sCurrencyCode . '.png'),
			'currency_sign' => $sCurrencySign, 
			'videos' => ($aPackage['videos']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
			'photos' => ($aPackage['photos']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
			'sounds' => ($aPackage['sounds']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
			'files' => ($aPackage['files']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),
			'featured' => ($aPackage['featured']) ? ucwords(_t('_bx_events_yes')) : ucwords(_t('_bx_events_no')),

		);
 
	    $this->addCss('levels.css');
	    return $this->parseHtmlByName('form_package', $aVars);
	}
 
 	function getPreListDisplay($sField, $sVal){ 
 		return htmlspecialchars_adv( _t($GLOBALS['aPreValues'][$sField][$sVal]['LKey']) );
	}
 
 	function getWebsiteUrl($sField, $sUrl){ 

        $sRealUrl = strncasecmp($sUrl, 'http://', 7) != 0 && strncasecmp($sUrl, 'https://', 8) != 0 ? 'http://' . $sUrl : $sUrl;
		
		$aUrlParts = parse_url($sRealUrl);
		$sDisplayUrl = ($aUrlParts['host']) ? $aUrlParts['host'] : $sRealUrl;
 
		return '<a target=_blank href="'.$sRealUrl.'">'.$sDisplayUrl.'</a>'; 
	}
 
	function getStateName($sCountry, $sState){ 
 
		return $this->_oDb->getStateName($sCountry, $sState);
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
 
	function youtubeIdOLD($url) {
		$v='';
		if (preg_match('%youtube\\.com/(.+)%', $url, $match)) {
			$match = $match[1];
			$replace = array("watch?v=", "v/", "vi/");
			$sQueryString = str_replace($replace, "", $match); 
			$aQueryParams = explode('&',$sQueryString);
			$v = $aQueryParams[0]; 
		}else{ 
			//.$url = parse_url($sVideoEmbed);
			//parse_str($url['query']);
			$video_id = substr( parse_url($url, PHP_URL_PATH), 1 );
			$v = ltrim( $video_id, '/' ); 
		} 

		return $v;  
	}

    function blockCustomSubItemFields (&$aDataEntry, $sType='', $aShow=array()) {
        
		$bHasValues = false;
		
		$sRet = '<table class="bx_events_fields">';
        bx_events_import ($sType.'FormAdd');   
		
		$sClass = 'BxEvents'.$sType.'FormAdd';
        $oForm = new $sClass ($GLOBALS['oBxEventsModule'], $_COOKIE['memberID']);
        foreach ($oForm->aInputs as $k => $a) {
            //if (!isset($a['display'])) continue;
 
            if (!in_array($a['name'],$aShow)) continue;
            
			if (!trim($aDataEntry[$k])) continue;

			$bHasValues = true;

            $sRet .= '<tr><td class="bx_events_field_name bx-def-font-grayed bx-def-padding-sec-right" valign="top">'. $a['caption'] . '<td><td class="bx_events_field_value">';
            if (is_string($a['display']) && is_callable(array($this, $a['display']))){ 

				if($a['name'] == 'state'){
					$sRet .= $this->getStateName($aDataEntry['country'], $aDataEntry[$k]);
				}else{ 
					$sRet .= call_user_func_array(array($this, $a['display']), array($a['listname'],$aDataEntry[$k]));
				}
 			}else{ 
				$sRet .= $aDataEntry[$k];
			}

            $sRet .= '<td></tr>';
        }
        $sRet .= '</table>';

        return ($bHasValues) ? $sRet : '';
    }

    function blockSubProfileInfo ($sType, &$aData, $bShowFields=true) {

		$this->_oMain = BxDolModule::getInstance('BxEventsModule');

        $aAuthor = getProfileInfo($aData['author_id']);
 
        $aVars = array (
            'author_unit' => get_member_thumbnail($aAuthor['ID'], 'none', true),
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
 
			'bx_if:location' => array( 
				'condition' =>  $aData['country'],
				'content' => array(
					'location' => $this->_oMain->_formatLocation($aData, true, true),
  				), 
			),
 
            'fields' => ($bShowFields) ? $this->blockCustomSubItemFields($aData, ucwords($sType)) : '',   
         );

        return $this->parseHtmlByName('block_subprofile_info', $aVars);
    }
 
    function discount_unit ($aData, $sEventDate) {
  
        $aVars = array ( 
            'info' => $aData['info'], 
            'tickets' => $aData['tickets'], 
            'cost' => $aData['cost'], 
            'deadline' => date('Y.m.d', strtotime($aData['deadline'])), 
            'event_date' => date('Y.m.d', $sEventDate), 
        );
 
        return $this->parseHtmlByName('discount_unit', $aVars);
 
    }


}
