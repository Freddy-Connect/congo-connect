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

class BxProfessionalBookingPageView extends BxDolTwigPageView {	

    function BxProfessionalBookingPageView(&$oBookingsMain, &$aBooking) {
        parent::BxDolTwigPageView('modzzz_professional_bookings_view', $oBookingsMain, $aBooking);
	
        $this->_oDb->_sTableMediaPrefix = $this->_oDb->_sTableBookingMediaPrefix;  
	}
  
	function getBlockCode_Info() {
        return $this->_oTemplate->blockSubProfileInfo ($this->aDataEntry);
    }

	function getBlockCode_Desc() {
        $aDataEntry = $this->_oDb->getEntryById($this->aDataEntry['professional_id']);

		$sLocation = $this->_blockCustomDisplay ($this->aDataEntry, 'location');
		$sContact = $this->_blockCustomDisplay ($this->aDataEntry, 'contact');
        
		$aVars = array (
            'title' => $this->aDataEntry['title'],  
            'description' => $this->aDataEntry['desc'],  

            'bx_if:location' => array (
                'condition' => $sLocation,
                'content' => array (
					'location' => $sLocation,
                ),
            ), 

            'bx_if:contact' => array (
                'condition' => $sContact,
                'content' => array (
					'contact' => $sContact
                ),
            ),  
        );

        return array($this->_oTemplate->parseHtmlByName('block_booking_description', $aVars));  
    }
  
	function _blockCustomDisplay($aDataEntry, $sType) {
		
		switch($sType) {
			case "contact":
				$aAllow = array('website','email','telephone','mobile','fax');
			break;
			case "location":
				$aAllow = array('address1','address2','city','state','country','zip');
			break;  
		}
  
		$sFields = $this->_oTemplate->blockCustomFields($aDataEntry, $aAllow, 'client');

		if(!$sFields) return;
 
		$aVars = array ( 
            'fields' => $sFields, 
        );

        return $this->_oTemplate->parseHtmlByName('custom_block_info', $aVars);   
    }
 
    function getBlockCode_Comments() {    
        modzzz_professional_import('BookingCmts');
        $o = new BxProfessionalBookingCmts ('modzzz_professional_booking', (int)$this->aDataEntry['id']);
        if (!$o->isEnabled()) 
            return '';
        return $o->getCommentsFirst ();
    }            

    function getBlockCode_Actions() {
        global $oFunctions;

		if (!$this->_oMain->isAllowedBookingManage($this->aDataEntry)) return;

        if ($this->_oMain->_iProfileId || $this->_oMain->isAdmin()) {
  
            $this->aInfo = array (
                'BaseUri' => $this->_oMain->_oConfig->getBaseUri(),
                'iViewer' => $this->_oMain->_iProfileId,
                'ownerID' => (int)$this->aDataEntry['author_id'],
                'ID' => (int)$this->aDataEntry['id'],
                'URI' => $this->aDataEntry['uri'], 
                'TitleEdit' => _t('_modzzz_professional_action_title_edit'),
                'TitleConfirmed' => _t('_modzzz_professional_action_title_confirmed'), 
                'TitleDelete' => _t('_modzzz_professional_action_title_cancel'), 
            );

            if (!$this->aInfo['TitleEdit'] && !$this->aInfo['TitleConfirmed'] && !$this->aInfo['TitleDelete'])
                return '';

            return $oFunctions->genObjectsActions($this->aInfo, 'modzzz_professional_booking');
        } 

        return '';
    }    
  
    function getCode() { 
        return parent::getCode();
    }    
}
