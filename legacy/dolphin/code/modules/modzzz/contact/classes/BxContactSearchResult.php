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

class BxContactSearchResult extends BxDolTwigSearchResult {
  
	 var $aCurrent = array(
		'name' => 'modzzz_contact',
		'title' => '_modzzz_contact_page_title_browse',
		'table' => 'modzzz_contact_main', 
		'ownFields' => array('id', 'title', 'desc', 'email'), 
		'searchFields' => array(),
		'join' => array(),  
		'restriction' => array(
			'activeStatus' => array('value' => 'approved', 'field'=>'status', 'operator'=>'='), 
		), 
		'paginate' => array('perPage' => 14, 'page' => 1, 'totalNum' => 0, 'totalPages' => 1),
		'sorting' => 'last',
		'rss' => array(),
		'ident' => 'id'
	 );

     function BxContactSearchResult($sMode = '', $sValue = '', $sValue2 = '', $sValue3 = '') {   

		unset($this->aCurrent['join']);
		unset($this->aCurrent['rss']);

        switch ($sMode) {

            case 'departments':
 
                $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "browse/departments";
                $this->aCurrent['title'] = _t('_modzzz_contact_page_title_departments');
				break;

            case 'active':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => 'title', 'operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'approved';
                $this->sBrowseUrl = "administration/active";
                $this->aCurrent['title'] = _t('_modzzz_contact_page_title_active_departments');
				break;
				
            case 'inactive':
                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => 'title', 'operator' => 'against');
                $this->aCurrent['restriction']['activeStatus']['value'] = 'pending';
                $this->sBrowseUrl = "administration/inactive";
                $this->aCurrent['title'] = _t('_modzzz_contact_page_title_inactive_departments');
				break; 
 
           case 'admin_fields':
				$this->sTypeUnit = true;

				$this->aCurrent['table'] = 'modzzz_contact_action_types';

                if (isset($_REQUEST['filter']))
                    $this->aCurrent['restriction']['keyword'] = array('value' => process_db_input($_REQUEST['filter'], BX_TAGS_STRIP), 'field' => 'title', 'operator' => '=');

				$this->aCurrent['sorting'] = 'type';

				$this->aCurrent['ownFields'] = array('id', 'select_type', 'compulsory', 'title');
                    
                unset($this->aCurrent['restriction']['activeStatus']); 

                $this->sBrowseUrl = "browse/admin";
                $this->aCurrent['title'] = _t('_modzzz_contact_page_title_admin_contact');
                break;
 
	         default:
                $this->isError = true;
        }

        $oMain = $this->getMain();

        $this->aCurrent['paginate']['perPage'] = 20;
 
        $this->sFilterName = 'filter';

        parent::BxDolTwigSearchResult();
    }

    function getAlterOrder() {
		if ($this->aCurrent['sorting'] == 'last') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_contact_main`.`title` ASC";
			return $aSql;
		} elseif ($this->aCurrent['sorting'] == 'type') {
			$aSql = array();
			$aSql['order'] = " ORDER BY `modzzz_contact_action_types`.`title` ASC";
			return $aSql; 
		}
	    return array();
    }

    function displaySearchUnit ($aData) {
        $oMain = $this->getMain();
		if($this->sTypeUnit)
			return $oMain->_oTemplate->type_unit($aData, $this->sUnitTemplate); 
		else			
			return $oMain->_oTemplate->unit($aData, $this->sUnitTemplate);
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

    function getMain() {
        return BxDolModule::getInstance('BxContactModule');
    }
 
    
    function _getPseud () {
        return array();
    }
  
}