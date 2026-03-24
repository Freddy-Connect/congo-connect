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

bx_import('BxDolTwigPageView');

class BxListingPageView extends BxDolTwigPageView {	

	function BxListingPageView(&$oMain, &$aDataEntry) {
		parent::BxDolTwigPageView('modzzz_listing_view', $oMain, $aDataEntry);
	
        $this->sSearchResultClassName = 'BxListingSearchResult';
        $this->sFilterName = 'filter';

        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'view/'. $this->aDataEntry['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  
  
 		$this->sUri = $this->aDataEntry['uri'];  
	}
 
	function getBlockCode_OperatingHours() {
  
 		$aPeriod = $this->_oDb->getPeriodFeed ($this->aDataEntry['id']); 

		if(!count($aPeriod)) return;

		$iCounter=0;
		$aFeeds = array();
		foreach ($aPeriod as $k => $r) {  
 
			$sDay = $this->_oDb->getDays($r['day']);
 
			$iFromHour = $r['from_hour'];
			$iToHour = $r['to_hour'];

			$iFromMinute = $r['from_minute'];
			$iToMinute = $r['to_minute'];

			$sFromPeriod = $this->_oDb->getPeriods($r['from_period']);
			$sToPeriod = $this->_oDb->getPeriods($r['to_period']);
 
			$aFeeds[$k] = array();  
			$aFeeds[$k]['margin'] = ($iCounter) ? '10px' : '0px';
			$aFeeds[$k]['name'] = "$sDay ($iFromHour:$iFromMinute $sFromPeriod "._t('_modzzz_listing_to')." $iToHour:$iToMinute $sToPeriod)";
			$iCounter++;
		}

		$aVarsChoice = array (  
			'bx_repeat:items' => $aFeeds,
		);  
		
		$sEachPeriod = $this->_oTemplate->parseHtmlByName('entry_view_operation_hours', $aVarsChoice);
		return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $sEachPeriod)); 
 	}	
	    
	function getBlockCode_Info() {

        $aData = $this->aDataEntry;
  
        $aAuthor = getProfileInfo($aData['author_id']);
 
		$sAuthorName =  $aAuthor['NickName'];
		$sFirstNameName =  $aAuthor['FirstName'];
		$sAuthorLink = getProfileLink($aAuthor['ID']);	
		$icoThumb = $GLOBALS['oFunctions']->getMemberThumbnail($aAuthor['ID'], 'none', true);
	
		
		$sUrl_compose =  BX_DOL_URL_ROOT  . 'mail.php?mode=compose&recipient_id='.$aData['author_id'] ;
        
		
		
		
		
		
		////freddy jobs integration
		$iId = $aData['id'];
		
		if(getParam('modzzz_listing_jobs_active')=='on'){
			$iJobsCount = $this->_oDb->getModzzzxJobsCount($iId);
		}
		///////////////////////////////////////////////////////
		
		////freddy classified integration
		if(getParam('modzzz_listing_classified_active')=='on'){
			$iClassifiedCount = $this->_oDb->getModzzzxClassifiedCount($iId);
		}
		///////////////////////freddy events integration
		
		if(getParam('modzzz_listing_boonex_events')=='on'){
			$iEventsCount = $this->_oDb->getBoonexEventsCount($iId);
		}else{
			$iEventsCount = (int)$this->aDataEntry['events_count'];
		}
		 $sBusinessFlag = genFlag($aData['country']);
	    $sBusinessCountry= $aData['country'];
         $sBusinessProvince = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sBusinessCountry, $aData['state']) : '';
		 
		 //Freddy ajout Befirend
		$iUserId = getLoggedId();
		$iIdFriend = $aData['author_id'];
		$aFriends = getMyFriendsEx($iUserId);
		$aFriendsIds = array_keys($aFriends);
		// [END] Freddy ajout Befirend
		
	 
        $aVars = array (
		////////FREDDY ADD ///////////////////////////////////////////////////////////////
		   'city' => $aData['city'],
			'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			'province' => $sBusinessProvince,
			'drapeau' => $sBusinessFlag,
		   // 'employees_count' => _t($GLOBALS['aPreValues']['ListingNombreEmployes'][$aData['employees_count']]['LKey']),
			'adresse' => $aData['address1'],
			'businesstelephone' => $aData['businesstelephone'], 
			 'listing_title' => $aData['title'],
			// 'zip' => $aData['zip'],
			
			
			
			 //Freddy ajout Befirend
		 
	 'bx_if:befriend' => array(
	'condition' => !in_array($iIdFriend, $aFriendsIds) && !isFriendRequest($iUserId, $iIdFriend) &&  !$this->_oMain->isEntryAdmin($aData),
	 'content' => array(
	 'FirstName' => $sFirstNameName,
	  'id' => $iIdFriend
	  
					    ),
				           ),
		// [END] Freddy ajout Befirend
		
		
		
		 'bx_if:contacter_url' => array(
	                'condition' =>  !$this->_oMain->isEntryAdmin($aData),
	                'content' => array(
	                'contacter_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'inquire/' . $aData['id'],
					    ),
				           ),
						   
						  
			 
			 
			 'bx_if:employees_count' => array( 
								'condition' =>$aData['employees_count'],
								'content' => array(
              'employees_count' => _t($GLOBALS['aPreValues']['ListingNombreEmployes'][$aData['employees_count']]['LKey']),  
								), 
							),
							
							
			 'bx_if:zip' => array( 
								'condition' =>$aData['zip'],
								'content' => array(
              'zip' => $aData['zip'],  
								), 
							),
			
			 
			'bx_if:avenue' => array( 
								'condition' =>$aData['address2'],
								'content' => array(
              'avenue' => $aData['address2'],  
								), 
							),
							
			'bx_if:LinkedinUrl' => array( 
								'condition' =>$aData['businessfax'],
								'content' => array(
              'LinkedinUrl' => $aData['businessfax'],  
								), 
							),
			
			'bx_if:businesswebsite' => array( 
								'condition' =>$aData['businesswebsite'],
								'content' => array(
              'businesswebsite' => $aData['businesswebsite'],  
								), 
							),
							
			'bx_if:businessemail' => array( 
								'condition' =>$aData['businessemail'],
								'content' => array(
              'businessemail' => $aData['businessemail'],  
								), 
							),
							
				
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			 //freddy ajout --- Afficher le nombre total des offres de formation, articles, events + lien
	
			'bx_if:jobs_count' => array( 
								'condition' => $iJobsCount,
								'content' => array(
			  'jobs_count_url'=>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/browse/' . $aData['uri'],
              'jobs_count' =>  $iJobsCount,  
								), 
							), 
							
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
							
				 ////////////////////[END] FREDDY FIN INTEGRATION///////////////////////////////////////////////
            'author_thumb' => $icoThumb,
			
			
			
            'date' => getLocaleDate($aData['created'], BX_DOL_LOCALE_DATE_SHORT),
            'date_ago' => defineTimeInterval($aData['created']),
            'fields' => '', 
			'bx_if:owner' => array( 
				'condition' =>  $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($aData),
				'content' => array(   
					'expire' => $aData['expiry_date'] ? date('M d, Y', $aData['expiry_date']) : _t('_modzzz_listing_never'),
					'featured' => $aData['featured'] ? ($aData['featured_expiry_date'] ? _t('_modzzz_listing_featured_until') .' '. $this->_oTemplate->filterCustomDate($aData['featured_expiry_date']) : _t('_modzzz_listing_featured_listing')) : _t('_modzzz_listing_not_featured'), 
				),  		
			), 
        );
        return array($this->_oTemplate->parseHtmlByName('entry_view_block_info', $aVars));
    }
 
	function getBlockCode_Desc() {

 		$aCategory = $this->_oDb->getCategoryById($this->aDataEntry['parent_category_id']);
		$sCategoryUrl = $this->_oDb->getCategoryUrl($aCategory['uri']); 
		if($this->aDataEntry['category_id']){

			$aSubCategoryLinks = array();
			$aSubCategories = explode(CATEGORIES_DIVIDER, $this->aDataEntry['category_id']);
			foreach($aSubCategories as $iSubCategoryId){
				$aSubCategory = $this->_oDb->getCategoryById($iSubCategoryId); 
				$sSubCategoryUrl = $this->_oDb->getSubCategoryUrl($aSubCategory['uri']);
				$sSubCategoryTitle = $aSubCategory['name'];
				$aSubCategoryLinks[] = "<a href='$sSubCategoryUrl'>$sSubCategoryTitle</a>";
			}
 
			$sSubCategoryCode = implode('<span class="bullet">&#8594;</span>', $aSubCategoryLinks);
		}
		 $sBusinessTitle = $this->aDataEntry['title'];
		 
		 
		////freddy jobs integration
		$iId =  $this->aDataEntry['id'];
		
		if(getParam('modzzz_listing_jobs_active')=='on'){
			$iJobsCount = $this->_oDb->getModzzzxJobsCount($iId);
		}
		
		if ($iJobsCount >=1){
			$iJobUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/browse/' .  $this->aDataEntry['uri'];
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
			$iClassifiedUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/browse/' .  $this->aDataEntry['uri'];
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
			$iEventdUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/browse/' .  $this->aDataEntry['uri'];
		} else{
			$iEventdUrl  ='javascript:void(0);';
		}
		
		if ($iEventsCount <=1){
			$iEventKey =_t('_modzzz_listing_action_event_single');
		} else{
			$iEventKey =_t('_modzzz_listing_action_event_plural');
		}
   ////////////////////////////////////////////////////////////////
	  
		 $sBusinessFlag = genFlag($this->aDataEntry['country']);
	    $sBusinessCountry= $this->aDataEntry['country'];
         $sBusinessProvince = ($this->aDataEntry['state']) ? $this->_oMain->_oDb->getStateName($sBusinessCountry, $this->aDataEntry['state']) : '';
		 
	
		 

        $aVars = array (
		   
            /*'breadcrumb' => $this->genBreadcrumb($aDataEntry),*/
            'desc' => $this->aDataEntry['desc'],  
			'listing_title' =>  $sBusinessTitle,
			
			 'city' => $this->aDataEntry['city'],
			'country' => _t($GLOBALS['aPreValues']['Country'][$this->aDataEntry['country']]['LKey']),
			'province' => $sBusinessProvince,
			'drapeau' => $sBusinessFlag,
			 'bx_if:zip' => array( 
								'condition' =>$this->aDataEntry['zip'],
								'content' => array(
              'zip' => $this->aDataEntry['zip'],  
								), 
							),
			  'bx_if:businesstelephone' => array( 
								'condition' =>$this->aDataEntry['businesstelephone'],
								'content' => array(
              'businesstelephone' => $this->aDataEntry['businesstelephone'],  
								), 
							),
							
						 
							
							
							
				'bx_if:adresse' => array( 
								'condition' =>$this->aDataEntry['address1'],
								'content' => array(
              'adresse' => $this->aDataEntry['address1'],  
								), 
							),
			
			 
			'bx_if:avenue' => array( 
								'condition' =>$this->aDataEntry['address2'],
								'content' => array(
              'avenue' => $this->aDataEntry['address2'],  
								), 
							),
							
			'bx_if:LinkedinUrl' => array( 
								'condition' =>$this->aDataEntry['businessfax'],
								'content' => array(
              'LinkedinUrl' => $this->aDataEntry['businessfax'],  
								), 
							),
			
			'bx_if:businesswebsite' => array( 
								'condition' =>$this->aDataEntry['businesswebsite'],
								'content' => array(
              'businesswebsite' => $this->aDataEntry['businesswebsite'],  
								), 
							),
							
			'bx_if:businessemail' => array( 
								'condition' =>$this->aDataEntry['businessemail'],
								'content' => array(
              'businessemail' => $this->aDataEntry['businessemail'],  
								), 
							),
							
				
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			 //freddy ajout --- Afficher le nombre total des offres de formation, articles, events + lien
	
			
			'bx_if:jobs_count' => array( 
								'condition' => $iJobsCount,
								'content' => array(
			  'jobs_count_url'=>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'review/browse/' . $aData['uri'],
              'jobs_count' =>  $iJobsCount,  
			  'Job_Single_Plural' => $iJobOfferkey,
								), 
							), 
							
			
							
			'bx_if:events_count' => array( 
								'condition' => $iEventsCount,
								'content' => array(
			  'events_count_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'event/browse/' . $this->aDataEntry['uri'],
              'events_count' =>  $iEventsCount,  
			   'Event_Single_Plural' => $iEventKey,
			  
								), 
							), 
			
			'bx_if:classified_count' => array( 
								'condition' => $iClassifiedCount,
								'content' => array(
			  'classified_count_url'=>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'news/browse/' . $this->aDataEntry['uri'],
              'classified_count' =>  $iClassifiedCount,  
			  'classified_Single_Plural' => $iClassifiedkey,
								), 
							), 
							
				 ////////////////////[END] FREDDY FIN INTEGRATION///////////////////////////////////////////////
            'author_thumb' => $icoThumb,
			
			
			
			
			
			
			
			
			
			
			
			
 
            'bx_if:parent_category' => array (
                'condition' => $this->aDataEntry['parent_category_id'],
                'content' => array ( 
					'category_name' => $aCategory['name'],
					'category_url' => $sCategoryUrl,
                 ),
            ),

            'bx_if:sub_category' => array (
                'condition' => $this->aDataEntry['category_id'],
                'content' => array ( 
					'subcategories' => $sSubCategoryCode,
                 ),
            )
		);	

        return array($this->_oTemplate->parseHtmlByName('block_description', $aVars));
    }

	function getBlockCode_BusinessContact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'businesscontact');
    }

	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }
 
	function getBlockCode_Jobs() {

		if(getParam("modzzz_listing_jobs_active")!='on')  
			return;

		$oJobs = BxDolModule::getInstance('BxJobsModule');

		$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->aDataEntry['uri'];
  	    $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

        $oJobs->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'jobs',
            'jobs',
            $this->_oDb->getParam('modzzz_listing_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	}
	
	//Freddy Integration modzzz articles
	 function getBlockCode_Articles () {
 
      if(getParam("modzzz_listing_modzzz_articles")!='on')  
			return;

		$oArticles = BxDolModule::getInstance('BxArticlesModule');

		$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->aDataEntry['uri'];
  	    $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

        $oArticles->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'articles',
            'articles',
            $this->_oDb->getParam('modzzz_listing_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	}
	
	
	// fin Integration modzzz articles
	// Classified
	function getBlockCode_Classified() {

		if(getParam("modzzz_listing_classified_active")!='on')  
			return;

		$oClassified = BxDolModule::getInstance('BxClassifiedModule');

		$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->aDataEntry['uri'];
  	    $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

        $oClassified->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'classified',
            'classified',
            $this->_oDb->getParam('modzzz_listing_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	}
 
 
	function getBlockCode_Coupons() {

		if(getParam("modzzz_listing_coupons_active")!='on')  
			return;

		$oCoupons = BxDolModule::getInstance('BxCouponsModule');
 
        $oCoupons->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'coupons',
            'coupons',
            $this->_oDb->getParam('modzzz_listing_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	}

	function getBlockCode_Deals() {

		if(getParam("modzzz_listing_deals_active")!='on')  
			return;

		$oDeals = BxDolModule::getInstance('BxDealsModule');

		$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'view/' . $this->aDataEntry['uri'];
  	    $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

        $oDeals->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'deals',
            'deals',
            $this->_oDb->getParam('modzzz_listing_perpage_view_subitems'), 
            array(), $this->aDataEntry['id'], true, false, false 
        );  
	} 

	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType)
		{  
			case "businesscontact":
				$aAllow = array('businessname','businesswebsite','businessemail','businesstelephone','businessfax');
			break;
			case "location":
				$aAllow = array('address1','address2','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry,$aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
  
	function getBlockCode_Media() {
		
		$oModuleDb = new BxDolModuleDb();   
 
        // top menu and sorting
        $aModes = array('photos', 'videos', 'sounds', 'files');
 
        foreach( $aModes as $sMyMode ) { 
			if(!$oModuleDb->isModule($sMyMode)) unset($aModes[$sMyMode]);
		}
  
        $aDBTopMenu = array();
  
        if (empty($_GET['mediaMode'])) {
			$sMode = 'photos';
			if(!in_array($sMode, $aModes)) 
        		$sMode = $aModes[0];  
        } else {
        	$sMode = (in_array($_GET['mediaMode'], $aModes) || $_GET['mediaMode']=='youtube') ? $_GET['mediaMode'] : $sMode = $aModes[0];
        }
   
        foreach( $aModes as $sMyMode ) {
      
			if(!$oModuleDb->isModule($sMyMode)) continue;

			$sModeTitle = _t('_sys_module_'.$sMyMode);
               
            $aDBTopMenu[$sModeTitle] = array('href' => $this->sUrlStart . "mediaMode=$sMyMode", 'dynamic' => true, 'active' => ( $sMyMode == $sMode ));
       }
	   $aDBTopMenu[_t('_modzzz_listing_block_video_embed')] = array('href' => $this->sUrlStart . "mediaMode=youtube", 'dynamic' => true, 'active' => ( 'youtube' == $sMode ));

	   switch($sMode){
			case 'photos':
				$aBlock = $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
			break;
			case 'videos': 
				$aBlock = $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
 			break;
			case 'sounds':
				$aBlock = $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
 			break;
			case 'files':
				$aBlock = $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
 			break; 
			case 'youtube':
				$aBlock = $this->getBlockCode_VideoEmbed();
 			break; 
	   }

	   $aBlock = is_array($aBlock) ? $aBlock : array($aBlock);

	   if($aBlock[0]=='') $aBlock[0] = MsgBox(_t('_Empty'));

	   $aBlock[1] = $aDBTopMenu;

	   return $aBlock;  
    }

	function getBlockCode_Photo() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Video() {
        return $this->_blockVideo ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'videos'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Sound() {
        return $this->_blockSound ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'sounds'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Files() {
        return $this->_blockFiles ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'files'), $this->aDataEntry['author_id']);
    }    

    function getBlockCode_Rate() {
        modzzz_listing_import('Voting');
        $o = new BxListingVoting ('modzzz_listing', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
 
        return array($o->getBigVoting ($this->_oMain->isAllowedRate($this->aDataEntry)));
    }        

    function getBlockCode_Comments() {    
        modzzz_listing_import('Cmts');
        $o = new BxListingCmts ('modzzz_listing', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) return '';
        return $o->getCommentsFirst ();
    }  
	 
    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_listing', '', (int)$this->aDataEntry['id']);
			
			$isFan = $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

			$isEmployee = $this->_oDb->isEmployee((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isEmployee((int)$this->aDataEntry['id'], $this->_oMain->_iProfileId, 1);

			$sTitleWork = $this->_oMain->isAdmin() ? _t('_modzzz_listing_action_title_work_admin') : _t('_modzzz_listing_action_title_work');
  
            $aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                'ScriptSubscribe' => $aSubscribeButton['script'],
                'TitleSubscribe' => $aSubscribeButton['title'], 
                'TitleEdit' => $this->_oMain->isAllowedEdit($this->aDataEntry) ? _t('_modzzz_listing_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_listing_action_title_delete') : '',
                'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_listing_action_title_share') : '',
                'TitleBroadcast' => $this->_oMain->isAllowedBroadcast($this->aDataEntry) ? _t('_modzzz_listing_action_title_broadcast') : '',  
         
				'TitleEmployeeAdmin' => $this->_oMain->isEntryAdmin($this->aDataEntry) ? $sTitleWork : '',
 
				'TitleEmployee' => ($isEmployee) ? _t('_modzzz_listing_action_title_resign') : ((!$this->_oMain->isEntryAdmin($this->aDataEntry)) && $this->_oMain->isAllowedWork($this->aDataEntry) ? $sTitleWork : ''),
 
				/*'TitleEmployee' => $this->_oMain->isAllowedWork($this->aDataEntry) ? ($isEmployee ? _t('_modzzz_listing_action_title_resign') : $sTitleWork) : '',*/

                'IconWork' => $isEmployee ? 'sign-out' : 'sign-in',

                'TitleManageEmployees' => $this->_oMain->isAllowedManageEmployees($this->aDataEntry) ? _t('_modzzz_listing_action_manage_employees') : '',

				'TitleJoin' => $this->_oMain->isAllowedJoin($this->aDataEntry) ? ($isFan ? _t('_modzzz_listing_action_title_leave') : _t('_modzzz_listing_action_title_join')) : '',
                'IconJoin' => $isFan ? 'sign-out' : 'sign-in',

				'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_listing_action_title_promote') : '',  
				'TitleClaim' => $this->_oMain->isAllowedClaim($this->aDataEntry) ? _t('_modzzz_listing_action_title_claim') : '',
                'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_listing_action_title_inquire') : '',
				'AddToFeatured' => $this->_oMain->isAllowedMarkAsFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_listing_action_remove_from_featured') : _t('_modzzz_listing_action_add_to_featured')) : '',

				'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($this->aDataEntry) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $this->aDataEntry['id']) ? _t('_modzzz_listing_action_remove_from_favorite') : _t('_modzzz_listing_action_add_to_favorite')) : '',

                'TitlePostReview' => $this->_oMain->isAllowedPostReviews($this->aDataEntry) ? _t('_modzzz_listing_action_title_post_review') : '',
                'TitleEventAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_listing_action_title_add_event') : '',
                'TitleNewsAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_listing_action_title_add_news') : '',
			  
                'TitleManageFans' => $this->_oMain->isAllowedManageFans($this->aDataEntry) ? _t('_modzzz_listing_action_manage_fans') : '',
				
				'TitlePurchaseFeatured' => '',

				/*'TitlePurchaseFeatured' => $this->_oMain->isAllowedPurchaseFeatured($this->aDataEntry) ? ($this->aDataEntry['featured'] ? _t('_modzzz_listing_action_title_extend_featured') : _t('_modzzz_listing_action_title_purchase_featured')) : '',*/
		 
                'TitleRelist' => $this->_oMain->isAllowedRelist($this->aDataEntry) ? _t('_modzzz_listing_action_title_relist') : '',
                'TitleExtend' => $this->_oMain->isAllowedExtend($this->aDataEntry) ? _t('_modzzz_listing_action_title_extend') : '',
                'TitlePremium' => $this->_oMain->isAllowedPremium($this->aDataEntry) ? _t('_modzzz_listing_action_title_premium') : '',


				'TitleUploadPhotos' => $this->_oMain->isAllowedUploadPhotos($this->aDataEntry) ? _t('_modzzz_listing_action_upload_photos') : '',
                'TitleUploadVideos' => $this->_oMain->isAllowedUploadVideos($this->aDataEntry) ? _t('_modzzz_listing_action_upload_videos') : '',
                'TitleEmbed' => $this->_oMain->isAllowedEmbed($this->aDataEntry) ? _t('_modzzz_listing_action_embed_video') : '', 
				'TitleUploadSounds' => $this->_oMain->isAllowedUploadSounds($this->aDataEntry) ? _t('_modzzz_listing_action_upload_sounds') : '',
                'TitleUploadFiles' => $this->_oMain->isAllowedUploadFiles($this->aDataEntry) ? _t('_modzzz_listing_action_upload_files') : '',
 
                'TitleAddDeal' => $this->_oMain->isAllowedModulePost('deals') ? _t('_modzzz_listing_action_title_add_deal') : '', 
                'TitleAddJob' => $this->_oMain->isAllowedModulePost('jobs') ? _t('_modzzz_listing_action_title_add_job') : '', 
				 'TitleAddClassified' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_listing_action_title_add_classified') : '', 
				 
				  'TitleArticlesAdd' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_listing_action_title_add_articles') : '',
				 'TitleAddJob' => ($this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry)) ? _t('_modzzz_listing_action_title_add_job') : '', 
				
				
                'TitleAddCoupon' => $this->_oMain->isAllowedModulePost('coupons') ? _t('_modzzz_listing_action_title_add_coupon') : '', 

            );

            if (!$aInfo['TitleEventAdd'] && !$aInfo['TitleNewsAdd'] && !$aInfo['TitlePostReview'] && !$aInfo['TitleEdit'] && !$aInfo['TitleDelete'] && !$aInfo['TitleShare'] && !$aInfo['TitleJoin'] && !$aInfo['TitleWork'] && !$aInfo['TitleInvite'] && !$aInfo['TitleManageFans'] && !$aInfo['TitleManageEmployees'] && !$aInfo['TitlePurchaseFeatured'] && !$aInfo['AddToFeatured'] && !$aInfo['TitleUploadPhotos'] && !$aInfo['TitleUploadVideos'] && !$aInfo['TitleEmbed'] && !$aInfo['TitleUploadSounds'] && !$aInfo['TitleUploadFiles'] && !$aInfo['TitleClaim'] && !$aInfo['TitleInquire'] && !$aInfo['TitleAddDeal'] && !$aInfo['TitleAddJob'] && !$aInfo['TitleAddClassified'] && !$aInfo['TitleArticlesAdd']  && !$aInfo['TitleAddCoupon']) 
                return '';

            return $oSubscription->getData() . $oFunctions->genObjectsActions($aInfo, 'modzzz_listing');
        } 

        return '';
    }    
 
	function getBlockCode_Local() {  
		return $this->ajaxBrowse('other_local', $this->_oDb->getParam('modzzz_listing_perpage_main_recent'),array(),$this->aDataEntry['id'],$this->aDataEntry['city'],$this->aDataEntry['state']);   
 	}

	function getBlockCode_Other() {    
		return $this->ajaxBrowse('other', $this->_oDb->getParam('modzzz_listing_perpage_main_recent'),array(),$this->aDataEntry['author_id'],$this->aDataEntry['id']); 
	}

    function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $isDisableRss = false, $isPublicOnly = true, $bShowAll=true) {
        $oMain = BxDolModule::getInstance('BxListingModule');

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
            return '';
 
        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl, -1, -1, $bShowAll);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }   

    function getBlockCode_Fans() {
        return parent::_blockFans ($this->_oDb->getParam('modzzz_listing_perpage_view_fans'), 'isAllowedViewFans', 'getFans');
    }            

    function getBlockCode_FansUnconfirmed() {
        return parent::_blockFansUnconfirmed (BX_LISTING_MAX_FANS);
    }

    function getCode() {
 
        $this->_oMain->_processFansActions ($this->aDataEntry, BX_LISTING_MAX_FANS);
        $this->_oMain->_processEmployeesActions ($this->aDataEntry, BX_LISTING_MAX_EMPLOYEES);

        return parent::getCode();
    }

	function getBlockCode_Reviews () {
 
        $this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'review/browse/' . $this->sUri;
  	    $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

        $this->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'review',
            'reviews',
            $this->_oDb->getParam('modzzz_listing_perpage_browse_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }
   
   function ajaxBrowseSubProfile($sType, $sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true, $bShowAll=true) {

        bx_import ('SearchResult', $this->_oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);
 
        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);
 
        if (!($s = $o->displaySubProfileResultBlock($sType))) {
			 return;
             //return array(MsgBox(_t('_Empty')), $aMenu);
		} 

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->_oConfig->getBaseUri() . $o->sBrowseUrl, -1, -1, $bShowAll);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    }    
 
 	function getBlockCode_Tags() {

		if(!trim($this->aDataEntry['tags'])) return;

        $aVars = array (
            'tags' => $this->_oTemplate->parseTags($this->aDataEntry['tags'], 'topic-link topic-hover'),
        );

        return array($this->_oTemplate->parseHtmlByName('entry_view_tags', $aVars));
	}

	function getBlockCode_Forum() {
  		 
		$iNumComments = getParam("modzzz_listing_perpage_main_comment");
		$aPosts = $this->_oDb->getLatestForumPosts($iNumComments, $this->aDataEntry['id']);
  
		if(empty($aPosts)) return;
			 
		$aVars['bx_repeat:entries'] = array();
  		foreach($aPosts as $aEachPost){

			$sForumUri = $aEachPost['forum_uri'];
			$sTopic = $aEachPost['topic_title']; 
			$sTopicUri = $aEachPost['topic_uri'];
			$sPostText = $aEachPost['post_text']; 
			$sDate = defineTimeInterval($aEachPost['when']); 
			$sListingName = $aEachPost['title']; 
 			$sPoster = $aEachPost['user']; 

			$iLimitChars = (int)getParam('modzzz_listing_forum_max_preview');
			$sPostText = $this->_oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
 			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sListingUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/listing/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 
							'item_title' => $sListingName, 
							'item_url' => $sListingUrl, 
							'created' => $sDate,
							'author_url' => getProfileLink(getID($sPoster)),
							'author' => $sPoster,
							'thumb_url' => $sMemberThumb,
						);
		}

		$sCode = $this->_oTemplate->parseHtmlByName('block_forum', $aVars);  

		return $sCode;
	}
  
    function getBlockCode_Employees() {
        return $this->_blockEmployees ($this->_oDb->getParam('modzzz_listing_perpage_view_employees'), 'isAllowedViewEmployees', 'getEmployees');
    }            

    function getBlockCode_EmployeesUnconfirmed() {
        return $this->_blockEmployeesUnconfirmed (BX_LISTING_MAX_EMPLOYEES);
    }

   function _blockEmployees($iPerPage, $sFuncIsAllowed = 'isAllowedViewEmployees', $sFuncGetEmployees = 'getEmployees')
    {
        if (!$this->_oMain->$sFuncIsAllowed($this->aDataEntry))
            return '';

        $iPage = (int)$_GET['page'];
        if( $iPage < 1)
            $iPage = 1;
        $iStart = ($iPage - 1) * $iPerPage;

        $aProfiles = array ();
        $iNum = $this->_oDb->$sFuncGetEmployees($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], true, $iStart, $iPerPage);
        if (!$iNum || !$aProfiles)
            return MsgBox(_t("_Empty"));
        $iPages = ceil($iNum / $iPerPage);

        bx_import('BxTemplSearchProfile');
        $oBxTemplSearchProfile = new BxTemplSearchProfile();
        $sMainContent = '';
        foreach ($aProfiles as $aProfile) {
            $sMainContent .= $oBxTemplSearchProfile->displaySearchUnit($aProfile, array ('ext_css_class' => 'bx-def-margin-sec-top-auto'));
        }
        $ret .= $sMainContent;

        $aDBBottomMenu = array();
        if ($iPages > 1) {
            $sUrlStart = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/".$this->aDataEntry[$this->_oDb->_sFieldUri];
            $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');
            if ($iPage > 1)
                $aDBBottomMenu[_t('_Back')] = array('href' => $sUrlStart . "page=" . ($iPage - 1), 'dynamic' => true, 'class' => 'backMembers', 'icon' => getTemplateIcon('sys_back.png'), 'icon_class' => 'left', 'static' => false);
            if ($iPage < $iPages) {
                $aDBBottomMenu[_t('_Next')] = array('href' => $sUrlStart . "page=" . ($iPage + 1), 'dynamic' => true, 'class' => 'moreMembers', 'icon' => getTemplateIcon('sys_next.png'), 'static' => false);
            }
        }

        $ret .= '<div class="clear_both"></div>';

        return array($ret, array(), $aDBBottomMenu);
    }

    function _blockEmployeesUnconfirmed($iEmployeesLimit = 1000)
    {
        if (!$this->_oMain->isEntryAdmin($this->aDataEntry))
            return '';

        $aProfiles = array ();
        $iNum = $this->_oDb->getEmployees($aProfiles, $this->aDataEntry[$this->_oDb->_sFieldId], false, 0, $iEmployeesLimit);
        if (!$iNum)
            return MsgBox(_t('_Empty'));

        $sActionsUrl = BX_DOL_URL_ROOT . $this->_oMain->_oConfig->getBaseUri() . "view/" . $this->aDataEntry[$this->_oDb->_sFieldUri] . '?ajax_employee_action=';
        $aButtons = array (
            array (
                'type' => 'submit',
                'name' => 'employees_reject',
                'value' => _t('_sys_btn_fans_reject'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_employees_content', '{$sActionsUrl}reject&ids=' + sys_manage_items_get_unconfirmed_employees_ids(), false, 'post'); return false;\"",
            ),
            array (
                'type' => 'submit',
                'name' => 'employees_confirm',
                'value' => _t('_sys_btn_fans_confirm'),
                'onclick' => "onclick=\"getHtmlData('sys_manage_items_unconfirmed_employees_content', '{$sActionsUrl}confirm&ids=' + sys_manage_items_get_unconfirmed_employees_ids(), false, 'post'); return false;\"",
            ),
        );
        bx_import ('BxTemplSearchResult');
        $sControl = BxTemplSearchResult::showAdminActionsPanel('sys_manage_items_unconfirmed_employees', $aButtons, 'sys_employee_unit');
        $aVars = array(
            'suffix' => 'unconfirmed_employees',
            'content' => $this->_oMain->_profilesEdit($aProfiles),
            'control' => $sControl,
        );
        return $this->_oMain->_oTemplate->parseHtmlByName('manage_items_form', $aVars);
    }
 
    function getBlockCode_News () {
 
		$this->_oTemplate->addCss(array('unit.css','twig.css'));

        return $this->ajaxBrowseSubProfile(
            'news',
            'news',
            $this->_oDb->getParam('modzzz_listing_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }
 
    function getBlockCode_Events () {
   
		if(getParam('modzzz_listing_boonex_events')=='on'){  
			$oEvent = BxDolModule::getInstance('BxEventsModule');
			$oEvent->_oTemplate->addCss(array('unit.css','twig.css'));
		}else{
			$this->_oTemplate->addCss(array('unit.css','twig.css'));
		}

        return $this->ajaxBrowseSubProfile(
            'event',
            'events',
            $this->_oDb->getParam('modzzz_listing_perpage_view_subitems'), 
            array(), $this->sUri, true, false 
        ); 
    }
 
	function getBlockCode_VideoEmbed() {

		$aVideoUrls = $this->_oDb->getYoutubeVideos($this->aDataEntry['id']);
		
		$sFirstVideoId = '';
		$sFirstVideoTitle = '';
		$aVideos = array();
		if(empty($aVideoUrls))
			return;

		foreach($aVideoUrls as $aEachUrl){  
			$sFirstVideoId = ($sFirstVideoId) ? $sFirstVideoId : $this->_oTemplate->youtubeId($aEachUrl['url']);
			$sFirstVideoTitle = ($sFirstVideoTitle) ? $sFirstVideoTitle : $aEachUrl['title'];
			$aVideos[] = array ( 
				'video_id' => $this->_oTemplate->youtubeId($aEachUrl['url']), 
				'video_title' => process_db_input($aEachUrl['title']), 
			);
		}

		$aVars = array(
			'video_id' => $sFirstVideoId,
			'video_title' => $sFirstVideoTitle,
			'bx_repeat:video' => $aVideos
		);
		 
        return $this->_oTemplate->parseHtmlByName('block_youtube_videos', $aVars);   
    }

	function getBlockCode_Logo() {  
		
		if(!$sIcon = $this->aDataEntry['icon']) return;
		
	
			 
		$aVars = array (
		    'image' => $this->_oDb->getLogo($this->aDataEntry['id'], $sIcon),
			
			
			
			
			'bx_if:remove' => array( 
				'condition' => $this->_oMain->isAdmin() || $this->_oMain->isEntryAdmin($this->aDataEntry),
				'content' => array( 
					'url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() .  'logo/remove/' . $this->aDataEntry['id'],  
					'changer_logo' =>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $this->aDataEntry['id'],
				), 
			) 
 		);
		return $this->_oTemplate->parseHtmlByName('block_logo', $aVars);	 
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////
	
	
	function getBlockCode_ManagePageUser() {
	 if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxListingModule');
    
		 
		 $aData = $this->aDataEntry;
		  $aProfileInfo = getProfileInfo($iProfileId);
		 
		 $aAuthor = getProfileInfo($aData['author_id']);
		$sAuthorLink = getProfileLink($aProfileInfo['ID']);	
		$sUserThumbnail = $GLOBALS['oFunctions']->getMemberAvatar($aProfileInfo['ID'], 'small');
		 
	 
	  global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {

            $oSubscription = new BxDolSubscription();
            $aSubscribeButton = $oSubscription->getButton($this->_oMain->_iProfileId, 'modzzz_listing', '', (int)$aData['id']);
			
			$isFan = $this->_oDb->isFan((int)$aData['id'], $this->_oMain->_iProfileId, 0) || $this->_oDb->isFan((int)$aData['id'], $this->_oMain->_iProfileId, 1);
	 
		
	$add_favorite_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'mark_favorite/' . $aData['id'];
	
	$showPopupAnyHtml = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'share_popup/' . $aData['id'];
	
	$Join_Suivre= BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'join/'. $aData['id'] . '/$this->_oMain->_iProfileId';
	
	$Renseignement_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'inquire/' . $aData['id'];
     $sBusinessFlag = genFlag($aData['country']);
	    $sBusinessCountry= $aData['country'];
         $sBusinessProvince = ($aData['state']) ? $this->_oMain->_oDb->getStateName($sBusinessCountry, $aData['state']) : '';
		 
		
  
		$aVars = array(  
		
		
		
		      'city' => $aData['city'],
			'country' => _t($GLOBALS['aPreValues']['Country'][$aData['country']]['LKey']),
			'province' => $sBusinessProvince,
			'drapeau' => $sBusinessFlag,
			 'zip' => $aData['zip'],  
			'adresse1' => $aData['address1'],
			'adresse2' => $aData['address2'],
			
			'bx_if:businesswebsite' => array( 
								'condition' => $aData['businesswebsite'],
								'content' => array(
			 
              'businesswebsite' =>  $aData['businesswebsite'],
								), 
							), 
							
			 'businessTelephone' =>  $aData['businesstelephone'],
			 'businessEmail' =>  $aData['businessemail'],
							
			
			 
		
		       'thumbnail_url' => $sUserThumbnail,
			   'Profile_Link' => $sAuthorLink,
			   
			     'TitleInquire' => $this->_oMain->isAllowedInquire($this->aDataEntry) ? _t('_modzzz_listing_action_title_inquire') : '',
				  'InquireUrl' =>$Renseignement_url,

			   
			   
			   'TitleShare' => $this->_oMain->isAllowedShare($this->aDataEntry) ? _t('_modzzz_jobs_action_title_share') : '',
			   'SharePopup' => $showPopupAnyHtml,
			   
			   
			    //'iViewer' => $this->_oMain->_iProfileId,
			   'TitleJoin' => $this->_oMain->isAllowedJoin($aData) ? ($isFan ? _t('_modzzz_listing_action_title_leave') : _t('_modzzz_listing_action_title_join')) : '',
			   
			   'Join_Suivre' => $Join_Suivre,
			   
			    
			   
			 'AddToFavorite' => $this->_oMain->isAllowedMarkAsFavorite($aData) ? ($this->_oMain->isFavorite($this->_oMain->_iProfileId, $aData['id']) ? _t('_modzzz_listing_action_remove_from_favorite') : _t('_modzzz_listing_action_add_to_favorite')) : '',
			 
			  'Favorite_Url' =>  $add_favorite_url, 
			  'id' => $aData['id'],
			  
			
			  
			 

  		);
		
  
		return $this->_oTemplate->parseHtmlByName('block_manage_page', $aVars);   
	}
	
	}
	
	
	////////////////////////////////////////////////////////////////////////
	
	function getBlockCode_ManageMyEntreprise() {
		
    
		if(!$this->_oMain->isAllowedEdit($this->aDataEntry)) return; 
		
		
		//$add_staff_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'staff/add/' . $this->aDataEntry['id']; 
		
		$add_photos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_photos/' . $this->aDataEntry['uri']; 
		$add_files_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'upload_files/' . $this->aDataEntry['uri']; 
		$embed_videos_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'embed/' . $this->aDataEntry['uri']; 
		
		$modifier_edit_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'edit/' . $this->aDataEntry['id'];
		$add_listing_cover_url = BX_DOL_URL_ROOT . 'modules/?r=' . 'listingcover/' . 'cover/' . 'add/' . $this->aDataEntry['id']; 
		$add_listing_enventr_url = BX_DOL_URL_ROOT . 'm/' . 'events/' . 'browse/' . 'my&bx_events_filter=' . 'add_event&listing=' . $this->aDataEntry['id'];
		$broadcast_url = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'broadcast/' . $this->aDataEntry['id']; 

		$InviteUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'invite/' . $this->aDataEntry['id'];
		
		$aVars = array(  
			
			   'add_photos_url' => $add_photos_url, 
			   'embed_videos' => $embed_videos_url, 
			   'add_files_url' => $add_files_url, 
			   'modifier_edit_url' =>  $modifier_edit_url, 
			   'add_listing_cover_url' =>  $add_listing_cover_url, 
			   'add_listing_enventr_url' =>  $add_listing_enventr_url, 
			    'broadcast' =>  $broadcast_url, 
				  
			// 'TitleInvite' => $this->_oMain->isAllowedSendInvitation($this->aDataEntry) ? _t('_modzzz_listing_action_title_promote') : '',  
			  'InviteUrl' => $InviteUrl,
			  
			   'TitleDelete' => $this->_oMain->isAllowedDelete($this->aDataEntry) ? _t('_modzzz_listing_action_title_delete') : '',
			   'supprimer_edit_url' =>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'delete/' . $this->aDataEntry['id'], 
			  'id' =>$this->aDataEntry['id'],
			
			 
  		);
		
		
		return $this->_oTemplate->parseHtmlByName('block_manage_listing', $aVars);   
	}
	/******************FIN FREDDY MANAGE classified****************/
	
	
	////////////////////////////////////////////////////////////////////////




}
