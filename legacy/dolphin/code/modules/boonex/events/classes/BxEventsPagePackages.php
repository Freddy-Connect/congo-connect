<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Referral
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
 
class BxEventsPagePackages extends BxDolTwigPageMain {

    function BxEventsPagePackages(&$oMain) {

		$this->_oDb = $oMain->_oDb;
        $this->_oConfig = $oMain->_oConfig;
 		$this->_oTemplate = $oMain->_oTemplate;

		parent::BxDolTwigPageMain('bx_events_packages', $oMain);
	}
   
    function getBlockCode_Packages() { 

        $aPackages = $this->_oDb->getAllPackages();
        if(empty($aPackages))
            return MsgBox(_t('_bx_events_packages_empty'));

        return $this->_oTemplate->displayAvailableLevels($aPackages);
	}



}