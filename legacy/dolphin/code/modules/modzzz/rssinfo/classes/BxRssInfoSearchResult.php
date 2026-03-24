<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx RssInfo
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

class BxRssInfoSearchResult extends BxDolTwigSearchResult {
	var $sShortUnit = '';

    var $aCurrent = array(
        'name' => 'modzzz_rssinfo',
        'title' => '_modzzz_rssinfo_page_title_browse',
        'table' => 'modzzz_rssinfo_main',
        'ownFields' => array('id', 'title', 'uri', 'updated', 'created', 'when', 'owner_id', 'link', 'category'),
        'searchFields' => array('title'),
        /*'join' => array(), */
        'restriction' => array(
            'activeStatus' => array('value' => 'approved', 'field'=>'status', 'operator'=>'='),
             'public' => array('value' => '', 'field' => 'allow_view_rssinfo_to', 'operator' => '='),
        ),
        'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
        'sorting' => 'last', 
        'ident' => 'id'
    );
     
    function BxRssInfoSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   
 
		$iTime = time();

        switch ($sMode) { 
            case 'pending':  
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration";
                $this->aCurrent['title'] = _t('_modzzz_rssinfo_page_title_pending_publish');
                unset($this->aCurrent['rssinfo']);
            break;  
            case 'admin': 
                $this->sBrowseUrl = "administration";
                $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->aCurrent['title'] = _t('_modzzz_rssinfo_page_title_admin_rssinfo');
                break;   
            default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_rssinfo_perpage_browse');
 
        $this->sFilterName = 'modzzz_rssinfo_filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_rssinfo_main`.`when` DESC";
			return $aSql;
 		}
	    return array();
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

    function displaySearchUnit ($aData) {
        $oMain = $this->getMain();
 
		return $oMain->_oTemplate->unit($aData, $this->sUnitTemplate);
    }
 
    function getMain() {
        return BxDolModule::getInstance('BxRssInfoModule');
    }
 
    function _getPseud () {
        return array(    
            'id' => 'id',
            'title' => 'title',
            'uri' => 'uri',
            'created' => 'created',  
            'when' => 'when',
        );
    }
}