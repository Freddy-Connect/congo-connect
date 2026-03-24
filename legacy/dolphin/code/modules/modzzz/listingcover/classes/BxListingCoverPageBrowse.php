<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Location
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

class BxListingCoverPageBrowse extends BxDolTwigPageMain {

    function BxListingCoverPageBrowse(&$oMain, $sUri) {

		$this->oDb = $oMain->oDb;
        $this->oConfig = $oMain->_oConfig;
        $this->oTemplate = $oMain->_oTemplate;
		$this->oMain = $oMain;
		$this->sUri = $sUri;

        $this->sSearchResultClassName = 'BxListingCoverSearchResult';
        $this->sFilterName = 'listingcover_filter';
		parent::BxDolTwigPageMain('modzzz_listingcover_browse', $oMain);

        if ($_POST['action_cover'] && is_array($_POST['entry'])) {
 
            foreach ($_POST['entry'] as $iMediaId) {

				$aMediaEntry = $this->oDb->getCoverMediaById ($iMediaId);

                if ($this->oDb->activateCover($aMediaEntry['listing_id'], $iMediaId)) {
                    //$this->oMain->onEventChanged ($iMediaId, 'approved');
                }
            }

        } elseif ($_POST['action_delete'] && is_array($_POST['entry'])) {

            foreach ($_POST['entry'] as $iMediaId) {
 
				$aMediaEntry = $this->oDb->getCoverMediaById ($iMediaId);

                $aDataEntry = $this->oDb->getEntryById((int)$aMediaEntry['entry_id']);
                if (!$this->oMain->isAllowedDelete($aDataEntry))
                    continue;
				
				if($aDataEntry['id'])
					$this->oDb->deleteEntryMedia($aDataEntry['id'], $iMediaId);
            }
        }  
	}
  
    function getBlockCode_Browse () {
 
        $oModule = BxDolModule::getInstance('BxListingModule');

        bx_import ('PageMain', $this->oMain->_aModule);
        $o = new BxListingCoverPageMain ($this->oMain);
        
		$o->sUrlStart = BX_DOL_URL_ROOT . $this->oConfig->getBaseUri() . 'cover/browse/' . $this->sUri;
        $o->sUrlStart .= (false === strpos($o->sUrlStart, '?') ? '?' : '&');

		$aDataEntry = $oModule->_oDb->getEntryByUri($this->sUri);
 
        $aBlock = $o->ajaxBrowse(
            'covers',
            getParam('modzzz_listingcover_perpage_browse'), 
            array(), $this->sUri, true, false, false 
        );  
 
		if($aBlock[0] == '') $aBlock[0] = MsgBox(_t('_Empty'));

		if(getParam("modzzz_listingcover_active")!='on')
			$aBlock[0] = MsgBox(_t('_Access denied'));
 
		if(!$oModule->isAllowedView($aDataEntry)) 
			$aBlock[0] = MsgBox(_t('_Access denied'));

		return $aBlock;  
    } 


}

