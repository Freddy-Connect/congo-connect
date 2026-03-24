<?php
/***************************************************************************
*                            Dolphin Smart Community Builder
*                              -------------------
*     begin                : Mon Mar 23 2006
*     copyright            : (C) 2007 BoonEx Confession
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

class BxManagerSearchResult extends BxDolTwigSearchResult {
  
	 var $aCurrent = array(
		'name' => 'modzzz_manager',
		'title' => '_modzzz_manager_page_title_browse',
		'table' => 'sys_objects_actions', 
		'ownFields' => array('ID', 'Caption', 'Icon', 'Url', 'Script', 'Eval', 'Order', 'Type', 'bDisplayInSubMenuHeader'), 
		'searchFields' => array(),
		'join' => array(),  
		'restriction' => array(), 
		'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
		'sorting' => 'last',
		'rss' => array(),
		'ident' => 'ID'
	 );

     function BxManagerSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   

		unset($this->aCurrent['join']);

        switch ($sMode) {

             case 'active':
				  
				$this->_sSortValue = $sValue;
				$this->_sMode = 'active';

				$this->aCurrent['restriction']['type'] = array('value' => $sValue, 'field' => 'Type', 'operator' => '=' );  
				break;

            case 'archive':
			 
				$this->_sMode = 'archive';
 
				$this->aCurrent['table'] = 'modzzz_manager_archive';  

				$this->aCurrent['ownFields'] = array('ID', 'Caption', 'Icon', 'Url', 'Script', 'Eval', 'Order', 'Type', 'bDisplayInSubMenuHeader', 'Date'); 
 
				$this->aCurrent['restriction']['type'] = array('value' => $sValue, 'field' => 'Type', 'operator' => '=' );  

				break;
 
	         default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = 200;
 
        $this->sFilterName = 'modzzz_manager_filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
 
		$aSql = array();
		$aSql['order'] = " ORDER BY `Order` ASC";
		return $aSql;  
    }

    function displayResultBlock () {
        global $oFunctions;
		$oMain = $this->getMain();
		
		if($this->_sMode == 'active'){
			$s = $this->displayParentResultBlock ();
			if ($s) {
	 
				$sSortUrl = BX_DOL_URL_ROOT . $oMain->_oConfig->getBaseUri() . 'sort/' . $this->_sSortValue;

				$sJsContent = $GLOBALS['oBxManagerModule']->_oTemplate->parseHtmlByName('jscript.html', array('url'=>$sSortUrl));
	 
				$s = $sJsContent . $s;

			}
		}else{
			$s = parent::displayResultBlock ();
		}

        if ($s) {

            $GLOBALS['oSysTemplate']->addDynamicLocation($oMain->_oConfig->getHomePath(), $oMain->_oConfig->getHomeUrl());
            $GLOBALS['oSysTemplate']->addCss(array('unit.css', 'twig.css'));
            return $GLOBALS['oSysTemplate']->parseHtmlByName('default_padding.html', array('content' => $s));
        }
        return '';
    }

    function displayParentResultBlock ()
    {
        $sCode = '';
        $aData = $this->getSearchData();
        if ($this->aCurrent['paginate']['totalNum'] > 0) {
            $sCode .= $this->addCustomParts();
            foreach ($aData as $aValue) {
                $sCode .= $this->displaySearchUnit($aValue);
            }
			$sCode = '<ul id="sortid">'.$sCode.'</ul>';
 
            $sCode = '<div class="result_block">' . $sCode . '<div class="clear_both"></div></div>';
        }
        return $sCode;
    }

    function displaySearchUnit ($aData)
    {
        $oMain = $this->getMain();
		if($this->_sMode == 'active') 
			return $oMain->_oTemplate->unit($aData, $this->sUnitTemplate);
		else
			return $oMain->_oTemplate->archive_unit($aData, $this->sUnitTemplate);
    }
 
    function getMain() {
        return BxDolModule::getInstance('BxManagerModule');
    }
  
    function _getPseud () {
        return array();
    }
  
}
