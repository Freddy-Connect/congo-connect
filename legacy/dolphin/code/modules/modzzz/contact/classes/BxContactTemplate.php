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

bx_import('BxDolTwigTemplate');
bx_import('BxDolCategories');

/*
 * Contact module View
 */
class BxContactTemplate extends BxDolTwigTemplate {

    var $_iPageIndex = 500;      
    
	/**
	 * Constructor
	 */
	function BxContactTemplate(&$oConfig, &$oDb) {
        parent::BxDolTwigTemplate($oConfig, $oDb);
    }
  
    function unit ($aData, $sTemplateName) {
 
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxContactModule');
   
        $aVars = array (            
            'id' => $aData['id'], 
            'edit_url' => BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/create/' . $aData['id'], 
            'title' => $aData['title'], 
            'desc' => $aData['desc'], 
            'email' => $aData['email'], 
          ); 
 
        return $this->parseHtmlByName($sTemplateName, $aVars);
     }
  
     function type_unit ($aData, $sTemplateName) {
  
        $aVars = array (
			'id' => $aData['id'],  
             'lang_key' => $aData['title'], 
             'title' => _t($aData['title']), 
             'type' => $this->_oDb->getSelectType($aData['select_type']), 
             'url' =>  BX_DOL_URL_ROOT . $this->_oConfig->getBaseUri() . 'administration/add_field/' . $aData['id'],  
        );
 
        return $this->parseHtmlByName('type_unit_admin', $aVars);
    }  

	function getMultiActionName($sActionIds){
		$aDbActions = explode(CATEGORIES_DIVIDER, $sActionIds);
		$aActions = array();
		foreach($aDbActions as $iActionId){
			$aActions[] = $this->getActionName($iActionId);
		}

		$sActions = implode(', ', $aActions);

		return $sActions;
	}
 
	function getActionName($iActionId){
        if (null == $this->_oMain)
            $this->_oMain = BxDolModule::getInstance('BxContactModule');
 
		return $this->_oMain->_oDb->getActionName($iActionId);
	}





   
}