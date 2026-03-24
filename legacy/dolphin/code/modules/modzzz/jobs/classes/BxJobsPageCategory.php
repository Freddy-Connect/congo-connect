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
 
class BxJobsPageCategory extends BxDolTwigPageMain {

	var $aCategoryInfo = array();
	var $sCategoryUri;

    function BxJobsPageCategory(&$oMain,$sUri) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;

        $this->sSearchResultClassName = 'BxJobsSearchResult';
        $this->sFilterName = 'filter';
		parent::BxDolTwigPageMain('modzzz_jobs_category', $oMain);
		
		$this->sCategoryUri = $sUri;
		$this->aCategoryInfo = $this->_oDb->getCategoryInfoByUri($this->sCategoryUri); 
 
		if($this->sCategoryUri && (!count($this->aCategoryInfo))){ 
			$sRedirectUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'home';
			header("location:{$sRedirectUrl}"); 
		}

		if(isset($_GET['ajax']) && ($_GET['ajax']=='sort')){
			echo $this->getBlockCode_CategoryJobs(); 
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
  
	    $aAllEntries = $this->_oDb->getCategoryInfo(0, true);
     
        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->_oDb->getParentCategoryCount($aEntry['id']);	 
			$sCatHref = $this->_oDb->getCategoryUrl($aEntry['uri']);  
			$sCategory = $aEntry['name'];
 
	        $aResult['bx_repeat:entries'][] = array(
                 /* Freddy mise en commentaire 
			    'cat_url' => $sCatHref, 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory ,
				*/ 
				
				// Freddy ajout/ Ne pas afficher les categories dont le nombre total est zero
				 'bx_if:categorie' => array( 
								'condition' => $iNumCategory >=1,
								'content' => array(
                 'cat_url' => $sCatHref, 
                 'cat_name' => $sCategory,
			     'num_items' => $iNumCategory , 
								), 
							),
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('jobs_categories', $aResult);  
	}
 
    function getSubCategories() { 
  		
		$iCategoryId = $this->aCategoryInfo['id']; 
 	    $aAllEntries = $this->_oDb->getSubCategoryInfo($iCategoryId, 0, true);
 
        $aResult = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$sCatHref = $this->_oDb->getSubCategoryUrl($aEntry['uri']);  
			$sCategory = $aEntry['name']; 
			$iNumCategory = $this->_oDb->getCategoryCount($aEntry['id']);	 

	        $aResult[] = array(
               	/* Freddy mise en commentaire 
			 'subcategory_url' => $sCatHref, 
                'subcategory' => $sCategory,
			    'subcategory_class' => ($iCategoryId==$aEntry['id']) ? "selected_subcat" : "subcat", 
			    'num_items' => $iNumCategory, 
			*/
			// Freddy ajout/ Ne pas afficher les categories dont le nombre total est zero
			 'bx_if:categorie' => array( 
								'condition' => $iNumCategory >=1,
								'content' => array(
                 'subcategory_url' => $sCatHref, 
                'subcategory' => $sCategory,
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
 
	    return $this->oTemplate->parseHtmlByName('jobs_subcategories', $aVars);  
	}
 
    function getBlockCode_CategoryJobs() {

		$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'categories/'.$this->aCategoryInfo['uri'].'?';
  
        $sCode = $this->ajaxBrowse('categories', $this->oDb->getParam('modzzz_jobs_perpage_main_recent'),array(), $this->aCategoryInfo['uri'], false, false);  

		return ($sCode) ? $sCode : array(MsgBox(_t('_Empty')), array());   
	}
 
    

}
