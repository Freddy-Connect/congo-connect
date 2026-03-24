<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx MemLog
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

class BxMemLogSearchResult extends BxDolTwigSearchResult {
 	
	var $sMode;
	var $iProfileOwnerId;

    var $aCurrent = array(
        'name' => 'modzzz_memlog',
        'title' => '_modzzz_memlog_page_title_browse',
        'table' => 'modzzz_memlog_main',
        'ownFields' => array('id', 'member_id', 'moderator_id', 'created'),
        'searchFields' => array(),
        'join' => array(), 
        'restriction' => array(),
        'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
        'sorting' => 'last',
        'rss' => array(),
        'ident' => 'id'
    );
     
    function BxMemLogSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '', $sValue4 = '') {    
		unset($this->aCurrent['rss']);
  
        switch ($sMode) {
  
            case 'moderating': 
				 
				$this->aCurrent['restriction']['moderator'] = array('value' => $sValue, 'field' => 'moderator_id', 'operator' => '=');
 
				$this->aCurrent['join']['profile'] = array(
                    'type' => 'inner',
                    'table' => 'Profiles',
                    'mainField' => 'member_id',
                    'onField' => 'ID',
                    'joinFields' => array('ID','NickName'),
				);
 
                $this->sBrowseUrl = "browse/moderating/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_memlog_page_title_moderating');
            break;  
            case 'moderators': 
  
				$this->aCurrent['restriction']['member'] = array('value' => $sValue, 'field' => 'member_id', 'operator' => '=');	 

				$this->aCurrent['join']['profile'] = array(
                    'type' => 'inner',
                    'table' => 'Profiles',
                    'mainField' => 'moderator_id',
                    'onField' => 'ID',
                    'joinFields' => array('ID','NickName'),
				);

                $this->sBrowseUrl = "browse/moderators/$sValue";
                $this->aCurrent['title'] = _t('_modzzz_memlog_page_title_moderators');
            break;  
            case '':
                $this->sBrowseUrl = 'browse/';
                $this->aCurrent['title'] = _t('_modzzz_memlog');
                break;

            default:
                $this->isError = true;
        }

        $oMain = $this->getMain();
  
        $this->aCurrent['paginate']['perPage'] = $oMain->_oDb->getParam('modzzz_memlog_perpage_browse');
 
        $this->sFilterName = 'memlog_filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		$aSql = array(); 
		$aSql['order'] = " ORDER BY `modzzz_memlog_main`.`created` DESC";
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
        return BxDolModule::getInstance('BxMemLogModule');
    }
 
    function _getPseud () {
        return array(    
            'created' => 'created'  
        );
    }
 
    function setPublicUnitsOnly($isPublic)
    { /*
        if($iLoggedId = getLoggedId()){
            $this->aCurrent['restriction']['public']['value'] = $isPublic ? array(BX_DOL_PG_ALL,BX_DOL_PG_MEMBERS) : false;
            $this->aCurrent['restriction']['public']['operator'] = $isPublic ? 'in' : '=';
        }else{
            $this->aCurrent['restriction']['public']['value'] = $isPublic ? BX_DOL_PG_ALL : false;
        }*/ 
    }



}
