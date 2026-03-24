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

bx_import ('BxDolTwigPageMain');

class BxEventsPageMain extends BxDolTwigPageMain {	

    function BxEventsPageMain(&$oEventsMain) {      
		
		$this->oMain = $oEventsMain;

        parent::BxDolTwigPageMain('bx_events_main', $oEventsMain);
        $this->sSearchResultClassName = 'BxEventsSearchResult';
        $this->sFilterName = 'bx_events_filter';
	}
	
	
	
	
	
	 // Freddy ajout function ajaxBrowseupcomingevent; Cette function a été copiée de /inc/classes/BxDolTwigPageMain.php
	  //Le but c'est de ne pas afficher les block dans Business Listing Home quand c'est vide
	 function ajaxBrowseupcomingevent($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
      // Freddy ajout $oMain = BxDolModule::getInstance('BxEventsModule');
	  $oMain = BxDolModule::getInstance('BxEventsModule');
	    bx_import ('SearchResult', $this->oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage;
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('_RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => 'rss')));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock()))
           
		    return $isPublicOnly ? array(MsgBox(_t('_Empty_Upcoming_Events')), $aMenu) : '';
		   return ;

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s,
            $aMenu,
            $sAjaxPaginate,
            '');
    }
	
	
	
	
	
	 // Freddy ajout function ajaxBrowse; Cette function a été copiée de /inc/classes/BxDolTwigPageMain.php
	  //Le but c'est de ne pas afficher les block dans Business Listing Home quand c'est vide
	 function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
      // Freddy ajout $oMain = BxDolModule::getInstance('BxEventsModule');
	  $oMain = BxDolModule::getInstance('BxEventsModule');
	    bx_import ('SearchResult', $this->oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue);
        $o->aCurrent['paginate']['perPage'] = $iPerPage;
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('_RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => 'rss')));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock()))
           //Freddy mise en commentaire
		   // return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';
		   return ;

        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->oConfig->getBaseUri() . $o->sBrowseUrl);

        return array(
            $s,
            $aMenu,
            $sAjaxPaginate,
            '');
    }
	
	
	
 
   /* Freddy modif pour appilique ajaxBrowseupcomingevent
    function getBlockCode_UpcomingList() {
        return $this->ajaxBrowse('upcoming', $this->oDb->getParam('bx_events_perpage_main_upcoming'));
    }
	*/
	
	/// freddy modif
	 function getBlockCode_UpcomingList() {
        return $this->ajaxBrowse('upcoming', $this->oDb->getParam('bx_events_perpage_main_upcoming'));
    }

    function getBlockCode_PastList() { 
        return $this->ajaxBrowse('past', $this->oDb->getParam('bx_events_perpage_main_past'));
    }        

