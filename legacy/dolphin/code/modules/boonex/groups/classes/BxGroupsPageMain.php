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

bx_import('BxDolTwigPageMain');

class BxGroupsPageMain extends BxDolTwigPageMain {

    function BxGroupsPageMain(&$oMain) {
        $this->sSearchResultClassName = 'BxGroupsSearchResult';
        $this->sFilterName = 'bx_groups_filter';
		parent::BxDolTwigPageMain('bx_groups_main', $oMain);
	}
 
    function getBlockCode_States() {
		$iProfileId = getLoggedId();

		$aProfile = ($iProfileId) ? getProfileInfo($iProfileId) : array(); 
		$sCountry = ($aProfile['Country']) ? $aProfile['Country'] : getParam('bx_groups_default_country');
  
		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$sCountry]['LKey']);

		$aStates = $this->oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$sCountry}' ORDER BY `State`");
		  
		if(!count($aStates)) return;
			 
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
 
	    $aStates = array($this->oTemplate->parseHtmlByName('block_states', $aVars)); 
		$aStates[3] = _t('_bx_groups_browse_groups_in_country', $sCountryName);
		
		return $aStates;  
	}

    function getBlockCode_LatestFeaturedGroup()
    {
        $aDataEntry = $this->oDb->getLatestFeaturedItem ();
        if (!$aDataEntry)
            return false;

        $aAuthor = getProfileInfo($aDataEntry['author_id']);

        $sImageUrl = '';
        $sImageTitle = '';
        $a = array ('ID' => $aDataEntry['author_id'], 'Avatar' => $aDataEntry['thumb']);
        $aImage = BxDolService::call('photos', 'get_image', array($a, 'file'), 'Search');

        bx_groups_import('Voting');
        $oRating = new BxGroupsVoting ('bx_groups', $aDataEntry['id']);

        $aVars = array (
            'bx_if:image' => array (
                'condition' => !$aImage['no_image'] && $aImage['file'],
                'content' => array (
                    'image_url' => !$aImage['no_image'] && $aImage['file'] ? $aImage['file'] : '',
                    'image_title' => !$aImage['no_image'] && $aImage['title'] ? $aImage['title'] : '',
                    'group_url' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
                ),
            ),
            'group_url' => BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'view/' . $aDataEntry['uri'],
            'group_title' => $aDataEntry['title'],
            'author_title' => _t('_From'),
            'author_username' => getNickName($aAuthor['ID']),
            'author_url' => getProfileLink($aAuthor['ID']),
            'rating' => $oRating->isEnabled() ? $oRating->getJustVotingElement (true, $aDataEntry['id']) : '',
            'fans_count' => $aDataEntry['fans_count'],
            'country_city' => $this->oMain->_formatLocation($aDataEntry, false, true),
        );
        return $this->oTemplate->parseHtmlByName('latest_featured_group', $aVars);
    }
 
    function getBlockCode_Recent() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('bx_groups_perpage_main_recent'));
    }  
	 
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'bx_groups',
            'orderby' => 'popular',
			'pagination' => getParam('tags_perpage_browse')
        );

		$sUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'tags';
  
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
  		
		$sType = 'bx_groups';
		
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
 
	    return $this->oTemplate->parseHtmlByName('group_categories', $aResult);  
	}
 
	function getBlockCode_Activities() {
		$iNumEntries = getParam("bx_groups_perpage_main_feed"); 
		$aActivity = $this->oDb->getActivityFeed($iNumEntries);
        
		if(empty($aActivity))
			return;

		$aResult['bx_repeat:entries'] = array();  
 		foreach($aActivity as $aEntry){
			 
			$iGroupId = $aEntry['group_id'];
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

	    return $this->oTemplate->parseHtmlByName('group_activities', $aResult);  
	}
 
	function getBlockCode_Comments() { 

		$iNumComments = getParam("bx_groups_perpage_main_comment");
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
			$iGroupId = $aEntry['cmt_object_id']; 
	 
			$sImage = '';
			if ($aEntry['thumb']) {
				$a = array ('ID' => $aEntry['author_id'], 'Avatar' => $aEntry['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
 
			$iLimitChars = (int)getParam('bx_groups_comments_max_preview');

			$sMessage = $this->oMain->_formatSnippetText($aEntry, $iLimitChars, $sMessage);
 
			$aGroup = $this->oDb->getEntryById($iGroupId);
			$sGroupUri = $aGroup['uri'];
			$sGroupTitle = $aGroup['title'];
 
			$sGroupUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sGroupUri;
   
			$aVars['bx_repeat:comments'][] = array (
				'thumb_url' => $sMemberThumb,
				'author_url' => $sNickLink,
				'author' => $sNickName,
				'created' => $dtSent,
				'snippet_text' => $sMessage,
				'item_url' => $sGroupUrl,
				'item_title' => $sGroupTitle,
 			);  
		}
 
		return $this->oTemplate->parseHtmlByName('block_comments', $aVars); 
	}
  
	function getBlockCode_Forum() {
    
		$iNumComments = (int)getParam("bx_groups_perpage_main_forum");
		$aPosts = $this->oDb->getLatestForumPosts($iNumComments);
  
		if(empty($aPosts))return;
			 
		$aVars['bx_repeat:entries'] = array();
  		foreach($aPosts as $aEachPost){

			$sForumUri = $aEachPost['forum_uri'];
			$sTopic = $aEachPost['topic_title']; 
			$sTopicUri = $aEachPost['topic_uri'];
			$sPostText = $aEachPost['post_text']; 
			$sDate = defineTimeInterval($aEachPost['when']); 
			$sGroupName = $aEachPost['title']; 
 			$sPoster = $aEachPost['user']; 

			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$iLimitChars = (int)getParam('bx_groups_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
  
			$sGroupUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/groups/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 

							'bx_if:main' => array( 
								'condition' => true,
								'content' => array(
									'item_title' => $sGroupName, 
									'item_url' => $sGroupUrl, 						
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
   		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&bx_groups_filter=add_group'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_group', $aVars);  

		return $sCode;
	}

	function getBlockCode_Featured() { 
		return $this->ajaxBrowse('featured', $this->oDb->getParam('bx_groups_perpage_main_featured'));
	} 

	function getBlockCode_PopularList() { 
		return $this->ajaxBrowse('popular', $this->oDb->getParam('bx_groups_perpage_main_popular'));
	}     

	function getBlockCode_TopList() { 
		return $this->ajaxBrowse('top', $this->oDb->getParam('bx_groups_perpage_main_top'));
	}     
 
	function getBlockCode_SearchOLD() {
		
		$sSearchUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/quick/'; 
	
		$aVars = array( 
			'search_url' => $sSearchUrl, 
		);
 
		$sCode = $this->oTemplate->parseHtmlByName('search_groups', $aVars);  

		return $sCode;
	} 

	function getBlockCode_Search() {
   
        $this->oTemplate->pageStart();
 
        bx_import('BxDolProfileFields');
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
        asort($aCountries);
        $aCountries = array_merge (array('' => _t('_bx_groups_all_countries')), $aCountries);
  
        $sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/?ajax=state&country=' ;
       
        bx_import('BxDolCategories');
        $oCategories = new BxDolCategories();
        $oCategories->getTagObjectConfig ();
        $aCategories = $oCategories->getCategoriesList('bx_groups', (int)$iProfileId, true);
 
        $aCategories[''] = _t('_bx_groups_all_categories'); 
 
        $aForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_groups',
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
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_bx_groups_form_caption_keyword'),
					'required' => false,
				/*        
					'required' => true,
                    'checker' => array (
                        'func' => 'length',
                        'params' => array(3,100),
                        'error' => _t ('_bx_groups_form_err_keyword'),
                    ),
				*/
                    'db' => array (
                        'pass' => 'Xss',
                    ), 
                ),               
                'Category' => array(
                    'type' => 'select_box',
                    'name' => 'Category',
                    'caption' => _t('_bx_groups_form_caption_category'),
                    'values' => $aCategories,
                    'required' => false,
                    'db' => array (
                        'pass' => 'Xss',
                    ),                   
                ),
 
                'Country' => array(
                    'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_bx_groups_form_caption_country'),
                    'values' => $aCountries,
                    'required' => false,
                    'attrs' => array(
                    'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
                    ),
                    'db' => array (
                    'pass' => 'Preg',
                    'params' => array('/([a-zA-Z]{0,2})/'),
                    ),                   
                ),

                'State' => array(
                    'type' => 'select',
                    'name' => 'State',
                    'caption' => _t('_bx_groups_form_caption_state'),
                            'attrs' => array(
                    'id' => 'substate',
                            ),
                          'db' => array (
                    'pass' => 'Preg',
                    'params' => array('/([a-zA-Z]+)/'),
                    ),
                ),
                         
                'City' => array(
                    'type' => 'text',
                    'name' => 'City',
                    'caption' => _t('_bx_groups_form_caption_city'),
                    'required' => false,
                    'db' => array (
                    'pass' => 'Xss',
                    ),               
                ),
  
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_bx_groups_continue'),
                    'colspan' => false,
                ),
            ),           
        );

        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
 
        if ($oForm->isSubmittedAndValid ()) {
 
            bx_groups_import ('SearchResult');
            $o = new BxGroupsSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('Country'), $oForm->getCleanValue('State'), $oForm->getCleanValue('City'));

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

        return $this->ajaxBrowse('user', $this->oDb->getParam('bx_groups_perpage_main_recent'), array(), getNickName($iProfileId),true,false);
    }  

    function getBlockCode_Joined() { 
 
		if(!$iProfileId = getLoggedId()) return;
		
		$aProfileInfo = getProfileInfo($iProfileId);

        return $this->ajaxBrowse('joined', $this->oDb->getParam('bx_groups_perpage_main_recent'), array(), $aProfileInfo['NickName'], true, false);
    }  
}
