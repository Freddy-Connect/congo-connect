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

bx_import('BxDolTwigSearchResult');

class BxListingCoverSearchResult extends BxDolTwigSearchResult {
 
    var $aCurrent = array(
        'name' => 'modzzz_listingcover',
        'title' => '_modzzz_listingcover_page_title_browse',
        'table' => 'modzzz_listingcover_main',
        'ownFields' => array('id', 'listing_id', 'title', 'uri', 'created'),
        'join' => array(
            'images' => array(
                    'type' => 'inner',
                    'table' => 'modzzz_listingcover_images',
                    'mainField' => 'id',
                    'onField' => 'entry_id',
                    'joinFields' => array('entry_id', 'media_id'),
            ),
        ),
        'restriction' => array(
            'activeStatus' => array('value' => 'approved', 'field'=>'status', 'operator'=>'='),
            'owner' => array('value' => '', 'field' => 'author_id', 'operator' => '='),
            'public' => array('value' => '', 'field' => 'allow_view_cover_to', 'operator' => '='),
        ),
        'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
        'sorting' => 'last',
        'rss' => array( 
            'title' => '',
            'link' => '',
            'image' => '',
            'profile' => 0,
            'fields' => array (
                'Link' => '',
                'Title' => 'title',
                'DateTimeUTS' => 'created',
                'Photo' => '',
            ),
        ),
        'ident' => 'id'
    );
     
    function BxListingCoverSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   
		
		unset($this->aCurrent['rss']);
		unset($this->aCurrent['restriction']['public']);
		
		$oMain = $this->getMain();
		$oListingMain = $this->getListingMain();

        switch ($sMode) {

			case 'covers':
	  
				$aListingEntry = $oListingMain->_oDb->getEntryByUri($sValue);
				$iListingId = (int)$aListingEntry['id'];
				$sTitle = $aListingEntry['title'];
 
				$this->aCurrent['restriction']['listing_id'] = array('value' => $iListingId, 'field' => 'listing_id', 'operator' => '='); 
				$this->sBrowseUrl = "cover/browse/$sValue";
				$this->aCurrent['title'] = _t('_modzzz_listingcover_caption_browse_listing_covers', $sTitle);
				break;   
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_listingcover');
                break;

            default:
                $this->isError = true;
        }
 
        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_listingcover_perpage_browse');
  
        $this->sFilterName = 'listingcover_filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		$aSql = array();
		$aSql['order'] = " ORDER BY `modzzz_listingcover_main`.`created` DESC";
		return $aSql;
    }

    function displayResultBlock () {
        global $oFunctions;
        $s = parent::displayResultBlock ();
        if ($s) {
            $oMain = $this->getMain();
            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }
  
    function displaySearchUnit ($aData)
    {
        $oMain = $this->getMain();
        return $oMain->_oTemplate->unit($aData, $this->sUnitTemplate);
    }
 
    function getMain() {
        return BxDolModule::getInstance('BxListingCoverModule');
    }

    function getListingMain() {
        return BxDolModule::getInstance('BxListingModule');
    }
 
    function _getPseud () {
        return array(    
            'id' => 'id',
            'title' => 'title',
            'uri' => 'uri',
            'created' => 'created',
            'author_id' => 'author_id',
        );
    }

    function showPagination($sUrlAdmin = false)
    {
        $oMain = $this->getMain();
        $oConfig = $oMain->_oConfig;
        bx_import('BxDolPaginate');
        $sUrlStart = BX_DOL_URL_ROOT . $oConfig->getBaseUri() . ($sUrlAdmin ? $sUrlAdmin : $this->sBrowseUrl);
 
        $sUrlStart .= (false === strpos($sUrlStart, '?') ? '?' : '&');
		
		//[begin] added keyword modification (modzzz)
		$sKeyWord = bx_get('keyword');
		if ($sKeyWord !== false)
			$sLink = 'keyword=' . clear_xss($sKeyWord) . '&';
   
		$sUrlStart .= $sLink;
		//[end] added keyword modification (modzzz)

        $oPaginate = new BxDolPaginate(array(
            'page_url' => $sUrlStart . 'page={page}&per_page={per_page}' . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) : ''),
            'count' => $this->aCurrent['paginate']['totalNum'],
            'per_page' => $this->aCurrent['paginate']['perPage'],
            'page' => $this->aCurrent['paginate']['page'],
            'per_page_changer' => true,
            'page_reloader' => true,
            'on_change_page' => '',
            'on_change_per_page' => "document.location='" . $sUrlStart . "page=1&per_page=' + this.value + '" . (false !== bx_get($this->sFilterName) ? '&' . $this->sFilterName . '=' . bx_get($this->sFilterName) ."';": "';"),
        ));

        return '<div class="clear_both"></div>'.$oPaginate->getPaginate();
    }

    function setPublicUnitsOnly($isPublic) { 
    }
 

}