    function getBlockCode_RecentlyAddedList() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('bx_events_perpage_main_recent'));
    }    

    //[begin] - ultimate events mod from modzzz    
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'bx_events',
            'orderby' => 'popular',
			'pagination' => getParam('tags_perpage_browse')
        );

		$sUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home';
  
        $oTags = new BxTemplTags();
        $oTags->getTagObjectConfig();
    
        return array(
            $oTags->display($aParam, $iBlockId, '', $sUrl),
            array(),
            array(),
            _t('_Tags')
        ); 

   }
 
    function getBlockCode_Categories() {
		bx_import('BxTemplCategories');
  		
		$sType = 'bx_events';
		
		$oCateg = new BxTemplCategories();
		$oCateg->getTagObjectConfig();

	    $aAllEntries = $this->oDb->getCategories($sType);
    
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->oDb->getCategoryCount($sType,$aEntry['Category']);	
	
			$sHrefTmpl = $oCateg->getHrefWithType($sType);  
			$sCategory = $aEntry['Category'];
            $sCatHref = str_replace( '{tag}', urlencode(title2uri($sCategory)), $sHrefTmpl);
 
	        $aResult['bx_repeat:entries'][] = array(
                'cat_url' => $sCatHref, 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory, 
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('event_categories', $aResult);  
	}
 
	function getBlockCode_Activities() {
		$iNumEntries = getParam("bx_events_perpage_main_feed"); 
		$aActivity = $this->oDb->getActivityFeed($iNumEntries);
        
		if(!count($aActivity))
			return;

		$aResult['bx_repeat:entries'] = array();  
 		foreach($aActivity as $aEntry){
			 
			$iEventId = $aEntry['event_id'];
			$sLangKey = _t($aEntry['lang_key']);
			$sParams = $aEntry['params'];
			$iActionDate = $aEntry['date'];

			$aDbParams = explode(";", $sParams);
			$aParams = array();
			foreach($aDbParams as $aEachParam) {
			
				$aParamItems = explode("|", $aEachParam);
				$sKey = $aParamItems[0];
				$sValue = $aParamItems[1];
				$aParams[$sKey] = $sValue;
			
				$sLangKey = str_replace('{'.$sKey.'}', $sValue, $sLangKey); 
			}
		  
			$aResult['bx_repeat:entries'][] = array(
			    'thumbnail' => $GLOBALS['oFunctions']->getMemberIcon($aParams['profile_id'], 'left'), 
 			    'description' => $sLangKey, 
 			    'date' => defineTimeInterval($iActionDate),  
			);	  
	    }

	    return $this->oTemplate->parseHtmlByName('event_activities', $aResult);  
	}

	function getBlockCode_Comments() { 
	  
		$iNumComments = getParam("bx_events_perpage_main_comment");
		$aAllEntries = $this->oDb->getLatestComments($iNumComments);

		if(!count($aAllEntries)) return; 
			
		$aVars = array (
			'bx_repeat:comments' => array (),
		);

		foreach($aAllEntries as $aEntry) {
		   
			$iMemberID = $aEntry['cmt_author_id'];
			$sNickName = getNickName($iMemberID);
			$sNickLink = getProfileLink($iMemberID);
			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail($iMemberID);
			$sMessage = $aEntry['cmt_text']; 
			$dtSent = defineTimeInterval($aEntry['date']);
			$iEventId = $aEntry['cmt_object_id']; 
	 
			$sImage = '';
			if ($aEntry['thumb']) {
				$a = array ('ID' => $aEntry['author_id'], 'Avatar' => $aEntry['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
 
			$iLimitChars = (int)getParam('bx_events_comments_max_preview');

			$sMessage = $this->oMain->_formatSnippetText($aEntry, $iLimitChars, $sMessage);
 
			$aEvent = $this->oDb->getEntryById($iEventId);
			$sEventUri = $aEvent['EntryUri'];
			$sEventTitle = $aEvent['Title'];
 
			$sEventUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sEventUri;
   
			$aVars['bx_repeat:comments'][] = array (
				'thumb_url' => $sMemberThumb,
				'author_url' => $sNickLink,
				'author' => $sNickName,
				'created' => $dtSent,
				'snippet_text' => $sMessage,
				'item_url' => $sEventUrl,
				'item_title' => $sEventTitle,
 			);  
		}
 
		return $this->oTemplate->parseHtmlByName('block_comments', $aVars); 
	}
 
	function getBlockCode_Forum() {
  		 
		$iNumComments = getParam("bx_events_perpage_main_comment");
		$aPosts = $this->oDb->getLatestForumPosts($iNumComments);
  
		if(empty($aPosts))
			return;

		$aVars['bx_repeat:entries'] = array();
  		foreach($aPosts as $aEachPost){

			$sForumUri = $aEachPost['forum_uri'];
			$sTopic = $aEachPost['topic_title']; 
			$sTopicUri = $aEachPost['topic_uri'];
			$sPostText = $aEachPost['post_text']; 
			$sDate = defineTimeInterval($aEachPost['when']); 
			$sEventName = $aEachPost['Title']; 
 			$sPoster = $aEachPost['user']; 

			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$iLimitChars = (int)getParam('bx_events_forum_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sEventUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/events/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 
 
							'bx_if:main' => array( 
								'condition' => true,
								'content' => array( 
									'item_title' => $sEventName, 
									'item_url' => $sEventUrl, 
								), 
							), 

							'created' => $sDate,
							'author_url' => getProfileLink(getID($sPoster)),
							'author' => $sPoster,
							'thumb_url' => $sMemberThumb,
						);
		}

		$sCode = $this->oTemplate->parseHtmlByName('block_forum', $aVars);  

		return $sCode;
	}



	function getBlockCode_Create() {
   		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&bx_events_filter=add_event'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_event', $aVars);  

		return $sCode;
	}

	function getBlockCode_Featured() { 
		return $this->ajaxBrowse('featured', $this->oDb->getParam('bx_events_perpage_main_featured'));
	} 

	function getBlockCode_PopularList() { 
		return $this->ajaxBrowse('popular_short', $this->oDb->getParam('bx_events_perpage_main_popular'));
	}     

	function getBlockCode_TopList() { 
		return $this->ajaxBrowse('top_short', $this->oDb->getParam('bx_events_perpage_main_top'));
	}     
 
    function getBlockCode_States() {
		$iProfileId = getLoggedId();

		$aProfile = ($iProfileId) ? getProfileInfo($iProfileId) : array(); 
		$sCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('bx_events_default_country');
 
		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$sCountry]['LKey']);

		$aStates = $this->oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$sCountry}' ORDER BY `State`");
		  
		if(!count($aStates))
			return;
 
		$aVars = array();
		$aVars['country_name'] = $sCountryName;
		$aVars['bx_repeat:entries'] = array(); 
		
  		foreach($aStates as $aEachState){
			 
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];

			$sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'local/' . $sCountry .'/'. $sStateCode;
  		
			$iNumCategory = $this->oDb->getStateCount($sStateCode);	 

			$aVars['country_name'] = $sCountryName;

			$aVars['bx_repeat:entries'][] = array(
		 
				'bx_if:selstate' => array( 
					'condition' => ($sStateCode == $this->sState),
					'content' => array( 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumCategory,  
					), 
				), 
				'bx_if:regstate' => array( 
					'condition' => ($sStateCode != $this->sState),
					'content' => array( 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumCategory,   
					), 
				), 

			 ); 
	    } 
 
	    return $this->oTemplate->parseHtmlByName('block_states_main', $aVars);   
	}
  
	function getBlockCode_Search() {
   
        $this->oTemplate->pageStart();
 
        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('bx_events', (int)$iProfileId, true);
		$aCategories[''] = _t('_bx_events_all_categories');  
 
		$sStateUrl = bx_append_url_params(BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home', 'ajax=state&country=');

        bx_import('BxDolProfileFields'); 
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
        $aCountries = array_merge (array('' => _t('_bx_events_all_countries')), $aCountries);

        $aForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_events',
                'action'   => '',
                'method'   => 'get',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
                'csrf' => array(
                    'disable' => true,
                ),
            ),
                  
            'inputs' => array(
               /* 'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_bx_events_caption_keyword'),
                    'required' => false,
					
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_bx_events_err_keyword'),
                    ),
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ), 
				*/               
                'Country' => array(
  					'type' => 'select',
                     'name' => 'Country',
                    'caption' => _t('_bx_events_form_caption_country'),
                    'values' => $aCountries,
  					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),                    
                ),
  				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_bx_events_caption_state'),
					'attrs' => array(
						'id' => 'substate',
					), 
					'required' => false, 
					'db' => array (
						'pass' => 'Preg', 
						'params' => array('/([a-zA-Z]+)/'),
					), 
				),	
				'Categories' => array(
					'type' => 'select_box',
					'name' => 'Categories',
					'caption' => _t('_bx_events_caption_categories'),
					'values' => $aCategories,
					'required' => false, 
					'db' => array (
						'pass' => 'Xss', 
					),                    
				),  					 
				/*'City' => array(
					'type' => 'text',
					'name' => 'City',
					'caption' => _t('_bx_events_form_caption_city'),
					'required' => false, 
					'db' => array (
						'pass' => 'Xss', 
					),                
				),
				'EventStart' => array(
					'type' => 'datetime',
					'name' => 'EventStart',
					'caption' => _t('_bx_events_caption_event_start'),
					'required' => false, 
					'db' => array (
						'pass' => 'DateTime', 
					),    
					'display' => 'filterDate',
				),                                
				'EventEnd' => array(
					'type' => 'datetime',
					'name' => 'EventEnd',
					'caption' => _t('_bx_events_caption_event_end'),
					'required' => false, 
					'db' => array (
						'pass' => 'DateTime', 
					),                    
					'display' => 'filterDate',
				),  
				*/
				
