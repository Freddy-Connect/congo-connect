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

bx_import('BxDolTwigPageView');

class BxEventsSponsorPageView extends BxDolTwigPageView {	

    function BxEventsSponsorPageView(&$oSponsorsMain, &$aSponsor) {
        parent::BxDolTwigPageView('bx_events_sponsors_view', $oSponsorsMain, $aSponsor);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableSponsorMediaPrefix; 
		$this->_oDb->_sFieldId = 'id'; 
	}
   
  	function getBlockCode_Desc() {

		$aEvent = $this->_oDb->getEntryById((int)$this->aDataEntry['event_id']);

        $aVars = array (
            'breadcrumb' => $this->_oTemplate->genBreadcrumb($aEvent),
            'description' => $this->aDataEntry['desc'], 
        );

        return array($this->_oTemplate->parseHtmlByName('block_description', $aVars));
    }

    function getBlockCode_Photos() {
        return $this->_blockPhoto ($this->_oDb->getMediaIds($this->aDataEntry['id'], 'images'), $this->aDataEntry['author_id']);
    }    
 
    function getBlockCode_Rate() {
        bx_events_import('SponsorVoting');
        $o = new BxEventsSponsorVoting ('bx_events_sponsor', (int)$this->aDataEntry['id']);
    	if (!$o->isEnabled()) return '';
        return array($o->getBigVoting ($this->_oMain->isAllowedRateSubProfile($this->_oDb->_sTableSponsor, $this->aDataEntry)));
    }    
	 
    function getBlockCode_Comments() {    
        bx_events_import('SponsorCmts');
        $o = new BxEventsSponsorCmts ('bx_events_sponsor', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
			$aSponsorEntry = $this->_oDb->getSponsorEntryById($this->aDataEntry['id']);
			$iEntryId = (int)$aSponsorEntry['event_id'];
	 
		    $aDataEntry = $this->_oDb->getEntryById($iEntryId);
   
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'],
                 'TitleEdit' => $this->_oMain->isAllowedEdit($aDataEntry) ? _t('_bx_events_action_title_edit') : '',
                'TitleDelete' => $this->_oMain->isAllowedDelete($aDataEntry) ? _t('_bx_events_action_title_delete') : ''

            );

            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleDelete'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'bx_events_sponsor');
        } 

        return '';
    }    
 
	function getBlockCode_Info() {
        return array($this->_oTemplate->blockSubProfileInfo ('sponsor', $this->aDataEntry, false));
    }
 
	function getBlockCode_Contact() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'contact');
    }

	function getBlockCode_Location() {
        return $this->_blockCustomDisplay ($this->aDataEntry, 'location');
    }

	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType)
		{  
			case "contact":
				$aAllow = array('website','email','telephone','fax');
			break;
			case "location":
				$aAllow = array('address1','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockCustomSubItemFields($aDataEntry, 'Sponsor', $aAllow);

		if(!$sFields) return;

		$aVars = array ( 
            'fields' => $sFields, 
        );

        return array($this->_oTemplate->parseHtmlByName('custom_block_info', $aVars));   
    }
   
    function getCode() { 
        return parent::getCode();
    }    


}