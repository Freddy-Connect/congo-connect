<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx ListingCover
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

bx_import('BxDolTwigTemplate');
 
class BxListingCoverTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxListingCoverTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }
 
	function unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxListingCoverModule');
 
 		$oModule = BxDolModule::getInstance('BxListingModule');
		$aDataEntry = $oModule->_oDb->getEntryById($aData['listing_id']);

        $sImage = '';
        if ($aData['media_id']) {
            $a = array ('ID' => $aData['author_id'], 'Avatar' => $aData['media_id']);
            $aImage = BxDolService::call('photos', 'get_image', array($a, 'browse'), 'Search');
            $sImage = $aImage['no_image'] ? '' : $aImage['file'];
        } 
 
        $aVars = array (            
            'id' => $aData['media_id'],
            'thumb_url' => $sImage ? $sImage : $this->getImageUrl('no-image-thumb.png'),
            'title' => ($aDataEntry['cover_id']==$aData['media_id']) ? _t('_modzzz_listingcover_title_default_cover') : '', 
        ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
    }
  

}