/*
				'Public' => array(
					'type' => 'select',
					'name' => 'Public',
					'caption' => _t('_bx_events_caption_public_only'),
					'values' => array(
						1=>_t('_bx_events_yes'),
						0=>_t('_bx_events_no') 
					),
					'required' => false, 
					'db' => array (
						'pass' => 'Xss', 
					),                    
				), 
*/
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_bx_events_continue'),
                    'colspan' => false,
                ),
            ),            
        );

 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
  
        if ($oForm->isSubmittedAndValid ()) {
 
			if($_GET['EventStart']) 
				$iStartDate = $oForm->getCleanValue('EventStart');
			if($_GET['EventEnd']) 
				$iEndDate = $oForm->getCleanValue('EventEnd');

            bx_events_import ('SearchResult');
            $o = new BxEventsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Categories'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('State'), $oForm->getCleanValue('City'), $iStartDate, $iEndDate );

            if ($o->isError) {
                $this->oTemplate->displayPageNotFound ();
                exit;
            }

            if ($s = $o->processing()) {
                echo $s;
            } else {
                $this->oTemplate->displayNoData ();
                exit;
            }

            $this->oMain->isAllowedSearch(true); // perform search action 

            $this->oTemplate->addCss ('unit.css');
            $this->oTemplate->addCss ('main.css');
            $this->oTemplate->pageCode($o->aCurrent['title'], false, false);
			exit;

        } 
 
        return array($oForm->getCode()); 
    }  

    function getBlockCode_Created() { 
		$iProfileId = getLoggedId();
 		
		if(!$iProfileId)return;

        $aProfile = getProfileInfo($iProfileId);

        return $this->ajaxBrowse('user_short', $this->oDb->getParam('bx_events_perpage_main_recent'), array(), $aProfile['NickName'],true,false);
    }  

    function getBlockCode_Joined() { 
		$iProfileId = getLoggedId();
		
		if(!$iProfileId) return;

        $aProfile = getProfileInfo($iProfileId);

        return $this->ajaxBrowse('joined_short', $this->oDb->getParam('bx_events_perpage_main_recent'), array(),$aProfile['NickName'],true,false);
    }  
  
     function getBlockCode_UpcomingPhoto()
    {
        $aEvent = $this->oDb->getUpcomingEvent (getParam('bx_events_main_upcoming_event_from_featured_only') ? true : false);
        if (!$aEvent)
            return false;

        $aAuthor = getProfileInfo($aEvent['ResponsibleID']);

        $a = array ('ID' => $aEvent['ResponsibleID'], 'Avatar' => $aEvent['PrimPhoto']);
        $aImage = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');

        bx_events_import('Voting');
        $oRating = new BxEventsVoting ('bx_events', (int)$aEvent['ID']);

        $sEventUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aEvent['EntryUri'];
		/////////  FREDDY //////////[END] FREDDY 14/06/2017 INTEGRATION BUSINESS LISTING 
		if($aEvent['listing_id']){
 			if(getParam("modzzz_listing_boonex_events")=='on' && $aEvent['company_type']=='listing'){
				$oListing = BxDolModule::getInstance('BxListingModule');

				$aCompany = $oListing->_oDb->getEntryById($aEvent['listing_id']);
				$sCompanyName = $aCompany['title'];
				$sCompanyUrl = BX_DOL_URL_ROOT . $oListing->_oConfig->getBaseUri() . 'view/' . $aCompany['uri'];
				  
			}

		}
		//////////[END] FREDDY 14/06/2017 INTEGRATION BUSINESS LISTING 
		
		/*[begin] group integration (modzzz)*/
		if(getParam('bx_groups_boonex_events')=='on'){
			$sGroupLink = BxDolModule::getInstance('BxGroupsModule')->getGroupLink($aEvent['group_id']);
		}
	    /*[end] group integration (modzzz)*/

        $aVars = array (
            'bx_if:image' => array (
                'condition' => !$aImage['no_image'] && $aImage['file'],
                'content' => array (
                    'image_url' => !$aImage['no_image'] && $aImage['file'] ? $aImage['file'] : '',
                    'image_title' => !$aImage['no_image'] && $aImage['title'] ? $aImage['title'] : '',
                    'event_url' => $sEventUrl,
                ),
            ),
            'event_url' => $sEventUrl,
            'event_title' => $aEvent['Title'],
            'event_start_in' => $this->oMain->_formatDateInBrowse($aEvent),
            'author_title' => _t('_bx_events_by_author'),
            'author_username' => getNickName($aAuthor['ID']),
            'author_url' => getProfileLink($aAuthor['ID']),

            'rating' => $oRating->isEnabled() ? $oRating->getJustVotingElement (true, $aEvent['ID']) : '',
            'participants' => $aEvent['FansCount'],
            'country_city' => $this->oMain->_formatLocation($aEvent, true, true),
            'place' => $aEvent['Place'],
			
			/*[begin] group integration (modzzz)*/
			'bx_if:group_event' => array (
				'condition' => (getParam('bx_groups_boonex_events')=='on' && $aEvent['group_id']),
				'content' => array (
					'group' => $sGroupLink,
				),
			),
		   /*[end] group integration (modzzz)*/
			
			 ///FREDDY 01/10/2016 INTEGRATION BUSINESS LISTING AND SCHOOLS
		            'company_event' => ($sCompanyName) ? $sCompanyName : _t('_bx_events_na'),
		            'company_event_url' => ($sCompanyUrl) ? $sCompanyUrl : 'javascript:void(0);',
	
	              	////////////////////
        );
        return $this->oTemplate->parseHtmlByName('main_event', $aVars);
    }

  



}
