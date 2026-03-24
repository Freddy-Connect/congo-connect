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
 
class BxEventsPageLocal extends BxDolTwigPageMain {
 
    function BxEventsPageLocal(&$oMain, $sCountry, $sState) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;

		$this->sCountry = $sCountry; 
		$this->sState = $sState; 

        $this->sSearchResultClassName = 'BxEventsSearchResult';
        $this->sFilterName = 'bx_events_filter';

		if($sCountry)
			parent::BxDolTwigPageMain('bx_events_local_state', $oMain); 
		else	
			parent::BxDolTwigPageMain('bx_events_local', $oMain); 

	}
 
    function getBlockCode_StateEvents() {
 
		if($this->sState){ 
			$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/'. $this->sCountry .'/'. $this->sState . '?';
 
			$aBlock = $this->ajaxBrowse('local_state', $this->oDb->getParam('bx_events_perpage_main_recent'), array(), $this->sState,false,false);
		}elseif($this->sCountry){
			$this->sUrlStart = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/'. $this->sCountry . '?';
 
			$aBlock = $this->ajaxBrowse('local_country', $this->oDb->getParam('bx_events_perpage_main_recent'), array(), $this->sCountry,false,false);
		}

		return $aBlock ? $aBlock : MsgBox(_t('_Empty')); 
	}

    function getBlockCode_States() {

		$sCountryName = _t($GLOBALS['aPreValues']['Country'][$this->sCountry]['LKey']);

		$aStates = $this->_oDb->getAll("SELECT `State`,`StateCode` FROM `States` WHERE CountryCode='{$this->sCountry}' ORDER BY `State`");
		 
		$aVars['bx_repeat:entries'] = array();        
  		foreach($aStates as $aEachState){
			 
			$sState = $aEachState['State'];
			$sStateCode = $aEachState['StateCode'];

			$iNumState = $this->oDb->getStateCount($sStateCode);	

			$sStateUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/' . $this->sCountry .'/'. $sStateCode;
  			
			$aVars['country_name'] = $sCountryName;

			$aVars['bx_repeat:entries'][] = array(
		 
				'bx_if:selstate' => array( 
					'condition' => ($sStateCode == $this->sState),
					'content' => array( 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumState, 
					), 
				), 
				'bx_if:regstate' => array( 
					'condition' => ($sStateCode != $this->sState),
					'content' => array( 
						'state_url' => $sStateUrl, 
						'state_name' => $sState,
						'num_items' => $iNumState,  
					), 
				), 

			 ); 
	    } 
 
	    $aBlock = array($this->oTemplate->parseHtmlByName('event_states', $aVars));  
		$aBlock[3] = _t('_bx_events_regions_for_country', $sCountryName);

		return $aBlock;
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

			$sCountryUrl = BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'local/' . $aEachRegion['ISO2'];

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

 
	    return $this->oTemplate->parseHtmlByName('event_regions', $aVars);  
	}
 
 

}