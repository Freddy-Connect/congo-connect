<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx News
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

class BxNewsPageMain extends BxDolTwigPageMain {

   
   // Freddy ajout function ajaxBrowse; Cette function a été copiée de /inc/classes/BxDolTwigPageMain.php
	  //Le but c'est de ne pas afficher les block dans Business Listing Home quand c'est vide
	 function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $isDisableRss = false, $isPublicOnly = true)
    {
      // Freddy ajout $oMain = BxDolModule::getInstance('BxListingModule');
	  $oMain = BxDolModule::getInstance('BxNewsModule');
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
   
   
   
   
   
    function BxNewsPageMain(&$oMain) {
		
		$this->oMain = $oMain;
        $this->_oConfig = $oMain->_oConfig;

        $this->sSearchResultClassName = 'BxNewsSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_news_main', $oMain);
	}
  
    function getBlockCode_Alphabet() {

		if(getParam('modzzz_news_show_letter')!='on') return;

		bx_import ('BxDolProfileFields');
	
		$sSearchUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/letter/'; 
 
		$aVars = array(); 
        $oProfileFields = new BxDolProfileFields(0);
        $aDBLetters = $oProfileFields->convertValues4Input('#!NewsLetter');
 
		foreach($aDBLetters as $sKey=>$sLKey){ 
			$aLetters[] = array(
				'letter' => $sKey,
				'letter_url' => $sSearchUrl . $sKey,
			); 
		} 
      
 		$aVars['bx_repeat:letters'] = $aLetters;        
 
		return $this->oTemplate->parseHtmlByName('search_letters', $aVars);   
 	} 
 
    function getBlockCode_NewsCategories() {
   		
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
 
	    return $this->oTemplate->parseHtmlByName('block_categories', $aResult);  
	}
  
    function getBlockCode_LatestFeaturedNews() { 
        return $this->ajaxBrowse('featured', 1);
    }
 
    function getBlockCode_Recent() { 
        return $this->ajaxBrowse('recent', $this->oDb->getParam('modzzz_news_perpage_main_recent'));
    }

	function getBlockCode_Popular() { 
        return $this->ajaxBrowse('popular', $this->oDb->getParam('modzzz_news_perpage_main_recent'));
    }
    
	function getBlockCode_Top() { 
        return $this->ajaxBrowse('top', $this->oDb->getParam('modzzz_news_perpage_main_recent'));
    }

	function getBlockCode_Create() {
   		
		if(!$this->oMain->isAdmin())
			return;

		$sAskUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'administration/create'; 
    
		$aVars = array( 
			'create_url' => $sAskUrl, 
  		);
 
		$sCode = $this->oTemplate->parseHtmlByName('create_news', $aVars);  

		return $sCode;
	}
 
	function getBlockCode_Search() {
		
		$sSearchUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/quick/'; 
	
		$aVars = array( 
			'search_url' => $sSearchUrl, 
		);
 
		return $this->oTemplate->parseHtmlByName('search_news', $aVars);  
	} 

    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_news',
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

    function getBlockCode_Archive() {

		$iNowMonth = date("n")-1;
		$iNowYear = date("Y");
  
		$iCreated = (int)db_value("SELECT MIN(`when`) FROM `modzzz_news_main` LIMIT 1");
 
		$iStartYear = ($iCreated) ? date("Y", $iCreated) : $iNowYear;
 
		$aVars = array (
			'bx_repeat:months' => array (),
		);

		for( $iYear=$iNowYear; $iYear>=$iStartYear; $iYear-- ) {
	 
			$sYearUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . "browse/archive/{$iYear}/0";

			if($iYear==$iNowYear) {
		 
				 if($iYear==$iNowYear)
					$iEndMonth = $iNowMonth;
				 else
					$iEndMonth = 11;

				for($iMonth=$iEndMonth; $iMonth>=0; $iMonth--) {

					$sMonthName = $this->oDb->getMonth($iMonth);
						
					$iNumMonthPosts = $this->oDb->countMonthPosts($iYear, $iMonth);
			  
					$sMonthUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . "browse/archive/{$iYear}/".($iMonth+1);
					
					$aVars['bx_repeat:months'][] = array (
						'bx_if:present' => array( 
							'condition' => true,
							'content' => array(
								'month' => $sMonthName,
							) 
						), 
						'num_posts' => $iNumMonthPosts,
						'year' => $iYear, 
						'url' => $sMonthUrl, 
					);   
				} 
			}else{ 
 
				$iNumMonthPosts = $this->oDb->countMonthPosts($iYear);

				$aVars['bx_repeat:months'][] = array ( 

					'bx_if:present' => array( 
						'condition' => false,
						'content' => array() 
					),

					'num_posts' => $iNumMonthPosts,
					'year' => $iYear, 
					'url' => $sYearUrl,
				);  
			}

		}

		return $this->oTemplate->parseHtmlByName('block_archive', $aVars); 
	}
 
	function getBlockCode_Comments(){ 
	   
		$iNumComments = getParam("modzzz_news_perpage_main_comment");
		$aAllEntries = $this->oDb->getLatestComments($iNumComments);

		if(!count($aAllEntries))
			return; 
   
		$aVars = array (
			'bx_repeat:comments' => array (),
		);

		$iLimitChars = (int)getParam('modzzz_news_comments_max_preview');

		foreach($aAllEntries as $aEntry) {
		   
			$iMemberID = $aEntry['cmt_author_id'];
			$sNickName = getNickName($iMemberID);
			$sNickLink = getProfileLink($iMemberID);
			$sMemberThumb = $GLOBALS['oFunctions']->getMemberThumbnail($iMemberID);
			$sMessage = $aEntry['cmt_text']; 
			$dtSent = defineTimeInterval($aEntry['date']);
			$iListingId = $aEntry['cmt_object_id']; 
	 
			$sImage = '';
			if ($aEntry['thumb']) {
				$a = array ('ID' => $aEntry['author_id'], 'Avatar' => $aEntry['thumb']);
				$aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
				$sImage = $aImage['no_image'] ? '' : $aImage['file'];
			}
  
			$sMessage = $this->oMain->_formatSnippetText($aEntry, $iLimitChars, $sMessage);
 
			$aListing = $this->oDb->getEntryById($iListingId);
			$sListingUri = $aListing['uri'];
			$sListingTitle = $aListing['title'];
 
			$sListingUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() .'view/' . $sListingUri;
   
			$aVars['bx_repeat:comments'][] = array (
				'thumb_url' => $sMemberThumb,
				'author_url' => $sNickLink,
				'author' => $sNickName,
				'created' => $dtSent,
				'snippet_text' => $sMessage,
				'item_url' => $sListingUrl,
				'item_title' => $sListingTitle,
 			);  
		}
 
		return $this->oTemplate->parseHtmlByName('block_comments', $aVars); 
	}

  
	function getNewsMain() {
        return BxDolModule::getInstance('BxNewsModule');
    }
 

}
