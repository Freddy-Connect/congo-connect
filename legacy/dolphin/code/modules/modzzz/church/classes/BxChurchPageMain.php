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
bx_import('BxTemplCategories');

class BxChurchPageMain extends BxDolTwigPageMain {

    function BxChurchPageMain(&$oMain) {

		$this->oDb = $oMain->_oDb;
        $this->oConfig = $oMain->_oConfig;
		$this->oMain = $oMain;

        $this->sSearchResultClassName = 'BxChurchSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_church_main', $oMain);
	}
   
    function getBlockCode_ChurchCategories() {
  
	    $aAllEntries = $this->oDb->getCategoryInfo(0, true);
     
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->oDb->getParentCategoryCount($aEntry['id']);	 
			$sCatHref = $this->oDb->getCategoryUrl($aEntry['uri']);  
			$sCategory = $aEntry['name'];
 
	        $aResult['bx_repeat:entries'][] = array(
                'cat_url' => $sCatHref, 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory, 
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('church_categories', $aResult);  
	}
  
    function getBlockCode_States() {
		$iProfileId = $_COOKIE['memberID'];

		if(!$iProfileId)
			return;

		$aProfile = getProfileInfo($iProfileId);
		$sCountry = ($aProfile['Country']) ? $aProfile['Country'] : 'US';
 
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
 
	    $aStates = array($this->oTemplate->parseHtmlByName('block_states_main', $aVars)); 
		$aStates[3] = _t('_modzzz_church_browse_church_in_country', $sCountryName);
		
		return $aStates;  
	}
   
    function getBlockCode_LatestFeaturedChurch() { 
	  
	    return $this->ajaxBrowse('featured', (int)getParam('modzzz_church_perpage_main_featured')); 
    }
 
    function getBlockCode_Recent() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('modzzz_church_perpage_main_recent'));
    }

	function getBlockCode_Popular() { 
        return $this->ajaxBrowse('popular', $this->oDb->getParam('modzzz_church_perpage_main_recent'));
    }
    
	function getBlockCode_Top() { 
        return $this->ajaxBrowse('top', $this->oDb->getParam('modzzz_church_perpage_main_recent'));
    }

	function getChurchMain() {
        return BxDolModule::getInstance('BxChurchModule');
    }


	function getBlockCode_Search() {
   
        $this->oTemplate->pageStart();
 
        bx_import('BxDolCategories');
        bx_import('BxDolProfileFields'); 
  
        $oProfileFields = new BxDolProfileFields(0);
        $aCountries = $oProfileFields->convertValues4Input('#!Country');
		$aCountries = array(''=>'') + $aCountries;
	 
   		$aStates = array();  
 
		$sStateUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=state&country=' ;
 
		$sCatUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'home/'.($this->oMain->isPermalinkEnabled() ? '?' : '&').'ajax=cat&parent=' ;
 
		$aCategories = $this->oDb->getFormCategoryArray();
 
        $aForm = array(

            'form_attrs' => array(
                'name'     => 'form_search_church',
                'action'   => '',
                'method'   => 'post',
            ),      

            'params' => array (
                'db' => array(
                    'submit_name' => 'submit_form',
                ),
            ),
                  
            'inputs' => array(
                'Keyword' => array(
                    'type' => 'text',
                    'name' => 'Keyword',
                    'caption' => _t('_modzzz_church_form_caption_keyword'),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                ),                
                'parent' => array(
                    'type' => 'select',
                    'name' => 'parent',
					'values'=> $aCategories,
                    'caption' => _t('_modzzz_church_parent_categories'),
					'attrs' => array(
						'onchange' => "getHtmlData('subcat','$sCatUrl'+this.value)",
                        'id' => 'parentcat',
					),
                    'required' => false, 
                ), 
                'Category' => array(
                    'type' => 'select',
                    'name' => 'Category',
					'values'=> array(),
                    'caption' => _t('_modzzz_church_sub_categories'),
					'attrs' => array(
                        'id' => 'subcat',
					),
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Int', 
                    ),
                ),
                'Country' => array(
					'type' => 'select',
                    'name' => 'Country',
                    'caption' => _t('_modzzz_church_form_caption_country'),
                    'values' => $aCountries,
					'attrs' => array(
						'onchange' => "getHtmlData('substate','$sStateUrl'+this.value)",
					), 
					'value' => $sDefaultCountry, 
                    'required' => false, 
                    'db' => array (
                        'pass' => 'Preg', 
                        'params' => array('/([a-zA-Z]{0,2})/'),
                    ),  
                ),
				'State' => array(
					'type' => 'select',
					'name' => 'State', 
					'caption' => _t('_modzzz_church_caption_state'),
					'values'=> $aStates,  
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
                    'caption' => _t('_modzzz_church_form_caption_city'),
                    'required' => false,  
                    'db' => array (
                        'pass' => 'Xss', 
                    ),
                    'display' => true,
                ),    
                'Submit' => array (
                    'type' => 'submit',
                    'name' => 'submit_form',
                    'value' => _t('_modzzz_church_continue'),
                    'colspan' => false,
                ),
            ),            
        );
 
        $oForm = new BxTemplFormView($aForm);
        $oForm->initChecker(); 
  
        if ($oForm->isSubmittedAndValid ()) {
 
            modzzz_church_import ('SearchResult');
            $o = new BxChurchSearchResult('search', $oForm->getCleanValue('Keyword'), $oForm->getCleanValue('Category'), $oForm->getCleanValue('City'), $oForm->getCleanValue('State'), $oForm->getCleanValue('Country') );
 
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
   
	function getBlockCode_Comments() { 

		$iNumComments = getParam("modzzz_church_perpage_view_subitems");
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
			$iChurchId = $aEntry['cmt_object_id']; 
	 
			$sImage = '';
			if ($aEntry['thumb']) {
				$a = array ('ID' => $aEntry['author_id'], 'Avatar' => $aEntry['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
 
			$iLimitChars = (int)getParam('modzzz_church_max_preview');

			$sMessage = $this->oMain->_formatSnippetText($aEntry, $iLimitChars, $sMessage);
 
			$aChurch = $this->oDb->getEntryById($iChurchId);
			$sChurchUri = $aChurch['uri'];
			$sChurchTitle = $aChurch['title'];
 
			$sChurchUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sChurchUri;
   
			$aVars['bx_repeat:comments'][] = array (
				'thumb_url' => $sMemberThumb,
				'author_url' => $sNickLink,
				'author' => $sNickName,
				'created' => $dtSent,
				'snippet_text' => $sMessage,
				'item_url' => $sChurchUrl,
				'item_title' => $sChurchTitle,
 			);  
		}
 
		return $this->oTemplate->parseHtmlByName('block_comments', $aVars); 
	}

	function getBlockCode_Forum() {
    
		$iNumComments = (int)getParam("modzzz_church_perpage_view_subitems");
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
			$sChurchName = $aEachPost['title']; 
 			$sPoster = $aEachPost['user']; 

			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail(getID($sPoster));

			$iLimitChars = (int)getParam('modzzz_church_max_preview');
			$sPostText = $this->oMain->_formatSnippetText($aEachPost, $iLimitChars, $sPostText);
 
			$sImage = '';
			if ($aEachPost['thumb']) {
				$a = array ('ID' => $aEachPost['author_id'], 'Avatar' => $aEachPost['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}

			$sChurchUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sForumUri;
			$sTopicUrl = BX_DOL_URL_ROOT . 'forum/church/forum/'.$sForumUri.'-0.htm#topic/'.$sTopicUri.'.htm';
	
			$aVars['bx_repeat:entries'][] = array( 
							'topic_url' => $sTopicUrl, 
							'topic' => $sTopic, 
							'snippet_text' => $sPostText, 

							'bx_if:main' => array( 
								'condition' => true,
								'content' => array(
									'item_title' => $sChurchName, 
									'item_url' => $sChurchUrl, 						
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
   		
		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/my&filter=add_church'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_church', $aVars);  

		return $sCode;
	}
 
    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_church',
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




}
