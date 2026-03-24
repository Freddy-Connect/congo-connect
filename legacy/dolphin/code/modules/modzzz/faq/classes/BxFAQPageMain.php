<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx FAQ
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

class BxFAQPageMain extends BxDolTwigPageMain {

    function BxFAQPageMain(&$oMain) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;

        $this->sSearchResultClassName = 'BxFAQSearchResult';
        $this->sFilterName = 'modzzz_faq_filter';
		parent::BxDolTwigPageMain('modzzz_faq_main', $oMain);
	}
  
    function getBlockCode_FAQCategories() {
   		
		$sType = 'modzzz_' . $this->_oConfig->getUri();
		
		$oCateg = new BxTemplCategories();
		$oCateg->getTagObjectConfig();

	    $aAllEntries = $this->_oDb->getCategories($sType);
    
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->_oDb->getCategoryCount($sType,$aEntry['Category']);	
	
			$sHrefTmpl = $oCateg->getHrefWithType($sType);  
			$sCategory = $aEntry['Category'];
            $sCatHref = str_replace( '{tag}', urlencode(title2uri($sCategory)), $sHrefTmpl);
 
	        $aResult['bx_repeat:entries'][] = array(
                'cat_url' => $sCatHref, 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory, 
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('faq_categories', $aResult);  
	}
   
    function getBlockCode_LatestFeaturedFAQ() {  
        return $this->ajaxBrowse('featured', 1, array(), '', false, false);
    }
 
    function getBlockCode_Recent() {  
        return $this->ajaxBrowse('recent', $this->oDb->getParam('modzzz_faq_perpage_main_recent'), array(), '', false, false);
    }

	function getBlockCode_Popular() { 
        return $this->ajaxBrowse('popular', $this->oDb->getParam('modzzz_faq_perpage_main_recent'), array(), '', false, false);
    }
    
	function getBlockCode_Top() { 
        return $this->ajaxBrowse('top', $this->oDb->getParam('modzzz_faq_perpage_main_recent'), array(), '', false, false);
    }

	function getBlockCode_Search() {
		
		$sSearchUrl = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'browse/quick/'; 
	
		$aVars = array( 
			'search_url' => $sSearchUrl, 
		);
 
		$sCode = $this->oTemplate->parseHtmlByName('search_faq', $aVars);  

		return $sCode;
	} 

    function getBlockCode_Tags($iBlockId) { 
        bx_import('BxTemplTagsModule');
        $aParam = array(
            'type' => 'modzzz_faq',
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
  
	function getFAQMain() {
        return BxDolModule::getInstance('BxFAQModule');
    }
  

}
