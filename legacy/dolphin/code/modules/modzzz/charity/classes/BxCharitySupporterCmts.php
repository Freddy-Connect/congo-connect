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

bx_import('BxTemplCmtsView');

class BxCharitySupporterCmts extends BxTemplCmtsView {
    
	/**
	 * Constructor
	 */
	function BxCharitySupporterCmts($sSystem, $iId) {
	    parent::BxTemplCmtsView($sSystem, $iId);
    }

    function getMain() {
        return BxDolModule::getInstance('BxCharityModule');
    }

    function isPostReplyAllowed () {
        if (!parent::isPostReplyAllowed())
            return false;
        $oMain = $this->getMain();        

		$aSupporterEntry = $oMain->_oDb->getSupporterEntryById($this->getId ());
        return $oMain->isAllowedCommentsSubProfile($oMain->_oDb->_sTableSupporter, $aSupporterEntry);
    }

    function isEditAllowedAll () {
        $oMain = $this->getMain();
		$aSupporterEntry = $oMain->_oDb->getSupporterEntryById($this->getId ());
        $aDataEntry = $oMain->_oDb->getEntryById($aSupporterEntry['charity_id']); 
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit ($aDataEntry))
            return true;
        return parent::isEditAllowedAll ();
    }

    function isRemoveAllowedAll () {
        $oMain = $this->getMain();
		$aSupporterEntry = $oMain->_oDb->getSupporterEntryById($this->getId ());
        $aDataEntry = $oMain->_oDb->getEntryById($aSupporterEntry['charity_id']); 
        if ($oMain->isAllowedCreatorCommentsDeleteAndEdit ($aDataEntry))
            return true;
        return parent::isRemoveAllowedAll ();
    }    
}
