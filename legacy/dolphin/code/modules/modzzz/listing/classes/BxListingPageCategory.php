<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Wish
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
 
class BxListingPageCategory extends BxDolTwigPageMain {

	var $aCategoryInfo = array();
	var $sCategoryUri;

    function BxListingPageCategory(&$oMain,$sUri) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;

        $this->sSearchResultClassName = 'BxListingSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_listing_category', $oMain);
		
		$this->sCategoryUri = $sUri;
		$this->aCategoryInfo = $this->_oDb->getCategoryInfoByUri($this->sCategoryUri); 
 
		if($this->sCategoryUri && (!count($this->aCategoryInfo))){ 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home';
			header("location:{$sRedirectUrl}"); 
		}

		if(isset($_GET['ajax']) && ($_GET['ajax']=='sort')){
			echo $this->getBlockCode_CategoryListing(); 
			exit;
		}  
	}
  
    function getBlockCode_Categories() {

		if($this->aCategoryInfo['id']){ 
			return $this->getSubCategories();
		}else{ 
			return $this->getCategories();
		} 
	}

    function getCategories() {
   
  		$iLanguageId = getLangIdByName(getCurrentLangName());

	    /* Freddy commentaire pour désactiver les category par langue
		$aAllEntries = $this->oDb->getParentCategories($iLanguageId);
		*/
		$aAllEntries = $this->oDb->getParentCategories();
		//////////////////fin fredd modif//////////////////
     
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{ 
			$iNumCategory = (int)$this->_oDb->getParentCategoryCount($aEntry['id']);	 
 
	        $aResult['bx_repeat:entries'][] = array(
               /*
			    'cat_url' => $this->oDb->getCategoryUrl($aEntry['uri']), 
                'cat_name' => $aEntry['name'],
			    'num_items' => $iNumCategory, 
				*/
				
				// Freddy ajout/ Ne pas afficher les categories dont le nombre total est zero
				 'bx_if:categorie' => array( 
								'condition' => $iNumCategory >=1,
								'content' => array(
                'cat_url' => $this->oDb->getCategoryUrl($aEntry['uri']), 
                'cat_name' => $aEntry['name'],
			    'num_items' => $iNumCategory, 
								), 
							),
				
				
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('listing_categories', $aResult);  
	}
 
    function getSubCategories() { 
  		
		$iCategoryId = (int)$this->aCategoryInfo['id']; 
 	    $aAllEntries = $this->_oDb->getSubCategories($iCategoryId);
 
        $aResult = array();        
 		foreach($aAllEntries as $aEntry)
		{   

			$iNumCategory = (int)$this->_oDb->getCategoryCount($aEntry['id']);	 

	        $aResult[] = array(
               /*
			    'subcategory_url' => $this->_oDb->getSubCategoryUrl($aEntry['uri']), 
                'subcategory' => $aEntry['name'],
			    'subcategory_class' => ($iCategoryId==$aEntry['id']) ? "selected_subcat" : "subcat", 
			    'num_items' => $iNumCategory, 
				*/
				
				// Freddy ajout/ Ne pas afficher les categories dont le nombre total est zero
			 'bx_if:categorie' => array( 
								'condition' => $iNumCategory >=1,
								'content' => array(
               'subcategory_url' => $this->_oDb->getSubCategoryUrl($aEntry['uri']), 
                'subcategory' => $aEntry['name'],
			    'subcategory_class' => ($iCategoryId==$aEntry['id']) ? "selected_subcat" : "subcat", 
			    'num_items' => $iNumCategory, 
								), 
							),
				
            );	        
	    } 

		$aVars = array(
			'bx_repeat:entries' => $aResult,
			'all_categories_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'categories',
			'parent_category_url' => $this->_oDb->getCategoryUrl($this->aCategoryInfo['uri']),
			'parent_category' => $this->aCategoryInfo['name'], 
		);
 
	    return $this->oTemplate->parseHtmlByName('listing_subcategories', $aVars);  
	}
 
    function getBlockCode_CategoryListing() {

 		$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'categories/'.$this->aCategoryInfo['uri'];
        $this->sUrlStart .= (false === strpos($this->sUrlStart, '?') ? '?' : '&');  

        $sCode = $this->ajaxBrowse('categories', $this->oDb->getParam('modzzz_listing_perpage_main_recent'),array(), $this->aCategoryInfo['uri'], false, false);  
  
		return ($sCode) ? $sCode : array(MsgBox(_t('_Empty')), array());  
	}
  

}
