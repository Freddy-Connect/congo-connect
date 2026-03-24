<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Event
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
 
class BxJobsPageLocal extends BxDolTwigPageMain {
 		var $_oDb;
        var $_oConfig; 
		var $sCountry; 
		var $sState; 
		var $sCategory; 

    function BxJobsPageLocal(&$oMain, $sCountry, $sState, $sCategory) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;

		$this->sCountry = $sCountry; 
		$this->sState = $sState; 
		$this->sCategory = $sCategory; 

        $this->sSearchResultClassName = 'BxJobsSearchResult';
        $this->sFilterName = 'filter';

		if($sCountry)
			parent::BxDolTwigPageMain('modzzz_jobs_local_state', $oMain); 
		else	
			parent::BxDolTwigPageMain('modzzz_jobs_local', $oMain); 

	}
 
    function getBlockCode_StateJobs() {
  
		if($this->sState){ 	 
			
			$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/'. $this->sCountry .'/'. $this->sState . ($this->sCategory  ? '/'. $this->sCategory : '') . '?';

			$sJobs = $this->ajaxBrowse('local_state', $this->oDb->getParam('modzzz_jobs_perpage_main_recent'), array(), $this->sCountry, $this->sState, $this->sCategory);
		}elseif($this->sCountry){  

 			$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local_country/'. $this->sCountry . ($this->sCategory  ? '/'. $this->sCategory : '') . '?';

			$sJobs = $this->ajaxBrowse('local_country', $this->oDb->getParam('modzzz_jobs_perpage_main_recent'), array(), $this->sCountry,   $this->sCategory); 
		} 

		return $sJobs;  
	}
  
    function getBlockCode_Categories() {
  

		if($this->sState){  
			$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/'. $this->sCountry .'/'. $this->sState . '/';

			$sItemName = $this->_oDb->getStateName($this->sCountry, $this->sState);

			$sItemLabel  = _t('_modzzz_jobs_category_drilldown_for_item', $sItemName);

		}elseif($this->sCountry){
			$sBaseUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local_country/'. $this->sCountry .'/' ;

  			$sItemName = _t($GLOBALS['aPreValues']['Country'][$this->sCountry]['LKey']);

			$sItemLabel  = _t('_modzzz_jobs_category_drilldown_for_item', $sItemName);
		}

	    $aAllEntries = $this->_oDb->getCategoryInfo(0, true);
     
		$aResult['item_name'] = $sItemName;
		$aResult['item_label'] = $sItemLabel;

        $aResult['bx_repeat:entries'] = array();        
 		foreach($aAllEntries as $aEntry)
		{	 
			$iNumCategory = $this->_oDb->getLocalCategoryCount($aEntry['id'], $this->sCountry, $this->sState);	 
			$sCategory = $aEntry['name'];
 
	        $aResult['bx_repeat:entries'][] = array(
                'cat_url' => $sBaseUrl . $aEntry['uri'], 
                'cat_name' => $sCategory,
			    'num_items' => $iNumCategory, 
            );	        
	    } 
 
	    return $this->oTemplate->parseHtmlByName('local_categories', $aResult);  
	}

    function getBlockCode_States() {

		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$this->sCountry]['LKey']);

		$aStates = $this->_oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$this->sCountry}' ORDER BY `State`");
		 
		$aVars['bx_repeat:entries'] = array();        
  		foreach($aStates as $aEachState){
			 
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];

			$sStateUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/' . $this->sCountry .'/'. $sStateCode;
  			
			$iNumCategory = $this->_oDb->getStateCount($sStateCode);	 
 
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
 
	    return $this->oTemplate->parseHtmlByName('jobs_states', $aVars);   
	}

    function getBlockCode_Region() {
 
		$aRegions = $this->_oDb->getAll("SELECT `ISO2`, `Country`, `Region` FROM `sys_countries` WHERE Region IS NOT NULL ORDER BY `Region`, `Country`");
		
		$iTotal = count($aRegions);
		$iRowTotal = (int)($iTotal / 4);
		 
		$sNewRegion = '';
		$sPrevRegion = '';
		$iCounter = 1;
		$aVars['bx_repeat:rows'] = array();
  		foreach($aRegions as $aEachRegion){
			
			$sNewRegion = $aEachRegion['Region'];

			$sCountryUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local_country/' . $aEachRegion['ISO2'];

			if(($iCounter==1) || (($iCounter%$iRowTotal)==1) ){
 				$aResult['bx_repeat:entries'] = array();        
			}
  
			$aResult['bx_repeat:entries'][] = array(
	 
				'bx_if:region' => array( 
					'condition' => ($sNewRegion != $sPrevRegion),
					'content' => array( 
						'region_name' => $aEachRegion['Region'],
						'country_url' => $sCountryUrl, 
						'country_name' => $aEachRegion['Country'], 
					), 
				), 

				'bx_if:country' => array( 
					'condition' => ($sNewRegion == $sPrevRegion),
					'content' => array( 
						'country_url' => $sCountryUrl, 
						'country_name' => $aEachRegion['Country'],
					), 
				),  

			 );

			 $bStart=false;
		
			if( ($iCounter%$iRowTotal)==0 ){ 
				$aVars['bx_repeat:rows'][]=$aResult;
			}

			$sPrevRegion = $sNewRegion; 
			$iCounter++;
	    } 
 
	    return $this->oTemplate->parseHtmlByName('jobs_regions', $aVars);  
	}
 
     function ajaxBrowse($sMode, $iPerPage, $aMenu = array(), $sValue = '', $sValue2 = '', $sValue3 = '', $isDisableRss = false, $isPublicOnly = true) {

        bx_import ('SearchResult', $this->oMain->_aModule);
        $sClassName = $this->sSearchResultClassName;
        $o = new $sClassName($sMode, $sValue, $sValue2, $sValue3);
        $o->aCurrent['paginate']['perPage'] = $iPerPage; 
        $o->setPublicUnitsOnly($isPublicOnly);

        if (!$aMenu)
            $aMenu = ($isDisableRss ? '' : array(_t('RSS') => array('href' => $o->aCurrent['rss']['link'] . (false === strpos($o->aCurrent['rss']['link'], '?') ? '?' : '&') . 'rss=1', 'icon' => getTemplateIcon('rss.png'))));

        if ($o->isError)
            return array(MsgBox(_t('_Error Occured')), $aMenu);

        if (!($s = $o->displayResultBlock())) 
            return $isPublicOnly ? array(MsgBox(_t('_Empty')), $aMenu) : '';


        $sFilter = (false !== bx_get($this->sFilterName)) ? $this->sFilterName . '=' . bx_get($this->sFilterName) . '&' : '';
        $oPaginate = new BxDolPaginate(array(
            'page_url' => 'javascript:void(0);',
            'count' => $o->aCurrent['paginate']['totalNum'],
            'per_page' => $o->aCurrent['paginate']['perPage'],
            'page' => $o->aCurrent['paginate']['page'],
            'on_change_page' => 'return !loadDynamicBlock({id}, \'' . $this->sUrlStart . $sFilter . 'page={page}&per_page={per_page}\');',
        ));
        $sAjaxPaginate = $oPaginate->getSimplePaginate($this->oConfig->getBaseUri() . $o->sBrowseUrl, -1, -1, false);

        return array(
            $s, 
            $aMenu,
            $sAjaxPaginate,
            '');
    } 

}
